(function($){

	'use strict';

	$.fn.dotaim_meta_box_post_settings_action = function()
	{

		if ( this.length === 0 )
		{
			return;
		}

		// -------------------------------------------------------------------------

		const $el = $(this);

		// -------------------------------------------------------------------------

		$el.click(function( event ) {

			event.preventDefault();

			// -----------------------------------------------------------------------

			$el
			.prop('disabled', true)
			.addClass('button_with_loader_loading');

			// -----------------------------------------------------------------------

			const params	= {
				action				: $el.data('ajax_action'),
				nonce					: $el.data('ajax_nonce'),
				metabox_id		: $el.data('metabox_id'),
				post_id				: $('#post_ID').val(),
				button_action	: $el.data('button_action'),
			};

			$.post( ajaxurl, params )
			.done( function( response ){

				//console.log('response:', response);

				// @consider message success/error for user

				if ( response.success )
				{
					if ( 'meta_image_generate' === $el.data('button_action') )
					{
						$('#dotaim_meta_box_post_settings_meta_image_url').val( response.data );
					}
				}
				else
				{
					console.warn( response.data );
				}

			})
			.always( function(){

				$el
				.removeClass('button_with_loader_loading')
				.prop('disabled', false);

			});

		});

	};

	// ---------------------------------------------------------------------------

	$(document).ready( function() {

		$('.dotaim_meta_box_post_settings_action').each( function(){

			$(this).dotaim_meta_box_post_settings_action();

		});

	});

})(jQuery);
