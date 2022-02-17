<?php
/**
 * Generate custom CSS
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Return CSS with custom colors and fonts
if (!function_exists('academee_customizer_get_css')) {

	function academee_customizer_get_css($colors=null, $fonts=null, $remove_spaces=true, $only_scheme='') {

		$css = array(
			'fonts' => '',
			'colors' => ''
		);
		
		// Theme fonts
		//---------------------------------------------
		if ($fonts === null) {
			$fonts = academee_get_theme_fonts();
		}
		
		if ($fonts) {

			// Make theme-specific fonts rules
			$fonts = academee_customizer_add_theme_fonts($fonts);

			$rez = array();
			$rez['fonts'] = <<<CSS

body {
	{$fonts['p_font-family']}
	{$fonts['p_font-size']}
	{$fonts['p_font-weight']}
	{$fonts['p_font-style']}
	{$fonts['p_line-height']}
	{$fonts['p_text-decoration']}
	{$fonts['p_text-transform']}
	{$fonts['p_letter-spacing']}
}
p, ul, ol, dl, blockquote, address {
	{$fonts['p_margin-top']}
	{$fonts['p_margin-bottom']}
}

h1 {
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_line-height']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_text-transform']}
	{$fonts['h1_letter-spacing']}
	{$fonts['h1_margin-top']}
	{$fonts['h1_margin-bottom']}
}
h2 {
	{$fonts['h2_font-family']}
	{$fonts['h2_font-size']}
	{$fonts['h2_font-weight']}
	{$fonts['h2_font-style']}
	{$fonts['h2_line-height']}
	{$fonts['h2_text-decoration']}
	{$fonts['h2_text-transform']}
	{$fonts['h2_letter-spacing']}
	{$fonts['h2_margin-top']}
	{$fonts['h2_margin-bottom']}
}
h3 {
	{$fonts['h3_font-family']}
	{$fonts['h3_font-size']}
	{$fonts['h3_font-weight']}
	{$fonts['h3_font-style']}
	{$fonts['h3_line-height']}
	{$fonts['h3_text-decoration']}
	{$fonts['h3_text-transform']}
	{$fonts['h3_letter-spacing']}
	{$fonts['h3_margin-top']}
	{$fonts['h3_margin-bottom']}
}
h4 {
	{$fonts['h4_font-family']}
	{$fonts['h4_font-size']}
	{$fonts['h4_font-weight']}
	{$fonts['h4_font-style']}
	{$fonts['h4_line-height']}
	{$fonts['h4_text-decoration']}
	{$fonts['h4_text-transform']}
	{$fonts['h4_letter-spacing']}
	{$fonts['h4_margin-top']}
	{$fonts['h4_margin-bottom']}
}
h5 {
	{$fonts['h5_font-family']}
	{$fonts['h5_font-size']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_font-style']}
	{$fonts['h5_line-height']}
	{$fonts['h5_text-decoration']}
	{$fonts['h5_text-transform']}
	{$fonts['h5_letter-spacing']}
	{$fonts['h5_margin-top']}
	{$fonts['h5_margin-bottom']}
}
h6 {
	{$fonts['h6_font-family']}
	{$fonts['h6_font-size']}
	{$fonts['h6_font-weight']}
	{$fonts['h6_font-style']}
	{$fonts['h6_line-height']}
	{$fonts['h6_text-decoration']}
	{$fonts['h6_text-transform']}
	{$fonts['h6_letter-spacing']}
	{$fonts['h6_margin-top']}
	{$fonts['h6_margin-bottom']}
}

input[type="text"],
input[type="number"],
input[type="email"],
input[type="tel"],
input[type="search"],
input[type="password"],
textarea,
textarea.wp-editor-area,
.select_container,
select,
.select_container select {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}

button,
.wp-block-button .wp-block-button__link,
input[type="button"],
input[type="reset"],
input[type="submit"],
.theme_button,
.gallery_preview_show .post_readmore,
.more-link,
div.esg-filter-wrapper .esg-filterbutton > span,
.academee_tabs .academee_tabs_titles li a {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

.top_panel .slider_engine_revo .slide_title {
	{$fonts['h1_font-family']}
}

blockquote,
mark, ins,
.logo_text,
.post_price.price,
.theme_scroll_down {
	{$fonts['h5_font-family']}
}

.post_meta {
	{$fonts['info_font-family']}
	{$fonts['info_font-size']}
	{$fonts['info_font-weight']}
	{$fonts['info_font-style']}
	{$fonts['info_line-height']}
	{$fonts['info_text-decoration']}
	{$fonts['info_text-transform']}
	{$fonts['info_letter-spacing']}
	{$fonts['info_margin-top']}
	{$fonts['info_margin-bottom']}
}

em, i,
.post-date, .rss-date 
.post_date, .post_meta_item, .post_counters_item,
.comments_list_wrap .comment_date,
.comments_list_wrap .comment_time,
.comments_list_wrap .comment_counters,
.top_panel .slider_engine_revo .slide_subtitle,
.logo_slogan,
fieldset legend,
.wp-caption-overlay .wp-caption .wp-caption-text,
.wp-caption-overlay .wp-caption .wp-caption-dd,
.format-audio .post_featured .post_audio_author,
.trx_addons_audio_player .audio_author,
.post_item_single .post_content .post_meta,
.author_bio .author_link,
.comments_list_wrap .comment_posted,
.comments_list_wrap .comment_reply {
	{$fonts['info_font-family']}
}
figure figcaption,
.wp-caption .wp-caption-text,
.wp-caption .wp-caption-dd, table th{
    {$fonts['h6_font-family']}
}
.search_wrap .search_results .post_meta_item,
.search_wrap .search_results .post_counters_item {
	{$fonts['p_font-family']}
}

.logo_text {
	{$fonts['logo_font-family']}
	{$fonts['logo_font-size']}
	{$fonts['logo_font-weight']}
	{$fonts['logo_font-style']}
	{$fonts['logo_line-height']}
	{$fonts['logo_text-decoration']}
	{$fonts['logo_text-transform']}
	{$fonts['logo_letter-spacing']}
}
.logo_footer_text {
	{$fonts['logo_font-family']}
}

.menu_main_nav_area {
	{$fonts['menu_font-size']}
	{$fonts['menu_line-height']}
}
.menu_main_nav > li,
.menu_main_nav > li > a {
	{$fonts['menu_font-family']}
	{$fonts['menu_font-weight']}
	{$fonts['menu_font-style']}
	{$fonts['menu_text-decoration']}
	{$fonts['menu_text-transform']}
	{$fonts['menu_letter-spacing']}
}
.menu_main_nav > li ul,
.menu_main_nav > li ul > li,
.menu_main_nav > li ul > li > a {
	{$fonts['submenu_font-family']}
	{$fonts['submenu_font-size']}
	{$fonts['submenu_font-weight']}
	{$fonts['submenu_font-style']}
	{$fonts['submenu_line-height']}
	{$fonts['submenu_text-decoration']}
	{$fonts['submenu_text-transform']}
	{$fonts['submenu_letter-spacing']}
}
.menu_mobile .menu_mobile_nav_area > ul > li,
.menu_mobile .menu_mobile_nav_area > ul > li > a {
	{$fonts['menu_font-family']}
}
.menu_mobile .menu_mobile_nav_area > ul > li li,
.menu_mobile .menu_mobile_nav_area > ul > li li > a {
	{$fonts['submenu_font-family']}
}


/* Custom Headers */
.sc_layouts_row,
.sc_layouts_row input[type="text"] {
	{$fonts['h5_font-family']}
	{$fonts['p_font-size']}
	{$fonts['p_font-weight']}
	{$fonts['menu_font-style']}
	{$fonts['menu_line-height']}
}
.sc_layouts_row.sc_layouts_row_type_narrow{
    {$fonts['p_font-family']}
}
.sc_layouts_row .sc_button {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.sc_layouts_menu_nav > li,
.sc_layouts_menu_nav > li > a {
	{$fonts['menu_font-family']}
	{$fonts['menu_font-weight']}
	{$fonts['menu_font-style']}
	{$fonts['menu_font-size']}
	{$fonts['menu_text-decoration']}
	{$fonts['menu_text-transform']}
	{$fonts['menu_letter-spacing']}
}
.sc_layouts_menu_popup .sc_layouts_menu_nav > li,
.sc_layouts_menu_popup .sc_layouts_menu_nav > li > a,
.sc_layouts_menu_nav > li ul,
.sc_layouts_menu_nav > li ul > li,
.sc_layouts_menu_nav > li ul > li > a {
	{$fonts['submenu_font-family']}
	{$fonts['submenu_font-size']}
	{$fonts['submenu_font-weight']}
	{$fonts['submenu_font-style']}
	{$fonts['submenu_line-height']}
	{$fonts['submenu_text-decoration']}
	{$fonts['submenu_text-transform']}
	{$fonts['submenu_letter-spacing']}
}

CSS;
			$rez = apply_filters('academee_filter_get_css', $rez, false, $fonts, '');
			$css['fonts'] = $rez['fonts'];

			
			// Border radius
			//--------------------------------------
			$rad = academee_get_border_radius();
			$rad50 = ' '.$rad != ' 0' ? '50%' : 0;
			$css['fonts'] .= <<<CSS



textarea.wp-editor-area {
	-webkit-border-radius: 0 0 {$rad} {$rad};
	    -ms-border-radius: 0 0 {$rad} {$rad};
			border-radius: 0 0 {$rad} {$rad};
}

/* Radius 50% or 0 */
.widget li a img {
	-webkit-border-radius: {$rad50};
	    -ms-border-radius: {$rad50};
			border-radius: {$rad50};
}

CSS;
		}


		// Theme colors
		//--------------------------------------
		if ($colors !== false) {
			$schemes = empty($only_scheme) ? array_keys(academee_get_list_schemes()) : array($only_scheme);
	
			if (count($schemes) > 0) {
				$rez = array();
				foreach ($schemes as $scheme) {
					// Prepare colors
					if (empty($only_scheme)) $colors = academee_get_scheme_colors($scheme);
	
					// Make theme-specific colors and tints
					$colors = academee_customizer_add_theme_colors($colors);
			
					// Make styles
					$rez['colors'] = <<<CSS

/* Common tags */
body {
	background-color: {$colors['bg_color']};
}
.scheme_self {
	color: {$colors['text']};
}
h1, h2, h3, h4, h5, h6,
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,
li a,
[class*="color_style_"] h1 a, [class*="color_style_"] h2 a, [class*="color_style_"] h3 a, [class*="color_style_"] h4 a, [class*="color_style_"] h5 a, [class*="color_style_"] h6 a, [class*="color_style_"] li a {
	color: {$colors['text_dark']};
}
h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
li a:hover {
	color: {$colors['text_link']};
}
.color_style_link2 h1 a:hover, .color_style_link2 h2 a:hover, .color_style_link2 h3 a:hover, .color_style_link2 h4 a:hover, .color_style_link2 h5 a:hover, .color_style_link2 h6 a:hover, .color_style_link2 li a:hover {
	color: {$colors['text_link2']};
}
.color_style_link3 h1 a:hover, .color_style_link3 h2 a:hover, .color_style_link3 h3 a:hover, .color_style_link3 h4 a:hover, .color_style_link3 h5 a:hover, .color_style_link3 h6 a:hover, .color_style_link3 li a:hover {
	color: {$colors['text_link3']};
}
.color_style_dark h1 a:hover, .color_style_dark h2 a:hover, .color_style_dark h3 a:hover, .color_style_dark h4 a:hover, .color_style_dark h5 a:hover, .color_style_dark h6 a:hover, .color_style_dark li a:hover {
	color: {$colors['text_link']};
}

dt, b, strong, i, em, mark, ins {	
	color: {$colors['text_dark']};
}
s, strike, del {	
	color: {$colors['text_light']};
}

code {
	color: {$colors['alter_text']};
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
}
code a {
	color: {$colors['alter_link']};
}
code a:hover {
	color: {$colors['alter_hover']};
}

a {
	color: {$colors['text_link']};
}
a:hover {
	color: {$colors['text_hover']};
}
.color_style_link2 a {
	color: {$colors['text_link2']};
}
.color_style_link2 a:hover {
	color: {$colors['text_hover2']};
}
.color_style_link3 a {
	color: {$colors['text_link3']};
}
.color_style_link3 a:hover {
	color: {$colors['text_hover3']};
}
.color_style_dark a {
	color: {$colors['text_dark']};
}
.color_style_dark a:hover {
	color: {$colors['text_link']};
}

blockquote {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}
blockquote:before {
	color: {$colors['inverse_text']};
}
blockquote a {
	color: {$colors['inverse_text']};
}
blockquote a:hover {
	color: {$colors['text_link']};
}

table th, table th + th, table td + th  {
	border-color: {$colors['extra_bd_color']};
}
table td, table th + td, table td + td {
	color: {$colors['alter_dark']};
	border-color: {$colors['alter_bd_color']};
}
table th {
	color: {$colors['inverse_text']};
	background-color: {$colors['extra_bg_color']};
}

table > tbody > tr:nth-child(2n+1) > td {
	background-color: {$colors['input_bg_color_04']};
}
table > tbody > tr:nth-child(2n) > td {
	background-color: {$colors['input_bg_color']};
}
table th a:hover {
	color: {$colors['extra_dark']};
}

.trx_addons_absent table th a:hover{
	color: {$colors['inverse_text']};
}

hr {
	border-color: {$colors['bd_color']};
}
figure figcaption,
.wp-caption .wp-caption-text,
.wp-caption .wp-caption-dd,
.wp-caption-overlay .wp-caption .wp-caption-text,
.wp-caption-overlay .wp-caption .wp-caption-dd {
	color: {$colors['inverse_text']};
	background-color: {$colors['extra_bg_color']};
}
ul > li:before {
	color: {$colors['text_link']};
}


/* Form fields
-------------------------------------------------- */


button[disabled],
.comment-form input[type="submit"][disabled],
.wpcf7-form input[type="submit"][disabled],
input[type="button"][disabled] {
    background-color: {$colors['text_light']} !important;
}

.mc4wp-form input[type="submit"][disabled]{
    color: {$colors['text']} !important;
}

.widget_search form:after,
.woocommerce.widget_product_search form:after,
.widget_display_search form:after,
#bbpress-forums #bbp-search-form:after {
	color: {$colors['input_text']};
}
.widget_search form:hover:after,
.woocommerce.widget_product_search form:hover:after,
.widget_display_search form:hover:after,
#bbpress-forums #bbp-search-form:hover:after {
	color: {$colors['input_dark']};
}
.trx_addons_field_error, input[type="text"].trx_addons_field_error{
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.search_wrap .search_form_wrap .search_form .search_submit{
    color: {$colors['text_dark']};
}
.sc_layouts_row_type_narrow .search_wrap .search_form_wrap .search_form .search_submit{
    color: {$colors['alter_link2']};
}


/* Field set */
fieldset {
	border-color: {$colors['bd_color']};
}
fieldset legend {
	color: {$colors['text_dark']};
	background-color: {$colors['bg_color']};
}

/* Text fields */
.woocommerce form .form-row input.input-text::-webkit-input-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text::-moz-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text:-ms-input-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text:-moz-placeholder{color: {$colors['text_dark']};}

input[placeholder]               { color: {$colors['text_dark']};opacity: 1; }
input::-webkit-input-placeholder { color: {$colors['text_dark']}; opacity: 1; }
input::-moz-placeholder          { color: {$colors['text_dark']}; opacity: 1; }
input:-ms-input-placeholder      { color: {$colors['text_dark']}; opacity: 1; }
input:-moz-placeholder      { color: {$colors['text_dark']};opacity: 1;  }


form.wpcf7-form input[placeholder]               { color: {$colors['text_dark']};opacity: 1; }
form.wpcf7-form input::-webkit-input-placeholder { color: {$colors['text_dark']}; opacity: 1; }
form.wpcf7-form input::-moz-placeholder          { color: {$colors['text_dark']}; opacity: 1; }
form.wpcf7-form input:-ms-input-placeholder      { color: {$colors['text_dark']}; opacity: 1; }
form.wpcf7-form input:-moz-placeholder      { color: {$colors['text_dark']};opacity: 1;  }

textarea[placeholder]               { color: {$colors['input_dark']};opacity: 1; }
textarea::-webkit-input-placeholder { color: {$colors['input_dark']};opacity: 1; }
textarea::-moz-placeholder          { color: {$colors['input_dark']};opacity: 1; }
textarea:-ms-input-placeholder      { color: {$colors['input_dark']}; opacity: 1; }
textarea:-moz-placeholder      { color: {$colors['input_dark']};opacity: 1;  }

input.trx_addons_field_error[placeholder]               { color: {$colors['input_dark']}; opacity: 1;}
input.trx_addons_field_error::-webkit-input-placeholder { color: {$colors['input_dark']}; opacity: 1; }
input.trx_addons_field_error::-moz-placeholder          { color: {$colors['input_dark']};opacity: 1;  }
input.trx_addons_field_error:-ms-input-placeholder      { color: {$colors['input_dark']}; opacity: 1; }
input.trx_addons_field_error:-moz-placeholder      { color: {$colors['input_dark']}; opacity: 1; }

textarea.trx_addons_field_error[placeholder]               { color: {$colors['input_dark']}; opacity: 1;}
textarea.trx_addons_field_error::-webkit-input-placeholder { color: {$colors['input_dark']};opacity: 1; }
textarea.trx_addons_field_error::-moz-placeholder          { color: {$colors['input_dark']}; opacity: 1;}
textarea.trx_addons_field_error:-moz-placeholder          { color: {$colors['input_dark']};opacity: 1; }
textarea.trx_addons_field_error:-ms-input-placeholder      { color: {$colors['input_dark']};opacity: 1;  }

input.trx_addons_field_error:focus[placeholder]               { color: {$colors['input_dark']};opacity: 1; }
input.trx_addons_field_error:focus::-webkit-input-placeholder { color: {$colors['input_dark']};opacity: 1;  }
input.trx_addons_field_error:focus::-moz-placeholder         { color: {$colors['input_dark']};opacity: 1;  }
input.trx_addons_field_error:focus:-ms-input-placeholder      { color: {$colors['input_dark']};opacity: 1;  }
input.trx_addons_field_error:focus:-moz-placeholder      { color: {$colors['input_dark']};opacity: 1;  }

textarea.trx_addons_field_error:focus[placeholder]            { color: {$colors['input_dark']};opacity: 1; }
textarea.trx_addons_field_error:focus::-webkit-input-placeholder { color: {$colors['input_dark']};opacity: 1; }
textarea.trx_addons_field_error:focus::-moz-placeholder         { color: {$colors['input_dark']};opacity: 1; }
textarea.trx_addons_field_error:focus:-ms-input-placeholder     { color: {$colors['input_dark']}; opacity: 1; }
textarea.trx_addons_field_error:focus:-moz-placeholder     { color: {$colors['input_dark']};opacity: 1;  }

input[type="search"]::-webkit-input-placeholder { color: {$colors['input_text']};opacity: 1;  }
input[type="search"]::-moz-placeholder { color: {$colors['input_text']};opacity: 1;  }
input[type="search"]:-ms-input-placeholder  { color: {$colors['input_text']};opacity: 1;  }

input[type="text"]::-webkit-input-placeholder { color: {$colors['inverse_text']};opacity: 1;  }
input[type="text"]::-moz-placeholder { color: {$colors['inverse_text']};opacity: 1;  }
input[type="text"]:-ms-input-placeholder  { color: {$colors['inverse_text']}; opacity: 1; }

.sc_form input[type="text"]::-webkit-input-placeholder { color: {$colors['text_dark']};opacity: 1; }
.sc_form input[type="text"]::-moz-placeholder         { color: {$colors['text_dark']};opacity: 1; }
.sc_form input[type="text"]:-ms-input-placeholder     { color: {$colors['text_dark']};opacity: 1;  }
.sc_form input[type="text"]:-moz-placeholder     { color: {$colors['text_dark']};opacity: 1;  }

.mc4wp-form input[type="email"]::-webkit-input-placeholder { color: {$colors['text_light']};opacity: 1; }
.mc4wp-form input[type="email"]::-moz-placeholder         { color: {$colors['text_light']};opacity: 1; }
.mc4wp-form input[type="email"]:-ms-input-placeholder     { color: {$colors['text_light']}; opacity: 1; }
.mc4wp-form input[type="email"]:-moz-placeholder     { color: {$colors['text_light']};opacity: 1;  }

.comments_wrap .comments_field input::-webkit-input-placeholder { color: {$colors['text_dark']};opacity: 1; }
.comments_wrap .comments_field input::-moz-placeholder         { color: {$colors['text_dark']};opacity: 1; }
.comments_wrap .comments_field input:-ms-input-placeholder     { color: {$colors['text_dark']}; opacity: 1; }
.comments_wrap .comments_field input:-moz-placeholder     { color: {$colors['text_dark']}; opacity: 1; }


.sc_layouts_row_type_narrow .search_form input::-webkit-input-placeholder { color: {$colors['alter_link2']}!important;opacity: 1;  }
.sc_layouts_row_type_narrow .search_form input::-moz-placeholder { color: {$colors['alter_link2']}!important;opacity: 1;  }
.sc_layouts_row_type_narrow .search_form input:-ms-input-placeholder  { color: {$colors['alter_link2']}!important;opacity: 1;  }



input[type="text"],
input[type="number"],
input[type="email"],
input[type="search"],
input[type="tel"],
input[type="password"],
.select2-container .select2-choice,
.select2-container .select2-selection,
textarea,
textarea.wp-editor-area,
/* BB Press */
#buddypress .dir-search input[type="search"],
#buddypress .dir-search input[type="text"],
#buddypress .groups-members-search input[type="search"],
#buddypress .groups-members-search input[type="text"],
#buddypress .standard-form input[type="color"],
#buddypress .standard-form input[type="date"],
#buddypress .standard-form input[type="datetime-local"],
#buddypress .standard-form input[type="datetime"],
#buddypress .standard-form input[type="email"],
#buddypress .standard-form input[type="month"],
#buddypress .standard-form input[type="number"],
#buddypress .standard-form input[type="password"],
#buddypress .standard-form input[type="range"],
#buddypress .standard-form input[type="search"],
#buddypress .standard-form input[type="tel"],
#buddypress .standard-form input[type="text"],
#buddypress .standard-form input[type="time"],
#buddypress .standard-form input[type="url"],
#buddypress .standard-form input[type="week"],
#buddypress .standard-form select,
#buddypress .standard-form textarea,
#buddypress form#whats-new-form textarea,
/* Booked */
#booked-page-form input[type="email"],
#booked-page-form input[type="text"],
#booked-page-form input[type="password"],
#booked-page-form textarea,
.booked-upload-wrap,
.booked-upload-wrap input {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']};
	background-color: {$colors['input_bg_color']};
}
.select_container{
color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']};
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="email"]:focus,
input[type="search"]:focus,
input[type="tel"]:focus,
input[type="search"]:focus,
input[type="password"]:focus,
select option:hover,
select option:focus,
.select2-container .select2-choice:hover,
textarea:focus,
textarea.wp-editor-area:focus,
/* BB Press */
#buddypress .dir-search input[type="search"]:focus,
#buddypress .dir-search input[type="text"]:focus,
#buddypress .groups-members-search input[type="search"]:focus,
#buddypress .groups-members-search input[type="text"]:focus,
#buddypress .standard-form input[type="color"]:focus,
#buddypress .standard-form input[type="date"]:focus,
#buddypress .standard-form input[type="datetime-local"]:focus,
#buddypress .standard-form input[type="datetime"]:focus,
#buddypress .standard-form input[type="email"]:focus,
#buddypress .standard-form input[type="month"]:focus,
#buddypress .standard-form input[type="number"]:focus,
#buddypress .standard-form input[type="password"]:focus,
#buddypress .standard-form input[type="range"]:focus,
#buddypress .standard-form input[type="search"]:focus,
#buddypress .standard-form input[type="tel"]:focus,
#buddypress .standard-form input[type="text"]:focus,
#buddypress .standard-form input[type="time"]:focus,
#buddypress .standard-form input[type="url"]:focus,
#buddypress .standard-form input[type="week"]:focus,
#buddypress .standard-form select:focus,
#buddypress .standard-form textarea:focus,
#buddypress form#whats-new-form textarea:focus,
/* Booked */
#booked-page-form input[type="email"]:focus,
#booked-page-form input[type="text"]:focus,
#booked-page-form input[type="password"]:focus,
#booked-page-form textarea:focus,
.booked-upload-wrap:hover,
.booked-upload-wrap input:focus {
	color: {$colors['input_dark']};
	border-color: {$colors['input_bd_hover']};
	background-color: {$colors['input_bg_hover']};
}
.select_container:hover{
color: {$colors['input_dark']};
	border-color: {$colors['input_bd_hover']};
}


/* Select containers */
.select_container:before {
	color: {$colors['input_text']};

}
.select_container:focus:before,
.select_container:hover:before {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']};
}
.select_container:after {
	color: {$colors['input_text']};
}
.select_container:focus:after,
.select_container:hover:after {
	color: {$colors['input_dark']};
}
.select_container select {
	color: {$colors['input_text']};
	background: {$colors['input_bg_color']} !important;
}
.select_container select:focus {
	color: {$colors['input_dark']};
}

.select2-results {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_hover']};
	background: {$colors['input_bg_color']};
}
.select2-results .select2-highlighted {
	color: {$colors['input_dark']};
	background: {$colors['input_bg_hover']};
}
.wpcf7-form input[type="checkbox"] + span:before,
input[type="radio"] + label:before,
input[type="checkbox"] + label:before {
	border-color: {$colors['input_bd_color']};
	background-color: {$colors['input_bg_color']};
}


/* Simple button */
.sc_button_simple:not(.sc_button_bg_image),
.sc_button_simple:not(.sc_button_bg_image):before,
.sc_button_simple:not(.sc_button_bg_image):after {
	color:{$colors['text_link']};
}
.sc_button_simple:not(.sc_button_bg_image):hover,
.sc_button_simple:not(.sc_button_bg_image):hover:before,
.sc_button_simple:not(.sc_button_bg_image):hover:after {
	color:{$colors['text_hover']} !important;
}

.sc_services_content .sc_button_simple:not(.sc_button_bg_image):hover,
.sc_services_content .sc_button_simple:not(.sc_button_bg_image):hover:before,
.sc_services_content .sc_button_simple:not(.sc_button_bg_image):hover:after {
	color:{$colors['inverse_text']} !important;
}



.sc_button_simple.color_style_link2:not(.sc_button_bg_image),
.sc_button_simple.color_style_link2:not(.sc_button_bg_image):before,
.sc_button_simple.color_style_link2:not(.sc_button_bg_image):after,
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image),
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image):before,
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image):after {
	color:{$colors['text_link2']};
}
.sc_button_simple.color_style_link2:not(.sc_button_bg_image):hover,
.sc_button_simple.color_style_link2:not(.sc_button_bg_image):hover:before,
.sc_button_simple.color_style_link2:not(.sc_button_bg_image):hover:after,
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image):hover,
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image):hover:before,
.color_style_link2 .sc_button_simple:not(.sc_button_bg_image):hover:after {
	color:{$colors['text_hover2']};
}

