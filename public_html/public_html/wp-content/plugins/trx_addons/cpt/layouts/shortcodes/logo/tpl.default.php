<?php
/**
 * The style "default" of the Logo
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_logo');

?><a href="<?php echo is_front_page() ? '#' : esc_url(home_url('/')); ?>"<?php
		if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"';
		?> class="sc_layouts_logo sc_layouts_logo_<?php
				echo esc_attr($args['type']);
				if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
				if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
				?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	?>><?php
	if (!empty($args['logo'])) {
		$args['logo'] = !empty($args['logo_retina']) && trx_addons_get_retina_multiplier(2) > 1
						? $args['logo_retina']
						: $args['logo'];
		$logo = trx_addons_get_attachment_url($args['logo'], 'full');
		$attr = trx_addons_getimagesize($logo);
		?><img class="logo_image" src="<?php echo esc_url($logo); ?>" alt="" <?php if (!empty($attr[3])) trx_addons_show_layout($attr[3]);?>><?php
	} else {
		if (empty($args['logo_text'])) $args['logo_text'] = get_bloginfo( 'name' );
		if (empty($args['logo_slogan'])) $args['logo_slogan'] = get_bloginfo( 'description', 'display' );
		trx_addons_show_layout(trx_addons_prepare_macros($args['logo_text']), '<span class="logo_text">', '</span>');
		trx_addons_show_layout(trx_addons_prepare_macros($args['logo_slogan']), '<span class="logo_slogan">', '</span>');
	}
?></a><!-- /.sc_layouts_logo --><?php

trx_addons_sc_layouts_showed('logo', true);
?>