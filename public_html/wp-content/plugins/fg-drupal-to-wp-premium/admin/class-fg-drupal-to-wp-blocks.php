<?php
/**
 * Blocks module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      2.19.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Blocks', false) ) {

	/**
	 * Blocks class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Blocks {

		private $plugin;
		private $imported_blocks_count = 0;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param object $plugin Admin plugin
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
		}

		/**
		 * Reset the stored last block id when emptying the database
		 */
		public function reset_last_block_id() {
			update_option('fgd2wp_last_block_id', 0);
		}
		
		/**
		 * Delete the imported blocks
		 */
		public function delete_imported_blocks() {
			
			// Delete the imported text widgets
			$deleted_widget_ids = array();
			$text_widget = new WP_Widget('text', 'Text');
			$widget_settings = $text_widget->get_settings();
			$new_widget_settings = array();
			foreach ( $widget_settings as $widget_id => $widget ) {
				if ( !isset($widget['drupal_block_id']) ) {
					$new_widget_settings[$widget_id] = $widget;
				} else {
					$deleted_widget_ids[] = $widget_id;
				}
			}
			$text_widget->save_settings($new_widget_settings);
			
			// Remove the deleted widgets from their containers
			$sidebars_widgets = get_option('sidebars_widgets');
			foreach ( $sidebars_widgets as $container => $widgets ) {
				if ( is_array($widgets) ) {
					$new_widgets = array();
					foreach ( $widgets as $widget ) {
						$widget_id = preg_replace('/^text-/', '', $widget);
						if ( !in_array($widget_id, $deleted_widget_ids) ) {
							$new_widgets[] = $widget;
						}
					}
					$sidebars_widgets[$container] = $new_widgets;
				}
			}
			update_option('sidebars_widgets', $sidebars_widgets);
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_blocks']) || !$this->plugin->premium_options['skip_blocks'] ) {
				$count += $this->get_blocks_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of blocks
		 * 
		 * @return int Number of blocks
		 */
		private function get_blocks_count() {
			$count = 0;
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return 0;
				
			} else {
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6
					$table_name = 'boxes';
				} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
					// Drupal 7
					$table_name = 'block_custom';
				} else {
					// Drupal 8+
					$table_name = 'block_content';
				}
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}$table_name
				";
			}
			$result = $this->plugin->drupal_query($sql);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}
		
		/**
		 * Import the blocks
		 */
		public function import_blocks() {
			if ( isset($this->plugin->premium_options['skip_blocks']) && $this->plugin->premium_options['skip_blocks'] ) {
				return;
			}
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$message = __('Importing blocks...', $this->plugin->get_plugin_name());
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_blocks_count());
			} else {
				$this->plugin->log($message);
			}
			$this->imported_blocks_count = 0;
			
			$text_widget = new WP_Widget('text', 'Text');
			$widget_settings = $text_widget->get_settings();
			$last_widget_id = (count($widget_settings) > 0)? max(array_keys($widget_settings)) : 0;
			
			$imported_blocks_ids = $this->get_imported_blocks_ids($widget_settings);
			
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$blocks = $this->get_blocks($this->plugin->chunks_size);
				$blocks_count = count($blocks);
				foreach ( $blocks as $block ) {
					if ( !in_array($block['bid'], $imported_blocks_ids) ) { // Don't reimport an existing block
						$widget_settings = $this->import_block($block, $text_widget, $widget_settings, ++$last_widget_id);
					}
					
					// Increment the Drupal last imported block ID
					update_option('fgd2wp_last_block_id', $block['bid']);
					
					if ( defined('WP_CLI') ) {
						$progress_cli->tick();
					}
				}
				
				$this->plugin->progressbar->increment_current_count($blocks_count);
			} while ( !is_null($blocks) && ($blocks_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			$this->plugin->display_admin_notice(sprintf(_n('%d block imported', '%d blocks imported', $this->imported_blocks_count, $this->plugin->get_plugin_name()), $this->imported_blocks_count));
		}
		
		/**
		 * Get the imported Drupal blocks IDs
		 * 
		 * @param array $widget_settings Widgets
		 * @return array Blocks IDs
		 */
		private function get_imported_blocks_ids($widget_settings) {
			$block_ids = array();
			foreach ( $widget_settings as $widget ) {
				if ( isset($widget['drupal_block_id']) ) {
					$block_ids[] = $widget['drupal_block_id'];
				}
			}
			return $block_ids;
		}
		
		/**
		 * Get the Drupal blocks
		 * 
		 * @param int $limit Maximum number of rows to return
		 * @return array Blocks
		 */
		private function get_blocks($limit) {
			$blocks = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$last_block_id = (int)get_option('fgd2wp_last_block_id'); // to restore the import where it left
			
			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return array();
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6
					$table_name = 'boxes';
				} else {
					// Drupal 7
					$table_name = 'block_custom';
				}
				$sql = "
					SELECT b.bid, b.body, b.info
					FROM ${prefix}$table_name b
					WHERE b.bid > '$last_block_id'
					LIMIT $limit
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT c.id AS bid, b.body_value AS body, b.body_summary AS info
					FROM ${prefix}block_content c
					LEFT JOIN ${prefix}block_content__body b ON b.entity_id = c.id AND b.deleted = 0
					WHERE c.id > '$last_block_id'
					LIMIT $limit
				";
			}
			$blocks = $this->plugin->drupal_query($sql);
			return $blocks;
		}
		
		/**
		 * Import a block
		 *
		 * @param array $block Block
		 * @param WP_Widget $text_widget Text widget instance
		 * @param array $widget_settings Current widget settings
		 * @param int $id_widget ID of the widget
		 * @return array $widget_settings New widget settings
		 */
		private function import_block($block, $text_widget, $widget_settings, $id_widget) {
			$content = $block['body'];
			if ( empty($content) ) {
				$content = $this->get_block_content($block['bid']);
			}
			
			if ( !empty($content) ) {
				// Process the media
				if ( !$this->plugin->plugin_options['skip_media'] ) {
					$content = $this->plugin->replace_media_shortcodes($content);
					$block_date = date('Y-m-d H:i:s');
					$result = $this->plugin->import_media_from_content($content, $block_date, array('ref' => 'block ID=' . $block['bid']));
					$post_media = array();
					if ( !empty($result['media']) ) {
						$post_media = $result['media'];
						$this->plugin->media_count++;
					}
					$content = $this->plugin->process_content($content, $post_media);
					$content = stripslashes($content);
				}

				// Update the widget_text option
				$widget_settings[$id_widget] = array(
					'title'		=> $this->get_block_title($block['bid']),
					'text'		=> $content,
					'filter'	=> false,
					'visual'	=> true,
					'drupal_block_id' => $block['bid'],
				);
				$text_widget->save_settings($widget_settings);

				// Update the sidebars_widgets option
				$sidebars_widgets = get_option('sidebars_widgets');
				$sidebars_widgets['wp_inactive_widgets'][] = 'text-' . $id_widget;
				update_option('sidebars_widgets', $sidebars_widgets);

				$this->imported_blocks_count++;
			}
			
			return $widget_settings;
		}
		
		/**
		 * Get the block title
		 * 
		 * @param int $block_id Block ID
		 * @return string Block title
		 */
		private function get_block_title($block_id) {
			$title = '';
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( version_compare($this->plugin->drupal_version, '6', '<') ) {
				// Drupal 5 and less
				return '';
				
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Drupal 6
					$table_name = 'blocks';
				} else {
					// Drupal 7
					$table_name = 'block';
				}
				$sql = "
					SELECT b.title
					FROM ${prefix}$table_name b
					WHERE b.delta = '$block_id'
					AND module = 'block'
					LIMIT 1
				";
			} else {
				// Drupal 8+
				$sql = "
					SELECT d.info AS title
					FROM ${prefix}block_content_field_data d
					WHERE d.id = '$block_id'
					LIMIT 1
				";
			}
			$result = $this->plugin->drupal_query($sql);
			if ( count($result) > 0 ) {
				$title = $result[0]['title'];
			}
			return $title;
		}
		
		/**
		 * Get the block content
		 * 
		 * @since 2.19.0
		 * 
		 * @param int $block_id Block ID
		 * @return string Block content
		 */
		private function get_block_content($block_id) {
			$content = '';
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7 and less
				return '';
				
			} else {
				// Drupal 8+
				$field_name = 'field_content';
				$table_name = 'block_content__' . $field_name;
				if ( !$this->plugin->table_exists($table_name) ) {
					return '';
				}
				$sql = "
					SELECT c.{$field_name}_value AS value
					FROM ${prefix}$table_name c
					WHERE c.entity_id = '$block_id'
					AND c.deleted = 0
					LIMIT 1
				";
			}
			$result = $this->plugin->drupal_query($sql);
			if ( count($result) > 0 ) {
				$content = $result[0]['value'];
			}
			return $content;
		}
		
	}
}