.sc_button_simple.color_style_link3:not(.sc_button_bg_image),
.sc_button_simple.color_style_link3:not(.sc_button_bg_image):before,
.sc_button_simple.color_style_link3:not(.sc_button_bg_image):after,
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image),
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image):before,
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image):after {
	color:{$colors['text_link3']};
}
.sc_button_simple.color_style_link3:not(.sc_button_bg_image):hover,
.sc_button_simple.color_style_link3:not(.sc_button_bg_image):hover:before,
.sc_button_simple.color_style_link3:not(.sc_button_bg_image):hover:after,
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image):hover,
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image):hover:before,
.color_style_link3 .sc_button_simple:not(.sc_button_bg_image):hover:after {
	color:{$colors['text_hover3']};
}

.sc_button_simple.color_style_dark:not(.sc_button_bg_image),
.sc_button_simple.color_style_dark:not(.sc_button_bg_image):before,
.sc_button_simple.color_style_dark:not(.sc_button_bg_image):after,
.color_style_dark .sc_button_simple:not(.sc_button_bg_image),
.color_style_dark .sc_button_simple:not(.sc_button_bg_image):before,
.color_style_dark .sc_button_simple:not(.sc_button_bg_image):after {
	color:{$colors['text_dark']};
}
.sc_button_simple.color_style_dark:not(.sc_button_bg_image):hover,
.sc_button_simple.color_style_dark:not(.sc_button_bg_image):hover:before,
.sc_button_simple.color_style_dark:not(.sc_button_bg_image):hover:after,
.color_style_dark .sc_button_simple:not(.sc_button_bg_image):hover,
.color_style_dark .sc_button_simple:not(.sc_button_bg_image):hover:before,
.color_style_dark .sc_button_simple:not(.sc_button_bg_image):hover:after {
	color:{$colors['text_link']};
}


