<?php
/**
 * The template to display default site header
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
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

?><header class="top_panel top_panel_default<?php
					echo !empty($academee_header_image) || !empty($academee_header_video) ? ' with_bg_image' : ' without_bg_image';
					if ($academee_header_video!='') echo ' with_bg_video';
					if ($academee_header_image!='') echo ' '.esc_attr(academee_add_inline_css_class('background-image: url('.esc_url($academee_header_image).');'));
					if (is_single() && has_post_thumbnail()) echo ' with_featured_image';
					if (academee_is_on(academee_get_theme_option('header_fullheight'))) echo ' header_fullheight trx-stretch-height';
					?> scheme_<?php echo esc_attr(academee_is_inherit(academee_get_theme_option('header_scheme')) 
													? academee_get_theme_option('color_scheme') 
													: academee_get_theme_option('header_scheme'));
					?>"><?php

	// Background video
	if (!empty($academee_header_video)) {
		get_template_part( 'templates/header-video' );
	}
	
	// Main menu
	if (academee_get_theme_option("menu_style") == 'top') {
		get_template_part( 'templates/header-navi' );
	}

	// Page title and breadcrumbs area
	get_template_part( 'templates/header-title');

	// Header widgets area
	get_template_part( 'templates/header-widgets' );


?></header>