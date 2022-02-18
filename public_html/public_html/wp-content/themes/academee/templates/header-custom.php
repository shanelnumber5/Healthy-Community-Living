<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.06
 */

$academee_header_css = $academee_header_image = '';
$academee_header_video = academee_get_header_video();
if (true || empty($academee_header_video)) {
	$academee_header_image = get_header_image();
	if (academee_is_on(academee_get_theme_option('header_image_override')) && apply_filters('academee_filter_allow_override_header_image', true)) {
		if (is_category()) {
			if (($academee_cat_img = academee_get_category_image()) != '')
				$academee_header_image = $academee_cat_img;
		} else if (is_singular() || academee_storage_isset('blog_archive')) {
			if (has_post_thumbnail()) {
				$academee_header_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				if (is_array($academee_header_image)) $academee_header_image = $academee_header_image[0];
			} else
				$academee_header_image = '';
		}
	}
}

$academee_header_id = str_replace('header-custom-', '', academee_get_theme_option("header_style"));
$academee_header_meta = get_post_meta($academee_header_id, 'trx_addons_options', true);

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr($academee_header_id); 
						?> top_panel_custom_<?php echo esc_attr(sanitize_title(get_the_title($academee_header_id)));
						echo !empty($academee_header_image) || !empty($academee_header_video) 
							? ' with_bg_image' 
							: ' without_bg_image';
						if ($academee_header_video!='') 
							echo ' with_bg_video';
						if ($academee_header_image!='') 
							echo ' '.esc_attr(academee_add_inline_css_class('background-image: url('.esc_url($academee_header_image).');'));
						if (!empty($academee_header_meta['margin']) != '') 
							echo ' '.esc_attr(academee_add_inline_css_class('margin-bottom: '.esc_attr(academee_prepare_css_value($academee_header_meta['margin'])).';'));
						if (is_single() && has_post_thumbnail()) 
							echo ' with_featured_image';
						if (academee_is_on(academee_get_theme_option('header_fullheight'))) 
							echo ' header_fullheight trx-stretch-height';
						?> scheme_<?php echo esc_attr(academee_is_inherit(academee_get_theme_option('header_scheme')) 
														? academee_get_theme_option('color_scheme') 
														: academee_get_theme_option('header_scheme'));
						?>"><?php

	// Background video
	if (!empty($academee_header_video)) {
		get_template_part( 'templates/header-video' );
	}
		
	// Custom header's layout
	do_action('academee_action_show_layout', $academee_header_id);

	// Header widgets area
	get_template_part( 'templates/header-widgets' );
		
?></header>