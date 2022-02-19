<?php
/**
 * Sinatra Base Typography section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Typography' ) ) :
	/**
	 * Sinatra Typography section in Customizer.
	 */
	class Sinatra_Customizer_Typography {

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
			$options['section']['sinatra_section_typography'] = array(
				'title'    => esc_html__( 'Base Typography', 'sinatra' ),
				'panel'    => 'sinatra_panel_general',
				'priority' => 30,
			);

			// HTML base font size.
			$options['setting']['sinatra_html_base_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Base Font Size', 'sinatra' ),
					'description' => esc_html__( 'REM base of the root (html) element.', 'sinatra' ),
					'section'     => 'sinatra_section_typography',
					'min'         => 8,
					'max'         => 30,
					'step'        => 1,
					'unit'        => 'px',
					'responsive'  => true,
				),
			);

			// Anti-Aliased Font Smoothing.
			$options['setting']['sinatra_font_smoothing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Font Smoothing', 'sinatra' ),
					'description' => esc_html__( 'Enable/Disable anti-aliasing font smoothing.', 'sinatra' ),
					'section'     => 'sinatra_section_typography',
				),
			);

			// Headings typography heading.
			$options['setting']['sinatra_typography_body_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Body & Content', 'sinatra' ),
					'section' => 'sinatra_section_typography',
				),
			);

			// Body Font.
			$options['setting']['sinatra_body_font'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'     => 'sinatra-typography',
					'label'    => esc_html__( 'Body Typography', 'sinatra' ),
					'section'  => 'sinatra_section_typography',
					'display'  => array(
						'font-family'     => array(),
						'font-subsets'    => array(),
						'font-weight'     => array(),
						'font-style'      => array(),
						'text-transform'  => array(),
						'text-decoration' => array(),
						'letter-spacing'  => array(),
						'font-size'       => array(),
						'line-height'     => array(),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_typography_body_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Headings typography heading.
			$options['setting']['sinatra_typography_headings_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Headings (H1 - H6)', 'sinatra' ),
					'section' => 'sinatra_section_typography',
				),
			);

			// Headings default.
			$options['setting']['sinatra_headings_font'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'     => 'sinatra-typography',
					'label'    => esc_html__( 'Headings Default', 'sinatra' ),
					'section'  => 'sinatra_section_typography',
					'display'  => array(
						'font-family'    => array(),
						'font-subsets'   => array(),
						'font-weight'    => array(),
						'font-style'     => array(),
						'text-transform' => array(),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_typography_headings_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			for ( $i = 1; $i <= 6; $i++ ) {

				$options['setting'][ 'sinatra_h' . $i . '_font' ] = array(
					'transport'         => 'postMessage',
					'sanitize_callback' => 'sinatra_sanitize_typography',
					'control'           => array(
						'type'     => 'sinatra-typography',
						/* translators: %s Heading size */
						'label'    => esc_html( sprintf( __( 'H%s', 'sinatra' ), $i ) ),
						'section'  => 'sinatra_section_typography',
						'display'  => array(
							'font-family'     => array(),
							'font-subsets'    => array(),
							'font-weight'     => array(),
							'font-style'      => array(),
							'text-transform'  => array(),
							'text-decoration' => array(),
							'letter-spacing'  => array(),
							'font-size'       => array(),
							'line-height'     => array(),
						),
						'required' => array(
							array(
								'control'  => 'sinatra_typography_headings_heading',
								'value'    => true,
								'operator' => '==',
							),
						),
					),
				);
			}

			$options['setting']['sinatra_heading_em_font'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'        => 'sinatra-typography',
					'label'       => esc_html__( 'Heading Emphasized Text', 'sinatra' ),
					'description' => esc_html__( 'Adds a separate font for styling of &lsaquo;em&rsaquo; tags, so you can create stylish typographic elements.', 'sinatra' ),
					'section'     => 'sinatra_section_typography',
					'display'     => array(
						'font-family'     => array(),
						'font-subsets'    => array(),
						'font-weight'     => array(),
						'font-style'      => array(),
						'text-transform'  => array(),
						'text-decoration' => array(),
						'letter-spacing'  => array(),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_typography_headings_heading',
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
new Sinatra_Customizer_Typography();
