<?php
/**
 * The template for displaying theme pre footer bar.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<div id="si-pre-footer">

	<?php
	if ( sinatra_is_pre_footer_cta_displayed() ) {
		get_template_part( 'template-parts/pre-footer/call-to-action' );
	}
	?>

</div><!-- END #si-pre-footer -->
