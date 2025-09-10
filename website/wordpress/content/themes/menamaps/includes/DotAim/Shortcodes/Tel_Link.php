<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;
use DotAim\F;

class Tel_Link extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'tel'				=> '',
			'text'			=> '',
			'obfuscate'	=> true,
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		if ( ! $tel )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $obfuscate )
		{
			if ( ! $text )
			{
				$text = $tel;
			}

			// -----------------------------------------------------------------------

			$hex = unpack( 'H*', $tel );

			if ( ! empty( $hex[1]) )
			{
				$tel = preg_replace( '~..~', '%$0', strtoupper( $hex[1] ) );
			}
		}
		else
		{
			if ( ! $text )
			{
				$text = $tel;
			}
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<a class="tel_link" href="tel:%1$s">%2$s</a>',
			$tel,
			$text
		);

	}
	// output()

}
// class Tel_Link
