;( function( $ ) {
	"use strict";

	var sinatraDependencies = {
		listenTo: {},

		init: function() {
			var self = this;

			// Initialize visibility on load
			wp.customize.control.each( function( control ) {
				self.showSinatraControl( control );
			});

			_.each( self.listenTo, function( slaves, master ) {
				_.each( slaves, function( slave ) {
					wp.customize( master, function( setting ) {
						var setupControl = function( control ) {
							var setActiveState,
								isDisplayed;

							isDisplayed = function() {
								return self.showSinatraControl( wp.customize.control( slave ) );
							};
							setActiveState = function() {
								control.active.set( isDisplayed() );
							};

							setActiveState();
							setting.bind( setActiveState );
							control.active.validate = isDisplayed;
						};
						wp.customize.control( slave, setupControl );
					} );
				} );
			});
			
		},

		/**
		 * Should we show the control?
		 *
		 * @since 1.0.0
		 * @param {string|object} control - The control-id or the control object.
		 * @returns {bool}
		 */
		showSinatraControl: function( control ) {
			var self     = this,
				show     = true,
				i;

			if ( _.isString( control ) ) {
				control = wp.customize.control( control );
			}

			// Exit early if control not found or if "required" argument is not defined.
			if ( 'undefined' === typeof control || ( control.params && _.isEmpty( control.params.required ) ) ) {
				return true;
			}

			// Loop control requirements.
			for ( i = 0; i < control.params.required.length; i++ ) {
				if ( ! self.checkCondition( control.params.required[ i ], control, 'AND' ) ) {
					show = false;
				}
			}

			return show;
		},

		/**
		 * Check a condition.
		 *
		 * @since 1.0.0
		 * @param {Object} requirement - The requirement, inherited from showSinatraControl.
		 * @param {Object} control - The control object.
		 * @param {string} relation - Can be one of 'AND' or 'OR'.
		 */
		checkCondition: function( requirement, control, relation ) {
			var self          = this,
			childRelation = ( 'AND' === relation ) ? 'OR' : 'AND',
			nestedItems,
			i;

			// If an array of other requirements nested, we need to process them separately.
			if ( 'undefined' !== typeof requirement[0] && 'undefined' === typeof requirement.control ) {
				nestedItems = [];

				// Loop sub-requirements.
				for ( i = 0; i < requirement.length; i++ ) {
					nestedItems.push( self.checkCondition( requirement[ i ], control, childRelation ) );
				}

				// OR relation. Check that true is part of the array.
				if ( 'OR' === childRelation ) {
					return ( -1 !== nestedItems.indexOf( true ) );
				}

				// AND relation. Check that false is not part of the array.
				return ( -1 === nestedItems.indexOf( false ) );
			}

			// Early exit if setting is not defined.
			if ( 'undefined' === typeof wp.customize.control( requirement.control ) ) {
				return true;
			}

			self.listenTo[ requirement.control ] = self.listenTo[ requirement.control ] || [];
			if ( -1 === self.listenTo[ requirement.control ].indexOf( control.id ) ) {
				self.listenTo[ requirement.control ].push( control.id );
			}

			return self.evaluate(
				requirement.value,
				wp.customize.control( requirement.control ).setting._value,
				requirement.operator
			);
		},

		/**
		 * Figure out if the 2 values have the relation we want.
		 *
		 * @since 1.0.0
		 * @param {mixed} value1 - The 1st value.
		 * @param {mixed} value2 - The 2nd value.
		 * @param {string} operator - The comparison to use.
		 * @returns {bool}
		 */
		evaluate: function( value1, value2, operator ) {
			var found = false;

			if ( '===' === operator ) {
				return value1 === value2;
			}
			if ( '==' === operator || '=' === operator || 'equals' === operator || 'equal' === operator ) {
				return value1 == value2;
			}
			if ( '!==' === operator ) {
				return value1 !== value2;
			}
			if ( '!=' === operator || 'not equal' === operator ) {
				return value1 != value2;
			}
			if ( '>=' === operator || 'greater or equal' === operator || 'equal or greater' === operator ) {
				return value2 >= value1;
			}
			if ( '<=' === operator || 'smaller or equal' === operator || 'equal or smaller' === operator ) {
				return value2 <= value1;
			}
			if ( '>' === operator || 'greater' === operator ) {
				return value2 > value1;
			}
			if ( '<' === operator || 'smaller' === operator ) {
				return value2 < value1;
			}
			if ( 'contains' === operator || 'in' === operator ) {
				if ( _.isArray( value1 ) && _.isArray( value2 ) ) {
					_.each( value2, function( value ) {
						if ( value1.includes( value ) ) {
							found = true;
							return false;
						}
	                } );
					return found;
				}
				if ( _.isArray( value2 ) ) {
					_.each( value2, function( value ) {
						if ( value == value1 ) { // jshint ignore:line
							found = true;
						}
					} );
					return found;
				}
				if ( _.isObject( value2 ) ) {
					if ( ! _.isUndefined( value2[ value1 ] ) ) {
						found = true;
					}
					_.each( value2, function( subValue ) {
						if ( value1 === subValue ) {
							found = true;
						}
					} );
					return found;
				}
				if ( _.isString( value2 ) ) {
					if ( _.isString( value1 ) ) {
						return ( -1 < value1.indexOf( value2 ) && -1 < value2.indexOf( value1 ) );
					}
					return -1 < value1.indexOf( value2 );
				}
			}
			return value1 == value2;
		},
	}; // END var sinatraDependencies

	$( document ).ready( function() {
		sinatraDependencies.init();
	});

} )( jQuery );