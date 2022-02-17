<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

						// Widgets area inside page content
						academee_create_widgets_area('widgets_below_content');
						?>				
					</div><!-- </.content> -->

					<?php
					// Show main sidebar
					get_sidebar();

					// Widgets area below page content
					academee_create_widgets_area('widgets_below_page');

					$academee_body_style = academee_get_theme_option('body_style');
					if ($academee_body_style != 'fullscreen') {
						?></div><!-- </.content_wrap> --><?php
					}
					?>
			</div><!-- </.page_content_wrap> -->

			<?php
			// Footer
			$academee_footer_style = academee_get_theme_option("footer_style");
			if (strpos($academee_footer_style, 'footer-custom-')===0) $academee_footer_style = 'footer-custom';
			get_template_part( "templates/{$academee_footer_style}");
			?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php if (academee_is_on(academee_get_theme_option('debug_mode')) && academee_get_file_dir('images/makeup.jpg')!='') { ?>
		<img src="<?php echo esc_url(academee_get_file_url('images/makeup.jpg')); ?>" id="makeup">
	<?php } ?>

	<?php wp_footer(); ?>

</body>
</html>