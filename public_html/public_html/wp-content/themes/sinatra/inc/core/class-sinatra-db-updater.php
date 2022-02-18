<?php
/**
 * The Database updater for Sinatra.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.1.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_DB_Updater' ) ) :

	/**
	 * Sinatra_DB_Updater Class.
	 */
	class Sinatra_DB_Updater {

		/**
		 * DB updates and callbacks that need to be run per version.
		 *
		 * @var array
		 */
		private static $db_updates = array(
			'1.1.0' => array(
				'v_1_1_0',
			),
		);

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'updates' ) );
			} else {
				add_action( 'wp', array( $this, 'updates' ) );
			}
		}

		/**
		 * Implement theme update logic.
		 *
		 * @since 1.0.0
		 */
		public function updates() {

			$updates         = $this->get_db_update_callbacks();
			$current_version = get_option( 'sinatra-theme-updater', null );

			if ( empty( $updates ) ) {
				return;
			}

			if ( ! is_null( $current_version ) && -1 < version_compare( $current_version, max( array_keys( $updates ) ) ) ) {
				return;
			}

			foreach ( $updates as $version => $callbacks ) {
				if ( version_compare( $current_version, $version, '<' ) ) {
					foreach ( $callbacks as $callback ) {
						call_user_func( array( 'Sinatra_DB_Updater', $callback ) );
					}
				}
			}

			// Update dynamic stylesheet on theme update.
			sinatra_dynamic_styles()->update_dynamic_file();

			$this->update_db_version();
		}

		/**
		 * Update DB version to current.
		 *
		 * @param string|null $version New Astra theme version or null.
		 */
		public static function update_db_version( $version = null ) {
			update_option( 'sinatra-theme-updater', SINATRA_THEME_VERSION );
		}

		/**
		 * Get list of DB update callbacks.
		 *
		 * @since  1.1.0
		 * @return array
		 */
		public function get_db_update_callbacks() {
			return self::$db_updates;
		}

		/**
		 * DB Update v1.1.0
		 *
		 * @since  1.1.0
		 * @return void
		 */
		public static function v_1_1_0() {

			sinatra()->options->set(
				'sinatra_single_post_elements',
				array(
					'thumb'          => sinatra()->options->get( 'sinatra_single_post_thumb' ),
					'category'       => sinatra()->options->get( 'sinatra_single_post_categories' ),
					'tags'           => sinatra()->options->get( 'sinatra_single_post_tags' ),
					'last-updated'   => sinatra()->options->get( 'sinatra_single_last_updated' ),
					'about-author'   => sinatra()->options->get( 'sinatra_single_about_author' ),
					'prev-next-post' => sinatra()->options->get( 'sinatra_single_post_next_prev' ),
				)
			);

			// Single Post Layout to Single Title Position.
			switch ( sinatra()->options->get( 'sinatra_single_post_layout' ) ) {

				case 'layout-1':
					sinatra()->options->set( 'sinatra_single_title_position', 'in-content' );
					break;

				case 'layout-2':
					sinatra()->options->set( 'sinatra_single_title_position', 'in-page-header' );
					break;
			}
		}
	}

endif;

new Sinatra_DB_Updater();
