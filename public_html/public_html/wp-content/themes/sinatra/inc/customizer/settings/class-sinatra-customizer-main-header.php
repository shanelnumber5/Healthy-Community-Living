<?php
/**
 * Sinatra Main Header Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Main_Header' ) ) :
	/**
	 * Sinatra Main Header section in Customizer.
	 */
	class Sinatra_Customizer_Main_Header {

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

			// Main Header Section.
			$options['section']['sinatra_section_main_header'] = array(
				'title'    => esc_html__( 'Main Header', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 20,
			);

			// Header Layout.
			$options['setting']['sinatra_header_layout'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-radio-image',
					'label'       => esc_html__( 'Header Layout', 'sinatra' ),
					'description' => esc_html__( 'Pre-defined positions of header elements, such as logo and navigation.', 'sinatra' ),
					'section'     => 'sinatra_section_main_header',
					'choices'     => array(
						'layout-1' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/header-layout-1.svg',
							'title' => esc_html__( 'Header 1', 'sinatra' ),
						),
						'layout-2' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/header-layout-2.svg',
							'title' => esc_html__( 'Header 2', 'sinatra' ),
						),
						'layout-3' => array(
							'image' => SINATRA_THEME_URI . '/inc/customizer/assets/images/header-layout-3.svg',
							'title' => esc_html__( 'Header 3', 'sinatra' ),
						),
					),
				),
			);

			// Header container width.
			$options['setting']['sinatra_header_container_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Header Width', 'sinatra' ),
					'description' => esc_html__( 'Stretch the Header container to full width, or match your site&rsquo;s content width.', 'sinatra' ),
					'section'     => 'sinatra_section_main_header',
					'choices'     => array(
						'content-width' => esc_html__( 'Content Width', 'sinatra' ),
						'full-width'    => esc_html__( 'Full Width', 'sinatra' ),
					),
				),
			);

			// Header widgets heading.
			$options['setting']['sinatra_header_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-heading',
					'label'       => esc_html__( 'Header Widgets', 'sinatra' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Header. Click the down arrow icon to expand widget options.', 'sinatra' ),
					'section'     => 'sinatra_section_main_header',
					'space'       => true,
				),
			);

			// Header widgets.
			$options['setting']['sinatra_header_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_widget',
				'control'           => array(
					'type'       => 'sinatra-widget',
					'label'      => esc_html__( 'Header Widgets', 'sinatra' ),
					'section'    => 'sinatra_section_main_header',
					'widgets'    => apply_filters(
						'sinatra_main_header_widgets',
						array(
							'search' => array(
								'max_uses' => 1,
							),
							'button' => array(
								'max_uses' => 1,
							),
						)
					),
					'locations'  => array(
						'left'  => esc_html__( 'Left', 'sinatra' ),
						'right' => esc_html__( 'Right', 'sinatra' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'   => array(
						array(
							'control'  => 'sinatra_header_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#sinatra-header',
					'render_callback'     => 'sinatra_header_content_output',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Header widget separator.
			$options['setting']['sinatra_header_widgets_separator'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Widgets Separator', 'sinatra' ),
					'description' => esc_html__( 'Display a separator line between widgets.', 'sinatra' ),
					'section'     => 'sinatra_section_main_header',
					'choices'     => array(
						'none'    => esc_html__( 'None', 'sinatra' ),
						'regular' => esc_html__( 'Regular', 'sinatra' ),
						'slanted' => esc_html__( 'Slanted', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_header_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Header design options heading.
			$options['setting']['sinatra_header_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Design Options', 'sinatra' ),
					'section' => 'sinatra_section_main_header',
					'space'   => true,
				),
			);

			// Header Background.
			$options['setting']['sinatra_header_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'        => 'sinatra-design-options',
					'label'       => esc_html__( 'Background', 'sinatra' ),
					'description' => '',
					'section'     => 'sinatra_section_main_header',
					'space'       => true,
					'display'     => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
							'image'    => esc_html__( 'Image', 'sinatra' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_header_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Header Text Color.
			$options['setting']['sinatra_header_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_main_header',
					'space'    => true,
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Tagline Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_header_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Header Border.
			$options['setting']['sinatra_header_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_main_header',
					'space'    => true,
					'display'  => array(
						'border' => array(
							'style'     => esc_html__( 'Style', 'sinatra' ),
							'color'     => esc_html__( 'Color', 'sinatra' ),
							'width'     => esc_html__( 'Width (px)', 'sinatra' ),
							'positions' => array(
								'top'    => esc_html__( 'Top', 'sinatra' ),
								'bottom' => esc_html__( 'Bottom', 'sinatra' ),
							),
							'separator' => esc_html__( 'Separator Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_header_heading_design_options',
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
new Sinatra_Customizer_Main_Header();
