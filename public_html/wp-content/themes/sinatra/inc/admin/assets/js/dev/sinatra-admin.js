//--------------------------------------------------------------------//
// Sinatra script that handles our admin functionality.
//--------------------------------------------------------------------//

;(function($) {

	"use strict";

	/**
	 * Holds most important methods that bootstrap the whole admin area.
	 * 
	 * @type {Object}
	 */
	var SinatraAdmin = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {
			
			// Document ready
			$(document).ready( SinatraAdmin.ready );

			// Window load
			$(window).on( 'load', SinatraAdmin.load );

			// Bind UI actions
			SinatraAdmin.bindUIActions();

			// Trigger event when Sinatra fully loaded
			$(document).trigger( 'sinatraReady' );
		},

		//--------------------------------------------------------------------//
		// Events
		//--------------------------------------------------------------------//

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {
		},


		/**
		 * Window load.
		 *
		 * @since 1.0.0
		 */
		load: function() {

			// Trigger resize once everything loaded.
			window.dispatchEvent( new Event( 'resize' ) );
		},


		/**
		 * Window resize.
		 *
		 * @since 1.0.0
		*/
		resize: function() {
		},


		//--------------------------------------------------------------------//
		// Functions
		//--------------------------------------------------------------------//


		/**
		 * Bind UI actions.
		 *
		 * @since 1.0.0
		*/
		bindUIActions: function() {
			var $wrap = $( '#wpwrap' );
			var $body = $( 'body' );
			var $this;

			$wrap.on( 'click', '.plugins .si-btn:not(.active)', function(e){

				e.preventDefault();

				if ( $wrap.find( '.plugins .si-btn.in-progress' ).length ) {
					return;
				}

				$this = $(this);

				SinatraAdmin.pluginAction( $this );
			});

			$( document ).on('wp-plugin-install-success', SinatraAdmin.pluginInstallSuccess );
			$( document ).on('wp-plugin-install-error',   SinatraAdmin.pluginInstallError);
		},

		pluginAction: function( $button ) {

			$button.addClass( 'in-progress' ).attr( 'disabled', 'disabled' ).html( sinatra_strings.texts[ $button.data('action') + '-inprogress' ] );

			if ( 'install' === $button.data( 'action' ) ) {

				if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
					wp.updates.requestFilesystemCredentials( event );

					$( document ).on( 'credential-modal-cancel', function() {

						$button.removeAttr('disabled').removeClass( 'in-progress' ).html( sinatra_strings.texts.install );

						wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
					} );
				}

				wp.updates.installPlugin( {
					slug: $button.data('plugin')
				});

			} else {
				
				var data = {
					_ajax_nonce: sinatra_strings.wpnonce,
					plugin: $button.data('plugin'),
					action: 'sinatra-plugin-' + $button.data('action'),
				};

				$.post( sinatra_strings.ajaxurl, data, function( response ){
					if ( response.success ) {
						if ( $button.data('redirect') ) {
							window.location.href = $button.data('redirect');
						} else {
							location.reload();
						}
					} else {
						$( '.plugins .si-btn.in-progress' ).removeAttr('disabled').removeClass( 'in-progress primary' ).addClass('secondary' ).html( sinatra_strings.texts.retry );
					}
				});
			}
		},

		pluginInstallSuccess: function( event, response ) {

			event.preventDefault();

			var $message = jQuery(event.target);
			var $init = $message.data('init');
			var activatedSlug; 

			if ( typeof $init === 'undefined' ) {
				activatedSlug = response.slug;
			} else {
				activatedSlug = $init;
			}

			var $button = $( '.plugins a[data-plugin="' + activatedSlug + '"]' );

			$button.data( 'action', 'activate' );

			SinatraAdmin.pluginAction( $button );
		},

		pluginInstallError: function( event, response ) {

			event.preventDefault();

			var $message = jQuery(event.target);
			var $init = $message.data('init');
			var activatedSlug; 

			if ( typeof $init === 'undefined' ) {
				activatedSlug = response.slug;
			} else {
				activatedSlug = $init;
			}

			var $button = $( '.plugins a[data-plugin="' + activatedSlug + '"]' );

			$button.attr( 'disabled', 'disabled' ).removeClass( 'in-progress primary' ).addClass('secondary' ).html( wp.updates.l10n.installFailedShort );
		},

	}; // END var SinatraAdmin

	SinatraAdmin.init();
	window.sinatraadmin = SinatraAdmin;
	
})(jQuery);