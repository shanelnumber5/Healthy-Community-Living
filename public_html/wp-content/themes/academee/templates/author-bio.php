<?php
/**
 * The template to display the Author bio
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */
?>

<div class="author_info scheme_dark author vcard" itemprop="author" itemscope itemtype="//schema.org/Person">

	<div class="author_avatar" itemprop="image">
		<?php 
		$academee_mult = academee_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 300*$academee_mult );
		?>
	</div><!-- .author_avatar -->

	<div class="author_description">
		<h5 class="author_title" itemprop="name"><?php echo wp_kses_data(sprintf(__('About %s', 'academee'), '<span class="fn">'.get_the_author().'</span>')); ?></h5>

		<div class="author_bio" itemprop="description">
			<?php echo wp_kses(wpautop(get_the_author_meta( 'description' )), 'academee_kses_content'); ?>


			<?php do_action('academee_action_user_meta'); ?>
		</div><!-- .author_bio -->

	</div><!-- .author_description -->

</div><!-- .author_info -->
