<?php
/**
 * The template for displaying theme copyright bar.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php do_action( 'sinatra_before_copyright' ); ?>
<div id="sinatra-copyright" <?php sinatra_copyright_classes(); ?>>
	<div class="si-container">
		<div class="si-flex-row">

			<div class="col-xs-12 center-xs col-md flex-basis-auto start-md"><?php do_action( 'sinatra_copyright_widgets', 'start' ); ?></div>
			<div class="col-xs-12 center-xs col-md flex-basis-auto end-md"><?php do_action( 'sinatra_copyright_widgets', 'end' ); ?></div>

		</div><!-- END .si-flex-row -->
	</div>
</div><!-- END #sinatra-copyright -->
<?php do_action( 'sinatra_after_copyright' ); ?>
