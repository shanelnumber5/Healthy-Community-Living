<?php
/**
 * The template to display the socials in the footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */


// Socials
if ( academee_is_on(academee_get_theme_option('socials_in_footer')) && ($academee_output = academee_get_socials_links()) != '') {
	?>
	<div class="footer_socials_wrap socials_wrap">
		<div class="footer_socials_inner">
			<?php academee_show_layout($academee_output); ?>
		</div>
	</div>
	<?php
}
?>