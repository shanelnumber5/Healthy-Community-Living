/* global academee_color_schemes, academee_dependencies, Color */

/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api ) {

	"use strict";

	var cssTemplate = {},
		updateCSS = true,
		htmlEncoder = document.createElement('div');

	// Add Templates with color schemes
	for (var i in academee_color_schemes) {
		cssTemplate[i] = wp.template( 'academee-color-scheme-'+i );
	}
	// Add Template with theme fonts
	cssTemplate['theme_fonts'] = wp.template( 'academee-fonts' );
	
	// Set initial state of controls
	api.bind('ready', function() {

		// Add 'reset' button
		jQuery('#customize-header-actions #save').before('<input type="button" class="button customize-action-reset" value="'+academee_customizer_vars['msg_reset']+'">');
		jQuery('#customize-header-actions .customize-action-reset').on('click', function(e) {
			if (confirm(academee_customizer_vars['msg_reset_confirm'])) {
				api('reset_options').set(1);
				jQuery('#customize-header-actions #save').removeAttr('disabled').trigger('click');
				setTimeout(function() { location.reload(true); }, 1000);
			}
		});

		// Add 'Refresh' button
		jQuery('#customize-header-actions .spinner').after('<button class="button customize-action-refresh icon-spin3"></button>');
		jQuery('#customize-header-actions .customize-action-refresh').on('click', function(e) {
			api.previewer.send( 'refresh-preview' );
			setTimeout(function() { api.previewer.refresh(); }, 500);
			e.preventDefault();
			return false;
		});

		// Blur the "load fonts" fields - regenerate options lists in the font-family controls
		jQuery('#customize-theme-controls [id^="customize-control-load_fonts"]').on('focusout', academee_customizer_update_load_fonts);		

		// Click on the actions button
		jQuery('#customize-theme-controls .control-section .customize-control-button input[type="button"]').on('click', academee_customizer_actions);

		// Check dependencies in the each section
		jQuery('#customize-theme-controls .control-section').each(function () {
			academee_customizer_check_dependencies(jQuery(this));
		});

		// Init color controls
		jQuery('#customize-theme-controls .academee_scheme_editor_selector').on('change', function(e) {
			academee_customizer_change_color_scheme(jQuery(this).val());
		});
		jQuery('#customize-theme-controls .academee_scheme_editor_colors input').on('change', function(e) {
			academee_customizer_change_field_colors(jQuery(this), jQuery(this).val());
			academee_customizer_update_color_scheme(jQuery(this).attr('name'), jQuery(this).val());
		});
		// Color picker
		academee_color_picker();
		jQuery('#customize-theme-controls .iColorPicker').each(function() {
			var clr = jQuery(this).val();
			academee_customizer_change_field_colors(jQuery(this), clr);
		});
		jQuery('#customize-theme-controls').on('focus', '.iColorPicker', function (e) {
			academee_color_picker_show(null, jQuery(this), function(fld, clr) {
				academee_customizer_change_field_colors(fld, clr);
				fld.val(clr).trigger('change');
			});
		});
		
		
	});
	
	// On change any control - check for dependencies
	api.bind('change', function(obj) {
		//if (obj.id == 'scheme_storage') return;
		academee_customizer_check_dependencies(jQuery('#customize-theme-controls #customize-control-'+obj.id).parents('.control-section'));
		academee_customizer_refresh_preview(obj);
	});

	// Check for dependencies
	function academee_customizer_check_dependencies(cont) {
		cont.find('.customize-control').each(function() {
			var id = jQuery(this).attr('id');
			if (id == undefined) return;
			id = id.replace('customize-control-', '');
			var depend = false;
			for (var fld in academee_dependencies) {
				if (fld == id) {
					depend = academee_dependencies[id];
					break;
				}
			}
			if (depend) {
				var dep_cnt = 0, dep_all = 0;
				var dep_cmp = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
				var dep_strict = typeof depend.strict != 'undefined';
				var fld=null, val='';
				for (var i in depend) {
					if (i == 'compare' || i == 'strict') continue;
					dep_all++;
					fld = cont.find('[data-customize-setting-link="'+i+'"]');
					if (fld.length > 0) {
						val = fld.attr('type')=='checkbox' || fld.attr('type')=='radio' 
									? (fld.parents('.customize-control').find('[data-customize-setting-link]:checked').length > 0
										? fld.parents('.customize-control').find('[data-customize-setting-link]:checked').val()
										: 0
										)
									: fld.val();
						if (val===undefined) val = '';
						for (var j in depend[i]) {
							if ( 
								   (depend[i][j]=='not_empty' && val!='') 										// Main field value is not empty - show current field
								|| (depend[i][j]=='is_empty' && val=='')										// Main field value is empty - show current field
								|| (val!=='' && (!isNaN(depend[i][j]) 											// Main field value equal to specified value - show current field
													? val==depend[i][j]
													: (dep_strict 
															? val==depend[i][j]
															: val.indexOf(depend[i][j])==0
														)
												)
									)
								|| (val!='' && (''+depend[i][j]).charAt(0)=='^' && val.indexOf(depend[i][j].substr(1))==-1)	// Main field value not equal to specified value - show current field
							) {
								dep_cnt++;
								break;
							}
						}
					} else
						dep_all--;
					if (dep_cnt > 0 && dep_cmp == 'or')
						break;
				}
				if ((dep_cnt > 0 && dep_cmp == 'or') || (dep_cnt == dep_all && dep_cmp == 'and')) {
					jQuery(this).show().removeClass('academee_options_no_use');
				} else {
					jQuery(this).hide().addClass('academee_options_no_use');
				}
			}
		});
	}

	// Refresh preview area on change any control
	function academee_customizer_refresh_preview(obj) {
		var id = obj.id, val = obj(), opt = '', rule = '';
		if (obj.transport!='postMessage' && id.indexOf('load_fonts-')==-1) return;
		var processed = false;

		// Update the CSS whenever a color setting is changed.
		if (updateCSS) {
			// Any color in the scheme_storage is changed
			if (id == 'scheme_storage') {
				processed = true;

			// Any theme fonts parameter is changed
			} else {
				for (opt in academee_theme_fonts) {
					for (rule in academee_theme_fonts[opt]) {
						if (opt+'_'+rule == id) {
							// Store new value in the color table
							academee_customizer_update_theme_fonts(opt, rule, val);
							processed = true;
							break;
						}
					}
					if (processed) break;
				}
			}
			// Refresh CSS
			if (processed) academee_customizer_update_css();
		}
		// If not catch change above - send message to previewer
		if (!processed) {
			api.previewer.send( 'refresh-other-controls', {id: id, value: val} );
		}
	}
	
	// Actions buttons
	function academee_customizer_actions(e) {
		var action = jQuery(this).data('action');
		if (action == 'refresh') {
			api.previewer.send( 'refresh-preview' );
			setTimeout(function() { api.previewer.refresh(); }, 500);
		}
	}
	
	// Change color scheme - update colors and generate css
	function academee_customizer_change_color_scheme(value) {
		updateCSS = false;
		for (var opt in academee_color_schemes[value].colors) {
			var fld = jQuery('#customize-theme-controls .academee_scheme_editor_colors input[name="'+opt+'"]');
			if (fld.length == 0) continue;
			fld.val( academee_color_schemes[value].colors[opt] );
			academee_customizer_change_field_colors(fld, academee_color_schemes[value].colors[opt]);
		}
		updateCSS = true;
		academee_customizer_update_css();
	}

	// Store new value in the color table
	function academee_customizer_update_color_scheme(opt, value) {
		academee_color_schemes[jQuery('.academee_scheme_editor_selector').val()].colors[opt] = value;
		api('scheme_storage').set(academee_serialize(academee_color_schemes))
	}
	
	// Change color in the field
	function academee_customizer_change_field_colors(fld, clr) {
		var hsb = academee_hex2hsb(clr);
		fld.css({
			'backgroundColor': clr,
			'color': hsb['b'] < 70 ? '#fff' : '#000'
		});
	}

	
	// Store new value in the theme fonts
	function academee_customizer_update_theme_fonts(opt, rule, value) {
		academee_theme_fonts[opt][rule] = value;
	}
	
	// Change theme fonts options if load fonts is changed
	function academee_customizer_update_load_fonts() {
		var opt_list=[], i, tag, sel, opt, name='', family='', val='', new_val = '', sel_idx = 0;
		updateCSS = false;
		for (i=1; i<=academee_customizer_vars['max_load_fonts']; i++) {
			name = api('load_fonts-'+i+'-name')();
			if (name == '') continue;
			family = api('load_fonts-'+i+'-family')();
			opt_list.push([name, family]);
		}
		for (tag in academee_theme_fonts) {
			sel = api.control( tag+'_font-family' ).container.find( 'select' );
			if (sel.length == 1) {
				opt = sel.find('option');
				sel_idx = sel.find(':selected').index();
				new_val = '';
				for (i=0; i<opt_list.length; i++) {
					val = '"'+opt_list[i][0]+'"'+(opt_list[i][1]!='inherit' ? ','+opt_list[i][1] : '');
					if (sel_idx-1 == i) new_val = val;
					opt.eq(i+1).val(val).text(opt_list[i][0]);
				}
				if (opt_list.length < opt.length-1) {
					for (i=opt.length-1; i>opt_list.length; i--) {
						opt.eq(i).remove();
					}
				}
				api(tag+'_font-family').set(sel_idx > 0 && sel_idx <= opt_list.length && new_val ? new_val : 'inherit');
			}
		}
		updateCSS = true;
	}

	
	// Generate the CSS for the current Color Scheme and send it to the preview window
	function academee_customizer_update_css() {

		if (!updateCSS) return;
	
		var css = '';
		
		// Make theme specific fonts rules
		var fonts = academee_customizer_add_theme_fonts(academee_theme_fonts);

		// Make styles and add into css
		css += academee_customizer_prepare_html_value(cssTemplate['theme_fonts']( fonts ));

		// Add colors
		for (var scheme in academee_color_schemes) {
			
			var colors = [];
			
			// Copy all colors!
			for (var i in academee_color_schemes[scheme].colors) {
				colors[i] = academee_color_schemes[scheme].colors[i];
			}
			
			// Make theme specific colors and tints
			colors = academee_customizer_add_theme_colors(colors);

			// Make styles and add into css
			css += cssTemplate[scheme]( colors );
		}
		api.previewer.send( 'refresh-color-scheme-css', css );
	}

	// Add custom colors into color scheme
	// Attention! Don't forget setup custom colors also in the theme.styles.php
	function academee_customizer_add_theme_colors(colors) {
		colors.bg_color_0 = Color( colors.bg_color ).toCSS( 'rgba', 0 );
		colors.bg_color_02 = Color( colors.bg_color ).toCSS( 'rgba', 0.2 );
		colors.bg_color_07 = Color( colors.bg_color ).toCSS( 'rgba', 0.7 );
		colors.bg_color_08 = Color( colors.bg_color ).toCSS( 'rgba', 0.8 );
		colors.bg_color_09 = Color( colors.bg_color ).toCSS( 'rgba', 0.9 );
		colors.alter_bg_color_07 = Color( colors.alter_bg_color ).toCSS( 'rgba', 0.7 );
		colors.alter_bg_color_04 = Color( colors.alter_bg_color ).toCSS( 'rgba', 0.4 );
		colors.alter_bg_color_02 = Color( colors.alter_bg_color ).toCSS( 'rgba', 0.2 );
		colors.alter_bd_color_02 = Color( colors.alter_bd_color ).toCSS( 'rgba', 0.2 );
		colors.extra_bg_color_07 = Color( colors.extra_bg_color ).toCSS( 'rgba', 0.7 );
		colors.text_dark_07 = Color( colors.text_dark ).toCSS( 'rgba', 0.7 );
		colors.text_link_02 = Color( colors.text_link ).toCSS( 'rgba', 0.2 );
		colors.text_link_07 = Color( colors.text_link ).toCSS( 'rgba', 0.7 );
		colors.text_link_blend = academee_hsb2hex(academee_hex2hsb( colors.text_link, 2, -5, 5 ));
		colors.alter_link_blend = academee_hsb2hex(academee_hex2hsb( colors.alter_link, 2, -5, 5 ));
		return colors;
	}

	// Add custom theme fonts rules
	// Attention! Don't forget setup custom theme fonts rules also in the theme.styles.php
	function academee_customizer_add_theme_fonts(fonts) {
		var rez = [];
		for (var tag in fonts) {
			//rez[tag] = fonts[tag];
			rez[tag+'_font-family'] = typeof fonts[tag]['font-family'] != 'undefined' && fonts[tag]['font-family'] != '' && fonts[tag]['font-family'] != 'inherit'
												? 'font-family:' + fonts[tag]['font-family'] + ';' 
												: '';
			rez[tag+'_font-size'] = typeof fonts[tag]['font-size'] != 'undefined' && fonts[tag]['font-size'] != 'inherit'
												? 'font-size:' + academee_customizer_prepare_css_value(fonts[tag]['font-size']) + ";"
												: '';
			rez[tag+'_line-height'] = typeof fonts[tag]['line-height'] != 'undefined' && fonts[tag]['line-height'] != '' && fonts[tag]['line-height'] != 'inherit'
												? 'line-height:' + fonts[tag]['line-height'] + ";"
												: '';
			rez[tag+'_font-weight'] = typeof fonts[tag]['font-weight'] != 'undefined' && fonts[tag]['font-weight'] != '' && fonts[tag]['font-weight'] != 'inherit'
												? 'font-weight:' + fonts[tag]['font-weight'] + ";"
												: '';
			rez[tag+'_font-style'] = typeof fonts[tag]['font-style'] != 'undefined' && fonts[tag]['font-style'] != '' && fonts[tag]['font-style'] != 'inherit'
												? 'font-style:' + fonts[tag]['font-style'] + ";"
												: '';
			rez[tag+'_text-decoration'] = typeof fonts[tag]['text-decoration'] != 'undefined' && fonts[tag]['text-decoration'] != '' && fonts[tag]['text-decoration'] != 'inherit'
												? 'text-decoration:' + fonts[tag]['text-decoration'] + ";"
												: '';
			rez[tag+'_text-transform'] = typeof fonts[tag]['text-transform'] != 'undefined' && fonts[tag]['text-transform'] != '' && fonts[tag]['text-transform'] != 'inherit'
												? 'text-transform:' + fonts[tag]['text-transform'] + ";"
												: '';
			rez[tag+'_letter-spacing'] = typeof fonts[tag]['letter-spacing'] != 'undefined' && fonts[tag]['letter-spacing'] != '' && fonts[tag]['letter-spacing'] != 'inherit'
												? 'letter-spacing:' + fonts[tag]['letter-spacing'] + ";"
												: '';
			rez[tag+'_margin-top'] = typeof fonts[tag]['margin-top'] != 'undefined' && fonts[tag]['margin-top'] != '' && fonts[tag]['margin-top'] != 'inherit'
												? 'margin-top:' + academee_customizer_prepare_css_value(fonts[tag]['margin-top']) + ";"
												: '';
			rez[tag+'_margin-bottom'] = typeof fonts[tag]['margin-bottom'] != 'undefined' && fonts[tag]['margin-bottom'] != '' && fonts[tag]['margin-bottom'] != 'inherit'
												? 'margin-bottom:' + academee_customizer_prepare_css_value(fonts[tag]['margin-bottom']) + ";"
												: '';
		}
		return rez;
	}
	
	// Add ed to css value
	function academee_customizer_prepare_css_value(val) {
		if (val != '' && val != 'inherit') {
			var ed = val.substr(-1);
			if ('0'<=ed && ed<='9') val += 'px';
		}
		return val;
	}
	
	// Convert HTML entities in the css value
	function academee_customizer_prepare_html_value(val) {
		return val.replace(/\&quot\;/g, '"');
	}

} )( wp.customize );
