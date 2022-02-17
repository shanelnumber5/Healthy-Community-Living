<?php
/**
 * Sinatra Pre Footer section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Pre_Footer' ) ) :
	/**
	 * Sinatra Pre Footer section in Customizer.
	 */
	class Sinatra_Customizer_Pre_Footer {

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

			// Pre Footer.
			$options['section']['sinatra_section_pre_footer'] = array(
				'title'    => esc_html__( 'Pre Footer', 'sinatra' ),
				'panel'    => 'sinatra_panel_footer',
				'priority' => 10,
			);

			// Pre Footer - Call to Action.
			$options['setting']['sinatra_pre_footer_cta'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Call to Action', 'sinatra' ),
					'section' => 'sinatra_section_pre_footer',
				),
			);

			// Enable Pre Footer CTA.
			$options['setting']['sinatra_enable_pre_footer_cta'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'label'    => esc_html__( 'Enable Call to Action', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#si-pre-footer',
					'render_callback'     => 'sinatra_pre_footer',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Pre Footer visibility.
			$options['setting']['sinatra_pre_footer_cta_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Device Visibility', 'sinatra' ),
					'description' => esc_html__( 'Devices where the Top Bar is displayed.', 'sinatra' ),
					'section'     => 'sinatra_section_pre_footer',
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer Hide on.
			$options['setting']['sinatra_pre_footer_cta_hide_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_no_sanitize',
				'control'           => array(
					'type'        => 'sinatra-checkbox-group',
					'label'       => esc_html__( 'Disable On: ', 'sinatra' ),
					'description' => esc_html__( 'Choose on which pages you want to disable Pre Footer Call to Action. ', 'sinatra' ),
					'section'     => 'sinatra_section_pre_footer',
					'choices'     => sinatra_get_display_choices(),
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer CTA Style.
			$options['setting']['sinatra_pre_footer_cta_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Style', 'sinatra' ),
					'description' => esc_html__( 'Choose CTA Style.', 'sinatra' ),
					'section'     => 'sinatra_section_pre_footer',
					'choices'     => array(
						'1' => esc_html__( 'Contained', 'sinatra' ),
						'2' => esc_html__( 'Fullwidth', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer CTA Text.
			$options['setting']['sinatra_pre_footer_cta_text'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_textarea',
				'control'           => array(
					'type'        => 'sinatra-textarea',
					'label'       => esc_html__( 'Content', 'sinatra' ),
					'description' => esc_html__( 'Shortcodes and basic html elements allowed.', 'sinatra' ),
					'placeholder' => esc_html__( 'Call to Action Content', 'sinatra' ),
					'section'     => 'sinatra_section_pre_footer',
					'rows'        => '5',
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer CTA Button Text.
			$options['setting']['sinatra_pre_footer_cta_btn_text'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'sinatra-text',
					'label'       => esc_html__( 'Button Text', 'sinatra' ),
					'description' => esc_html__( 'Label for the CTA button.', 'sinatra' ),
					'section'     => 'sinatra_section_pre_footer',
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer CTA Button URL.
			$options['setting']['sinatra_pre_footer_cta_btn_url'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'sinatra-text',
					'label'       => esc_html__( 'Button Link', 'sinatra' ),
					'description' => esc_html__( 'Link for the CTA button.', 'sinatra' ),
					'placeholder' => 'http://',
					'section'     => 'sinatra_section_pre_footer',
					'required'    => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer CTA open in new tab.
			$options['setting']['sinatra_pre_footer_cta_btn_new_tab'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'label'    => esc_html__( 'Open link in new tab?', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer - Call to Action Design Options.
			$options['setting']['sinatra_pre_footer_cta_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Design Options', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer - Call to Action Background.
			$options['setting']['sinatra_pre_footer_cta_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'sinatra' ),
							'gradient' => esc_html__( 'Gradient', 'sinatra' ),
							'image'    => esc_html__( 'Image', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_pre_footer_cta_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer - Call to Action Text Color.
			$options['setting']['sinatra_pre_footer_cta_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_pre_footer_cta_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Pre Footer - Call to Action Border.
			$options['setting']['sinatra_pre_footer_cta_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'label'    => esc_html__( 'Border', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'display'  => array(
						'border' => array(
							'style'     => esc_html__( 'Style', 'sinatra' ),
							'color'     => esc_html__( 'Color', 'sinatra' ),
							'width'     => esc_html__( 'Width (px)', 'sinatra' ),
							'positions' => array(
								'top'    => esc_html__( 'Top', 'sinatra' ),
								'right'  => esc_html__( 'Right', 'sinatra' ),
								'bottom' => esc_html__( 'Bottom', 'sinatra' ),
								'left'   => esc_html__( 'Left', 'sinatra' ),
							),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_pre_footer_cta_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// CTA typography heading.
			$options['setting']['sinatra_pre_footer_cta_typography'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_pre_footer',
					'required' => array(
						array(
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// CTA font size.
			$options['setting']['sinatra_pre_footer_cta_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'       => 'sinatra-range',
					'label'      => esc_html__( 'Font Size', 'sinatra' ),
					'section'    => 'sinatra_section_pre_footer',
					'min'        => 8,
					'max'        => 90,
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
							'control'  => 'sinatra_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_enable_pre_footer_cta',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_pre_footer_cta_typography',
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
new Sinatra_Customizer_Pre_Footer();
