<?php

/**
 * Navigation menus module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.66.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Menus', false) ) {

	/**
	 * Navigation menus class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Menus {

		private $menus = array(); // Menus to import
		private $all_menus = array(); // All Drupal menus
		private $menus_count = 0;
		private $plugin;
		private $imported_posts = array(); // Already imported posts
		private $imported_terms = array(); // Already imported terms
		private $imported_menu_items = array(); // Imported menu items (used to set the parent menu items)

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Reset the stored last menu id when emptying the database
		 * 
		 */
		public function reset_last_menu_id() {
			update_option('fgd2wp_last_menu_id', 0);
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_menus']) || !$this->plugin->premium_options['skip_menus'] ) {
				$count += $this->get_menus_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of menus
		 * 
		 * @return int Number of menus
		 */
		private function get_menus_count() {
			$count = 0;
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return 0;
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 6 & 7
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}menu_links m
					WHERE m.module = 'menu'
					AND (m.router_path IN('node/%', 'taxonomy/term/%')
						OR m.external = 1)
					AND m.hidden = 0
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}menu_link_content_data m
					WHERE (m.link__uri LIKE 'entity:node/%'
						OR m.link__uri LIKE 'internal:/term/taxonomy%'
						OR m.external = 1)
					AND m.enabled = 1
				";
			}
			$result = $this->plugin->drupal_query($sql);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}
		
		/**
		 * Import the navigation menus
		 * 
		 */
		public function import_menus() {
			if ( isset($this->plugin->premium_options['skip_menus']) && $this->plugin->premium_options['skip_menus'] ) {
				return;
			}
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$message = __('Importing menus...', $this->plugin->get_plugin_name());
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_menus_count());
			} else {
				$this->plugin->log($message);
			}
			
			// Get all the menus with their alias and parent IDs
			$this->all_menus = $this->get_all_menus();
			
			// Define the lists of imported posts, categories, custom posts and custom taxonomies to make the links between Drupal and WordPress objects
			$this->imported_posts = $this->plugin->get_imported_drupal_posts_with_post_type();
			$this->imported_terms = $this->plugin->get_imported_drupal_taxonomies();
			do_action('fgd2wp_pre_import_menus');
			
			$this->menus = $this->get_menus();
			$all_menus_count = count($this->menus);
			$offset = 0;
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$imported_menus = array();
				$menus = array_slice($this->menus, $offset, $this->plugin->chunks_size, true);
				$menus_count = count($menus);
				foreach ( $menus as $menu ) {
					$new_menu = $this->add_menu($menu);
					if ( !empty($new_menu) ) {
						$imported_menus[$new_menu['drupal_menu_id']] = $new_menu;
					}
					// Increment the Drupal last imported menu ID
					update_option('fgd2wp_last_menu_id', $menu['id']);
					
					if ( defined('WP_CLI') ) {
						$progress_cli->tick();
					}
				}
				
				$this->plugin->progressbar->increment_current_count($menus_count);
				
				do_action('fgd2wp_post_import_menus', $imported_menus);
				
				$offset += $this->plugin->chunks_size;
			} while ( $offset < $all_menus_count );
			
			$this->set_parent_menu_items(); // Set the parent menu items if the parent have been saved after their children
			
					
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			$this->plugin->display_admin_notice(sprintf(_n('%d menu item imported', '%d menu items imported', $this->menus_count, $this->plugin->get_plugin_name()), $this->menus_count));
		}
		
		/**
		 * Get all the Drupal menus with their alias and parent ID
		 * 
		 */
		private function get_all_menus() {
			$menus = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return array();
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 6 & 7
				$sql = "
					SELECT m.mlid AS id, m.menu_name, m.plid AS parent_id
					FROM ${prefix}menu_links m
					WHERE m.module = 'menu'
					AND (m.router_path IN('node/%', 'taxonomy/term/%')
						OR m.external = 1)
					AND m.hidden = 0
					ORDER BY m.mlid
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT m.id, m.menu_name, mp.id AS parent_id
					FROM ${prefix}menu_link_content_data m
					LEFT JOIN ${prefix}menu_link_content mp ON mp.uuid = REPLACE(m.parent, 'menu_link_content:', '')
					WHERE (m.link__uri LIKE 'entity:node/%'
						OR m.link__uri LIKE 'internal:/term/taxonomy%'
						OR m.external = 1)
					AND m.enabled = 1
				";
			}
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$menus[$row['id']] = $row;
			}
			return $menus;
		}
		
		/**
		 * Get the Drupal menus
		 * 
		 * @return array Menus
		 */
		protected function get_menus() {
			$menus = array();

			$last_menu_id = (int)get_option('fgd2wp_last_menu_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];

			// Hooks for adding extra cols and extra criteria
			$extra_cols = apply_filters('fgd2wp_get_menus_add_extra_cols', '');
			$extra_joins = apply_filters('fgd2wp_get_menus_add_extra_joins', '');
			$extra_criteria = apply_filters('fgd2wp_get_menus_add_extra_criteria', '');

			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return array();
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 6 & 7
				$sql = "
					SELECT m.mlid AS id, m.menu_name, m.link_title, m.options, m.link_path, m.plid AS parent_id, m.external, m.weight
					$extra_cols
					FROM ${prefix}menu_links m
					$extra_joins
					WHERE m.module = 'menu'
					AND (m.router_path IN('node/%', 'taxonomy/term/%')
						OR m.external = 1)
					AND m.hidden = 0
					$extra_criteria
					AND m.mlid > '$last_menu_id'
					ORDER BY m.mlid
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT m.id, m.menu_name, m.title AS link_title, m.link__options AS options, m.link__uri AS link_path, mp.id AS parent_id, m.external, m.weight
					$extra_cols
					FROM ${prefix}menu_link_content_data m
					LEFT JOIN ${prefix}menu_link_content mp ON mp.uuid = REPLACE(m.parent, 'menu_link_content:', '')
					$extra_joins
					WHERE (m.link__uri LIKE 'entity:node/%'
						OR m.link__uri LIKE 'internal:/term/taxonomy%'
						OR m.external = 1)
					AND m.enabled = 1
					$extra_criteria
				";
			}
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$menus[$row['id']] = $row;
			}
			return $menus;
		}
		
		/**
		 * Add a menu
		 *
		 * @param array $menu Nav menu
		 * @return mixed (array: $new_menu Imported menu | false)
		 */
		private function add_menu($menu) {
			$new_menu = false;
			
			// Get the menu
			$menu_obj = wp_get_nav_menu_object($menu['menu_name']);
			
			if ( $menu_obj ) {
				$menu_id = $menu_obj->term_id;
			} else {
				// Create the menu
				$menu_id = wp_create_nav_menu($menu['menu_name']);
				add_term_meta($menu_id, '_fgd2wp_old_menu_id', $menu['menu_name']);
				do_action('fgd2wp_post_create_nav_menu', $menu_id, $menu);
			}
			
			if ( !is_null($menu_id) && !is_a($menu_id, 'WP_Error') ) {
				// Get the menu item data
				$menu_item = $this->get_menu_item($menu);
				if ( is_null($menu_item) ) {
					$menu_item = apply_filters('fgd2wp_get_menu_item', $menu_item, $menu);
				}
				if ( !is_null($menu_item) ) {
					// Create the menu item
					$menu_item_id = $this->add_menu_item($menu_item, $menu_id, $menu);
					if ( is_int($menu_item_id) ) {
						// Add the Drupal ID as a post meta to not import it again
						add_post_meta($menu_item_id, '_fgd2wp_old_menu_item_id', $menu['id'], true);
						
						$new_menu = array(
							'drupal_menu_id'	=> $menu['id'],
							'menu_name'			=> $menu['menu_name'],
							'title'				=> $menu['link_title'],
							'drupal_parent_id'	=> $menu['parent_id'],
							'object_id'			=> $menu_item['object_id'],
							'type'				=> $menu_item['type'],
							'url'				=> $menu_item['url'],
							'object'			=> $menu_item['object'],
							'description'		=> $menu_item['description'],
							'parent_id'			=> $menu_item['parent_id'],
						);
					}
				}
				
				do_action('fgd2wp_post_add_menu', $menu_item, $menu);
				
				return $new_menu;
			}
		}
		
		/**
		 * Get the menu item data (object_id, type, url, object)
		 * 
		 * @param array $menu Menu item row
		 * @return array Menu item || null
		 */
		private function get_menu_item($menu) {
			$menu_item_object_id = 0;
			$menu_item_type = '';
			$menu_item_url = '';
			$menu_item_object = '';
			$drupal_menu_id = $menu['id'];
			$matches = array();
			if ( $menu['external'] == 1 ) {
				// External link
				$menu_item_type = 'custom';
				$menu_item_object = 'custom';
				$menu_item_url = $menu['link_path'];
				if ( !preg_match('#https?://#', $menu_item_url) ) { // relative URL
					$menu_item_url = home_url() . '/' . $menu['link_path'];
				}
				
			} elseif ( preg_match('#node/(\d+)#', $menu['link_path'], $matches) ) {
				// Node
				$node_id = $matches[1];
				$menu_item_type = 'post_type';
				if ( array_key_exists($node_id, $this->imported_posts) ) {
					$menu_item_object_id = $this->imported_posts[$node_id]['post_id'];
					$menu_item_object = $this->imported_posts[$node_id]['post_type'];
				} else {
					return;
				}
				
			} elseif ( preg_match('#taxonomy/term/(\d+)#', $menu['link_path'], $matches) || // Drupal 6 & 7
						preg_match('#term/taxonomy/(\d+)#', $menu['link_path'], $matches) ) { // Drupal 8
				// Taxonomy term
				$term_id = $matches[1];
				$menu_item_type = 'taxonomy';
				if ( array_key_exists($term_id, $this->imported_terms) ) {
					$menu_item_object_id = $this->imported_terms[$term_id]['term_id'];
					$menu_item_object = $this->imported_terms[$term_id]['taxonomy'];
				} else {
					return;
				}
			}
			
			// Description
			if ( is_resource($menu['options']) ) { // PostgreSQL bytea type
				$menu['options'] = stream_get_contents($menu['options']);
			}
			$options = unserialize($menu['options']);
			$menu_item_description = isset($options['attributes']['title'])? $options['attributes']['title'] : '';
			
			// Parent
			$wp_parent_id = isset($this->all_menus[$drupal_menu_id]) && isset($this->imported_menu_items[$this->all_menus[$drupal_menu_id]['parent_id']])? $this->imported_menu_items[$this->all_menus[$drupal_menu_id]['parent_id']] : 0;
			
			return array(
				'object_id'		=> $menu_item_object_id,
				'type'			=> $menu_item_type,
				'url'			=> $menu_item_url,
				'object'		=> $menu_item_object,
				'description'	=> $menu_item_description,
				'parent_id'		=> $wp_parent_id,
			);
		}
		
		/**
		 * Add a menu item
		 * 
		 * @param array $menu_item Menu item
		 * @param int $menu_id WordPress menu ID
		 * @param array $menu Menu
		 * @return int Menu item ID
		 */
		private function add_menu_item($menu_item, $menu_id, $menu) {
			$drupal_menu_id = $menu['id'];
			$menu_data = array(
				'menu-item-object-id'	=> $menu_item['object_id'],
				'menu-item-type'		=> $menu_item['type'],
				'menu-item-url'			=> $menu_item['url'],
				'menu-item-db-id'		=> 0,
				'menu-item-object'		=> $menu_item['object'],
				'menu-item-parent-id'	=> $menu_item['parent_id'],
				'menu-item-position'	=> $menu['weight'],
				'menu-item-title'		=> $menu['link_title'],
				'menu-item-description'	=> $menu_item['description'],
				'menu-item-status'		=> 'publish',
			);
			$menu_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_data);
			if ( is_int($menu_item_id) ) {
				$this->menus_count++;
				$this->imported_menu_items[$drupal_menu_id] = $menu_item_id;
			}
			return $menu_item_id;
		}
		
		/**
		 * Set the parent menu items if the parent have been saved after their children
		 * 
		 */
		private function set_parent_menu_items() {
			$imported_menu_items = $this->get_imported_menu_items();
			foreach ( $imported_menu_items as $old_id => $wp_id ) {
				if ( isset($this->all_menus[$old_id]) ) {
					$wp_parent_id = isset($imported_menu_items[$this->all_menus[$old_id]['parent_id']])? $imported_menu_items[$this->all_menus[$old_id]['parent_id']] : 0;
					if ( !empty($wp_parent_id) ) {
						update_post_meta($wp_id, '_menu_item_menu_item_parent', $wp_parent_id);
					}
				}
			}
		}
		
		/**
		 * Get the imported menu items
		 * 
		 * @global Object $wpdb WordPress database object
		 * @return array Imported menu items
		 */
		private function get_imported_menu_items() {
			global $wpdb;
			$imported_menu_items = array();
			$meta_key = '_fgd2wp_old_menu_item_id';
			
			$sql = "
				SELECT p.ID, pm.meta_value
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
				WHERE p.post_type = 'nav_menu_item'
				AND pm.meta_key = '$meta_key'
			";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$imported_menu_items[$result->meta_value] = $result->ID;
			}
			ksort($imported_menu_items);
			return $imported_menu_items;
		}
		
	}
}
