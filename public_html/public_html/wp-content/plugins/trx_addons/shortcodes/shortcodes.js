/**
 * Shortcodes common scripts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */


(function() {

	"use strict";
	
	// Fullheight elements
	//jQuery(document).on('action.init_hidden_elements', trx_addons_sc_fullheight);
	//jQuery(document).on('action.init_shortcodes', trx_addons_sc_fullheight);
	//jQuery(document).on('action.resize_trx_addons', trx_addons_sc_fullheight);

	function trx_addons_sc_fullheight(e, container) {
	
		if (container === undefined) container = jQuery('body');
		if (container === undefined || container.length === undefined || container.length == 0) return;
	
		container.find('.trx_addons_stretch_height').each(function () {
			var fullheight_item = jQuery(this);
			// If item now invisible
			if (jQuery(this).parents('div:hidden,article:hidden').length > 0) {
				return;
			}
			var wh = 0;
			var fullheight_row = jQuery(this).parents('.vc_row-o-full-height');
			if (fullheight_row.length > 0) {
				wh = fullheight_row.css('height') != 'auto' ? fullheight_row.height() : 'auto';
			} else {
				if (screen.height > 1000) {
					var adminbar = jQuery('#wpadminbar');
					wh = jQuery(window).height() - (adminbar.length > 0 ? adminbar.height() : 0);
				} else
					wh = 'auto';
			}
			if (wh == 'auto' || wh > 0) fullheight_item.height(wh);
		});
	}


	// Equal height elements
	jQuery(document).on('action.resize_trx_addons', trx_addons_sc_equalheight);

	function trx_addons_sc_equalheight(e, container) {
		if (container === undefined) container = jQuery('body');
		if (container===undefined || container.length === undefined || container.length == 0) return;
		container.find('[data-equal-height],.trx_addons_equal_height').each(function () {
			var eh_wrap = jQuery(this);
			var eh_items_selector = eh_wrap.data('equal-height');
			if (eh_items_selector === undefined) eh_items_selector = '>*';
			var max_h = 0;
			var items = [];
			var row_y = 0;
			var i=0;
			eh_wrap.find(eh_items_selector).each(function() {
				var el = jQuery(this);
				el.css('visibility', 'hidden').height('auto');
				var el_height = el.height();
				var el_offset = el.offset().top;
				if (row_y == 0) row_y = el_offset;
				if (row_y < el_offset) {
					if (items.length > 0) {
						if (max_h > 0) {
							for (i=0; i<items.length; i++)
								items[i].css('visibility', 'visible').height(max_h);
						}
						items = [];
						max_h = 0;
					}
					row_y = el_offset;
				}
				if (el_height > max_h) max_h = el_height;
				items.push(el);
			});
			if (items.length > 0 && max_h > 0) {
				for (i=0; i<items.length; i++)
					items[i].css('visibility', 'visible').height(max_h);
			}
		});
	}

})();