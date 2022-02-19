<?php
/**
 * Header Cart Widget icon.
 *
 * @package Sinatra
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sinatra_cart_count = WC()->cart->get_cart_contents_count();
$sinatra_cart_icon  = apply_filters( 'sinatra_wc_cart_widget_icon', 'shopping-cart' );

?>
<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="si-cart">
	<?php echo sinatra()->icons->get_svg( $sinatra_cart_icon ); ?>
	<?php if ( $sinatra_cart_count > 0 ) { ?>
		<span class="si-cart-count"><?php echo esc_html( $sinatra_cart_count ); ?></span>
	<?php } ?>
</a>
