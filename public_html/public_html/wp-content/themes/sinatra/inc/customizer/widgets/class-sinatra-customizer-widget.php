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

if ( ! class_exists( 'Sinatra_Customizer_Widget' ) ) :

	/**
	 * Sinatra Customizer widget class
	 */
	class Sinatra_Customizer_Widget {

		/**
		 * Root ID for all widgets of this type.
		 *
		 * @since 1.0.0
		 * @var mixed|string
		 */
		public $id_base;

		/**
		 * Unique ID string of the current instance (id_base-number).
		 *
		 * @since 1.0.0
		 * @var bool|string
		 */
		public $id;

		/**
		 * Name for this widget type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $name;

		/**
		 * Icon for this widget type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $icon;

		/**
		 * Type of widget. Shortened for id_base.
		 *
		 * @since 1.0.0
		 * @var bool|string
		 */
		public $type;

		/**
		 * Description for this widget type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $description;

		/**
		 * Option name for this widget type.
		 *
		 * @since 1.0.
		 * @var string
		 */
		public $option_name;

		/**
		 * Option name for this widget type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $values;

		/**
		 * Unique ID number of the current instance.
		 *
		 * @since 1.0.0
		 * @var bool|int
		 */
		public $number = false;

		/**
		 * Array of locations for widgets.
		 *
		 * @var array
		 */
		public $locations = array();

		/**
		 * Array of locations for widgets.
		 *
		 * @var array
		 */
		public $visibility = array();

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @param array $args Array with widget information.
		 */
		public function __construct( $args = array() ) {

			$this->id_base     = isset( $args['id_base'] ) ? strtolower( $args['id_base'] ) : strtolower( get_class( $this ) );
			$this->name        = isset( $args['name'] ) ? $args['name'] : get_class( $this );
			$this->description = isset( $args['description'] ) ? $args['description'] : '';
			$this->id          = isset( $args['id'] ) ? $this->id_base . '_' . $args['id'] : $this->id_base;
			$this->icon        = isset( $args['icon'] ) ? $args['icon'] : 'dashicons dashicons-plus';
			$this->option_name = 'sinatra_customizer_widget_' . $this->id_base;
			$this->values      = isset( $args['values'] ) ? $args['values'] : array();
			$this->number      = isset( $args['number'] ) ? intval( $args['number'] ) : '__i__';
			$this->type        = isset( $args['type'] ) ? $args['type'] : '';
			$this->locations   = isset( $args['locations'] ) ? $args['locations'] : array();
			$this->visibility  = isset( $args['visibility'] ) ? $args['visibility'] : array();
		}

		/**
		 * Displays the form fields for this widget.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function form() {}

		/**
		 * Displays the complete widget control.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function template() {
			?>
			<div class="widget" id="sinatra_widget-<?php echo esc_attr( $this->id_base ); ?>-<?php echo esc_attr( $this->number ); ?>" data-widget-base="<?php echo esc_attr( $this->id_base ); ?>" data-widget-type="<?php echo esc_attr( $this->type ); ?>">

				<div class="widget-top">		

					<div class="widget-title-action">
						<button type="button" class="widget-action" aria-expanded="false">
							<span class="screen-reader-text"><?php esc_html_e( 'Edit widget:', 'sinatra' ); ?> <?php echo esc_html( $this->name ); ?></span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
					</div>

					<div class="widget-title">
						<i class="<?php echo esc_attr( $this->icon ); ?>"></i>
						<h3><?php echo esc_html( $this->name ); ?></h3>
					</div><!-- END .widget-title -->

					<span class="in-widget-title"></span>
				</div><!-- END .widget-top -->

				<div class="widget-inside">

					<div class="form">
						<div class="widget-content">
							<?php
							$this->form();
							?>

							<?php if ( isset( $this->locations ) && is_array( $this->locations ) && ! empty( $this->locations ) ) { ?>

								<?php $current_location = isset( $this->values['location'] ) ? $this->values['location'] : key( $this->locations ); ?>

								<p>
									<label for="sinatra-widget-location"><?php esc_html_e( 'Widget Location', 'sinatra' ); ?></label>

									<span class="buttonset">
										<?php foreach ( $this->locations as $id => $name ) { ?>
											<input 
												class="switch-input screen-reader-text" 
												type="radio" 
												value="<?php echo esc_attr( $id ); ?>" 
												name="_customize-widget-location-<?php echo esc_attr( $this->number ); ?>-<?php echo esc_attr( $this->id ); ?>" 
												id="<?php echo esc_attr( $this->id ) . '-' . esc_attr( $this->number ) . '-' . esc_attr( $id ); ?>-location" 
												<?php checked( $id, $current_location, true ); ?> 
												data-option-name="location">
												<label 
													class="switch-label" 
													for="<?php echo esc_attr( $this->id ) . '-' . esc_attr( $this->number ) . '-' . esc_attr( $id ); ?>-location"><?php echo esc_html( $name ); ?>
												</label>
											</input>
										<?php } ?>
									</span>

								</p>
							<?php } ?>

							<?php if ( isset( $this->visibility ) && is_array( $this->visibility ) && ! empty( $this->visibility ) ) { ?>

								<?php $current_visibility = isset( $this->values['visibility'] ) ? $this->values['visibility'] : key( array_reverse( $this->visibility ) ); ?>

								<p>
									<label for="sinatra-widget-visibility"><?php esc_html_e( 'Visibility', 'sinatra' ); ?></label>

									<select 
										name="_customize-widget-visibility-<?php echo esc_attr( $this->number ); ?>-<?php echo esc_attr( $this->id ); ?>"
										id="<?php echo esc_attr( $this->id ) . '-' . esc_attr( $this->number ) . '-' . esc_attr( $id ); ?>"
										data-option-name="visibility"
										>
										<?php foreach ( $this->visibility as $id => $name ) { ?>
											<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $current_visibility, true ); ?>>
												<?php echo esc_html( $name ); ?>
											</option> 
										<?php } ?>
									</select>

								</p>
							<?php } ?>

						</div><!-- .widget-content -->

						<div class="widget-control-actions">
							<div class="alignleft">
								<button type="button" class="button-link button-link-delete widget-control-remove"><?php esc_html_e( 'Delete', 'sinatra' ); ?></button>
								<span class="widget-control-close-wrapper">
									|
									<button type="button" class="button-link widget-control-close"><?php esc_html_e( 'Done', 'sinatra' ); ?></button>
								</span>
							</div>

							<div class="alignright">
								<!-- <span class="spinner"></span> -->
							</div>

							<br class="clear">
						</div>
					</div>
				</div>
				<!-- END .widget-inside -->

				<?php if ( ! empty( $this->description ) ) { ?>
					<div class="widget-description"><?php echo esc_html( $this->description ); ?></div><!-- END .widget-description -->
				<?php } ?>

			</div><!-- END .widget -->

			<?php
		}
	}
endif;
