<?php
/**
 * Template parts.
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

/**
 * Adds the meta tag to the site header.
 *
 * @since 1.0.0
 */
function sinatra_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
}
add_action( 'wp_head', 'sinatra_meta_viewport', 1 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 *
 * @since 1.0.0
 */
function sinatra_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'sinatra_pingback_header' );

/**
 * Adds the meta tag for website accent color.
 *
 * @since 1.0.0
 */
function sinatra_meta_theme_color() {

	$color = sinatra_option( 'accent_color' );

	if ( $color ) {
		printf( '<meta name="theme-color" content="%s">', esc_attr( $color ) );
	}
}
add_action( 'wp_head', 'sinatra_meta_theme_color' );

/**
 * Outputs the theme top bar area.
 *
 * @since 1.0.0
 */
function sinatra_topbar_output() {

	if ( ! sinatra_is_top_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/topbar/topbar' );
}
add_action( 'sinatra_header', 'sinatra_topbar_output', 10 );

/**
 * Outputs the top bar widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in top bar.
 */
function sinatra_topbar_widgets_output( $location ) {

	do_action( 'sinatra_top_bar_widgets_before_' . $location );

	$sinatra_top_bar_widgets = sinatra_option( 'top_bar_widgets' );

	if ( is_array( $sinatra_top_bar_widgets ) && ! empty( $sinatra_top_bar_widgets ) ) {
		foreach ( $sinatra_top_bar_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'sinatra_top_bar_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'si-topbar-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'si-topbar-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'sinatra-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'sinatra_topbar_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'sinatra_top_bar_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .si-topbar-widget -->' );
			}
		}
	}

	do_action( 'sinatra_top_bar_widgets_after_' . $location );
}
add_action( 'sinatra_topbar_widgets', 'sinatra_topbar_widgets_output' );

/**
 * Outputs the theme header area.
 *
 * @since 1.0.0
 */
function sinatra_header_output() {

	if ( ! sinatra_is_header_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/header/base' );
}
add_action( 'sinatra_header', 'sinatra_header_output', 20 );

/**
 * Outputs the header widgets in Header Widget Locations.
 *
 * @since 1.0.0
 * @param string $locations Widget location.
 */
function sinatra_header_widgets( $locations ) {

	$locations      = (array) $locations;
	$all_widgets    = (array) sinatra_option( 'header_widgets' );
	$header_widgets = $all_widgets;
	$header_class   = '';

	if ( ! empty( $locations ) ) {

		$header_widgets = array();

		foreach ( $locations as $location ) {

			$header_class = ' sinatra-widget-location-' . $location;

			$header_widgets[ $location ] = array();

			if ( ! empty( $all_widgets ) ) {
				foreach ( $all_widgets as $i => $widget ) {
					if ( $location === $widget['values']['location'] ) {
						$header_widgets[ $location ][] = $widget;
					}
				}
			}
		}
	}

	echo '<div class="si-header-widgets si-header-element' . esc_attr( $header_class ) . '">';

	if ( ! empty( $header_widgets ) ) {
		foreach ( $header_widgets as $location => $widgets ) {

			do_action( 'sinatra_header_widgets_before_' . $location );

			if ( ! empty( $widgets ) ) {
				foreach ( $widgets as $widget ) {
					if ( function_exists( 'sinatra_header_widget_' . $widget['type'] ) ) {

						$classes   = array();
						$classes[] = 'si-header-widget__' . esc_attr( $widget['type'] );
						$classes[] = 'si-header-widget';

						if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
							$classes[] = 'sinatra-' . esc_attr( $widget['values']['visibility'] );
						}

						$classes = apply_filters( 'sinatra_header_widget_classes', $classes, $widget );
						$classes = trim( implode( ' ', $classes ) );

						printf( '<div class="%s"><div class="si-widget-wrapper">', esc_attr( $classes ) );
						call_user_func( 'sinatra_header_widget_' . $widget['type'], $widget['values'] );
						printf( '</div></div><!-- END .si-header-widget -->' );
					}
				}
			}

			do_action( 'sinatra_header_widgets_after_' . $location );
		}
	}

	echo '</div><!-- END .si-header-widgets -->';
}
add_action( 'sinatra_header_widget_location', 'sinatra_header_widgets', 1 );

/**
 * Outputs the content of theme header.
 *
 * @since 1.0.0
 */
function sinatra_header_content_output() {

	// Get the selected header layout from Customizer.
	$header_layout = sinatra_option( 'header_layout' );

	?>
	<div id="sinatra-header-inner">
	<?php

	// Load header layout template.
	get_template_part( 'template-parts/header/header', $header_layout );

	?>
	</div><!-- END #sinatra-header-inner -->
	<?php
}
add_action( 'sinatra_header_content', 'sinatra_header_content_output' );

