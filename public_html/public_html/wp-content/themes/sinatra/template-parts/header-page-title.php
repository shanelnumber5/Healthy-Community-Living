<?php
/**
 * Template part for displaying page header.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<div <?php sinatra_page_header_classes(); ?><?php sinatra_page_header_atts(); ?>>
	<div class="si-container">

	<?php do_action( 'sinatra_page_header_start' ); ?>

	<?php if ( sinatra_page_header_has_title() ) { ?>

		<div class="si-page-header-wrapper">

			<div class="si-page-header-title">
				<?php sinatra_page_header_title(); ?>
			</div>

			<?php $sinatra_description = apply_filters( 'sinatra_page_header_description', sinatra_get_the_description() ); ?>

			<?php if ( $sinatra_description ) { ?>

				<div class="si-page-header-description">
					<?php echo wp_kses( $sinatra_description, sinatra_get_allowed_html_tags() ); ?>
				</div>

			<?php } ?>
		</div>

	<?php } ?>

	<?php do_action( 'sinatra_page_header_end' ); ?>

	</div>
</div>
