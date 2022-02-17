<?php

/**
 * Drupal 6 User Profile module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.71.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_User_Profile', false) ) {

	/**
	 * User Profile class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_User_Profile {

		private $plugin;
		private $profile_fields = array();
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Initialize the profile fields
		 * 
		 */
		public function init_profile_fields() {
			$this->profile_fields = array();
			$profile_fields = $this->get_profile_fields();
			foreach ( $profile_fields as $field ) {
				// First name field
				if ( preg_match('/first_?name/', $field['name']) ) {
					$this->profile_fields['first_name'] = $field['fid'];
				}
				
				// Last name field
				if ( preg_match('/last_?name/', $field['name']) ) {
					$this->profile_fields['last_name'] = $field['fid'];
				}
				
				// Web site field
				if ( preg_match('/web$/', $field['name']) ) {
					$this->profile_fields['website'] = $field['fid'];
				}
			}
		}
		
		/**
		 * Get the profile fields (Drupal 6)
		 * 
		 * @return array Profile fields
		 */
		private function get_profile_fields() {
			$profile_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( $this->plugin->table_exists('profile_fields') ) {
				$sql = "
					SELECT pf.fid, pf.name
					FROM ${prefix}profile_fields pf
				";
				$profile_fields = $this->plugin->drupal_query($sql);
			}
			
			return $profile_fields;
		}
		
		/**
		 * Get the user's first name
		 * 
		 * @param string $first_name First name
		 * @param int $drupal_user_id Drupal user ID
		 * @return string First name
		 */
		public function get_user_first_name($first_name, $drupal_user_id) {
			if ( isset($this->profile_fields['first_name']) ) {
				$result = $this->get_profile_value($this->profile_fields['first_name'], $drupal_user_id);
				if ( !empty($result) ) {
					$first_name = $result;
				}
			}
			return $first_name;
		}

		/**
		 * Get the user's last name
		 * 
		 * @param string $last_name Last name
		 * @param int $drupal_user_id Drupal user ID
		 * @return string Last name
		 */
		public function get_user_last_name($last_name, $drupal_user_id) {
			if ( isset($this->profile_fields['last_name']) ) {
				$result = $this->get_profile_value($this->profile_fields['last_name'], $drupal_user_id);
				if ( !empty($result) ) {
					$last_name = $result;
				}
			}
			return $last_name;
		}

		/**
		 * Get the user's web site
		 * 
		 * @param string $website Web site
		 * @param int $drupal_user_id Drupal user ID
		 * @return string Web site
		 */
		public function get_user_website($website, $drupal_user_id) {
			if ( isset($this->profile_fields['website']) ) {
				$result = $this->get_profile_value($this->profile_fields['website'], $drupal_user_id);
				if ( !empty($result) ) {
					$website = $result;
				}
			}
			return $website;
		}
		
		/**
		 * Get a profile value (Drupal 6)
		 * 
		 * @param int $fid Profile field ID
		 * @param int $uid User ID
		 * @return string Profile value
		 */
		private function get_profile_value($fid, $uid) {
			$value = '';
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT pv.value
				FROM ${prefix}profile_values pv
				WHERE pv.fid = '$fid'
				AND pv.uid = '$uid'
				LIMIT 1
			";
			$results = $this->plugin->drupal_query($sql);
			if ( count($results) > 0 ) {
				$value = $results[0]['value'];
			}
			return $value;
		}
		
		/**
		 * Get the user fields values (Drupal 6)
		 * 
		 * @since 1.77.0
		 * 
		 * @param array $user_fields_values User fields values
		 * @param array $user User data
		 * @return array User fields values
		 */
		public function get_user_fields_values($user_fields_values, $user) {
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Version 6
				$profile_values = $this->get_profile_values($user['uid']);
				foreach ( $profile_values as $profile_value ) {
					$field = array(
						'label' => $profile_value['title'],
						'type' => $profile_value['type'],
						'required' => $profile_value['required'],
					);
					if ( !empty($profile_value['options']) ) {
						$options = explode("\n", $profile_value['options']);
						foreach ( $options as $option ) {
							$field['options'][$option] = $option;
						}
					}
					$user_fields_values[$profile_value['name']] = array(
						'field' => $field,
						'values' => array($profile_value['value']),
					);
				}
			}
			return $user_fields_values;
		}
		
		/**
		 * Get profile values for a user (Drupal 6)
		 * 
		 * @since 1.77.0
		 * 
		 * @param int $uid User ID
		 * @return array Profile values
		 */
		private function get_profile_values($uid) {
			$values = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( $this->plugin->table_exists('profile_values') && $this->plugin->table_exists('profile_fields') ) {
				$sql = "
					SELECT pv.value, pf.title, pf.name, pf.type, pf.required, pf.options
					FROM ${prefix}profile_values pv
					INNER JOIN ${prefix}profile_fields pf ON pf.fid = pv.fid
					WHERE pv.uid = '$uid'
				";
				$values = $this->plugin->drupal_query($sql);
			}
			return $values;
		}
		
	}
}
