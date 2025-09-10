(function( $, app, plugin ) {

	'use strict';

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAIN VARS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	const defaults = {

		/*
			starts with name attr is the default attr to find the selector,
			the exception is for array field,
			which needs to be found using name$ (ends with) because of the way it's name is set
		*/

		prefix	: '',
		selector: 'name^',

		// -------------------------------------------------------------------------

		selector_vals_splitter: ':', // : or |
		vals_splitter					: '|', // | or ,

		// -------------------------------------------------------------------------

		parent_class: 'form-table',

		// -------------------------------------------------------------------------

		animation_speed: 250

	};

	// ---------------------------------------------------------------------------

	plugin.defaults	= $.extend( {}, plugin.defaults, defaults );

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAIN VARS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETUP - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	plugin._init = function()
	{

		const api = this;

		// -------------------------------------------------------------------------

		api.prefix									= api.options.prefix;
		api.selector								= api.options.selector;
		api.selector_vals_splitter	= api.options.selector_vals_splitter;
		api.vals_splitter						= api.options.vals_splitter;
		api.parent_class						= api.options.parent_class;
		api.animation_speed					= api.options.animation_speed;

		// -------------------------------------------------------------------------

		api._set_data();

		if ( ! api.data )
		{
			return;
		}

		// -------------------------------------------------------------------------

		api._config();

		// -------------------------------------------------------------------------

		api._display();

	};
	// _init()



	/**
	 * @internal
	 */
	plugin._set_data = function()
	{

		const api			= this;
		const data		= api.$el.data('conditional_display') || '';
		const tokens	= api._get_tokens( data );

		// -------------------------------------------------------------------------

		api.data = {};

		// -------------------------------------------------------------------------

		if ( ! tokens.length )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// {section_1_group_1_test_0|checked}{section_1_group_1_test_4|some_val}{section_1_group_1_comma_sep|val_1,val_2}

		let i;

		for ( i = 0; i < tokens.length; i++ )
		{
			const token			= tokens[i].split( api.selector_vals_splitter );
			const selector	= token[0] || '';
			const val				= token[1] || '';

			if ( selector && val )
			{
				api.data[ selector ] = val.split( api.vals_splitter );
			}
		}

	};
	// _set_data()



	/**
	 * @internal
	 */
	plugin._get_tokens = function( str )
	{

		const output	= [];
		const regex		= new RegExp('{([^}]+)}', 'g'); // or /{([^}]+)}/g

		// -------------------------------------------------------------------------

		let found;

		while ( found = regex.exec( str ) )
		{
			output.push( found[1] );
		}

		// -------------------------------------------------------------------------

		return output;

	};
	// _get_tokens()



	/**
	 * @internal
	 */
	plugin._config = function()
	{

		const api = this;

		// -------------------------------------------------------------------------

		api.el_id = `${api.prefix}${api.id}`;

		// -------------------------------------------------------------------------

		api.$el
				.attr( 'id', api.el_id )
				.data('conditional_display_data', api.data);

		// -------------------------------------------------------------------------

		api._set_selectors();

	};
	// _config()



	/**
	 * @internal
	 */
	plugin._set_selectors = function()
	{

		const api = this;

		// -------------------------------------------------------------------------

		api.$selectors = {};

		// -------------------------------------------------------------------------

		$.each( api.$el.data('conditional_display_data'), function( key, val ) {

			// the default would be '[name^="' + key + '"]',
			// while for an array field it would be '[name$="' + key + '"]'

			const selector = `[${api.selector}="${key}"]`;

			// -----------------------------------------------------------------------

			let $selector;

			if ( api.parent_class )
			{
				$selector = api.$el
										.parents(`.${api.parent_class}`)
										.first()
										.find( selector );
			}
			else
			{
				$selector = $( selector );
			}

			// -----------------------------------------------------------------------

			// not found? continue...

			if ( $selector.length == 0 )
			{
				return true;
			}

			// -----------------------------------------------------------------------

			api.$selectors[ key ] = $selector;

			api._bind_selector( $selector, key );

			// -----------------------------------------------------------------------

			// check if selector is conditional

			const $selector_is_conditional = $selector.parents(`.${api.prefix}conditional_display`);

			if ( $selector_is_conditional.length == 0 )
			{
				return true;
			}

			// -----------------------------------------------------------------------

			const selector_dependents = $selector_is_conditional.data('conditional_display_dependents') || {};

			selector_dependents[ api.el_id ] = api.$el;

			$selector_is_conditional.data( 'conditional_display_dependents', selector_dependents );

			$selector_is_conditional.data( 'conditional_display_selectors', api.$selectors );

		});

	};
	// _set_selectors()



	/**
	 * @internal
	 */
	plugin._bind_selector = function( $selector )
	{

		const api = this;

		// -------------------------------------------------------------------------

		// @todo: bind to $('#wpbody') for dynamically added elements

		$selector.on( 'change', function( event ) {

			//event.stopImmediatePropagation();

			api._display( api._get_val( $(this) ) );

		});

	};
	// _bind_selector()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETUP - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DISPLAY - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	plugin._display = function( selector_val )
	{

		const api					= this;
		const $selectors	= api.$selectors || {};
		const data				= api.$el.data('conditional_display_data') || {};
		let show					= true;

		// -------------------------------------------------------------------------

		$.each( $selectors, function( key, $selector ) {

			const show_vals	= data[ key ] || [];
			show						= api._show_it( $selector, show_vals, selector_val );

			// -----------------------------------------------------------------------

			// if 1 is false break, otherwise continue
			// show = false (break) | show = true (continue)

			return show;

		});

		// -------------------------------------------------------------------------

		if ( show )
		{
			api.$el.show( api.animation_speed, () => api._visibility_class( api.$el, show ) );

			api._display_dependents( show );
		}
		else
		{
			api._display_dependents( show );

			api.$el.hide( api.animation_speed, () => api._visibility_class( api.$el, show ) );
		}

	};
	// _display()



	/**
	 * @internal
	 */
	plugin._display_dependents = function( show )
	{

		const api					= this;
		const $dependents	= api.$el.data('conditional_display_dependents') || {};

		// -------------------------------------------------------------------------

		$.each( $dependents, function( index, $dependent ) {

			if ( show )
			{
				const $selectors	= api.$el.data('conditional_display_selectors')	|| {};
				const data				= $dependent.data('conditional_display_data') 	|| {};
				let _show					= true;

				$.each( $selectors, function( key, $selector ) {

					const show_vals	= data[ key ] || [];
					_show						= api._show_it( $selector, show_vals );

					// -------------------------------------------------------------------

					return _show;

				});

				// ---------------------------------------------------------------------

				if ( _show )
				{
					$dependent.show( api.animation_speed, () => api._visibility_class( $dependent, _show ) );
				}
			}
			else
			{
				$dependent.hide( api.animation_speed, () => api._visibility_class( $dependent, show ) );
			}

		});

	};
	// _display_dependents()



	/**
	 * @internal
	 */
	plugin._show_it = function( $selector, show_vals, selector_val )
	{

		const api						= this;
		const _selector_val	= selector_val || api._get_val( $selector );
		const _show_vals 		= show_vals || [];

		// -------------------------------------------------------------------------

		if ( $.isArray( _selector_val ) )
		{
			if ( _show_vals.length > 0 )
			{
				let i;

				for ( i = 0; i < _selector_val.length; i++ )
				{
					if ( $.inArray( _selector_val[ i ], _show_vals ) > -1 )
					{
						return true;
					}
				}
			}
		}
		else
		{
			// get element type
			// $conditional.is('input') or $conditional.get(0).tagName to use in switch

			if ( ( $selector.is(':checkbox') || $selector.is(':radio') ) && $selector.is(':checked') )
			{
				return true;
			}
			else
			{
				if ( _show_vals.length > 0 && $.inArray( _selector_val, _show_vals ) > -1 )
				{
					return true;
				}
			}
		}

		// -------------------------------------------------------------------------

		return false;

	};
	// _show_it()



	/**
	 * @internal
	 */
	plugin._visibility_class = function( $el, show )
	{

		if ( show )
		{
			//$el.removeClass('conditional_display_hidden');
		}
		else
		{
			//$el.addClass('conditional_display_hidden');
		}

	};
	// _visibility_class()



	/**
	 * @internal
	 */
	plugin._get_val = function( $el )
	{

		let output = $el.val();

		// -------------------------------------------------------------------------

		if ( ( $el.is(':checkbox') || $el.is(':radio') ) && $el.hasClass('select_checkbox_input') )
		{
			const id		= $el.attr('id') || false;
			const name	= $el.attr('name');
			let $checked;

			if ( id )
			{
				$checked = $(`#${id}:checked`);
			}
			else
			{
				$checked = $(`[name="${name}"]:checked`);
			}

			// -----------------------------------------------------------------------

			if ( $checked.length > 0 )
			{
				const vals = [];

				$.each( $checked, function() {

					vals.push( $(this).val() );

				});

				// ---------------------------------------------------------------------

				output = vals;
			}
			else
			{
				output = -1;
			}
		}

		// -------------------------------------------------------------------------

		return output;

	};
	// _get_val()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DISPLAY - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/**
	 * add it
	 */
	app.add_plugin( plugin );



})( jQuery, window.dotaim, window.dotaim.plugin('dotaim_field_conditional_display') );
