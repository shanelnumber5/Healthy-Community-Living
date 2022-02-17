<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( !function_exists( 'academee_vc_get_css' ) ) {
	add_filter( 'academee_filter_get_css', 'academee_vc_get_css', 10, 4 );
	function academee_vc_get_css($css, $colors, $fonts, $scheme='') {
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS
.vc_tta.vc_tta-accordion .vc_tta-panel-title .vc_tta-title-text {
	{$fonts['p_font-family']}
}
.vc_progress_bar.vc_progress_bar_narrow .vc_single_bar .vc_label .vc_label_units, .vc_progress_bar.vc_progress_bar_narrow .vc_single_bar .vc_label, .vc_icon_element.vc_icon_element-outer + .wpb_text_column.wpb_content_element p {
	{$fonts['h5_font-family']}
}

CSS;
		}

		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS

/* Row and columns */
.scheme_self.vc_section,
.scheme_self.wpb_row,
.scheme_self.wpb_column > .vc_column-inner > .wpb_wrapper,
.scheme_self.wpb_text_column {
	color: {$colors['text']};
}
.scheme_self.vc_section[data-vc-full-width="true"],
.scheme_self.wpb_row[data-vc-full-width="true"],
.scheme_self.wpb_text_column {
	background-color: {$colors['bg_color']};
}
.scheme_self.vc_row.vc_parallax[class*="scheme_"] .vc_parallax-inner:before {
	background-color: {$colors['bg_color_08']};
}

/* Accordion */
.vc_tta.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_dark']};
}
.vc_tta.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:before,
.vc_tta.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:after {
	border-color: {$colors['inverse_link']};
}
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-title > a {
	color: {$colors['text_dark']};
}
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-title > a,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-title > a:hover {
	color: {$colors['text_link']};
}
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-title > a .vc_tta-controls-icon,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-title > a:hover .vc_tta-controls-icon {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-title > a .vc_tta-controls-icon:before,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-title > a .vc_tta-controls-icon:after {
	border-color: {$colors['inverse_link']};
}

/* Tabs */
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tabs-list .vc_tta-tab > a {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_dark']};
}
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tabs-list .vc_tta-tab > a:hover,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tabs-list .vc_tta-tab.vc_active > a {
	color: {$colors['text_link']};
	background-color: {$colors['text_dark']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-heading{
    color: {$colors['inverse_link']};
	background-color: {$colors['text_dark']};
	border-color: {$colors['text_dark']};
}
.wpb-js-composer .vc_tta.vc_general .vc_tta-panel-title > a{
    border-color: {$colors['text_dark']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-title > a{
    color: {$colors['inverse_link']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-heading:focus, .wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-heading:hover{
    background-color: {$colors['text_dark']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-heading:focus a, .wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel .vc_tta-panel-heading:hover a{
     color: {$colors['text_link']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-heading{
     background-color: {$colors['text_dark']};
}
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-heading a{
    color: {$colors['text_link']};
}


/* Separator */
.vc_separator.vc_sep_color_grey .vc_sep_line {
	border-color: {$colors['bd_color']};
}

/* Progress bar */
.vc_progress_bar.vc_progress_bar_narrow .vc_single_bar {
	background-color: {$colors['input_bg_color']};
}
.vc_progress_bar.vc_progress_bar_narrow.vc_progress-bar-color-bar_red .vc_single_bar .vc_bar {
	background-color: {$colors['alter_link']};
}
.vc_progress_bar.vc_progress_bar_narrow .vc_single_bar .vc_label {
	color: {$colors['text_dark']};
}
.vc_progress_bar.vc_progress_bar_narrow .vc_single_bar .vc_label .vc_label_units {
	color: {$colors['text_dark']};
}
.vc_progress_bar .vc_single_bar .vc_bar{
background-color: {$colors['text_link']};
}
.vc_progress_bar .vc_single_bar:nth-child(even) .vc_bar{
background-color: {$colors['text_hover']};
}
.wpb-js-composer .vc_tta.vc_tta-spacing-1 .vc_tta-tab:after{
    background-color: {$colors['text_dark']};
}
.vc_icon_element.vc_icon_element-outer + .wpb_text_column.wpb_content_element p{
    color: {$colors['text_dark']};
}

CSS;
		}
		
		return $css;
	}
}
?>