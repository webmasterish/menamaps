<?php

namespace DotAim;

use \DotAim\F;
use \DotAim\File;

/**
 * @since 1.0.0
 */
final class DotAim
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @notes:
	 * this will be re-populated in system() based on stylesheet values
	 */
	public $system_min_versions = [
		'WordPress'	=> '6.0',
		'PHP'				=> '7.4',
		'MySQL'			=> '8.0',
	];

	/**
	 * @since 1.0.0
	 */
	public $app_config;

	/**
	 * @notes:
	 * this will be reset to stylesheet theme version in init_properties()
	 */
	public $version = '1.0.0';

	/**
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * @since 1.0.0
	 */
	public $id;

	/**
	 * @since 1.0.0
	 */
	public $textdomain;

	/**
	 * @since 1.0.0
	 */
	public $prefix;

	/**
	 * @since 1.0.0
	 */
	public $theme_name;
	public $css_prefix;
	public $db_option_name;
	public $db_option_name_prefix;
	public $meta_box_prefix;
	public $admin_settings;

	// ---------------------------------------------------------------------------

	// static properties

	/**
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @since 1.0.0
	 */
	protected static $_theme;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * STATIC METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public static function instance()
	{

		if ( is_null( self::$_instance ) )
		{
			self::$_instance = new self();
		}

		// -------------------------------------------------------------------------

		return self::$_instance;

	}
	// instance()



	/**
	 * @since 1.0.0
	 */
	public static function theme()
	{

		if ( ! empty( self::$_theme ) )
		{
			return self::$_theme;
		}

		// -------------------------------------------------------------------------

		// @consider using if ( function_exists('wp_get_theme') )

		self::$_theme = wp_get_theme();

		// -------------------------------------------------------------------------

		return self::$_theme;

	}
	// theme()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * STATIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{

		if ( true !== $this->system_check() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// for autoloading classes

		$this->NS();

		// -------------------------------------------------------------------------

		// APP is defined in wp-config.php

		$this->app_config = ! empty( APP ) ? APP : [];

		// -------------------------------------------------------------------------

		// properties - general

		$theme = self::theme();

		$this->theme_name	= $theme->get('Name')				?: F::array_get( $this->app_config, ['theme_name'] );
		$this->textdomain	= $theme->get('TextDomain')	?: F::array_get( $this->app_config, ['theme_id'] );
		$this->version		= $theme->get('Version');
		$this->name				= $this->theme_name;
		$this->id					= $this->textdomain;
		$this->prefix			= "{$this->id}_";

		// -------------------------------------------------------------------------

		// mainly used in css and js

		$this->css_prefix = strtolower( __NAMESPACE__ ) . '_';

		// -------------------------------------------------------------------------

		// properties - settings

		$this->db_option_name					= "{$this->prefix}options";
		$this->db_option_name_prefix	= "{$this->db_option_name}_";
		$this->meta_box_prefix				= "{$this->prefix}meta_box_";

		// -------------------------------------------------------------------------

		// properties - admin

		$this->admin_settings = [

			'capability'			=> 'manage_options',
			'db_option_name'	=> "{$this->db_option_name_prefix}admin",

			// -----------------------------------------------------------------------

			'menu_slug'				=>  $this->id,
			'menu_title'			=>  $this->name,
			'menu_icon'				=>  '',
			'menu_position'		=>  '',

			// -----------------------------------------------------------------------

			'page_title'			=>  $this->name,
			'page_subtitle'		=>  '',
			'page_description'=>  '',
			'page_icon'				=>  '',

			// -----------------------------------------------------------------------

			// @todo: use filter or args in __construct() or config file

			'components_directories' => [
				path_join( __DIR__, 'Admin/Components' ),
			],

		];

		// -------------------------------------------------------------------------

		$this->hooks();

		// -------------------------------------------------------------------------

		$this->do_action('loaded');

	}
	// __construct()



	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );

	}
	// __clone()



	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );

	}
	// __wakeup()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
 	 * ---------------------------------------------------------------------------
	 * SYSTEM - START
 	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function system()
	{

		static $system;

		if ( isset( $system ) )
		{
			return $system;
		}

		// -------------------------------------------------------------------------

		$theme = self::theme();

		if ( $RequiresWP = $theme->get('RequiresWP') )
		{
			$this->system_min_versions['WordPress'] = $RequiresWP;
		}

		if ( $RequiresPHP = $theme->get('RequiresPHP') )
		{
			$this->system_min_versions['PHP'] = $RequiresPHP;
		}

		// -------------------------------------------------------------------------

		// no system check is set

		if ( empty( $this->system_min_versions ) )
		{
			return null;
		}

		// -------------------------------------------------------------------------

		global $wp_version, $wpdb;

		// -------------------------------------------------------------------------

		$system = [];

		foreach ( $this->system_min_versions as $key => $min_ver )
		{
			if ( ! $min_ver )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			switch ( strtolower( $key ) )
			{
				case 'mysql':

					$current_ver = $wpdb->db_version();

					break;

				// ---------------------------------------------------------------------

				case 'php':

					$current_ver = PHP_VERSION;

					break;

				// ---------------------------------------------------------------------

				case 'wordpress':
				case 'wp':

					$current_ver = $wp_version;

					break;
			}

			// -----------------------------------------------------------------------

			if ( empty( $current_ver ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$system[ $key ] = [
				'current_ver'	=> $current_ver,
				'min_ver'			=> $min_ver,
				'check'				=> version_compare( $current_ver,  $min_ver, '>=' ),
			];

			// -----------------------------------------------------------------------

			// if 1 doesn't meet the min required ver, it's set to false

			if ( empty( $system[ $key ]['check'] ) )
			{
				$system['all_check'] = false;
			}
		}

		// -------------------------------------------------------------------------

		// if the check all is not yet set that means all ok, or no min versions are required

		if ( ! isset( $system['all_check'] ) )
		{
			$system['all_check'] = true;
		}

		// -------------------------------------------------------------------------

		// sort by key

		if (  empty( $system ) )
		{
			ksort( $system );
		}

		// -------------------------------------------------------------------------

		return $system;

	}
	// system()



	/**
	 * @since 1.0.0
	 */
	public function system_check( $admin_notice = true )
	{

		static $system_check;

		if ( isset( $system_check ) )
		{
			return $system_check;
		}

		// -------------------------------------------------------------------------

		$system_check = false;

		// -------------------------------------------------------------------------

		// system check is not set, return ok/true

		if ( null === $system = $this->system() )
		{
			$system_check = true;

			return $system_check;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $system['all_check'] ) )
		{
			$system_check = true;

			return $system_check;
		}

		// -------------------------------------------------------------------------

		if ( ! $admin_notice )
		{
			return $system_check;
		}

		// -------------------------------------------------------------------------

		$checks = [];

		foreach ( $system as $key => $value )
		{
			if ( 'all_check' === $key )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( empty( $value['check'] ) )
			{
				$checks[] = sprintf(
					__( '<strong>%s Version %s</strong> &ndash; <em>your current version is %s</em>' ),
					$key,
					$value['min_ver'],
					$value['current_ver']
				);
			}
		}

		// -------------------------------------------------------------------------

		$theme_name = self::theme()->get('Name');

		// -------------------------------------------------------------------------

		if ( ! empty( $checks ) )
		{
			$checks =
				"<p>Your system is incompatible with the minimum requirements to run {$theme_name}</p>" .
				'<p>Please update your system to at least:</p>' .
				'<ul class="ul-disc">' .
					'<li>' . implode( '</li><li>', $checks ) . '</li>' .
				'<ul>';
		}
		else
		{
			$checks = '';
		}

		// -------------------------------------------------------------------------

		$title	= "<h3>Incompatible System To Run {$theme_name}.</h3>";
		$msg		= "<div id=\"message\" class=\"fade error\">{$title}{$checks}</div>";

		add_action( 'admin_notices', function() use ( $msg ){ echo $msg; } );

		// -------------------------------------------------------------------------

		return $system_check;

	}
	// system_check()

	/* ===========================================================================
 	 * ---------------------------------------------------------------------------
	 * SYSTEM - END
 	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CLASSES RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	private function classes_load()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		// for autoloading 3rd party libraries

		require_once $this->dir_includes('lib/vendor/autoload.php');

		// -------------------------------------------------------------------------

		$this->Admin();

		// -------------------------------------------------------------------------

		$this->Settings();

		// -------------------------------------------------------------------------

		// @consider only if feature is enabled in settings

		$this->Post_Engagement()->init();

		// -------------------------------------------------------------------------

		$this->Posts();
		$this->Post();

		// -------------------------------------------------------------------------

		$this->Shortcodes();

		// -------------------------------------------------------------------------

		$this->Images();

		// -------------------------------------------------------------------------

		$this->Nav_Menus();

		// -------------------------------------------------------------------------

		$this->Meta_Boxes();

		// -------------------------------------------------------------------------

		$this->Frontend();

		// -------------------------------------------------------------------------

		if ( $this->Settings()->get_component_app_settings( 'general', 'features', 'enable_contact' ) )
		{
			$this->Contact();
		}

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// classes_load()



	/**
	 * @since 1.0.0
	 */
	public function NS()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$dir				= __DIR__;
			$namespace	= __NAMESPACE__;
			$class_name	= "{$namespace}\NS";
			$class_path	= path_join( $dir, 'NS.php' );

			// -----------------------------------------------------------------------

			if ( ! class_exists( $class_name ) )
			{
				require_once $class_path;
			}

			// -----------------------------------------------------------------------

			$instance = new $class_name( $namespace, $dir );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// NS()



	/**
	 * @since 1.0.0
	 */
	public function Admin()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Admin\Admin( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Admin()



	/**
	 * @since 1.0.0
	 */
	public function Settings()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Settings\Settings( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Settings()



	/**
	 * @since 1.0.0
	 */
	public function Posts()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Posts\Posts( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Posts()



	/**
	 * @since 1.0.0
	 */
	public function Post()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Posts\Post( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Post()



	/**
	 * @since 1.0.0
	 */
	public function Post_Engagement()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Posts\Post_Engagement( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Post_Engagement()



	/**
	 * @since 1.0.0
	 */
	public function Shortcodes()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Shortcodes\Shortcodes( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Shortcodes()



	/**
	 * @since 1.0.0
	 */
	public function Images()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Images\Images( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Images()



	/**
	 * @since 1.0.0
	 */
	public function Nav_Menus()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Nav_Menus\Nav_Menus( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Nav_Menus()



	/**
	 * @since 1.0.0
	 */
	public function Meta_Boxes()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Meta_Boxes\Meta_Boxes( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Meta_Boxes()



	/**
	 * @since 1.0.0
	 */
	public function Frontend()
	{

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Frontend\Frontend( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Frontend()



	/**
	 * @since 1.0.0
	 */
	public function Contact()
	{

		if ( ! $this->Settings()->get_component_app_settings( 'general', 'features', 'enable_contact' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		static $instance;

		if ( ! isset( $instance ) )
		{
			$instance = new \DotAim\Contact\Contact( $this );
		}

		// -------------------------------------------------------------------------

		return $instance;

	}
	// Contact()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CLASSES RELATED - END
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
	public function call_object_func( $obj, $func_name, $args = null )
	{

		if ( empty( $obj ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_object( $obj ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$fn = [ $obj, $func_name ];

		if ( ! is_callable( $fn ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return call_user_func( $fn, $args );

	}
	// call_object_func()



	/**
	 * @since 1.0.0
	 */
	public function is_local_dev()
	{

		$re = '/(localhost|\.localhost|\.local|\.dev|\.vbox)$/i';

		if ( preg_match( $re, $_SERVER['SERVER_NAME'], $matches ) )
		{
			return true;
		}

	}
	// is_local_dev()



	/**
	 * @since 1.0.0
	 */
	public function debug_log( $val, $title = '', $backtrace_index = 2 )
	{

		if ( $this->Settings()->get_component_app_settings( 'misc', 'logs', 'debug_enable' ) )
		{
			debug_log( $val, $title, $backtrace_index );
		}

	}
	// debug_log()



	/**
	 * @since 1.0.0
	 */
	public function transients_active()
	{

		static $transients_active;

		if ( ! isset( $transients_active ) )
		{
			$transients_active = $this->Settings()->get_component_app_settings(
				'misc',
				'caching',
				'transients_active'
			);
		}

		// -------------------------------------------------------------------------

		return $transients_active;

	}
	// transients_active()



	/**
	 * @since 1.0.0
	 */
	public function transients_default_expiration()
	{

		static $transients_default_expiration;

		if ( isset( $transients_default_expiration ) )
		{
			return $transients_default_expiration;
		}

		// -------------------------------------------------------------------------

		// default to 0 - never expire

		$transients_default_expiration = 0;

		// -------------------------------------------------------------------------

		$caching_settings		= $this->Settings()->get_component_app_settings( 'misc', 'caching' );
		$default_expiration	= $caching_settings['transients_default_expiration'] ?? 0;

		// -------------------------------------------------------------------------

		if ( 		! $caching_settings
				 || ! $default_expiration )
		{
			return $transients_default_expiration;
		}

		// -------------------------------------------------------------------------

		switch ( $default_expiration )
		{
			case 'custom':

				$transients_default_expiration = $caching_settings['transients_default_expiration_custom'] ?? 0;

				break;

			// ---------------------------------------------------------------------

			default:

				if ( defined( $default_expiration ) )
				{
					$transients_default_expiration = constant( $default_expiration );
				}

				break;
		}

		// -------------------------------------------------------------------------

		$transients_default_expiration = absint( $transients_default_expiration );

		// -------------------------------------------------------------------------

		return $transients_default_expiration;

	}
	// transients_default_expiration()



	/**
	 * @since 1.0.0
	 */
	public function section_theme_options( $append = [] )
	{

		$options = [

			''				=> $this->__('None'),
			'white'		=> '',
			'gray'		=> '',
			'primary'	=> '',
			'blue'		=> '',
			'brown'		=> '',
			'orange'	=> '',
			'purple'	=> '',
			'yellow'	=> '',

			// -----------------------------------------------------------------------

			// source: https://www.svgbackgrounds.com/

			'endless_constellation'	=> '',
			'spectrum_gradient'			=> '',
			'liquid_cheese'					=> '',
			'wintery_sunburst'			=> '',
			'subtle_prism'					=> '',
			'radiant_gradient'			=> '',
			'rose_petals'						=> '',
			'dragon_scales'					=> '',
			'quantum_gradient'			=> '',
			'slanted_gradient'			=> '',
			'scattered_forcefields'	=> '',
			'flat_mountains'				=> '',
			'sun_tornado'						=> '',
			'rainbow_vortex'				=> '',
		];

		// -------------------------------------------------------------------------

		if ( ! empty( $append ) )
		{
			$options = array_merge( $options, $append );
		}

		// -------------------------------------------------------------------------

		foreach ( $options as $key => &$val )
		{
			if ( ! strlen( $val ) )
			{
				$val = F::humanize( $key );
			}
		}

		// -------------------------------------------------------------------------

		return $options;

	}
	// section_theme_options()



	/**
	 * @since 1.0.0
	 */
	public function html_attributes( ...$args )
	{

		return F::html_attributes( ...$args );

	}
	// html_attributes()



	/**
	 * @since 1.0.0
	 */
	public function cookie_name_prefix()
	{

		return $this->prefix;

	}
	// cookie_name_prefix()



	/**
	 * @since 1.0.0
	 */
	public function cookies_default_attributes()
	{

		return [
			'expires'		=> time() + ( 1 * MONTH_IN_SECONDS ),
			'path'			=> COOKIEPATH,
			'domain'		=> COOKIE_DOMAIN,
			'secure'		=> is_ssl(),
			'httponly'	=> false,		// set to false to make it accessible by js
			'samesite'	=> 'None',	// None || Lax  || Strict
		];

	}
	// cookies_default_attributes()



	/**
	 * @since 1.0.0
	 */
	public function is_first_time_visitor()
	{

		$cookie_name = $this->cookie_name_prefix() . 'visited';

		if ( isset( $_COOKIE[ $cookie_name ] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$cookie_options = wp_parse_args(
			['expires' => time() + ( 1 * YEAR_IN_SECONDS )],
			$this->cookies_default_attributes()
		);

		setcookie( $cookie_name, true, $cookie_options );

		// -------------------------------------------------------------------------

		return true;

	}
	// is_first_time_visitor()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
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
	public function hook_prefix( $hook_type )
	{

		$out = $this->prefix;

		// -------------------------------------------------------------------------

		switch ( $hook_type )
		{
			case 'action':
			case 'filter':

				$out .= $hook_type . '_';

				break;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// hook_prefix()



	/**
	 * @since 1.0.0
	 */
	public function apply_filters( $tag, $value )
	{

		return apply_filters( $this->hook_prefix('filter') . $tag, $value );

	}
	// apply_filters()



	/**
	 * @since 1.0.0
	 */
	public function add_filter( $tag, $function_name, $priority = 10, $accepted_args = 1 )
	{

		return add_filter(
			$this->hook_prefix('filter') . $tag,
			$function_name,
			$priority,
			$accepted_args
		);

	}
	// add_filter()



	/**
	 * @since 1.0.0
	 */
	public function remove_filter( $tag, $function_name, $priority = 10 )
	{

		return remove_filter(
			$this->hook_prefix('filter') . $tag,
			$function_name,
			$priority
		);

	}
	// remove_filter()



	/**
	 * @since 1.0.0
	 */
	public function do_action( $tag, $arg = '' )
	{

		do_action( $this->hook_prefix('action') . $tag, $arg );

	}
	// do_action()



	/**
	 * @since 1.0.0
	 */
	public function add_action( $tag, $function_name, $priority = 10, $accepted_args = 1 )
	{

		return add_action(
			$this->hook_prefix('action') . $tag,
			$function_name,
			$priority,
			$accepted_args
		);

	}
	// add_action()



	/**
	 * @since 1.0.0
	 */
	public function remove_action( $tag, $function_name, $priority = 10 )
	{

		return remove_action(
			$this->hook_prefix('action') . $tag,
			$function_name,
			$priority
		);

	}
	// remove_action()



	/**
	 * @since 1.0.0
	 */
	public function hooks()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		$this->theme_hooks();

		// -------------------------------------------------------------------------

		$this->admin_hooks();

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// hooks()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * THEME RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function theme_hooks()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		add_action( 'after_switch_theme', [ $this, 'after_switch_theme' ] );

		// -------------------------------------------------------------------------

		add_action( 'after_setup_theme', [ $this, 'plugins_load' ], 5 );

		// -------------------------------------------------------------------------

		add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ] );

		// -------------------------------------------------------------------------

		if ( 		WP_DEBUG
				 && current_user_can( $this->admin_settings['capability'] ) )
		{
			$fn = function() {

				printf(
					'<!-- %s queries in %s seconds. -->',
					get_num_queries(),
					timer_stop( 0 )
				);

			};

			// -----------------------------------------------------------------------

			add_action( 'wp_footer', $fn, 100 );
		}

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// theme_hooks()



	/**
	 * @since 1.0.0
	 */
	public function after_switch_theme()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		$redirect = $this->Settings()->defaults_add();

		// -------------------------------------------------------------------------

		$this->Post_Engagement()->maybe_create_or_update_table();

		// -------------------------------------------------------------------------

		flush_rewrite_rules();

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

		// -------------------------------------------------------------------------

		if ( $redirect )
		{
			$page_handle = $this->prefix . 'app_install';

			if ( F::admin_page_exists( $page_handle ) )
			{
				wp_redirect( F::admin_page_url( $page_handle ) );
			}
		}

	}
	// after_switch_theme()



	/**
	 * @since 1.0.0
	 */
	public function after_setup_theme()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		// @notes regarding how files are loaded in load_theme_textdomain():
		// default dir path is WP_LANG_DIR . "/themes/$textdomain-$locale.mo"
		// if translation file is in custom dir then file name is "$locale.mo"

		load_theme_textdomain(
			$this->textdomain,
			File::untrailingslashit( $this->dir_languages() )
		);

		// -------------------------------------------------------------------------

		$this->classes_load();

		// -------------------------------------------------------------------------

		$this->add_theme_support();

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// after_setup_theme()



	/**
	 * @since 1.0.0
	 */
	private function add_theme_support()
	{

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		$features = [
			'title-tag',
			'automatic-feed-links',
			'post-thumbnails',
			'comments',
			'threaded-comments',
		];

		// -------------------------------------------------------------------------

		if ( $features = $this->apply_filters( __FUNCTION__, $features ) )
		{
			foreach ( $features as $feature => $args )
			{
				if ( empty( $args ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				if ( is_array( $args ) )
				{
					add_theme_support( $feature, $args );
				}
				else
				{
					add_theme_support( $args );
				}
			}
		}

		// -------------------------------------------------------------------------

		// @notes:
		// using remove_action because if the theme doesn't support widgets
		// we get this error when viewing wp-admin/customize.php:
		// PHP Notice: Trying to get property 'title' of non-object
		// in wp-includes/class-wp-customize-widgets.php on line 905

		if ( ! in_array( 'widgets', $features ) )
		{
			global $wp_customize;

			if ( isset( $wp_customize ) )
			{
				remove_action(
					'customize_controls_print_footer_scripts',
					[$wp_customize->widgets, 'output_widget_control_templates']
				);
			}
		}

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// add_theme_support()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * THEME RELATED - END
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
	public function admin_hooks()
	{

		if ( ! is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->do_action( 'before_' . __FUNCTION__ );

		// -------------------------------------------------------------------------

		// @todo

		//add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

		// -------------------------------------------------------------------------

		$this->do_action( __FUNCTION__ );

	}
	// admin_hooks()



	/**
	 * @since 1.0.0
	public function enqueue_block_editor_assets()
	{

		// @todo: check if page is post edit

		if ( ! is_rtl() )
		{
			//return;
		}

		// -------------------------------------------------------------------------

		wp_enqueue_style(
			"{$this->prefix}editor_rtl",
			$this->url_css() . 'admin/editor_rtl.css',
			[],
			$this->version
		);

	}
	// enqueue_block_editor_assets()
	 */

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PLUGINS RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function plugins_load()
	{

		$active_plugins = apply_filters(
			'active_plugins',
			get_option( 'active_plugins' )
		);

		// -------------------------------------------------------------------------

		$plugins = [];

		// -------------------------------------------------------------------------

		$metabox_plugins = [
			'meta-box',
			'meta-box-conditional-logic',
			'meta-box-columns',
			'meta-box-group',
			'mb-user-meta',
		];

		foreach ( $metabox_plugins as $name )
		{
			$name						= "{$name}/{$name}.php";
			$plugins[$name]	= $this->dir_includes("plugins/{$name}");
		}

		// -------------------------------------------------------------------------

		foreach ( $plugins as $plugin => $plugin_path )
		{
			if ( ! in_array( $plugin, $active_plugins ) )
			{
				if ( is_readable( $plugin_path ) )
				{
					require_once $plugin_path;
				}
			}
		}

	}
	// plugins_load()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PLUGINS RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DIR PATHS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function get_path( $type, $path = '', $trailingslashit = true )
	{

		$out	= '';
		$base	= '';

		switch ( $type )
		{
			case 'dir':

				$base = get_template_directory();

				break;

			// -----------------------------------------------------------------------

			case 'url':

				$base = get_template_directory_uri();

				break;
		}

		// -------------------------------------------------------------------------

		if ( $base )
		{
			$out = path_join( $base, $path );

			// -----------------------------------------------------------------------

			if ( $trailingslashit )
			{
				$out = File::trailingslashit( $out );
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_path()



	/**
	 * @since 1.0.0
	 */
	public function dir( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->get_path( 'dir' );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir()



	/**
	 * @since 1.0.0
	 */
	public function dir_assets( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'assets', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_assets()



	/**
	 * @since 1.0.0
	 */
	public function dir_css( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir_assets( 'css', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_css()



	/**
	 * @since 1.0.0
	 */
	public function dir_js( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir_assets( 'js', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_js()



	/**
	 * @since 1.0.0
	 */
	public function dir_images( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir_assets( 'images', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_images()



	/**
	 * @since 1.0.0
	 */
	public function dir_includes( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'includes', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_includes()



	/**
	 * @since 1.0.0
	 */
	public function dir_languages( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'languages', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_languages()



	/**
	 * @since 1.0.0
	 */
	public function dir_data( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'data', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_data()



	/**
	 * @since 1.0.0
	 */
	public function dir_logs( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'logs', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_logs()



	/**
	 * @since 1.0.0
	 */
	public function dir_templates( $path = '', $trailingslashit = false )
	{

		static $dir;

		if ( ! isset( $dir ) )
		{
			$dir = $this->dir( 'templates', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $dir, $path ) : $dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// dir_templates()



	/**
	 * @since 1.0.0
	 */
	public function tmp_dir( $path = '', $trailingslashit = false )
	{

		static $tmp_dir;

		if ( ! isset( $tmp_dir ) )
		{
			$tmp_dir				= false; // set to false as default in case we return
			$wp_upload_dir	= wp_upload_dir();
			$dir_path				= path_join( $wp_upload_dir['basedir'], 'tmp' );

			if ( ! file_exists( $dir_path ) )
			{
				if ( ! wp_mkdir_p( $dir_path ) )
				{
					return;
				}

				// ---------------------------------------------------------------------

				// Create index.php file to prevent directory listing

				@file_put_contents(
					path_join( $dir_path, 'index.php' ),
					'<?php // Silence is golden'
				);

				// ---------------------------------------------------------------------

				// Add .htaccess for extra security

				@file_put_contents(
					path_join( $dir_path, '.htaccess' ),
					implode( "\n", [
						'# Disable directory browsing',
						'Options -Indexes',

						// @consider
						/*
						'',
						'# Protect files and directories',
						'<FilesMatch ".(html|js)$">',
						'  Order Allow,Deny',
						'  Deny from all',
						'</FilesMatch>',
						*/
					])
				);
			}

			// -----------------------------------------------------------------------

			$tmp_dir = $dir_path;
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $tmp_dir, $path ) : $tmp_dir;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// tmp_dir()



	/**
	 * @since 1.0.0
	 */
	public function tmp_dir_url( $path = '', $trailingslashit = false )
	{

		static $tmp_dir_url;

		if ( ! isset( $tmp_dir_url ) )
		{
			$tmp_dir_url = false; // set to false as default in case we return

			if ( ! $tmp_dir = $this->tmp_dir( ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$wp_upload_dir = wp_upload_dir();

			if ( 		! empty( $wp_upload_dir['error'] )
					 || ( empty( $wp_upload_dir['basedir'] ) && empty( $wp_upload_dir['baseurl'] ) ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$tmp_dir_url = str_replace(
				$wp_upload_dir['basedir'],
				$wp_upload_dir['baseurl'],
				wp_normalize_path( $tmp_dir )
			);
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $tmp_dir_url, $path ) : $tmp_dir_url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// tmp_dir_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * DIR PATHS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * URLS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function path_to_url( $path, $trailingslashit = false )
	{

		$path				= wp_normalize_path( $path );
		$theme_url	= get_template_directory_uri();
		$theme_dir	= wp_normalize_path( get_template_directory() );
		$theme_name	= basename( $theme_dir );
		$pattern		= '/themes\/' . preg_quote( $theme_name, '/' ) . '\/(.*?)$/';

		// -------------------------------------------------------------------------

		// this is used in case the thee is symlinked
		// extract the path after themes/theme_name/

		if ( preg_match( $pattern, $path, $matches ) )
		{
			$url = path_join( $theme_url, $matches[1] );
		}
		else
		{
			$url = str_replace( $theme_dir, $theme_url, $path );
		}

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $url ) : $url;

	}
	// path_to_url()



	/**
	 * @since 1.0.0
	 */
	public function url( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->get_path( 'url' );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url()



	/**
	 * @since 1.0.0
	 */
	public function url_assets( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'assets', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_assets()



	/**
	 * @since 1.0.0
	 */
	public function url_css( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url_assets( 'css', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_css()



	/**
	 * @since 1.0.0
	 */
	public function url_js( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url_assets( 'js', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_js()



	/**
	 * @since 1.0.0
	 */
	public function url_images( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url_assets( 'images', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_images()



	/**
	 * @since 1.0.0
	 */
	public function url_includes( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'includes', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_includes()



	/**
	 * @since 1.0.0
	 */
	public function url_languages( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'languages', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_languages()



	/**
	 * @since 1.0.0
	 */
	public function url_data( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'data', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_data()



	/**
	 * @since 1.0.0
	 */
	public function url_logs( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'logs', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_logs()



	/**
	 * @since 1.0.0
	 */
	public function url_templates( $path = '', $trailingslashit = false )
	{

		static $url;

		if ( ! isset( $url ) )
		{
			$url = $this->url( 'templates', $trailingslashit );
		}

		// -------------------------------------------------------------------------

		$out = $path ? path_join( $url, $path ) : $url;

		// -------------------------------------------------------------------------

		return $trailingslashit ? File::trailingslashit( $out ) : $out;

	}
	// url_templates()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * URLS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * i18n RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function __( $text )
	{

		if ( function_exists( '\__' ) )
		{
			return \__( $text, $this->textdomain );
		}

		// -------------------------------------------------------------------------

		return $text;

	}
	// __()



	/**
	 * alias of __()
	 *
	 * @since 1.0.0
	 */
	public function translate( $text )
	{

		return $this->__( $text );

	}
	// translate()



	/**
	 * @since 1.0.0
	 */
	public function _e( $text )
	{

		echo $this->__( $text );

	}
	// _e()



	/**
	 * @since 1.0.0
	 */
	public function _x( $text, $context = '' )
	{

		if ( function_exists( '\_x' ) )
		{
			return \_x( $text, $context, $this->textdomain );
		}

		// -------------------------------------------------------------------------

		return $text;

	}
	// _x()



	/**
	 * @since 1.0.0
	 */
	public function _n( $single, $plural, $number )
	{

		if ( function_exists( '\_n' ) )
		{
			return \_n( $single, $plural, $number, $this->textdomain );
		}

		// -------------------------------------------------------------------------

		return $single;

	}
	// _n()



	/**
	 * @since 1.0.0
	 */
	public function _nx( $single, $plural, $number, $context = '' )
	{

		if ( function_exists( '\_n' ) )
		{
			return \_nx( $single, $plural, $number, $context, $this->textdomain );
		}

		// -------------------------------------------------------------------------

		return $single;

	}
	// _nx()



	/**
	 * @since 1.0.0
	 */
	public function locale_is_arabic()
	{

		return F::starts_with( get_locale(), 'ar' );

	}
	// locale_is_arabic()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * i18n RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * REMOTE URL RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function remote_get_default_cache_time()
	{

		return F::is_wp_local_dev() ? MONTH_IN_SECONDS : WEEK_IN_SECONDS;

	}
	// remote_get_default_cache_time()



	/**
	 * @internal
	 */
	public function remote_get( $url, $cache_time = null, $args = [] )
	{

		$fn					= __FUNCTION__;
		$id					= "{$this->prefix}{$fn}";
		$cache_time	= is_null( $cache_time ) ? $this->remote_get_default_cache_time() : $cache_time;
		$hash				= md5( json_encode([
			'url'					=> $url,
			'args'				=> $args,
			'cache_time'	=> $cache_time, // to ensure we have a new transient if cache_time is changed
		]));
		$transient_name	= "{$id}_{$hash}";

		// -------------------------------------------------------------------------

		if ( false === ( $response = get_transient( $transient_name ) ) )
		{
			// validate url before making request
			$url = esc_url_raw( $url );

			if ( empty( $url ) )
			{
				return new \WP_Error(
					"{$id}_invalid_url",
					$this->__('Invalid URL provided')
				);
			}

			// -----------------------------------------------------------------------

			$response = wp_safe_remote_get( $url, $args );

			if ( is_wp_error( $response ) )
			{
				return $response;
			}

			// -----------------------------------------------------------------------

			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code != 200 )
			{
				$body = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( $error_message = F::array_get( $body, ['error', 'message'] ) )
				{
					$error_message = sprintf( '%s: %s', $id, $error_message );
				}
				else
				{
					$error_message = sprintf(
						$this->__('%s response code is %d (%s)'),
						$id,
						$response_code,
						wp_remote_retrieve_response_message( $response )
					);
				}

				// ---------------------------------------------------------------------

				return new \WP_Error( "{$id}_response_code", $error_message );
			}

			// -----------------------------------------------------------------------

			if ( $cache_time )
			{
				set_transient(
					$transient_name,
					$response,
					$cache_time
				);
			}
		}

		// -------------------------------------------------------------------------

		return $response;

	}
	// remote_get()



	/**
	 * @internal
	 */
	public function remote_get_body( ...$args )
	{

		$response = $this->remote_get( ...$args );

		if ( is_wp_error( $response ) )
		{
			return $response;
		}

		// -------------------------------------------------------------------------

		return wp_remote_retrieve_body( $response );

	}
	// remote_get_body()



	/**
	 * @internal
	 */
	public function get_meta_tags( $url, $cache_time = null, $args = [] )
	{

		static $meta_tags = [];

		$key = md5( json_encode(['url' => $url, 'args' => $args]) );

		if ( $cache_time && isset( $meta_tags[ $key ] ) )
		{
			return $meta_tags[ $key ];
		}

		// -------------------------------------------------------------------------

		// @todo: optionize limit_response_size
		$defaults		= ['limit_response_size' => 100000]; // Size in bytes 100000 bytes = 100 kb
		$args				= wp_parse_args( $args, $defaults );
		$body				= $this->remote_get_body( $url, $cache_time, $args );

		if ( is_wp_error( $body ) )
		{
			return $body;
		}

		// -------------------------------------------------------------------------

		// @consider cashing as transient if DOMDocument takes time

		try {

			$doc = new \DOMDocument();
			$doc->loadHTML( $body, LIBXML_NOERROR );

			// -----------------------------------------------------------------------

			$meta_tags[ $key ] = [];

			// -----------------------------------------------------------------------

			$titles = $doc->getElementsByTagName('title');

			if ( $titles->length > 0 )
			{
				$meta_tags[ $key ]['title'] = $titles->item(0)->textContent;
			}

			// -----------------------------------------------------------------------

			$metas = $doc->getElementsByTagName('meta');

			for ( $i = 0; $i < $metas->length; $i++ )
			{
				$meta			= $metas->item( $i );
				$name			= $meta->getAttribute('name') ?: $meta->getAttribute('property');
				$content	= $meta->getAttribute('content');

				if ( $name && $content )
				{
					$meta_tags[ $key ][ $name ] = $content;
				}
			}

			// -----------------------------------------------------------------------

			return $meta_tags[ $key ];

		} catch ( \Exception $e ) {

			return new \WP_Error( $prefix, $e->getMessage() );

		}

	}
	// get_meta_tags()



	/**
	 * @internal
	 */
	public function get_meta_tag( $url, $key, $cache_time = null, $args = [] )
	{

		$meta_tags = $this->get_meta_tags( $url, $cache_time, $args );

		if ( empty( $meta_tags ) || is_wp_error( $meta_tags ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// that means it's an array of keys based on priority

		if ( is_array( $key ) )
		{
			foreach ( $key as $priority_key )
			{
				if ( ! empty( $meta_tags[ $priority_key ] ) )
				{
					return $meta_tags[ $priority_key ];
				}
			}
		}
		else
		{
			return isset( $meta_tags[ $key ] ) ? $meta_tags[ $key ] : null;
		}

	}
	// get_meta_tag()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * REMOTE URL RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function set_as_404( $exit = true )
	{

		global $wp_query;

		$wp_query->set_404();

		status_header( 404 );

		get_template_part( 404 );

		// -------------------------------------------------------------------------

		if ( $exit )
		{
			exit;
		}

	}
	// set_as_404()



	/**
	 * @internal
	 */
	public function get_contact_email( $fallback_to_site_email = true )
	{

		// @consider to optionize it in app settings instead of having it wp-config

		if ( defined('APP') && ! empty( APP['contact_email'] ) )
		{
			return APP['contact_email'];
		}

		// -------------------------------------------------------------------------

		if ( $fallback_to_site_email )
		{
			return get_bloginfo('admin_email');
		}

	}
	// get_contact_email()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class DotAim
