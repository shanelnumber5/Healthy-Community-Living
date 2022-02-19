<?php
/**
 * Sinatra Sticky Header Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Sticky_Header' ) ) :
	/**
	 * Sinatra Sticky Header section in Customizer.
	 */
	class Sinatra_Customizer_Sticky_Header {

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

			// Sticky Header Section.
			$options['section']['sinatra_section_sticky_header'] = array(
				'title'    => esc_html__( 'Sticky Header', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 80,
			);

			// Enable Transparent Header.
			$options['setting']['sinatra_sticky_header'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'label'   => esc_html__( 'Enable Sticky Header', 'sinatra' ),
					'section' => 'sinatra_section_sticky_header',
				),
			);

			// Responsive heading.
			$options['setting']['sinatra_sticky_header_responsive'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'section'  => 'sinatra_section_sticky_header',
					'label'    => esc_html__( 'Responsive', 'sinatra' ),
					'required' => array(
						array(
							'control'  => 'sinatra_sticky_header',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hide sticky header on.
			$options['setting']['sinatra_sticky_header_hide_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_no_sanitize',
				'control'           => array(
					'type'        => 'sinatra-checkbox-group',
					'label'       => esc_html__( 'Hide on: ', 'sinatra' ),
					'description' => esc_html__( 'Choose on which devices to hide Sticky Header on. ', 'sinatra' ),
					'section'     => 'sinatra_section_sticky_header',
					'choices'     => sinatra_get_device_choices(),
					'required'    => array(
						array(
							'control'  => 'sinatra_sticky_header',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_sticky_header_responsive',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Sinatra_Customizer_Sticky_Header();
