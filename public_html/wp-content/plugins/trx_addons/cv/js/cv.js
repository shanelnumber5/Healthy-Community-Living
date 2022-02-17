/**
 * CV Card scripts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

// Init handlers
jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";
	
	if (jQuery('.trx_addons_cv_section').length > 0) {
		// AJAX loader for the tabs
		jQuery('.trx_addons_cv_section_ajax').on( "tabsbeforeactivate", ".trx_addons_tabs", function( event, ui ) {
			if (ui.newPanel.data('need-content')) trx_addons_tab_content_loader(ui.newPanel, 1);
		});
	
		// AJAX loader for the pages
		jQuery('.trx_addons_cv_section_ajax').on( "click", '.trx_addons_pagination a', function(e) {
			var panel = jQuery(this).parents('.trx_addons_tabs_content');
			if (panel.length == 0) panel = jQuery(this).parents('.trx_addons_cv_section_content');
			trx_addons_tab_content_loader(panel, jQuery(this).data('page'));
			e.preventDefault();
			return false;
		});
	
		// Change URL on click on the sections or/and on the tabs
		jQuery('.trx_addons_cv_section').on( "click", '.trx_addons_cv_section_title, .trx_addons_tabs_titles > li > a', function(e) {
			trx_addons_document_set_location(trx_addons_add_to_url(location.href, {
				'section': jQuery(this).parents('.trx_addons_cv_section').data('section'),
				'tab': jQuery(this).hasClass('trx_addons_cv_section_title') ? '' : jQuery(this).data('tab')
			}));
			e.preventDefault();
			return false;
		});
	
		// Click on the Print and Download icons in the section header
		jQuery('.trx_addons_cv_section_title > a.trx_addons_cv_section_title_icon').on( "click", function(e) {
			if (!e) {
				window.event.cancelBubble = true;
			} else if (e.stopPropagation) {
				e.stopPropagation();
			}
		});
	
		// Collect section titles as navigation buttons
		if (jQuery('.trx_addons_cv_navi_buttons').length > 0) {
			var cont = jQuery('.trx_addons_cv_navi_buttons');
			var titles = '';
			var href = location.href;
			cont.find('.trx_addons_cv_section_title').each(function(idx) {
				var section = jQuery(this).parent().data('section');
				titles += '<a href="javascript:void()" class="trx_addons_cv_navi_buttons_item'+(href.indexOf('section='+section)>0 || (href.indexOf('section=')==-1 && idx==0) ? ' trx_addons_cv_navi_buttons_item_active' : '')+'"'
							+ ' data-idx="'+idx+'"'
							+ ' data-section="'+section+'"'
							+ ' title="'+jQuery(this).text()+'"'
							+ '></a>';
			});
			cont.append('<div class="trx_addons_cv_navi_buttons_area">'+titles+'</div>');
			cont.find('.trx_addons_cv_navi_buttons_area a').on('click', function(e) {
				cont.find('.trx_addons_cv_section').eq(jQuery(this).data('idx')).find('.trx_addons_cv_section_title').trigger('click');
				jQuery(this).parent().find('.trx_addons_cv_navi_buttons_item').removeClass('trx_addons_cv_navi_buttons_item_active');
				jQuery(this).addClass('trx_addons_cv_navi_buttons_item_active');
				e.preventDefault();
				return false;
			});
			// Change button's state on switch accordion
			jQuery(document).on('action.init_hidden_elements', trx_addons_cv_navi_buttons_state);
		}

		// Click on the Splash CV button - simple decrease header width
		jQuery('.trx_addons_cv_header .trx_addons_cv_button_cv2').on( "click", function(e) {
			jQuery('body').removeClass('trx_addons_cv_splash');
			e.preventDefault();
			return false;
		});
		
	}

	// Load tab's content
	function trx_addons_tab_content_loader(panel, page) {
		if (panel.html().replace(/\s/g, '')=='') 
			panel.html('<div style="min-height:64px;"></div>');
		else
			panel.find('> *').css('opacity', 0);
		panel.data('need-content', false).addClass('trx_addons_loading');
		jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
			nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
			action: 'trx_addons_ajax_get_posts',
			section: panel.parents('.trx_addons_cv_section').data('section'),
			tab: panel.data('tab'),
			page: page
		}).done(function(response) {
			panel.removeClass('trx_addons_loading');
			var rez = {};
			try {
				rez = JSON.parse(response);
			} catch (e) {
				rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
				console.log(response);
			}
			if (rez.error !== '') {
				panel.html('<div class="trx_addons_error">'+rez.error+'</div>');
			} else {
				panel.html(rez.data).fadeIn(function() {
					jQuery(document).trigger('action.init_shortcodes', [panel]);
					jQuery(document).trigger('action.init_hidden_elements', [panel]);
				});
			}
		});
	}
	
	// Change button's state on switch accordion
	function trx_addons_cv_navi_buttons_state(e, container) {
		var act = jQuery('.trx_addons_cv_section_title.ui-state-active');
		var buttons = jQuery('.trx_addons_cv_navi_buttons_item');
		if (act.length > 0 && buttons.length > 0) {
			buttons.removeClass('trx_addons_cv_navi_buttons_item_active');
			buttons.eq(act.parent().index()).addClass('trx_addons_cv_navi_buttons_item_active');
		}
	}
	
});
