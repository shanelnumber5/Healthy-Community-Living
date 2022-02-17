<?php

/**
 * Users module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.2.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Users', false) ) {

	/**
	 * Users class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Users {

		private $users = array();
		private $users_count = 0;
		private $user__user_picture_table_exists = false;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Add user cols in the get_nodes request
		 *
		 * @param string $cols
		 * @return string Cols separating by commas (with a comma at start)
		 */
		public function add_user_cols_in_get_nodes($cols) {
			$cols .= ', n.uid';
			return $cols;
		}

		/**
		 * Delete all users except the current user
		 *
		 */
		public function delete_users($action) {
			global $wpdb;
			
			$sql_queries = array();

			$current_user = get_current_user_id();
			
			if ( $action == 'all' ) {
				
				// Delete all users except the current user
				if ( is_multisite() ) {
					$blogusers = get_users(array('exclude' => $current_user));
					foreach ( $blogusers as $user ) {
						wp_delete_user($user->ID);
					}
				} else { // monosite (quicker)
					$sql_queries[] = <<<SQL
-- Delete User meta
DELETE FROM $wpdb->usermeta
WHERE user_id != '$current_user'
SQL;

				$sql_queries[] = <<<SQL
-- Delete Users
DELETE FROM $wpdb->users
WHERE ID != '$current_user'
SQL;

					// Execute SQL queries
					if ( count($sql_queries) > 0 ) {
						foreach ( $sql_queries as $sql ) {
							$wpdb->query($sql);
						}
					}
				}
				$this->reset_users_autoincrement();
				
			} else {
				
				// Delete only the imported users
				
				if ( is_multisite() ) {
					$users = $this->plugin->get_imported_drupal_users();
					foreach ( $users as $user_id ) {
						if ( $user_id != $current_user ) {
							wp_delete_user($user_id);
						}
					}
					
				} else {
					// Truncate the temporary table
					$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;

					// Insert the imported users IDs in the temporary table
					$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT user_id FROM $wpdb->usermeta
WHERE meta_key LIKE '_fgd2wp_%'
SQL;

					$sql_queries[] = <<<SQL
-- Delete Users and user metas
DELETE u, um
FROM $wpdb->users u
LEFT JOIN $wpdb->usermeta um ON um.user_id = u.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE u.ID = del.id;
SQL;

					// Execute SQL queries
					if ( count($sql_queries) > 0 ) {
						foreach ( $sql_queries as $sql ) {
							$wpdb->query($sql);
						}
					}
				}
			}
			wp_cache_flush();
			
			// Reset the Drupal last imported user ID
			update_option('fgd2wp_last_user_id', 0);
			update_option('fgd2wp_last_author_id', 0);

			$this->plugin->display_admin_notice(__('Users deleted', $this->plugin->get_plugin_name()));
		}

		/**
		 * Reset the wp_users autoincrement
		 */
		private function reset_users_autoincrement() {
			global $wpdb;
			
			$sql = "SELECT IFNULL(MAX(ID), 0) + 1 FROM $wpdb->users";
			$max_id = $wpdb->get_var($sql);
			$sql = "ALTER TABLE $wpdb->users AUTO_INCREMENT = $max_id";
			$wpdb->query($sql);
		}
		
		/**
		 * Import the authors
		 * 
		 */
		public function import_authors() {
			if ( isset($this->plugin->premium_options['skip_users']) && $this->plugin->premium_options['skip_users'] ) {
				$this->set_imported_users();
				return;
			}
			if ( isset($this->plugin->premium_options['skip_nodes']) && $this->plugin->premium_options['skip_nodes'] ) {
				$this->set_imported_users();
				return;
			}
			$this->plugin->log(__('Importing authors...', $this->plugin->get_plugin_name()));

			$this->user__user_picture_table_exists = $this->plugin->table_exists('user__user_picture');

			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$users = $this->get_authors($this->plugin->chunks_size);
				$users_count = count($users);
				$users = apply_filters('fgd2wpp_post_get_authors', $users);
				foreach ( $users as $user ) {
					$user['roles'] = $this->get_user_roles($user['uid']);
					$new_user_id = $this->add_user($user['name'], $user['mail'], $user['pass'], $user['uid'], $user['created'], $user['roles'], 'author');
					if ( !is_wp_error($new_user_id) ) {
						$user['new_id'] = $new_user_id;
						do_action('fgd2wpp_post_add_user', $new_user_id, $user);
					}

					// Increment the Drupal last imported user ID
					update_option('fgd2wp_last_author_id', $user['uid']);
				}
			} while ( !is_null($users) && ($users_count > 0) );

			$this->plugin->log(__('Authors imported', $this->plugin->get_plugin_name()));
		}
		
		/**
		 * Get all the Drupal authors
		 * 
		 * @param int $limit Number of users max
		 * @return array Users
		 */
		private function get_authors($limit=1000) {
			$users = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$last_author_id = (int)get_option('fgd2wp_last_author_id'); // to restore the import where it left
			
			// Exclude the selected node types
			if ( isset($this->plugin->premium_options['nodes_to_skip']) && !empty($this->plugin->premium_options['nodes_to_skip']) ) {
				$excluded_node_types = implode("', '", $this->plugin->premium_options['nodes_to_skip']);
				$and_exclude_node_types = "AND n.type NOT IN('$excluded_node_types')";
			} else {
				$and_exclude_node_types = '';
			}
			
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
					// Drupal 4
					$extra_cols = ", u.timestamp AS created, '' AS picture";
				} else {
					// Drupal 5, 6 and 7
					$extra_cols = ', u.created, u.picture';
				}
				$sql = "
					SELECT DISTINCT u.uid, u.name, u.mail, u.pass
					$extra_cols
					FROM ${prefix}users u
					INNER JOIN ${prefix}node n ON n.uid = u.uid
				";
			} else {
				// Drupal 8
				if ( $this->user__user_picture_table_exists ) {
					$picture_field = 'up.user_picture_target_id AS picture';
					$join_user_picture = "LEFT JOIN ${prefix}user__user_picture up ON up.entity_id = u.uid";
				} else {
					$picture_field = "'' AS picture";
					$join_user_picture = '';
				}
				$sql = "
					SELECT DISTINCT u.uid, u.name, u.mail, u.pass, u.created, $picture_field
					FROM ${prefix}users_field_data u
					$join_user_picture
					INNER JOIN ${prefix}node_field_data n ON n.uid = u.uid
				";
			}
			$sql .= "
				WHERE u.uid > '$last_author_id'
				AND u.status = 1
				$and_exclude_node_types
				ORDER BY u.uid
				LIMIT $limit
			";
			$users = $this->plugin->drupal_query($sql);
			return $users;
		}
		
		/**
		 * Set the author of a post
		 * 
		 * @param array $newpost WordPress post
		 * @param array $node Drupal node
		 * @return array WordPress post
		 */
		public function set_post_author($newpost, $node) {
			$drupal_user_id = $node['uid'];
			if ( array_key_exists($drupal_user_id, $this->plugin->imported_users) ) {
				$user_id = $this->plugin->imported_users[$drupal_user_id];
				$newpost['post_author'] = $user_id;
			}
			return $newpost;
		}

		/**
		 * Import all the users
		 * 
		 */
		public function import_users() {
			if ( isset($this->plugin->premium_options['skip_users']) && $this->plugin->premium_options['skip_users'] ) {
				return;
			}
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$message = __('Importing users...', $this->plugin->get_plugin_name());
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_users_count());
			} else {
				$this->plugin->log($message);
			}
			
			// Hook for other actions
			do_action('fgd2wpp_pre_import_users', $this->users);
			
			$this->user__user_picture_table_exists = $this->plugin->table_exists('user__user_picture');
			
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$users = $this->get_users($this->plugin->chunks_size);
				$users_count = count($users);
				foreach ( $users as &$user ) {
					$user['roles'] = $this->get_user_roles($user['uid']);
					$new_user_id = $this->add_user($user['name'], $user['mail'], $user['pass'], $user['uid'], $user['created'], $user['roles']);
					if ( !is_wp_error($new_user_id) ) {
						$user['new_id'] = $new_user_id;
						do_action('fgd2wpp_post_add_user', $new_user_id, $user);
					}
					
					// Increment the Drupal last imported user ID
					update_option('fgd2wp_last_user_id', $user['uid']);
					
					if ( defined('WP_CLI') ) {
						$progress_cli->tick();
					}
				}
				
				// Hook for other actions
				do_action('fgd2wpp_post_import_users', $users);
				
				$this->plugin->progressbar->increment_current_count($users_count);
				
			} while ( !is_null($users) && ($users_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			$this->plugin->display_admin_notice(sprintf(_n('%d user imported', '%d users imported', $this->users_count, $this->plugin->get_plugin_name()), $this->users_count));
		}
		
		/**
		 * Get all the Drupal users
		 * 
		 * @param int $limit Number of users max
		 * @return array Users
		 */
		private function get_users($limit=1000) {
			$users = array();
			$last_user_id = (int)get_option('fgd2wp_last_user_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				if ( version_compare($this->plugin->drupal_version, '5', '<') ) {
					// Drupal 4
					$extra_cols = ", u.timestamp AS created, '' AS picture";
				} else {
					// Drupal 5, 6 and 7
					$extra_cols = ', u.created, u.picture';
				}
				$sql = "
					SELECT u.uid, u.name, u.mail, u.pass
					$extra_cols
					FROM ${prefix}users u
				";
			} else {
				// Drupal 8
				if ( $this->user__user_picture_table_exists ) {
					$picture_field = 'up.user_picture_target_id AS picture';
					$join_user_picture = "LEFT JOIN ${prefix}user__user_picture up ON up.entity_id = u.uid";
				} else {
					$picture_field = "'' AS picture";
					$join_user_picture = '';
				}
				$sql = "
					SELECT u.uid, u.name, u.mail, u.pass, u.created, $picture_field
					FROM ${prefix}users_field_data u
					$join_user_picture
				";
			}
			$sql .= "
				WHERE u.uid > '$last_user_id'
				AND u.status = 1
				ORDER BY u.uid
				LIMIT $limit
			";
			$sql = apply_filters('fgd2wpp_get_users_sql', $sql);
			$users = $this->plugin->drupal_query($sql);
			return $users;
		}
		
		/**
		 * Get the Drupal user roles
		 *
		 * @param int $user_id User ID
		 * @return array User roles
		 */
		private function get_user_roles($user_id) {
			$user_roles = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT r.name
					FROM ${prefix}role r
					INNER JOIN ${prefix}users_roles ur ON ur.rid = r.rid
					WHERE ur.uid = '$user_id'
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT r.roles_target_id AS name
					FROM ${prefix}user__roles r
					WHERE r.entity_id = '$user_id'
					AND r.deleted = 0
				";
			}
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				$user_roles[] = $row['name'];
			}
			return $user_roles;
		}

		/**
		 * Map the user role
		 * 
		 * @since 2.6.0
		 * 
		 * @param string $role Drupal role
		 * @return string WP Role
		 */
		private function map_role($role) {
			if ( (stripos($role, 'admin') !== false) ) {
				$wp_role = 'administrator';
			} elseif ( (stripos($role, 'editor') !== false) ) {
				$wp_role = 'editor';
			} elseif ( (stripos($role, 'author') !== false) ) {
				$wp_role = 'author';
			} else {
				$wp_role = $role;
			}
			return $wp_role;
		}
		
		/**
		 * Add a user if it does not exist
		 *
		 * @param string $name User's name
		 * @param string $email User's email
		 * @param string $password User's password in Drupal
		 * @param int $drupal_user_id User's id in Drupal
		 * @param string $register_date Registration date
		 * @param array $roles User's roles
		 * @param string $default_role User's default role - default: subscriber
		 * @return int User ID
		 */
		private function add_user($name, $email, $password, $drupal_user_id, $register_date, $roles, $default_role='subscriber') {
			$matches = array();
			
			$login = sanitize_user($name, true);
			if ( empty($login) ) {
				$login = sanitize_user($email);
			}
			$email = sanitize_email($email);

			$display_name = $name;

			// Get the first and last name
			if ( preg_match("/(\w+) +(.*)/u", $display_name, $matches) ) {
				$first_name = $matches[1];
				$last_name = $matches[2];
			} else {
				$first_name = $display_name;
				$last_name = '';
			}
			$user = get_user_by('slug', $login);
			if ( !$user ) {
				$user = get_user_by('email', $email);
			}
			if ( !$user ) {
				$first_name = apply_filters('fgd2wpp_get_user_first_name', $first_name, $drupal_user_id);
				$last_name = apply_filters('fgd2wpp_get_user_last_name', $last_name, $drupal_user_id);
				$website = apply_filters('fgd2wpp_get_user_website', '', $drupal_user_id);
				
				// Roles
				$wp_roles = array();
				foreach ( $roles as $role ) {
					$wp_role = $this->map_role($role);
					$wp_role_slug = sanitize_title($wp_role);
					$wp_roles[] = $wp_role_slug;
					add_role($wp_role_slug, $wp_role, array('read' => true, 'level_0' => true)); // Add the role if it doesn't exist yet
				}
				if ( empty($wp_roles) ) {
					$wp_roles[] = $default_role;
				}
				$main_role = array_shift($wp_roles);
				
				// Create a new user
				$userdata = array(
					'user_login'		=> $login,
					'user_pass'			=> wp_generate_password( 12, false ),
					'user_email'		=> $email,
					'user_url'			=> $website,
					'display_name'		=> $display_name,
					'first_name'		=> $first_name,
					'last_name'			=> $last_name,
					'user_registered'	=> date('Y-m-d H:i:s', $register_date),
					'role'				=> $main_role,
				);
				$user_id = wp_insert_user($userdata);
				if ( is_wp_error($user_id) ) {
//					$this->plugin->display_admin_error(sprintf(__('Creating user %s: %s', $this->plugin->get_plugin_name()), $login, $user_id->get_error_message()));
				} else {
					$this->users_count++;
					add_user_meta($user_id, '_fgd2wp_old_user_id', $drupal_user_id, true);
					if ( !empty($password) ) {
						// Drupal password to authenticate the users
						add_user_meta($user_id, 'drupalpass', $password, true);
					}
					// Other roles
					if ( !empty($wp_roles) ) {
						$user = get_user_by('id', $user_id);
						foreach ( $wp_roles as $role ) {
							$user->add_role($role);
						}
					}
//					$this->plugin->display_admin_notice(sprintf(__('User %s created', $this->plugin->get_plugin_name()), $login));
				}
			} else {
				$user_id = $user->ID;
				global $blog_id;
				if ( is_multisite() && $blog_id && !is_user_member_of_blog($user_id) ) {
					// Add user to the current blog (in multisite)
					add_user_to_blog($blog_id, $user_id, $role);
					$this->users_count++;
				}
			}
			return $user_id;
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_users']) || !$this->plugin->premium_options['skip_users'] ) {
				$count += $this->get_users_count();
			}
			return $count;
		}

		/**
		 * Get users info
		 *
		 * @param string $message Message to display when displaying Drupal info
		 * @return string Message
		 */
		public function get_users_info($message) {
			// Users
			$users_count = $this->get_users_count();
			$message .= sprintf(_n('%d user', '%d users', $users_count, $this->plugin->get_plugin_name()), $users_count) . "\n";
			
			return $message;
		}
		
		/**
		 * Get the number of Drupal users
		 * 
		 * @return int Number of users
		 */
		private function get_users_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Drupal 7
				$table_name = 'users';
			} else {
				// Drupal 8
				$table_name = 'users_field_data';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table_name} u
				WHERE u.status = 1
			";
			$result = $this->plugin->drupal_query($sql);
			$users_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $users_count;
		}

		/**
		 * Get the WordPress database info
		 * 
		 * @param string $database_info Database info
		 * @return string Database info
		 */
		public function get_database_info($database_info) {
			
			// Users
			$count_users = count_users();
			$users_count = $count_users['total_users'];
			$database_info .= sprintf(_n('%d user', '%d users', $users_count, $this->plugin->get_plugin_name()), $users_count) . "<br />";
			
			return $database_info;
		}

		/**
		 * Allow Unicode characters in usernames
		 * 
		 * @since 1.76.0
		 */
		public function allow_unicode_usernames() {
			if ( isset($this->plugin->premium_options['unicode_usernames']) && $this->plugin->premium_options['unicode_usernames'] ) {
				add_filter('sanitize_user', array($this, 'sanitize_user'), 10, 3);
				add_filter('wp_pre_insert_user_data', array($this, 'pre_insert_user_data'));
			}
		}
		
		/**
		 * Filter the sanitize_user function to allow Unicode characters in usernames
		 * 
		 * @since 1.76.0
		 * 
		 * @param string $username Username
		 * @param string $raw_username Raw username
		 * @param boolean $strict Strict mode
		 * @return string Username
		 */
		public function sanitize_user($username, $raw_username, $strict = false) {
			$username = $raw_username;

			$username = wp_strip_all_tags( $username );
			// Kill octets
			$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
			$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

			// If strict, reduce to Unicode letters, marks or numbers for max portability.
			// cf https://www.regular-expressions.info/unicode.html
			if ( $strict ) {
				$username = preg_replace( '|[^\p{L}\p{M}\p{N} _.\-@]|iu', '', $username );
			}

			$username = trim( $username );
			// Consolidate contiguous whitespace
			$username = preg_replace( '|\s+|', ' ', $username );

			return $username;
		}
		
		/**
		 * Set the user nicename to the user login
		 * It avoids plenty of %xx characters in the nicename and a too long nicename for Unicode usernames
		 * Required to allow the user to login with a Unicode username
		 * 
		 * @since 1.76.0
		 * 
		 * @param array $data User data
		 * @return array User data
		 */
		public function pre_insert_user_data($data) {
			$data['user_nicename'] = sanitize_title(mb_substr($data['user_login'], 0, 50));
			return $data;
		}
		
		/**
		 * Set the list of imported users
		 * 
		 * @since 3.13.0
		 */
		public function set_imported_users() {
			$this->plugin->imported_users = $this->plugin->get_imported_drupal_users();
		}
		
	}
}
