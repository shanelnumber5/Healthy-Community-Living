<?php

/**
 * Video Embed Field module
 *
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      1.70.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Video_Embed_Field', false) ) {

	/**
	 * Video Embed Field class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Video_Embed_Field {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Get the Video Embed custom fields
		 * 
		 * @param array $custom_fields Custom fields
		 * @param string $field Field data
		 * @param array $data_storage Custom field data storage
		 * @param array $data Custom field data
		 * @return array Custom fields
		 */
		public function get_video_embed_custom_fields($custom_fields, $field, $data_storage, $data) {
			if ( in_array($field['module'], array('video_embed_field', 'youtube', 'vimeo')) ) {
				$field_slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin(remove_accents($data['label'])));
				
				// Get the first column only
				$columns_keys = array_keys($field['columns']);
				if ( count($columns_keys) > 0 ) {
					$first_column = $columns_keys[0];
					$field['columns'] = array($first_column => $field['columns'][$first_column]);
				}
				
				$custom_fields[$field_slug] = $field;
			}
			return $custom_fields;
		}
		
	}
}
