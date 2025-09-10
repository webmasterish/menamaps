(function($){

	'use strict';

	// ---------------------------------------------------------------------------

	$(document).ready( function() {

		const $wpbody = $('#wpbody');

		// -------------------------------------------------------------------------

		$wpbody.on('click', '.notice.dismiss .dismiss_button', function( e ) {

			e.preventDefault();

			// -----------------------------------------------------------------------

			$(this)
			.parent('.notice.dismiss')
			.fadeOut( 'fast', function(){ $(this).remove() } );

		});

		// -------------------------------------------------------------------------

		$wpbody.on('click', '.notice.collapsible .collapsible_toggle', function( e ) {

			e.preventDefault();

			// -----------------------------------------------------------------------

			const $notice = $(this).parents('.notice.collapsible').first();

			$('.notice_message', $notice).first().toggle( 'fast', function() {

				$notice.toggleClass( 'collapsed', $(this).is(':hidden') );

			});

		});

		// -------------------------------------------------------------------------

		let current_active_tab_index = 0;

		$('.nav-tab').each( function( index, el ) {

			if ( $( el ).hasClass('nav-tab-active') )
			{
				current_active_tab_index = index;

				return false;
			}

		});

		const $panel_tabs = $('.dotaim_component_panels').tabs({

			active: current_active_tab_index,

			// -----------------------------------------------------------------------

			show: 'slideDown', // { effect: 'slideDown', duration: 800 }

			// -----------------------------------------------------------------------

			beforeActivate: function( event, ui ) {

				$('.nav-tab', ui.oldTab ).removeClass('nav-tab-active');
				$('.nav-tab', ui.newTab ).addClass('nav-tab-active');

			},

			// -----------------------------------------------------------------------

			activate: ( e, ui ) => save_panels( ui.newTab ),

		});

		const $sortable_tabs = $panel_tabs.find('.ui-tabs-nav').sortable({

			axis	: 'x',
			update: ( event, ui ) => save_panels( ui.item ),

			//placeholder: 'ui-state-highlight',

		});

		const get_panels_prefs = function()
		{

			const prefs = {};

			$('.dotaim_component_panel_section')
			.filter('.collapsed')
			.each( function( index ) {

				const $section		= $(this);
				const panel_id		= $section.data('panel_id');
				const section_id	= $section.data('section_id');

				if ( ! panel_id || ! section_id )
				{
					return true;
				}

				// ---------------------------------------------------------------------

				if ( ! $.isPlainObject( prefs[ panel_id ] ) )
				{
					prefs[ panel_id ] = {
						sections: {
							collapsed	: [],
							order			: [],
						},
					};
				}

				// ---------------------------------------------------------------------

				prefs[ panel_id ]['sections']['collapsed'].push( section_id );

			});

			// -----------------------------------------------------------------------

			return prefs;

		};
		// get_panels_prefs()

		const save_panels = function( $tab )
		{

			if ( 		! _.get( window, 'ajaxurl' )
					 || ! $panel_tabs.data('options_user_prefs_nonce_action')
					 || ! $panel_tabs.data('options_user_prefs_nonce') )
			{
				return;
			}

			// -----------------------------------------------------------------------

			if ( typeof $tab === 'undefined' )
			{
				$tab = $('.ui-tabs-active');
			}

			// -----------------------------------------------------------------------

			if ( ! $tab.length )
			{
				return;
			}

			// -----------------------------------------------------------------------

			const params = {
				action: $panel_tabs.data('options_user_prefs_nonce_action'),
				nonce	: $panel_tabs.data('options_user_prefs_nonce'),
				panels: {
					current_id: $tab.data('panel_id'),
					order			: $sortable_tabs.sortable('toArray', { attribute: 'data-panel_id' }),
					prefs			: get_panels_prefs(),
				},
			};

			$.post( ajaxurl, params );

		};
		// save_panels()

		// -------------------------------------------------------------------------

		$('.dotaim_component_panel_section.collapsible .section_head')
		.css({'cursor':'pointer'})
		.on('click', function( e ) {

			e.preventDefault();

			// -----------------------------------------------------------------------

			const $el				= $(this);
			const $section	= $el.parent('.dotaim_component_panel_section');

			$el
			.next()
			.toggle( 'fast', function() {

				$section.toggleClass( 'collapsed', $(this).is(':hidden') );

				save_panels();

			});


		});

		// -------------------------------------------------------------------------

		$('.dotaim_component_form .submit .button[data-ajax]')
		.on('click', function( e ) {

			e.preventDefault();

			// -----------------------------------------------------------------------

			const $el						= $(this);
			const component_id	= $el.data('component_id');
			const $form					= $el.parents('.dotaim_component_form').first();
			const nonce					= $(`#${component_id}_nonce`, $form).val();

			// -----------------------------------------------------------------------

			$el
			.prop('disabled', true)
			.addClass('button_with_loader_loading');

			// -----------------------------------------------------------------------

			const $submit = $el.parents('.submit').first();

			$('.dotaim_component_notice', $submit).remove();

			// -----------------------------------------------------------------------

			const params = {
				nonce,
				action				: `${component_id}_action`,
				component_id	: component_id,
				panel_id			: $el.data('panel_id'),
				section_id		: $el.data('section_id'),
				form_data			: $form.serialize(),
			};

			params[ $el.attr('name') ] = true;

			$.post( ajaxurl, params )
			.done( function( response ){

				if ( $submit.length )
				{
					const notice = _.get( response, ['data', 'notice'] );

					if ( notice )
					{
						$( notice ).prependTo( $submit ).fadeIn();
					}
				}
				else
				{
					console[ response.success ? 'log' : 'warn' ]( response.data );
				}

				// ---------------------------------------------------------------------

			})
			.always( function(){

				$el
				.removeClass('button_with_loader_loading')
				.prop('disabled', false);

			});


		});

	});

})(jQuery);