/* Bordered button */
.sc_button_bordered:not(.sc_button_bg_image) {
	color:{$colors['text_link']};
	border-color:{$colors['text_link']};
}
.sc_button_bordered:not(.sc_button_bg_image):hover {
	color:{$colors['text_hover']} !important;
	border-color:{$colors['text_hover']} !important;
}
.sc_button_bordered.color_style_link2:not(.sc_button_bg_image) {
	color:{$colors['text_link2']};
	border-color:{$colors['text_link2']};
}
.sc_button_bordered.color_style_link2:not(.sc_button_bg_image):hover {
	color:{$colors['text_hover2']} !important;
	border-color:{$colors['text_hover2']} !important;
}
.sc_button_bordered.color_style_link3:not(.sc_button_bg_image) {
	color:{$colors['text_link3']};
	border-color:{$colors['text_link3']};
}
.sc_button_bordered.color_style_link3:not(.sc_button_bg_image):hover {
	color:{$colors['text_hover3']} !important;
	border-color:{$colors['text_hover3']} !important;
}
.sc_button_bordered.color_style_dark:not(.sc_button_bg_image) {
	color:{$colors['text_dark']};
	border-color:{$colors['text_dark']};
}
.sc_button_bordered.color_style_dark:not(.sc_button_bg_image):hover {
	color:{$colors['text_link']} !important;
	border-color:{$colors['text_link']} !important;
}

