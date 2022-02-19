<?php
/**
 * Sinatra Customizer info control class.
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

if ( ! class_exists( 'Sinatra_Customizer_Control_Info' ) ) :
	/**
	 * Sinatra Customizer info control class.
	 */
	class Sinatra_Customizer_Control_Info extends Sinatra_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'sinatra-info';

		/**
		 * Custom URL.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $url = '';

		/**
		 * Link target.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $target = '_blank';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

			// Script debug.
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Control type.
			$sinatra_type = str_replace( 'sinatra-', '', $this->type );

			/**
			 * Enqueue control stylesheet
			 */
			wp_enqueue_style(
				'sinatra-' . $sinatra_type . '-control-style',
				SINATRA_THEME_URI . '/inc/customizer/controls/' . $sinatra_type . '/' . $sinatra_type . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION,
				'all'
			);
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['url']    = $this->url;
			$this->json['target'] = $this->target;
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 */
		protected function content_template() {
			?>
			<div class="sinatra-info-wrapper sinatra-control-wrapper">

				<# if ( data.label ) { #>
					<span class="sinatra-control-heading customize-control-title sinatra-field">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<div class="description customize-control-description sinatra-field sinatra-info-description">{{{ data.description }}}</div>
				<# } #>

				<a href="{{ data.url }}" class="button button-primary" target="{{ data.target }}" rel="noopener noreferrer"><?php esc_html_e( 'Learn More', 'sinatra' ); ?></a>

			</div><!-- END .sinatra-control-wrapper -->
			<?php
		}

	}
endif;
