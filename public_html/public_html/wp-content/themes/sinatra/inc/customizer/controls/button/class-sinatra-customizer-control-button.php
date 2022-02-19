<?php
/**
 * Sinatra Customizer custom background control class.
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

if ( ! class_exists( 'Sinatra_Customizer_Control_Button' ) ) :
	/**
	 * Sinatra Customizer custom background control class.
	 */
	class Sinatra_Customizer_Control_Button extends Sinatra_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'sinatra-button';

		/**
		 * Ajax Action.
		 *
		 * @var string
		 */
		public $ajax_action = '';

		/**
		 * Button text.
		 *
		 * @var string
		 */
		public $button_text = '';

		/**
		 * Button url.
		 *
		 * @var string
		 */
		public $button_url = '#';

		/**
		 * Link target.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $target = '_blank';

		/**
		 * Set the default typography options.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @param string               $id      Control ID.
		 * @param array                $args    Default parent's arguments.
		 */
		public function __construct( $manager, $id, $args = array() ) {

			parent::__construct( $manager, $id, $args );

			if ( $this->ajax_action ) {
				$this->button_url = '#';
			}
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['button_text'] = $this->button_text;
			$this->json['button_url']  = $this->button_url;
			$this->json['target']      = $this->target;
			$this->json['ajax_action'] = $this->ajax_action;
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
			<div class="sinatra-button-wrapper sinatra-control-wrapper">

				<# if ( data.label ) { #>
					<span class="sinatra-control-heading customize-control-title sinatra-field">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.ajax_action ) { #>
					<span class="spinner"></span>
				<# } #>

				<a href="{{ data.button_url }}" class="button button-secondary" rel="noopener noreferrer" target="{{ data.target }}"<# if ( data.ajax_action ) { #> data-ajax-action="{{ data.ajax_action }}"<# } #>>{{{ data.button_text }}}</a>

			</div><!-- END .sinatra-button-wrapper -->

			<# if ( data.description ) { #>
				<div class="description customize-control-description sinatra-field">{{{ data.description }}}</div>
			<# } #>

			<?php
		}

	}
endif;
