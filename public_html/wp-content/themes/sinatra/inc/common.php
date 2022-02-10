<?php
/**
 * Common functions used in backend and frontend of the theme.
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

if ( ! function_exists( 'sinatra_get_allowed_html_tags' ) ) {
	/**
	 * Array of allowed HTML Tags.
	 *
	 * @since 1.0.0
	 * @param string $type predefined HTML tags group name.
	 * @return array, allowed HTML tags.
	 */
	function sinatra_get_allowed_html_tags( $type = 'post' ) {

		$tags = array();

		switch ( $type ) {

			case 'basic':
				$tags = array(
					'strong' => array(),
					'em'     => array(),
					'b'      => array(),
					'br'     => array(),
					'i'      => array(
						'class' => array(),
					),
					'img'    => array(
						'src'    => array(),
						'alt'    => array(),
						'width'  => array(),
						'height' => array(),
						'class'  => array(),
						'id'     => array(),
					),
					'span'   => array(
						'class' => array(),
					),
					'a'      => array(
						'href'   => array(),
						'rel'    => array(),
						'target' => array(),
						'class'  => array(),
						'role'   => array(),
						'id'     => array(),
					),
				);
				break;

			case 'button':
				$tags = array(
					'strong' => array(),
					'em'     => array(),
					'span'   => array(
						'class' => array(),
					),
					'i'      => array(
						'class' => array(),
					),
				);
				break;

			case 'span':
				$tags = array(
					'span' => array(
						'class' => array(),
					),
				);
				break;

			case 'icon':
				$tags = array(
					'i'    => array(),
					'span' => array(),
					'img'  => array(),
				);
				break;

			case 'post':
				$tags = wp_kses_allowed_html( 'post' );

				$tags = array_merge(
					$tags,
					array(
						'svg'     => array(
							'class'       => true,
							'xmlns'       => true,
							'width'       => true,
							'height'      => true,
							'viewbox'     => true,
							'aria-hidden' => true,
							'role'        => true,
							'focusable'   => true,
						),
						'path'    => array(
							'fill'      => true,
							'fill-rule' => true,
							'd'         => true,
							'transform' => true,
						),
						'polygon' => array(
							'fill'      => true,
							'fill-rule' => true,
							'points'    => true,
							'transform' => true,
							'focusable' => true,
						),
						'title'   => array(),
					)
				);

				break;

			case 'svg':
				$tags = array(
					'svg'     => array(
						'class'       => true,
						'xmlns'       => true,
						'width'       => true,
						'height'      => true,
						'viewbox'     => true,
						'aria-hidden' => true,
						'role'        => true,
						'focusable'   => true,
					),
					'path'    => array(
						'fill'      => true,
						'fill-rule' => true,
						'd'         => true,
						'transform' => true,
					),
					'polygon' => array(
						'fill'      => true,
						'fill-rule' => true,
						'points'    => true,
						'transform' => true,
						'focusable' => true,
					),
					'title'   => array(),
				);
				break;

			default:
				$tags = array(
					'strong' => array(),
					'em'     => array(),
					'b'      => array(),
					'i'      => array(),
					'img'    => array(
						'src'    => array(),
						'alt'    => array(),
						'width'  => array(),
						'height' => array(),
						'class'  => array(),
						'id'     => array(),
					),
					'span'   => array(),
					'a'      => array(
						'href'   => array(),
						'rel'    => array(),
						'target' => array(),
						'class'  => array(),
						'role'   => array(),
						'id'     => array(),
					),
				);
				break;
		}

		return apply_filters( 'sinatra_allowed_html_tags', $tags, $type );
	}
}

/**
 * Returns the value for option.
 *
 * @since 1.0.0
 *
 * @param  string $id Option ID.
 * @param  string $prefix Theme prefix.
 * @param  string $type Option or Theme Mod.
 * @return mixed Option value.
 */
function sinatra_option( $id, $prefix = 'sinatra_', $type = 'theme_mod' ) {

	if ( 'theme_mod' === $type ) {
		return sinatra()->options->get( $prefix . $id );
	} else {
		return get_option( $prefix . $id, sinatra()->options->get( $prefix . $id ) );
	}
}

