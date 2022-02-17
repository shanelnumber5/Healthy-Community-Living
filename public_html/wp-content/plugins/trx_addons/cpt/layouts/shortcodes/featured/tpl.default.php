<?php
/**
 * The style "default" of the Featured image
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_layouts_featured');

$need_content = !empty($args['content']);
$need_image = is_singular() && in_array(get_post_type(), array('post', 'page')) && has_post_thumbnail();

if ( $need_content || $need_image )  {
	if ($need_image) {
		$trx_addons_attachment_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
		if (!empty($trx_addons_attachment_src[0]))
			$args['css'] = 'background-image:url('.esc_url($trx_addons_attachment_src[0]).');' . $args['css'];
		else
			$need_image = false;
	}
	if ( $need_content || $need_image )  {
		if (!empty($args['height']))
			$args['css'] = trx_addons_get_css_dimensions_from_values(array('min-height' => $args['height'])) . ';' . $args['css'];
		?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_featured<?php
				if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
				if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
				if ($need_content) echo ' with_content';
				if ($need_image) echo ' with_image';
				if (!empty($args['align']) && !trx_addons_is_inherit($args['align'])) echo ' sc_align_'.esc_attr($args['align']); 
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
			if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"'; ?>><?php
			
			if ($need_content) trx_addons_show_layout($args['content'], '<div class="sc_layouts_featured_content">', '</div>');

		?></div><!-- /.sc_layouts_featured --><?php

		trx_addons_sc_layouts_showed('featured', $need_image);
	}
}
?>