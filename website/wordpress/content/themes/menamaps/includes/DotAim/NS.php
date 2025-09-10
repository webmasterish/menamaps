<?php

namespace DotAim;

/**
 * @since 1.0.0
 */
class NS
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	// Public

	/**
	 * @since 1.0.0
	 */
	public $name 					= null;
	public $to_array			= null;
	public $vendor				= null;
	public $separator 		= null; // '\\';
	public $dir_separator = null; // '/';
	public $include_path	= null;
	public $path					= null;
	public $file_extension= '.php';

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
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct( $namespace = '', $include_path = '', $set_all = false )
	{

		// set namespace - default to current namespace if empty

		$this->name = ! empty( $namespace ) ? $namespace : __NAMESPACE__;

		// -------------------------------------------------------------------------

		// set include path - default to current dir

		$this->include_path = ! empty( $include_path ) ? $include_path : __DIR__;

		// -------------------------------------------------------------------------

		// by default populate/set all is set to false so it only populates when needed - saves resourses

		if ( $set_all )
		{
			$this->set( $namespace );
		}

		// -------------------------------------------------------------------------

		// register autoload

		spl_autoload_register( [ $this, 'autoload' ] );

	}
	// __construct()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AUTOLOAD - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function autoload( $class_name )
	{

		if ( ! $this->is_ns_class( $class_name ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$file_path = $this->class_path( $class_name );

		// -------------------------------------------------------------------------

		if ( is_readable( $file_path ) )
		{
			require_once $file_path;
		}

	}
	// autoload()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AUTOLOAD - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function set( $namespace, $include_path = '' )
	{

		// set namespace

		$this->name = $namespace;

		// -------------------------------------------------------------------------

		// only set it if it has a value

		if ( ! empty( $include_path ) )
		{
			$this->include_path = $include_path;
		}

		// -------------------------------------------------------------------------

		// get all properties (populated with new namespace)

		$properties = get_object_vars( $this );

		// -------------------------------------------------------------------------

		$out = [];

		foreach ( $properties as $name => $value )
		{
			//if ( 'name' !== $name || 'include_path' !== $name )
			//{
				// clear it

				//$this->{$name} = null;

				// ---------------------------------------------------------------------

				// set it

				$fn = [ $this, $name ];

				if ( is_callable( $fn ) )
				{
					// clear it

					//$this->{$name} = null;

					$this->{$name} = call_user_func( $fn );
				}
			//}

			// -----------------------------------------------------------------------

			// add to output

			//$out[ $name ] = $value;
			$out[ $name ] = $this->{$name};
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// set()



	/**
	 * @since 1.0.0
	 */
	public function name()
	{

		return $this->{__FUNCTION__};

	}
	// name()



	/**
	 * @since 1.0.0
	 */
	public function include_path()
	{

		return $this->{__FUNCTION__};

	}
	// include_path()



	/**
	 * @since 1.0.0
	 */
	public function separator()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = '\\';

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// separator()



	/**
	 * Alias of separator()
	 *
	 * @since 1.0.0
	 */
	public function namespace_separator()
	{

		return $this->separator();

	}
	// namespace_separator()



	/**
	 * @since 1.0.0
	 */
	public function dir_separator()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		if ( defined( 'DIRECTORY_SEPARATOR' ) && DIRECTORY_SEPARATOR )
		{
			$this->{__FUNCTION__} = DIRECTORY_SEPARATOR;
		}
		else
		{
			$this->{__FUNCTION__} = '/';
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// dir_separator()



	/**
	 * Alias of dir_separator()
	 *
	 * @since 1.0.0
	 */
	public function directory_separator()
	{

		return $this->dir_separator();

	}
	// directory_separator()



	/**
	 * @since 1.0.0
	 */
	public function file_extension()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = '.php';

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// file_extension()



	/**
	 * @since 1.0.0
	 */
	public function to_array()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = explode( $this->separator(), $this->name() );

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// to_array()



	/**
	 * @since 1.0.0
	 */
	public function vendor()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$ns_arr = $this->to_array();

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = ! empty( $ns_arr[0] ) ? $ns_arr[0] : '';

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// vendor()



	/**
	 * @since 1.0.0
	 */
	public function path()
	{

		if ( ! is_null( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$ns_path = '';

		/*
		if ( $ns = trim( str_replace( __NAMESPACE__, '', $this->name() ), $this->separator() ) )
		{
			$ns_path = $this->to_path( $ns );
		}
		*/

		// -------------------------------------------------------------------------

		$include_path = '';

		if ( ! empty( $this->include_path ) )
		{
			//$include_path = File::trailingslashit( $this->include_path );
			$include_path = rtrim( $this->include_path, $this->dir_separator() ) . $this->dir_separator();
		}

		// -------------------------------------------------------------------------

		if ( $path = $include_path . $ns_path )
		{
			//$path = File::trailingslashit( $path );
			$path = rtrim( $path, $this->dir_separator() ) . $this->dir_separator();
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $path;

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// path()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CLASS RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function class_name( $class_name )
	{

		$last_ns_pos= $this->last_ns_pos( $class_name );

		if ( false !== $last_ns_pos ) {

			$namespace	= substr( $class_name, 0, $last_ns_pos );
			$class_name = substr( $class_name, $last_ns_pos + 1 );

		}

		// -------------------------------------------------------------------------

		return $class_name;

	}
	// class_name()



	/**
	 * @since 1.0.0
	 */
	public function class_id( $class_name )
	{

		$parsed = $this->parse_class_name( $class_name );

		// -------------------------------------------------------------------------

		if ( ! empty( $parsed['namespace'] ) )
		{
			// only add class name if it's not the same as a sub-namespace

			if ( ! in_array( $parsed['name'], $parsed['namespace'] ) )
			{
				$parsed['namespace'][] = $parsed['name'];
			}

			// -----------------------------------------------------------------------

			$out = implode( '_', $parsed['namespace'] ) ;
		}
		else
		{
			$out = str_replace( $this->separator(), '_', $class_name );
		}

		// -------------------------------------------------------------------------

		$out = strtolower( $out );

		// -------------------------------------------------------------------------

		return $out;

	}
	// class_id()


	/**
	 * @since 1.0.0
	 */
	public function class_file_name( $class_name )
	{

		return $this->class_name( $class_name ) . $this->file_extension();

	}
	// class_file_name()



	/**
	 * @since 1.0.0
	 */
	public function class_relative_path( $class_name )
	{

		// remove leading namespace and ns separators from begining and end

		$class_name = trim( str_replace( $this->name(), '', $class_name ), $this->separator() );

		// -------------------------------------------------------------------------

		// convert to path and add file extension

		$path = $this->to_path( $class_name ) . $this->file_extension();

		// -------------------------------------------------------------------------

		return $path;

	}
	// class_relative_path()



	/**
	 * @since 1.0.0
	 */
	public function class_path( $class_name )
	{

		return $this->path() . $this->class_relative_path( $class_name );

	}
	// class_path()



	/**
	 * @since 1.0.0
	 */
	public function is_ns_class( $class_name )
	{

		// trim ns separator "\" if any

		$class_name = trim( $class_name, $this->separator() );

		// -------------------------------------------------------------------------

		$ns		= $this->name() . $this->separator();
		$ns_len	= strlen( $ns );

		// -------------------------------------------------------------------------

		return ( $ns === substr( $class_name, 0, $ns_len ) );

	}
	// is_ns_class()



	/**
	 * @since 1.0.0
	 */
	public function parse_class_name( $class_name )
	{

		// trim ns separator "\" if any

		$class_name = trim( $class_name, $this->separator() );

		// -------------------------------------------------------------------------

		$arr = explode( $this->separator(), $class_name );

		// -------------------------------------------------------------------------

		return [
			'namespace'	=> array_slice( $arr, 0, -1 ),
			//'namespace'	=> array_values( array_filter( array_slice( $arr, 0, -1 ) ) ),
			'name'			=> join( '', array_slice( $arr, -1 ) ),
		];

	}
	// parse_class_name()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CLASS RELATED - END
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
	public function last_ns_pos( $class_name )
	{

		return strripos( $class_name, $this->separator() );

	}
	// last_ns_pos()



	/**
	 * @since 1.0.0
	 */
	public function to_path( $string )
	{

		return str_replace( $this->separator(), $this->dir_separator(), $string );

	}
	// to_path()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class NS
