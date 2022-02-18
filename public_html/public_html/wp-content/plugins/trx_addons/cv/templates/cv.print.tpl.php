<?php
/**
 * CV Card Templates: Print version of the CV Resume
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}
add_filter('show_admin_bar', '__return_false');
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="//gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<script>(function(html){html.className = html.className.replace(/no-js/,'js')})(document.documentElement);</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class('trx_addons_cv_prn'); ?>>

	<?php 
	do_action( 'before' ); 
	$trx_addons_cv_prn_photo = trx_addons_clear_thumb_size(trx_addons_get_option('contacts_photo'));
	$trx_addons_cv_prn_name = trx_addons_get_option('contacts_name');
	$trx_addons_cv_prn_position = trx_addons_get_option('contacts_position');
	$trx_addons_cv_prn_phone = trx_addons_get_option('contacts_phone');
	$trx_addons_cv_prn_email = trx_addons_get_option('contacts_email');
	$trx_addons_cv_prn_address = trx_addons_get_option('contacts_address');
	$trx_addons_cv_prn_description = trx_addons_get_option('contacts_description');
	?>

	<div class="trx_addons_cv_prn_body_wrap">

		<div class="trx_addons_cv_prn_section trx_addons_cv_prn_header">

			<div class="trx_addons_cv_prn_header_left">
				<?php if ($trx_addons_cv_prn_photo) { ?>
					<div class="trx_addons_cv_prn_photo"><img src="<?php echo esc_url($trx_addons_cv_prn_photo); ?>" <?php trx_addons_getimagesize($trx_addons_cv_prn_photo, true); ?> alt=""></div>
				<?php } ?>
				<?php if ($trx_addons_cv_prn_name) { ?>
					<div class="trx_addons_cv_prn_title">
						<h1 class="trx_addons_cv_prn_name"><?php echo esc_html($trx_addons_cv_prn_name); ?></h1>
						<p class="trx_addons_cv_prn_position"><?php echo esc_html($trx_addons_cv_prn_position); ?></p>
					</div>
				<?php } ?>
			</div>

			<div class="trx_addons_cv_prn_header_right">
				<?php if ($trx_addons_cv_prn_name) { ?>
					<div class="trx_addons_cv_prn_info_row trx_addons_cv_prn_info_name">
						<span class="trx_addons_cv_prn_info_label"><?php esc_html_e('Name:', 'trx_addons'); ?></span>
						<span class="trx_addons_cv_prn_info_data"><?php echo esc_html($trx_addons_cv_prn_name); ?></span>
					</div>
				<?php } ?>
				<?php if ($trx_addons_cv_prn_address) { ?>
					<div class="trx_addons_cv_prn_info_row trx_addons_cv_prn_info_address">
						<span class="trx_addons_cv_prn_info_label"><?php esc_html_e('Address:', 'trx_addons'); ?></span>
						<span class="trx_addons_cv_prn_info_data"><?php echo esc_html($trx_addons_cv_prn_address); ?></span>
					</div>
				<?php } ?>
				<?php if ($trx_addons_cv_prn_phone) { ?>
					<div class="trx_addons_cv_prn_info_row trx_addons_cv_prn_info_phone">
						<span class="trx_addons_cv_prn_info_label"><?php esc_html_e('Phone:', 'trx_addons'); ?></span>
						<span class="trx_addons_cv_prn_info_data"><?php echo esc_html($trx_addons_cv_prn_phone); ?></span>
					</div>
				<?php } ?>
				<?php if ($trx_addons_cv_prn_email) { ?>
					<div class="trx_addons_cv_prn_info_row trx_addons_cv_prn_info_email">
						<span class="trx_addons_cv_prn_info_label"><?php esc_html_e('E-mail:', 'trx_addons'); ?></span>
						<span class="trx_addons_cv_prn_info_data"><?php echo antispambot($trx_addons_cv_prn_email); ?></span>
					</div>
				<?php } ?>
			</div>

			<?php if ($trx_addons_cv_prn_description) { ?>
				<div class="trx_addons_cv_prn_header_description"><?php echo esc_html($trx_addons_cv_prn_description); ?></div>
			<?php } ?>

		</div><!-- /.trx_addons_cv_prn_header -->

		<?php
		$trx_addons_cv_prn_resume_parts = trx_addons_get_option('cv_resume_parts');
		$trx_addons_cv_prn_types = $TRX_ADDONS_STORAGE['cpt_resume_types'];
		if (is_array($trx_addons_cv_prn_resume_parts) && count($trx_addons_cv_prn_resume_parts) > 0) {
			foreach ($trx_addons_cv_prn_resume_parts as $trx_addons_cv_prn_type => $trx_addons_cv_prn_type_enable) {
				if ( (int)$trx_addons_cv_prn_type_enable == 0 || empty($trx_addons_cv_prn_types[$trx_addons_cv_prn_type]) ) continue;
				?>
				<div class="trx_addons_cv_prn_section trx_addons_cv_prn_section_<?php echo esc_attr($trx_addons_cv_prn_type); ?>">
					<h2 class="trx_addons_cv_prn_section_title"><?php echo esc_html($trx_addons_cv_prn_types[$trx_addons_cv_prn_type]); ?></h2>
					<?php trx_addons_cv_resume_show_posts($trx_addons_cv_prn_type, -1); ?>
				</div><!-- /.trx_addons_cv_prn_section -->
				<?php
			}
		}
		?>

	</div><!-- /.trx_addons_cv_prn_body_wrap -->

	<script>
		// Open popup 'Print' after 1 sec.
    	if (location.href.indexOf('cv_download') < 0)
			setTimeout(function() { window.print(); }, 1000);
	</script>   

	<?php wp_footer(); ?>
	
</body>
</html>