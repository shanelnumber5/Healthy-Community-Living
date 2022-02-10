<?php
/**
 * Sinatra Customizer custom background control class.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Customizer_Control_Background' ) ) :
	/**
	 * Sinatra Customizer custom background control class.
	 */
	class Sinatra_Customizer_Control_Background extends Sinatra_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'sinatra-background';

		/**
		 * Show advanced settings.
		 *
		 * @since  1.0.0
		 * @var    boolean
		 */
		public $advanced = true;

		/**
		 * Media upload strings.
		 *
		 * @since  1.0.0
		 * @var    boolean
		 */
		public $strings = array();

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

			$default_strings = array(
				'placeholder'  => __( 'No image selected', 'sinatra' ),
				'less'         => __( 'Less Settings', 'sinatra' ),
				'more'         => __( 'Advanced', 'sinatra' ),
				'select_image' => __( 'Select Image', 'sinatra' ),
				'use_image'    => __( 'Use This Image', 'sinatra' ),
			);

			$strings = isset( $args['strings'] ) ? $args['strings'] : array();

			$this->strings = wp_parse_args( $strings, $default_strings );
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['advanced'] = $this->advanced;
			$this->json['l10n']     = $this->strings;
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
			<div class="sinatra-background-wrapper sinatra-control-wrapper">

				<# if ( data.label ) { #>
					<div class="sinatra-control-heading customize-control-title sinatra-field">
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
					</div>
				<# } #>

				<!-- Background Image -->
				<div class="background-image">

					<div class="attachment-media-view background-image-upload">

						<# if ( data.value['background-image'] ) { #>
							<div class="thumbnail thumbnail-image"><img src="{{ data.value['background-image'] }}" alt="" /></div>
						<# } else { #>
							<div class="placeholder"><?php esc_html_e( 'No image selected', 'sinatra' ); ?></div>
						<# } #>

						<div class="actions">

							<button class="button background-image-upload-remove-button<# if ( ! data.value['background-image'] ) { #> hidden<# } #>"><?php esc_html_e( 'Remove', 'sinatra' ); ?></button>

							<button type="button" class="button background-image-upload-button">{{{ data.l10n.select_image }}}</button>

							<# if ( data.advanced ) { #>
								<a href="#" class="advanced-settings<# if ( ! data.value['background-image'] ) { #> hidden<# } #>">
									<span class="message"><?php esc_html_e( 'Advanced', 'sinatra' ); ?></span>
									<span class="dashicons dashicons-arrow-down"></span>
								</a>
							<# } #>

						</div>
					</div>
				</div>

				<# if ( data.advanced ) { #>
					<!-- Background Advanced -->
					<div class="background-image-advanced">

						<!-- Background Repeat -->
						<div class="background-repeat">
							<select {{{ data.inputAttrs }}}>
								<option value="no-repeat"<# if ( 'no-repeat' === data.value['background-repeat'] ) { #> selected <# } #>><?php esc_html_e( 'No Repeat', 'sinatra' ); ?></option>
								<option value="repeat"<# if ( 'repeat' === data.value['background-repeat'] ) { #> selected <# } #>><?php esc_html_e( 'Repeat All', 'sinatra' ); ?></option>
								<option value="repeat-x"<# if ( 'repeat-x' === data.value['background-repeat'] ) { #> selected <# } #>><?php esc_html_e( 'Repeat Horizontally', 'sinatra' ); ?></option>
								<option value="repeat-y"<# if ( 'repeat-y' === data.value['background-repeat'] ) { #> selected <# } #>><?php esc_html_e( 'Repeat Vertically', 'sinatra' ); ?></option>
							</select>
						</div>

						<!-- Background Position -->
						<div class="background-position">

							<h4><?php esc_html_e( 'Background Position', 'sinatra' ); ?></h4>

							<div class="sinatra-range-wrapper">
								<span><?php esc_html_e( 'Horizontal', 'sinatra' ); ?></span>
								<div class="sinatra-control-wrap">
									<input 
										type="range" 
										data-key="background-position-x"
										value="{{ data.value['background-position-x'] }}" 
										min="0" 
										max="100" 
										step="1" />
									<input 
										type="number" 
										class="sinatra-range-input"
										value="{{ data.value['background-position-x'] }}"  />
									<span class="sinatra-range-unit">%</span>
								</div>
							</div>

							<div class="sinatra-range-wrapper">
								<span><?php esc_html_e( 'Vertical', 'sinatra' ); ?></span>
								<div class="sinatra-control-wrap">
									<input 
										type="range"
										data-key="background-position-y"
										value="{{ data.value['background-position-y'] }}" 
										min="0" 
										max="100" 
										step="1" />
									<input 
										type="number"
										class="sinatra-range-input"
										value="{{ data.value['background-position-y'] }}"  />
									<span class="sinatra-range-unit">%</span>
								</div>
							</div>

						</div>

						<!-- Background Size -->
						<div class="background-size">
							<h4><?php esc_html_e( 'Background Size', 'sinatra' ); ?></h4>
							<div class="buttonset">
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="cover" name="_customize-bg-{{{ data.id }}}-size" id="{{ data.id }}cover" <# if ( 'cover' === data.value['background-size'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}cover"><?php esc_html_e( 'Cover', 'sinatra' ); ?></label>
								</input>
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="contain" name="_customize-bg-{{{ data.id }}}-size" id="{{ data.id }}contain" <# if ( 'contain' === data.value['background-size'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}contain"><?php esc_html_e( 'Contain', 'sinatra' ); ?></label>
								</input>
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="auto" name="_customize-bg-{{{ data.id }}}-size" id="{{ data.id }}auto" <# if ( 'auto' === data.value['background-size'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}auto"><?php esc_html_e( 'Auto', 'sinatra' ); ?></label>
								</input>
							</div>
						</div>

						<!-- Background Attachment -->
						<div class="background-attachment">
							<h4><?php esc_html_e( 'Background Attachment', 'sinatra' ); ?></h4>
							<div class="buttonset">
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="inherit" name="_customize-bg-{{{ data.id }}}-attachment" id="{{ data.id }}inherit" <# if ( 'inherit' === data.value['background-attachment'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}inherit"><?php esc_html_e( 'Inherit', 'sinatra' ); ?></label>
								</input>
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="scroll" name="_customize-bg-{{{ data.id }}}-attachment" id="{{ data.id }}scroll" <# if ( 'scroll' === data.value['background-attachment'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}scroll"><?php esc_html_e( 'Scroll', 'sinatra' ); ?></label>
								</input>
								<input {{{ data.inputAttrs }}} class="switch-input screen-reader-text" type="radio" value="fixed" name="_customize-bg-{{{ data.id }}}-attachment" id="{{ data.id }}fixed" <# if ( 'fixed' === data.value['background-attachment'] ) { #> checked="checked" <# } #>>
									<label class="switch-label" for="{{ data.id }}fixed"><?php esc_html_e( 'Fixed', 'sinatra' ); ?></label>
								</input>
							</div>
						</div>

						<!-- Background Image ID -->
						<input type="hidden" value="{{ data.value['background-image-id'] }}" class="background-image-id" />
					</div>
				<# } #>
			<?php
		}

	}
endif;
