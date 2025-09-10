<?php

namespace DotAim;

use DotAim\F;

class Icons
{

	/**
	 * @internal
	 */
	public static function render( $args = [] )
	{

		$defaults = [
			'id'							=> null,
			'class'						=> 'w-6 h-6',
			'fill'						=> 'none',
			'viewBox'					=> '0 0 24 24',
			'title'						=> '',
			'additional_attr'	=> null,
			'content'					=> '',
			'echo'						=> false,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! $content )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$attr = F::html_attributes([
			'id'					=> $id,
			'class'				=> $class,
			'fill'				=> $fill,
			'viewBox'			=> $viewBox,
			'aria-hidden'	=> 'true',
			'xmlns'				=> 'http://www.w3.org/2000/svg',
		]);

		if ( $additional_attr )
		{
			$attr = "{$attr} {$additional_attr}";
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<svg%s>%s%s</svg>',
			$attr,
			$title ? "<title>$title</title>" : null,
			is_array( $content ) ? implode( $content ) : $content
		);

	}
	// render()



	/**
	 * @internal
	 */
	public static function dotaim( $args = [] )
	{

		$args['viewBox'] = '0 0 32 32';
		$args['content'] = implode([
			'<path fill="#000" style="fill: var(--color1, #000)" d="M31.625 16c0 8.629-6.996 15.625-15.625 15.625s-15.625-6.996-15.625-15.625c0-8.629 6.996-15.625 15.625-15.625s15.625 6.996 15.625 15.625z"></path>',
			'<path fill="#fff" style="fill: var(--color2, #fff)" d="M22.25 16c0 3.452-2.798 6.25-6.25 6.25s-6.25-2.798-6.25-6.25c0-3.452 2.798-6.25 6.25-6.25s6.25 2.798 6.25 6.25z"></path>',
		]);

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// dotaim()



	/**
	 * @internal
	 */
	public static function bulb( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a3 3 0 0 1 3-3m-2 15h4m0-3c0-4.1 4-4.9 4-9A6 6 0 1 0 6 9c0 4 4 5 4 9h4Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bulb()



	/**
	 * @internal
	 */
	public static function bulb_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M7.05 4.05A7 7 0 0 1 19 9c0 2.407-1.197 3.874-2.186 5.084l-.04.048C15.77 15.362 15 16.34 15 18a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1c0-1.612-.77-2.613-1.78-3.875l-.045-.056C6.193 12.842 5 11.352 5 9a7 7 0 0 1 2.05-4.95ZM9 21a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2h-4a1 1 0 0 1-1-1Zm1.586-13.414A2 2 0 0 1 12 7a1 1 0 1 0 0-2 4 4 0 0 0-4 4 1 1 0 0 0 2 0 2 2 0 0 1 .586-1.414Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bulb_filled()



	/**
	 * @internal
	 */
	public static function tag( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.583 8.445h.01M10.86 19.71l-6.573-6.63a.993.993 0 0 1 0-1.4l7.329-7.394A.98.98 0 0 1 12.31 4l5.734.007A1.968 1.968 0 0 1 20 5.983v5.5a.992.992 0 0 1-.316.727l-7.44 7.5a.974.974 0 0 1-1.384.001Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// tag()



	/**
	 * @internal
	 */
	public static function folder( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 8H4m0-2v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1h-5.032a1 1 0 0 1-.768-.36l-1.9-2.28a1 1 0 0 0-.768-.36H5a1 1 0 0 0-1 1Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// folder()



	/**
	 * @internal
	 */
	public static function upvote( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11c.889-.086 1.416-.543 2.156-1.057a22.323 22.323 0 0 0 3.958-5.084 1.6 1.6 0 0 1 .582-.628 1.549 1.549 0 0 1 1.466-.087c.205.095.388.233.537.406a1.64 1.64 0 0 1 .384 1.279l-1.388 4.114M7 11H4v6.5A1.5 1.5 0 0 0 5.5 19v0A1.5 1.5 0 0 0 7 17.5V11Zm6.5-1h4.915c.286 0 .372.014.626.15.254.135.472.332.637.572a1.874 1.874 0 0 1 .215 1.673l-2.098 6.4C17.538 19.52 17.368 20 16.12 20c-2.303 0-4.79-.943-6.67-1.475"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// upvote()



	/**
	 * @internal
	 */
	public static function upvoted( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M15.03 9.684h3.965c.322 0 .64.08.925.232.286.153.532.374.717.645a2.109 2.109 0 0 1 .242 1.883l-2.36 7.201c-.288.814-.48 1.355-1.884 1.355-2.072 0-4.276-.677-6.157-1.256-.472-.145-.924-.284-1.348-.404h-.115V9.478a25.485 25.485 0 0 0 4.238-5.514 1.8 1.8 0 0 1 .901-.83 1.74 1.74 0 0 1 1.21-.048c.396.13.736.397.96.757.225.36.32.788.269 1.211l-1.562 4.63ZM4.177 10H7v8a2 2 0 1 1-4 0v-6.823C3 10.527 3.527 10 4.176 10Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// upvoted()



	/**
	 * @internal
	 */
	public static function comment( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// comment()



	/**
	 * @internal
	 */
	public static function bookmark( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 21-5-4-5 4V3.889a.92.92 0 0 1 .244-.629.808.808 0 0 1 .59-.26h8.333a.81.81 0 0 1 .589.26.92.92 0 0 1 .244.63V21Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bookmark()



	/**
	 * @internal
	 */
	public static function bookmarked( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path d="M7.833 2c-.507 0-.98.216-1.318.576A1.92 1.92 0 0 0 6 3.89V21a1 1 0 0 0 1.625.78L12 18.28l4.375 3.5A1 1 0 0 0 18 21V3.889c0-.481-.178-.954-.515-1.313A1.808 1.808 0 0 0 16.167 2H7.833Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bookmarked()



	/**
	 * @internal
	 */
	public static function flag( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 14v7M5 4.971v9.541c5.6-5.538 8.4 2.64 14-.086v-9.54C13.4 7.61 10.6-.568 5 4.97Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// flag()



	/**
	 * @internal
	 */
	public static function eye( $args = [] )
	{

		$args['content'] = [
			'<path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>',
			'<path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>',
		];

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// eye()



	/**
	 * @internal
	 */
	public static function eye_slash( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// eye_slash()



	/**
	 * @internal
	 */
	public static function share( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M7.926 10.898 15 7.727m-7.074 5.39L15 16.29M8 12a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm12 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm0-11a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// share()



	/**
	 * @internal
	 */
	public static function share_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path d="M17.5 3a3.5 3.5 0 0 0-3.456 4.06L8.143 9.704a3.5 3.5 0 1 0-.01 4.6l5.91 2.65a3.5 3.5 0 1 0 .863-1.805l-5.94-2.662a3.53 3.53 0 0 0 .002-.961l5.948-2.667A3.5 3.5 0 1 0 17.5 3Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// share_filled()



	/**
	 * @internal
	 */
	public static function plus( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 20 20';
		$args['content']	= '<path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// plus()



	/**
	 * @internal
	 */
	public static function info( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// info()



	/**
	 * @internal
	 */
	public static function info_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// info_filled()



	/**
	 * @internal
	 */
	public static function exclamation( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// exclamation()



	/**
	 * @internal
	 */
	public static function exclamation_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v5a1 1 0 1 0 2 0V8Zm-1 7a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// exclamation_filled()



	/**
	 * @internal
	 */
	public static function clock( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// clock()



	/**
	 * @internal
	 */
	public static function user( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// user()



	/**
	 * @internal
	 */
	public static function user_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// user_filled()



	/**
	 * @internal
	 */
	public static function user_add( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12h4m-2 2v-4M4 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// user_add()



	/**
	 * @internal
	 */
	public static function sign_in( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// sign_in()



	/**
	 * @internal
	 */
	public static function search( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// search()



	/**
	 * @internal
	 */
	public static function sort( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// sort()



	/**
	 * @internal
	 */
	public static function angle_up( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// angle_up()



	/**
	 * @internal
	 */
	public static function angle_down( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// angle_down()



	/**
	 * @internal
	 */
	public static function angle_left( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// angle_left()



	/**
	 * @internal
	 */
	public static function angle_right( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// angle_right()



	/**
	 * @internal
	 */
	public static function arrow_right( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// arrow_right()



	/**
	 * @internal
	 */
	public static function arrow_right_alt( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.153 19 21 12l-4.847-7H3l4.848 7L3 19h13.153Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// arrow_right_alt()



	/**
	 * @internal
	 */
	public static function arrow_up( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4 4m-4-4-4 4"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// arrow_up()



	/**
	 * @internal
	 */
	public static function arrow_down( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 14-4-4m4 4 4-4"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// arrow_down()



	/**
	 * @internal
	 */
	public static function bars( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bars()



	/**
	 * @internal
	 */
	public static function close( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// close()



	/**
	 * @internal
	 */
	public static function trash( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// trash()



	/**
	 * @internal
	 */
	public static function brain( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// brain()



	/**
	 * @internal
	 */
	public static function magic_wand( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.872 9.687 20 6.56 17.44 4 4 17.44 6.56 20 16.873 9.687Zm0 0-2.56-2.56M6 7v2m0 0v2m0-2H4m2 0h2m7 7v2m0 0v2m0-2h-2m2 0h2M8 4h.01v.01H8V4Zm2 2h.01v.01H10V6Zm2-2h.01v.01H12V4Zm8 8h.01v.01H20V12Zm-2 2h.01v.01H18V14Zm2 2h.01v.01H20V16Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// magic_wand()



	/**
	 * @internal
	 */
	public static function moon_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 20 20';
		$args['content']	= '<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// moon_filled()



	/**
	 * @internal
	 */
	public static function sun_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 20 20';
		$args['content']	= '<path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// sun_filled()



	/**
	 * @internal
	 */
	public static function check( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// check()



	/**
	 * @internal
	 */
	public static function check_circle_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 24 24';
		$args['content']	= '<path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// check_circle_filled()



	/**
	 * @internal
	 */
	public static function book_open( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// book_open()



	/**
	 * @internal
	 */
	public static function envelope( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// envelope()



	/**
	 * @internal
	 */
	public static function email( $args = [] )
	{

		return self::envelope( $args );

	}
	// email()



	/**
	 * @internal
	 */
	public static function rocket( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.051 8.102-3.778.322-1.994 1.994a.94.94 0 0 0 .533 1.6l2.698.316m8.39 1.617-.322 3.78-1.994 1.994a.94.94 0 0 1-1.595-.533l-.4-2.652m8.166-11.174a1.366 1.366 0 0 0-1.12-1.12c-1.616-.279-4.906-.623-6.38.853-1.671 1.672-5.211 8.015-6.31 10.023a.932.932 0 0 0 .162 1.111l.828.835.833.832a.932.932 0 0 0 1.111.163c2.008-1.102 8.35-4.642 10.021-6.312 1.475-1.478 1.133-4.77.855-6.385Zm-2.961 3.722a1.88 1.88 0 1 1-3.76 0 1.88 1.88 0 0 1 3.76 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// rocket()



	/**
	 * @internal
	 */
	public static function palette( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7h.01m3.486 1.513h.01m-6.978 0h.01M6.99 12H7m9 4h2.706a1.957 1.957 0 0 0 1.883-1.325A9 9 0 1 0 3.043 12.89 9.1 9.1 0 0 0 8.2 20.1a8.62 8.62 0 0 0 3.769.9 2.013 2.013 0 0 0 2.03-2v-.857A2.036 2.036 0 0 1 16 16Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// palette()



	/**
	 * @internal
	 */
	public static function cog( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z"/>
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// cog()



	/**
	 * @internal
	 */
	public static function server( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1M5 12h14M5 12a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1m-2 3h.01M14 15h.01M17 9h.01M14 9h.01"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// server()



	/**
	 * @internal
	 */
	public static function user_headset( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.079 6.839a3 3 0 0 0-4.255.1M13 20h1.083A3.916 3.916 0 0 0 18 16.083V9A6 6 0 1 0 6 9v7m7 4v-1a1 1 0 0 0-1-1h-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1Zm-7-4v-6H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h1Zm12-6h1a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v-6Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// user_headset()



	/**
	 * @internal
	 */
	public static function support( $args = [] )
	{

		return self::user_headset( $args );

	}
	// support()



	/**
	 * @internal
	 */
	public static function mobile( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 15h12M6 6h12m-6 12h.01M7 21h10a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// mobile()



	/**
	 * @internal
	 */
	public static function edit( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// edit()



	/**
	 * @internal
	 */
	public static function messages( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6l3 3v-3h2V9h-2M4 4h11v8H9l-3 3v-3H4V4Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// messages()



	/**
	 * @internal
	 */
	public static function adjustment( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 4v10m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v2m6-16v2m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v10m6-16v10m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v2"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// adjustment()



	/**
	 * @internal
	 */
	public static function setup( $args = [] )
	{

		return self::adjustment( $args );

	}
	// setup()



	/**
	 * @internal
	 */
	public static function shield_with_check( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 11.5 11 13l4-3.5M12 20a16.405 16.405 0 0 1-5.092-5.804A16.694 16.694 0 0 1 5 6.666L12 4l7 2.667a16.695 16.695 0 0 1-1.908 7.529A16.406 16.406 0 0 1 12 20Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// shield_with_check()



	/**
	 * @internal
	 */
	public static function chart_up_with_dollar( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// chart_up_with_dollar()



	/**
	 * @internal
	 */
	public static function chart_mixed( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// chart_mixed()



	/**
	 * @internal
	 */
	public static function bug( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5 9 4V3m5 2 1-1V3m-3 6v11m0-11a5 5 0 0 1 5 5m-5-5a5 5 0 0 0-5 5m5-5a4.959 4.959 0 0 1 2.973 1H15V8a3 3 0 0 0-6 0v2h.027A4.959 4.959 0 0 1 12 9Zm-5 5H5m2 0v2a5 5 0 0 0 10 0v-2m2.025 0H17m-9.975 4H6a1 1 0 0 0-1 1v2m12-3h1.025a1 1 0 0 1 1 1v2M16 11h1a1 1 0 0 0 1-1V8m-9.975 3H7a1 1 0 0 1-1-1V8"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bug()



	/**
	 * @internal
	 */
	public static function list( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// list()



	/**
	 * @internal
	 */
	public static function list_framed( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// list_framed()



	/**
	 * @internal
	 */
	public static function list_framed_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm4.996 2a1 1 0 0 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 8a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 11a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 14a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// list_framed_filled()



	/**
	 * @internal
	 */
	public static function briefcase( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 0 0-2 2v4m5-6h8M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m0 0h3a2 2 0 0 1 2 2v4m0 0v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6m18 0s-4 2-9 2-9-2-9-2m9-2h.01"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// briefcase()



	/**
	 * @internal
	 */
	public static function code( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 8-4 4 4 4m8 0 4-4-4-4m-2-3-4 14"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// code()



	/**
	 * @internal
	 */
	public static function star( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-width="2" d="M11.083 5.104c.35-.8 1.485-.8 1.834 0l1.752 4.022a1 1 0 0 0 .84.597l4.463.342c.9.069 1.255 1.2.556 1.771l-3.33 2.723a1 1 0 0 0-.337 1.016l1.03 4.119c.214.858-.71 1.552-1.474 1.106l-3.913-2.281a1 1 0 0 0-1.008 0L7.583 20.8c-.764.446-1.688-.248-1.474-1.106l1.03-4.119A1 1 0 0 0 6.8 14.56l-3.33-2.723c-.698-.571-.342-1.702.557-1.771l4.462-.342a1 1 0 0 0 .84-.597l1.753-4.022Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// star()



	/**
	 * @internal
	 */
	public static function star_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// star_filled()



	/**
	 * @internal
	 */
	public static function download( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// download()



	/**
	 * @internal
	 */
	public static function download_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd"/>
  <path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// download_filled()



	/**
	 * @internal
	 */
	public static function upload( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v9m-5 0H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2M8 9l4-5 4 5m1 8h.01"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// upload()



	/**
	 * @internal
	 */
	public static function upload_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M12 3a1 1 0 0 1 .78.375l4 5a1 1 0 1 1-1.56 1.25L13 6.85V14a1 1 0 1 1-2 0V6.85L8.78 9.626a1 1 0 1 1-1.56-1.25l4-5A1 1 0 0 1 12 3ZM9 14v-1H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-4v1a3 3 0 1 1-6 0Zm8 2a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// upload_filled()



	/**
	 * @internal
	 */
	public static function link( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// link()



	/**
	 * @internal
	 */
	public static function external_link( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.0002 5H8.2002C7.08009 5 6.51962 5 6.0918 5.21799C5.71547 5.40973 5.40973 5.71547 5.21799 6.0918C5 6.51962 5 7.08009 5 8.2002V15.8002C5 16.9203 5 17.4801 5.21799 17.9079C5.40973 18.2842 5.71547 18.5905 6.0918 18.7822C6.5192 19 7.07899 19 8.19691 19H15.8031C16.921 19 17.48 19 17.9074 18.7822C18.2837 18.5905 18.5905 18.2839 18.7822 17.9076C19 17.4802 19 16.921 19 15.8031V14M20 9V4M20 4H15M20 4L13 11"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// external_link()



	/**
	 * @internal
	 */
	public static function badge_check( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// badge_check()



	/**
	 * @internal
	 */
	public static function badge_check_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// badge_check_filled()



	/**
	 * @internal
	 */
	public static function award( $args = [] )
	{

		$args['content'] = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.171 12.906-2.153 6.411 2.672-.89 1.568 2.34 1.825-5.183m5.73-2.678 2.154 6.411-2.673-.89-1.568 2.34-1.825-5.183M9.165 4.3c.58.068 1.153-.17 1.515-.628a1.681 1.681 0 0 1 2.64 0 1.68 1.68 0 0 0 1.515.628 1.681 1.681 0 0 1 1.866 1.866c-.068.58.17 1.154.628 1.516a1.681 1.681 0 0 1 0 2.639 1.682 1.682 0 0 0-.628 1.515 1.681 1.681 0 0 1-1.866 1.866 1.681 1.681 0 0 0-1.516.628 1.681 1.681 0 0 1-2.639 0 1.681 1.681 0 0 0-1.515-.628 1.681 1.681 0 0 1-1.867-1.866 1.681 1.681 0 0 0-.627-1.515 1.681 1.681 0 0 1 0-2.64c.458-.361.696-.935.627-1.515A1.681 1.681 0 0 1 9.165 4.3ZM14 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// award()



	/**
	 * @internal
	 */
	public static function award_filled( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['content']	= '<path d="M11 9a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z"/><path fill-rule="evenodd" d="M9.896 3.051a2.681 2.681 0 0 1 4.208 0c.147.186.38.282.615.255a2.681 2.681 0 0 1 2.976 2.975.681.681 0 0 0 .254.615 2.681 2.681 0 0 1 0 4.208.682.682 0 0 0-.254.615 2.681 2.681 0 0 1-2.976 2.976.681.681 0 0 0-.615.254 2.682 2.682 0 0 1-4.208 0 .681.681 0 0 0-.614-.255 2.681 2.681 0 0 1-2.976-2.975.681.681 0 0 0-.255-.615 2.681 2.681 0 0 1 0-4.208.681.681 0 0 0 .255-.615 2.681 2.681 0 0 1 2.976-2.975.681.681 0 0 0 .614-.255ZM12 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" clip-rule="evenodd"/><path d="M5.395 15.055 4.07 19a1 1 0 0 0 1.264 1.267l1.95-.65 1.144 1.707A1 1 0 0 0 10.2 21.1l1.12-3.18a4.641 4.641 0 0 1-2.515-1.208 4.667 4.667 0 0 1-3.411-1.656Zm7.269 2.867 1.12 3.177a1 1 0 0 0 1.773.224l1.144-1.707 1.95.65A1 1 0 0 0 19.915 19l-1.32-3.93a4.667 4.667 0 0 1-3.4 1.642 4.643 4.643 0 0 1-2.53 1.21Z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// award_filled()



	/**
	 * @internal
	 */
	public static function linux( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M16.671 0c-0.207 0-0.42 0.011-0.639 0.028-5.636 0.444-4.141 6.409-4.227 8.397-0.103 1.457-0.4 2.605-1.401 4.028-1.18 1.401-2.836 3.667-3.621 6.028-0.371 1.109-0.547 2.245-0.383 3.319-0.052 0.045-0.101 0.091-0.148 0.18-0.347 0.357-0.599 0.801-0.883 1.119-0.265 0.265-0.647 0.356-1.063 0.533-0.417 0.181-0.877 0.359-1.152 0.908-0.12 0.251-0.181 0.524-0.176 0.801 0 0.265 0.036 0.535 0.073 0.715 0.077 0.532 0.155 0.972 0.052 1.293-0.331 0.907-0.372 1.528-0.14 1.98 0.232 0.445 0.713 0.625 1.252 0.801 1.081 0.267 2.547 0.18 3.7 0.799 1.235 0.623 2.488 0.895 3.488 0.627 0.701-0.153 1.293-0.617 1.611-1.26 0.783-0.004 1.64-0.359 3.013-0.445 0.932-0.077 2.099 0.356 3.437 0.265 0.033 0.18 0.084 0.265 0.152 0.445l0.004 0.004c0.521 1.037 1.484 1.509 2.512 1.428 1.028-0.080 2.123-0.715 3.009-1.741 0.841-1.020 2.244-1.445 3.171-2.004 0.464-0.265 0.839-0.625 0.865-1.137 0.031-0.533-0.265-1.083-0.952-1.836v-0.129l-0.004-0.004c-0.227-0.267-0.333-0.713-0.451-1.235-0.113-0.535-0.243-1.048-0.656-1.395h-0.004c-0.079-0.072-0.164-0.089-0.251-0.18-0.075-0.051-0.161-0.083-0.253-0.085 0.575-1.704 0.352-3.4-0.231-4.925-0.711-1.88-1.953-3.517-2.9-4.644-1.061-1.34-2.101-2.609-2.081-4.492 0.036-2.869 0.316-8.177-4.725-8.185zM17.376 4.54h0.017c0.284 0 0.528 0.083 0.779 0.264 0.255 0.18 0.44 0.443 0.585 0.711 0.14 0.345 0.211 0.612 0.221 0.965 0-0.027 0.008-0.053 0.008-0.079v0.139c-0.003-0.009-0.005-0.020-0.005-0.028l-0.005-0.032c-0.003 0.324-0.071 0.644-0.2 0.941-0.063 0.167-0.159 0.32-0.284 0.447-0.039-0.021-0.076-0.040-0.117-0.056-0.14-0.060-0.265-0.085-0.38-0.177-0.095-0.037-0.193-0.068-0.292-0.088 0.065-0.079 0.193-0.177 0.243-0.264 0.071-0.171 0.109-0.352 0.117-0.536v-0.025c0.003-0.18-0.024-0.361-0.081-0.533-0.060-0.18-0.135-0.268-0.244-0.445-0.112-0.088-0.223-0.176-0.356-0.176h-0.021c-0.124 0-0.235 0.040-0.349 0.176-0.127 0.125-0.221 0.276-0.273 0.445-0.071 0.169-0.112 0.352-0.12 0.533v0.025c0.003 0.119 0.011 0.239 0.027 0.356-0.257-0.089-0.584-0.18-0.809-0.269-0.013-0.087-0.021-0.176-0.024-0.265v-0.027c-0.011-0.352 0.057-0.701 0.2-1.025 0.109-0.292 0.309-0.541 0.573-0.711 0.228-0.172 0.505-0.265 0.792-0.265zM13.427 4.619h0.048c0.189 0 0.36 0.064 0.532 0.18 0.195 0.172 0.352 0.384 0.459 0.62 0.12 0.265 0.188 0.535 0.204 0.889v0.005c0.009 0.179 0.008 0.268-0.003 0.355v0.107c-0.040 0.009-0.075 0.024-0.111 0.032-0.203 0.073-0.365 0.18-0.524 0.267 0.016-0.119 0.017-0.239 0.004-0.356v-0.020c-0.016-0.177-0.052-0.265-0.109-0.444-0.041-0.136-0.117-0.257-0.221-0.356-0.065-0.060-0.153-0.091-0.244-0.085h-0.028c-0.095 0.008-0.173 0.055-0.248 0.176-0.085 0.103-0.14 0.228-0.16 0.36-0.036 0.144-0.047 0.292-0.031 0.441v0.019c0.016 0.18 0.049 0.268 0.108 0.445 0.060 0.179 0.129 0.267 0.22 0.357 0.015 0.012 0.028 0.024 0.045 0.032-0.093 0.076-0.156 0.093-0.235 0.181-0.049 0.037-0.107 0.080-0.175 0.091-0.139-0.167-0.261-0.347-0.367-0.536-0.128-0.28-0.195-0.584-0.207-0.889-0.023-0.301 0.015-0.603 0.107-0.891 0.073-0.263 0.203-0.505 0.377-0.713 0.171-0.177 0.347-0.267 0.557-0.267zM15.255 6.893c0.441 0 0.976 0.087 1.62 0.532 0.391 0.267 0.697 0.359 1.404 0.624h0.004c0.34 0.181 0.54 0.355 0.637 0.532v-0.175c0.097 0.196 0.104 0.424 0.021 0.627-0.164 0.415-0.688 0.859-1.419 1.124v0.003c-0.357 0.18-0.668 0.444-1.033 0.62-0.368 0.18-0.784 0.389-1.349 0.356-0.203 0.011-0.407-0.020-0.597-0.089-0.147-0.080-0.291-0.168-0.429-0.264-0.26-0.18-0.484-0.443-0.816-0.62v-0.007h-0.007c-0.533-0.328-0.821-0.683-0.915-0.948-0.092-0.357-0.007-0.625 0.257-0.8 0.299-0.18 0.507-0.361 0.644-0.448 0.139-0.099 0.191-0.136 0.235-0.175h0.003v-0.004c0.225-0.269 0.581-0.625 1.119-0.801 0.185-0.048 0.392-0.087 0.621-0.087zM18.987 9.751c0.479 1.889 1.596 4.633 2.315 5.964 0.381 0.712 1.14 2.212 1.469 4.032 0.208-0.007 0.439 0.024 0.684 0.085 0.861-2.228-0.728-4.623-1.452-5.288-0.295-0.268-0.309-0.447-0.164-0.447 0.785 0.712 1.82 2.096 2.195 3.676 0.172 0.713 0.212 1.472 0.028 2.227 0.089 0.037 0.18 0.081 0.273 0.089 1.376 0.712 1.884 1.251 1.64 2.049v-0.057c-0.081-0.004-0.16 0-0.241 0h-0.020c0.201-0.623-0.243-1.1-1.42-1.632-1.22-0.533-2.195-0.448-2.361 0.62-0.009 0.057-0.016 0.088-0.023 0.18-0.091 0.031-0.185 0.071-0.279 0.085-0.573 0.357-0.883 0.892-1.057 1.583-0.173 0.711-0.227 1.541-0.273 2.492v0.004c-0.028 0.445-0.228 1.117-0.425 1.801-2 1.429-4.773 2.051-7.132 0.445-0.148-0.257-0.328-0.499-0.536-0.711-0.099-0.167-0.224-0.316-0.367-0.445 0.243 0 0.451-0.039 0.62-0.089 0.193-0.089 0.343-0.251 0.419-0.445 0.144-0.356 0-0.929-0.46-1.551-0.46-0.623-1.241-1.327-2.384-2.028-0.84-0.532-1.315-1.16-1.533-1.861-0.22-0.712-0.191-1.447-0.020-2.193 0.327-1.427 1.164-2.813 1.699-3.684 0.143-0.087 0.049 0.18-0.544 1.299-0.528 1.001-1.521 3.329-0.163 5.139 0.051-1.319 0.344-2.62 0.863-3.835 0.752-1.704 2.324-4.672 2.448-7.024 0.064 0.048 0.289 0.18 0.385 0.269 0.291 0.177 0.507 0.444 0.787 0.62 0.281 0.268 0.636 0.447 1.168 0.447 0.052 0.004 0.1 0.008 0.148 0.008 0.548 0 0.972-0.179 1.328-0.357 0.387-0.179 0.695-0.445 0.987-0.533h0.007c0.623-0.18 1.115-0.536 1.393-0.933zM21.901 21.695c0.049 0.801 0.457 1.66 1.176 1.836 0.784 0.179 1.912-0.444 2.388-1.020l0.281-0.012c0.42-0.011 0.769 0.013 1.129 0.356l0.004 0.004c0.277 0.265 0.407 0.708 0.521 1.168 0.113 0.535 0.205 1.040 0.545 1.421 0.648 0.703 0.86 1.208 0.848 1.52l0.004-0.008v0.024l-0.004-0.016c-0.020 0.349-0.247 0.528-0.664 0.793-0.84 0.535-2.328 0.949-3.276 2.093-0.824 0.983-1.828 1.519-2.715 1.588-0.885 0.071-1.649-0.267-2.099-1.197l-0.007-0.004c-0.28-0.535-0.16-1.367 0.075-2.253 0.235-0.891 0.571-1.793 0.617-2.531 0.049-0.952 0.101-1.78 0.26-2.419 0.16-0.62 0.411-1.063 0.855-1.312l0.060-0.029zM7.481 21.76h0.013c0.071 0 0.14 0.007 0.209 0.019 0.501 0.073 0.941 0.444 1.364 1.003l1.213 2.219 0.004 0.004c0.324 0.711 1.005 1.419 1.585 2.184 0.579 0.797 1.027 1.508 0.972 2.093v0.008c-0.076 0.992-0.639 1.531-1.5 1.725-0.86 0.18-2.027 0.003-3.193-0.619-1.291-0.715-2.824-0.625-3.809-0.803-0.492-0.088-0.815-0.268-0.964-0.535-0.148-0.265-0.151-0.801 0.164-1.64v-0.004l0.003-0.004c0.156-0.445 0.040-1.004-0.036-1.492-0.073-0.535-0.111-0.945 0.057-1.253 0.213-0.445 0.528-0.532 0.919-0.711 0.393-0.18 0.855-0.269 1.221-0.625h0.003v-0.004c0.341-0.357 0.593-0.801 0.891-1.117 0.253-0.268 0.507-0.448 0.884-0.448zM17.027 9.661c-0.58 0.268-1.26 0.713-1.984 0.713-0.723 0-1.293-0.356-1.705-0.621-0.207-0.179-0.373-0.357-0.499-0.447-0.219-0.179-0.192-0.445-0.099-0.445 0.145 0.021 0.172 0.18 0.265 0.268 0.128 0.088 0.287 0.265 0.481 0.444 0.388 0.267 0.907 0.623 1.555 0.623 0.647 0 1.404-0.356 1.864-0.621 0.26-0.18 0.593-0.445 0.864-0.623 0.208-0.183 0.199-0.357 0.372-0.357 0.172 0.021 0.045 0.179-0.196 0.444-0.241 0.18-0.615 0.447-0.92 0.624v-0.001zM15.584 7.549v-0.029c-0.008-0.025 0.017-0.056 0.039-0.067 0.099-0.057 0.24-0.036 0.347 0.005 0.084 0 0.213 0.089 0.2 0.18-0.008 0.065-0.113 0.088-0.18 0.088-0.073 0-0.123-0.057-0.188-0.091-0.069-0.024-0.195-0.011-0.217-0.087zM14.849 7.549c-0.027 0.077-0.151 0.065-0.221 0.088-0.063 0.033-0.115 0.091-0.185 0.091-0.068 0-0.175-0.025-0.183-0.091-0.012-0.088 0.117-0.177 0.2-0.177 0.108-0.041 0.245-0.063 0.345-0.007 0.025 0.012 0.048 0.040 0.040 0.067v0.028h0.004z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// linux()



	/**
	 * @internal
	 */
	public static function bash( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path fill="none" d="M4.24 4.24h119.53v119.53H4.24z"/><path d="M109.01 28.64L71.28 6.24c-2.25-1.33-4.77-2-7.28-2s-5.03.67-7.28 2.01l-37.74 22.4c-4.5 2.67-7.28 7.61-7.28 12.96v44.8c0 5.35 2.77 10.29 7.28 12.96l37.73 22.4c2.25 1.34 4.76 2 7.28 2 2.51 0 5.03-.67 7.28-2l37.74-22.4c4.5-2.67 7.28-7.62 7.28-12.96V41.6c0-5.34-2.77-10.29-7.28-12.96zM79.79 98.59l.06 3.22c0 .39-.25.83-.55.99l-1.91 1.1c-.3.15-.56-.03-.56-.42l-.03-3.17c-1.63.68-3.29.84-4.34.42-.2-.08-.29-.37-.21-.71l.69-2.91c.06-.23.18-.46.34-.6.06-.06.12-.1.18-.13.11-.06.22-.07.31-.03 1.14.38 2.59.2 3.99-.5 1.78-.9 2.97-2.72 2.95-4.52-.02-1.64-.9-2.31-3.05-2.33-2.74.01-5.3-.53-5.34-4.57-.03-3.32 1.69-6.78 4.43-8.96l-.03-3.25c0-.4.24-.84.55-1l1.85-1.18c.3-.15.56.04.56.43l.03 3.25c1.36-.54 2.54-.69 3.61-.44.23.06.34.38.24.75l-.72 2.88c-.06.22-.18.44-.33.58a.77.77 0 01-.19.14c-.1.05-.19.06-.28.05-.49-.11-1.65-.36-3.48.56-1.92.97-2.59 2.64-2.58 3.88.02 1.48.77 1.93 3.39 1.97 3.49.06 4.99 1.58 5.03 5.09.05 3.44-1.79 7.15-4.61 9.41zm26.34-60.5l-35.7 22.05c-4.45 2.6-7.73 5.52-7.74 10.89v43.99c0 3.21 1.3 5.29 3.29 5.9-.65.11-1.32.19-1.98.19-2.09 0-4.15-.57-5.96-1.64l-37.73-22.4c-3.69-2.19-5.98-6.28-5.98-10.67V41.6c0-4.39 2.29-8.48 5.98-10.67l37.74-22.4c1.81-1.07 3.87-1.64 5.96-1.64s4.15.57 5.96 1.64l37.74 22.4c3.11 1.85 5.21 5.04 5.8 8.63-1.27-2.67-4.09-3.39-7.38-1.47z"/><path fill="#4FA847" d="M99.12 90.73l-9.4 5.62c-.25.15-.43.31-.43.61v2.46c0 .3.2.43.45.28l9.54-5.8c.25-.15.29-.42.29-.72v-2.17c0-.3-.2-.42-.45-.28z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// bash()



	/**
	 * @internal
	 */
	public static function git( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M31.395 14.573l-13.972-13.971c-0.805-0.804-2.109-0.804-2.917 0l-2.895 2.9 3.68 3.68c0.86-0.287 1.839-0.093 2.519 0.588 0.688 0.687 0.877 1.677 0.584 2.533l3.544 3.547c0.86-0.297 1.849-0.104 2.533 0.58 0.961 0.96 0.961 2.512 0 3.472-0.959 0.959-2.508 0.959-3.467 0-0.719-0.721-0.899-1.783-0.539-2.661l-3.319-3.301v8.7c0.235 0.115 0.456 0.271 0.651 0.464 0.951 0.961 0.951 2.511 0 3.467-0.959 0.961-2.519 0.961-3.479 0-0.959-0.959-0.959-2.505 0-3.464 0.243-0.24 0.516-0.421 0.807-0.541v-8.785c-0.289-0.121-0.565-0.296-0.8-0.535-0.727-0.727-0.901-1.789-0.528-2.679l-3.616-3.633-9.581 9.575c-0.8 0.807-0.8 2.112 0 2.919l13.973 13.969c0.805 0.805 2.109 0.805 2.915 0l13.907-13.907c0.807-0.804 0.807-2.109 0-2.916z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// git()



	/**
	 * @internal
	 */
	public static function docker( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M6.427 23.033c-0.912 0-1.739-0.747-1.739-1.653s0.747-1.657 1.74-1.657c0.997 0 1.747 0.747 1.747 1.656s-0.829 1.653-1.74 1.653zM27.776 14.016c-0.18-1.323-1-2.4-2.080-3.227l-0.42-0.333-0.339 0.413c-0.659 0.747-0.92 2.071-0.84 3.060 0.080 0.749 0.32 1.493 0.739 2.072-0.339 0.173-0.757 0.333-1.080 0.503-0.76 0.249-1.499 0.333-2.24 0.333h-21.387l-0.080 0.493c-0.16 1.576 0.080 3.227 0.749 4.72l0.325 0.58v0.080c2 3.311 5.56 4.8 9.437 4.8 7.459 0 13.576-3.227 16.476-10.177 1.9 0.083 3.819-0.413 4.72-2.235l0.24-0.413-0.4-0.249c-1.080-0.659-2.56-0.747-3.8-0.413l-0.024 0.003zM17.099 12.693h-3.237v3.227h3.24v-3.229l-0.003 0.004zM17.099 8.636h-3.237v3.227h3.24v-3.223l-0.003-0.004zM17.099 4.497h-3.237v3.227h3.24v-3.227h-0.003zM21.059 12.693h-3.219v3.227h3.227v-3.229l-0.009 0.004zM9.061 12.693h-3.217v3.227h3.229v-3.229l-0.013 0.004zM13.101 12.693h-3.2v3.227h3.219v-3.229l-0.020 0.004zM5.061 12.693h-3.195v3.227h3.237v-3.229l-0.040 0.004zM13.101 8.636h-3.2v3.227h3.219v-3.223l-0.020-0.004zM9.041 8.636h-3.192v3.227h3.217v-3.223l-0.021-0.004z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// docker()



	/**
	 * @internal
	 */
	public static function mysql( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= implode([
			'<path d="M21.873 7.568c-0.153 0-0.257 0.019-0.365 0.044v0.017h0.019c0.072 0.139 0.195 0.24 0.285 0.364 0.072 0.143 0.133 0.285 0.205 0.427l0.019-0.020c0.125-0.088 0.187-0.229 0.187-0.444-0.053-0.063-0.061-0.125-0.107-0.187-0.053-0.089-0.168-0.133-0.24-0.204zM7.693 25.16h-1.236c-0.040-2.083-0.164-4.040-0.36-5.88h-0.011l-1.88 5.88h-0.94l-1.867-5.88h-0.013c-0.137 1.764-0.224 3.724-0.26 5.88h-1.127c0.073-2.621 0.256-5.080 0.547-7.373h1.533l1.78 5.419h0.011l1.796-5.419h1.46c0.323 2.687 0.512 5.147 0.571 7.373zM13.049 19.72c-0.504 2.727-1.168 4.711-1.989 5.947-0.643 0.955-1.347 1.431-2.111 1.431-0.204 0-0.453-0.061-0.755-0.184v-0.659c0.147 0.023 0.32 0.035 0.515 0.035 0.357 0 0.644-0.1 0.863-0.296 0.263-0.24 0.393-0.509 0.393-0.807 0-0.207-0.103-0.627-0.307-1.259l-1.352-4.208h1.213l0.969 3.147c0.219 0.715 0.311 1.213 0.273 1.497 0.533-1.419 0.904-2.969 1.113-4.644h1.173zM29.483 25.16h-3.507v-7.373h1.18v6.467h2.327zM25.056 25.34l-1.355-0.667c0.12-0.101 0.236-0.211 0.34-0.333 0.577-0.675 0.864-1.677 0.864-3.004 0-2.44-0.957-3.661-2.873-3.661-0.939 0-1.672 0.309-2.2 0.929-0.573 0.677-0.861 1.675-0.861 2.993 0 1.296 0.253 2.248 0.765 2.853 0.467 0.547 1.169 0.82 2.111 0.82 0.352 0 0.675-0.044 0.967-0.131l1.767 1.029 0.48-0.829zM20.667 23.684c-0.3-0.48-0.449-1.253-0.449-2.315 0-1.857 0.565-2.787 1.693-2.787 0.591 0 1.027 0.223 1.303 0.667 0.299 0.483 0.448 1.248 0.448 2.297 0 1.872-0.565 2.811-1.693 2.811-0.593 0-1.027-0.223-1.304-0.667zM18.456 23.117c0 0.627-0.229 1.141-0.688 1.541s-1.071 0.6-1.845 0.6c-0.724 0-1.419-0.229-2.097-0.687l0.316-0.635c0.584 0.293 1.111 0.437 1.587 0.437 0.443 0 0.791-0.097 1.044-0.293 0.251-0.196 0.4-0.472 0.4-0.82 0-0.44-0.307-0.813-0.864-1.127-0.517-0.284-1.551-0.876-1.551-0.876-0.563-0.409-0.843-0.848-0.843-1.569 0-0.6 0.209-1.080 0.627-1.447 0.42-0.371 0.96-0.553 1.627-0.553 0.683 0 1.307 0.181 1.867 0.547l-0.284 0.635c-0.48-0.203-0.953-0.307-1.419-0.307-0.377 0-0.669 0.091-0.872 0.275-0.204 0.181-0.331 0.413-0.331 0.699 0 0.437 0.312 0.813 0.888 1.133 0.524 0.287 1.583 0.893 1.583 0.893 0.577 0.407 0.864 0.84 0.864 1.557z"></path>',
			'<path d="M30.965 15.315c-0.713-0.019-1.267 0.053-1.729 0.251-0.133 0.053-0.347 0.053-0.365 0.223 0.073 0.071 0.084 0.187 0.147 0.285 0.107 0.179 0.291 0.417 0.461 0.543 0.187 0.147 0.373 0.288 0.569 0.413 0.347 0.213 0.74 0.34 1.080 0.555 0.193 0.125 0.391 0.284 0.587 0.417 0.097 0.067 0.16 0.187 0.285 0.229v-0.027c-0.061-0.080-0.080-0.196-0.14-0.285-0.089-0.089-0.179-0.169-0.267-0.257-0.259-0.347-0.58-0.649-0.927-0.9-0.285-0.195-0.909-0.467-1.027-0.793l-0.017-0.019c0.195-0.017 0.427-0.088 0.613-0.141 0.303-0.080 0.58-0.063 0.893-0.141 0.141-0.036 0.284-0.080 0.427-0.125v-0.080c-0.16-0.16-0.28-0.377-0.445-0.527-0.453-0.393-0.956-0.776-1.472-1.097-0.28-0.179-0.635-0.293-0.929-0.445-0.107-0.053-0.285-0.080-0.347-0.169-0.16-0.195-0.253-0.453-0.367-0.685-0.256-0.491-0.507-1.033-0.729-1.551-0.16-0.349-0.257-0.697-0.453-1.017-0.92-1.516-1.916-2.435-3.448-3.333-0.329-0.187-0.724-0.267-1.141-0.365-0.223-0.011-0.445-0.027-0.667-0.036-0.147-0.063-0.288-0.232-0.413-0.313-0.507-0.32-1.819-1.013-2.192-0.096-0.24 0.579 0.356 1.149 0.563 1.443 0.153 0.204 0.347 0.437 0.453 0.667 0.063 0.155 0.080 0.313 0.143 0.475 0.141 0.392 0.276 0.829 0.463 1.196 0.097 0.187 0.204 0.383 0.329 0.551 0.072 0.097 0.195 0.143 0.223 0.303-0.125 0.181-0.133 0.445-0.205 0.667-0.32 1.009-0.195 2.257 0.259 3 0.143 0.221 0.483 0.712 0.937 0.524 0.4-0.16 0.312-0.667 0.427-1.113 0.027-0.107 0.009-0.177 0.064-0.249v0.020c0.125 0.251 0.251 0.489 0.365 0.74 0.275 0.437 0.755 0.891 1.156 1.193 0.213 0.16 0.383 0.437 0.649 0.536v-0.027h-0.020c-0.057-0.077-0.133-0.115-0.205-0.177-0.16-0.16-0.34-0.356-0.467-0.533-0.373-0.503-0.703-1.053-0.996-1.624-0.147-0.28-0.269-0.581-0.387-0.857-0.053-0.107-0.053-0.267-0.143-0.32-0.133 0.195-0.329 0.364-0.427 0.604-0.169 0.384-0.187 0.856-0.251 1.347-0.036 0.009-0.019 0-0.036 0.019-0.285-0.069-0.383-0.365-0.489-0.613-0.267-0.633-0.311-1.651-0.080-2.38 0.063-0.187 0.329-0.776 0.223-0.955-0.056-0.169-0.232-0.267-0.329-0.404-0.116-0.165-0.24-0.38-0.32-0.569-0.213-0.499-0.32-1.051-0.552-1.549-0.107-0.231-0.293-0.472-0.445-0.684-0.169-0.24-0.356-0.409-0.491-0.693-0.044-0.097-0.107-0.259-0.036-0.365 0.019-0.072 0.056-0.1 0.125-0.12 0.117-0.096 0.447 0.029 0.563 0.083 0.329 0.133 0.607 0.259 0.883 0.445 0.125 0.088 0.26 0.257 0.42 0.301h0.187c0.285 0.063 0.607 0.019 0.873 0.097 0.473 0.152 0.9 0.373 1.283 0.613 1.168 0.741 2.128 1.793 2.78 3.048 0.107 0.205 0.153 0.393 0.251 0.607 0.187 0.44 0.417 0.884 0.607 1.309 0.187 0.42 0.367 0.848 0.635 1.196 0.133 0.187 0.669 0.284 0.909 0.381 0.177 0.080 0.453 0.153 0.613 0.251 0.307 0.187 0.605 0.4 0.893 0.605 0.147 0.101 0.591 0.324 0.617 0.504z"></path>',
		]);

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// mysql()



	/**
	 * @internal
	 */
	public static function php( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= implode([
			'<path d="M9.347 13.609h-1.259l-0.687 3.531h1.117c0.741 0 1.293-0.14 1.656-0.419 0.363-0.28 0.607-0.745 0.733-1.399 0.123-0.627 0.067-1.069-0.165-1.327-0.233-0.257-0.697-0.387-1.396-0.387z"></path>',
			'<path d="M16 7.584c-8.836 0-16 3.768-16 8.416s7.164 8.417 16 8.417 16-3.769 16-8.417c0-4.648-7.164-8.416-16-8.416zM11.653 17.519c-0.348 0.333-0.767 0.584-1.223 0.735-0.448 0.144-1.020 0.219-1.713 0.219h-1.575l-0.436 2.241h-1.837l1.64-8.435h3.533c1.063 0 1.837 0.279 2.325 0.837 0.488 0.557 0.635 1.336 0.44 2.336-0.082 0.425-0.223 0.803-0.416 1.148l0.010-0.019c-0.191 0.34-0.44 0.653-0.748 0.937zM17.019 18.472l0.724-3.732c0.084-0.424 0.052-0.715-0.091-0.868-0.143-0.155-0.448-0.232-0.916-0.232h-1.456l-0.939 4.833h-1.824l1.64-8.436h1.823l-0.436 2.243h1.624c1.023 0 1.727 0.179 2.115 0.535s0.504 0.933 0.351 1.732l-0.763 3.925h-1.852zM27.148 15.452c-0.080 0.425-0.221 0.803-0.416 1.148l0.010-0.018c-0.191 0.34-0.44 0.653-0.748 0.937-0.338 0.323-0.746 0.574-1.2 0.728l-0.023 0.007c-0.448 0.144-1.020 0.219-1.715 0.219h-1.573l-0.436 2.243h-1.837l1.64-8.435h3.532c1.063 0 1.837 0.279 2.325 0.837 0.488 0.556 0.636 1.335 0.441 2.335z"></path>',
			'<path d="M23.688 13.609h-1.257l-0.688 3.531h1.117c0.743 0 1.295-0.14 1.656-0.419 0.363-0.28 0.607-0.745 0.735-1.399 0.123-0.627 0.065-1.069-0.167-1.327s-0.699-0.387-1.396-0.387z"></path>',
		]);

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// php()



	/**
	 * @internal
	 */
	public static function python( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M19.080 0.24l1.2 0.267 0.973 0.347 0.787 0.4 0.6 0.427 0.453 0.453 0.333 0.453 0.213 0.44 0.133 0.4 0.053 0.347 0.027 0.267-0.013 0.173v7.12l-0.067 0.84-0.173 0.733-0.28 0.613-0.347 0.507-0.4 0.413-0.44 0.333-0.467 0.253-0.467 0.187-0.44 0.133-0.4 0.093-0.347 0.053-0.28 0.027h-7.96l-0.92 0.067-0.787 0.187-0.667 0.293-0.547 0.36-0.44 0.427-0.36 0.467-0.267 0.48-0.2 0.493-0.133 0.467-0.093 0.427-0.053 0.36-0.027 0.28v4.080h-2.973l-0.28-0.040-0.373-0.093-0.427-0.16-0.467-0.24-0.48-0.347-0.48-0.48-0.467-0.613-0.427-0.787-0.373-0.973-0.28-1.173-0.187-1.4-0.067-1.64 0.080-1.627 0.213-1.387 0.32-1.16 0.427-0.947 0.48-0.76 0.533-0.587 0.56-0.44 0.56-0.32 0.533-0.213 0.48-0.133 0.427-0.067 0.32-0.013h0.213l0.080 0.013h10.88v-1.107h-7.787l-0.013-3.667-0.027-0.493 0.067-0.453 0.147-0.413 0.227-0.373 0.333-0.347 0.413-0.307 0.507-0.267 0.587-0.24 0.68-0.2 0.773-0.16 0.853-0.133 0.947-0.080 1.027-0.053 1.12-0.027 1.693 0.067 1.427 0.173zM10.68 2.88l-0.307 0.44-0.107 0.547 0.107 0.547 0.307 0.453 0.44 0.293 0.547 0.12 0.547-0.12 0.44-0.293 0.307-0.453 0.107-0.547-0.107-0.547-0.307-0.44-0.44-0.293-0.547-0.12-0.547 0.12-0.44 0.293z"></path>
<path d="M28.133 8.147l0.373 0.080 0.427 0.16 0.467 0.24 0.48 0.36 0.48 0.467 0.467 0.627 0.427 0.787 0.373 0.973 0.28 1.173 0.187 1.387 0.067 1.64-0.080 1.64-0.213 1.387-0.32 1.147-0.427 0.947-0.48 0.76-0.533 0.6-0.56 0.44-0.56 0.32-0.533 0.213-0.48 0.12-0.427 0.067-0.32 0.027-0.213-0.013h-10.96v1.093h7.787l0.013 3.68 0.027 0.48-0.067 0.453-0.147 0.413-0.227 0.387-0.333 0.333-0.413 0.32-0.507 0.267-0.587 0.227-0.68 0.2-0.773 0.173-0.853 0.12-0.947 0.093-1.027 0.053-1.12 0.013-1.693-0.053-1.427-0.187-1.2-0.267-0.973-0.333-0.787-0.4-0.6-0.44-0.453-0.453-0.333-0.453-0.213-0.44-0.133-0.4-0.053-0.333-0.027-0.267 0.013-0.173v-7.12l0.067-0.853 0.173-0.72 0.28-0.613 0.347-0.507 0.4-0.427 0.44-0.32 0.467-0.267 0.467-0.187 0.44-0.133 0.4-0.080 0.347-0.053 0.28-0.027 0.173-0.013h7.787l0.92-0.067 0.787-0.187 0.667-0.28 0.547-0.373 0.44-0.427 0.36-0.467 0.267-0.48 0.2-0.48 0.133-0.467 0.093-0.427 0.053-0.373 0.027-0.28v-4.080h2.787l0.187 0.013 0.28 0.040zM19.507 27.147l-0.307 0.44-0.107 0.547 0.107 0.547 0.307 0.44 0.44 0.307 0.547 0.107 0.547-0.107 0.44-0.307 0.307-0.44 0.107-0.547-0.107-0.547-0.307-0.44-0.44-0.307-0.547-0.107-0.547 0.107-0.44 0.307z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// python()



	/**
	 * @internal
	 */
	public static function wordpress( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M28.625 9.1c1.12 2.049 1.757 4.4 1.757 6.9 0 5.305-2.875 9.941-7.151 12.433l4.393-12.703c0.82-2.053 1.093-3.695 1.093-5.152 0-0.54-0.035-1.040-0.093-1.48zM17.984 9.239c0.863-0.040 1.643-0.14 1.643-0.14 0.776-0.1 0.685-1.24-0.089-1.199 0 0-2.34 0.18-3.84 0.18-1.419 0-3.8-0.2-3.8-0.2-0.78-0.040-0.881 1.14-0.1 1.18 0 0 0.72 0.081 1.5 0.12l2.24 6.14-3.16 9.44-5.239-15.56c0.865-0.040 1.645-0.133 1.645-0.133 0.78-0.1 0.688-1.24-0.087-1.195 0 0-2.328 0.184-3.832 0.184-0.267 0-0.584-0.011-0.92-0.020 2.603-3.836 7.035-6.416 12.055-6.416 3.745 0 7.153 1.429 9.715 3.777-0.061-0.004-0.121-0.012-0.188-0.012-1.413 0-2.416 1.231-2.416 2.552 0 1.187 0.684 2.191 1.413 3.375 0.548 0.96 1.187 2.191 1.187 3.969 0 1.22-0.472 2.659-1.095 4.639l-1.433 4.78-5.2-15.48 0.001 0.019zM16 30.379c-1.412 0-2.775-0.204-4.064-0.583l4.316-12.541 4.42 12.116c0.032 0.071 0.067 0.135 0.104 0.199-1.493 0.524-3.1 0.812-4.776 0.812zM1.615 16c0-2.085 0.448-4.067 1.247-5.853l6.859 18.799c-4.795-2.332-8.104-7.251-8.105-12.945zM16 0c-8.82 0-16 7.18-16 16s7.18 16 16 16 16-7.18 16-16-7.18-16-16-16z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// wordpress()



	/**
	 * @internal
	 */
	public static function wordpress_original( $args = [] )
	{

		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path fill-rule="evenodd" clip-rule="evenodd" fill="#337BA2" d="M43.257 121.233c.079 1.018.029 2.071.299 3.037.115.408.9.629 1.381.935l.625.401c-.235.137-.469.389-.707.392a165.82 165.82 0 01-5.598.002c-.248-.004-.491-.237-.735-.364.198-.143.388-.391.597-.408 1.251-.105 1.632-.865 1.626-1.989-.011-2.066-.006-4.134.003-6.202.005-1.152-.322-1.993-1.679-2.045-.188-.008-.366-.296-.548-.453.182-.111.366-.321.546-.318 2.39.029 4.79-.024 7.167.177 1.873.159 3.107 1.455 3.234 2.949.138 1.639-.703 2.764-2.605 3.486l-.729.272c1.225 1.158 2.31 2.29 3.516 3.272.535.437 1.293.697 1.989.817 1.393.238 2.149-.361 2.187-1.742.061-2.229.032-4.461.011-6.691-.01-1.022-.449-1.697-1.589-1.753-.215-.01-.42-.253-.629-.388.239-.14.478-.4.715-.399 2.432.02 4.875-.055 7.291.161 4.123.366 6.42 3.797 5.214 7.588-.735 2.312-2.495 3.619-4.759 3.773-3.958.27-7.938.215-11.909.243-.316.002-.706-.341-.944-.623-.914-1.085-1.776-2.213-2.668-3.316-.27-.334-.571-.641-.858-.961l-.444.147zm13.119-5.869c0 2.785-.034 5.484.036 8.18.011.414.41 1.039.78 1.187 1.457.581 3.812-.368 4.47-1.842.881-1.973.988-4.05-.203-5.922-1.175-1.847-3.132-1.663-5.083-1.603zm-13.021 4.561c1.262.032 2.653.313 3.192-1.073.302-.777.234-1.982-.183-2.69-.633-1.076-1.906-.888-3.01-.752l.001 4.515z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#515151" d="M96.77 119.35c.834-.18 1.661-.154 2.198-.537.451-.32.563-1.116.908-1.886.199.357.386.539.39.724.025 1.38.03 2.761 0 4.141-.005.216-.226.427-.347.641-.136-.114-.339-.2-.399-.346-.733-1.771-.729-1.772-2.843-1.583.309 1.382-.763 3.149.89 4.058.843.463 2.203.371 3.189.068.841-.256 1.48-1.171 2.212-1.798v3.096c-.405.036-.712.086-1.021.086-4.141.006-8.282-.012-12.422.019-.714.006-1.197-.174-1.633-.773-.857-1.182-1.799-2.302-2.725-3.432-.232-.283-.534-.508-1.021-.962 0 1.154-.042 1.981.012 2.802.056.858.469 1.427 1.418 1.534.279.032.529.325.792.5-.271.105-.54.298-.812.303-1.827.026-3.653.025-5.48.001-.28-.004-.558-.173-.866-.275l.156-.303c2.244-.906 2.25-.906 2.248-3.508a343.88 343.88 0 00-.039-4.87c-.017-1.121-.321-2.01-1.689-2.058-.197-.007-.384-.287-.577-.441.226-.113.453-.325.678-.323 2.311.022 4.635-.054 6.93.16 2.512.234 4.065 2.329 3.132 4.257-.51 1.053-1.688 1.783-2.725 2.818.984.9 2.117 2.194 3.491 3.135 1.941 1.33 3.268.571 3.317-1.748.041-1.947-.007-3.896-.015-5.845-.004-1.155-.361-1.994-1.717-2.013-.185-.003-.367-.2-.586-.33.705-.52 7.499-.709 10.448-.332l.19 3.214-.333.136c-.686-.717-.601-2.199-2.02-2.204-1.084-.005-2.168-.119-3.332-.189.003 1.356.003 2.59.003 4.063zm-12.647.566c2.61.105 3.646-.603 3.584-2.364-.061-1.698-1.195-2.383-3.584-2.121v4.485z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#3179A1" d="M11.555 120.682c.996-2.947 1.979-5.897 3.003-8.834.141-.404.486-.737.737-1.104.248.378.587.725.729 1.14.931 2.719 1.817 5.451 2.722 8.179.072.219.165.43.375.969.928-2.813 1.787-5.308 2.564-7.829.27-.873-.081-1.504-1.097-1.618-.335-.039-.66-.17-1.051-.274.676-.749 5.957-.804 6.827-.108-.236.112-.424.271-.618.279-1.65.064-2.414 1.097-2.884 2.521-1.258 3.81-2.54 7.611-3.817 11.415-.133.395-.3.778-.452 1.166l-.421.03-3.579-10.821-3.619 10.788-.354.022c-.185-.401-.412-.79-.547-1.207-1.167-3.581-2.319-7.167-3.474-10.751-.495-1.539-.99-3.069-3.012-3.167-.132-.006-.253-.229-.38-.35.158-.13.316-.373.476-.375 2.272-.024 4.546-.024 6.818.001.158.001.313.247.47.379-.169.126-.319.309-.508.367-1.82.55-1.951.761-1.378 2.526.723 2.233 1.468 4.457 2.204 6.686l.266-.03z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#4D4D4D" d="M65.484 111.25c.279-.241.435-.494.587-.491 2.957.044 5.938-.093 8.864.247 2.768.321 4.301 2.919 3.466 5.359-.748 2.189-2.593 2.874-4.68 3.064-.881.081-1.776.013-2.824.013.093 1.453.14 2.78.275 4.098.113 1.114.863 1.56 1.923 1.65.239.021.457.288.684.442-.25.126-.498.36-.75.363-2.191.029-4.384.028-6.575.002-.263-.003-.523-.219-.784-.336.218-.165.432-.463.656-.472 1.463-.056 2.012-.964 2.03-2.235.044-3.081.04-6.162.002-9.243-.016-1.31-.649-2.148-2.072-2.206-.212-.008-.422-.13-.802-.255zm5.523 6.706c2.682.278 3.703.022 4.349-1.167.648-1.192.65-2.439-.116-3.566-1.059-1.559-2.679-1.098-4.233-1.154v5.887z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#3279A1" d="M31.076 126.463c-2.396-.104-4.348-.856-5.794-2.647-2.053-2.542-1.741-5.994.711-8.192 2.645-2.37 7.018-2.472 9.733-.171 1.838 1.559 2.709 3.533 2.111 5.953-.675 2.73-2.601 4.192-5.218 4.856-.546.137-1.122.149-1.543.201zm4.544-6.249l-.224-.147c-.149-.709-.236-1.439-.458-2.125-.642-1.971-1.986-2.945-3.963-2.949-1.97-.004-3.295.975-3.939 2.967-.572 1.771-.498 3.526.383 5.18 1.315 2.468 4.829 2.931 6.549.736.802-1.023 1.116-2.43 1.652-3.662z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#505050" d="M122.748 114.317l.893-.782v4.376l-.259.195c-.209-.295-.498-.562-.615-.891-.591-1.655-1.865-2.553-3.319-2.117-.499.149-1.099.649-1.232 1.11-.109.376.285 1.12.671 1.374 1.008.664 2.131 1.156 3.214 1.703 2.356 1.192 3.198 2.845 2.401 4.736-.809 1.921-3.263 2.915-5.462 2.173-.606-.206-1.167-.544-1.728-.811l-.857 1.126-.379-.116c0-1.477-.009-2.954.015-4.431.002-.143.215-.282.33-.423.18.218.448.41.527.66.523 1.656 1.53 2.756 3.325 2.94 1.023.105 2.023-.021 2.378-1.187.324-1.067-.42-1.669-1.219-2.124-.879-.5-1.808-.909-2.708-1.37-.395-.203-.798-.404-1.153-.665-1.257-.927-1.753-2.263-1.381-3.618.332-1.211 1.523-2.237 2.997-2.28 1.091-.031 2.195.25 3.561.422zm-16.269 11.027c-.166.33-.258.607-.429.821-.103.128-.356.25-.49.208-.127-.04-.262-.294-.265-.456-.021-1.299-.021-2.599.001-3.896.002-.159.178-.314.274-.471.184.117.446.193.537.362.169.314.208.696.356 1.024.668 1.482 2.021 2.409 3.573 2.184.649-.093 1.45-.586 1.772-1.138.434-.741-.086-1.504-.814-1.925-.979-.566-1.993-1.075-3.009-1.571-2.297-1.121-3.266-2.972-2.443-4.719.818-1.737 3.33-2.46 5.429-1.556.256.11.499.25.7.354l1.078-.886c.113.317.185.426.186.535.008 1.216.005 2.431.005 3.646l-.317.212c-.211-.27-.504-.509-.619-.814-.573-1.532-1.48-2.381-2.81-2.219-.624.075-1.419.504-1.726 1.018-.45.755.2 1.361.885 1.729.963.519 1.949.992 2.926 1.483 2.418 1.213 3.269 2.898 2.434 4.824-.813 1.876-3.346 2.847-5.517 2.077-.564-.199-1.087-.52-1.717-.826z"/><path fill-rule="evenodd" clip-rule="evenodd" fill="#494949" d="M65.261 1.395C38.48.917 16.103 22.648 16.096 49c-.008 27.11 21.338 48.739 48.077 48.699 26.49-.039 47.932-21.587 47.932-48.167C112.104 23.384 90.76 1.85 65.261 1.395zm-1.148 93.887c-25.326.006-45.694-20.529-45.693-46.067.001-24.88 20.685-45.48 45.674-45.489 25.432-.008 45.695 20.654 45.687 46.587-.008 24.483-20.807 44.964-45.668 44.969zm24.395-59.347c-.994-1.638-2.216-3.227-2.778-5.013-.64-2.032-1.171-4.345-.832-6.382.576-3.454 3.225-5.169 6.812-5.497C72.086.83 41.248 7.349 29.885 27.138c4.374-.203 8.55-.468 12.729-.524.791-.011 2.1.657 2.286 1.277.416 1.385-.748 1.868-1.986 1.963-1.301.102-2.604.199-4.115.314l14.935 44.494c.359-.587.507-.752.572-.945 2.762-8.255 5.54-16.505 8.232-24.784.246-.755.124-1.755-.146-2.531-1.424-4.111-3.13-8.133-4.379-12.294-.855-2.849-1.988-4.692-5.355-4.362-.574.056-1.273-1.178-1.916-1.816.777-.463 1.548-1.316 2.332-1.328a659.24 659.24 0 0120.572.006c.786.013 1.557.889 2.335 1.364-.681.622-1.267 1.554-2.063 1.794-1.276.385-2.691.312-4.218.448l14.953 44.484c2.266-7.524 4.374-14.434 6.422-21.36 1.83-6.182.74-11.957-2.567-17.403zM52.719 88.149c-.092.267-.097.563-.168 1.007 8.458 2.344 16.75 2.175 25.24-.685l-12.968-35.52c-4.151 12.064-8.131 23.63-12.104 35.198zm-6.535-1.606L26.646 32.947c-8.814 17.217-2.109 43.486 19.538 53.596zm54.452-55.403c-.27 2.994-.082 6.327-.941 9.362-2.023 7.152-4.496 14.181-6.877 21.229-2.588 7.66-5.28 15.286-7.927 22.927 12.437-7.372 19.271-18.253 20.359-32.555.62-8.14-2.188-19.412-4.614-20.963z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// wordpress_original()



	/**
	 * @internal
	 */
	public static function laravel( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path d="M27.271.11c-.2.078-5.82 3.28-12.487 7.112-8.078 4.644-12.227 7.09-12.449 7.32-.19.225-.34.482-.438.76-.167.564-.179 82.985-.01 83.578.061.23.26.568.44.754.436.46 48.664 28.19 49.25 28.324.272.065.577.054.88-.03.658-.165 48.76-27.834 49.188-28.286.175-.195.375-.532.44-.761.084-.273.115-4.58.115-13.655v-13.26l11.726-6.735c11.056-6.357 11.733-6.755 12.017-7.191l.29-.47V43.287c0-15.548.03-14.673-.585-15.235-.165-.146-5.798-3.433-12.53-7.31L100.89 13.71h-1.359l-11.963 6.87c-6.586 3.788-12.184 7.027-12.457 7.203-.272.18-.597.512-.73.753l-.242.417-.054 13.455-.048 13.46-9.879 5.69c-5.434 3.124-9.957 5.71-10.053 5.734-.175.049-.187-1.232-.187-25.966V15.293l-.26-.447c-.326-.545 1.136.324-13.544-8.114C27.803-.348 28.098-.2 27.27.11zm11.317 10.307c5.15 2.955 9.364 5.4 9.364 5.43 0 .031-4.516 2.641-10.035 5.813l-10.041 5.765-10.023-5.764c-5.507-3.173-10.02-5.783-10.02-5.814 0-.03 4.505-2.64 10.013-5.805l9.999-5.752.69.376c3.357 1.907 6.708 3.824 10.053 5.751zm71.668 13.261c5.422 3.122 9.908 5.702 9.95 5.744.114.103-19.774 11.535-20.046 11.523-.272-.008-19.915-11.335-19.907-11.473.01-.157 19.773-11.527 19.973-11.496.091.022 4.607 2.59 10.03 5.702zM16.3 25.328l9.558 5.503.055 27.247.05 27.252.233.368c.122.194.352.459.52.581.158.115 5.477 3.146 11.818 6.724l11.52 6.506v11.527c0 6.326-.043 11.516-.097 11.516-.041 0-10-5.699-22.124-12.676L5.793 97.201l-.03-38.966-.019-38.954.49.271c.283.15 4.807 2.748 10.065 5.775zm33.754 19.18v25.109l-.387.253c-.525.332-19.667 11.335-19.732 11.335-.03 0-.054-11.336-.054-25.193l.012-25.182 10-5.752c5.499-3.165 10.034-5.733 10.088-5.714.039.024.073 11.34.073 25.144zm38.15-5.775 10.023 5.763V55.92c0 10.838-.011 11.42-.176 11.357-.107-.041-4.642-2.64-10.083-5.774l-9.91-5.69v-11.42c0-6.287.032-11.424.062-11.424.043 0 4.577 2.592 10.084 5.764zm34.164 5.587c0 6.254-.042 11.412-.084 11.462-.072.115-19.896 11.538-20.022 11.538-.031 0-.062-5.135-.062-11.423v-11.42l10-5.756c5.507-3.16 10.042-5.752 10.084-5.752.053 0 .084 5.105.084 11.351zM95.993 70.933 52.005 96.04 32.056 84.693S76 59.277 76.176 59.343zm2.215 14.827-.034 11.442-22.028 12.676c-12.12 6.976-22.082 12.675-22.132 12.675-.053 0-.095-4.658-.095-11.516V99.51l22.08-12.592c12.132-6.923 22.101-12.59 22.154-12.602.043 0 .062 5.148.054 11.443z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// laravel()



	/**
	 * @internal
	 */
	public static function codeigniter( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M11.32 32c-2.053-0.907-3.448-2.861-3.631-5.099 0.12-2.303 1.336-4.407 3.267-5.661-0.317 0.773-0.24 1.653 0.2 2.36 0.501 0.7 1.363 1.036 2.207 0.861 1.203-0.339 1.907-1.587 1.568-2.789-0.12-0.421-0.36-0.803-0.688-1.091-1.36-1.107-2.043-2.844-1.8-4.581 0.233-0.92 0.743-1.752 1.461-2.38-0.54 1.44 0.983 2.861 2.005 3.56 1.813 1.088 3.56 2.284 5.232 3.581 1.827 1.44 2.823 3.693 2.667 6-0.411 2.453-2.147 4.48-4.513 5.24 4.733-1.053 9.613-4.813 9.707-10.147-0.093-4.267-2.64-8.096-6.533-9.84h-0.173c0.087 0.209 0.128 0.435 0.12 0.661 0.013-0.147 0.013-0.293 0-0.44 0.021 0.173 0.021 0.347 0 0.52-0.296 1.213-1.52 1.96-2.736 1.664-0.485-0.12-0.92-0.393-1.232-0.787-1.56-2 0-4.276 0.261-6.476 0.16-2.813-1.125-5.503-3.405-7.147 1.141 1.903-0.379 4.4-1.484 5.821-1.107 1.421-2.707 2.48-4.011 3.72-1.405 1.307-2.693 2.744-3.849 4.28-2.499 3.053-3.48 7.080-2.667 10.94 1.115 3.72 4.207 6.515 8.020 7.24h0.021z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// codeigniter()



	/**
	 * @internal
	 */
	public static function magento( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M16 32l-5.94-3.429v-16l3.96-2.287v16.001l1.98 1.203 1.98-1.203v-16.001l3.961 2.287v16l-5.941 3.429zM29.855 8v16l-3.959 2.285v-16.001l-9.896-5.711-9.901 5.711v16.001l-3.953-2.285v-16l13.855-8 13.855 8z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// magento()



	/**
	 * @internal
	 */
	public static function html5( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M2 0h28l-2.547 28.751-11.484 3.249-11.419-3.251-2.551-28.749zM11.375 13l-0.309-3.624 13.412 0.004 0.307-3.496-17.568-0.004 0.931 10.68h12.168l-0.435 4.568-3.88 1.072-3.94-1.080-0.251-2.813h-3.479l0.44 5.561 7.229 1.933 7.172-1.924 0.992-10.876h-12.789z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// html5()



	/**
	 * @internal
	 */
	public static function css3( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M2 0h28l-2.547 28.751-11.484 3.249-11.42-3.251-2.549-28.749zM24.787 5.884l-17.573-0.004 0.284 3.496 13.5 0.003-0.34 3.621h-8.853l0.32 3.431h8.243l-0.488 4.697-3.88 1.072-3.941-1.080-0.251-2.813h-3.48l0.387 5.14 7.287 2.271 7.164-2.040 1.623-17.792z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// css3()



	/**
	 * @internal
	 */
	public static function tailwindcss( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 256 154';
		$args['content']	= '<path d="M128 0C93.867 0 72.533 17.067 64 51.2 76.8 34.133 91.733 27.733 108.8 32c9.737 2.434 16.697 9.499 24.401 17.318C145.751 62.057 160.275 76.8 192 76.8c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C174.249 14.743 159.725 0 128 0ZM64 76.8C29.867 76.8 8.533 93.867 0 128c12.8-17.067 27.733-23.467 44.8-19.2 9.737 2.434 16.697 9.499 24.401 17.318C81.751 138.857 96.275 153.6 128 153.6c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C110.249 91.543 95.725 76.8 64 76.8Z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// tailwindcss()



	/**
	 * @internal
	 */
	public static function sass( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M16 0c8.836 0 16 7.164 16 16s-7.164 16-16 16-16-7.164-16-16 7.164-16 16-16zM12.82 21.331c0.233 0.86 0.208 1.664-0.032 2.389l-0.087 0.24c-0.032 0.081-0.069 0.16-0.104 0.235-0.187 0.387-0.435 0.747-0.74 1.080-0.931 1.012-2.229 1.396-2.787 1.073-0.6-0.349-0.301-1.78 0.779-2.92 1.161-1.224 2.827-2.012 2.827-2.012v-0.004l0.144-0.081zM26.035 6.849c-0.723-2.844-5.436-3.779-9.896-2.193-2.652 0.943-5.525 2.424-7.591 4.356-2.457 2.295-2.848 4.295-2.687 5.131 0.569 2.948 4.609 4.876 6.271 6.307v0.008c-0.489 0.24-4.075 2.039-4.915 3.9-0.9 1.96 0.14 3.361 0.82 3.54 2.1 0.581 4.26-0.48 5.42-2.199 1.12-1.681 1.021-3.841 0.539-4.901 0.661-0.18 1.44-0.26 2.44-0.139 2.801 0.32 3.361 2.080 3.24 2.8-0.12 0.719-0.697 1.139-0.899 1.259-0.2 0.121-0.26 0.16-0.241 0.241 0.020 0.12 0.121 0.12 0.28 0.1 0.22-0.040 1.461-0.6 1.521-1.961 0.060-1.72-1.581-3.639-4.5-3.6-1.2 0.021-1.961 0.121-2.5 0.341-0.040-0.060-0.081-0.1-0.14-0.14-1.8-1.94-5.14-3.3-5-5.88 0.040-0.94 0.38-3.419 6.4-6.419 4.94-2.461 8.881-1.78 9.561-0.28 0.977 2.139-2.101 6.12-7.241 6.699-1.96 0.22-2.98-0.539-3.241-0.82-0.279-0.3-0.319-0.32-0.419-0.259-0.16 0.080-0.060 0.34 0 0.5 0.16 0.4 0.78 1.1 1.861 1.46 0.939 0.3 3.24 0.479 6-0.6 3.099-1.199 5.519-4.54 4.819-7.34l0.097 0.089z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// sass()



	/**
	 * @internal
	 */
	public static function javascript( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M0 0h32v32h-32v-32zM29.379 24.368c-0.233-1.46-1.184-2.687-4.004-3.831-0.981-0.46-2.072-0.78-2.396-1.52-0.121-0.44-0.14-0.68-0.061-0.94 0.2-0.861 1.22-1.12 2.020-0.88 0.52 0.16 1 0.56 1.301 1.2 1.379-0.901 1.379-0.901 2.34-1.5-0.36-0.56-0.539-0.801-0.781-1.040-0.84-0.94-1.959-1.42-3.779-1.379l-0.94 0.119c-0.901 0.22-1.76 0.7-2.28 1.34-1.52 1.721-1.081 4.721 0.759 5.961 1.82 1.36 4.481 1.659 4.821 2.94 0.32 1.56-1.16 2.060-2.621 1.88-1.081-0.24-1.68-0.781-2.34-1.781l-2.44 1.401c0.28 0.64 0.6 0.919 1.080 1.479 2.32 2.341 8.12 2.221 9.161-1.339 0.039-0.12 0.32-0.94 0.099-2.2l0.061 0.089zM17.401 14.708h-2.997c0 2.584-0.012 5.152-0.012 7.74 0 1.643 0.084 3.151-0.184 3.615-0.44 0.919-1.573 0.801-2.088 0.64-0.528-0.261-0.796-0.621-1.107-1.14-0.084-0.14-0.147-0.261-0.169-0.261l-2.433 1.5c0.407 0.84 1 1.563 1.765 2.023 1.14 0.68 2.672 0.9 4.276 0.54 1.044-0.301 1.944-0.921 2.415-1.881 0.68-1.24 0.536-2.76 0.529-4.461 0.016-2.739 0-5.479 0-8.239l0.005-0.075z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// javascript()



	/**
	 * @internal
	 */
	public static function alpinejs( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path fill-rule="evenodd" d="M98.444 35.562 126 62.997 98.444 90.432 70.889 62.997z" clip-rule="evenodd"/><path fill-rule="evenodd" d="m29.556 35.562 57.126 56.876H31.571L2 62.997z" clip-rule="evenodd"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// alpinejs()



	/**
	 * @internal
	 */
	public static function nodejs( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path d="M63.999 0a5.617 5.617 0 0 0-2.789.744L11.445 29.646 28.852 61.58 64.592.053A8.177 8.177 0 0 0 64.003 0Zm2.316.605v.002l35.194 60.577 16.545-30.449a5.483 5.483 0 0 0-1.028-.817L91.38 15.024 66.7.754h-.007c-.12-.061-.252-.099-.378-.15Zm-1.024.248L29.417 62.616l35.579 65.278c.1-.02.198-.023.297-.05l35.653-65.624ZM10.586 30.176c-1.502 1.031-2.35 2.752-2.35 4.595v58.478c0 .93.254 1.838.684 2.645l19.34-33.293Zm108.161 1.4-16.643 30.629 17.66 30.398V34.77c0-1.15-.382-2.265-1.017-3.195zm-17.204 31.667-34.808 64.062.055-.028 50.243-29.183.004-.002c1.402-.793 2.3-2.155 2.604-3.693zm-72.718.394L9.545 96.832c.406.5.885.936 1.43 1.266l.001.004 49.702 28.866.53.305.006.002h.002c.257.151.528.266.798.372.144.054.288.104.433.146.125.037.251.062.376.089.242.051.483.088.727.108.118.01.237.01.355.01z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// nodejs()



	/**
	 * @internal
	 */
	public static function npm( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M0 9.779v10.667h8.888v1.776h7.112v-1.776h16v-10.667h-32zM8.888 18.664h-1.776v-5.333h-1.78v5.333h-3.552v-7.108h7.108v7.108zM14.221 18.664v1.781h-3.553v-8.889h7.112v7.109l-3.559-0.001zM30.223 18.664h-1.773v-5.333h-1.781v5.333h-1.78v-5.333h-1.773v5.333h-3.561v-7.108h10.669v7.108z"></path>
<path d="M14.22 13.333h1.78v3.556h-1.78v-3.556z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// npm()



	/**
	 * @internal
	 */
	public static function webpack( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M28.021 24.16l-11.553 6.506v-5.068l7.198-3.942 4.355 2.503zM28.811 23.448v-13.607l-4.228 2.429v8.746l4.228 2.432zM3.901 24.16l11.553 6.506v-5.068l-7.198-3.942-4.355 2.503zM3.111 23.448v-13.607l4.228 2.429v8.746l-4.228 2.432zM3.605 8.96l11.849-6.674v4.9l-7.649 4.19-4.2-2.415zM28.317 8.96l-11.849-6.674v4.9l7.649 4.19 4.2-2.415zM15.454 24.446l-7.102-3.887v-7.703l7.101 4.083v7.508zM16.468 24.446l7.101-3.887v-7.703l-7.101 4.083v7.508zM8.833 11.965l7.129-3.904 7.129 3.904-7.129 4.099-7.129-4.099z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// webpack()



	/**
	 * @internal
	 */
	public static function vite( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path d="M91.557 1.92 49.05 10.248a1.057 1.057 0 0 0-.856.977l-2.617 44.168a1.05 1.05 0 0 0 1.299 1.097l11.836-2.736c1.444-.332 2.78.972 2.482 2.424l-3.517 17.218a1.047 1.047 0 0 0 1.35 1.227l7.308-2.223c1.475-.447 2.929.881 2.62 2.391l-5.59 27.047c-.128.62.192 1.014.638 1.197.446.183.95.127 1.293-.404l.61-.945 34.626-69.106c.41-.818-.254-1.692-1.146-1.52L87.2 33.413c-1.49.29-2.792-1.136-2.369-2.594l7.951-27.562A1.046 1.046 0 0 0 91.56 1.92h-.002zM6.086 14.86a3.123 3.123 0 0 0-.463.02c-2.106.254-3.467 2.637-2.318 4.651l58.582 102.73c1.203 2.11 4.25 2.098 5.437-.023L124.766 19.52c1.289-2.305-.688-5.067-3.286-4.594l-32.744 5.969-2.943 10.2v.003a1.043 1.043 0 0 0 1.217 1.334h.002l12.183-2.354v.002c1.646-.319 2.986 1.446 2.233 2.947l-34.653 69.155-.638.992a2.055 2.055 0 0 1-2.512.787 2.057 2.057 0 0 1-1.238-2.324l5.59-27.047a1.05 1.05 0 0 0-1.35-1.233l-7.309 2.223c-1.472.448-2.927-.875-2.62-2.383l3.517-17.218a1.049 1.049 0 0 0-1.278-1.25l-11.835 2.736c-1.328.304-2.6-.771-2.522-2.131l1.973-33.277L6.566 14.91a3.279 3.279 0 0 0-.48-.05z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// vite()



	/**
	 * @internal
	 */
	public static function vuejs( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M25.596 2.144l0.004-0.008h-5.9l-3.7 6.4-3.696-6.4h-5.9v0.007h-6.404l16 27.715 16-27.713z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// vuejs()



	/**
	 * @internal
	 */
	public static function react( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M18.852 16c0 1.575-1.277 2.852-2.852 2.852s-2.852-1.277-2.852-2.852c0-1.575 1.277-2.852 2.852-2.852s2.852 1.277 2.852 2.852z"></path>
<path d="M8.011 21.673l-0.629-0.16c-4.691-1.185-7.381-3.197-7.381-5.519s2.691-4.333 7.381-5.519l0.629-0.159 0.177 0.624c0.545 1.86 1.163 3.443 1.899 4.957l-0.082-0.186 0.135 0.284-0.135 0.284c-0.656 1.329-1.275 2.912-1.761 4.551l-0.056 0.22-0.177 0.623zM7.089 11.933c-3.565 1.001-5.753 2.533-5.753 4.061 0 1.527 2.188 3.059 5.753 4.061 0.512-1.62 1.046-2.965 1.662-4.263l-0.086 0.202c-0.531-1.098-1.066-2.443-1.511-3.828l-0.065-0.234zM23.989 21.673l-0.177-0.625c-0.544-1.859-1.162-3.441-1.9-4.954l0.081 0.184-0.135-0.284 0.135-0.284c0.656-1.329 1.275-2.911 1.762-4.55l0.056-0.221 0.177-0.624 0.631 0.159c4.689 1.185 7.38 3.197 7.38 5.52s-2.691 4.333-7.38 5.519l-0.631 0.16zM23.335 15.995c0.64 1.385 1.169 2.747 1.576 4.061 3.567-1.003 5.753-2.535 5.753-4.061 0-1.528-2.188-3.059-5.753-4.061-0.511 1.619-1.045 2.963-1.662 4.262l0.086-0.2z"></path>
<path d="M7.080 11.927l-0.177-0.623c-1.319-4.648-0.919-7.979 1.097-9.141 1.977-1.141 5.152 0.207 8.479 3.621l0.453 0.465-0.453 0.465c-1.146 1.19-2.207 2.482-3.162 3.853l-0.068 0.103-0.18 0.257-0.313 0.027c-1.875 0.151-3.596 0.439-5.264 0.86l0.218-0.047-0.629 0.159zM9.608 3.087c-0.357 0-0.673 0.077-0.94 0.231-1.325 0.764-1.56 3.42-0.647 7.004 1.21-0.285 2.681-0.521 4.18-0.658l0.131-0.010c0.894-1.264 1.792-2.37 2.756-3.414l-0.020 0.022c-2.080-2.025-4.049-3.175-5.46-3.175zM22.393 30.236c-0.001 0-0.001 0 0 0-1.9 0-4.34-1.431-6.872-4.031l-0.453-0.465 0.453-0.465c1.146-1.19 2.206-2.482 3.16-3.854l0.068-0.103 0.18-0.257 0.312-0.027c1.875-0.15 3.597-0.438 5.267-0.858l-0.218 0.046 0.629-0.159 0.179 0.624c1.316 4.645 0.917 7.977-1.099 9.139-0.449 0.259-0.988 0.411-1.562 0.411-0.016 0-0.031-0-0.047-0l0.002 0zM16.932 25.728c2.080 2.025 4.049 3.175 5.46 3.175h0.001c0.356 0 0.673-0.077 0.939-0.231 1.325-0.764 1.561-3.421 0.647-7.005-1.212 0.286-2.682 0.522-4.182 0.658l-0.13 0.010c-0.893 1.265-1.791 2.371-2.755 3.416l0.020-0.022z"></path>
<path d="M24.92 11.927l-0.629-0.159c-1.453-0.376-3.175-0.664-4.939-0.806l-0.111-0.007-0.312-0.027-0.18-0.257c-1.021-1.474-2.082-2.766-3.237-3.966l0.009 0.010-0.453-0.465 0.453-0.465c3.325-3.413 6.499-4.761 8.479-3.621 2.016 1.163 2.416 4.493 1.099 9.14l-0.179 0.624zM19.667 9.653c1.523 0.139 2.969 0.364 4.312 0.668 0.915-3.584 0.679-6.24-0.647-7.004-1.317-0.761-3.793 0.405-6.4 2.944 0.943 1.022 1.841 2.128 2.666 3.289l0.069 0.103zM9.608 30.236c-0.014 0-0.030 0-0.046 0-0.574 0-1.113-0.152-1.577-0.419l0.015 0.008c-2.016-1.161-2.416-4.492-1.097-9.139l0.176-0.624 0.629 0.159c1.54 0.388 3.239 0.661 5.047 0.812l0.313 0.027 0.179 0.257c1.022 1.475 2.083 2.767 3.239 3.967l-0.010-0.010 0.453 0.465-0.453 0.465c-2.531 2.6-4.971 4.031-6.868 4.031zM8.021 21.667c-0.915 3.584-0.679 6.241 0.647 7.005 1.316 0.751 3.791-0.407 6.4-2.944-0.943-1.022-1.841-2.129-2.667-3.29l-0.069-0.103c-1.629-0.146-3.099-0.382-4.534-0.711l0.224 0.043z"></path>
<path d="M16 22.504c-1.097 0-2.225-0.048-3.355-0.141l-0.313-0.027-0.18-0.257c-0.544-0.768-1.139-1.691-1.697-2.639l-0.103-0.19c-0.452-0.758-0.971-1.732-1.45-2.728l-0.105-0.243-0.133-0.284 0.133-0.284c0.584-1.239 1.102-2.212 1.658-3.161l-0.103 0.191c0.552-0.955 1.159-1.907 1.8-2.829l0.18-0.257 0.313-0.027c1.005-0.090 2.174-0.142 3.355-0.142s2.35 0.052 3.505 0.153l-0.15-0.011 0.312 0.027 0.179 0.257c1.177 1.666 2.294 3.567 3.252 5.56l0.104 0.24 0.135 0.284-0.135 0.284c-1.058 2.232-2.176 4.133-3.439 5.925l0.083-0.125-0.179 0.257-0.312 0.027c-1.129 0.093-2.259 0.141-3.356 0.141zM13.071 21.059c1.973 0.148 3.885 0.148 5.86 0 1.004-1.445 1.978-3.104 2.827-4.836l0.101-0.228c-0.947-1.958-1.922-3.617-3.012-5.19l0.083 0.126c-0.877-0.071-1.898-0.111-2.929-0.111s-2.052 0.040-3.063 0.119l0.134-0.008c-1.008 1.447-1.983 3.106-2.83 4.84l-0.099 0.224c0.951 1.96 1.926 3.619 3.014 5.193l-0.085-0.13z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// react()



	/**
	 * @internal
	 */
	public static function angular( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M13.24 16.86h5.512l-2.757-6.54z"></path>
<path d="M15.995 0.012l-15.080 5.305 2.3 19.68 12.78 6.991 12.784-6.984 2.299-19.684-15.083-5.307zM25.405 24.408h-3.515l-1.893-4.668h-8.004l-1.893 4.668h-3.517l9.413-20.864 9.409 20.864z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// angular()



	/**
	 * @internal
	 */
	public static function meteor( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M0 0.312l29.216 27.383s0.549 0.767-0.165 1.535c-0.713 0.768-1.648 0.111-1.648 0.111l-27.403-29.028zM8.677 3.056l22.68 20.851s0.551 0.768-0.164 1.536c-0.712 0.768-1.647 0.111-1.647 0.111l-20.869-22.497zM2.581 8.928l22.68 20.851s0.549 0.768-0.164 1.536-1.647 0.109-1.647 0.109l-20.869-22.496zM16.012 5.415l15.848 14.569s0.383 0.535-0.116 1.073-1.151 0.077-1.151 0.077l-14.581-15.72zM4.589 15.731l15.848 14.568s0.38 0.533-0.117 1.071c-0.5 0.537-1.151 0.079-1.151 0.079l-14.58-15.717zM23.505 8.817l7.235 6.607s0.189 0.251-0.059 0.503c-0.247 0.251-0.571 0.036-0.571 0.036l-6.605-7.145zM8.237 22.975l7.233 6.608s0.192 0.251-0.056 0.503-0.569 0.035-0.569 0.035l-6.608-7.145z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// meteor()



	/**
	 * @internal
	 */
	public static function ionic( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 32 32';
		$args['content']	= '<path d="M16 8.706c-4.019 0-7.294 3.269-7.294 7.294 0 4.019 3.269 7.294 7.294 7.294s7.294-3.275 7.294-7.294-3.275-7.294-7.294-7.294z"></path>
<path d="M29.794 6.031c0 1.836-1.489 3.325-3.325 3.325s-3.325-1.489-3.325-3.325c0-1.836 1.489-3.325 3.325-3.325s3.325 1.489 3.325 3.325z"></path>
<path d="M30.563 9.369l-0.137-0.306-0.225 0.25c-0.544 0.619-1.238 1.094-2.006 1.381l-0.212 0.081 0.087 0.206c0.663 1.594 1 3.281 1 5.013 0 7.206-5.863 13.075-13.075 13.075s-13.069-5.863-13.069-13.069 5.869-13.075 13.075-13.075c1.956 0 3.844 0.425 5.6 1.263l0.206 0.1 0.087-0.206c0.319-0.75 0.831-1.419 1.475-1.938l0.262-0.212-0.3-0.156c-2.281-1.175-4.75-1.775-7.331-1.775-8.825 0-16 7.175-16 16s7.175 16 16 16 16-7.175 16-16c0-2.306-0.481-4.537-1.438-6.631z"></path>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// ionic()



	/**
	 * @internal
	 */
	public static function nextjs( $args = [] )
	{

		$args['fill']			= 'currentColor';
		$args['viewBox']	= '0 0 128 128';
		$args['content']	= '<path d="M64 0C28.7 0 0 28.7 0 64s28.7 64 64 64c11.2 0 21.7-2.9 30.8-7.9L48.4 55.3v36.6h-6.8V41.8h6.8l50.5 75.8C116.4 106.2 128 86.5 128 64c0-35.3-28.7-64-64-64zm22.1 84.6l-7.5-11.3V41.8h7.5v42.8z"/>';

		// -------------------------------------------------------------------------

		return self::render( $args );

	}
	// nextjs()

}
// class Icons
