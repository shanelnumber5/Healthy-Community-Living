<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */

// Footer sidebar
$academee_footer_name = academee_get_theme_option('footer_widgets');
$academee_footer_present = !academee_is_off($academee_footer_name) && is_active_sidebar($academee_footer_name);
if ($academee_footer_present) { 
	academee_storage_set('current_sidebar', 'footer');
	$academee_footer_wide = academee_get_theme_option('footer_wide');
	ob_start();
	if ( is_active_sidebar($academee_footer_name) ) {
		dynamic_sidebar($academee_footer_name);
	}
	$academee_out = trim(ob_get_contents());
	ob_end_clean();
	if (!empty($academee_out)) {
		$academee_out = preg_replace("/<\\/aside>[\r\n\s]*<aside/", "</aside><aside", $academee_out);
		$academee_need_columns = true;	//or check: strpos($academee_out, 'columns_wrap')===false;
		if ($academee_need_columns) {
			$academee_columns = max(0, (int) academee_get_theme_option('footer_columns'));
			if ($academee_columns == 0) $academee_columns = min(4, max(1, substr_count($academee_out, '<aside ')));
			if ($academee_columns > 1)
				$academee_out = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($academee_columns).' widget ', $academee_out);
			else
				$academee_need_columns = false;
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo !empty($academee_footer_wide) ? ' footer_fullwidth' : ''; ?> sc_layouts_row  sc_layouts_row_type_normal">
			<div class="footer_widgets_inner widget_area_inner">
				<?php 
				if (!$academee_footer_wide) { 
					?><div class="content_wrap"><?php
				}
				if ($academee_need_columns) {
					?><div class="columns_wrap"><?php
				}
				do_action( 'academee_action_before_sidebar' );
				academee_show_layout($academee_out);
				do_action( 'academee_action_after_sidebar' );
				if ($academee_need_columns) {
					?></div><!-- /.columns_wrap --><?php
				}
				if (!$academee_footer_wide) {
					?></div><!-- /.content_wrap --><?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
?>