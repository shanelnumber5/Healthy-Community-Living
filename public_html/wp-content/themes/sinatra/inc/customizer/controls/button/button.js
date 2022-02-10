;(function( $ ) {

	wp.customize.controlConstructor['sinatra-button'] = wp.customize.Control.extend({

		ready: function() {

			'use strict';

			var control = this;

			control.container.on( 'click', '.button', function(e){

				var $this = $(this),
					action = $this.data( 'ajax-action' );

				// Check for ajax action.
				if ( action ) {
					e.preventDefault();

					$this.siblings( '.spinner' ).addClass( 'activated' );

					var data = {
						_ajax_nonce: sinatra_customizer_localized.wpnonce,
						action: action,
					};

			 		$.post( sinatra_customizer_localized.ajaxurl, data, function(response) {
			 			
			 			// Check response
			 			if ( response.success ) {

			 				$this.siblings( '.spinner' ).removeClass( 'activated' );

			 				// Should we reload the page?
			 				if ( 'undefined' !== typeof response.data.reload && response.data.reload ) {
			 					location.reload();
			 				}
			 			}
			 		});
				}
			});
		},
	});

})( jQuery );