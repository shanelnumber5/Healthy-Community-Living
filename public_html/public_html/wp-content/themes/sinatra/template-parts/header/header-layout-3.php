<?php
/**
 * The template for displaying header layout 3.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<div class="si-header-container">
	<div class="si-logo-container">
		<div class="si-container">

			<?php
			do_action( 'sinatra_header_widget_location', 'left' );
			sinatra_header_logo_template();
			do_action( 'sinatra_header_widget_location', 'right' );
			?>

			<span class="si-header-element si-mobile-nav">
				<?php sinatra_hamburger( sinatra_option( 'main_nav_mobile_label' ), 'sinatra-primary-nav' ); ?>
			</span>

		</div><!-- END .si-container -->
	</div><!-- END .si-logo-container -->

	<div class="si-nav-container">
		<div class="si-container">

			<?php sinatra_main_navigation_template(); ?>

		</div><!-- END .si-container -->
	</div><!-- END .si-nav-container -->
</div><!-- END .si-header-container -->
