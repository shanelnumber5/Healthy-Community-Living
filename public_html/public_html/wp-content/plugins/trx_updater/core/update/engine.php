<?php
namespace TrxUpdater\Core\Update;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Engine extends Base {

	/**
	 * Current theme engine
	 *
	 * Version of the engine of the current (active) theme
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var engine
	 */
	protected $engine;

	/**
	 * Active theme info
	 *
	 * Info about active theme
	 *
	 * @since 1.5.0
	 * @access private
	 *
	 * @var theme
	 */
	private $theme;

	/**
	 * Theme parts to save while upgrade
	 *
	 * Save theme parts before upgrade and restore its after upgrade
	 *
	 * @since 1.5.0
	 * @access private
	 *
	 * @var theme_parts
	 */
	private $theme_parts;

	/**
	 * Files and folders to exclude from upgrade
	 *
	 * Theme files and folders to exclude from upgrade
	 *
	 * @since 1.5.0
	 * @access private
	 *
	 * @var theme_exclude
	 */
	private $theme_exclude;

	/**
	 * Class constructor.
	 *
	 * Initializing themes update manager.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct( $manager ) {

		parent::__construct( $manager );

		add_action( 'init', array( $this, 'init') );

		add_filter( 'wp_get_update_data', array( $this, 'add_engine_to_update_counts' ), 10, 2 );
		add_action( 'core_upgrade_preamble', array( $this, 'add_engine_to_update_screen' ), 8 );
		add_action( 'update-custom_update-engine', array( $this, 'update_engine' ) );
	}

	/**
	 * Init object
	 *
	 * Get current (active) theme info and the engine version from upgrade server
	 *
	 * Fired by `init` action
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function init() {
		$this->theme_parts   = array();
		$this->theme_exclude = array();
		$this->theme = $this->get_theme_info();
		if ( ! empty( $this->theme['engine']['upgrade_allowed'] ) ) {
			$this->engine = $this->get_engine_info();
			$this->theme_exclude = apply_filters( 'trx_updater_filter_theme_exclude',
													array(
														'folders' => array(
															'.git',
															'.idea',
															'.vscode'
														),
														'files'   => array(
															'.gitignore',
															'changelog.txt',
															'readme.txt',
															'screenshot.jpg',
															'style.css',
															'style.scss',
															'style.css.map',
														)
													)
												);

			if ( ! is_array( $this->theme_exclude['folders'] ) ) {
				$this->theme_exclude['folders'] = array();
			}
			foreach( $this->theme_exclude['folders'] as $k => $v ) {
				$v = chop( trx_updater_esc( $v ) );
				$v = str_replace( '..', '', $v );
				$this->theme_exclude['folders'][ $k ] = $v;
			}

			if ( ! is_array( $this->theme_exclude['files'] ) ) {
				$this->theme_exclude['files'] = array();
			}
			foreach( $this->theme_exclude['files'] as $k => $v ) {
				$v = chop( trx_updater_esc( $v ) );
				$v = str_replace( '..', '', $v );
				$this->theme_exclude['files'][ $k ] = $v;
			}
		}
	}

	/**
	 * Retrieve info about current theme
	 *
	 * Retrieve info about current (active) theme from the updates server
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function get_theme_info() {
		return apply_filters( 'trx_updater_filter_get_theme_info', array(
					'slug'        => $this->theme_slug,
					'title'       => $this->theme_name,
					'key'         => $this->theme_key,
					'version'     => $this->theme_version,
					'engine'      => array(
										'name' => '',
										'version' => '0.0.1',
										'upgrade_allowed' => false
									),
					'icon'        => $this->get_item_icon( 'theme', $this->theme_slug, $this->theme_name ),
				) );
	}

	/**
	 * Retrieve info about the latest version of the engine
	 *
	 * Retrieve info about the latest version of the engine from the updates server
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function get_engine_info() {
		$data = get_transient( 'trx_updater_engine_info' );
		if ( ! is_array( $data ) || ! empty( $_GET['force-check'] ) ) {
			$data = array(
						$this->theme_slug => array(
													'theme_slug'  => '',
													'theme_name'  => '',
													'version'     => '0.0.1',
													'update_from' => '0.0.2',
													)
						);
			$response = trx_updater_fgc( $this->update_url
											. '?action=info_engine'
											. '&theme_slug=' . urlencode( $this->theme_slug )
											. '&theme_name=' . urlencode( $this->theme_name )
											. '&key=' . urlencode( $this->theme_key )
										);
			if ( !empty($response) && is_serialized($response) ) {
				$response = unserialize($response);
				if ( !empty($response['data']) && substr($response['data'], 0, 1) == '{' ) {
					$data[ $this->theme_slug ] = json_decode($response['data'], true);
				}
			}
			set_transient( 'trx_updater_engine_info', $data, 12 * 60 * 60 );       // Store to the cache for 12 hours
		}
		return apply_filters( 'trx_updater_filter_get_engine_info', array(
					'theme_slug'  => ! empty( $data[$this->theme_slug]['theme_slug'] )  ? $data[$this->theme_slug]['theme_slug'] : '',
					'theme_name'  => ! empty( $data[$this->theme_slug]['theme_name'] )  ? $data[$this->theme_slug]['theme_name'] : '',
					'version'     => ! empty( $data[$this->theme_slug]['version'] )     ? $data[$this->theme_slug]['version'] : '',
					'update_from' => ! empty( $data[$this->theme_slug]['update_from'] ) ? $data[$this->theme_slug]['update_from'] : '',
				) );
	}

	/**
	 * Count engine updates
	 *
	 * Return 1 if a new version of the engine is available
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function count_engine_updates() {
		return ! empty( $this->theme['engine']['upgrade_allowed'] )
			&& ! empty( $this->theme['engine']['version'] )
			&& ! empty( $this->engine['version'] )
			&& version_compare( $this->engine['version'], $this->theme['engine']['version'], '>' )
			&& ( empty( $this->engine['update_from'] ) || version_compare( $this->engine['version'], $this->engine['update_from'], '>=' ) )
				? 1
				: 0;
	}

	/**
	 * Add 1 to the WordPress updates count
	 *
	 * Add 1 to the WordPress updates count if new engine is available.
	 *
	 * Fired by `wp_get_update_data` action.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function add_engine_to_update_counts($update_data, $titles) {
		if ( current_user_can( 'update_themes' ) && ! empty( $this->theme['engine']['upgrade_allowed'] ) ) {
			$update = $this->count_engine_updates();
			if ( $update > 0 ) {
				if ( empty( $update_data[ 'counts' ][ 'engine' ] ) ) {
					$update_data[ 'counts' ][ 'engine' ] = 0;
				}
				$update_data[ 'counts' ][ 'engine' ] += $update;
				$update_data[ 'counts' ][ 'total' ]  += $update;
				// Translators: %d: number of updates available to installed skins
				$titles['engine']                     = sprintf( _n( '%d Theme Core Update', '%d Theme Core Updates', $update_data[ 'counts' ][ 'engine' ], 'trx-updater' ), $update );
				$update_data['title']                 = esc_attr( implode( ', ', $titles ) );
			}
		}
		return $update_data;
	}

	/**
	 * Add new engine version to the WordPress update screen
	 *
	 * Add new engine version to the WordPress update screen
	 *
	 * Fired by `core_upgrade_preamble` action.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function add_engine_to_update_screen() {
		if ( current_user_can( 'update_themes' ) && ! empty( $this->theme['engine']['upgrade_allowed'] ) ) {
			$update = $this->count_engine_updates();
			if ( $update == 0 ) return;
			?>
			<h2>
				<?php
				// Translators: add theme name to the section title
				esc_html_e( 'Active theme components: Theme Core files', 'trx-updater' );
				?>
			</h2>
			<p>
				<?php esc_html_e( 'Active theme have a new core files available. Check it and then click &#8220;Update Theme Core&#8221;.', 'trx-updater' ); ?>
			</p>
			<div class="upgrade trx_updater_upgrade trx_updater_upgrade_engine">
				<p><input id="upgrade-engine" class="button trx_updater_upgrade_button trx_updater_upgrade_engine_button" type="button" value="<?php esc_attr_e( 'Update Theme Core', 'trx-updater' ); ?>" /></p>
				<table class="widefat updates-table" id="update-engine-table">
					<tbody class="plugins engine">
						<?php $checkbox_id = 'checkbox_' . md5( $this->theme['slug'] . '_engine' ); ?>
						<tr>
							<td class="check-column">
								<input type="checkbox"
									name="checked[]"
									id="<?php echo esc_attr( $checkbox_id ); ?>"
									data-update-url="<?php echo esc_url( $this->get_iau_link( $this->theme_slug, 'update', 'engine' ) ); ?>"
									value="<?php echo esc_attr( $this->theme['slug'] ); ?>"
								/>
								<label for="<?php echo esc_attr( $checkbox_id ); ?>" class="screen-reader-text">
									<?php
									// Translators: %s: Theme name
									printf( esc_html__( 'Select %s', 'trx-updater' ), $this->theme['title'] );
									?>
								</label>
							</td>
							<td class="plugin-title"><p>
								<?php echo $this->theme['icon']; ?>
								<strong><?php echo esc_html( $this->theme['title'] ); ?></strong>
								<?php
								// Translators: 1: Theme version, 2: new version
								printf(
									esc_html__( 'You have theme core version %1$s installed. Update to %2$s.', 'trx-updater' ),
									$this->theme['engine']['version'],
									$this->engine['version']
								);
								?>
							</p></td>
						</tr>
					</tbody>
				</table>
				<p><input id="upgrade-theme-2" class="button trx_updater_upgrade_button trx_updater_upgrade_engine_button" type="button" value="<?php esc_attr_e( 'Update Theme Core', 'trx-updater' ); ?>" /></p>
			</div>
			<?php
		}
	}

	/**
	 * Update engine
	 *
	 * Download a new engine from the upgrade server and update it
	 *
	 * Fired by `update-custom_update-theme` action.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function update_engine() {
		$nonce = trx_updater_get_value_gp('_wpnonce');
		$engine = trx_updater_get_value_gp('engine');
		if ( ! empty( $nonce )
			&& $engine == $this->theme_slug
			&& wp_verify_nonce( $nonce, "update-engine_{$engine}" )
			&& current_user_can( 'update_themes' )
			&& ! empty( $this->theme['engine']['upgrade_allowed'] )
		) {
			// Prepare URL to upgrade server
			$theme_url = sprintf( $this->update_url 
									. '?action=install_engine'
									. '&src=%1$s'
									. '&key=%2$s'
									. '&theme_slug=%3$s'
									. '&theme_name=%4$s'
									. '&domain=%5$s'
									. '&rnd=%6$s',
								urlencode( $this->get_theme_market_code() ),
								urlencode( $this->theme_key ),
								urlencode( $this->theme_slug ),
								urlencode( $this->theme_name ),
								urlencode( trx_updater_remove_protocol( get_home_url(), true ) ),
								mt_rand()
							);
			// Add theme data to upgrade cache
			$v = explode('.', '' . $this->theme_version );
			$v_length = count( $v );
			if ( $v_length > 3 ) {
				$v[ $v_length - 1 ]++;
			} else {
				$v[] = 1;
			}
			$v = join( '.', $v );
			$this->inject_update_info( 'themes', array(
				$this->theme_slug => array(
								'theme' => $this->theme_slug,
								'new_version' => $v,
								'package' => $theme_url,
								'requires' => '4.7.0',
								'requires_php' => '5.6.0'
								)
			) );
			// Load upgrader
			if ( ! class_exists( 'Theme_Upgrader' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			}
			$upgrader = new \Theme_Upgrader(
							new \Theme_Upgrader_Skin(
								array(
									'title'   => sprintf( __( 'Updating Theme Core "%s"', 'trx-updater' ), $this->theme_name ),
									'nonce'   => "update-engine_{$this->theme_slug}",
									'url'     => add_query_arg( array( 'package' => $theme_url ), 'update.php?action=upgrade-theme' ),
									'theme'   => $this->theme_slug,
									'type'    => 'upload',
									'package' => $theme_url
								)
							)
						);
			$this->before_engine_upgrade();
			$upgrader->upgrade( $this->theme_slug );
			$this->after_engine_upgrade();
		}
	}

	/**
	 * Prepare current theme to engine upgrade
	 *
	 * Backup skins before upgrade engine
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function before_engine_upgrade() {
		$theme_dir = get_template_directory();
		// Backup skins
		$skins_dir     = $theme_dir . '/skins/';
		$skins_json    = $skins_dir . 'skins.json';
		$skins_options = $skins_dir . 'skins-options.php';
		if ( file_exists( $skins_json ) ) {
			if ( file_exists( $skins_options ) ) {
				require_once $skins_options;
			}
			$skins_info = json_decode( trx_updater_fgc( $skins_json ), true );
			$skins_list = glob( $skins_dir . '*', GLOB_ONLYDIR);
			if ( is_array( $skins_list ) ) {
				$this->theme_parts['skins'] = array();
				foreach( $skins_list as $sdir ) {
					$sname = basename( $sdir );
					$rnd = str_replace('.', '', mt_rand());
					$result = wp_upload_bits( "backup-{$this->theme_slug}-skin-{$sname}-{$rnd}.zip", 0, '' );
					if ( ! empty( $result['file'] ) ) {
						if ( trx_updater_pack_archive( $result['file'], $sdir ) ) {
							$this->theme_parts['skins'][$sname] = array(
								'backup'  => $result['file'],
								'info'    => isset( $skins_info[$sname] ) ? $skins_info[$sname] : '',
								'options' => isset( $skins_options[$sname]['options'] ) ? $skins_options[$sname]['options'] : '',
							);
						}
					}
				}
			}
		}
		// Backup plugins list
		$plugins_dir  = $theme_dir . '/plugins/';
		$plugins_list = glob( $plugins_dir . '*', GLOB_ONLYDIR);
		if ( array( $plugins_list ) ) {
			$this->theme_parts['plugins'] = array();
			foreach( $plugins_list as $pdir ) {
				$pname = basename( $pdir );
				$this->theme_parts['plugins'][$pname] = array(
					'slug' => $pname
				);
			}
		}
		// Backup exclude files and folders
		$this->theme_parts['exclude'] = array();
		if ( ! empty( $this->theme_exclude['files'] ) && count( $this->theme_exclude['files'] ) > 0 ) {
			$files = array();
			foreach( $this->theme_exclude['files'] as $file ) {
				if ( ! empty( $file ) && file_exists( $theme_dir . '/' . $file ) ) {
					$files[] = $theme_dir . '/' . $file;
				}
			}
			if ( count( $files ) > 0 ) {
				$rnd = str_replace('.', '', mt_rand());
				$result = wp_upload_bits( "backup-{$this->theme_slug}-exclude-{$rnd}.zip", 0, '' );
				if ( ! empty( $result['file'] ) ) {
					if ( trx_updater_pack_archive( $result['file'], $theme_dir, $files ) ) {
						$this->theme_parts['exclude'][''] = array(
							'backup' => $result['file'],
							'mode'   => 0
							);
					}
				}
			}
		}
		if ( ! empty( $this->theme_exclude['folders'] ) ) {
			foreach( $this->theme_exclude['folders'] as $folder ) {
				if ( is_dir( $theme_dir . '/' . $folder ) ) {
					$rnd = str_replace('.', '', mt_rand());
					$result = wp_upload_bits( "backup-{$this->theme_slug}-exclude-{$rnd}.zip", 0, '' );
					if ( ! empty( $result['file'] ) ) {
						if ( trx_updater_pack_archive( $result['file'], $theme_dir . '/' . $folder ) ) {
							$this->theme_parts['exclude']["/{$folder}"] = array(
								'backup' => $result['file'],
								'mode'   => trx_updater_getmod( $theme_dir . '/' . $folder ),
								);
						}
					}
				}
			}
		}
	}

	/**
	 * Restore current theme parts after engine is upgraded
	 *
	 * Restore skins after theme engine is upgraded
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function after_engine_upgrade() {
		$theme_dir = get_template_directory();

		// Delete excess (not used in the theme) plugins
		if ( ! empty( $this->theme_parts['plugins'] ) && is_array( $this->theme_parts['plugins'] ) ) {
			$plugins_dir  = $theme_dir . '/plugins/';
			$plugins_list = glob( $plugins_dir . '*', GLOB_ONLYDIR);
			if ( array( $plugins_list ) ) {
				foreach( $plugins_list as $pdir ) {
					$pname = basename( $pdir );
					if ( ! isset( $this->theme_parts['plugins'][$pname] ) ) {
						trx_updater_del_folder( $pdir );
					}
				}
			}
		}

		// Delete skins from engine
		// (not need, because skins was removed on the server)
		if ( ! empty( $this->theme_parts['skins'] ) && is_array( $this->theme_parts['skins'] ) ) {
			$skins_dir  = $theme_dir . '/skins/';
			if ( is_dir( $skins_dir ) ) {
				$skins_list = glob( $skins_dir . '*', GLOB_ONLYDIR);
				if ( array( $skins_list ) ) {
					foreach( $skins_list as $sdir ) {
						if ( is_dir( $sdir ) ) {
							trx_updater_del_folder( $sdir );
						}
					}
				}
			}
		}

		// Restore saved skins
		if ( ! empty( $this->theme_parts['skins'] ) && is_array( $this->theme_parts['skins'] ) ) {
			$skins_dir     = $theme_dir . '/skins/';
			$skins_json    = $skins_dir . 'skins.json';
			$skins_options = $skins_dir . 'skins-options.php';
			if ( is_dir( $skins_dir ) ) {
				$skins_info           = array();
				$skins_options_output = '<?php'
										. "\n//" . esc_html__( 'Skins', 'basekit' )
										. "\n\$skins_options = array(";
				$counter = 0;
				foreach( $this->theme_parts['skins'] as $skin_name => $skin_data ) {
					$sdir = $skins_dir . trx_updater_esc( $skin_name );
					if ( ! empty( $skin_data['backup'] ) && file_exists( $skin_data['backup'] ) ) {
						if ( mkdir( $sdir ) && is_dir( $sdir ) ) {
							unzip_file( $skin_data['backup'], $sdir );
							if ( isset( $skin_data['info'] ) ) {
								$skins_info[$skin_name] = $skin_data['info'];
							}
							if ( isset( $skin_data['options'] ) ) {
								$skins_options_output .= ( $counter++ ? ',' : '' )
															. "\n\t'{$skin_name}' => array("
																. "\n\t\t'options' => " . '"' . str_replace( array( "\r", "\n" ), array( '\r', '\n' ), addslashes( $skin_data['options'] ) ) . '"'
															. "\n\t)";
							}
						}
						unlink( $skin_data['backup'] );
					}
				}
				// Save skins.json
				trx_updater_fpc( $skins_json, json_encode( $skins_info, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS ) );
				// Save skis-options.php
				if ( $counter > 0 ) {
					$skins_options_output .= "\n);"
										. "\n?>";
					trx_updater_fpc( $skins_options, $skins_options_output );
				}
			}
		}

		// Restore exclude files and folders
		if ( ! empty( $this->theme_parts['exclude'] ) ) {
			foreach( $this->theme_parts['exclude'] as $exclude_dir => $exclude_data ) {
				if ( ! empty( $exclude_data['backup'] ) && file_exists( $exclude_data['backup'] ) ) {
					unzip_file( $exclude_data['backup'], $theme_dir . $exclude_dir );
					if ( is_dir( $theme_dir . $exclude_dir ) ) {
						if ( ! empty( $exclude_data['mode'] ) ) {
							$rez = chmod( $theme_dir . $exclude_dir, $exclude_data['mode'] );
						}
						if ( $exclude_dir == '/.git' && defined( 'PHP_OS' ) && strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {
							@exec( 'attrib ' . $theme_dir . $exclude_dir . ' +H > nul' );
						}
					}
				}
				unlink( $exclude_data['backup'] );
			}
		}
	}

}
