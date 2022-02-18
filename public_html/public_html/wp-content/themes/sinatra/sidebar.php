<?php
/**
 * The template for displaying theme sidebar.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

if ( ! sinatra_is_sidebar_displayed() ) {
	return;
}

$sinatra_sidebar = sinatra_get_sidebar();
?>

<aside id="secondary" class="widget-area si-sidebar-container"<?php sinatra_schema_markup( 'sidebar' ); ?> role="complementary">

	<div class="si-sidebar-inner">
		<?php do_action( 'sinatra_before_sidebar' ); ?>

		<?php
		if ( is_active_sidebar( $sinatra_sidebar ) ) {

			dynamic_sidebar( $sinatra_sidebar );

		} elseif ( current_user_can( 'edit_theme_options' ) ) {

			$sinatra_sidebar_name = sinatra_get_sidebar_name_by_id( $sinatra_sidebar );
			?>
			<div class="si-sidebar-widget si-widget sinatra-no-widget">

				<div class='h4 widget-title'><?php echo esc_html( $sinatra_sidebar_name ); ?></div>

				<p class='no-widget-text'>
					<?php if ( is_customize_preview() ) { ?>
						<a href='#' class="sinatra-set-widget" data-sidebar-id="<?php echo esc_attr( $sinatra_sidebar ); ?>">
					<?php } else { ?>
						<a href='<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>'>
					<?php } ?>
						<?php esc_html_e( 'Click here to assign a widget.', 'sinatra' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
		?>

		<?php do_action( 'sinatra_after_sidebar' ); ?>
	</div>

</aside><!--#secondary .widget-area -->

<?php
