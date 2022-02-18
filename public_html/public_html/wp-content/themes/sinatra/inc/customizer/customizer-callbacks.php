<?php
/**
 * Sinatra Customizer sanitization callback functions.
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

/**
 * Number sanitization callback
 *
 * @since 1.0.0
 * @param int $val Number.
 */
function sinatra_sanitize_number( $val ) {
	return is_numeric( $val ) ? $val : 0;
}

/**
 * Toggle field sanitization.
 *
 * @since 1.0.0
 * @param mixed  $input    Value of the toggle field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_toggle( $input, $setting ) {

	// Ensure input is a slug.
	$input = sanitize_key( $input );

	return true === (bool) $input;
}

/**
 * Select field sanitization callback.
 *
 * @since 1.0.0
 * @param mixed  $input    Value of the select field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_select( $input, $setting ) {

	$multiple = isset( $setting->manager->get_control( $setting->id )->multiple ) ? $setting->manager->get_control( $setting->id )->multiple : false;

	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;

	if ( $multiple ) {

		// Check if input is array.
		if ( is_array( $input ) && ! empty( $input ) ) {

			$return = array();

			// Get only valid values.
			foreach ( $input as $selected ) {

				$selected = sanitize_key( $selected );

				if ( array_key_exists( $selected, $choices ) ) {
					$return[] = $selected;
				}
			}

			// Return valid only.
			return $return;

		} else {

			// Return default if input valid.
			return array();
		}
	} else {

		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}

}

/**
 * Textarea field sanitization callback.
 *
 * @since 1.0.0
 * @param mixed  $input    Value of the select field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_textarea( $input, $setting ) {

	return wp_kses_post( $input );
}

/**
 * Color field sanitization callback
 *
 * @since 1.0.0
 * @param string $color Color code.
 */
function sinatra_sanitize_color( $color ) {

	if ( empty( $color ) || is_array( $color ) ) {
		return '';
	}

	if ( false === strpos( $color, 'rgba' ) ) {
		return sinatra_sanitize_hex_color( $color );
	}

	return sinatra_sanitize_alpha_color( $color );
}

/**
 * Sanitize HEX color.
 *
 * @since 1.0.0
 * @param string $color Color code in HEX.
 */
function sinatra_sanitize_hex_color( $color ) {

	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return '';
}

/**
 * Sanitize Alpha color.
 *
 * @since 1.0.0
 * @param string $color Color code in RGBA.
 */
function sinatra_sanitize_alpha_color( $color ) {

	if ( '' === $color ) {
		return '';
	}

	if ( false === strpos( $color, 'rgba' ) ) {
		/* Hex sanitize */
		return sinatra_sanitize_hex_color( $color );
	}

	/* rgba sanitize */
	$color = str_replace( ' ', '', $color );
	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
	return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
}

/**
 * Sanitize image.
 *
 * @since 1.0.0
 * @param mixed  $image    Selected image..
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_image( $image, $setting ) {
	/*
	 * Array of valid image file types.
	 *
	 * The array includes image mime types that are included in wp_get_mime_types()
	 */
	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png',
		'bmp'          => 'image/bmp',
		'tif|tiff'     => 'image/tiff',
		'ico'          => 'image/x-icon',
		'svg'          => 'image/svg+xml',
	);

	// Return an array with file extension and mime_type.
	$file = wp_check_filetype( $image, $mimes );

	// If $image has a valid mime_type, return it; otherwise, return the default.
	return ( $file['ext'] ? $image : $setting->default );
}

/**
 * Sanitize widget control value.
 *
 * @since 1.0.0
 * @param array  $widgets Array of saved widgets.
 * @param object $setting WP Customize manager object.
 */
