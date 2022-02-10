/**
 * Update Customizer settings live.
 *
 * @since 1.0.0
 */
(function ($) {
	'use strict';

	// Declare variables
	var api = wp.customize,
		$body = $('body'),
		$head = $('head'),
		$style_tag,
		$link_tag,
		sinatra_visibility_classes = 'sinatra-hide-mobile sinatra-hide-tablet sinatra-hide-mobile-tablet',
		sinatra_style_tag_collection = [],
		sinatra_link_tag_collection = [];

	/**
	 * Helper function to get style tag with id.
	 */
	function sinatra_get_style_tag(id) {
		if (sinatra_style_tag_collection[id]) {
			return sinatra_style_tag_collection[id];
		}

		$style_tag = $('head').find('#sinatra-dynamic-' + id);

		if (!$style_tag.length) {
			$('head').append('<style id="sinatra-dynamic-' + id + '" type="text/css" href="#"></style>');
			$style_tag = $('head').find('#sinatra-dynamic-' + id);
		}

		sinatra_style_tag_collection[id] = $style_tag;

		return $style_tag;
	}

	/**
	 * Helper function to get link tag with id.
	 */
	function sinatra_get_link_tag(id, url) {
		if (sinatra_link_tag_collection[id]) {
			return sinatra_link_tag_collection[id];
		}

		$link_tag = $('head').find('#sinatra-dynamic-link-' + id);

		if (!$link_tag.length) {
			$('head').append('<link id="sinatra-dynamic-' + id + '" type="text/css" rel="stylesheet" href="' + url + '"/>');
			$link_tag = $('head').find('#sinatra-dynamic-link-' + id);
		} else {
			$link_tag.attr('href', url);
		}

		sinatra_link_tag_collection[id] = $link_tag;

		return $link_tag;
	}

	/*
	 * Helper function to print visibility classes.
	 */
	function sinatra_print_visibility_classes($element, newval) {
		if (!$element.length) {
			return;
		}

		$element.removeClass(sinatra_visibility_classes);

		if ('all' !== newval) {
			$element.addClass('sinatra-' + newval);
		}
	}

	/*
	 * Helper function to convert hex to rgba.
	 */
	function sinatra_hex2rgba(hex, opacity) {
		if ('rgba' === hex.substring(0, 4)) {
			return hex;
		}

		// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF").
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;

		hex = hex.replace(shorthandRegex, function (m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

		if (opacity) {
			if (opacity > 1) {
				opacity = 1;
			}

			opacity = ',' + opacity;
		}

		if (result) {
			return 'rgba(' + parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16) + opacity + ')';
		}

		return false;
	}

	/**
	 * Helper function to lighten or darken the provided hex color.
	 */
	function sinatra_luminance(hex, percent) {
		// Convert RGB color to HEX.
		if (hex.includes('rgb')) {
			hex = sinatra_rgba2hex(hex);
		}

		// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF").
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;

		hex = hex.replace(shorthandRegex, function (m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

		var isColor = /^#[0-9A-F]{6}$/i.test(hex);

		if (!isColor) {
			return hex;
		}

		var from, to;

		for (var i = 1; i <= 3; i++) {
			result[i] = parseInt(result[i], 16);
			from = percent < 0 ? 0 : result[i];
			to = percent < 0 ? result[i] : 255;
			result[i] = result[i] + Math.ceil((to - from) * percent);
		}

		result = '#' + sinatra_dec2hex(result[1]) + sinatra_dec2hex(result[2]) + sinatra_dec2hex(result[3]);

		return result;
	}

	/**
	 * Convert dec to hex.
	 */
	function sinatra_dec2hex(c) {
		var hex = c.toString(16);
		return hex.length == 1 ? '0' + hex : hex;
	}

	/**
	 * Convert rgb to hex.
	 */
	function sinatra_rgba2hex(c) {
		var a, x;

		a = c.split('(')[1].split(')')[0].trim();
		a = a.split(',');

		var result = '';

		for (var i = 0; i < 3; i++) {
			x = parseInt(a[i]).toString(16);
			result += 1 === x.length ? '0' + x : x;
		}

		if (result) {
			return '#' + result;
		}

		return false;
	}

	/**
	 * Check if is light color.
	 */
	function sinatra_is_light_color(color = '') {
		var r, g, b, brightness;

		if (color.match(/^rgb/)) {
			color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
			r = color[1];
			g = color[2];
			b = color[3];
		} else {
			color = +('0x' + color.slice(1).replace(color.length < 5 && /./g, '$&$&'));
			r = color >> 16;
			g = (color >> 8) & 255;
			b = color & 255;
		}

		brightness = (r * 299 + g * 587 + b * 114) / 1000;

		return brightness > 137;
	}

	/**
	 * Detect if we should use a light or dark color on a background color.
	 */
	function sinatra_light_or_dark(color, dark = '#000000', light = '#FFFFFF') {
		return sinatra_is_light_color(color) ? dark : light;
	}

	/**
	 * Spacing field CSS.
	 */
	function sinatra_spacing_field_css(selector, property, setting, responsive) {
		if (!Array.isArray(setting) && 'object' !== typeof setting) {
			return;
		}

		// Set up unit.
		var unit = 'px',
			css = '';

		if ('unit' in setting) {
			unit = setting.unit;
		}

		var before = '',
			after = '';

		Object.keys(setting).forEach(function (index, el) {
			if ('unit' === index) {
				return;
			}

			if (responsive) {
				if ('tablet' === index) {
					before = '@media only screen and (max-width: 768px) {';
					after = '}';
				} else if ('mobile' === index) {
					before = '@media only screen and (max-width: 480px) {';
					after = '}';
				} else {
					before = '';
					after = '';
				}

				css += before + selector + '{';

				Object.keys(setting[index]).forEach(function (position) {
					if ('border' === property) {
						position += '-width';
					}

					if (setting[index][position]) {
						css += property + '-' + position + ': ' + setting[index][position] + unit + ';';
					}
				});

				css += '}' + after;
			} else {
				if ('border' === property) {
					index += '-width';
				}

				css += property + '-' + index + ': ' + setting[index] + unit + ';';
			}
		});

		if (!responsive) {
			css = selector + '{' + css + '}';
		}

		return css;
	}

	/**
	 * Range field CSS.
	 */
	function sinatra_range_field_css(selector, property, setting, responsive, unit) {
		var css = '',
			before = '',
			after = '';

		if (responsive && (Array.isArray(setting) || 'object' === typeof setting)) {
			Object.keys(setting).forEach(function (index, el) {
				if (setting[index]) {
					if ('tablet' === index) {
						before = '@media only screen and (max-width: 768px) {';
						after = '}';
					} else if ('mobile' === index) {
						before = '@media only screen and (max-width: 480px) {';
						after = '}';
					} else if ('desktop' === index) {
						before = '';
						after = '';
					} else {
						return;
					}

					css += before + selector + '{' + property + ': ' + setting[index] + unit + '; }' + after;
				}
			});
		}

		if (!responsive) {
			if (setting.value) {
				setting = setting.value;
			} else {
				setting = 0;
			}

			css = selector + '{' + property + ': ' + setting + unit + '; }';
		}

		return css;
	}

	/**
	 * Typography field CSS.
	 */
	function sinatra_typography_field_css(selector, setting) {
		var css = '';

		css += selector + '{';

		if ('default' === setting['font-family']) {
			css += 'font-family: ' + sinatra_customizer_preview.default_system_font + ';';
		} else if (setting['font-family'] in sinatra_customizer_preview.fonts.standard_fonts.fonts) {
			css += 'font-family: ' + sinatra_customizer_preview.fonts.standard_fonts.fonts[setting['font-family']]['fallback'] + ';';
		} else if ('inherit' !== setting['font-family']) {
			css += 'font-family: "' + setting['font-family'] + '";';
		}

		css += 'font-weight:' + setting['font-weight'] + ';';
		css += 'font-style:' + setting['font-style'] + ';';
		css += 'text-transform:' + setting['text-transform'] + ';';

		if ('text-decoration' in setting) {
			css += 'text-decoration:' + setting['text-decoration'] + ';';
		}

		if ('letter-spacing' in setting) {
			css += 'letter-spacing:' + setting['letter-spacing'] + setting['letter-spacing-unit'] + ';';
		}

		if ('line-height-desktop' in setting) {
			css += 'line-height:' + setting['line-height-desktop'] + ';';
		}

		if ('font-size-desktop' in setting && 'font-size-unit' in setting) {
			css += 'font-size:' + setting['font-size-desktop'] + setting['font-size-unit'] + ';';
		}

		css += '}';

		if ('font-size-tablet' in setting && setting['font-size-tablet']) {
			css += '@media only screen and (max-width: 768px) {' + selector + '{' + 'font-size: ' + setting['font-size-tablet'] + setting['font-size-unit'] + ';' + '}' + '}';
		}

		if ('line-height-tablet' in setting && setting['line-height-tablet']) {
			css += '@media only screen and (max-width: 768px) {' + selector + '{' + 'line-height:' + setting['line-height-tablet'] + ';' + '}' + '}';
		}

		if ('font-size-mobile' in setting && setting['font-size-mobile']) {
			css += '@media only screen and (max-width: 480px) {' + selector + '{' + 'font-size: ' + setting['font-size-mobile'] + setting['font-size-unit'] + ';' + '}' + '}';
		}

		if ('line-height-mobile' in setting && setting['line-height-mobile']) {
			css += '@media only screen and (max-width: 480px) {' + selector + '{' + 'line-height:' + setting['line-height-mobile'] + ';' + '}' + '}';
		}

		return css;
	}

	/**
	 * Load google font.
	 */
	function sinatra_enqueue_google_font(font) {
		if (sinatra_customizer_preview.fonts.google_fonts.fonts[font]) {
			var id = 'google-font-' + font.trim().toLowerCase().replace(' ', '-');
			var url = sinatra_customizer_preview.google_fonts_url + '/css?family=' + font + ':' + sinatra_customizer_preview.google_font_weights;

			var tag = sinatra_get_link_tag(id, url);
		}
	}

	/**
	 * Design Options field CSS.
	 */
	function sinatra_design_options_css(selector, setting, type) {
		var css = '',
			before = '',
			after = '';

		if ('background' === type) {
			var bg_type = setting['background-type'];

			css += selector + '{';

			if ('color' === bg_type) {
				setting['background-color'] = setting['background-color'] ? setting['background-color'] : 'inherit';
				css += 'background: ' + setting['background-color'] + ';';
			} else if ('gradient' === bg_type) {
				css += 'background: ' + setting['gradient-color-1'] + ';';

				if ('linear' === setting['gradient-type']) {
					css +=
						'background: -webkit-linear-gradient(' +
						setting['gradient-linear-angle'] +
						'deg, ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);' +
						'background: -o-linear-gradient(' +
						setting['gradient-linear-angle'] +
						'deg, ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);' +
						'background: linear-gradient(' +
						setting['gradient-linear-angle'] +
						'deg, ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);';
				} else if ('radial' === setting['gradient-type']) {
					css +=
						'background: -webkit-radial-gradient(' +
						setting['gradient-position'] +
						', circle, ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);' +
						'background: -o-radial-gradient(' +
						setting['gradient-position'] +
						', circle, ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);' +
						'background: radial-gradient(circle at ' +
						setting['gradient-position'] +
						', ' +
						setting['gradient-color-1'] +
						' ' +
						setting['gradient-color-1-location'] +
						'%, ' +
						setting['gradient-color-2'] +
						' ' +
						setting['gradient-color-2-location'] +
						'%);';
				}
			} else if ('image' === bg_type) {
				css +=
					'' +
					'background-image: url(' +
					setting['background-image'] +
					');' +
					'background-size: ' +
					setting['background-size'] +
					';' +
					'background-attachment: ' +
					setting['background-attachment'] +
					';' +
					'background-position: ' +
					setting['background-position-x'] +
					'% ' +
					setting['background-position-y'] +
					'%;' +
					'background-repeat: ' +
					setting['background-repeat'] +
					';';
			}

			css += '}';

			// Background image color overlay.
			if ('image' === bg_type && setting['background-color-overlay'] && setting['background-image']) {
				css += selector + '::after { background-color: ' + setting['background-color-overlay'] + '; }';
			} else {
				css += selector + '::after { background-color: initial; }';
			}
		} else if ('color' === type) {
			setting['text-color'] = setting['text-color'] ? setting['text-color'] : 'inherit';
			setting['link-color'] = setting['link-color'] ? setting['link-color'] : 'inherit';
			setting['link-hover-color'] = setting['link-hover-color'] ? setting['link-hover-color'] : 'inherit';

			css += selector + ' { color: ' + setting['text-color'] + '; }';
			css += selector + ' a { color: ' + setting['link-color'] + '; }';
			css += selector + ' a:hover { color: ' + setting['link-hover-color'] + ' !important; }';
		} else if ('border' === type) {
			setting['border-color'] = setting['border-color'] ? setting['border-color'] : 'inherit';
			setting['border-style'] = setting['border-style'] ? setting['border-style'] : 'solid';
			setting['border-left-width'] = setting['border-left-width'] ? setting['border-left-width'] : 0;
			setting['border-top-width'] = setting['border-top-width'] ? setting['border-top-width'] : 0;
			setting['border-right-width'] = setting['border-right-width'] ? setting['border-right-width'] : 0;
			setting['border-bottom-width'] = setting['border-bottom-width'] ? setting['border-bottom-width'] : 0;

			css += selector + '{';
			css += 'border-color: ' + setting['border-color'] + ';';
			css += 'border-style: ' + setting['border-style'] + ';';
			css += 'border-left-width: ' + setting['border-left-width'] + 'px;';
			css += 'border-top-width: ' + setting['border-top-width'] + 'px;';
			css += 'border-right-width: ' + setting['border-right-width'] + 'px;';
			css += 'border-bottom-width: ' + setting['border-bottom-width'] + 'px;';
			css += '}';
		} else if ('separator_color' === type) {
			css += selector + ':after{ background-color: ' + setting['separator-color'] + '; }';
		}

		return css;
	}

	/**
	 * Logo max height.
	 */
	api('sinatra_logo_max_height', function (value) {
		value.bind(function (newval) {
			var $logo = $('.sinatra-logo');

			if (!$logo.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_logo_max_height');
			var style_css = '';

			style_css += sinatra_range_field_css('.sinatra-logo img', 'max-height', newval, true, 'px');
			style_css += sinatra_range_field_css('.sinatra-logo img.si-svg-logo', 'height', newval, true, 'px');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Logo text font size.
	 */
	api('sinatra_logo_text_font_size', function (value) {
		value.bind(function (newval) {
			var $logo = $('#sinatra-header .sinatra-logo .site-title');

			if (!$logo.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_logo_text_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('#sinatra-header .sinatra-logo .site-title', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Logo margin.
	 */
	api('sinatra_logo_margin', function (value) {
		value.bind(function (newval) {
			var $logo = $('.sinatra-logo');

			if (!$logo.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_logo_margin');

			var style_css = sinatra_spacing_field_css('.sinatra-logo .logo-inner', 'margin', newval, true);
			$style_tag.html(style_css);
		});
	});

	/**
	 * Tagline.
	 */
	api('blogdescription', function (value) {
		value.bind(function (newval) {
			if ($('.sinatra-logo').find('.site-description').length) {
				$('.sinatra-logo').find('.site-description').html(newval);
			}
		});
	});

	/**
	 * Site Title.
	 */
	api('blogname', function (value) {
		value.bind(function (newval) {
			if ($('.sinatra-logo').find('.site-title').length) {
				$('.sinatra-logo').find('.site-title').find('a').html(newval);
			}
		});
	});

	/**
	 * Site Layout.
	 */
	api('sinatra_site_layout', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-layout__\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-layout__' + newval);
		});
	});

	/**
	 * Sticky Sidebar.
	 */
	api('sinatra_sidebar_sticky', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)si-sticky-\S+/g) || []).join(' ');
			});

			if (newval) {
				$body.addClass('si-sticky-' + newval);
			}
		});
	});

	/**
	 * Sidebar width.
	 */
	api('sinatra_sidebar_width', function (value) {
		value.bind(function (newval) {
			var $sidebar = $('#secondary');

			if (!$sidebar.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_sidebar_width');
			var style_css = '#secondary { width: ' + newval.value + '%; }';
			style_css += 'body:not(.sinatra-no-sidebar) #primary { ' + 'max-width: ' + (100 - parseInt(newval.value)) + '%;' + '};';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Sidebar style.
	 */
	api('sinatra_sidebar_style', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-sidebar-style-\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-sidebar-style-' + newval);
		});
	});

	/**
	 * Responsive sidebar position.
	 */
	api('sinatra_sidebar_responsive_position', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)si-sidebar-r__\S+/g) || []).join(' ');
			});

			if (newval) {
				$body.addClass('si-sidebar-r__' + newval);
			}
		});
	});

	/**
	 * Featured Image Position (Horizontal Blog layout)
	 */
	api('sinatra_blog_image_position', function (value) {
		value.bind(function (newval) {
			$('.si-blog-entry-wrapper').removeClass(function (index, className) {
				return (className.match(/(^|\s)si-thumb-\S+/g) || []).join(' ');
			});

			$('.si-blog-entry-wrapper').addClass('si-thumb-' + newval);
		});
	});

	/**
	 * Single page - title in header alignment.
	 */
	api('sinatra_single_title_alignment', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)si-page-title-align-\S+/g) || []).join(' ');
			});

			$body.addClass('si-page-title-align-' + newval);
		});
	});

	/**
	 * Single Page title spacing.
	 */
	api('sinatra_single_title_spacing', function (value) {
		value.bind(function (newval) {
			var $page_header = $('.page-header');

			if (!$page_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_single_title_spacing');

			var style_css = sinatra_spacing_field_css('.si-single-title-in-page-header #page .page-header .si-page-header-wrapper', 'padding', newval, true);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Single post narrow container width.
	 */
	api('sinatra_single_narrow_container_width', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_single_narrow_container_width');
			var style_css = '';

			style_css +=
				'.single-post.narrow-content .entry-content > :not([class*="align"]):not([class*="gallery"]):not(.wp-block-image):not(.quote-inner):not(.quote-post-bg), ' +
				'.single-post.narrow-content .mce-content-body:not([class*="page-template-full-width"]) > :not([class*="align"]):not([data-wpview-type*="gallery"]):not(blockquote):not(.mceTemp), ' +
				'.single-post.narrow-content .entry-footer, ' +
				'.single-post.narrow-content .post-nav, ' +
				'.single-post.narrow-content .entry-content > .alignwide, ' +
				'.single-post.narrow-content p.has-background:not(.alignfull):not(.alignwide)' +
				'.single-post.narrow-content #sinatra-comments-toggle, ' +
				'.single-post.narrow-content #comments, ' +
				'.single-post.narrow-content .entry-content .aligncenter, ' +
				'.single-post.narrow-content .si-narrow-element, ' +
				'.single-post.narrow-content.si-single-title-in-content .entry-header, ' +
				'.single-post.narrow-content.si-single-title-in-content .entry-meta, ' +
				'.single-post.narrow-content.si-single-title-in-content .post-category, ' +
				'.single-post.narrow-content.sinatra-no-sidebar .si-page-header-wrapper, ' +
				'.single-post.narrow-content.sinatra-no-sidebar .si-breadcrumbs > .si-container > nav {' +
				'max-width: ' +
				parseInt(newval.value) +
				'px; margin-left: auto; margin-right: auto; ' +
				'}';

			style_css += '.single-post.narrow-content .author-box, ' + '.single-post.narrow-content .entry-content > .alignwide { ' + 'max-width: ' + (parseInt(newval.value) + 70) + 'px;' + '}';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Single post content font size.
	 */
	api('sinatra_single_content_font_size', function (value) {
		value.bind(function (newval) {
			var $content = $('.single-post');

			if (!$content.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_single_content_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('.single-post .entry-content', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Header container width.
	 */
	api('sinatra_header_container_width', function (value) {
		value.bind(function (newval) {
			var $header = $('#sinatra-header');

			if (!$header.length) {
				return;
			}

			if ('full-width' === newval) {
				$header.addClass('si-container__wide');
			} else {
				$header.removeClass('si-container__wide');
			}
		});
	});

	/**
	 * Main navigation disply breakpoint.
	 */
	api('sinatra_main_nav_mobile_breakpoint', function (value) {
		value.bind(function (newval) {
			var $nav = $('#sinatra-header-inner .sinatra-nav');

			if (!$nav.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_main_nav_mobile_breakpoint');
			var style_css = '';

			style_css += '@media screen and (min-width: ' + parseInt(newval) + 'px) {#sinatra-header-inner .sinatra-nav {display:flex} .si-mobile-nav {display:none;} }';
			style_css += '@media screen and (max-width: ' + parseInt(newval) + 'px) {#sinatra-header-inner .sinatra-nav {display:none} .si-mobile-nav {display:inline-flex;} }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Mobile Menu Button Label.
	 */
	api('sinatra_main_nav_mobile_label', function (value) {
		value.bind(function (newval) {
			if ($('.si-hamburger-sinatra-primary-nav').find('.hamburger-label').length) {
				$('.si-hamburger-sinatra-primary-nav').find('.hamburger-label').html(newval);
			}
		});
	});

	/**
	 * Main Nav Font color.
	 */
	api('sinatra_main_nav_font_color', function (value) {
		value.bind(function (newval) {
			var $navigation = $('#sinatra-header-inner .sinatra-nav');

			if (!$navigation.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_main_nav_font_color');
			var style_css = '';

			// Link color.
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			style_css += '#sinatra-header-inner .sinatra-nav > ul > li > a { color: ' + newval['link-color'] + '; }';

			// Link hover color.
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : api.value('sinatra_accent_color')();
			style_css +=
				'#sinatra-header-inner .sinatra-nav > ul > li > a:hover, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.menu-item-has-children:hover > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-item > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-ancestor > a ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.page_item_has_children:hover > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current_page_item > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current_page_ancestor > a ' +
				'{ color: ' +
				newval['link-hover-color'] +
				'; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Main Nav Background.
	 */
	api('sinatra_main_nav_background', function (value) {
		value.bind(function (newval) {
			var $navigation = $('.sinatra-header-layout-3 .si-nav-container');

			if (!$navigation.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_main_nav_background');
			var style_css = sinatra_design_options_css('.sinatra-header-layout-3 .si-nav-container', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Main Nav Border.
	 */
	api('sinatra_main_nav_border', function (value) {
		value.bind(function (newval) {
			var $navigation = $('.sinatra-header-layout-3 .si-nav-container');

			if (!$navigation.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_main_nav_border');
			var style_css = sinatra_design_options_css('.sinatra-header-layout-3 .si-nav-container', newval, 'border');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Main Nav font size.
	 */
	api('sinatra_main_nav_font_size', function (value) {
		value.bind(function (newval) {
			var $nav = $('#sinatra-header-inner');

			if (!$nav.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_main_nav_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('.sinatra-nav.si-header-element, .sinatra-header-layout-1 .si-header-widgets, .sinatra-header-layout-2 .si-header-widgets', 'font-size', newval, false, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Top Bar container width.
	 */
	api('sinatra_top_bar_container_width', function (value) {
		value.bind(function (newval) {
			var $topbar = $('#sinatra-topbar');

			if (!$topbar.length) {
				return;
			}

			if ('full-width' === newval) {
				$topbar.addClass('si-container__wide');
			} else {
				$topbar.removeClass('si-container__wide');
			}
		});
	});

	/**
	 * Top Bar visibility.
	 */
	api('sinatra_top_bar_visibility', function (value) {
		value.bind(function (newval) {
			var $topbar = $('#sinatra-topbar');

			sinatra_print_visibility_classes($topbar, newval);
		});
	});

	/**
	 * Top Bar widgets separator.
	 */
	api('sinatra_top_bar_widgets_separator', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-topbar__separators-\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-topbar__separators-' + newval);
		});
	});

	/**
	 * Top Bar background.
	 */
	api('sinatra_top_bar_background', function (value) {
		value.bind(function (newval) {
			var $topbar = $('#sinatra-topbar');

			if (!$topbar.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_top_bar_background');
			var style_css = sinatra_design_options_css('#sinatra-topbar', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Top Bar color.
	 */
	api('sinatra_top_bar_text_color', function (value) {
		value.bind(function (newval) {
			var $topbar = $('#sinatra-topbar');

			if (!$topbar.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_top_bar_text_color');
			var style_css = '';

			newval['text-color'] = newval['text-color'] ? newval['text-color'] : 'inherit';
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : 'inherit';

			// Text color.
			style_css += '#sinatra-topbar { color: ' + newval['text-color'] + '; }';

			// Link color.
			style_css += '.si-topbar-widget__text a, ' + '.si-topbar-widget .sinatra-nav > ul > li > a, ' + '.si-topbar-widget__socials .sinatra-social-nav > ul > li > a, ' + '#sinatra-topbar .si-topbar-widget__text .si-icon { color: ' + newval['link-color'] + '; }';

			// Link hover color.
			style_css +=
				'#sinatra-topbar .sinatra-nav > ul > li > a:hover, ' +
				'#sinatra-topbar .sinatra-nav > ul > li.menu-item-has-children:hover > a,  ' +
				'#sinatra-topbar .sinatra-nav > ul > li.current-menu-item > a, ' +
				'#sinatra-topbar .sinatra-nav > ul > li.current-menu-ancestor > a, ' +
				'#sinatra-topbar .si-topbar-widget__text a:hover, ' +
				'#sinatra-topbar .sinatra-social-nav > ul > li > a .si-icon.bottom-icon { color: ' +
				newval['link-hover-color'] +
				'; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Top Bar border.
	 */
	api('sinatra_top_bar_border', function (value) {
		value.bind(function (newval) {
			var $topbar = $('#sinatra-topbar');

			if (!$topbar.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_top_bar_border');
			var style_css = sinatra_design_options_css('#sinatra-topbar', newval, 'border');

			style_css += sinatra_design_options_css('#sinatra-topbar .si-topbar-widget', newval, 'separator_color');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Header menu item hover animation.
	 */
	api('sinatra_main_nav_hover_animation', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-menu-animation-\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-menu-animation-' + newval);
		});
	});

	/**
	 * Header widgets separator.
	 */
	api('sinatra_header_widgets_separator', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-header__separators-\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-header__separators-' + newval);
		});
	});

	/**
	 * Header background.
	 */
	api('sinatra_header_background', function (value) {
		value.bind(function (newval) {
			var $header = $('#sinatra-header-inner');

			if (!$header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_header_background');
			var style_css = sinatra_design_options_css('#sinatra-header-inner', newval, 'background');

			if ('color' === newval['background-type'] && newval['background-color']) {
				style_css += '.si-header-widget__cart .si-cart .si-cart-count { border: 2px solid ' + newval['background-color'] + '; }';
			} else {
				style_css += '.si-header-widget__cart .si-cart .si-cart-count { border: none; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Header font color.
	 */
	api('sinatra_header_text_color', function (value) {
		value.bind(function (newval) {
			var $header = $('#sinatra-header');

			if (!$header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_header_text_color');
			var style_css = '';

			// Text color.
			style_css += '.sinatra-logo .site-description { color: ' + newval['text-color'] + '; }';

			// Link color.
			if (newval['link-color']) {
				style_css += '#sinatra-header, ' + '.si-header-widgets a:not(.si-btn), ' + '.sinatra-logo a,' + '.si-hamburger { color: ' + newval['link-color'] + '; }';
				style_css += '.hamburger-inner,' + '.hamburger-inner::before,' + '.hamburger-inner::after { background-color: ' + newval['link-color'] + '; }';
			}

			// Link hover color.
			if (newval['link-hover-color']) {
				style_css +=
					'.si-header-widgets a:not(.si-btn):hover, ' +
					'#sinatra-header-inner .si-header-widgets .sinatra-active,' +
					'.sinatra-logo .site-title a:hover, ' +
					'.si-hamburger:hover .hamburger-label, ' +
					'.is-mobile-menu-active .si-hamburger .hamburger-label,' +
					'#sinatra-header-inner .sinatra-nav > ul > li > a:hover,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.menu-item-has-children:hover > a,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-item > a,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-ancestor > a,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.page_item_has_children:hover > a,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.current_page_item > a,' +
					'#sinatra-header-inner .sinatra-nav > ul > li.current_page_ancestor > a { color: ' +
					newval['link-hover-color'] +
					'; }';

				style_css +=
					'.si-hamburger:hover .hamburger-inner,' +
					'.si-hamburger:hover .hamburger-inner::before,' +
					'.si-hamburger:hover .hamburger-inner::after,' +
					'.is-mobile-menu-active .si-hamburger .hamburger-inner,' +
					'.is-mobile-menu-active .si-hamburger .hamburger-inner::before,' +
					'.is-mobile-menu-active .si-hamburger .hamburger-inner::after { background-color: ' +
					newval['link-hover-color'] +
					'; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Header border.
	 */
	api('sinatra_header_border', function (value) {
		value.bind(function (newval) {
			var $header = $('#sinatra-header-inner');

			if (!$header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_header_border');
			var style_css = sinatra_design_options_css('#sinatra-header-inner', newval, 'border');

			// Separator color.
			newval['separator-color'] = newval['separator-color'] ? newval['separator-color'] : 'inherit';
			style_css += '.si-header-widget:after { background-color: ' + newval['separator-color'] + '; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Hero container width.
	 */
	api('sinatra_hero_hover_slider_container', function (value) {
		value.bind(function (newval) {
			var $hero_container = $('#hero .si-hero-container');

			if (!$hero_container.length) {
				return;
			}

			if ('full-width' === newval) {
				$hero_container.addClass('si-container__wide');
			} else {
				$hero_container.removeClass('si-container__wide');
			}
		});
	});

	/**
	 * Hero overlay style.
	 */
	api('sinatra_hero_hover_slider_overlay', function (value) {
		value.bind(function (newval) {
			var $hero = $('#hero .si-hover-slider');

			if (!$hero.length) {
				return;
			}

			$hero
				.removeClass(function (index, className) {
					return (className.match(/(^|\s)slider-overlay-\S+/g) || []).join(' ');
				})
				.addClass('slider-overlay-' + newval);
		});
	});

	/**
	 * Hero height.
	 */
	api('sinatra_hero_hover_slider_height', function (value) {
		value.bind(function (newval) {
			var $hero = $('#hero');

			if (!$hero.length) {
				return;
			}

			$hero.find('.hover-slide-item').css('height', newval.value + 'px');
		});
	});

	/**
	 * Hero visibility.
	 */
	api('sinatra_hero_visibility', function (value) {
		value.bind(function (newval) {
			sinatra_print_visibility_classes($('#hero'), newval);
		});
	});

	/**
	 * Custom input style.
	 */
	api('sinatra_custom_input_style', function (value) {
		value.bind(function (newval) {
			if (newval) {
				$body.addClass('si-input-supported');
			} else {
				$body.removeClass('si-input-supported');
			}
		});
	});

	/**
	 * Pre Footer Call to Action Enable.
	 */
	api('sinatra_enable_pre_footer_cta', function (value) {
		value.bind(function (newval) {
			if (newval) {
				$body.addClass('si-pre-footer-cta-style-' + api.value('sinatra_pre_footer_cta_style')());
			} else {
				$body.removeClass(function (index, className) {
					return (className.match(/(^|\s)si-pre-footer-cta-style-\S+/g) || []).join(' ');
				});
			}
		});
	});

	/**
	 * Pre Footer Call to Action visibility.
	 */
	api('sinatra_pre_footer_cta_visibility', function (value) {
		value.bind(function (newval) {
			var $cta = $('.si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			sinatra_print_visibility_classes($cta, newval);
		});
	});

	/**
	 * Pre Footer Call to Action Text.
	 */
	api('sinatra_pre_footer_cta_text', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			$cta.find('p.h3').html(newval);
		});
	});

	/**
	 * Pre Footer Call to Action Style.
	 */
	api('sinatra_pre_footer_cta_style', function (value) {
		value.bind(function (newval) {
			$body
				.removeClass(function (index, className) {
					return (className.match(/(^|\s)si-pre-footer-cta-style-\S+/g) || []).join(' ');
				})
				.addClass('si-pre-footer-cta-style-' + api.value('sinatra_pre_footer_cta_style')());
		});
	});

	/**
	 * Pre Footer Call to Action Button Text.
	 */
	api('sinatra_pre_footer_cta_btn_text', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			if (newval) {
				$cta.find('a').css('display', 'inline-flex').html(newval);
			} else {
				$cta.find('a').css('display', 'none').html('');
			}
		});
	});

	/**
	 * Pre Footer Call to Action Background.
	 */
	api('sinatra_pre_footer_cta_background', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_pre_footer_cta_background');
			var style_css = '';

			if ('color' === newval['background-type']) {
				style_css += sinatra_design_options_css('.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::before, .si-pre-footer-cta-style-2 #si-pre-footer::before', newval, 'background');
				style_css += '.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::after,' + '.si-pre-footer-cta-style-2 #si-pre-footer::after' + '{ background-image: none; }';
			} else {
				style_css += sinatra_design_options_css('.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::after', newval, 'background');
				style_css += sinatra_design_options_css('.si-pre-footer-cta-style-2 #si-pre-footer::after', newval, 'background');
			}

			if ('image' === newval['background-type'] && newval['background-color-overlay'] && newval['background-image']) {
				style_css += '.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::before,' + '.si-pre-footer-cta-style-2 #si-pre-footer::before' + '{ background-color: ' + newval['background-color-overlay'] + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Pre Footer Call to Action Text Color.
	 */
	api('sinatra_pre_footer_cta_text_color', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_pre_footer_cta_text_color');
			var style_css = '';

			style_css += sinatra_design_options_css('#si-pre-footer .h2', newval, 'color');
			style_css += sinatra_design_options_css('#si-pre-footer .h3', newval, 'color');
			style_css += sinatra_design_options_css('#si-pre-footer .h4', newval, 'color');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Pre Footer Call to Action Border.
	 */
	api('sinatra_pre_footer_cta_border', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_pre_footer_cta_border');
			var style_css = sinatra_design_options_css('.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::before, .si-pre-footer-cta-style-2 #si-pre-footer::before', newval, 'border');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Pre Footer CTA font size.
	 */
	api('sinatra_pre_footer_cta_font_size', function (value) {
		value.bind(function (newval) {
			var $cta = $('#si-pre-footer .si-pre-footer-cta');

			if (!$cta.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_pre_footer_cta_font_size');
			var style_css = sinatra_range_field_css('#si-pre-footer .h3', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * WooCommerce sale badge text.
	 */
	api('sinatra_product_sale_badge_text', function (value) {
		value.bind(function (newval) {
			var $badge = $('.woocommerce ul.products li.product .onsale, .woocommerce span.onsale').not('.sold-out');

			if (!$badge.length) {
				return;
			}

			$badge.html(newval);
		});
	});

	/**
	 * Accent color.
	 */
	api('sinatra_accent_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_accent_color');
			var style_css;

			// Background colors.
			style_css =
				'.si-header-widgets .si-cart .si-cart-count,' +
				'#si-scroll-top:hover::before, ' +
				'.sinatra-menu-animation-underline #sinatra-header-inner .sinatra-nav > ul > li > a > span::before, ' +
				'.si-btn, ' +
				'#infinite-handle span, ' +
				'input[type=submit], ' +
				'.comment-form input[type=checkbox]:checked, ' +
				'#comments .bypostauthor-badge, ' +
				'input[type=radio]:checked::before, ' +
				'.single .post-tags a:hover, ' +
				'.single .post-category .cat-links a:hover, ' +
				'#main .mejs-controls .mejs-time-rail .mejs-time-current, ' +
				'.si-hamburger:hover .hamburger-inner, ' +
				'.si-hamburger:hover .hamburger-inner::before, ' +
				'.si-hamburger:hover .hamburger-inner::after, ' +
				'.tagcloud a:hover, ' +
				'.si-btn.sinatra-read-more::after, ' +
				'.post_format-post-format-quote .si-blog-entry-content .quote-post-bg::after, ' +
				'.si-hover-slider .post-category a,' +
				'.si-single-title-in-page-header.single .page-header .post-category a,' +
				'.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::after,' +
				'.si-pre-footer-cta-style-2 #si-pre-footer::after,' +
				'.entry-media > a:hover .entry-media-icon::before, ' +
				'.si-woo-steps .si-step.is-active > span:first-child, ' +
				'.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::after, ' +
				'.si-pre-footer-cta-style-2 #si-pre-footer::after, ' +
				'.site-main .woocommerce #respond input#submit, ' +
				'.site-main .woocommerce a.button, ' +
				'.site-main .woocommerce button.button, ' +
				'.site-main .woocommerce input.button, ' +
				'.select2-container--default .select2-results__option--highlighted[data-selected], ' +
				'.si-input-supported input[type=radio]:checked:before, ' +
				'.si-input-supported input[type=checkbox]:checked, ' +
				'.woocommerce ul.products li.product .onsale, ' +
				'.woocommerce span.onsale, ' +
				'.woocommerce-store-notice, ' +
				'p.demo_store, ' +
				'.woocommerce ul.products li.product .button, ' +
				'.sinatra-sidebar-style-2 #secondary .widget-title:before, ' +
				'.widget.woocommerce .wc-layered-nav-term:hover .count, ' +
				'.widget.woocommerce .product-categories li a:hover ~ .count, ' +
				'.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen a:before, ' +
				'.woocommerce .widget_rating_filter ul li.chosen a::before, ' +
				'.widget.woocommerce .wc-layered-nav-term.chosen .count, ' +
				'.widget.woocommerce .product-categories li.current-cat > .count, ' +
				'.woocommerce .widget_price_filter .ui-slider .ui-slider-handle, ' +
				'.woocommerce .widget_price_filter .ui-slider .ui-slider-handle:after, ' +
				'.woocommerce .widget_layered_nav_filters ul li a:hover, ' +
				'.woocommerce div.product form.cart .button, ' +
				'.widget.woocommerce .wc-layered-nav-rating a:hover em, ' +
				'.widget.woocommerce .wc-layered-nav-rating.chosen a em, ' +
				'.widget .cat-item a:hover + span, ' +
				'.widget_archive li a:hover + span, ' +
				'.widget .cat-item.current-cat a + span, ' +
				'#sinatra-footer .widget .cat-item a:hover + span, ' +
				'#sinatra-footer .widget_archive li a:hover + span, ' +
				'#sinatra-footer .widget .cat-item.current-cat a + span, ' +
				'.si-btn.btn-outline:hover, ' +
				'.si-hamburger:hover .hamburger-inner, ' +
				'.si-hamburger:hover .hamburger-inner::before, ' +
				'.si-hamburger:hover .hamburger-inner::after, ' +
				'.is-mobile-menu-active .si-hamburger .hamburger-inner, ' +
				'.is-mobile-menu-active .si-hamburger .hamburger-inner::before, ' +
				'.is-mobile-menu-active .si-hamburger .hamburger-inner::after, ' +
				'.woocommerce div.product div.images .woocommerce-product-gallery__trigger:hover:before, ' +
				'.woocommerce #review_form #respond .form-submit input { ' +
				'background-color: ' +
				newval +
				';' +
				'}';

			// Hover accent background color.
			style_css +=
				'.si-btn:hover, ' +
				'input[type=submit]:hover, ' +
				'#infinite-handle span:hover, ' +
				'.site-main .woocommerce #respond input#submit, ' +
				'.site-main .woocommerce a.button:hover, ' +
				'.site-main .woocommerce button.button:hover, ' +
				'.site-main .woocommerce input.button:hover, ' +
				'.si-hover-slider .post-category a:hover, ' +
				'.si-single-title-in-page-header.single .page-header .post-category a:hover, ' +
				'.woocommerce ul.products li.product .button:hover, ' +
				'.woocommerce .widget_price_filter .ui-slider .ui-slider-range, ' +
				'.wc-layered-nav-rating a:hover .star-rating span:before, ' +
				'.woocommerce #review_form #respond .form-submit input:hover { ' +
				'background-color: ' +
				sinatra_luminance(newval, 0.15) +
				';' +
				'}';

			// Hover accent color.
			style_css += '.wc-layered-nav-rating a:hover .star-rating span:before { ' + 'color: ' + sinatra_luminance(newval, 0.15) + ';' + '}';

			style_css += 'code, ' + 'kbd, ' + 'var, ' + 'samp, ' + 'mark, ' + 'span.highlight, ' + 'tt { ' + 'background-color: ' + sinatra_hex2rgba(newval, 0.12) + ';' + '}';

			style_css += 'code.block { ' + 'background-color: ' + sinatra_hex2rgba(newval, 0.075) + ';' + '}';

			// Colors.
			style_css +=
				'.content-area a:not(.si-btn):not(.wp-block-button__link),' +
				'.si-sidebar-container a:hover:not(.si-btn), ' +
				'.si-header-widgets .si-header-widget:hover, ' +
				'.si-header-widgets .si-header-widget.sinatra-active .si-icon.si-search, ' +
				'#sinatra-header-inner .si-header-widgets .sinatra-active, ' +
				'.sinatra-logo .site-title a:hover,' +
				'#sinatra-header-inner .sinatra-nav > ul > li > a:hover, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.menu-item-has-children:hover > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-item > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current-menu-ancestor > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.page_item_has_children:hover > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current_page_item > a, ' +
				'#sinatra-header-inner .sinatra-nav > ul > li.current_page_ancestor > a, ' +
				'#sinatra-topbar .sinatra-nav > ul > li > a:hover, ' +
				'#sinatra-topbar .sinatra-nav > ul > li.menu-item-has-children:hover > a,  ' +
				'#sinatra-topbar .sinatra-nav > ul > li.current-menu-item > a, ' +
				'#sinatra-topbar .sinatra-nav > ul > li.current-menu-ancestor > a, ' +
				'.si-topbar-widget__text a:hover, ' +
				'.si-topbar-widget__text a, ' +
				'.si-header-widgets a:not(.si-btn):hover, ' +
				'.sinatra-social-nav > ul > li > a .si-icon.bottom-icon, ' +
				'.sinatra-pagination .navigation .nav-links .page-numbers:hover, ' +
				'.widget .cat-item.current-cat > a, ' +
				'.widget ul li.current_page_item > a, ' +
				'#main .search-form .search-submit:hover, ' +
				'#cancel-comment-reply-link:hover, ' +
				'.comment-form .required, ' +
				'.navigation .nav-links .page-numbers:hover, ' +
				'#main .entry-meta a:hover, ' +
				'#main .author-box-title a, ' +
				'.single .post-category a, ' +
				'.page-links span:hover, ' +
				'.site-content .page-links span:hover, ' +
				'.wc-cart-widget-header .si-cart-subtotal span, ' +
				'.si-header-widget__cart:hover > a, ' +
				'.woocommerce #yith-wcwl-form table.shop_table .product-subtotal .amount, ' +
				'.woocommerce .woocommerce-cart-form table.shop_table .product-subtotal .amount, ' +
				'.si-woo-steps .si-step.is-active, ' +
				'.cart_totals .order-total td, ' +
				'.navigation .nav-links .page-numbers.current, ' +
				'.page-links > span, ' +
				'.site-content .page-links > span, ' +
				'.woocommerce ul.products li.product .price, ' +
				'.woocommerce .woocommerce-checkout-review-order .order-total .woocommerce-Price-amount.amount, ' +
				'.woocommerce-info::before, ' +
				'#main .woocommerce-MyAccount-navigation li.is-active, ' +
				'.woocommerce .star-rating span::before, ' +
				'.widget.woocommerce .wc-layered-nav-term:hover a, ' +
				'.widget.woocommerce .wc-layered-nav-term a:hover,' +
				'.widget.woocommerce .product-categories li a:hover, ' +
				'.widget.woocommerce .product-categories li.current-cat > a, ' +
				'.woocommerce ins .amount, ' +
				'.woocommerce .widget_rating_filter ul li.chosen a::before, ' +
				'.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen a, ' +
				'.woocommerce .widget_shopping_cart .total .amount, ' +
				'.woocommerce .widget_shopping_cart .total .tax_label, ' +
				'.woocommerce.widget_shopping_cart .total .amount, ' +
				'.woocommerce.widget_shopping_cart .total .tax_label, ' +
				'.si-btn.btn-outline, ' +
				'.woocommerce .widget_shopping_cart .cart_list li a.remove:hover:before, ' +
				'.woocommerce div.product .woocommerce-tabs ul.tabs li.active > a,' +
				'.woocommerce.widget_shopping_cart .cart_list li a.remove:hover:before, ' +
				'.woocommerce div.product p.price, ' +
				'.woocommerce div.product span.price, ' +
				'.woocommerce div.product #reviews .comment-form-rating .stars a, ' +
				'.woocommerce div.product .woocommerce-pagination ul li span.current, ' +
				'.woocommerce div.product .woocommerce-pagination ul li a:hover, ' +
				'code, ' +
				'kbd, ' +
				'var, ' +
				'samp, ' +
				'tt, ' +
				'.is-mobile-menu-active .si-hamburger .hamburger-label, ' +
				'.si-hamburger:hover .hamburger-label, ' +
				'.single #main .post-nav a:hover, ' +
				'#sinatra-topbar .si-topbar-widget__text .si-icon, ' +
				'.sinatra-core-custom-list-widget .si-widget-icon {' +
				'color: ' +
				newval +
				';' +
				'}';

			// Selection.
			style_css += '#main ::-moz-selection { background-color: ' + newval + '; color: #FFF; }';
			style_css += '#main ::selection { background-color: ' + newval + '; color: #FFF; }';

			// Border color.
			style_css +=
				'#comments .comment-actions .reply a:hover, ' +
				'.comment-form input[type=checkbox]:checked, .comment-form input[type=checkbox]:focus, ' +
				'.comment-form input[type=radio]:checked, .comment-form input[type=radio]:focus, ' +
				'.single .post-category a, ' +
				'#secondary .widget-title, ' +
				'.si-hover-slider .post-category a, ' +
				'.si-single-title-in-page-header.single .page-header .post-category a, ' +
				'.entry-content blockquote, ' +
				'.wp-block-quote.is-style-large, ' +
				'.wp-block-quote.is-large, ' +
				'.wp-block-quote.has-text-align-right, ' +
				'[type="radio"]:checked + label:before, ' +
				'.si-input-supported input[type=radio]:checked, ' +
				'.si-input-supported input[type=checkbox]:checked, ' +
				'.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen a:before, ' +
				'.widget.woocommerce .widget_rating_filter.chosen a:after, ' +
				'.si-btn.btn-outline, ' +
				'.page-links > span, .site-content .page-links > span, ' +
				'.navigation .nav-links .page-numbers.current, ' +
				'.woocommerce div.product div.images .flex-control-thumbs li img.flex-active, ' +
				'.woocommerce div.product .woocommerce-pagination ul li span.current {' +
				'border-color: ' +
				newval +
				';' +
				'}';

			// Border bottom color.
			style_css +=
				'#masthead .si-header-widgets .dropdown-item::after, ' +
				'.sinatra-nav > ul .sub-menu::after,' +
				'textarea:focus, ' +
				'input[type="text"]:focus, ' +
				'input[type="email"]:focus, ' +
				'input[type=password]:focus, ' +
				'input[type=tel]:focus, ' +
				'input[type=url]:focus, ' +
				'input[type=search]:focus, ' +
				'input[type=date]:focus, ' +
				'#add_payment_method table.cart td.actions .coupon .input-text:focus, ' +
				'.woocommerce-cart table.cart td.actions .coupon .input-text:focus, ' +
				'.woocommerce-checkout table.cart td.actions .coupon .input-text:focus  {' +
				'border-bottom-color: ' +
				newval +
				';' +
				'}';

			// Border top color.
			style_css += '.si-header-widgets .dropdown-item, ' + '.site .woocommerce-info, ' + '.preloader-1 > div, ' + '.si-header-element.sinatra-nav .sub-menu {' + 'border-top-color: ' + newval + ';' + '}';

			// Fill color.
			style_css +=
				'.sinatra-animate-arrow:hover .arrow-handle, ' +
				'.sinatra-animate-arrow:hover .arrow-bar, ' +
				'.sinatra-animate-arrow:focus .arrow-handle, ' +
				'.sinatra-animate-arrow:focus .arrow-bar, ' +
				'.sinatra-pagination .navigation .nav-links .page-numbers.next:hover .sinatra-animate-arrow .arrow-handle,' +
				'.sinatra-pagination .navigation .nav-links .page-numbers.prev:hover .sinatra-animate-arrow .arrow-handle,' +
				'.sinatra-pagination .navigation .nav-links .page-numbers.next:hover .sinatra-animate-arrow .arrow-bar,' +
				'.sinatra-pagination .navigation .nav-links .page-numbers.prev:hover .sinatra-animate-arrow .arrow-bar {' +
				'fill: ' +
				newval +
				';' +
				'}';

			// Box shadow.
			style_css += '.si-input-supported input[type=checkbox]:focus:hover { ' + 'box-shadow: inset 0 0 0 2px ' + newval + '; ' + '}';

			// Gradient.
			style_css +=
				'.si-pre-footer-cta-style-1 #si-pre-footer .si-flex-row::before,' +
				'.si-pre-footer-cta-style-2 #si-pre-footer::before { ' +
				'background: linear-gradient(to right, ' +
				sinatra_hex2rgba(newval, 0.9) +
				' 0%, ' +
				sinatra_hex2rgba(newval, 0.82) +
				' 35%, ' +
				sinatra_hex2rgba(newval, 0.4) +
				' 100% );' +
				'-webkit-gradient(linear, left top, right top, from(' +
				sinatra_hex2rgba(newval, 0.9) +
				'), color-stop(35%, ' +
				sinatra_hex2rgba(newval, 0.82) +
				'), to(' +
				sinatra_hex2rgba(newval, 0.4) +
				')); }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Content background color.
	 */
	api('sinatra_boxed_content_background_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_boxed_content_background_color');
			var style_css = '';

			if (newval) {
				style_css =
					'.sinatra-layout__boxed #page, ' +
					'.sinatra-layout__boxed-separated #content, ' +
					'.sinatra-layout__boxed-separated.sinatra-sidebar-style-3 #secondary .si-widget, ' +
					'.sinatra-layout__boxed-separated.sinatra-sidebar-style-3 .elementor-widget-sidebar .si-widget, ' +
					'.sinatra-layout__boxed-separated.blog .sinatra-article, ' +
					'.sinatra-layout__boxed-separated.search-results .sinatra-article, ' +
					'.sinatra-layout__boxed-separated.category .sinatra-article { background-color: ' +
					newval +
					'; }';

				style_css += '@media screen and (max-width: 960px) { ' + '.sinatra-layout__boxed-separated #page { background-color: ' + newval + '; }' + '}';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Content text color.
	 */
	api('sinatra_content_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_content_text_color');
			var style_css = '';

			if (newval) {
				style_css =
					'body { ' +
					'color: ' +
					newval +
					';' +
					'}' +
					'.comment-form .comment-notes, ' +
					'#comments .no-comments, ' +
					'#page .wp-caption .wp-caption-text,' +
					'#comments .comment-meta,' +
					'.comments-closed,' +
					'.entry-meta,' +
					'.si-entry cite,' +
					'legend,' +
					'.si-page-header-description,' +
					'.page-links em,' +
					'.site-content .page-links em,' +
					'.single .entry-footer .last-updated,' +
					'.single .post-nav .post-nav-title,' +
					'#main .widget_recent_comments span,' +
					'#main .widget_recent_entries span,' +
					'#main .widget_calendar table > caption,' +
					'.post-thumb-caption, ' +
					'.wp-block-image figcaption, ' +
					'.si-cart-item .si-x,' +
					'.woocommerce form.login .lost_password a,' +
					'.woocommerce form.register .lost_password a,' +
					'.woocommerce a.remove,' +
					'#add_payment_method .cart-collaterals .cart_totals .woocommerce-shipping-destination, ' +
					'.woocommerce-cart .cart-collaterals .cart_totals .woocommerce-shipping-destination, ' +
					'.woocommerce-checkout .cart-collaterals .cart_totals .woocommerce-shipping-destination,' +
					'.woocommerce ul.products li.product .si-loop-product__category-wrap a,' +
					'.woocommerce ul.products li.product .si-loop-product__category-wrap,' +
					'.woocommerce .woocommerce-checkout-review-order table.shop_table thead th,' +
					'#add_payment_method #payment div.payment_box, ' +
					'.woocommerce-cart #payment div.payment_box, ' +
					'.woocommerce-checkout #payment div.payment_box,' +
					'#add_payment_method #payment ul.payment_methods .about_paypal, ' +
					'.woocommerce-cart #payment ul.payment_methods .about_paypal, ' +
					'.woocommerce-checkout #payment ul.payment_methods .about_paypal,' +
					'.woocommerce table dl,' +
					'.woocommerce table .wc-item-meta,' +
					'.widget.woocommerce .reviewer,' +
					'.woocommerce.widget_shopping_cart .cart_list li a.remove:before,' +
					'.woocommerce .widget_shopping_cart .cart_list li a.remove:before,' +
					'.woocommerce .widget_shopping_cart .cart_list li .quantity, ' +
					'.woocommerce.widget_shopping_cart .cart_list li .quantity,' +
					'.woocommerce div.product .woocommerce-product-rating .woocommerce-review-link,' +
					'.woocommerce div.product .woocommerce-tabs table.shop_attributes td,' +
					'.woocommerce div.product .product_meta > span span:not(.si-woo-meta-title), ' +
					'.woocommerce div.product .product_meta > span a,' +
					'.woocommerce .star-rating::before,' +
					'.woocommerce div.product #reviews #comments ol.commentlist li .comment-text p.meta,' +
					'.ywar_review_count,' +
					'.woocommerce .add_to_cart_inline del, ' +
					'.woocommerce div.product p.price del, ' +
					'.woocommerce div.product span.price del { color: ' +
					sinatra_hex2rgba(newval, 0.75) +
					'; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Content link hover color.
	 */
	api('sinatra_content_link_hover_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_content_link_hover_color');
			var style_css = '';

			if (newval) {
				// Content link hover.
				style_css +=
					'.content-area a:not(.si-btn):not(.wp-block-button__link):hover, ' +
					'.si-woo-before-shop select.custom-select-loaded:hover ~ #si-orderby, ' +
					'#add_payment_method #payment ul.payment_methods .about_paypal:hover, ' +
					'.woocommerce-cart #payment ul.payment_methods .about_paypal:hover, ' +
					'.woocommerce-checkout #payment ul.payment_methods .about_paypal:hover, ' +
					'.si-breadcrumbs a:hover, ' +
					'.woocommerce div.product .woocommerce-product-rating .woocommerce-review-link:hover, ' +
					'.woocommerce ul.products li.product .meta-wrap .woocommerce-loop-product__link:hover, ' +
					'.woocommerce ul.products li.product .si-loop-product__category-wrap a:hover { ' +
					'color: ' +
					newval +
					';' +
					'}';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Content text color.
	 */
	api('sinatra_headings_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_headings_color');
			var style_css = '';

			if (newval) {
				style_css = 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .sinatra-logo .site-title, .error-404 .page-header h1 { ' + 'color: ' + newval + ';' + '}';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Scroll Top visibility.
	 */
	api('sinatra_scroll_top_visibility', function (value) {
		value.bind(function (newval) {
			sinatra_print_visibility_classes($('#si-scroll-top'), newval);
		});
	});

	/**
	 * Page Preloader visibility.
	 */
	api('sinatra_preloader_visibility', function (value) {
		value.bind(function (newval) {
			sinatra_print_visibility_classes($('#si-preloader'), newval);
		});
	});

	/**
	 * Footer visibility.
	 */
	api('sinatra_footer_visibility', function (value) {
		value.bind(function (newval) {
			sinatra_print_visibility_classes($('#sinatra-footer'), newval);
		});
	});

	/**
	 * Footer background.
	 */
	api('sinatra_footer_background', function (value) {
		value.bind(function (newval) {
			var $footer = $('#colophon');

			if (!$footer.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_footer_background');
			var style_css = sinatra_design_options_css('#colophon', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Footer font color.
	 */
	api('sinatra_footer_text_color', function (value) {
		var $footer = $('#sinatra-footer'),
			copyright_separator_color,
			style_css;

		value.bind(function (newval) {
			if (!$footer.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_footer_text_color');

			style_css = '';

			newval['text-color'] = newval['text-color'] ? newval['text-color'] : 'inherit';
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : 'inherit';
			newval['widget-title-color'] = newval['widget-title-color'] ? newval['widget-title-color'] : 'inherit';

			// Text color.
			style_css += '#colophon { color: ' + newval['text-color'] + '; }';

			// Link color.
			style_css += '#colophon a { color: ' + newval['link-color'] + '; }';

			// Link hover color.
			style_css += '#colophon a:hover, #colophon li.current_page_item > a, #colophon .sinatra-social-nav > ul > li > a .si-icon.bottom-icon ' + '{ color: ' + newval['link-hover-color'] + '; }';

			// Widget title color.
			style_css += '#colophon .widget-title { color: ' + newval['widget-title-color'] + '; }';

			// Copyright separator color.
			copyright_separator_color = sinatra_light_or_dark(newval['text-color'], 'rgba(255,255,255,0.1)', 'rgba(0,0,0,0.1)');
			// copyright_separator_color = sinatra_luminance( newval['text-color'], 0.8 );

			style_css += '#sinatra-copyright.contained-separator > .si-container:before { background-color: ' + copyright_separator_color + '; }';
			style_css += '#sinatra-copyright.fw-separator { border-top-color: ' + copyright_separator_color + '; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Footer border.
	 */
	api('sinatra_footer_border', function (value) {
		value.bind(function (newval) {
			var $footer = $('#sinatra-footer');

			if (!$footer.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_footer_border');
			var style_css = '';

			if (newval['border-top-width']) {
				style_css += '#colophon { ' + 'border-top-width: ' + newval['border-top-width'] + 'px;' + 'border-top-style: ' + newval['border-style'] + ';' + 'border-top-color: ' + newval['border-color'] + ';' + '}';
			}

			if (newval['border-bottom-width']) {
				style_css += '#colophon { ' + 'border-bottom-width: ' + newval['border-bottom-width'] + 'px;' + 'border-bottom-style: ' + newval['border-style'] + ';' + 'border-bottom-color: ' + newval['border-color'] + ';' + '}';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Copyright layout.
	 */
	api('sinatra_copyright_layout', function (value) {
		value.bind(function (newval) {
			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)sinatra-copyright-layout-\S+/g) || []).join(' ');
			});

			$body.addClass('sinatra-copyright-' + newval);
		});
	});

	/**
	 * Copyright separator.
	 */
	api('sinatra_copyright_separator', function (value) {
		value.bind(function (newval) {
			var $copyright = $('#sinatra-copyright');

			if (!$copyright.length) {
				return;
			}

			$copyright.removeClass('fw-separator contained-separator');

			if ('none' !== newval) {
				$copyright.addClass(newval);
			}
		});
	});

	/**
	 * Copyright visibility.
	 */
	api('sinatra_copyright_visibility', function (value) {
		value.bind(function (newval) {
			sinatra_print_visibility_classes($('#sinatra-copyright'), newval);
		});
	});

	/**
	 * Copyright background.
	 */
	api('sinatra_copyright_background', function (value) {
		value.bind(function (newval) {
			var $copyright = $('#sinatra-copyright');

			if (!$copyright.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_copyright_background');
			var style_css = sinatra_design_options_css('#sinatra-copyright', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Copyright text color.
	 */
	api('sinatra_copyright_text_color', function (value) {
		value.bind(function (newval) {
			var $copyright = $('#sinatra-copyright');

			if (!$copyright.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_copyright_text_color');
			var style_css = '';

			newval['text-color'] = newval['text-color'] ? newval['text-color'] : 'inherit';
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : 'inherit';

			// Text color.
			style_css += '#sinatra-copyright { color: ' + newval['text-color'] + '; }';

			// Link color.
			style_css += '#sinatra-copyright a { color: ' + newval['link-color'] + '; }';

			// Link hover color.
			style_css +=
				'#sinatra-copyright a:hover, #sinatra-copyright .sinatra-social-nav > ul > li > a .si-icon.bottom-icon, #sinatra-copyright li.current_page_item > a, #sinatra-copyright .sinatra-nav > ul > li.current-menu-item > a, #sinatra-copyright .sinatra-nav > ul > li.current-menu-ancestor > a #sinatra-copyright .sinatra-nav > ul > li:hover > a, #sinatra-copyright .sinatra-social-nav > ul > li > a .si-icon.bottom-icon { color: ' +
				newval['link-hover-color'] +
				'; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Container width.
	 */
	api('sinatra_container_width', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_container_width');
			var style_css;

			style_css = '.si-container,' + '.alignfull > div { ' + 'max-width: ' + newval.value + 'px;' + '}';

			style_css +=
				'.sinatra-layout__boxed #page, .sinatra-layout__boxed.si-sticky-header.sinatra-is-mobile #sinatra-header-inner, ' +
				'.sinatra-layout__boxed.si-sticky-header:not(.sinatra-header-layout-3) #sinatra-header-inner, ' +
				'.sinatra-layout__boxed.si-sticky-header:not(.sinatra-is-mobile).sinatra-header-layout-3 #sinatra-header-inner .si-nav-container > .si-container { max-width: ' +
				(parseInt(newval.value) + 100) +
				'px; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Transparent Header Logo max height.
	 */
	api('sinatra_tsp_logo_max_height', function (value) {
		value.bind(function (newval) {
			var $logo = $('.si-tsp-header .sinatra-logo');

			if (!$logo.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_tsp_logo_max_height');
			var style_css = '';

			style_css += sinatra_range_field_css('.si-tsp-header .sinatra-logo img', 'max-height', newval, true, 'px');
			style_css += sinatra_range_field_css('.si-tsp-header .sinatra-logo img.si-svg-logo', 'height', newval, true, 'px');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Transparent Header Logo margin.
	 */
	api('sinatra_tsp_logo_margin', function (value) {
		value.bind(function (newval) {
			var $logo = $('.si-tsp-header .sinatra-logo');

			if (!$logo.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_tsp_logo_margin');

			var style_css = sinatra_spacing_field_css('.si-tsp-header .sinatra-logo .logo-inner', 'margin', newval, true);
			$style_tag.html(style_css);
		});
	});

	/**
	 * Transparent header - Main Header & Topbar background.
	 */
	api('sinatra_tsp_header_background', function (value) {
		value.bind(function (newval) {
			var $tsp_header = $('.si-tsp-header');

			if (!$tsp_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_tsp_header_background');

			var style_css = '';
			style_css += sinatra_design_options_css('.si-tsp-header #sinatra-header-inner', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Transparent header - Main Header & Topbar font color.
	 */
	api('sinatra_tsp_header_font_color', function (value) {
		value.bind(function (newval) {
			var $tsp_header = $('.si-tsp-header');

			if (!$tsp_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_tsp_header_font_color');

			var style_css = '';

			newval['text-color'] = newval['text-color'] ? newval['text-color'] : 'inherit';
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : 'inherit';

			/** Header **/

			// Text color.
			style_css += '.si-tsp-header .sinatra-logo .site-description { color: ' + newval['text-color'] + '; }';

			// Link color.
			if (newval['link-color']) {
				style_css += '.si-tsp-header #sinatra-header, ' + '.si-tsp-header .si-header-widgets a:not(.si-btn), ' + '.si-tsp-header .sinatra-logo a,' + '.si-tsp-header .si-hamburger, ' + '.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li > a { color: ' + newval['link-color'] + '; }';
				style_css += '.si-tsp-header .hamburger-inner,' + '.si-tsp-header .hamburger-inner::before,' + '.si-tsp-header .hamburger-inner::after { background-color: ' + newval['link-color'] + '; }';
			}

			// Link hover color.
			if (newval['link-hover-color']) {
				style_css +=
					'.si-tsp-header .si-header-widgets a:not(.si-btn):hover, ' +
					'.si-tsp-header #sinatra-header-inner .si-header-widgets .sinatra-active,' +
					'.si-tsp-header .sinatra-logo .site-title a:hover, ' +
					'.si-tsp-header .si-hamburger:hover .hamburger-label, ' +
					'.is-mobile-menu-active .si-tsp-header .si-hamburger .hamburger-label,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li > a:hover,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.menu-item-has-children:hover > a,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.current-menu-item > a,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.current-menu-ancestor > a,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.page_item_has_children:hover > a,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.current_page_item > a,' +
					'.si-tsp-header #sinatra-header-inner .sinatra-nav > ul > li.current_page_ancestor > a { color: ' +
					newval['link-hover-color'] +
					'; }';

				style_css +=
					'.si-tsp-header .si-hamburger:hover .hamburger-inner,' +
					'.si-tsp-header .si-hamburger:hover .hamburger-inner::before,' +
					'.si-tsp-header .si-hamburger:hover .hamburger-inner::after,' +
					'.is-mobile-menu-active .si-tsp-header .si-hamburger .hamburger-inner,' +
					'.is-mobile-menu-active .si-tsp-header .si-hamburger .hamburger-inner::before,' +
					'.is-mobile-menu-active .si-tsp-header .si-hamburger .hamburger-inner::after { background-color: ' +
					newval['link-hover-color'] +
					'; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Transparent header - Main Header & Topbar border.
	 */
	api('sinatra_tsp_header_border', function (value) {
		value.bind(function (newval) {
			var $tsp_header = $('.si-tsp-header');

			if (!$tsp_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_tsp_header_border');

			var style_css = '';

			style_css += sinatra_design_options_css('.si-tsp-header #sinatra-header-inner', newval, 'border');

			// Separator color.
			newval['separator-color'] = newval['separator-color'] ? newval['separator-color'] : 'inherit';
			style_css += '.si-tsp-header .si-header-widget:after { background-color: ' + newval['separator-color'] + '; }';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Page Header layout.
	 */
	api('sinatra_page_header_alignment', function (value) {
		value.bind(function (newval) {
			if ($body.hasClass('single-post')) {
				return;
			}

			$body.removeClass(function (index, className) {
				return (className.match(/(^|\s)si-page-title-align-\S+/g) || []).join(' ');
			});

			$body.addClass('si-page-title-align-' + newval);
		});
	});

	/**
	 * Page Header spacing.
	 */
	api('sinatra_page_header_spacing', function (value) {
		value.bind(function (newval) {
			var $page_header = $('.page-header');

			if (!$page_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_page_header_spacing');

			var style_css = sinatra_spacing_field_css('.si-page-title-align-left .page-header.si-has-page-title, .si-page-title-align-right .page-header.si-has-page-title, .si-page-title-align-center .page-header .si-page-header-wrapper', 'padding', newval, true);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Page Header background.
	 */
	api('sinatra_page_header_background', function (value) {
		value.bind(function (newval) {
			var $page_header = $('.page-header');

			if (!$page_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_page_header_background');

			var style_css = '';
			style_css += sinatra_design_options_css('.page-header', newval, 'background');
			style_css += sinatra_design_options_css('.si-tsp-header:not(.si-tsp-absolute) #masthead', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Header Text color.
	 */
	api('sinatra_page_header_text_color', function (value) {
		value.bind(function (newval) {
			var $page_header = $('.page-header');

			if (!$page_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_page_header_text_color');
			var style_css = '';

			newval['text-color'] = newval['text-color'] ? newval['text-color'] : 'inherit';
			newval['link-color'] = newval['link-color'] ? newval['link-color'] : 'inherit';
			newval['link-hover-color'] = newval['link-hover-color'] ? newval['link-hover-color'] : 'inherit';

			// Text color.
			style_css += '.page-header .page-title { color: ' + newval['text-color'] + '; }';
			style_css += '.page-header .si-page-header-description' + '{ color: ' + sinatra_hex2rgba(newval['text-color'], 0.75) + '}';

			// Link color.
			style_css += '.page-header .si-breadcrumbs a' + '{ color: ' + newval['link-color'] + '; }';

			style_css += '.page-header .si-breadcrumbs span,' + '.page-header .breadcrumb-trail .trail-items li::after, .page-header .si-breadcrumbs .separator' + '{ color: ' + sinatra_hex2rgba(newval['link-color'], 0.75) + '}';

			$style_tag.html(style_css);
		});
	});

	/**
	 * Page Header border.
	 */
	api('sinatra_page_header_border', function (value) {
		value.bind(function (newval) {
			var $page_header = $('.page-header');

			if (!$page_header.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_page_header_border');
			var style_css = sinatra_design_options_css('.page-header', newval, 'border');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Breadcrumbs alignment.
	 */
	api('sinatra_breadcrumbs_alignment', function (value) {
		value.bind(function (newval) {
			var $breadcrumbs = $('#main > .si-breadcrumbs > .si-container');

			if (!$breadcrumbs.length) {
				return;
			}

			$breadcrumbs.removeClass(function (index, className) {
				return (className.match(/(^|\s)si-text-align\S+/g) || []).join(' ');
			});

			$breadcrumbs.addClass('si-text-align-' + newval);
		});
	});

	/**
	 * Breadcrumbs spacing.
	 */
	api('sinatra_breadcrumbs_spacing', function (value) {
		value.bind(function (newval) {
			var $breadcrumbs = $('.si-breadcrumbs');

			if (!$breadcrumbs.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_breadcrumbs_spacing');

			var style_css = sinatra_spacing_field_css('.si-breadcrumbs', 'padding', newval, true);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Breadcrumbs Background.
	 */
	api('sinatra_breadcrumbs_background', function (value) {
		value.bind(function (newval) {
			var $breadcrumbs = $('.si-breadcrumbs');

			if (!$breadcrumbs.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_breadcrumbs_background');
			var style_css = sinatra_design_options_css('.si-breadcrumbs', newval, 'background');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Breadcrumbs Text Color.
	 */
	api('sinatra_breadcrumbs_text_color', function (value) {
		value.bind(function (newval) {
			var $breadcrumbs = $('.si-breadcrumbs');

			if (!$breadcrumbs.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_breadcrumbs_text_color');
			var style_css = sinatra_design_options_css('.si-breadcrumbs', newval, 'color');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Breadcrumbs Border.
	 */
	api('sinatra_breadcrumbs_border', function (value) {
		value.bind(function (newval) {
			var $breadcrumbs = $('.si-breadcrumbs');

			if (!$breadcrumbs.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_breadcrumbs_border');
			var style_css = sinatra_design_options_css('.si-breadcrumbs', newval, 'border');

			$style_tag.html(style_css);
		});
	});

	/**
	 * Base HTML font size.
	 */
	api('sinatra_html_base_font_size', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_html_base_font_size');
			var style_css = sinatra_range_field_css('html', 'font-size', newval, true, 'px');
			$style_tag.html(style_css);
		});
	});

	/**
	 * Font smoothing.
	 */
	api('sinatra_font_smoothing', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_font_smoothing');

			if (newval) {
				$style_tag.html('*,' + '*::before,' + '*::after {' + '-moz-osx-font-smoothing: grayscale;' + '-webkit-font-smoothing: antialiased;' + '}');
			} else {
				$style_tag.html('*,' + '*::before,' + '*::after {' + '-moz-osx-font-smoothing: auto;' + '-webkit-font-smoothing: auto;' + '}');
			}

			$style_tag = sinatra_get_style_tag('sinatra_html_base_font_size');
			var style_css = sinatra_range_field_css('html', 'font-size', newval, true, 'px');
			$style_tag.html(style_css);
		});
	});

	/**
	 * Body font.
	 */
	api('sinatra_body_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_body_font');
			var style_css = sinatra_typography_field_css('body', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Headings font.
	 */
	api('sinatra_headings_font', function (value) {
		var style_css, selector;
		value.bind(function (newval) {
			selector = 'h1, .h1, .sinatra-logo .site-title, .page-header h1.page-title';
			selector += ', h2, .h2, .woocommerce div.product h1.product_title';
			selector += ', h3, .h3, .woocommerce #reviews #comments h2';
			selector += ', h4, .h4, .woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title';
			selector += ', h5, h6';

			style_css = sinatra_typography_field_css(selector, newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag = sinatra_get_style_tag('sinatra_headings_font');
			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 1 font.
	 */
	api('sinatra_h1_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h1_font');

			var style_css = sinatra_typography_field_css('h1, .h1, .sinatra-logo .site-title, .page-header h1.page-title', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 2 font.
	 */
	api('sinatra_h2_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h2_font');

			var style_css = sinatra_typography_field_css('h2, .h2, .woocommerce div.product h1.product_title', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 3 font.
	 */
	api('sinatra_h3_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h3_font');

			var style_css = sinatra_typography_field_css('h3, .h3, .woocommerce #reviews #comments h2', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 4 font.
	 */
	api('sinatra_h4_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h4_font');

			var style_css = sinatra_typography_field_css('h4, .h4, .woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 5 font.
	 */
	api('sinatra_h5_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h5_font');
			var style_css = sinatra_typography_field_css('h5', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading 6 font.
	 */
	api('sinatra_h6_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_h6_font');
			var style_css = sinatra_typography_field_css('h6', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Heading emphasized font.
	 */
	api('sinatra_heading_em_font', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_heading_em_font');
			var style_css = sinatra_typography_field_css('h1 em, h2 em, h3 em, h4 em, h5 em, h6 em, .h1 em, .h2 em, .h3 em, .h4 em, .sinatra-logo .site-title em, .error-404 .page-header h1 em', newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Sidebar widget title font size.
	 */
	api('sinatra_sidebar_widget_title_font_size', function (value) {
		value.bind(function (newval) {
			var $widget_title = $('#main .widget-title');

			if (!$widget_title.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_sidebar_widget_title_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('#main .widget-title', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Footer widget title font size.
	 */
	api('sinatra_footer_widget_title_font_size', function (value) {
		value.bind(function (newval) {
			var $widget_title = $('#colophon .widget-title');

			if (!$widget_title.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_footer_widget_title_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('#colophon .widget-title', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	/**
	 * Page title font size.
	 */
	api('sinatra_page_header_font_size', function (value) {
		value.bind(function (newval) {
			var $page_title = $('.page-header .page-title');

			if (!$page_title.length) {
				return;
			}

			$style_tag = sinatra_get_style_tag('sinatra_page_header_font_size');
			var style_css = '';

			style_css += sinatra_range_field_css('#page .page-header .page-title', 'font-size', newval, true, newval.unit);

			$style_tag.html(style_css);
		});
	});

	var $btn_selectors =
		'.si-btn, ' +
		'body:not(.wp-customizer) input[type=submit], ' +
		'.site-main .woocommerce #respond input#submit, ' +
		'.site-main .woocommerce a.button, ' +
		'.site-main .woocommerce button.button, ' +
		'.site-main .woocommerce input.button, ' +
		'.woocommerce ul.products li.product .added_to_cart, ' +
		'.woocommerce ul.products li.product .button, ' +
		'.woocommerce div.product form.cart .button, ' +
		'.woocommerce #review_form #respond .form-submit input, ' +
		'#infinite-handle span';

	var $btn_hover_selectors =
		'.si-btn:hover, ' +
		'.si-btn:focus, ' +
		'body:not(.wp-customizer) input[type=submit]:hover, ' +
		'body:not(.wp-customizer) input[type=submit]:focus, ' +
		'.site-main .woocommerce #respond input#submit:hover, ' +
		'.site-main .woocommerce #respond input#submit:focus, ' +
		'.site-main .woocommerce a.button:hover, ' +
		'.site-main .woocommerce a.button:focus, ' +
		'.site-main .woocommerce button.button:hover, ' +
		'.site-main .woocommerce button.button:focus, ' +
		'.site-main .woocommerce input.button:hover, ' +
		'.site-main .woocommerce input.button:focus, ' +
		'.woocommerce ul.products li.product .added_to_cart:hover, ' +
		'.woocommerce ul.products li.product .added_to_cart:focus, ' +
		'.woocommerce ul.products li.product .button:hover, ' +
		'.woocommerce ul.products li.product .button:focus, ' +
		'.woocommerce div.product form.cart .button:hover, ' +
		'.woocommerce div.product form.cart .button:focus, ' +
		'.woocommerce #review_form #respond .form-submit input:hover, ' +
		'.woocommerce #review_form #respond .form-submit input:focus, ' +
		'#infinite-handle span:hover';

	/**
	 * Primary button background color.
	 */
	api('sinatra_primary_button_bg_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_bg_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_selectors + '{ background-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button hover background color.
	 */
	api('sinatra_primary_button_hover_bg_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_hover_bg_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_hover_selectors + ' { background-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button text color.
	 */
	api('sinatra_primary_button_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_selectors + ' { color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button hover text color.
	 */
	api('sinatra_primary_button_hover_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_hover_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_hover_selectors + ' { color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button border width.
	 */
	api('sinatra_primary_button_border_width', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_border_width');
			var style_css = '';

			if (newval) {
				style_css = $btn_selectors + ' { border-width: ' + newval.value + 'px; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button border radius.
	 */
	api('sinatra_primary_button_border_radius', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_border_radius');
			var style_css = '';

			if (newval) {
				style_css = $btn_selectors + ' { ' + 'border-top-left-radius: ' + newval['top-left'] + 'px;' + 'border-top-right-radius: ' + newval['top-right'] + 'px;' + 'border-bottom-left-radius: ' + newval['bottom-left'] + 'px;' + 'border-bottom-right-radius: ' + newval['bottom-right'] + 'px; }';

				console.log(style_css);
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button border color.
	 */
	api('sinatra_primary_button_border_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_border_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_selectors + ' { border-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button hover border color.
	 */
	api('sinatra_primary_button_hover_border_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_hover_border_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_hover_selectors + ' { border-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Primary button typography.
	 */
	api('sinatra_primary_button_typography', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_primary_button_typography');
			var style_css = sinatra_typography_field_css($btn_selectors, newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	// Secondary button.
	var $btn_sec_selectors = '.btn-secondary, .si-btn.btn-secondary';

	var $btn_sec_hover_selectors = '.btn-secondary:hover, ' + '.btn-secondary:focus, ' + '.si-btn.btn-secondary:hover, ' + '.si-btn.btn-secondary:focus';

	/**
	 * Secondary button background color.
	 */
	api('sinatra_secondary_button_bg_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_bg_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_selectors + '{ background-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button hover background color.
	 */
	api('sinatra_secondary_button_hover_bg_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_hover_bg_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_hover_selectors + '{ background-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button text color.
	 */
	api('sinatra_secondary_button_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_selectors + '{ color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button hover text color.
	 */
	api('sinatra_secondary_button_hover_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_hover_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_hover_selectors + '{ color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button border width.
	 */
	api('sinatra_secondary_button_border_width', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_border_width');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_selectors + ' { border-width: ' + newval.value + 'px; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button border radius.
	 */
	api('sinatra_secondary_button_border_radius', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_border_radius');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_selectors + ' { ' + 'border-top-left-radius: ' + newval['top-left'] + 'px;' + 'border-top-right-radius: ' + newval['top-right'] + 'px;' + 'border-bottom-left-radius: ' + newval['bottom-left'] + 'px;' + 'border-bottom-right-radius: ' + newval['bottom-right'] + 'px; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button border color.
	 */
	api('sinatra_secondary_button_border_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_border_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_selectors + ' { border-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button hover border color.
	 */
	api('sinatra_secondary_button_hover_border_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_hover_border_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_sec_hover_selectors + ' { border-color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Secondary button typography.
	 */
	api('sinatra_secondary_button_typography', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_secondary_button_typography');
			var style_css = sinatra_typography_field_css($btn_sec_selectors, newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	// Text button.
	var $btn_text_selectors = '.si-btn.btn-text-1, .btn-text-1';

	var $btn_text_hover_selectors = '.si-btn.btn-text-1:hover, .si-btn.btn-text-1:focus, .btn-text-1:hover, .btn-text-1:focus';

	/**
	 * Text button text color.
	 */
	api('sinatra_text_button_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_text_button_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_text_selectors + '{ color: ' + newval + '; }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Text button hover text color.
	 */
	api('sinatra_text_button_hover_text_color', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_text_button_hover_text_color');
			var style_css = '';

			if (newval) {
				style_css = $btn_text_hover_selectors + '{ color: ' + newval + '; }';
				style_css += '.si-btn.btn-text-1 > span::before { background-color: ' + newval + ' }';
			}

			$style_tag.html(style_css);
		});
	});

	/**
	 * Text button typography.
	 */
	api('sinatra_text_button_typography', function (value) {
		value.bind(function (newval) {
			$style_tag = sinatra_get_style_tag('sinatra_text_button_typography');
			var style_css = sinatra_typography_field_css($btn_text_selectors, newval);

			sinatra_enqueue_google_font(newval['font-family']);

			$style_tag.html(style_css);
		});
	});

	// Selective refresh.
	if (api.selectiveRefresh) {
		// Bind partial content rendered event.
		api.selectiveRefresh.bind('partial-content-rendered', function (placement) {
			// Hero Hover Slider.
			if ('sinatra_hero_hover_slider_post_number' === placement.partial.id || 'sinatra_hero_hover_slider_elements' === placement.partial.id) {
				document.querySelectorAll(placement.partial.params.selector).forEach((item) => {
					sinatraHoverSlider(item);
				});

				// Force refresh height.
				api('sinatra_hero_hover_slider_height', function (newval) {
					newval.callbacks.fireWith(newval, [newval.get()]);
				});
			}

			// Preloader style.
			if ('sinatra_preloader_style' === placement.partial.id) {
				$body.removeClass('si-loaded');

				setTimeout(function () {
					window.sinatra.preloader();
				}, 300);
			}
		});
	}

	// Custom Customizer Preview class (attached to the Customize API)
	api.sinatraCustomizerPreview = {
		// Init
		init: function () {
			var self = this; // Store a reference to "this"
			var previewBody = self.preview.body;

			previewBody.on('click', '.sinatra-set-widget', function () {
				self.preview.send('set-footer-widget', $(this).data('sidebar-id'));
			});
		},
	};

	/**
	 * Capture the instance of the Preview since it is private (this has changed in WordPress 4.0)
	 *
	 * @see https://github.com/WordPress/WordPress/blob/5cab03ab29e6172a8473eb601203c9d3d8802f17/wp-admin/js/customize-controls.js#L1013
	 */
	var sinatraOldPreview = api.Preview;
	api.Preview = sinatraOldPreview.extend({
		initialize: function (params, options) {
			// Store a reference to the Preview
			api.sinatraCustomizerPreview.preview = this;

			// Call the old Preview's initialize function
			sinatraOldPreview.prototype.initialize.call(this, params, options);
		},
	});

	// Document ready
	$(function () {
		// Initialize our Preview
		api.sinatraCustomizerPreview.init();
	});
})(jQuery);
