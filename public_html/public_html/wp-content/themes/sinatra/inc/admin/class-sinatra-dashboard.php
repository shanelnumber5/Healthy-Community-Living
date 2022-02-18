<?php
/**
 * Sinatra About page class.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Dashboard' ) ) :
	/**
	 * Sinatra Dashboard page class.
	 */
	final class Sinatra_Dashboard {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Main Sinatra Dashboard Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Dashboard
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Dashboard ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Register admin menu item under Appearance menu item.
			 */
			add_action( 'admin_menu', array( $this, 'add_to_menu' ), 10 );
			add_filter( 'submenu_file', array( $this, 'highlight_submenu' ) );

			/**
			 * Ajax activate & deactivate plugins.
			 */
			add_action( 'wp_ajax_sinatra-plugin-activate', array( $this, 'activate_plugin' ) );
			add_action( 'wp_ajax_sinatra-plugin-deactivate', array( $this, 'deactivate_plugin' ) );
		}

		/**
		 * Register our custom admin menu item.
		 *
		 * @since 1.0.0
		 */
		public function add_to_menu() {

			/**
			 * Dashboard page.
			 */
			add_theme_page(
				esc_html__( 'Sinatra Theme', 'sinatra' ),
				'Sinatra Theme',
				apply_filters( 'sinatra_manage_cap', 'edit_theme_options' ),
				'sinatra-dashboard',
				array( $this, 'render_dashboard' )
			);

			/**
			 * Plugins page.
			 */
			add_theme_page(
				esc_html__( 'Plugins', 'sinatra' ),
				'Plugins',
				apply_filters( 'sinatra_manage_cap', 'edit_theme_options' ),
				'sinatra-plugins',
				array( $this, 'render_plugins' )
			);

			// Hide from admin navigation.
			remove_submenu_page( 'themes.php', 'sinatra-plugins' );

			/**
			 * Changelog page.
			 */
			add_theme_page(
				esc_html__( 'Changelog', 'sinatra' ),
				'Changelog',
				apply_filters( 'sinatra_manage_cap', 'edit_theme_options' ),
				'sinatra-changelog',
				array( $this, 'render_changelog' )
			);

			// Hide from admin navigation.
			remove_submenu_page( 'themes.php', 'sinatra-changelog' );
		}

		/**
		 * Render dashboard page.
		 *
		 * @since 1.0.0
		 */
		public function render_dashboard() {

			// Render dashboard navigation.
			$this->render_navigation();

			?>
			<div class="si-container">

				<div class="sinatra-section-title">
					<h2 class="sinatra-section-title"><?php esc_html_e( 'Getting Started', 'sinatra' ); ?></h2>
				</div><!-- END .sinatra-section-title -->

				<div class="sinatra-section sinatra-columns">

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-admin-plugins"></i><?php esc_html_e( 'Install Plugins', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Explore recommended plugins. These free plugins provide additional features and customization options.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons">
								<a href="<?php echo esc_url( menu_page_url( 'sinatra-plugins', false ) ); ?>" class="si-btn secondary" role="button"><?php esc_html_e( 'Install Plugins', 'sinatra' ); ?></a>
							</div><!-- END .sinatra-buttons -->
						</div>
					</div>

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-layout"></i><?php esc_html_e( 'Start with a Template', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Don&rsquo;t want to start from scratch? Import a pre-built demo website in 1-click and get a head start.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons plugins">

								<?php
								if ( file_exists( WP_PLUGIN_DIR . '/sinatra-core/sinatra-core.php' ) && is_plugin_inactive( 'sinatra-core/sinatra-core.php' ) ) {
									$class       = 'si-btn secondary';
									$button_text = __( 'Activate Sinatra Core', 'sinatra' );
									$link        = '#';
									$data        = ' data-plugin="sinatra-core" data-action="activate" data-redirect="' . esc_url( admin_url( 'admin.php?page=sinatra-demo-library' ) ) . '"';
								} elseif ( ! file_exists( WP_PLUGIN_DIR . '/sinatra-core/sinatra-core.php' ) ) {
									$class       = 'si-btn secondary';
									$button_text = __( 'Install Sinatra Core', 'sinatra' );
									$link        = '#';
									$data        = ' data-plugin="sinatra-core" data-action="install" data-redirect="' . esc_url( admin_url( 'admin.php?page=sinatra-demo-library' ) ) . '"';
								} else {
									$class       = 'si-btn secondary active';
									$button_text = __( 'Browse Demos', 'sinatra' );
									$link        = admin_url( 'admin.php?page=sinatra-demo-library' );
									$data        = '';
								}

								printf(
									'<a class="%1$s" %2$s %3$s role="button"> %4$s </a>',
									esc_attr( $class ),
									isset( $link ) ? 'href="' . esc_url( $link ) . '"' : '',
									$data, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									esc_html( $button_text )
								);
								?>

							</div><!-- END .sinatra-buttons -->
						</div>
					</div>

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-palmtree"></i><?php esc_html_e( 'Upload Your Logo', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Kick off branding your new site by uploading your logo. Simply upload your logo and customize as you need.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons">
								<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>" class="si-btn secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upload Logo', 'sinatra' ); ?></a>
							</div><!-- END .sinatra-buttons -->
						</div>
					</div>

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-welcome-widgets-menus"></i><?php esc_html_e( 'Change Menus', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Customize menu links and choose what&rsquo;s displayed in available theme menu locations.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons">
								<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="si-btn secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Go to Menus', 'sinatra' ); ?></a>
							</div><!-- END .sinatra-buttons -->
						</div>
					</div>

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-art"></i><?php esc_html_e( 'Change Colors', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Replace the default theme colors and make your website color scheme match your brand design.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons">
								<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=sinatra_section_colors' ) ); ?>" class="si-btn secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Change Colors', 'sinatra' ); ?></a>
							</div><!-- END .sinatra-buttons -->
						</div>
					</div>

					<div class="sinatra-column">
						<div class="sinatra-box">
							<h4><i class="dashicons dashicons-editor-help"></i><?php esc_html_e( 'Need Help?', 'sinatra' ); ?></h4>
							<p><?php esc_html_e( 'Head over to our site to learn more about the Sinatra theme, read help articles and get support.', 'sinatra' ); ?></p>

							<div class="sinatra-buttons">
								<a href="https://sinatrawp.com/docs/" target="_blank" rel="noopener noreferrer" class="si-btn secondary"><?php esc_html_e( 'Help Articles', 'sinatra' ); ?></a>
							</div><!-- END .sinatra-buttons -->
						</div>
					</div>
				</div><!-- END .sinatra-section -->

				<div class="sinatra-section large-section">
					<div class="sinatra-hero">
						<img src="<?php echo esc_url( SINATRA_THEME_URI . '/assets/images/si-customize.svg' ); ?>" alt="<?php echo esc_html( 'Customize' ); ?>" />
					</div>

					<h2><?php esc_html_e( 'Let‘s customize your website', 'sinatra' ); ?></h2>
					<p><?php esc_html_e( 'There are many changes you can make to customize your website. Explore Sinatra customization options and make it unique.', 'sinatra' ); ?></p>

					<div class="sinatra-buttons">
						<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="si-btn primary large-button"><?php esc_html_e( 'Start Customizing', 'sinatra' ); ?></a>
					</div><!-- END .sinatra-buttons -->

				</div><!-- END .sinatra-section -->

				<?php do_action( 'sinatra_about_content_after' ); ?>

			</div><!-- END .si-container -->

			<?php
		}

		/**
		 * Render the recommended plugins page.
		 *
		 * @since 1.0.0
		 */
		public function render_plugins() {

			// Render dashboard navigation.
			$this->render_navigation();

			$plugins = sinatra_plugin_utilities()->get_recommended_plugins();
			?>
			<div class="si-container">

				<div class="sinatra-section-title">
					<h2 class="sinatra-section-title"><?php esc_html_e( 'Recommended Plugins', 'sinatra' ); ?></h2>
				</div><!-- END .sinatra-section-title -->

				<div class="sinatra-section sinatra-columns plugins">

					<?php if ( is_array( $plugins ) && ! empty( $plugins ) ) { ?>
						<?php foreach ( $plugins as $plugin ) { ?>

							<?php
							// Check plugin status.
							if ( sinatra_plugin_utilities()->is_activated( $plugin['slug'] ) ) {
								$btn_class = 'si-btn secondary';
								$btn_text  = esc_html__( 'Deactivate', 'sinatra' );
								$action    = 'deactivate';
								$notice    = '<span class="si-active-plugin"><span class="dashicons dashicons-yes"></span>' . esc_html__( 'Plugin activated', 'sinatra' ) . '</span>';
							} elseif ( sinatra_plugin_utilities()->is_installed( $plugin['slug'] ) ) {
								$btn_class = 'si-btn primary';
								$btn_text  = esc_html__( 'Activate', 'sinatra' );
								$action    = 'activate';
								$notice    = '';
							} else {
								$btn_class = 'si-btn primary';
								$btn_text  = esc_html__( 'Install & Activate', 'sinatra' );
								$action    = 'install';
								$notice    = '';
							}
							?>

							<div class="sinatra-column column-6">
								<div class="sinatra-box">

									<div class="plugin-image">
										<img src="<?php echo esc_url( $plugin['thumb'] ); ?>" alt="<?php echo esc_html( $plugin['name'] ); ?>"/>					
									</div>

									<div class="plugin-info">
										<h4><?php echo esc_html( $plugin['name'] ); ?></h4>
										<p><?php echo esc_html( $plugin['desc'] ); ?></p>
										<div class="sinatra-buttons">
											<?php echo ( wp_kses_post( $notice ) ); ?>
											<a href="#" class="<?php echo esc_attr( $btn_class ); ?>" data-plugin="<?php echo esc_attr( $plugin['slug'] ); ?>" data-action="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $btn_text ); ?></a>
										</div>
									</div>

								</div>
							</div>
						<?php } ?>
					<?php } ?>

				</div><!-- END .sinatra-section -->

				<?php do_action( 'sinatra_recommended_plugins_after' ); ?>

			</div><!-- END .si-container -->

			<?php
		}

		/**
		 * Render the changelog page.
		 *
		 * @since 1.0.0
		 */
		public function render_changelog() {

			// Render dashboard navigation.
			$this->render_navigation();

			$changelog = SINATRA_THEME_PATH . '/changelog.txt';

			if ( ! file_exists( $changelog ) ) {
				$changelog = esc_html__( 'Changelog file not found.', 'sinatra' );
			} elseif ( ! is_readable( $changelog ) ) {
				$changelog = esc_html__( 'Changelog file not readable.', 'sinatra' );
			} else {
				global $wp_filesystem;

				// Check if the the global filesystem isn't setup yet.
				if ( is_null( $wp_filesystem ) ) {
					WP_Filesystem();
				}

				$changelog = $wp_filesystem->get_contents( $changelog );
			}

			?>
			<div class="si-container">

				<div class="sinatra-section-title">
					<h2 class="sinatra-section-title">
						<span><?php esc_html_e( 'Sinatra Theme Changelog', 'sinatra' ); ?></span>
						<span class="changelog-version"><?php echo esc_html( sprintf( 'v%1$s', SINATRA_THEME_VERSION ) ); ?></span>
					</h2>

				</div><!-- END .sinatra-section-title -->

				<div class="sinatra-section sinatra-columns">

					<div class="sinatra-column column-12">
						<div class="sinatra-box sinatra-changelog">
							<pre><?php echo esc_html( $changelog ); ?></pre>
						</div>
					</div>
				</div><!-- END .sinatra-columns -->

				<?php do_action( 'sinatra_after_changelog' ); ?>

			</div><!-- END .si-container -->
			<?php
		}

		/**
		 * Render admin page navigation tabs.
		 *
		 * @since 1.0.0
		 */
		public function render_navigation() {

			// Get navigation items.
			$menu_items = $this->get_navigation_items();

			?>
			<div class="si-container">

				<div class="sinatra-tabs">
					<ul>
						<?php
						// Determine current tab.
						$base = $this->get_current_page();

						// Display menu items.
						foreach ( $menu_items as $item ) {

							// Check if we're on a current item.
							$current = false !== strpos( $base, $item['id'] ) ? 'current-item' : '';
							?>

							<li class="<?php echo esc_attr( $current ); ?>">
								<a href="<?php echo esc_url( $item['url'] ); ?>">
									<?php echo esc_html( $item['name'] ); ?>

									<?php
									if ( isset( $item['icon'] ) && $item['icon'] ) {
										sinatra_print_admin_icon( $item['icon'] );
									}
									?>
								</a>
							</li>

						<?php } ?>
					</ul>
				</div><!-- END .sinatra-tabs -->

			</div><!-- END .si-container -->
			<?php
		}

		/**
		 * Return the current Sinatra Dashboard page.
		 *
		 * @since 1.0.0
		 * @return string $page Current dashboard page slug.
		 */
		public function get_current_page() {

			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'dashboard'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page = str_replace( 'sinatra-', '', $page );
			$page = apply_filters( 'sinatra_dashboard_current_page', $page );

			return esc_html( $page );
		}

		/**
		 * Print admin page navigation items.
		 *
		 * @since 1.0.0
		 * @return array $items Array of navigation items.
		 */
		public function get_navigation_items() {

			$items = array(
				'dashboard' => array(
					'id'   => 'dashboard',
					'name' => esc_html__( 'About', 'sinatra' ),
					'icon' => '',
					'url'  => menu_page_url( 'sinatra-dashboard', false ),
				),
				'plugins'   => array(
					'id'   => 'plugins',
					'name' => esc_html__( 'Recommended Plugins', 'sinatra' ),
					'icon' => '',
					'url'  => menu_page_url( 'sinatra-plugins', false ),
				),
				'changelog' => array(
					'id'   => 'changelog',
					'name' => esc_html__( 'Changelog', 'sinatra' ),
					'icon' => '',
					'url'  => menu_page_url( 'sinatra-changelog', false ),
				),
			);

			return apply_filters( 'sinatra_dashboard_navigation_items', $items );
		}

		/**
		 * Activate plugin.
		 *
		 * @since 1.0.0
		 */
		public function activate_plugin() {

			// Security check.
			check_ajax_referer( 'sinatra_nonce' );

			// Plugin data.
			$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';

			if ( empty( $plugin ) ) {
				wp_send_json_error( esc_html__( 'Missing plugin data', 'sinatra' ) );
			}

			if ( $plugin ) {

				$response = sinatra_plugin_utilities()->activate_plugin( $plugin );

				if ( is_wp_error( $response ) ) {
					wp_send_json_error( $response->get_error_message(), $response->get_error_code() );
				}

				wp_send_json_success();
			}

			wp_send_json_error( esc_html__( 'Failed to activate plugin. Missing plugin data.', 'sinatra' ) );
		}

		/**
		 * Deactivate plugin.
		 *
		 * @since 1.0.0
		 */
		public function deactivate_plugin() {

			// Security check.
			check_ajax_referer( 'sinatra_nonce' );

			// Plugin data.
			$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';

			if ( empty( $plugin ) ) {
				wp_send_json_error( esc_html__( 'Missing plugin data', 'sinatra' ) );
			}

			if ( $plugin ) {
				$response = sinatra_plugin_utilities()->deactivate_plugin( $plugin );

				if ( is_wp_error( $response ) ) {
					wp_send_json_error( $response->get_error_message(), $response->get_error_code() );
				}

				wp_send_json_success();
			}

			wp_send_json_error( esc_html__( 'Failed to deactivate plugin. Missing plugin data.', 'sinatra' ) );
		}

		/**
		 * Highlight dashboard page for plugins page.
		 *
		 * @since 1.0.0
		 * @param string $submenu_file The submenu file.
		 */
		public function highlight_submenu( $submenu_file ) {

			global $pagenow;

			// Check if we're on sinatra plugins or changelog page.
			if ( 'themes.php' === $pagenow ) {
				if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( 'sinatra-plugins' === $_GET['page'] || 'sinatra-changelog' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$submenu_file = 'sinatra-dashboard';
					}
				}
			}

			return $submenu_file;
		}
	}
endif;

/**
 * The function which returns the one Sinatra_Dashboard instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sinatra_dashboard = sinatra_dashboard(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function sinatra_dashboard() {
	return Sinatra_Dashboard::instance();
}

sinatra_dashboard();
