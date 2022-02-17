<?php
/**
 * Sinatra Hero Section Settings section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Hero' ) ) :
	/**
	 * Sinatra Page Title Settings section in Customizer.
	 */
	class Sinatra_Customizer_Hero {

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

			// Hero Section.
			$options['section']['sinatra_section_hero'] = array(
				'title'    => esc_html__( 'Hero', 'sinatra' ),
				'priority' => 3,
			);

			// Hero enable.
			$options['setting']['sinatra_enable_hero'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-toggle',
					'section' => 'sinatra_section_hero',
					'label'   => esc_html__( 'Enable Hero Section', 'sinatra' ),
				),
			);

			// Visibility.
			$options['setting']['sinatra_hero_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Device Visibility', 'sinatra' ),
					'description' => esc_html__( 'Devices where the Hero is displayed.', 'sinatra' ),
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'sinatra' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'sinatra' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'sinatra' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hero display on.
			$options['setting']['sinatra_hero_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_no_sanitize',
				'control'           => array(
					'type'        => 'sinatra-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'sinatra' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Hero. ', 'sinatra' ),
					'section'     => 'sinatra_section_hero',
					'choices'     => array(
						'home'       => array(
							'title' => esc_html__( 'Home Page', 'sinatra' ),
						),
						'posts_page' => array(
							'title' => esc_html__( 'Blog / Posts Page', 'sinatra' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider heading.
			$options['setting']['sinatra_hero_hover_slider'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'section'  => 'sinatra_section_hero',
					'label'    => esc_html__( 'Style', 'sinatra' ),
					'required' => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider container width.
			$options['setting']['sinatra_hero_hover_slider_container'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Width', 'sinatra' ),
					'description' => esc_html__( 'Stretch the container to full width, or match your site&rsquo;s content width.', 'sinatra' ),
					'choices'     => array(
						'content-width' => esc_html__( 'Content Width', 'sinatra' ),
						'full-width'    => esc_html__( 'Full Width', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider height.
			$options['setting']['sinatra_hero_hover_slider_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Height', 'sinatra' ),
					'description' => esc_html__( 'Set the height of the container.', 'sinatra' ),
					'min'         => 350,
					'max'         => 1000,
					'step'        => 1,
					'unit'        => 'px',
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider overlay.
			$options['setting']['sinatra_hero_hover_slider_overlay'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Overlay', 'sinatra' ),
					'description' => esc_html__( 'Choose hero overlay style.', 'sinatra' ),
					'choices'     => array(
						'none' => esc_html__( 'None', 'sinatra' ),
						'1'    => esc_html__( 'Overlay 1', 'sinatra' ),
						'2'    => esc_html__( 'Overlay 2', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider Elements.
			$options['setting']['sinatra_hero_hover_slider_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_sortable',
				'control'           => array(
					'type'        => 'sinatra-sortable',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Post Elements', 'sinatra' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'sinatra' ),
					'sortable'    => false,
					'choices'     => array(
						'category'  => esc_html__( 'Categories', 'sinatra' ),
						'meta'      => esc_html__( 'Post Details', 'sinatra' ),
						'read_more' => esc_html__( 'Continue Reading', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'sinatra_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post Settings heading.
			$options['setting']['sinatra_hero_hover_slider_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-heading',
					'section'  => 'sinatra_section_hero',
					'label'    => esc_html__( 'Post Settings', 'sinatra' ),
					'required' => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['sinatra_hero_hover_slider_post_number'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Post Number', 'sinatra' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'sinatra' ),
					'min'         => 1,
					'max'         => 4,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'sinatra_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post category.
			$options['setting']['sinatra_hero_hover_slider_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_hero',
					'label'       => esc_html__( 'Category', 'sinatra' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'sinatra' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'sinatra_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_hover_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_hero_type',
							'value'    => 'hover-slider',
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Sinatra_Customizer_Hero();