/**
 * Get default for option.
 *
 * @since 1.1.0
 *
 * @param  string $id Option ID.
 * @param  string $prefix Theme prefix.
 * @return mixed Option value.
 */
function sinatra_get_default( $id, $prefix = 'sinatra_' ) {
	return sinatra()->options->get_default( $prefix . $id );
}

/**
 * Checks to see if Top Bar is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean if Top Bar is enabled.
 */
function sinatra_is_top_bar_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$top_bar_displayed = sinatra_option( 'top_bar_enable' );

	if ( $post_id && $top_bar_displayed ) {
		$top_bar_displayed = ! get_post_meta( $post_id, 'sinatra_disable_topbar', true );
	}

	// Do not show top bar on 404 page.
	if ( is_404() ) {
		$top_bar_displayed = false;
	}

	return apply_filters( 'sinatra_is_top_bar_displayed', $top_bar_displayed, $post_id );
}

/**
 * Checks to see if Page Preloader is displayed.
 *
 * @since 1.0.0
 *
 * @return boolean, true if Preloader is displayed.
 */
function sinatra_is_preloader_displayed() {

	$displayed = (bool) sinatra_option( 'preloader' );

	return apply_filters( 'sinatra_is_preloader_displayed', $displayed );
}

/**
 * Checks to see if Header is displayed.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean true if Header is displayed.
 */
