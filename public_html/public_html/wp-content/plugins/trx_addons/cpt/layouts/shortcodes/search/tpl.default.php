<?php
/**
 * The style "default" of the Search form
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_search');

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_search<?php
		if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
		if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"'; ?>><?php

	do_action('trx_addons_action_search', $args['style'], 'layouts_search', $args['ajax'] > 0);
	
?></div><!-- /.sc_layouts_search --><?php

trx_addons_sc_layouts_showed('search', true);
?>