/* Normal button */

button,
.wp-block-button:not(.is-style-outline) .wp-block-button__link,
input[type="reset"],
input[type="submit"],
input[type="button"],
.more-link,
.comments_wrap .form-submit input[type="submit"],

/* ThemeREX Addons */
.sc_button_default,
.sc_button:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image),
.sc_action_item_link,
.socials_share:not(.socials_type_drop) .social_icon,
/* Tribe Events */
#tribe-bar-form .tribe-bar-submit input[type="submit"],
#tribe-bar-form.tribe-bar-mini .tribe-bar-submit input[type="submit"],
#tribe-bar-views li.tribe-bar-views-option a,
#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option.tribe-bar-active a,
#tribe-events .tribe-events-button,
.tribe-events-button,
.tribe-events-cal-links a,
.tribe-events-sub-nav li a,
/* WooCommerce */
.woocommerce #respond input#submit,
.woocommerce .button, .woocommerce-page .button,
.woocommerce a.button, .woocommerce-page a.button,
.woocommerce button.button, .woocommerce-page button.button,
.woocommerce input.button, .woocommerce-page input.button,
.woocommerce input[type="button"], .woocommerce-page input[type="button"],
.woocommerce input[type="submit"], .woocommerce-page input[type="submit"],
.woocommerce nav.woocommerce-pagination ul li a,
.woocommerce #respond input#submit.alt,
.woocommerce a.button.alt,
.woocommerce button.button.alt,
.woocommerce input.button.alt {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
	box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
}

.wp-block-button.is-style-outline .wp-block-button__link{
	color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.sc_button.custom_button:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image), .sticky a.more-link{
	background-color: {$colors['bg_color']};
	color: {$colors['text_dark']};
	box-shadow: 0px 0px 0 13px {$colors['text_link2']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link2']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link2']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link2']};
}
.sc_button.custom_button:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image):hover, .sticky a.more-link:hover{
	background-color: {$colors['text_dark']};
	box-shadow: 0px 0px 0 13px {$colors['text_dark_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_dark_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_dark_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_dark_04']};
}
.theme_button {
	color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_link']} !important;
}
.sc_price_link {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link_bd']};
}
.sc_button_default.color_style_link2,
.sc_button.color_style_link2:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image) {
	background-color: {$colors['text_link2']};
}
.sc_button_default.color_style_link3,
.sc_button.color_style_link3:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image) {
	background-color: {$colors['text_link3']};
}
.sc_button_default.color_style_dark,
.sc_button.color_style_dark:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image) {
	color: {$colors['bg_color']};
	background-color: {$colors['text_dark']};
}

button:hover,
button:focus,
.wp-block-button:not(.is-style-outline) .wp-block-button__link:hover,
input[type="submit"]:hover,
input[type="submit"]:focus,
input[type="reset"]:hover,
input[type="reset"]:focus,
input[type="button"]:hover,
input[type="button"]:focus,
.more-link:hover,
.comments_wrap .form-submit input[type="submit"]:hover,
.comments_wrap .form-submit input[type="submit"]:focus,

