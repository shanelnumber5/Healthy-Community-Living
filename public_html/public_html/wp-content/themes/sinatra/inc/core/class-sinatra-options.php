<?php
/**
 * Sinatra Options Class.
 *
 * @package  Sinatra
 * @author   Sinatra Team <hello@sinatrawp.com>
 * @since    1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Options' ) ) :

	/**
	 * Sinatra Options Class.
	 */
	class Sinatra_Options {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Options variable.
		 *
		 * @since 1.0.0
		 * @var mixed $options
		 */
		private static $options;

		/**
		 * Main Sinatra_Options Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Options
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Options ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Refresh options.
			add_action( 'after_setup_theme', array( $this, 'refresh' ) );
		}

		/**
		 * Set default option values.
		 *
		 * @since  1.0.0
		 * @return array Default values.
		 */
		public function get_defaults() {

			$defaults = array(

				/**
				 * General Settings.
				 */

				// Layout.
				'sinatra_site_layout'                      => 'fw-contained',
				'sinatra_container_width'                  => 1200,

				// Base Colors.
				'sinatra_accent_color'                     => '#3857F1',
				'sinatra_content_text_color'               => '#30373e',
				'sinatra_headings_color'                   => '#23282d',
				'sinatra_content_link_hover_color'         => '#23282d',
				'sinatra_body_background_heading'          => true,
				'sinatra_content_background_heading'       => true,
				'sinatra_boxed_content_background_color'   => '#FFFFFF',
				'sinatra_scroll_top_visibility'            => 'all',

				// Base Typography.
				'sinatra_html_base_font_size'              => array(
					'desktop' => 16,
				),
				'sinatra_font_smoothing'                   => true,
				'sinatra_typography_body_heading'          => false,
				'sinatra_typography_headings_heading'      => false,
				'sinatra_body_font'                        => sinatra_typography_defaults(
					array(
						'font-family'         => 'default',
						'font-weight'         => 400,
						'font-size-desktop'   => '0.9375',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.733',
					)
				),
				'sinatra_headings_font'                    => sinatra_typography_defaults(
					array(
						'font-weight'     => 500,
						'font-style'      => 'normal',
						'text-transform'  => 'none',
						'text-decoration' => 'none',
					)
				),
				'sinatra_h1_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 600,
						'font-size-desktop'   => '2.375',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.1',
					)
				),
				'sinatra_h2_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 'inherit',
						'font-size-desktop'   => '1.875',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.25',
					)
				),
				'sinatra_h3_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 'inherit',
						'font-size-desktop'   => '1.625',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.25',
					)
				),
				'sinatra_h4_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 'inherit',
						'font-size-desktop'   => '1.25',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.5',
					)
				),
				'sinatra_h5_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 'inherit',
						'font-size-desktop'   => '1',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.5',
					)
				),
				'sinatra_h6_font'                          => sinatra_typography_defaults(
					array(
						'font-weight'         => 'inherit',
						'font-size-desktop'   => '0.6875',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.72',
						'text-transform'      => 'uppercase',
						'letter-spacing'      => '2',
					)
				),
				'sinatra_heading_em_font'                  => sinatra_typography_defaults(
					array(
						'font-weight' => 'inherit',
						'font-style'  => 'italic',
					)
				),
				'sinatra_footer_widget_title_font_size'    => array(
					'desktop' => 1.125,
					'unit'    => 'em',
				),

				// Primary Button.
				'sinatra_primary_button_heading'           => false,
				'sinatra_primary_button_bg_color'          => '',
				'sinatra_primary_button_hover_bg_color'    => '',
				'sinatra_primary_button_text_color'        => '#FFFFFF',
				'sinatra_primary_button_hover_text_color'  => '#FFFFFF',
				'sinatra_primary_button_border_radius'     => array(
					'top-left'     => 2,
					'top-right'    => 2,
					'bottom-right' => 2,
					'bottom-left'  => 2,
					'unit'         => 'px',
				),
				'sinatra_primary_button_border_width'      => 1,
				'sinatra_primary_button_border_color'      => 'rgba(0, 0, 0, 0.12)',
				'sinatra_primary_button_hover_border_color' => 'rgba(0, 0, 0, 0.12)',
				'sinatra_primary_button_typography'        => sinatra_typography_defaults(
					array(
						'font-family'         => 'inherit',
						'font-weight'         => 500,
						'font-size-desktop'   => '0.9375',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),

				// Secondary Button.
				'sinatra_secondary_button_heading'         => false,
				'sinatra_secondary_button_bg_color'        => '#23282d',
				'sinatra_secondary_button_hover_bg_color'  => '#3e4750',
				'sinatra_secondary_button_text_color'      => '#FFFFFF',
				'sinatra_secondary_button_hover_text_color' => '#FFFFFF',
				'sinatra_secondary_button_border_radius'   => array(
					'top-left'     => 2,
					'top-right'    => 2,
					'bottom-right' => 2,
					'bottom-left'  => 2,
					'unit'         => 'px',
				),
				'sinatra_secondary_button_border_width'    => 1,
				'sinatra_secondary_button_border_color'    => 'rgba(0, 0, 0, 0.12)',
				'sinatra_secondary_button_hover_border_color' => 'rgba(0, 0, 0, 0.12)',
				'sinatra_secondary_button_typography'      => sinatra_typography_defaults(
					array(
						'font-family'         => 'inherit',
						'font-weight'         => 500,
						'font-size-desktop'   => '0.9375',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),

				// Text button.
				'sinatra_text_button_heading'              => false,
				'sinatra_text_button_text_color'           => '#23282d',
				'sinatra_text_button_hover_text_color'     => '',
				'sinatra_text_button_typography'           => sinatra_typography_defaults(
					array(
						'font-family'         => 'inherit',
						'font-weight'         => 500,
						'font-size-desktop'   => '0.9375',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),

				// Misc Settings.
				'sinatra_enable_schema'                    => true,
				'sinatra_custom_input_style'               => true,
				'sinatra_preloader_heading'                => false,
				'sinatra_preloader'                        => false,
				'sinatra_preloader_style'                  => '1',
				'sinatra_preloader_visibility'             => 'all',
				'sinatra_scroll_top_heading'               => false,
				'sinatra_enable_scroll_top'                => true,

				/**
				 * Logos & Site Title.
				 */
				'sinatra_logo_default_retina'              => '',
				'sinatra_logo_max_height'                  => array(
					'desktop' => 30,
				),
				'sinatra_logo_margin'                      => array(
					'desktop' => array(
						'top'    => 25,
						'right'  => 0,
						'bottom' => 25,
						'left'   => 0,
					),
					'tablet'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'mobile'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'unit'    => 'px',
				),
				'sinatra_display_tagline'                  => false,
				'sinatra_logo_heading_site_identity'       => true,
				'sinatra_typography_logo_heading'          => false,
				'sinatra_logo_text_font_size'              => array(
					'desktop' => 1.875,
					'unit'    => 'rem',
				),

				/**
				 * Header.
				 */

				// Top Bar.
				'sinatra_top_bar_enable'                   => false,
				'sinatra_top_bar_container_width'          => 'content-width',
				'sinatra_top_bar_visibility'               => 'hide-mobile-tablet',
				'sinatra_top_bar_heading_widgets'          => true,
				'sinatra_top_bar_widgets'                  => array(
					array(
						'classname' => 'sinatra_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => esc_html__( 'This is a placeholder text widget in Top Bar section.', 'sinatra' ),
							'location'   => 'left',
							'visibility' => 'all',
						),
					),
				),
				'sinatra_top_bar_widgets_separator'        => 'regular',
				'sinatra_top_bar_heading_design_options'   => false,
				'sinatra_top_bar_background'               => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
						),
					)
				),
				'sinatra_top_bar_text_color'               => sinatra_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'sinatra_top_bar_border'                   => sinatra_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => '1',
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0, .085)',
							'separator-color'     => '#cccccc',
						),
					)
				),

				// Main Header.
				'sinatra_header_layout'                    => 'layout-1',
				'sinatra_header_container_width'           => 'content-width',
				'sinatra_header_heading_widgets'           => true,
				'sinatra_header_widgets'                   => array(
					array(
						'classname' => 'sinatra_customizer_widget_search',
						'type'      => 'search',
						'values'    => array(
							'location'   => 'left',
							'visibility' => 'hide-mobile-tablet',
						),
					),
				),
				'sinatra_header_widgets_separator'         => 'none',
				'sinatra_header_heading_design_options'    => false,
				'sinatra_header_background'                => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'sinatra_header_border'                    => sinatra_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-color'        => 'rgba(0,0,0, .085)',
							'separator-color'     => '#cccccc',
						),
					)
				),
				'sinatra_header_text_color'                => sinatra_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#66717f',
							'link-color' => '#23282d',
						),
					)
				),

				// Transparent Header.
				'sinatra_tsp_header'                       => false,
				'sinatra_tsp_header_disable_on'            => array(
					'404',
					'posts_page',
					'archive',
					'search',
				),
				'sinatra_tsp_logo_heading'                 => false,
				'sinatra_tsp_logo'                         => '',
				'sinatra_tsp_logo_retina'                  => '',
				'sinatra_tsp_logo_max_height'              => array(
					'desktop' => 30,
				),
				'sinatra_tsp_logo_margin'                  => array(
					'desktop' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'tablet'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'mobile'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'unit'    => 'px',
				),
				'sinatra_tsp_colors_heading'               => false,
				'sinatra_tsp_header_background'            => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color' => array(),
						),
					)
				),
				'sinatra_tsp_header_font_color'            => sinatra_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'sinatra_tsp_header_border'                => sinatra_design_options_defaults(
					array(
						'border' => array(),
					)
				),

				// Sticky Header.
				'sinatra_sticky_header'                    => false,
				'sinatra_sticky_header_hide_on'            => array( '' ),

				// Main Navigation.
				'sinatra_main_nav_heading_animation'       => false,
				'sinatra_main_nav_hover_animation'         => 'underline',
				'sinatra_main_nav_heading_sub_menus'       => false,
				'sinatra_main_nav_sub_indicators'          => true,
				'sinatra_main_nav_heading_mobile_menu'     => false,
				'sinatra_main_nav_mobile_breakpoint'       => 960,
				'sinatra_main_nav_mobile_label'            => '',
				'sinatra_nav_design_options'               => false,
				'sinatra_main_nav_background'              => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
						),
					)
				),
				'sinatra_main_nav_border'                  => sinatra_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 1,
							'border-bottom-width' => 1,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0, .085)',
						),
					)
				),
				'sinatra_main_nav_font_color'              => sinatra_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'sinatra_typography_main_nav_heading'      => false,
				'sinatra_main_nav_font_size'               => array(
					'value' => 0.9375,
					'unit'  => 'rem',
				),

				// Page Header.
				'sinatra_page_header_enable'               => true,
				'sinatra_page_header_alignment'            => 'left',
				'sinatra_page_header_spacing'              => array(
					'desktop' => array(
						'top'    => 30,
						'bottom' => 30,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'sinatra_page_header_background'           => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array( 'background-color' => 'rgba(0,0,0,.025)' ),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'sinatra_page_header_text_color'           => sinatra_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'sinatra_page_header_border'               => sinatra_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0,.062)',
						),
					)
				),
				'sinatra_typography_page_header'           => false,
				'sinatra_page_header_font_size'            => array(
					'desktop' => 1.625,
					'unit'    => 'rem',
				),

				// Breadcrumbs.
				'sinatra_breadcrumbs_enable'               => true,
				'sinatra_breadcrumbs_hide_on'              => array( 'home' ),
				'sinatra_breadcrumbs_position'             => 'in-page-header',
				'sinatra_breadcrumbs_alignment'            => 'left',
				'sinatra_breadcrumbs_spacing'              => array(
					'desktop' => array(
						'top'    => 15,
						'bottom' => 15,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'sinatra_breadcrumbs_heading_design'       => false,
				'sinatra_breadcrumbs_background'           => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'sinatra_breadcrumbs_text_color'           => sinatra_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'sinatra_breadcrumbs_border'               => sinatra_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 0,
							'border-bottom-width' => 0,
							'border-color'        => '',
							'border-style'        => 'solid',
						),
					)
				),

				/**
				 * Hero.
				 */
				'sinatra_enable_hero'                      => false,
				'sinatra_hero_type'                        => 'hover-slider',
				'sinatra_hero_visibility'                  => 'all',
				'sinatra_hero_enable_on'                   => array( 'home' ),
				'sinatra_hero_hover_slider'                => false,
				'sinatra_hero_hover_slider_container'      => 'content-width',
				'sinatra_hero_hover_slider_height'         => 500,
				'sinatra_hero_hover_slider_overlay'        => '1',
				'sinatra_hero_hover_slider_elements'       => array(
					'category'  => true,
					'meta'      => true,
					'read_more' => true,
				),
				'sinatra_hero_hover_slider_posts'          => false,
				'sinatra_hero_hover_slider_post_number'    => 3,
				'sinatra_hero_hover_slider_category'       => array(),

				/**
				 * Blog.
				 */

				// Blog Page / Archive.
				'sinatra_blog_entry_elements'              => array(
					'thumbnail'      => true,
					'header'         => true,
					'meta'           => true,
					'summary'        => true,
					'summary-footer' => true,
				),
				'sinatra_blog_entry_meta_elements'         => array(
					'author'   => true,
					'date'     => true,
					'category' => true,
					'tag'      => false,
					'comments' => true,
				),
				'sinatra_entry_meta_icons'                 => false,
				'sinatra_excerpt_length'                   => 30,
				'sinatra_excerpt_more'                     => '&hellip;',
				'sinatra_blog_layout'                      => 'blog-layout-1',
				'sinatra_blog_image_position'              => 'left',
				'sinatra_blog_image_size'                  => 'large',
				'sinatra_blog_horizontal_post_categories'  => true,
				'sinatra_blog_horizontal_read_more'        => false,

				// Single Post.
				'sinatra_single_post_layout_heading'       => false,
				'sinatra_single_title_position'            => 'in-content',
				'sinatra_single_title_alignment'           => 'left',
				'sinatra_single_title_spacing'             => array(
					'desktop' => array(
						'top'    => 152,
						'bottom' => 100,
					),
					'tablet'  => array(
						'top'    => 90,
						'bottom' => 55,
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'sinatra_single_content_width'             => 'narrow',
				'sinatra_single_narrow_container_width'    => 700,
				'sinatra_single_post_elements_heading'     => false,
				'sinatra_single_post_meta_elements'        => array(
					'author'   => true,
					'date'     => true,
					'comments' => true,
					'category' => false,
				),
				'sinatra_single_post_thumb'                => true,
				'sinatra_single_post_categories'           => true,
				'sinatra_single_post_tags'                 => true,
				'sinatra_single_last_updated'              => true,
				'sinatra_single_about_author'              => true,
				'sinatra_single_post_next_prev'            => true,
				'sinatra_single_post_elements'             => array(
					'thumb'          => true,
					'category'       => true,
					'tags'           => true,
					'last-updated'   => true,
					'about-author'   => true,
					'prev-next-post' => true,
				),
				'sinatra_single_toggle_comments'           => false,
				'sinatra_single_entry_meta_icons'          => false,
				'sinatra_typography_single_post_heading'   => false,
				'sinatra_single_content_font_size'         => array(
					'desktop' => '1',
					'unit'    => 'rem',
				),

				/**
				 * Sidebar.
				 */

				'sinatra_sidebar_position'                 => 'right-sidebar',
				'sinatra_single_post_sidebar_position'     => 'no-sidebar',
				'sinatra_single_page_sidebar_position'     => 'default',
				'sinatra_archive_sidebar_position'         => 'default',
				'sinatra_sidebar_options_heading'          => false,
				'sinatra_sidebar_style'                    => '1',
				'sinatra_sidebar_width'                    => 25,
				'sinatra_sidebar_sticky'                   => '',
				'sinatra_sidebar_responsive_position'      => 'after-content',
				'sinatra_typography_sidebar_heading'       => false,
				'sinatra_sidebar_widget_title_font_size'   => array(
					'desktop' => 1,
					'unit'    => 'rem',
				),

				/**
				 * Footer.
				 */

				// Pre Footer.
				'sinatra_pre_footer_cta'                   => true,
				'sinatra_enable_pre_footer_cta'            => false,
				'sinatra_pre_footer_cta_visibility'        => 'all',
				'sinatra_pre_footer_cta_hide_on'           => array(),
				'sinatra_pre_footer_cta_style'             => '1',
				'sinatra_pre_footer_cta_text'              => wp_kses_post( __( 'This is an example of <em>Pre Footer</em> section in Sinatra.', 'sinatra' ) ),
				'sinatra_pre_footer_cta_btn_text'          => wp_kses_post( __( 'Example Button', 'sinatra' ) ),
				'sinatra_pre_footer_cta_btn_url'           => '#',
				'sinatra_pre_footer_cta_btn_new_tab'       => false,
				'sinatra_pre_footer_cta_design_options'    => false,
				'sinatra_pre_footer_cta_background'        => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'sinatra_pre_footer_cta_border'            => sinatra_design_options_defaults(
					array(
						'border' => array(),
					)
				),
				'sinatra_pre_footer_cta_text_color'        => sinatra_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#FFFFFF',
						),
					)
				),
				'sinatra_pre_footer_cta_typography'        => false,
				'sinatra_pre_footer_cta_font_size'         => array(
					'desktop' => 1.75,
					'unit'    => 'rem',
				),

				// Copyright.
				'sinatra_enable_copyright'                 => true,
				'sinatra_copyright_layout'                 => 'layout-1',
				'sinatra_copyright_separator'              => 'contained-separator',
				'sinatra_copyright_visibility'             => 'all',
				'sinatra_copyright_heading_widgets'        => true,
				'sinatra_copyright_widgets'                => array(
					array(
						'classname' => 'sinatra_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => esc_html__( 'Copyright {{the_year}} &mdash; {{site_title}}. All rights reserved. {{theme_link}}', 'sinatra' ),
							'location'   => 'start',
							'visibility' => 'all',
						),
					),
				),
				'sinatra_copyright_heading_design_options' => false,
				'sinatra_copyright_background'             => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
						),
					)
				),
				'sinatra_copyright_text_color'             => sinatra_design_options_defaults(
					array(
						'color' => array(
							'text-color'       => '',
							'link-color'       => '',
							'link-hover-color' => '#FFFFFF',
						),
					)
				),

				// Main Footer.
				'sinatra_enable_footer'                    => true,
				'sinatra_footer_layout'                    => 'layout-1',
				'sinatra_footer_widgets_align_center'      => false,
				'sinatra_footer_visibility'                => 'all',
				'sinatra_footer_heading_design_options'    => false,
				'sinatra_footer_background'                => sinatra_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#23282d',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'sinatra_footer_text_color'                => sinatra_design_options_defaults(
					array(
						'color' => array(
							'text-color'         => '#9BA1A7',
							'link-color'         => '',
							'link-hover-color'   => '#FFFFFF',
							'widget-title-color' => '#FFFFFF',
						),
					)
				),
				'sinatra_footer_border'                    => sinatra_design_options_defaults(
					array(
						'border' => array(),
					)
				),
				'sinatra_typography_main_footer_heading'   => false,
			);

			$defaults = apply_filters( 'sinatra_default_option_values', $defaults );

			return $defaults;
		}

		/**
		 * Get the options from static array()
		 *
		 * @since  1.0.0
		 * @return array    Return array of theme options.
		 */
		public function get_options() {
			return self::$options;
		}

		/**
		 * Get the options from static array()
		 *
		 * @since  1.0.0
		 * @return array    Return array of theme options.
		 */
		public function get( $id ) {
			$value = isset( self::$options[ $id ] ) ? self::$options[ $id ] : self::get_default( $id );
			$value = apply_filters( "theme_mod_{$id}", $value ); // phpcs:ignore
			return $value;
		}

		/**
		 * Set option.
		 *
		 * @since  1.0.0
		 */
		public function set( $id, $value ) {
			set_theme_mod( $id, $value );
			self::$options[ $id ] = $value;
		}

		/**
		 * Refresh options.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function refresh() {
			self::$options = wp_parse_args(
				get_theme_mods(),
				self::get_defaults()
			);
		}

		/**
		 * Returns the default value for option.
		 *
		 * @since  1.0.0
		 * @param  string $id Option ID.
		 * @return mixed      Default option value.
		 */
		public function get_default( $id ) {
			$defaults = self::get_defaults();
			return isset( $defaults[ $id ] ) ? $defaults[ $id ] : false;
		}
	}

endif;
