<?php

/**
 * Module to check the modules that are needed
 *
 * @link       https://wordpress.org/plugins/fg-drupal-to-wp/
 * @since      1.2.0
 *
 * @package    FG_Drupal_to_WordPress
 * @subpackage FG_Drupal_to_WordPress/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Modules_Check', false) ) {

	/**
	 * Class to check the modules that are needed
	 *
	 * @package    FG_Drupal_to_WordPress
	 * @subpackage FG_Drupal_to_WordPress/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Modules_Check {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Check if some modules are needed
		 *
		 */
		public function check_modules() {
			$premium_url = 'https://www.fredericgilles.net/fg-drupal-to-wordpress/';
			$message_premium = __('Your Drupal database contains %s. You need the <a href="%s" target="_blank">Premium version</a> to import them.', 'fg-drupal-to-wp');
			if ( defined('FGD2WPP_LOADED') ) {
				// Message for the Premium version
				$message_addon = __('Your Drupal database contains %1$s. You need the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-drupal-to-wp');
			} else {
				// Message for the free version
				$message_addon = __('Your Drupal database contains %1$s. You need the <a href="%2$s" target="_blank">Premium version</a> and the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-drupal-to-wp');
			}
			$modules = array(
				// Check if we need the Premium version: check the number of users
				array(array($this, 'count'),
					array('users', 1),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('users', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of comments (Drupal 6)
				array(array($this, 'count'),
					array('comments', 2),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('comments', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of comments (Drupal 7 & 8)
				array(array($this, 'count'),
					array('comment', 2),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('comments', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of custom nodes
				array(array($this, 'count_custom_nodes'),
					array('node', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('custom nodes', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of custom taxonomies (Drupal 6)
				array(array($this, 'count_custom_taxonomies'),
					array('term_data', 'vocabulary', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('custom taxonomies', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of custom taxonomies (Drupal 7)
				array(array($this, 'count_custom_taxonomies'),
					array('taxonomy_term_data', 'taxonomy_vocabulary', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('custom taxonomies', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of custom taxonomies (Drupal 8)
				array(array($this, 'count_drupal8_custom_taxonomies'),
					array(0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('custom taxonomies', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of URL alias
				array(array($this, 'count'),
					array('url_alias', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('URL alias', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of navigation menus (Drupal 6 & 7)
				array(array($this, 'count_menus'),
					array('taxonomy_term_data', 'taxonomy_vocabulary', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('navigation menus', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of navigation menus (Drupal 8)
				array(array($this, 'count'),
					array('menu_link_content_data', 0),
					'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
					sprintf($message_premium, __('navigation menus', 'fg-drupal-to-wp'), $premium_url)
				),
				
				// Check if we need the CCK add-on (Drupal 6)
				array(array($this, 'count'),
					array('content_node_field', 0),
					'fg-drupal-to-wp-premium-cck-module/fg-drupal-to-wp-cck.php',
					sprintf($message_addon, __('CCK data', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'cck/', __('CCK add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the CCK add-on (Drupal 5)
				array(array($this, 'count'),
					array('node_field', 0),
					'fg-drupal-to-wp-premium-cck-module/fg-drupal-to-wp-cck.php',
					sprintf($message_addon, __('CCK data', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'cck/', __('CCK add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Location add-on
				array(array($this, 'count'),
					array('location', 0),
					'fg-drupal-to-wp-premium-location-module/fg-drupal-to-wp-location.php',
					sprintf($message_addon, __('Location custom fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'location/', __('Location add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Metatag add-on (Metatag module)
				array(array($this, 'count'),
					array('metatag', 0),
					'fg-drupal-to-wp-premium-metatag-module/fg-drupal-to-wp-metatag.php',
					sprintf($message_addon, __('meta tags', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'metatag/', __('Metatag add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Metatag add-on (Nodewords module)
				array(array($this, 'count'),
					array('nodewords', 0),
					'fg-drupal-to-wp-premium-metatag-module/fg-drupal-to-wp-metatag.php',
					sprintf($message_addon, __('nodewords', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'metatag/', __('Metatag add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Metatag add-on (Page Title module)
				array(array($this, 'count'),
					array('page_title', 0),
					'fg-drupal-to-wp-premium-metatag-module/fg-drupal-to-wp-metatag.php',
					sprintf($message_addon, __('page titles', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'metatag/', __('Metatag add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Metatag add-on (Drupal 8)
				array(array($this, 'count'),
					array('node__field_meta_tags', 0),
					'fg-drupal-to-wp-premium-metatag-module/fg-drupal-to-wp-metatag.php',
					sprintf($message_addon, __('page titles', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'metatag/', __('Metatag add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Name add-on
				array(array($this, 'check_drupal7_module'),
					array('name'),
					'fg-drupal-to-wp-premium-name-module/fg-drupal-to-wp-name.php',
					sprintf($message_addon, __('Name custom fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'name/', __('Name add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Address add-on
				array(array($this, 'check_drupal7_module'),
					array('addressfield'),
					'fg-drupal-to-wp-premium-address-module/fg-drupal-to-wp-address.php',
					sprintf($message_addon, __('Addressfield custom fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'address/', __('Address add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Ubercart add-on
				array(array($this, 'count'),
					array('uc_products', 0),
					'fg-drupal-to-wp-premium-ubercart-module/fg-drupal-to-wp-ubercart.php',
					sprintf($message_addon, __('Ubercart products', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'ubercart/', __('Ubercart add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Internationalization add-on
				array(array($this, 'count_enabled'),
					array('languages', 1),
					'fg-drupal-to-wp-premium-internationalization-module/fgd2wp-internationalization.php',
					sprintf($message_addon, __('translations', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'internationalization/', __('Internationalization add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the NodeBlock add-on
				array(array($this, 'count'),
					array('nodeblock', 0),
					'fg-drupal-to-wp-premium-nodeblock-module/fg-drupal-to-wp-nodeblock.php',
					sprintf($message_addon, __('node blocks', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'nodeblock/', __('NodeBlock add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Entity Reference add-on (Drupal 7)
				array(array($this, 'check_drupal7_type'),
					array('entityreference'),
					'fg-drupal-to-wp-premium-entityreference-module/fg-drupal-to-wp-entityreference.php',
					sprintf($message_addon, __('Entity Reference relationships', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'entityreference/', __('Entity Reference add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Entity Reference add-on (Drupal 8)
				array(array($this, 'check_drupal8_entity_reference_fields'),
					array(),
					'fg-drupal-to-wp-premium-entityreference-module/fg-drupal-to-wp-entityreference.php',
					sprintf($message_addon, __('Entity Reference relationships', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'entityreference/', __('Entity Reference add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Media Provider add-on (S3 URLs)
				array(array($this, 'check_file_managed_starting_by'),
					array('s3'),
					'fg-drupal-to-wp-premium-mediaprovider-module/fg-drupal-to-wp-mediaprovider.php',
					sprintf($message_addon, __('S3 URLs', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'mediaprovider/', __('Media Provider add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Media Provider add-on (SoundCloud URLs)
				array(array($this, 'check_file_managed_starting_by'),
					array('soundcloud'),
					'fg-drupal-to-wp-premium-mediaprovider-module/fg-drupal-to-wp-mediaprovider.php',
					sprintf($message_addon, __('SoundCloud media fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'mediaprovider/', __('Media Provider add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Media Provider add-on (YouTube URLs)
				array(array($this, 'check_file_managed_starting_by'),
					array('youtube'),
					'fg-drupal-to-wp-premium-mediaprovider-module/fg-drupal-to-wp-mediaprovider.php',
					sprintf($message_addon, __('YouTube media fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'mediaprovider/', __('Media Provider add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Media Provider add-on (Vimeo URLs)
				array(array($this, 'check_file_managed_starting_by'),
					array('vimeo'),
					'fg-drupal-to-wp-premium-mediaprovider-module/fg-drupal-to-wp-mediaprovider.php',
					sprintf($message_addon, __('Vimeo media fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'mediaprovider/', __('Media Provider add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Forum add-on
				array(array($this, 'count'),
					array('forum', 0),
					'fg-drupal-to-wp-premium-forum-module/fg-drupal-to-wp-forum.php',
					sprintf($message_addon, __('forums', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'forum/', __('Forum add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Field Collection add-on
				array(array($this, 'count_drupal7_items_of_field_type'),
					array('field_collection_item'),
					'fg-drupal-to-wp-premium-fieldcollection-module/fg-drupal-to-wp-fieldcollection.php',
					sprintf($message_addon, __('field collections', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'fieldcollection/', __('Field Collection add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Paragraphs add-on (Drupal 7)
				array(array($this, 'count_drupal7_items_of_field_type'),
					array('paragraphs_item'),
					'fg-drupal-to-wp-premium-paragraphs-module/fg-drupal-to-wp-paragraphs.php',
					sprintf($message_addon, __('paragraphs', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'paragraphs/', __('Paragraphs add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Paragraphs add-on (Drupal 8)
				array(array($this, 'check_drupal8_paragraphs_fields'),
					array(),
					'fg-drupal-to-wp-premium-paragraphs-module/fg-drupal-to-wp-paragraphs.php',
					sprintf($message_addon, __('paragraphs', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'paragraphs/', __('Paragraphs add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Commerce add-on
				array(array($this, 'count'),
					array('commerce_product', 0),
					'fg-drupal-to-wp-premium-commerce-module/fg-drupal-to-wp-commerce.php',
					sprintf($message_addon, __('Commerce products', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'commerce/', __('Commerce add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Countries add-on
				array(array($this, 'count'),
					array('countries_country', 0),
					'fg-drupal-to-wp-premium-countries-module/fg-drupal-to-wp-countries.php',
					sprintf($message_addon, __('countries', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'countries/', __('Countries add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Profile2 add-on
				array(array($this, 'count'),
					array('profile', 0),
					'fg-drupal-to-wp-premium-profile2-module/fg-drupal-to-wp-profile2.php',
					sprintf($message_addon, __('Profile2 user fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'profile2/', __('Profile2 add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Geodata add-on (Geofield module)
				array(array($this, 'check_drupal7_module'),
					array('geofield'),
					'fg-drupal-to-wp-premium-geodata-module/fg-drupal-to-wp-geodata.php',
					sprintf($message_addon, __('Geofield custom fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'geodata/', __('Geodata add-on', 'fg-drupal-to-wp'))
				),
				
				// Check if we need the Geodata add-on (Geolocation Field module)
				array(array($this, 'check_drupal7_module'),
					array('geolocation'),
					'fg-drupal-to-wp-premium-geodata-module/fg-drupal-to-wp-geodata.php',
					sprintf($message_addon, __('Geolocation custom fields', 'fg-drupal-to-wp'), $premium_url, $premium_url . 'geodata/', __('Geodata add-on', 'fg-drupal-to-wp'))
				),
				
			);
			foreach ( $modules as $module ) {
				list($callback, $params, $plugin, $message) = $module;
				if ( !is_plugin_active($plugin) ) {
					if ( call_user_func_array($callback, $params) ) {
						$this->plugin->display_admin_warning($message);
					}
				}
			}
		}

		/**
		 * Count the number of rows in the table
		 *
		 * @param string $table Table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count($table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "SELECT COUNT(*) AS nb FROM ${prefix}${table}";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of enabled rows in the table
		 *
		 * @since 1.46.0
		 * 
		 * @param string $table Table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_enabled($table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "SELECT COUNT(*) AS nb FROM ${prefix}${table} WHERE enabled = 1";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of custom nodes
		 *
		 * @since 1.3.0
		 * 
		 * @param string $table Table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_custom_nodes($table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}${table}
				WHERE type NOT IN('article', 'page')
			";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of custom taxonomies
		 *
		 * @since 1.3.0
		 * 
		 * @param string $term_table Terms table
		 * @param string $vocabulary_table Vocabulary table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_custom_taxonomies($term_table, $vocabulary_table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}${term_table} t
				INNER JOIN ${prefix}${vocabulary_table} tv ON tv.vid = t.vid
				WHERE tv.name NOT IN('categories', 'tags')
			";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of Drupal 8 custom taxonomies
		 *
		 * @since 1.3.0
		 * 
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_drupal8_custom_taxonomies($min_value) {
			if ( !$this->plugin->table_exists('taxonomy_term_field_data') ) {
				return 0;
			}
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}taxonomy_term_field_data
				WHERE vid NOT IN('categories', 'tags')
			";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Check if a Drupal 7 module is installed
		 *
		 * @since 1.14.0
		 * 
		 * @param string $module Module
		 * @return bool Trigger the warning or not
		 */
		private function check_drupal7_module($module) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}field_config
				WHERE module = '$module'
			";
			return ($this->count_sql($sql) > 0);
		}

		/**
		 * Check if a Drupal 7 specific type of data is used
		 *
		 * @since 1.37.0
		 * 
		 * @param string $type Node type
		 * @return bool Trigger the warning or not
		 */
		private function check_drupal7_type($type) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}field_config
				WHERE type = '$type'
			";
			return ($this->count_sql($sql) > 0);
		}

		/**
		 * Check if a Drupal 8 contains Entity Reference node fields
		 *
		 * @since 1.42.0
		 * 
		 * @return bool Trigger the warning or not
		 */
		private function check_drupal8_entity_reference_fields() {
			$fields = $this->plugin->get_drupal_config_like('field.field.node.%.field_%');
			foreach ( $fields as $data ) {
				if ( isset($data['field_type']) && isset($data['entity_type']) && ($data['field_type'] == 'entity_reference') && ($data['entity_type'] == 'node') ) {
					if ( !(isset($data['settings']['handler']) && ($data['settings']['handler'] == 'default:taxonomy_term')) ) { // exclude the taxonomy references
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Check if a Drupal 8 contains Paragraphs node fields
		 *
		 * @since 1.75.0
		 * 
		 * @return bool Trigger the warning or not
		 */
		private function check_drupal8_paragraphs_fields() {
			$fields = $this->plugin->get_drupal_config_like('field.field.node.%.field_%');
			foreach ( $fields as $data ) {
				if ( isset($data['settings']['handler']) && ($data['settings']['handler'] == 'default:paragraph') ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Check if the database contains some files started by a given protocol (s3, soundcloud, youtube, vimeo)
		 * 
		 * @since 1.39.0
		 * 
		 * @param string $protocol Drupal protocol (s3, soundcloud, youtube, vimeo)
		 * @return bool True if some files are found with the protocol
		 */
		private function check_file_managed_starting_by($protocol) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb FROM ${prefix}file_managed
				WHERE uri LIKE '$protocol://%'
			";
			return ($this->count_sql($sql) > 0);
		}
		
		/**
		 * Check if the database contains some items of a specific field type
		 * 
		 * @since 1.75.0
		 * 
		 * @param string $field_type Field type
		 * @return bool True if the database contains this field type
		 */
		private function count_drupal7_items_of_field_type($field_type) {
			$result = false;
			if ( $this->plugin->table_exists('field_config') ) { // Drupal 7
				$prefix = $this->plugin->plugin_options['prefix'];
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}field_config_instance fci
					WHERE fci.entity_type = '$field_type'
				";
				$result = ($this->count_sql($sql) > 0);
			}
			return $result;
		}
		
		/**
		 * Check if the database contains navigation menus (Drupal 6 & 7)
		 * 
		 * @since 1.66.0
		 * 
		 * @return bool True if the database contains at least one menu
		 */
		private function count_menus() {
			$result = false;
			if ( $this->plugin->table_exists('menu_links') ) {
				$prefix = $this->plugin->plugin_options['prefix'];
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}menu_links m
					WHERE m.module = 'menu'
					AND (m.router_path IN('node/%', 'taxonomy/term/%')
						OR m.external = 1)
					AND m.hidden = 0
				";
				$result = ($this->count_sql($sql) > 0);
			}
			return $result;
		}
		
		/**
		 * Execute the SQL request and return the nb value
		 *
		 * @param string $sql SQL request
		 * @return int Count
		 */
		private function count_sql($sql) {
			$count = 0;
			$result = $this->plugin->drupal_query($sql, false);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}

	}
}
