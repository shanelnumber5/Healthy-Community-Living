<?php
/**
 * The style "hover" of the Services item
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();
$svg_present = false;
$image = '';
if ( has_post_thumbnail() ) {
	$image = trx_addons_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), trx_addons_get_thumb_size('masonry') );
}
if (empty($args['featured'])) $args['featured'] = 'icon';
if (empty($args['hide_bg_image'])) $args['hide_bg_image'] = 0;

if ($args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?> "><?php
}
?>
<div class="sc_services_item<?php
	echo !isset($args['hide_excerpt']) || $args['hide_excerpt']==0 ? ' with_content' : ' without_content';
	echo !empty($image) ? ' with_image' : '';
	echo $args['featured']=='icon' ? ' with_icon' : '';
	echo $args['featured']=='number' ? ' with_number' : '';
?>">
	<div class="sc_services_item_header <?php echo $args['hide_bg_image']==1 ? ' without_image' : ''; ?>"<?php if ($args['hide_bg_image']==0 && !empty($image)) echo ' style="background-image: url('.esc_url($image).');"'; ?>>
		<div class="sc_services_item_header_inner">
			<?php
			if ($args['featured']=='icon' && !empty($meta['icon'])) {
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
			} else if ($args['featured']=='number') {
				?><span class="sc_services_item_number"><?php
					$number = get_query_var('trx_addons_args_item_number');
					printf("%02d", $number);
				?></span><?php
			}	
			?>
			<h6 class="sc_services_item_title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h6>
			<div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div>
		</div>
		<a class="sc_services_item_link" href="<?php echo esc_url($link); ?>"></a>
	</div>
	<?php if (!isset($args['hide_excerpt']) || $args['hide_excerpt']==0) { ?>
		<div class="sc_services_item_content"<?php if (!empty($image)) echo ' style="background-image: url('.esc_url($image).');"'; ?>>
			<div class="sc_services_item_content_inner">
				<h6 class="sc_services_item_title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h6>
				<div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div>
				<div class="sc_services_item_text"><?php the_excerpt(); ?></div>
			</div>
			<a class="sc_services_item_link" href="<?php echo esc_url($link); ?>"></a>
		</div>
	<?php } ?>
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