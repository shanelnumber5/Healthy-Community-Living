<?php

/**
 * Toolset methods
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.62.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_CPT_Toolset', false) ) {

	/**
	 * Toolset class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_CPT_Toolset implements FG_Drupal_to_WordPress_iCPT {
		
		private $plugin;
		public $wpcf_version = ''; // Toolset version
		private static $toolset_relationships = array(); // Toolset relationships
		private $wpcf_options = array();
		
		/**
		 * Constructor
		 * 
		 * @since 1.62.0
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
			$this->wpcf_version = get_option('wpcf-version');
			add_action('fgd2wp_post_register_custom_fields', array($this, 'set_wpcf_options'));
		}
		
		/**
		 * Set the WPCF checkboxes and select options in an array
		 * 
		 * @since 3.4.0
		 */
		public function set_wpcf_options() {
			$this->wpcf_options = $this->get_wpcf_options();
		}
		
		/**
		 * Get the available options for all custom fields
		 * 
		 * @since 3.4.0
		 * 
		 * @return array Options
		 */
		private function get_wpcf_options() {
			$wpcf_options = array();
			foreach ( array('wpcf-usermeta', 'wpcf-fields') as $option_name ) {
				$wpcf_fields = get_option($option_name, array());
				foreach ( $wpcf_fields as $custom_field_name => $wpcf_field ) {
					if ( isset($wpcf_field['data']['options']) ) {
						foreach ($wpcf_field['data']['options'] as $key => $data ) {
							$value = '';
							if ( isset($data['set_value']) ) {
								$value = $data['set_value'];
							} elseif ( isset($data['value']) ) {
								$value = $data['value'];
							}
							if ( !empty($value) ) {
								$wpcf_options[$custom_field_name][$value] = array(
									'key' => $key,
									'title' => $data['title'],
								);
							}
						}
					}
				}
			}
			return $wpcf_options;
		}
		
		/**
		 * Check if the Toolset Types plugin is activated
		 */
		public function check_required_plugins() {
			if ( !is_plugin_active('types/wpcf.php') ) {
				$this->plugin->display_admin_warning(sprintf(__('The <a href="%s" target="_blank">Toolset Types plugin</a> is required to manage the custom post types, the custom taxonomies and the custom fields.', 'fgd2wpp'), 'https://www.fredericgilles.net/toolset-types'));
			}
		}
		
		/**
		 * Check if the repeating fields are supported with the current Toolset Types version
		 * 
		 * @return bool Repeating fields supported
		 */
		public function is_repeating_fields_supported() {
			return is_plugin_active('types/wpcf.php') && version_compare($this->wpcf_version, '3.0', '>=');
		}
		
		/**
		 * Return the Toolset field prefix
		 * 
		 * @since 3.0.0
		 * 
		 * @return string Field prefix
		 */
		public function get_field_prefix() {
			return 'wpcf-';
		}
		
		/**
		 * Delete the Toolset data
		 * 
		 * @since 1.61.0
		 * 
		 * @global object $wpdb
		 */
		public function delete_toolset_data() {
			global $wpdb;
			
			$toolset_tables = array('toolset_associations', 'toolset_connected_elements', 'toolset_maps_address_cache', 'toolset_post_guid_id', 'toolset_relationships', 'toolset_type_sets');
			
			$wpdb->show_errors();
			$sql_queries = array();
			$sql_queries[] = "SET FOREIGN_KEY_CHECKS=0;";

			foreach ( $toolset_tables as $table ) {
				if ( !is_null($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}$table'")) ) { // If the table exists
					$sql_queries[] = "TRUNCATE {$wpdb->prefix}$table";
				}
			}
			$sql_queries[] = "SET FOREIGN_KEY_CHECKS=1;";

			// Execute SQL queries
			if ( count($sql_queries) > 2 ) {
				foreach ( $sql_queries as $sql ) {
					$wpdb->query($sql);
				}
			}
			$wpdb->hide_errors();
		}
		
		/**
		 * Register a taxonomy on Types
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types=array(), $hierarchical=true) {
			$wpcf_custom_taxonomies = get_option('wpcf-custom-taxonomies', array());
			if ( !is_array($wpcf_custom_taxonomies) ) {
				$wpcf_custom_taxonomies = array();
			}
			if ( !isset($wpcf_custom_taxonomies[$taxonomy]) ) {
				$wpcf_custom_taxonomy = array(
					$taxonomy => array(
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'search_items' => 'Search %s',
							'popular_items' => 'Popular %s',
							'all_items' => 'All %s',
							'parent_item' => 'Parent %s',
							'parent_item_colon' => 'Parent %s:',
							'edit_item' => 'Edit %s',
							'view_item' => 'View %s',
							'update_item' => 'Update %s',
							'add_new_item' => 'Add New %s',
							'new_item_name' => 'New %s Name',
							'separate_items_with_commas' => 'Separate %s with commas',
							'add_or_remove_items' => 'Add or remove %s',
							'choose_from_most_used' => 'Choose from the most used %s',
							'not_found' => "No $plural found.",
							'no_terms' => 'No %s',
							'items_list_navigation' => '%s list navigation',
							'items_list' => '%s list',
							'menu_name' => '%s',
							'name_admin_bar' => '%s',
						),
						'description' => $description,
						'public' => 1,
						'publicly_queryable' => 1,
						'hierarchical' => $hierarchical? 'hierarchical': 'flat',
						'show_ui' => 1,
						'show_in_menu' => 1,
						'show_in_nav_menus' => 1,
						'show_tagcloud' => 1,
						'show_in_quick_edit' => 1,
						'show_admin_column' => 1,
						'meta_box_cb' => "post_{$taxonomy}_meta_box",
						'rewrite' => array (
							'enabled' => 1,
							'with_front' => 1,
							'hierarchical' => $hierarchical? 1: '',
							'slug' => '',
						),
						'query_var' => '',
						'update_count_callback' => '',
						'name' => $taxonomy,
						'label' => $plural,
						'slug' => $taxonomy,
					),
				);
				// Associate the post type to the taxonomy
				foreach ( $post_types as $post_type ) {
					$wpcf_custom_taxonomy[$taxonomy]['supports'][$post_type] = 1;
				}
				$wpcf_custom_taxonomies = array_merge($wpcf_custom_taxonomies, $wpcf_custom_taxonomy);
				update_option('wpcf-custom-taxonomies', $wpcf_custom_taxonomies);
			}
		}
		
		/**
		 * Get the custom taxonomies
		 * 
		 * @since 3.0.0
		 * 
		 * @return array Custom taxonomies
		 */
		public function get_custom_taxonomies() {
			return get_option('wpcf-custom-taxonomies', array());
		}
		
		/**
		 * Get the taxonomy name
		 * 
		 * @since 3.0.0
		 * 
		 * @param array $taxonomy_object Taxonomy object
		 * @return string Taxonomy name
		 */
		public function get_taxonomy_name($taxonomy_object) {
			return $taxonomy_object['labels']['name'];
		}
		
		/**
		 * Register the custom fields for a post type
		 *
		 * @param array $custom_fields Custom fields
		 * @param string $post_type Post type
		 * @return int Number of fields imported
		 */
		public function register_custom_post_fields($custom_fields, $post_type) {
			$fields_count = 0;
			$wpcf_fields = array();

			// Create the fields (in option table)
			$group_fields = array();
			foreach ( $custom_fields as $field_slug => $field ) {
				if ( in_array($field_slug, array('body', 'excerpt')) ) {
					continue; // Don't register the body and excerpt fields
				}
				if ( isset($field['do_not_register']) && $field['do_not_register'] ) {
					continue;
				}
				$order = isset($field['order'])? $field['order'] : 0;
				$fields_group_post_id = $this->get_custom_fields_group($post_type, $field);
				
				// Field collection or Paragraphs field
				if ( isset($field['collection']) ) {
					$group_field_slug = apply_filters('fgd2wp_register_types_post_field', $field_slug, $post_type, $field);
					if ( !empty($group_field_slug) ) {
						$group_fields[$fields_group_post_id][] = array('slug' => $group_field_slug, 'order' => $order);
						$fields_count++;
					}
					continue;
				}
				
				$wpcf_field = $this->register_custom_field($field_slug, $field, 'postmeta');
				$wpcf_fields = array_merge($wpcf_fields, $wpcf_field);
				$group_fields[$fields_group_post_id][] = array('slug' => $field_slug, 'order' => $order);
				$fields_count++;
			}
			update_option('wpcf-fields', array_merge(get_option('wpcf-fields', array()), $wpcf_fields));
			
			// Assign the field to the fields group (in postmeta table)
			foreach ( $group_fields as $group_field_id => $fields ) {
				usort($fields, array($this, 'sort_custom_fields')); // Sort the fields by their order
				$types_group_fields = array();
				foreach ( $fields as $field ) {
					$types_group_fields[] = $field['slug'];
				}
				update_post_meta($group_field_id, '_wp_types_group_fields', implode(',', $types_group_fields));
			}
			
			$fields_count = apply_filters('fgd2wp_post_register_types_post_fields', $fields_count, $custom_fields);
			
			return $fields_count;
		}
		
		/**
		 * Sort the fields by their order
		 * 
		 * @since 3.0.0
		 * 
		 * @param array $field1 Field1
		 * @param array $field2 Field2
		 * @return int -1 |0 | 1
		 */
		private function sort_custom_fields($field1, $field2) {
			return strnatcmp($field1['order'], $field2['order']);
		}
		
		/**
		 * Register a taxonomy custom field
		 * 
		 * @since 1.40.0
		 * 
		 * @param string $custom_field_name Custom field name
		 * @param array $custom_field_data Custom field data
		 */
		public function register_custom_taxonomy_field($custom_field_name, $custom_field_data) {
			// Get the custom fields group
			$fields_group_post_id = $this->get_custom_taxonomy_fields_group($custom_field_data['taxonomy']);
			
			$wpcf_term_meta = get_option('wpcf-termmeta', array());
			if ( !is_array($wpcf_term_meta) ) {
				$wpcf_term_meta = array();
			}
			if ( empty($wpcf_term_meta) || !isset($wpcf_term_meta[$custom_field_name]) ) {
				$wpcf_field = $this->register_custom_field($custom_field_name, $custom_field_data, 'termmeta');
				$wpcf_term_meta = array_merge($wpcf_term_meta, $wpcf_field);
				update_option('wpcf-termmeta', $wpcf_term_meta);
			}
			
			// Assign the field to the fields group (in postmeta table)
			if ( isset($fields_group_post_id) ) {
				if ( !empty($fields_group_post_id) ) {
					$wp_types_group_fields_list = get_post_meta($fields_group_post_id, '_wp_types_group_fields', true);
					$wp_types_group_fields = explode(',', $wp_types_group_fields_list);
					if ( !in_array($custom_field_name, $wp_types_group_fields) ) {
						$wp_types_group_fields[] = $custom_field_name;
						update_post_meta($fields_group_post_id, '_wp_types_group_fields', implode(',', $wp_types_group_fields));
					}
				}
			}
		}
		
		/**
		 * Register a field object using the WPCF structure
		 * 
		 * @since 1.47.0
		 * 
		 * @param string $field_slug Field slug
		 * @param array $field Field data
		 * @param string $meta_type Meta type (postmeta | termmeta | usermeta)
		 * @return array WPCF field
		 */
		public function register_custom_field($field_slug, $field, $meta_type) {
			$name = $field['label'];
			$module = isset($field['module'])? $field['module'] : '';
			// Map the custom field type
			$type = $this->map_toolset_field_type($this->plugin->map_custom_field_type($field['type'], $name, $module), $field);
			$default_value = isset($field['default_value'][0]['value'])? $field['default_value'][0]['value']: '';
			$default_value = (string)$default_value;

			// Create the field
			$wpcf_field = array(
				$field_slug => array(
					'id' => $field_slug,
					'slug' => $field_slug,
					'type' => $type,
					'name' => $name,
					'description' => isset($field['description'])? $field['description'] : '',
					'data' => array(
						'slug-pre-save' => $field_slug,
 						'user_default_value' => $default_value,
						'repetitive' => isset($field['repetitive'])? $field['repetitive']: 0,
						'conditional_display' => array(
							'relation' => 'AND',
							'custom' => '',
						),
	                    'submit-key' => $field_slug,
						'disabled_by_type' => 0,
					),
					'meta_key' => 'wpcf-' . $field_slug,
					'meta_type' => $meta_type,
				),
			);

			// Datetime field
			if ( $field['type'] == 'datetime') {
				$wpcf_field[$field_slug]['data']['date_and_time'] = 'and_time';
			}
			
			// Checkbox field
			if ( $type == 'checkbox' ) {
				$wpcf_field[$field_slug]['data'] = array(
                    'slug-pre-save' => $field_slug,
                    'set_value' => 1,
                    'save_empty' => 'no',
                    'display' => 'db',
					'conditional_display' => array(
						'relation' => 'AND',
						'custom' => '',
					),
                    'submit-key' => $field_slug,
					'disabled_by_type' => 0,
				);
			}
			
			// Options for checkboxes and select box
			if ( in_array($type, array('checkboxes', 'radio', 'select')) ) {
				$default_id = '';
				if ( isset($field['options']) ) {
					foreach ( $field['options'] as $option_name => $option_value ) {
						$option_name = (string)$option_name;
						$wpcf_option_name = 'wpcf-fields-' . $type . '-option-' . md5($option_value) . '-1';
						$wpcf_option_value = array(
							'title'		=> $option_value,
						);
						if ( $type == 'checkboxes' ) {
							// Checkboxes
							$wpcf_option_value['set_value'] = $option_name;
							$wpcf_option_value['display'] = 'db';
							if ( $option_name == $default_value ) {
								$wpcf_option_value['checked'] = 1;
							}
						} else {
							// Select box or radio box
							$wpcf_option_value['value'] = $option_name;
							$wpcf_option_value['display_value'] = $option_name;
							if ( $option_name == $default_value ) {
								$default_id = $wpcf_option_name;
							}
						}
						$wpcf_field[$field_slug]['data']['options'][$wpcf_option_name] = $wpcf_option_value;
					}
					unset($wpcf_field[$field_slug]['data']['user_default_value']);
					unset($wpcf_field[$field_slug]['data']['repetitive']);
					if ( $type == 'checkboxes' ) {
						$wpcf_field[$field_slug]['data']['save_empty'] = 'no';
					} else {
						$wpcf_field[$field_slug]['data']['display'] = 'db';
					}
					// Default value
					if ( !empty($default_id) ) {
						$wpcf_field[$field_slug]['data']['options']['default'] = $default_id;
					}
				}
			}

			// Required field
			if ( isset($field['required']) && $field['required'] ) {
				$wpcf_field[$field_slug]['data']['validate']['required'] = array(
					'active' => 1,
					'value' => 'true',
					'message' => __('This field is required.', 'fgd2wpp'),
				);
			}
			
			$wpcf_field = apply_filters('fgd2wp_pre_register_wpcf_field', $wpcf_field);
			return $wpcf_field;
		}
		
		/**
		 * Map the Drupal field type to a Toolset field type
		 * 
		 * @since 3.0.0
		 * 
		 * @param string $field_type Field type
		 * @param array $field Field
		 * @return string Toolset field type
		 */
		private function map_toolset_field_type($field_type, $field) {
			switch ( $field_type ) {
				case 'text':
					$field_type = 'textfield';
					break;
				case 'datetime':
					$field_type = 'date';
					break;
				case 'color':
					$field_type = 'colorpicker';
					break;
				case 'checkbox':
					$field_type = 'checkboxes';
					break;
				case 'select':
					if ( isset($field['cardinality']) && ($field['cardinality'] != 1) ) {
						$field_type = 'checkboxes'; // Replace the multi-select (not supported on Toolset) by checkboxes
					}
					break;
				case 'post_object':
					$field_type = 'post';
					break;
			}
			return $field_type;
		}
		
		/**
		 * Get a custom fields group and create it if it doesn't exist yet
		 * 
		 * @param string $post_type Post type
		 * @param string $field Field data
		 * @return int Field group post ID
		 */
		private function get_custom_fields_group($post_type, $field) {
			
			if ( isset($field['field_group'] ) && !empty($field['field_group']) ) {
				$fields_group_title = $field['field_group'];
			} else {
				$fields_group_title = ucfirst($post_type);
			}
			$fields_group_title .= ' fields';
			$fields_group_name = sanitize_title($fields_group_title);
			
			// Test if the fields group doesn't already exist
			$fields_group_posts = get_posts(array(
				'name' => $fields_group_name,
				'post_type' => 'wp-types-group',
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));
			if ( $fields_group_posts ) {
				$fields_group_post_id = $fields_group_posts[0]->ID;
			} else {
				$fields_group_post_id = $this->create_custom_fields_group($fields_group_title, $fields_group_name);
			}
			
			$this->create_field_group_post_type_relation($fields_group_post_id, $post_type);
			
			return $fields_group_post_id;
		}
		
		/**
		 * Get a custom fields group and create it if it doesn't exist yet
		 * 
		 * @since 1.40.0
		 * 
		 * @param string $taxonomy Taxonomy
		 * @return int Field group post ID
		 */
		private function get_custom_taxonomy_fields_group($taxonomy) {
			
			$fields_group_title = ucfirst($taxonomy) . ' fields';
			$fields_group_name = sanitize_title($fields_group_title);
			
			// Test if the fields group doesn't already exist
			$fields_group_posts = get_posts(array(
				'name' => $fields_group_name,
				'post_type' => 'wp-types-term-group',
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));
			if ( $fields_group_posts ) {
				$fields_group_post_id = $fields_group_posts[0]->ID;
			} else {
				$fields_group_post_id = $this->create_custom_taxonomy_fields_group($fields_group_title, $fields_group_name);
			}
			
			$this->create_field_group_taxonomy_relation($fields_group_post_id, $taxonomy);
			
			return $fields_group_post_id;
		}
		
		/**
		 * Create a custom fields group
		 * 
		 * @param string $fields_group_title Fields group title
		 * @param string $fields_group_name Fields group name
		 * @return int Field group post ID
		 */
		private function create_custom_fields_group($fields_group_title, $fields_group_name) {
			
			// Create the fields group (in post table)
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'publish',
				'post_title'		=> $fields_group_title,
				'post_name'			=> $fields_group_name,
				'post_type'			=> 'wp-types-group',
			);
			$fields_group_post_id = wp_insert_post($new_post, true);
			if ( !is_wp_error($fields_group_post_id) ) {
				add_post_meta($fields_group_post_id, '_fgd2wp_old_group_name', $fields_group_name, true);
				add_post_meta($fields_group_post_id, '_wpcf_conditional_display', array ('relation' => 'AND', 'custom' => ''), true);
				add_post_meta($fields_group_post_id, '_wp_types_group_templates', 'all', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_admin_styles', '', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_terms', 'all', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_fields', '', true);
				add_post_meta($fields_group_post_id, '_wp_types_group_filters_association', 'any', true);
			}
			return $fields_group_post_id;
		}
		
		/**
		 * Create a custom taxonomy fields group
		 * 
		 * @since 1.40.0
		 * 
		 * @param string $fields_group_title Fields groupe title
		 * @param string $fields_group_name Fields groupe name
		 * @return int Field group post ID
		 */
		private function create_custom_taxonomy_fields_group($fields_group_title, $fields_group_name) {
				
			// Create the taxonomy fields group (in post table)
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'publish',
				'post_title'		=> $fields_group_title,
				'post_name'			=> $fields_group_name,
				'post_type'			=> 'wp-types-term-group',
			);
			$fields_group_post_id = wp_insert_post($new_post, true);
			if ( !is_wp_error($fields_group_post_id) ) {
				add_post_meta($fields_group_post_id, '_fgd2wp_old_term_group_name', $fields_group_name, true);
			}
			return $fields_group_post_id;
		}
		
		/**
		 * Create a relation between the field group and the post type
		 * 
		 * @since 1.14.0
		 * 
		 * @param int $fields_group_post_id Field group post ID
		 * @param string $post_type Post type
		 */
		private function create_field_group_post_type_relation($fields_group_post_id, $post_type) {
			if ( !empty($fields_group_post_id) ) {
				$group_post_types_list = get_post_meta($fields_group_post_id, '_wp_types_group_post_types', true);
				$group_post_types = empty($group_post_types_list)? array() : explode(',', $group_post_types_list);
				if ( !in_array($post_type, $group_post_types) ) {
					$group_post_types[] = $post_type;
					$group_post_types_list = implode(',', $group_post_types);
					update_post_meta($fields_group_post_id, '_wp_types_group_post_types', $group_post_types_list);
					do_action('fgd2wp_post_create_field_group_post_type_relation', $fields_group_post_id, $post_type);
				}
			}
		}
		
		/**
		 * Create a relation between the taxonomy field group and the taxonomy
		 * 
		 * @since 1.40.0
		 * 
		 * @param int $fields_group_post_id Field group post ID
		 * @param string $taxonomy Taxonomy
		 */
		private function create_field_group_taxonomy_relation($fields_group_post_id, $taxonomy) {
			if ( !empty($fields_group_post_id) ) {
				$taxonomy = $this->plugin->map_taxonomy($taxonomy);
				$associated_taxonomies = get_post_meta($fields_group_post_id, '_wp_types_associated_taxonomy', false);
				if ( !in_array($taxonomy, $associated_taxonomies) ) {
					add_post_meta($fields_group_post_id, '_wp_types_associated_taxonomy', $taxonomy);
				}
			}
		}
		
		/**
		 * Register the User field group and the user fields in Types
		 * 
		 * @since 1.47.0
		 * 
		 * @param array $custom_fields Custom fields
		 * @return int Number of fields imported
		 */
		public function register_custom_user_fields($custom_fields) {
			$fields_count = 0;
			$wpcf_fields = get_option('wpcf-usermeta', array());
			if ( !is_array($wpcf_fields) ) {
				$wpcf_fields = array();
			}

			$fields_group_post_id = $this->get_user_fields_group('User fields');
			
			// Create the fields (in option table)
			$group_fields = array();
			foreach ( $custom_fields as $field_slug => $field ) {
				$wpcf_field = $this->register_custom_field($field_slug, $field, 'usermeta');
				$wpcf_fields = array_merge($wpcf_fields, $wpcf_field);
				$group_fields[$fields_group_post_id][] = $field_slug;
				$fields_count++;
			}
			update_option('wpcf-usermeta', $wpcf_fields);
			
			// Assign the field to the fields group (in postmeta table)
			foreach ( $group_fields as $group_field_id => $fields ) {
				update_post_meta($group_field_id, '_wp_types_group_fields', implode(',', $fields));
			}
			
			return $fields_count;
		}
		
		/**
		 * Get a user fields group and create it if it doesn't exist yet
		 * 
		 * @since 1.47.0
		 * 
		 * @param string $fields_group_title User fields group title
		 * @return int Field group post ID
		 */
		private function get_user_fields_group($fields_group_title) {
			
			$fields_group_name = sanitize_title($fields_group_title);
			
			// Test if the fields group doesn't already exist
			$fields_group_posts = get_posts(array(
				'name' => $fields_group_name,
				'post_type' => 'wp-types-user-group',
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));
			if ( $fields_group_posts ) {
				$fields_group_post_id = $fields_group_posts[0]->ID;
			} else {
				$fields_group_post_id = $this->create_user_fields_group($fields_group_title, $fields_group_name);
			}
			
			return $fields_group_post_id;
		}
		
		/**
		 * Create a user fields group
		 * 
		 * @since 1.47.0
		 * 
		 * @param string $fields_group_title Fields group title
		 * @param string $fields_group_name Fields group name
		 * @return int Field group post ID
		 */
		private function create_user_fields_group($fields_group_title, $fields_group_name) {
				
			// Create the fields group (in post table)
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'publish',
				'post_title'		=> $fields_group_title,
				'post_name'			=> $fields_group_name,
				'post_type'			=> 'wp-types-user-group',
			);
			$fields_group_post_id = wp_insert_post($new_post, true);
			if ( $fields_group_post_id ) {
				add_post_meta($fields_group_post_id, '_wp_types_group_showfor', 'all', true);
			}
			return $fields_group_post_id;
		}
		
		/**
		 * Register a builtin post type on Types
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular post type name
		 * @param string $plural Plural post type name
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 */
		public function register_builtin_post_type($post_type, $singular, $plural, $description, $taxonomies) {
			$wpcf_custom_types = get_option('wpcf-custom-types', array());
			if ( !is_array($wpcf_custom_types) ) {
				$wpcf_custom_types = array();
			}
			if ( empty($wpcf_custom_types) || !isset($wpcf_custom_types[$post_type]) ) {
				$taxonomies_array = array();
				// Add the post builtin taxonomies
				if ( $post_type == 'post' ) {
					$taxonomies_array['category'] = 1;
					$taxonomies_array['post_tag'] = 1;
				}
				foreach ( $taxonomies as $taxonomy ) {
					if ( $taxonomy != 'tags' ) {
						$taxonomies_array[$taxonomy] = 1;
					}
				}
				$wpcf_custom_type = array(
					$post_type => array(
						'wpcf-post-type' => $post_type,
						'icon' => 'admin-post',
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
						),
						'slug' => $post_type,
						'description' => $description,
						'public' => 'public',
						'menu_position' => 0,
						'taxonomies' => $taxonomies_array,
						'_builtin' => 1,
					),
				);
				$wpcf_custom_types = array_merge($wpcf_custom_types, $wpcf_custom_type);
				update_option('wpcf-custom-types', $wpcf_custom_types);
			}
		}
		
		/**
		 * Register a post type on Types
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular label
		 * @param string $plural Plural label
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 * @param array $parent_post_types Parent post types
		 */
		public function register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, $parent_post_types=array()) {
			$wpcf_custom_types = get_option('wpcf-custom-types', array());
			if ( !is_array($wpcf_custom_types) ) {
				$wpcf_custom_types = array();
			}
			if ( is_numeric($post_type) ) {
				// The post type must not be entirely numeric
				$post_type = '_' . $post_type;
			}
			if ( empty($wpcf_custom_types) || !isset($wpcf_custom_types[$post_type]) ) {
				// Taxonomies
				$taxonomies_array = array();
				foreach ( $taxonomies as $taxonomy ) {
					$taxonomies_array[$taxonomy] = 1;
				}
				$wpcf_custom_type = array(
					$post_type => array(
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'add_new' => 'Add New',
							'add_new_item' => 'Add New %s',
							'edit_item' => 'Edit %s',
							'new_item' => 'New %s',
							'view_item' => 'View %s',
							'search_items' => 'Search %s',
							'not_found' => 'No %s found',
							'not_found_in_trash' => 'No %s found in Trash',
							'parent_item_colon' => 'Parent %s',
							'all_items' => '%s',
						),
						'slug' => $post_type,
						'description' => $description,
						'public' => 'public',
						'menu_position' => 0,
						'menu_icon' => '',
						'taxonomies' => $taxonomies_array,
						'supports' => array(
							'title' => 1,
							'editor' => 1,
							'thumbnail' => 1,
							'author' => 1,
//							'custom-fields' => 1,
						),
						'rewrite' => array(
							'enabled' => 1,
							'custom' => 'normal',
							'slug' => '',
							'with_front' => 1,
							'feeds' => 1,
							'pages' => 1,
						),
						'has_archive' => 1,
						'show_in_menu' => 1,
						'show_in_menu_page' => '',
						'show_ui' => 1,
						'publicly_queryable' => 1,
						'can_export' => 1,
						'show_in_nav_menus' => 1,
						'query_var_enabled' => 1,
						'query_var' => '',
						'permalink_epmask' => 'EP_PERMALINK',
					),
				);
				
				if ( version_compare($this->wpcf_version, '3.0', '<') ) {
					// Toolset < 3.0
					// Parent post types
					$parent_post_types_array = array();
					foreach ( $parent_post_types as $parent_post_type ) {
						$parent_post_types_array[$parent_post_type] = 1;
					}
					if ( !empty($parent_post_types_array) ) {
						$wpcf_custom_type[$post_type]['post_relationship']['belongs'] = $parent_post_types_array;
					}
				}
				
				$wpcf_custom_types = array_merge($wpcf_custom_types, $wpcf_custom_type);
				update_option('wpcf-custom-types', $wpcf_custom_types);
				register_post_type($post_type, $wpcf_custom_type[$post_type]);
			}
		}
		
		/**
		 * Get the custom post types
		 * 
		 * @since 3.0.0
		 * 
		 * @return array Custom post types
		 */
		public function get_custom_post_types() {
			return get_option('wpcf-custom-types', array());
		}
		
		/**
		 * Get the post type name
		 * 
		 * @since 3.0.0
		 * 
		 * @param array $post_type_object Post type object
		 * @return string Post type name
		 */
		public function get_post_type_name($post_type_object) {
			return $post_type_object['labels']['name'];
		}
		
		/**
		 * Register the post types relationships
		 * 
		 * @since 1.16.0
		 * 
		 * @param array $node_types_relations Node Types Relationships
		 */
		public function register_post_types_relationships($node_types_relations) {
			if ( version_compare($this->wpcf_version, '3.0', '<') ) {
				
				// Toolset < 3.0
				$wpcf_relations = get_option('wpcf_post_relationship', array());
				foreach ( $node_types_relations as $child => $parents ) {
					foreach ( $parents as $parent ) {
						$wpcf_relations[$parent['post_type']][$child] = array();
					}
				}
				update_option('wpcf_post_relationship', $wpcf_relations);
			} else {
				
				// Toolset 3.0+
				$this->get_current_toolset_relationships();
				foreach ( $node_types_relations as $child => $parents ) {
					foreach ( $parents as $parent ) {
						$relationship_label = !empty($parent['label'])? $parent['label'] : $parent['post_type'];
						$relationship_slug = $this->normalize_slug($parent['slug'] . '-' . $child . '-' . $parent['post_type']);
						$this->add_toolset_relationship($relationship_slug, $child, $parent['post_type'], $relationship_label, $parent['cardinality'], 'wizard');
					}
				}
			}
		}
		
		/**
		 * Set the $toolset_relationships variable
		 */
		public function get_current_toolset_relationships() {
			self::$toolset_relationships = $this->get_toolset_relationships();
		}
		
		/**
		 * Get the Toolset relationships
		 * 
		 * @since 1.61.0
		 * 
		 * @global $wpdb
		 * @return array Relationships
		 */
		private function get_toolset_relationships() {
			global $wpdb;
			$relationships = array();
			
			$sql = "SELECT r.id, r.slug FROM {$wpdb->prefix}toolset_relationships r ORDER BY r.id";
			$result = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $result as $row ) {
				$relationships[$row['slug']] = $row['id'];
			}
			return $relationships;
		}
		
		/**
		 * Set a post relationship
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $post_id Post ID
		 * @param string $custom_field_name Custom field name
		 * @param int $related_id Related post ID
		 * @param array $custom_field Custom field
		 * @param string $relationship_slug Relationship slug (Toolset only)
		 */
		public function set_post_relationship($post_id, $custom_field_name, $related_id, $custom_field, $relationship_slug) {
			$this->set_post_association($related_id, $post_id, $relationship_slug);
		}
		
		/**
		 * Add a Toolset association between two posts
		 * 
		 * @since 1.62.0
		 * 
		 * @param int $parent_id Parent post ID
		 * @param int $child_id Child post ID
		 * @param string $relationship_slug Relationship slug
		 * @return bool Success
		 */
		public function set_post_association($parent_id, $child_id, $relationship_slug) {
			$return = false;
			
			// Toolset 3.0+
			if ( isset(self::$toolset_relationships[$relationship_slug]) && function_exists('toolset_connect_posts') ) {
				if ( preg_match('/-(.*)-(\1)$/', $relationship_slug) ) {
					// Prevent a Toolset bug: the relationship is inverted if the parent type is the same as the child type
					$result = toolset_connect_posts($relationship_slug, $child_id, $parent_id);
				} else {
					$result = toolset_connect_posts($relationship_slug, $parent_id, $child_id);
				}
				$return = $result['success'];
			}
			return $return;
		}
		
		/**
		 * Normalize the slug
		 * 
		 * @param string $slug Slug
		 * @return string Slug
		 */
		public function normalize_slug($slug) {
			$slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin($slug));
			return $slug;
		}
		
		/**
		 * Add a Toolset relationship
		 * 
		 * @since 1.61.0
		 * 
		 * @global object $wpdb WPDB object
		 * @param string $slug Relationship slug
		 * @param string $child_post_type Child post type
		 * @param string $parent_post_type_and_label Parent post type
		 * @param string $relationship_label Relationship label
		 * @param int cardinality_parent_max Max cardinality for the parent (-1 for many-to-many relationship)
		 * @param string $origin wizard | repeatable_group
		 * @return int Relationship ID
		 */
		public function add_toolset_relationship($slug, $child_post_type, $parent_post_type, $relationship_label, $cardinality_parent_max, $origin) {
			global $wpdb;
			$relationship_id = 0;
			
			if ( isset(self::$toolset_relationships[$slug]) ) {
				// Relationship already exists
				$relationship_id = self::$toolset_relationships[$slug];
				
			} else {
				$table_name = $wpdb->prefix . 'toolset_relationships';

				$parent_type_set_id = $this->add_toolset_type_set($parent_post_type);
				$child_type_set_id = $this->add_toolset_type_set($child_post_type);

				$singular = preg_replace('/s$/', '', ucwords($relationship_label));

				$result = $wpdb->insert($table_name, array(
					'slug' => $slug,
					'display_name_plural' => FG_Drupal_to_WordPress_Tools::plural($singular),
					'display_name_singular' => $singular,
					'driver' => 'toolset',
					'parent_domain' => 'posts',
					'parent_types' => $parent_type_set_id,
					'child_domain' => 'posts',
					'child_types' => $child_type_set_id,
					'ownership' => 0,
					'cardinality_parent_max' => $cardinality_parent_max,
					'cardinality_parent_min' => 0,
					'cardinality_child_max' => -1,
					'cardinality_child_min' => 0,
					'is_distinct' => ($origin == 'repeatable_group')? 0 : 1,
					'origin' => $origin,
					'role_name_parent' => 'parent',
					'role_name_child' => 'child',
					'role_name_intermediary' => 'association',
					'role_label_parent_singular' => 'Parent',
					'role_label_child_singular' => 'Child',
					'role_label_parent_plural' => 'Parents',
					'role_label_child_plural' => 'Children',
					'needs_legacy_support' => 0,
					'is_active' => 1,
				));
				if ( !empty($result) ) {
					$relationship_id = $wpdb->insert_id;
					self::$toolset_relationships[$slug] = $relationship_id;
				}
			}
			return $relationship_id;
		}
		
		/**
		 * Add a Toolset type set
		 * 
		 * @since 1.61.0
		 * 
		 * @global object $wpdb WPDB object
		 * @param string $post_type Post type
		 * @return int Type set ID
		 */
		private function add_toolset_type_set($post_type) {
			global $wpdb;
			$type_set_id = 0;
			
			$table_name = $wpdb->prefix . 'toolset_type_sets';
			
			$sql = "SELECT MAX(`set_id`) FROM `$table_name`";
			$max = $wpdb->get_var($sql);
			
			$result = $wpdb->insert($table_name, array(
				'set_id' => $max + 1,
				'type' => substr($post_type, 0, 20), // the type is limited to 20 characters
			));
			if ( !empty($result) ) {
				$type_set_id = $wpdb->insert_id;
			}
			return $type_set_id;
		}
		
		/**
		 * Add a custom field value as a post meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_post_field($new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_key = $custom_field_name;
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $date, $new_post_id);
			foreach ( $meta_values as $meta_value ) {
				if ( is_scalar($meta_value) ) {
					$meta_value = addslashes($this->plugin->replace_media_shortcodes(stripslashes($meta_value)));
				}
				add_post_meta($new_post_id, $this->get_field_prefix() . $meta_key, $meta_value);
			}
		}
		
		/**
		 * Add a custom field value as a term meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_term_id WordPress term ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 */
		public function set_custom_term_field($new_term_id, $custom_field_name, $custom_field, $custom_field_values) {
			$meta_key = $custom_field_name;
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values);
			foreach ( $meta_values as $meta_value ) {
				add_term_meta($new_term_id, $this->get_field_prefix() . $meta_key, $meta_value);
			}
		}
		
		/**
		 * Add a custom field value as a user meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_user_id WordPress user ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_user_field($new_user_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_key = $custom_field_name;
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $date);
			foreach ( $meta_values as $meta_value ) {
				add_user_meta($new_user_id, $this->get_field_prefix() . $meta_key, $meta_value);
			}
		}
		
		/**
		 * Convert custom field values to meta values
		 * 
		 * @since 1.12.1
		 * 
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 * @param int $new_post_id WordPress post ID
		 * @return array Meta values
		 */
		private function convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $date='', $new_post_id='') {
			$meta_values = array();
			$module = isset($custom_field['module'])? $custom_field['module'] : '';
			$custom_field_type = $this->map_toolset_field_type($this->plugin->map_custom_field_type($custom_field['type'], $custom_field['label'], $module), $custom_field);
			switch ( $custom_field_type ) {
				// Date
				case 'date':
				case 'datetime':
					foreach ( $custom_field_values as $custom_field_value ) {
						if ( is_array($custom_field_value) ) {
							foreach ( $custom_field_value as $subvalue ) {
								$meta_values[] = $this->convert_to_timestamp($subvalue);
							}
						} else {
							$meta_values[] = $this->convert_to_timestamp($custom_field_value);
						}
					}
					break;

				// Image
				case 'image':
				case 'file':
					if ( !$this->plugin->plugin_options['skip_media'] ) {
						foreach ( $custom_field_values as $file ) {
							// Import media
							$file_date = isset($file['timestamp'])? date('Y-m-d H:i:s', $file['timestamp']) : $date;
							$file_date = apply_filters('fgd2wp_get_custom_field_file_date', $file_date, $date);
							$image_attributs = array(
								'image_alt' => $this->plugin->get_image_attributes($file, 'alt'),
								'description' => $this->plugin->get_image_attributes($file, 'description'),
								'image_caption' => isset($file['caption'])? $file['caption'] : '',
							);
							$filename = preg_replace('/\..*$/', '', basename($file['filename']));
							$attachment_id = $this->plugin->import_media($filename, $this->plugin->get_path_from_uri($file['uri']), $file_date, $image_attributs);
							if ( $attachment_id ) {
								$this->plugin->media_count++;
								$attachment_url = wp_get_attachment_url($attachment_id);
								if ( !empty($attachment_url) ) {
									// Assign the media URL to the postmeta
									if ( !empty($new_post_id) ) {
										$set_featured_image = ($this->plugin->plugin_options['featured_image'] == 'featured') && !$this->plugin->thumbnail_is_set;
										$this->plugin->add_post_media($new_post_id, array('post_date' => $file_date), array($attachment_id), $set_featured_image); // Attach the media to the post
										$this->plugin->thumbnail_is_set = true;
									}
									// Set the field value
									$meta_values[] = $attachment_url;
								}
							}
						}
					}
					break;

				// URL or embedded media
				case 'url':
				case 'embed':
					foreach ( $custom_field_values as $custom_field_value ) {
						if ( isset($custom_field_value['uri']) ) {
							$wpcf_value = $this->plugin->get_path_from_uri($custom_field_value['uri']);
						} else {
							if ( is_array($custom_field_value) ) {
								$wpcf_value = $custom_field_value['url'];
							} else {
								$wpcf_value = $custom_field_value;
							}
						}
						$meta_values[] = $wpcf_value;
					}
					break;
					
				// Checkboxes
				case 'checkboxes':
					if ( isset($this->wpcf_options[$custom_field_name]) ) {
						$options = $this->wpcf_options[$custom_field_name];
						$wpcf_values = array();
						foreach ( $custom_field_values as $values ) {
							if ( is_array($values) ) {
								foreach ( $values as $value ) {
									if ( isset($options[$value]) ) {
										$wpcf_values[$options[$value]['key']] = $options[$value]['title'];
									}
								}
							}
						}
						$meta_values = $wpcf_values;
					}
					break;
				
				// Node reference
				case 'nodereference':
					// Node references are imported as relationships
					break;
				
				default:
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								$wpcf_value = implode("<br />\n", $custom_field_value);
							} else {
								$wpcf_value = $custom_field_value;
							}
							$wpcf_value = $this->plugin->replace_media_links($wpcf_value, $date);

							$meta_values[] = $wpcf_value;
						}
					} else {
						$meta_values[] = $custom_field_values;
					}
			}
			return $meta_values;
		}
		
		/**
		 * Convert a date to a timestamp
		 * 
		 * @since 1.72.0
		 * 
		 * @param mixed $date Date
		 * @return int Timestamp
		 */
		private function convert_to_timestamp($date) {
			if ( is_numeric($date) ) {
				$timestamp = $date;
			} else {
				$date = preg_replace('/-00/', '-01', $date); // For dates with month=00 or day=00
				$timestamp = strtotime($date);
			}
			return $timestamp;
		}
		
		/**
		 * Set the user picture
		 * 
		 * @since 3.0.0
		 * 
		 * @param int $user_id User ID
		 * @param int $image_id Image ID
		 */
		public function set_user_picture($user_id, $image_id) {
			$image_url = wp_get_attachment_url($image_id);
			add_user_meta($user_id, $this->get_field_prefix() . 'picture', $image_url);
		}
		
	}
}
