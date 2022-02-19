<?php
/**
 * Sinatra Copyright Bar section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Copyright_Settings' ) ) :
	/**
	 * Sinatra Copyright Bar section in Customizer.
	 */
	class Sinatra_Customizer_Copyright_Settings {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Registers our custom options in Customizer.
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
			$options['section']['sinatra_section_copyright_bar'] = array(
				'title'    => esc_html__( 'Copyright Bar', 'sinatra' ),
				'priority' => 30,
				'panel'    => 'sinatra_panel_footer',
			);

			// Enable Copyright Bar.
			$options['setting']['sinatra_enable_copyright'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'label'   => esc_html__( 'Enable Copyright Bar', 'sinatra' ),
					'section' => 'sinatra_section_copyright_bar',
				),
			);

			// Copyright Layout.
			$options['setting']['sinatra_copyright_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-radio-image',
					'section'     => 'sinatra_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Layout', 'sinatra' ),
					'description' => esc_html__( 'Choose your site&rsquo;s copyright widgets layout.', 'sinatra' ),
					'choices'     => array(
						'layout-1' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/copyright-layout-1.svg',
							'title' => esc_html__( 'Centered', 'sinatra' ),
						),
						'layout-2' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/copyright-layout-2.svg',
							'title' => esc_html__( 'Inline', 'sinatra' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Enable Copyright Bar.
			$options['setting']['sinatra_copyright_separator'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Separator', 'sinatra' ),
					'description' => esc_html__( 'Select type of Copyright Separator.', 'sinatra' ),
					'choices'     => array(
						'none'                => esc_html__( 'None', 'sinatra' ),
						'contained-separator' => esc_html__( 'Contained Separator', 'sinatra' ),
						'fw-separator'        => esc_html__( 'Fullwidth Separator', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright visibility.
			$options['setting']['sinatra_copyright_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_copyright_bar',
					'label'       => esc_html__( 'Device Visibility', 'sinatra' ),
					'description' => esc_html__( 'Devices where Copyright Bar is displayed.', 'sinatra' ),
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets heading.
			$options['setting']['sinatra_copyright_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-heading',
					'section'     => 'sinatra_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Bar Widgets', 'sinatra' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Copyright Bar.', 'sinatra' ),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets.
			$options['setting']['sinatra_copyright_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_widget',
				'control'           => array(
					'type'       => 'sinatra-widget',
					'section'    => 'sinatra_section_copyright_bar',
					'label'      => esc_html__( 'Copyright Bar Widgets', 'sinatra' ),
					'widgets'    => array(
						'text'    => array(
							'max_uses' => 3,
						),
						'nav'     => array(
							'menu_location' => apply_filters( 'sinatra_footer_menu_location', 'sinatra-footer' ),
							'max_uses'      => 1,
						),
						'socials' => array(
							'max_uses' => 1,
							'styles'   => array(
								'minimal' => esc_html__( 'Minimal', 'sinatra' ),
								'rounded' => esc_html__( 'Rounded', 'sinatra' ),
							),
						),
					),
					'locations'  => array(
						'start' => esc_html__( 'Start', 'sinatra' ),
						'end'   => esc_html__( 'End', 'sinatra' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'   => array(
						array(
							'control'  => 'sinatra_copyright_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#sinatra-copyright',
					'render_callback'     => 'sinatra_copyright_bar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Copyright design options heading.
			$options['setting']['sinatra_copyright_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'section'  => 'sinatra_section_copyright_bar',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'required' => array(
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright Background.
			$options['setting']['sinatra_copyright_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'section'  => 'sinatra_section_copyright_bar',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'space'    => true,
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_copyright_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright Text Color.
			$options['setting']['sinatra_copyright_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'        => 'sinatra-design-options',
					'section'     => 'sinatra_section_copyright_bar',
					'label'       => esc_html__( 'Font Color', 'sinatra' ),
					'description' => '',
					'space'       => true,
					'display'     => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_copyright_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_copyright',
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
new Sinatra_Customizer_Copyright_Settings();
