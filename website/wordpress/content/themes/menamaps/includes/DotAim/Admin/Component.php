<?php

namespace DotAim\Admin;

use DotAim\F;
use DotAim\File;

/**
 * @internal
 */
class Component
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $core;
	public $args;
	public $class_name;
	public $css_class;
	public $prefix;
	public $admin_page_hook;
	public $form_nonce_action;
	public $form_nonce_name;
	public $form_posting_msg;
	public $options;
	public $options_user_prefs_nonce_action;
	public $options_user_prefs_nonce;
	public $panels;
	public $dir;
	public $url;

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
	public function __construct( $core, $args )
	{

		$this->core				= $core;
		$this->args				= $args;
		$this->class_name	= $this->core->NS()->class_name( get_class( $this ) );

		// -------------------------------------------------------------------------

		$class_name_for_css	= strtolower( $this->core->NS()->class_name( __CLASS__ ) );
		$this->css_class		= "{$this->core->css_prefix}{$class_name_for_css}";

		// -------------------------------------------------------------------------

		$this->prefix = "{$this->sanitized_name}_{$this->id}_";

		// -------------------------------------------------------------------------

		$this->form_nonce_action	= "{$this->id}_action";
		$this->form_nonce_name		= "{$this->id}_nonce";

		if ( is_admin() && $this->has_options && $this->db_option_name )
		{
			add_action(
				"wp_ajax_{$this->form_nonce_action}",
				[ $this, 'form_posting_process_ajax' ]
			);
		}

		// -------------------------------------------------------------------------

		if ( is_admin() )
		{
			$this->options_user_prefs_nonce_action	= "{$this->id}_options_user_prefs_action";
			$this->options_user_prefs_nonce					= wp_create_nonce( $this->options_user_prefs_nonce_action );

			add_action(
				"wp_ajax_{$this->options_user_prefs_nonce_action}",
				[ $this, 'options_user_prefs_process_ajax' ]
			);
		}

		// -------------------------------------------------------------------------

		$this->init();

	}
	// __construct()



	/**
	 * @since 1.0.0
	 */
	public function __get( $name )
	{

		if ( ! isset( $this->{$name} ) && isset( $this->args[ $name ] ) )
		{
			return $this->args[ $name ];
		}

	}
	// __get()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
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

		// make sure panels are loaded even if we don't have a rendering of page

		// @notes:
		// can't use in init, ecause if component or panel needs to use it, it would be too late
		//
		//add_action( 'init', [ $this, 'panels' ] );
		$this->panels();

		// -------------------------------------------------------------------------

		add_action( 'admin_menu', [ $this, 'admin_page_add' ] );

		// -------------------------------------------------------------------------

		add_action( 'admin_init', [ $this, 'form_posting_process' ] );

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
	 * @internal
	 */
	public function __( ...$args ) { return $this->core->__( ...$args ); }



	/**
	 * @internal
	 */
	public function in_component_page()
	{

		if ( 		is_admin()
				 && isset( $_GET['page'] )
				 && $this->menu_slug )
		{
			return ( $this->menu_slug === $_GET['page'] );
		}

	}
	// in_component_page()



	/**
	 * @internal
	 */
	protected function call_method( $method_name, ...$args )
	{

		if ( method_exists( $this, $method_name ) )
		{
			return $this->{$method_name}( ...$args );
		}

	}
	// call_method()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN PAGE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function admin_page_add()
	{

		if ( ! $this->show_ui )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$callback_fn = [ $this, $this->callback_func ];

		if ( ! is_callable( $callback_fn ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $this->parent_slug )
		{
			$this->admin_page_hook = add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				$callback_fn,
				$this->menu_icon,
				$this->menu_position
			);
		}
		else
		{
			$this->admin_page_hook = add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				$callback_fn
			);
		}

		// -------------------------------------------------------------------------

		if ( empty( $this->admin_page_hook ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$fn = [ $this, 'admin_page_load' ];

		if ( is_callable( $fn ) )
		{
			add_action( "load-{$this->admin_page_hook}", $fn );
		}

	}
	// admin_page_add()



	/**
	 * @since 1.0.0
	 */
	public function admin_page_load()
	{

		if ( empty( $this->admin_page_hook ) )
		{
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen = get_current_screen() )
		{
			return;
		}

		if ( $screen->id != $this->admin_page_hook )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

		// -------------------------------------------------------------------------

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_page_enqueue' ] );

		// -------------------------------------------------------------------------

		// @consider using here or as needed in each individual component

		/*
		$actions = [
			'admin_enqueue_scripts',
			'admin_print_styles',
			'admin_print_scripts',
			'admin_head',
			'admin_footer',
			'admin_print_footer_scripts',
		];

		foreach ( $actions as $action )
		{
			$fn = [ $this, $action ];

			if ( is_callable( $fn ) )
			{
				add_action( $action, $fn );
			}
		}
		*/

		// -------------------------------------------------------------------------

		return true;

	}
	// admin_page_load()



	/**
	 * @since 1.0.0
	 */
	public function admin_page_enqueue()
	{

		$this->admin_page_enqueue_styles();

		// -------------------------------------------------------------------------

		$this->admin_page_enqueue_scripts();

	}
	// admin_page_enqueue()



	/**
	 * use in sub-class as needed
	public function admin_page_styles_files()
	{

		// example:

		return [
			$this->dir_css('some_style.css')		=> null,
			$this->dir_css('another_style.css')	=> ['dashicons', 'media-views'],
		];

	}
	// admin_page_styles_files()
	 */



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_enqueue_styles()
	{

		if ( ! $files = $this->call_method('admin_page_styles_files') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$paths					= array_keys( $files );
		$dependencies		= $this->core->Admin()->enqueue_get_dependencies( $files );
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
	// admin_page_enqueue_styles()



	/**
	 * use in sub-class as needed
	public function admin_page_scripts_files()
	{

		// example:

		return [
			$this->dir_js('some_script.js')			=> ['jquery', 'lodash'],
			$this->dir_js('another_script.js')	=> ['media-upload', 'media-editor'],
		];

	}
	// admin_page_scripts_files()
	 */



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_enqueue_scripts()
	{

		if ( ! $files = $this->call_method('admin_page_scripts_files') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$paths					= array_keys( $files );
		$dependencies		= $this->core->Admin()->enqueue_get_dependencies( $files );
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

		$dependencies_media = ['media-upload', 'media-editor'];

		if ( $url = File::concatenate_files( $paths, $dest_file, $args ) )
		{
			if ( 		! empty( $dependencies )
					 && array_intersect( $dependencies_media, $dependencies ) )
			{
				wp_enqueue_media();
			}

			// -----------------------------------------------------------------------

			wp_enqueue_script( $handle, $url, $dependencies, $version, $in_footer );
		}

	}
	// admin_page_enqueue_scripts()



	/**
	 * @since 1.0.0
	 */
	public function admin_body_class( $class )
	{

		$classes = [
			$this->css_class,
			"{$this->css_class}_{$this->sanitized_name}",
			"{$this->css_class}_{$this->id}",
			"{$this->css_class}_{$this->menu_slug}",

			// this prefixes it with theme name
			"{$this->core->prefix}component",
		];

		// -------------------------------------------------------------------------

		$classes = implode( ' ', $classes );

		// -------------------------------------------------------------------------

		if ( $class )
		{
			$classes = "{$class} {$classes}";
		}

		// -------------------------------------------------------------------------

		return $classes;

	}
	// admin_body_class()



	/**
	 * @since 1.0.0
	 */
	public function admin_page_render()
	{

		if ( wp_doing_ajax() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$content = [];

		// -------------------------------------------------------------------------

		if ( $header = $this->admin_page_header() )
		{
			$content[] = $header;
		}

		// -------------------------------------------------------------------------

		if ( $body = $this->admin_page_body() )
		{
			$content[] = $body;
		}

		// -------------------------------------------------------------------------

		if ( $footer = $this->admin_page_footer() )
		{
			$content[] = $footer;
		}

		// -------------------------------------------------------------------------

		if ( empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$content	= implode( $content );
		$attr			= F::html_attributes([
			'id'		=> "{$this->css_class}_{$this->sanitized_name}",
			'class'	=> [
				"{$this->css_class}_page",
				'wrap',
			],
		]);

		echo "<div{$attr}>{$content}</div>";

	}
	// admin_page_render()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_header( $echo = true )
	{

		$content = [];

		// -------------------------------------------------------------------------

		if ( $title = $this->admin_page_header_title() )
		{
			$content[] = $title;
		}

		// -------------------------------------------------------------------------

		if ( $notice = $this->admin_page_header_notice() )
		{
			$content[] = $notice;
		}

		// -------------------------------------------------------------------------

		if ( $description = $this->admin_page_header_description() )
		{
			$content[] = $description;
		}

		// -------------------------------------------------------------------------

		if ( empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return implode( $content );

	}
	// admin_page_header()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_header_title()
	{

		if ( ! $this->page_title )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$content		= [];
		$css_class	= ["{$this->css_class}_header_title"];

		// -------------------------------------------------------------------------

		if ( $this->page_icon )
		{
			/*
			$attr = F::html_attributes([
				'class' => [
					"{$this->css_class}_header_icon",
					"{$this->css_class}_dashicons",
					$this->page_icon
				],
			]);

			$content[]		= "<span{$attr}>&nbsp;</span>";
			$css_class[]	= 'with_icon';
			*/
			$css_class[] = "dashicons-before {$this->page_icon}";
		}

		// -------------------------------------------------------------------------

		$attr = F::html_attributes([
			'class' => ["{$this->css_class}_header_title_text"],
		]);

		$content[] = "<span{$attr}>{$this->page_title}</span>";

		// -------------------------------------------------------------------------

		if ( $this->page_subtitle )
		{
			$attr = F::html_attributes([
				'class' => [
					"{$this->css_class}_header_subtitle",
					'subtitle'
				],
			]);

			$content[]		= "<span{$attr}>{$this->page_subtitle}</span>";
			$css_class[]	= 'with_subtitle';
		}

		// -------------------------------------------------------------------------

		$content	= implode( $content );
		$attr			= F::html_attributes([ 'class' => $css_class ]);

		return "<h2{$attr}>{$content}</h2>";

	}
	// admin_page_header_title()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_header_notice()
	{

		if ( ! empty( $_REQUEST['msg'] ) )
		{
			$out = $this->notice_markup([
				'success'	=> ! empty( $_REQUEST['success'] ),
				'error'		=> ! empty( $_REQUEST['error'] ),
				'message'	=> '<p><strong>' . urldecode( $_REQUEST['msg'] ) . '</strong></p>',
			]);
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $this->form_posting_msg ) )
		{
			$out = $this->form_posting_msg;

			$this->form_posting_msg = '';
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $out ) )
		{
			return $out;
		}

	}
	// admin_page_header_notice()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_header_description()
	{

		if ( ! $this->description )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$attr = F::html_attributes([
			'class' => [
				"{$this->css_class}_header_description",
				'description',
			],
		]);

		return "<div{$attr}>{$this->description}</div>";

	}
	// admin_page_header_description()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_body()
	{

		$fn = 'admin_page_body_content';

		if ( method_exists( $this, $fn ) )
		{
			if ( $content = $this->{$fn}() )
			{
				$attr = F::html_attributes([
					'class' => ["{$this->css_class}_body"],
				]);

				return "<div{$attr}>{$content}</div>";
			}
		}

	}
	// admin_page_body()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_body_content()
	{

		$panels = $this->panels();

		if ( empty( $panels ) )
		{
			return $this->content_none_markup();
		}

		// -------------------------------------------------------------------------

		if ( $active_tab = $this->options_user_prefs(['panels', 'current_id']) )
		{
			// ensure that the panel exists

			if ( ! isset( $panels[ $active_tab ] ) )
			{
				$active_tab = null;
			}
		}

		// -------------------------------------------------------------------------

		$panels_markup	= [];
		$nav_tabs				= [];

		foreach ( $panels as $panel )
		{
			if ( ! $panel_markup = $this->core->call_object_func( $panel, 'markup' ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$panels_markup[] = $panel_markup;

			// -----------------------------------------------------------------------

			$nav_tab_attr_class = ['nav-tab'];

			// set to first one if not set

			if ( is_null( $active_tab ) || ! strlen( $active_tab ) )
			{
				$active_tab = $panel->id;
			}

			if ( $active_tab === $panel->id )
			{
				$nav_tab_attr_class[] = 'nav-tab-active';
			}

			if ( $panel->icon )
			{
				$nav_tab_attr_class[] = "dashicons-before {$panel->icon}";
			}

			$nav_tab_attr = F::html_attributes([
				'href'	=> "#{$panel->css_class}_{$panel->id}",
				'class'	=> $nav_tab_attr_class,
			]);

			// -----------------------------------------------------------------------

			$nav_tabs[] = sprintf(
				'<li data-panel_id="%s"><a%s>%s</a></li>',
				$panel->id,
				$nav_tab_attr,
				$panel->title
			);
		}

		if ( empty( $panels_markup ) )
		{
			return $this->content_none_markup();
		}

		// -------------------------------------------------------------------------

		$content = implode([
			'<h2 class="nav-tab-wrapper">' .
				'<ul>' . implode( $nav_tabs ) . '</ul>' .
			'</h2>',
			implode( $panels_markup ),
		]);

		$attr = F::html_attributes([
			'class'	=> [
				"{$this->css_class}_panels",
				'ui-tabs',
				'ui-widget',
				'ui-widget-content',
			],
			'data-options_user_prefs_nonce_action'	=> $this->options_user_prefs_nonce_action,
			'data-options_user_prefs_nonce'					=> $this->options_user_prefs_nonce,
		]);

		// -------------------------------------------------------------------------

		$panels = "<div{$attr}>{$content}</div>";

		if ( ! $this->has_options )
		{
			return $panels;
		}

		// -------------------------------------------------------------------------

		$form_content = [];

		// -------------------------------------------------------------------------

		if ( $submit_top = $this->submit_all_options_top() )
		{
			$form_content[] = $submit_top;
		}

		// -------------------------------------------------------------------------

		$form_content[] = wp_nonce_field(
			$action 	= $this->form_nonce_action,
			$name 		= $this->form_nonce_name,
			$referer 	= false,
			$_echo 		= false
		);

		// -------------------------------------------------------------------------

		$form_content[] = $panels;

		// -------------------------------------------------------------------------

		if ( $submit_bottom = $this->submit_all_options_bottom() )
		{
			$form_content[] = $submit_bottom;
		}

		// -------------------------------------------------------------------------

		$form_attr = F::html_attributes([
			'method'						=> 'post',
			'id'								=> "{$this->css_class}_form_{$this->id}",
			'class'							=> ["{$this->css_class}_form"],
			'data-component_id'	=> $this->id,
		]);

		// -------------------------------------------------------------------------

		return sprintf('<form%s>%s</form>', $form_attr, implode( $form_content ) );

	}
	// admin_page_body_content()



	/**
	 * @since 1.0.0
	 */
	protected function admin_page_footer()
	{

		$fn = 'admin_page_footer_content';

		if ( method_exists( $this, $fn ) )
		{
			if ( $content = $this->{$fn}() )
			{
				$attr = F::html_attributes([
					'class' => ["{$this->css_class}_footer"],
				]);

				return "<footer{$attr}>{$content}</footer>";
			}
		}

	}
	// admin_page_footer()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN PAGE - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PANELS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function panels()
	{

		if ( isset( $this->panels ) )
		{
			return $this->panels;
		}

		// -------------------------------------------------------------------------

		$this->panels = [];

		// -------------------------------------------------------------------------

		$panels_files = File::get_directory_files( $this->dir('panels') );

		if ( empty( $panels_files ) )
		{
			return $this->panels;
		}

		// -------------------------------------------------------------------------

		$fn = 'panels_disabled';

		if ( method_exists( $this, $fn ) )
		{
			$panels_disabled = $this->{$fn}();
		}

		// -------------------------------------------------------------------------

		foreach ( $panels_files as $file_data )
		{
			if ( empty( $file_data['name'] ) || empty( $file_data['path'] ) )
			{
				continue;
			}

			if ( ! $class_name = F::array_get( $file_data, ['class', 'full_name'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$sanitized_name	= str_replace( '[-_\s]+', '_', $file_data['name'] );
			$sanitized_name	= strtolower( preg_replace( '/[\W]/', '', $sanitized_name ) );

			$panel_args = [
				'id'					=> $sanitized_name,
				'capability'	=> $this->capability,
				'title'				=> F::humanize( $file_data['name'] ),
				'icon'				=> '',
				'content'			=> '',
				'before'			=> '',
				'after'				=> '',
				'ajax'				=> false,
			];

			// -----------------------------------------------------------------------

			if ( 		! empty( $panels_disabled )
					 && in_array( $sanitized_name, $panels_disabled ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! class_exists( $class_name ) )
			{
				require_once( $file_data['path'] );
			}

			// -----------------------------------------------------------------------

			$this->panels[ $sanitized_name ] = new $class_name( $this, $panel_args );
		}

		// -------------------------------------------------------------------------

		$user_prefs_panels_order = $this->options_user_prefs(['panels', 'order']);

		if ( ! empty( $user_prefs_panels_order ) )
		{
			$panels_order = $user_prefs_panels_order;
		}
		else
		{
			$fn = 'panels_default_order';

			if ( method_exists( $this, $fn ) )
			{
				$panels_order = $this->{$fn}();
			}
		}

		if ( ! empty( $panels_order ) )
		{
			$this->panels = F::array_sort_by_array( $this->panels, $panels_order );
		}

		// -------------------------------------------------------------------------

		return $this->panels;

	}
	// panels()



	/**
	 * use in sub-class as needed
	 */
	//protected function panels_disabled(){}



	/**
	 * use in sub-class as needed
	 */
	//protected function panels_default_order(){}

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PANELS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MARKUP - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function submit_markup( $args = [] )
	{

		$defaults = [

			'component_id'	=> $this->id,
			'panel_id'			=> '',
			'section_id'		=> '',
			'action_suffix'	=> '',
			'ajax'					=> false,

			// -----------------------------------------------------------------------

			'buttons' => [

				'left' => [
					[
						'id'		=> 'save',
						'text'	=> $this->__('Save'),
						'attr'	=> ['class' => ['button', 'button-primary']],
					],
					[
						'id'					=> 'reset',
						'text'				=> $this->__('Reset'),
						'attr'				=> ['class' => ['button', 'button-secondary']],
						'confirm_msg'	=> $this->__(
							'This action is irreversible and will delete all related database entries.' .
							'\n\n' .
							'Are you sure you want to Reset Options?'
						),
					],
				],

				// ---------------------------------------------------------------------

				/*
				'right' => [
					[
						'id'		=> 'export',
						'text'	=> $this->__('Export Options'),
						'attr'	=> ['class' => ['button', 'button-secondary']],
					],
					[
						'id'		=> 'import',
						'text'	=> $this->__('Import Options'),
						'attr'	=> ['class' => ['button', 'button-secondary']],
					],
				],
				*/

			],

		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( empty( $buttons ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @todo: once i figure a way to reset fields using ajax, remove 'reset''

		$unsupported_ajax_buttons = ['reset', 'export', 'import'];

		// -------------------------------------------------------------------------

		$markup = [];

		foreach ( $buttons as $position => $position_buttons )
		{
			if ( empty( $position_buttons ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$markup[ $position ] = [];

			foreach ( $position_buttons as $button )
			{
				if ( empty( $button['id'] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				$button_attr = F::array_get( $button, ['attr'], [] );

				$button_attr['type'] = 'submit';
				$button_attr['name'] = "{$button['id']}{$action_suffix}";

				if ( empty( $button['text'] ) )
				{
					$button['text'] = F::humanize( $button['id'] );
				}

				// ---------------------------------------------------------------------

				if ( ! empty( $button['confirm_msg'] ) )
				{
					$button_attr['onclick'] = "return confirm('{$button['confirm_msg']}')";
				}

				// ---------------------------------------------------------------------

				if ( ! empty( $ajax ) )
				{
					if ( ! in_array( $button['id'], $unsupported_ajax_buttons ) )
					{
						$button_attr['data-ajax']						= true;
						$button_attr['data-component_id']		= ! empty( $component_id )? $component_id	: null;
						$button_attr['data-panel_id']				= ! empty( $panel_id )		? $panel_id			: null;
						$button_attr['data-section_id']			= ! empty( $section_id )	? $section_id		: null;
						$button_attr['class'][]							= 'button_with_loader';
						$button['text']											= sprintf(
							'<span class="button_with_loader_text">%s</span>',
							$button['text']
						);
					}
				}

				// ---------------------------------------------------------------------

				$markup[ $position ][] = sprintf(
					'<button%s>%s</button>',
					F::html_attributes( $button_attr ),
					$button['text']
				);
			}

			// -----------------------------------------------------------------------

			if ( empty( $markup[ $position ] ) )
			{
				unset( $markup[ $position ] );

				continue;
			}

			// -----------------------------------------------------------------------

			$markup[ $position ] = sprintf(
				'<div%s>%s</div>',
				F::html_attributes([
					'id'		=> "submit_{$position}{$action_suffix}",
					'class'	=> ["submit_{$position}"],
				]),
				implode( ' ', $markup[ $position ] )
			);
		}

		// -------------------------------------------------------------------------

		if ( empty( $markup ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return sprintf('<div class="submit">%s</div>', implode( $markup ) );

	}
	// submit_markup() - End



	/**
	 * @since 1.0.0
	 */
	public function submit_all_options_bottom( $args = [] )
	{

		$defaults = [

			'action_suffix'	=> '_all_options',
			'ajax'					=> true,

			// -----------------------------------------------------------------------

			'buttons' => [

				'left' => [
					[
						'id'		=> 'save',
						'text'	=> $this->__('Save All Options'),
						'attr'	=> ['class' => ['button', 'button-primary', 'button-hero']],
					],
					[
						'id'					=> 'reset',
						'text'				=> $this->__('Reset All Options'),
						'attr'				=> ['class' => ['button', 'button-secondary', 'button-hero']],
						'confirm_msg'	=> $this->__(
							'This action is irreversible and will delete all related database entries.' .
							'\r\n\n' .
							'Are you sure you want to Reset All Options?'
						),
					],
				],

				// ---------------------------------------------------------------------

				'right' => [
					[
						'id'		=> 'export',
						'text'	=> $this->__('Export All Options'),
						'attr'	=> ['class' => ['button', 'button-secondary']],
					],
					// @todo
					/*
					[
						'id'		=> 'import',
						'text'	=> $this->__('Import All Options'),
						'attr'	=> ['class' => ['button', 'button-secondary']],
					],
					*/
				],

			],

		];

		$args = F::parse_args_deep( $args, $defaults );

		// -------------------------------------------------------------------------

		return $this->submit_markup( $args );

	}
	// submit_all_options_bottom() - End



	/**
	 * @since 1.0.0
	 */
	public function submit_all_options_top( $args = [] )
	{

		// @consider

	}
	// submit_all_options_top() - End



	/**
	 * @since 1.0.0
	 */
	public function notice_markup( $args = [] )
	{

		$defaults = [
			'success'			=> false,
			'error'				=> false,
			'title'				=> null,
			'message'			=> '',
			'dismiss'			=> true,
			'collapsible'	=> true,
		];

		$args = F::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$attr = [
			'id'		=> "{$this->css_class}_notice",
			'class'	=> [
				"{$this->css_class}_notice",
				'notice',
				$dismiss			? 'dismiss'			: null,
				$collapsible	? 'collapsible'	: null,
			],
		];

		if ( $success )
		{
			$attr['class'][] = 'updated';
		}
		elseif ( $error )
		{
			$attr['class'][] = 'error';
		}

		// -------------------------------------------------------------------------

		// default title based on success/error

		if ( ! isset( $title ) )
		{
			$title = $success ? $this->__('Success') : ( $error ? $this->__('Error') : null );
		}

		// -------------------------------------------------------------------------

		$content = [];

		if ( $title )
		{
			$collapsible_toggle = '';

			if ( $collapsible )
			{
				$collapsible_toggle = '<button class="collapsible_toggle dashicons-before" type="button"></button>';

				$attr['class'][] = 'collapsed';
			}

			$content[] = "<h3 class=\"notice_title\">{$collapsible_toggle}{$title}</h3>";
		}

		if ( $message )
		{
			$content[] = "<div class=\"notice_message\">{$message}</div>";
		}

		if ( empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $dismiss )
		{
			$content[] = '<span class="dashicons dashicons-dismiss dismiss_button"></span>';
		}

		// -------------------------------------------------------------------------

		$content = implode( $content );

		// -------------------------------------------------------------------------

		$attr	= F::html_attributes( $attr );

		return "<div{$attr}>{$content}</div>";

	}
	// notice_markup() - End



	/**
	 * @since 1.0.0
	 */
	public function content_none_markup( $message = null )
	{

		//$this->__('No panels or content defined for this component.')

		return sprintf(
			'<div%s><p>%s</p></div>',
			F::html_attributes(['class' => "{$this->css_class}_content_none"]),
			$message ?: $this->__('No content defined.')
		);

	}
	// content_none_markup() - End

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MARKUP - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FORM POSTING - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function form_posting_process()
	{

		// @notes: this is only for regular posting not ajax

		if ( ! $this->in_component_page() )
		{
			return;
		}

		if ( ! current_user_can( $this->capability ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->form_posting_msg = '';

		// -------------------------------------------------------------------------

		if ( ! $this->has_options || ! $this->db_option_name )
		{
			return;
		}

		if ( ! $nonce = F::array_get( $_POST, [ $this->form_nonce_name ] ) )
		{
			return;
		}

		if ( ! wp_verify_nonce( $nonce, $this->form_nonce_action ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $action = $this->form_posting_process_action() )
		{
			return;
		}

		if ( is_wp_error( $action ) )
		{
			$notice = $this->notice_markup([
				'error'		=> true,
				'message'	=> "<p><strong>{$action->get_error_message()}</strong></p>",
			]);
		}
		else
		{
			if ( ! $result = $this->{$action['name']}( $action['args'] ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			if ( is_wp_error( $result ) )
			{
				$notice = $this->notice_markup([
					'error'		=> true,
					'message'	=> "<p><strong>{$result->get_error_message()}</strong></p>",
				]);
			}
			else
			{
				$notice = $this->notice_markup([
					'success'	=> ! empty( $result['success'] ),
					'error'		=> ! empty( $result['error'] ),
					'message'	=> "<p>{$result['message']}</p>",
				]);
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $notice ) )
		{
			add_action( 'admin_notices', function() use ( $notice ){ echo $notice; } );
		}

	}
	// form_posting_process()



	/**
	 * @since 1.0.0
	 */
	public function form_posting_process_ajax()
	{

		if ( ! $nonce = F::array_get( $_POST, ['nonce'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! wp_verify_nonce( $nonce, $this->form_nonce_action ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$this->__('Nonce Error')}</strong></p>",
				]),
			]);
		}

		// -------------------------------------------------------------------------

		if ( ! current_user_can( $this->capability ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$this->__('Permission Error')}</strong></p>",
				]),
			]);
		}

		// -------------------------------------------------------------------------

		$required_data = [
			'component_id',
			'form_data',
		];

		foreach ( $required_data as $key )
		{
			if ( ! isset( $_POST[ $key ] ) )
			{
				wp_send_json_error([
					'notice' => $this->notice_markup([
						'error'		=> true,
						'title'		=> '',
						'message'	=> sprintf(
							"<p><strong>{$this->__('Missing Required Data "%s"')}</strong></p>",
							$key
						),
					]),
				]);
			}
		}

		// -------------------------------------------------------------------------

		if ( $_POST['component_id'] !== $this->id )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$this->__('Component ID Error')}</strong></p>",
				]),
			]);
		}

		// -------------------------------------------------------------------------

		$action = $this->form_posting_process_action();

		if ( empty( $action ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$this->__('Action empty')}</strong></p>",
				]),
			]);
		}

		if ( is_wp_error( $action ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$action->get_error_message()}</strong></p>",
				]),
			]);
		}

		// -------------------------------------------------------------------------

		parse_str( $_POST['form_data'], $action['args']['form_data'] );

		// -------------------------------------------------------------------------

		if ( ! $result = $this->{$action['name']}( $action['args'] ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$this->__('Action Has No Results')}</strong></p>",
				]),
			]);
		}

		// -------------------------------------------------------------------------

		if ( is_wp_error( $result ) )
		{
			wp_send_json_error([
				'notice' => $this->notice_markup([
					'error'		=> true,
					'title'		=> '',
					'message'	=> "<p><strong>{$result->get_error_message()}</strong></p>",
				]),
			]);
		}
		else
		{
			$message = F::array_get( $result, ['message'] );

			wp_send_json_success([
				'notice' => $this->notice_markup([
					'success'	=> true,
					'title'		=> ! empty( $message ) ? '' : null,
					'message'	=> ! empty( $message ) ? "<p><strong>{$message}</strong></p>" : null,
				]),
				'processed_time_seconds' => timer_stop(),
			]);
		}

	}
	// form_posting_process_ajax()



	/**
	 * @since 1.0.0
	 */
	protected function form_posting_process_action()
	{

		$name = '';
		$args = [];

		if ( isset( $_POST['save_all_options'] ) )
		{
			$name = 'options_save';
		}
		elseif ( isset( $_POST['reset_all_options'] ) )
		{
			$name = 'options_reset';
		}
		elseif ( isset( $_POST['export_all_options'] ) )
		{
			$name = 'options_export';
		}
		elseif ( isset( $_POST['import_all_options'] ) )
		{
			$name = 'options_import';
		}
		else
		{
			$section_action = $this->form_posting_get_section_action();

			if ( ! empty( $section_action['action'] ) )
			{
				$name = "options_{$section_action['action']}";
				$args	= [
					'panel'		=> $section_action['panel'],
					'section'	=> $section_action['section'],
				];
			}
		}

		// -------------------------------------------------------------------------

		if ( ! $name )
		{
			$error = array_values([
				'code'		=> __FUNCTION__ . '_name_empty',
				'message'	=> $this->__('Unsupported Action'),
			]);

			return new \WP_Error( ...$error );
		}

		// -------------------------------------------------------------------------

		if ( ! method_exists( $this, $name ) )
		{
			$error = array_values([
				'code'		=> __FUNCTION__ . '_method_exists',
				'message'	=> sprintf( $this->__('Unsupported Action "%s"'), $name ),
			]);

			return new \WP_Error( ...$error );
		}

		// -------------------------------------------------------------------------

		return [
			'name' => $name,
			'args' => $args,
		];

	}
	// form_posting_process_action()




	/**
	 * @since 1.0.0
	 */
	protected function form_posting_get_section_action()
	{

		if ( ! $panels = $this->panels() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $panels as $panel )
		{
			if ( ! $panel->id )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// @notes:
			// i had to parse them here because they are only parsed in $panel->markup()
			// which happens after this action, meaning content is not yet set to $panel->args

			$panel_args = wp_parse_args( $panel->config(), $panel->args );

			if ( ! $panel_content = F::array_get( $panel_args, ['content'] ) )
			{
				continue;
			}

			if ( ! is_array( $panel_content ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			foreach ( $panel_content as $section )
			{
				if ( empty( $section['id'] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				$action_suffix = "_{$panel->id}_{$section['id']}";

				// ---------------------------------------------------------------------

				if ( isset( $_POST["save{$action_suffix}"] ) )
				{
					return [
						'action'	=> 'save',
						'panel'		=> $panel,
						'section'	=> $section,
					];
				}

				// ---------------------------------------------------------------------

				if ( isset( $_POST["reset{$action_suffix}"] ) )
				{
					return [
						'action'	=> 'reset',
						'panel'		=> $panel,
						'section'	=> $section,
					];
				}
			}
		}

	}
	// form_posting_get_section_action()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FORM POSTING - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * OPTIONS/SETTINGS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function options( $path = null, $defaults = true, $refresh = false )
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! isset( $this->options ) || $refresh )
		{
			// @todo: not sure if i want to merge defaults even before they are saved
			$this->options = $defaults ? $this->options_defaults() : [];
			$saved_options = get_option( $this->db_option_name, [] );

			if ( ! empty( $saved_options ) )
			{
				$this->options = F::parse_args_deep( $saved_options, $this->options );
			}
		}

		// -------------------------------------------------------------------------

		if ( ! $path )
		{
			return $this->options;
		}

		// -------------------------------------------------------------------------

		if ( ! is_array( $path ) )
		{
			$path = [ $path ];
		}

		// -------------------------------------------------------------------------

		return F::array_get( $this->options, $path );

	}
	// options()



	/**
	 * @since 1.0.0
	 */
	public function options_defaults()
	{

		$out = [
			'info'				=> $this->options_info(),
			'user_prefs'	=> [],
			'settings'		=> [],
		];

		// -------------------------------------------------------------------------

		$panels = $this->panels();

		if ( empty( $panels ) || ! $this->has_options )
		{
			return $out;
		}

		// -------------------------------------------------------------------------

		foreach ( $panels as $panel )
		{
			if ( ! $panel->id )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$panel_args = wp_parse_args( $panel->config(), $panel->args );

			if ( ! $panel_content = F::array_get( $panel_args, ['content'] ) )
			{
				continue;
			}

			if ( ! is_array( $panel_content ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			foreach ( $panel_content as $section )
			{
				if ( 		empty( $section['id'] )
						 || empty( $section['content'] )
						 || ! is_array( $section['content'] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				foreach ( $section['content'] as $field )
				{
					if ( empty( $field['id'] ) || empty( $field['type'] ) )
					{
						continue;
					}

					// -------------------------------------------------------------------

					if ( ! isset( $field['default'] ) )
					{
						continue;
					}

					// -------------------------------------------------------------------

					// @todo:
					// group and sub-groups / array_field where i need to have a recursive loop

					if ( 'group' === $field['type'] )
					{
						if ( empty( $field['fields'] ) )
						{
							continue;
						}
					}
					else
					{
						$out['settings'][ $panel->id ][ $section['id'] ][ $field['id'] ] = $field['default'];
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// options_defaults()



	/**
	 * @since 1.0.0
	 */
	public function options_info()
	{

		return [
			'id'						=> $this->id,
			'class_name'		=> $this->class_name,
			'version'				=> $this->version,
			'last_modified'	=> gmdate( 'Y-m-d H:i:s', time() ),
		];

	}
	// options_info()



	/**
	 * @since 1.0.0
	 */
	public function options_settings( $path = null, $default = null, $stripslashes = true )
	{

		$settings = $this->options('settings');

		if ( empty( $settings ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $path )
		{
			$settings	= F::array_get( $settings, $path, $default );
		}

		// -------------------------------------------------------------------------

		return $stripslashes ? stripslashes_deep( $settings ) : $settings;

	}
	// options_settings()



	/**
	 * @since 1.0.0
	 */
	public function options_save( $args = [] )
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'panel'			=> null,
			'section'		=> null,
			'form_data'	=> $_POST,
		];

		$args = F::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$what = 'all';

		if ( ! empty( $panel ) )
		{
			if ( ! empty( $section ) )
			{
				$what = 'section';
			}
			else
			{
				$what = 'panel';
			}
		}

		// -------------------------------------------------------------------------

		$options = get_option( $this->db_option_name, [] );

		// -------------------------------------------------------------------------

		$out = [
			'success'	=> false,
			'error'		=> false,
			'message'	=> '',
		];

		switch ( $what )
		{
			case 'all':

				$options['settings'] = [];

				foreach ( $this->panels() as $panel )
				{
					if ( isset( $form_data[ $panel->id ] ) )
					{
						$options['settings'][ $panel->id ] = $form_data[ $panel->id ];
					}
				}

				$out['message'] = sprintf( $this->__('%s Options Saved'), $this->page_title );

				break;

			// -----------------------------------------------------------------------

			case 'panel':

				F::array_set(
					$options,
					['settings', $panel->id ],
					F::array_get( $form_data, [ $panel->id ], [] )
				);

				$out['message'] = sprintf( $this->__('%s Options Saved'), $panel->title );

				break;

			// -----------------------------------------------------------------------

			case 'section':

				F::array_set(
					$options,
					['settings', $panel->id, $section['id'] ],
					F::array_get( $form_data, [ $panel->id, $section['id'] ], [] )
				);

				$out['message'] = sprintf(
					$this->__('%s Options Saved'),
					$panel->get_section_title( $section )
				);

				break;
		}

		// -------------------------------------------------------------------------

		$options['info'] = $this->options_info();

		// -------------------------------------------------------------------------

		if ( $updated = update_option( $this->db_option_name, $options ) )
		{
			$out['success'] = true;
		}
		else
		{
			$out['error']		= true;
			$out['message']	= $this->__('Update Option Failed.');
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// options_save()



	/**
	 * @since 1.0.0
	 */
	public function options_reset( $args = [] )
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'panel'		=> null,
			'section'	=> null,
		];

		$args = F::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$what = 'all';

		if ( ! empty( $panel ) )
		{
			if ( ! empty( $section ) )
			{
				$what = 'section';
			}
			else
			{
				$what = 'panel';
			}
		}

		// -------------------------------------------------------------------------

		$options_defaults = $this->options_defaults();

		// -------------------------------------------------------------------------

		$out = [
			'success'	=> false,
			'error'		=> false,
			'message'	=> '',
		];

		switch ( $what )
		{
			case 'all':

				delete_option( $this->db_option_name );

				add_option( $this->db_option_name, $options_defaults );

				return [
					'success' => true,
					'message' => sprintf(
						$this->__('%s Options Reset'),
						$this->page_title
					),
				];

				break;

			// -----------------------------------------------------------------------

			case 'panel':

				$options = get_option( $this->db_option_name, [] );
				$options = F::parse_args_deep( $options, $options_defaults );

				// ---------------------------------------------------------------------

				$panel_defaults	= F::array_get(
					$options_defaults,
					[ 'settings', $panel->id ],
					[]
				);

				F::array_set( $options, ['settings', $panel->id ], $panel_defaults );

				// ---------------------------------------------------------------------

				$out['message'] = sprintf( $this->__('%s Reset'), $panel->title );

				break;

			// -----------------------------------------------------------------------

			case 'section':

				$options = get_option( $this->db_option_name, [] );
				$options = F::parse_args_deep( $options, $options_defaults );

				// ---------------------------------------------------------------------

				$section_defaults	= F::array_get(
					$options_defaults,
					[ 'settings', $panel->id, $section['id'] ],
					[]
				);

				F::array_set(
					$options,
					['settings', $panel->id, $section['id'] ],
					$section_defaults
				);

				// ---------------------------------------------------------------------

				$out['message'] = sprintf(
					$this->__('%s Reset'),
					$panel->get_section_title( $section )
				);

				break;
		}

		if ( ! isset( $options ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$options['info'] = $this->options_info();

		// -------------------------------------------------------------------------

		if ( $updated = update_option( $this->db_option_name, $options ) )
		{
			$out['success'] = true;
		}
		else
		{
			$out['error']		= true;
			$out['message']	= $this->__('Update Option Failed.');
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// options_reset()



	/**
	 * @since 1.0.0
	 */
	public function options_export()
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$options	= get_option( $this->db_option_name, [] );
		$filename	= $this->db_option_name . '_' . gmdate('Y-m-d_H-i-s') . '.txt';

		// -------------------------------------------------------------------------

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/plain; charset=' . get_option('blog_charset'), true );

		// -------------------------------------------------------------------------

		// @consider exporting as json

		echo serialize( $options );

		// -------------------------------------------------------------------------

		die();

	}
	// options_export()



	/**
	 * @since 1.0.0
	 */
	public function options_import()
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @todo

	}
	// options_import()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * OPTIONS/SETTINGS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * OPTIONS/SETTINGS - user_prefs - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function options_user_prefs( $option = null, $user_id = null )
	{

		if ( ! isset( $user_id ) )
		{
			if ( ! $user_id = get_current_user_id() )
			{
				return;
			}
		}

		// -------------------------------------------------------------------------

		$path = ['user_prefs', $user_id ];

		if ( $option )
		{
			if ( is_array( $option ) )
			{
				$path = array_merge( $path, $option );
			}
			else
			{
				$path[] = $option;
			}
		}

		// -------------------------------------------------------------------------

		return $this->options( $path );

	}
	// options_user_prefs()



	/**
	 * @since 1.0.0
	 */
	public function options_user_prefs_process_ajax()
	{

		if ( ! $this->db_option_name )
		{
			return;
		}

		if ( ! $panels = F::array_get( $_POST, ['panels'] ) )
		{
			return;
		}

		if ( ! $nonce = F::array_get( $_POST, ['nonce'] ) )
		{
			return;
		}

		if ( ! wp_verify_nonce( $nonce, $this->options_user_prefs_nonce_action ) )
		{
			return;
		}

		if ( ! $user_id = get_current_user_id() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// ref:
		/*
		$options = [
			'user_prefs' => [
				$user_id => [
					'panels' => [
						'current_id'	=> '',
						'order'				=> [],
						'prefs				=> [
							'panel_id' => [
								'sections' => [
									'collapsed'	=> [],
									'order'			=> [],
								],
							],
							'another_panel_id' => [...],
						],
					],
				],
			],
		];
		*/

		$defaults = [
			'current_id'	=> '',
			'order'				=> [],
			'prefs'				=> [],
		];

		$panels = wp_parse_args( $panels, $defaults );

		// -------------------------------------------------------------------------

		$options = get_option( $this->db_option_name, [] );

		F::array_set( $options, ['user_prefs', $user_id, 'panels' ], $panels );

		$options['info'] = $this->options_info();

		// -------------------------------------------------------------------------

		if ( update_option( $this->db_option_name, $options ) )
		{
			wp_send_json_success( $this->__('User Prefs Saved') );
		}

	}
	// options_user_prefs_process_ajax()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * OPTIONS/SETTINGS - user_prefs - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PATHS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function dir( $path = '' )
	{

		if ( empty( $this->{__FUNCTION__} ) )
		{
			$reflector = new \ReflectionClass( get_class( $this ) );

			$this->{__FUNCTION__} = dirname( $reflector->getFileName() );
		}

		// -------------------------------------------------------------------------

		return $path ? path_join( $this->{__FUNCTION__}, $path ) : $this->{__FUNCTION__};

	}
	// dir()



	/**
	 * @since 1.0.0
	 */
	public function dir_assets( $path = '' )
	{

		$dir = $this->dir('assets');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// dir_assets()



	/**
	 * @since 1.0.0
	 */
	public function dir_css( $path = '' )
	{

		$dir = $this->dir_assets('css');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// dir_css()



	/**
	 * @since 1.0.0
	 */
	public function dir_js( $path = '' )
	{

		$dir = $this->dir_assets('js');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// dir_js()



	/**
	 * @since 1.0.0
	 */
	public function dir_images( $path = '' )
	{

		$dir = $this->dir_assets('images');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// dir_images()



	/**
	 * @since 1.0.0
	 */
	public function dir_cache( $path = '' )
	{

		$dir = $this->dir('cache');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// dir_cache()



	/**
	 * @since 1.0.0
	 */
	public function url( $path = '' )
	{

		if ( empty( $this->{__FUNCTION__} ) )
		{
			$this->{__FUNCTION__} = $this->core->path_to_url( $this->dir() );
		}

		// -------------------------------------------------------------------------

		return $path ? path_join( $this->{__FUNCTION__}, $path ) : $this->{__FUNCTION__};

	}
	// url()



	/**
	 * @since 1.0.0
	 */
	public function url_assets( $path = '' )
	{

		$dir = $this->url('assets');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// url_assets()



	/**
	 * @since 1.0.0
	 */
	public function url_css( $path = '' )
	{

		$dir = $this->url_assets('css');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// url_css()



	/**
	 * @since 1.0.0
	 */
	public function url_js( $path = '' )
	{

		$dir = $this->url_assets('js');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// url_js()



	/**
	 * @since 1.0.0
	 */
	public function url_images( $path = '' )
	{

		$dir = $this->url_assets('images');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// url_images()



	/**
	 * @since 1.0.0
	 */
	public function url_cache( $path = '' )
	{

		$dir = $this->url('cache');

		return $path ? path_join( $dir, $path ) : $dir;

	}
	// url_cache()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PATHS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Component
