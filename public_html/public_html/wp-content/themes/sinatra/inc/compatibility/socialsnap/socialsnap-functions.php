<?php
/**
 * Sinatra Social Snap compatibility functions.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.1.0
 */

if ( ! function_exists( 'sinatra_entry_meta_shares' ) ) :
	/**
	 * Add share count information to entry meta.
	 *
	 * @since 1.1.0
	 */
	function sinatra_entry_meta_shares() {

		$share_count = socialsnap_get_total_share_count();

		// Icon.
		$icon = sinatra()->icons->get_meta_icon( 'share', sinatra()->icons->get_svg( 'share-2', array( 'aria-hidden' => 'true' ) ) );

		$output = sprintf(
			'<span class="share-count">%3$s%1$s %2$s</span>',
			socialsnap_format_number( $share_count ),
			esc_html( _n( 'Share', 'Shares', $share_count, 'sinatra' ) ),
			$icon
		);

		echo wp_kses( apply_filters( 'sinatra_entry_share_count', $output ), sinatra_get_allowed_html_tags() );
	}
endif;
