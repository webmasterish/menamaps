<?php

if ( ! $social_media = DA()->Settings()->get('footer_social_media') )
{
	return;
}

$social_media = \DotAim\F::split_string( $social_media );

if ( empty( $social_media ) )
{
	return;
}

// -----------------------------------------------------------------------------

$link_common_attr = [
	'target'	=> '_blank',
	'rel'			=> 'noopener nofollow',
	'class'		=> [
		'social_media_link',
		// @consider: optionize
		//'border border-primary-200 dark:border-none',
		//'shadow-lg dark:shadow-none',
	],
];

if ( DA()->Settings()->get('footer_social_media_add_grayscale') )
{
	$link_common_attr['class'][] = 'grayscale hover:grayscale-0 transition duration-200 ease-in-out';
}

// -----------------------------------------------------------------------------

$links = [];

foreach ( $social_media as $item )
{
	$item = \DotAim\F::split_string( $item, '/\|/' );

	if ( empty( $item[0] ) )
	{
		continue;
	}

	$url = $item[0];

	// ---------------------------------------------------------------------------

	$attr = [
		'class'				=> ['social_media_icon'],
		'aria-hidden'	=> 'true',
		'xmlns'				=> 'http://www.w3.org/2000/svg',
	];

	$parsed_url = parse_url( $url );

	if ( ! empty( $parsed_url['host'] ) )
	{
		switch ( $parsed_url['host'] )
		{
			case 'wa.me':

				$attr['class'][] = 'whatsapp';

				break;

			// -----------------------------------------------------------------------

			case 'youtu.be':

				$attr['class'][] = 'youtube';

				break;

			// -----------------------------------------------------------------------

			default:

				if ( $domain = preg_replace( '/^www\\.|\\..*/', '', $parsed_url['host'] ) )
				{
					$attr['class'][] = $domain;
				}

				break;
		}
	}

	$icon = sprintf( '<svg%s></svg>', DA()->html_attributes( $attr ) );

	// ---------------------------------------------------------------------------

	$link_attr = array_merge(
		$link_common_attr,
		[
			'href'	=> $url,
			'title'	=> ! empty( $item[1] ) ? $item[1] : null,
		]
	);

	// ---------------------------------------------------------------------------

	$links[] = sprintf( '<a%s>%s</a>', DA()->html_attributes( $link_attr ), $icon );
}

// -----------------------------------------------------------------------------

if ( ! empty( $links ) )
{
	printf( '<div id="footer_social_media">%s</div>', implode( '', $links ) );
}