function sinatra_is_header_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = true;

	if ( $post_id ) {
		$displayed = ! get_post_meta( $post_id, 'sinatra_disable_header', true );
	}

	return apply_filters( 'sinatra_is_header_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Transparent Header is enabled.
 *
 * @since 1.0.0
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean, true if Header is transparent.
 */
function sinatra_is_header_transparent( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$enabled = sinatra_option( 'tsp_header' );

	if ( $enabled && sinatra_is_section_disabled( sinatra_option( 'tsp_header_disable_on' ), $post_id ) ) {
		$enabled = false;
	}

	if ( $post_id ) {

		$_meta = get_post_meta( $post_id, 'sinatra_transparent_header', true );

		if ( 'enable' === $_meta ) {
			$enabled = true;
		} elseif ( 'disable' === $_meta ) {
			$enabled = false;
		}
	}

	return apply_filters( 'sinatra_transparent_header', $enabled, $post_id );
}

/**
 * Checks to see if Pre Footer is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean true if Pre Footer is enabled.
 */
function sinatra_is_pre_footer_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = false;

	// Customizer option to enable pre-footer.
	if ( sinatra_option( 'enable_pre_footer_cta' ) ) {
		$displayed = true;
	}

	// At least one pre-footer are has to be enabled.
	if ( ! sinatra_is_pre_footer_cta_displayed( $post_id ) ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_pre_footer_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Pre Footer is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean True if Pre Footer is enabled.
 */
function sinatra_is_pre_footer_cta_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = false;

	if ( sinatra_option( 'enable_pre_footer_cta' ) ) {
		$displayed = true;
	}

	if ( $displayed && sinatra_is_section_disabled( sinatra_option( 'pre_footer_cta_hide_on' ), $post_id ) ) {
		$displayed = false;
	}

	if ( $post_id && $displayed ) {
		$displayed = ! get_post_meta( $post_id, 'sinatra_disable_prefooter_cta', true );
	}

	// Do not show pre footer widgets on 404 page.
	if ( is_404() ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_pre_footer_cta_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Hero section is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean True if Hero is enabled.
 */
function sinatra_is_hero_displayed( $post_id = 0 ) {

	$displayed = true;

	if ( ! sinatra_option( 'enable_hero' ) ) {
		$displayed = false;
	}

	if ( $displayed && ! sinatra_is_section_disabled( sinatra_option( 'hero_enable_on' ), $post_id ) ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_hero_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Main Footer is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean, true if Main Footer is enabled.
 */
function sinatra_is_footer_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$footer_displayed = sinatra_option( 'enable_footer' );

	if ( $post_id && $footer_displayed ) {
		$footer_displayed = ! get_post_meta( $post_id, 'sinatra_disable_footer', true );
	}

	// Do not show footer widgets on 404 page.
	if ( is_404() ) {
		$footer_displayed = false;
	}

	if ( $footer_displayed && ! current_user_can( 'edit_theme_options' ) ) {

		$footer_columns = sinatra_get_footer_column_count();
		$footer_active  = false;

		for ( $i = 1; $i <= $footer_columns; $i++ ) {
			$footer_active = $footer_active || is_active_sidebar( 'sinatra-footer-' . $i );
		}

		$footer_displayed = $footer_displayed && $footer_active;
	}

	return apply_filters( 'sinatra_is_footer_displayed', $footer_displayed, $post_id );
}

/**
 * Checks to see if Copyright Bar is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean, true if Copyright bar is enabled.
 */
function sinatra_is_copyright_bar_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = sinatra_option( 'enable_copyright' );
	$widgets   = sinatra_option( 'copyright_widgets' );

	if ( $displayed && ! is_array( $widgets ) || empty( $widgets ) ) {
		$displayed = false;
	}

	if ( $post_id && $displayed ) {
		$displayed = ! get_post_meta( $post_id, 'sinatra_disable_copyright', true );
	}

	// Do not show copyright bar on 404 page.
	if ( is_404() ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_copyright_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Colophon section is enabled.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean, true if Colophon is enabled.
 */
function sinatra_is_colophon_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = true;

	// Do not show colophon if both footer and copyright bar are not displayed.
	if ( ! sinatra_is_footer_displayed( $post_id ) && ! sinatra_is_copyright_bar_displayed( $post_id ) ) {
		$displayed = false;
	}

	// Do not show colophon on 404 page.
	if ( is_404() ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_colophon_displayed', $displayed, $post_id );
}

/**
 * Checks to see if Page Header is displayed.
 *
 * @since 1.0.0
 *
 * @param  int $post_id Optional. The post ID to check.
 * @return boolean, if Page Header is displayed.
 */
function sinatra_is_page_header_displayed( $post_id = 0 ) {

	if ( ! $post_id ) {
		$post_id = sinatra_get_the_id();
	}

	$displayed = sinatra_option( 'page_header_enable' );

	if ( $post_id && $displayed ) {
		$displayed = sinatra_page_header_has_title( $post_id ) || sinatra_page_header_has_breadcrumbs( $post_id );
	} elseif ( is_404() ) {
		$displayed = false;
	}

	if ( is_front_page() ) {
		$displayed = false;
	}

	return apply_filters( 'sinatra_is_page_header_displayed', $displayed, $post_id );
}

/**
 * Checks to see if WooCommerce is installed & activated.
 *
 * @since 1.0.0
 * @return boolean, if Copyright bar is enabled.
 */
function sinatra_is_woocommerce_activated() {
	return class_exists( 'woocommerce' );
}

/**
 * Get registered sidebar name by sidebar ID.
 *
 * @since  1.0.0
 * @param  string $sidebar_id Sidebar ID.
 * @return string Sidebar name.
 */
function sinatra_get_sidebar_name_by_id( $sidebar_id = '' ) {

	if ( ! $sidebar_id ) {
		return;
	}

	global $wp_registered_sidebars;
	$sidebar_name = '';

	if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
		$sidebar_name = $wp_registered_sidebars[ $sidebar_id ]['name'];
	}

	return $sidebar_name;
}

/**
 * Convert hexdec color string to rgb(a) string.
 *
 * @since  1.0.0
 * @param  string           $color Hex color code.
 * @param  string | boolean $opacity opacity value.
 * @return string color in rgba format.
 */
function sinatra_hex2rgba( $color, $opacity = false ) {

	$default = 'rgb(0,0,0)';

	// Return default if no color provided.
	if ( empty( $color ) ) {
		return $default;
	}

	if ( substr( $color, 0, 4 ) === 'rgba' ) {
		return $color;
	}

	// Sanitize $color if "#" is provided.
	if ( '#' === $color[0] ) {
		$color = substr( $color, 1 );
	}

	// Check if color has 6 or 3 characters and get values.
	if ( 6 === strlen( $color ) ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( 3 === strlen( $color ) ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	// Convert hexadec to rgb.
	$rgb = array_map( 'hexdec', $hex );

	// Check if opacity is set(rgba or rgb).
	if ( $opacity ) {

		if ( abs( $opacity ) > 1 ) {
			$opacity = 1;
		}

		$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode( ',', $rgb ) . ')';
	}

	// Return rgb(a) color string.
	return $output;
}

/**
 * Convert rgb(a) color string to hex string.
 *
 * @since  1.0.0
 * @param  string $color rgb(a) color code.
 * @return string color in HEX format.
 */
function sinatra_rgba2hex( $color ) {

	preg_match( '/rgba?\(\s?([0-9]{1,3}),\s?([0-9]{1,3}),\s?([0-9]{1,3})/i', $color, $matches );

	if ( ! is_array( $matches ) || count( $matches ) < 3 ) {
		return false;
	}

	$hex = '';

	for ( $i = 1; $i <= 3; $i++ ) {
		$x = dechex( (int) $matches[ $i ] );

		$hex .= ( 1 === strlen( $x ) ) ? '0' . $x : $x;
	}

	if ( $hex ) {
		return '#' . $hex;
	}

	return false;
}

/**
 * Lightens/darkens a given colour (in hex format), returning the altered color in hex format.
 *
 * @since 1.0.0
 * @param string $hexcolor Color as hexadecimal (with or without hash).
 * @param float  $percent Decimal ( 0.2 = lighten by 20%, -0.4 = darken by 40% ).
 * @return string Lightened/Darkend color as hexadecimal (with hash)
 */
function sinatra_luminance( $hexcolor, $percent ) {

	if ( empty( $hexcolor ) ) {
		return;
	}

	// Check if color is in RGB format and convert to HEX.
	if ( false !== strpos( $hexcolor, 'rgb' ) ) {
		$hexcolor = sinatra_rgba2hex( $hexcolor );
	}

	if ( strlen( $hexcolor ) < 6 ) {
		$hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
	}

	$hexcolor = array_map( 'hexdec', str_split( str_pad( str_replace( '#', '', $hexcolor ), 6, '0' ), 2 ) );

	foreach ( $hexcolor as $i => $color ) {
		$from           = $percent < 0 ? 0 : $color;
		$to             = $percent < 0 ? $color : 255;
		$pvalue         = ceil( ( $to - $from ) * $percent );
		$hexcolor[ $i ] = str_pad( dechex( $color + $pvalue ), 2, '0', STR_PAD_LEFT );
	}

	// Return hex color.
	return '#' . implode( $hexcolor );
}

/**
 * Determine whether a hex color is light.
 *
 * @param  mixed $color Color.
 * @return bool  True if a light color.
 */
function sinatra_is_light_color( $color ) {

	// Ensure we color is in hex format.
	if ( false !== strpos( $color, 'rgb' ) ) {
		$color = sinatra_rgba2hex( $color );
	}

	$hex = str_replace( '#', '', $color );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155;
}

/**
 * Detect if we should use a light or dark color on a background color.
 *
 * @param mixed  $color Color.
 * @param string $dark  Darkest reference. Defaults to '#000000'.
 * @param string $light Lightest reference. Defaults to '#FFFFFF'.
 * @return string
 */
function sinatra_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
	return sinatra_is_light_color( $color ) ? $dark : $light;
}

if ( ! function_exists( 'sinatra_get_prop' ) ) :

	/**
	 * Get a specific property of an array without needing to check if that property exists.
	 *
	 * Provide a default value if you want to return a specific value if the property is not set.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $array   Array from which the property's value should be retrieved.
	 * @param string $prop    Name of the property to be retrieved.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return null|string|mixed The value
	 */
	function sinatra_get_prop( $array, $prop, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		if ( isset( $array[ $prop ] ) ) {
			$value = $array[ $prop ];
		} else {
			$value = '';
		}

		return empty( $value ) && null !== $default ? $default : $value;
	}
endif;

/**
 * Print objects to error log.
 *
 * @since  1.0.0
 * @param  string $object Object to be printed.
 */
function sinatra_log( $object ) {
	// phpcs:disable
	ob_start();
	print_r( $object );
	error_log( ob_get_clean() );
	// phpcs:enable
}

/**
 * Returns blog page URL.
 *
 * @since 1.0.0
 * @return String, current page URL.
 */
function sinatra_get_blog_url() {

	$blog_url = '';

	// If front page is set to display a static page, get the URL of the posts page.
	if ( 'page' === get_option( 'show_on_front' ) ) {

		$page_for_posts = get_option( 'page_for_posts' );

		if ( $page_for_posts ) {
			$blog_url = get_permalink( $page_for_posts );
		}
	} else {

		// The front page IS the posts page. Get its URL.
		$blog_url = home_url( '/' );
	}

	return apply_filters( 'sinatra_site_url', $blog_url );
}

/**
 * Returns array of default values for Design Options field.
 *
 * @since  1.0.0
 * @param  array $options Default options.
 * @return array $defaults array of default values.
 */
function sinatra_design_options_defaults( $options = array() ) {

	$defaults = array();

	// Background options.
	if ( isset( $options['background'] ) ) {

		// Default background type.
		if ( isset( $options['background']['background-type'] ) && in_array( $options['background']['background-type'], array( 'color', 'image', 'gradient' ), true ) ) {
			$defaults['background-type'] = $options['background']['background-type'];
		} else {
			$defaults['background-type'] = 'color';
		}

		// Background color defaults.
		if ( isset( $options['background']['color'] ) ) {
			$defaults += wp_parse_args(
				(array) $options['background']['color'],
				array(
					'background-color' => '',
				)
			);
		}

		// Background image defaults.
		if ( isset( $options['background']['image'] ) ) {
			$defaults += wp_parse_args(
				(array) $options['background']['image'],
				array(
					'background-image'         => '',
					'background-repeat'        => 'no-repeat',
					'background-position-x'    => '50',
					'background-position-y'    => '50',
					'background-size'          => 'cover',
					'background-attachment'    => 'inherit',
					'background-image-id'      => '',
					'background-color-overlay' => 'rgba(0,0,0,0.5)',
				)
			);
		}

		// Background gradient defaults.
		if ( isset( $options['background']['gradient'] ) ) {
			$defaults += wp_parse_args(
				(array) $options['background']['gradient'],
				array(
					'gradient-color-1'          => '#16222A',
					'gradient-color-1-location' => '0',
					'gradient-color-2'          => '#3A6073',
					'gradient-color-2-location' => '100',
					'gradient-type'             => 'linear',
					'gradient-linear-angle'     => '45',
					'gradient-position'         => 'center center',
				)
			);
		}
	}

	// Border default.
	if ( isset( $options['border'] ) ) {
		$defaults += wp_parse_args(
			(array) $options['border'],
			array(
				'border-left-width'   => '',
				'border-top-width'    => '',
				'border-right-width'  => '',
				'border-bottom-width' => '',
				'border-color'        => '',
				'style'               => 'solid',
				'separator-color'     => '',
			)
		);
	}

	// Color default.
	if ( isset( $options['color'] ) ) {
		$defaults += wp_parse_args(
			(array) $options['color'],
			array(
				'text-color'       => '',
				'link-color'       => '',
				'link-hover-color' => '',
			)
		);
	}

	return apply_filters( 'sinatra_design_options_defaults', $defaults, $options );
}

/**
 * Returns array of default values for Typography field.
 *
 * @since  1.0.0
 * @param  array $options Default options.
 * @return array $defaults array of default values.
 */
function sinatra_typography_defaults( $options = array() ) {

	$defaults = apply_filters(
		'sinatra_typography_defaults',
		array(
			'font-family'         => 'inherit',
			'font-subsets'        => array(),
			'font-weight'         => '400',
			'font-style'          => 'inherit',
			'text-transform'      => 'inherit',
			'text-decoration'     => 'inherit',
			'font-size-desktop'   => '',
			'font-size-tablet'    => '',
			'font-size-mobile'    => '',
			'font-size-unit'      => 'px',
			'letter-spacing'      => '0',
			'letter-spacing-unit' => 'px',
			'line-height-desktop' => '',
			'line-height-tablet'  => '',
			'line-height-mobile'  => '',
			'line-height-unit'    => '',
		)
	);

	$options = wp_parse_args( $options, $defaults );

	return $options;
}

if ( ! function_exists( 'sinatra_enable_page_builder_compatibility' ) ) :

	/**
	 * Allow filter to enable/disable page builder compatibility.
	 *
	 * @since 1.0.0
	 *
	 * @return  bool True - If the page builder compatibility is enabled. False - IF the page builder compatibility is disabled.
	 */
	function sinatra_enable_page_builder_compatibility() {
		return apply_filters( 'sinatra_enable_page_builder_compatibility', true );
	}

endif;

/**
 * Insert into array before specified key.
 *
 * @since 1.0.0
 * @param array  $array     Array to be modified.
 * @param array  $pairs     Array of key => value pairs to insert.
 * @param mixed  $key       Key of $array to insert before or after.
 * @param string $position  Before or after $key.
 * @return array $result    Array with inserted $new value.
 */
function sinatra_array_insert( $array, $pairs, $key, $position = 'after' ) {

	$key_pos = array_search( $key, array_keys( $array ), true );

	if ( 'after' === $position ) {
		$key_pos++;
	}

	if ( false !== $key_pos ) {
		$result = array_slice( $array, 0, $key_pos );
		$result = array_merge( $result, $pairs );
		$result = array_merge( $result, array_slice( $array, $key_pos ) );
	} else {
		$result = array_merge( $array, $pairs );
	}

	return $result;
}

/**
 * Get background color based on site layout.
 *
 * @since  1.0.0
 * @return string Background color.
 */
function sinatra_get_background_color() {

	$site_layout = sinatra_get_site_layout();

	if ( in_array( $site_layout, array( 'boxed', 'boxed-separated' ), true ) ) {
		$background_color = sinatra_option( 'boxed_content_background_color' );
	} else {
		$background_color = '#' . get_background_color();
	}

	return $background_color;
}

/**
 * Check if a section is disabled.
 *
 * @since 1.1.0
 *
 * @param  array $disabled_on Array of pages where the section is disabled.
 * @param  int   $post_id     Current page ID.
 * @return bool               Section is displayed.
 */
function sinatra_is_section_disabled( $disabled_on = array(), $post_id = 0 ) {

	$disabled = false;

	if ( is_front_page() && in_array( 'home', $disabled_on, true ) ) {
		$disabled = true;
	} elseif ( is_home() && in_array( 'posts_page', $disabled_on, true ) ) {
		$disabled = true;
	} elseif ( is_search() && in_array( 'search', $disabled_on, true ) ) {
		$disabled = true;
	} elseif ( is_archive() && in_array( 'archive', $disabled_on, true ) ) {
		$disabled = true;
	} elseif ( is_404() && in_array( '404', $disabled_on, true ) ) {
		$disabled = true;
	} elseif ( ( is_singular() || ! empty( $post_id ) ) && ! is_front_page() ) {

		if ( empty( $post_id ) ) {
			$post_id = sinatra_get_the_id();
		}

		if ( in_array( get_post_type( $post_id ), $disabled_on, true ) ) {
			$disabled = true;
		}
	}

	return $disabled;
}

/**
 * Get all the registered image sizes along with their dimensions.
 *
 * @since 1.1.0
 * @return array $image_sizes The image sizes
 */
function sinatra_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$default_image_sizes = get_intermediate_image_sizes();

	foreach ( $default_image_sizes as $size ) {
		$image_sizes[ $size ]['width']  = intval( get_option( "{$size}_size_w" ) );
		$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
		$image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}

	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	}

	$image_sizes['full'] = array(
		'width'  => '',
		'height' => '',
		'crop'   => '',
	);

	return $image_sizes;
}

if ( ! function_exists( 'sinatra_display_notices' ) ) :
	/**
	 * Display notices.
	 */
	function sinatra_display_notices() {
		return defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG || defined( 'WP_DEBUG' ) && WP_DEBUG;
	}
endif;
