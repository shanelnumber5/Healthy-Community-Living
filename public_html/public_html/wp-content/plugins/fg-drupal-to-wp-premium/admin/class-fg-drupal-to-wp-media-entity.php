<?php

/**
 * Media Entity module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.61.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Media_Entity', false) ) {

	/**
	 * Media Entity class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Media_Entity {
		
		private $plugin;
		private $media_taxonomies_fields = array();
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;
		}
		
		/**
		 * Reset the stored last ids during emptying the database
		 * 
		 */
		public function reset_last_custom_content_ids() {
			// Media entities
			$this->reset_options_like("fgd2wp_last_media_%_id");
		}
		
		/**
		 * Reset options
		 * 
		 * @global object $wpdb
		 * @param string $search Search string
		 */
		private function reset_options_like($search) {
			global $wpdb;
			$sql = $wpdb->prepare("UPDATE $wpdb->options SET option_value = 0 WHERE option_name LIKE %s", $search);
			$wpdb->query($sql);
		}
		
		/**
		 * Get the number of Drupal media entities
		 * 
		 * @param string $sql SQL request
		 * @param string $node_type Node type (article, page)
		 * @param string $entity_type Entity type (node, media)
		 * @return string SQL request
		 */
		public function get_nodes_count_sql($sql, $node_type, $entity_type) {
			if ( $entity_type == 'media' ) {
				$prefix = $this->plugin->plugin_options['prefix'];
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}media n
					WHERE n.bundle = '$node_type'
				";
			}
			return $sql;
		}

		/**
		 * Add the Media Entities to the SQL request
		 * 
		 * @param string $sql SQL
		 * @return string SQL
		 */
		public function get_nodes_types($sql) {
			$sql = preg_replace("/WHERE c.name LIKE 'node.type.%'/", "WHERE (c.name LIKE 'node.type.%' OR c.name LIKE 'media_entity.bundle.%')", $sql);
			return $sql;
		}
		
		/**
		 * Build the Media Entity node type
		 * 
		 * @param array $node_type Node type
		 * @param string $node_name Node name
		 * @param array $data Node type data
		 * @return array Node type
		 */
		public function build_node_type($node_type, $node_name, $data) {
			if ( preg_match('/^media_entity\./', $node_name) && isset($data['id']) && isset($data['label']) ) {
				$node_type = array(
					'type' => $data['id'],
					'name' => $data['label'],
					'description' => $data['description'],
					'entity_type' => 'media',
				);
			}
			return $node_type;
		}
		
		/**
		 * Get the media entities
		 * 
		 * @param string $sql SQL request
		 * @param string $prefix Drupal table prefix
		 * @param int $last_drupal_id Last Drupal imported ID
		 * @param int $limit Limit
		 * @param string $content_type Content type (article, page)
		 * @param string $entity_type Entity type
		 */
		public function get_media_entities($sql, $prefix, $last_drupal_id, $limit, $content_type, $entity_type) {
			if ( ($entity_type == 'media') && version_compare($this->plugin->drupal_version, '8', '>=') ) {
				// Version 8
				$table_name = $entity_type . '_field_data';
				
				// Hooks for adding extra cols and extra joins
				$extra_cols = apply_filters('fgd2wp_get_nodes_add_extra_cols', '');
				$extra_joins = apply_filters('fgd2wp_get_nodes_add_extra_joins', '');
				
				$sql = "
					SELECT n.mid AS nid, n.vid, n.name AS title, n.bundle AS type, n.status, n.created, 0 AS sticky
					$extra_cols
					FROM ${prefix}${table_name} n
					$extra_joins
					WHERE n.bundle = '$content_type'
					AND n.mid > '$last_drupal_id'
					ORDER BY n.mid
					LIMIT $limit
				";
			}
			return $sql;
		}
		
		/**
		 * Get the Drupal 8 fields corresponding to media taxonomies
		 * 
		 * @return array Fields corresponding to media taxonomies
		 */
		public function get_drupal8_media_taxonomies_fields() {
			if ( version_compare($this->plugin->drupal_version, '8', '>=') ) {
				// Version 8
				$this->media_taxonomies_fields = array();
				$fields = $this->plugin->get_drupal_config_like('field.field.media.%.field_%');
				foreach ( $fields as $data ) {
					if ( isset($data['settings']['handler']) && ($data['settings']['handler'] == 'default:taxonomy_term') ) {
						$this->media_taxonomies_fields[] = $data['field_name'];
					}
				}
			}
		}
		
		/**
		 * Get the media entity terms related to a media
		 * 
		 * @param array $terms Terms
		 * @param int $media_id Media ID
		 * @return array Terms
		 */
		public function get_node_taxonomies_terms($terms, $media_id, $entity_type) {
			if ( ($entity_type == 'media') && version_compare($this->plugin->drupal_version, '8', '>=') ) {
				// Version 8
				$prefix = $this->plugin->plugin_options['prefix'];
				foreach ( $this->media_taxonomies_fields as $field ) {
					$sql = "
						SELECT t.tid, t.name, t.vid AS taxonomy
						FROM ${prefix}media__${field} i
						INNER JOIN ${prefix}taxonomy_term_field_data t ON t.tid = i.${field}_target_id
						WHERE i.entity_id = '$media_id'
					";
					$terms = array_merge($terms, $this->plugin->drupal_query($sql));
				}
			}
			
			return $terms;
		}
		
		/**
		 * Get the imported media
		 * 
		 * @param array $imported_posts Imported posts
		 * @return array Imported posts + imported media
		 */
		public function get_imported_media($imported_posts) {
			$imported_posts['media'] = $this->plugin->get_imported_drupal_posts_with_post_type('_fgd2wp_old_media_id');
			return $imported_posts;
		}
		
	}
}
