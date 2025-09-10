<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;
use DotAim\F;

class Email_Link extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'email'			=> '',
			'text'			=> '',
			'obfuscate'	=> true,
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		if ( ! $email || ! F::is_email( $email ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $obfuscate )
		{
			if ( ! $text )
			{
				$split	= explode( '@', $email );
				$user		= $split[0];
				$domain	= $split[1];
				$ext		= substr( $domain, strpos( $domain, '.' ) + 1 );
				$domain	= str_replace( ".{$ext}", '', $domain );
				$text		= "{$user}@{$domain}<small>.{$domain}</small>.{$ext}";
			}

			// -----------------------------------------------------------------------

			$hex = unpack( 'H*', $email );

			if ( ! empty( $hex[1]) )
			{
				$email = preg_replace( '~..~', '%$0', strtoupper( $hex[1] ) );
			}
		}
		else
		{
			if ( ! $text )
			{
				$text = $email;
			}
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<a class="email_link" href="mailto:%1$s">%2$s</a>',
			$email,
			$text
		);

	}
	// output()

}
// class Email_Link
