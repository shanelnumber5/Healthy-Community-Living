<?php
/**
 * Meta Boxes class.
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

if ( ! class_exists( 'Sinatra_Meta_Boxes' ) ) :

	/**
	 * Meta Boxes Class.
	 */
	class Sinatra_Meta_Boxes {

		/**
		 * Singleton instance.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Metabox settings array.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $metabox = array();

		/**
		 * Whether this is a new post. Once the post is saved and we're
		 * no longer on the `post-new.php` screen, this is going to be
		 * `false`.
		 *
		 * @since 1.0.0
		 * @var   bool
		 */
		public $is_new_post = false;

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				self::$instance->actions();
			}

			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {}

		/**
		 * Sets up initial actions.
		 *
		 * @since  1.0.0
		 */
		private function actions() {

			// Call the register function.
			add_action( 'load-post.php', array( $this, 'register' ) );
			add_action( 'load-post-new.php', array( $this, 'register' ) );

			// Load assets.
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		}

		/**
		 * Registration callback.
		 *
		 * @since  1.0.0
		 */
		public function register() {

			// If this is a new post, set the new post boolean.
			if ( 'load-post-new.php' === current_action() ) {
				$this->is_new_post = true;
			}

			// Add meta boxes.
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 1 );

			// Save settings.
			add_action( 'save_post', array( $this, 'update' ) );

			// Config.
			add_action( 'pre_get_posts', array( $this, 'metabox_config' ) );
		}

		/**
		 * Metabox config array
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function metabox_config() {

			/**
			 * Layout settings.
			 */
			$layout_settings = array();

			// Sidebar position.
			$layout_settings['sinatra_sidebar_position'] = array(
				'id'      => 'sinatra_sidebar_position',
				'name'    => esc_html__( 'Sidebar', 'sinatra' ),
				'type'    => 'select',
				'default' => 'default',
				'choices' => array(
					''              => esc_html__( 'Default (from Customizer)', 'sinatra' ),
					'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
					'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
					'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
				),
			);

			// Content layout.
			$layout_settings['sinatra_content_layout'] = array(
				'id'      => 'sinatra_content_layout',
				'name'    => esc_html__( 'Content Layout', 'sinatra' ),
				'type'    => 'select',
				'default' => 'default',
				'choices' => array(
					''                => esc_html__( 'Default (from Customizer)', 'sinatra' ),
					'fw-contained'    => esc_html__( 'Full Width: Contained', 'sinatra' ),
					'fw-stretched'    => esc_html__( 'Full Width: Stretched', 'sinatra' ),
					'boxed-separated' => esc_html__( 'Boxed Content', 'sinatra' ),
					'boxed'           => esc_html__( 'Boxed', 'sinatra' ),
				),
			);

			// Transparent header.
			$layout_settings['sinatra_transparent_header'] = array(
				'id'      => 'sinatra_transparent_header',
				'name'    => esc_html__( 'Transparent Header', 'sinatra' ),
				'type'    => 'select',
				'default' => '',
				'choices' => array(
					''        => esc_html__( 'Default (from Customizer)', 'sinatra' ),
					'enable'  => esc_html__( 'Enable', 'sinatra' ),
					'disable' => esc_html__( 'Disable', 'sinatra' ),
				),
			);

			// Start checkbox group.
			$layout_settings['sinatra_page_sections'] = array(
				'id'     => 'sinatra_page_sections',
				'type'   => 'group',
				'fields' => array(),
			);

			// Disable Top Bar.
			if ( sinatra_option( 'top_bar_enable' ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_topbar'] = array(
					'id'      => 'sinatra_disable_topbar',
					'name'    => esc_html__( 'Disable Top Bar', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Disable Header.
			$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_header'] = array(
				'id'      => 'sinatra_disable_header',
				'name'    => esc_html__( 'Disable Main Header', 'sinatra' ),
				'default' => false,
				'type'    => 'checkbox',
			);

			// Disable Page Title.
			if ( sinatra_option( 'page_header_enable' ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_page_title'] = array(
					'id'      => 'sinatra_disable_page_title',
					'name'    => esc_html__( 'Disable Page Title', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Disable Page Breadcrumbs.
			if ( sinatra_option( 'page_header_enable' ) && sinatra_option( 'breadcrumbs_enable' ) && ! sinatra_is_section_disabled( sinatra_option( 'breadcrumbs_hide_on' ), get_the_ID() ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_breadcrumbs'] = array(
					'id'      => 'sinatra_disable_breadcrumbs',
					'name'    => esc_html__( 'Disable Breadcrumbs', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Disable Featured Image.
			$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_thumbnail'] = array(
				'id'      => 'sinatra_disable_thumbnail',
				'name'    => esc_html__( 'Disable Featured Image', 'sinatra' ),
				'default' => false,
				'type'    => 'checkbox',
			);

			// Disable Prefooter.
			if ( sinatra_option( 'enable_pre_footer_cta' ) && ! sinatra_is_section_disabled( sinatra_option( 'pre_footer_cta_hide_on' ), get_the_ID() ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_prefooter_cta'] = array(
					'id'      => 'sinatra_disable_prefooter_cta',
					'name'    => esc_html__( 'Disable Pre Footer CTA', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Disable Footer.
			if ( sinatra_option( 'enable_footer' ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_footer'] = array(
					'id'      => 'sinatra_disable_footer',
					'name'    => esc_html__( 'Disable Main Footer', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Disable Copyright.
			if ( sinatra_option( 'enable_copyright' ) ) {
				$layout_settings['sinatra_page_sections']['fields']['sinatra_disable_copyright'] = array(
					'id'      => 'sinatra_disable_copyright',
					'name'    => esc_html__( 'Disable Copyright Bar', 'sinatra' ),
					'default' => false,
					'type'    => 'checkbox',
				);
			}

			// Set metabox options.
			$this->metabox = array(
				'sinatra-theme-page-options' => array(
					'id'         => 'sinatra-theme-page-options',
					'post_type'  => $this->get_post_types(),
					'capability' => 'edit_posts',
					'label'      => esc_html__( 'Theme Page Options', 'sinatra' ),
					'context'    => 'side',
					'priority'   => 'core',
					'settings'   => $layout_settings,
				),
			);

			$this->metabox = apply_filters( 'sinatra_metabox_options', $this->metabox );
		}

		/**
		 * Register Metabox.
		 *
		 * @since 1.0.0
		 * @param string $post_type Post Type of the current post page.
		 */
		public function add_meta_boxes( $post_type ) {

			if ( ! is_array( $this->metabox ) ) {
				return;
			}

			foreach ( $this->metabox as $id => $metabox ) {

				// If the manager is registered for the current post type, add a meta box.
				if ( in_array( $post_type, (array) $metabox['post_type'], true ) && $this->check_capability( $metabox['capability'] ) ) {

					add_meta_box(
						$metabox['id'],
						$metabox['label'],
						array( $this, 'render' ),
						$post_type,
						$metabox['context'],
						$metabox['priority'],
						array( 'settings' => $metabox['settings'] )
					);
				}
			}
		}

		/**
		 * Update Metabox value.
		 *
		 * @since 1.0.0
		 * @param int $post_id Post ID.
		 */
		public function update( $post_id ) {

			$do_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
			$is_autosave = wp_is_post_autosave( $post_id );
			$is_revision = wp_is_post_revision( $post_id );

			if ( $do_autosave || $is_autosave || $is_revision ) {
				return;
			}

			// Loop through all metaboxes.
			foreach ( $this->metabox as $id => $metabox ) {

				// Capability check.
				if ( $this->check_capability( $metabox['capability'] ) && isset( $_POST[ $metabox['id'] ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $metabox['id'] ] ) ), $metabox['id'] . '_nonce' ) ) { // phpcs:ignore

					// Loop through metabox settings.
					foreach ( $metabox['settings'] as $setting ) {

						// Group has nested settings.
						if ( 'group' === $setting['type'] ) {
							foreach ( $setting['fields'] as $id => $config ) {
								$this->save_value( $post_id, $config );
							}
						} else {
							$this->save_value( $post_id, $setting );
						}
					}
				}
			}
		}

		/**
		 * Save the new value to postmeta database.
		 *
		 * @since  1.0.0
		 * @param  object $post_id Post object ID.
		 * @param  array  $setting Metabox config array.
		 * @return void
		 */
		public function save_value( $post_id, $setting ) {

			// Get stored value.
			$old_value = $this->get_value( $post_id, $setting );

			// Get new value.
			$new_value = $this->get_posted_value( $setting );

			if ( ! $new_value && $old_value ) {
				delete_post_meta( $post_id, $setting['id'] );
			} elseif ( $new_value !== $old_value ) {
				update_post_meta( $post_id, $setting['id'], $new_value );
			}
		}

		/**
		 * Print the meta box HTML Markup.
		 *
		 * @since  1.0.0
		 * @param  object $post    Post object.
		 * @param  array  $metabox Metabox config array.
		 * @return void
		 */
		public function render( $post, $metabox ) {

			if ( empty( $metabox['args']['settings'] ) ) {
				return;
			}

			foreach ( $metabox['args']['settings'] as $setting ) {

				echo '<div class="sinatra-meta-box-field">';

				if ( isset( $setting['render_field_callback'] ) && function_exists( $setting['render_field_callback'] ) ) {
					call_user_func( $setting['render_field_callback'], $post, $setting );
				} elseif ( method_exists( $this, 'render_field_' . $setting['type'] ) ) {
					call_user_func( array( $this, 'render_field_' . $setting['type'] ), $post, $setting );
				}

				echo '</div>';
			}

			// Nonce field to validate on save.
			wp_nonce_field( $metabox['id'] . '_nonce', $metabox['id'] );
		}

		/**
		 * Render select field.
		 *
		 * @since 1.0.0
		 * @param object $post    Current post object.
		 * @param array  $setting Array of field settings.
		 */
		public function render_field_select( $post, $setting ) {

			$saved_value = get_post_meta( $post->ID, $setting['id'], true );
			?>

			<div class="components-base-control">
				<div class="components-base-control__field">

					<?php if ( isset( $setting['name'] ) ) { ?>
						<label class="post-attributes-label-wrapper components-base-control__label" for="<?php echo esc_attr( $setting['id'] ); ?>">
							<?php echo esc_html( $setting['name'] ); ?>
						</label>
					<?php } ?>

					<select name="<?php echo esc_attr( $setting['id'] ); ?>" id="<?php echo esc_attr( $setting['id'] ); ?>">
						<?php foreach ( $setting['choices'] as $key => $value ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $saved_value, $key ); ?>><?php echo esc_html( $value ); ?></option>
						<?php } ?>
					</select>

				</div>
			</div>

			<?php if ( isset( $setting['desc'] ) ) { ?>
				<p class="post-attributes-description-wrapper components-base-control__help description">
					<?php echo esc_html( $setting['desc'] ); ?>
				</p>
			<?php } ?>
			<?php
		}

		/**
		 * Render group of fields.
		 *
		 * @since 1.0.0
		 * @param object $post    Current post object.
		 * @param array  $setting Array of field settings.
		 */
		public function render_field_group( $post, $setting ) {
			?>

			<?php if ( isset( $setting['name'] ) ) { ?>
				<p class="post-attributes-label-wrapper">
					<strong><?php echo esc_html( $setting['name'] ); ?></strong>
				</p>
			<?php } ?>

			<?php if ( isset( $setting['desc'] ) ) { ?>
				<p class="post-attributes-description-wrapper">
					<?php echo esc_html( $setting['desc'] ); ?>
				</p>
			<?php } ?>

			<?php
			foreach ( $setting['fields'] as $key => $value ) {
				if ( method_exists( $this, 'render_field_' . $value['type'] ) ) {
					call_user_func( array( $this, 'render_field_' . $value['type'] ), $post, $value );
				}
			}
		}

		/**
		 * Render checkbox group field.
		 *
		 * @since 1.0.0
		 * @param object $post    Current post object.
		 * @param array  $setting Array of field settings.
		 */
		public function render_field_checkbox( $post, $setting ) {

			$saved = $this->get_value( $post->ID, $setting );
			?>
			<div class="components-panel__row sinatra-checkbox-row">
				<div class="components-base-control">
					<div class="components-base-control__field">
						<span class="components-checkbox-control__input-container si-checkbox-wrapper">
							<input type="checkbox" class="components-checkbox-control__input" id="<?php echo esc_attr( $setting['id'] ); ?>" name="<?php echo esc_attr( $setting['id'] ); ?>"<?php checked( $saved, true ); ?>>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" role="img" class="si-checked-icon components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M9 18.6L3.5 13l1-1L9 16.4l9.5-9.9 1 1z"></path></svg>
						</span>
						<label class="components-checkbox-control__label" for="<?php echo esc_attr( $setting['id'] ); ?>"><?php echo esc_html( $setting['name'] ); ?></label>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Render textarea field.
		 *
		 * @since 1.0.0
		 * @param object $post    Current post object.
		 * @param array  $setting Array of field settings.
		 */
		public function render_field_textarea( $post, $setting ) {

			$saved = $this->get_value( $post->ID, $setting );
			?>

			<div class="components-base-control">
				<div class="components-base-control__field">

					<?php if ( isset( $setting['name'] ) ) { ?>
						<label class="post-attributes-label-wrapper components-base-control__label" for="<?php echo esc_attr( $setting['id'] ); ?>">
							<?php echo esc_html( $setting['name'] ); ?>
						</label>
					<?php } ?>

					<textarea class="widefat" id="<?php echo esc_attr( $setting['id'] ); ?>" name="<?php echo esc_attr( $setting['id'] ); ?>" rows="5"><?php echo wp_kses_post( $saved ); ?></textarea>

				</div>
			</div>

			<?php if ( isset( $setting['desc'] ) ) { ?>
				<p class="post-attributes-description-wrapper components-base-control__help description">
					<?php echo wp_kses_post( $setting['desc'] ); ?>
				</p>
			<?php } ?>

			<?php
		}

		/**
		 * Render text field.
		 *
		 * @since 1.0.0
		 * @param object $post    Current post object.
		 * @param array  $setting Array of field settings.
		 */
		public function render_field_text( $post, $setting ) {

			$saved = $this->get_value( $post->ID, $setting );
			?>

			<div class="components-base-control">
				<div class="components-base-control__field">

					<?php if ( isset( $setting['name'] ) ) { ?>
						<label class="post-attributes-label-wrapper components-base-control__label" for="<?php echo esc_attr( $setting['id'] ); ?>">
							<?php echo esc_html( $setting['name'] ); ?>
						</label>
					<?php } ?>

					<input type="text" class="widefat" id="<?php echo esc_attr( $setting['id'] ); ?>" name="<?php echo esc_attr( $setting['id'] ); ?>" value="<?php echo wp_kses_post( $saved ); ?>" />

				</div>
			</div>

			<?php if ( isset( $setting['desc'] ) ) { ?>
				<p class="post-attributes-description-wrapper components-base-control__help description">
					<?php echo wp_kses_post( $setting['desc'] ); ?>
				</p>
			<?php } ?>

			<?php
		}

		/**
		 * Load required assets on the admin page(s).
		 *
		 * @since 1.0.0
		 * @param string $hook Current page hook.
		 */
		public function load_assets( $hook ) {

			global $post;

			if ( 'post-new.php' === $hook || 'post.php' === $hook ) {

				$post_type  = $post->post_type;
				$post_types = array();

				foreach ( $this->metabox as $mb ) {
					$post_types = array_merge( $post_types, (array) $mb['post_type'] );
				}

				$post_types = array_unique( $post_types );

				if ( in_array( $post_type, $post_types, true ) ) {

					// Script debug.
					$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
					$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

					// Enqueue editor style.
					wp_enqueue_style(
						'sinatra-metabox-style',
						SINATRA_THEME_URI . '/inc/admin/assets/css/sinatra-meta-boxes' . $sinatra_suffix . '.css',
						false,
						SINATRA_THEME_VERSION
					);
				}
			}
		}

		/**
		 * Gets the value of the setting.
		 *
		 * @since 1.0.0
		 * @param int   $post_id Post ID.
		 * @param array $setting Array of setting data.
		 */
		public function get_value( $post_id, $setting ) {

			$value = get_post_meta( $post_id, $setting['id'], true );

			return ! $value && $this->is_new_post ? $setting['default'] : $value;
		}

		/**
		 * Gets the posted value of the setting.
		 *
		 * @since 1.0.0
		 * @param array $setting Setting config array.
		 */
		public function get_posted_value( $setting ) {

			$value = '';

			if ( isset( $_POST[ $setting['id'] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$value = wp_unslash( $_POST[ $setting['id'] ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}

			return $this->sanitize( $value, $setting );
		}

		/**
		 * Return array of post types for metabox.
		 *
		 * @since 1.0.0
		 * @param array $args Args for get_post_types function.
		 */
		public function get_post_types( $args = false ) {

			if ( ! $args ) {
				$args = array(
					'public' => true,
				);
			}

			$return = get_post_types( $args );

			return $return;
		}

		/**
		 * Sanitizes the value of the setting.
		 *
		 * @since 1.0.0
		 * @param mixed $value   Value of the field to be sanitized.
		 * @param arary $setting Setting config array.
		 */
		public function sanitize( $value, $setting ) {

			$filter = isset( $setting['type'] ) ? $setting['type'] : '';

			switch ( $filter ) {
				case 'select':
					// Ensure input is a slug.
					$value = sanitize_key( $value );

					// Get list of choices from the setting.
					$choices = $setting['choices'];

					// If the input is a valid key, return it; otherwise, return the default.
					$value = array_key_exists( $value, $choices ) ? $value : $setting['default'];
					break;
				case 'checkbox':
					// Ensure boolean value for $value.
					$value = (bool) $value;
					break;
				case 'text':
					$value = sanitize_text_field( $value );
					break;
				case 'textarea':
					$value = wp_kses_post( $value );
					break;
				case 'no_sanitize':
				default:
					break;
			}

			return $value;
		}

		/**
		 * Checks if the control should be allowed at all.
		 *
		 * @since 1.0.0
		 * @param string $cap Capability.
		 */
		public function check_capability( $cap ) {

			if ( ! current_user_can( $cap ) ) {
				return false;
			}

			return true;
		}
	}
endif;

/**
 * The function which returns the one Sinatra Meta Boxes instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function sinatra_meta_boxes() {
	return Sinatra_Meta_Boxes::get_instance();
}

sinatra_meta_boxes();
