<?php
/**
 * The template to show mobile menu
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */
?>
<div class="menu_mobile_overlay"></div>
<div class="menu_mobile menu_mobile_<?php echo esc_attr(academee_get_theme_option('menu_mobile_fullscreen') > 0 ? 'fullscreen' : 'narrow'); ?> scheme_dark">
	<div class="menu_mobile_inner">
		<a class="menu_mobile_close icon-cancel"></a><?php

		// Logo
		set_query_var('academee_logo_args', array('type' => 'inverse'));
		get_template_part( 'templates/header-logo' );
		set_query_var('academee_logo_args', array());

		// Mobile menu
		$academee_menu_mobile = academee_get_nav_menu('menu_mobile');
		if (empty($academee_menu_mobile)) {
			$academee_menu_mobile = apply_filters('academee_filter_get_mobile_menu', '');
			if (empty($academee_menu_mobile)) $academee_menu_mobile = academee_get_nav_menu('menu_main');
			if (empty($academee_menu_mobile)) $academee_menu_mobile = academee_get_nav_menu();
		}
		if (!empty($academee_menu_mobile)) {
			if (!empty($academee_menu_mobile))
				$academee_menu_mobile = str_replace(
					array('menu_main', 'id="menu-', 'sc_layouts_menu_nav', 'sc_layouts_hide_on_mobile', 'hide_on_mobile'),
					array('menu_mobile', 'id="menu_mobile-', '', '', ''),
					$academee_menu_mobile
					);
			if (strpos($academee_menu_mobile, '<nav ')===false)
				$academee_menu_mobile = sprintf('<nav class="menu_mobile_nav_area">%s</nav>', $academee_menu_mobile);
			academee_show_layout(apply_filters('academee_filter_menu_mobile_layout', $academee_menu_mobile));
		}


		?>
	</div>
</div>
