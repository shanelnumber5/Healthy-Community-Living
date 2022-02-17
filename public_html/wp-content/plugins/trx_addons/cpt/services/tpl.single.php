<?php
/**
 * The template to display the service's single page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4
 */

global $TRX_ADDONS_STORAGE;

get_header();

while ( have_posts() ) { the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'services_page itemscope' ); ?>
		itemscope itemtype="http://schema.org/Article">

    <?php
    if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ||  !trx_addons_sc_layouts_showed('title')  ) {?>

		<section class="services_page_header">

			<?php
			// Image
			if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
				?><div class="services_page_featured"><?php
					the_post_thumbnail( trx_addons_get_thumb_size('huge'), array(
								'alt' => get_the_title(),
								'itemprop' => 'image'
								)
							);
				?></div><?php
			}
			
			// Title
			if ( !trx_addons_sc_layouts_showed('title') ) {
				?><h2 class="services_page_title"><?php the_title(); ?></h2><?php
			}
			?>

		</section>

		<?php
			}
    ?>

		<?php

		// Post content
		?><section class="services_page_content entry-content" itemprop="articleBody"><?php
			the_content( );
		?></section><!-- .entry-content --><?php

	?></article><?php

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
?>