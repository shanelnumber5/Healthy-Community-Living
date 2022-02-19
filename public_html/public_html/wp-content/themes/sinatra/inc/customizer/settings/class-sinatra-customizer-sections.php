<?php
/**
 * Sinatra Customizer sections and panels.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Customizer_Sections' ) ) :
	/**
	 * Sinatra Customizer sections and panels.
	 */
	class Sinatra_Customizer_Sections {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom panels in Customizer.
			 */
			add_filter( 'sinatra_customizer_options', array( $this, 'register_panel' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_panel( $options ) {

			// General panel.
			$options['panel']['sinatra_panel_general'] = array(
				'title'    => esc_html__( 'General Settings', 'sinatra' ),
				'priority' => 1,
			);

			// Header panel.
			$options['panel']['sinatra_panel_header'] = array(
				'title'    => esc_html__( 'Header', 'sinatra' ),
				'priority' => 3,
			);

			// Footer panel.
			$options['panel']['sinatra_panel_footer'] = array(
				'title'    => esc_html__( 'Footer', 'sinatra' ),
				'priority' => 5,
			);

			// Blog settings.
			$options['panel']['sinatra_panel_blog'] = array(
				'title'    => esc_html__( 'Blog', 'sinatra' ),
				'priority' => 6,
			);

			return $options;
		}
	}
endif;
new Sinatra_Customizer_Sections();
