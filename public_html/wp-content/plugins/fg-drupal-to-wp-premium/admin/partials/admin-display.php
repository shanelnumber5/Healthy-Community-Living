<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin/partials
 */
?>
<div id="fgd2wp_admin_page" class="wrap">
	<h2><?php print $data['title'] ?></h2>
	
	<p><?php print $data['description'] ?></p>
	
	<?php require('database-info.php'); ?>
	
	<?php require('tabs.php'); ?>
	<?php switch ( $data['tab'] ): ?>
<?php case 'help': ?>
	<?php require('help-instructions.tpl.php'); ?>
	<?php require('help-options.tpl.php'); ?>
	<?php break; ?>

<?php case 'debuginfo': ?>
	<?php require('debug-info.php'); ?>
	<?php break; ?>

<?php default: ?>
	<?php require('empty-content.php'); ?>
	
	<form id="form_import" method="post">

		<?php wp_nonce_field( 'parameters_form', 'fgd2wp_nonce' ); ?>

		<table class="form-table">
			<?php require('settings.php'); ?>
			<?php do_action('fgd2wp_post_display_settings_options'); ?>
			<?php require('database-settings.php'); ?>
			<?php require('behavior.php'); ?>
			<?php require('premium-features.php'); ?>
			
			<?php do_action('fgd2wp_post_display_behavior_options'); ?>
			<?php require('actions.php'); ?>
			<?php require('progress-bar.php'); ?>
			<?php require('logger.php'); ?>
		</table>
	</form>
	
	<?php require('after-migration.php'); ?>
	
	<?php require('paypal-donate.php'); ?>
	<?php endswitch; ?>
	
</div>
