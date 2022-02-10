<?php
/**
 * The template for displaying theme top bar.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<?php do_action( 'sinatra_before_topbar' ); ?>
<div id="sinatra-topbar" <?php sinatra_top_bar_classes(); ?>>
	<div class="si-container">
		<div class="si-flex-row">
			<div class="col-md flex-basis-auto start-sm"><?php do_action( 'sinatra_topbar_widgets', 'left' ); ?></div>
			<div class="col-md flex-basis-auto end-sm"><?php do_action( 'sinatra_topbar_widgets', 'right' ); ?></div>
		</div>
	</div>
</div><!-- END #sinatra-topbar -->
<?php do_action( 'sinatra_after_topbar' ); ?>
