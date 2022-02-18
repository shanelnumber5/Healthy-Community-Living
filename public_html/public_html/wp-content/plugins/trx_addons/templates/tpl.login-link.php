<?php
/**
 * The template to display login link
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0.1
 */

// Display link
$args = get_query_var('trx_addons_args_login');

// If user not logged in
if ( !is_user_logged_in() ) {
	?><a href="#trx_addons_login_popup" class="trx_addons_popup_link trx_addons_login_link "><?php
		?><span class="sc_layouts_item_icon sc_layouts_login_icon trx_addons_icon-user-alt"></span><?php
		?><span class="sc_layouts_item_details sc_layouts_login_details"><?php
			$rows = explode('|', $args['login_text']);
			if (!empty($rows[0])) {
				?><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1"><?php echo esc_html($rows[0]); ?></span><?php
			}
			if (!empty($rows[1])) {
				?><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2"><?php echo esc_html($rows[1]); ?></span><?php
			}
		?></span><?php
	?></a><?php

// Else if user logged in
} else {
	?><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="trx_addons_login_link"><?php
		?><span class="sc_layouts_item_icon sc_layouts_login_icon trx_addons_icon-user-times"></span><?php
		?><span class="sc_layouts_item_details sc_layouts_login_details"><?php
			$current_user = wp_get_current_user();
			$rows = explode('|', str_replace('%s',
											$current_user->user_firstname,	// user_login or user_firstname or user_lastname
											$args['logout_text'])
							);
			if (!empty($rows[0])) {
				?><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1"><?php echo esc_html($rows[0]); ?></span><?php
			}
			if (!empty($rows[1])) {
				?><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2"><?php echo esc_html($rows[1]); ?></span><?php
			}
		?></span><?php
	?></a><?php 
}
?>