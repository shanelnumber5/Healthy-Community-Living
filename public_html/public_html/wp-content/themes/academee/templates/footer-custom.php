<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.10
 */

$academee_footer_scheme =  academee_is_inherit(academee_get_theme_option('footer_scheme')) ? academee_get_theme_option('color_scheme') : academee_get_theme_option('footer_scheme');
$academee_footer_id = str_replace('footer-custom-', '', academee_get_theme_option("footer_style"));
$academee_footer_meta = get_post_meta($academee_footer_id, 'trx_addons_options', true);
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr($academee_footer_id); 
						?> footer_custom_<?php echo esc_attr(sanitize_title(get_the_title($academee_footer_id))); 
						if (!empty($academee_footer_meta['margin']) != '') 
							echo ' '.esc_attr(academee_add_inline_css_class('margin-top: '.esc_attr(academee_prepare_css_value($academee_footer_meta['margin'])).';'));
						?> scheme_<?php echo esc_attr($academee_footer_scheme); 
						?>">
	<?php
    // Custom footer's layout
    do_action('academee_action_show_layout', $academee_footer_id);
	?>
</footer><!-- /.footer_wrap -->
