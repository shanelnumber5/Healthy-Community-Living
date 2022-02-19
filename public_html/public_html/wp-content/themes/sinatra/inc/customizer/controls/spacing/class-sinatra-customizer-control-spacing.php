<?php
/**
 * Sinatra Customizer custom spacing control class.
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

if ( ! class_exists( 'Sinatra_Customizer_Control_Spacing' ) ) :
	/**
	 * Sinatra Customizer custom select control class.
	 */
	class Sinatra_Customizer_Control_Spacing extends Sinatra_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'sinatra-spacing';

		/**
		 * The unit.
		 *
		 * @var string
		 */
		public $unit = array();

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['title']   = esc_html__( 'Link values', 'sinatra' );
			$this->json['choices'] = $this->choices;
			$this->json['unit']    = $this->unit;
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
			<div class="sinatra-control-wrapper sinatra-spacing-wrapper<# if ( data.responsive ) { #> sinatra-control-responsive <# } #>">

				<# if ( data.label ) { #>
					<div class="customize-control-title">

						<span>{{{ data.label }}}</span>

						<# if ( data.description ) { #>
							<i class="sinatra-info-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
									<circle cx="12" cy="12" r="10"></circle>
									<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
									<line x1="12" y1="17" x2="12" y2="17"></line>
								</svg>
								<span class="sinatra-tooltip">{{{ data.description }}}</span>
							</i>
						<# } #>

						<# if ( ! _.isEmpty( data.responsive ) ) { #>
							<?php $this->responsive_devices(); ?>
						<# } #>
					</div>
				<# } #>

				<# if ( ! _.isEmpty( data.unit ) ) { #>
					<div class="sinatra-control-unit">
						<# _.each( data.unit, function( unit ){ #>
							<input 
								type="radio" 
								id="spacing-unit-{{ data.id }}-{{ unit }}" 
								name="spacing-unit-{{ data.id }}" 
								value="{{ unit }}" 
								<# if ( unit === data.value['unit'] ) { #> checked="checked"<# } #> />
							<label for="spacing-unit-{{ data.id }}-{{ unit }}">{{{ unit }}}</label>
						<# }); #>
					</div>
				<# } #>

				<div class="sinatra-control-wrap">

					<a href="#" class="reset-defaults">
						<span class="dashicons dashicons-image-rotate"></span>
					</a>

					<# if ( ! _.isEmpty( data.responsive ) ) { #>

						<# _.each( data.responsive, function( settings, device ){ #>
							<ul class="{{ device }} control-responsive" data-device="{{ device }}">

								<# _.each( data.choices, function( title, id ){ #>

									<# if ( _.isEmpty( data.value[ device ] ) ) { 
										data.value[ device ] = [];
									} #>

									<li class="spacing-control-wrap spacing-input">
										<input {{{ data.inputAttrs }}} name="spacing-control-{{ device }}-{{ id }}" type="number" data-spacing-choice="{{ id }}" value="{{{ data.value[ device ][ id ] }}}" data-default="{{ data.default[ device ][ id ] }}"/>
										<span class="sinatra-spacing-label">{{{ title }}}</span>
									</li>

								<# }); #>

								<# if ( _.size( data.choices ) > 1 ) { #>
									<li class="spacing-control-wrap">
										<div class="spacing-link-values">
											<span class="dashicons dashicons-admin-links sinatra-spacing-linked" data-element="{{ data.id }}" title="{{ data.title }}"></span>
											<span class="dashicons dashicons-editor-unlink sinatra-spacing-unlinked" data-element="{{ data.id }}" title="{{ data.title }}"></span>
										</div>
									</li>
								<# } #>

							</ul>
						<# }); #>
					<# } else { #>

						<ul class="active">

							<# _.each( data.choices, function( title, id ){ #>

								<li class="spacing-control-wrap spacing-input">
									<input {{{ data.inputAttrs }}} name="spacing-control-{{ id }}" type="number" data-spacing-choice="{{ id }}" value="{{{ data.value[ id ] }}}" data-default="{{ data.default[ id ] }}"/>
									<span class="sinatra-spacing-label">{{{ title }}}</span>
								</li>

							<# }); #>

							<li class="spacing-control-wrap">
								<div class="spacing-link-values">
									<span class="dashicons dashicons-admin-links sinatra-spacing-linked" data-element="{{ data.id }}" title="{{ data.title }}"></span>
									<span class="dashicons dashicons-editor-unlink sinatra-spacing-unlinked" data-element="{{ data.id }}" title="{{ data.title }}"></span>
								</div>
							</li>
						</ul>

					<# } #>

				</div><!-- END .sinatra-control-wrap -->

			</div><!-- END .sinatra-control-wrapper -->
			<?php
		}
	}
endif;
