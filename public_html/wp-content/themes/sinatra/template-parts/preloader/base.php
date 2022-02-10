<?php
/**
 * The template for displaying page preloader.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<div id="si-preloader"<?php sinatra_preloader_classes(); ?>>
	<?php get_template_part( 'template-parts/preloader/preloader', sinatra_option( 'preloader_style' ) ); ?>
</div><!-- END #si-preloader -->
