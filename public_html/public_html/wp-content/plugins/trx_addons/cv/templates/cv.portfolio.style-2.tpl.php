<?php
/**
 * CV Card Templates: Single Portfolio Slide - Style 2
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

$trx_addons_cv_args = get_query_var('trx_addons_args_cv_portfolio');

$trx_addons_cv_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

if ($trx_addons_cv_args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($trx_addons_cv_args['columns'] > 1) {
	?><div class="trx_addons_cv_portfolio_column <?php echo esc_attr(trx_addons_get_column_class(1, $trx_addons_cv_args['columns'])); ?>"><?php
}
?>
	<div class="trx_addons_cv_portfolio_item">
		<?php
		if (has_post_thumbnail()) {
			$trx_addons_cv_post_link = !empty($trx_addons_cv_meta['alter_link']) ? $trx_addons_cv_meta['alter_link'] : get_permalink();
			$trx_addons_cv_post_title = get_the_title();
			$trx_addons_cv_large_image_url = trx_addons_get_attachment_url( get_post_thumbnail_id(), trx_addons_get_thumb_size('big') );
			?>
			<div class="trx_addons_cv_portfolio_item_thumb trx_addons_hover trx_addons_hover_style_zoomin">
				<?php the_post_thumbnail( trx_addons_get_thumb_size('avatar'), array('alt' => $trx_addons_cv_post_title) ); ?>
				<div class="trx_addons_hover_mask"></div>
				<div class="trx_addons_hover_content">
					<h6 class="trx_addons_hover_title"><a href="<?php echo esc_url($trx_addons_cv_post_link); ?>"><?php echo esc_html($trx_addons_cv_meta['subtitle'] ? $trx_addons_cv_meta['subtitle'] : $trx_addons_cv_post_title); ?></a></h6>
					<a href="<?php echo esc_url($trx_addons_cv_post_link); ?>" class="trx_addons_hover_icon trx_addons_hover_icon_link"></a>
					<a href="<?php echo esc_url($trx_addons_cv_large_image_url); ?>" class="trx_addons_hover_icon trx_addons_hover_icon_zoom"></a>
				</div>
			</div>
		<?php } ?>
	</div>
<?php
if ($trx_addons_cv_args['slider'] || $trx_addons_cv_args['columns'] > 1) {
	?></div><?php
}
?>