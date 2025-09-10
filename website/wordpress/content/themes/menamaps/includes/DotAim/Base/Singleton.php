<?php

namespace DotAim\Base;

use DotAim\F;

class Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @since 1.0.0
	 */
	protected $core;

	/**
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * @since 1.0.0
	 */
	public $id;

	/**
	 * @since 1.0.0
	 */
	public $prefix;

	/**
	 * @since 1.0.0
	 */
	public $class_name;
	public $name;
	public $dir;
	public $url;
	protected $methods;

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
	public static function instance( $core )
	{

		if ( is_null( self::$_instance ) )
		{
			self::$_instance = new self( $core );
		}

		// -------------------------------------------------------------------------

		return self::$_instance;

	}
	// instance()

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



	/**
	 * @since 1.0.0
	 */
	public function __construct( $core )
	{

		$this->core				= $core;
		$this->version		= $this->core->version;

		// -------------------------------------------------------------------------

		$this->class_name	= $this->core->NS()->class_name( get_class( $this ) );
		$this->name				= F::humanize( $this->class_name );

		// -------------------------------------------------------------------------

		$this->id			= $this->core->NS()->class_id( get_class( $this ) );
		$this->prefix	= "{$this->id}_";

		// -------------------------------------------------------------------------

		// call init() if it's callable

		$this->core->call_object_func( $this, 'init' );

	}
	// __construct()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROTECTED METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function hook_prefix( $hook_type = '', $full_prefix = false )
	{

		$out = strtolower("{$this->class_name}_");

		// -------------------------------------------------------------------------

		if ( $full_prefix )
		{
			$out = $this->core->hook_prefix( $hook_type ) . $out;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// hook_prefix()



	/**
	 * @since 1.0.0
	 */
	protected function fn_hook_name( $fn, $hook_type = '', $full_prefix = false )
	{

		return $this->hook_prefix( $hook_type, $full_prefix ) . $fn;

	}
	// fn_hook_name()



	/**
	 * @since 1.0.0
	 */
	protected function fn_filter_name( $fn, $full_prefix = false )
	{

		return $this->fn_hook_name( $fn, 'filter', $full_prefix );

	}
	// fn_filter_name()



	/**
	 * @since 1.0.0
	 */
	protected function fn_action_name( $fn, $full_prefix = false )
	{

		return $this->fn_hook_name( $fn, 'action', $full_prefix );

	}
	// fn_action_name()



	/**
	 * @since 1.0.0
	 */
	protected function common_fn( $fn, $out = '', $echo = true, $tab = '' )
	{

		//$out = $this->core->apply_filters( $this->fn_filter_name( $fn ), $out );
		$out = $this->apply_filters( $fn, $out );

		// -------------------------------------------------------------------------

		if ( $echo && $out )
		{
			if ( is_array( $out ) )
			{
				$out = $tab . implode( "\n" . $tab, $out ) . "\n";
			}

			// -----------------------------------------------------------------------

			echo $out;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// common_fn()



	/**
	 * @since 1.0.0
	 */
	protected function apply_filters( $tag, $value )
	{

		return $this->core->apply_filters( $this->fn_filter_name( $tag ), $value );

	}
	// apply_filters()



	/**
	 * @since 1.0.0
	 */
	protected function do_action( $tag, $arg = '' )
	{

		return $this->core->do_action( $this->fn_action_name( $tag ), $arg );

	}
	// do_action()



	/**
	 * @since 1.0.0
	 */
	protected function __( ...$args )
	{

		return $this->core->__( ...$args );

	}
	// __()



	/**
	 * @since 1.0.0
	 */
	protected function _e( ...$args )
	{

		$this->core->_e( ...$args );

	}
	// _e()



	/**
	 * @since 1.0.0
	 */
	protected function _x( ...$args )
	{

		return $this->core->_x( ...$args );

	}
	// _x()



	/**
	 * @since 1.0.0
	 */
	protected function _n( ...$args )
	{

		return $this->core->_n( ...$args );

	}
	// _n()



	/**
	 * @since 1.0.0
	 */
	protected function _nx( ...$args )
	{

		return $this->core->_nx( ...$args );

	}
	// _nx()



	/**
	 * @since 1.0.0
	 */
	protected function methods()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$class			= get_class( $this ); // __CLASS__ if used in child class
		$reflector	= new \ReflectionClass( $class );
		$methods		= $reflector->getMethods( \ReflectionMethod::IS_PUBLIC );

		foreach ( $methods as $method )
		{
			// skip functions not in this class (the ones in parent class)

			if ( $class !== $method->class )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$this->{__FUNCTION__}[] = $method->name;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// methods()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROTECTED METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * alias of F::html_attributes()
	 */
	public function attr( ...$args )
	{

		return F::html_attributes( ...$args );

	}
	// attr()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
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

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PATHS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Singleton
