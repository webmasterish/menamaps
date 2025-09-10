<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Site_Tagline extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		return get_bloginfo( 'description' );

	}
	// output()

}
// class Site_Tagline
