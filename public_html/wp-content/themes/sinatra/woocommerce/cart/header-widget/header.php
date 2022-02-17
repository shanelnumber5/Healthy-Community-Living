<?php
/**
 * Header Cart Widget dropdown header.
 *
 * @package Sinatra
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sinatra_cart_count    = WC()->cart->get_cart_contents_count();
$sinatra_cart_subtotal = WC()->cart->get_cart_subtotal();

?>
<div class="wc-cart-widget-header">
	<span class="si-cart-count">
		<?php
		/* translators: %s: the number of cart items; */
		echo wp_kses_post( sprintf( _n( '%s item', '%s items', $sinatra_cart_count, 'sinatra' ), $sinatra_cart_count ) );
		?>
	</span>

	<span class="si-cart-subtotal">
		<?php
		/* translators: %s is the cart subtotal. */
		echo wp_kses_post( sprintf( __( 'Subtotal: %s', 'sinatra' ), '<span>' . $sinatra_cart_subtotal . '</span>' ) );
		?>
	</span>
</div>
