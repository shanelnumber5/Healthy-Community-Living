<?php
/**
 * The style "default" of the Anchor
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_anchor');
?><a id="sc_anchor_<?php echo esc_attr($args['id']); ?>" class="sc_anchor sc_anchor_<?php echo esc_attr($args['type']); ?>" 
		title="<?php echo esc_attr($args['title']); ?>" 
		data-icon="<?php echo esc_attr($args['icon']); ?>" 
		data-url="<?php echo esc_url($args['url']); ?>"
></a>