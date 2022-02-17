<?php
/**
 * The style "default" of the Cart
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_cart');
if (trx_addons_exists_woocommerce()) {
	$cart_items = WC()->cart->get_cart_contents_count();
	$cart_summa = strip_tags(WC()->cart->get_cart_subtotal());

	?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_cart<?php
			if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
			if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
		?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"'; ?>>
		<span class="sc_layouts_item_icon sc_layouts_cart_icon trx_addons_icon-shopping-store-cart-"><span><?php echo esc_html($cart_summa) ?></span></span>
		<span class="sc_layouts_item_details sc_layouts_cart_details">
			<span class="sc_layouts_item_details_line1 sc_layouts_cart_label"><?php echo !empty($args['text']) ? esc_html($args['text']) : esc_html__('Shopping Cart', 'trx_addons'); ?></span>
			<span class="sc_layouts_item_details_line2 sc_layouts_cart_totals">

				<span class="sc_layouts_cart_items"><?php
					echo esc_html($cart_items) . ' ' . esc_html(_n('item', 'items', $cart_items, 'trx_addons'));
				?></span>
				- 
				<span class="sc_layouts_cart_summa"><?php trx_addons_show_layout($cart_summa); ?></span>
			</span>
		</span><!-- /.sc_layouts_cart_details -->
		<span class="sc_layouts_cart_items_short"><?php echo esc_html($cart_items); ?></span>
		<div class="sc_layouts_cart_widget widget_area">
			<span class="sc_layouts_cart_widget_close trx_addons_icon-cancel"></span>
			<?php the_widget( 'WC_Widget_Cart', 'title=&hide_if_empty=0' ); ?>
		</div><!-- /.sc_layouts_cart_widget -->
	</div><!-- /.sc_layouts_cart --><?php

	trx_addons_sc_layouts_showed('cart', true);
}
?>