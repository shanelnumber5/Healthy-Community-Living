<?php
/**
 * Sinatra Logo section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Logo' ) ) :
	/**
	 * Sinatra Logo section in Customizer.
	 */
	class Sinatra_Customizer_Logo {

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

			// Logo Retina.
			$options['setting']['sinatra_logo_default_retina'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_background',
				'control'           => array(
					'type'        => 'sinatra-background',
					'section'     => 'title_tagline',
					'label'       => esc_html__( 'Retina Logo', 'sinatra' ),
					'description' => esc_html__( 'Upload exactly 2x the size of your default logo to make your logo crisp on HiDPI screens. This options is not required if logo above is in SVG format.', 'sinatra' ),
					'priority'    => 20,
					'advanced'    => false,
					'strings'     => array(
						'select_image' => __( 'Select logo', 'sinatra' ),
						'use_image'    => __( 'Select', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '!=',
						),
					),
				),
				'partial'           => array(
					'selector'            => '.sinatra-logo',
					'render_callback'     => 'sinatra_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Logo Max Height.
			$options['setting']['sinatra_logo_max_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Logo Height', 'sinatra' ),
					'description' => esc_html__( 'Maximum logo image height.', 'sinatra' ),
					'section'     => 'title_tagline',
					'priority'    => 30,
					'min'         => 0,
					'max'         => 1000,
					'step'        => 10,
					'unit'        => 'px',
					'responsive'  => true,
					'required'    => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '!=',
						),
					),
				),
			);

			// Logo margin.
			$options['setting']['sinatra_logo_margin'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Logo Margin', 'sinatra' ),
					'description' => esc_html__( 'Specify spacing around logo. Negative values are allowed.', 'sinatra' ),
					'section'     => 'title_tagline',
					'settings'    => 'sinatra_logo_margin',
					'priority'    => 40,
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'sinatra' ),
						'right'  => esc_html__( 'Right', 'sinatra' ),
						'bottom' => esc_html__( 'Bottom', 'sinatra' ),
						'left'   => esc_html__( 'Left', 'sinatra' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
				),
			);

			// Show tagline.
			$options['setting']['sinatra_display_tagline'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'label'    => esc_html__( 'Display Tagline', 'sinatra' ),
					'section'  => 'title_tagline',
					'settings' => 'sinatra_display_tagline',
					'priority' => 80,
				),
				'partial'           => array(
					'selector'            => '.sinatra-logo',
					'render_callback'     => 'sinatra_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Site Identity heading.
			$options['setting']['sinatra_logo_heading_site_identity'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Site Identity', 'sinatra' ),
					'section'  => 'title_tagline',
					'settings' => 'sinatra_logo_heading_site_identity',
					'priority' => 50,
					'toggle'   => false,
				),
			);

			// Logo typography heading.
			$options['setting']['sinatra_typography_logo_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'title_tagline',
					'priority' => 100,
					'required' => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
					),
				),
			);

			// Site title font size.
			$options['setting']['sinatra_logo_text_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'       => 'sinatra-range',
					'label'      => esc_html__( 'Site Title Font Size', 'sinatra' ),
					'section'    => 'title_tagline',
					'priority'   => 100,
					'min'        => 8,
					'max'        => 30,
					'step'       => 1,
					'responsive' => true,
					'unit'       => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 90,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
					),
					'required'   => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_typography_logo_heading',
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
new Sinatra_Customizer_Logo();
