<?php
/**
 * Buttons section in Customizer » General Settings.
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

if ( ! class_exists( 'Sinatra_Customizer_Buttons' ) ) :
	/**
	 * Buttons section in Customizer » General Settings.
	 */
	class Sinatra_Customizer_Buttons {

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
			$options['section']['sinatra_section_buttons'] = array(
				'title'    => esc_html__( 'Buttons', 'sinatra' ),
				'panel'    => 'sinatra_panel_general',
				'priority' => 60,
			);

			/**
			 * Primary Button
			 */

			$options['setting']['sinatra_primary_button_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Primary Button', 'sinatra' ),
					'section' => 'sinatra_section_buttons',
				),
			);

			// Primary button background color.
			$options['setting']['sinatra_primary_button_bg_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Background Color', 'sinatra' ),
					'description' => esc_html__( 'Set primary button background color. If left empty, accent color will be used instead.', 'sinatra' ),
					'section'     => 'sinatra_section_buttons',
					'opacity'     => true,
					'required'    => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button hover background color.
			$options['setting']['sinatra_primary_button_hover_bg_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Hover Background Color', 'sinatra' ),
					'description' => esc_html__( 'Set primary button hover background color. If left empty, lightened accent color will be used instead.', 'sinatra' ),
					'section'     => 'sinatra_section_buttons',
					'opacity'     => true,
					'required'    => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button text color.
			$options['setting']['sinatra_primary_button_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button text hover color.
			$options['setting']['sinatra_primary_button_hover_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Hover Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button border width.
			$options['setting']['sinatra_primary_button_border_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'     => 'sinatra-range',
					'section'  => 'sinatra_section_buttons',
					'label'    => esc_html__( 'Border Width', 'sinatra' ),
					'min'      => 0,
					'max'      => 15,
					'step'     => 1,
					'unit'     => 'px',
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button border radius.
			$options['setting']['sinatra_primary_button_border_radius'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Border Radius', 'sinatra' ),
					'description' => esc_html__( 'Specify primary button corner roundness. Top left, top right, bottom left and bottom right is the order of the corresponding corners.', 'sinatra' ),
					'section'     => 'sinatra_section_buttons',
					'choices'     => array(
						'top-left'     => '&nwarr;',
						'top-right'    => '&nearr;',
						'bottom-right' => '&searr;',
						'bottom-left'  => '&swarr;',
					),
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button border color.
			$options['setting']['sinatra_primary_button_border_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Border Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button hover border color.
			$options['setting']['sinatra_primary_button_hover_border_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Hover Border Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Primary button typography.
			$options['setting']['sinatra_primary_button_typography'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'     => 'sinatra-typography',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'display'  => array(
						'font-family'    => array(),
						'font-subsets'   => array(),
						'font-weight'    => array(),
						'text-transform' => array(),
						'letter-spacing' => array(),
						'font-size'      => array(),
						'line-height'    => array(),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_primary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			/**
			 * Secondary Button
			 */

			// Secondary button.
			$options['setting']['sinatra_secondary_button_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Secondary Button', 'sinatra' ),
					'section' => 'sinatra_section_buttons',
				),
			);

			// Secondary button background color.
			$options['setting']['sinatra_secondary_button_bg_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Background Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button hover background color.
			$options['setting']['sinatra_secondary_button_hover_bg_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Hover Background Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button text color.
			$options['setting']['sinatra_secondary_button_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button text hover color.
			$options['setting']['sinatra_secondary_button_hover_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Hover Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button border width.
			$options['setting']['sinatra_secondary_button_border_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'     => 'sinatra-range',
					'section'  => 'sinatra_section_buttons',
					'label'    => esc_html__( 'Border Width', 'sinatra' ),
					'min'      => 0,
					'max'      => 15,
					'step'     => 1,
					'unit'     => 'px',
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button border radius.
			$options['setting']['sinatra_secondary_button_border_radius'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Border Radius', 'sinatra' ),
					'description' => esc_html__( 'Specify secondary button corner roundness. Top left, top right, bottom left and bottom right is the order of the corresponding corners.', 'sinatra' ),
					'section'     => 'sinatra_section_buttons',
					'choices'     => array(
						'top-left'     => '&nwarr;',
						'top-right'    => '&nearr;',
						'bottom-right' => '&searr;',
						'bottom-left'  => '&swarr;',
					),
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button border color.
			$options['setting']['sinatra_secondary_button_border_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Border Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button hover border color.
			$options['setting']['sinatra_secondary_button_hover_border_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Hover Border Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Secondary button typography.
			$options['setting']['sinatra_secondary_button_typography'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'     => 'sinatra-typography',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'display'  => array(
						'font-family'    => array(),
						'font-subsets'   => array(),
						'font-weight'    => array(),
						'text-transform' => array(),
						'letter-spacing' => array(),
						'font-size'      => array(),
						'line-height'    => array(),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_secondary_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			/**
			 * Text Button
			 */

			$options['setting']['sinatra_text_button_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Text Button', 'sinatra' ),
					'section' => 'sinatra_section_buttons',
				),
			);

			// Text button text color.
			$options['setting']['sinatra_text_button_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'     => 'sinatra-color',
					'label'    => esc_html__( 'Text Color', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'opacity'  => true,
					'required' => array(
						array(
							'control'  => 'sinatra_text_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Text button text hover color.
			$options['setting']['sinatra_text_button_hover_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_color',
				'control'           => array(
					'type'        => 'sinatra-color',
					'label'       => esc_html__( 'Hover Text Color', 'sinatra' ),
					'description' => esc_html__( 'If left empty, accent color will be used instead.', 'sinatra' ),
					'section'     => 'sinatra_section_buttons',
					'opacity'     => true,
					'required'    => array(
						array(
							'control'  => 'sinatra_text_button_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Text button typography.
			$options['setting']['sinatra_text_button_typography'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_typography',
				'control'           => array(
					'type'     => 'sinatra-typography',
					'label'    => esc_html__( 'Typography', 'sinatra' ),
					'section'  => 'sinatra_section_buttons',
					'display'  => array(
						'font-family'    => array(),
						'font-subsets'   => array(),
						'font-weight'    => array(),
						'text-transform' => array(),
						'letter-spacing' => array(),
						'font-size'      => array(),
						'line-height'    => array(),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_text_button_heading',
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
new Sinatra_Customizer_Buttons();
