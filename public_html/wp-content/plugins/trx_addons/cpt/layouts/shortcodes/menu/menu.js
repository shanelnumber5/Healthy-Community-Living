/* global jQuery:false */

(function() {
	"use strict";

	jQuery(document).on('action.before_ready_trx_addons', function() {
		// Init Superfish menu - global declaration to use in other scripts
		window.trx_addons_init_sfmenu = function(selector) {
			jQuery(selector).show().each(function() {
				var animation_in = jQuery(this).parent().data('animation-in');
				if (animation_in == undefined) animation_in = "none";
				var animation_out = jQuery(this).parent().data('animation-out');
				if (animation_out == undefined) animation_out = "none";
				jQuery(this).addClass('inited').superfish({
					delay: 500,
					animation: {
						opacity: 'show'
					},
					animationOut: {
						opacity: 'hide'
					},
					speed: 		animation_in!='none' ? 500 : 200,
					speedOut:	animation_out!='none' ? 500 : 200,
					autoArrows: false,
					dropShadows: false,
					onBeforeShow: function(ul) {
						if (jQuery(this).parents("ul").length > 1){
							var w = jQuery(window).width();  
							var par_offset = jQuery(this).parents("ul").offset().left;
							var par_width  = jQuery(this).parents("ul").outerWidth();
							var ul_width   = jQuery(this).outerWidth();
							if (par_offset+par_width+ul_width > w-20 && par_offset-ul_width > 0)
								jQuery(this).addClass('submenu_left');
							else
								jQuery(this).removeClass('submenu_left');
						}
						if (jQuery(this).parents('[class*="columns-"]').length == 0 && animation_in!='none') {
							jQuery(this).removeClass('animated fast '+animation_out);
							jQuery(this).addClass('animated fast '+animation_in);
						}
					},
					onBeforeHide: function(ul) {
						if (jQuery(this).parents('[class*="columns-"]').length == 0 && animation_out!='none') {
							jQuery(this).removeClass('animated fast '+animation_in);
							jQuery(this).addClass('animated fast '+animation_out);
						}
					}
				});
			});
		};
	
		// Init superfish menus
		trx_addons_init_sfmenu('.sc_layouts_menu:not(.inited) > ul:not(.inited)');
	
		// Check if menu need collapse (before menu showed)
		trx_addons_menu_collapse();

		// Show menu		
		jQuery('.sc_layouts_menu:not(.inited)').each(function() {
			if (jQuery(this).find('>ul.inited').length == 1) jQuery(this).addClass('inited');
		});
	
		// Slide effect for menu
		jQuery('.menu_hover_slide_line:not(.slide_inited),.menu_hover_slide_box:not(.slide_inited)').each(function() {
			var menu = jQuery(this).addClass('slide_inited');
			var style = menu.hasClass('menu_hover_slide_line') ? 'line' : 'box';
			setTimeout(function() {
				menu.find('>ul').spasticNav({
					style: style,
					//color: '',
					colorOverride: false
				});
			}, 500);
		});
	
		// Burger with popup
		jQuery('.sc_layouts_menu_mobile_button_burger:not(.inited)').each(function() {
			var burger = jQuery(this);
			var popup = burger.find('.sc_layouts_menu_popup');
			if (popup.length == 1) {
				burger.addClass('inited').on('click', '>a', function(e) {
					popup.toggleClass('opened').slideToggle();
					e.preventDefault();
					return false;
				});
				jQuery(document).on('click', function(e) {
					jQuery('.sc_layouts_menu_popup.opened').removeClass('opened').slideUp();
				});
			}
		});
	
	});
	

	// Collapse menu on resize
	jQuery(document).on('action.resize_trx_addons', function() {
		trx_addons_menu_collapse();
	});
	
	// Collapse menu items
	function trx_addons_menu_collapse() {
		jQuery('.sc_layouts_menu').each(function() {
			var ul = jQuery(this).find('>ul.inited');
			if (ul.length == 0) return;
			var sc_layouts_item = ul.parents('.sc_layouts_item');
			if (!sc_layouts_item.parent().hasClass('wpb_wrapper') && !sc_layouts_item.parent().hasClass('sc_layouts_column')) return;
			var w_max = sc_layouts_item.parent().width();
			sc_layouts_item.siblings().each(function() {
				w_max -= jQuery(this).outerWidth();
			});
			w_max -= (parseFloat(sc_layouts_item.css('marginLeft')) + parseFloat(sc_layouts_item.css('marginRight')));
			w_max -= 2;	// Leave 2px empty
			var w_all = 0;
			var move = false;
			var li_collapse = ul.find('li.menu-item.menu-collapse');
			if (li_collapse.length==0) {
				ul.append('<li class="menu-item menu-collapse"><a href="#" class="sf-with-ul trx_addons_icon-dot3"></a><ul class="submenu"></ul></li>');
				li_collapse = ul.find('li.menu-item.menu-collapse');
			}
			var li_collapse_ul = li_collapse.find('> ul');
			// Check if need to move items
			ul.find('> li').each(function(idx) {
				var cur_item = jQuery(this);
				cur_item.data('index', idx);
				if (move) return;
				w_all += !cur_item.hasClass('menu-collapse') || cur_item.css('display')!='none' ? cur_item.outerWidth() : 0;
				if (w_all > w_max) move = true;
			});
			// If need to move items to the collapsed item
			if (move) {
				w_all = li_collapse.outerWidth();
				ul.find("> li:not('.menu-collapse')").each(function(idx) {
					var cur_item = jQuery(this);
					var cur_width = cur_item.outerWidth();
					if (w_all <= w_max) w_all += cur_width;
					if (w_all > w_max) {
						var moved = false;
						li_collapse_ul.find('>li').each(function() {
							if (!moved && Number(jQuery(this).data('index')) > idx) {
								cur_item.attr('data-width', cur_width).insertBefore(jQuery(this));
								moved = true;
							}
						});
						if (!moved) cur_item.attr('data-width', cur_width).appendTo(li_collapse_ul);
					}
				});
				li_collapse.show();
				
			// Else - move items to the menu again
			} else {
				var items = li_collapse_ul.find('>li');
				var cnt = 0;
				move = true;
				//w_all += 20; 	// Leave 20px empty
				items.each(function() {
					if (!move) return;
					if (items.length - cnt == 1)
						w_all -= li_collapse.outerWidth();
					w_all += parseFloat(jQuery(this).data('width'));
					if (w_all < w_max) {
						jQuery(this).insertBefore(li_collapse);
						cnt++;
					} else
						move = false;
				});
				if (items.length - cnt == 0) li_collapse.hide();
			}
		});
	}

})();