<?php
/**
 * Main Class powering Gutenframe Functionality.
 */
class EIG_Module_Gutenframe {
	/**
	 * This method effectively fires at 'init' priority 10, so earlier actions cannot be used.
	 * It's also instantiated inside an is_user_logged_in(), so no further auth checks are needed.
	 */
	public function __construct() {
		// write css
		add_action( 'admin_print_styles-post.php', array( $this, 'maybe_hide_core_admin_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'maybe_hide_core_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_and_maybe_enqueue_pymjs' ) );
		if ( self::is_gutenframe() ) {
			// remove X-Frame_Options: sameorigin header in Core
			// original added in /wp-includes/default-filters.php
			remove_action( 'admin_init', 'send_frame_options_header', 10 );
		}
	}

	/**
	 * Use CSS to Hide Left-Menu & Admin Bar
	 * Tweak Gutenberg to fill all available space that was reserved for the above.
	 */
	public function maybe_hide_core_admin_styles() {
		if ( self::is_gutenframe() ) {
			?>
			<style type="text/css">
				#adminmenumain,
				#wpadminbar {
					display: none !important; /* Hide Left Menu & Admin Bar */
				}

				#wpcontent {
					margin-left: 0px !important; /* nudge main content container to fill available space */
				}

				.edit-post-header {
					top: 0 !important;
					left: 0 !important;
				}

				.edit-post-layout__content {
					margin-left: 0px !important;
				}

				@media screen and (max-width: 600px) {
					#wpbody { /* override additional real estate for larger mobile admin bar */
						padding-top: 0px !important;
					}
				}

				@media (min-width: 782px) {
					/* both normally 88px; Admin Bar is 32px at this breakpoint. 88 - 56 */
					.edit-post-sidebar {
						top: 56px !important; 
					}
					.edit-post-layout__content {
						top: 56px !important;
					}
				}

				@media (min-width: 601px) and (max-width:781px) {
					.edit-post-layout__content {
						margin-top: -46px !important; /* tuck extra whitespace in middle media query */
					}
					.edit-post-sidebar {
						margin-top: -47px !important; /* same as above, 1 extra px to prevent double border in UI */
					}
				}
				/* Override Menu Items: Fullscreen and Manage All Reusable Blocks Menu Items */
				.components-popover__content > div:first-of-type > div[role="menu"] > button:nth-of-type(3),
				.components-popover__content a[href="edit.php?post_type=wp_block"] {
					display: none !important;
				}
			</style>
			<?php
		} // end gutenframe conditional
	}

	/**
	 * Registers pym.js to allow for responsive <iframe> embeds.
	 *
	 * @see http://blog.apps.npr.org/pym.js/
	 * @return void
	 */
	public function register_and_maybe_enqueue_pymjs() {
		wp_register_script(
			'gutenframe-pym',
			EIG_GUTENFRAME_PYM_URL,
			array(),
			'1.3.2',
			true
		);

		wp_add_inline_script(
			'gutenframe-pym',
			"var pymChild = new pym.Child({ xdomain: '\\\*\\.bluehost.com' });"
		);

		if ( self::is_gutenframe() ) {
			wp_enqueue_script( 'gutenframe-pym' );
		}
	}

	/**
	 * Test for query parameter gutenframe=bluehost and post.php
	 *
	 * @return boolean
	 */
	public static function is_gutenframe() {
		if ( self::is_create_or_edit_screen()
			&& ! empty( $_GET['gutenframe'] )
			&& 'bluehost' === sanitize_text_field( $_GET['gutenframe'] )
		) {
			return true;
		}

		return false;
	}

	/**
	 * This function cannot be used before muplugins_loaded. It must be loaded on 'plugins_loaded' or later.
	 *
	 * @return boolean
	 */
	public static function is_create_or_edit_screen() {
		global $pagenow;

		if ( ! empty( $pagenow )
			&& is_string( $pagenow )
			&& ( false !== strpos( $pagenow, 'post.php' ) || false !== strpos( $pagenow, 'post-new.php' ) )
		) {
			return true;
		}

		return false;
	}
}
