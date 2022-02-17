<?php
/**
 * The style "default" of the Button
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.3
 */

$args = get_query_var('trx_addons_args_sc_button');

$args['css'] .= (!empty($args['bg_image']) ? 'background-image:url(' . esc_url($args['bg_image']) . ');' : '');

if (!trx_addons_is_off($args['align'])) {
?><div class="sc_item_button sc_button_wrap<?php if (!trx_addons_is_off($args['align'])) echo ' sc_align_'.esc_attr($args['align']); ?>"><?php
}
	?><a href="<?php echo esc_url($args['link']); ?>"<?php
		if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"';
		?> class="sc_button sc_button_<?php
				echo esc_attr($args['type']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
				if (!empty($args['size'])) echo ' sc_button_size_'.esc_attr($args['size']);
				if (!empty($args['bg_image'])) echo ' sc_button_bg_image';
				if (!empty($args['icon_position'])) echo ' sc_button_icon_'.esc_attr($args['icon_position']);
				?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		?>><?php
	
	// Icon or Image
	if (!empty($args['image']) || !empty($args['icon'])) {
		?><span class="sc_button_icon"><?php
			if (!empty($args['image'])) {
				$attr = trx_addons_getimagesize($args['image']);
				?><img class="sc_icon_as_image" src="<?php echo esc_url($args['image']); ?>" alt=""<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			} else if (trx_addons_is_url($args['icon'])) {
				$attr = trx_addons_getimagesize($args['icon']);
				?><img class="sc_icon_as_image" src="<?php echo esc_url($args['icon']); ?>" alt=""<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			} else {
				?><span class="<?php echo esc_attr($args['icon']); ?>"></span><?php
			}
		?></span><?php
	}
	if (!empty($args['title']) || !empty($args['subtitle'])) {
		?><span class="sc_button_text<?php if (!trx_addons_is_off($args['text_align'])) echo ' sc_align_'.esc_attr($args['text_align']); ?>"><?php
			if (!empty($args['subtitle'])) {
				?><span class="sc_button_subtitle"><?php echo esc_html($args['subtitle']); ?></span><?php
			}
			if (!empty($args['title'])) {
				?><span class="sc_button_title"><?php echo esc_html($args['title']); ?></span><?php
			}
		?></span><!-- /.sc_button_text --><?php
	}
?></a><!-- /.sc_button --><?php
if (!trx_addons_is_off($args['align'])) {
?></div><!-- /.sc_item_button --><?php
}