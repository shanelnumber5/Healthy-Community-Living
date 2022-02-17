<?php
/**
 * The style "chess" of the Services item
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');
$link = get_permalink();
?>
<div class="sc_services_item<?php
	echo !isset($args['hide_excerpt']) || $args['hide_excerpt']==0 ? ' with_content' : ' without_content';
	if ($number-1 == $args['offset']) echo ' sc_services_item_active';
?>"><?php
	trx_addons_get_template_part('templates/tpl.featured.php',
									'trx_addons_args_featured',
									apply_filters('trx_addons_filter_args_featured', array(
														'class' => 'sc_services_item_header',
														'show_no_image' => true,
														'thumb_bg' => true,
														'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-tabs')
														),
													'services-tabs'
													)
								);
	?><div class="sc_services_item_content">
		<div class="sc_services_item_content_inner">
			<h3 class="sc_services_item_title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h3>
			<div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div>
			<?php if (!isset($args['hide_excerpt']) || $args['hide_excerpt']==0) { ?>
				<div class="sc_services_item_text"><?php the_excerpt(); ?></div>
				<div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>" class="sc_button"><?php esc_html_e('Learn more', 'trx_addons'); ?></a></div>
			<?php } ?>
		</div>
	</div>
</div>
