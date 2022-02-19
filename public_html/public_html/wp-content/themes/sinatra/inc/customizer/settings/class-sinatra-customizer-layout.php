<?php
/**
 * Sinatra Layout section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Layout' ) ) :
	/**
	 * Sinatra Layout section in Customizer.
	 */
	class Sinatra_Customizer_Layout {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom options in Customizer.
			 */
			add_filter( 'sinatra_customizer_options', array( $this, 'register_options' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Section.
			$options['section']['sinatra_layout_section'] = array(
				'title'    => esc_html__( 'Layout', 'sinatra' ),
				'panel'    => 'sinatra_panel_general',
				'priority' => 10,
			);

			// Site layout.
			$options['setting']['sinatra_site_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_layout_section',
					'label'       => esc_html__( 'Site Layout', 'sinatra' ),
					'description' => esc_html__( 'Choose your site&rsquo;s main layout.', 'sinatra' ),
					'choices'     => array(
						'fw-contained'    => esc_html__( 'Full Width: Contained', 'sinatra' ),
						'fw-stretched'    => esc_html__( 'Full Width: Stretched', 'sinatra' ),
						'boxed-separated' => esc_html__( 'Boxed Content', 'sinatra' ),
						'boxed'           => esc_html__( 'Boxed', 'sinatra' ),
					),
				),
			);

			// Container width.
			$options['setting']['sinatra_container_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'section'     => 'sinatra_layout_section',
					'label'       => esc_html__( 'Content Width', 'sinatra' ),
					'description' => esc_html__( 'Change your site&rsquo;s main container width.', 'sinatra' ),
					'min'         => 500,
					'max'         => 1920,
					'step'        => 10,
					'unit'        => 'px',
					'required'    => array(
						array(
							'control'  => 'sinatra_site_layout',
							'value'    => 'fw-stretched',
							'operator' => '!=',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Sinatra_Customizer_Layout();
