;(function( $ ) {

	wp.customize.controlConstructor['sinatra-background'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this,
				range,
				range_input,
				timer,
				sinatra_range_input_number_timeout,
				value   = control.setting._value;

			// Hide unnecessary controls.
			control.container.find( '.background-image-advanced' ).hide();

			// Background-Repeat.
			control.container.on( 'change', '.background-repeat select', function() {
				control.saveValue( 'background-repeat', $( this ).val() );
			});

			// Background-Size.
			control.container.on( 'change click', '.background-size input', function() {
				control.saveValue( 'background-size', $( this ).val() );
			});

			// Background-Attachment.
			control.container.on( 'change click', '.background-attachment input', function() {
				control.saveValue( 'background-attachment', $( this ).val() );
			});

			// Background-Image.
			control.container.on( 'click', '.background-image-upload-button', function( e ) {

				var image = wp.media({ 
					multiple: false,
					title: control.params.l10n.select_image,
					button: {
						text: control.params.l10n.use_image
					},
				}).open().on( 'select', function() {

					// This will return the selected image from the Media Uploader, the result is an object.
					var uploadedImage = image.state().get( 'selection' ).first(),
						uploadedImageJSON = uploadedImage.toJSON(),
						previewImage,
						imageUrl,
						imageID,
						imageWidth,
						imageHeight,
						preview,
						removeButton;

					if ( ! _.isUndefined( uploadedImageJSON.sizes ) ) {
						if ( ! _.isUndefined( uploadedImageJSON.sizes.medium ) ) {
							previewImage = uploadedImageJSON.sizes.medium.url;
						} else if ( ! _.isUndefined( uploadedImageJSON.sizes.thumbnail ) ) {
							previewImage = uploadedImageJSON.sizes.thumbnail.url;
						} else if ( ! _.isUndefined( uploadedImageJSON.sizes.full ) ) {
							previewImage = uploadedImageJSON.sizes.full.url;
						} else {
							previewImage = uploadedImageJSON.url;
						}
					} else {
						previewImage = uploadedImageJSON.url;
					}

					imageUrl    = uploadedImageJSON.url;
					imageID     = uploadedImageJSON.id;
					imageWidth  = uploadedImageJSON.width;
					imageHeight = uploadedImageJSON.height;

					// Show extra controls if the value has an image.
					if ( '' !== imageUrl ) {
						control.container.find( '.background-image-advanced' ).show();
						control.container.find( '.advanced-settings' ).removeClass( 'hidden' ).addClass( 'up' );
					}

					control.saveValue( 'background-image', imageUrl );
					control.saveValue( 'background-image-id', imageID );
					preview      = control.container.find( '.placeholder, .thumbnail' );
					removeButton = control.container.find( '.background-image-upload-remove-button' );

					if ( preview.length ) {
						preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
					}
					if ( removeButton.length ) {
						removeButton.show();
					}
				});

				e.preventDefault();
			});

			control.container.on( 'click', '.background-image-upload-remove-button', function( e ) {

				var preview,
					removeButton;

				e.preventDefault();

				control.saveValue( 'background-image', '' );
				control.saveValue( 'background-image-id', '' );

				preview      = control.container.find( '.placeholder, .thumbnail' );
				removeButton = control.container.find( '.background-image-upload-remove-button' );

				// Hide unnecessary controls.
				control.container.find( '.background-image-advanced' ).hide();
				control.container.find( '.advanced-settings' ).addClass( 'hidden' ).removeClass( 'up' );

				if ( preview.length ) {
					preview.removeClass().addClass( 'placeholder' ).html( control.params.l10n.placeholder );
				}

				if ( removeButton.length ) {
					removeButton.hide();
				}
			});

			control.container.on( 'click', '.advanced-settings', function( e ) {

				$(this).toggleClass('up');
				control.container.find( '.background-image-advanced' ).toggle();
			});

			// Change the text value
			control.container.find( 'input.sinatra-range-input' ).on( 'change keyup', function() {
				control.autocorrect_range_input_number( $( this ), 1000, sinatra_range_input_number_timeout );
			} ).on( 'focusout', function() {
				control.autocorrect_range_input_number( $( this ), 0, sinatra_range_input_number_timeout );
			} );

			// Update the range value
			control.container.find( 'input[type=range]' ).on( 'mousedown', function() {

				range 			= $( this );
				range_input 	= range.parent().children( '.sinatra-range-input' );
				value 			= range.attr( 'value' );

				range_input.val( value );
				
				range.mousemove( function() {

					value = range.attr( 'value' );
					range_input.val( value );
						
					clearTimeout( sinatra_range_input_number_timeout );

					sinatra_range_input_number_timeout = setTimeout( function() {
						control.saveValue( range.data( 'key' ), value );
					}, 25 );

				} );
			} );
		},

		/**
		 * Saves the value.
		 */
		saveValue: function( property, value ) {

			var val = this.setting.get();
			val = val || {};

			if ( value !== val[ property ] ) {
				val = JSON.parse( JSON.stringify( val ) );
				val[ property ] = value;
				this.setting.set( val );
			}
		},

		autocorrect_range_input_number: function( input_number, timeout ) {

			var range_input 	= input_number,
				range 			= range_input.parent().find( 'input[type="range"]' ),
				value 			= parseFloat( range_input.val() ),
				reset 			= parseFloat( range.find( '.sinatra-reset-range' ).attr( 'data-reset_value' ) ),
				step 			= parseFloat( range_input.attr( 'step' ) ),
				min 			= parseFloat( range_input.attr( 'min') ),
				max 			= parseFloat( range_input.attr( 'max') );

			clearTimeout( timeout );

			timeout = setTimeout( function() {

				if ( isNaN( value ) ) {
					range_input.val( reset );
					range.val( reset ).trigger( 'change' );
					return;
				}

				if ( step >= 1 && value % 1 !== 0 ) {
					value = Math.round( value );
					range_input.val( value );
					range.val( value ).trigger( 'change' );
				}

				if ( value > max ) {
					range_input.val( max );
					range.val( max ).trigger( 'change' );
				}

				if ( value < min ) {
					range_input.val( min );
					range.val( min ).trigger( 'change' );
				}

			}, timeout );

			range.val( value ).trigger( 'change' );
			this.saveValue( range.data('key'), value );
		}
	});

})( jQuery );