/**
 * Outputs the main footer area.
 *
 * @since 1.0.0
 */
function sinatra_footer_output() {

	if ( ! sinatra_is_footer_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/base' );

}
add_action( 'sinatra_footer', 'sinatra_footer_output', 20 );

/**
 * Outputs the copyright area.
 *
 * @since 1.0.0
 */
function sinatra_copyright_bar_output() {

	if ( ! sinatra_is_copyright_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/copyright/copyright' );
}
add_action( 'sinatra_footer', 'sinatra_copyright_bar_output', 30 );

/**
 * Outputs the copyright widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in copyright.
 */
function sinatra_copyright_widgets_output( $location ) {

	do_action( 'sinatra_copyright_widgets_before_' . $location );

	$sinatra_widgets = sinatra_option( 'copyright_widgets' );

	if ( is_array( $sinatra_widgets ) && ! empty( $sinatra_widgets ) ) {
		foreach ( $sinatra_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( isset( $widget['values'], $widget['values']['location'] ) && $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'sinatra_copyright_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'si-copyright-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'si-copyright-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'sinatra-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'sinatra_copyright_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'sinatra_copyright_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .si-copyright-widget -->' );
			}
		}
	}

	do_action( 'sinatra_copyright_widgets_after_' . $location );

}
add_action( 'sinatra_copyright_widgets', 'sinatra_copyright_widgets_output' );

/**
 * Outputs the theme sidebar area.
 *
 * @since 1.0.0
 */
function sinatra_sidebar_output() {

	if ( sinatra_is_sidebar_displayed() ) {
		get_sidebar();
	}
}
add_action( 'sinatra_sidebar', 'sinatra_sidebar_output' );

/**
 * Outputs the back to top button.
 *
 * @since 1.0.0
 */
function sinatra_back_to_top_output() {

	if ( ! sinatra_option( 'enable_scroll_top' ) ) {
		return;
	}

	get_template_part( 'template-parts/misc/back-to-top' );
}
add_action( 'sinatra_after_page_wrapper', 'sinatra_back_to_top_output' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function sinatra_page_header_template() {

	do_action( 'sinatra_before_page_header' );

	if ( sinatra_is_page_header_displayed() ) {
		if ( is_singular( 'post' ) ) {
			get_template_part( 'template-parts/header-page-title-single' );
		} else {
			get_template_part( 'template-parts/header-page-title' );
		}
	}

	do_action( 'sinatra_after_page_header' );
}
add_action( 'sinatra_page_header', 'sinatra_page_header_template' );

/**
 * Outputs the theme hero content.
 *
 * @since 1.0.0
 */
function sinatra_hero() {

	if ( ! sinatra_is_hero_displayed() ) {
		return;
	}

	// Hero type.
	$hero_type = sinatra_option( 'hero_type' );

	do_action( 'sinatra_before_hero' );

	// Enqueue Sinatra Slider script.
	wp_enqueue_script( 'sinatra-slider' );

	?>
	<div id="hero" <?php sinatra_hero_classes(); ?>>
		<?php get_template_part( 'template-parts/hero/hero', $hero_type ); ?>
	</div><!-- END #hero -->
	<?php

	do_action( 'sinatra_after_hero' );
}
add_action( 'sinatra_after_masthead', 'sinatra_hero', 30 );

/**
 * Outputs the queried articles.
 *
 * @since 1.0.0
 */
function sinatra_content() {

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', sinatra_get_article_feed_layout() );
		endwhile;

		sinatra_pagination();

		else :
			get_template_part( 'template-parts/content/content', 'none' );
		endif;
}
add_action( 'sinatra_content', 'sinatra_content' );
add_action( 'sinatra_content_archive', 'sinatra_content' );
add_action( 'sinatra_content_search', 'sinatra_content' );

/**
 * Outputs the theme single content.
 *
 * @since 1.0.0
 */
function sinatra_content_singular() {

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			if ( is_singular( 'post' ) ) {
				do_action( 'sinatra_content_single' );
			} else {
				do_action( 'sinatra_content_page' );
			}

		endwhile;
		else :
			get_template_part( 'template-parts/content/content', 'none' );
	endif;
}
add_action( 'sinatra_content_singular', 'sinatra_content_singular' );

/**
 * Outputs the theme 404 page content.
 *
 * @since 1.0.0
 */
function sinatra_404_page_content() {

	get_template_part( 'template-parts/content/content', '404' );
}
add_action( 'sinatra_content_404', 'sinatra_404_page_content' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function sinatra_content_page() {

	get_template_part( 'template-parts/content/content', 'page' );
}
add_action( 'sinatra_content_page', 'sinatra_content_page' );

/**
 * Outputs the theme single post content.
 *
 * @since 1.0.0
 */
function sinatra_content_single() {

	get_template_part( 'template-parts/content/content', 'single' );
}
add_action( 'sinatra_content_single', 'sinatra_content_single' );

/**
 * Outputs the comments template.
 *
 * @since 1.0.0
 */
function sinatra_output_comments() {
	comments_template();
}
add_action( 'sinatra_after_singular', 'sinatra_output_comments' );

/**
 * Outputs the theme archive page info.
 *
 * @since 1.0.0
 */
function sinatra_archive_info() {

	// Author info.
	if ( is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'about-author' );
	}
}
add_action( 'sinatra_before_content', 'sinatra_archive_info' );

/**
 * Outputs more posts button to author description box.
 *
 * @since 1.0.0
 */
function sinatra_add_author_posts_button() {
	if ( ! is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'author-posts-button' );
	}
}
add_action( 'sinatra_entry_after_author_description', 'sinatra_add_author_posts_button' );

/**
 * Outputs Comments Toggle button.
 *
 * @since 1.0.0
 */
function sinatra_comments_toggle() {

	if ( sinatra_comments_toggle_displayed() ) {
		get_template_part( 'template-parts/entry/entry-show-comments' );
	}
}
add_action( 'sinatra_before_comments', 'sinatra_comments_toggle' );

/**
 * Outputs Pre-Footer area.
 *
 * @since 1.0.0
 */
function sinatra_pre_footer() {

	if ( ! sinatra_is_pre_footer_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/pre-footer/base' );
}
add_action( 'sinatra_before_colophon', 'sinatra_pre_footer' );

/**
 * Outputs Page Preloader.
 *
 * @since 1.0.0
 */
function sinatra_preloader() {

	if ( ! sinatra_is_preloader_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/preloader/base' );
}
add_action( 'sinatra_before_page_wrapper', 'sinatra_preloader' );

/**
 * Outputs breadcrumbs after header.
 *
 * @since  1.1.0
 * @return void
 */
function sinatra_breadcrumb_after_header_output() {

	if ( 'below-header' === sinatra_option( 'breadcrumbs_position' ) && sinatra_has_breadcrumbs() ) {

		$alignment = 'si-text-align-' . sinatra_option( 'breadcrumbs_alignment' );

		$args = array(
			'container_before' => '<div class="si-breadcrumbs"><div class="si-container ' . $alignment . '">',
			'container_after'  => '</div></div>',
		);

		sinatra_breadcrumb( $args );
	}
}
add_action( 'sinatra_main_start', 'sinatra_breadcrumb_after_header_output' );

/**
 * Outputs breadcumbs in page header.
 *
 * @since  1.1.0
 * @return void
 */
function sinatra_breadcrumb_page_header_output() {

	if ( sinatra_page_header_has_breadcrumbs() ) {

		if ( is_singular( 'post' ) ) {
			$args = array(
				'container_before' => '<div class="si-container si-breadcrumbs">',
				'container_after'  => '</div>',
			);
		} else {
			$args = array(
				'container_before' => '<div class="si-breadcrumbs">',
				'container_after'  => '</div>',
			);
		}

		sinatra_breadcrumb( $args );
	}
}
add_action( 'sinatra_page_header_end', 'sinatra_breadcrumb_page_header_output' );

/**
 * Replace tranparent header logo.
 *
 * @since  1.1.1
 * @param  string $output Current logo markup.
 * @return string         Update logo markup.
 */
function sinatra_transparent_header_logo( $output ) {

	// Check if transparent header is displayed.
	if ( sinatra_is_header_transparent() ) {

		// Check if transparent logo is set.
		$logo = sinatra_option( 'tsp_logo' );
		$logo = isset( $logo['background-image-id'] ) ? $logo['background-image-id'] : false;

		$retina = sinatra_option( 'tsp_logo_retina' );
		$retina = isset( $retina['background-image-id'] ) ? $retina['background-image-id'] : false;

		if ( $logo ) {
			$output = sinatra_get_logo_img_output( $logo, $retina, 'si-tsp-logo' );
		}
	}

	return $output;
}
add_filter( 'sinatra_logo_img_output', 'sinatra_transparent_header_logo' );
add_filter( 'sinatra_site_title_markup', 'sinatra_transparent_header_logo' );

/**
 * Output the main navigation template.
 */
function sinatra_main_navigation_template() {
	get_template_part( 'template-parts/header/navigation' );
}

/**
 * Output the Header logo template.
 */
function sinatra_header_logo_template() {
	get_template_part( 'template-parts/header/logo' );
}
