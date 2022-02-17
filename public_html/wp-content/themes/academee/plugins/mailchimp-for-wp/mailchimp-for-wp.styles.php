<?php
// Add plugin-specific colors and fonts to the custom CSS
if (!function_exists('academee_mailchimp_get_css')) {
	add_filter('academee_filter_get_css', 'academee_mailchimp_get_css', 10, 4);
	function academee_mailchimp_get_css($css, $colors, $fonts, $scheme='') {
		
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS

CSS;
		
			
			$rad = academee_get_border_radius();
			$css['fonts'] .= <<<CSS

CSS;
		}

		
		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS

.mc4wp-form input[type="email"] {
	background-color: {$colors['alter_dark']};
	border-color: {$colors['alter_dark']};
	color: {$colors['bg_color']};
}
.mc4wp-form .mc4wp-alert {
	background-color: {$colors['text_link']};
	border-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.mc4wp-form .mc4wp-form-fields input[type="email"]{
   box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
}
.mc4wp-form .mc4wp-form-fields input[type="submit"]{
box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['inverse_text_04']}!important;
}
.mc4wp-form .mc4wp-form-fields input[type="submit"]:hover{
box-shadow: 0px 0px 0 13px {$colors['inverse_dark_03']}!important;
    -webkit-box-shadow: 0px 0px 0 13px {$colors['inverse_dark_03']}!important;
    -moz-box-shadow: 0px 0px 0 13px {$colors['inverse_dark_03']}!important;
    -o-box-shadow: 0px 0px 0 13px {$colors['inverse_dark_03']}!important;
}

CSS;
		}

		return $css;
	}
}
?>