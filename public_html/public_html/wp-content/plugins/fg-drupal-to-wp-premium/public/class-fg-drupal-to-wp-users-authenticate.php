<?php

/**
 * Users authentication module
 * Authenticate the WordPress users using the imported Drupal passwords
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.2.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/public
 */

use Drupal\Core\Password\PhpassHashedPassword;

if ( !class_exists('FG_Drupal_to_WordPress_Users_Authenticate', false) ) {

	/**
	 * Users authentication class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Users_Authenticate {

		/**
		 * Authenticate a user using his Drupal password
		 *
		 * @param WP_User $user User data
		 * @param string $username User login entered
		 * @param string $password Password entered
		 * @return WP_User User data
		 */
		public static function auth_signon($user, $username, $password) {
			
			if ( is_a($user, 'WP_User') ) {
				// User is already identified
				return $user;
			}
			
			if ( empty($username) || empty($password) ) {
				return $user;
			}
			
			$wp_user = get_user_by('login', $username); // Try to find the user by his login
			if ( !is_a($wp_user, 'WP_User') ) {
				$wp_user = get_user_by('email', $username); // Try to find the user by his email
				if ( !is_a($wp_user, 'WP_User') ) {
					// username not found in WP users
					return $user;
				}
			}
			
			// Get the imported drupalpass
			$drupalpass = get_user_meta($wp_user->ID, 'drupalpass', true);
			if ( empty($drupalpass) ) {
				return $user;
			}
			
			// Authenticate the user using the Drupal password
			// Drupal 6: MD5
			// Drupal 7 & 8: PhpassHashedPassword
			$passwordHasher = new PhpassHashedPassword(1);
			$stripped_password = stripslashes($password);
			if ( ($drupalpass == md5($stripped_password)) || $passwordHasher->check($stripped_password, $drupalpass) ) {
				// Update WP user password
				add_filter('send_password_change_email', '__return_false'); // Prevent an email to be sent
				wp_update_user(array('ID' => $wp_user->ID, 'user_pass' => $password));
				// To prevent the user to log in again with his Drupal password once he has successfully logged in. The following times, his password stored in WordPress will be used instead.
				delete_user_meta($wp_user->ID, 'drupalpass');
				
				return $wp_user;
			}
			
			return $user;
		}
		
	}
}
