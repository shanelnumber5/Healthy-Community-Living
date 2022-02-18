/**
 * Live-update changed settings in real time in the Customizer preview.
 */
( function( $ ) {

	"use strict";

	var $style = $('#academee-color-scheme-css'),
		api = wp.customize;

	// Prepare inline styles in the preview window
	if ( $style.length == 0 ) {
		$style = $('head').append( '<'+'style type="text/css" id="academee-color-scheme-css" />' ).find('#academee-color-scheme-css');
	}

	// Refresh preview without page reload when controls are changed
	api.bind( 'preview-ready', function() {

		// Change css when color scheme or separate color controls are changed
		api.preview.bind( 'refresh-color-scheme-css', function( css ) {
			$style.html( css );
		} );

		// Any other controls are changed
		api.preview.bind( 'refresh-other-controls', function( obj ) {
			var id = obj.id, val = obj.value;

			if (id == 'body_style') {
				$('body').removeClass('body_style_boxed body_style_wide body_style_fullwide').addClass('body_style_'+val);
				$(window).trigger('resize');

			} else if (id == 'color_scheme') {
				$('body,html').removeClass('scheme_default scheme_light scheme_dark scheme_black').addClass('scheme_'+val);

			} else if (id == 'header_scheme') {
				$('.top_panel').removeClass('scheme_default scheme_light scheme_dark scheme_black').addClass('scheme_'+val);

			} else if (id == 'menu_scheme') {
				$('.sc_layouts_menu, .menu_side_wrap, .menu_mobile').removeClass('scheme_default scheme_light scheme_dark scheme_black').addClass('scheme_'+val);

			} else if (id == 'sidebar_scheme') {
				$('.sidebar').removeClass('scheme_default scheme_light scheme_dark scheme_black').addClass('scheme_'+val);

			} else if (id == 'footer_scheme') {
				$('.footer_wrap, .footer_widgets_wrap, .footer_copyright_wrap').removeClass('scheme_default scheme_light scheme_dark scheme_black').addClass('scheme_'+val);

			} else if (id.indexOf('expand_content') == 0) {
				if ( $('body').hasClass('sidebar_hide') ) {
					if (val == 1)
						$('body').addClass('expand_content');
					else
						$('body').removeClass('expand_content');
				}

			} else if (id.indexOf('remove_margins') == 0) {
				if (val == 1)
					$('body').addClass('remove_margins');
				else
					$('body').removeClass('remove_margins');

			} else if (id.indexOf('sidebar_position') == 0) {
				if ($('body').hasClass('sidebar_show'))
					$('body').removeClass('sidebar_left sidebar_right').addClass('sidebar_'+val);

			} else if (id == 'blogname') {
				$('.sc_layouts_logo .logo_text').html( academee_prepare_macros(val) );

			} else if (id == 'blogdescription') {
				$( '.sc_layouts_logo .logo_slogan' ).html( academee_prepare_macros(val) );

			} else if (id == 'copyright') {
				$( '.copyright_text' ).html( academee_prepare_macros(val) );

			}
		} );
				
	} );

} )( jQuery );