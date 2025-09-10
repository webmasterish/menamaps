<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Site_Name_Link extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		return sprintf(
			'<a href="%s">%s</a>',
			get_bloginfo('url'),
			get_bloginfo('name')
		);

	}
	// output()

}
// class Site_Name_Link
