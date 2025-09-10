(function( $, app, plugin ) {

	'use strict';

	// ---------------------------------------------------------------------------

	/**
	 * @internal
	 */
	plugin._init = function()
	{

		if ( typeof wp !== 'undefined' && _.get( wp, ['media', 'editor'] ) )
		{
			this._bind();
		}

	};
	// _init()



	/**
	 * @internal
	 */
	plugin._bind = function()
	{

		const $el		= this.$el;
		let $button	= $el.next('button');

		if ( ! $button.length )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// bind to wpbody so it will work for dynamically added elements

		const $wpbody					= $('#wpbody');
		const button_selector	= '.input_with_button > button'; // @todo: optionize or use $el parent to get it

		$wpbody.on('click', button_selector, function( e ) {

			$button = $(this);

			event.preventDefault();

			// -----------------------------------------------------------------------

			// @todo:
			// Uncaught ReferenceError: wpActiveEditor is not defined (in wp-admin/js/media-upload.js)
			// eventhough this error doesn't affect the functionality
			// but it's unoying to see it

			const send_attachment = wp.media.editor.send.attachment;

			wp.media.editor.send.attachment = function( props, attachment ) {

				let url = attachment.url;

				if ( 'image' === attachment.type )
				{
					const selected_size = attachment.sizes[ props.size ] || false;

					if ( selected_size )
					{
						url = selected_size.url;
					}
				}

				// @todo:
				//	- when there is multiple media fields
				//		the default selected media is that of the last set one
				//	- fix Uncaught ReferenceError: wpActiveEditor is not defined

				$button.prev('input[type="text"]').val( url );

				// ---------------------------------------------------------------------

				wp.media.editor.send.attachment = send_attachment;

			}

			// -----------------------------------------------------------------------

			wp.media.editor.open();

		});

	};
	// _bind()



	/**
	 * add it
	 */
	app.add_plugin( plugin );



})( jQuery, window.dotaim, window.dotaim.plugin('dotaim_field_media') );
