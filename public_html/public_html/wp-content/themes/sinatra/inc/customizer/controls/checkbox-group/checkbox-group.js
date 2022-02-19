;(function($) {

 	"use strict";

 	wp.customize.controlConstructor['sinatra-checkbox-group'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this,
				setting = control.setting.get();

			control.container.on( 'click', 'input[type="checkbox"]', function() {
				control.save();
			});
		},

		/**
		 * Store value for range control.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 *
		 * @returns {void}
		 */
		save: function() {

			var value = [];

			this.container.find( 'input[type="checkbox"]' ).each( function( index, el ) {
				if ( $(el).is( ':checked' ) ) {
					value.push( $(el).data('id') );
				}
			} );

			this.setting.set( value );
		},
	});

})(jQuery);