/* ThemeREX Addons */
.sc_button_default:hover,
.sc_button:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image):hover,
.sc_action_item_link:hover,
.socials_share:not(.socials_type_drop) .social_icon:hover,
/* Tribe Events */
#tribe-bar-form .tribe-bar-submit input[type="submit"]:hover,
#tribe-bar-form .tribe-bar-submit input[type="submit"]:focus,
#tribe-bar-form.tribe-bar-mini .tribe-bar-submit input[type="submit"]:hover,
#tribe-bar-form.tribe-bar-mini .tribe-bar-submit input[type="submit"]:focus,
#tribe-bar-views li.tribe-bar-views-option a:hover,
#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option.tribe-bar-active a:hover,
#tribe-events .tribe-events-button:hover,
.tribe-events-button:hover,
.tribe-events-cal-links a:hover,
.tribe-events-sub-nav li a:hover,
/* WooCommerce */
.woocommerce #respond input#submit:hover,
.woocommerce .button:hover, .woocommerce-page .button:hover,
.woocommerce a.button:hover, .woocommerce-page a.button:hover,
.woocommerce button.button:hover, .woocommerce-page button.button:hover,
.woocommerce input.button:hover, .woocommerce-page input.button:hover,
.woocommerce input[type="button"]:hover, .woocommerce-page input[type="button"]:hover,
.woocommerce input[type="submit"]:hover, .woocommerce-page input[type="submit"]:hover,
.woocommerce nav.woocommerce-pagination ul li a:hover,
.woocommerce nav.woocommerce-pagination ul li span.current {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
	box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
}
.wp-block-button.is-style-outline .wp-block-button__link:hover{
	color: {$colors['text_hover']};
	border-color: {$colors['text_hover']};
}
.sc_button.sc_button_default2:not(.sc_button_simple):not(.sc_button_bordered), .sc_action_item_link.sc_button_default2{
    color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
	box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']};
}
.sc_button.sc_button_default2:not(.sc_button_simple):not(.sc_button_bordered):hover,
.sc_action_item_link.sc_button_default2:hover{
    color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
}



.woocommerce #respond input#submit.alt:hover,
.woocommerce a.button.alt:hover,
.woocommerce button.button.alt:hover,
.woocommerce input.button.alt:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}
.theme_button:hover,
.theme_button:focus {
	color: {$colors['inverse_hover']} !important;
	background-color: {$colors['text_link_blend']} !important;
}
.sc_price .sc_price_link,
.sc_price_link{
box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
}
.sc_price:hover .sc_price_link,
.sc_price_link:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
	box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
}
.sc_button_default.color_style_link2:hover,
.sc_button.color_style_link2:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image):hover {
	background-color: {$colors['text_hover2']};
}
.sc_button_default.color_style_link3:hover,
.sc_button.color_style_link3:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image):hover {
	background-color: {$colors['text_hover3']};
}
.sc_button_default.color_style_dark:hover,
.sc_button.color_style_dark:not(.sc_button_simple):not(.sc_button_bordered):not(.sc_button_bg_image):hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
}
.comment-author-link{
    color: {$colors['alter_link']};
}
.widget li.recentcomments{
    color: {$colors['alter_text']};
}
/* Buttons in sidebars */

/* MailChimp */
.mc4wp-form input[type="submit"]{
	color: {$colors['input_dark']};
	background-color: {$colors['inverse_link']};
	border-color: {$colors['text_link_bd']};
}
/* WooCommerce */
#btn-buy,
.woocommerce .woocommerce-message .button,
.woocommerce .woocommerce-error .button,
.woocommerce .woocommerce-info .button,
.widget.woocommerce .button,
.widget.woocommerce a.button,
.widget.woocommerce button.button,
.widget.woocommerce input.button,
.widget.woocommerce input[type="button"],
.widget.woocommerce input[type="submit"],
.widget.WOOCS_CONVERTER .button,
.widget.yith-woocompare-widget a.button,
.widget_product_search .search_button {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link_bd']};
}
/* MailChimp */
.mc4wp-form input[type="submit"]:hover,
.mc4wp-form input[type="submit"]:focus{
    color: {$colors['inverse_link']};
	background-color: {$colors['inverse_dark']};
    border-color: {$colors['text_link_bd']};
}
/* WooCommerce */
#btn-buy:hover,
.woocommerce .woocommerce-message .button:hover,
.woocommerce .woocommerce-error .button:hover,
.woocommerce .woocommerce-info .button:hover,
.widget.woocommerce .button:hover,
.widget.woocommerce a.button:hover,
.widget.woocommerce button.button:hover,
.widget.woocommerce input.button:hover,
.widget.woocommerce input[type="button"]:hover,
.widget.woocommerce input[type="button"]:focus,
.widget.woocommerce input[type="submit"]:hover,
.widget.woocommerce input[type="submit"]:focus,
.widget.WOOCS_CONVERTER .button:hover,
.widget.yith-woocompare-widget a.button:hover,
.widget_product_search .search_button:hover {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_hover']};
    border-color: {$colors['text_link_bd_hover']};
}
.widget.woocommerce .button+.button.checkout:hover{
   color: {$colors['text_link']};
}
.sc_layouts_cart_widget .widget.woocommerce .button+.button.checkout{
     color: {$colors['inverse_text']};
}
.sc_layouts_cart_widget .widget.woocommerce .button+.button.checkout:hover{
   color: {$colors['text_link']};
}
/*slider*/
.tp-caption.button_slider:hover, .button_slider:hover{
    background-color: {$colors['text_link']}!important;
}
.tp-caption.button_slider, .button_slider{
   box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['text_hover_04']}!important;
}
.tp-caption.button_slider:hover, .button_slider:hover{
   box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']}!important;
}
.ares .tp-bullet{
    background-color: {$colors['bg_color_07']};
}
.ares .tp-bullet:hover, .ares .tp-bullet.selected{
    background-color: {$colors['bg_color']};
}
/* Buttons in WP Editor */
.wp-editor-container input[type="button"] {
	background-color: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
	color: {$colors['alter_dark']};
	-webkit-box-shadow: 0 1px 0 0 {$colors['alter_bd_hover']};
	    -ms-box-shadow: 0 1px 0 0 {$colors['alter_bd_hover']};
			box-shadow: 0 1px 0 0 {$colors['alter_bd_hover']};	
}
.wp-editor-container input[type="button"]:hover,
.wp-editor-container input[type="button"]:focus {
	background-color: {$colors['alter_bg_hover']};
	border-color: {$colors['alter_bd_hover']};
	color: {$colors['alter_link']};
}



/* WP Standard classes */
.sticky {
	border-color: {$colors['text_link']};
	background-color: {$colors['text_link']};
}
.sticky .label_sticky {
	border-top-color: {$colors['text_link']};
}
.sticky .post_content_inner p{
	color: {$colors['inverse_text']};
}

.sticky .post_title a{
	color: {$colors['inverse_text']};
}
.sticky.post_item .post_title a:hover{
	color: {$colors['text_dark']};
}
.sticky a.more-link:hover{
	color: {$colors['inverse_text']};
}
.sticky .post_meta_item.post_date a, .sticky .post_meta_item.post_categories a,
.sticky .post_meta_item.post_categories:before, .sticky .post_meta_item.post_date:before{
	color: {$colors['inverse_text_07']};
}
.sticky .post_meta_item.post_date a:hover{
	color: {$colors['text_dark']};
}


/* Page */
#page_preloader,
.scheme_self.header_position_under .page_content_wrap,
.page_wrap {
	background-color: {$colors['bg_color']};
}
.preloader_wrap > div {
	background-color: {$colors['text_link']};
}

/* Header */
.scheme_self.top_panel.with_bg_image:before {
	background-color: {$colors['bg_color_07']};
}
.scheme_self.top_panel .slider_engine_revo .slide_subtitle,
.top_panel .slider_engine_revo .slide_subtitle {
	color: {$colors['text_link']};
}
.top_panel_default .top_panel_navi,
.scheme_self.top_panel_default .top_panel_navi {
	background-color: {$colors['bg_color']};
}
.top_panel_default .top_panel_title,
.scheme_self.top_panel_default .top_panel_title {
	background-color: {$colors['alter_bg_color']};
}


