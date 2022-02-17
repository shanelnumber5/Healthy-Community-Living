<?php
/**
 * The style "chess" of the Services item
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');

$link = get_permalink();

if ($args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?> "><?php
}
?>
<div class="sc_services_item<?php
	echo !isset($args['hide_excerpt']) || $args['hide_excerpt']==0 ? ' with_content' : ' without_content';
?>"><?php
	trx_addons_get_template_part('templates/tpl.featured.php',
									'trx_addons_args_featured',
									apply_filters('trx_addons_filter_args_featured', array(
												'class' => 'sc_services_item_header',
												'show_no_image' => true,
												'thumb_bg' => true,
												'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-chess')
												),
												'services-chess'
												)
								);
	?>
	<div class="sc_services_item_content">
		<?php
		$title_tag = 'h6';
		if ($args['columns'] == 1) $title_tag = 'h4';
		?>
		<<?php echo esc_attr($title_tag); ?> class="sc_services_item_title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></<?php echo esc_attr($title_tag); ?>>
		<div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div>
		<?php if (!isset($args['hide_excerpt']) || $args['hide_excerpt']==0) { ?>
			<div class="sc_services_item_text"><?php the_excerpt(); ?></div>
		<?php } ?>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
?>