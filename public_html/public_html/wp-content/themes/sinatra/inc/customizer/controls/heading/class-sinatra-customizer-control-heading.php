<?php
/**
 * Sinatra Customizer custom heading control class.
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

if ( ! class_exists( 'Sinatra_Customizer_Control_Heading' ) ) :
	/**
	 * Sinatra Customizer custom heading control class.
	 */
	class Sinatra_Customizer_Control_Heading extends Sinatra_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'sinatra-heading';

		/**
		 * Top spacer.
		 *
		 * @since  1.0.0
		 * @var    boolean
		 */
		public $space = true;

		/**
		 * Heading style. Possible options are: regular-heading and sub-heading.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $style = 'regular-heading';

		/**
		 * Toggler.
		 *
		 * @since  1.0.0
		 * @var    boolean
		 */
		public $toggle = true;

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['space']  = ( true === $this->space || 'true' === $this->space ) ? ' top-spacer' : '';
			$this->json['toggle'] = ( true === $this->toggle || 'true' === $this->toggle ) ? ' toggle-heading' : '';
			$this->json['style']  = $this->style;
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
			<# if ( data.space ) { #>
				<div class="sinatra-heading-top-space {{ data.style }}"></div>
			<# } #>
			<div class="sinatra-heading-wrapper sinatra-control-wrapper{{ data.space }}{{ data.toggle }} {{ data.style }}">

				<# if ( data.label ) { #>
					<span class="sinatra-control-heading">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<i class="sinatra-info-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg><span class="sinatra-tooltip top-right-tooltip">{{{ data.description }}}</span></i>
				<# } #>

				<# if ( data.toggle ) { #>
				<span class="sinatra-heading-toggle">
					<input type="checkbox" id="{{ data.id }}" name="{{ data.id }}" <# if ( data.value ) { #> checked="checked" <# } #>>

					<label for="{{ data.id }}" aria-hidden="true">
					</label>
				</span>
				<# } #>

			</div><!-- END .sinatra-heading-wrapper -->
			<?php
		}

	}
endif;