/* Tabs */
div.esg-filter-wrapper .esg-filterbutton > span,
.academee_tabs .academee_tabs_titles li a {
	color: {$colors['text_dark']};
	background-color: {$colors['input_bg_color']};
	box-shadow: 0px 0px 0 13px {$colors['input_bg_color_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['input_bg_color_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['input_bg_color_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['input_bg_color_04']};
}
div.esg-filter-wrapper .esg-filterbutton > span:hover,
.academee_tabs .academee_tabs_titles li a:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
}
div.esg-filter-wrapper .esg-filterbutton.selected > span,
.academee_tabs .academee_tabs_titles li.ui-state-active a {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -webkit-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -moz-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
    -o-box-shadow: 0px 0px 0 13px {$colors['text_link_04']};
}

/* Post layouts */
.post_item {
	color: {$colors['text']};
}
.post_meta,
.post_meta_item,
.post_meta_item a,
.post_meta_item:before,
.post_meta_item:after,
.post_meta_item:hover:before,
.post_meta_item:hover:after,
.post_date a,
.post_date:before,
.post_date:after,
.post_info .post_info_item,
.post_info .post_info_item a,
.post_info_counters .post_counters_item,
.post_counters .socials_share .socials_caption:before,
.post_counters .socials_share .socials_caption:hover:before {
	color: {$colors['text_light']};
}
.post_date a:hover,
a.post_meta_item:hover,
a.post_meta_item:hover:before,
.post_meta_item a:hover,
.post_meta_item a:hover:before,
.post_info .post_info_item a:hover,
.post_info .post_info_item a:hover:before,
.post_info_counters .post_counters_item:hover,
.post_info_counters .post_counters_item:hover:before {
	color: {$colors['text_dark']};
}

.sc_blogger.sc_blogger_classic .post_meta,
.sc_blogger.sc_blogger_classic .post_meta_item,
.sc_blogger.sc_blogger_classic .post_meta_item a{
color: {$colors['text']};
}
.post_item .post_title a:hover {
	color: {$colors['text_link']};
}
.sidebar_inner .post_info_counters .post_counters_item{
    color: {$colors['alter_text']};
}

.post_meta_item.post_categories,
.post_meta_item.post_categories a {
	color: {$colors['text_light']};
}
.post_meta_item.post_categories a:hover {
	color: {$colors['text_dark']};
}

.post_meta_item .socials_share .social_items {
	background-color: {$colors['bg_color']};
}
.post_meta_item .social_items,
.post_meta_item .social_items:before {
	background-color: {$colors['bg_color']};
	border-color: {$colors['bd_color']};
	color: {$colors['text_light']};
}
.woocommerce .woocommerce-pagination:before,
.post_layout_excerpt:not(.sticky) + .post_layout_excerpt:not(.sticky):before, .nav-links:before {
    background: {$colors['input_bg_hover']}; /* Old browsers */
    background: -moz-linear-gradient(left, {$colors['input_bg_hover']} 1%, {$colors['bg_color']} 95%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, right top, color-stop(1%,{$colors['input_bg_hover']}), color-stop(95%,{$colors['bg_color']})); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* Opera 11.10+ */
    background: -ms-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* IE10+ */
    background: linear-gradient(to right, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$colors['input_bg_hover']}', endColorstr='{$colors['bg_color']}',GradientType=0 ); /* IE6-9 */
}

.scheme_self.footer_wrap .post_info .post_info_item a{
    color: {$colors['alter_text']};
}

.post_layout_classic {
	border-color: {$colors['bd_color']};
}

.scheme_self.gallery_preview:before {
	background-color: {$colors['bg_color']};
}
.scheme_self.gallery_preview {
	color: {$colors['text']};
}

.post_meta .post_meta_item.post_edit > a:after,
.post_meta .post_meta_item:after,
.vc_inline-link:after{
 background: {$colors['input_bg_color']}; /* Old browsers */
    background: -moz-linear-gradient(top, {$colors['input_bg_color']} 0%, {$colors['text_light']} 50%, {$colors['input_bg_color']} 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,{$colors['input_bg_color']}), color-stop(50%,{$colors['text_light']}), color-stop(100%,{$colors['input_bg_color']})); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, {$colors['input_bg_color']} 0%,{$colors['text_light']} 50%,{$colors['input_bg_color']} 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, {$colors['input_bg_color']} 0%,{$colors['text_light']} 50%,{$colors['input_bg_color']} 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top, {$colors['input_bg_color']} 0%,{$colors['text_light']} 50%,{$colors['input_bg_color']} 100%); /* IE10+ */
    background: linear-gradient(to bottom, {$colors['input_bg_color']} 0%,{$colors['text_light']} 50%,{$colors['input_bg_color']} 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$colors['input_bg_color']}', endColorstr='{$colors['input_bg_color']}',GradientType=0 ); /* IE6-9 */
}

/* Post Formats */

/* Audio */
.trx_addons_audio_player .audio_author,
.format-audio .post_featured .post_audio_author {
	color: {$colors['inverse_text']};
}
.format-audio .post_featured.without_thumb .post_audio {
	border-color: {$colors['alter_bg_hover']};
	background-color: {$colors['alter_bg_hover']}
}
.format-audio .post_featured.without_thumb .post_audio_title,
.without_thumb .mejs-controls .mejs-currenttime,
.without_thumb .mejs-controls .mejs-duration {
	color: {$colors['inverse_text']};

}

.trx_addons_audio_player.without_cover {
	border-color: {$colors['alter_bg_hover']};
	background-color: {$colors['alter_bg_hover']};
}
.trx_addons_audio_player.with_cover .audio_caption {
	color: {$colors['inverse_link']};
}
.trx_addons_audio_player.without_cover .audio_author, .trx_addons_audio_player .audio_author + .audio_caption {
	color: {$colors['inverse_text']};
}

.trx_addons_audio_player .mejs-container .mejs-controls .mejs-time {
	color: {$colors['inverse_text']};
}
.trx_addons_audio_player.with_cover .mejs-container .mejs-controls .mejs-time {
	color: {$colors['inverse_text']};
}

.footer_default.scheme_dark .mejs-container .mejs-controls,
.widget_area.scheme_dark .mejs-container .mejs-controls,
.mejs-container .mejs-controls,
.mejs-embed,
.mejs-embed body {
	background: {$colors['text_dark_07']};
}



.scheme_dark.footer_default .mejs-controls .mejs-time-rail .mejs-time-current,
.scheme_dark.sidebar .mejs-controls .mejs-time-rail .mejs-time-current,
.mejs-controls .mejs-time-rail .mejs-time-current, 
.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current{
background: {$colors['inverse_bd_color']};
}
.trx_addons_audio_player .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total:before, .trx_addons_audio_player .mejs-controls .mejs-time-rail .mejs-time-total:before, .mejs-controls .mejs-time-rail .mejs-time-loaded{
    background: {$colors['inverse_bd_hover']};
}
.mejs-controls .mejs-button {
	color: {$colors['inverse_link']};
	background: {$colors['text_link']};
}
.mejs-controls .mejs-button:hover {
	color: {$colors['inverse_hover']};
	background: {$colors['text_hover']};
}

/* Aside */
.format-aside .post_content_inner {
	color: {$colors['alter_dark']};
	background-color: {$colors['alter_bg_color']};
}

/* Link and Status */
.format-link .post_content_inner,
.format-status .post_content_inner {
	color: {$colors['text_dark']};
}

/* Chat */
.format-chat p > b,
.format-chat p > strong {
	color: {$colors['text_dark']};
}

/* Video */

.sidebar_inner .trx_addons_video_player.with_cover .video_hover {
	color: {$colors['alter_link']};
}
.sidebar_inner .trx_addons_video_player.with_cover .video_hover:hover {
	color: {$colors['inverse_hover']};
	background-color: {$colors['alter_link']};
}

/* Chess */
.post_layout_chess .post_content_inner:after {
	background: linear-gradient(to top, {$colors['bg_color']} 0%, {$colors['bg_color_0']} 100%) no-repeat scroll right top / 100% 100% {$colors['bg_color_0']};
}
.post_layout_chess_1 .post_meta:before {
	background-color: {$colors['bd_color']};
}

/* Pagination */
.nav-links-old {
	color: {$colors['text_dark']};
}
.nav-links-old a:hover {
	color: {$colors['text_dark']};
	border-color: {$colors['text_dark']};
}

div.esg-pagination .esg-pagination-button,
.page_links > a,
.comments_pagination .page-numbers{
	color: {$colors['text_light']};
}
div.esg-pagination .esg-pagination-button:hover,
div.esg-pagination .esg-pagination-button.selected,
.page_links > a:hover,
.page_links > span:not(.page_links_title),
.comments_pagination a.page-numbers:hover,
.comments_pagination .page-numbers.current{
	color: {$colors['text_dark']};
}
.woocommerce .woocommerce-pagination ul a.page-numbers,
.nav-links a.page-numbers{
color: {$colors['extra_text']};
}
.woocommerce nav.woocommerce-pagination ul li a:hover,
.woocommerce nav.woocommerce-pagination ul li span.current,
.nav-links a.page-numbers:hover,
.nav-links .page-numbers.current {
color: {$colors['text_dark']};
}
/* Single post */
.post_item_single .post_header .post_date {
	color: {$colors['text_light']};
}
.post_item_single .post_header .post_categories,
.post_item_single .post_header .post_categories a {
	color: {$colors['text_link']};
}
.post_item_single .post_header .post_meta_item,
.post_item_single .post_header .post_meta_item:before,
.post_item_single .post_header .post_meta_item:hover:before,
.post_item_single .post_header .post_meta_item a,
.post_item_single .post_header .post_meta_item a:before,
.post_item_single .post_header .post_meta_item a:hover:before,
.post_item_single .post_header .post_meta_item .socials_caption,
.post_item_single .post_header .post_meta_item .socials_caption:before,
.post_item_single .post_header .post_edit a {
	color: {$colors['text_light']};
}
.post_item_single .post_meta_item:hover,
.post_item_single .post_meta_item > a:hover,
.post_item_single .post_meta_item .socials_caption:hover,
.post_item_single .post_edit a:hover {
	color: {$colors['text_dark']};
}
.post_item_single .post_content .post_meta_label,
.post_item_single .post_content .post_meta_item:hover .post_meta_label {
	color: {$colors['text_dark']};
}
.post_item_single .post_content .post_tags,
.post_item_single .post_content .post_tags a {
	color: {$colors['text_link']};
}
.post_item_single .post_content .post_tags a:hover {
	color: {$colors['text_hover']};
}
.post_item_single .post_content .post_meta .post_share .social_item .social_icon {
	color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_hover']};
}
.post_item_single .post_content .post_meta .post_share .social_item:hover .social_icon {
	color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_link']};
}

