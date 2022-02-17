<?php
/**
 * The style "default" of the Container
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

$args = get_query_var('trx_addons_args_sc_layouts_container');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_container<?php
		if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
		if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
		if (!empty($args['align']) && !trx_addons_is_inherit($args['align'])) echo ' sc_align_'.esc_attr($args['align']); 
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>><?php
	trx_addons_show_layout($args['content']);
?></div><!-- /.sc_layouts_container -->