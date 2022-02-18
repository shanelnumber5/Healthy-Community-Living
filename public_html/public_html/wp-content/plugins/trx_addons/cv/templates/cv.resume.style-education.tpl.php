<?php
/**
 * CV Card Templates: Single Education Item
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
	?><div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_column 
				<?php echo esc_attr(trx_addons_get_column_class(1, $trx_addons_cv_args['columns'])); ?> 
				<?php echo ' trx_addons_cv_resume_row' . esc_attr($trx_addons_cv_args['columns'] < $trx_addons_cv_args['number'] ? '2' : '1'); ?>"><?php
}
?>
	<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item">
		<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_number"><?php echo esc_html($trx_addons_cv_args['number']+($trx_addons_cv_args['page']-1)*$trx_addons_cv_args['count']);?></div>
		<div class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_header">
			<h6 class="trx_addons_cv<?php echo esc_attr($trx_addons_cv_prn); ?>_resume_item_title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h6>
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