<?php
/**
 * The base template for displaying theme header area.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>
<?php do_action( 'sinatra_before_header' ); ?>
<div id="sinatra-header" <?php sinatra_header_classes(); ?>>
	<?php do_action( 'sinatra_header_content' ); ?>
</div><!-- END #sinatra-header -->
<?php do_action( 'sinatra_after_header' ); ?>
