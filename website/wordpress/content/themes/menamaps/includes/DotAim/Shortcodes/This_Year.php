<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class This_Year extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		return date( 'Y' );

	}
	// output()

}
// class This_Year
