;(function( $ ) {

	wp.customize.controlConstructor['sinatra-color'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this,
				color = control.setting.get();

			// Changes.
			control.container.on( 'color-updated', '.sinatra-color-control', function(){
				value = $(this).val();

				if ( value !== color ) {
					control.setting.set( value );
					color = value;
				}
			});
		}
	});

	/**
	 * Override the stock color.js toString() method to add support for
	 * outputting RGBa or Hex.
	 */
	Color.prototype.toString = function( flag ) {

		// If our no-alpha flag has been passed in, output RGBa value with 100% opacity.
		// This is used to set the background color on the opacity slider during color changes.
		if ( 'no-alpha' == flag ) {
			return this.toCSS( 'rgba', '1' ).replace( /\s+/g, '' );
		}

		// If we have a proper opacity value, output RGBa.
		if ( 1 > this._alpha ) {
			return this.toCSS( 'rgba', this._alpha ).replace( /\s+/g, '' );
		}

		// Proceed with stock color.js hex output.
		var hex = parseInt( this._color, 10 ).toString( 16 );
		if ( this.error ) { return ''; }
		if ( hex.length < 6 ) {
			for ( var i = 6 - hex.length - 1; i >= 0; i-- ) {
				hex = '0' + hex;
			}
		}

		return '#' + hex;
	};

	/**
	 * Given an RGBa, RGB, or hex color value, return the alpha channel value.
	 */
	function sinatra_get_alpha_value_from_color( value ) {
		var alphaVal;

		// Remove all spaces from the passed in value to help our RGBa regex.
		value = value.replace( / /g, '' );

		if ( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ ) ) {
			alphaVal = parseFloat( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ )[1] ).toFixed(2) * 100;
			alphaVal = parseInt( alphaVal );
		} else {
			alphaVal = 100;
		}

		return alphaVal;
	}

	/**
	 * Force update the alpha value of the color picker object and maybe the alpha slider.
	 */
	function sinatra_update_alpha_value_on_color_control( alpha, $control, $alphaSlider, update_slider ) {
		var iris, colorPicker, color;

		iris = $control.data( 'a8cIris' );
		colorPicker = $control.data( 'wpWpColorPicker' );

		// Set the alpha value on the Iris object.
		iris._color._alpha = alpha;

		// Store the new color value.
		color = iris._color.toString();

		// Set the value of the input.
		$control.val( color );

		// Update the background color of the color picker.
		colorPicker.toggler.css({
			'background-color': color
		});

		// Maybe update the alpha slider itself.
		if ( update_slider ) {
			sinatraupdate_alpha_value_on_alpha_slider( alpha, $alphaSlider );
		}

		// Trigger change.
		$control.trigger('color-updated');
	}

	/**
	 * Update the slider handle position and label.
	 */
	function sinatra_update_alpha_value_on_alpha_slider( alpha, $alphaSlider ) {
		$alphaSlider.slider( 'value', alpha );
		$alphaSlider.find( '.ui-slider-handle' ).text( alpha.toString() );
	}

	/**
	 * Initialization trigger.
	 */

	$( document ).ready( function( $ ) {

		// Loop over each control and transform it into our color picker.
		$( '.sinatra-color-control' ).each( function() {

			// Scope the vars.
			var $control, startingColor, showOpacity, defaultColor, colorPickerOptions,
				$container, $alphaSlider, alphaVal, sliderOptions;

			// Store the control instance.
			$control = $( this );

			// Get a clean starting value for the option.
			startingColor = $control.val().replace( /\s+/g, '' );

			// Get some data off the control.
			showOpacity  = $control.attr( 'data-show-opacity' );
			defaultColor = $control.attr( 'data-default-color' );

			// Set up the options that we'll pass to wpColorPicker().
			colorPickerOptions = {
				change: function( event, ui ) {
					var value, alpha, $transparency;

					value = ui.color.toString();
					$control.val( value );

					// Set the opacity value on the slider handle when the default color button is clicked.
					if ( defaultColor == value ) {
						alpha = sinatra_get_alpha_value_from_color( value );
						$alphaSlider.find( '.ui-slider-handle' ).text( alpha );
					}

					// Always show the background color of the opacity slider at 100% opacity.
					$transparency = $container.find( '.transparency' );
					$transparency.css( 'background-color', ui.color.toString( 'no-alpha' ) );

					$control.trigger('color-updated');
				},
				palettes: sinatra_customizer_localized.color_palette // Use the passed in palette.
			};

			// Create the colorpicker.
			$control.wpColorPicker( colorPickerOptions );

			$container = $control.parents( '.wp-picker-container:first' );

			// Insert our opacity slider.
			if ( 'true' == showOpacity ) {
				$( '<div class="alpha-color-picker-container">' +
						'<div class="min-click-zone click-zone"></div>' +
						'<div class="max-click-zone click-zone"></div>' +
						'<div class="alpha-slider"></div>' +
						'<div class="transparency"></div>' +
					'</div>' ).appendTo( $container.find( '.wp-picker-holder' ) );
			}

			$alphaSlider = $container.find( '.alpha-slider' );

			// If starting value is in format RGBa, grab the alpha channel.
			alphaVal = sinatra_get_alpha_value_from_color( startingColor );

			// Set up jQuery UI slider() options.
			sliderOptions = {
				create: function( event, ui ) {
					var value = $( this ).slider( 'value' );

					// Set up initial values.
					$( this ).find( '.ui-slider-handle' ).text( value );
					$( this ).siblings( '.transparency ').css( 'background-color', startingColor );
				},
				value: alphaVal,
				range: 'max',
				step: 1,
				min: 0,
				max: 100,
				animate: 300
			};

			// Initialize jQuery UI slider with our options.
			$alphaSlider.slider( sliderOptions );

			// Maybe show the opacity on the handle.
			if ( 'true' == showOpacity ) {
				$alphaSlider.find( '.ui-slider-handle' ).addClass( 'show-opacity' );
			}

			// Bind event handlers for the click zones.
			$container.find( '.min-click-zone' ).on( 'click', function() {
				sinatra_update_alpha_value_on_color_control( 0, $control, $alphaSlider, true );
			});

			$container.find( '.max-click-zone' ).on( 'click', function() {
				sinatra_update_alpha_value_on_color_control( 100, $control, $alphaSlider, true );
			});

			// Bind event handler for clicking on a palette color.
			$container.find( '.iris-palette' ).on( 'click', function(e) {
				e.preventDefault();

				var color, alpha;

				color = $( this ).css( 'background-color' );
				alpha = sinatra_get_alpha_value_from_color( color );

				sinatra_update_alpha_value_on_alpha_slider( alpha, $alphaSlider );

				// Sometimes Iris doesn't set a perfect background-color on the palette,
				// for example rgba(20, 80, 100, 0.3) becomes rgba(20, 80, 100, 0.298039).
				// To compensante for this we round the opacity value on RGBa colors here
				// and save it a second time to the color picker object.
				if ( alpha != 100 ) {
					color = color.replace( /[^,]+(?=\))/, ( alpha / 100 ).toFixed( 2 ) );
				}

				$control.val( color );
				$control.wpColorPicker( 'color', color );
			});

			// Bind event handler for clicking on the 'Clear' button.
			$container.find( '.button.wp-picker-clear' ).on( 'click', function(e) {
				e.preventDefault();

				$control.val( '' );

				sinatra_update_alpha_value_on_alpha_slider( 100, $alphaSlider );

				$control.trigger( 'color-updated' );
			});

			// Bind event handler for clicking on the 'Default' button.
			$container.find( '.button.wp-picker-default' ).on( 'click', function(e) {
				e.preventDefault();

				var alpha = sinatra_get_alpha_value_from_color( defaultColor );

				sinatra_update_alpha_value_on_alpha_slider( alpha, $alphaSlider );
			});

			// Bind event handler for typing or pasting into the input.
			$control.on( 'input', function(e) {
				e.preventDefault();

				var value = $( this ).val();
				var alpha = sinatra_get_alpha_value_from_color( value );

				sinatra_update_alpha_value_on_alpha_slider( alpha, $alphaSlider );
				
				$control.trigger('color-updated');
			});

			// Update all the things when the slider is interacted with.
			$alphaSlider.slider().on( 'slide', function( event, ui ) {
				var alpha = parseFloat( ui.value ) / 100.0;

				sinatra_update_alpha_value_on_color_control( alpha, $control, $alphaSlider, false );

				// Change value shown on slider handle.
				$( this ).find( '.ui-slider-handle' ).text( ui.value );
			});

			// Fix Safari issue on input click
			$( '.iris-picker, .sinatra-color-control' ).on( 'click', function(e) {
				e.preventDefault();
			});
		});
	});
})( jQuery );