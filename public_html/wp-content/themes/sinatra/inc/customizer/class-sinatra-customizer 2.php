<?php
/**
 * Sinatra Customizer class
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

if ( ! class_exists( 'Sinatra_Customizer' ) ) :
	/**
	 * Sinatra Customizer class
	 */
	class Sinatra_Customizer {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Customizer options.
		 *
		 * @since 1.0.0
		 * @var Array
		 */
		private static $options;

		/**
		 * Main Sinatra_Customizer Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Customizer
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Customizer ) ) {
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

			// Loads our Customizer custom controls.
			add_action( 'customize_register', array( $this, 'load_custom_controls' ) );

			// Loads our Customizer helper functions.
			add_action( 'customize_register', array( $this, 'load_customizer_helpers' ) );

			// Loads our Customizer widgets classes.
			add_action( 'customize_register', array( $this, 'load_customizer_widgets' ) );

			// Tweak inbuilt sections.
			add_action( 'customize_register', array( $this, 'customizer_tweak' ), 11 );

			// Registers our Customizer options.
			add_action( 'after_setup_theme', array( $this, 'register_options' ) );

			// Registers our Customizer options.
			add_action( 'customize_register', array( $this, 'register_options_new' ) );

			// Loads our Customizer controls assets.
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'load_assets' ), 10 );

			// Enqueues our Customizer preview assets.
			add_action( 'customize_preview_init', array( $this, 'load_preview_assets' ) );

			// Add available top bar widgets panel.
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'sinatra_customizer_widgets' ) );
			add_action( 'customize_controls_print_footer_scripts', array( 'Sinatra_Customizer_Control', 'template_units' ) );
		}

		/**
		 * Loads our Customizer custom controls.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $customizer Instance of WP_Customize_Manager class.
		 */
		public function load_custom_controls( $customizer ) {

			// Directory where each custom control is located.
			$path = SINATRA_THEME_PATH . '/inc/customizer/controls/';

			// Require base control class.
			require $path . '/class-sinatra-customizer-control.php'; // phpcs:ignore

			$controls = $this->get_custom_controls();

			// Load custom controls classes.
			foreach ( $controls as $control => $class ) {
				$control_path = $path . '/' . $control . '/class-sinatra-customizer-control-' . $control . '.php';
				if ( file_exists( $control_path ) ) {
					require_once $control_path; // phpcs:ignore
					$customizer->register_control_type( $class );
				}
			}
		}

		/**
		 * Loads Customizer helper functions and sanitization callbacks.
		 *
		 * @since 1.0.0
		 */
		public function load_customizer_helpers() {
			require SINATRA_THEME_PATH . '/inc/customizer/customizer-helpers.php'; // phpcs:ignore
			require SINATRA_THEME_PATH . '/inc/customizer/customizer-callbacks.php'; // phpcs:ignore
			require SINATRA_THEME_PATH . '/inc/customizer/customizer-partials.php'; // phpcs:ignore
		}

		/**
		 * Loads Customizer widgets classes.
		 *
		 * @since 1.0.0
		 */
		public function load_customizer_widgets() {

			$widgets = sinatra_get_customizer_widgets();

			require SINATRA_THEME_PATH . '/inc/customizer/widgets/class-sinatra-customizer-widget.php'; // phpcs:ignore

			foreach ( $widgets as $id => $class ) {

				$path = SINATRA_THEME_PATH . '/inc/customizer/widgets/class-sinatra-customizer-widget-' . $id . '.php';

				if ( file_exists( $path ) ) {
					require $path; // phpcs:ignore
				}
			}
		}

		/**
		 * Move inbuilt panels into our sections.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $customizer Instance of WP_Customize_Manager class.
		 */
		public static function customizer_tweak( $customizer ) {

			// Site Identity to Logo.
			$customizer->get_section( 'title_tagline' )->priority = 2;
			$customizer->get_section( 'title_tagline' )->title    = esc_html__( 'Logos &amp; Site Title', 'sinatra' );

			// Custom logo.
			$customizer->get_control( 'custom_logo' )->description = esc_html__( 'Upload your logo image here.', 'sinatra' );
			$customizer->get_control( 'custom_logo' )->priority    = 10;
			$customizer->get_setting( 'custom_logo' )->transport   = 'postMessage';

			// Add selective refresh partial for Custom Logo.
			$customizer->selective_refresh->add_partial(
				'custom_logo',
				array(
					'selector'            => '.sinatra-logo',
					'render_callback'     => 'sinatra_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				)
			);

			// Site title.
			$customizer->get_setting( 'blogname' )->transport   = 'postMessage';
			$customizer->get_control( 'blogname' )->description = esc_html__( 'Enter the name of your site here.', 'sinatra' );
			$customizer->get_control( 'blogname' )->priority    = 60;

			// Site description.
			$customizer->get_setting( 'blogdescription' )->transport   = 'postMessage';
			$customizer->get_control( 'blogdescription' )->description = esc_html__( 'A tagline is a short phrase, or sentence, used to convey the essence of the site.', 'sinatra' );
			$customizer->get_control( 'blogdescription' )->priority    = 70;

			// Site icon.
			$customizer->get_control( 'site_icon' )->priority = 90;

			// Site Background.
			$background_fields = array(
				'background_color',
				'background_image',
				'background_preset',
				'background_position',
				'background_size',
				'background_repeat',
				'background_attachment',
				'background_image',
			);

			foreach ( $background_fields as $field ) {
				$customizer->get_control( $field )->section  = 'sinatra_section_colors';
				$customizer->get_control( $field )->priority = 50;
			}

			// Load the custom section class.
			require SINATRA_THEME_PATH . '/inc/customizer/class-sinatra-customizer-info-section.php'; // phpcs:ignore

			// Register custom section types.
			$customizer->register_section_type( 'Sinatra_Customizer_Info_Section' );
		}

		/**
		 * Registers our Customizer options.
		 *
		 * @since 1.0.0
		 */
		public function register_options() {

			// Directory where each individual section is located.
			$path = SINATRA_THEME_PATH . '/inc/customizer/settings/class-sinatra-customizer-';

			/**
			 * Customizer sections.
			 */
			$sections = array(
				'sections',
				'colors',
				'typography',
				'layout',
				'top-bar',
				'main-header',
				'main-navigation',
				'hero',
				'page-header',
				'logo',
				'single-post',
				'blog-page',
				'main-footer',
				'copyright-settings',
				'pre-footer',
				'buttons',
				'misc',
				'transparent-header',
				'sticky-header',
				'sidebar',
				'breadcrumbs',
			);

			foreach ( $sections as $section ) {
				if ( file_exists( $path . $section . '.php' ) ) {
					require_once $path . $section . '.php'; // phpcs:ignore
				}
			}
		}

		/**
		 * Registers our Customizer options.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		public function register_options_new( $customizer ) {

			$options = $this->get_customizer_options();

			if ( isset( $options['panel'] ) && ! empty( $options['panel'] ) ) {
				foreach ( $options['panel'] as $id => $args ) {
					$this->add_panel( $id, $args, $customizer );
				}
			}

			if ( isset( $options['section'] ) && ! empty( $options['section'] ) ) {
				foreach ( $options['section'] as $id => $args ) {
					$this->add_section( $id, $args, $customizer );
				}
			}

			if ( isset( $options['setting'] ) && ! empty( $options['setting'] ) ) {
				foreach ( $options['setting'] as $id => $args ) {
					$this->add_setting( $id, $args, $customizer );
					$this->add_control( $id, $args['control'], $customizer );
				}
			}
		}

		/**
		 * Filter and return Customizer options.
		 *
		 * @since 1.0.0
		 *
		 * @return Array Customizer options for registering Sections/Panels/Controls.
		 */
		public function get_customizer_options() {
			if ( ! is_null( self::$options ) ) {
				return self::$options;
			}

			return apply_filters( 'sinatra_customizer_options', array() );
		}

		/**
		 * Register Customizer Panel.
		 *
		 * @since 1.0.0
		 *
		 * @param Array                $panel Panel settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_panel( $id, $args, $customizer ) {
			$class = sinatra_get_prop( $args, 'class', 'WP_Customize_Panel' );

			$customizer->add_panel( new $class( $customizer, $id, $args ) );
		}

		/**
		 * Register Customizer Section.
		 *
		 * @since 1.0.0
		 *
		 * @param Array                $section Section settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_section( $id, $args, $customizer ) {
			$class = sinatra_get_prop( $args, 'class', 'WP_Customize_Section' );

			$customizer->add_section( new $class( $customizer, $id, $args ) );
		}

		/**
		 * Register Customizer Control.
		 *
		 * @since 1.0.0
		 *
		 * @param Array                $control Control settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_control( $id, $args, $customizer ) {
			$class = $this->get_control_class( sinatra_get_prop( $args, 'type' ) );

			$args['setting'] = $id;

			if ( false !== $class ) {
				$customizer->add_control( new $class( $customizer, $id, $args ) );
			} else {
				$customizer->add_control( $id, $args );
			}
		}

		/**
		 * Register Customizer Setting.
		 *
		 * @since 1.0.0
		 *
		 * @param Array                $setting Settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_setting( $id, $setting, $customizer ) {
			$setting = wp_parse_args( $setting, $this->get_customizer_defaults( 'setting' ) );

			$customizer->add_setting(
				$id,
				array(
					'default'           => sinatra()->options->get_default( $id ),
					'type'              => sinatra_get_prop( $setting, 'type' ),
					'transport'         => sinatra_get_prop( $setting, 'transport' ),
					'sanitize_callback' => sinatra_get_prop( $setting, 'sanitize_callback', 'sinatra_no_sanitize' ),
				)
			);

			$partial = sinatra_get_prop( $setting, 'partial', false );

			if ( $partial && isset( $customizer->selective_refresh ) ) {

				$customizer->selective_refresh->add_partial(
					$id,
					array(
						'selector'            => sinatra_get_prop( $partial, 'selector' ),
						'container_inclusive' => sinatra_get_prop( $partial, 'container_inclusive' ),
						'render_callback'     => sinatra_get_prop( $partial, 'render_callback' ),
						'fallback_refresh'    => sinatra_get_prop( $partial, 'fallback_refresh' ),
					)
				);
			}
		}

		/**
		 * Return custom controls.
		 *
		 * @since 1.0.0
		 *
		 * @return Array custom control slugs & classnames.
		 */
		private function get_custom_controls() {
			return apply_filters(
				'sinatra_custom_customizer_controls',
				array(
					'toggle'         => 'Sinatra_Customizer_Control_Toggle',
					'select'         => 'Sinatra_Customizer_Control_Select',
					'heading'        => 'Sinatra_Customizer_Control_Heading',
					'color'          => 'Sinatra_Customizer_Control_Color',
					'range'          => 'Sinatra_Customizer_Control_Range',
					'spacing'        => 'Sinatra_Customizer_Control_Spacing',
					'widget'         => 'Sinatra_Customizer_Control_Widget',
					'radio-image'    => 'Sinatra_Customizer_Control_Radio_Image',
					'background'     => 'Sinatra_Customizer_Control_Background',
					'text'           => 'Sinatra_Customizer_Control_Text',
					'textarea'       => 'Sinatra_Customizer_Control_Textarea',
					'typography'     => 'Sinatra_Customizer_Control_Typography',
					'button'         => 'Sinatra_Customizer_Control_Button',
					'sortable'       => 'Sinatra_Customizer_Control_Sortable',
					'info'           => 'Sinatra_Customizer_Control_Info',
					'design-options' => 'Sinatra_Customizer_Control_Design_Options',
					'alignment'      => 'Sinatra_Customizer_Control_Alignment',
					'checkbox-group' => 'Sinatra_Customizer_Control_Checkbox_Group',
				)
			);
		}

		/**
		 * Return default values for customizer parts.
		 *
		 * @since 1.0.0
		 *
		 * @return Array default values for the Customizer Configurations.
		 */
		private function get_customizer_defaults( $type ) {

			$defaults = array();

			switch ( $type ) {
				case 'setting':
					$defaults = array(
						'type'      => 'theme_mod',
						'transport' => 'refresh',
					);
					break;

				case 'control':
					$defaults = array();
					break;

				default:
					break;
			}

			return apply_filters(
				'sinatra_customizer_configuration_defaults',
				$defaults,
				$type
			);
		}

		/**
		 * Get custom control classname.
		 *
		 * @since 1.0.0
		 *
		 * @param string $control Control ID.
		 *
		 * @return string Control classname.
		 */
		private function get_control_class( $type ) {

			if ( false !== strpos( $type, 'sinatra-' ) ) {

				$controls = $this->get_custom_controls();
				$type     = trim( str_replace( 'sinatra-', '', $type ) );

				if ( isset( $controls[ $type ] ) ) {
					return $controls[ $type ];
				}
			}

			return false;
		}

		/**
		 * Loads our own Customizer assets.
		 *
		 * @since 1.0.0
		 */
		public function load_assets() {

			// Script debug.
			$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			/**
			 * Enqueue our Customizer styles.
			 */
			wp_enqueue_style(
				'sinatra-customizer-styles',
				SINATRA_THEME_URI . '/inc/customizer/assets/css/sinatra-customizer' . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION
			);

			/**
			 * Enqueue our Customizer controls script.
			 */
			wp_enqueue_script(
				'sinatra-customizer-js',
				SINATRA_THEME_URI . '/inc/customizer/assets/js/' . $sinatra_dir . 'customize-controls' . $sinatra_suffix . '.js',
				array( 'wp-color-picker', 'jquery', 'customize-base' ),
				SINATRA_THEME_VERSION,
				true
			);

			/**
			 * Enqueue Customizer controls dependency script.
			 */
			wp_enqueue_script(
				'sinatra-control-dependency-js',
				SINATRA_THEME_URI . '/inc/customizer/assets/js/' . $sinatra_dir . 'customize-dependency' . $sinatra_suffix . '.js',
				array( 'jquery' ),
				SINATRA_THEME_VERSION,
				true
			);

			/**
			 * Localize JS variables
			 */
			$sinatra_customizer_localized = array(
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'wpnonce'                 => wp_create_nonce( 'sinatra_customizer' ),
				'color_palette'           => array( '#ffffff', '#000000', '#e4e7ec', '#3857F1', '#f7b40b', '#e04b43', '#30373e', '#8a63d4' ),
				'preview_url_for_section' => $this->get_preview_urls_for_section(),
				'strings'                 => array(
					'selectCategory' => esc_html__( 'Select a category', 'sinatra' ),
				),
			);

			/**
			 * Allow customizer localized vars to be filtered.
			 */
			$sinatra_customizer_localized = apply_filters( 'sinatra_customizer_localized', $sinatra_customizer_localized );

			wp_localize_script(
				'sinatra-customizer-js',
				'sinatra_customizer_localized',
				$sinatra_customizer_localized
			);
		}

		/**
		 * Loads customizer preview assets
		 *
		 * @since 1.0.0
		 */
		public function load_preview_assets() {

			// Script debug.
			$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : SINATRA_THEME_VERSION;

			wp_enqueue_script(
				'sinatra-customizer-preview-js',
				SINATRA_THEME_URI . '/inc/customizer/assets/js/' . $sinatra_dir . 'customize-preview' . $sinatra_suffix . '.js',
				array( 'customize-preview', 'customize-selective-refresh', 'jquery' ),
				$version,
				true
			);

			// Enqueue Customizer preview styles.
			wp_enqueue_style(
				'sinatra-customizer-preview-styles',
				SINATRA_THEME_URI . '/inc/customizer/assets/css/sinatra-customizer-preview' . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION
			);

			/**
			 * Localize JS variables.
			 */
			$sinatra_customizer_localized = array(
				'default_system_font' => sinatra()->fonts->get_default_system_font(),
				'fonts'               => sinatra()->fonts->get_fonts(),
				'google_fonts_url'    => '//fonts.googleapis.com',
				'google_font_weights' => '100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i',
			);

			/**
			 * Allow customizer localized vars to be filtered.
			 */
			$sinatra_customizer_localized = apply_filters( 'sinatra_customize_preview_localized', $sinatra_customizer_localized );

			wp_localize_script(
				'sinatra-customizer-preview-js',
				'sinatra_customizer_preview',
				$sinatra_customizer_localized
			);
		}

		/**
		 * Print the html template used to render the add top bar widgets frame.
		 *
		 * @since 1.0.0
		 */
		public function sinatra_customizer_widgets() {

			// Get customizer widgets.
			$widgets = sinatra_get_customizer_widgets();

			// Check if any available widgets exist.
			if ( ! is_array( $widgets ) || empty( $widgets ) ) {
				return;
			}
			?>
			<div id="sinatra-available-widgets">

				<div class="sinatra-widget-caption">
					<h3></h3>
					<a href="#" class="sinatra-close-widgets-panel"></a>
				</div><!-- END #sinatra-available-widgets-caption -->

				<div id="sinatra-available-widgets-list">

					<?php foreach ( $widgets as $id => $classname ) { ?>
						<?php $widget = new $classname(); ?>

						<div id="sinatra-widget-tpl-<?php echo esc_attr( $widget->id_base ); ?>" data-widget-id="<?php echo esc_attr( $widget->id_base ); ?>" class="sinatra-widget">
							<?php $widget->template(); ?>
						</div>

					<?php } ?>

				</div><!-- END #sinatra-available-widgets-list -->
			</div>
			<?php
		}

		/**
		 * Get preview URL for a section. The URL will load when the section is opened.
		 *
		 * @return string
		 */
		public function get_preview_urls_for_section() {

			$return = array();

			// Preview a random single post for Single Post section.
			$posts = get_posts(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 1,
					'orderby'        => 'rand',
				)
			);

			if ( count( $posts ) ) {
				$return['sinatra_section_blog_single_post'] = get_permalink( $posts[0] );
			}

			// Preview blog page.
			$return['sinatra_section_blog_page'] = sinatra_get_blog_url();

			return $return;
		}
	}
endif;
