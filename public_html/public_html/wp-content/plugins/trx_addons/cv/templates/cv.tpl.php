<?php
/**
 * CV Card Templates: Main template for the CV homepage
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}
$trx_addons_show_splash = trx_addons_is_on(trx_addons_get_option('cv_use_splash')) 
							&& trx_addons_is_off(trx_addons_get_option('cv_hide_blog')) 
							&& is_front_page() 
							&& !isset($_COOKIE['trx_addons_cv_splash'])
							&& (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], home_url())===false) 
							&& empty($_SERVER['QUERY_STRING']);
$trx_addons_classes = 'trx_addons_cv' . ($trx_addons_show_splash ? ' trx_addons_cv_splash' : '');
if ($trx_addons_show_splash) 
	setcookie('trx_addons_cv_splash', 1, 0, '/');
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="//gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class($trx_addons_classes); ?>>

	<?php 
	do_action( 'before' ); 
	$trx_addons_cv_header_image = trx_addons_clear_thumb_size(trx_addons_get_option('cv_header_image'));
	$trx_addons_cv_header_image_style = trx_addons_get_option('cv_header_image_style');
	$trx_addons_cv_header_letter = trx_addons_get_option('cv_header_letter');
	$trx_addons_cv_header_text = trx_addons_get_option('cv_header_text');
	if (empty($trx_addons_cv_header_text)) $trx_addons_cv_header_text = get_bloginfo('name');
	$trx_addons_cv_header_socials = trx_addons_get_option('cv_header_socials');
	$trx_addons_cv_header_socials_links = trx_addons_is_on($trx_addons_cv_header_socials) ? trx_addons_get_socials_links() : '';
	?>

	<div class="trx_addons_cv_body_wrap<?php
			if (trx_addons_is_on(trx_addons_get_option('cv_header_narrow'))) {
				echo ' trx_addons_cv_header_narrow';
			}
		?>">

		<div class="trx_addons_cv_header trx_addons_cv_tint_<?php echo esc_attr(trx_addons_get_option('cv_header_tint'));
			if ($trx_addons_cv_header_image) {
				echo ' trx_addons_cv_header_image_style_' . esc_attr($trx_addons_cv_header_image_style);
			}
		?>"
		<?php
			if ($trx_addons_cv_header_image && in_array($trx_addons_cv_header_image_style, array('cover', 'fit'))) {
				echo ' style="background-image: url(' . esc_url($trx_addons_cv_header_image) . ');"';
			}
		?>>

			<div class="trx_addons_cv_header_data">
				
				<?php if ($trx_addons_cv_header_image && $trx_addons_cv_header_image_style=='boxed') { ?>
					<div class="trx_addons_cv_header_image"><img src="<?php echo esc_url($trx_addons_cv_header_image); ?>" <?php trx_addons_getimagesize($trx_addons_cv_header_image, true); ?> alt=""></div>
				<?php } else { ?>
					<h1 class="trx_addons_cv_header_letter"><?php echo esc_html($trx_addons_cv_header_letter ? $trx_addons_cv_header_letter : '&nbsp;'); ?></h1>
				<?php } ?>
	
				<?php if ($trx_addons_cv_header_text) { ?>
					<h5 class="trx_addons_cv_header_text"><?php echo esc_html($trx_addons_cv_header_text); ?></h5>
				<?php } ?>
	
				<?php if ($trx_addons_cv_header_socials_links) { ?>
					<div class="trx_addons_cv_header_socials"><?php trx_addons_show_layout($trx_addons_cv_header_socials_links); ?></div>
				<?php } ?>

			</div><!-- /.trx_addons_cv_header_data -->

			<?php
			// Show Splash buttons
			if ($trx_addons_show_splash) {
				// Show "Blog" button
				if (($bt = trx_addons_get_option('cv_button_blog2'))=='')
					$bt = trx_addons_get_file_url('cv/images/button_blog2.png');
				if ($bt) {
					$is = trx_addons_getimagesize($bt);
					echo '<a href="'.esc_url(trx_addons_add_to_url(home_url(), array('cv'=>0))).'" class="trx_addons_cv_button2 trx_addons_cv_button_blog2"><img src="'.esc_url($bt).'" '.(!empty($is[3]) ? $is[3] : '').' alt="'.esc_html__('Go to Blog ...', 'trx_addons').'"></a>';
				}
				// Show "CV" button
				if (($bt = trx_addons_get_option('cv_button_cv2'))=='')
					$bt = trx_addons_get_file_url('cv/images/button_cv2.png');
				if ($bt) {
					$is = trx_addons_getimagesize($bt);
					echo '<a href="'.esc_url(trx_addons_get_cv_page_link()).'" class="trx_addons_cv_button2 trx_addons_cv_button_cv2"><img src="'.esc_url($bt).'" '.(!empty($is[3]) ? $is[3] : '').' alt="'.esc_html__('Go to VCard ...', 'trx_addons').'"></a>';
				}
			}
			?>

		</div><!-- /.trx_addons_cv_header -->

		<div class="trx_addons_cv_content<?php if (!is_single()) echo ' trx_addons_accordion trx_addons_cv_navi_' . esc_attr(trx_addons_get_option('cv_navigation')); ?>">
			<?php
			if (is_single()) {
				if ( ($trx_addons_cv_fdir = trx_addons_get_file_dir('cv/templates/cv.single.tpl.php')) != '') { include_once $trx_addons_cv_fdir; }
			} else {
				$trx_addons_cv_parts = trx_addons_get_option('cv_parts');
				if (is_array($trx_addons_cv_parts) && count($trx_addons_cv_parts) > 0) {
					foreach ($trx_addons_cv_parts as $trx_addons_cv_k => $trx_addons_cv_v) {
						if ( (int)$trx_addons_cv_v == 0 ) continue;
						if ( ($trx_addons_cv_fdir = trx_addons_get_file_dir('cv/templates/cv.'.trx_addons_esc($trx_addons_cv_k).'.tpl.php')) != '') { include_once $trx_addons_cv_fdir; }
					}
				}
			}
			?>
		</div><!-- /.trx_addons_cv_content -->

	</div><!-- /.trx_addons_cv_body_wrap -->
	
	<?php wp_footer(); ?>
	
</body>
</html>