function sinatra_sanitize_widget( $widgets, $setting ) {

	// Widgets has to be an array.
	if ( ! is_array( $widgets ) || empty( $widgets ) ) {
		return array();
	}

	$control = $setting->manager->get_control( $setting->id );

	// Control widgets.
	$control_widgets = $control->widgets;

	// Control locations.
	$control_locations = $control->locations;

	// Control visibility options.
	$control_visibility = $control->visibility;

	// Used to control max uses for widgets.
	$widget_uses = array();

	foreach ( $widgets as $index => $widget ) {

		// Check if this widget is allowed.
		if ( ! array_key_exists( $widget['type'], $control_widgets ) ) {
			unset( $widgets[ $index ] );
			continue;
		}

		// Set max usage for widget.
		if ( ! isset( $widget_uses[ $widget['type'] ] ) ) {
			$widget_uses[ $widget['type'] ] = isset( $control_widgets[ $widget['type'] ]['max_uses'] ) ? intval( $control_widgets[ $widget['type'] ]['max_uses'] ) : -1;
		}

		// Remove if max widgets exceeded count.
		if ( -1 !== $widget_uses[ $widget['type'] ] ) {

			if ( $widget_uses[ $widget['type'] ] > 0 ) {

				// Widget count still good.
				$widget_uses[ $widget['type'] ]--;
			} else {

				// Exceeded widget max usage.
				unset( $widgets[ $index ] );
				continue;
			}
		}

		// Ensure widget has values.
		if ( ! isset( $widget['values'] ) || ! is_array( $widget['values'] ) ) {
			$widget['values'] = array();
		}

		// Validate the location parameter.
		if ( isset( $widget['values']['location'] ) ) {

			if ( empty( $control_locations ) ) {

				// No locations available.
				unset( $widget['values']['location'] );
			} elseif ( ! array_key_exists( $widget['values']['location'], $control_locations ) ) {

				// This locations is not available, take one from array of available locations.
				$widget['values']['location'] = key( $control_locations );
			}
		} elseif ( ! empty( $control_locations ) ) {

			// Widget has to have a location option.
			$widget['values']['location'] = key( $control_locations );
		}

		// Validate the visibility parameter.
		if ( isset( $widget['values']['visibility'] ) ) {

			if ( empty( $control_visibility ) ) {

				// No visibility available.
				$widget['values']['visibility'] = '';
			} elseif ( ! array_key_exists( $widget['values']['visibility'], $control_visibility ) ) {

				// This locations is not available, take one from array of available locations.
				$widget['values']['visibility'] = key( $control_visibility );
			}
		} elseif ( ! empty( $control_visibility ) ) {

			// Widget has to have a location option.
			$widget['values']['visibility'] = key( $control_visibility );
		}

		// Validate widget values.
		if ( ! empty( $widget['values'] ) ) {
			$classname = $widget['classname'];
			$instance  = new $classname( $widget['values'] );

			$widget['values'] = $instance->values;
		}
	}

	return $widgets;
}

/**
 * No sanitization. Used for controls that only output HTML.
 *
 * @since 1.0.0
 * @param mixed $val Value.
 */
function sinatra_no_sanitize( $val ) {
	return $val;
}

/**
 * Sanitize Background control value.
 *
 * @since 1.0.0
 * @param mixed $background Value.
 */
function sinatra_sanitize_background( $background ) {

	$bg_params = array(
		'background-image'      => '',
		'background-image-id'   => '',
		'background-repeat'     => 'repeat',
		'background-position-x' => '50',
		'background-position-y' => '50',
		'background-size'       => 'auto',
		'background-attachment' => 'scroll',
	);

	foreach ( $bg_params as $key => $value ) {

		if ( isset( $background[ $key ] ) ) {

			if ( 'background-image' === $key ) {
				$bg_params[ $key ] = esc_url_raw( $background[ $key ] );
			} else {
				$bg_params[ $key ] = esc_attr( $background[ $key ] );
			}
		}
	}

	if ( empty( $bg_params['background-image'] ) ) {
		$bg_params['background-image-id'] = '';
	}

	return $bg_params;
}

/**
 * Sanitize Spacing control value.
 *
 * @since 1.0.0
 * @param mixed  $values  Values.
 * @param object $setting WP Customize Setting instance.
 * @param mixed  $default Default Value.
 */
function sinatra_sanitize_spacing( $values, $setting, $default = array() ) {

	$control         = $setting->manager->get_control( $setting->id );
	$control_choices = $control->choices;
	$control_units   = $control->unit;

	foreach ( $control_choices as $key => $value ) {
		if ( ! isset( $values[ $key ] ) ) {
			$values[ $key ] = isset( $default[ $key ] ) ? $default[ $key ] : 0;
		}
	}

	foreach ( $values as $key => $value ) {

		if ( 'unit' === $key ) {
			continue;
		}

		if ( ! isset( $control_choices[ $key ] ) ) {
			unset( $values[ $key ] );
			continue;
		}

		$values[ $key ] = is_numeric( $value ) ? $value : '';
	}

	if ( isset( $values['unit'] ) && ! in_array( $values['unit'], $control_units, true ) ) {
		if ( isset( $default['unit'] ) ) {
			$values['unit'] = $default['unit'];
		} elseif ( ! empty( $control_units ) ) {
			$values['unit'] = $control_units[0];
		} else {
			$values['unit'] = '';
		}
	}

	return $values;
}