.post-password-form input[type="submit"] {
	border-color: {$colors['text_dark']};
}
.post-password-form input[type="submit"]:hover,
.post-password-form input[type="submit"]:focus {
	color: {$colors['bg_color']};
}

/* Single post navi */
.nav-links-single .nav-links {
	border-color: {$colors['bd_color']};
}
.nav-links-single .nav-links a .meta-nav {
	color: {$colors['text_light']};
}
.nav-links-single .nav-links a .post_date {
	color: {$colors['text_light']};
}
.nav-links-single .nav-links a:hover .meta-nav,
.nav-links-single .nav-links a:hover .post_date {
	color: {$colors['text_dark']};
}
.nav-links-single .nav-links a:hover .post-title {
	color: {$colors['text_link']};
}

/* Author info */
.scheme_self.author_info {
	color: {$colors['text']};
	background-color: {$colors['bg_color']};
}
.scheme_self.author_info .author_title {
	color: {$colors['inverse_text']};
}
.scheme_self.author_info a {
	color: {$colors['text_dark']};
}
.scheme_self.author_info a:hover {
	color: {$colors['text_link']};
}
.scheme_self.author_info .socials_wrap .social_item .social_icon {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.scheme_self.author_info .socials_wrap .social_item:hover .social_icon {
	color: {$colors['inverse_hover']};
	background-color: {$colors['text_hover']};
}
.author_bio p{
    color: {$colors['inverse_text']}
}
.comments_list_wrap .comment_posted {
     color: {$colors['text_light']}
}
/* Related posts */
.related_wrap {
	border-color: {$colors['bd_color']};
}
.related_wrap .related_item_style_1 .post_header {
	background-color: {$colors['bg_color_07']};
}
.related_wrap .related_item_style_1:hover .post_header {
	background-color: {$colors['bg_color']};
}
.related_wrap .related_item_style_1 .post_date a {
	color: {$colors['text']};
}
.related_wrap .related_item_style_1:hover .post_date a {
	color: {$colors['text_light']};
}
.related_wrap .related_item_style_1:hover .post_date a:hover {
	color: {$colors['text_dark']};
}

/* Comments */
.comments_list_wrap,
.comments_list_wrap > ul {
	border-color: {$colors['bd_color']};
}
.comments_list_wrap li + li,
.comments_list_wrap li ul {
	border-color: {$colors['bd_color']};
}
.comments_list_wrap .comment_info {
	color: {$colors['text_dark']};
}
.comments_list_wrap .comment_counters a {
	color: {$colors['text_link']};
}
.comments_list_wrap .comment_counters a:before {
	color: {$colors['text_link']};
}
.comments_list_wrap .comment_counters a:hover:before,
.comments_list_wrap .comment_counters a:hover {
	color: {$colors['text_hover']};
}
.comments_list_wrap .comment_text {
	color: {$colors['text_dark']};
}
.comments_list_wrap .comment_reply a {
	color: {$colors['text_dark']};
}
.comments_list_wrap .comment_reply a:hover {
	color: {$colors['text_link']};
}
.comments_form_wrap {
	border-color: {$colors['bd_color']};
}
.comments_wrap .comments_notes {
	color: {$colors['text_light']};
}


/* Page 404 */
.post_item_404 .page_title {
	color: {$colors['text_light']};
}
.post_item_404 .page_description {
	color: {$colors['text_link']};
}
.post_item_404 .go_home {
	border-color: {$colors['text_dark']};
}
.post_item_404 .go_home:hover {
	color: {$colors['inverse_text']}!important;
	background-color: {$colors['text_hover']}!important;
}

/* Sidebar */
.scheme_self.sidebar .sidebar_inner {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
}
.sidebar_inner .widget + .widget,
 .sidebar[class*="scheme_"] .widget+.widget{

}

.scheme_self.sidebar .widget+.widget:before,
.scheme_self.sidebar[class*="scheme_"] .widget+.widget:before{
 background: {$colors['alter_bg_color']}; /* Old browsers */
    background: -moz-linear-gradient(left, {$colors['alter_bg_color']} 0%, {$colors['alter_bd_color']} 50%, {$colors['alter_bg_color']} 100%); /* FF3.6+ */

    background: -webkit-linear-gradient(left, {$colors['alter_bg_color']} 0%,{$colors['alter_bd_color']} 50%,{$colors['alter_bg_color']} 100%); /* Chrome10+,Safari5.1+ */

    background: -ms-linear-gradient(left, {$colors['alter_bg_color']} 0%,{$colors['alter_bd_color']} 50%,{$colors['alter_bg_color']} 100%); /* IE10+ */
    background: linear-gradient(to right, {$colors['alter_bg_color']} 0%,{$colors['alter_bd_color']} 50%,{$colors['alter_bg_color']} 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$colors['alter_bg_color']}', endColorstr='{$colors['alter_bg_color']}',GradientType=0 ); /* IE6-9 */
}




.scheme_self.sidebar h1, .scheme_self.sidebar h2, .scheme_self.sidebar h3, .scheme_self.sidebar h4, .scheme_self.sidebar h5, .scheme_self.sidebar h6,
.scheme_self.sidebar h1 a, .scheme_self.sidebar h2 a, .scheme_self.sidebar h3 a, .scheme_self.sidebar h4 a, .scheme_self.sidebar h5 a, .scheme_self.sidebar h6 a {
	color: {$colors['alter_dark']};
}
.scheme_self.sidebar h1 a:hover, .scheme_self.sidebar h2 a:hover, .scheme_self.sidebar h3 a:hover, .scheme_self.sidebar h4 a:hover, .scheme_self.sidebar h5 a:hover, .scheme_self.sidebar h6 a:hover {
	color: {$colors['alter_link']};
}


/* Widgets */
.widget ul > li:before {
	background-color: {$colors['text_link']};
}
.scheme_self.sidebar ul > li:before {
	background-color: {$colors['alter_link']};
}
.scheme_self.sidebar a {
	color: {$colors['alter_link']};
}
.scheme_self.sidebar a:hover {
	color: {$colors['alter_hover']};
}
.scheme_self.sidebar li > a,
.scheme_self.sidebar .post_title > a {
	color: {$colors['alter_link']};
}
.scheme_self.sidebar li > a:hover,
.scheme_self.sidebar .post_title > a:hover {
	color: {$colors['text_link']};
}
.scheme_self.sidebar .textwidget p{
	color: {$colors['alter_link']};
}
.scheme_self.footer_default .select_container select:focus,
.scheme_self.sidebar .select_container select:focus{
	color: {$colors['alter_link']};
}



/* Archive */
.scheme_self.sidebar .widget_archive li {
	color: {$colors['alter_dark']};
}

/* Calendar */
.wp-block-calendar caption,
.wp-block-calendar tbody td a,
.wp-block-calendar th,
.widget_calendar caption,
.widget_calendar tbody td a,
.widget_calendar th {
	color: {$colors['text_dark']};
}
.scheme_self.sidebar .widget_calendar caption,
.scheme_self.sidebar .widget_calendar th {
	color: {$colors['alter_dark']};
}
.scheme_self.sidebar .widget_calendar tbody td a{
    color: {$colors['alter_link']};
}
.wp-block-calendar tbody td,
.widget_calendar tbody td {
	color: {$colors['text']} !important;
}
.scheme_self.sidebar .widget_calendar tbody td {
	color: {$colors['alter_link']} !important;
}
.wp-block-calendar tbody td a:hover,
.widget_calendar tbody td a:hover {
	color: {$colors['text_link']};
}
.scheme_self.sidebar .widget_calendar tbody td a:hover {
	color: {$colors['alter_link']};
}
.wp-block-calendar tbody td a:after,
.widget_calendar tbody td a:after {
	background-color: {$colors['text_link']};
}
.scheme_self.sidebar .widget_calendar tbody td a:after {
	background-color: {$colors['text_link']};
}
.wp-block-calendar td#today,
.widget_calendar td#today {
	color: {$colors['inverse_text']} !important;
}
.wp-block-calendar td#today a,
.widget_calendar td#today a {
	color: {$colors['inverse_link']};
}
.wp-block-calendar td#today a:hover,
.widget_calendar td#today a:hover {
	color: {$colors['text_hover']};
}
.wp-block-calendar td#today:before,
.widget_calendar td#today:before {
	background-color: {$colors['text_link']};
}

