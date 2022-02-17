/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";
	
	// Field "Country" is changed - refresh states
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_country,select#trx_addons_country,select[name="trx_addons_options_field_country"]', function () {
		"use strict";
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_country')
							? fld.parents('.vc_edit-form-tab').find('select.properties_state')
							: (fld.attr('name')=='trx_addons_options_field_country'
								? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_state"]')
								: fld.parents('form').find('select#trx_addons_state')
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_country')
							? slave_fld.parent().prev()
							: (fld.attr('name')=='trx_addons_options_field_country'
								? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')
								: slave_fld.parents('form').find('label#trx_addons_state_label')
								);
			trx_addons_refresh_list('states', fld.val(), slave_fld, slave_lbl);
		}
	});

	// Field "State" is changed - refresh cities
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_state,select#trx_addons_state,select[name="trx_addons_options_field_state"]', function () {
		"use strict";
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_state')
							? fld.parents('.vc_edit-form-tab').find('select.properties_city')
							: (fld.attr('name')=='trx_addons_options_field_state'
								? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_city"]')
								: fld.parents('form').find('select#trx_addons_city')
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_state')
							? slave_fld.parent().prev()
							: (fld.attr('name')=='trx_addons_options_field_state'
								? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')
								: slave_fld.parents('form').find('label#trx_addons_city_label')
								);
			var country = 0;
			if (fld.val() == 0) {
				country = fld.hasClass('properties_state')
								? fld.parents('.vc_edit-form-tab').find('select.properties_country').val()
								: (fld.attr('name')=='trx_addons_options_field_state'
									? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_country"]').val()
									: fld.parents('form').find('select#trx_addons_country').val()
									);
			}
			trx_addons_refresh_list('cities', {'state': fld.val(), 'country': country}, slave_fld, slave_lbl);
		}
	});

	// Field "City" is changed - refresh neighborhoods
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.properties_city,select#trx_addons_city,select[name="trx_addons_options_field_city"]', function () {
		"use strict";
		var fld = jQuery(this);
		var slave_fld = fld.hasClass('properties_city')
							? fld.parents('.vc_edit-form-tab').find('select.properties_neighborhood')
							: (fld.attr('name')=='trx_addons_options_field_city'
								? fld.parents('.trx_addons_options_section').find('select[name="trx_addons_options_field_neighborhood"]')
								: fld.parents('form').find('select#trx_addons_neighborhood')
								);
		if (slave_fld.length > 0) {
			var slave_lbl = fld.hasClass('properties_city')
							? slave_fld.parent().prev()
							: (fld.attr('name')=='trx_addons_options_field_city'
								? slave_fld.parents('.trx_addons_options_item').find('.trx_addons_options_item_title')
								: slave_fld.parents('form').find('label#trx_addons_neighborhood_label')
								);
			trx_addons_refresh_list('neighborhoods', fld.val(), slave_fld, slave_lbl);
		}
	});
	
});