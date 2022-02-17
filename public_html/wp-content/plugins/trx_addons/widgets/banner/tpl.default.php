<?php
/**
 * The style "default" of the Widget "Banner"
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_banner');
extract($args);
		
// Before widget (defined by themes)
if ( trx_addons_is_on($fullwidth) ) $before_widget = str_replace('class="widget ', 'class="widget widget_fullwidth ', $before_widget);
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
if ($banner_image!='') {
	$banner_image = trx_addons_get_attachment_url($banner_image, trx_addons_get_thumb_size('masonry'));
	$attr = trx_addons_getimagesize($banner_image);
	echo (!empty($banner_link) ? '<a href="' . esc_url($banner_link) . '"' : '<span') . ' class="image_wrap">'
			. '<img src="' . esc_url($banner_image) . '" alt="' . esc_attr($title) . '"' . (!empty($attr[3]) ? ' '.trim($attr[3]) : '')	. '>'
			. (!empty($banner_link) ? '</a>': '</span>');
}
if ($banner_code!='') {
    trx_addons_show_layout( do_shortcode( $banner_code ) );
}
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>