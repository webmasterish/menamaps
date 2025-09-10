<?php

/**
 * @internal
 */
function debug_log_file()
{

	static $log_file = null;

	if ( isset( $log_file ) )
	{
		return $log_file;
	}

	// ---------------------------------------------------------------------------

	if ( 		defined('WP_DEBUG_LOG')
			 && WP_DEBUG_LOG
			 && is_string( WP_DEBUG_LOG ) )
	{
		$log_file = WP_DEBUG_LOG;
	}
	else
	{
		$log_file = path_join( __DIR__, 'logs/debug.log' );
	}

	// ---------------------------------------------------------------------------

	return $log_file;

}
// debug_log_file()



/**
 * @internal
 */
function debug_log( $val, $title = '', $backtrace_index = 1, $log_file = null )
{

	if ( ! $log_file )
	{
		$log_file	= debug_log_file();
	}

	// ---------------------------------------------------------------------------

	if ( ! file_exists( $log_file ) )
	{
		$dest_dir = pathinfo( $log_file, PATHINFO_DIRNAME );

		if ( ! wp_mkdir_p( $dest_dir ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_writable( $dest_dir ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		touch( $log_file );
		// @consider
		//chmod( $log_file, 0666 );
	}

	// ---------------------------------------------------------------------------

	if ( ! is_writable( $log_file ) )
	{
		return;
	}

	// ---------------------------------------------------------------------------

	$trace			= '';
	$backtrace	= debug_backtrace();

	// ---------------------------------------------------------------------------

	if ( ! empty( $backtrace[ $backtrace_index ] ) )
	{
		$trace			= [];
		$backtrace	= $backtrace[ $backtrace_index ];

		// -------------------------------------------------------------------------

		if ( ! empty( $backtrace['file'] ) )
		{
			$trace[] = "File  : {$backtrace['file']}";
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $backtrace['line'] ) )
		{
			$trace[] = "Line  : {$backtrace['line']}";
		}

		// -------------------------------------------------------------------------

		$method = [];

		if ( ! empty( $backtrace['class'] ) )
		{
			$method[] = $backtrace['class'];
		}

		if ( ! empty( $backtrace['function'] ) )
		{
			$method[] = $backtrace['function'];
		}

		if ( $method )
		{
			$trace[] = 'Method: ' . implode( '::', $method );
		}

		// -------------------------------------------------------------------------

		if ( $trace )
		{
			$trace = implode( "\n", $trace );
		}
	}

	// ---------------------------------------------------------------------------

	if ( 		! empty( $val )
			 && ( is_array( $val ) || is_object( $val ) ) )
	{
		$val = print_r( $val, true );
	}
	else
	{
		// we can assume it's empty
		if ( is_array( $val ) || is_object( $val ) )
		{
			$val = '';
		}
	}

	// ---------------------------------------------------------------------------

	$title = $title ? $title : 'Val   ';

	$entry = [
		'Date  : ' . date('Y-m-d H:i:s'),
		$trace,
		"{$title}: {$val}",
		//'backtrace: ' . "\n" . print_r( $backtrace, true ),
	];

	$entry = implode( "\n", $entry ) . "\n";

	// ---------------------------------------------------------------------------

	file_put_contents(
		$log_file,
		$entry . PHP_EOL,
		FILE_APPEND | LOCK_EX
	);

}
// debug_log



/* =============================================================================
 * -----------------------------------------------------------------------------
 * LOAD DOTAIM - START
 * -----------------------------------------------------------------------------
 * ========================================================================== */

/**
 * @internal
 */
require_once 'includes/DotAim/DotAim.php';

// -----------------------------------------------------------------------------

/**
 * Returns the main instance of DotAim class.
 */
function DotAim()
{

	$instance = \DotAim\DotAim::instance();

	// ---------------------------------------------------------------------------

	if ( true === $instance->system_check() )
	{
		return $instance;
	}

}
// DotAim_Loader()



/**
 * Alias of DotAim() function.
 */
function DA()
{

	return DotAim();

}
// DA()



/**
 * Load
 */
DotAim();

/* =============================================================================
 * -----------------------------------------------------------------------------
 * LOAD DOTAIM - END
 * -----------------------------------------------------------------------------
 * ========================================================================== */