/**
 * Sanitize Range control value.
 *
 * @since 1.0.0
 * @param mixed  $value   Values.
 * @param object $setting WP Customize Setting instance.
 * @param mixed  $default Default Value.
 */
function sinatra_sanitize_range( $value, $setting, $default = '' ) {

	if ( is_array( $value ) ) {
		if ( isset( $value['value'], $value['unit'] ) ) {
			return array(
				'value' => floatval( $value['value'] ),
				'unit'  => sanitize_text_field( $value['unit'] ),
			);
		} elseif ( isset( $value['value'] ) ) {
			return floatval( $value['value'] );
		}
	}

	return is_numeric( $value ) ? floatval( $value ) : $default;
}

/**
 * Sanitize Responsive control value.
 * Iterate through all responsive breakpoints and sanitize each value.
 *
 * @since 1.0.0
 * @param mixed  $values  Values.
 * @param object $setting WP Customize Setting instance.
 */
function sinatra_sanitize_responsive( $values, $setting ) {

	$control       = $setting->manager->get_control( $setting->id );
	$control_type  = str_replace( 'sinatra-', '', $control->type );
	$control_units = isset( $control->unit ) ? $control->unit : false;

	if ( is_array( $control->responsive ) && ! empty( $control->responsive ) ) {

		// Ensure all responsive devices are in value.
		foreach ( $control->responsive as $device => $settings ) {

			if ( ! isset( $values[ $device ] ) ) {
				$values[ $device ] = isset( $setting->default[ $device ] ) ? $setting->default[ $device ] : '';
			}
		}

		// Ensure all devices in value are allowed and sanitize value.
		foreach ( $values as $device => $value ) {

			if ( 'unit' === $device ) {
				continue;
			}

			if ( ! isset( $control->responsive[ $device ] ) ) {
				unset( $values[ $device ] );
				continue;
			}

			// Sanitize value.
			$values[ $device ] = call_user_func_array(
				'sinatra_sanitize_' . $control_type,
				array(
					$values[ $device ],
					$setting,
					isset( $setting->default[ $device ] ) ? $setting->default[ $device ] : '',
				)
			);
		}
	}

	return $values;
}

