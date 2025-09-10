(function(){

	'use strict';

	// ---------------------------------------------------------------------------

	const urldecode = str => decodeURIComponent( str.replace(/\+/g, ' ') );

	// ---------------------------------------------------------------------------

	APP.share = async function( button ) {

		//console.log( 'button:', button );

		const data = {
			url		: button.dataset.url   ? urldecode( button.dataset.url )   : document.location.href,
			title	: button.dataset.title ? urldecode( button.dataset.title ) : document.title,
		};

		if ( button.dataset.text )
		{
			data.text = urldecode( button.dataset.text );
		}
		else
		{
			const meta_description = document.querySelector('meta[name="description"]');

			if ( meta_description && meta_description.content )
			{
				data.text = meta_description.content;
			}
		}

		//console.log( 'data:', data );

		if ( navigator.share && navigator.canShare( data ) )
		{
			try {

				await navigator.share( data );

			} catch ( err ) {

				//console.error( err );

			}
		}
		else
		{
			// @todo: fallback

			//console.log('navigator.share not supported');
		}

	};
	// share()

	// ---------------------------------------------------------------------------


	document.addEventListener('DOMContentLoaded', function() {

		document.querySelectorAll('.share_button')
		.forEach( function( button ) {

			if ( navigator.share )
			{
				button.classList.remove('hidden');
			}
			else
			{
				// just in case hidden class was not added

				button.style.display = 'none';
			}

			// -----------------------------------------------------------------------

			// @notes:
			// not auto adding because the event needs to be re-added
			// if the button is loaded with htmx

			/*
			button.addEventListener('click', async ( event ) => {

				await APP.share( button );

			});
			*/

		});

	});

})();
