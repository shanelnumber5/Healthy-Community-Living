/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).on('action.ready_trx_addons', function() {
	"use strict";

	var rows = jQuery('.sc_layouts_row_fixed');
	
	// If page contain fixed rows
	if (rows.length > 0) {
		
		// Add placeholders before each row
		rows.each(function() {
			if (!jQuery(this).next().hasClass('sc_layouts_row_fixed_placeholder'))
				jQuery(this).after('<div class="sc_layouts_row_fixed_placeholder" style="background-color:'+jQuery(this).css('background-color')+';"></div>');
		});
		jQuery(document).on('action.scroll_trx_addons', function() {
			trx_addons_cpt_layouts_fix_rows(rows, false);
		});
		jQuery(document).on('action.resize_trx_addons', function() {
			trx_addons_cpt_layouts_fix_rows(rows, true);
		});
	}

	function trx_addons_cpt_layouts_fix_rows(rows, resize) {
		
		if (jQuery(window).width() <= 960) {
			rows.removeClass('sc_layouts_row_fixed_on').css({'top': 'auto'});
			return;
		}
		
		var scroll_offset = jQuery(window).scrollTop();
		var admin_bar = jQuery('#wpadminbar');
		var rows_offset = Math.max(0, admin_bar.length > 0 && admin_bar.css('display')!='none' && admin_bar.css('position')=='fixed' 
							? admin_bar.height() 
							: 0);

		rows.each(function() {
			
			var placeholder = jQuery(this).next();
			var offset = parseInt(jQuery(this).hasClass('sc_layouts_row_fixed_on') ? placeholder.offset().top : jQuery(this).offset().top);
			if (isNaN(offset)) offset = 0;

			// Fix/unfix row
			if (scroll_offset + rows_offset <= offset) {
				if (jQuery(this).hasClass('sc_layouts_row_fixed_on')) {
					jQuery(this).removeClass('sc_layouts_row_fixed_on').css({'top': 'auto'});
				}
			} else {
				var h = jQuery(this).outerHeight();
				if (!jQuery(this).hasClass('sc_layouts_row_fixed_on')) {
					if (rows_offset + h < jQuery(window).height() * 0.33) {
						placeholder.height(h);
						jQuery(this).addClass('sc_layouts_row_fixed_on').css({'top': rows_offset+'px'});
						h = jQuery(this).outerHeight();
					}
				} else if (resize && jQuery(this).hasClass('sc_layouts_row_fixed_on') && jQuery(this).offset().top != rows_offset) {
					jQuery(this).css({'top': rows_offset+'px'});
				}
				rows_offset += h;
			}
		});
	}
});