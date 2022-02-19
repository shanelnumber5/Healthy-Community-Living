<?php

namespace Endurance\WP\Module\Data\API;

use Endurance\WP\Module\Data\Event;
use Endurance\WP\Module\Data\EventManager;
use Endurance\WP\Module\Data\HubConnection;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * REST API controller for sending events to the hub.
 */
class Events extends WP_REST_Controller {

	/**
	 * Instance of the EventManager class.
	 *
	 * @var EventManager
	 */
	public $event_manager;

	/**
	 * Instance of the HubConnection class.
	 *
	 * @var HubConnection
	 */
	public $hub;

	/**
	 * Events constructor.
	 *
	 * @param HubConnection $hub           Instance of the HubConnection class.
	 * @param EventManager  $event_manager Instance of the EventManager class.
	 */
	public function __construct( HubConnection $hub, EventManager $event_manager ) {
		$this->event_manager = $event_manager;
		$this->hub           = $hub;
		$this->namespace     = 'bluehost/v1/data';
		$this->rest_base     = 'events';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see   register_rest_route()
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/',
			array(
				'args' => array(
					'action'   => array(
						'required'          => true,
						'description'       => __( 'Event action' ),
						'type'              => 'string',
						'sanitize_callback' => function ( $value ) {
							return sanitize_title( $value );
						},
					),
					'category' => array(
						'default'           => 'admin',
						'description'       => __( 'Event category' ),
						'type'              => 'string',
						'sanitize_callback' => function ( $value ) {
							return sanitize_title( $value );
						},
					),
					'data'     => array(
						'description' => __( 'Event data' ),
						'type'        => 'object',
					),
					'queue'    => array(
						'default'           => true,
						'description'       => __( 'Whether or not to queue the event' ),
						'type'              => 'boolean',
						'sanitize_callback' => function ( $value ) {
							return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
						}
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
			)
		);

	}

	/**
	 * Dispatches a new event.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {

		$category = $request->get_param( 'category' );
		$action   = $request->get_param( 'action' );
		$data     = ! empty( $request['data'] ) ? $request['data'] : array();

		$event = new Event( $category, $action, $data );

		// If request isn't to be queued, we want the realtime response.
		if ( ! $request['queue'] ) {
			$notifications = [];
			$hub_response  = $this->hub->notify( [ $event ], true );

			if ( is_wp_error( $hub_response ) ) {
				return new \WP_REST_Response( $hub_response->get_error_message(), 401 );
			}

			$status_code = wp_remote_retrieve_response_code( $hub_response );

			if ( 200 !== $status_code ) {
				return new \WP_REST_Response( wp_remote_retrieve_response_message( $hub_response ), $status_code );
			}

			$payload = json_decode( wp_remote_retrieve_body( $hub_response ) );
			if ( $payload && is_array( $payload->data ) ) {
				$notifications = $payload;
			}

			return new \WP_REST_Response( $notifications, 201 );
		}

		// Otherwise, queue the event.
		$this->event_manager->push( $event );

		$response = rest_ensure_response(
			array(
				'category' => $category,
				'action'   => $action,
				'data'     => $data,
			)
		);
		$response->set_status( 202 );

		return $response;
	}

	/**
	 * User is required to be logged in.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error
	 *
	 * @since 1.0
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'read' ) ) {
			return new \WP_Error(
				'rest_cannot_log_event',
				__( 'Sorry, you are not allowed to use this endpoint.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}
}
