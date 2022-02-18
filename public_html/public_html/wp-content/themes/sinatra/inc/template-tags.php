<?php
/**
 * Template tags used throught the theme.
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

if ( ! function_exists( 'sinatra_get_schema_markup' ) ) :
	/**
	 * Return correct schema markup.
	 *
	 * @since 1.0.0
	 * @param string $location Location for schema parameters.
	 */
	function sinatra_get_schema_markup( $location = '' ) {

		// Check if schema is enabled.
		if ( ! sinatra_is_schema_enabled() ) {
			return;
		}

		// Return if no location parameter is passed.
		if ( ! $location ) {
			return;
		}

		$schema = '';

		if ( 'url' === $location ) {
			$schema = 'itemprop="url"';
		} elseif ( 'name' === $location ) {
			$schema = 'itemprop="name"';
		} elseif ( 'text' === $location ) {
			$schema = 'itemprop="text"';
		} elseif ( 'headline' === $location ) {
			$schema = 'itemprop="headline"';
		} elseif ( 'image' === $location ) {
			$schema = 'itemprop="image"';
		} elseif ( 'header' === $location ) {
			$schema = 'itemtype="https://schema.org/WPHeader" itemscope="itemscope"';
		} elseif ( 'site_navigation' === $location ) {
			$schema = 'itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope"';
		} elseif ( 'logo' === $location ) {
			$schema = 'itemprop="logo"';
		} elseif ( 'description' === $location ) {
			$schema = 'itemprop="description"';
		} elseif ( 'organization' === $location ) {
			$schema = 'itemtype="https://schema.org/Organization" itemscope="itemscope" ';
		} elseif ( 'footer' === $location ) {
			$schema = 'itemtype="http://schema.org/WPFooter" itemscope="itemscope"';
		} elseif ( 'sidebar' === $location ) {
			$schema = 'itemtype="http://schema.org/WPSideBar" itemscope="itemscope"';
		} elseif ( 'main' === $location ) {
			$schema = 'itemtype="http://schema.org/WebPageElement" itemprop="mainContentOfPage"';

			if ( is_singular( 'post' ) ) {
				$schema = 'itemscope itemtype="http://schema.org/Blog"';
			}
		} elseif ( 'author' === $location ) {
			$schema = 'itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person"';
		} elseif ( 'name' === $location ) {
			$schema = 'itemprop="name"';
		} elseif ( 'datePublished' === $location ) {
			$schema = 'itemprop="datePublished"';
		} elseif ( 'dateModified' === $location ) {
			$schema = 'itemprop="dateModified"';
		} elseif ( 'article' === $location ) {
			$schema = 'itemscope="" itemtype="https://schema.org/CreativeWork"';
		} elseif ( 'comment' === $location ) {
			$schema = 'itemprop="comment" itemscope="" itemtype="https://schema.org/Comment"';
		} elseif ( 'html' === $location ) {
			if ( is_singular() ) {
				$schema = 'itemscope itemtype="http://schema.org/WebPage"';
			} else {
				$schema = 'itemscope itemtype="http://schema.org/Article"';
			}
		}

		$schema = ' ' . trim( apply_filters( 'sinatra_schema_markup', $schema, $location ) );

		return $schema;
	}
endif;

