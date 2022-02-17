<?php
/**
 * Sinatra Main Footer section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Main_Footer' ) ) :
	/**
	 * Sinatra Main Footer section in Customizer.
	 */
	class Sinatra_Customizer_Main_Footer {

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
			$options['section']['sinatra_section_main_footer'] = array(
				'title'    => esc_html__( 'Main Footer', 'sinatra' ),
				'panel'    => 'sinatra_panel_footer',
				'priority' => 20,
			);

			// Enable Footer.
			$options['setting']['sinatra_enable_footer'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'label'   => esc_html__( 'Enable Main Footer', 'sinatra' ),
					'section' => 'sinatra_section_main_footer',
				),
			);

			// Footer Layout.
			$options['setting']['sinatra_footer_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-radio-image',
					'label'       => esc_html__( 'Column Layout', 'sinatra' ),
					'description' => esc_html__( 'Choose your site&rsquo;s footer column layout.', 'sinatra' ),
					'section'     => 'sinatra_section_main_footer',
					'choices'     => array(
						'layout-1' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/footer-layout-1.svg',
							'title' => esc_html__( '1/4 + 1/4 + 1/4 + 1/4', 'sinatra' ),
						),
						'layout-2' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/footer-layout-2.svg',
							'title' => esc_html__( '1/3 + 1/3 + 1/3', 'sinatra' ),
						),
						'layout-3' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/footer-layout-3.svg',
							'title' => esc_html__( '2/3 + 1/3', 'sinatra' ),
						),
						'layout-4' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/footer-layout-4.svg',
							'title' => esc_html__( '1/3 + 2/3', 'sinatra' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#sinatra-footer-widgets',
					'render_callback'     => 'sinatra_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Center footer widgets..
			$options['setting']['sinatra_footer_widgets_align_center'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'label'    => esc_html__( 'Center Widget Content', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#sinatra-footer-widgets',
					'render_callback'     => 'sinatra_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Main Footer visibility.
			$options['setting']['sinatra_footer_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Device Visibility', 'sinatra' ),
					'description' => esc_html__( 'Devices where Main Footer is displayed.', 'sinatra' ),
					'section'     => 'sinatra_section_main_footer',
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Design Options heading.
			$options['setting']['sinatra_footer_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Background.
			$options['setting']['sinatra_footer_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
							'image'    => esc_html__( 'Image', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Text Color.
			$options['setting']['sinatra_footer_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
					'display'  => array(
						'color' => array(
							'text-color'         => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'         => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color'   => esc_html__( 'Link Hover Color', 'sinatra' ),
							'widget-title-color' => esc_html__( 'Widget Title Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Border.
			$options['setting']['sinatra_footer_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
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
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer typography heading.
			$options['setting']['sinatra_typography_main_footer_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_main_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer widget title font size.
			$options['setting']['sinatra_footer_widget_title_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Widget Title Font Size', 'sinatra' ),
					'description' => esc_html__( 'Choose your widget title font size.', 'sinatra' ),
					'section'     => 'sinatra_section_main_footer',
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
							'control'  => 'sinatra_typography_main_footer_heading',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_footer',
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
new Sinatra_Customizer_Main_Footer();
