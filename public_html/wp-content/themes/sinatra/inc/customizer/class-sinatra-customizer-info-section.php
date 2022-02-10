<?php
/**
 * Custom section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_Info_Section' ) ) :
	/**
	 * Custom section in Customizer.
	 */
	class Sinatra_Customizer_Info_Section extends WP_Customize_Section {

		/**
		 * The type of customize section being rendered.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $type = 'sinatra-info';

		/**
		 * Button style.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $style = '';

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
		public $target = '';

		/**
		 * Tagline.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $tagline = '';

		/**
		 * Add custom parameters to pass to the JS via JSON.
		 *
		 * @since  1.0.0
		 */
		public function json() {
			$json = parent::json();

			$json['url']     = $this->url;
			$json['target']  = $this->target;
			$json['tagline'] = $this->tagline;
			$json['style']   = $this->style ? ' ' . $this->style : false;

			return $json;
		}

		/**
		 * Outputs the Underscore.js template.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		protected function render_template() { ?>

		<li id="accordion-section-{{ data.id }}" class="accordion-section{{ data.style }} control-section control-section-{{ data.type }} cannot-expand">

			<h3 class="accordion-section-title">

				<# if ( data.url ) { #>

					<# if ( data.style ) { #>
						<a href="{{ data.url }}" target="{{ data.target }}" rel="noopener noreferrer" class="button button-primary button-large sinatra-info-link">{{ data.title }}
					<# } else { #>
						<a href="{{ data.url }}" target="{{ data.target }}" rel="noopener noreferrer" class="sinatra-info-link">{{ data.title }}
						<span class="dashicons dashicons-performance"></span>

						<# if ( data.tagline ) { #>
							<span class="sinatra-info-tagline">{{ data.tagline }}</span>
						<# } #>
					<# } #>
				</a>
				<# } #>
			</h3>
		</li>
			<?php
		}
	}
endif;
