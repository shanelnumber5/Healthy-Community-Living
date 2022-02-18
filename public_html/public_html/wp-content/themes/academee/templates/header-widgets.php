<?php
/**
 * The template to display the widgets area in the header
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Header sidebar
$academee_header_name = academee_get_theme_option('header_widgets');
$academee_header_present = !academee_is_off($academee_header_name) && is_active_sidebar($academee_header_name);
if ($academee_header_present) { 
	academee_storage_set('current_sidebar', 'header');
	$academee_header_wide = academee_get_theme_option('header_wide');
	ob_start();
	if ( is_active_sidebar($academee_header_name) ) {
		dynamic_sidebar($academee_header_name);
	}
	$academee_widgets_output = ob_get_contents();
	ob_end_clean();
	if (!empty($academee_widgets_output)) {
		$academee_widgets_output = preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $academee_widgets_output);
		$academee_need_columns = strpos($academee_widgets_output, 'columns_wrap')===false;
		if ($academee_need_columns) {
			$academee_columns = max(0, (int) academee_get_theme_option('header_columns'));
			if ($academee_columns == 0) $academee_columns = min(6, max(1, substr_count($academee_widgets_output, '<aside ')));
			if ($academee_columns > 1)
				$academee_widgets_output = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($academee_columns).' widget ', $academee_widgets_output);
			else
				$academee_need_columns = false;
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo !empty($academee_header_wide) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<div class="header_widgets_inner widget_area_inner">
				<?php 
				if (!$academee_header_wide) { 
					?><div class="content_wrap"><?php
				}
				if ($academee_need_columns) {
					?><div class="columns_wrap"><?php
				}
				do_action( 'academee_action_before_sidebar' );
				academee_show_layout($academee_widgets_output);
				do_action( 'academee_action_after_sidebar' );
				if ($academee_need_columns) {
					?></div>	<!-- /.columns_wrap --><?php
				}
				if (!$academee_header_wide) {
					?></div>	<!-- /.content_wrap --><?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
?>