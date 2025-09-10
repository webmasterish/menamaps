(function( $, app, plugin ) {

	'use strict';

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAIN VARS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	const defaults = {

		// @todo:
		// is_multiple/is_single

		css_class									: 'array_field',
		max_items									: 0, // less than 1 no limit
		max_items_message					: 'Maximum allowed items reached.',
		item_header_field_selector: ':input:first',
		animation_speed						: 'fast',

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

		const el_options = {};

		$.each( api.$el.data(), function( key, value ) {

			if ( key.startsWith('options_') )
			{
				el_options[  key.replace('options_', '') ] = value;
			}

		});

		api.options	= $.extend( api.options, el_options );

		$.each( api.options, function( key, value ) {

			if ( ! api.hasOwnProperty( key ) )
			{
				api[ key ] = value;
			}

		});

		// -------------------------------------------------------------------------

		api.css_prefix		= `${api.css_class}_`;
		api.$list					= $(`.${api.css_prefix}list`, api.$el).first();
		api.item_template	= $(`.${api.css_prefix}item_template`, api.$el).first().html();

		// -------------------------------------------------------------------------

		// @notes:
		// remove "for" attr from labels because it's set to id which isn't relevant
		// and clicking on label is handled in api.bind()

		$(`.${api.css_prefix}item label`).removeAttr('for');

		// -------------------------------------------------------------------------

		api.bind();

		// -------------------------------------------------------------------------

		api.refresh();

		// -------------------------------------------------------------------------

		// @notes:
		// using $(`.${api.css_prefix}item_settings`, $list).trigger('change')
		// doesn't work in api.refresh(), so i had to use it this way

		$(`.${api.css_prefix}item_settings`, api.$list).each( function() {

			const $settings	= $(this);
			const $input		= $( api.item_header_field_selector, $settings ).first();

			if ( $input.length )
			{
				api.change_item_header_title( $input );
			}

		});

	};
	// _init()



	/**
	 * @internal
	 */
	plugin.bind = function()
	{

		const api = this;

		// -------------------------------------------------------------------------

		api.$el.on('click', `.${api.css_prefix}action`, function( e ) {

			e.preventDefault();

			e.stopPropagation();

			// -----------------------------------------------------------------------

			const $button						= $(this);
			const action						= $button.data('action');
			const list_items_count	= api.$list.children(`.${api.css_prefix}item`).length;

			switch ( action )
			{
				case 'item_add':

					if ( 		api.max_items > 0
							 && list_items_count >= api.max_items )
					{
						alert( api.max_items_message );

						break;
					}

					// -------------------------------------------------------------------

					const $new_item = $( api.item_template );

					if ( $button.parent().is(`.${api.css_prefix}item_actions, .${api.css_prefix}item_header_actions`) )
					{
						const $parent_item = $button.parents(`.${api.css_prefix}item`).first();

						if ( $parent_item.length )
						{
							$new_item.insertAfter( $parent_item );
						}
						else
						{
							api.$list.append( $new_item );
						}
					}
					else
					{
						api.$list.append( $new_item );
					}

					$new_item.removeClass('collapsed');

					$(`.${api.css_prefix}item_settings`, $new_item).find(':input:first').focus();

					api.attach_plugins( $new_item );

					api.refresh();

					break;

				// ---------------------------------------------------------------------

				case 'item_clone':

					if ( api.max_items > 0 && list_items_count >= api.max_items )
					{
						alert(api.max_items_message);

						break;
					}

					// -------------------------------------------------------------------

					// Get the source item and its index
					const $source_item = $button.parents(`.${api.css_prefix}item`).first();
					const source_index = $source_item.index();

					// Create HTML clone without data and events
					const source_html = $source_item[0].outerHTML;
					const $clone_item = $(source_html);

					// Insert the clone after the source item
					$clone_item.insertAfter($source_item).removeClass('collapsed');

					// Focus on the first input
					$(`.${api.css_prefix}item_settings`, $clone_item).find(':input:first').focus();

					// Initialize plugins for the cloned item
					api.attach_plugins($clone_item);

					// Update field attributes for the clone
					api.update_item_indices($clone_item, source_index, 1, true);

					// Update indices for all subsequent items
					const $next_items = $clone_item.nextAll(`.${api.css_prefix}item`);
					if ( $next_items.length )
					{
						api.update_item_indices( $next_items, source_index, 1, true );
					}

					api.refresh();

					break;

				// ---------------------------------------------------------------------

				case 'item_delete':

					const $item_to_delete	= $button.parents(`.${api.css_prefix}item`).first();
					const delete_index		= $item_to_delete.index();

					// Get all items that come after this one
					const $subsequent_items = $item_to_delete.nextAll(`.${api.css_prefix}item`);

					$item_to_delete.fadeOut(api.animation_speed, function() {

						$(this).remove();

						// Update indices for subsequent items
						if ( $subsequent_items.length )
						{
							api.update_item_indices( $subsequent_items, delete_index, -1, false );
						}

						api.refresh();
					});

					break;

				// ---------------------------------------------------------------------

				case 'collapse_all':

					$(`.${api.css_prefix}item`, api.$list)
					.addClass('collapsed')
					.children(`.${api.css_prefix}item_settings`)
					.hide( api.animation_speed );

					break;

				// ---------------------------------------------------------------------

				case 'delete_all':

					const confirm_message = $button.data('confirm_message');

					if ( confirm_message && ! confirm( confirm_message ) )
					{
						return;
					}

					api.$list.empty();

					api.refresh();

					break;
			}

		});

		// -------------------------------------------------------------------------

		api.$el.on('click', `.${api.css_prefix}item.collapsible .${api.css_prefix}item_collapsible_toggle`, function( e ) {

			e.preventDefault();

			e.stopPropagation();

			// -----------------------------------------------------------------------

			const $item = $(this).parents(`.${api.css_prefix}item`).first();

			$(`.${api.css_prefix}item_settings`, $item).first()
			.toggle( api.animation_speed, function() {

				$item.toggleClass( 'collapsed', $(this).is(':hidden') );

			});

		});

		// -------------------------------------------------------------------------

		api.$el.on('click', `.${api.css_prefix}item label`, function( e ) {

			// @notes:
			// we need default events for the case of checkbox wrapped in label
			//e.preventDefault();

			e.stopPropagation();

			// -----------------------------------------------------------------------

			const $input = $(this)
											.parents('tr')
											.first()
											.find('td')
											.first()
											.find(':input')
											.first()
											.focus();

			// -----------------------------------------------------------------------

			if ( $input.is(':checkbox') || $input.is(':radio') )
			{
				$input
				.prop('checked', ! $input.prop('checked'))
				.trigger('change'); // this is needed for label in th to work, but not reuired for label wrapping checkbox
			}

		});

		// -------------------------------------------------------------------------

		api.$list.sortable({
			cursor			: 'move',
			handle			: `.${api.css_prefix}item_sort_handle`,
			placeholder	: 'ui-state-highlight',
			stop				: function( event, ui ) { api.set_fields_attributes(); }
		});

	};
	// bind()



	/**
	 * Common function to update indices for items
	 */
	plugin.update_item_indices = function( $items, start_index, offset, is_clone )
	{

		const api				= this;
		const array_key	= api.$el.data('key');

		// -------------------------------------------------------------------------

		$items.each( function( item_offset ) {

			const $item			= $(this);
			const old_index	= start_index + item_offset + (is_clone ? 0 : 1);
			const new_index	= old_index + offset;

			// -----------------------------------------------------------------------

			// Update input names/ids

			$item.find(':input').each( function() {

				const $input = $(this);
				const name = $input.attr('name');

				if ( name )
				{
					// Pattern to match field names with this array's key
					const pattern = new RegExp(`(.*\\[${array_key}\\])\\[(\\d+)\\](.*)$`);
					const matches = name.match(pattern);

					if ( matches )
					{
						const prefix				= matches[1];  // e.g., twitter[auto_tweet][schedule]
						const current_index	= parseInt(matches[2]); // e.g., 0
						const suffix				= matches[3];   // e.g., [tweet][0][when]

						// Only update if this is within our target range
						if ( 		( is_clone && current_index >= old_index )
								 || ( ! is_clone && current_index > start_index ) )
						{
							const new_name	= `${prefix}[${new_index}]${suffix}`;
							const new_id		= api.field_name_to_id(new_name);

							$input.attr('name', new_name).attr('id', new_id);
						}
					}
				}

			});

			// -----------------------------------------------------------------------

			// Update conditional display attributes

			$item.find('[data-conditional_display]').each( function() {

				const $el					= $(this);
				const conditional	= $el.attr('data-conditional_display');

				if ( conditional )
				{
					// Pattern for conditional attributes
					const pattern = new RegExp(`\\{(.*\\[${array_key}\\])\\[(\\d+)\\](.*):(.*)}$`);
					const matches = conditional.match(pattern);

					if ( matches )
					{
						const prefix				= matches[1];
						const current_index	= parseInt(matches[2]);
						const suffix				= matches[3];
						const options				= matches[4];

						// Only update if within target range
						if ( 		( is_clone && current_index >= old_index )
								 || ( ! is_clone && current_index > start_index ) )
						{
							const new_conditional = `{${prefix}[${new_index}]${suffix}:${options}}`;

							$el.attr('data-conditional_display', new_conditional);
						}
					}
				}

			});

			// -----------------------------------------------------------------------

			// Update nested array fields

			$item.find(`.${api.css_class}`).each( function() {

				const $nested	= $(this);
				const prefix	= $nested.attr('data-field_name_prefix');

				if ( prefix )
				{
					// Pattern for nested array prefixes
					const pattern = new RegExp(`(.*\\[${array_key}\\])\\[(\\d+)\\](.*)$`);
					const matches = prefix.match(pattern);

					if ( matches )
					{
						const prefix_base		= matches[1];
						const current_index	= parseInt(matches[2]);
						const suffix				= matches[3];

						// Only update if within target range
						if ( 		( is_clone && current_index >= old_index )
								 || ( ! is_clone && current_index > start_index ) )
						{
							const new_prefix = `${prefix_base}[${new_index}]${suffix}`;

							$nested.attr('data-field_name_prefix', new_prefix);
						}
					}

					// -------------------------------------------------------------------

					// Also update ID prefixes

					const id_prefix = $nested.attr('data-field_id_prefix');

					if ( id_prefix )
					{
						const parts = id_prefix.split('_');

						// Update numeric parts that match our target indices
						for ( let i = 0; i < parts.length; i++ )
						{
							if ( /^\d+$/.test( parts[i] ) )
							{
								const index = parseInt(parts[i]);
								if ( 		( is_clone && index >= old_index)
										 || ( ! is_clone && index > start_index ) )
								{
									parts[i] = String(new_index);

									break; // Only update the first matching index
								}
							}
						}

						$nested.attr('data-field_id_prefix', parts.join('_'));
					}

					// -------------------------------------------------------------------

					// Refresh nested array fields if needed

					const nested_api = $nested.data('dotaim_field_array_field');

					if ( nested_api && typeof nested_api.refresh === 'function' )
					{
						nested_api.refresh();
					}
				}

			});

		});

	};
	// update_item_indices()



	/**
	 * @internal
	 */
	plugin.set_fields_attributes = function()
	{

		const api = this;

		// -------------------------------------------------------------------------

		// Get basic info for this array field

		const key							= api.$el.data('key');
		let field_name_prefix	= api.$el.attr('data-field_name_prefix');

		// -------------------------------------------------------------------------

		// Initialize field name prefix if not set

		if ( ! field_name_prefix )
		{
			const panel_id		= api.$el.data('panel_id');
			const section_id	= api.$el.data('section_id');
			field_name_prefix	= `${panel_id}[${section_id}][${key}]`;
		}

		// -------------------------------------------------------------------------

		// Update this array field's data attribute

		api.$el.attr('data-field_name_prefix', field_name_prefix);

		// -------------------------------------------------------------------------

		// Process all items in this array field

		const $items = api.$list.children(`.${api.css_prefix}item`);

		$items.each( function( item_index ) {

			const $item = $(this);

			// -----------------------------------------------------------------------

			// Process all input elements directly in this item (not nested ones)

			$item.find(':input').each( function() {

				const $field = $(this);

				// Skip fields that belong to nested array fields

				if ( 		$field.closest(`.${api.css_class}`, $item).length > 0
						 && ! $field.closest(`.${api.css_class}`, $item).is( api.$el ) )
				{
					return;
				}

				// ---------------------------------------------------------------------

				const field_key = $field.data('key');

				if ( field_key )
				{
					// Set the proper name and ID
					const name	= `${field_name_prefix}[${item_index}][${field_key}]`;
					const id		= api.field_name_to_id(name);

					$field.attr('name', name).attr('id', id);
				}

			});

			// -----------------------------------------------------------------------

			// Update conditional display attributes

			$item.find('[data-conditional_display]').each( function() {

				const $field = $(this);

				// Skip fields that belong to nested array fields

				if ( 		$field.closest(`.${api.css_class}`, $item).length > 0
						 && ! $field.closest(`.${api.css_class}`, $item).is( api.$el ) )
				{
					return;
				}

				// ---------------------------------------------------------------------

				const conditional = $field.attr('data-conditional_display');

				if ( conditional )
				{
					// Parse and update the conditional
					const match = conditional.match(/\{([^:]+):(.+)\}/);

					if ( match )
					{
						// Get the field name and options
						let field_name	= match[1];
						const options		= match[2];

						// Update the field name with the correct index
						const pattern				= new RegExp(`(.*\\[${key}\\])(?:\\[\\d+\\])?(.*)`);
						const name_matches	= field_name.match(pattern);

						if ( name_matches )
						{
							const name_prefix = name_matches[1];
							const name_suffix = name_matches[2];

							// Create the new name with the current index
							field_name = `${name_prefix}[${item_index}]${name_suffix}`;

							// Update the conditional attribute
							$field.attr('data-conditional_display', `{${field_name}:${options}}`);
						}
					}
				}

			});

			// -----------------------------------------------------------------------

			// Update nested array fields

			$item.find(`.${api.css_class}`).each( function() {

				const $nested = $(this);

				// Skip if this isn't a direct child of the current item

				if ( $nested.closest(`.${api.css_prefix}item`, $item).length > 1 )
				{
					return;
				}

				// ---------------------------------------------------------------------

				const nested_key = $nested.data('key');

				if ( nested_key )
				{
					// Set the correct field name prefix

					const nested_prefix = `${field_name_prefix}[${item_index}][${nested_key}]`;

					$nested.attr('data-field_name_prefix', nested_prefix);

					// -------------------------------------------------------------------

					// Update the ID prefix too

					const base_id						= api.field_name_to_id(field_name_prefix);
					const nested_id_prefix	= `${base_id}_${item_index}_${nested_key}`;

					$nested.attr('data-field_id_prefix', nested_id_prefix);

					// -------------------------------------------------------------------

					// Refresh the nested array field if initialized

					const nested_api = $nested.data('dotaim_field_array_field');

					if ( nested_api && typeof nested_api.set_fields_attributes === 'function' )
					{
						nested_api.set_fields_attributes();
					}
				}

			});

		});

	};
	// set_fields_attributes()



	/**
	 * @internal
	 */
	plugin.field_name_to_id = function( name )
	{

		return name
					.replace(/(\[\]|\])$/ig, '')	// replace last occurence of '[]' or ']'
					.replace(/(\]\[|\[)/ig, '_')	// replace '][' '[' with underscore
					.replace(/\]$/g, '')					// replace last occurence of ']'

	};
	// field_name_to_id()



	/**
	 * @internal
	 */
	plugin.change_item_header_title = function( $input )
	{

		const api	= this;
		let val		= $input.val();

		if ( ( $input.is(':checkbox') || $input.is(':radio') ) && ! $input.is(':checked') )
		{
			val = '';
		}

		// -------------------------------------------------------------------------

		const $title = $input
									.parents(`.${api.css_prefix}item`).first()
									.find(`.${api.css_prefix}item_header_title`).first();

		$title.html( val.replace(/(<([^>]+)>)/ig, '') );

	};
	// change_item_header_title()



	/**
	 * @internal
	 */
	plugin.attach_plugins = function( $item )
	{

		const api = this;

		// -------------------------------------------------------------------------

		if ( $.fn.wpColorPicker )
		{
			$('.dotaim_field_color').wpColorPicker();
		}

		// -------------------------------------------------------------------------

		if ( $.fn.dotaim_field_media )
		{
			$('.dotaim_field_media').dotaim_field_media();
		}

		// -------------------------------------------------------------------------

		if ( $.fn.dotaim_field_conditional_display )
		{
			$('[data-conditional_display]').dotaim_field_conditional_display();
		}

		// -------------------------------------------------------------------------

		if ( $.fn.dotaim_field_array_field )
		{
			$('[data-array_field]', api.$list).dotaim_field_array_field();
		}

	};
	// attach_plugins()



	/**
	 * @internal
	 */
	plugin.refresh = function( $list )
	{

		const api = this;

		$list = $list || api.$list;

		// -------------------------------------------------------------------------

		const $actions			= $list.next(`.${api.css_prefix}actions`).first();
		const $collapse_all	= $actions.children(`.${api.css_prefix}action[data-action="collapse_all"]`);
		const $delete_all		= $actions.children(`.${api.css_prefix}action[data-action="delete_all"]`);

		if ( $list.children(`.${api.css_prefix}item`).length )
		{
			$collapse_all.show();
			$delete_all.show();
		}
		else
		{
			$collapse_all.hide();
			$delete_all.hide();
		}

		// -------------------------------------------------------------------------

		api.set_fields_attributes();

		// -------------------------------------------------------------------------

		$(`.${api.css_prefix}item_settings`, $list).each( function() {

			const $settings	= $(this);
			const $input		= $( api.item_header_field_selector, $settings ).first();

			if ( $input.length )
			{
				const input_id	= $input.prop('id');
				const event			= 'change keyup paste focusout';

				$settings
				.off( event, `#${input_id}`)
				.on( event, `#${input_id}`, function( e ) {

					api.change_item_header_title( $(this) );

				});
			}

		});

		// -------------------------------------------------------------------------

		api.$el.trigger( 'refresh', api );

	};
	// refresh()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETUP - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/**
	 * add it
	 */
	app.add_plugin( plugin );



})( jQuery, window.dotaim, window.dotaim.plugin('dotaim_field_array_field') );
