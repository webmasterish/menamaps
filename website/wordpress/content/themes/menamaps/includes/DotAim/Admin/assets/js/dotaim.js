/**
 * dotaim Namespace
 */
(function( $, document, window, undefined ) {

	'use strict';

	// ---------------------------------------------------------------------------

	const app = {};

	// ---------------------------------------------------------------------------

	// Closure to contain cached plugins

	app.plugin = function()
	{

		// Internal plugins cache.

		const plugins = {};

		// -------------------------------------------------------------------------

		// Create a new plugin reference scaffold or load an existing one

		return function( name )
		{
			// If this plugin has already been created, return it.

			if ( plugins[ name ] )
			{
				return plugins[ name ];
			}

			// -----------------------------------------------------------------------

			// Create a comon plugin and save it under this name

			const plugin = {

				// default info

				info: {
					name				: name,
					shortname		: name,
					title				: '',
					version			: _.get( window, 'dotaim_admin.version' ),
					description	: '',
				},

				// ---------------------------------------------------------------------

				// default options

				defaults: {},

				// ---------------------------------------------------------------------

				// default init

				init: function( element, options, id ) {

					this.el		= element;
					this.$el	= $( element );

					// -------------------------------------------------------------------

					this.id					= id;
					this.prefix_ids	= this.id + '_';
					this.suffix_ids	= '_' + this.id;

					// -------------------------------------------------------------------

					this.options = $.extend( true, {}, this.defaults, options );

					// -------------------------------------------------------------------

					this._name = this.info.name;

					// -------------------------------------------------------------------

					this._defaults = this.defaults;

					// -------------------------------------------------------------------

					this._prefix = this.info.shortname + '_';

					// -------------------------------------------------------------------

					if ( $.isFunction( this._init ) )
					{
						this._init();
					}

					// -------------------------------------------------------------------

					return this;

				},
				// init()

				// ---------------------------------------------------------------------

				options_reset: function( options ) {

					this.options = $.extend( true, {}, this.options, options );

				},
				// options_reset()

			};

			// -----------------------------------------------------------------------

			// Save it as new object

			plugins[ name ] = Object.create( plugin );

			// -----------------------------------------------------------------------

			return plugins[ name ];
		};

	}();
	// app.plugin()

	// ---------------------------------------------------------------------------

	// Create a plugin based on a defined object

	app.add_plugin = function( plugin )
	{

		if ( ! plugin.info.name )
		{
			return;
		};

		// -------------------------------------------------------------------------

		const name = plugin.info.name;

		// -------------------------------------------------------------------------

		// make sure it's not yet added as a plugin

		if ( $.fn[ name ] )
		{
			return;
		};

		// -------------------------------------------------------------------------

		$.fn[ name ] = function( params )
		{

			const plugin_name	= `plugin_${name}`;
			const elements		= this;
			let retval				= this;
			let args;
			let instance;
			let id;

			// -----------------------------------------------------------------------

			if ( typeof params === 'string' )
			{
				args = Array.prototype.slice.call( arguments, 1 );

				// ---------------------------------------------------------------------

				elements.each( function( i ) {

					instance = $.data( this, plugin_name );

					// -------------------------------------------------------------------

					// not initialized

					if ( ! instance )
					{
						console.error(
							`Cannot call methods on ${name} prior to initialization; ` +
							`attempted to call method "${params}"`
						);

						return;
					}

					// -------------------------------------------------------------------

					// method doesn't exist or it's private

					if ( ! $.isFunction( instance[ params ] ) || params.charAt(0) === '_' )
					{
						console.error(`No such method "${params}" for ${name} instance`);

						return;
					}

					// -------------------------------------------------------------------

					// call method

					retval = instance[ params ].apply( instance, args );

				});
			}
			else
			{
				elements.each( function( i ) {

					instance = $.data( this, plugin_name );

					// -------------------------------------------------------------------

					if ( instance )
					{
						instance.options_reset( params );
					}
					else
					{
						id = name + '_' + new Date().getTime();

						$.data( this, plugin_name, Object.create( plugin ).init( this, params, id ) );
						$.data( this, `${plugin_name}_id`, id );
					}

				});
			}

			// -----------------------------------------------------------------------

			return retval || elements;

		};
		// $.fn[ name ]()

	};
	// app.add_plugin()

	// ---------------------------------------------------------------------------

	// Namespace

	window.dotaim = app;

})( window.jQuery, window.document, window );



(function( $ ) {

	$.dotaim = $.dotaim || {};

	// ---------------------------------------------------------------------------

	$.dotaim.settings = {

		'prefix'	: 'dotaim_forms_fields_',
		'defaults': {} // could use this to set each plugin defaults

	};

	// ---------------------------------------------------------------------------

	// @consider
	/*
	$.dotaim.forms = {

		fields: {
			'conditional_display'	: {},
			'media'								: {},
			'array_field'					: {},
		}

	};
	*/

})( window.jQuery );