if ( ! function_exists( 'sinatra_schema_markup' ) ) :
	/**
	 * Outputs correct schema markup
	 *
	 * @since 1.0.0
	 * @param string $location Location for schema parameters.
	 */
	function sinatra_schema_markup( $location ) {
		echo sinatra_get_schema_markup( $location ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'sinatra_logo' ) ) :
	/**
	 * Outputs theme logo markup.
	 *
	 * @since 1.0.0
	 * @param boolean|string $echo - Print the logo or return as string.
	 */
	function sinatra_logo( $echo = true ) {

		$display_site_description = sinatra_option( 'display_tagline' );
		$site_title               = sinatra_get_site_title();
		$site_url                 = sinatra_get_site_url();

		$site_title_output       = '';
		$site_description_output = '';

		// Check if a custom logo image has been uploaded.
		if ( sinatra_has_logo() ) {

			$default_logo = sinatra_option( 'custom_logo', '' );

			$retina_logo = sinatra_option( 'logo_default_retina' );
			$retina_logo = isset( $retina_logo['background-image-id'] ) ? $retina_logo['background-image-id'] : false;

			$site_title_output = sinatra_get_logo_img_output( $default_logo, $retina_logo );

			// Allow logo output to be filtered.
			$site_title_output = apply_filters( 'sinatra_logo_img_output', $site_title_output );

		} else {

			// Set tag to H1 for home page, span for other pages.
			$site_title_tag = is_home() || is_front_page() ? 'h1' : 'span';
			$site_title_tag = apply_filters( 'sinatra_site_title_tag', $site_title_tag );

			// Site Title HTML markup.
			$site_title_output = apply_filters(
				'sinatra_site_title_markup',
				sprintf(
					'<%1$s class="site-title"%4$s>
						<a href="%2$s" rel="home"%5$s>
							%3$s
						</a>
					</%1$s>',
					tag_escape( $site_title_tag ),
					esc_url( $site_url ),
					esc_html( $site_title ),
					sinatra_get_schema_markup( 'name' ),
					sinatra_get_schema_markup( 'url' )
				)
			);
		}

		// Output site description if enabled in Customizer.
		if ( $display_site_description ) {

			$site_description_output = apply_filters(
				'sinatra_site_description_markup',
				sprintf(
					'<p class="site-description"%2$s>
						%1$s
					</p>',
					esc_html( sinatra_get_site_description() ),
					sinatra_get_schema_markup( 'description' )
				)
			);
		}

		$site_title_output = '<div class="logo-inner">' . $site_title_output . $site_description_output . '</div>';

		// Allow output to be filtered.
		$output = apply_filters( 'sinatra_logo_output', $site_title_output );

		// Echo or return the output.
		if ( $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
endif;

if ( ! function_exists( 'sinatra_get_logo_img_output' ) ) :
	/**
	 * Outputs logo image markup.
	 *
	 * @param int    $logo Attachment ID of the logo image.
	 * @param int    $retina Attachment ID of the retina logo image.
	 * @param string $class Additional CSS class.
	 * @since 1.0.0
	 */
	function sinatra_get_logo_img_output( $logo, $retina = '', $class = '' ) {

		$output = '';

		// Logo attributes.
		$logo_attr = array(
			'url'    => '',
			'width'  => '',
			'height' => '',
			'class'  => '',
			'alt'    => '',
		);

		// Check if a custom logo has been uploaded.
		if ( $logo ) {

			// Get default logo src, width & height.
			$default_logo_attachment_src = wp_get_attachment_image_src( $logo, 'full' );

			if ( $default_logo_attachment_src ) {
				$logo_attr['url']    = $default_logo_attachment_src[0];
				$logo_attr['width']  = $default_logo_attachment_src[1];
				$logo_attr['height'] = $default_logo_attachment_src[2];
			}

			// Check if uploaded logo is SVG.
			$mimes          = array();
			$mimes['svg']   = 'image/svg+xml';
			$file_type      = wp_check_filetype( $logo_attr['url'], $mimes );
			$file_extension = $file_type['ext'];

			if ( 'svg' === $file_extension ) {
				$logo_attr['width']  = '100%';
				$logo_attr['height'] = '100%';
				$logo_attr['class']  = 'si-svg-logo';
			}

			// Get default logo alt.
			$default_logo_alt = get_post_meta( $logo, '_wp_attachment_image_alt', true );
			$logo_attr['alt'] = $default_logo_alt ? $default_logo_alt : sinatra_get_site_title();

			// Build srcset attribute.
			$srcset = '';

			if ( $retina ) {
				$retina_logo_image = wp_get_attachment_image_url( $retina, 'full' );

				if ( $retina_logo_image ) {
					$srcset = ' srcset="' . esc_attr( $logo_attr['url'] ) . ' 1x, ' . esc_attr( $retina_logo_image ) . ' 2x"';
				}
			}

			// Build logo output.
			$output = sprintf(
				'<a href="%1$s" rel="home" class="%2$s"%3$s>
					<img src="%4$s" alt="%5$s" width="%6$s" height="%7$s" class="%8$s"%9$s%10$s/>
				</a>',
				esc_url( sinatra_get_site_url() ),
				esc_attr( trim( $class ) ),
				sinatra_get_schema_markup( 'url' ),
				esc_url( $logo_attr['url'] ),
				esc_attr( $logo_attr['alt'] ),
				esc_attr( $logo_attr['width'] ),
				esc_attr( $logo_attr['height'] ),
				esc_attr( $logo_attr['class'] ),
				$srcset,
				sinatra_get_schema_markup( 'logo' )
			);
		}

		return $output;
	}
endif;

if ( ! function_exists( 'sinatra_edit_post_link' ) ) :

	/**
	 * Function to get Edit Post Link
	 *
	 * @since 1.0.0
	 *
	 * @param string      $text   Optional. Anchor text. If null, default is 'Edit This'. Default null.
	 * @param string      $before Optional. Display before edit link. Default empty.
	 * @param string      $after  Optional. Display after edit link. Default empty.
	 * @param int|WP_Post $id     Optional. Post ID or post object. Default is the global `$post`.
	 * @param string      $class  Optional. Add custom class to link. Default 'post-edit-link'.
	 */
	function sinatra_edit_post_link( $text, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {

		if ( apply_filters( 'sinatra_edit_post_link', true ) && get_edit_post_link() ) {

			edit_post_link( $text, $before, $after, $id, $class );
		}
	}
endif;

if ( ! function_exists( 'sinatra_page_header_title' ) ) :
	/**
	 * Output the Page Header title tag.
	 *
	 * @since 1.0.0
	 * @param boolean $echo Display or return the title.
	 */
	function sinatra_page_header_title( $echo = true ) {

		$title = apply_filters( 'sinatra_page_header_title', sinatra_get_the_title() );
		$tag   = apply_filters( 'sinatra_page_header_title_tag', 'h1' );
		$class = array( 'page-title' );
		$class = apply_filters( 'sinatra_page_header_title_class', $class );

		if ( ! empty( $class ) ) {
			$class = ' class="' . esc_attr( trim( implode( ' ', $class ) ) ) . '"';
		} else {
			$class = '';
		}

		$before = '<' . tag_escape( $tag ) . $class . sinatra_get_schema_markup( 'headline' ) . '>';
		$after  = '</' . tag_escape( $tag ) . '>';
		$title  = $before . wp_kses( $title, sinatra_get_allowed_html_tags() ) . $after;

		if ( $echo ) {
			echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $title;
		}
	}
endif;

if ( ! function_exists( 'sinatra_hamburger' ) ) :
	/**
	 * Output the hamburger button.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $button_title Menu title.
	 * @param  string $menu_id Menu ID.
	 */
	function sinatra_hamburger( $button_title, $menu_id ) {

		$classes = array( 'si-hamburger', 'hamburger--spin', 'si-hamburger-' . esc_attr( $menu_id ) );
		$classes = apply_filters( 'sinatra_hamburger_menu_classes', $classes );
		$classes = trim( implode( ' ', $classes ) );

		?>
		<button class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php esc_attr_e( 'Menu', 'sinatra' ); ?>" aria-controls="<?php echo esc_attr( $menu_id ); ?>" type="button">

			<?php if ( $button_title || is_customize_preview() ) { ?>
				<span class="hamburger-label uppercase-text"><?php echo wp_kses( $button_title, sinatra_get_allowed_html_tags( 'button' ) ); ?></span>
			<?php } ?>

			<span class="hamburger-box">
				<span class="hamburger-inner"></span>
			</span>

		</button>
		<?php
	}
endif;

if ( ! function_exists( 'sinatra_pagination' ) ) :
	/**
	 * Output the pagination navigation.
	 *
	 * @since 1.0.0
	 */
	function sinatra_pagination() {

		// Don't print empty markup if there's only one page.
		if ( $GLOBALS['wp_query']->max_num_pages <= 1 ) {
			return;
		}

		?>
		<div class="sinatra-pagination">
			<?php

			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => sinatra_animated_arrow( 'left', 'button', false ) . '<span class="screen-reader-text">' . __( 'Previous page', 'sinatra' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'sinatra' ) . '</span>' . sinatra_animated_arrow( 'right', 'button', false ),
				)
			);
			?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'sinatra_link_pages' ) ) :
	/**
	 * Output the wp_link_pages.
	 *
	 * @since 1.0.0
	 */
	function sinatra_link_pages() {

		wp_link_pages(
			array(
				'before'      => '<div class="page-links"><em>' . esc_html__( 'Pages', 'sinatra' ) . '</em>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			)
		);
	}
endif;

if ( ! function_exists( 'sinatra_animated_arrow' ) ) :
	/**
	 * Output the animated button HTML markup.
	 *
	 * @since 1.0.0
	 * @param string  $style button style. Can be 'right', or 'left'.
	 * @param string  $type  type attribute for <button> element.
	 * @param boolean $echo  echo the outpur or return.
	 * @return string | void
	 */
	function sinatra_animated_arrow( $style = 'right', $type = 'button', $echo = false ) {

		if ( false !== $type ) {

			$button = '
			<button type="' . esc_attr( $type ) . '" class="sinatra-animate-arrow ' . esc_attr( $style ) . '-arrow" aria-hidden="true" role="button" tabindex="-1">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="18px" viewBox="0 0 30 18" enable-background="new 0 0 30 18" xml:space="preserve">
					
					<path class="arrow-handle" d="M2.511,9.007l7.185-7.221c0.407-0.409,0.407-1.071,0-1.48s-1.068-0.409-1.476,0L0.306,8.259 c-0.408,0.41-0.408,1.072,0,1.481l7.914,7.952c0.407,0.408,1.068,0.408,1.476,0s0.407-1.07,0-1.479L2.511,9.007z">
					</path>
					
					<path class="arrow-bar" fill-rule="evenodd" clip-rule="evenodd" d="M1,8h28.001c0.551,0,1,0.448,1,1c0,0.553-0.449,1-1,1H1c-0.553,0-1-0.447-1-1
					                            C0,8.448,0.447,8,1,8z">
					</path>
				</svg>
			</button>';

		} else {
			$button = '<svg aria-hidden="true" class="sinatra-animate-arrow ' . esc_attr( $style ) . '-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="18px" viewBox="0 0 30 18" enable-background="new 0 0 30 18" xml:space="preserve">
					<path class="arrow-handle" d="M2.511,9.007l7.185-7.221c0.407-0.409,0.407-1.071,0-1.48s-1.068-0.409-1.476,0L0.306,8.259 c-0.408,0.41-0.408,1.072,0,1.481l7.914,7.952c0.407,0.408,1.068,0.408,1.476,0s0.407-1.07,0-1.479L2.511,9.007z"></path>
					<path class="arrow-bar" fill-rule="evenodd" clip-rule="evenodd" d="M30,9c0,0.553-0.447,1-1,1H1c-0.551,0-1-0.447-1-1c0-0.552,0.449-1,1-1h28.002 C29.554,8,30,8.448,30,9z"></path>
					</svg>';
		}

		if ( $echo ) {
			echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $button;
		}
	}
endif;

if ( ! function_exists( 'sinatra_excerpt' ) ) :
	/**
	 * Get excerpt.
	 *
	 * @since 1.0.0
	 * @param int    $length the length of the excerpt.
	 * @param string $more What to append if $text needs to be trimmed.
	 */
	function sinatra_excerpt( $length = null, $more = null ) {

		global $post;

		// Check if this post has a custom excerpt.
		if ( has_excerpt( $post->ID ) ) {
			$output = $post->post_excerpt;
		} else {
			// Check for more tag.
			if ( strpos( $post->post_content, '<!--more-->' ) ) {
				$output = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			} else {

				if ( null === $length ) {
					$length = apply_filters( 'excerpt_length', intval( sinatra_option( 'excerpt_length' ) ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				}

				if ( null === $more ) {
					$more = apply_filters( 'excerpt_more', sinatra_option( 'excerpt_more' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				}

				$output = wp_trim_words( strip_shortcodes( $post->post_content ), $length, $more );
			}
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'sinatra_entry_meta_author' ) ) :
	/**
	 * Prints HTML with meta information about theme author.
	 *
	 * @since 1.0.0
	 * @param array $args Author meta arguments.
	 */
	function sinatra_entry_meta_author( $args = array() ) {

		$defaults = array(
			'show_avatar' => is_single() && sinatra_option( 'single_entry_meta_icons' ) || ! is_single() && sinatra_option( 'entry_meta_icons' ),
			'user_id'     => get_post_field( 'post_author', get_the_ID() ),
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'sinatra_entry_meta_author_args', $args );

		?>
		<span class="post-author">
			<span class="posted-by vcard author"<?php sinatra_schema_markup( 'author' ); ?>>
				<span class="screen-reader-text"><?php esc_html_e( 'Posted by', 'sinatra' ); ?></span>

				<?php if ( $args['show_avatar'] ) { ?>
					<span class="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'email', $args['user_id'] ), 30 ); ?>
					</span>
				<?php } ?>

				<span>
					<?php // Translators: Author Name. ?>
					<?php esc_html_e( 'By ', 'sinatra' ); ?>
					<a class="url fn n" title="<?php /* translators: %1$s Author */ printf( esc_attr__( 'View all posts by %1$s', 'sinatra' ), get_the_author() ); ?>" 
						href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $args['user_id'] ) ) ); ?>" rel="author"<?php sinatra_schema_markup( 'url' ); ?>>
						<span class="author-name"<?php sinatra_schema_markup( 'name' ); ?>><?php echo esc_html( get_the_author_meta( 'display_name', $args['user_id'] ) ); ?></span>
					</a>
				</span>
			</span>
		</span>
		<?php
	}
endif;

if ( ! function_exists( 'sinatra_entry_meta_date' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 *
	 * @since 1.0.0
	 * @param array $args Date meta arguments.
	 */
	function sinatra_entry_meta_date( $args = array() ) {

		$defaults = array(
			'show_published' => true,
			'show_modified'  => false,
			'modified_label' => esc_html__( 'Last updated on', 'sinatra' ),
			'date_format'    => '',
			'before'         => '<span class="posted-on">',
			'after'          => '</span>',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'sinatra_entry_date_args', $args );

		// Icon.
		$icon = sinatra()->icons->get_meta_icon( 'date', sinatra()->icons->get_svg( 'clock', array( 'aria-hidden' => 'true' ) ) );

		if ( $args['show_published'] ) {

			if ( $args['show_modified'] && get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published" datetime="%1$s"%2$s>%3$s</time><time class="updated" datetime="%4$s"%5$s>%6$s</time>';
			} else {
				$time_string = '<time class="entry-date published updated" datetime="%1$s"%2$s>%3$s</time>';
			}
		} elseif ( $args['show_modified'] ) {

			if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published updated" datetime="%4$s"%5$s>%6$s</time>';
			} else {
				$time_string = '<time class="entry-date updated" datetime="%4$s"%5$s>%6$s</time>';
			}
		}

		$args['modified_label'] = $args['modified_label'] ? $args['modified_label'] . ' ' : '';

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			sinatra_get_schema_markup( 'datePublished' ),
			$icon . esc_html( get_the_date( $args['date_format'] ) ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			sinatra_get_schema_markup( 'dateModified' ),
			esc_html( $args['modified_label'] ) . esc_html( get_the_modified_date( $args['date_format'] ) )
		);

		echo wp_kses(
			sprintf(
				'%1$s%2$s%3$s',
				$args['before'],
				$time_string,
				$args['after']
			),
			sinatra_get_allowed_html_tags()
		);
	}
endif;

if ( ! function_exists( 'sinatra_entry_meta_comments' ) ) :
	/**
	 * Prints HTML with meta information for the comments.
	 *
	 * @since 1.0.0
	 */
	function sinatra_entry_meta_comments() {

		$icon = sinatra()->icons->get_meta_icon( 'comments', sinatra()->icons->get_svg( 'message-square', array( 'aria-hidden' => 'true' ) ) );

		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';

			comments_popup_link( wp_kses_post( $icon ) . esc_html__( 'No Comments', 'sinatra' ), wp_kses( $icon, sinatra_get_allowed_html_tags( 'post' ) ) . esc_html__( '1 Comment', 'sinatra' ), wp_kses( $icon, sinatra_get_allowed_html_tags( 'post' ) ) . esc_html__( '% Comments', 'sinatra' ), 'comments-link' );

			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'sinatra_entry_meta_category' ) ) :
	/**
	 * Prints HTML with meta information for the categories.
	 *
	 * @since 1.0.0
	 * @param string $sep Category separator.
	 * @param bool   $show_icon Show an icon for the meta detail.
	 * @param bool   $return Return or output.
	 */
	function sinatra_entry_meta_category( $sep = ', ', $show_icon = true, $return = false ) {

		$categories_list = get_the_category_list( $sep );
		$output          = '';

		// Icon.
		$icon = $show_icon ? sinatra()->icons->get_meta_icon( 'category', sinatra()->icons->get_svg( 'bookmark', array( 'aria-hidden' => 'true' ) ) ) : '';

		if ( $categories_list ) {

			/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
			$output = wp_kses(
				apply_filters(
					'sinatra_entry_meta_category',
					sprintf(
						'<span class="cat-links"><span class="screen-reader-text">%1$s</span>%3$s%2$s</span>',
						__( 'Posted in', 'sinatra' ),
						'<span>' . $categories_list . '</span>',
						$icon
					)
				),
				sinatra_get_allowed_html_tags()
			);

			if ( $return ) {
				return $output; // return is used by Core plugin for Posts widget.
			} else {
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

	}
endif;

if ( ! function_exists( 'sinatra_entry_meta_tag' ) ) :
	/**
	 * Prints HTML with meta information for the tags.
	 *
	 * @since 1.0.0
	 * @param string $before    Before entry meta tag.
	 * @param string $sep       Separator string.
	 * @param string $after     After entry meta tag.
	 * @param int    $id        Post ID.
	 * @param bool   $show_icon Show an icon for the meta detail.
	 */
	function sinatra_entry_meta_tag( $before = '<span class="cat-links"><span>', $sep = ', ', $after = '</span></span>', $id = 0, $show_icon = true ) {

		$icon = $show_icon ? sinatra()->icons->get_meta_icon( 'tags', sinatra()->icons->get_svg( 'tag', array( 'aria-hidden' => 'true' ) ) ) : '';

		// Add icon.
		$before = $before . wp_kses( $icon, sinatra_get_allowed_html_tags() );

		/* translators: used between list items, there is a space after the comma. */
		$tags_list = get_the_tag_list( $before, $sep, $after, $id );

		if ( $tags_list && ! post_password_required() ) {

			$tag_string = '<span class="screen-reader-text">%1$s </span>%2$s';

			/* translators: 1: posted in label, only visible to screen readers. 2: list of tags. */
			echo wp_kses(
				apply_filters(
					'sinatra_entry_meta_tag',
					sprintf(
						$tag_string,
						__( 'Tags:', 'sinatra' ),
						$tags_list
					)
				),
				sinatra_get_allowed_html_tags()
			);
		}

	}
endif;

if ( ! function_exists( 'sinatra_get_post_media' ) ) :

	/**
	 * Post format featured media: image / gallery / audio / video etc.
	 *
	 * @since  1.0
	 * @return mixed
	 * @param  string $post_format Post Format.
	 * @param  mixed  $post        Post object.
	 */
	function sinatra_get_post_media( $post_format = false, $post = null ) {

		if ( false === $post_format ) {
			$post_format = get_post_format( $post );
		}

		$return = '';

		switch ( $post_format ) {

			case 'video':
				$return = sinatra_get_video_from_post( $post );
				break;

			case 'audio':
				$return = do_shortcode( sinatra_get_audio_from_post( $post ) );
				break;

			case 'gallery':
				$gallery = sinatra_get_post_gallery( $post );

				if ( isset( $gallery['ids'] ) ) {

					$img_ids = explode( ',', $gallery['ids'] );

					if ( is_array( $img_ids ) && ! empty( $img_ids ) ) {
						foreach ( $img_ids as $img_id ) {

							$image_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
							$image_url = wp_get_attachment_url( $img_id );

							$return .= '<a href="' . esc_url( get_permalink( $post ) ) . '" >';
							$return .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" >';
							$return .= '</a>';
						}
					}
				}
				break;

			case 'image':
			default:
				$size    = sinatra_option( 'blog_image_size' );
				$caption = false;

				if ( is_single( $post ) || is_page( $post ) ) {

					$caption = true;

					if ( 'no-sidebar' === sinatra_get_sidebar_position( $post ) ) {
						$size = 'full';
					}
				}

				if ( has_post_thumbnail( $post ) ) {
					$return = sinatra_get_post_thumbnail( $post, $size, $caption );
				} elseif ( 'image' === $post_format ) {
					$return = sinatra_get_image_from_post( $post );
				}

				break;
		}

		return apply_filters( 'sinatra_get_post_media', $return, $post_format, $post );
	}
endif;

if ( ! function_exists( 'sinatra_post_media' ) ) :

	/**
	 * Print HTML format featured media: image / gallery / audio / video etc.
	 *
	 * @since 1.0
	 * @return mixed
	 * @param  string $post_format Post Format.
	 */
	function sinatra_post_media( $post_format = false ) {
		echo sinatra_get_post_media( $post_format ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'sinatra_top_bar_widget_text' ) ) :
	/**
	 * Outputs the top bar text widget.
	 *
	 * @since 1.0.0
	 * @param array $options Array of widget options.
	 */
	function sinatra_top_bar_widget_text( $options ) {

		$content = isset( $options['content'] ) ? $options['content'] : '';
		$content = apply_filters( 'sinatra_dynamic_strings', $content );

		echo '<span>' . wp_kses( do_shortcode( $content ), sinatra_get_allowed_html_tags() ) . '</span>';
	}
endif;

if ( ! function_exists( 'sinatra_top_bar_widget_nav' ) ) :
	/**
	 * Outputs the top bar navigation widget.
	 *
	 * @param array $options Array of navigation widget options.
	 * @since 1.0.0
	 */
	function sinatra_top_bar_widget_nav( $options ) {

		$defaults = array(
			'menu_id'     => 'sinatra-topbar-nav',
			'container'   => false,
			'menu_class'  => false,
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'menu'        => '',
		);

		$options = wp_parse_args( $options, $defaults );
		$options = apply_filters( 'sinatra_top_bar_navigation_args', $options );

		if ( empty( $options['menu'] ) ) {
			if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
				?>
				<ul>
					<li class="sinatra-empty-nav">
						<?php
						if ( is_customize_preview() ) {
							esc_html_e( 'Menu not assigned', 'sinatra' );
						} else {
							?>
							<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=sinatra_top_bar_widgets' ) ); ?>"><?php echo esc_html__( 'Assign a menu', 'sinatra' ); ?></a>
						<?php } ?>
					</li>
				</ul>
				<?php
			}
			return;
		}

		$options['before_nav'] = '<nav class="sinatra-nav" role="navigation" aria-label="' . esc_attr( $options['menu'] ) . '">';
		$options['after_nav']  = '</nav>';

		sinatra_navigation( $options );
	}
endif;

if ( ! function_exists( 'sinatra_top_bar_widget_socials' ) ) :
	/**
	 * Outputs the top bar social links widget.
	 *
	 * @param array $options Array of widget options.
	 * @since 1.0.0
	 */
	function sinatra_top_bar_widget_socials( $options ) {
		sinatra_social_links( $options );
	}
endif;

if ( ! function_exists( 'sinatra_header_widget_search' ) ) :
	/**
	 * Outputs the header search widget.
	 *
	 * @since 1.0.0
	 * @param array $options Array of widget options.
	 */
	function sinatra_header_widget_search( $options ) {
		get_template_part( 'template-parts/header/widgets/search' );
	}
endif;

if ( ! function_exists( 'sinatra_header_widget_button' ) ) :
	/**
	 * Outputs the header button widget.
	 *
	 * @since 1.0.0
	 * @param array $options Array of widget options.
	 */
	function sinatra_header_widget_button( $options ) {

		$class = array( $options['class'] );

		if ( isset( $options['style'] ) ) {
			$class[] = $options['style'];
		}

		$class[] = 'si-btn';

		$class = apply_filters( 'sinatra_header_widget_button_class', $class );
		$class = trim( implode( ' ', $class ) );

		$text = empty( $options['text'] ) ? __( 'Add Button Text', 'sinatra' ) : $options['text'];

		$target = 'target="_self"';

		if ( '_blank' === $options['target'] ) {
			$target = 'target="_blank" rel="noopener noreferrer"';
		}

		echo wp_kses(
			sprintf(
				'<a href="%1$s" class="%2$s" %3$s role="button"><span>%4$s</span></a>',
				esc_url( $options['url'] ),
				esc_attr( $class ),
				$target,
				esc_html( $text )
			),
			sinatra_get_allowed_html_tags()
		);
	}
endif;

if ( ! function_exists( 'sinatra_copyright_widget_text' ) ) :
	/**
	 * Outputs the top bar text widget.
	 *
	 * @since 1.0.0
	 * @param array $options Array of widget options.
	 */
	function sinatra_copyright_widget_text( $options ) {
		sinatra_top_bar_widget_text( $options );
	}
endif;

if ( ! function_exists( 'sinatra_copyright_widget_nav' ) ) :
	/**
	 * Outputs the copyright navigation widget.
	 *
	 * @param array $options Array of widget options.
	 * @since 1.0.0
	 */
	function sinatra_copyright_widget_nav( $options ) {

		$defaults = array(
			'menu_id'     => 'sinatra-footer-nav',
			'container'   => false,
			'menu_class'  => false,
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'menu'        => '',
		);

		$options = wp_parse_args( $options, $defaults );
		$options = apply_filters( 'sinatra_copyright_navigation_args', $options );

		if ( empty( $options['menu'] ) ) {
			if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
				?>
				<ul>
					<li class="sinatra-empty-nav">
						<?php
						if ( is_customize_preview() ) {
							esc_html_e( 'Menu not assigned', 'sinatra' );
						} else {
							?>
							<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=sinatra_copyright_widgets' ) ); ?>"><?php echo esc_html__( 'Assign a menu', 'sinatra' ); ?></a>
						<?php } ?>
					</li>
				</ul>
				<?php
			}
			return;
		}

		$options['before_nav'] = '<nav role="navigation" class="sinatra-nav">';
		$options['after_nav']  = '</nav>';

		sinatra_navigation( $options );

	}
endif;

if ( ! function_exists( 'sinatra_copyright_widget_socials' ) ) :
	/**
	 * Outputs the copyright social links widget.
	 *
	 * @param array $options Array of widget options.
	 * @since 1.0.0
	 */
	function sinatra_copyright_widget_socials( $options ) {
		sinatra_social_links( $options );
	}
endif;

if ( ! function_exists( 'sinatra_footer_widgets' ) ) :
	/**
	 * Outputs the footer widgets.
	 *
	 * @since 1.0.0
	 */
	function sinatra_footer_widgets() {

		$footer_layout  = sinatra_option( 'footer_layout' );
		$column_classes = sinatra_get_footer_column_class( $footer_layout );

		if ( is_array( $column_classes ) && ! empty( $column_classes ) ) {
			foreach ( $column_classes as $i => $column_class ) {

				$sidebar_id = 'sinatra-footer-' . ( $i + 1 );
				?>
				<div class="sinatra-footer-column <?php echo esc_attr( $column_class ); ?>">
					<?php
					if ( is_active_sidebar( $sidebar_id ) ) {
						dynamic_sidebar( $sidebar_id );
					} else {

						if ( current_user_can( 'edit_theme_options' ) ) {

							$sidebar_name = sinatra_get_sidebar_name_by_id( $sidebar_id );
							?>
							<div class="si-footer-widget si-widget sinatra-no-widget">

								<div class='h4 widget-title'><?php echo esc_html( $sidebar_name ); ?></div>

								<p class='no-widget-text'>
									<?php if ( is_customize_preview() ) { ?>
										<a href='#' class="sinatra-set-widget" data-sidebar-id="<?php echo esc_attr( $sidebar_id ); ?>">
									<?php } else { ?>
										<a href='<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>'>
									<?php } ?>
										<?php esc_html_e( 'Click here to assign a widget.', 'sinatra' ); ?>
									</a>
								</p>
							</div>
							<?php
						}
					}
					?>
				</div>
				<?php
			}
		}
	}
endif;

if ( ! function_exists( 'sinatra_comment' ) ) :
	/**
	 * Comment and pingback output function.
	 *
	 * @since 1.0.0
	 * @param string $comment Comment content.
	 * @param array  $args    Comment arguments.
	 * @param int    $depth   Comment depth.
	 */
	function sinatra_comment( $comment, $args, $depth ) {

		global $post;

		if ( 'pingback' === $comment->comment_type ) {
			?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

				<article id="comment-<?php comment_ID(); ?>" class="sinatra-pingback">
					<p><?php esc_html_e( 'Pingback: ', 'sinatra' ); ?><span<?php sinatra_schema_markup( 'author_name' ); ?>><?php comment_author_link(); ?></span> <?php edit_comment_link( esc_html__( '(Edit)', 'sinatra' ), '<span class="edit-link">', '</span>' ); ?></p>
				</article>

		<?php } else { ?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<article 
				<?php
				comment_class( 'comment-body' );
				sinatra_schema_markup( 'comment' );
				?>
				>

					<header class="comment-header">
						<div class="comment-author vcard">

							<span class="comment-author-avatar">
								<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>

								<?php if ( $comment->user_id === $post->post_author ) { ?>
									<span class="bypostauthor-badge" aria-hidden="true" title="<?php esc_attr_e( 'The post author', 'sinatra' ); ?>"><?php echo esc_html_x( 'A', 'Post author badge on comments', 'sinatra' ); ?></span>
								<?php } ?>
							</span>

							<span class="comment-author-meta">
								<cite class="fn">
									<?php comment_author_link(); ?>
								</cite>
							</span>

						</div><!-- END .comment-author -->

						<div class="comment-actions">
							<?php
							$sinatra_comment_reply_link = get_comment_reply_link(
								array_merge(
									$args,
									array(
										'depth'      => $depth,
										'reply_text' => $args['reply_text'],
									)
								)
							);
							?>
							<div class="edit">
								<?php edit_comment_link( __( 'Edit', 'sinatra' ) ); ?>
							</div>

							<?php
							if ( current_user_can( 'edit_comment', get_comment_ID() ) && null !== $sinatra_comment_reply_link ) {
								?>
								<span class="si-comment-sep"></span>
								<?php
							}
							?>

							<div class="reply">
								<?php echo $sinatra_comment_reply_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
					</header><!-- END .comment-header -->

					<div class="comment-meta commentmetadata">
						<?php comment_date(); ?>,
						<a href="<?php echo esc_url( get_comment_link() ); ?>" class="comment-date">
							<time datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php comment_time(); ?></time>
						</a>
					</div><!-- END .comment-meta -->

					<div class="comment-content">
						<?php if ( '0' === $comment->comment_approved ) : ?>
							<p class="comment-awaiting-moderation"><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'sinatra' ); ?></em></p>
						<?php endif; ?>

						<?php comment_text(); ?>
					</div><!-- END .comment-content -->

				</article><!-- END .comment-body -->
		<?php } // endif ?>
		<?php
	}
endif;

if ( ! function_exists( 'sinatra_social_links' ) ) :
	/**
	 * The template tag for displaying social icons.
	 *
	 * @param  array $args Args for wp_nav_menu function.
	 * @since  1.0.0
	 * @return void
	 */
	function sinatra_social_links( $args = array() ) {

		$defaults = array(
			'fallback_cb'     => '',
			'menu'            => '',
			'container'       => 'nav',
			'container_class' => 'sinatra-social-nav',
			'menu_class'      => 'sinatra-socials-menu',
			'depth'           => 1,
			'link_before'     => '<span class="screen-reader-text">',
			'link_after'      => '</span>' . sinatra()->icons->get_svg( 'external-link', array( 'aria-hidden' => 'true' ) ) . sinatra()->icons->get_svg(
				'external-link',
				array(
					'aria-hidden' => 'true',
					'class'       => 'bottom-icon',
				)
			),
			'style'           => '',
			'align'           => '',
			'size'            => 'si-standard',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'sinatra_social_links_args', $args );

		// Add style class to container_class.
		if ( ! empty( $args['style'] ) ) {
			$args['container_class'] .= ' ' . esc_attr( $args['style'] );
		}

		// Add alignment class to container_class.
		if ( ! empty( $args['align'] ) ) {
			$args['menu_class'] .= ' ' . esc_attr( $args['align'] );
		}

		// Add size class to container_class.
		if ( ! empty( $args['size'] ) ) {
			$args['container_class'] .= ' ' . esc_attr( $args['size'] );
		}

		if ( ! empty( $args['menu'] ) && is_nav_menu( $args['menu'] ) ) {
			wp_nav_menu( $args );
		}
	}
endif;

if ( ! function_exists( 'sinatra_navigation' ) ) :
	/**
	 * The template tag for displaying social icons.
	 *
	 * @param  array $args Args for wp_nav_menu function.
	 * @since  1.0.0
	 * @return void
	 */
	function sinatra_navigation( $args = array() ) {

		$defaults = array(
			'before_nav' => '',
			'after_nav'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['items_wrap'] = isset( $args['items_wrap'] ) ? $args['items_wrap'] : '<ul id="%1$s" class="%2$s">%3$s</ul>';
		$args['items_wrap'] = $args['before_nav'] . $args['items_wrap'] . $args['after_nav'];

		$args = apply_filters( 'sinatra_navigation_args', $args );

		if ( ! empty( $args['menu'] ) && is_nav_menu( $args['menu'] ) ) {
			wp_nav_menu( $args );
		}
	}
endif;

if ( ! function_exists( 'sinatra_breadcrumb' ) ) :
	/**
	 * Outputs breadcrumbs trail
	 *
	 * @param array $args Array of breadcrumb options.
	 */
	function sinatra_breadcrumb( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'container_before' => '',
				'container_after'  => '',
			)
		);

		echo wp_kses_post( $args['container_before'] );

		sinatra_breadcrumb_trail(
			array(
				'show_browse' => false,
			)
		);

		echo wp_kses_post( $args['container_after'] );
	}
endif;
