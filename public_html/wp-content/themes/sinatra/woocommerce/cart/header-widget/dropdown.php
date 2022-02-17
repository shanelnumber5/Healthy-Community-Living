<?php
/**
 * Header Cart Widget dropdown.
 *
 * @package Sinatra
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="wc-cart-dropdown" class="dropdown-item">

	<?php
	if ( WC()->cart->get_cart_contents_count() < 1 ) {
		wc_get_template_part( 'cart/header-widget/empty' );
	} else {
		wc_get_template_part( 'cart/header-widget/header' );
		wc_get_template_part( 'cart/header-widget/content' );
		wc_get_template_part( 'cart/header-widget/buttons' );
	}
	?>

</div>
