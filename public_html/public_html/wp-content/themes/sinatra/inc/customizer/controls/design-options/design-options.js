;(function($) {

 	"use strict";

 	wp.customize.controlConstructor['sinatra-design-options'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this,
				setting = control.setting.get(),
				range,
				range_input,
				value,
				sinatra_range_input_number_timeout,
				popup_content = control.container.find( '.popup-content' );


			// Range controls.
			control.container.find( '.sinatra-range-wrapper' ).each( function() {
				var $this = $(this);

				$this.rangeControl({
					id:         control.params.id + '-' + $this.data('option-id'),
					option:     $this.data('option-id'),
					value:      setting[ $this.data('options-id') ],
					responsive: control.params.responsive,
					change: function() {
						control.update_value();
					}
				});
			});

			// Change the text value
			control.container.find( 'input.sinatra-range-input' ).on( 'change keyup', function() {
				control.autocorrect_range_input_number( $( this ), 1000, sinatra_range_input_number_timeout );
			} ).on( 'focusout', function() {
				control.autocorrect_range_input_number( $( this ), 0, sinatra_range_input_number_timeout );
			});

			// Visibility deps.
			control.container.on( 'change', '[data-option="background-type"], [data-option="gradient-type"]', function(){
				var field = $(this).attr( 'data-option' );
				control.container.find( '[data-dep-field="' + field + '"]' ).hide();
				control.container.find( '[data-dep-field="' + field + '"][data-dep-value="' + $(this).val() + '"]' ).show();
			});

			control.container.find( '[data-option="background-type"], [data-option="gradient-type"]' ).trigger('change');

			// Changes.
			control.container.on( 'color-updated', '.sinatra-color-control', function(){
				control.update_value();
			});

			control.container.on( 'change', '.sinatra-select-wrapper', function(){
				control.update_value();
			});

			// Advanced panel.
			control.container.find( '.popup-link' ).on( 'click', function(){
				popup_content.toggleClass( 'hidden' );
				$(this).toggleClass( 'active' );
				$(this).siblings( '.reset-defaults' ).toggleClass( 'active' );

				// Close the panel on outside click.
				$( 'body' ).on( 'click', outside_click_close );
			});

			var outside_click_close = function(e) {

				if ( ! $( e.target ).closest( '.customize-save-button-wrapper' ).length && 
					 ! control.container.has( $( e.target ).closest( '.popup-link' ) ).length && 
					 ! control.container.has( $( e.target ).closest( '.popup-content' ) ).length && 
					 ! popup_content.hasClass( 'hidden' ) &&
					 ! $( e.target ).closest( '.reset-defaults' ).length ) {
					popup_content.addClass( 'hidden' );
					control.container.find( '.popup-link' ).removeClass( 'active' );
					control.container.find( '.reset-defaults' ).removeClass( 'active' );
					$( 'body' ).off( 'click', outside_click_close );
				}
			};

			// Hide unnecessary controls.
			control.container.find( '.background-image-advanced' ).hide();

			// Background-Repeat.
			control.container.on( 'change', '.background-repeat select', function() {
				control.update_value();
			});

			// Background-Size.
			control.container.on( 'change click', '.background-size input', function() {
				control.update_value();
			});

			// Background-Attachment.
			control.container.on( 'change click', '.background-attachment input', function() {
				control.update_value();
			});

			// Background-Image.
			control.container.on( 'click', '.background-image-upload-button', function( e ) {

				$( 'body' ).off( 'click', outside_click_close );

				var image = wp.media({ 
					multiple: false,
					title: control.params.l10n.image.select_image,
					button: {
						text: control.params.l10n.image.use_image
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

					control.container.find( '[data-option="background-image"]' ).val( imageUrl );
					control.container.find( '[data-option="background-image-id"]' ).val( imageID );

					control.update_value();

					preview      = control.container.find( '.placeholder, .thumbnail' );
					removeButton = control.container.find( '.background-image-upload-remove-button' );

					if ( preview.length ) {
						preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
					}
					if ( removeButton.length ) {
						removeButton.show();
					}

					setTimeout( function() {
						$( 'body' ).on( 'click', outside_click_close );
					}, 100 );
				});

				e.preventDefault();
			});

			control.container.on( 'click', '.background-image-upload-remove-button', function( e ) {

				var preview,
					removeButton;

				e.preventDefault();

				control.container.find( '[data-option="background-image"]' ).val( '' );
				control.container.find( '[data-option="background-image-id"]' ).val( '' );

				control.update_value();

				preview      = control.container.find( '.placeholder, .thumbnail' );
				removeButton = control.container.find( '.background-image-upload-remove-button' );

				// Hide unnecessary controls.
				control.container.find( '.background-image-advanced' ).hide();
				control.container.find( '.advanced-settings' ).addClass( 'hidden' ).removeClass( 'up' );

				if ( preview.length ) {
					preview.removeClass().addClass( 'placeholder' ).html( control.params.l10n.image.placeholder );
				}

				if ( removeButton.length ) {
					removeButton.hide();
				}
			});

			control.container.on( 'click', '.advanced-settings', function( e ) {
				$(this).toggleClass('up');
				control.container.find( '.background-image-advanced' ).toggle();
			});

			// Spacing field.
			
			// Linked button
			control.container.on( 'click', '.sinatra-spacing-linked', function() {

				// Set up variables
				var $this = $( this );
				
				// Remove linked class
				$this.closest( 'ul' ).find( '.spacing-input' ).removeClass( 'linked' );
				
				// Remove class
				$this.parent( '.spacing-link-values' ).removeClass( 'unlinked' );
			});

			// Unlinked button
			control.container.on( 'click', '.sinatra-spacing-unlinked', function() {

				// Set up variables
				var $this = $( this );
				
				// Remove linked class
				$this.closest( 'ul' ).find( '.spacing-input' ).addClass( 'linked' );
				
				// Remove class
				$this.parent( '.spacing-link-values' ).addClass( 'unlinked' );
			});

			// Values linked inputs
			control.container.on( 'input', '.linked input', function() {
				var $val = $( this ).val();
				$(this).closest( '.spacing-input' ).siblings( '.linked' ).find( 'input' ).val( $val ).change();
			});

			// Store new inputs
			control.container.on( 'change input', '.spacing-input input', function() {
				control.update_value();
			});

			// Reset default.
			control.container.find( '.reset-defaults' ).on( 'click', function() {

				var item, option_id;

				control.container.find( '[data-option]' ).each( function() {
					item = $(this);
					option_id = item.data('option');

					if ( 'background-size' === option_id || 'background-attachment' === option_id ) {
						
						item.prop( 'checked', false );

						if ( ( option_id in control.params.default ) && control.params.default[ option_id ] === item.val() ) {
							item.prop( 'checked', true );
						}

					} else {
						item.val( control.params.default[ item.data('option') ] ).trigger( 'change' );
					}
				});

				control.container.find( '.background-image-upload-remove-button' ).click();

				control.update_value();
			});
		},

		// Update value.
		update_value: function(){

			var self = this,
			    value = {},
			    option;

			self.container.find( '[data-option]' ).each( function(){
				option = $(this).data('option');

				if ( 'background-size' === option || 'background-attachment' === option ) {
					value[ option ] = $(this).is( ':checked' ) ? $(this).val() : value[ option ];
				} else {
					value[ option ] = $(this).val();
				}
			});

			self.setting.set( value );
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
			this.update_value();
		}
	});

})(jQuery);