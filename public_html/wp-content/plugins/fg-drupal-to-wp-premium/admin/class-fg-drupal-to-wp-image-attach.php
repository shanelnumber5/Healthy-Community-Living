<?php

/**
 * Image Attach module
 *
 * Import the images from the Image Attach Drupal module
 * 
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      1.46.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Image_Attach', false) ) {

	/**
	 * Image Attach class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Image_Attach {

		private $plugin;
		private $image_attach_data_exist = false;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Test if the image_attach table exists
		 * 
		 * @return bool image_attach table exists
		 */
		public function test_image_attach_data() {
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Version 6
				$this->image_attach_data_exist = $this->plugin->table_exists('image');
			} else {
				// Version 7+
				$this->image_attach_data_exist = $this->plugin->table_exists('image_attach');
			}
		}
		
		/**
		 * Import the images attached to the node
		 * 
		 * @param array $media Media
		 * @param array $node Node data
		 * @param date $post_date Post date
		 * @return array Media
		 */
		public function import_images($media, $node, $post_date) {
			if ( $this->image_attach_data_exist && !$this->plugin->plugin_options['skip_media'] ) {
				$images = $this->get_images($node['nid']);
				// Import the images
				foreach ( $images as $image ) {
					$filename = $image['uri'];
					$image_alt = preg_replace('/[-_]/', ' ', preg_replace('/\..*$/', '', basename($filename)));
					$attachment_id = $this->plugin->import_media($image_alt, $filename, $post_date, array(), array('ref' => 'node ID=' . $node['nid']));
					if ( $attachment_id !== false ) {
						$this->plugin->media_count++;
						$media[$filename] = $attachment_id;
					}
				}
			}
			return $media;
		}
		
		/**
		 * Get the images
		 * 
		 * @param int $nid Node ID
		 * @return array Images
		 */
		private function get_images($nid) {
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
					// Version 5
					$timestamp_field = "'' AS timestamp";
				} else {
					// Version 6
					$timestamp_field = 'f.timestamp';
				}
				$sql = "
					SELECT f.fid, f.filename, f.filepath AS uri, f.filemime, $timestamp_field
					FROM ${prefix}image i
					INNER JOIN ${prefix}files f ON f.fid = i.fid
					WHERE i.nid = '$nid'
					AND i.image_size = '_original'
				";
			} else {
				// Version 7+
				$table_name = 'file_managed';
				$uri_field = 'uri';
				if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
					// Version 7
					$timestamp_field = 'timestamp';
				} else {
					// Version 8
					$timestamp_field = 'created AS timestamp';
				}
				$sql = "
					SELECT f.fid, f.filename, f.${uri_field}, f.filemime, f.${timestamp_field}
					FROM ${prefix}image_attach ia
					INNER JOIN ${prefix}image i ON i.nid = ia.iid
					INNER JOIN ${prefix}${table_name} f ON f.fid = i.fid
					WHERE ia.nid = '$nid'
					AND i.image_size = '_original'
					ORDER BY ia.weight
				";
			}
			$images = $this->plugin->drupal_query($sql);
			return $images;
		}
		
	}
}
