<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_sidebar_position = academee_get_theme_option('sidebar_position');
if (academee_sidebar_present()) {
	ob_start();
	$academee_sidebar_name = academee_get_theme_option('sidebar_widgets');
	academee_storage_set('current_sidebar', 'sidebar');
	if ( is_active_sidebar($academee_sidebar_name) ) {
		dynamic_sidebar($academee_sidebar_name);
	}
	$academee_out = trim(ob_get_contents());
	ob_end_clean();
	if (!empty($academee_out)) {
		?>
		<div class="sidebar <?php echo esc_attr($academee_sidebar_position); ?> widget_area<?php if (!academee_is_inherit(academee_get_theme_option('sidebar_scheme'))) echo ' scheme_'.esc_attr(academee_get_theme_option('sidebar_scheme')); ?>" role="complementary">
			<div class="sidebar_inner">
				<?php
				do_action( 'academee_action_before_sidebar' );
				academee_show_layout(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $academee_out));
				do_action( 'academee_action_after_sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
		</div><!-- /.sidebar -->
		<?php
	}
}
?>