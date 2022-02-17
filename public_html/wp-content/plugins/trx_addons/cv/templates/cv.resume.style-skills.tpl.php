<?php
/**
 * CV Card Templates: Single Skills item
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

if (trx_addons_get_value_gp('cv_prn')==1 ) {		// Print version

	?>
	<div class="trx_addons_cv_prn_resume_item">
		<div class="trx_addons_cv_prn_resume_item_skills">
			<h5 class="trx_addons_cv_prn_resume_item_title"><?php the_title(); ?></h5>
			<div class="trx_addons_cv_prn_resume_item_skill">
				<div class="trx_addons_sc_skills_total sc_skills_total" style="width: <?php echo esc_html($trx_addons_cv_meta['skill']); ?>%;"><span><?php echo esc_html($trx_addons_cv_meta['skill']); ?>%</span></div>
			</div>
			<div class="trx_addons_cv_prn_resume_item_text"><?php 
				if (trx_addons_is_on(trx_addons_get_option('cv_resume_print_full')))
					the_content();
				else
					the_excerpt(); 
			?></div>
		</div>
	</div>
	<?php

} else {											// Screen version
	
	if ($trx_addons_cv_args['slider']) {
		?><div class="swiper-slide"><?php
	} else if ($trx_addons_cv_args['columns'] > 1) {
		?><div class="trx_addons_cv_resume_column <?php echo esc_attr(trx_addons_get_column_class(1, $trx_addons_cv_args['columns'])); ?>"><?php
	}
	?>
	<div class="trx_addons_cv_resume_item trx_addons_cv_resume_item_hover_flash"><!-- or trx_addons_cv_resume_item_hover_shift -->
		<div class="trx_addons_cv_resume_item_skills trx_addons_sc_skills sc_skills sc_skills_counter" data-type="counter">
			<div class="trx_addons_cv_resume_item_skill trx_addons_sc_skills_item sc_skills_item">
				<div class="trx_addons_sc_skills_total sc_skills_total" 
					data-ed="" 
					data-start="0" 
					data-stop="<?php echo esc_attr($trx_addons_cv_meta['skill']); ?>"
					data-max="100" 
					data-step="<?php echo esc_attr(max(1, round(mt_rand(1,3)))); ?>"
					data-speed="<?php echo esc_attr(max(1, round(mt_rand(10,50)))); ?>"
					>0</div>
			</div>
			<h5 class="trx_addons_cv_resume_item_title"><?php the_title(); ?></h5>
		</div>
		<a class="trx_addons_cv_resume_item_link" href="<?php echo esc_url(get_permalink()); ?>"></a>
	</div>
	<?php
	if ($trx_addons_cv_args['slider'] || $trx_addons_cv_args['columns'] > 1) {
		?></div><?php
	}
}
?>