.wp-block-calendar td#today a:after,
.widget_calendar td#today a:after {
	background-color: {$colors['inverse_link']};
}
.wp-block-calendar td#today a:hover:after,
.widget_calendar td#today a:hover:after {
	background-color: {$colors['inverse_hover']};
}
.wp-calendar-nav .wp-calendar-nav-prev a,
.wp-calendar-nav .wp-calendar-nav-next a,
.widget_calendar #prev a,
.widget_calendar #next a {
	color: {$colors['text_link']};
}

.wp-calendar-nav .wp-calendar-nav-prev a:hover,
.wp-calendar-nav .wp-calendar-nav-next a:hover,
.widget_calendar #prev a:hover,
.widget_calendar #next a:hover {
	color: {$colors['text_hover']};
}

.wp-calendar-nav .wp-calendar-nav-prev a::before,
.wp-calendar-nav .wp-calendar-nav-next a::before,
.widget_calendar td#prev a:before,
.widget_calendar td#next a:before {
	background-color: {$colors['bg_color']};
}
.scheme_self.sidebar .wp-calendar-nav .wp-calendar-nav-prev a::before,
.scheme_self.sidebar .wp-calendar-nav .wp-calendar-nav-next a::before,
.scheme_self.sidebar .widget_calendar td#prev a:before,
.scheme_self.sidebar .widget_calendar td#next a:before {
	background-color: {$colors['alter_bg_color']};
}

/* Categories */
.widget_categories li {
	color: {$colors['text_dark']};
}
.scheme_self.sidebar .widget_categories li {
	color: {$colors['alter_dark']};
}

/* Tag cloud */
.widget_product_tag_cloud a,
.widget_tag_cloud a,
.wp-block-tag-cloud a {
	color: {$colors['text_dark']};
	background-color: {$colors['bd_color']};
}
.scheme_self.sidebar .widget_product_tag_cloud a,
.scheme_self.sidebar .widget_tag_cloud a {
	color: {$colors['text_light']};
	background-color: {$colors['inverse_bd_hover']};
}
.wp-block-tag-cloud a:hover,
.widget_product_tag_cloud a:hover,
.widget_tag_cloud a:hover {
	color: {$colors['inverse_text']} !important;
	background-color: {$colors['text_link']};
}
.scheme_self.sidebar .widget_product_tag_cloud a:hover,
.scheme_self.sidebar .widget_tag_cloud a:hover {
	background-color: {$colors['text_link']};
}

/* RSS */
.widget_rss .widget_title a:first-child {
	color: {$colors['text_link']};
}
.scheme_self.sidebar .widget_rss .widget_title a:first-child {
	color: {$colors['alter_link']};
}
.widget_rss .widget_title a:first-child:hover {
	color: {$colors['text_hover']};
}
.scheme_self.sidebar .widget_rss .widget_title a:first-child:hover {
	color: {$colors['alter_hover']};
}
.widget_rss .rss-date {
	color: {$colors['text_light']};
}
.scheme_self.sidebar .widget_rss .rss-date {
	color: {$colors['alter_light']};
}
.widget_rss .rssSummary, .widget_rss cite{
	color: {$colors['alter_link']};
}





/* Footer */
.scheme_self.footer_wrap,
.footer_wrap .scheme_self.vc_row {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
}
.scheme_self.footer_wrap .widget,
.scheme_self.footer_wrap .sc_content .wpb_column,
.footer_wrap .scheme_self.vc_row .widget,
.footer_wrap .scheme_self.vc_row .sc_content .wpb_column {
	border-color: {$colors['alter_bd_color']};
}
.scheme_self.footer_wrap h1, .scheme_self.footer_wrap h2, .scheme_self.footer_wrap h3,
.scheme_self.footer_wrap h4, .scheme_self.footer_wrap h5, .scheme_self.footer_wrap h6,
.scheme_self.footer_wrap h1 a, .scheme_self.footer_wrap h2 a, .scheme_self.footer_wrap h3 a,
.scheme_self.footer_wrap h4 a, .scheme_self.footer_wrap h5 a, .scheme_self.footer_wrap h6 a,
.footer_wrap .scheme_self.vc_row h1, .footer_wrap .scheme_self.vc_row h2, .footer_wrap .scheme_self.vc_row h3,
.footer_wrap .scheme_self.vc_row h4, .footer_wrap .scheme_self.vc_row h5, .footer_wrap .scheme_self.vc_row h6,
.footer_wrap .scheme_self.vc_row h1 a, .footer_wrap .scheme_self.vc_row h2 a, .footer_wrap .scheme_self.vc_row h3 a,
.footer_wrap .scheme_self.vc_row h4 a, .footer_wrap .scheme_self.vc_row h5 a, .footer_wrap .scheme_self.vc_row h6 a {
	color: {$colors['alter_dark']};
}
.scheme_self.footer_wrap h1 a:hover, .scheme_self.footer_wrap h2 a:hover, .scheme_self.footer_wrap h3 a:hover,
.scheme_self.footer_wrap h4 a:hover, .scheme_self.footer_wrap h5 a:hover, .scheme_self.footer_wrap h6 a:hover,
.footer_wrap .scheme_self.vc_row h1 a:hover, .footer_wrap .scheme_self.vc_row h2 a:hover, .footer_wrap .scheme_self.vc_row h3 a:hover,
.footer_wrap .scheme_self.vc_row h4 a:hover, .footer_wrap .scheme_self.vc_row h5 a:hover, .footer_wrap .scheme_self.vc_row h6 a:hover {
	color: {$colors['alter_link']};
}
.scheme_self.footer_wrap .widget li:before,
.footer_wrap .scheme_self.vc_row .widget li:before {
	background-color: {$colors['alter_link']};
}
.scheme_self.footer_wrap li a,
.footer_wrap .scheme_self.vc_row li a {
	color: {$colors['alter_link']};
}
.scheme_self.footer_wrap li a:hover,
.footer_wrap .scheme_self.vc_row li a:hover {
	color: {$colors['alter_hover']};
}


.footer_logo_inner {
	border-color: {$colors['alter_bd_color']};
}
.footer_logo_inner:after {
	background-color: {$colors['alter_text']};
}
.footer_socials_inner .social_item .social_icon {
	color: {$colors['alter_text']};
}
.footer_socials_inner .social_item:hover .social_icon {
	color: {$colors['alter_dark']};
}
.menu_footer_nav_area ul li a {
	color: {$colors['alter_dark']};
}
.menu_footer_nav_area ul li a:hover {
	color: {$colors['alter_link']};
}
.menu_footer_nav_area ul li+li:before {
	border-color: {$colors['alter_light']};
}

.footer_copyright_inner {
	background-color: {$colors['bg_color']};
	border-color: {$colors['bd_color']};
	color: {$colors['text_dark']};
}
.footer_copyright_inner a {
	color: {$colors['text_dark']};
}
.footer_copyright_inner a:hover {
	color: {$colors['text_link']};
}
.footer_copyright_inner .copyright_text {
	color: {$colors['text']};
}


/* Third-party plugins */

.mfp-bg {
	background-color: {$colors['bg_color_07']};
}
.mfp-image-holder .mfp-close,
.mfp-iframe-holder .mfp-close,
.mfp-close-btn-in .mfp-close {
	color: {$colors['text_dark']};
	background-color: transparent;
}
.mfp-image-holder .mfp-close:hover,
.mfp-iframe-holder .mfp-close:hover,
.mfp-close-btn-in .mfp-close:hover {
	color: {$colors['text_link']};
}



.wp-block-cover p:not(.has-text-color) > strong,
.wp-block-cover p:not(.has-text-color) > a,
.wp-block-cover p:not(.has-text-color){
	color: {$colors['inverse_text']};
}
.wp-block-cover p:not(.has-text-color) > a:hover{
	color: {$colors['extra_hover2']};
}


CSS;
				
					$rez = apply_filters('academee_filter_get_css', $rez, $colors, false, $scheme);
					$css['colors'] .= $rez['colors'];
				}
			}
		}
				
		$css_str = (!empty($css['fonts']) ? $css['fonts'] : '')
				. (!empty($css['colors']) ? $css['colors'] : '');
		return apply_filters( 'academee_filter_prepare_css', $css_str, $remove_spaces );
	}
}
?>