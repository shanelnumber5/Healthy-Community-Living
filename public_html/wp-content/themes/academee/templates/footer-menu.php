<?php
/**
 * The template to display menu in the footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */

// Footer menu
$academee_menu_footer = academee_get_nav_menu(array(
											'location' => 'menu_footer',
											'class' => 'sc_layouts_menu sc_layouts_menu_default'
											));
if (!empty($academee_menu_footer)) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php academee_show_layout($academee_menu_footer); ?>
		</div>
	</div>
	<?php
}
?>