<?php
/**
 * Header Cart Widget cart & checkout buttons.
 *
 * @package Sinatra
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="si-cart-buttons">
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="si-btn btn-text-1" role="button">
		<span><?php esc_html_e( 'View Cart', 'sinatra' ); ?></span>
	</a>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="si-btn btn-fw" role="button">
		<span><?php esc_html_e( 'Checkout', 'sinatra' ); ?></span>
	</a>
</div>