/**
 * Typography field sanitization.
 *
 * @since 1.0.0
 * @param mixed  $value    Value of the toggle field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_typography( $value, $setting ) {

	$defaults = array(
		'font-family'         => '',
		'font-subsets'        => array(),
		'font-weight'         => '',
		'font-style'          => '',
		'text-transform'      => '',
		'font-size-desktop'   => '',
		'font-size-tablet'    => '',
		'font-size-mobile'    => '',
		'font-size-unit'      => '',
		'letter-spacing'      => '',
		'letter-spacing-unit' => '',
		'line-height-desktop' => '',
		'line-height-tablet'  => '',
		'line-height-mobile'  => '',
		'line-height-unit'    => '',
	);

	$defaults = wp_parse_args( $setting->default, $defaults );

	$value = wp_parse_args( $value, $defaults );

	return $value;
}

/**
 * Design Options field sanitization.
 *
 * @since 1.0.0
 * @param mixed  $value    Value of the toggle field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_design_options( $value, $setting ) {

	$control = $setting->manager->get_control( $setting->id );

	$sanitized = (array) $setting->default;

	if ( isset( $control->display ) ) {

		if ( isset( $control->display['background'] ) ) {

			if ( ! array_key_exists( $value['background-type'], $control->display['background'] ) ) {
				$sanitized['background-type'] = 'color';
			} else {
				$sanitized['background-type'] = $value['background-type'];
			}

			// Color.
			if ( array_key_exists( 'color', $control->display['background'] ) ) {

				if ( isset( $value['background-color'] ) ) {
					$sanitized['background-color'] = sinatra_sanitize_color( $value['background-color'] );
				}
			}

			// Gradient.
			if ( array_key_exists( 'gradient', $control->display['background'] ) ) {

				if ( isset( $value['gradient-color-1'] ) ) {
					$sanitized['gradient-color-1'] = sinatra_sanitize_color( $value['gradient-color-1'] );
				}

				if ( isset( $value['gradient-color-2'] ) ) {
					$sanitized['gradient-color-2'] = sinatra_sanitize_color( $value['gradient-color-2'] );
				}

				if ( isset( $value['gradient-color-1-location'] ) ) {
					$sanitized['gradient-color-1-location'] = sinatra_sanitize_range( $value['gradient-color-1-location'], $setting );
				}

				if ( isset( $value['gradient-color-2-location'] ) ) {
					$sanitized['gradient-color-2-location'] = sinatra_sanitize_range( $value['gradient-color-2-location'], $setting );
				}

				if ( isset( $value['gradient-type'] ) && in_array( $value['gradient-type'], array( 'linear', 'radial' ), true ) ) {
					$sanitized['gradient-type'] = $value['gradient-type'];
				}

				if ( isset( $value['gradient-linear-angle'] ) ) {
					$sanitized['gradient-linear-angle'] = sinatra_sanitize_range( $value['gradient-linear-angle'], $setting );
				}

				if ( isset( $value['gradient-position'] ) ) {
					$sanitized['gradient-position'] = sanitize_text_field( $value['gradient-position'] );
				}
			}

			// Image.
			if ( array_key_exists( 'image', $control->display['background'] ) ) {

				if ( isset( $value['background-image'] ) ) {
					$sanitized['background-image'] = sanitize_text_field( $value['background-image'] );
				}

				if ( isset( $value['background-image-id'] ) ) {
					$sanitized['background-image-id'] = sanitize_text_field( $value['background-image-id'] );
				}

				if ( isset( $value['background-repeat'] ) ) {
					$sanitized['background-repeat'] = sanitize_text_field( $value['background-repeat'] );
				}

				if ( isset( $value['background-position-x'] ) ) {
					$sanitized['background-position-x'] = intval( $value['background-position-x'] );
				}

				if ( isset( $value['background-position-y'] ) ) {
					$sanitized['background-position-y'] = intval( $value['background-position-y'] );
				}

				if ( isset( $value['background-size'] ) ) {
					$sanitized['background-size'] = sanitize_text_field( $value['background-size'] );
				}

				if ( isset( $value['background-attachment'] ) ) {
					$sanitized['background-attachment'] = sanitize_text_field( $value['background-attachment'] );
				}

				if ( isset( $value['background-color-overlay'] ) ) {
					$sanitized['background-color-overlay'] = sanitize_text_field( $value['background-color-overlay'] );
				}
			}
		}

		if ( isset( $control->display['color'] ) ) {
			foreach ( $control->display['color'] as $id => $title ) {
				if ( isset( $value[ $id ] ) ) {
					$sanitized[ $id ] = sinatra_sanitize_color( $value[ $id ] );
				}
			}
		}

		if ( isset( $control->display['border'] ) ) {

			// Border Color.
			if ( array_key_exists( 'color', $control->display['border'] ) && isset( $value['border-color'] ) ) {
				$sanitized['border-color'] = sinatra_sanitize_color( $value['border-color'] );
			}

			// Border Style.
			if ( isset( $value['border-style'] ) ) {
				$sanitized['border-style'] = sanitize_key( $value['border-style'] );
			}

			// Separator Border Style.
			if ( isset( $value['separator-color'] ) ) {
				$sanitized['separator-color'] = sinatra_sanitize_color( $value['separator-color'] );
			}

			// Border Width.
			$border_width = array( 'left', 'top', 'right', 'bottom' );

			foreach ( $border_width as $position ) {
				if ( isset( $value[ 'border-' . $position . '-width' ] ) ) {
					$sanitized[ 'border-' . $position . '-width' ] = intval( $value[ 'border-' . $position . '-width' ] );
				}
			}
		}
	}

	return $sanitized;
}

/**
 * Sortable field sanitization.
 *
 * @since 1.0.0
 * @param mixed  $value    Value of the toggle field.
 * @param object $setting  Setting object.
 */
function sinatra_sanitize_sortable( $value, $setting ) {

	$control = $setting->manager->get_control( $setting->id );
	$choices = $control->choices;
	$default = $setting->default;

	if ( empty( $value ) ) {
		return $default;
	}

	foreach ( $value as $item => $config ) {
		if ( ! isset( $choices[ $item ] ) ) {
			unset( $value[ $item ] );
		}
	}

	foreach ( $choices as $item => $config ) {
		if ( ! isset( $value[ $item ] ) ) {
			$value[ $item ] = isset( $default[ $item ] ) ? $default[ $item ] : false;
		}
	}

	return $value;
}
