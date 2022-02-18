/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

// Load details by AJAX and show in the popup
jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";
	jQuery('.sc_dishes_popup:not(.inited)')
		.addClass('inited')
		.on('click', 'a', function(e) {
			trx_addons_dishes_show_details(jQuery(this).parents('.sc_dishes_item'), true);
			e.preventDefault();
			return false;
		});
	if (jQuery('.sc_dishes_popup.inited').length > 0) {
		jQuery('body:not(.sc_dishes_popup_inited)')
			.addClass('sc_dishes_popup_inited')
			.on('click', '#trx_addons_dishes_details_popup_overlay, .trx_addons_dishes_details_popup_close', function(e) {
				jQuery('#trx_addons_dishes_details_popup').fadeOut();
				jQuery('#trx_addons_dishes_details_popup_overlay').fadeOut();
			})
			.on('click', '.trx_addons_dishes_details_popup_prev,.trx_addons_dishes_details_popup_next', function(e) {
				var popup = jQuery('#trx_addons_dishes_details_popup');
				var dish_item = popup.data('dish_item');
				if (!dish_item || dish_item.length == 0) return;
				var dishes_items = dish_item.parents('.sc_dishes').find('.sc_dishes_item');
				var cur_idx = -1;
				dishes_items.each(function(idx) {
					if (jQuery(this).data('post_id') == dish_item.data('post_id')) cur_idx = idx;
				});
				if (cur_idx == -1) return;
				dish_item = jQuery(this).hasClass('trx_addons_dishes_details_popup_prev') 
								? (cur_idx > 0 ? dishes_items.eq(cur_idx-1) : false)
								: (cur_idx < dishes_items.length-1 ? dishes_items.eq(cur_idx+1) : false);
				if (!dish_item || dish_item.length == 0) return;
				popup.fadeOut();
				trx_addons_dishes_show_details(dish_item, false);
			});
	}
	
	function trx_addons_dishes_show_details(dish_item, show_overlay) {
		jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
			action: 'dishes_details',
			nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
			post_id: dish_item.data('post_id')
		}).done(function(response) {
			var rez = {};
			if (response=='' || response==0) {
				rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
			} else {
				try {
					rez = JSON.parse(response);
				} catch (e) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
					console.log(response);
				}
			}
			var msg = rez.error === '' ? rez.data : rez.error;
			var popup = jQuery('#trx_addons_dishes_details_popup');
			var overlay = jQuery('#trx_addons_dishes_details_popup_overlay');
			if (popup.length == 0) {
				jQuery('body').append(
					'<div id="trx_addons_dishes_details_popup_overlay"></div>'
					+ '<div id="trx_addons_dishes_details_popup">'
						+ '<div class="trx_addons_dishes_details_content"></div>'
						+ '<span class="trx_addons_dishes_details_popup_close trx_addons_icon-cancel"></span>'
						+ '<span class="trx_addons_dishes_details_popup_prev trx_addons_icon-left"></span>'
						+ '<span class="trx_addons_dishes_details_popup_next trx_addons_icon-right"></span>'
					+ '</div>');
				popup = jQuery('#trx_addons_dishes_details_popup');
				overlay = jQuery('#trx_addons_dishes_details_popup_overlay');
			}
			popup.data('dish_item', dish_item).find('.trx_addons_dishes_details_content').html(msg);
			if (show_overlay) overlay.fadeIn();
			popup.fadeIn();
		});
	}
});