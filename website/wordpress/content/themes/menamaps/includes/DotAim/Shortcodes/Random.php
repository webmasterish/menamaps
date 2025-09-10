<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;
use DotAim\F;

class Random extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = ['values' => '']; // separate by comma

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		if ( ! $values )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$arr = array_filter( array_map( 'trim', explode( ',', $values ) ) );

		// @notes:
		// i could use array_unique(), but i'll skip it so that in case we want to
		// give a particular value more chance to be randomly slected

		// -------------------------------------------------------------------------

		return F::array_get_random_value( $arr );

	}
	// output()

}
// class Random
