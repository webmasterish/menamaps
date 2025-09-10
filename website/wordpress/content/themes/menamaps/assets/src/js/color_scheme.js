(function(){

	'use strict';

	// ---------------------------------------------------------------------------

	// Define the functionality in a global scope
	// so it can also be used in htmx events

	window.dotaim_color_scheme_toggle = {

		is_dark: function() {

			if ( document.documentElement.classList.contains('dark') )
			{
				return true;
			}

			// -----------------------------------------------------------------------

			if ( document.documentElement.classList.contains('light') )
			{
				return false;
			}

			// -----------------------------------------------------------------------

			return localStorage.getItem('color_scheme') === 'dark'
					|| ( ! ('color_scheme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

		},

		// -------------------------------------------------------------------------

		set_initial_theme: function() {

			if ( this.is_dark() )
			{
				document.documentElement.classList.add('dark');
			}
			else
			{
				document.documentElement.classList.remove('dark');
			}

		},

		// -------------------------------------------------------------------------

		set_hidden_class: function() {

			const dark_icon		= document.getElementById('color_scheme_toggle_dark_icon');
			const light_icon	= document.getElementById('color_scheme_toggle_light_icon');

			// -----------------------------------------------------------------------

			dark_icon.classList.add('hidden');
			light_icon.classList.add('hidden');

			// -----------------------------------------------------------------------

			if ( this.is_dark() )
			{
				light_icon.classList.remove('hidden');
			}
			else
			{
				dark_icon.classList.remove('hidden');
			}

		},

		// -------------------------------------------------------------------------

		initialize: function() {

			const color_scheme_toggle	= document.getElementById('color_scheme_toggle');
			const dark_icon						= document.getElementById('color_scheme_toggle_dark_icon');
			const light_icon					= document.getElementById('color_scheme_toggle_light_icon');

			if ( ! color_scheme_toggle || ! dark_icon || ! light_icon )
			{
				return;
			}

			// -----------------------------------------------------------------------

			if ( color_scheme_toggle.dataset.color_scheme_toggle_initialize )
			{
				return;
			}

			color_scheme_toggle.dataset.color_scheme_toggle_initialize = true;

			// -----------------------------------------------------------------------

			// Set the appropriate icon based on current theme

			this.set_hidden_class();

			// -----------------------------------------------------------------------

			// Add click event listener

			color_scheme_toggle.addEventListener('click', function( event ) {

				event.preventDefault();

				// ---------------------------------------------------------------------

				// set local storage

				if ( localStorage.getItem('color_scheme') )
				{
					if ( localStorage.getItem('color_scheme') === 'light' )
					{
						document.documentElement.classList.add('dark');
						localStorage.setItem('color_scheme', 'dark');
					}
					else
					{
						document.documentElement.classList.remove('dark');
						localStorage.setItem('color_scheme', 'light');
					}
				}
				else
				{
					if ( document.documentElement.classList.contains('dark') )
					{
						document.documentElement.classList.remove('dark');
						localStorage.setItem('color_scheme', 'light');
					}
					else
					{
						document.documentElement.classList.add('dark');
						localStorage.setItem('color_scheme', 'dark');
					}
				}

				// ---------------------------------------------------------------------

				// Update the icon visibility

				window.dotaim_color_scheme_toggle.set_hidden_class();

			});

		},

	};

	// ---------------------------------------------------------------------------

	// Set initial theme

  window.dotaim_color_scheme_toggle.set_initial_theme();

	// ---------------------------------------------------------------------------

	// Initialize on DOMContentLoaded

	document.addEventListener('DOMContentLoaded', function() {

		window.dotaim_color_scheme_toggle.initialize();


	});

})();
