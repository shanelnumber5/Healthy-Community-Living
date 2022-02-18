/* global jQuery:false */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";

	// Add arrows to the WooCommerce categories on homepages
	jQuery('body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories .has_children > a').append('<span class="open_child_menu"></span>');

	// Open/Close submenu
	jQuery('body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories').on('click', 'ul.product-categories.plain li a .open_child_menu', function(e) {
		var $a = jQuery(this).parent();
		if ($a.siblings('ul:visible').length > 0)
			$a.siblings('ul').slideUp().parent().removeClass('opened');
		else {
			jQuery(this).parents('li').siblings('li').find('ul:visible').slideUp().parent().removeClass('opened');
			$a.siblings('ul').slideDown().parent().addClass('opened');
		}
		e.preventDefault();
		return false;
	});

	// Resize handlers
	jQuery(document).on('action.resize_trx_addons', function() {
		trx_addons_woocommerce_resize_actions();
	});
	trx_addons_woocommerce_resize_actions();

	// Switch popup menu / hierarchical list on product categories list placed in sidebar
	function trx_addons_woocommerce_resize_actions() {
		var cat_menu = jQuery('body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories');
		var sb = cat_menu.parents('.widget_area');
		if (sb.length > 0 && cat_menu.length > 0) {
			if (sb.width() == sb.parents('.content_wrap').width()) {
				if (cat_menu.hasClass('inited')) {
					cat_menu.removeClass('inited').addClass('plain').superfish('destroy');
					cat_menu.find('ul.animated').removeClass('animated').addClass('no_animated');
				}
			} else {
				if (!cat_menu.hasClass('inited')) {
					cat_menu.removeClass('plain').addClass('inited');
					cat_menu.find('ul.no_animated').removeClass('no_animated').addClass('animated');
					trx_addons_init_sfmenu('body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories');
				}
			}
		}
	}
	
	// Check available product variations
	jQuery('.variations_form.cart:not(.inited)').each(function() {
		var form = jQuery(this).addClass('inited');
		var trx_addons_attribs = form.find('.trx_addons_attrib_item');
		if (trx_addons_attribs.length == 0) return;
		// Click on our variations attribs
		trx_addons_attribs.on('click', function(e) {
			if (!jQuery(this).hasClass('trx_addons_attrib_disabled')) {
				jQuery(this).addClass('trx_addons_attrib_selected').siblings().removeClass('trx_addons_attrib_selected');
				var term = jQuery(this).data('value');
				var attrib = jQuery(this).parents('.trx_addons_attrib_extended').data('attrib');
				var select_box = jQuery(this).parents('.trx_addons_attrib_extended').parent().find('#'+attrib).trigger('touchstart');
				select_box.find('option:selected').removeAttr('selected');
				select_box.find('option[value="'+term+'"]').attr('selected', 'selected');
				select_box.trigger('change');
				trx_addons_woocommerce_check_variations(form);//, attrib
			}
			e.preventDefault();
			return false;
		});
		// Click on the default attrib
		var busy = false;
		form.find( '.variations select' ).on('click', function(e) {
			if (!busy) {
				busy = true;
				trx_addons_woocommerce_check_variations(form);
				busy = false;
			}
		});
		trx_addons_woocommerce_check_variations(form);
	});
	
	function trx_addons_woocommerce_check_variations(form, exclude) {
		setTimeout(function() {
			if (exclude == undefined) exclude = '';
			// Refresh selects
			form.find( '.variations select' ).each( function() {
				var select_box = jQuery(this);
				var attrib_box = select_box.siblings('.trx_addons_attrib_extended').length==1 
									? select_box.siblings('.trx_addons_attrib_extended')
									: select_box.parent().siblings('.trx_addons_attrib_extended');
				if (select_box.attr('id') != exclude) select_box.trigger('touchstart');
				attrib_box.find('.trx_addons_attrib_item').removeClass('trx_addons_attrib_selected').addClass('trx_addons_attrib_disabled');
				select_box.find('option').each(function() {
					attrib_box.find('.trx_addons_attrib_item[data-value="'+jQuery(this).val()+'"]')
								.removeClass('trx_addons_attrib_disabled')
								.toggleClass('trx_addons_attrib_selected', jQuery(this).get(0).selected);
				});
			});
		}, 10);
	}
});