<?php
/**
 * The template to display the site logo in the footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */

// Logo
if (academee_is_on(academee_get_theme_option('logo_in_footer'))) {
	$academee_logo_image = '';
	if (academee_get_retina_multiplier(2) > 1)
		$academee_logo_image = academee_get_theme_option( 'logo_footer_retina' );
	if (empty($academee_logo_image)) 
		$academee_logo_image = academee_get_theme_option( 'logo_footer' );
	$academee_logo_text   = get_bloginfo( 'name' );
	if (!empty($academee_logo_image) || !empty($academee_logo_text)) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if (!empty($academee_logo_image)) {
					$academee_attr = academee_getimagesize($academee_logo_image);
					echo '<a href="'.esc_url(home_url('/')).'"><img src="'.esc_url($academee_logo_image).'" class="logo_footer_image" alt="'.esc_attr(basename($academee_logo_image)).'"'.(!empty($academee_attr[3]) ? sprintf(' %s', $academee_attr[3]) : '').'></a>' ;
				} else if (!empty($academee_logo_text)) {
					echo '<h1 class="logo_footer_text"><a href="'.esc_url(home_url('/')).'">' . esc_html($academee_logo_text) . '</a></h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
?>