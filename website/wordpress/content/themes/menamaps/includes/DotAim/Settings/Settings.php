<?php

namespace DotAim\Settings;

use DotAim\F;
use DotAim\Base\Singleton;

final class Settings extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $db_option_name;

	/**
	 * @since 1.0.0
	 */
	public $Customize;
	public $get_components_settings;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function __construct( $core )
	{

		$this->db_option_name	= $core->db_option_name;

		// -------------------------------------------------------------------------

		parent::__construct( $core );

		// -------------------------------------------------------------------------

		// Customize class needs to be initiated so it can register sections

		$this->Customize();

	}
	// __construct()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GETTERS & SETTERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function post_type_panel_id( $post_type = null )
	{

		$post_type ??= get_post_type();

		return "post_types_panel_section_{$post_type}";

	}
	// post_type_panel_id()



	/**
	 * @since 1.0.0
	 */
	public function taxonomy_panel_id( $taxonomy = null )
	{

		$taxonomy ??= get_queried_object()->taxonomy;

		return "taxonomies_panel_section_{$taxonomy}";

	}
	// taxonomy_panel_id()



	/**
	 * @since 1.0.0
	 */
	public function get( $setting = '', $default = false )
	{

		return $this->Customize()->{__FUNCTION__}( $setting, $default );

	}
	// get()



	/**
	 * @since 1.0.0
	 */
	public function get_components_settings()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		// default to empty array

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$compnents = $this->core->Admin()->components();

		if ( empty( $compnents ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		foreach ( $compnents as $component )
		{
			if ( 		empty( $component['active'] )
					 || empty( $component['id'] )
					 || empty( $component['name'] )
					 || empty( $component['db_option_name'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$saved_options = get_option( $component['db_option_name'] );

			// -----------------------------------------------------------------------

			// @todo:
			// should use "id" instead of "name" because the id is unique
			// the problem is using the "id" as key is not human recognizable
			//
			// also should consider "sanitized_name" instead of "name"

			$this->{__FUNCTION__}[ $component['name'] ] = ! empty( $saved_options['settings'] )
																									? $saved_options['settings']
																									: [];
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// get_components_settings()



	/**
	 * @since 1.0.0
	 */
	public function get_component_settings( $component, $args = [] )
	{

		if ( ! $component )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'section'				=> null,
			'group'					=> null,
			'option'				=> null,
			'stripslashes'	=> true,
			'do_shortcode'	=> true,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! $components_settings = $this->get_components_settings() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( empty( $components_settings[ $component ] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$settings = $components_settings[ $component ];

		// -------------------------------------------------------------------------

		// section not set? return evrything

		if ( ! isset( $section ) )
		{
			return $settings;
		}

		// -------------------------------------------------------------------------

		// section is set but doesn't exist in db? i'm gone

		if ( ! isset( $settings[ $section ] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// group not set? return section

		if ( ! isset( $group ) )
		{
			return $settings[ $section ];
		}

		// -------------------------------------------------------------------------

		// group is set but doesn't exist in db? i'm gone

		if ( ! isset( $settings[ $section ][ $group ] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// option not set? return group

		if ( ! isset( $option ) )
		{
			$group_settings = $settings[ $section ][ $group ];

			// -----------------------------------------------------------------------

			if ( $group_settings && $stripslashes )
			{
				$group_settings = stripslashes_deep( $group_settings );
			}

			// -----------------------------------------------------------------------

			return $group_settings;
		}

		// -------------------------------------------------------------------------

		// option is set but doesn't exist in db? i'm gone

		if ( ! isset( $settings[ $section ][ $group ][ $option ] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// no value, i'm gone

		if ( ! $out = $settings[ $section ][ $group ][ $option ] )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// the option exists and has a value, let's see how it needs to be outputed

		if ( $stripslashes )
		{
			$out = stripslashes_deep( $out );
		}

		// -------------------------------------------------------------------------

		// do shortcode?

		if ( $do_shortcode )
		{
			if ( ! is_array( $out ) )
			{
				$out = do_shortcode( $out );
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_component_settings()



	/**
	 * @since 1.0.0
	 */
	public function get_component_app_settings( $section = null, $group = null, $option = null, $stripslashes = true )
	{

		$args = [
			'section'			=> $section,
			'group'				=> $group,
			'option'			=> $option,
			'stripslashes'=> $stripslashes,
		];

		// -------------------------------------------------------------------------

		return $this->get_component_settings( 'App_Settings', $args );

	}
	// get_component_app_settings()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GETTERS & SETTERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DEFAULTS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function defaults_add()
	{

		if ( $options = get_option( $this->db_option_name ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$options_file = $this->core->dir_data('default_options.txt');

		if ( ! is_readable( $options_file ) )
		{
			return;
		}

		if ( ! $raw_data = file_get_contents( $options_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$data = maybe_unserialize( $raw_data );

		if ( empty( $data['options'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// can be used to redirect to a particular install page in admin

		$added = false;

		// -------------------------------------------------------------------------

		foreach ( $data['options'] as $option_name => $option_value )
		{
			if ( empty( $option_value ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// delete it so we'll add imported one instead

			delete_option( $option_name );

			// -----------------------------------------------------------------------

			// update modification date

			$option_value['info']['last_modified'] = gmdate( 'Y-m-d H:i:s', time() );

			// -----------------------------------------------------------------------

			// add it

			add_option( $option_name, $option_value );

			// -----------------------------------------------------------------------

			// set it to true

			$added = true;
		}

		// -------------------------------------------------------------------------

		return $added;

	}
	// defaults_add()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DEFAULTS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function Customize()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = new Customize\Customize( $this->core );

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// Customize()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Settings
