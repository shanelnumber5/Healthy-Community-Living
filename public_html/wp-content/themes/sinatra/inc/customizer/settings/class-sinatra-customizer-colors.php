<?php
/**
 * Sinatra Base Colors section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Colors' ) ) :
	/**
	 * Sinatra Colors section in Customizer.
	 */
	class Sinatra_Customizer_Colors {

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
			$options['section']['sinatra_section_colors'] = array(
				'title'    => esc_html__( 'Base Colors', 'sinatra' ),
				'panel'    => 'sinatra_panel_general',
				'priority' => 20,
			);

			// Accent color.
			$options['setting']['sinatra_accent_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Accent Color', 'sinatra' ),
					'description' => esc_html__( 'The accent color is used subtly throughout your site, to call attention to key elements.', 'sinatra' ),
					'section'     => 'sinatra_section_colors',
					'priority'    => 10,
					'opacity'     => false,
				),
			);

			// Body background heading.
			$options['setting']['sinatra_body_background_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'priority' => 40,
					'label'    => esc_html__( 'Body Background', 'sinatra' ),
					'section'  => 'sinatra_section_colors',
					'toggle'   => false,
				),
			);

			// Content background heading.
			$options['setting']['sinatra_content_colors_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'priority' => 50,
					'label'    => esc_html__( 'Content', 'sinatra' ),
					'section'  => 'sinatra_section_colors',
					'toggle'   => false,
				),
			);

			// Content text color.
			$options['setting']['sinatra_content_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_colors',
					'priority' => 50,
					'opacity'  => true,
				),
			);

			// Content text color.
			$options['setting']['sinatra_content_link_hover_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Link Hover Color', 'sinatra' ),
					'description' => esc_html__( 'This only applies to entry content area, other links will use the accent color on hover.', 'sinatra' ),
					'section'     => 'sinatra_section_colors',
					'priority'    => 50,
					'opacity'     => true,
				),
			);

			// Headings color.
			$options['setting']['sinatra_headings_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Headings Color', 'sinatra' ),
					'section'  => 'sinatra_section_colors',
					'priority' => 50,
					'opacity'  => true,
				),
			);

			// Content background color.
			$options['setting']['sinatra_boxed_content_background_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Boxed Content - Background Color', 'sinatra' ),
					'description' => esc_html__( 'Only used if Site Layout is Boxed or Boxed Content.', 'sinatra' ),
					'section'     => 'sinatra_section_colors',
					'priority'    => 50,
					'opacity'     => true,
				),
			);

			return $options;
		}

	}
endif;
new Sinatra_Customizer_Colors();
