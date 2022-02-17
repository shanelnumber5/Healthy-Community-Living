<?php
/**
 * Sinatra Transparent Header Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Transparent_Header' ) ) :
	/**
	 * Sinatra Main Transparent section in Customizer.
	 */
	class Sinatra_Customizer_Transparent_Header {

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

			// Transparent Header Section.
			$options['section']['sinatra_section_transparent_header'] = array(
				'title'    => esc_html__( 'Transparent Header', 'sinatra' ),
				'panel'    => 'sinatra_panel_header',
				'priority' => 80,
			);

			// Enable Transparent Header.
			$options['setting']['sinatra_tsp_header'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'label'   => esc_html__( 'Enable Globally', 'sinatra' ),
					'section' => 'sinatra_section_transparent_header',
				),
			);

			// Disable choices.
			$disable_choices = array(
				'404' => array(
					'title' => esc_html__( '404 page', 'sinatra' ),
				),
				'posts_page' => array(
					'title' => esc_html__( 'Blog / Posts page', 'sinatra' ),
				),
				'archive' => array(
					'title' => esc_html__( 'Archive pages', 'sinatra' ),
				),
				'search' => array(
					'title' => esc_html__( 'Search pages', 'sinatra' ),
				),
				'post' => array(
					'title' => esc_html__( 'Posts', 'sinatra' ),
				),
				'page' => array(
					'title' => esc_html__( 'Pages', 'sinatra' ),
				),
			);

			// Get additionally registered post types.
			$post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				),
				'objects'
			);

			if ( is_array( $post_types ) && ! empty( $post_types ) ) {
				foreach ( $post_types as $slug => $post_type ) {
					$disable_choices[ $slug ] = array(
						'title' => $post_type->label,
					);
				}
			}

			// Transparent header display on.
			$options['setting']['sinatra_tsp_header_disable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_no_sanitize',
				'control'           => array(
					'type'        => 'sinatra-checkbox-group',
					'label'       => esc_html__( 'Disable On: ', 'sinatra' ),
					'description' => esc_html__( 'Choose on which pages you want to disable Transparent Header. ', 'sinatra' ),
					'section'     => 'sinatra_section_transparent_header',
					'choices'     => $disable_choices,
					'required'    => array(
						array(
							'control'  => 'sinatra_tsp_header',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Logo Settings Heading.
			$options['setting']['sinatra_tsp_logo_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Logo Settings', 'sinatra' ),
					'section'  => 'sinatra_section_transparent_header',
				),
			);

			// Logo.
			$options['setting']['sinatra_tsp_logo'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_background',
				'control'           => array(
					'type'        => 'sinatra-background',
					'section'     => 'sinatra_section_transparent_header',
					'label'       => esc_html__( 'Alternative Logo', 'sinatra' ),
					'description' => esc_html__( 'Upload a different logo to be used with Transparent Header.', 'sinatra' ),
					'advanced'    => false,
					'strings'     => array(
						'select_image' => __( 'Select logo', 'sinatra' ),
						'use_image'    => __( 'Select', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_tsp_logo_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '.sinatra-logo',
					'render_callback'     => 'sinatra_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Logo Retina.
			$options['setting']['sinatra_tsp_logo_retina'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_background',
				'control'           => array(
					'type'        => 'sinatra-background',
					'section'     => 'sinatra_section_transparent_header',
					'label'       => esc_html__( 'Alternative Logo - Retina', 'sinatra' ),
					'description' => esc_html__( 'Upload exactly 2x the size of your alternative logo to make your logo crisp on HiDPI screens. This options is not required if logo above is in SVG format.', 'sinatra' ),
					'advanced'    => false,
					'strings'     => array(
						'select_image' => __( 'Select logo', 'sinatra' ),
						'use_image'    => __( 'Select', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_tsp_logo_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '.sinatra-logo',
					'render_callback'     => 'sinatra_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Logo Max Height.
			$options['setting']['sinatra_tsp_logo_max_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Logo Height', 'sinatra' ),
					'description' => esc_html__( 'Maximum logo image height on transparent header.', 'sinatra' ),
					'section'     => 'sinatra_section_transparent_header',
					'min'         => 0,
					'max'         => 1000,
					'step'        => 10,
					'unit'        => 'px',
					'responsive'  => true,
					'required'    => array(
						array(
							'control'  => 'sinatra_tsp_logo_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Logo margin.
			$options['setting']['sinatra_tsp_logo_margin'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Logo Margin', 'sinatra' ),
					'description' => esc_html__( 'Specify spacing around logo on transparent header. Negative values are allowed. Leave empty to inherit from Logos & Site Title » Logo Margin.', 'sinatra' ),
					'section'     => 'sinatra_section_transparent_header',
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'sinatra' ),
						'right'  => esc_html__( 'Right', 'sinatra' ),
						'bottom' => esc_html__( 'Bottom', 'sinatra' ),
						'left'   => esc_html__( 'Left', 'sinatra' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_tsp_logo_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Custom Colors Heading.
			$options['setting']['sinatra_tsp_colors_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'label'    => esc_html__( 'Main Header Colors', 'sinatra' ),
					'section'  => 'sinatra_section_transparent_header',
				),
			);

			// Background.
			$options['setting']['sinatra_tsp_header_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'section'  => 'sinatra_section_transparent_header',
					'label'    => esc_html__( 'Background', 'sinatra' ),
					'space'    => true,
					'display'  => array(
						'background' => array(
							'color' => esc_html__( 'Solid Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_tsp_colors_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Text Color.
			$options['setting']['sinatra_tsp_header_font_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'section'  => 'sinatra_section_transparent_header',
					'label'    => esc_html__( 'Font Color', 'sinatra' ),
					'space'    => true,
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'sinatra' ),
							'link-color'       => esc_html__( 'Link Color', 'sinatra' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'sinatra' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'sinatra_tsp_colors_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Border.
			$options['setting']['sinatra_tsp_header_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_design_options',
				'control'           => array(
					'type'     => 'sinatra-design-options',
					'section'  => 'sinatra_section_transparent_header',
					'label'    => esc_html__( 'Border', 'sinatra' ),
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
							'control'  => 'sinatra_tsp_colors_heading',
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
new Sinatra_Customizer_Transparent_Header();
