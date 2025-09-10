<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Date_Now extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'format' => get_option('date_format'),
			'locale' => '',
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		if ( $locale )
		{
			switch_to_locale( $locale );
		}

		// -------------------------------------------------------------------------

		return wp_date( $format );

	}
	// output()

}
// class Date_Now
