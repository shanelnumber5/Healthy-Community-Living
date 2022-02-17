<?php
/**
 * Sinatra Breadcrumbs Settings section in Customizer.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.1.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Customizer_Breadcrumbs' ) ) :
	/**
	 * Sinatra Breadcrumbs Settings section in Customizer.
	 */
	class Sinatra_Customizer_Breadcrumbs {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.1.0
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
		 * @since 1.1.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Main Navigation Section.
			$options['section']['sinatra_section_breadcrumbs'] = array(
				'title'    => esc_html__( 'Breadcrumbs', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 70,
			);

			// Breadcrumbs.
			$options['setting']['sinatra_breadcrumbs_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'label'    => esc_html__( 'Enable Breadcrumbs', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
				),
			);

			// Hide breadcrumbs on.
			$options['setting']['sinatra_breadcrumbs_hide_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_no_sanitize',
				'control'           => array(
					'type'        => 'sinatra-checkbox-group',
					'label'       => esc_html__( 'Disable On: ', 'sinatra' ),
					'description' => esc_html__( 'Choose on which pages you want to disable breadcrumbs. ', 'sinatra' ),
					'section'     => 'sinatra_section_breadcrumbs',
					'choices'     => sinatra_get_display_choices(),
					'required'    => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Position.
			$options['setting']['sinatra_breadcrumbs_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'     => 'sinatra-select',
					'label'    => esc_html__( 'Position', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
					'choices'  => array(
						'in-page-header' => esc_html__( 'In Page Header', 'sinatra' ),
						'below-header'   => esc_html__( 'Below Header (Separate Container)', 'sinatra' ),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Alignment.
			$options['setting']['sinatra_breadcrumbs_alignment'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'     => 'sinatra-alignment',
					'label'    => esc_html__( 'Alignment', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
					'choices'  => 'horizontal',
					'icons'    => array(
						'left'   => 'dashicons dashicons-editor-alignleft',
						'center' => 'dashicons dashicons-editor-aligncenter',
						'right'  => 'dashicons dashicons-editor-alignright',
					),
					'required' => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['sinatra_breadcrumbs_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Spacing', 'sinatra' ),
					'description' => esc_html__( 'Specify top and bottom padding.', 'sinatra' ),
					'section'     => 'sinatra_section_breadcrumbs',
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
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Design options heading.
			$options['setting']['sinatra_breadcrumbs_heading_design'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
					'required' => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Background design.
			$options['setting']['sinatra_breadcrumbs_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
							'image'    => esc_html__( 'Image', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Text Color.
			$options['setting']['sinatra_breadcrumbs_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Border.
			$options['setting']['sinatra_breadcrumbs_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_breadcrumbs',
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
							'control'  => 'sinatra_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Sinatra_Customizer_Breadcrumbs();
