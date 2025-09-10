(function($){

	'use strict';

	// ---------------------------------------------------------------------------

	$(document).ready( function() {

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
			//$('.conditional_display')
			$('[data-conditional_display]').dotaim_field_conditional_display();
		}

		// -------------------------------------------------------------------------

		if ( $.fn.dotaim_field_array_field )
		{
			$('[data-array_field]').dotaim_field_array_field();
		}

	});

})(jQuery);
