/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";

	// Added to cart
	if (jQuery('.sc_layouts_cart').length > 0) {
		jQuery('body:not(.added_to_cart_inited)').addClass('added_to_cart_inited').bind('added_to_cart', function() {
			// Update amount on the cart button
			var total = jQuery('.widget_shopping_cart').eq(0).find('.total .amount').text();
			if (total != undefined) {
				jQuery('.sc_layouts_cart_summa').text(total);
			}
			// Update count items on the cart button
			var cnt = 0;
			jQuery('.widget_shopping_cart_content').eq(0).find('.cart_list li').each(function() {
				var q = jQuery(this).find('.quantity').html().split(' ', 2);
				if (!isNaN(q[0]))
					cnt += Number(q[0]);
			});
			var items = jQuery('.sc_layouts_cart_items').eq(0).text().split(' ', 2);
			items[0] = cnt;
			jQuery('.sc_layouts_cart_items').text(items[0]+' '+items[1]);
			jQuery('.sc_layouts_cart_items_short').text(items[0]);
			// Update data-attr on button
			jQuery('.sc_layouts_cart').data({
				'items': cnt ? cnt : 0,
				'summa': total ? total : 0
			});
		});
		// Show/Hide cart 
		jQuery('.sc_layouts_cart:not(.inited)')
			.addClass('inited')
			.on('click', '.sc_layouts_cart_icon,.sc_layouts_cart_details', function(e) {
				var widget = jQuery(this).siblings('.sc_layouts_cart_widget');
				if (widget.length > 0 && widget.text().replace(/\s*/g, '')!='') {
					jQuery(this).siblings('.sc_layouts_cart_widget').slideToggle();
				}
				e.preventDefault();
				return false;
			})
			.on('click', '.sc_layouts_cart_widget_close', function(e) {
				jQuery(this).parent().slideUp();
				e.preventDefault();
				return false;
			});
	}
});