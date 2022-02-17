<?php

/**
 * ACF methods
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      3.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_CPT_ACF', false) ) {

	/**
	 * ACF class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_CPT_ACF {
		
		private $plugin;
		private $custom_fields = array();
		
		/**
		 * Constructor
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
			
			add_action('fgd2wp_pre_import', array($this, 'set_custom_fields'), 99);
		}
		
		/**
		 * Check if ACF and CPT UI are activated
		 */
		public function check_required_plugins() {
			if ( !defined('ACF') ) {
				$this->plugin->display_admin_warning(sprintf(__('The <a href="%s" target="_blank">Advanced Custom Fields plugin</a> is required to manage the custom fields.', 'fgd2wpp'), 'https://wordpress.org/plugins/advanced-custom-fields/'));
			}
			
			if ( !defined('CPTUI_VERSION') ) {
				$this->plugin->display_admin_warning(sprintf(__('The <a href="%s" target="_blank">Custom Post Type UI plugin</a> is required to manage the custom posts and the custom taxonomies.', 'fgd2wpp'), 'https://wordpress.org/plugins/custom-post-type-ui/'));
			}
		}
		
		/**
		 * Set the custom fields in an array
		 * 
		 * @since 3.4.0
		 */
		public function set_custom_fields() {
			$this->custom_fields = $this->get_acf_custom_fields();
		}
		
		/**
		 * Get the ACF custom fields
		 * 
		 * @since 3.4.0
		 * 
		 * @return array Fields
		 */
		private function get_acf_custom_fields() {
			global $wpdb;
			
			$fields = array();
			$sql = "
				SELECT p.post_name AS field_id, p.post_excerpt AS field_slug, pp.post_excerpt AS parent_slug
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->posts} pp ON pp.ID = p.post_parent AND pp.post_type = 'acf-field'
				WHERE p.post_type = 'acf-field'
			";
			$results = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $results as $row ) {
				$field_slug = $row['field_slug'];
				if ( !empty($row['parent_slug']) ) {
					// Subfield
					$field_slug = $row['parent_slug'] . '_' . $field_slug;
				}
				$fields[$field_slug] = $row['field_id'];
			}
			return $fields;
		}
		
		/**
		 * Check if the repeating fields are supported with the current ACF version
		 * 
		 * @return bool Repeating fields supported
		 */
		public function is_repeating_fields_supported() {
			return defined('ACF_PRO');
		}
		
		/**
		 * Get the field prefix
		 * 
		 * @return string Field prefix
		 */
		public function get_field_prefix() {
			return '';
		}
		
		/**
		 * Register a builtin post type
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular post type name
		 * @param string $plural Plural post type name
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 */
		public function register_builtin_post_type($post_type, $singular, $plural, $description, $taxonomies) {
			// Builtin post types are not registered with CPT UI
		}
		
		/**
		 * Register a post type on CPT UI
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular label
		 * @param string $plural Plural label
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 * @param array $parent_post_types Parent post types
		 */
		public function register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, $parent_post_types) {
			if ( function_exists('cptui_register_single_post_type') ) {
				$custom_post_types = get_option('cptui_post_types', array());
				if ( !is_array($custom_post_types) ) {
					$custom_post_types = array();
				}
				if ( is_numeric($post_type) ) {
					// The post type must not be entirely numeric
					$post_type = '_' . $post_type;
				}
				if ( empty($custom_post_types) || !isset($custom_post_types[$post_type]) ) {
					$cptui_post_type = array(
						'name' => $post_type,
						'label' => $plural,
						'singular_label' => $singular,
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'add_new' => 'Add New',
							'add_new_item' => "Add New $singular",
							'edit_item' => "Edit $singular",
							'new_item' => "New $singular",
							'view_item' => "View $singular",
							'search_items' => "Search $singular",
							'not_found' => "No $singular found",
							'not_found_in_trash' => "No $singular found in Trash",
							'parent_item_colon' => "Parent $singular",
							'all_items' => $plural,
						),
						'description' => $description,
						'public' => 'true',
						'publicly_queryable' => 'true',
						'show_ui' => 'true',
						'show_in_nav_menus' => 'true',
						'delete_with_user' => 'false',
						'show_in_rest' => 'true',
						'rest_base' => '',
						'rest_controller_class' => '',
						'has_archive' => 'true',
						'has_archive_string' => '',
						'exclude_from_search' => 'false',
						'capability_type' => 'post',
						'hierarchical' => 'false',
						'rewrite' => 'true',
						'rewrite_slug' => '',
						'rewrite_withfront' => 'true',
						'query_var' => 'true',
						'query_var_slug' => '',
						'menu_position' => '',
						'show_in_menu' => 'true',
						'show_in_menu_string' => '',
						'menu_icon' => '',
						'supports' => array('title', 'editor', 'thumbnail', 'author', 'custom-fields'),
						'taxonomies' => $taxonomies,
						'custom_supports' => '',
					);
					$custom_post_types = array_merge($custom_post_types, array($post_type => $cptui_post_type));
					update_option('cptui_post_types', $custom_post_types);
					cptui_register_single_post_type($cptui_post_type);
				}
			}
		}
		
		/**
		 * Register a taxonomy on CPT UI
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical) {
			if ( function_exists('cptui_register_single_taxonomy') ) {
				$custom_taxonomies = get_option('cptui_taxonomies', array());
				if ( !is_array($custom_taxonomies) ) {
					$custom_taxonomies = array();
				}
				if ( !isset($custom_taxonomies[$taxonomy]) ) {
					$cptui_taxonomy = array(
						'name' => $taxonomy,
						'label' => $plural,
						'singular_label' => $singular,
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'search_items' => "Search $plural",
							'popular_items' => "Popular $plural",
							'all_items' => "All $plural",
							'parent_item' => "Parent $singular",
							'parent_item_colon' => "Parent $singular:",
							'edit_item' => "Edit $singular",
							'view_item' => "View $singular",
							'update_item' => "Update $singular",
							'add_new_item' => "Add New $singular",
							'new_item_name' => "New $singular Name",
							'separate_items_with_commas' => "Separate $plural with commas",
							'add_or_remove_items' => "Add or remove $plural",
							'choose_from_most_used' => "Choose from the most used $plural",
							'not_found' => "No $plural found.",
							'no_terms' => "No $plural",
							'items_list_navigation' => "$plural list navigation",
							'items_list' => "$plural list",
							'menu_name' => $plural,
							'name_admin_bar' => $plural,
						),
						'description' => $description,
						'public' => 'true',
						'publicly_queryable' => 'true',
						'hierarchical' => $hierarchical? 'true': 'false',
						'show_ui' => 'true',
						'show_in_menu' => 'true',
						'show_in_nav_menus' => 'true',
						'query_var' => 'true',
						'query_var_slug' => '',
						'rewrite' => 'true',
						'rewrite_slug' => '',
						'rewrite_withfront' => 'true',
						'rewrite_hierarchical' => $hierarchical? 'true': 'false',
						'show_admin_column' => 'true',
						'show_in_rest' => 'true',
						'show_in_quick_edit' => 'true',
						'rest_base' => '',
						'rest_controller_class' => '',
						'meta_box_cb' => '',
						'default_term' => '',
						'object_types' => $post_types,
					);
					$custom_taxonomies = array_merge($custom_taxonomies, array($taxonomy => $cptui_taxonomy));
					update_option('cptui_taxonomies', $custom_taxonomies);
					cptui_register_single_taxonomy($cptui_taxonomy);
				}
			}
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
			if ( !empty($custom_fields) ) {
				// Create the ACF group
				$fields_group_id = $this->create_acf_group($post_type);

				// Create the ACF fields
				foreach ( $custom_fields as $field_slug => $field ) {
					if ( in_array($field_slug, array('body', 'excerpt')) ) {
						continue; // Don't register the body and excerpt fields
					}
					$custom_fields_count = apply_filters('fgd2wp_register_custom_post_field', 0, $field_slug, $field, $post_type, $fields_group_id); // Allow the add-ons to intercept the creation of the field
					if ( $custom_fields_count == 0 ) {
						$field_id = $this->create_acf5_field($field_slug, $field, $post_type, $fields_group_id);
						if ( !is_wp_error($field_id) ) {
							$fields_count++;
						}
					} else {
						$fields_count += $custom_fields_count;
					}
				}
			}
			return $fields_count;
		}
		
		/**
		 * Create the ACF fields group
		 * 
		 * @param string $group_name Group name
		 * @param string $entity post_type | taxonomy
		 * @return int Fields group ID
		 */
		private function create_acf_group($group_name, $entity='post_type') {
			$meta_key = '_fgd2wp_old_fields_group_name';
			
			// Check if the group already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $group_name);
			
			if ( empty($new_post_id) ) {
				// Create a new group
				$group_title = ucfirst($group_name) . ' ' . __('fields', 'fgd2wpp');
				$group_slug = 'group_' . uniqid();
				$post_excerpt = sanitize_title($group_title);
				$value = ($entity == 'user_form')? 'all' : $group_name; // "all" for a user fields group
				$content = array(
					'location' => array(
						array(
							array(
								'param' => $entity,
								'operator' => '==',
								'value' => $value,
							)
						)
					),
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'description' => '',
				);
				
				// Insert the post
				$new_post = array(
					'post_title'		=> $group_title,
					'post_name'			=> $group_slug,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $post_excerpt,
					'post_type'			=> 'acf-field-group',
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $group_name, true);
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Create an ACF field (version 5)
		 * 
		 * @param string $field_slug Field slug
		 * @param array $field Field data
		 * @param string $post_type Post type
		 * @param int $fields_group_id Fields group ID
		 * @return int Field ID
		 */
		public function create_acf5_field($field_slug, $field, $post_type, $fields_group_id) {
			$post_parent = $fields_group_id;
			$title = $field['label'];
			$module = isset($field['module'])? $field['module'] : '';
			$field_type = $this->map_acf_field_type($this->plugin->map_custom_field_type($field['type'], $field['label'], $module), $field);
			if ( isset($field['taxonomy']) ) {
				$field_slug = $field['taxonomy'] . '-' . $field_slug;
			}
			
			if ( $field_type == 'taxonomy' ) {
				// Don't import the taxonomy relationships as a field
				return;
			}
			
			$order = isset($field['order'])? $field['order'] : 0;
			
			// Repetitive fields
			if ( isset($field['repetitive']) && $field['repetitive'] && $this->is_repeating_fields_supported() && ($field['type'] != 'paragraphs') ) {
				$parent_id = $this->create_repeater_field($title, $field_slug, $order, $post_type, $fields_group_id);
				if ( !is_wp_error($parent_id) && ($parent_id != 0) ) {
					$post_parent = $parent_id;
					
					// Field collection or Paragraphs field
					if ( isset($field['collection']) ) {
						do_action('fgd2wp_post_insert_collection_field', $parent_id, $field['collection']);
						return;
					}

				}
			}
				
			// Content
			$content = array(
				'type' => $field_type,
				'instructions' => '',
				'required' => isset($field['required']) && !empty($field['required'])? 1 : 0,
				'default_value' => isset($field['default_value'][0]['value'])? $field['default_value'][0]['value'] : '',
				'placeholder' => isset($field['description'])? $field['description'] : '',
			);
			// Multiple select
			if ( isset($field['cardinality']) && ($field['cardinality'] != 1) ) {
				$content['multiple'] = 1;
			}
			// Choices
			if ( isset($field['options']) && !empty($field['options']) ) {
				if ( is_array($field['options']) ) {
					$choices = $field['options'];
				} else {
					$choices = array();
					$values = explode("\r", $field['options']);
					foreach ( $values as $item ) {
						$item = trim($item);
						list($item_key, $item_value) = explode('|', $item);
						$choices[$item_key] = $item_value;
					}
				}
				$content['choices'] = $choices;
			}
			
			// Post object
			if ( $field_type == 'post_object' ) {
				$content['post_type'] = array();
				if ( isset($field['referenceable_types']) ) {
					foreach ( $field['referenceable_types'] as $referenceable_type ) {
						$content['post_type'][] = $this->plugin->map_post_type($referenceable_type);
					}
				}
				$content['multiple'] = 0; // Multiple values are managed by the Repeater field
			}
			
			// Taxonomy
			if ( $field_type == 'taxonomy' ) {
				$content['taxonomy'] = '';
				if ( isset($field['referenceable_types']) ) {
					foreach ( $field['referenceable_types'] as $referenceable_type ) {
						$content['taxonomy'] = $this->plugin->map_taxonomy($referenceable_type);
					}
				}
			}
			
			return $this->insert_acf_field($title, $field_slug, $order, $post_type, $post_parent, $content);
		}
		
		/**
		 * Map the Drupal field type to an ACF field type
		 * 
		 * @param string $field_type Field type
		 * @param array $field Field
		 * @return string ACF field type
		 */
		public function map_acf_field_type($field_type, $field) {
			switch ( $field_type ) {
				case 'textfield':
					$acf_type = 'text';
					break;
				case 'numeric':
					$acf_type = 'number';
					break;
				case 'checkbox':
					$acf_type = 'true_false';
					break;
				case 'checkboxes':
					$acf_type = 'checkbox';
					break;
				case 'date':
					$acf_type = 'date_picker';
					break;
				case 'time':
					$acf_type = 'time_picker';
					break;
				case 'url':
					$acf_type = 'link';
					break;
				case 'color':
					$acf_type = 'color_picker';
					break;
				case 'video':
					$acf_type = 'url';
					break;
				case 'nodereference':
					if ( isset($field['target_type']) ) {
						switch ( $field['target_type'] ) {
							case 'taxonomy_term':
								$acf_type = 'taxonomy';
								break;
							case 'user':
								$acf_type = 'user';
								break;
							default:
								$acf_type = 'post_object';
						}
					} else {
						$acf_type = 'post_object';
					}
					break;
				case 'group':
					$acf_type = 'group';
					break;
				default:
					$acf_type = apply_filters('fgd2wp_map_acf_field_type', $field_type, $field);
			}
			return $acf_type;
		}
		
		/**
		 * Create a repeater field (ACF Pro)
		 * 
		 * @param string $title Field title
		 * @param string $field_slug Field slug
		 * @param int $order Order
		 * @param string $post_type Post type
		 * @param int $fields_group_id Fields group ID
		 * @return int Field ID
		 */
		private function create_repeater_field($title, $field_slug, $order, $post_type, $fields_group_id) {
			$content = array(
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => '',
				'max' => '',
				'layout' => 'table',
				'button_label' => '',
			);
			$field_slug = 'collection-' . $field_slug;
			
			return $this->insert_acf_field($title, $field_slug, $order, $post_type, $fields_group_id, $content);
		}
		
		/**
		 * Create an ACF field
		 * 
		 * @param string $title Field title
		 * @param string $field_slug Field slug
		 * @param int $order Order
		 * @param string $post_type Post type
		 * @param int $parent_id Parent ID
		 * @param array $content Content
		 * @return int Field ID
		 */
		public function insert_acf_field($title, $field_slug, $order, $post_type, $parent_id, $content) {
			$meta_key = '_fgd2wp_old_field_name';
			$meta_value = $post_type . '-' . $field_slug;
			
			// Check if the field already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $meta_value);
			
			if ( empty($new_post_id) ) {

				// Insert the post
				$field_key = 'field_' . uniqid();
				$new_post = array(
					'post_title'		=> $title,
					'post_name'			=> $field_key,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $field_slug,
					'post_type'			=> 'acf-field',
					'post_parent'		=> $parent_id,
					'menu_order'		=> $order,
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $meta_value, true); // To avoid importing the same field
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Register a custom taxonomy field
		 * 
		 * @param string $custom_field_name Custom field name
		 * @param array $custom_field_data Custom field data
		 */
		public function register_custom_taxonomy_field($custom_field_name, $custom_field_data) {
			// Create the ACF group
			$fields_group_id = $this->create_acf_group($this->plugin->map_taxonomy($custom_field_data['taxonomy']), 'taxonomy');
			// Create the field
			$field_slug = sanitize_title(preg_replace('/^field_/', '', $custom_field_data['field_name']));
			$this->create_acf5_field($field_slug, $custom_field_data, $custom_field_data['taxonomy'], $fields_group_id);
		}
		
		/**
		 * Register the user fields
		 * 
		 * @param array $custom_fields Custom user fields
		 * @return array Fields IDs
		 */
		public function register_custom_user_fields($custom_fields) {
			$fields_ids = array();
			// Create the ACF group
			$fields_group_id = $this->create_acf_group('user', 'user_form');
			foreach ( $custom_fields as $custom_field ) {
				// Create the field
				$field_slug = sanitize_title(preg_replace('/^field_/', '', $custom_field['field_name']));
				$fields_group_id = apply_filters('fgd2wp_user_parent_group_id', $fields_group_id, $custom_field);
				$field_id = $this->create_acf5_field($field_slug, $custom_field, 'user', $fields_group_id);
				if ( !empty($field_id) ) {
					$fields_ids[$field_slug] = $field_id;
				}
			}
			return $fields_ids;
		}
		
		/**
		 * Register the post types relationships
		 * 
		 * @param array $relationships Node Types Relationships
		 */
		public function register_post_types_relationships($relationships) {
			// CPT UI doesn't manage post types relationships
		}
		
		/**
		 * Get the custom post types
		 * 
		 * @return array Custom post types
		 */
		public function get_custom_post_types() {
			return get_option('cptui_post_types', array());
		}
		
		/**
		 * Get the post type name
		 * 
		 * @param array $post_type_object Post type object
		 * @return string Post type name
		 */
		public function get_post_type_name($post_type_object) {
			return $post_type_object['label'];
		}
		
		/**
		 * Get the custom taxonomies
		 * 
		 * @return array Custom taxonomies
		 */
		public function get_custom_taxonomies() {
			return get_option('cptui_taxonomies', array());
		}
		
		/**
		 * Get the taxonomy name
		 * 
		 * @param array $taxonomy_object Taxonomy object
		 * @return string Taxonomy name
		 */
		public function get_taxonomy_name($taxonomy_object) {
			return $taxonomy_object['label'];
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
			$index = 0;
			// Repeater field
			if ( isset($custom_field['repetitive']) && $custom_field['repetitive'] && $this->is_repeating_fields_supported() ) {
				$repeater_field_name = 'collection-' . $custom_field_name;
				$index = intval(get_post_meta($new_post_id, $repeater_field_name, true));
				$custom_field_name = $repeater_field_name . '_%d_' . $custom_field_name;
			}
			
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_values, $date, $new_post_id);
			foreach ( $meta_values as $meta_value ) {
				$meta_key = sprintf($custom_field_name, $index++);
				if ( is_scalar($meta_value) ) {
					$meta_value = addslashes($this->plugin->replace_media_shortcodes(stripslashes($meta_value)));
				}
				update_post_meta($new_post_id, $meta_key, $meta_value);
				if ( isset($this->custom_fields[$meta_key]) ) {
					update_post_meta($new_post_id, '_' . $meta_key, $this->custom_fields[$meta_key]);
				}
			}
			
			// Repeater field
			if ( isset($custom_field['repetitive']) && $custom_field['repetitive'] && $this->is_repeating_fields_supported() ) {
				// Update the last index of the repeater field
				update_post_meta($new_post_id, $repeater_field_name, $index);
				if ( isset($this->custom_fields[$repeater_field_name]) ) {
					update_post_meta($new_post_id, '_' . $repeater_field_name, $this->custom_fields[$repeater_field_name]);
				}
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
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_values);
			foreach ( $meta_values as $meta_value ) {
				update_term_meta($new_term_id, $meta_key, $meta_value);
				if ( isset($this->custom_fields[$meta_key]) ) {
					update_term_meta($new_term_id, '_' . $meta_key, $this->custom_fields[$meta_key]);
				}
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
			$meta_key = apply_filters('fgd2wp_get_user_meta_key', $custom_field_name, $custom_field);
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_values, $date);
			foreach ( $meta_values as $meta_value ) {
				update_user_meta($new_user_id, $meta_key, $meta_value);
				if ( isset($this->custom_fields[$meta_key]) ) {
					update_user_meta($new_user_id, '_' . $meta_key, $this->custom_fields[$meta_key]);
				}
			}
		}
		
		/**
		 * Convert custom field values to meta values
		 * 
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 * @param int $new_post_id WordPress post ID
		 * @return array Meta values
		 */
		private function convert_custom_field_to_meta_values($custom_field, $custom_field_values, $date='', $new_post_id='') {
			$meta_values = array();
			$module = isset($custom_field['module'])? $custom_field['module'] : '';
			$custom_field_type = $this->map_acf_field_type($this->plugin->map_custom_field_type($custom_field['type'], $custom_field['label'], $module), $custom_field);
			switch ( $custom_field_type ) {
				// Date
				case 'date_picker':
					foreach ( $custom_field_values as $custom_field_value ) {
						if ( is_array($custom_field_value) ) {
							foreach ( $custom_field_value as $subvalue ) {
								$meta_values[] = $this->convert_to_date($subvalue);
							}
						} else {
							$meta_values[] = $this->convert_to_date($custom_field_value);
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
								// Assign the media URL to the postmeta
								if ( !empty($new_post_id) ) {
									$set_featured_image = ($this->plugin->plugin_options['featured_image'] == 'featured') && !$this->plugin->thumbnail_is_set;
									$this->plugin->add_post_media($new_post_id, array('post_date' => $file_date), array($attachment_id), $set_featured_image); // Attach the media to the post
									$this->plugin->thumbnail_is_set = true;
								}
								// Set the field value
								$meta_values[] = $attachment_id;
							}
						}
					}
					break;

				// Link
				case 'link':
					foreach ( $custom_field_values as $custom_field_value ) {
						$title = isset($custom_field_value['title'])? $custom_field_value['title'] : (isset($custom_field_value['filename'])? $custom_field_value['filename'] : '');
						$url = isset($custom_field_value['url'])? $custom_field_value['url'] : (isset($custom_field_value['uri'])? $custom_field_value['uri'] : '');
						$meta_values[] = array(
							'title' => $title,
							'url' => $this->plugin->get_path_from_uri($url),
							'target' => '_blank',
						);
					}
					break;
					
				// Checkbox or select boxes
				case 'checkbox':
				case 'select':
					if ( isset($custom_field['options']) && is_array($custom_field['options']) ) {
						$options = array_keys($custom_field['options']);
						$acf_values = array();
						foreach ( $custom_field_values as $values ) {
							if ( is_array($values) ) {
								foreach ( $values as $value ) {
									if ( in_array($value, $options) ) {
										$acf_values[] = $value;
									}
								}
							} elseif ( is_scalar($values) ) {
								if ( in_array($values, $options) ) {
									$acf_values[] = $values;
								}
							}
						}
						$meta_values[] = $acf_values;
					}
					break;
				
				// Post object
				case 'post_object':
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								foreach ( $custom_field_value as &$value ) {
									$value = $value;
								}
								$acf_value = implode("\n", $custom_field_value);
							} else {
								$acf_value = $custom_field_value;
							}
							$meta_values[] = $acf_value;
						}
					} else {
						$meta_values[] = $custom_field_values;
					}
					break;
				
				// User
				case 'user':
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								foreach ( $custom_field_value as $value ) {
									if ( isset($this->plugin->imported_users[$value]) ) {
										$meta_values[] = $this->plugin->imported_users[$value];
									}
								}
							} else {
								if ( isset($this->plugin->imported_users[$custom_field_value]) ) {
									$meta_values[] = $this->plugin->imported_users[$custom_field_value];
								}
							}
						}
					}
					break;
				
				default:
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								$acf_value = implode("<br />\n", $custom_field_value);
							} else {
								$acf_value = $custom_field_value;
							}
							$acf_value = $this->plugin->replace_media_links($acf_value, $date);

							$meta_values[] = $acf_value;
						}
					} else {
						$meta_values[] = $custom_field_values;
					}
			}
			return apply_filters('fgd2wp_convert_custom_field_to_meta_values', $meta_values, $custom_field, $custom_field_values, $new_post_id, $date);
		}
		
		/**
		 * Convert a date with a MySQL format
		 * 
		 * @param mixed $date Date
		 * @return date Date
		 */
		private function convert_to_date($date) {
			if ( is_numeric($date) ) {
				$formatted_date = date('Y-m-d H:i:s', $date);
			} else {
				$formatted_date = preg_replace('/-00/', '-01', $date); // For dates with month=00 or day=00
				$formatted_date = preg_replace('/T/', ' ', $formatted_date); // For ISO date
			}
			return $formatted_date;
		}
		
		/**
		 * Set the user picture
		 * 
		 * @param int $user_id User ID
		 * @param int $image_id Image ID
		 */
		public function set_user_picture($user_id, $image_id) {
			add_user_meta($user_id, $this->get_field_prefix() . 'picture', $image_id);
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
			$this->set_custom_post_field($post_id, $custom_field_name, $custom_field, array($related_id));
		}
		
	}
}
