<?php

namespace DotAim\Settings\Customize;

class Color_Scale_Generator
{

	/**
	 * @internal
	 */
	private const DEFAULT_STOP	= 500;
	private const DEFAULT_STOPS = [0, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950, 1000];



	/**
	 * @internal
	 */
	public static function generate_color_scale( $hex_color )
	{

		$base_color	= self::normalize_hex( $hex_color);
		$base_hsl		= self::hex_to_hsl( $base_color );

		// -------------------------------------------------------------------------

		// Default configuration

		$config = [
			'h'						=> 0, // hue tweak
			's'						=> 0, // saturation tweak
			'l_min'				=> 0,
			'l_max'				=> 100,
			'value_stop'	=> self::DEFAULT_STOP
		];

		// -------------------------------------------------------------------------

		// Generate the scales

		$hue_scale					= self::create_hue_scale( $config['h'], $config['value_stop'] );
		$saturation_scale		= self::create_saturation_scale( $config['s'], $config['value_stop'] );
		$distribution_scale	= self::create_distribution_values(
			$base_hsl['l'],
			$config['value_stop'],
			$config['l_min'],
			$config['l_max'],
		);

		// -------------------------------------------------------------------------

		$swatches = [];

		foreach ( $hue_scale as $index => $scale )
		{
			$stop = $scale['stop'];

			if ( $stop === $config['value_stop'] )
			{
				$swatches[$stop] = $base_color;

				continue;
			}

			// -----------------------------------------------------------------------

			// Apply the tweaks

			$new_h = self::unsigned_modulo($base_hsl['h'] + $hue_scale[$index]['tweak'], 360);
			$new_s = min(max($base_hsl['s'] + $saturation_scale[$index]['tweak'], 0), 100);
			$new_l = min(max($distribution_scale[$index]['tweak'], 0), 100);

			$swatches[$stop] = self::hsl_to_hex($new_h, $new_s, $new_l);
		}

		// -------------------------------------------------------------------------

		// Remove 0 and 1000 stops as they're only used for calculations

		unset( $swatches[0], $swatches[1000] );

		// -------------------------------------------------------------------------

		return $swatches;

	}
	// generate_color_scale()



	/**
	 * @internal
	 */
	private static function create_hue_scale( $tweak = 0, $stop = self::DEFAULT_STOP )
	{

		$stops = self::DEFAULT_STOPS;
		$index = array_search( $stop, $stops );

		if ( $index === false )
		{
			return;
			//throw new Exception("Invalid stop value: {$stop}");
		}

		// -------------------------------------------------------------------------

		$scale = [];

		foreach ( $stops as $current_stop )
		{
			$diff					= abs(array_search($current_stop, $stops) - $index);
			$tweak_value	= $tweak ? $diff * $tweak : 0;

			$scale[] = [
				'stop' => $current_stop,
				'tweak' => $tweak_value
			];
		}

		// -------------------------------------------------------------------------

		return $scale;

	}
	// create_hue_scale()



	/**
	 * @internal
	 */
	private static function create_saturation_scale( $tweak = 0, $stop = self::DEFAULT_STOP )
	{

		$stops = self::DEFAULT_STOPS;
		$index = array_search( $stop, $stops );

		if ( $index === false )
		{
			return;
			//throw new Exception("Invalid stop value: {$stop}");
		}

		// -------------------------------------------------------------------------

		$scale = [];

		foreach ( $stops as $current_stop )
		{
			$diff					= abs( array_search( $current_stop, $stops ) - $index );
			$tweak_value	= $tweak ? round(($diff + 1) * $tweak * (1 + $diff / 10)) : 0;

			$scale[] = [
				'stop'	=> $current_stop,
				'tweak'	=> min( $tweak_value, 100 )
			];
		}

		// -------------------------------------------------------------------------

		return $scale;

	}
	// create_saturation_scale()



