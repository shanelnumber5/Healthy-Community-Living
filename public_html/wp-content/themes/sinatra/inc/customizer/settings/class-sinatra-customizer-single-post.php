<?php
/**
 * Sinatra Blog - Single Post section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Single_Post' ) ) :
	/**
	 * Sinatra Blog - Single Post section in Customizer.
	 */
	class Sinatra_Customizer_Single_Post {

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
			$options['section']['sinatra_section_blog_single_post'] = array(
				'title'    => esc_html__( 'Single Post', 'sinatra' ),
				'panel'    => 'sinatra_panel_blog',
				'priority' => 20,
			);

			// Single post layout.
			$options['setting']['sinatra_single_post_layout_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Layout', 'sinatra' ),
					'section' => 'sinatra_section_blog_single_post',
				),
			);

			// Content Layout.
			$options['setting']['sinatra_single_title_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Title Position', 'sinatra' ),
					'description' => esc_html__( 'Select title position for single post pages.', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'choices'     => array(
						'in-content'     => esc_html__( 'In Content', 'sinatra' ),
						'in-page-header' => esc_html__( 'In Page Header', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Alignment.
			$options['setting']['sinatra_single_title_alignment'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'     => 'sinatra-alignment',
					'label'    => esc_html__( 'Title Alignment', 'sinatra' ),
					'section'  => 'sinatra_section_blog_single_post',
					'choices'  => 'horizontal',
					'icons'    => array(
						'left'   => 'dashicons dashicons-editor-alignleft',
						'center' => 'dashicons dashicons-editor-aligncenter',
						'right'  => 'dashicons dashicons-editor-alignright',
					),
					'required' => array(
						array(
							'control'  => 'sinatra_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['sinatra_single_title_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-spacing',
					'label'       => esc_html__( 'Title Spacing', 'sinatra' ),
					'description' => esc_html__( 'Specify title top and bottom padding.', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
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
							'control'  => 'sinatra_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_single_title_position',
							'value'    => 'in-page-header',
							'operator' => '==',
						),
					),
				),
			);

			// Content width.
			$options['setting']['sinatra_single_content_width'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Content Width', 'sinatra' ),
					'description' => esc_html__( 'Narrow content width or match your site&rsquo;s Content Width (defined in General Settings &raquo; Layout).', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'choices'     => array(
						'wide'   => esc_html__( 'Content Width', 'sinatra' ),
						'narrow' => esc_html__( 'Narrow Width', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Narrow container width.
			$options['setting']['sinatra_single_narrow_container_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Narrow Container Width', 'sinatra' ),
					'description' => esc_html__( 'Choose the width (in px) for narrow container on single posts.', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'min'         => 500,
					'max'         => 1500,
					'step'        => 10,
					'required'    => array(
						array(
							'control'  => 'sinatra_single_content_width',
							'value'    => 'narrow',
							'operator' => '==',
						),
						array(
							'control'  => 'sinatra_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Single post elements.
			$options['setting']['sinatra_single_post_elements_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Post Elements', 'sinatra' ),
					'section' => 'sinatra_section_blog_single_post',
				),
			);

			$options['setting']['sinatra_single_post_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_sortable',
				'control'           => array(
					'type'        => 'sinatra-sortable',
					'section'     => 'sinatra_section_blog_single_post',
					'label'       => esc_html__( 'Post Elements', 'sinatra' ),
					'description' => esc_html__( 'Set visibility of post elements.', 'sinatra' ),
					'sortable'    => false,
					'choices'     => array(
						'thumb'          => esc_html__( 'Featured Image', 'sinatra' ),
						'category'       => esc_html__( 'Post Categories', 'sinatra' ),
						'tags'           => esc_html__( 'Post Tags', 'sinatra' ),
						'last-updated'   => esc_html__( 'Last Updated Date', 'sinatra' ),
						'about-author'   => esc_html__( 'About Author Box', 'sinatra' ),
						'prev-next-post' => esc_html__( 'Next/Prev Post Links', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Meta/Post Details Layout.
			$options['setting']['sinatra_single_post_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_sortable',
				'control'           => array(
					'type'        => 'sinatra-sortable',
					'label'       => esc_html__( 'Post Meta', 'sinatra' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'sinatra' ),
						'date'     => esc_html__( 'Publish Date', 'sinatra' ),
						'comments' => esc_html__( 'Comments', 'sinatra' ),
						'category' => esc_html__( 'Categories', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Meta icons.
			$options['setting']['sinatra_single_entry_meta_icons'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'     => 'sinatra-toggle',
					'section'  => 'sinatra_section_blog_single_post',
					'label'    => esc_html__( 'Show avatar and icons in post meta', 'sinatra' ),
					'required' => array(
						array(
							'control'  => 'sinatra_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Toggle Comments.
			$options['setting']['sinatra_single_toggle_comments'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Show Toggle Comments', 'sinatra' ),
					'description' => esc_html__( 'Hide comments and comment form behind a toggle button. ', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'required'    => array(
						array(
							'control'  => 'sinatra_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Single Post typography heading.
			$options['setting']['sinatra_typography_single_post_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Typography', 'sinatra' ),
					'section' => 'sinatra_section_blog_single_post',
				),
			);

			// Single post content font size.
			$options['setting']['sinatra_single_content_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Post Content Font Size', 'sinatra' ),
					'description' => esc_html__( 'Choose your single post content font size.', 'sinatra' ),
					'section'     => 'sinatra_section_blog_single_post',
					'responsive'  => true,
					'unit'        => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 30,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 1.875,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 1.875,
							'step' => 0.01,
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_typography_single_post_heading',
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
new Sinatra_Customizer_Single_Post();
