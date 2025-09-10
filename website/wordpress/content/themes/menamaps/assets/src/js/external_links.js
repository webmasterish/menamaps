document.addEventListener('DOMContentLoaded', function() {

	const anchors = document.querySelectorAll('a');

	anchors.forEach( function( link ) {

		if ( ! link.getAttribute('href') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		try {

			// @consider if i want to get by 'a[rel="external"]'

			// Create a URL object from the href attribute
			const link_url = new URL( link.getAttribute('href'), window.location.origin );

			// Check if the link is external
			const is_external = link_url.origin !== window.location.origin && /^https?:/.test( link_url.href );

			if ( is_external )
			{
				link.setAttribute('target', '_blank');
				link.setAttribute('rel', 'noopener');
			}

		} catch ( err ) {

			// ...

		}

	});

});
