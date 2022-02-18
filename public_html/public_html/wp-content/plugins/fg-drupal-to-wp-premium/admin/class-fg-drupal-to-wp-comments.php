<?php

/**
 * Comments module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.2.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Comments', false) ) {

	/**
	 * Comments class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Comments {

		private $comments_body_storage_field; // Comment body storage field for Drupal 8
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Reset the stored last comment id during emptying the database
		 * 
		 */
		public function reset_last_comment_id() {
			update_option('fgd2wp_last_comment_id', 0);
			delete_transient('as_comment_count');
		}
		
		/**
		 * Get comments info
		 *
		 * @param string $message Message to display when displaying Drupal info
		 * @return string Message
		 */
		public function get_comments_info($message) {
			// Comments
			$comments_count = $this->get_comments_count();
			$message .= sprintf(_n('%d comment', '%d comments', $comments_count, $this->plugin->get_plugin_name()), $comments_count) . "\n";
			
			return $message;
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_comments']) || !$this->plugin->premium_options['skip_comments'] ) {
				$count += $this->get_comments_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of Drupal comments
		 * 
		 * @return int Number of comments
		 */
		private function get_comments_count() {
			if ( !$this->plugin->comments_enabled ) {
				return 0;
			}
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 6
				$table_name = 'comments';
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$table_name = 'comment';
			} else {
				// Drupal 8
				$table_name = 'comment_field_data';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table_name}
			";
			$result = $this->plugin->drupal_query($sql);
			$comments_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $comments_count;
		}
		
		/**
		 * Import the comments
		 * 
		 */
		public function import_comments() {
			if ( isset($this->plugin->premium_options['skip_comments']) && $this->plugin->premium_options['skip_comments'] ) {
				return;
			}
			
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			$message = __('Importing comments...', $this->plugin->get_plugin_name());
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_comments_count());
			} else {
				$this->plugin->log($message);
			}
			$imported_comments_count = 0;
			
			// Get the imported data
			$imported_posts = $this->plugin->get_imported_drupal_posts();
			$imported_users = $this->plugin->get_imported_drupal_users();
			$imported_comments = $this->get_imported_comments();
			
			$this->comments_body_storage_field = $this->get_comments_body_storage_field();
			do {
				if ( $this->plugin->import_stopped() ) {
					break;
				}
				
				$comments = $this->get_comments($this->plugin->chunks_size);
				$comments_count = count($comments);
				if ( is_array($comments) ) {
					foreach ( $comments as $comment ) {
						// Increment the last imported comment ID
						update_option('fgd2wp_last_comment_id', $comment['cid']);
						
						// Get the parent post
						if ( !array_key_exists($comment['nid'], $imported_posts) ) {
							continue;
						}
						$post_id = $imported_posts[$comment['nid']];
						
						// Get the parent comment
						$parent_id = array_key_exists($comment['pid'], $imported_comments)? $imported_comments[$comment['pid']] : 0;
						
						// Author
						$author_id = array_key_exists($comment['uid'], $imported_users)? $imported_users[$comment['uid']] : 0;
						if ( $author_id == 0 ) {
							// Try to match the current user
							$current_user = wp_get_current_user();
							if ( ($comment['mail'] == $current_user->data->user_email) || ($comment['name'] == $current_user->data->user_login) ) {
								$author_id = get_current_user_id();
							}
						}
						
						// Content
						$content = $comment['message'];
						if ( !empty($comment['subject']) ) {
							$content = '<strong>' . $comment['subject'] . '</strong>' . '<br />' . $content;
						}
						if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
							// Drupal 6
							$comment_approved = 1 - intval($comment['status']);
						} else {
							// Drupal 7
							$comment_approved = $comment['status'];
						}
						$data = array(
							'comment_post_ID' => $post_id,
							'comment_author' => $comment['name'],
							'comment_author_email' => $comment['mail'],
							'comment_author_url' => $comment['homepage'],
							'comment_author_IP' => $comment['hostname'],
							'comment_content' => $content,
							'comment_type' => '',
							'comment_parent' => $parent_id,
							'comment_date' => date('Y-m-d H:i:s', $comment['created']),
							'comment_approved' => $comment_approved,
							'user_id'	=> $author_id,
						);
						
						$data = apply_filters('fgd2wp_pre_insert_comment', $data, $comment);
						if ( !empty($data) ) {
							
							$comment_id = wp_insert_comment($data);
							
							if ( !empty($comment_id) ) {
								add_comment_meta($comment_id, '_fgd2wp_old_comment_id', $comment['cid'], true);
								$imported_comments[$comment['cid']] = $comment_id;

								$imported_comments_count++;
							}
						}
						
						if ( defined('WP_CLI') ) {
							$progress_cli->tick();
						}
					}
				}
				$this->plugin->progressbar->increment_current_count($comments_count);
			} while ( !is_null($comments) && ($comments_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			$this->plugin->display_admin_notice(sprintf(_n('%d comment imported', '%d comments imported', $imported_comments_count, $this->plugin->get_plugin_name()), $imported_comments_count));
		}

		/**
		 * Get the imported comments
		 * 
		 * return array $comments Comments IDs
		 */
		private function get_imported_comments() {
			global $wpdb;
			$comments = array();

			$sql = "SELECT comment_id, meta_value FROM {$wpdb->commentmeta} WHERE meta_key = '_fgd2wp_old_comment_id'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$comments[$result->meta_value] = $result->comment_id;
			}
			ksort($comments);
			return $comments;
		}
		
		/**
		 * Get the Comments body storage field name
		 * 
		 * @since 1.65.2
		 * 
		 * @return string Comments body storage field name
		 */
		private function get_comments_body_storage_field() {
			$field_name = '';
			if ( version_compare($this->plugin->drupal_version, '8', '>=') ) {
				// Drupal 8
				$fields = $this->plugin->get_drupal_config_like('field.storage.comment.%body');
				foreach ( array_keys($fields) as $name ) {
					$field_name = preg_replace('/^field\.storage\.comment\./', '', $name);
				}
			}
			return $field_name;
		}
		
		/**
		 * Get the comments
		 * 
		 * @param int $limit Number of comments max
		 * @return array Array of comments
		 */
		private function get_comments($limit=1000) {
			if ( !$this->plugin->comments_enabled ) {
				return array();
			}
			$comments = array();
			$last_id = (int)get_option('fgd2wp_last_comment_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 4
				$sql = "
					SELECT c.cid, c.pid, c.nid, c.uid, c.subject, c.hostname, c.timestamp AS created, c.status, u.name, u.mail, '' AS homepage, c.comment AS message, n.type
					FROM ${prefix}comments c
					LEFT JOIN ${prefix}users u ON u.uid = c.uid
					INNER JOIN ${prefix}node n ON n.nid = c.nid
					WHERE c.cid > '$last_id'
					ORDER BY c.cid
					LIMIT $limit
				";
				
			} elseif ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Drupal 5 & 6
				$sql = "
					SELECT c.cid, c.pid, c.nid, c.uid, c.subject, c.hostname, c.timestamp AS created, c.status, c.name, c.mail, c.homepage, c.comment AS message, n.type
					FROM ${prefix}comments c
					INNER JOIN ${prefix}node n ON n.nid = c.nid
					WHERE c.cid > '$last_id'
					ORDER BY c.cid
					LIMIT $limit
				";
				
			} else {
				if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
					// Drupal 7
					$table_name = 'comment';
					$node_id_field = 'nid';
					$node_id_field_as = 'nid';
					$body_table_name = 'field_data_comment_body';
					$body_field = 'comment_body_value';
				} else {
					// Drupal 8
					$table_name = 'comment_field_data';
					$node_id_field = 'entity_id';
					$node_id_field_as = 'entity_id AS nid';
					$body_table_name = 'comment__' . $this->comments_body_storage_field;
					$body_field = $this->comments_body_storage_field . '_value';
				}

				$sql = "
					SELECT c.cid, c.pid, c.$node_id_field_as, c.uid, c.subject, c.hostname, c.created, c.status, c.name, c.mail, c.homepage, cb.$body_field AS message, n.type
					FROM ${prefix}${table_name} c
					INNER JOIN ${prefix}${body_table_name} cb ON cb.entity_id = c.cid AND cb.deleted = 0
					INNER JOIN ${prefix}node n ON n.nid = c.$node_id_field
					WHERE c.cid > '$last_id'
					ORDER BY c.cid
					LIMIT $limit
				";
			}
			$sql = apply_filters('fgd2wp_get_comments_sql', $sql);
			$comments = $this->plugin->drupal_query($sql);
			return $comments;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @param string $database_info Database info
		 * @return string Database info
		 */
		public function get_database_info($database_info) {
			
			// Comments
			$comments_count_obj = wp_count_comments();
			$comments_count = $comments_count_obj->total_comments;
			$database_info .= sprintf(_n('%d comment', '%d comments', $comments_count, $this->plugin->get_plugin_name()), $comments_count) . "<br />";
			
			return $database_info;
		}

	}
}
