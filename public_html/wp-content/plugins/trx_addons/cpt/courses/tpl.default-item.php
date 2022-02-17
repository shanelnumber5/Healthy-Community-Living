<?php
/**
 * The style "default" of the Courses
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_courses');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

if (!empty($args['slider'])) {
	?><div class="swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?>"><?php
}
?>
<div class="sc_courses_item trx_addons_hover trx_addons_hover_style_links">
	<?php if (has_post_thumbnail()) { ?>
		<div class="sc_courses_item_thumb">
			<?php the_post_thumbnail( trx_addons_get_thumb_size($args['columns'] > 2 ? 'medium' : 'big'), array('alt' => get_the_title()) ); ?>
			<span class="sc_courses_item_categories"><?php trx_addons_show_layout(trx_addons_get_post_terms(' ', get_the_ID(), TRX_ADDONS_CPT_COURSES_TAXONOMY)); ?></span>
		</div>
	<?php } ?>
	<div class="sc_courses_item_info">
		<div class="sc_courses_item_header">
			<h4 class="sc_courses_item_title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h4>
			<div class="sc_courses_item_meta">
				<span class="sc_courses_item_meta_item sc_courses_item_meta_date"><?php
					$dt = $meta['date'];
					echo sprintf($dt < date_i18n('Y-m-d') ? esc_html__('Started on %s', 'trx_addons') : esc_html__('Starting %s', 'trx_addons'), '<span class="sc_courses_item_date">' . date(get_option('date_format'), strtotime($dt)) . '</span>');
				?></span>
				<span class="sc_courses_item_meta_item sc_courses_item_meta_duration"><?php echo esc_html($meta['duration']); ?></span>
            </div>
		</div>
		<div class="sc_courses_item_price"><?php
			$price = explode('/', $meta['price']);
			echo esc_html($price[0]) . (!empty($price[1]) ? '<span class="sc_courses_item_period">'.$price[1].'</span>' : '');
		?></div>
	</div>

</div>
<?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
?>