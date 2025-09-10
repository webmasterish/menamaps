<?php

namespace DotAim;

/**
 * @since 1.0.0
 */
class File
{

	/**
	 * @since 1.0.0
	 */
	public static function namespace_separator()
	{

		return '\\';

	}
	// namespace_separator()



	/**
	 * Alias of namespace_separator()
	 *
	 * @since 1.0.0
	 */
	public static function ns_separator()
	{

		return self::namespace_separator();

	}
	// ns_separator()



	/**
	 * @since 1.0.0
	 */
	public static function directory_separator()
	{

		return DIRECTORY_SEPARATOR;

	}
	// directory_separator()



	/**
	 * Alias of directory_separator()
	 *
	 * @since 1.0.0
	 */
	public static function dir_separator()
	{

		return self::directory_separator();

	}
	// dir_separator()



	/**
	 * @since 1.0.0
	 */
	public static function trailingslashit( $str = '' )
	{

		if ( ! $str )
		{
			//return $str;
		}

		// -------------------------------------------------------------------------

		if ( function_exists( '\trailingslashit' ) )
		{
			return \trailingslashit( $str );
		}

		// -------------------------------------------------------------------------

		return rtrim( $str, self::dir_separator() ) . self::dir_separator();

		// -------------------------------------------------------------------------

		// or...

		// return self::untrailingslashit( $str ) . self::dir_separator();

		// but as trailingslashit function doesn't exist, so would be the case for
		// untrailingslashit, hence, it's faster to simply use rtrim

	}
	// trailingslashit()



	/**
	 * @since 1.0.0
	 */
	public static function untrailingslashit( $str = '' )
	{

		if ( ! $str )
		{
			return $str;
		}

		// -------------------------------------------------------------------------

		if ( function_exists( '\untrailingslashit' ) )
		{
			return \untrailingslashit( $str );
		}

		// -------------------------------------------------------------------------

		return rtrim( $str, self::dir_separator() );

	}
	// untrailingslashit()



	/**
	 * @since 1.0.0
	 */
	public static function sanitize_path( $path = '' )
	{

		if ( ! $path )
		{
			return $path;
		}

		// -------------------------------------------------------------------------

		 // sanitize for Win32 installs

		if ( 	defined( 'DIRECTORY_SEPARATOR' )
			 && '\\' == DIRECTORY_SEPARATOR )
		{
			$path = str_replace ( '\\', self::dir_separator(), $path );
		}

		// -------------------------------------------------------------------------

		 // remove any duplicate slash

		$path = preg_replace( '|/+|', self::dir_separator(), $path );

		// -------------------------------------------------------------------------

		return $path;

	}
	// sanitize_path()



