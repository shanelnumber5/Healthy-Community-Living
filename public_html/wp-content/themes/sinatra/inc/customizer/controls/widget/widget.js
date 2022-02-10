;(function($) {

 	"use strict";

	wp.customize.controlConstructor['sinatra-widget'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this;

			control.widget_count = control.container.find( '.widget' ).length;
			control.setupSortable();

			// Expand widget content on header click
			control.container.on( 'click', '.sinatra-widget-container .widget-top', function(){
				$(this).closest( '.widget' ).toggleClass( 'sinatra-expanded' ).find( '.widget-inside' ).slideToggle( 200 );
			});
			
			// Minimize widget content when clicked on Done
			control.container.on( 'click', '.sinatra-widget-container .widget-control-close', function() {
				$(this).closest( '.widget' ).toggleClass( 'sinatra-expanded' ).find( '.widget-inside' ).slideToggle( 200 );
			});

			// Show available widgets
			control.container.on( 'click', '.sinatra-add-widget', function(e) {
				e.preventDefault();
				control.updateList();
			});

		 	control.container.on( 'change paste keyup', 'input, textarea, select', function(e){
		 		control.update();
		 	});

		 	control.container.on( 'click', '.widget-control-remove', function(){
		 		$(this).closest( '.widget' ).remove();
		 		control.update();
		 		control.updateList();
		 	});

		 	control.container.on( 'click', '.sinatra-widget-edit-nav', function(){
		 		wp.customize.control( 'nav_menu_locations[' + $(this).closest( '.sinatra-widget-nav-container' ).data( 'menu-location' ) + ']' ).focus();
		 		control.close();
		 	});

		 	// Close the panel if the URL in the preview changes
			wp.customize.previewer.bind( 'url', this.close );

			$( control.container ).find( '.sinatra-widget-nav-container' ).each( function(){

				var $this = $(this);
				control.bindMenuLocation( $this );
			});
		},

		bindMenuLocation: function( $container ) {
			var menu_location = $container.data('menu-location');

			// Bind menu location setting
			wp.customize( 'nav_menu_locations[' + menu_location + ']', function( value ) {
				value.bind( function( newval ) {
					
					if ( newval ) {
						var menu_name = wp.customize.control( 'nav_menu_locations[' + menu_location + ']' ).container.find('option:selected').html();
					
						$container.addClass( 'sinatra-widget-nav-has-menu' )
							.find( '.sinatra-widget-nav-name' )
							.html( menu_name );
					} else {
						$container.removeClass('sinatra-widget-nav-has-menu');
					}

				});
			});
		},

		// Changes visibility of available widgets
		updateList: function(){

			var widget,
				self = this,
				widgets = self.params.widgets;

			// Filter which widgets are available.
			if ( widgets ) {

				// Hide all widgets.
				$( '#sinatra-available-widgets-list .sinatra-widget' ).hide().removeClass('disabled');

				// Display allowed widgets.
				$.each( widgets, function( index, el ) {

					widget = $( '#sinatra-available-widgets-list #sinatra-widget-tpl-sinatra_customizer_widget_' + index );

					widget.show();

					if ( el.hasOwnProperty( 'max_uses' ) && el.max_uses > 0 && el.max_uses <= $(self.container).find('.sinatra-widget-container [data-widget-type="' + index + '"]').length ) {
						widget.addClass('disabled');
					}
				});
			} else {
				// Show all widgets
				$( '#sinatra-available-widgets-list .sinatra-widget' ).show();
			}
		},

		addWidget: function( widget_id_base ) {
			var widget_html,
				widget_uuid;

			widget_uuid = this.setting.id + '-' + this.widget_count;

			// Get widget form
			widget_html = $.trim( $(this.container).find( '.sinatra-widget-tpl-' + widget_id_base ).html() );
			widget_html = widget_html.replace( /<[^<>]+>/g, function( m ) {
				return m.replace( /__i__|%i%/g, widget_uuid );
			} );

			// Append new widget.
			var $widget = $( widget_html ).appendTo( this.container.find( '.sinatra-widget-container' ) );
			
			// Increase widget count.
			this.widget_count++;

			// Expand the widget and focus first setting.
			$widget.find( '.widget-top' ).trigger( 'click' );

			this.update();

			if ( $widget.find( '.sinatra-widget-nav-container' ).length ) {
				this.bindMenuLocation( $widget.find( '.sinatra-widget-nav-container' ) );
			}
		},

		close: function() {
			$( 'body' ).removeClass( 'sinatra-adding-widget' );
		},

		update: function() {

			// Get all widgets in the area
			var widgets = this.container.find( '.sinatra-widget-container .widget' );
			var inputs, widgetobj, new_value = [], option, checked, $widget;

			if ( widgets.length ) {

				// Get from each widfget
				_.each( widgets, function( widget ){

					$widget   = $( widget );
					widgetobj = {};
					widgetobj.classname = $widget.data( 'widget-base' );
					widgetobj.type = $widget.data( 'widget-type' );
					widgetobj.values = {};

					inputs = $widget.find( 'input, textarea, select' );

					_.each( inputs, function( input ){

						option = $( input ).attr('data-option-name');

						// Save values.
						if ( typeof option !== typeof undefined && option !== false) {
							widgetobj.values[ $(input).attr('data-option-name') ] = $(input).val();
						}
					});

					_.each( $widget.find( '.buttonset' ), function( buttonset ){

						// Save location if exist.
						checked = $( buttonset ).find( 'input[type="radio"]:checked');
							
						// Save values.
						if ( typeof checked !== typeof undefined && checked !== false) {
							widgetobj.values[ checked.data('option-name') ] = checked.val();
						}
					});

					new_value.push( widgetobj );
				});

				this.setting.set( new_value );
			} else {
				this.setting.set( false );
			}
		},

		setupSortable: function() {

			var self = this;

			$( this.container ).find( '.sinatra-widget-container' ).sortable({
				items: '> .widget',
				handle: '.widget-top',
				intersect: 'pointer',
				axis: 'y',
				update: function() {
					self.update();
				}
			});
		}
	});


 	$(document).ready( function(){

 		var control;

	 	$( '.wp-full-overlay' ).on( 'click', '.sinatra-add-widget, .sinatra-close-widgets-panel', function(e) {
	 		e.preventDefault();

	 		$( 'body' ).toggleClass( 'sinatra-adding-widget' );

	 		if ( $( this ).data( 'location-title' ) ) {
	 			control = wp.customize.control( $(this).data('control') );
	 			$( '#sinatra-available-widgets' ).attr( 'data-control', control.params.id ).find( '.sinatra-widget-caption' ).find( 'h3' ).html( $(this).data( 'location-title' ) );
	 		}
	 	});

	 	$( '.wp-full-overlay' ).on( 'click', '.customize-section-back', function(e) {
	 		$( 'body' ).removeClass( 'sinatra-adding-widget' );
	 		$( '#sinatra-available-widgets' ).removeAttr( 'data-control' );
	 	});

		// Add widget to widget control.
	 	$( '#sinatra-available-widgets' ).on( 'click', '.sinatra-widget', function(e) {

	 		// Get active control.
			control = wp.customize.control( $( '#sinatra-available-widgets' ).attr('data-control') );

	 		var widget_id = $( this ).data( 'widget-id' );
	 		var widget_form = control.addWidget( widget_id );
	 		
	 		control.close();
	 	});
	});
 	
})(jQuery);