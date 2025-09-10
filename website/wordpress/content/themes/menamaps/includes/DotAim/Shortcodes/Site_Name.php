<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Site_Name extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		return get_bloginfo( 'name' );

	}
	// output()

}
// class Site_Name
