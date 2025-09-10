<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Tag_Cloud extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts = [], $content = '' )
	{

		$defaults = [];

		//$atts = shortcode_atts( $defaults, $atts );
		$atts = wp_parse_args( $defaults, $atts );

		$atts['echo'] = false;

		extract( $atts );

		// -------------------------------------------------------------------------

		$out = wp_tag_cloud( $atts );

		if ( ! $out )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = "<div class=\"tag_cloud\">{$out}</div>";

		// -------------------------------------------------------------------------

		return $out;

	}
	// output()

}
// class Tag_Cloud
