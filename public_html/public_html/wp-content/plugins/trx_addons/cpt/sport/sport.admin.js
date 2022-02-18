/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";
	
	// Add 'sport' to the 'Add New' link's URL in the competitions
	if (jQuery('body').hasClass('post-type-cpt_competitions')) {
		var sport = jQuery('select#cpt_competitions_sports').length == 1 ? jQuery('select#cpt_competitions_sports > option:selected').val() : jQuery('input#cpt_competitions_sports').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'cpt_competitions_sports': sport}));
		}

	// Add 'competition' to the 'Add New' link's URL  in the rounds and players
	} else if (jQuery('body').hasClass('post-type-cpt_rounds') || jQuery('body').hasClass('post-type-cpt_players')) {
		var competition = jQuery('select#competition').length == 1 ? jQuery('select#competition > option:selected').val() : jQuery('input#competition').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'competition': competition}));
		}

	// Add 'competition' and 'round' to the 'Add New' link's URL in the matches
	} else if (jQuery('body').hasClass('post-type-cpt_matches')) {
		var competition = jQuery('select#competition').length == 1 ? jQuery('select#competition > option:selected').val() : jQuery('input#competition').val();
		var cur_round = jQuery('select#round').length == 1 ? jQuery('select#round > option:selected').val() : jQuery('input#round').val();
		var add_new = jQuery('a.page-title-action');
		if (add_new.length > 0) {
			var href = jQuery('a.page-title-action').attr('href');
			jQuery('a.page-title-action').attr('href', trx_addons_add_to_url(href, {'competition': competition, 'round': cur_round}));
		}
		
	}
	
	// Field "Sport" changed - refresh competitions and rounds
	//--------------------------------------------------------
	jQuery('body').on('change', 'select.sport', function () {
		"use strict";
		var fld = jQuery(this);
		var comp_fld = fld.parents('.vc_edit-form-tab').find('select.competition');
		if (comp_fld.length > 0) {
			var comp_lbl = comp_fld.parent().prev();
			trx_addons_refresh_list('competitions', fld.val(), comp_fld, comp_lbl);
		}
	});
	jQuery('body').on('change', 'select.competition', function () {
		"use strict";
		var fld = jQuery(this);
		var round_fld = fld.parents('.vc_edit-form-tab').find('select.round');
		if (round_fld.length > 0) {
			var round_lbl = round_fld.parent().prev();
			trx_addons_refresh_list('rounds', fld.val(), round_fld, round_lbl);
		}
	});
	
});