<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Path extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'type'						=> 'dir',
			'path'						=> '',
			'trailingslashit'	=> false,
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		return $this->core->get_path( $type, $path, $trailingslashit );

	}
	// output()

}
// class Path
