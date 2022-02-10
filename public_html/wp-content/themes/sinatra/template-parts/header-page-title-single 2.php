<?php
/**
 * Template part for displaying page header for single post.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<div <?php sinatra_page_header_classes(); ?><?php sinatra_page_header_atts(); ?>>

	<?php do_action( 'sinatra_page_header_start' ); ?>

	<?php if ( 'in-page-header' === sinatra_option( 'single_title_position' ) ) { ?>

		<div class="si-container">
			<div class="si-page-header-wrapper">

				<?php
				if ( sinatra_single_post_displays( 'category' ) ) {
					get_template_part( 'template-parts/entry/entry', 'category' );
				}

				if ( sinatra_page_header_has_title() ) {
					echo '<div class="si-page-header-title">';
					sinatra_page_header_title();
					echo '</div>';
				}

				if ( sinatra_has_entry_meta_elements() ) {
					get_template_part( 'template-parts/entry/entry', 'meta' );
				}
				?>

			</div>
		</div>

	<?php } ?>

	<?php do_action( 'sinatra_page_header_end' ); ?>

</div>
