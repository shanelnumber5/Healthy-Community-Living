<?php
/**
 * Sinatra Customizer widgets class.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Customizer_Widget_Cart' ) ) :

	/**
	 * Sinatra Customizer widget class
	 */
	class Sinatra_Customizer_Widget_Cart extends Sinatra_Customizer_Widget {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @param array $args An array of the values for this widget.
		 */
		public function __construct( $args = array() ) {

			parent::__construct( $args );

			$this->name        = esc_html__( 'Cart', 'sinatra' );
			$this->description = esc_html__( 'Displays WooCommerce cart.', 'sinatra' );
			$this->icon        = 'dashicons dashicons-cart';
			$this->type        = 'cart';
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function form() {}
	}
endif;
