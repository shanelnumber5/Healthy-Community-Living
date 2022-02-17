<?php

/**
 * Custom Content module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.3.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Custom_Content', false) ) {

	/**
	 * Custom Content class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Custom_Content {
		
		private $plugin;
		private $node_types = array();
		private $custom_fields = array();
		private $node_types_for_taxonomy = array();		// Taxonomy/node type relations
		private $taxonomies_for_node_type = array();	// Node type/taxonomy relations
		private $taxonomy_custom_fields = array();
		private $user_fields = array();
		private $builtin_post_types = array();
		private $regular_user_fields = array(); // First name, last name and website fields
		 
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;
			$this->builtin_post_types = array(
				'article'	=> array('slug' => 'post', 'singular' => __('Post'), 'plural' => __('Posts')),
				'story'		=> array('slug' => 'post', 'singular' => __('Post'), 'plural' => __('Posts')),
				'post'		=> array('slug' => 'post', 'singular' => __('Post'), 'plural' => __('Posts')),
				'page'		=> array('slug' => 'page', 'singular' => __('Page'), 'plural' => __('Pages')),
			);
		}
		
		/**
		 * Actions when emptying the WordPress content
		 */
		public function empty_database() {
			delete_option('fgd2wp_imported_node_types_with_custom_fields');
			if ( $this->plugin->cpt_format == 'toolset' ) {
				$this->plugin->cpt->delete_toolset_data();
			}
		}
		
		/**
		 * Init the CPT format variables
		 */
		public function init_cpt_format() {
			$this->plugin->cpt_format = $this->plugin->premium_options['cpt_format'];
			$this->plugin->cpt = new FG_Drupal_to_WordPress_CPT($this->plugin, $this->plugin->cpt_format);
		}
		
		/**
		 * Get custom content info
		 *
		 * @param string $message Message to display when displaying Drupal info
		 * @return string Message
		 */
		public function get_custom_content_info($message) {
			
			// Taxonomies
			if ( !isset($this->plugin->premium_options['skip_taxonomies']) || !$this->plugin->premium_options['skip_taxonomies'] ) {
				$taxonomies = $this->get_custom_taxonomies();
				foreach ( $taxonomies as $taxonomy ) {
					$terms_count = $this->plugin->get_taxonomies_terms_count($taxonomy['machine_name']);
					$message .= $terms_count . ' ' . $taxonomy['name'] . "\n";
				}
			}
			
			// Nodes
			if ( !isset($this->plugin->premium_options['skip_nodes']) || !$this->plugin->premium_options['skip_nodes'] ) {
				$nodes_types = $this->get_node_types(true);
				foreach ( $nodes_types as $node_type ) {
					$nodes_count = $this->plugin->get_nodes_count($node_type['type'], $node_type['entity_type']);
					$message .= $nodes_count . ' ' . $node_type['name'] . "\n";
				}
			}
			
			return $message;
		}
		
		/**
		 * Check if the required plugins are activated
		 */
		public function check_required_plugins() {
			$custom_node_types = $this->get_node_types(true);
			if ( count($custom_node_types) > 0 ) {
				$this->plugin->cpt->check_required_plugins();
			}
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			
			// Taxonomies
			if ( !isset($this->plugin->premium_options['skip_taxonomies']) || !$this->plugin->premium_options['skip_taxonomies'] ) {
				$taxonomies = $this->get_custom_taxonomies();
				foreach ( $taxonomies as $taxonomy ) {
					$count += $this->plugin->get_taxonomies_terms_count($taxonomy['machine_name']);
				}
			}
			
			// Nodes
			if ( !isset($this->plugin->premium_options['skip_nodes']) || !$this->plugin->premium_options['skip_nodes'] ) {
				$nodes_types = $this->get_node_types(true);
				foreach ( $nodes_types as $node_type ) {
					$node_type_type = $node_type['type'];
					if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($node_type_type, $this->plugin->premium_options['nodes_to_skip']) ) {
						$count += $this->plugin->get_nodes_count($node_type_type, $node_type['entity_type']);
					}
				}
			}
			
			return $count;
		}
		
		/**
		 * Get the custom taxonomies
		 * 
		 * @return array Taxonomies names
		 */
		private function get_custom_taxonomies() {
			$taxonomies = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
				// Drupal 4
				$sql = "
					SELECT DISTINCT tv.name, tv.name AS machine_name, tv.description, tv.hierarchy
					FROM ${prefix}term_data t
					INNER JOIN ${prefix}vocabulary tv ON tv.vid = t.vid
					WHERE tv.name NOT IN('categories', 'tags')
				";
				$taxonomies = $this->plugin->drupal_query($sql);
				
			} elseif ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 5 & 6
				$sql = "
					SELECT DISTINCT tv.name, tv.name AS machine_name, tv.description, (1-tv.tags) AS hierarchy
					FROM ${prefix}term_data t
					INNER JOIN ${prefix}vocabulary tv ON tv.vid = t.vid
					WHERE tv.name NOT IN('categories', 'tags')
				";
				if ( defined('FGD2WPP_IMPORT_FORUM_TO_BBPRESS') ) {
					$sql .= "AND tv.module != 'forum' AND tv.name != 'Forums'\n";
				}
				$taxonomies = $this->plugin->drupal_query($sql);
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT tv.name, tv.machine_name, tv.description, 1 AS hierarchy
					FROM ${prefix}taxonomy_vocabulary tv
					WHERE tv.machine_name NOT IN('categories', 'tags')
				";
				if ( defined('FGD2WPP_IMPORT_FORUM_TO_BBPRESS') ) {
					$sql .= "AND tv.module != 'forum'\n";
				}
				$taxonomies = $this->plugin->drupal_query($sql);
				
			} else {
				// Drupal 8
				$sql = "
					SELECT c.name, c.data
					FROM ${prefix}config c
					WHERE c.name LIKE 'taxonomy.vocabulary.%'
					AND c.name NOT IN('taxonomy.vocabulary.categories', 'taxonomy.vocabulary.tags')
				";
				if ( defined('FGD2WPP_IMPORT_FORUM_TO_BBPRESS') ) {
					$sql .= "AND c.name != 'taxonomy.vocabulary.forums'\n";
				}
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					$data = unserialize($row['data']);
					if ( isset($data['vid']) ) {
						$taxonomy = array(
							'name' => $data['name'],
							'machine_name' => $data['vid'],
							'description' => $data['description'],
							'hierarchy' => isset($data['hierarchy'])? $data['hierarchy'] : '',
						);
						$taxonomies[] = $taxonomy;
					}
				}
			}
			
			return $taxonomies;
		}
		
		/**
		 * Add the partial import nodes content to the successful database connection message
		 * 
		 * @param array $response Response
		 * @return array Response
		 */
		public function add_partial_import_nodes_content_to_response($response) {
			$response['partial_import_nodes'] = $this->get_partial_import_nodes_content();
			return $response;
		}
		
		/**
		 * Save the partial import nodes options
		 * 
		 */
		public function save_partial_import_nodes_options() {
			if ( $this->plugin->drupal_connect() ) {
				$this->get_partial_import_nodes_content();
			}
		}
		
		/**
		 * Get the partial import nodes content
		 * 
		 * @return string HTML content
		 */
		public function get_partial_import_nodes_content() {
			global $drupal_db;
			
			$content = '';
			if ( !empty($drupal_db) ) {
				$node_types = $this->get_node_types(false);
				if ( count($node_types) > 0 ) {
					$nodes_to_skip = isset($this->plugin->premium_options['nodes_to_skip'])? $this->plugin->premium_options['nodes_to_skip'] : array();
					if ( empty($nodes_to_skip) ) {
						$nodes_to_skip = array();
					}
					foreach ( $node_types as $node_type ) {
						$node_type_type = $node_type['type'];
						$skip_node = in_array($node_type_type, $nodes_to_skip);
						$content .= '<input id="skip_node_' . $node_type_type . '" name="nodes_to_skip[]" type="checkbox" value="' . $node_type_type . '" ' . checked($skip_node, 1, false) . ' /> <label for="skip_node_' . $node_type_type . '" >' . sprintf(__("Don't import the nodes of type %s (%s)", 'fgd2wpp'), $node_type['name'], $node_type_type) . "</label><br />\n";
					}
				}
				update_option('fgd2wp_partial_import_nodes_html', $content);
			}
			return $content;
		}
		
		/**
		 * Get the custom node types
		 * 
		 * @param bool $custom_nodes_only Get only the custom nodes
		 * @return array Node types
		 */
		private function get_node_types($custom_nodes_only=false) {
			$node_types = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
					// Drupal 4
					$sql = "
						SELECT DISTINCT nt.type, nt.type AS name, '' AS description, 'node' AS entity_type
						FROM ${prefix}node nt
						WHERE 1 = 1
					";
				} else {
					// Drupal 5, 6 and 7
					$sql = "
						SELECT nt.type, nt.name, nt.description, 'node' AS entity_type
						FROM ${prefix}node_type nt
						WHERE 1 = 1
					";
				}
				if ( $custom_nodes_only ) {
					$sql .= "AND nt.type NOT IN('article', 'story', 'post', 'page')\n";
				}
				if ( defined('FGD2WPP_IMPORT_FORUM_TO_BBPRESS') ) {
					$sql .= "AND nt.type != 'forum'\n";
				}
				$sql = apply_filters('fgd2wp_get_nodes_types_sql', $sql);
				$node_types = $this->plugin->drupal_query($sql);
			} else {
				// Drupal 8
				$sql = "
					SELECT c.name, c.data
					FROM ${prefix}config c
					WHERE c.name LIKE 'node.type.%'
				";
				if ( $custom_nodes_only ) {
					$sql .= "AND c.name NOT IN('node.type.article', 'node.type.story', 'node.type.post', 'node.type.page')\n";
				}
				if ( defined('FGD2WPP_IMPORT_FORUM_TO_BBPRESS') ) {
					$sql .= "AND c.name != 'node.type.forum'\n";
				}
				$sql = apply_filters('fgd2wp_get_nodes_types_sql', $sql);
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					$data = unserialize($row['data']);
					$node_type = array();
					if ( isset($data['type']) && isset($data['name']) ) {
						$node_type = array(
							'type' => $data['type'],
							'name' => $data['name'],
							'description' => $data['description'],
							'entity_type' => 'node',
						);
					}
					$node_type = apply_filters('fgd2wp_node_type', $node_type, $row['name'], $data);
					if ( !empty($node_type) ) {
						$node_types[] = $node_type;
					}
				}
			}
			$node_types = apply_filters('fgd2wp_get_node_types', $node_types);
			return $node_types;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @param string $database_info Database info
		 * @return string Database info
		 */
		public function get_database_info($database_info) {
			
			// Custom taxonomies
			$taxonomies = $this->plugin->cpt->get_custom_taxonomies();
			if ( is_array($taxonomies) ) {
				foreach ( $taxonomies as $taxonomy_name => $taxonomy_object ) {
					if ( !in_array($taxonomy_name, array('category', 'post_tag')) ) {
						$terms_count = wp_count_terms($taxonomy_name);
						if ( !is_wp_error($terms_count) ) {
							$database_info .= $terms_count . ' ' . $this->plugin->cpt->get_taxonomy_name($taxonomy_object) . "<br />";
						}
					}
				}
			}
			
			// Custom post types
			$post_types = $this->plugin->cpt->get_custom_post_types();
			if ( is_array($post_types) ) {
				foreach ( $post_types as $post_type_name => $post_type_object ) {
					if ( !in_array($post_type_name, array('post', 'page')) && post_type_exists($post_type_name) ) {
						$post_type_count_object = wp_count_posts($post_type_name);
						$post_type_count = $post_type_count_object->publish + $post_type_count_object->draft + $post_type_count_object->future + $post_type_count_object->pending;
						$database_info .= $post_type_count . ' ' . $this->plugin->cpt->get_post_type_name($post_type_object) . "<br />";
					}
				}
			}
			
			return $database_info;
		}
		
		/**
		 * Reset the stored last ids during emptying the database
		 * 
		 */
		public function reset_last_custom_content_ids() {
			// Taxonomies
			$this->plugin->reset_options_like("fgd2wp_last_taxonomy_%_id");
			
			// Nodes
			$this->plugin->reset_options_like("fgd2wp_last_node_%_id");
		}
		
		/**
		 * Register custom post types, custom taxonomies and custom fields
		 * 
		 */
		public function register_custom_content() {
			
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			if ( !isset($this->plugin->premium_options['skip_taxonomies']) || !$this->plugin->premium_options['skip_taxonomies']
				|| !isset($this->plugin->premium_options['skip_nodes']) || !$this->plugin->premium_options['skip_nodes'] ) {
				$this->get_all_node_types();
				$this->get_nodestypes_taxonomies_relations();
				$this->get_nodestypes_nodestypes_relations();
				$this->register_custom_taxonomies();
				$this->register_taxonomies_custom_fields();
				$this->register_builtin_post_types();
				$this->register_custom_post_types();
				$this->plugin->cpt->register_post_types_relationships($this->node_types_relations);
				$this->register_custom_fields();
				if ( function_exists('wpcf_init_custom_types_taxonomies') ) {
					wpcf_init_custom_types_taxonomies();
				}
				flush_rewrite_rules();
				$this->register_custom_taxonomies_for_builtin_post_types();
				$this->register_taxonomies_for_custom_post_types(); // Must be called after flush_rewrite_rules(). Otherwise the post_type/taxonomy relationships are lost if the user language is not the same as the site language
			}
		}
		
		/**
		 * Get all the node types
		 * 
		 * @since 1.61.0
		 */
		private function get_all_node_types() {
			$this->node_types = $this->get_node_types(false); // Get all the node types
		}
		
		/**
		 * Set the node types/taxonomies relations in two arrays:
		 * $node_types_for_taxonomy[]
		 * $taxonomies_for_node_type[]
		 * 
		 */
		private function get_nodestypes_taxonomies_relations() {
			$this->node_types_for_taxonomy = array();
			$this->taxonomies_for_node_type = array();
			foreach ( $this->node_types as $node_type ) {
				$node_type_type = $node_type['type'];
				if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($node_type_type, $this->plugin->premium_options['nodes_to_skip']) ) {
					$this->taxonomies_for_node_type[$node_type_type] = array();
					$taxonomies = $this->get_node_type_taxonomies($node_type_type, $node_type['entity_type']);
					foreach ($taxonomies as $taxonomy ) {
						$this->node_types_for_taxonomy[$taxonomy][] = $node_type_type;
						$this->taxonomies_for_node_type[$node_type_type][] = $taxonomy;
					}
				}
			}
			// Remove duplicates from the node_types_for_taxonomy array
			foreach ( array_keys($this->node_types_for_taxonomy) as $taxonomy ) {
				$this->node_types_for_taxonomy[$taxonomy] = array_unique($this->node_types_for_taxonomy[$taxonomy]);
			}
			// Remove duplicates from the taxonomies_for_node_type array
			foreach ( array_keys($this->taxonomies_for_node_type) as $node_type ) {
				$this->taxonomies_for_node_type[$node_type] = array_unique($this->taxonomies_for_node_type[$node_type]);
			}
		}
		
		/**
		 * Get the taxonomies associated with a node type
		 * 
		 * @param string $node_type Node type
		 * @param string $entity_type Entity type (node, media)
		 * @return array Taxonomies
		 */
		private function get_node_type_taxonomies($node_type, $entity_type) {
			$taxonomies = array();
			
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
				// No associations for Drupal 4
				
			} elseif ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 5 and 6
				$sql = "
					SELECT LOWER(v.name) AS name
					FROM ${prefix}vocabulary v
					INNER JOIN ${prefix}vocabulary_node_types vnt ON vnt.vid = v.vid
					WHERE vnt.type = '$node_type'
				";
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					$taxonomies[] = $this->plugin->build_taxonomy_slug($row['name']);
				}
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT fc.data
					FROM ${prefix}field_config fc
					INNER JOIN ${prefix}field_config_instance fci ON fci.field_id = fc.id
					WHERE fci.bundle = '$node_type'
					AND fc.type = 'taxonomy_term_reference'
				";
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					if ( is_resource($row['data']) ) { // PostgreSQL bytea type
						$row['data'] = stream_get_contents($row['data']);
					}
					$data = unserialize($row['data']);
					if ( isset($data['settings']['allowed_values']) ) {
						$allowed_values = $data['settings']['allowed_values'];
						foreach ( $allowed_values as $allowed_value ) {
							if ( isset($allowed_value['vocabulary']) ) {
								$taxonomies[] = $this->plugin->build_taxonomy_slug($allowed_value['vocabulary']);
							}
						}
					}
				}
				
			} else {
				// Drupal 8
				$sql = "
					SELECT c.name, c.data
					FROM ${prefix}config c
					WHERE c.name LIKE 'field.field.$entity_type.$node_type.%'
				";
				$sql = apply_filters('fgd2wp_get_node_type_taxonomies_sql', $sql, $node_type);
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					$data = unserialize($row['data']);
					if ( isset($data['field_type']) && ($data['field_type'] == 'entity_reference') && isset($data['settings']['handler_settings']['target_bundles']) ) {
						foreach ( $data['settings']['handler_settings']['target_bundles'] as $taxonomy ) {
							$taxonomies[] = $this->plugin->build_taxonomy_slug($taxonomy);
						}
					}
				}
				
			}
			$taxonomies = apply_filters('fgd2wp_get_node_type_taxonomies', $taxonomies, $node_type);
			return $taxonomies;
		}
		
		/**
		 * Set the node types/node types relations
		 * 
		 * @since 1.16.0
		 */
		private function get_nodestypes_nodestypes_relations() {
			$this->node_types_relations = array();
			$fields = $this->get_node_reference_fields();
			foreach ( $fields as $row ) {
				$data = $row['data'];
				$field_slug = sanitize_title(preg_replace('/^field_/', '', $row['field_name']));
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6
					$entity_type = isset($data['settings']['target_type'])? $data['settings']['target_type'] : 'node';
					$label = $row['label'];
				} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
					// Drupal 7
					$entity_type = isset($data['settings']['target_type'])? $data['settings']['target_type'] : 'node';
					$label = $row['instance_data']['label'];
				} else {
					// Drupal 8
					$entity_type = $data['entity_type'];
					$label = $data['label'];
				}
				$cardinality = isset($row['cardinality'])? $row['cardinality'] : 1;
				if ( $cardinality <= 0 ) {
					$cardinality = -1;
				}
				
				if ( isset($row['type_name']) && in_array($entity_type, $this->get_allowed_entity_types()) ) {
					$post_type = $this->plugin->map_post_type($row['type_name']);
					if ( !isset($this->node_types_relations[$post_type]) ) {
						$this->node_types_relations[$post_type] = array();
					}
					foreach ( $this->plugin->get_referenceable_types($data) as $drupal_post_type ) {
						$wp_child_type = $this->plugin->map_post_type($drupal_post_type);
						$relationship_cardinality = ($wp_child_type == $post_type)? -1 : $cardinality; // N:N relationship if parent post type = child post type
						$this->node_types_relations[$post_type][] = array(
							'slug' => $field_slug,
							'label' => $label,
							'post_type' => $wp_child_type,
							'entity_type' => $this->get_entity_type_from_node_type($drupal_post_type),
							'cardinality' => $relationship_cardinality,
						);
					}
				}
			}
		}
		
		/**
		 * Get the allowed entity types
		 * 
		 * @since 2.37.0
		 * 
		 * @return array Entity types
		 */
		private function get_allowed_entity_types() {
			return apply_filters('fgd2wp_get_allowed_entity_types', array('node'));
		}
		
		/**
		 * Get the node reference fields
		 * 
		 * @since 1.16.0
		 * 
		 * @return array Node reference fields
		 */
		private function get_node_reference_fields() {
			$fields = array();
			$table_name = '';
			if ( $this->plugin->drupal_version == 7 ) {
				// Drupal 7
				$data_field = 'data';
				$instance_data_field = 'fi.data';
				$label_field = "''";
				$cardinality_field = 'f.cardinality';
				$node_type = 'bundle AS type_name';
				$table_name = 'field_config';
				$and_active = 'AND f.deleted = 0';
				$node_reference = 'node_reference';
			} elseif ( $this->plugin->drupal_version == 6 ) {
				// Drupal 6
				$data_field = 'global_settings AS data';
				$instance_data_field = "''";
				$label_field = 'fi.label';
				$cardinality_field = 'f.multiple';
				$node_type = 'type_name';
				$table_name = 'content_node_field';
				$and_active = 'AND f.active = 1';
				$node_reference = 'nodereference';
			} elseif ( $this->plugin->drupal_version == 5 ) {
				// Drupal 5
				$data_field = 'global_settings AS data';
				$instance_data_field = "''";
				$label_field = "''";
				$cardinality_field = 'f.multiple';
				$node_type = 'type_name';
				$table_name = 'node_field';
				$and_active = '';
				$node_reference = 'nodereference';
			}
			if ( !empty($table_name) && $this->plugin->table_exists($table_name) ) {
				$prefix = $this->plugin->plugin_options['prefix'];

				$sql = "
					SELECT f.field_name, f.${data_field}, fi.${node_type}, ${instance_data_field} AS instance_data, $cardinality_field, $label_field AS label
					FROM ${prefix}${table_name} f
					INNER JOIN ${prefix}${table_name}_instance fi ON fi.field_name = f.field_name
					WHERE f.type = '$node_reference'
					$and_active
				";
				$fields = $this->plugin->drupal_query($sql);
				
				foreach ( $fields as &$field ) {
					$field['data'] = unserialize($field['data']);
					$field['instance_data'] = unserialize($field['instance_data']);
					$field['do_not_register'] = true;
				}
				// Cardinality for Drupal 6 "multiple" field
				$fields = $this->convert_multiple_to_cardinality($fields);
			}
			$fields = apply_filters('fgd2wp_get_node_reference_fields', $fields);
			return $fields;
		}
		
		/**
		 * Convert the Drupal 6 "multiple" field to "cardinality"
		 * 
		 * @since 1.89.0
		 * 
		 * @param array $fields Fields
		 * @return array Fields
		 */
		private function convert_multiple_to_cardinality($fields) {
			foreach ( $fields as &$row ) {
				if ( isset($row['multiple']) ) {
					switch ( $row['multiple'] ) {
						case 0:
							$row['cardinality'] = 1;
							break;
						case 1:
							$row['cardinality'] = -1; // unlimited
							break;
						default:
							$row['cardinality'] = $row['multiple'];
					}
				}
			}
			return $fields;
		}
		
		/**
		 * Get the entity type corresponding to a node type
		 * 
		 * @since 1.61.0
		 * 
		 * @param string $drupal_node_type Drupal node type
		 * @return string Entity type
		 */
		private function get_entity_type_from_node_type($drupal_node_type) {
			$entity_type = 'node';
			foreach ( $this->node_types as $node_type ) {
				if ( $node_type['type'] == $drupal_node_type ) {
					$entity_type = $node_type['entity_type'];
					break;
				}
			}
			return $entity_type;
		}
		
		/**
		 * Register the custom taxonomies
		 * 
		 */
		private function register_custom_taxonomies() {
			$taxonomies = $this->get_custom_taxonomies();
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy_slug = $this->plugin->build_taxonomy_slug($taxonomy['machine_name']);
				$taxonomy_slug = apply_filters('fgd2wp_pre_register_taxonomy', $taxonomy_slug, $taxonomy);
				if ( isset($this->node_types_for_taxonomy[$taxonomy_slug]) ) {
					$node_types = $this->node_types_for_taxonomy[$taxonomy_slug];
					$post_types = array_map(array($this->plugin, 'map_post_type'), $node_types);
					$singular = preg_replace('/s$/', '', $taxonomy['name']); // Remove the trailing s
					$singular = ucfirst($singular);
					$plural = FG_Drupal_to_WordPress_Tools::plural($singular);
					$taxonomy_slug = $this->plugin->map_taxonomy($taxonomy_slug);
					$this->plugin->cpt->register_custom_taxonomy($taxonomy_slug, $singular, $plural, $taxonomy['description'], $post_types, $taxonomy['hierarchy']);
					$this->plugin->display_admin_notice(sprintf(__('%s taxonomy registered', 'fgd2wpp'), $taxonomy['name']));
				}
			}
		}
		
		/**
		 * Register the taxonomies custom fields
		 * 
		 * @since 1.40.0
		 */
		private function register_taxonomies_custom_fields() {
			$custom_fields = $this->get_taxonomies_custom_fields();
			foreach ( $custom_fields as $custom_field_name => $custom_field_data ) {
				if ( isset($custom_field_data['taxonomy']) ) {
					$this->plugin->cpt->register_custom_taxonomy_field($custom_field_name, $custom_field_data);
					$taxonomy = $this->plugin->map_taxonomy($custom_field_data['taxonomy']);
					$this->taxonomy_custom_fields[$taxonomy][$custom_field_name] = $custom_field_data;
				}
			}
		}
		
		/**
		 * Get the taxonomies custom fields
		 * 
		 * @since 1.40.0
		 * 
		 * @return array Custom fields
		 */
		private function get_taxonomies_custom_fields() {
			$custom_fields = array();
			
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 6
				// TODO
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$custom_fields = $this->get_drupal7_taxonomies_custom_fields();
			} else {
				// Drupal 8
				$custom_fields = $this->get_drupal8_custom_fields('%', 'taxonomy_term');
			}
			$custom_fields = apply_filters('fgd2wp_get_taxonomies_custom_fields', $custom_fields);
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 7 taxonomies custom fields
		 * 
		 * @since 1.40.0
		 * 
		 * @return array Custom fields
		 */
		private function get_drupal7_taxonomies_custom_fields() {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT fc.field_name, fc.data, fci.data AS data_instance, fc.module, fc.cardinality, fci.bundle AS taxonomy
				FROM ${prefix}field_config fc
				INNER JOIN ${prefix}field_config_instance fci ON fci.field_id = fc.id
				WHERE fci.entity_type = 'taxonomy_term'
			";
			$result = $this->plugin->drupal_query($sql);
			$custom_fields = $this->process_drupal7_custom_fields($result);
			return $custom_fields;
		}
		
		/**
		 * Register the builtin post types
		 * 
		 */
		private function register_builtin_post_types() {
			foreach ( $this->builtin_post_types as $drupal_type => $wp_type ) {
				if ( isset($this->taxonomies_for_node_type[$drupal_type]) ) {
					$node_type_taxonomies = $this->taxonomies_for_node_type[$drupal_type];
					$this->plugin->cpt->register_builtin_post_type($wp_type['slug'], $wp_type['singular'], $wp_type['plural'], '', $node_type_taxonomies);
				}
			}
		}
		
		/**
		 * Register the custom taxonomies for builtin post types
		 * 
		 */
		private function register_custom_taxonomies_for_builtin_post_types() {
			foreach ( $this->builtin_post_types as $drupal_type => $wp_type ) {
				if ( isset($this->taxonomies_for_node_type[$drupal_type]) ) {
					$node_type_taxonomies = $this->taxonomies_for_node_type[$drupal_type];
					foreach ( $node_type_taxonomies as $taxonomy ) {
						$taxonomy_slug = $this->plugin->build_taxonomy_slug($taxonomy);
						register_taxonomy_for_object_type($taxonomy_slug, $wp_type['slug']);
					}
				}
			}
		}
		
		/**
		 * Register the taxonomies for the custom post types
		 * 
		 * @since 2.32.1
		 */
		private function register_taxonomies_for_custom_post_types() {
			foreach ( $this->node_types as $node_type ) {
				if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($node_type['type'], $this->plugin->premium_options['nodes_to_skip']) ) {
					$node_type_type = $this->plugin->map_post_type($node_type['type']);
					$node_type_taxonomies = isset($this->taxonomies_for_node_type[$node_type_type])? $this->taxonomies_for_node_type[$node_type_type] : array();
					$post_type = $this->plugin->map_post_type($node_type_type);
					$post_type = apply_filters('fgd2wp_pre_register_post_type', $post_type, $node_type);
					if ( !empty($post_type) ) {
						foreach ( $node_type_taxonomies as $taxonomy ) {
							$taxonomy_slug = $this->plugin->map_taxonomy($taxonomy);
							register_taxonomy_for_object_type($taxonomy_slug, $post_type);
						}
					}
				}
			}
		}
		
		/**
		 * Register the custom post types
		 * 
		 */
		private function register_custom_post_types() {
			
			$nodes_types = $this->get_node_types(true);
			foreach ( $nodes_types as $node_type ) {
				if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($node_type['type'], $this->plugin->premium_options['nodes_to_skip']) ) {
					$node_type_type = $this->plugin->map_post_type($node_type['type']);
					$node_type_taxonomies = isset($this->taxonomies_for_node_type[$node_type_type])? $this->taxonomies_for_node_type[$node_type_type] : array();
					$node_type_parents = isset($this->node_types_relations[$node_type_type])? array_keys($this->get_post_types_from_node_types_relations($this->node_types_relations[$node_type_type])): array();
					$post_type = $this->plugin->map_post_type($node_type_type);
					$post_type = apply_filters('fgd2wp_pre_register_post_type', $post_type, $node_type);
					if ( !empty($post_type) ) {
						$mapped_taxonomies = array_map(array($this->plugin, 'map_taxonomy'), $node_type_taxonomies);
						$singular = preg_replace('/s$/', '', $node_type['name']); // Remove the trailing s
						$singular = ucfirst($singular);
						$plural = FG_Drupal_to_WordPress_Tools::plural($singular);
						$this->plugin->cpt->register_custom_post_type($post_type, $singular, $plural, $node_type['description'], $mapped_taxonomies, $node_type_parents);
						foreach ( $node_type_taxonomies as $taxonomy ) {
							$taxonomy_slug = $this->plugin->map_taxonomy($taxonomy);
							register_taxonomy_for_object_type($taxonomy_slug, $post_type);
						}
						$this->plugin->display_admin_notice(sprintf(__('%s post type registered', 'fgd2wpp'), $node_type['name']));
					}
				}
			}
		}
		
		/**
		 * Get the post types from the node types relations
		 * 
		 * @since 1.61.0
		 * 
		 * @param array $relations Node types relations
		 * @return array Post types
		 */
		private function get_post_types_from_node_types_relations($relations) {
			$post_types = array();
			foreach ( $relations as $relation ) {
				$post_types[$relation['post_type']] = $relation;
			}
			return $post_types;
		}
			
		/**
		 * Get the Drupal post types corresponding to a WordPress post type
		 * 
		 * @since 1.35.0
		 * 
		 * @param string $wp_post_type WordPress post type
		 * @return array Potential Drupal post types corresponding to the WP post type
		 */
		private function get_drupal_post_types_from_wp_post_type($wp_post_type) {
			if ( $wp_post_type == 'post' ) {
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6 and less
					$post_types = array('article', 'story', 'post');
				} else {
					// Drupal 7+
					$post_types = array('article', 'post');
				}
			} else {
				$post_types = array($wp_post_type);
			}
			$post_types = apply_filters('fgd2wp_get_drupal_post_types_from_wp_post_type', $post_types, $wp_post_type);
			return $post_types;
		}
		
		/**
		 * Register the custom fields
		 * 
		 */
		private function register_custom_fields() {
			$imported_node_types_with_custom_fields = get_option('fgd2wp_imported_node_types_with_custom_fields', array());
			$node_types = apply_filters('fgd2wp_pre_register_custom_fields_get_node_types', $this->node_types);
			foreach ( $node_types as $node_type ) {
				$drupal_node_type = $node_type['type'];
				if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($drupal_node_type, $this->plugin->premium_options['nodes_to_skip']) ) {
					$post_type = $this->plugin->map_post_type($drupal_node_type);
					$custom_fields = $this->get_custom_fields($drupal_node_type, $node_type['entity_type']);
					if ( !in_array($node_type, $imported_node_types_with_custom_fields) ) { // Don't re-import the already imported custom fields
						$this->plugin->cpt->register_custom_post_fields($custom_fields, $post_type);
					}
					$this->custom_fields[$node_type['entity_type']][$drupal_node_type] = $custom_fields;
					$imported_node_types_with_custom_fields[] = $node_type;
				}
			}
			update_option('fgd2wp_imported_node_types_with_custom_fields', $imported_node_types_with_custom_fields);
			do_action('fgd2wp_post_register_custom_fields', $this->custom_fields);
		}
		
		/**
		 * Get the custom fields
		 * 
		 * @param string $node_type Node type
		 * @param string $entity_type Entity type (node, media)
		 * @return array Custom fields
		 */
		private function get_custom_fields($node_type, $entity_type) {
			$custom_fields = array();
			
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 6
				$custom_fields = $this->get_drupal6_custom_fields($node_type);
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$custom_fields = $this->get_drupal7_custom_fields($node_type);
			} else {
				// Drupal 8
				$custom_fields = $this->get_drupal8_custom_fields($node_type, $entity_type);
			}
			$custom_fields = apply_filters('fgd2wp_get_custom_fields', $custom_fields, $node_type);
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 6 custom fields
		 * 
		 * @param string $node_type Node type
		 * @return array Custom fields
		 */
		private function get_drupal6_custom_fields($node_type) {
			$custom_fields = array();
			
			// Upload field type
			if ( ($this->plugin->get_drupal_variable('upload_' . $node_type) == "1") && $this->plugin->table_exists('upload') ) {
				$field_slug = 'attachment';
				$field = array(
					'field_name' => '',
					'table_name' => 'upload',
					'module' => 'upload',
					'columns' => array('fid' => 'fid'),
					'label' => 'Attachment',
					'type' => 'file',
					'description' => '',
					'default_value' => '',
					'required' => false,
					'repetitive' => true,
				);
				$custom_fields[$field_slug] = $field;
			}
			
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 7 custom fields
		 * 
		 * @param string $node_type Node type
		 * @return array Custom fields
		 */
		private function get_drupal7_custom_fields($node_type) {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT fc.field_name, fc.data, fci.data AS data_instance, fc.module, fc.cardinality
				FROM ${prefix}field_config fc
				INNER JOIN ${prefix}field_config_instance fci ON fci.field_id = fc.id
				WHERE fci.bundle = '$node_type'
				AND fc.type != 'taxonomy_term_reference'
			";
			$result = $this->plugin->drupal_query($sql);
			$custom_fields = $this->process_drupal7_custom_fields($result);
			return $custom_fields;
		}
		
		/**
		 * Process the Drupal 7 custom fields
		 * 
		 * @param array $raw_custom_fields Raw custom fields
		 * @return array Processed custom fields
		 */
		private function process_drupal7_custom_fields($raw_custom_fields) {
			$custom_fields = array();
			foreach ( $raw_custom_fields as $row ) {
				$field_name = $row['field_name'];
				if ( !in_array($field_name, array('body', 'field_categories', 'field_tags')) ) { // Standard fields already imported
					if ( is_resource($row['data']) ) { // PostgreSQL bytea type
						$row['data'] = stream_get_contents($row['data']);
					}
					$data = unserialize($row['data']);
					if ( is_resource($row['data_instance']) ) { // PostgreSQL bytea type
						$row['data_instance'] = stream_get_contents($row['data_instance']);
					}
					$data_instance = unserialize($row['data_instance']);
					if ( isset($data_instance['label']) ) {
						$field_type = $this->get_drupal7_field_type($row['module'], $data_instance, $row['cardinality']);
						list($table_name, $columns) = $this->plugin->get_drupal7_storage_location($field_name, $data, $field_type);
						if ( !empty($table_name) && !empty($columns) ) {
							$field = array(
								'field_name' => $field_name,
								'table_name' => $table_name,
								'module' => $row['module'],
								'columns' => $columns,
								'label' => $data_instance['label'],
								'type' => $field_type,
								'description' => isset($data_instance['description'])? $data_instance['description']: '',
								'default_value' => isset($data_instance['default_value'])? $data_instance['default_value']: '',
								'required' => isset($data_instance['required'])? $data_instance['required']: false,
								'cardinality' => $row['cardinality'],
								'repetitive' => (($row['cardinality'] != 1) && ($row['module'] != 'list')) || (($row['module'] == 'date') && (count($columns) > 1)),
								'entity_type' => isset($row['entity_type'])? $row['entity_type']: '',
								'order' => isset($data_instance['widget']['weight'])? $data_instance['widget']['weight'] : 0,
							);
							if ( isset($data['settings']['allowed_values']) ) {
								if ( is_array($data['settings']['allowed_values']) ) {
									// Options stored in an array
									$field['options'] = $data['settings']['allowed_values'];
								} else {
									// Options stored on separate rows. Name and value separated by |
									$options = explode("\n", $data['settings']['allowed_values']);
									foreach ( $options as $option ) {
										if ( strpos($option, '|') !== false ) {
											list($option_name, $option_value) = explode('|', $option);
											$field['options'][$option_name] = $option_value;
										}
									}
								}
							}
							$referenceable_types = $this->plugin->get_referenceable_types($data);
							if ( !empty($referenceable_types) ) {
								$field['referenceable_types'] = $referenceable_types;
								$field['target_type'] = $data['settings']['target_type'];
							}
							if ( isset($row['taxonomy']) ) {
								$field['taxonomy'] = $row['taxonomy'];
							}
							if ( isset($row['bundle']) ) {
								$field['bundle'] = $row['bundle'];
							}

							$field = apply_filters('fgd2wp_get_custom_field', $field, $data, $data_instance);

							if ( isset($field['columns']['value']) || isset($field['columns']['fid']) || isset($field['columns']['url']) || isset($field['referenceable_types']) ) {
								// Get only the standard types and the referenceable types
								$field_slug = preg_replace('/^field_/', '', $field_name);
								$field_slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin(remove_accents($field_slug)));
								if ( isset($field['taxonomy']) ) {
									$field_slug = $field['taxonomy'] . '-' . $field_slug;
								}
								$custom_fields[$field_slug] = $field;
							} else {
								$custom_fields = apply_filters('fgd2wp_post_get_custom_field', $custom_fields, $field, $data, $data_instance);
							}
						}
					} else {
						$custom_fields = apply_filters('fgd2wp_get_drupal7_custom_fields', $custom_fields, $row, $data, $data_instance);
					}
				}
			}
			return $custom_fields;
		}
		
		/**
		 * Get the field type
		 * 
		 * @since 1.50.0
		 * 
		 * @param string $module Module
		 * @param array $data_instance Field data instance
		 * @param int $cardinality Cardinality
		 * @return string Field type
		 */
		private function get_drupal7_field_type($module, $data_instance, $cardinality) {
			$field_type = '';
			
			if ( $module == 'file' ) { // for file custom types
				$field_type = 'file';
			} elseif ( $module == 'node_reference' ) { // for node_references
				$field_type = 'node_reference';
			} elseif ( $module == 'entityreference' ) { // for entityreference
				$field_type = 'entityreference';
			} else {
				if ( isset($data_instance['widget']['type']) && !empty($data_instance['widget']['type']) ) {
					$field_type = $data_instance['widget']['type'];
				} elseif ( isset($data_instance['widget']['module']) && !empty($data_instance['widget']['module']) ) {
					$field_type = $data_instance['widget']['module'];
				} else {
					$field_type = 'text'; // Default
				}
				
				// Choose between checkboxes and radio depending on the cardinality
				if ( ($field_type == 'options_buttons') && ($cardinality == -1) ) {
						$field_type = 'options';
				}
			}
			
			return $field_type;
		}
		
		/**
		 * Get the Drupal 8 custom fields
		 * 
		 * @param string $node_type Node type or %
		 * @param string $entity_type Entity type (node, media, taxonomy_term)
		 * @return array Custom fields
		 */
		private function get_drupal8_custom_fields($node_type, $entity_type) {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT c.data
				FROM ${prefix}config c
				WHERE c.name LIKE 'field.field.$entity_type.$node_type.field_%'
			";
			$sql = apply_filters('fgd2wp_get_drupal8_custom_fields_sql', $sql, $node_type);
			$result = $this->plugin->drupal_query($sql);
			$custom_fields = $this->process_drupal8_custom_fields($result, $node_type, $entity_type);
			return $custom_fields;
		}
		
		/**
		 * Process the Drupal 8 custom fields
		 * 
		 * @since 1.42.0
		 * 
		 * @param array $raw_custom_fields Raw custom fields
		 * @param string $node_type Node type or %
		 * @param string $entity_type (node, user, taxonomy_term)
		 * @return array Processed custom fields
		 */
		private function process_drupal8_custom_fields($raw_custom_fields, $node_type, $entity_type) {
			$custom_fields = array();
			$weights = $this->get_drupal8_field_weights($node_type, $entity_type);
			foreach ( $raw_custom_fields as $row ) {
				$data = unserialize($row['data']);
				if ( isset($data['field_type']) ) {
					if ( isset($data['label']) ) {
						$data_storage = $this->plugin->get_drupal8_storage($data['field_name'], $entity_type);
						$field_slug = preg_replace('/^field_/', '', $data['field_name']);
						$field_slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin(remove_accents($field_slug)));
						$module = isset($data['dependencies']['module'][0])? $data['dependencies']['module'][0] : '';
						$column_key = ($data['field_type'] == 'entity_reference')? 'target_id' : $field_slug;
						switch ( $data['field_type'] ) {
							case 'entity_reference':
							case 'entity_reference_revisions':
								$column_suffix = '_target_id';
								break;
							case 'link':
								$column_suffix = '_uri' . ', ' . $data['field_name'] . '_title';
								break;
							case 'color_field_type':
								$column_suffix = '_color';
								break;
							default:
								$column_suffix = '_value';
						}
						$field = array(
							'field_name' => $data['field_name'],
							'entity_type' => $data['entity_type'],
							'table_name' => $entity_type .'__' . $data['field_name'],
							'module' => $module,
							'columns' => array($column_key => $data['field_name'] . $column_suffix),
							'label' => $data['label'],
							'type' => $data['field_type'],
							'description' => $data['description'],
							'default_value' => $data['default_value'],
							'required' => $data['required'],
							'cardinality' => $data_storage['cardinality'],
							'repetitive' => ($data_storage['cardinality'] != 1) && ($data['field_type'] != 'list_string'),
							'order' => isset($weights[$data['field_name']])? $weights[$data['field_name']] : 0,
						);
						if ( ($data['entity_type'] == 'taxonomy_term') && isset($data['bundle']) ) {
							$field['taxonomy'] = $data['bundle'];
						}
						if ( isset($data_storage['settings']['allowed_values']) ) {
							foreach ( $data_storage['settings']['allowed_values'] as $allowed_value ) {
								$field['options'][$allowed_value['value']] = $allowed_value['label'];
							}
						}
						$referenceable_types = $this->plugin->get_referenceable_types($data);
						if ( !empty($referenceable_types) ) {
							if ( ((count($referenceable_types) == 1) && in_array('image', $referenceable_types)) || $this->is_media_type($data) ) {
								// Media set as Entity reference
								$data['field_type'] = 'image';
								$field['type'] = 'image';
							} else {
								$field['referenceable_types'] = $referenceable_types;
							}
						}
						
						$field = apply_filters('fgd2wp_get_custom_field', $field, $data_storage, $data);

						if ( in_array($data['field_type'], array('text', 'image', 'list', 'options', 'datetime', 'text_long', 'text_with_summary', 'textstyled', 'list_string', 'integer', 'float', 'string', 'string_long', 'email', 'telephone', 'link', 'file', 'boolean', 'video_embed_field', 'color_field_type')) ) {
							// Get only the standard types
							$custom_fields[$field_slug] = $field;
						} else {
							$custom_fields = apply_filters('fgd2wp_post_get_custom_field', $custom_fields, $field, $data_storage, $data);
						}
					}
				}
			}
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 8 field weights (sort orders)
		 * 
		 * @since 3.0.0
		 * 
		 * @param string $node_type Node type
		 * @param string $entity_type (node, user, taxonomy_term)
		 * @return array Weights
		 */
		private function get_drupal8_field_weights($node_type, $entity_type) {
			$weights = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT c.data
				FROM ${prefix}config c
				WHERE c.name LIKE 'core.entity_form_display.$entity_type.$node_type.default'
			";
			$sql = apply_filters('fgd2wp_get_drupal8_custom_fields_sql', $sql, $node_type);
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$data = unserialize($row['data']);
				if ( isset($data['content']) ) {
					foreach ( $data['content'] as $field_name => $properties ) {
						if ( isset($properties['weight']) ) {
							$weights[$field_name] = $properties['weight'];
						}
					}
				}
			}
			return $weights;
		}
		
		/**
		 * Check if the field is a media
		 * 
		 * @since 1.70.0
		 * 
		 * @param string $settings Field settings
		 * @return boolean
		 */
		private function is_media_type($settings) {
			$is_media = false;
			if ( isset($settings['settings']) ) {
				$settings = $settings['settings']; // Drupal 7+
			}
			$is_media = isset($settings['handler']) && ($settings['handler'] == 'default:media');
			return $is_media;
		}
		
		/**
		 * Check is the field is a node reference field
		 * 
		 * @since 1.50.1
		 * 
		 * @param array $field Field data
		 * @return bool True if it is a node reference field
		 */
		private function is_node_reference_field($field) {
			if ( $this->is_user_reference_field($field) ) {
				// User reference field
				return false;
			}
			return in_array($field['type'], array('nodereference', 'node_reference', 'entityreference', 'entity_reference')) || 
				   (isset($field['module']) && in_array($field['module'], array('node_reference', 'entityreference')));
		}
		
		/**
		 * Check is the field is a user reference field
		 * 
		 * @since 3.13.0
		 * 
		 * @param array $field Field data
		 * @return bool True if it is a user reference field
		 */
		private function is_user_reference_field($field) {
			return isset($field['target_type']) && ($field['target_type'] == 'user');
		}
		
		/**
		 * Set the relations between a node and its taxonomies
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param array $node Node
		 * @param string $post_type Post type
		 * @param string $entity_type Entity type (node, media)
		 */
		public function set_node_taxonomies_relations($new_post_id, $node, $post_type='post', $entity_type='node') {
			$terms = $this->plugin->get_node_taxonomies_terms($node['nid'], '', '', $entity_type);
			
			// Split the terms by taxonomy
			$terms_by_taxonomy = array();
			foreach ( $terms as $term ) {
				$terms_by_taxonomy[$term['taxonomy']][] = $term;
			}
			
			foreach ( $terms_by_taxonomy as $taxonomy => $terms ) {
				$terms_ids = $this->plugin->get_wp_taxonomies_terms_ids($terms);
				$terms_ids = apply_filters('fgd2wp_get_node_terms_ids', $terms_ids, $node, $terms);
				$taxonomy_slug = $this->plugin->map_taxonomy($taxonomy);
				wp_set_object_terms($new_post_id, $terms_ids, $taxonomy_slug, true);
			}
		}
		
		/**
		 * Import custom taxonomies
		 * 
		 */
		public function import_custom_taxonomies_terms() {
			
			// Taxonomies terms
			if ( !isset($this->plugin->premium_options['skip_taxonomies']) || !$this->plugin->premium_options['skip_taxonomies'] ) {
				$taxonomies = $this->get_custom_taxonomies();
				foreach ( $taxonomies as $taxonomy ) {
					if ( $this->plugin->import_stopped() ) {
						return;
					}
					$terms_count = $this->plugin->import_taxonomies_terms($taxonomy['machine_name'], $taxonomy['name']);
					$this->plugin->display_admin_notice(sprintf(__('%d %s imported', 'fgd2wpp'), $terms_count, $taxonomy['name']));
				}
			}
		}
		
		/**
		 * Import custom post types and custom fields
		 * 
		 */
		public function import_custom_nodes() {
			
			if ( !isset($this->plugin->premium_options['skip_nodes']) || !$this->plugin->premium_options['skip_nodes'] ) {
				$nodes_types = $this->get_node_types(true);
				foreach ( $nodes_types as $node_type ) {
					if ( $this->plugin->import_stopped() ) {
						return;
					}
					$node_type_type = $node_type['type'];
					if ( !isset($this->plugin->premium_options['nodes_to_skip']) || !in_array($node_type_type, $this->plugin->premium_options['nodes_to_skip']) ) {
						$nodes_count = $this->plugin->import_nodes($node_type_type, $node_type['entity_type']);
						$this->plugin->display_admin_notice(sprintf(__('%d %s imported', 'fgd2wpp'), $nodes_count, FG_Drupal_to_WordPress_Tools::plural($node_type['name'])));
					}
				}
			}
		}
		
		/**
		 * Import the relations between the nodes
		 * 
		 * @since 1.16.0
		 */
		public function import_nodes_relations() {
			do_action('fgd2wp_pre_import_nodes_relations');
			
			if ( !isset($this->plugin->premium_options['skip_nodes']) || !$this->plugin->premium_options['skip_nodes'] ) {
				$imported_posts = array();
				$imported_posts['node'] = $this->plugin->get_imported_drupal_posts_with_post_type();
				$imported_posts = apply_filters('fgd2wp_get_imported_posts', $imported_posts);
				foreach ( $imported_posts as $entity_type => $posts ) {
					$message = sprintf(__('Importing %s relationships...', 'fgd2wpp'), $entity_type);
					if ( defined('WP_CLI') ) {
						$progress_cli = \WP_CLI\Utils\make_progress_bar($message, count($posts));
					} else {
						$this->plugin->log($message);
					}

					foreach ( $posts as $node_id => $post ) {
						if ( in_array($post['post_type'], array_keys($this->node_types_relations)) ) {
							$post_id = $post['post_id'];
							$node_type_parents = isset($this->node_types_relations[$post['post_type']])? $this->get_post_types_from_node_types_relations($this->node_types_relations[$post['post_type']]): array();
							$drupal_post_types = $this->get_drupal_post_types_from_wp_post_type($post['post_type']);
							foreach ( $drupal_post_types as $drupal_post_type ) {
								if ( isset($this->custom_fields[$entity_type][$drupal_post_type]) && is_array($this->custom_fields[$entity_type][$drupal_post_type]) ) {
									foreach ( $this->custom_fields[$entity_type][$drupal_post_type] as $custom_field_name => $custom_field ) {
										if ( isset($custom_field['referenceable_types']) ) {
											foreach ( $custom_field['referenceable_types'] as $type ) {
												$wp_type = $this->plugin->map_post_type($type);
												if ( in_array($wp_type, array_keys($node_type_parents))) {
													$parent_entity_type = $node_type_parents[$wp_type]['entity_type'];
													$node = array(
														'nid' => $node_id,
														'type' => $drupal_post_type,
													);
													$custom_field_values = $this->plugin->get_node_custom_field_values($node, $custom_field, $entity_type);
													if ( !empty($custom_field_values) ) {
														foreach ( $custom_field_values as $custom_field_value ) {
															$target_field = isset($custom_field['columns']['target_id'])? $custom_field['columns']['target_id'] : (isset($custom_field['columns']['nid'])? $custom_field['columns']['nid'] : 'nid');
															$target_nid = $this->get_target_nid($custom_field_value, $target_field);
															if ( !empty($target_nid) && isset($imported_posts[$parent_entity_type][$target_nid]) ) {
																if ( $imported_posts[$parent_entity_type][$target_nid]['post_type'] == $wp_type ) {
																	$related_id = $imported_posts[$parent_entity_type][$target_nid]['post_id'];
																	$relationship_slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin($custom_field_name . '-' . $post['post_type'] . '-' . $wp_type));
																	$this->plugin->cpt->set_post_relationship($post_id, $custom_field_name, $related_id, $custom_field, $relationship_slug);
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
						if ( defined('WP_CLI') ) {
							$progress_cli->tick(1);
						}
					}
					if ( defined('WP_CLI') ) {
						$progress_cli->finish();
					}
				}
				do_action('fgd2wp_post_import_nodes_relations');
			}
		}
		
		/**
		 * Get the target node ID of a custom field value
		 * 
		 * @since 1.35.0
		 * 
		 * @param array $custom_field_value Field value
		 * @param string $target_field Target field
		 * @return int Drupal node ID
		 */
		private function get_target_nid($custom_field_value, $target_field) {
			$target_nid = 0;
			if ( isset($custom_field_value[$target_field]) ) {
				$target_nid = $custom_field_value[$target_field];
			}
			return $target_nid;
		}
		
		/**
		 * Import the node fields
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param array $node Node
		 * @param string $post_type Post type
		 * @param string $entity_type Entity type (node, media)
		 */
		public function import_node_fields($new_post_id, $node, $post_type='post', $entity_type='node') {
			if ( isset($this->custom_fields[$entity_type][$node['type']]) ) {
				$this->plugin->thumbnail_is_set = false; // To set the thumbnail with the first image
				foreach ( $this->custom_fields[$entity_type][$node['type']] as $custom_field_name => $custom_field ) {
					if ( $this->is_node_reference_field($custom_field) ) {
						continue; // Don't import the node references as custom fields ; they are imported as post relationships
					}
					$date = empty($node['created'])? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $node['created']);
					$custom_field_values = $this->plugin->get_node_custom_field_values($node, $custom_field, $entity_type);
					if ( isset($custom_field['collection']) || ($custom_field['type'] == 'paragraphs')) { // Field collection or Paragraphs field
						do_action('fgd2wp_import_node_field', $new_post_id, $custom_field_name, $post_type, $custom_field, $custom_field_values, $date);
						continue;
					}
					if ( $custom_field['type'] == 'content_taxonomy' ) { // Taxonomy field
						do_action('fgd2wp_import_taxonomy_field', $new_post_id, $custom_field_name, $custom_field, $custom_field_values);
						continue;
					}
					
					$custom_field_values = apply_filters('fgd2wp_pre_import_custom_field_values', $custom_field_values, $new_post_id, $custom_field_name, $custom_field, $date);
					
					if ( !empty($custom_field_values) ) {
						list($custom_field_name, $custom_field, $custom_field_values) = apply_filters('fgd2wp_import_node_fields', array($custom_field_name, $custom_field, $custom_field_values));
						switch ( $custom_field_name ) {
							case 'body':
								$this->add_custom_field_as_post_field($new_post_id, 'post_content', $custom_field_values, $date);
								break;
							case 'excerpt':
								$this->add_custom_field_as_post_field($new_post_id, 'post_excerpt', $custom_field_values, $date);
								break;
							default:
								$this->plugin->cpt->set_custom_post_field($new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date);
						}
					}
				}
			}
		}
		
		/**
		 * Modify the post content or the post excerpt
		 * 
		 * @since 1.12.1
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param string $field post_content | post_excerpt
		 * @param array $field_values Field values
		 * @param date $date Node date
		 */
		private function add_custom_field_as_post_field($new_post_id, $field, $field_values, $date='') {
			if ( !empty($field_values) ) {
				if ( is_array($field_values[0]) ) {
					$value = implode("<br />\n", $field_values[0]);
				} else {
					$value = $field_values[0];
				}
				$value = $this->plugin->replace_media_shortcodes($value);
				$value = $this->plugin->replace_media_links($value, $date);
				$post = get_post($new_post_id, ARRAY_A);
				$value = $post[$field] . $value;
				wp_update_post(array(
					'ID' => $new_post_id,
					$field => $value,
				));
			}
		}
		
		/**
		 * Import the term custom fields
		 * 
		 * @param int $new_term_id Term ID
		 * @param array $term Term data
		 * @param string $wp_taxonomy Taxonomy
		 */
		public function import_term_custom_fields($new_term_id, $term, $wp_taxonomy) {
			if ( isset($this->taxonomy_custom_fields[$wp_taxonomy]) ) {
				foreach ( $this->taxonomy_custom_fields[$wp_taxonomy] as $custom_field_name => $custom_field ) {
					$custom_field_values = $this->plugin->get_term_custom_field_values($term, $custom_field);
					if ( !empty($custom_field_values) ) {
						list($custom_field_name, $custom_field, $custom_field_values) = apply_filters('fgd2wp_import_term_custom_fields', array($custom_field_name, $custom_field, $custom_field_values));
						$this->plugin->cpt->set_custom_term_field($new_term_id, $custom_field_name, $custom_field, $custom_field_values);
					}
				}
			}
		}
		
		/**
		 * Register the User field group and the user fields
		 * 
		 * @since 1.47.0
		 */
		public function register_user_fields() {
			if ( isset($this->plugin->premium_options['skip_users']) && $this->plugin->premium_options['skip_users'] ) {
				return;
			}
			// Picture field
			$picture_fields = array(
				'picture' => array(
					'field_name' => 'picture',
					'label' => __('Picture', 'fgd2wpp'),
					'type' => 'image',
				)
			);
			$custom_fields = $this->get_user_fields();
			$this->user_fields = $custom_fields;
			$custom_fields = array_merge($picture_fields, $custom_fields);
			$this->plugin->cpt->register_custom_user_fields($custom_fields);
			do_action('fgd2wp_post_register_user_fields', $custom_fields);
		}
		
		/**
		 * Get the user fields
		 * 
		 * @since 1.47.0
		 * 
		 * @return array User fields
		 */
		private function get_user_fields() {
			$custom_fields = array();
			
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 6
				$custom_fields = $this->get_drupal6_user_fields();
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$custom_fields = $this->get_drupal7_user_fields();
			} else {
				// Drupal 8
				$custom_fields = $this->get_drupal8_user_fields();
			}
			$custom_fields = apply_filters('fgd2wp_get_user_fields', $custom_fields);
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 6 user fields
		 * 
		 * @since 1.77.0
		 * 
		 * @return array Custom fields
		 */
		private function get_drupal6_user_fields() {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( $this->plugin->table_exists('profile_fields') ) {
				$sql = "
					SELECT pf.fid, pf.title, pf.name, pf.type, pf.required, pf.options
					FROM ${prefix}profile_fields pf
					ORDER BY pf.fid
				";
				$result = $this->plugin->drupal_query($sql);
				foreach ( $result as $row ) {
					if ( preg_match('/((first|last)_?name|web$)/', $row['name']) ) {
						// First name, last name and web are imported as regular user fields and not as custom fields
						continue;
					}
					$options = array();
					foreach ( explode("\n", $row['options']) as $option ) {
						if ( !empty($option) ) {
							$options[$option] = $option;
						}
					}
					$custom_fields[$row['name']] = array(
						'field_name' => $row['name'],
						'label' => $row['title'],
						'type' => $row['type'],
						'required' => $row['required'],
						'options' => $options,
						'entity_type' => 'user',
					);
				}
			}
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 7 user fields
		 * 
		 * @since 1.47.0
		 * 
		 * @return array Custom fields
		 */
		private function get_drupal7_user_fields() {
			$custom_fields = array();
			$this->regular_user_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT fc.field_name, fc.data, fci.data AS data_instance, fc.module, fc.cardinality, fci.entity_type, fci.bundle
				FROM ${prefix}field_config fc
				INNER JOIN ${prefix}field_config_instance fci ON fci.field_id = fc.id
				WHERE fci.bundle = 'user'
				AND fc.type != 'taxonomy_term_reference'
			";
			$sql = apply_filters('fgd2wp_get_drupal7_user_fields_sql', $sql, $prefix);
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$data_instance = unserialize($row['data_instance']);
				if ( preg_match('/((first|last)_?name|web(_?site)?$)/', $row['field_name']) ||
					 preg_match('/((first|last) ?name|web(_?site)?$)/i', $data_instance['label']) ) {
					// First name, last name and web are imported as regular user fields and not as custom fields
					$this->regular_user_fields[] = $row;
				} else {
					$custom_fields[] = $row;
				}
			}
			$custom_fields = $this->process_drupal7_custom_fields($custom_fields);
			$this->regular_user_fields = $this->process_drupal7_custom_fields($this->regular_user_fields);
			
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 8 user fields
		 * 
		 * @return array Custom fields
		 */
		private function get_drupal8_user_fields() {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT c.name, c.data
				FROM ${prefix}config c
				WHERE c.name LIKE 'field.field.user.user.field_%'
			";
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$field_name = preg_replace('/field.field.user.user.field_/', '', $row['name']);
				if ( preg_match('/((first|last)_?name|web(_?site)?$)/', $field_name) ) {
					// First name, last name and web are imported as regular user fields and not as custom fields
					$this->regular_user_fields[] = $row;
				} else {
					$custom_fields[] = $row;
				}
			}
			$custom_fields = $this->process_drupal8_custom_fields($custom_fields, 'user', 'user');
			$this->regular_user_fields = $this->process_drupal8_custom_fields($this->regular_user_fields, 'user', 'user');
			return $custom_fields;
		}
		
		/**
		 * Add the user picture as a custom field
		 * 
		 * @since 1.47.0
		 * 
		 * @param int $new_user_id WP user ID
		 * @param int $user Drupal user
		 */
		public function add_user_picture($new_user_id, $user) {
			if ( !$this->plugin->plugin_options['skip_media'] && !empty($user['picture']) ) {
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6
					$uri = $user['picture'];
					$image = array(
						'filename' => basename($uri),
						'uri' => $uri,
						'timestamp' => time(),
					);
				} else {
					// Drupal 7+
					$picture_id = $user['picture'];
					$image = $this->plugin->get_image($picture_id);
				}
				if ( !empty($image) ) {
					$featured_image = array(
						'name' => $image['filename'],
						'filename' => $this->plugin->get_path_from_uri($image['uri']),
						'date' => date('Y-m-d H:i:s', $image['timestamp']),
						'attributs' => array(
							'image_alt' => $this->plugin->get_image_attributes($image, 'alt'),
						),
					);
					$featured_image_id = $this->plugin->import_media($featured_image['name'], $featured_image['filename'], $featured_image['date'], $featured_image['attributs'], array('ref' => 'user ID=' . $user['uid']));
					if ( !empty($featured_image_id) ) {
						$this->plugin->cpt->set_user_picture($new_user_id, $featured_image_id);
					}
				}
			}
		}
		
		/**
		 * Import the Drupal user fields values
		 * 
		 * @since 1.47.0
		 * 
		 * @param int $new_user_id WP user ID
		 * @param array $user Drupal user
		 */
		public function import_user_fields_values($new_user_id, $user) {
			// Get the user fields values
			$user_fields_values = array();
			foreach ( $this->user_fields as $custom_field_name => $custom_field ) {
				$custom_field_values = $this->plugin->get_user_custom_field_values($user, $custom_field);
				if ( !empty($custom_field_values) ) {
					$user_fields_values[$custom_field_name] = array(
						'field' => $custom_field,
						'values' => $custom_field_values,
					);
				}
			}
			$user_fields_values = apply_filters('fgd2wpp_get_user_fields_values', $user_fields_values, $user);
			
			// Save the user fields values
			$date = date('Y-m-d H:i:s', $user['created']);
			foreach ( $user_fields_values as $custom_field_name => $user_field_values ) {
				$this->plugin->cpt->set_custom_user_field($new_user_id, $custom_field_name, $user_field_values['field'], $user_field_values['values'], $date);
			}
		}
		
		/**
		 * Get the user's first name
		 * 
		 * @since 1.77.0
		 * 
		 * @param string $first_name First name
		 * @param int $drupal_user_id Drupal user ID
		 * @return string First name
		 */
		public function get_user_first_name($first_name, $drupal_user_id) {
			$new_first_name = $this->get_user_field('', $drupal_user_id, 'first_?name');
			if ( empty($new_first_name) ) {
				$new_first_name = $this->get_user_field($first_name, $drupal_user_id, 'name');
			}
			return $new_first_name;
		}
		
		/**
		 * Get the user's last name
		 * 
		 * @since 1.77.0
		 * 
		 * @param string $last_name Last name
		 * @param int $drupal_user_id Drupal user ID
		 * @return string Last name
		 */
		public function get_user_last_name($last_name, $drupal_user_id) {
			return $this->get_user_field($last_name, $drupal_user_id, 'last_?name');
		}

		/**
		 * Get the user's web site
		 * 
		 * @since 1.77.0
		 * 
		 * @param string $website Web site
		 * @param int $drupal_user_id Drupal user ID
		 * @return string Web site
		 */
		public function get_user_website($website, $drupal_user_id) {
			return $this->get_user_field($website, $drupal_user_id, 'web(_?site)?$');
		}
		
		/**
		 * Get a user field
		 * 
		 * @since 1.77.0
		 * 
		 * @param string $field_value Field value
		 * @param int $drupal_user_id Drupal user ID
		 * @param string $pattern Search pattern
		 * @return string Field value
		 */
		private function get_user_field($field_value, $drupal_user_id, $pattern) {
			foreach ( $this->regular_user_fields as $field_name => $field ) {
				if ( preg_match('/' . $pattern . '/', $field_name) ) {
					$entity_id = apply_filters('fgd2wp_get_user_entity_id', $drupal_user_id, $field);
					$result = $this->plugin->get_custom_field_values($entity_id, null, $field, $field['entity_type']);
					if ( !empty($result) && (count($result) > 0) ) {
						$field_value = array_shift($result[0]);
					}
				}
			}
			return $field_value;
		}
		
		/**
		 * Modify the links in the post custom fields
		 * 
		 * @since 2.8.0
		 * 
		 * @param WP_Post $post WP Post
		 */
		public function modify_links_in_custom_fields($post) {
			global $wpdb;
			
			$custom_fields = $this->get_custom_fields_from_post_id($post->ID);
			foreach ( $custom_fields as $custom_field ) {
				$new_value = $this->plugin->modify_links_in_string($custom_field['meta_value']);
				if ( $new_value != $custom_field['meta_value'] ) {
					$wpdb->update($wpdb->postmeta, array('meta_value' => $new_value), array('meta_id' => $custom_field['meta_id']) );
				}
			}
		}

		/**
		 * Returns the custom fields of a post
		 *
		 * @since 2.8.0
		 * 
		 * @param int WordPress post ID
		 * @return array Fields
		 */
		private function get_custom_fields_from_post_id($post_id) {
			global $wpdb;
			$field_prefix = $this->plugin->cpt->get_field_prefix();

			$sql = "
				SELECT meta_id, meta_key, meta_value
				FROM {$wpdb->postmeta}
				WHERE post_id = '$post_id'
				AND meta_key LIKE '{$field_prefix}%'
			";
			$fields = $wpdb->get_results($sql, ARRAY_A);
			return $fields;
		}
		
	}
}
