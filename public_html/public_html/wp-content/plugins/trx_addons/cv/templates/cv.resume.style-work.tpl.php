<?php
/**
 * CV Card Templates: Single Work Experience Item
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

$trx_addons_cv_args = get_query_var('trx_addons_args_cv_resume');
$trx_addons_cv_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$trx_addons_cv_prn  = trx_addons_get_value_gp('cv_prn')==1 ? '_prn' : '';

if ($trx_addons_cv_args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($trx_addons_cv_args['columns'] > 1) {
	?><div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_column <?php echo esc_attr(trx_addons_get_column_class(1, $trx_addons_cv_args['columns'])); ?>"><?php
}
?>
	<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item">
		<?php
		$trx_addons_cv_post_link = get_permalink();
		$trx_addons_cv_post_title = get_the_title();
		if (has_post_thumbnail()) {
			$trx_addons_cv_large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'big' );
			?>
			<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_thumb trx_addons_hover trx_addons_hover_style_zoomin">
				<?php the_post_thumbnail( trx_addons_get_thumb_size('avatar'), array('alt' => $trx_addons_cv_post_title) ); ?>
				<div class="trx_addons_hover_mask"></div>
				<div class="trx_addons_hover_content">
					<h6 class="trx_addons_hover_title"><a href="<?php echo esc_url($trx_addons_cv_post_link); ?>"><?php echo esc_html($trx_addons_cv_meta['subtitle'] ? $trx_addons_cv_meta['subtitle'] : $trx_addons_cv_post_title); ?></a></h6>
					<a href="<?php echo esc_url($trx_addons_cv_post_link); ?>" class="trx_addons_hover_icon trx_addons_hover_icon_link"></a>
					<a href="<?php echo esc_url($trx_addons_cv_large_image_url[0]); ?>" class="trx_addons_hover_icon trx_addons_hover_icon_zoom"></a>
				</div>
			</div>
		<?php } ?>
		<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_meta"><?php echo esc_html($trx_addons_cv_meta['period']);?></div>
		<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_header">
			<h6 class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_title"><a href="<?php echo esc_url($trx_addons_cv_post_link); ?>"><?php the_title(); ?></a></h6>
			<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_subtitle"><?php echo esc_html($trx_addons_cv_meta['subtitle']);?></div>
		</div>
		<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_text"><?php 
			if (trx_addons_get_value_gp('cv_prn')==1 && trx_addons_is_on(trx_addons_get_option('cv_resume_print_full')))
				the_content();
			else
				the_excerpt(); 
		?></div>
	</div>
<?php
if ($trx_addons_cv_args['slider'] || $trx_addons_cv_args['columns'] > 1) {
	?></div><?php
}
?>