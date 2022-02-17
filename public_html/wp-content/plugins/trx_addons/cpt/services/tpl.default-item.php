<?php
/**
 * The style "default" of the Services
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4
 */

$args = get_query_var('trx_addons_args_sc_services');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();
$featured_position = !empty($args['featured_position']) ? $args['featured_position'] : 'top';
$svg_present = false;

if ($args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?>"><?php
}
?>
<div class="sc_services_item<?php
	echo isset($args['hide_excerpt']) && $args['hide_excerpt'] ? ' without_content' : ' with_content';
	echo empty($args['featured']) || $args['featured']=='image' 
					? ' with_image' 
					: ($args['featured']=='icon' ? ' with_icon' : ' with_number');
	echo ' sc_services_item_featured_'.esc_attr($featured_position);
?>">
	<?php
	// Featured image or icon
	if ( has_post_thumbnail() && (empty($args['featured']) || $args['featured']=='image')) {
		trx_addons_get_template_part('templates/tpl.featured.php',
										'trx_addons_args_featured',
										apply_filters('trx_addons_filter_args_featured', array(
															'class' => 'sc_services_item_thumb',
															'hover' => 'zoomin',
															'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size($args['columns'] > 2 ? 'medium' : 'big'), 'services-default')
															),
														'services-default'
														)
									);
	} else if ($args['featured']=='icon' && !empty($meta['icon'])) {
		$svg = $img = '';
		if (trx_addons_is_url($meta['icon'])) {
			$img = $meta['icon'];
			$meta['icon'] = basename($meta['icon']);
		} else if (!empty($args['icons_animation']) && $args['icons_animation'] > 0 && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_esc($meta['icon']).'.svg')) != '')
			$svg_present = true;
		?><a href="<?php echo esc_url($link); ?>"
			 id="<?php echo esc_attr($args['id'].'_'.trim($meta['icon'])); ?>"
			 class="sc_services_item_icon <?php
					echo !empty($svg) 
							? 'sc_icon_type_svg'
							: (!empty($img) 
								? 'sc_icon_type_img'
								: esc_attr($meta['icon'])
								);
					?>"
			 ><?php
			if (!empty($svg)) {
				trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
			} else if (!empty($img)) {
				$attr = trx_addons_getimagesize($img);
				?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt=""<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
			}
		?></a><?php
	} else {
		?><span class="sc_services_item_number"><?php
			$number = get_query_var('trx_addons_args_item_number');
			printf("%02d", $number);
		?></span><?php
	}
	?>	
	<div class="sc_services_item_info">
		<div class="sc_services_item_header">
			<h4 class="sc_services_item_title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h4>
			<div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div>
		</div>
		<?php if (!isset($args['hide_excerpt']) || $args['hide_excerpt']==0) { ?>
			<div class="sc_services_item_content"><?php the_excerpt(); ?></div>
			<div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>" class="sc_button sc_button_simple"><?php esc_html_e('Learn more', 'trx_addons'); ?></a></div>
		<?php } ?>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && $svg_present) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url('shortcodes/icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx_addons-sc_icons', trx_addons_get_file_url('shortcodes/icons/icons.js'), array('jquery'), null, true );
}
?>