	/**
	 * @since 1.0.0
	 */
	public static function is_readable( $file )
	{

		if ( ! $file )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		if ( F::is_url( $file ) )
		{
			$file_headers = @get_headers( $file );

			if ( ! empty( $file_headers[0] ) )
			{
				if ( 'HTTP/1.1 404 Not Found' === $file_headers[0] )
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ( ! @is_readable( $file ) )
			{
				return false;
			}
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// is_readable()



	/**
	 * @since 1.0.0
	 */
	public static function is_writable( $file, $chmod = true )
	{

		if ( ! $file )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		if ( ! @is_writable( $file ) )
		{
			if ( ! $chmod )
			{
				return false;
			}

			// -----------------------------------------------------------------------

			// try chmoding the file,
			// and if that doesn't work try it with it's parent directory

			if ( ! @chmod( $file, 0666 ) )
			{
				$dir = @dirname( $file );

				if ( ! @is_writable( $dir ) )
				{
					if ( ! @chmod( $dir, 0666 ) )
					{
						return false;
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// is_writable()



	/**
	 * @since 1.0.0
	 */
	public static function is_older_than( $file, $seconds )
	{

		if ( ! file_exists( $file ) )
		{
			// if it doesn't so i can consider it older and it needs to be refreshed

			return true;
		}

		// -------------------------------------------------------------------------

		return filemtime( $file ) + (int) $seconds < time();

	}
	// is_older_than()



	/**
	 * @since 1.0.0
	 */
	public static function save_data( $file, $data )
	{

		$dir = dirname( $file );

		if ( wp_mkdir_p( $dir ) )
		{
			return file_put_contents( $file, $data );
		}

	}
	// save_data()



	/**
	 * @since 1.0.0
	 */
	public static function clear( $file )
	{

		$f = @fopen( $file , 'r+' );

		if ( $f !== false )
		{
			ftruncate( $f, 0 );

			fclose( $f );
		}

	}
	// clear()



	/**
	 * @since 1.0.0
	 */
	public static function get_directory_files( $dir_path, $exclude = '', $ext = 'php', $ksort = true )
	{

		if ( ! $dir_path && ! $ext )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$dir_path = self::trailingslashit( $dir_path );

		// -------------------------------------------------------------------------

		if ( ! $dir = @dir( $dir_path ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $exclude )
		{
			// make sure it's an array

			if ( ! is_array( $exclude ) )
			{
				$exclude = preg_split( "/[\s,]+/", $exclude, -1, PREG_SPLIT_NO_EMPTY );
			}

			// -----------------------------------------------------------------------

			// make sure file has ext

			foreach ( $exclude as $k => $file )
			{
				if ( ! preg_match( '|\.' . $ext . '$|', $file ) )
				{
					$exclude[ $k ] = $file . '.' . $ext;
				}

			}
		}

		// -------------------------------------------------------------------------

		$out = [];

		// -------------------------------------------------------------------------

		while( ( $file = $dir->read() ) !== false )
		{
			// skip "." and ".."

			if ( preg_match( '|^\.+$|', $file ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// skip any file that doesn't match the extension

			if ( ! preg_match( '|\.' . $ext . '$|', $file ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// skip excluded files

			if ( $exclude && in_array( $file, $exclude ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$ext_len 			= strlen( $ext ) + 1;
			$file_no_ext 	= substr( $file, 0, -$ext_len );
			$file_name 		= $file_no_ext;
			$file_path 		= $dir_path . $file;

			// -----------------------------------------------------------------------

			$out[ $file_no_ext ] = [
				'file'	=> $file,
				'name'	=> $file_name,
				'path'	=> $file_path,
				'url'		=> self::map_path( $file_path ),
				'class'	=> self::get_class_name_from_file( $file_path ),
			];
		}

		// -------------------------------------------------------------------------

		if ( empty( $out ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $ksort )
		{
			ksort( $out );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_directory_files()



	/**
	 * @since 1.0.0
	 */
	public static function get_class_name_from_file( $file )
	{

		if ( ! file_exists( $file ) )
		{
			return;
		}

		if ( ! $content = file_get_contents( $file ) )
		{
			return;
		}

		if ( ! preg_match('/\bnamespace\s+([^\s;]+).*?\bclass\s+([^\s{]+)/s', $content, $matches) )
		{
			return;
		}

		// class name is required
		if ( empty( $matches[2] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$namespace	= ! empty( $matches[1] ) ? trim( $matches[1] ) : '';
		$class			= trim( $matches[2] );
		$out				= [
			'namespace'	=> $namespace,
			'name'			=> $class,
		];

		$out['full_name'] = strlen( $namespace )
											? $namespace . self::ns_separator() . $class
											: $class;

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_class_name_from_file()

	/*
	public static function get_class_name_from_file( $file )
	{

		if ( ! $handle = @fopen( $file, 'r' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$class = $namespace = $buffer = '';

		$i = 0;

		// -------------------------------------------------------------------------

		while ( ! $class )
		{
			if ( feof( $handle ) )
			{
				break;
			}

			// -----------------------------------------------------------------------

			$buffer .= fread( $handle, 512 );

			if ( strpos( $buffer, '{' ) === false )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! $tokens = @token_get_all( $buffer ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			for ( ; $i < count( $tokens ); $i++ )
			{
				// get namespace name

				if ( $tokens[$i][0] === T_NAMESPACE )
				{
					for ( $j = $i + 1; $j < count( $tokens ); $j++ )
					{
						if ( $tokens[$j][0] === T_STRING )
						{
							$namespace .= self::ns_separator() . $tokens[$j][1];
						}
						elseif ( $tokens[$j] === '{' || $tokens[$j] === ';' )
						{
							break;
						}
					}
				}

				// ---------------------------------------------------------------------

				// get class name

				if ( $tokens[$i][0] === T_CLASS )
				{
					for ( $j = $i + 1; $j < count( $tokens ); $j++ )
					{
						if ( $tokens[$j] === '{' )
						{
							$class = $tokens[ $i + 2 ][1];
						}
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( false !== $handle )
		{
			@fclose( $handle );
		}

		// -------------------------------------------------------------------------

		if ( empty( $class ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = [
			'namespace'	=> $namespace,
			'name'			=> $class,
		];

		$out['full_name'] = ! empty( $namespace ) ? $namespace . self::ns_separator() . $class : $class;

		// or

		//$out['fully_qualified_name'] = ! empty( $namespace ) ? $namespace . self::ns_separator() . $class : $class;

		// -------------------------------------------------------------------------

		return $out;

	}
	*/
	// get_class_name_from_file()



	/**
	 * @since 1.0.0
	 */
	public static function map_path( $path )
	{

		if ( ! $path )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$real_path = realpath( $path );

		// -------------------------------------------------------------------------

		$dir	= null;
		$is_dir	= false;

		if ( is_file( $real_path ) )
		{
			$dir = dirname( $real_path );
		}
		elseif ( is_dir( $real_path ) )
		{
			$dir = $real_path;

			$is_dir = true;
		}

		if ( ! $dir )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( strlen( $dir ) < strlen( $_SERVER['DOCUMENT_ROOT'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( 	isset( $_SERVER['HTTPS'] )
			 && strtolower( $_SERVER['HTTPS'] ) != 'off' )
		{
			$schema = 'https';
		}
		else
		{
			$schema = 'http';
		}

		// -------------------------------------------------------------------------

		// add trailing slash to host

		$http_host = self::trailingslashit( $_SERVER['HTTP_HOST'] );

		// -------------------------------------------------------------------------

		// remove trailing slash from document root

		$document_root = self::untrailingslashit( $_SERVER['DOCUMENT_ROOT'] );

		// -------------------------------------------------------------------------

		// path without schema, because sanitize_path removes duplicate slashes

		$path_no_schema = $http_host . substr( $real_path, strlen( $document_root ) );

		// -------------------------------------------------------------------------

		// sanitize path

		$url = self::sanitize_path( $path_no_schema );

		// -------------------------------------------------------------------------

		// only add a trailing slash if it's a directory

		if ( $is_dir )
		{
			$url = self::trailingslashit( $url );
		}

		// -------------------------------------------------------------------------

		// add schema

		$out = "{$schema}://{$url}";

		// -------------------------------------------------------------------------

		return $out;

	}
	// map_path()



	/**
	 * @since 1.0.0
	 */
	public static function concatenate_files( $src, $dest_file, $args = '' )
	{

		if ( ! $src )
		{
			return false;
		}

		if ( ! $dest_file )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'src_dir' 			=> '',
			'dest_dir' 			=> '',
			'dest_dir_url' 	=> '',
			'rebuild' 			=> false,
			'minify' 				=> false,
			'minify_search' => [
				"\r\n",
				"\r",
				"\n",
				"\t"
			],
			'remove_comments' 	=> false,
			'files_separator' 	=> '', 		// i.e "\n"
			'add_file_names' 		=> false,
			'add_end_semicolon' => false, 	// for js files
			'before' 						=> '',
			'after' 						=> '',
			'add_end_of_file' 	=> false,
			'return_content'		=> false, 	// if set to false filemtime is returned
			'return_file_path'	=> false, 	// if set to false filemtime is returned
			'return_file_url'		=> false, 	// if set to false filemtime is returned
		];

		$args = F::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		// split source paths by " ", \r, \t, \n, \f, comma and non empty

		if ( ! is_array( $src ) )
		{
			$src_paths = preg_split( "/[\s,]+/", $src, -1, PREG_SPLIT_NO_EMPTY );
		}
		else
		{
			$src_paths = $src;
		}

		// -------------------------------------------------------------------------

		if ( empty( $src_paths ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$create 				= false;
		$changed 				= false;
		$src_dir 				= self::trailingslashit( $src_dir );
		$dest_dir 			= self::trailingslashit( $dest_dir );
		$dest_dir_url 	= self::trailingslashit( $dest_dir_url );
		$dest_file_url 	= $dest_dir_url . $dest_file;
		$dest_file 			= $dest_dir 	. $dest_file;
		$dest_file_ext 	= pathinfo( $dest_file, PATHINFO_EXTENSION );

		// -------------------------------------------------------------------------

		// verify if we can write to destination file

		if ( ! file_exists( $dest_file ) )
		{
			$dest_dir_path = pathinfo( $dest_file, PATHINFO_DIRNAME );

			if ( ! self::is_writable( $dest_dir_path ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			// the file is new, so make sure to create and rebuild

			$create = $rebuild = true;
		}
		else
		{
			if ( ! self::is_writable( $dest_file ) )
			{
				return;
			}
		}

		// -------------------------------------------------------------------------

		// make sure source files and directories are readable

		$src_files = [];

		foreach ( $src_paths as $path )
		{
			if ( function_exists( '\do_shortcode' ) )
			{
				$path = \do_shortcode( $path );
			}

			// -----------------------------------------------------------------------

			$path = $src_dir . ltrim( $path, '/' );

			// -----------------------------------------------------------------------

			if ( ! @file_exists( $path ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( @is_dir( $path ) )
			{
				$_glob = glob( self::trailingslashit( $path ) . '*.' . $dest_file_ext );

				if ( ! is_array( $_glob ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				foreach ( $_glob as $_file )
				{
					if ( $_file === $dest_file )
					{
						continue;
					}

					// -------------------------------------------------------------------

					$src_files[] = $_file;
				}
			}
			elseif ( @is_file( $path ) )
			{
				if ( ! self::is_readable( $path ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				$src_files[] = $path;
			}

		}

		if ( ! $src_files )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$src_files = array_unique( $src_files );

		// -------------------------------------------------------------------------

		// create blank file and set permissions

		if ( $create )
		{
			touch( $dest_file );
			chmod( $dest_file, 0666 );
		}

		// -------------------------------------------------------------------------

		// check if any src file is modified

		$dest_filemtime	= filemtime( $dest_file );

		foreach ( (array)$src_files as $_file )
		{
			if ( filemtime( $_file ) > $dest_filemtime )
			{
				$changed = true;

				// ---------------------------------------------------------------------

				break;
			}
		}

		// -------------------------------------------------------------------------

		$contents = '';

		if ( $changed || $rebuild || $create )
		{
			$contents = [];

			foreach ( (array) $src_files as $_file )
			{
				if ( ! $_content = trim( file_get_contents( $_file ) ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				if ( $remove_comments )
				{
					$_content = trim( preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $_content ) );
				}

				// ---------------------------------------------------------------------

				if ( $minify )
				{
					$_content = F::minify( $_content, ['search' => $minify_search] );
					//$_content = str_replace( $minify_search, '', $_content );
				}

				// ---------------------------------------------------------------------

				if ( $add_file_names )
				{
					$_content = "\n" . '/*' .  basename( $_file ) . '*/' . "\n" . $_content;
				}

				// ---------------------------------------------------------------------

				if ( $add_end_semicolon && ( $dest_file_ext === 'js' ) )
				{
					if ( substr( $_content, -1, 1 ) != ';' )
					{
						$_content .= ';';
					}
				}

				// ---------------------------------------------------------------------

				$contents[] = $_content;
			}

			// -----------------------------------------------------------------------

			if ( $add_end_of_file )
			{
				$after .= "\n" . sprintf(
					'/* End of file ./%s | Modified: %s */',
					basename( $dest_file ),
					date( 'Y-m-d H:i:s' )
				);
			}

			// -----------------------------------------------------------------------

			$contents = implode( $files_separator, $contents );
			$contents = $before . trim( $contents ) . $after;

			// -----------------------------------------------------------------------

			file_put_contents( $dest_file, $contents );

			// -----------------------------------------------------------------------

			// get new modification time

			$dest_filemtime = filemtime( $dest_file );
		}

		// -------------------------------------------------------------------------

		$out = '';

		if ( $return_content )
		{
			if ( ! $contents && file_exists( $dest_file ) )
			{
				$contents = file_get_contents( $dest_file );
			}

			// -----------------------------------------------------------------------

			$out = $contents;
		}
		else
		{
			// @todo:
			//
			//	should consider including filetime with name instead of param as there
			//	could be issues with browser caching...
			//
			//	this would require stripping the extension and
			//	then appending it to new filename
			//
			//	the name would be "{$dest_file_url}_{$dest_filemtime}.{ext}"

			if ( $return_file_path )
			{
				$out = $dest_file . '?' . $dest_filemtime;
			}
			elseif ( $return_file_url )
			{
				$out = $dest_file_url . '?' . $dest_filemtime;
			}

		}

		// -------------------------------------------------------------------------

		if ( ! $out )
		{
			$out = $dest_filemtime;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// concatenate_files()



	/**
	 * @since 1.0.0
	 */
	public static function export( $content, $filename = '', $content_type = 'text/plain', $charset = 'UTF-8' )
	{

		$disposition = ['attachment'];

		if ( ! empty( $filename ) )
		{
			$disposition[] = 'filename=' . $filename;
		}

		$disposition = implode( '; ', $disposition );

		// -------------------------------------------------------------------------

		$type = [ $content_type ];

		if ( ! empty( $charset ) )
		{
			$type[] = 'charset=' . $charset;
		}

		$type = implode( '; ', $type );

		// -------------------------------------------------------------------------

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: ' . $disposition );
		header( 'Content-Type: ' . $type, $replace = true );

		// -------------------------------------------------------------------------

		echo $content;

		// -------------------------------------------------------------------------

		die();

	}
	// export()



	/**
	 * @internal
	 */
	public static function download( $url, $file_path, $force_refresh = false )
	{

		// check if file exists if we're not forcing a refresh

		if ( ! $force_refresh )
		{
			if ( file_exists( $file_path ) )
			{
				return $file_path;
			}
		}

		// -------------------------------------------------------------------------

		// validate URL before making request

		$url = esc_url_raw( $url );

		if ( empty( $url ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$args	= [];

		// -------------------------------------------------------------------------

		$response	= wp_safe_remote_get( $url, $args );

		if ( is_wp_error( $response ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// make sure response code is 200

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 		empty( $response_code )
				 || 200 != $response_code )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $body = wp_remote_retrieve_body( $response ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$dir = dirname( $file_path );

		if ( ! wp_mkdir_p( $dir ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$ifp = @fopen( $file_path, 'wb' );

		if ( ! $ifp )
		{
			return;
		}

		// -------------------------------------------------------------------------

		@fwrite( $ifp, $body );

		fclose( $ifp );

		// -------------------------------------------------------------------------

		// Set correct file permissions

		clearstatcache();

		$stat		= @stat( dirname( $file_path ) );
		$perms	= $stat['mode'] & 0007777;
		$perms	= $perms & 0000666;
		@chmod( $file_path, $perms );

		clearstatcache();

		// -------------------------------------------------------------------------

		return $file_path;

	}
	// download()



	/**
	 * @since 1.0.0
	public static function header_data( $file )
	{

	}
	// header_data()
	 */

}
// class File
