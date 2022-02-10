;(function($) {

 	"use strict";

 	// Typography control
	wp.customize.controlConstructor[ 'sinatra-typography' ] = wp.customize.Control.extend({
		
		ready: function() {
			"use strict";

			var control = this,
				setting = control.setting.get(),
				populated = false,
				$font_family,
				value,
				popup_content = control.container.find( '.popup-content' );

			control.container.find( '#font-family-' + control.params.id ).select2();
			control.container.find( '#font-subsets-' + control.params.id ).select2();

			control.update_font_subsets_field( setting['font-family'] );
			
			control.container.on( 'change', '#font-family-' + control.params.id, function(){
				control.update_font_weight_field( $(this).val() );
				control.update_font_subsets_field( $(this).val() );
			});

			// Update value.
			control.container.on( 'change', '[data-option]', function(){
				control.update_value();
			});

			// Range controls.
			control.container.find( '.sinatra-range-wrapper' ).each( function() {
				var $this = $(this);

				$this.rangeControl({
					id:         control.params.id + '-' + $this.data('option-id'),
					option:     $this.data('option-id'),
					unit:       control.params.units[ $this.data('option-id') ],
					value:      setting[ $this.data('options-id') ],
					responsive: control.params.responsive,
					change: function() {
						control.update_value();
					}
				});
			});

			// Populate font-family field
			control.container.on( 'select2:opening', '#font-family-' + control.params.id, function() {
				control.populate_font_family_field();
				control.container.off( 'select2:opening', '#font-family-' + control.params.id );
			});

			// Populate font-weight field.
			control.container.on( 'mousedown', '[data-option="font-weight"]', function(){
				control.update_font_weight_field( control.container.find( '#font-family-' + control.params.id ).val() );
				control.container.off( 'mousedown', '[data-option="font-weight"]' );
			});

			// Populate font-subsets field.
			control.container.on( 'select2:opening', '#font-subsets-' + control.params.id, function() {
				control.update_font_subsets_field( control.container.find( '#font-family-' + control.params.id ).val() );
				control.container.off( 'mousedown', '[data-option="font-subsets"]' );
			});

			// Advanced panel.
			control.container.find( '.popup-link' ).on( 'click', function(){

				popup_content.toggleClass( 'hidden' );

				$(this).toggleClass( 'active' );
				$(this).siblings( '.reset-defaults' ).toggleClass( 'active' );

				// Close the panel on outside click.
				$( 'body' ).on( 'click', outside_click_close );
			});

			// Reset options.
			control.container.find( '.reset-defaults' ).on( 'click', function(){
				
				var selectFields = [ 'font-weight', 'font-style', 'text-transform', 'text-decoration' ],
					rangeFields = [ 'font-size', 'line-height', 'letter-spacing' ];

				if ( 'font-family' in control.params.display ) {
					control.populate_font_family_field();
					control.container.find( '#font-family-' + control.params.id ).val( control.params.default[ 'font-family' ] ).trigger( 'change' );
				}

				selectFields.forEach( (item) => {
					if ( item in control.params.display ) {
						control.container.find( '[data-option="' + item + '"]' ).val( control.params.default[ item ] ).trigger( 'change' );
					}
				});

				rangeFields.forEach( (item) => {
					control.container.find( '[data-option-id="' + item + '"]' ).find( '.sinatra-reset-range' ).click();
				});
			});

			var outside_click_close = function(e) {

				if ( ! $( e.target ).closest( '.select2-container' ).length &&
					 ! $( e.target ).closest( '.customize-save-button-wrapper' ).length && 
					 ! $( e.target ).closest( '.reset-defaults' ).length &&
					 ! control.container.has( $( e.target ).closest( '.popup-link' ) ).length && 
					 ! control.container.has( $( e.target ).closest( '.popup-content' ) ).length && 
					 ! popup_content.hasClass( 'hidden' ) ) {
					popup_content.addClass( 'hidden' );
					control.container.find( '.popup-link' ).removeClass( 'active' );
					control.container.find( '.reset-defaults' ).removeClass( 'active' );
					$( 'body' ).off( 'click', outside_click_close );
				}
			};
		},

		// Update value.
		update_value: function(){

			var self = this,
			    value = {},
			    option;

			self.container.find( '[data-option]' ).each( function(){

				option = $(this).data('option');
				
				if ( 'font-size-unit' === option || 'line-height-unit' === option || 'letter-spacing-unit' === option ) {
					value[ option ] = $(this).is( ':checked' ) ? $(this).val() : value[ option ];
				} else {
					value[ option ] = $(this).val();
				}
			});

			self.setting.set( value );
		},

		// Populate available font weights for given font family.
		update_font_weight_field: function( font_family ) {

			var self     = this,
				options  = '',
				selected = '',
				setting  = self.setting.get();

			if ( 'inherit' === font_family ) {
				selected = ' selected="selected"';
			}

			options += '<option value="inherit"' + selected + '>' + self.params.l10n.inherit + '</option>';

			if ( 'inherit' === font_family || 'default' === font_family ) {

				var default_weights = [ '100', '200', '300','400','500', '600', '700', '800', '900' ];

				$.each( default_weights, function( index, variant ){

					if ( variant === setting['font-weight'] ) {
						selected = ' selected="selected"';
					} else {
						selected = '';
					}

					options += '<option value="' + variant + '"' + selected + '>' + self.params.l10n.weights[ variant ] + '</option>';

				});
			} else {

				$.each( sinatra_typography_vars.fonts, function( group_id, group ){

					if ( 'undefined' !== typeof group.fonts[ font_family ] ) {
						
						$.each( group.fonts[ font_family ].variants, function( index, variant ){

							if ( variant === setting['font-weight'] ) {
								selected = ' selected="selected"';
							} else {
								selected = '';
							}

							options += '<option value="' + variant + '"' + selected + '>' + self.params.l10n.weights[ variant ] + '</option>';

						});
						return;
					}
				});
			}

			self.container.find( '.sinatra-typography-font-weight' ).find( 'select' ).html( options );
		},

		// Populate available font subsets for given font family.
		update_font_subsets_field: function( font_family ) {

			var self     = this,
				options  = '',
				selected = '',
				setting  = self.setting.get(),
				field    = self.container.find( '.sinatra-typography-font-subsets' );

			if ( 'default' === font_family || 'inherit' === font_family ) {
				field.hide();
				return;
			}

			$.each( sinatra_typography_vars.fonts, function( group_id, group ){

				if ( 'undefined' !== typeof group.fonts[ font_family ] ) {

					if ( 'system_fonts' === group_id || 'standard_fonts' === group_id ) {
						field.hide();
					} else {
						field.show();
					}

					$.each( group.fonts[ font_family ].subsets, function( index, subsets ){

						selected = '';

						if ( setting['font-subsets'] && -1 !== setting['font-subsets'].indexOf( subsets ) || 'latin' === subsets ) {
							selected = ' selected="selected"';
						}

						options += '<option value="' + subsets + '"' + selected + '>' + subsets + '</option>';
					});
					return;
				}
			});

			field.find( 'select' ).html( options );
		},

		// Populate available font families.
		populate_font_family_field: function(e){

			var self     = this,
				options  = '',
				selected = '',
				setting  = self.setting.get();

			selected = 'inherit' === setting['font-family'] ? ' selected="selected"' : '';
			options += '<option value="inherit"' + selected + '>' + self.params.l10n.inherit + '</option>';

			selected = 'default' === setting['font-family'] ? ' selected="selected"' : '';
			options += '<option value="default"' + selected + '>' + self.params.l10n.default + '</option>';

			$.each( sinatra_typography_vars.fonts, function( group_id, group ){

				options += '<optgroup label="' + group.name + '">';

				$.each( group.fonts, function( font, font_options ) {

					if ( font === setting['font-family'] ) {
						selected = ' selected="selected"';
					} else {
						selected = '';
					}

					options += '<option value="' + font + '"' + selected + '>' + font + '</option>';
				});

				options += '</optgroup>';
			});

			self.container.find( '#font-family-' + self.params.id ).html( options );
		},
	});

})(jQuery);