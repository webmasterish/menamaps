<?php

namespace DotAim\Admin;

use DotAim\Base\Singleton;
use DotAim\File;
use DotAim\F;

/**
 * @internal
 */
#[\AllowDynamicProperties]
class Admin extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $components;
	public $components_directories;
	public $components_classes;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



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

		// @notes:
		// can't use in init, ecause if component or panel needs to use it, it would be too late
		//
		//add_action( 'init', [ $this, 'load_active_components' ] );
		$this->load_active_components();

		// -------------------------------------------------------------------------

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );

	}
	// init()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * INIT - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function get_id_from_path( $path )
	{

		return md5( $path );

	}
	// get_id_from_path()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COMPONENTS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function components()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		if ( empty( $this->components_directories() ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		foreach ( $this->components_directories() as $source_dir )
		{
			if ( ! is_dir( $source_dir ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$found_dirs = glob( path_join( $source_dir, '*' ), GLOB_ONLYDIR );

			if ( empty( $found_dirs ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$components = [];

			foreach ( $found_dirs as $dir )
			{
				if ( $data = $this->get_component_data( path_join( $source_dir, $dir ) ) )
				{
					$components[ $data['id'] ] = $data;
				}
			}

			if ( empty( $components ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// sort by name asc and keep array keys

			uasort( $components, fn( $a, $b ) => $a['name'] <=> $b['name'] );

			// -----------------------------------------------------------------------

			$this->{__FUNCTION__} = array_merge( $this->{__FUNCTION__}, $components );
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// components()



	/**
	 * @since 1.0.0
	 */
	public function components_directories()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$components_directories = $this->apply_filters(
			'components_directories',
			$this->core->admin_settings['components_directories']
		);

		if ( empty( $components_directories ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$components_directories = array_filter( array_map( 'trim', $components_directories ) );

		if ( empty( $components_directories ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = array_unique( $components_directories );

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// components_directories()



	/**
	 * @since 1.0.0
	 */
	public function get_component_data( $dir )
	{

		if ( ! $dir )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$name	= basename( $dir );
		$file	= path_join( $dir, "{$name}.php" );

		if ( ! is_readable( $file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		// should use a unique key because we could have components
		// with the same name but placed in diferent folders

		$id = $this->get_id_from_path( $file );

		// -------------------------------------------------------------------------

		$cache_key = __FUNCTION__ . "_{$id}";

		if ( isset( $this->{$cache_key} ) )
		{
			return $this->{$cache_key};
		}

		$this->{$cache_key} = [];

		// -------------------------------------------------------------------------

		$default_headers = [
			'class_name'		=> 'Class Name',
			'name'					=> 'Component Name',
			'description'		=> 'Description',
			'version'				=> 'Version',
			'component_uri'	=> 'Component URI',
			'author'				=> 'Author',
			'author_uri'		=> 'Author URI',
			'tags'					=> 'Tags',
			'show_ui'				=> 'Show UI',
			'has_options'		=> 'Has Options',
			'capability'		=> 'Capability',
			'menu_title'		=> 'Menu Title',
			'menu_icon'			=> 'Menu Icon',
			'menu_position'	=> 'Menu Position',
			'page_title'		=> 'Page Title',
			'page_subtitle'	=> 'Page Sub-Title',
			'page_icon'			=> 'Page Icon',
			'parent_slug'		=> 'Parent Slug',
			'active'				=> 'Active',
			// @notes:
			// default_data['active'] is true,
			// to deactivate set to 0 in component file
		];

		// -------------------------------------------------------------------------

		$file_data = get_file_data( $file, $default_headers );

		if ( empty( $file_data ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$menu_slug			= str_replace( '[-_\s]+', '_', $name );
		$menu_slug			= preg_replace( '/[\W]/', '', $menu_slug );
		$sanitized_name	= strtolower( $menu_slug ); // mainly used for css/js
		$menu_slug			= "{$sanitized_name}_{$id}";

		// -------------------------------------------------------------------------

		$default_data = [
			'id'							=> $id,
			'name'						=> '',
			'humanized_name'	=> '',
			'sanitized_name'	=> $sanitized_name,
			'dir'							=> $dir,
			'file'						=> $file,
			'menu_slug'				=> $menu_slug,
			'db_option_name'	=> "{$this->core->admin_settings['db_option_name']}_{$menu_slug}",
			'parent_slug'			=> false, // $this->core->admin_settings['menu_slug'],
			'capability'			=> $this->core->admin_settings['capability'],
			'version'					=> $this->core->version,
			'callback_func'		=> 'admin_page_render',
			'active'					=> true,	// @todo: this requires dashboard management
			//'order'					=> 0,			// @consider: this is also part of dashboard
		];

		$data = wp_parse_args( array_filter( $file_data, 'strlen' ), $default_data );

		// -------------------------------------------------------------------------

		$allowed_tags = [
			'a'				=> [ 'title' => [], 'href' => [] ],
			'abbr'		=> [ 'title' => [] ],
			'acronym'	=> [ 'title' => [] ],
			'code'		=> [],
			'em'			=> [],
			'strong'	=> [],
			'span'		=> [],
		];

		// -------------------------------------------------------------------------

		if ( empty( $data['class_name'] ) )
		{
			$class_name_from_file = File::get_class_name_from_file( $file );

			if ( ! empty( $class_name_from_file['full_name'] ) )
			{
				$data['class_name'] = $class_name_from_file['full_name'];
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $data['name'] ) )
		{
			$data['name']						= $name;
			$data['humanized_name']	= F::humanize( $name );
		}
		else
		{
			$data['name']						= wp_kses( $data['name'], $allowed_tags );
			$data['humanized_name'] = $data['name'];
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['description'] ) )
		{
			$data['description'] = wptexturize( wp_kses( $data['description'], $allowed_tags ) );
		}

		// -------------------------------------------------------------------------

		$data['version'] = wp_kses( $data['version'], $allowed_tags );

		// -------------------------------------------------------------------------

		if ( ! empty( $data['component_uri'] ) )
		{
			$data['component_uri'] = esc_url( $data['component_uri'] );
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['author_uri'] ) )
		{
			$data['author_uri']	= esc_url( $data['author_uri'] );
		}

		if ( empty( $data['author'] ) )
		{
			$data['author'] = $data['author_name'] = $this->__('Anonymous');
		}
		else
		{
			$data['author_name'] = wp_kses( $data['author'], $allowed_tags );

			if ( empty( $data['author_uri'] ) )
			{
				$data['author'] = $data['author_name'];
			}
			else
			{
				$data['author'] = sprintf(
					'<a href="%1$s" title="%2$s">%3$s</a>',
					$data['author_uri'],
					$this->__('Visit author homepage'),
					$data['author_name']
				);
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['tags'] ) )
		{
			$data['tags'] = array_map( 'trim', explode( ',', wp_kses( $data['tags'], [] ) ) );
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['menu_title'] ) )
		{
			$data['menu_title'] = wp_kses( $data['menu_title'], $allowed_tags );
		}
		else
		{
			$data['menu_title'] = $data['humanized_name'];
		}

		// -------------------------------------------------------------------------

		if ( empty( $data['menu_icon'] ) && ! empty( $data['page_icon'] ) )
		{
			$data['menu_icon'] = $data['page_icon'];
		}

		// -------------------------------------------------------------------------

		if ( isset( $data['menu_position'] ) )
		{
			$data['menu_position'] = $data['menu_position'];
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['page_title'] ) )
		{
			$data['page_title'] = wp_kses( $data['page_title'], $allowed_tags );
		}
		else
		{
			$data['page_title'] = $data['menu_title'];
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $data['page_subtitle'] ) )
		{
			$data['page_subtitle'] = wp_kses( $data['page_subtitle'], $allowed_tags );
		}

		// -------------------------------------------------------------------------

		if ( empty( $data['page_icon'] ) && ! empty( $data['menu_icon'] ) )
		{
			$data['page_icon'] = $data['menu_icon'];
		}

		// -------------------------------------------------------------------------

		$this->{$cache_key} = $data;

		// -------------------------------------------------------------------------

		return $this->{$cache_key};

	}
	// get_component_data()



	/**
	 * @since 1.0.0
	 */
	public function load_active_components()
	{

		$components = $this->components();

		if ( empty( $components ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->components_classes = [];

		foreach ( $components as $component )
		{
			if ( 		empty( $component['active'] )
					 || empty( $component['file'] )
					 || empty( $component['class_name'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// @notes:
			// components should be loaded regardless of ussr capabilities
			// current_user_can is only applicable when rendering an admin page
			/*
			if ( ! empty( $component['capability'] ) )
			{
				if ( ! current_user_can( $component['capability'] ) )
				{
					continue;
				}
			}
			*/

			// -----------------------------------------------------------------------

			if ( ! is_readable( $component['file'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! class_exists( $component['class_name'] ) )
			{
				require_once( $component['file'] );
			}

			// -----------------------------------------------------------------------

			$this->components_classes[ $component['id'] ] = new $component['class_name'](
				$this->core,
				$component
			);
		}

	}
	// load_active_components()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COMPONENTS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SCRIPTS & STYLES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function enqueue()
	{

		$this->enqueue_styles();

		// -------------------------------------------------------------------------

		$this->enqueue_scripts();

	}
	// enqueue()



	/**
	 * @since 1.0.0
	 */
	protected function enqueue_styles()
	{

		$files = [

			$this->dir_css('general.css') => null,

			// -----------------------------------------------------------------------

			$this->dir_css('component_page.css') => [
				'dashicons',
				'wp-color-picker',
				'media-views',
			],

		];

		// -------------------------------------------------------------------------

		$paths					= array_keys( $files );
		$dependencies		= $this->enqueue_get_dependencies( $files );
		$dest_filename	= 'compiled_styles';
		$handle					= "{$this->prefix}{$dest_filename}";
		$version				= $this->version;
		$media					= 'all';

		// -------------------------------------------------------------------------

		$dest_file	= "{$dest_filename}.css";
		$args				= [
			'dest_dir'				=> $this->dir_css(),
			'dest_dir_url'		=> $this->url_css(),
			'minify'					=> true,
			'remove_comments'	=> true,
			'add_file_names'	=> true,
			'return_file_url'	=> true,
		];

		if ( $url = File::concatenate_files( $paths, $dest_file, $args ) )
		{
			wp_enqueue_style( $handle, $url, $dependencies, $version, $media );
		}

	}
	// enqueue_styles()



	/**
	 * @since 1.0.0
	 */
	protected function enqueue_scripts()
	{

		$dependencies_media = ['media-upload', 'media-editor'];

		// -------------------------------------------------------------------------

		$files = [

			$this->dir_js('dotaim.js') => ['jquery', 'lodash'],

			// -----------------------------------------------------------------------

			$this->dir_js('dotaim_field_conditional_display.js') => ['jquery'],

			// -----------------------------------------------------------------------

			$this->dir_js('dotaim_field_media.js') => $dependencies_media,

			// -----------------------------------------------------------------------

			$this->dir_js('dotaim_field_array_field.js') => ['jquery', 'jquery-ui-sortable'],

			// -----------------------------------------------------------------------

			$this->dir_js('fields.js') => ['jquery', 'wp-color-picker'],

			// -----------------------------------------------------------------------

			$this->dir_js('component_page.js') => [
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
			],

		];

		// -------------------------------------------------------------------------

		$paths					= array_keys( $files );
		$dependencies		= $this->enqueue_get_dependencies( $files );
		$dest_filename	= 'compiled_scripts';
		$handle					= "{$this->prefix}{$dest_filename}";
		$version				= $this->version;
		$in_footer			= true;

		// -------------------------------------------------------------------------

		$dest_file	= "{$dest_filename}.js";
		$args				= [
			'dest_dir'				=> $this->dir_js(),
			'dest_dir_url'		=> $this->url_js(),
			'minify'					=> false,
			'remove_comments'	=> false,
			'add_file_names'	=> true,
			'return_file_url'	=> true,
		];

		if ( $url = File::concatenate_files( $paths, $dest_file, $args ) )
		{
			if ( 		! empty( $dependencies )
					 && array_intersect( $dependencies_media, $dependencies ) )
			{
				wp_enqueue_media();
			}

			// -----------------------------------------------------------------------

			wp_enqueue_script( $handle, $url, $dependencies, $version, $in_footer );

			// -----------------------------------------------------------------------

			wp_localize_script(
				$handle,
				$object_name	= $this->id,
				$l10n 				= [
					'version'				=> $this->version,
					'is_local_dev'	=> $this->core->is_local_dev(),
				]
			);
		}

	}
	// enqueue_scripts()



	/**
	 * @since 1.0.0
	 */
	public function enqueue_get_dependencies( $files )
	{

		$dependencies = [];

		foreach ( array_values( $files ) as $file_dependencies )
		{
			if ( $file_dependencies )
			{
				if ( is_array( $file_dependencies ) )
				{
					$dependencies = array_merge( $dependencies, $file_dependencies );
				}
				else
				{
					$dependencies[] = $file_dependencies;
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $dependencies ) )
		{
			$dependencies = array_unique( array_filter( $dependencies ) );
		}

		// -------------------------------------------------------------------------

		return $dependencies;

	}
	// enqueue_get_dependencies()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SCRIPTS & STYLES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Admin
