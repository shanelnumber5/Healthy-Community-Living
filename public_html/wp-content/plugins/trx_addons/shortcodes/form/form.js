/**
 * Shortcode Contact form
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).on('action.init_shortcodes', function(e, container) {

	"use strict";
	
	// Contact form validate and submit
	if (container.find('.sc_form_form:not(.sc_form_custom):not(.inited)').length > 0) {
		container.find('.sc_form_form:not(.sc_form_custom):not(.inited)')
			.addClass('inited')
			.submit(function(e) {
				sc_form_validate(jQuery(this));
				e.preventDefault();
				return false;
			});
	}

	// Mark field as 'filled'
	jQuery('[class*="sc_input_hover_"] input, [class*="sc_input_hover_"] textarea').each(function() {
		sc_form_mark_filled(jQuery(this));
	});
	jQuery('[class*="sc_input_hover_"] input, [class*="sc_input_hover_"] textarea').on('blur change', function() {
		sc_form_mark_filled(jQuery(this));
	});
	jQuery('input, textarea, select').on('change', function() {
		jQuery(this).removeClass('trx_addons_field_error');
	});

	
	// Mark fields
	function sc_form_mark_filled(field) {
		if (field.val()!='')
			field.addClass('filled');
		else
			field.removeClass('filled');
	}
	
	// Validate form
	function sc_form_validate(form){
		var url = form.attr('action');
		if (url == '') return false;
		form.find('input').removeClass('trx_addons_error_field');
		var error = trx_addons_form_validate(form, {
				rules: [
					{
						field: "name",
						min_length: { value: 1,	 message: TRX_ADDONS_STORAGE['msg_field_name_empty'] },
					},
					{
						field: "email",
						min_length: { value: 1,	 message: TRX_ADDONS_STORAGE['msg_field_email_empty'] },
						mask: { value: TRX_ADDONS_STORAGE['email_mask'], message: TRX_ADDONS_STORAGE['msg_field_email_not_valid'] }
					},
					{
						field: "message",
						min_length: { value: 1,  message: TRX_ADDONS_STORAGE['msg_field_text_empty'] },
					}
				]
			});
	
		if (!error && url!='#') {
			jQuery.post(url, {
				action: "send_sc_form",
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				data: form.serialize()
			}).done(function(response) {
				var rez = {};
				try {
					rez = JSON.parse(response);
				} catch(e) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
					console.log(response);
				}
				var result = form.find(".trx_addons_message_box").toggleClass("trx_addons_message_box_error", false).toggleClass("trx_addons_message_box_success", false);
				if (rez.error === '') {
					form.get(0).reset();
					result.addClass("trx_addons_message_box_success").html(TRX_ADDONS_STORAGE['msg_send_complete']);
				} else {
					result.addClass("trx_addons_message_box_error").html(TRX_ADDONS_STORAGE['msg_send_error'] + ' ' + rez.error);
				}
				result.fadeIn().delay(3000).fadeOut();
			});
		}
		return !error;
	}
});