<?php
/**
 * CV Card Templates: Single Testimonial Slide
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

$trx_addons_cv_args =	get_query_var('trx_addons_args_cv_testimonials');

$trx_addons_cv_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

if ($trx_addons_cv_args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($trx_addons_cv_args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $trx_addons_cv_args['columns'])); ?>"><?php
}
?>
	<div class="trx_addons_cv_testimonials_item">
		<h6 class="trx_addons_cv_testimonials_item_title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h6>
		<div class="trx_addons_cv_testimonials_item_subtitle"><?php echo esc_html($trx_addons_cv_meta['subtitle']);?></div>
		<?php if (has_post_thumbnail()) { ?>
		<div class="trx_addons_cv_testimonials_item_thumb trx_addons_hover trx_addons_hover_style_info">
			<?php the_post_thumbnail( trx_addons_get_thumb_size('portrait'), array('alt' => get_the_title()) ); ?>
			<div class="trx_addons_hover_mask"></div>
			<div class="trx_addons_hover_content">
				<!-- <h6 class="trx_addons_hover_title"></h6> -->
				<?php if (($trx_addons_cv_excerpt = get_the_excerpt()) != '') { ?>
				<div class="trx_addons_hover_text"><?php echo esc_html($trx_addons_cv_excerpt); ?></div>
				<?php } ?>
				<a href="<?php echo esc_url(get_permalink()); ?>" class="trx_addons_hover_link"><?php esc_html_e('Read More', 'trx_addons'); ?></a>
			</div>
		</div>
		<?php } ?>
	</div>
<?php
if ($trx_addons_cv_args['slider'] || $trx_addons_cv_args['columns'] > 1) {
	?></div><?php
}
?>