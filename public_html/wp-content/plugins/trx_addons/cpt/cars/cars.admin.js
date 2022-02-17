/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";
	
	// Field "Maker" is changed - refresh states
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.cars_maker,select#trx_addons_maker,select[name="trx_addons_options_field_maker"]', function () {
		"use strict";
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('cars_maker')
							? fld.parents('.vc_edit-form-tab').find('select.cars_model')
							: (fld.attr('name')=='trx_addons_options_field_maker'
								? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_model"]')
								: fld.parents('form').find('select#trx_addons_model')
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('cars_maker')
							? slave_fld.parent().prev()
							: (fld.attr('name')=='trx_addons_options_field_maker'
								? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')
								: slave_fld.parents('form').find('label#trx_addons_model_label')
								);
			trx_addons_refresh_list('models', fld.val(), slave_fld, slave_lbl);
		}
	});
	
});