	/**
	 * @internal
	 */
	private static function create_distribution_values( $lightness, $stop = self::DEFAULT_STOP, $min = 0, $max = 100 )
	{

		$stops			= self::DEFAULT_STOPS;
		$stop_index	= array_search( $stop, $stops );

		if ( $stop_index === false )
		{
			return;
			//throw new Exception("Invalid stop value: {$stop}");
		}

		// -------------------------------------------------------------------------

		// Create base values

		$values = [
			['stop' => 0		, 'tweak' => $max],
			['stop' => 50		, 'tweak' => 98], // Fixed 98% lightness for shade 50
			['stop' => $stop, 'tweak' => $lightness],
			['stop' => 1000	, 'tweak' => $min]
		];

		// -------------------------------------------------------------------------

		// Create missing stops

		foreach ( $stops as $current_stop )
		{
			if ( 		$current_stop === 0
					 || $current_stop === 50
					 || $current_stop === 1000
					 || $current_stop === $stop )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$diff						= abs( ( $current_stop - $stop ) / 100 );
			$current_index	= array_search( $current_stop, $stops );

			if ( $current_stop < $stop )
			{
				$total_diff	= abs($stop_index - array_search(0, $stops)) - 1;
				$increment	= $max - $lightness;
				$tweak			= ($increment / $total_diff) * $diff + $lightness;
			}
			else
			{
				$total_diff	= abs($stop_index - array_search(1000, $stops)) - 1;
				$increment	= $lightness - $min;
				$tweak			= $lightness - ($increment / $total_diff) * $diff;
			}

			// -----------------------------------------------------------------------

			$values[] = [
				'stop'	=> $current_stop,
				'tweak'	=> round( $tweak )
			];
		}

		// -------------------------------------------------------------------------

		usort( $values, fn( $a, $b ) => $a['stop'] - $b['stop'] );

		// -------------------------------------------------------------------------

		return $values;

	}
	// create_distribution_values()



	/**
	 * @internal
	 */
	private static function normalize_hex( $hex )
	{

		$hex = ltrim( $hex, '#' );

		return '#' . strtoupper( $hex );

	}
	// normalize_hex()



	/**
	 * @internal
	 */
	private static function unsigned_modulo( $x, $n )
	{

		return ( ( $x % $n ) + $n ) % $n;

	}
	// unsigned_modulo()



	/**
	 * @internal
	 */
	private static function hex_to_hsl( $hex )
	{

		$hex = ltrim($hex, '#');

		$r = hexdec(substr($hex, 0, 2)) / 255;
		$g = hexdec(substr($hex, 2, 2)) / 255;
		$b = hexdec(substr($hex, 4, 2)) / 255;

		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		$l = ($max + $min) / 2;

		if ( $max === $min )
		{
			$h = $s = 0;
		}
		else
		{
			$d = $max - $min;
			$s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

			switch( $max )
			{
				case $r:
					$h = ($g - $b) / $d + ($g < $b ? 6 : 0);
					break;
				case $g:
					$h = ($b - $r) / $d + 2;
					break;
				case $b:
					$h = ($r - $g) / $d + 4;
					break;
			}

			$h = $h / 6;
		}

		// -------------------------------------------------------------------------

		return [
			'h' => round( $h * 360 ),
			's' => round( $s * 100 ),
			'l' => round( $l * 100 )
		];

	}
	// hex_to_hsl()



	/**
	 * @internal
	 */
	private static function hsl_to_hex( $h, $s, $l )
	{

		$h = $h / 360;
		$s = $s / 100;
		$l = $l / 100;

		if ($s === 0)
		{
			$r = $g = $b = $l;
		}
		else
		{
			$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
			$p = 2 * $l - $q;

			$r = self::hue_to_rgb($p, $q, $h + 1/3);
			$g = self::hue_to_rgb($p, $q, $h);
			$b = self::hue_to_rgb($p, $q, $h - 1/3);
		}

		// -------------------------------------------------------------------------

		return sprintf('#%02X%02X%02X',
			round($r * 255),
			round($g * 255),
			round($b * 255)
		);

	}
	// hsl_to_hex()



	/**
	 * @internal
	 */
	private static function hue_to_rgb( $p, $q, $t )
	{

		if ($t < 0) $t += 1;
		if ($t > 1) $t -= 1;
		if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
		if ($t < 1/2) return $q;
		if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

		// -------------------------------------------------------------------------

		return $p;

	}
	// hue_to_rgb()

}
// class Color_Scale_Generator
