<?php

/**
 * Media module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      2.13.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Media', false) ) {

	/**
	 * Media class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Media {
		
		private $plugin;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;
		}
		
		/**
		 * Import the Drupal Media media
		 * 
		 * @param string $content Content
		 * @return string Content
		 */
		public function import_media($content) {
			$matches = array();
			$matches2 = array();
			$embed_tags = apply_filters('fgd2wp_embed_tags', array('drupal-media'));
			foreach ( $embed_tags as $embed_tag ) {
				if ( preg_match_all("#<$embed_tag (.*?)</$embed_tag>#", $content, $matches) ) {
					foreach ( $matches[1] as $tag ) {
						if ( preg_match('/uuid="(.*?)"/', $tag, $matches2) ) {
							$uuid = $matches2[1];
							$media = $this->get_media($uuid);
							if ( !empty($media) ) {
								$value = implode("\n", $this->get_media_values($media));
								if ( !empty($value) ) {
									$content = preg_replace("#<$embed_tag (.*?)uuid=\"$uuid\"(.*?)</$embed_tag>#", $value, $content);
								}
							}
						}
					}
				}
			}
			return $content;
		}
		
		/**
		 * Get the Media object
		 * 
		 * @param string $uuid UUID
		 * @return array Media data
		 */
		private function get_media($uuid) {
			$media = array();

			$prefix = $this->plugin->plugin_options['prefix'];
			if ( $this->plugin->table_exists('media') ) {
				$sql = "
					SELECT m.mid, m.bundle
					FROM ${prefix}media m
					WHERE m.uuid = '$uuid'
					LIMIT 1
				";
				$result = $this->plugin->drupal_query($sql);
				if ( count($result) > 0 ) {
					$media = $result[0];
				}
			}
			return $media;
		}
		
		/**
		 * Get the media value
		 * 
		 * @param array $media Media data
		 * @return array Values
		 */
		public function get_media_values($media) {
			$values = array();

			$prefix = $this->plugin->plugin_options['prefix'];
			$bundle = $media['bundle'];
			$mid = $media['mid'];
			if ( !empty($bundle) && !empty($mid) ) {
				$field_names = $this->get_media_field_names($bundle);
				foreach ( $field_names as $field_name ) {
					$table_name = 'media__' . $field_name;
					$value_field_name = $this->guess_column_name($table_name, $field_name);
					$target_field_name = $field_name . '_target_id';
					if ( !empty($value_field_name) ) {
						// Value field
						$sql = "
							SELECT m.$value_field_name AS value
							FROM ${prefix}$table_name m
							WHERE m.entity_id = '$mid'
							LIMIT 1
						";
						$result = $this->plugin->drupal_query($sql);
						if ( count($result) > 0 ) {
							$value = $result[0]['value'];
							if ( !empty($value) ) {
								if ( preg_match('/^http/', $value) ) {
									$value = "[embed]{$value}[/embed]"; // Embed hyperlinks
								}
								$values[] = $value;
							}
						}
					} elseif ( $this->plugin->column_exists($table_name, $target_field_name) ) {
						// Target ID field
						$extra_columns = array();
						switch ( $bundle ) {
							case 'image':
								$alt_field = $field_name . '_alt AS alt';
								$title_field = $field_name . '_title AS title';
								$extra_columns = array($alt_field, $title_field);
								break;
						}
						$extra_columns_str = '';
						foreach ( $extra_columns as $extra_column ) {
							$extra_columns_str .= ', ' . $extra_column;
						}
						$sql = "
							SELECT fm.filename, fm.uri$extra_columns_str
							FROM ${prefix}$table_name m
							INNER JOIN ${prefix}file_managed fm ON fm.fid = m.$target_field_name
							WHERE m.entity_id = '$mid'
							LIMIT 1
						";
						$result = $this->plugin->drupal_query($sql);
						if ( count($result) > 0 ) {
							$uri = $this->plugin->get_path_from_uri($result[0]['uri']);
							switch ( $bundle ) {
								case 'image':
									$values[] = sprintf('<img src="%s" alt="%s" title="%s" />', $uri, $result[0]['alt'], $result[0]['title']);
									break;
								default:
									$values[] = $result[0]['uri'];
							}
						}
					}
				}
			}
			return $values;
		}
		
		/**
		 * Get the field names related to a bundle
		 * 
		 * @param string $bundle Bundle
		 * @return array Field names
		 */
		private function get_media_field_names($bundle) {
			$fields = array();
			$tables = $this->plugin->get_drupal_config_like("field.field.media.$bundle.%");
			foreach ( $tables as $table ) {
				if ( $table['bundle'] == $bundle ) {
					if ( $this->does_field_type_match_bundle($table['field_type'], $bundle) ) {
						$fields[] = $table['field_name'];
					}
				}
			}
			return $fields;
		}
		
		/**
		 * Does the field type match the bundle?
		 * 
		 * @since 3.5.3
		 * 
		 * @param string $field_type Field type
		 * @param type $bundle Bundle
		 * @return boolean
		 */
		private function does_field_type_match_bundle($field_type, $bundle) {
			$match = true;
			switch ( $bundle ) {
				case 'image':
					$match = ($field_type == 'image'); // If the bundle = image, keep only the image fields
					break;
				case 'video':
					$match = ($field_type == 'video_embed_field'); // If the bundle = video, keep only the video fields
					break;
				default:
					$match = true;
			}
			return $match;
		}
		
		/**
		 * Guess the column name
		 * 
		 * @since 2.35.0
		 * 
		 * @param string $table_name Table name
		 * @param string $field_name Field name
		 * @return string Column name
		 */
		private function guess_column_name($table_name, $field_name) {
			$suffixes = array('_value', '_uri');
			foreach ( $suffixes as $suffix ) {
				$column = $field_name . $suffix;
				if ( $this->plugin->column_exists($table_name, $column) ) {
					return $column;
				}
			}
			return '';
		}
		
		/**
		 * Get the media caption
		 * 
		 * @since 3.8.0
		 * 
		 * @param array $custom_field_values Values
		 * @return array Values
		 */
		public function get_caption($custom_field_values) {
			foreach ( $custom_field_values as &$value ) {
				$caption_fieldname = 'field_caption';
				if ( isset($value['fid']) && $this->plugin->column_exists('field_data_' . $caption_fieldname, $caption_fieldname . '_value')) {
					$prefix = $this->plugin->plugin_options['prefix'];
					$sql = "
						SELECT c.{$caption_fieldname}_value AS caption
						FROM ${prefix}field_data_{$caption_fieldname} c
						WHERE c.entity_id = {$value['fid']}
						AND c.deleted = 0
						ORDER BY c.delta
						LIMIT 1
					";
					$results = $this->plugin->drupal_query($sql);
					if ( count($results) > 0 ) {
						$value['caption'] = $results[0]['caption'];
					}
				}
			}
			return $custom_field_values;
		}
		
	}
}
