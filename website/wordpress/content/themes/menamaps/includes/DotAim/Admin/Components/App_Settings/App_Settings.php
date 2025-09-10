<?php
/**
 * =============================================================================
 *
 * -------------------------------- D O T A I M --------------------------------
 *
 * =============================================================================
 *
 * Component Name:
 * Description: 		App Settings Component
 * Version:
 * Component URI:		http://dotaim.com/
 * Author: 					Bassam Mardini
 * Author URI: 			http://dotaim.com/
 * Tags:
 * Has Options: 		true
 * Show UI: 				true
 * Capability:
 * Menu Title:
 * Menu Icon:
 * Menu Position:		3
 * Page Title:
 * Page Sub-Title:
 * Page Icon:				dashicons-admin-settings
 * Parent Slug:
 *
 * -----------------------------------------------------------------------------
 */

namespace DotAim\Admin\Components\App_Settings;

/**
 * @since 1.0.0
 */
class App_Settings extends \DotAim\Admin\Component
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PANELS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function panels_default_order()
	{

		return [
			'general',
			'misc',
			'media',
			'notifications',
			'users',
		];

	}
	// panels_default_order()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PANELS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class App_Settings
