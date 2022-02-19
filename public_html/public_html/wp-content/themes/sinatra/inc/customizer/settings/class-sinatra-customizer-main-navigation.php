<?php
/**
 * Sinatra Main Navigation Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Main_Navigation' ) ) :
	/**
	 * Sinatra Main Navigation Settings section in Customizer.
	 */
	class Sinatra_Customizer_Main_Navigation {

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

			// Main Navigation Section.
			$options['section']['sinatra_section_main_navigation'] = array(
				'title'    => esc_html__( 'Main Navigation', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 30,
			);

			// Navigation animation heading.
			$options['setting']['sinatra_main_nav_heading_animation'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Animation', 'sinatra' ),
					'section' => 'sinatra_section_main_navigation',
				),
			);

			// Hover animation.
			$options['setting']['sinatra_main_nav_hover_animation'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Hover Animation', 'sinatra' ),
					'description' => esc_html__( 'Choose menu item hover animation style.', 'sinatra' ),
					'section'     => 'sinatra_section_main_navigation',
					'choices'     => array(
						'none'      => esc_html__( 'None', 'sinatra' ),
						'underline' => esc_html__( 'Underline', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_main_nav_heading_animation',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sub Menus heading.
			$options['setting']['sinatra_main_nav_heading_sub_menus'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Sub Menus', 'sinatra' ),
					'section' => 'sinatra_section_main_navigation',
				),
			);

			// Sub-Menu Indicators.
			$options['setting']['sinatra_main_nav_sub_indicators'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Dropdown Indicators', 'sinatra' ),
					'description' => esc_html__( 'Show indicators (arrow icons) on parent menu items that have sub menus.', 'sinatra' ),
					'section'     => 'sinatra_section_main_navigation',
					'required'    => array(
						array(
							'control'  => 'sinatra_main_nav_heading_sub_menus',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '.main-navigation',
					'render_callback'     => 'sinatra_main_navigation_template',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Mobile Menu heading.
			$options['setting']['sinatra_main_nav_heading_mobile_menu'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Mobile Menu', 'sinatra' ),
					'section' => 'sinatra_section_main_navigation',
				),
			);

			// Mobile Menu Breakpoint.
			$options['setting']['sinatra_main_nav_mobile_breakpoint'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Mobile Breakpoint', 'sinatra' ),
					'description' => esc_html__( 'Choose the breakpoint (in px) when to show the mobile navigation.', 'sinatra' ),
					'section'     => 'sinatra_section_main_navigation',
					'min'         => 0,
					'max'         => 1920,
					'step'        => 1,
					'unit'        => 'px',
					'required'    => array(
						array(
							'control'  => 'sinatra_main_nav_heading_mobile_menu',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Mobile Menu Button Label.
			$options['setting']['sinatra_main_nav_mobile_label'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'sinatra-text',
					'label'       => esc_html__( 'Mobile Menu Button Label', 'sinatra' ),
					'description' => esc_html__( 'This text will be displayed next to the mobile menu button.', 'sinatra' ),
					'section'     => 'sinatra_section_main_navigation',
					'placeholder' => esc_html__( 'Leave empty to hide the label...', 'sinatra' ),
					'required'    => array(
						array(
							'control'  => 'sinatra_main_nav_heading_mobile_menu',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Navigation design options heading.
			$options['setting']['sinatra_nav_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Design Options', 'sinatra' ),
					'section' => 'sinatra_section_main_navigation',
				),
			);

			// Navigation Background.
			$options['setting']['sinatra_main_nav_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_main_navigation',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_nav_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_header_layout',
							'value'    => 'layout-3',
							'operator' => '==',
						),
					),
				),
			);

			// Navigation Font Color.
			$options['setting']['sinatra_main_nav_font_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_main_navigation',
					'display'  => array(
						'color' => array(
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_nav_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Navigation Border.
			$options['setting']['sinatra_main_nav_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_main_navigation',
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
							'control'  => 'sinatra_nav_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_header_layout',
							'value'    => 'layout-3',
							'operator' => '==',
						),
					),
				),
			);

			// Main navigation typography heading.
			$options['setting']['sinatra_typography_main_nav_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Typography', 'sinatra' ),
					'section' => 'sinatra_section_main_navigation',
				),
			);

			// Main navigation font size.
			$options['setting']['sinatra_main_nav_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Font Size', 'sinatra' ),
					'description' => esc_html__( 'Choose your main navigation font size.', 'sinatra' ),
					'section'     => 'sinatra_section_main_navigation',
					'unit'        => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 25,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 2,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 2,
							'step' => 0.01,
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_typography_main_nav_heading',
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
new Sinatra_Customizer_Main_Navigation();
