<?php

namespace DotAim\Browser;

use \DotAim\F;

class Screenshot
{

	/**
	 * @internal
	 */
	const DEFAULT_WIDTH					= 1366;
	const DEFAULT_HEIGHT				= 768;
	const DEFAULT_TIMEOUT				= 30000;
	const DEFAULT_NETWORK_IDLE	= 1000;
	const DEFAULT_USER_AGENT		= 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36';



	/**
	 * Save a screenshot from HTML content or URL
	 *
	 * @param string $source HTML content or URL to capture
	 * @param string $output_path Path to save the screenshot
	 * @param array $args Screenshot options
	 * @return string|\WP_Error Path to saved screenshot or WP_Error on failure
	 */
	public static function save( $source, $output_path, $args = [] )
	{

		if ( ! $source )
		{
			return new \WP_Error(
				__FUNCTION__ . '_source_required',
				self::__('Source required.')
			);
		}

		if ( ! $output_path )
		{
			return new \WP_Error(
				__FUNCTION__ . '_output_path_required',
				self::__('Output path required.')
			);
		}

		// -------------------------------------------------------------------------

		$defaults = [

			'browser'						=> 'chrome',
			'browser_bin_paths'	=> null, // in case we need to override in "{$browser}_path"()

			// -----------------------------------------------------------------------

			'flags'	=> [
				'--headless',
				'--disable-gpu',
				'--no-sandbox',
				'--disable-crash-reporter',
				'--disable-crashpad',
				'--no-crashpad',
				'--disable-dev-shm-usage',
				'--hide-scrollbars',
				'--force-device-scale-factor=1',
				'--disable-extensions',
			],

			// -----------------------------------------------------------------------

			'width'		=> self::DEFAULT_WIDTH,
			'height'	=> self::DEFAULT_HEIGHT,

			// @todo
			//'full_page' => false,

			// -----------------------------------------------------------------------

			'user_agent' => self::DEFAULT_USER_AGENT,

			// -----------------------------------------------------------------------

			'timeout' => self::DEFAULT_TIMEOUT,

			// @consider
			//'network_idle' => self::DEFAULT_NETWORK_IDLE,

			// -----------------------------------------------------------------------

			'refresh' => false,

		];

		$args = array_merge( $defaults, $args );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! $refresh && file_exists( $output_path ) )
		{
			return $output_path;
		}

		// -------------------------------------------------------------------------

		$method_name = "{$browser}_path";

		if ( ! method_exists( __CLASS__, $method_name ) )
		{
			return new \WP_Error(
				__FUNCTION__ . '_not_supported',
				sprintf( self::__('Browser "%s" is not supported.'), $browser )
			);
		}

		if ( ! $browser_path = self::$method_name( $browser_bin_paths ) )
		{
			return new \WP_Error(
				__FUNCTION__ . '_not_found',
				sprintf( self::__('Browser "%s" exucutable not found.'), $browser )
			);
		}

		// -------------------------------------------------------------------------

		try {

			if ( F::is_url( $source ) )
			{
				$url = $source;
			}
			else
			{
				$tmp_filename = sprintf('%s.html', pathinfo( $output_path, PATHINFO_FILENAME ) );

				if ( ! $tmp_file = DA()->tmp_dir( $tmp_filename ) )
				{
					return new \WP_Error(
						__FUNCTION__ . '_tmp_dir_error',
						self::__('Unable to find or create temporary directory.')
					);
				}

				// ---------------------------------------------------------------------

				$bytes_written = file_put_contents( $tmp_file, $source );

				if ( ! $bytes_written )
				{
					return new \WP_Error(
						__FUNCTION__ . '_file_put_contents_error',
						sprintf(
							self::__('Nothing saved to temporary file "%s".'),
							$tmp_file
						)
					);
				}

				// ---------------------------------------------------------------------

				// @consider which is better to use

				//$url = "file://{$tmp_file}";
				$url = DA()->tmp_dir_url( $tmp_filename );
			}

			// -----------------------------------------------------------------------

			$cmd_args = [];

			if ( ! empty( $flags ) )
			{
				$cmd_args[] = implode( ' ', $flags );
			}

			if ( $user_agent )
			{
				$cmd_args[] = sprintf('--user-agent="%s"', $user_agent);
			}

			if ( $timeout > 0 )
			{
				$cmd_args[] = sprintf('--timeout=%d', $timeout);
			}

			$cmd_args[] = sprintf('--window-size=%d,%d', $width, $height);

			$cmd_args[] = sprintf('--screenshot=%s', escapeshellarg( $output_path ));

			$cmd_args[] = escapeshellarg( $url );

			// -----------------------------------------------------------------------

			$cmd = sprintf(
				'%s %s',
				escapeshellcmd( $browser_path ),
				implode( ' ', $cmd_args )
			);

			$output			= [];
			$return_var	= 0;

			exec( "{$cmd} 2>&1", $output, $return_var );

			// -----------------------------------------------------------------------

			if ( 		! empty( $tmp_file )
					 && ! DA()->is_local_dev() )
			{
				@unlink( $tmp_file );
			}

			// -----------------------------------------------------------------------

			if ( $return_var !== 0 )
			{
				self::terminate_browser_processes( $browser );
			}

			// -----------------------------------------------------------------------

			if ( ! file_exists( $output_path ) )
			{
				$error_output = implode( "\n", $output );

				return new \WP_Error(
					__FUNCTION__ . '_unable_to_save_screenshot',
					sprintf(
						self::__('Unable to save screenshot to "%s". Error: %s'),
						$output_path,
						$error_output
					),
					$cmd
				);
			}

			// -----------------------------------------------------------------------

			return $output_path;

		} catch ( \Exception $e ) {

			// make sure browser is shutdown/closed

			self::terminate_browser_processes( $browser );

			// -----------------------------------------------------------------------

			return new \WP_Error(
				__FUNCTION__ . '_error',
				sprintf(
					self::__('Browser "%s" Screenshot Error: %s.'),
					$browser,
					$e->getMessage()
				)
			);

		}

	}
	// save()



	/**
	 * Terminate any hanging browser processes
	 *
	 * @param string $browser Browser name
	 * @return void
	 */
	private static function terminate_browser_processes( $browser )
	{

		try {

			switch ( $browser )
			{
				case 'chrome':

					@exec('pkill -f chrome > /dev/null 2>&1');
					@exec('pkill -f chromium > /dev/null 2>&1');

					break;
			}

		} catch ( \Exception $e ) {}

	}
	// terminate_browser_processes()



	/**
	 * Get Chrome executable path
	 *
	 * @return string|false Path to Chrome executable or false if not found
	 */
	public static function chrome_path( $bin_paths = null )
	{

		static $path;

		if ( isset( $path ) )
		{
			return $path;
		}

		// -------------------------------------------------------------------------

		$path = false;

		// -------------------------------------------------------------------------

		if ( is_null( $bin_paths ) )
		{
			$bin_paths = [
				'/usr/bin/google-chrome',
				'/usr/bin/google-chrome-stable',
				'/usr/bin/chromium',
				'/usr/bin/chromium-browser',
			];
		}

		// -------------------------------------------------------------------------

		foreach ( $bin_paths as $bin_path )
		{
			if ( file_exists( $bin_path ) && is_executable( $bin_path ) )
			{
				$path = $bin_path;

				break;
			}
		}

		// -------------------------------------------------------------------------

		return $path;

	}
	// chrome_path()



	/**
	 * @internal
	 */
	private static function __( ...$args )
	{

		return DA()->__( ...$args );

	}
	// __()

}
// class Screenshot
