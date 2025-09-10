<?php

namespace DotAim\Nav_Menus;

use DotAim\Base\Singleton;
use DotAim\F;

final class Nav_Menus extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * INIT - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function init()
	{

		$this->hooks();

	}
	// init()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * INIT - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function hooks()
	{

		$this->core->add_action(
			'add_theme_support',
			[$this, 'register_nav_menus']
		);

		// -------------------------------------------------------------------------

		add_filter(
			'nav_menu_css_class',
			[$this, 'nav_menu_css_class'],
			10,
			3
		);

		add_filter(
			'nav_menu_link_attributes',
			[$this, 'nav_menu_link_attributes'],
			10,
			3
		);

	}
	// hooks()



	/**
	 * @since 1.0.0
	 */
	public function register_nav_menus()
	{

		$locations = [
			'header_nav' => $this->__( 'Header Nav' ),
			'footer_nav' => $this->__( 'Footer Nav' ),
		];

		// -------------------------------------------------------------------------

		$locations = $this->apply_filters( __FUNCTION__ . '_locations', $locations );

		// -------------------------------------------------------------------------

		if ( $locations )
		{
			register_nav_menus( $locations );

			// -----------------------------------------------------------------------

			add_theme_support( 'menus' );
		}

	}
	// register_nav_menus()



	/**
	 * this is related to list item
	 */
	public function nav_menu_css_class( $classes, $item, $args )
	{

		// applicable to all

		$classes[] = "{$args->theme_location}_item";

		// -------------------------------------------------------------------------

		return $classes;

	}
	// nav_menu_css_class()



	/**
	 * this is related to anchor links in li
	 */
	public function nav_menu_link_attributes( $atts, $menu_item, $args )
	{

		$classes = [];

		// -------------------------------------------------------------------------

		// set it in wp_nav_menu as custom args

		if ( isset( $args->add_a_class ) )
		{
			$classes[] = $args->add_a_class;
		}

		if ( isset( $args->add_a_current_class ) && $menu_item->current )
		{
			$classes[] = $args->add_a_current_class;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $classes ) )
		{
			$atts['class'] = implode( ' ', $classes );
		}

		// -------------------------------------------------------------------------

		return $atts;

	}
	// nav_menu_link_attributes()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Nav_Menus
