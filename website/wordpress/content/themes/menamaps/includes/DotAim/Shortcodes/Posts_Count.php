<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Posts_Count extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		$defaults = [
			'post_type' => 'post',
			'status'		=> 'publish',
		];

		$atts = shortcode_atts( $defaults, $atts );

		extract( $atts );

		// -------------------------------------------------------------------------

		if ( ! post_type_exists( $post_type ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = wp_count_posts( $post_type );

		// -------------------------------------------------------------------------

		if ( empty( $out->{$status} ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = $out->{$status};

		// -------------------------------------------------------------------------

		return $out;

	}
	// output()

}
// class Posts_Count
