<?php
/**
 * Sinatra Page Title Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Page_Header' ) ) :
	/**
	 * Sinatra Page Title Settings section in Customizer.
	 */
	class Sinatra_Customizer_Page_Header {

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

			// Page Title Section.
			$options['section']['sinatra_section_page_header'] = array(
				'title'    => esc_html__( 'Page Header', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 60,
			);

			// Page Header enable.
			$options['setting']['sinatra_page_header_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'label'   => esc_html__( 'Enable Page Header', 'sinatra' ),
					'section' => 'sinatra_section_page_header',
				),
			);

			// Alignment.
			$options['setting']['sinatra_page_header_alignment'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'     => 'sinatra-alignment',
					'label'    => esc_html__( 'Title Alignment', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'choices'  => 'horizontal',
					'icons'    => array(
						'left'   => 'dashicons dashicons-editor-alignleft',
						'center' => 'dashicons dashicons-editor-aligncenter',
						'right'  => 'dashicons dashicons-editor-alignright',
					),
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['sinatra_page_header_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Page Title Spacing', 'sinatra' ),
					'description' => esc_html__( 'Specify Page Title top and bottom padding.', 'sinatra' ),
					'section'     => 'sinatra_section_page_header',
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'sinatra' ),
						'bottom' => esc_html__( 'Bottom', 'sinatra' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header design options heading.
			$options['setting']['sinatra_page_header_heading_design'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header background design.
			$options['setting']['sinatra_page_header_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
							'image'    => esc_html__( 'Image', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_page_header_heading_design',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header Text Color.
			$options['setting']['sinatra_page_header_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_page_header_heading_design',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header Border.
			$options['setting']['sinatra_page_header_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'display'  => array(
						'border' => array(
							'style'     => esc_html__( 'Style', 'sinatra' ),
							'color'     => esc_html__( 'Color', 'sinatra' ),
							'width'     => esc_html__( 'Width (px)', 'sinatra' ),
							'positions' => array(
								'top'    => esc_html__( 'Top', 'sinatra' ),
								'bottom' => esc_html__( 'Bottom', 'sinatra' ),
							),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_page_header_heading_design',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header typography heading.
			$options['setting']['sinatra_typography_page_header'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_page_header',
					'required' => array(
						array(
							'control'  => 'sinatra_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header font size.
			$options['setting']['sinatra_page_header_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Page Title Font Size', 'sinatra' ),
					'description' => esc_html__( 'Choose your page title font size.', 'sinatra' ),
					'section'     => 'sinatra_section_page_header',
					'responsive'  => true,
					'unit'        => array(
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
					'required'    => array(
						array(
							'control'  => 'sinatra_typography_page_header',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_page_header_enable',
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
new Sinatra_Customizer_Page_Header();
