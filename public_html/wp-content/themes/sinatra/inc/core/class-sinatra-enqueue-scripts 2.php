<?php
/**
 * Enqueue scripts & styles.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Enqueue and register scripts and styles.
 *
 * @since 1.0.0
 */
function sinatra_enqueues() {

	// Script debug.
	$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
	$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Enqueue theme stylesheet.
	wp_enqueue_style(
		'sinatra-styles',
		SINATRA_THEME_URI . '/assets/css/style' . $sinatra_suffix . '.css',
		false,
		SINATRA_THEME_VERSION,
		'all'
	);

	// Enqueue IE specific styles.
	wp_enqueue_style(
		'sinatra-ie',
		SINATRA_THEME_URI . '/assets/css/compatibility/ie' . $sinatra_suffix . '.css',
		false,
		SINATRA_THEME_VERSION,
		'all'
	);

	wp_style_add_data( 'sinatra-ie', 'conditional', 'IE' );

	// Enqueue HTML5 shiv.
	wp_register_script(
		'html5shiv',
		SINATRA_THEME_URI . '/assets/js/' . $sinatra_dir . 'vendors/html5' . $sinatra_suffix . '.js',
		array(),
		'3.7.3',
		true
	);

	// Load only on < IE9.
	wp_script_add_data(
		'html5shiv',
		'conditional',
		'lt IE 9'
	);

	// Flexibility.js for crossbrowser flex support.
	wp_enqueue_script(
		'sinatra-flexibility',
		SINATRA_THEME_URI . '/assets/js/' . $sinatra_dir . 'vendors/flexibility' . $sinatra_suffix . '.js',
		array(),
		SINATRA_THEME_VERSION,
		false
	);

	wp_add_inline_script(
		'sinatra-flexibility',
		'flexibility(document.documentElement);'
	);

	wp_script_add_data(
		'sinatra-flexibility',
		'conditional',
		'IE'
	);

	// Register ImagesLoaded library.
	wp_register_script(
		'imagesloaded',
		SINATRA_THEME_URI . '/assets/js/' . $sinatra_dir . 'vendors/imagesloaded' . $sinatra_suffix . '.js',
		array(),
		'4.1.4',
		true
	);

	// Register Sinatra slider.
	wp_register_script(
		'sinatra-slider',
		SINATRA_THEME_URI . '/assets/js/sinatra-slider' . $sinatra_suffix . '.js',
		array( 'imagesloaded' ),
		SINATRA_THEME_VERSION,
		true
	);

	// Load comment reply script if comments are open.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Enqueue main theme script.
	wp_enqueue_script(
		'sinatra-js',
		SINATRA_THEME_URI . '/assets/js/sinatra' . $sinatra_suffix . '.js',
		array(),
		SINATRA_THEME_VERSION,
		true
	);

	// Comment count used in localized strings.
	$comment_count = get_comments_number();

	// Localized variables so they can be used for translatable strings.
	$localized = array(
		'ajaxurl'               => esc_url( admin_url( 'admin-ajax.php' ) ),
		'nonce'                 => wp_create_nonce( 'sinatra-nonce' ),
		'responsive-breakpoint' => intval( sinatra_option( 'main_nav_mobile_breakpoint' ) ),
		'sticky-header'         => array(
			'enabled' => sinatra_option( 'sticky_header' ),
			'hide_on' => sinatra_option( 'sticky_header_hide_on' ),
		),
		'strings'               => array(
			/* translators: %s Comment count */
			'comments_toggle_show' => $comment_count > 0 ? esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $comment_count, 'sinatra' ), $comment_count ) ) : esc_html__( 'Leave a Comment', 'sinatra' ),
			'comments_toggle_hide' => esc_html__( 'Hide Comments', 'sinatra' ),
		),
	);

	wp_localize_script(
		'sinatra-js',
		'sinatra_vars',
		apply_filters( 'sinatra_localized', $localized )
	);

	// Enqueue google fonts.
	sinatra()->fonts->enqueue_google_fonts();

	// Add additional theme styles.
	do_action( 'sinatra_enqueue_scripts' );
}
add_action( 'wp_enqueue_scripts', 'sinatra_enqueues' );

/**
 * Skip link focus fix for IE11.
 *
 * @since 1.0.0
 *
 * @return void
 */
function sinatra_skip_link_focus_fix() {
	?>
	<script>
	!function(){var e=-1<navigator.userAgent.toLowerCase().indexOf("webkit"),t=-1<navigator.userAgent.toLowerCase().indexOf("opera"),n=-1<navigator.userAgent.toLowerCase().indexOf("msie");(e||t||n)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var e,t=location.hash.substring(1);/^[A-z0-9_-]+$/.test(t)&&(e=document.getElementById(t))&&(/^(?:a|select|input|button|textarea)$/i.test(e.tagName)||(e.tabIndex=-1),e.focus())},!1)}();
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'sinatra_skip_link_focus_fix' );

/**
 * Enqueue assets for the Block Editor.
 *
 * @since 1.0.0
 *
 * @return void
 */
function sinatra_block_editor_assets() {

	// RTL version.
	$rtl = is_rtl() ? '-rtl' : '';

	// Minified version.
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Enqueue block editor styles.
	wp_enqueue_style(
		'sinatra-block-editor-styles',
		SINATRA_THEME_URI . '/inc/admin/assets/css/sinatra-block-editor-styles' . $rtl . $min . '.css',
		false,
		SINATRA_THEME_VERSION,
		'all'
	);

	// Enqueue google fonts.
	sinatra()->fonts->enqueue_google_fonts();

	// Add dynamic CSS as inline style.
	wp_add_inline_style(
		'sinatra-block-editor-styles',
		apply_filters( 'sinatra_block_editor_dynamic_css', sinatra_dynamic_styles()->get_block_editor_css() )
	);
}
add_action( 'enqueue_block_editor_assets', 'sinatra_block_editor_assets' );
