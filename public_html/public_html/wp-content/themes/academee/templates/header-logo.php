<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_args = get_query_var('academee_logo_args');

// Site logo
$academee_logo_image  = academee_get_logo_image(isset($academee_args['type']) ? $academee_args['type'] : '');
$academee_logo_text   = academee_is_on(academee_get_theme_option('logo_text')) ? get_bloginfo( 'name' ) : '';
$academee_logo_slogan = get_bloginfo( 'description', 'display' );
if (!empty($academee_logo_image) || !empty($academee_logo_text)) {
	?><a class="sc_layouts_logo" href="<?php echo is_front_page() ? '#' : esc_url(home_url('/')); ?>"><?php
		if (!empty($academee_logo_image)) {
			$academee_attr = academee_getimagesize($academee_logo_image);
			echo '<img src="'.esc_url($academee_logo_image).'" alt="'. esc_attr(basename($academee_logo_image)).'"'.(!empty($academee_attr[3]) ? sprintf(' %s', $academee_attr[3]) : '').'>' ;
		} else {
			academee_show_layout(academee_prepare_macros($academee_logo_text), '<span class="logo_text">', '</span>');
			academee_show_layout(academee_prepare_macros($academee_logo_slogan), '<span class="logo_slogan">', '</span>');
		}
	?></a><?php
}
?>