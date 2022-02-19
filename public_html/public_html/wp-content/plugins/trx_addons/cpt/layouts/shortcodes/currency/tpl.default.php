<?php
/**
 * The style "default" of the Currency Switcher
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.14
 */

$args = get_query_var('trx_addons_args_sc_layouts_currency');

if (trx_addons_exists_woocommerce() && class_exists('WOOCS')) {
	?><div<?php if (!empty($args['id'])) echo ' id="' . esc_attr($args['id']) . '"'; ?> class="sc_layouts_currency<?php
			if (!empty($args['hide_on_tablet'])) echo ' hide_on_tablet';
			if (!empty($args['hide_on_mobile'])) echo ' hide_on_mobile';
			if (!empty($args['class'])) echo ' ' . esc_attr($args['class']);
			?>"<?php
			if (!empty($args['css'])) echo ' style="' . esc_attr($args['css']) . '"'; ?>>
		<div class="menu_user_currency">
			<?php echo do_shortcode('[woocs show_flags="0"]'); ?>
		</div>
	</div><!-- /.sc_layouts_currency --><?php

	trx_addons_sc_layouts_showed('currency', true);
}
?>