<?php

/**
 * Interface for Custom post types implementations
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      3.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !interface_exists('FG_Drupal_to_WordPress_iCPT', false) ) {

	/**
	 * iCPT interface
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	interface FG_Drupal_to_WordPress_iCPT {
		
		public function check_required_plugins();
		public function is_repeating_fields_supported();
		public function get_field_prefix();
		public function register_builtin_post_type($post_type, $singular, $plural, $description, $taxonomies);
		public function register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, $parent_post_types);
		public function register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical);
		public function register_custom_post_fields($custom_fields, $post_type);
		public function register_custom_taxonomy_field($custom_field_name, $custom_field_data);
		public function register_custom_user_fields($custom_fields);
		public function register_post_types_relationships($relationships);
		public function get_custom_post_types();
		public function get_post_type_name($post_type_object);
		public function get_custom_taxonomies();
		public function get_taxonomy_name($taxonomy_object);
		public function set_custom_post_field($new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date);
		public function set_custom_term_field($new_term_id, $custom_field_name, $custom_field, $custom_field_values);
		public function set_custom_user_field($new_user_id, $custom_field_name, $custom_field, $custom_field_values, $date);
		public function set_user_picture($user_id, $image_id);
		public function set_post_relationship($post_id, $custom_field_name, $related_id, $custom_field, $relationship_slug);
		
	}
}
