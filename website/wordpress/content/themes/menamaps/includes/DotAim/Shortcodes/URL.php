<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class URL extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'page' => '',
			'path' => '',
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		// default

		$out = home_url( '/' );

		// -------------------------------------------------------------------------

		if ( ! empty( $page ) )
		{
			if ( $post = $this->core->Post()->get_by_title( $page, 'page' ) )
			{
				$out = get_permalink( $post->ID );
			}
		}
		elseif ( ! empty( $path ) )
		{
			switch ( $path )
			{
				case 'template':
				case 'stylesheet':

					$fn = "get_{$path}_directory_uri";

					$out = trailingslashit( $fn() );

					break;

				// ---------------------------------------------------------------------

				case 'assets':
				case 'css':
				case 'images':
				case 'js':
				case 'includes':

					$fn = "url_{$path}";

					$out = $this->core->{$fn}();

					break;

				// ---------------------------------------------------------------------

				default:

					$out = path_join( $out, $path );
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// output()

}
// class URL
