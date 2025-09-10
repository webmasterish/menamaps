<?php

namespace DotAim;

/**
 * @since 1.0.0
 */
class F
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PARSE RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function parse_args( $args, $defaults = '' )
	{

		// if no $args and no $defaults, i'm gone...

		if ( empty( $args ) && empty( $defaults ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( function_exists( '\wp_parse_args' ) )
		{
			return \wp_parse_args( $args, $defaults );
		}

		// -------------------------------------------------------------------------

		if ( is_object( $args ) )
		{
			$out = get_object_vars( $args );
		}
		elseif ( is_array( $args ) )
		{
			$out =& $args;
		}
		else
		{
			parse_str( $args, $out );
		}

		// -------------------------------------------------------------------------

		if ( is_array( $defaults ) )
		{
			return array_merge( $defaults, $out );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// parse_args()



	/**
	 * @since 1.0.0
	 */
	public static function parse_args_deep( &$args, $defaults )
	{

		$args			= (array) $args;
		$defaults	= (array) $defaults;
		$out			= $defaults;

		// -------------------------------------------------------------------------

		foreach ( $args as $key => &$value )
		{
			if ( is_array( $value ) && isset( $out[ $key ] ) )
			{
				$out[ $key ] = self::parse_args_deep( $value, $out[ $key ] );
			}
			else
			{
				$out[ $key ] = $value;
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// parse_args_deep()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PARSE RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * VALIDATE AND CONDITIONAL CHECKS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function is_email( $str )
	{

		if ( filter_var( $str, FILTER_VALIDATE_EMAIL ) === false )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// is_email()



	/**
	 * @since 1.0.0
	 */
	public static function is_url( $str )
	{

		if ( filter_var( $str, FILTER_VALIDATE_URL ) === false )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// is_url()



	/**
	 * @since 1.0.0
	 */
	public static function is_ip( $str, $check_if_remote = false )
	{

		if ( $check_if_remote )
		{
			$options = [
				'flags'	=> FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
			];
		}
		else
		{
			$options = null;
		}

		// -------------------------------------------------------------------------

		//if ( filter_var( $str, FILTER_VALIDATE_IP ) === false )
		if ( filter_var( $str, FILTER_VALIDATE_IP, $options ) === false )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// is_ip()



	/**
	 * check if we're in frontend
	 *
	 * @since 1.0.0
	 */
	public static function is_frontend( $pagenow_excludes = array( 'wp-login.php', 'wp-register.php' ) )
	{

		// check if we're in admin

		if ( is_admin() )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		// check if we're in an excluded page

		if ( 	! empty( $GLOBALS['pagenow'] )
			 && ! empty( $pagenow_excludes ) )
		{
			if ( in_array( $GLOBALS['pagenow'], $pagenow_excludes ) )
			{
				return false;
			}
		}

		// -------------------------------------------------------------------------

		// looks like we are

		return true;

	}
	// is_frontend()



	/**
	 * @since 1.0.0
	 */
	public static function is_wp_local_dev()
	{

		return ( defined('WP_LOCAL_DEV') && WP_LOCAL_DEV );

	}
	// is_wp_local_dev()



	/**
	 * @since 1.0.0
	 */
	public static function is_wp_debug()
	{

		return ( defined('WP_DEBUG') && WP_DEBUG );

	}
	// is_wp_debug()



	/**
	 * @since 1.0.0
	 */
	public static function is_script_debug()
	{

		return ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG );

	}
	// is_script_debug()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * VALIDATE AND CONDITIONAL CHECKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * STRINGS RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function minify( $str, $args = [] )
	{

		if ( ! $str || ! is_string( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		$defaults = [

			'search'	=> ["\r\n", "\r", "\n", "\t"],
			'replace'	=> '',

			// -----------------------------------------------------------------------

			'comments_patterns' => [

				// Remove all multi-line comments `/* ... */`

				'/\/\*[\s\S]*?\*\//',

				// ---------------------------------------------------------------------

				// Remove single-line comments `// ...` excluding:
				// - URLs with protocols http://
				// - Protocol-relative URLs //
				// - Data URIs data:*/

				//'/(?<!:|\/|\')\/\/(?!(\/|\*)).*?(?=\n|$)/m',
				'/(?<!:|\/|\')\/\/(?!(\/)|([\w\-]+\.)[\w\.\-\/]+).*?(?=\n|$)/m',

			],

			// -----------------------------------------------------------------------

			'space_patterns' => [

				// Multiple spaces to single space
				'/\s+/' => ' ',

				// @consider:
				//
				// Space after comma
				//'/\s*,\s*/' => ',',
				//
				// Space around brackets and semicolons
				//'/\s*([\{\}\[\]\(\)])\s*/' => '$1',
				//
				// Space before/after colon in property declarations
				//'/\s*:\s*/' => ':',
				//
				// Space before semicolon
				//'/\s*;/' => ';',
				//
				// Optional space after semicolon if not end of line
				//'/;\s+/' => ';',

			],

		];

		$args = self::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! empty( $comments_patterns ) )
		{
			$str = preg_replace( $comments_patterns, '', $str );
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $search ) )
		{
			$str = str_replace( $search, $replace, $str );
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $space_patterns ) )
		{
			$str = preg_replace(
				array_keys( $space_patterns ),
				array_values( $space_patterns ),
				$str
			);
		}

		// -------------------------------------------------------------------------

		return trim( $str );

	}
	// minify()



	/**
	 * @since 1.0.0
	 */
	public static function stripslashes( $value, $deep = true )
	{

		if ( ! $value )
		{
			return $value;
		}

		// -------------------------------------------------------------------------

		if ( is_string( $value ) )
		{
			return stripslashes( $value );
		}

		// -------------------------------------------------------------------------

		if ( $deep )
		{
			if ( function_exists( '\wp_unslash' ) )
			{
				$value = \wp_unslash( $value );
			}
			elseif ( function_exists( '\stripslashes_deep' ) )
			{
				$value = \stripslashes_deep( $value );
			}
		}

		// -------------------------------------------------------------------------

		return $value;

	}
	// stripslashes()



	/**
	 * @since 1.0.0
	 */
	public static function absint( $maybeint )
	{

		if ( function_exists( '\absint' ) )
		{
			return \absint( $maybeint );
		}

		// -------------------------------------------------------------------------

		return abs( intval( $maybeint ) );

	}
	// absint()



	/**
	 * @since 1.0.0
	 */
	public static function limit_by_chars( $str, $args = '' )
	{

		if ( ! $str )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$defaults = array(
			'max_chars'	=> 160,
			'more'			=> '&hellip;',
		);

		$args = self::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$out = trim( $str );

		// -------------------------------------------------------------------------

		if ( function_exists( '\strip_shortcodes' ) )
		{
			$out = \strip_shortcodes( $out );
		}

		$out = strip_tags( $out );
		$out = trim( $out );
		$out = preg_replace( '/\s\s+/', ' ', $out );

		// -------------------------------------------------------------------------

		$len 				= strlen( $out );
		$max_chars 	= self::absint( $max_chars );

		if ( 	$max_chars > 0
			 && $len > $max_chars )
		{
			// find last space to have a complete word

			$pos = strrpos( $out, ' ', $max_chars - $len );

			// -----------------------------------------------------------------------

			if ( $pos )
			{
				$out = substr( $out, 0, $pos );
			}
			else
			{
				$out = substr( $out, 0, $max_chars );
			}

			// -----------------------------------------------------------------------

			$out .= $more;
		}

		// -------------------------------------------------------------------------

		return $out;

		// -------------------------------------------------------------------------

		/*
		@notes: derived from wp_trim_excerpt() in wp-includes/formatting.php

		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = strip_tags( $text );

		$excerpt_length = $this->get_setting( 'posts_use_content_for_meta_description' );

		$words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) > $excerpt_length )
		{
			array_pop( $words );
			$description = implode( ' ', $words );
			$description = $text . $excerpt_more;
		}
		else
		{
			$description = implode( ' ', $words );
		}


		$description = trim( $description );
		$description = esc_attr( strip_tags( $description ) );
		*/

	}
	// limit_by_chars()



	/**
	 * @since 1.0.0
	 */
	public static function camelize( $str, $lcfirst = true )
	{

		if ( empty( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		if ( ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$str = trim( $str );

		// -------------------------------------------------------------------------

		if ( $lcfirst )
		{
			$str = strtolower( $str[0] ) . substr( $str, 1 );
		}

		// -------------------------------------------------------------------------

		$str = preg_replace_callback(
			'/[-_\s]+(.)?/u',
			function ( $match ) {
				return $match[1] ? strtoupper( $match[1] ) : '';
			},
			$str
		);

		// -------------------------------------------------------------------------

		$str = preg_replace_callback(
			'/[\d]+(.)?/u',
			function ( $match ) {
				return strtoupper( $match[0] );
			},
			$str
		);

		// -------------------------------------------------------------------------

		return $str;

	}
	// camelize()



	/**
	 * @since 1.0.0
	 */
	public static function uncamelize( $str, $space = ' ' )
	{

		if ( empty( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		if ( ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$str = trim( $str );

		// -------------------------------------------------------------------------

		$str = preg_replace(
			'/(?!^)[[:upper:]][[:lower:]]/',
			'$0',
			preg_replace( '/(?!^)[[:upper:]]+/', $space . '$0', $str )
		);

		// -------------------------------------------------------------------------

		return $str;

	}
	// uncamelize()



	/**
	 * @since 1.0.0
	 */
	public static function dasherize( $str )
	{

		if ( empty( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		if ( ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$str = trim( $str );

		// -------------------------------------------------------------------------

		$str = mb_ereg_replace( '\B([A-Z])', '-\1', $str );
		$str = mb_ereg_replace( '[-_\s]+', '-', $str );
		$str = mb_strtolower( $str );

		// -------------------------------------------------------------------------

		return $str;

	}
	// dasherize()



	/**
	 * @since 1.0.0
	 */
	public static function underscore( $str )
	{

		if ( empty( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		if ( ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$str = trim( $str );

		// -------------------------------------------------------------------------

		$str = mb_ereg_replace( '\B([A-Z])', '_\1', $str );
		$str = mb_ereg_replace( '[-_\s]+', '_', $str );
		$str = mb_strtolower( $str );

		// -------------------------------------------------------------------------

		return $str;

	}
	// underscore()



	/**
	 * @since 1.0.0
	 */
	public static function humanize( $str, $uncamelize = true, $titleize = true )
	{

		if ( empty( $str ) )
		{
			return '';
		}

		// -------------------------------------------------------------------------

		if ( ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$str = trim( $str );

		// -------------------------------------------------------------------------

		$space = ' ';

		// -------------------------------------------------------------------------

		// reverse camelized

		if ( $uncamelize )
		{
			$str = self::uncamelize( $str, $space );
		}

		// -------------------------------------------------------------------------

		// reverse dasherized and underscored

		$str = str_replace( array( '_', '-' ), $space, $str );

		// -------------------------------------------------------------------------

		// normalize white space

		$str = preg_replace( '/\s+/', $space, $str );

		// -------------------------------------------------------------------------

		$str = $titleize ? ucwords( $str ) : ucfirst( strtolower( $str ) );

		// -------------------------------------------------------------------------

		return $str;

	}
	// humanize()



	/**
	 * @since 1.0.0
	 */
	public static function starts_with( $haystack, $needle )
	{

		if ( function_exists('str_starts_with') )
		{
			return str_starts_with( $haystack, $needle );
		}

		// -------------------------------------------------------------------------

		return ! strncmp( $haystack, $needle, strlen( $needle ) );

	}
	// starts_with()



	/**
	 * @since 1.0.0
	 */
	public static function ends_with( $haystack, $needle )
	{

		if ( function_exists('str_ends_with') )
		{
			return str_ends_with( $haystack, $needle );
		}

		// -------------------------------------------------------------------------

		return substr( $haystack, -strlen( $needle ) ) === $needle;

	}
	// ends_with()



	/**
	 * @since 1.0.0
	 */
	public static function str_contains( $haystack, $needle )
	{

		if ( function_exists('str_contains') )
		{
			return str_contains( $haystack, $needle );
		}

		// -------------------------------------------------------------------------

		return strpos( $haystack, $needle ) !== false;

	}
	// str_contains()



	/**
	 * @internal
	 */
	public static function wrap_token_text( $text, $left = '{{', $right = '}}' )
	{

		return "{$left}{$text}{$right}";

	}
	// wrap_token_text()



	/**
	 * @since 1.0.0
	 */
	public static function replace_tokens_in_template( $template, $tokens_replacements )
	{

		$out = str_replace(
			array_keys( $tokens_replacements ),
			array_values( $tokens_replacements ),
			self::stripslashes( $template )
		);

		// -------------------------------------------------------------------------

		if ( $out )
		{
			$out = trim( do_shortcode( $out ) );

			// -----------------------------------------------------------------------

			// replace multiple spaces or tabs with a single space
			// happens when a token or shortcode is used but nothing is generated

			$out = preg_replace("/ {2,}|\t/", ' ', $out);
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// replace_tokens_in_template()



	/**
	 * @internal
	 */
	public static function hashtag_it( $str )
	{

		if ( ! $str = trim( $str ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$str = sanitize_file_name( $str );
		$str = preg_replace( '/[\-\s+]/', '_', $str );
		$str = preg_replace( '/[^\da-z_]/i', '', $str );
		$str = preg_replace( '/_+/', '_', $str );

		if ( ! $str = trim( $str, '_' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! self::starts_with( $str, '#' ) )
		{
			$str = "#{$str}";
		}

		// -------------------------------------------------------------------------

		return $str;

	}
	//  hashtag_it()



	/**
	 * @internal
	 */
	public static function text_is_rtl( $text )
	{

		if ( ! $text = trim( $text ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$rtl_char_ranges = [
			'\x{0590}-\x{05FF}', // Hebrew
			'\x{0600}-\x{06FF}', // Arabic, Persian, Urdu
			'\x{0750}-\x{077F}', // Arabic Supplement
			'\x{08A0}-\x{08FF}', // Arabic Extended-A
			'\x{FB50}-\x{FDFF}', // Arabic Presentation Forms-A
			'\x{FE70}-\x{FEFF}', // Arabic Presentation Forms-B
			'\x{FB00}-\x{FBFF}', // Additional range for Persian and Urdu
			'\x{0670}-\x{06FF}'  // Additional range for Persian and Urdu
		];

		$pattern = '/[' . implode( '', $rtl_char_ranges ) . ']/u';

		// -------------------------------------------------------------------------

		return preg_match( $pattern, $text );

	}
	//  text_is_rtl()



	/**
	 * remove arabic diacritic (tashkeel)
	 */
	public static function remove_arabic_diacritic( $text )
	{

		if ( ! $text = trim( $text ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$diacritic_char_ranges = [
			'\x{064B}-\x{065F}',
			'\x{0670}',
			'\x{0640}',

			//'\x{0610}-\x{061A}',
			//'\x{06D6}-\x{06DC}',
			'\x{06DF}-\x{06E8}',
			//'\x{06EA}-\x{06ED}',
		];

		$pattern = '/[' . implode( '', $diacritic_char_ranges ) . ']/u';

		// -------------------------------------------------------------------------

		return preg_replace( $pattern, '', $text );

	}
	//  remove_arabic_diacritic()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * STRINGS RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DATE/TIME RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public static function current_timestamp( $utc = false )
	{

		// utc false is local time

		return current_time( 'timestamp', $utc );

	}
	// current_timestamp()



	/**
	 * @internal
	 */
	public static function current_timestamp_utc()
	{

		// utc time - same as using time()

		return self::current_timestamp( true );

	}
	// current_timestamp_utc()



	/**
	 * @internal
	 */
	public static function get_nearest_hour_timestamp( $next = false, $utc = true )
	{

		$time			= self::current_timestamp( $utc );
		$minutes	= $time % 3600;			// pulls the remainder of the hour.
		$out			= $time - $minutes;	// start off rounded down.

		// -------------------------------------------------------------------------

		// if next is set then regardless of the current minute add 1 hour
		// otherwise add one hour if 30 mins or higher

		if ( $next )
		{
			$out += 3600;
		}
		elseif ( $minutes >= 1800 )
		{
			$out += 3600;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_nearest_hour_timestamp()



	/**
	 * @internal
	 */
	public static function get_current_time( $timestamp = '', $format = 'H:i' )
	{

		$timestamp = $timestamp ?: self::current_timestamp();

		// -------------------------------------------------------------------------

		return date( $format, $timestamp );

	}
	// get_current_time()



	/**
	 * @internal
	 */
	public static function get_current_hour( $timestamp = '', $format = 'G' )
	{

		// default format G is 0 to 23

		return self::get_current_time( $timestamp, $format );

	}
	// get_current_hour()



	/**
	 * @internal
	 */
	public static function get_current_day_of_week( $timestamp = '', $format = 'l' )
	{

		// default format l is Sunday through Saturday

		return self::get_current_time( $timestamp, $format );

	}
	// get_current_day_of_week()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DATE/TIME RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ARRAYS RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function array_sort_by( $arr, $sort_by, $flag = null, $reverse = false )
	{

		$out = $arr;

		// -------------------------------------------------------------------------

		switch ( $sort_by )
		{
			case 'key':

				if ( $reverse )
				{
					krsort( $out, $flag );
				}
				else
				{
					ksort( $out, $flag );
				}

				break;

			// -----------------------------------------------------------------------

			case 'value':

				if ( $reverse )
				{
					rsort( $out, $flag );
				}
				else
				{
					sort( $out, $flag );
				}

				break;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// array_sort_by()



	/**
	 * @since 1.0.0
	 */
	public static function array_sort_by_array( $arr, $sort_by )
	{

		$out = array();

		// -------------------------------------------------------------------------

		foreach ( (array) $sort_by as $key )
		{
			if ( array_key_exists( $key, $arr ) )
			{
				$out[ $key ] = $arr[ $key ];

				unset( $arr[ $key ] );
			}
		}

		// -------------------------------------------------------------------------

		return $out + $arr;

	}
	// array_sort_by_array()



	/**
	 * @since 1.0.0
	 */
	public static function array_ksort_deep( &$array, $flag = SORT_REGULAR )
	{

		foreach ( $array as &$value )
		{
			if ( is_array( $value ) )
			{
				self::array_ksort_deep( $value );
			}
		}

		// -------------------------------------------------------------------------

		return ksort( $array );

	}
	// array_ksort_deep()



	/**
	 * @since 1.0.0
	 */
	public static function array_insert_at_position( $original_array, $to_insert, $position, $preserve_keys = true )
	{

		if ( 		! is_array( $original_array )
				 || empty( $original_array )
				 || empty( $to_insert ) )
		{
			return $original_array;
		}

		// -------------------------------------------------------------------------

		if ( $preserve_keys )
		{
			$out =
				array_slice( $original_array, 0, $position, $preserve_keys ) +
				$to_insert +
				array_slice( $original_array, $position, count( $original_array ) - 1, $preserve_keys );
		}
		else
		{
			// ref: http://php.net/manual/en/function.array-splice.php

			$length = 0;

			$out = array_splice( $original_array, $position, $length, $to_insert );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// array_insert_at_position()



	/**
	 * @since 1.0.0
	 */
	public static function array_get_random_value( $arr )
	{

		if ( ! is_array( $arr ) )
		{
			return $arr;
		}

		// -------------------------------------------------------------------------

		return $arr[ array_rand( $arr ) ];

	}
	// array_get_random_value()



	/**
	 * @since 1.0.0
	 */
	public static function str_to_array( $str, $delimiter = ',', $no_empty = true, $unique = true )
	{

		if ( ! $str )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		if ( is_string( $str ) )
		{
			$out = explode( $delimiter, $str );
		}
		else
		{
			// the passed param `$str` could be an array

			$out = $str;
		}

		// -------------------------------------------------------------------------

		// make sure

		if ( ! is_array( $out ) )
		{
			return $out;
		}

		// -------------------------------------------------------------------------

		$out = array_map( 'trim', $out );

		// -------------------------------------------------------------------------

		if ( $no_empty )
		{
			$out = array_filter( $out );
		}

		// -------------------------------------------------------------------------

		if ( $unique )
		{
			$out = array_unique( $out );
		}

		// -------------------------------------------------------------------------

		// might be needed if using json_encode and we don't want an object

		$out = array_values( $out );

		// -------------------------------------------------------------------------

		return $out;

	}
	// str_to_array()



	/**
	 * split string into an array
	 *
	 * example patterns:
	 * - by new lines or commas	: '/\r\n|\r|\n|,/' (default)
	 * - by space and commas		: '/[\s,]+/'
	 * - by new lines						: '/\r\n|\r|\n/'
	 */
	public static function split_string( $str, $pattern = '/\r\n|\r|\n|,/', $no_empty = true, $unique = true )
	{

		if ( ! $str || ! is_string( $str ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$out = preg_split( $pattern, $str, -1, PREG_SPLIT_NO_EMPTY );

		if ( empty( $out ) )
		{
			return $out;
		}

		// -------------------------------------------------------------------------

		$out = array_map( 'trim', $out );

		// -------------------------------------------------------------------------

		if ( $no_empty )
		{
			$out = array_filter( $out );
		}

		// -------------------------------------------------------------------------

		if ( $unique )
		{
			$out = array_unique( $out );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// split_string()



	/**
	 * @since 1.0.0
	 */
	public static function array_hash( Array $array, $serialize = false )
	{

		self::array_ksort_deep( $array );

		// -------------------------------------------------------------------------

		// @notes: json_encode is faster ref: https://stackoverflow.com/a/7723730

		$array = $serialize ? serialize( $array ) : json_encode( $array );

		// -------------------------------------------------------------------------

		return md5( $array );

	}
	// array_hash()



	/**
	 * equivalent of JavaScript's lodash.get()
	 */
	public static function array_get( $array, $path, $default = null )
	{

		return _wp_array_get( $array, $path, $default );

	}
	// array_get()



	/**
	 * equivalent of JavaScript's lodash.set()
	 */
	public static function array_set( &$array, $path, $value = null )
	{

		_wp_array_set( $array, $path, $value );

	}
	// array_set()



	/**
	 * @internal
	 */
	public static function array_calculate_average( $array )
	{

		return ! empty( $array ) ? array_sum( $array ) / count( $array ) : 0;

	}
	// array_calculate_average()



	/**
	 * @internal
	 */
	public static function array_calculate_median( $array, $count = null )
	{

		if ( empty( $array ) )
		{
			return 0;
		}

		// -------------------------------------------------------------------------

		sort( $array );

		// -------------------------------------------------------------------------

		$count	= $count ?? count( $array );
		$mid		= floor( ( $count - 1 ) / 2 );

		// -------------------------------------------------------------------------

		return ( $array[ $mid ] + $array[ $mid + 1 - $count % 2 ] ) / 2;

	}
	// array_calculate_median()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ARRAYS RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HTML RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function html_attributes( $args, $esc_attr = true )
	{
		/*
		@example:

			$args = array(
				'id'	=> 'some-id',
				'class'	=> array(
					'a-class',
					'another-class',
				),
				'style'	=> array(
					'color'			=> 'red',
					'background'=> 'green',
				),
			);
		*/

		// -------------------------------------------------------------------------

		// if no $args, i'm gone...

		if ( empty( $args ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// only parse if it's an array

		if ( ! is_array( $args ) )
		{
			return $args;
		}

		// -------------------------------------------------------------------------

		// set $out as an array so it can be populated accordingly

		$out = array();

		// -------------------------------------------------------------------------

		// loop through attributes and set $out accordingly

		foreach ( $args as $attr => $val )
		{
			// @notes:
			// using is_null( $val ) instead of ! strlen( $val )
			// because of cases such as <option value="">

			if ( ! is_array( $val ) && is_null( $val ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// check if $val is an array for cases such as "class" and "style"

			if ( is_array( $val ) )
			{
				if ( empty( $val ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				if ( 'style' === $attr )
				{
					$styles = [];

					foreach ( $val as $property => $property_value )
					{
						$styles[] = trim( $property ) . ':' . trim( $property_value );
					}

					if ( ! empty( $styles ) )
					{
						$val = implode( ';', $styles );
					}
					else
					{
						$val = '';
					}
				}
				else
				{
					$val = array_unique( $val );
					$val = implode( ' ', $val );
				}
			}

			// -----------------------------------------------------------------------

			if ( ! is_array( $val ) )
			{
				if ( $esc_attr )
				{
					if ( self::is_url( $val ) )
					{
						if ( function_exists( '\esc_url' ) )
						{
							$val = \esc_url( $val );
						}
					}
					else
					{
						if ( function_exists( '\esc_attr' ) )
						{
							$val = \esc_attr( $val );
						}
					}
				}

				// ---------------------------------------------------------------------

				if ( $attr === $val )
				{
					$out[] = trim( $attr );
				}
				else
				{
					$out[] = sprintf( '%s="%s"', trim( $attr ), trim( $val ) );
				}
			}
		}

		// -------------------------------------------------------------------------

		// split by space if not empty, otherwise, set as empty string

		$out = ! empty( $out ) ? ' ' . implode( ' ', $out ) : '';

		// -------------------------------------------------------------------------

		return $out;

	}
	// html_attributes()



	/**
	 * @since 1.0.0
	 */
	public static function html_wrap( $content, $args = '' )
	{

		$defaults = array(
			'tag'				=> 'div',
			'attr'			=> [],
			'no_empty'	=> true, // no empty content
			'minify'		=> false, // remove tabs and new lines
			//'indent'	=> true, // adds tabs and new lines where applicable
			'tab'				=> '',
		);

		$args = self::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		// return if $content is empty and only none empty $content is allowed

		if ( $no_empty && empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_array( $content ) )
		{
			// remove empty elements

			$content = array_filter( $content );

			// -----------------------------------------------------------------------

			// remove tabs new lines - they'll be added in implode

			$content = array_map( 'trim', $content );

			//$content = array_map( 'ltrim', $content );

			// -----------------------------------------------------------------------

			// split into string

			$content = implode( "\n" . $tab . "\t", $content );
		}

		// -------------------------------------------------------------------------

		// make sure to add only 1 tab and 1 new line after $content

		$content = ltrim( $content, "\t" );

		$content = rtrim( $content, "\n" );

		// -------------------------------------------------------------------------

		// stripslashes - not sure if it should be optional

		$content = stripslashes( $content );

		// -------------------------------------------------------------------------

		$out =
			$tab . '<' . $tag . self::html_attributes( $attr ) . '>' . "\n" .
			$tab . "\t" . $content . "\n" .
			$tab . '</' . $tag . '>';

		// -------------------------------------------------------------------------

		// minify

		if ( ! empty( $minify ) )
		{
			$out = self::minify( $out );
		}
		else
		{
			// auto find selector from $attr id or from the first class

			if ( ! empty( $attr['id'] ) )
			{
				$selector = '#' . $attr['id'];
			}
			elseif ( ! empty( $attr['class'] ) )
			{
				if ( is_array( $attr['class'] ) )
				{
					$selector = '.' . $attr['class'][0];
				}
				else
				{
					$selector = '.' . $attr['class'];
				}
			}

			// -----------------------------------------------------------------------

			// add closing comment if we have a selector

			if ( ! empty( $selector ) )
			{
				$out .= '<!-- ' . $selector . ' -->';
			}

			// -----------------------------------------------------------------------

			// add final new line

			$out .= "\n";
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// html_wrap()



	/**
	 * @since 1.0.0
	 */
	public static function html_comment( $content, $title = '', $args = '' )
	{

		$defaults = array(
			//'title'		=> '', // $this->page_title,
			//'content'	=> '',
			'char'			=> '=',
			'repeat'		=> 36, // 36 | 76
			'link'			=> '', // $this->cls_args['link_url'],
			'style'			=> '1', // 1 | 2
			'tab'				=> '',
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( empty( $content ) )
		{
			return $content;
		}

		// -------------------------------------------------------------------------

		$link			= ! empty( $link ) ? "\n" . $tab . $link : '';
		$repeater	= ( $char && $repeat ) ? "\n" . $tab . str_repeat( $char, $repeat ) : '';

		// -------------------------------------------------------------------------

		switch ( $style ) {

			case '1':

				/*
				<!-- $title - START
				==================================== -->
				$content
				<!-- $title - END
				==================================== -->
				*/

				$end = $start = $tab . '<!-- %1$s - %2$s' . $link . $repeater . ' -->';

				break;

			// -------------------------------------------------------------

			case '2':

				/*
				<!-- $title - START
				==================================== -->
				$content
				<!-- $title - END -->
				*/

				$start= $tab . '<!-- %1$s - %2$s' . $link . $repeater . ' -->';
				$end	= $tab . '<!-- %1$s - %2$s -->';

				break;

			// -------------------------------------------------------------

			default:

				/*
				<!--
				$title - START
				========================================
				-->
				$content
				<!--
				========================================
				$title - END
				-->
				*/

				$start= $tab . '<!--' . "\n" . $tab . '%1$s - %2$s' . $link . $repeater . "\n" . '-->';
				$end	= $tab . '<!--' . $repeater . "\n" . $tab . '%1$s - %2$s' . $link . "\n" . '-->';

				break;
		}

		// -------------------------------------------------------------------------

		$out =
			sprintf( $start, $title, 'START' ) . "\n" .
			$content .
			sprintf( $end, $title, 'END' ) . "\n";

		// -------------------------------------------------------------------------

		return $out;

	}
	// html_comment()



	/**
	 * @since 1.0.0
	 */
	public static function html_link( $content, $attr = '', $minify = true )
	{

		$wrap_args = array(
			'tag'		=> 'a',
			'attr'	=> $attr,
			'minify'=> $minify,
		);

		// -------------------------------------------------------------------------

		return self::html_wrap( $content, $wrap_args );

	}
	// html_link()



	/**
	 * @since 1.0.0
	 */
	public static function highlight_text( $str, $text_to_highlight, $args = [] )
	{

		if ( ! strlen( $str ) )
		{
			return;
		}

		if ( ! strlen( $text_to_highlight ) )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'tag_open'	=> '<span class="highlight_text">',
			'tag_close'	=> '</span>',
		];

		$args = self::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		return preg_replace(
			'/(' . preg_quote( $text_to_highlight, '/' ) .')/i',
			$tag_open . "\\1" . $tag_close,
			$str
		);

	}
	// highlight_text()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HTML RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * USER RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function is_user_logged_in()
	{

		if ( ! function_exists( 'is_user_logged_in' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return is_user_logged_in();

	}
	// is_user_logged_in()



	/**
	 * @since 1.0.0
	 */
	public static function get_current_user()
	{

		if ( ! self::is_user_logged_in() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! function_exists( 'wp_get_current_user' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return wp_get_current_user();

	}
	// get_current_user()



	/**
	 * @since 1.0.0
	 */
	public static function get_current_user_id()
	{

		if ( ! self::is_user_logged_in() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! function_exists( 'get_current_user_id' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return get_current_user_id();

	}
	// get_current_user_id()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * USER RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function admin_page_url( $page, $additional_query = array() )
	{

		// @notes: $page is the admin page menu slug

		if ( ! $page )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$url = \admin_url( 'admin.php' );

		// -------------------------------------------------------------------------

		// query

		$query = array( 'page' => $page );

		if ( $additional_query && is_array( $additional_query ) )
		{
			$query = array_merge( $query, $additional_query );
		}

		// -------------------------------------------------------------------------

		//@notes: same as \admin_url( 'admin.php?page=' . $page );

		$out = add_query_arg( $query, $url );

		// -------------------------------------------------------------------------

		return $out;

	}
	// admin_page_url()



	/**
	 * @since 1.0.0
	 */
	public static function admin_page_link( $text, $page, $query = array() )
	{

		if ( ! $url = self::admin_page_url( $page, $query ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$attr = ['href' => $url];

		// -------------------------------------------------------------------------

		$out = self::html_link( $text, $attr );

		// -------------------------------------------------------------------------

		return $out;

	}
	// admin_page_link()



	/**
	 * @since 1.0.0
	 */
	public static function admin_page_exists( $handle, $sub = false )
	{

		if ( ! is_admin() || ( self::doing_ajax() ) )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		global $menu, $submenu;

		$the_menu = $sub ? $submenu : $menu;

		if ( empty( $the_menu ) )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		foreach ( $the_menu as $key => $item )
		{
			if ( $sub )
			{
				foreach ( $item as $sub_menu )
				{
					if ( 	isset( $sub_menu[2] )
						 && $handle == $sub_menu[2] )
					{
						return true;
					}
				}
			}
			else
			{
				if ( 	isset( $item[2] )
					 && $handle == $item[2] )
				{
					return true;
				}
			}
		}

		// -------------------------------------------------------------------------

		return false;

	}
	// admin_page_exists()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FORMAT RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function format_percent( $number, $fraction_digits = null, $locale = null )
	{

		$locale = $locale ?? get_locale();

		// -------------------------------------------------------------------------

		$formatter = new \NumberFormatter( $locale, \NumberFormatter::PERCENT );

		// -------------------------------------------------------------------------

		if ( isset( $fraction_digits ) )
		{
			$formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, $fraction_digits );
		}

		// -------------------------------------------------------------------------

		return $formatter->format( $number );

	}
	// format_percent()



	/**
	 * @since 1.0.0
	 */
	public static function format_currency( $number, $fraction_digits = null, $currency = 'USD', $locale = null )
	{

		$locale = $locale ?? get_locale();

		// -------------------------------------------------------------------------

		$formatter = new \NumberFormatter( $locale, \NumberFormatter::CURRENCY );

		// -------------------------------------------------------------------------

		// defaults to 2 if USD

		if ( isset( $fraction_digits ) )
		{
			$formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, $fraction_digits );
		}

		// -------------------------------------------------------------------------

		return $formatter->formatCurrency( $number, $currency );

	}
	// format_currency()



	/**
	 * @since 1.0.0
	 */
	public static function number_format_short( $number, $decimals = 1 )
	{

		$out = $number;

		// -------------------------------------------------------------------------

		$abbreviations = array_reverse([
			0		=> '',		//
			3		=> 'K',		// Thousand
			6		=> 'M',		// Million
			9		=> 'B',		// Billion
			12	=> 'T',		// Trillion
			15	=> 'Qa',	// Quadrillion
			18	=> 'Qi',	// Quintillion
		], true);

		foreach ( $abbreviations as $exp => $abbreviation )
		{
			$pow = pow( 10, $exp );

			if ( $number >= $pow )
			{
				$display_number	= $number / $pow;
				$suffix					= $abbreviation;

				// ---------------------------------------------------------------------

				$has_decimals = ( $exp >= 3 && round( $display_number ) < 100 );

				// ---------------------------------------------------------------------

				if ( $has_decimals )
				{
					if ( is_string( $decimals ) )
					{
						// this is mainly the case for appending "+" sign

						$display_number	= floor( $display_number );
						$suffix				 .= $decimals;
						$decimals				= 0;
					}
				}
				else
				{
					$decimals = 0;
				}

				// ---------------------------------------------------------------------

				$formatted = number_format( $display_number, $decimals ) . $suffix;

				break;
			}
		}

		// -------------------------------------------------------------------------

		// remove any ".0" from end

		if ( ! empty( $formatted ) )
		{
			$abbreviations = implode('', array_filter( array_values( $abbreviations ) ) );

			$out = preg_replace( "/\.[0]+([{$abbreviations}]+?)$/i", '$1', $formatted );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// number_format_short()



	/**
	 * alias of number_format_short()
	 */
	public static function abbreviate_number( ...$args )
	{

		return self::number_format_short( ...$args );

	}
	// abbreviate_number()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COLOR RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function get_random_hex_color( $brightness = '' )
	{

		switch ( $brightness )
		{
			case 'light': return sprintf( '#%06X', mt_rand( 0xFFFFFF / 1.5, 0xFFFFFF ) );
			case 'dark'	: return sprintf( '#%06X', mt_rand( 0x000000, 0xFFFFFF / 1.5 ) );
			default			: return sprintf( '#%06X', mt_rand( 0, 0xFFFFFF ) );
		}

	}
	// get_random_hex_color()



	/**
	 * @since 1.0.0
	 */
	public static function string_to_hex_color( $str )
	{

		$code = substr( dechex( crc32( $str ) ), 0, 6 );

		return "#{$code}";

	}
	// string_to_hex_color()



	/**
	 * @since 1.0.0
	 */
	public static function get_contrast_color( $color, $light_color = '#ffffff', $dark_color = '#000000' )
	{

		if ( ! $color )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! self::starts_with( $color, '#' ) )
		{
			$color = "#$color";
		}

		// -------------------------------------------------------------------------

		$r = hexdec( substr( $color, 1, 2 ) );
		$g = hexdec( substr( $color, 3, 2 ) );
		$b = hexdec( substr( $color, 5, 2 ) );

		return ( ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) )/1000 > 128 ) ? $dark_color : $light_color;

	}
	// get_contrast_color()



	/**
	 * used for auto gradient color 2
	 */
	public static function generate_subtle_color( $hex_color, $percentage = 10, $lighten = null )
	{

		if ( is_null( $lighten ) )
		{
			$lighten = self::get_contrast_color( $hex_color, true, false );
		}

		// -------------------------------------------------------------------------

		$percent = $lighten ? (100 - $percentage) / 100 : (100 + $percentage) / 100;

		// -------------------------------------------------------------------------

		$subtle_color	= "#";

		foreach ( str_split( substr( $hex_color, 1 ), 2 ) as $part )
		{
			$color = hexdec( $part );
			$color = $lighten
						 ? min( 255, $color + ( 255 - $color ) * $percentage / 100 )
						 : max( 0, $color - ( $color * $percentage / 100 ) );

			$subtle_color .= str_pad( dechex( (int) $color ), 2, '0', STR_PAD_LEFT );
		}

		// -------------------------------------------------------------------------

		return $subtle_color;

	}
	// generate_subtle_color()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COLOR RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function doing_ajax()
	{

		return ( defined( 'DOING_AJAX' ) && DOING_AJAX );

	}
	// doing_ajax()



	/**
	 * @since 1.0.0
	 */
	public static function print_r( $what = '', $title = '', $echo = true, $exit = false )
	{

		$out = array();

		// -------------------------------------------------------------------------

		if ( $title )
		{
			$out[] = '<h2>' . $title . '</h2>';
		}

		// -------------------------------------------------------------------------

		if ( $what )
		{
			$out[] =
				'<pre>' . "\n" .
					print_r( $what, true ) .
				'</pre>' . "\n";
		}

		// -------------------------------------------------------------------------

		if ( $out )
		{
			$out = implode( "\n", $out );

			// -----------------------------------------------------------------------

			if ( $echo )
			{
				echo $out;
			}
		}

		// -------------------------------------------------------------------------

		if ( $exit )
		{
			exit;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// print_r()



	/**
	 * @since 1.0.0
	 */
	public static function debug_page( $return_text = false )
	{

		$out = array();

		// -------------------------------------------------------------------------

		if ( function_exists( 'timer_stop' ) )
		{
			$out['seconds'] = timer_stop();
		}

		// -------------------------------------------------------------------------

		if ( function_exists( 'get_num_queries' ) )
		{
			$out['num_queries'] = get_num_queries();
		}

		// -------------------------------------------------------------------------

		if ( ! $out )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $return_text )
		{
			$out =
				'seconds    : ' . $out['seconds'] . "\n" .
				'num_queries: ' . $out['num_queries'] . "\n";
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// debug_page()



	/**
	 * @since 1.0.0
	 */
	public static function get_ip_address()
	{

		$keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		];

		// -------------------------------------------------------------------------

		foreach ( $keys as $key )
		{
			if ( false === array_key_exists( $key, $_SERVER ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			foreach ( explode( ',', $_SERVER[ $key ] ) as $ip )
			{
				// trim it just in case

				$ip = trim( $ip );

				// -------------------------------------------------------------------

				// check it

				if ( self::is_ip( $ip, true ) )
				{
					return $ip;
				}
			}
		}

		// -------------------------------------------------------------------------

		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;

	}
	// get_ip_address()



	/**
	 * @since 1.0.0
	 */
	public static function is_exec_cmd( $cmd )
	{

		if ( self::is_exec_enabled() )
		{
			return exec( "which {$cmd}" );
		}

	}
	// is_exec_cmd()



	/**
	 * @since 1.0.0
	 */
	public static function is_exec_enabled()
	{

		static $enabled;

		if ( isset( $enabled ) )
		{
			return $enabled;
		}

		// -------------------------------------------------------------------------

		$enabled = false;

		// -------------------------------------------------------------------------

		if ( ini_get('safe_mode') )
		{
			return $enabled;
		}

		// -------------------------------------------------------------------------

		$disabled_functions = explode( ',', ini_get('disable_functions') );

		// -------------------------------------------------------------------------

		$enabled = ! in_array( 'exec', $disabled_functions );

		// -------------------------------------------------------------------------

		return $enabled;

	}
	// is_exec_enabled()



	/**
	 * @since 1.0.0
	 */
	public static function get_favicon_url( $url, $size = 128, $provider = 'google' )
	{

		$domain = parse_url( $url )['host'];

		// -------------------------------------------------------------------------

		switch ( $provider )
		{
			case 'google'			: return "https://www.google.com/s2/favicons?domain={$domain}&sz={$size}";
			case 'faviconkit'	: return "https://api.faviconkit.com/{$domain}/{$size}";
			case 'icon.horse'	: return "https://icon.horse/icon/{$domain}";
		}

	}
	// get_favicon_url()



	/**
	 * @since 1.0.0
	 */
	public static function get_user_browser_language( $remove_locale = false )
	{

		if ( empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$language = strtok( $_SERVER['HTTP_ACCEPT_LANGUAGE'], ',' );

		// -------------------------------------------------------------------------

		return $remove_locale ? strtok(';') : $language;

	}
	// get_user_browser_language()



	/**
	 * @internal
	 */
	public static function button_spinner()
	{

		ob_start();

		?>
		<span class="button_spinner opacity-0 htmx-indicator absolute inset-0 flex items-center justify-center">
			<svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 100 101" aria-hidden="true" role="status" xmlns="http://www.w3.org/2000/svg">
				<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
				<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
			</svg>
		</span>
		<?php

		return trim( ob_get_clean() );

	}
	// button_spinner()



	/**
	 * @internal
	 */
	public static function get_alert( $args = [] )
	{

		$defaults = [
			'type'				=> '', // info | success | warning | error | default
			'message'			=> '',
			'with_icon'		=> false,
			'bordered'		=> false,
			'dismissible'	=> false,
			'auto_hide'		=> null, // time in millisec
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! $message )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$icon			= null;
		$button		= null;
		$content	= [];
		$attr			= [
			'role'	=> 'alert',
			'class'	=> [
				( $with_icon || $dismissible ) ? 'flex items-center' : null,
				'p-4',
				'text-sm',
				'rounded-lg',
			],
		];

		// -------------------------------------------------------------------------

		switch ( $type )
		{
			case 'info':

				$attr['class'][] = 'text-blue-800 bg-blue-50 dark:bg-neutral-800 dark:text-blue-400';

				if ( $bordered )
				{
					$attr['class'][] = 'border border-blue-300 dark:border-blue-800';
				}

				if ( $with_icon )
				{
					//$icon = null; // todo
				}

				break;

			// -----------------------------------------------------------------------

			case 'success':

				$attr['class'][] = 'text-green-800 bg-green-50 dark:bg-neutral-800 dark:text-green-400';

				if ( $bordered )
				{
					$attr['class'][] = 'border border-green-300 dark:border-green-800';
				}

				if ( $with_icon )
				{
					//$icon = null; // todo
				}

				break;

			// -----------------------------------------------------------------------

			case 'warning':

				$attr['class'][] = 'text-yellow-800 bg-yellow-50 dark:bg-neutral-800 dark:text-yellow-300';

				if ( $bordered )
				{
					$attr['class'][] = 'border border-yellow-300 dark:border-yellow-800';
				}

				if ( $with_icon )
				{
					//$icon = null; // todo
				}

				break;

			// -----------------------------------------------------------------------

			case 'error':

				$attr['class'][] = 'text-red-800 bg-red-50 dark:bg-neutral-800 dark:text-red-400';

				if ( $bordered )
				{
					$attr['class'][] = 'border border-red-300 dark:border-red-800';
				}

				if ( $with_icon )
				{
					//$icon = null; // todo
				}

				break;

			// -----------------------------------------------------------------------

			default:

				$attr['class'][] = 'text-neutral-800 bg-neutral-50 dark:bg-neutral-800 dark:text-neutral-300';

				if ( $bordered )
				{
					$attr['class'][] = 'border border-neutral-300 dark:border-neutral-600';
				}

				if ( $with_icon )
				{
					//$icon = null; // todo
				}

				break;
		}

		// -------------------------------------------------------------------------

		if ( $dismissible )
		{
			//$button = null; // todo
		}

		// -------------------------------------------------------------------------

		if ( $auto_hide )
		{
			$attr['x-data']				= '{show: true}';
			$attr['x-show']				= 'show';
			$attr['x-transition']	= '';
			$attr['x-init']				= sprintf(
				'setTimeout(() => show = false, %d)',
				absint( $auto_hide )
			);
		}

		// -------------------------------------------------------------------------

		$content = array_filter([ $icon, $message, $button ]);

		// -------------------------------------------------------------------------

		return sprintf(
			'<div%s>%s</div>',
			self::html_attributes( $attr ),
			implode( $content )
		);

	}
	// get_alert()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class F
