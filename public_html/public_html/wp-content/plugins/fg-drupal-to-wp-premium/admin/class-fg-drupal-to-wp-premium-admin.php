<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 * @author     Frédéric GILLES
 */
class FG_Drupal_to_WordPress_Premium_Admin extends FG_Drupal_to_WordPress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $premium_options = array();				// Options specific for the Premium version
	
	public $imported_users = array();				// List of imported users
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		parent::__construct($plugin_name, $version);
		
		$this->faq_url = 'https://www.fredericgilles.net/fg-drupal-to-wordpress/faq/';

	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		if ( !defined('WP_CLI') ) { // deactivate_plugins() doesn't work with WP CLI on Windows
			$this->deactivate_free_version();
		}

		parent::init();
	}

	/**
	 * Deactivate the free version of FG Drupal to WordPress to avoid conflicts between both plugins
	 */
	private function deactivate_free_version() {
		deactivate_plugins( 'fg-drupal-to-wp/fg-drupal-to-wp.php' );
	}
	
	/**
	 * Set the Premium options
	 * 
	 * @since 3.0.2
	 */
	public function set_premium_options() {
		// Default options values
		$this->premium_options = array(
			'cpt_format'				=> 'acf',
			'unicode_usernames'			=> false,
			'url_redirect'				=> true,
			'skip_taxonomies'			=> false,
			'skip_nodes'				=> false,
			'nodes_to_skip'				=> array(),
			'skip_users'				=> false,
			'skip_menus'				=> false,
			'skip_comments'				=> false,
			'skip_blocks'				=> false,
			'skip_redirects'			=> false,
		);
		$this->premium_options = apply_filters('fgd2wpp_post_init_premium_options', $this->premium_options);
		$options = get_option('fgd2wpp_options');
		if ( is_array($options) ) {
			$this->premium_options = array_merge($this->premium_options, $options);
		}
	}
	
	/**
	 * Get the WP options name
	 * 
	 * @since 2.0.0
	 * 
	 * @param array $option_names Option names
	 * @return array Option names
	 */
	public function get_option_names($option_names) {
		$option_names = parent::get_option_names($option_names);
		$option_names[] = 'fgd2wpp_options';
		return $option_names;
	}

	/**
	 * Add information to the admin page
	 * 
	 * @param array $data
	 * @return array
	 */
	public function process_admin_page($data) {
		$data['title'] = __('Import Drupal Premium', $this->plugin_name);
		$data['description'] = __('This plugin will import articles, stories, pages, images, categories, tags, users and comments from a Drupal database into WordPress.<br />Compatible with Drupal versions 4, 5, 6, 7, 8 and 9.', $this->plugin_name);
		$data['description'] .= "<br />\n" . sprintf(__('For any issue, please read the <a href="%s" target="_blank">FAQ</a> first.', $this->plugin_name), $this->faq_url);

		// Premium options
		foreach ( $this->premium_options as $key => $value ) {
			$data[$key] = $value;
		}
		// Partial import nodes
		$data['partial_import_nodes'] = get_option('fgd2wp_partial_import_nodes_html');
		
		return $data;
	}

	/**
	 * Save the Premium options
	 *
	 */
	public function save_premium_options() {
		$this->premium_options = array_merge($this->premium_options, $this->validate_form_premium_info());
		update_option('fgd2wpp_options', $this->premium_options);
	}

	/**
	 * Validate POST info
	 *
	 * @return array Form parameters
	 */
	private function validate_form_premium_info() {
		$args = array(
			'cpt_format'				=> FILTER_SANITIZE_STRING,
			'unicode_usernames'			=> FILTER_VALIDATE_BOOLEAN,
			'url_redirect'				=> FILTER_VALIDATE_BOOLEAN,
			'skip_taxonomies'			=> FILTER_VALIDATE_BOOLEAN,
			'skip_nodes'				=> FILTER_VALIDATE_BOOLEAN,
			'nodes_to_skip'				=> array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_REQUIRE_ARRAY),
			'skip_users'				=> FILTER_VALIDATE_BOOLEAN,
			'skip_menus'				=> FILTER_VALIDATE_BOOLEAN,
			'skip_comments'				=> FILTER_VALIDATE_BOOLEAN,
			'skip_blocks'				=> FILTER_VALIDATE_BOOLEAN,
			'skip_redirects'			=> FILTER_VALIDATE_BOOLEAN,
		);
		$inputs = filter_input_array(INPUT_POST, $args);
		$inputs = apply_filters('fgd2wpp_validate_form_premium_info', $inputs);
		return $inputs;
	}
	
	/**
	 * Delete all the Yoast SEO data
	 * 
	 * @since 2.1.0
	 * 
	 * @global object $wpdb WPDB object
	 * @param string $action Action
	 */
	public function delete_yoastseo_data($action) {
		global $wpdb;
		if ( $action == 'all' ) {
			$wpdb->hide_errors();
			$sql_queries = array();
			
			// Delete the Yoast SEO tables
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_indexable";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_indexable_hierarchy";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_migrations";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_primary_term";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_seo_links";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}yoast_seo_meta";
			
			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$wpdb->query($sql);
				}
			}
		}
	}
	
	/**
	 * Replace media shortcodes in the node
	 *
	 * @param array $node Node
	 * @return array Node
	 */
	public function replace_media_shortcodes_in_node($node) {
		if ( !$this->plugin_options['skip_media'] ) {
			if ( isset($node['body_summary']) ) {
				$node['body_summary'] = $this->replace_media_shortcodes($node['body_summary']);
			}
			if ( isset($node['body_value']) ) {
				$node['body_value'] = $this->replace_media_shortcodes($node['body_value']);
			}
		}
		return $node;
	}
	
	/**
	 * Replace media shortcodes in the content
	 *
	 * @param string $content Content
	 * @return string Processed content
	 */
	public function replace_media_shortcodes($content) {
		$matches = array();
		$images_data = array();
		$shortcodes_to_replace = array();
		
		// Search the shortcodes
		if ( preg_match_all('/\[\[(\{.*?"type":"media".*?\})\]\]/', $content, $matches, PREG_SET_ORDER) ) {
			foreach ( $matches as $match ) {
				$shortcodes_to_replace[] = $match[0];
				$shortcode = $match[1];
				$images_data[] = json_decode($shortcode, ARRAY_A);
			}
		}
		if ( preg_match_all('/\[img_assist.*?\|([fn]id)=(\d+).*?\|(?:alt|desc)=(.*?)(?:\|.*?)?\]/', $content, $matches, PREG_SET_ORDER) ) {
			foreach ( $matches as $match ) {
				$shortcodes_to_replace[] = $match[0];
				$key = $match[1];
				$id = $match[2];
				$fid = ($key == 'fid')? $id : $this->get_original_image_fid($id);
				if ( !empty($fid) ) {
					$images_data[] = array(
						'type' => 'media',
						'fid' => $fid,
						'attributes' => array(
							'alt' => $match[3],
						)
					);
				}
			}
		}
		
		// Replace the shortcodes in the content
		foreach ( $images_data as $index => $image_data ) {
			$image = $this->get_image($image_data['fid']);
			if ( !empty($image) ) {
				$filename = $this->get_path_from_uri($image['uri']);
				if ( preg_match('/^video/', $image['filemime']) ) {
					// Video
					$replacement = $filename;
				} else {
					// Image
					$alt = isset($image_data['alt'])? $image_data['alt'] : '';
					$width_assertion = isset($image_data['attributes']['width']) ? ' width="' . $image_data['attributes']['width'] . '"' : '';
					$height_assertion = isset($image_data['attributes']['height']) ? ' height="' . $image_data['attributes']['height'] . '"' : '';
					$style_assertion = isset($image_data['attributes']['style']) ? ' style="' . $image_data['attributes']['style'] . '"' : '';
					$img_src = '<img src="' . $filename . '" alt="' . $alt . '"' . $width_assertion . $height_assertion . $style_assertion . ' />';
					$caption = $this->get_image_caption($image_data);
					if ( !empty($caption) ) {
						// With caption
						$replacement = '<figure class="wp-caption alignnone">';
						$replacement .= $img_src;
						$replacement .= '<figcaption class="wp-caption-text">' . $caption . '</figcaption>';
						$replacement .= '</figure>';
					} else {
						// Without caption
						$replacement = $img_src;
					}
				}
				$content = preg_replace('#' . preg_quote($shortcodes_to_replace[$index]) . '#', $replacement, $content);
			}
		}
		return $content;
	}
	
	/**
	 * Import the media from the content and replace the media links in the content
	 * 
	 * @param string $content Content
	 * @param date $date Date
	 * @return string Content
	 */
	public function replace_media_links($content, $date) {
		if ( !$this->plugin_options['skip_media'] ) {
			// Import the media
			$result = $this->import_media_from_content($content, $date);
			$post_media = $result['media'];
			$this->media_count += $result['media_count'];

			// Replace the media links
			$content = $this->process_content($content, $post_media);
		}
		return $content;
	}

	/**
	 * Get the original image fid from a nid
	 * 
	 * @since 1.35.0
	 * 
	 * @param int $nid Image nid
	 * @return int Image fid
	 */
	private function get_original_image_fid($nid) {
		$fid = 0;
		$prefix = $this->plugin_options['prefix'];
		if ( version_compare($this->drupal_version, '7', '<') ) {
			// Drupal 6
			$sql = "
				SELECT f.fid
				FROM ${prefix}files f
				INNER JOIN ${prefix}image i ON i.fid = f.fid
				WHERE i.nid = '$nid'
				AND i.image_size = '_original'
			";
		} else {
			// Drupal 7+
			$sql = "
				SELECT f.fid
				FROM ${prefix}file_managed f
				INNER JOIN ${prefix}file_usage u ON u.fid = f.fid
				WHERE u.id = '$nid'
				AND u.type = 'node'
			";
		}
		$result = $this->drupal_query($sql);
		if ( isset($result[0]) ) {
			$fid = $result[0]['fid'];
		}
		return $fid;
	}
	
	/**
	 * Get the image data
	 * 
	 * @param int $fid File ID
	 * @return array Image data
	 */
	public function get_image($fid) {
		$image = array();

		$prefix = $this->plugin_options['prefix'];

		if ( version_compare($this->drupal_version, '7', '<') ) {
			// Drupal 6
			$table_name = 'files';
			$uri_field = 'filepath AS uri';
		} else {
			// Drupal 7+
			$table_name = 'file_managed';
			$uri_field = 'uri';
		}
		if ( version_compare($this->drupal_version, '6', '<') ) {
			// Drupal 5 and less
			$timestamp_field = "'' AS timestamp";
		} elseif ( version_compare($this->drupal_version, '8', '<') ) {
			// Drupal 6 and 7
			$timestamp_field = 'f.timestamp';
		} else {
			// Drupal 8
			$timestamp_field = 'f.created AS timestamp';
		}
		$sql = "
			SELECT f.fid, f.filename, f.$uri_field, f.filemime, $timestamp_field
			FROM ${prefix}$table_name f
			WHERE f.fid = '$fid'
			LIMIT 1
		";
		$result = $this->drupal_query($sql);
		if ( isset($result[0]) ) {
			$image = $result[0];
		}
		return $image;
	}
	
	/**
	 * Get the image caption
	 * 
	 * @since 3.7.0
	 * 
	 * @param array $image_data Image data
	 * @return string Caption
	 */
	private function get_image_caption($image_data) {
		$caption = '';
		if ( isset($image_data['fields']['field_caption[und][0][value]']) ) {
			$caption = $image_data['fields']['field_caption[und][0][value]'];
		}
		return $caption;
	}
	
	/**
	 * Get the referenceable types from the field settings
	 * 
	 * @since 1.23.1
	 * 
	 * @param string $settings Field settings
	 * @return array Referenceable types
	 */
	public function get_referenceable_types($settings) {
		$types = array();
		if ( isset($settings['settings']) ) {
			$settings = $settings['settings']; // Drupal 7+
		}
		if ( isset($settings['referenceable_types']) && is_array($settings['referenceable_types']) ) {
			foreach ( $settings['referenceable_types'] as $key => $value ) {
				if ( !empty($value) ) {
					$types[] = $key;
				}
			}
		}
		$types = apply_filters('fgd2wp_get_referenceable_types', $types, $settings);
		return $types;
	}
	
	/**
	 * Get the table name and the columns where the field is stored in the Drupal database
	 * 
	 * @since 1.50.0
	 * 
	 * @param string $field_name Field name
	 * @param array $data Field config data
	 * @param string $field_type Field type
	 * @return array [table_name, columns]
	 */
	public function get_drupal7_storage_location($field_name, $data, $field_type) {
		$table_name = '';
		$columns = array();

		if ( isset($data['storage']['details']['sql']['FIELD_LOAD_CURRENT']) ) {
			$tables = $data['storage']['details']['sql']['FIELD_LOAD_CURRENT'];
			$table_names = array_keys($tables);
			if ( count($table_names) > 0 ) {
				$table_name = $table_names[0];
				$columns = $tables[$table_name];
			}
		} else {
			// Get the default values
			$default_table_name = 'field_data_' . $field_name;
			switch ( $field_type ) {
				case 'image':
				case 'image_image':
				case 'image_miw':
					$default_columns = array(
						'fid' => $field_name . '_fid',
						'alt' => $field_name . '_alt',
						'title' => $field_name . '_title',
						'width' => $field_name . '_width',
						'height' => $field_name . '_height',
					);
					break;

				case 'file':
				case 'media':
					$default_columns = array(
						'fid' => $field_name . '_fid',
						'display' => $field_name . '_display',
						'description' => $field_name . '_description',
					);
					break;

				case 'node_reference':
				case 'node_reference_autocomplete':
					$default_columns = array(
						'target_id' => $field_name . '_nid',
					);
					break;

				case 'entityreference':
				case 'entityreference_autocomplete':
					$default_columns = array(
						'target_id' => $field_name . '_target_id',
					);
					break;

				case 'link':
				case 'link_field':
					$default_columns = array(
						'url' => $field_name . '_url',
						'title' => $field_name . '_title',
					);
					break;

				case 'video_embed_field_video':
					$default_columns = array(
						'url' => $field_name . '_video_url',
					);
					break;

				default:
					$default_columns = array(
						'value' => $field_name . '_value',
					);
			}
			$default_columns = apply_filters('fgd2wp_get_field_columns', $default_columns, $default_table_name, $data, $field_type);
			
			// Check if the table name and the columns exist
			if ( $this->table_exists($default_table_name) ) {
				$existing_default_columns = array();
				foreach ( $default_columns as $column_key => $column_name ) {
					if ( $this->column_exists($default_table_name, $column_name) ) {
						$existing_default_columns[$column_key] = $column_name;
					}
				}
				if ( !empty($existing_default_columns) ) {
					$table_name = $default_table_name;
					$columns = $existing_default_columns;
				}
			}
		}
		return array($table_name, $columns);
	}

	/**
	 * Get the table name and the columns where the field is stored in the Drupal 8 database
	 * 
	 * @since 1.75.0
	 * 
	 * @param string $field_name Field name
	 * @param string $entity_type Entity Type (node | media | paragraph)
	 * @return array Storage
	 */
	public function get_drupal8_storage($field_name, $entity_type = 'node') {
		$data_storage = array();
		$config = $this->get_drupal_config_like("field.storage.$entity_type.$field_name");
		if ( !empty($config) ) {
			$data_storage = array_shift($config);
		}
		return $data_storage;
	}
	
	/**
	 * Get the node custom field values
	 * 
	 * @param array $node Node
	 * @param array $custom_field Custom field
	 * @param string $entity_type Entity type (node, media)
	 * @return array Custom field values
	 */
	public function get_node_custom_field_values($node, $custom_field, $entity_type='node') {
		return $this->get_custom_field_values($node['nid'], $node, $custom_field, $entity_type);
	}

	/**
	 * Get the term custom field values
	 * 
	 * @since 1.40.0
	 * 
	 * @param array $term Term
	 * @param array $custom_field Custom field
	 * @return array Custom field values
	 */
	public function get_term_custom_field_values($term, $custom_field) {
		return $this->get_custom_field_values($term['tid'], $term, $custom_field, 'taxonomy_term');
	}

	/**
	 * Get the user custom field values
	 * 
	 * @since 1.47.0
	 * 
	 * @param array $user User
	 * @param array $custom_field Custom field
	 * @return array Custom field values
	 */
	public function get_user_custom_field_values($user, $custom_field) {
		$entity_id = apply_filters('fgd2wp_get_user_entity_id', $user['uid'], $custom_field);
		return $this->get_custom_field_values($entity_id, $user, $custom_field, $custom_field['entity_type']);
	}

	/**
	 * Get the custom field values
	 * 
	 * @param int $entity_id Entity ID
	 * @param array $entity Entity (Node, Term, User)
	 * @param array $custom_field Custom field
	 * @param array $entity_type (node | taxonomy_term | user | field_collection_item | paragraphs_item)
	 * @return array Custom field values
	 */
	public function get_custom_field_values($entity_id, $entity, $custom_field, $entity_type) {
		$custom_field_values = array();
		$node_type = isset($entity['type'])? $entity['type'] : '';
		if ( isset($custom_field['table_name']) && !empty($custom_field['columns']) ) {
			$field_value_name = $custom_field['field_name'];
			$table_name = $custom_field['table_name'];
			if ( $this->table_exists($table_name) ) {
				$columns = $this->get_columns_list($custom_field['columns']);
				$extra_criteria = '';
				$extra_joins = '';
				$prefix = $this->plugin_options['prefix'];
				$entity_field = 'entity_id';

				if ( version_compare($this->drupal_version, '7', '<') ) {
					// Drupal 6
					$order_by = '';
				} else {
					// Drupal 7 & 8
					$order_by = 'ORDER BY f.delta';
					if ( version_compare($this->drupal_version, '8', '<') ) {
						// Drupal 7
						$extra_criteria = "AND f.entity_type = '$entity_type'";
					}
					$columns .= ', f.delta';
				}
				$module = isset($custom_field['module'])? $custom_field['module'] : '';
				$field_type = $this->map_custom_field_type($custom_field['type'], $custom_field['field_name'], $module);
				if ( in_array($field_type, array('image', 'file', 'media', 'url', 'embed')) ) {
					if ( version_compare($this->drupal_version, '7', '<') ) {
						// Drupal 6 and less
						$file_table_name = 'files';
						$uri_field = 'filepath AS uri';
						$link_url_field = 'url';
						$entity_field = 'nid';
						$image_fields = "'' AS alt, '' AS title";
						$file_fields = "'' AS description";
						$field_file_id_field = 'f.' . (isset($custom_field['columns']['fid'])? $custom_field['columns']['fid'] : 'fid');
						if ( version_compare($this->drupal_version, '6', '<') ) {
							// Drupal 5 and less
							$timestamp_field = "'' AS timestamp";
						} else {
							// Drupal 6
							$timestamp_field = 'fm.timestamp';
						}
					} elseif ( version_compare($this->drupal_version, '8', '<') ) {
						// Drupal 7
						$file_table_name = 'file_managed';
						$uri_field = 'uri';
						$link_url_field = 'url';
						$image_fields = "f.${field_value_name}_alt AS alt, f.${field_value_name}_title AS title";
						if ( $this->column_exists($table_name, $field_value_name . '_description') ) {
							$file_fields = "f.${field_value_name}_description AS description";
						} else {
							$file_fields = "'' AS description";
						}
						$timestamp_field = 'fm.timestamp';
						$field_file_id_field = 'f.' . (isset($custom_field['columns']['fid'])? $custom_field['columns']['fid'] : 'fid');
					} else {
						// Drupal 8
						$file_table_name = 'file_managed';
						$uri_field = 'uri';
						$link_url_field = 'uri'; // URI not URL on Drupal 8
						$timestamp_field = 'fm.created AS timestamp';
						$file_fields = "f.${field_value_name}_description AS description";
						$field_file_id_field = 'f.' . $field_value_name . '_target_id';
						if ( $this->column_exists($table_name, $field_value_name . '_alt') ) {
							$image_fields = "f.${field_value_name}_alt AS alt, f.${field_value_name}_title AS title";
						}
						if ( empty($custom_field['module']) ) {
							// Drupal 8 structure modified using media_field_data as an intermediary table
							$image_fields = "m.thumbnail__alt AS alt, m.thumbnail__title AS title";
							$extra_joins = "INNER JOIN ${prefix}media_field_data m ON m.mid = f.${field_value_name}_target_id";
							$field_file_id_field = 'm.thumbnail__target_id';
						}
					}
				}

				// Bundle
				if ( isset($custom_field['bundle']) && !empty($custom_field['bundle']) ) {
					$extra_criteria .= "AND f.bundle = '" . $custom_field['bundle'] . "'";
				}
				
				// Default language
				if ( isset($entity['language']) && !empty($entity['language']) && ($entity['language'] != 'und') ) {
					if ( version_compare($this->drupal_version, '8', '>=') ) {
						// Version 8
						$extra_criteria .= " AND f.langcode IN('" . $entity['language'] . "', 'und')";
					} else if ( version_compare($this->drupal_version, '7', '>=') ) {
						// Version 7
						$extra_criteria .= " AND f.language IN('" . $entity['language'] . "', 'und')";
					}
				}
				
				$extra_criteria = apply_filters('fgd2wp_get_data_field_extra_criteria', $extra_criteria, $entity, 'f.');

				switch ( $field_type ) {

					// Image
					case 'image':
						$sql = "
							SELECT fm.fid, fm.filename, fm.$uri_field, $timestamp_field, $image_fields
							FROM ${prefix}$table_name f
							$extra_joins
							INNER JOIN ${prefix}$file_table_name fm ON fm.fid = $field_file_id_field
							WHERE f.$entity_field = '$entity_id'
							$extra_criteria
							$order_by
						";
						break;

					// File
					case 'file':
					case 'media':
					case 'embed':
						$sql = "
							SELECT fm.fid, fm.filename, fm.$uri_field, $timestamp_field, $file_fields
							FROM ${prefix}$table_name f
							$extra_joins
							INNER JOIN ${prefix}$file_table_name fm ON fm.fid = $field_file_id_field
							WHERE f.$entity_field = '$entity_id'
							$extra_criteria
							$order_by
						";
						break;

					// URL
					case 'url':
						$sql = "
							SELECT f.{$field_value_name}_{$link_url_field} AS url, f.{$field_value_name}_title AS title
							FROM ${prefix}$table_name f
							$extra_joins
							WHERE f.$entity_field = '$entity_id'
							$extra_criteria
							$order_by
						";
						break;

					// Node reference
					case 'node_reference':
						if ( $this->column_exists($table_name, "{$field_value_name}_nid") ) {
							$sql = "
								SELECT f.{$field_value_name}_nid
								FROM ${prefix}$table_name f
								INNER JOIN ${prefix}node n ON n.vid = f.revision_id
								WHERE n.nid = '$entity_id'
								$extra_criteria
								$order_by
							";
						}
						break;

					default:
						$sql = "
							SELECT DISTINCT $columns
							FROM ${prefix}$table_name f
							WHERE f.$entity_field = '$entity_id'
							$extra_criteria
							$order_by
						";
				}
				$sql = apply_filters('fgd2wp_get_custom_field_values_sql', $sql, $entity_id, $node_type, $custom_field);
				$custom_field_values = $this->drupal_query($sql);
				
				// Remove the "delta" column from the result
				foreach ( $custom_field_values as &$row ) {
					if ( isset($row['delta']) ) {
						unset($row['delta']);
					}
				}
			}
		}
		$custom_field_values = apply_filters('fgd2wp_get_custom_field_values', $custom_field_values, $entity_id, $node_type, $custom_field, $entity_type);
		return $custom_field_values;
	}

	/**
	 * Get a comma separated list of columns
	 * 
	 * @since 1.8.0
	 * 
	 * @param array $columns Columns
	 * @return string List of columns separated by commas
	 */
	private function get_columns_list($columns) {
		$filtered_columns = array();
		foreach ( $columns as $column ) {
			if ( !preg_match('/_format$/', $column) ) {
				$filtered_columns[] = $column;
			}
		}
		return implode(', ', $filtered_columns);
	}

	/**
	 * Map a custom field type
	 * 
	 * @param string $drupal_type Drupal field type
	 * @param string $name Field name
	 * @param string $module Drupal module
	 * @return string Mapped WordPress field type
	 */
	public function map_custom_field_type($drupal_type, $name, $module='') {
		if ( in_array($module, array('entityreference', 'entity_reference')) ) {
			$type = 'nodereference';
		} elseif ( $module == 'image' ) {
			$type = 'image';
		} elseif ( $module == 'link' ) {
			$type = 'url';
		} else {
			switch ( $drupal_type ) {
				case 'date':
				case 'datetime':
				case 'date_popup':
				case 'date_select':
				case 'date_text':
					$type = 'date';
					break;
				case 'time':
					$type = 'time';
					break;
				case 'download':
				case 'media':
				case 'media_generic':
					// Special case for the fields with video in the name
					if ( (stripos($name, 'video') !== false)
					  || (stripos($name, 'וידאו') !== false) ) { // video in Hebrew
						$type = 'url'; // Video URLs
					} else {
						$type = 'file';
					}
					break;
				case 'embed':
					$type = 'embed';
					break;
				case 'file':
				case 'filefield':
					$type = 'file';
					break;
				case 'image':
				case 'imagepro':
				case 'imagefield':
				case 'imagefield_crop':
				case 'imagefield_crop_widget':
				case 'image_image':
				case 'image_fupload_imagefield':
				case 'image_miw':
				case 'swfupload':
					$type = 'image';
					break;
				case 'text':
				case 'text_textfield':
				case 'string':
				case 'string_long':
				case 'textstyled':
					$type = 'textfield';
					break;
				case 'textarea':
				case 'text_long':
				case 'text_textarea':
				case 'text_with_summary':
				case 'text_textarea_with_summary':
				case 'text_textarea_maxlength_js':
				case 'paragraphs':
					$type = 'wysiwyg';
					break;
				case 'video':
				case 'video_embed_field':
					$type = 'video';
					break;
				case 'options_onoff':
				case 'boolean':
					$type = 'checkbox';
					break;
				case 'checkbox':
				case 'options':
					$type = 'checkboxes';
					break;
				case 'options_buttons':
					$type = 'radio';
					break;
				case 'select':
				case 'selection':
				case 'options_select':
				case 'list_string':
					$type = 'select';
					break;
				case 'nodereference':
				case 'node_reference_autocomplete':
				case 'entity_reference':
				case 'entityreference':
					$type = 'nodereference';
					break;
				case 'integer':
				case 'float':
				case 'number':
					$type = 'numeric';
					break;
				case 'email':
					$type = 'email';
					break;
				case 'telephone':
					$type = 'phone';
					break;
				case 'link':
				case 'link_field':
				case 'youtube':
				case 'vimeo':
				case 'video_embed_field_video':
					$type = 'url';
					break;
				case 'content_taxonomy':
					$type = 'taxonomy';
					break;
				case 'color_field_type':
					$type = 'color';
					break;
				case 'group':
					$type = 'group';
					break;
				default:
					$type = 'textfield';
			}
		}
		return $type;
	}
	
	/**
	 * Import the Drupal 5 attachments
	 * 
	 * @since 2.3.0
	 * 
	 * @param int $new_post_id WP Post ID
	 * @param array $node Node
	 */
	public function import_drupal5_attachments($new_post_id, $node) {
		if ( version_compare($this->drupal_version, '6', '<') ) {
			// Drupal 5 and less
			if ( !$this->plugin_options['skip_media'] ) {
				$post_date = empty($node['created'])? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $node['created']);
				$attachments = $this->get_drupal5_attachments($node['nid']);
				foreach ( $attachments as $attachment ) {
					$attachment_id = $this->import_media($attachment['filename'], $attachment['filepath'], $post_date, array(), array('ref' => 'node ID=' . $node['nid']));
					if ( $attachment_id !== false ) {
						$this->media_count++;
						// Add the attachment as a custom field
						add_post_meta($new_post_id, 'attachment', $attachment_id);
					}
				}
			}
		}
	}
	
	/**
	 * Get the Drupal attachments of a node
	 * 
	 * @since 2.3.0
	 * 
	 * @param int $nid NID
	 * @return array Attachments
	 */
	private function get_drupal5_attachments($nid) {
		$attachments = array();
		$prefix = $this->plugin_options['prefix'];
		
		if ( $this->column_exists('files', 'nid') ) {
			$sql = "
				SELECT f.fid, f.filename, f.filepath, f.filemime
				FROM ${prefix}files f
				WHERE f.nid = '$nid'
			";
			$attachments = $this->drupal_query($sql);
		}
		return $attachments;
	}
	
	/**
	 * Reset options
	 * 
	 * @since 2.36.0
	 * 
	 * @global object $wpdb
	 * @param string $search Search string
	 */
	public function reset_options_like($search) {
		global $wpdb;
		$sql = $wpdb->prepare("UPDATE $wpdb->options SET option_value = 0 WHERE option_name LIKE %s", $search);
		$wpdb->query($sql);
	}

}
