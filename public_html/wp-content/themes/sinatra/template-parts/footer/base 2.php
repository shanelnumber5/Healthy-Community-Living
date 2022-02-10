<?php
/**
 * The template for displaying theme footer.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php do_action( 'sinatra_before_footer' ); ?>
<div id="sinatra-footer" <?php sinatra_footer_classes(); ?>>
	<div class="si-container">
		<div class="si-flex-row" id="sinatra-footer-widgets">

			<?php sinatra_footer_widgets(); ?>

		</div><!-- END .si-flex-row -->
	</div><!-- END .si-container -->
</div><!-- END #sinatra-footer -->
<?php do_action( 'sinatra_after_footer' ); ?>
