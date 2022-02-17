<?php
/**
 * Sinatra Top Bar Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Top_Bar' ) ) :
	/**
	 * Sinatra Top Bar Settings section in Customizer.
	 */
	class Sinatra_Customizer_Top_Bar {

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
			$options['section']['sinatra_section_top_bar'] = array(
				'title'    => esc_html__( 'Top Bar', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 10,
			);

			// Enable Top Bar.
			$options['setting']['sinatra_top_bar_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Enable Top Bar', 'sinatra' ),
					'description' => esc_html__( 'Top Bar is a section with widgets located above Main Header area.', 'sinatra' ),
					'section'     => 'sinatra_section_top_bar',
				),
			);

			// Top Bar container width.
			$options['setting']['sinatra_top_bar_container_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Top Bar Width', 'sinatra' ),
					'description' => esc_html__( 'Stretch the Top Bar container to full width, or match your site&rsquo;s content width.', 'sinatra' ),
					'section'     => 'sinatra_section_top_bar',
					'choices'     => array(
						'content-width' => esc_html__( 'Content Width', 'sinatra' ),
						'full-width'    => esc_html__( 'Full Width', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar visibility.
			$options['setting']['sinatra_top_bar_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Device Visibility', 'sinatra' ),
					'description' => esc_html__( 'Devices where the Top Bar is displayed.', 'sinatra' ),
					'section'     => 'sinatra_section_top_bar',
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar widgets heading.
			$options['setting']['sinatra_top_bar_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-heading',
					'label'       => esc_html__( 'Top Bar Widgets', 'sinatra' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Top Bar.', 'sinatra' ),
					'section'     => 'sinatra_section_top_bar',
					'required'    => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar widgets.
			$options['setting']['sinatra_top_bar_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_widget',
				'control'           => array(
					'type'       => 'sinatra-widget',
					'label'      => esc_html__( 'Top Bar Widgets', 'sinatra' ),
					'section'    => 'sinatra_section_top_bar',
					'widgets'    => array(
						'text'    => array(
							'max_uses' => 3,
						),
						'nav'     => array(
							'max_uses' => 1,
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
							'control'  => 'sinatra_top_bar_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#sinatra-topbar',
					'render_callback'     => 'sinatra_topbar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Top Bar widget separator.
			$options['setting']['sinatra_top_bar_widgets_separator'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Widgets Separator', 'sinatra' ),
					'description' => esc_html__( 'Display a separator line between widgets.', 'sinatra' ),
					'section'     => 'sinatra_section_top_bar',
					'choices'     => array(
						'none'    => esc_html__( 'None', 'sinatra' ),
						'regular' => esc_html__( 'Regular', 'sinatra' ),
						'slanted' => esc_html__( 'Slanted', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_top_bar_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar design options heading.
			$options['setting']['sinatra_top_bar_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'section'  => 'sinatra_section_top_bar',
					'required' => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Background.
			$options['setting']['sinatra_top_bar_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_top_bar',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_top_bar_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Text Color.
			$options['setting']['sinatra_top_bar_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_top_bar',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_top_bar_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Border.
			$options['setting']['sinatra_top_bar_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_top_bar',
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
							'control'  => 'sinatra_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_top_bar_heading_design_options',
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
new Sinatra_Customizer_Top_Bar();
