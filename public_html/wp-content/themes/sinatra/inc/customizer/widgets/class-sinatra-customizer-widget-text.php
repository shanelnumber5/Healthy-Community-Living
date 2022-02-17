<?php
/**
 * Sinatra Customizer widgets class.
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

if ( ! class_exists( 'Sinatra_Customizer_Widget_Text' ) ) :

	/**
	 * Sinatra Customizer widget class
	 */
	class Sinatra_Customizer_Widget_Text extends Sinatra_Customizer_Widget {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @param array $args An array of the values for this widget.
		 */
		public function __construct( $args = array() ) {

			$args['name']        = __( 'Text', 'sinatra' );
			$args['description'] = __( 'Arbitrary text.', 'sinatra' );
			$args['icon']        = 'dashicons dashicons-text';
			$args['type']        = 'text';

			$values = array(
				'content'    => esc_html__( 'Text widget content goes here...', 'sinatra' ),
				'visibility' => 'all',
			);

			$args['values'] = isset( $args['values'] ) ? wp_parse_args( $args['values'], $values ) : $values;

			$args['values']['content'] = wp_kses( $args['values']['content'], sinatra_get_allowed_html_tags() );

			parent::__construct( $args );
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function form() {
			?>
			<p>
				<label for="widget-text-<?php echo esc_attr( $this->id ); ?>-<?php echo esc_attr( $this->number ); ?>-content"><?php esc_html_e( 'Content', 'sinatra' ); ?>:</label>
				<textarea class="widefat" id="widget-text-<?php echo esc_attr( $this->id ); ?>-<?php echo esc_attr( $this->number ); ?>-content" name="widget-text[<?php echo esc_attr( $this->number ); ?>][content]" data-option-name="content" rows="4"><?php echo wp_kses( $this->values['content'], sinatra_get_allowed_html_tags() ); ?></textarea>
				<span class="description">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %1$s is opening anchor tag, %2$s is a closing anchor tag. */
							__( 'Shortcodes and basic html elements allowed. See the list of %1$savailable dynamic strings%2$s.', 'sinatra' ),
							'<a href="' . esc_url( 'https://sinatrawp.com/docs/sinatra-dynamic-strings/' ) . '" target="_blank" rel="noopener noreferrer">',
							'</a>'
						)
					);
					?>

				</span>
			</p>
			<?php
		}
	}
endif;
