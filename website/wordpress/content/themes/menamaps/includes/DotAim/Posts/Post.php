<?php

namespace DotAim\Posts;

use DotAim\Base\Singleton;
use DotAim\F;

final class Post extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $meta_keys;

	/**
	 * @since 1.0.0
	 */
	public $GeoPattern;

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

		$this->meta_keys = [
			'featured'													=> "{$this->core->meta_box_prefix}featured",
			'meta_image_url'										=> "{$this->core->meta_box_prefix}post_settings_meta_image_url",
			'meta_image_auto_generate_disable'	=> "{$this->core->meta_box_prefix}post_settings_meta_image_auto_generate_disable",
		];

		// -------------------------------------------------------------------------

		add_action( 'delete_attachment', [ $this, 'hook_delete_attachment' ] );

	}
	// init()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * INIT - END
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
	public function hook_delete_attachment( $attachment_id )
	{

		if ( $attachment_url = wp_get_attachment_url( $attachment_id ) )
		{
			// delete all of them

			delete_metadata(
				'post',
				null,
				$this->meta_keys['meta_image_url'],
				$attachment_url,
				true
			);
		}

	}
	// hook_delete_attachment()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST FIELDS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function id()
	{

		return get_the_ID();

	}
	// id()



	/**
	 * @since 1.0.0
	 */
	public function title( $post = 0 )
	{

		return get_the_title( $post );

	}
	// title()



	/**
	 * @since 1.0.0
	 */
	public function content( $post = 0 )
	{

		// @todo;
		//	- this only gets the content of $post in loop, need to allow getting by id/post..

		if ( $post )
		{
			// @todo

			//get_post_field( $field, $post = null, $context = 'display' );
		}
		else
		{
			if ( in_the_loop() )
			{
				$out = apply_filters( 'the_content', get_the_content() );
			}
			else
			{
				global $post;

				setup_postdata( $post );

				$out = apply_filters( 'the_content', get_the_content() );

				wp_reset_postdata();
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// content()



	/**
	 * @since 1.0.0
	 */
	public function excerpt( $post = null )
	{

		return apply_filters( 'the_excerpt', get_the_excerpt( $post ) );

	}
	// excerpt()



	/**
	 * @since 1.0.0
	 */
	public function author( $post = null )
	{

		return $this->field( 'post_author' );

	}
	// author()



	/**
	 * @since 1.0.0
	 */
	public function date( $format = '', $post = null )
	{

		return get_the_date( $format, $post );

	}
	// date()



	/**
	 * @since 1.0.0
	 */
	public function field( $field, $post_id = null )
	{

		if ( ! $post_id )
		{
			$post_id = $this->id();
		}

		// -------------------------------------------------------------------------

		$out = get_post_field( $field, $post_id, 'display' );

		if ( is_wp_error( $out ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// field()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST FIELDS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST URLS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function permalink( $post = 0, $leavename = false )
	{

		return get_permalink( $post, $leavename );

	}
	// permalink()



	/**
	 * @since 1.0.0
	 */
	public function edit_url( $post = 0, $context = 'display' )
	{

		return get_edit_post_link( $post, $context );

	}
	// edit_url()



	/**
	 * @since 1.0.0
	 */
	public function delete_url( $post = 0, $force_delete = false )
	{

		return get_delete_post_link( $post = 0, $deprecated = '', $force_delete );

	}
	// delete_url()



	/**
	 * @since 1.0.0
	 */
	public function date_url( $post = 0 )
	{

		return get_day_link(
			get_the_date( 'Y', $post ),
			get_the_date( 'm', $post ),
			get_the_date( 'd', $post )
		);

	}
	// date_url()



	/**
	 * Alias of date_url()
	 *
	 * @since 1.0.0
	 */
	public function day_url()
	{

		return call_user_func_array( [ $this, 'date_url' ], func_get_args() );

	}
	// day_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST URLS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST COMMENTS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	// @todo

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST COMMENTS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST TERMS AND TAXONOMIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function taxonomies( $post = '', $exclude = [] )
	{

		if ( empty( $post ) )
		{
			global $post;
		}

		// -------------------------------------------------------------------------

		if ( ! $taxonomies = get_object_taxonomies( $post, 'objects' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// only public but not sure if we need to exclude post_formats...?

		$out = [];

		foreach ( $taxonomies as $key => $taxonomy )
		{
			if ( ! $taxonomy->public || ! $taxonomy->publicly_queryable )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! empty( $exclude ) )
			{
				if ( in_array( $key, (array) $exclude ) )
				{
					continue;
				}
			}

			// -----------------------------------------------------------------------

			$out[ $key ] = $taxonomy;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// taxonomies()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST TERMS AND TAXONOMIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ATTACHMENTS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * Alias of attached_media()
	 *
	 * @since 1.0.0
	 */
	public function attachments()
	{

		return call_user_func_array( [ $this, 'attached_media' ], func_get_args() );

	}
	// attachments()



	/**
	 * @since 1.0.0
	 */
	public function attached_media( $type = 'image', $post = 0 )
	{

		return get_attached_media( $type, $post );

	}
	// attached_media()



	/**
	 * @since 1.0.0
	 */
	public function attached_images( $post = 0 )
	{

		return $this->attached_media( 'image', $post );

	}
	// attached_images()



	/**
	 * @since 1.0.0
	 */
	public function attached_images_urls( $size = '', $post = 0, $icon = false )
	{

		if ( ! $images = $this->attached_images( $post ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = [];

		// -------------------------------------------------------------------------

		foreach ( (array) $images as $image )
		{
			if ( empty( $image->ID ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! $url = wp_get_attachment_image_url( $image->ID, $size, $icon ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$out[ $image->ID ] = $url;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// attached_images_urls()



	/**
	 * @since 1.0.0
	 */
	public function first_attached_image( $post = 0 )
	{

		if ( ! $out = $this->attached_images( $post ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return array_values( $out )[0];

	}
	// first_attached_image()



	/**
	 * @since 1.0.0
	 */
	public function first_attached_image_url( $size = '', $post = 0, $icon = false )
	{

		if ( ! $out = $this->attached_images_urls( $size, $post, $icon ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return array_values( $out )[0];

	}
	// first_attached_image_url()



	/**
	 * @internal
	 */
	public function attachment_post_title_from_file_name( $file_name )
	{

		// simply remove the extension

		return preg_replace( '/\.[^.]+$/', '', basename( $file_name ) );

	}
	// attachment_post_title_from_file_name()



	/**
	 * @internal
	 */
	public function attachment_exists( $filename )
	{

		$post_title = $this->attachment_post_title_from_file_name( $filename );

		// -------------------------------------------------------------------------

		$attachment = $this->get_by_title( $post_title, 'attachment' );

		if ( ! empty( $attachment->ID ) )
		{
			return $attachment->ID;
		}

	}
	// attachment_exists()



	/**
	 * @internal
	 */
	public function attachment_save( $path, $post_id = 0 )
	{

		if ( ! @is_readable( $path ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$attachment_filename = basename( $path );
		//$attachment_filename = "{$post_title}-{$attachment_filename}";

		// -------------------------------------------------------------------------

		if ( $found_id = $this->attachment_exists( $attachment_filename ) )
		{
			return $found_id;
		}

		// -------------------------------------------------------------------------

		// upload it

		$file_bits					= file_get_contents( $path );
		$attachment_upload	= wp_upload_bits( $attachment_filename, null, $file_bits );

		// wp_upload_bits returns [ 'file' => $new_file, 'url' => $url, 'error' => false ];

		if ( 		! empty( $attachment_upload['error'] )
				 || empty( $attachment_upload['file'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// insert attachment

		$attachment_file = $attachment_upload['file'];

		if ( ! $attachment_mime_type = F::array_get( $attachment_upload, ['type'] ) )
		{
			$attachment_filetype	= wp_check_filetype( basename( $attachment_file ), null );
			$attachment_mime_type	= $attachment_filetype['type'];
		}

		if ( ! empty( $attachment_upload['url'] ) )
		{
			$guid = $attachment_upload['url'];
		}
		else
		{
			$wp_upload_dir = wp_upload_dir();

			$guid = path_join( $wp_upload_dir['url'], basename( $attachment_file ) );
		}

		$attachment_args = [
			'guid'					=> $guid,
			'post_mime_type'=> $attachment_mime_type,
			'post_title'		=> $this->attachment_post_title_from_file_name( $attachment_file ),
		];

		if ( ! $attachment_id = wp_insert_attachment( $attachment_args, $attachment_file, $post_id ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// attachment metadata

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $attachment_file );

		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		// -------------------------------------------------------------------------

		return $attachment_id;

	}
	// attachment_save()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ATTACHMENTS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST THUMBNAIL/FEATURED IMAGE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function thumbnail_id( $post = null )
	{

		return get_post_thumbnail_id( $post );

	}
	// thumbnail_id()



	/**
	 * @since 1.0.0
	 */
	public function thumbnail_url( $size = 'thumbnail', $post = null )
	{

		return get_the_post_thumbnail_url( $post, $size );

	}
	// thumbnail_url()



	/**
	 * Alias of thumbnail_id()
	 *
	 * @since 1.0.0
	 */
	public function featured_image_id()
	{

		return call_user_func_array( [ $this, 'thumbnail_id' ], func_get_args() );

	}
	// featured_image_id()



	/**
	 * Alias of thumbnail_url()
	 *
	 * @since 1.0.0
	 */
	public function featured_image_url()
	{

		return call_user_func_array( [ $this, 'thumbnail_url' ], func_get_args() );

	}
	// featured_image_url()



	/**
	 * @since 1.0.0
	 */
	public function generate_meta_image( $post_id, $args = [] )
	{

		if ( ! $post_id )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// make sure Imagick exists

		if ( 		! extension_loaded('imagick')
				 || ! class_exists( 'Imagick', false )
				 || ! class_exists( 'ImagickDraw', false ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$defaults = [
			'title'												=> get_the_title( $post_id ),
			'logo'												=> $this->core->dir_images('logo_for_meta_image.png'),
			'width'												=> 1200,
			'height'											=> 630,
			'background_color'						=> '', // F::get_random_hex_color(),
			'background_gradient'					=> F::get_random_hex_color('light') . '-' . F::get_random_hex_color('light'),
			'font'												=> '',
			'font_size'										=> 60,
			'font_weight'									=> 900,
			'text_gravity'								=> \Imagick::GRAVITY_NORTH,
			'text_color'									=> '#ffffff',
			'stroke_width'								=> 1,
			'stroke_color'								=> '#000000',
			'color_for_dark_background'		=> '#FAFAFA',
			'color_for_light_background'	=> '#333333',
			'padding'											=> 200,
			'ext'													=> 'png',
			'meta_key'										=> $this->meta_keys['meta_image_url'],
			'set_post_thumbnail'					=> false,
			//'refresh'										=> $this->core->is_local_dev(),
		];

		// -------------------------------------------------------------------------

		$meta_tags_settings = $this->core->Settings()->get_component_app_settings( 'misc', 'meta_tags' );

		if ( ! empty( $meta_tags_settings ) )
		{
			foreach ( $defaults as $key => &$value )
			{
				// @consider
				// keeping default random colors if setting is empty for certain options

				if ( isset( $meta_tags_settings["image_auto_generate_{$key}"] ) )
				{
					$value = $meta_tags_settings["image_auto_generate_{$key}"];
				}
			}
		}

		// -------------------------------------------------------------------------

		$args = wp_parse_args( $args, $defaults );

		// -------------------------------------------------------------------------

		if ( ! $args['title'] && ! $args['logo'] )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $args['title'] ) )
		{
			$args['title'] = html_entity_decode( $args['title'] );
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $args['logo'] ) )
		{
			$args['logo'] = do_shortcode( $args['logo'] );
		}

		// -------------------------------------------------------------------------

		if ( empty( $args['background_color'] ) )
		{
			if ( ! empty( $args['background_gradient'] ) )
			{
				// get the last gradient color so that the text color is set accordingly

				$gradients = explode( '-', $args['background_gradient'] );

				$args['background_color'] = end( $gradients );
			}
			else
			{
				$args['background_color'] = F::string_to_hex_color( $args['title'] );
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $args['text_color'] ) )
		{
			$args['text_color'] = F::get_contrast_color(
				$args['background_color'],
				$args['color_for_dark_background'],
				$args['color_for_light_background']
			);
		}

		// -------------------------------------------------------------------------

		if ( $args['stroke_width'] && ! $args['stroke_color'] )
		{
			$args['stroke_color'] = F::get_contrast_color(
				$args['text_color'],
				$args['color_for_dark_background'],
				$args['color_for_light_background']
			);
		}

		// -------------------------------------------------------------------------

		extract( $args );

		// -------------------------------------------------------------------------

		$dir = path_join( __DIR__, 'cache' );

		if ( ! wp_mkdir_p( $dir ) )
		{
			return;
		}

		$filename	= md5( json_encode( $args ) ) . ".{$ext}";
		$file			= path_join( $dir, $filename );

		// -------------------------------------------------------------------------

		// check if attachment exists before generating image

		if ( $attachment_id = $this->attachment_exists( $filename ) )
		{
			if ( ! $attachment_url = wp_get_attachment_url( $attachment_id ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			if ( $meta_key )
			{
				update_post_meta( $post_id, $meta_key, $attachment_url );
			}

			// -----------------------------------------------------------------------

			if ( $set_post_thumbnail )
			{
				set_post_thumbnail( $post_id, $attachment_id );
			}

			// -----------------------------------------------------------------------

			return $attachment_url;
		}

		// -------------------------------------------------------------------------

		$inner_width	= $width  - ( $padding * 2 );
		$inner_height	= $height - ( $padding * 2 );

		// -------------------------------------------------------------------------

		$base_img = new \Imagick();

		if ( ! empty( $background_gradient ) )
		{
			$base_img->newPseudoImage( $width, $height, "gradient:{$background_gradient}" );
		}
		else
		{
			$base_img->newImage( $width, $height, $background_color );
		}

		$base_img->setImageFormat( $ext );

		// -------------------------------------------------------------------------

		if ( $logo )
		{
			// @notes: the try/catch is used in case logo doesn't exist

			try {

				$logo_img = new \Imagick();
				$logo_img->readImage( $logo );
				$logo_img->scaleImage( $inner_width / 2, $inner_height / 2, true );

				$logo_img_Width		= $logo_img->getImageWidth();
				$logo_img_Height	= $logo_img->getImageHeight();
				$logo_img_x				= ( $width - $logo_img_Width ) / 2;
				$logo_img_y 			= $text_gravity === \Imagick::GRAVITY_SOUTH ? $padding : $padding / 2;

				$base_img->compositeImage(
					$logo_img,
					\Imagick::COMPOSITE_OVER,
					absint( $logo_img_x ),
					absint( $logo_img_y )
				);

			} catch ( \Exception $e ) {

				if ( $this->core->is_local_dev() )
				{
					debug_log( $e->getMessage() );
				}

			}
		}

		// -------------------------------------------------------------------------

		$text_img = new \ImagickDraw();

		if ( $stroke_width )
		{
			$text_img->setStrokeWidth( $stroke_width );

			if ( $stroke_color )
			{
				$text_img->setStrokeColor( $stroke_color );
			}
		}

		if ( ! empty( $font ) )
		{
			$text_img->setFont( $font );
		}

		if ( ! empty( $font_size ) )
		{
			$text_img->setFontSize( $font_size );
		}

		if ( ! empty( $font_weight ) )
		{
			$text_img->setFontWeight( $font_weight );
		}

		$text_img->setFillColor( $text_color );
		$text_img->setGravity( $text_gravity );

		$text								= trim( $title );
		$text_height				= 0;
		$line_height_ratio	= 1;

		// Run until we find a font size that doesn't exceed $height in pixels

		while ( 0 == $text_height || $text_height > $inner_height )
		{
			if ( $text_height > 0 )
			{
				$font_size--;
			}

			$text_img->setFontSize( $font_size );

			// -----------------------------------------------------------------------

			$words				= preg_split( '%\s%', $text, -1, PREG_SPLIT_NO_EMPTY );
			$lines				= [];
			$i						= 0;
			$line_height	= 0;

			while ( count( $words ) > 0 )
			{
				$metrics			= $base_img->queryFontMetrics( $text_img, implode( ' ', array_slice( $words, 0, ++$i ) ) );
				$line_height	= max( $metrics['textHeight'], $line_height );

				if ( $metrics['textWidth'] > $inner_width || count( $words ) < $i )
				{
					$lines[]	= implode( ' ', array_slice( $words, 0, --$i ) );
					$words		= array_slice( $words, $i );
					$i				= 0;
				}
			}

			// -----------------------------------------------------------------------

			$text_height = count( $lines ) * $line_height * $line_height_ratio;

			// don't run endlessly if something goes wrong

			if ( $text_height === 0 )
			{
				return false;
			}
		}

		// -------------------------------------------------------------------------

		// write text on base image

		$x_pos = 0;
		$y_pos = 0;

		switch ( $text_gravity )
		{
			case \Imagick::GRAVITY_NORTH:
			case \Imagick::GRAVITY_NORTHWEST:
			case \Imagick::GRAVITY_NORTHEAST:

				$y_pos = ( $height - $text_height ) / 2;

				break;

			// -----------------------------------------------------------------------

			case \Imagick::GRAVITY_SOUTH:
			case \Imagick::GRAVITY_SOUTHWEST:
			case \Imagick::GRAVITY_SOUTHEAST:

				$y_pos = $padding;

				break;
		}

		for ( $i = 0; $i < count( $lines ); $i++ )
		{
			$base_img->annotateImage(
				$text_img,
				$x_pos,
				$y_pos + ( $i * $line_height * $line_height_ratio ),
				0,
				$lines[ $i ]
			);
		}

		// -------------------------------------------------------------------------

		$text_img->clear();

		$generated = $base_img->writeImage( $file );

		$base_img->clear();

		if ( ! $generated )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $attachment_id = $this->attachment_save( $file, $post_id ) )
		{
			return;
		}

		if ( $file && ! $this->core->is_local_dev() )
		{
			@unlink( $file );
		}

		// -------------------------------------------------------------------------

		if ( ! $attachment_url = wp_get_attachment_url( $attachment_id ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $meta_key )
		{
			update_post_meta( $post_id, $meta_key, $attachment_url );
		}

		// -------------------------------------------------------------------------

		if ( $set_post_thumbnail )
		{
			set_post_thumbnail( $post_id, $attachment_id );
		}

		// -------------------------------------------------------------------------

		return $attachment_url;

	}
	// generate_meta_image()



	/**
	 * @since 1.0.0
	 */
	public function get_meta_image_url( $post_id )
	{

		// 1. check meta value

		if ( $meta_image_url = get_post_meta( $post_id, $this->meta_keys['meta_image_url'], true ) )
		{
			return $meta_image_url;
		}

		// -------------------------------------------------------------------------

		// 2. check featured image

		if ( $thumbnail_url = $this->thumbnail_url( 'xl', $post_id ) )
		{
			return $thumbnail_url;
		}

		// -------------------------------------------------------------------------

		// 3. auto generate one

		if ( ! get_post_meta( $post_id, $this->meta_keys['meta_image_auto_generate_disable'], true ) )
		{
			if ( ! $post_type = get_post_type( $post_id ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$enable_for_post_types = $this->core->Settings()->get_component_app_settings(
				'misc',
				'meta_tags',
				'image_auto_generate_enable'
			);

			if ( 		! empty( $enable_for_post_types )
					 && in_array( $post_type, $enable_for_post_types ) )
			{
				return $this->generate_meta_image( $post_id );
			}
		}

	}
	// get_meta_image_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST THUMBNAIL/FEATURED IMAGE - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * IMAGES IN POST CONTENT - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function content_images_urls( $size = '', $post = null )
	{

		if ( ! $content = $this->content( $post ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes: how to know they are not attached images

		preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $images );

		if ( empty( $images[1] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return $images[1];

	}
	// content_images_urls()



	/**
	 * @since 1.0.0
	 */
	public function first_content_image_url( $size = '', $post = null )
	{

		if ( ! $out = $this->content_images_urls( $post ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return array_values( $out )[0];

	}
	// first_content_image_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * IMAGES IN POST CONTENT - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST IMAGE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function image_url( $size = '', $args = [], $post = null )
	{

		$defaults = [
			/*
			 * auto
			 * custom_field
			 * thumbnail
			 * first_attached_image
			 * first_content_image
			 * random_paths
			 * GeoPattern
			*/
			'source'		=> 'auto',
			'checks'		=> [				// source auto checks order for a post image
				'thumbnail',
				'meta_image',
				'first_attached_image',
				//'first_content_image',
			],
			'meta_key'	=> '',			// applicable to source auto or meta_image
			'fallback'	=> [
				//'random_paths',
				'GeoPattern',
			],
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( 'auto' !== $source )
		{
			$checks = [ $source ];
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $fallback ) )
		{
			$checks = array_merge( $checks, (array) $fallback );
		}

		// -------------------------------------------------------------------------

		$out = '';

		// -------------------------------------------------------------------------

		//F::print_r( $checks, '$checks' );

		foreach ( $checks as $fn )
		{
			$fn = $fn . '_url';

			// -----------------------------------------------------------------------

			if ( ! method_exists( $this, $fn ) )
			{
				//F::print_r( $fn, '! method_exists( $this, $fn )' );

				continue;
			}

			// -----------------------------------------------------------------------

			if ( 'meta_image_url' === $fn )
			{
				if ( empty( $meta_key ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				if ( $out = $this->meta_image_url( $meta_key, $size, $post ) )
				{
					break;
				}
			}
			else
			{
				if ( $out = $this->{$fn}( $size, $post ) )
				{
					break;
				}
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// image_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST IMAGE - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function meta_value( $key, $args = [], $post_id = null, $single = false )
	{

		if ( ! F::starts_with( $key, $this->core->meta_box_prefix ) )
		{
			$key = "{$this->core->meta_box_prefix}{$key}";
		}

		// -------------------------------------------------------------------------

		if ( function_exists( '\rwmb_meta' ) )
		{
			return rwmb_meta( $key, $args, $post_id );
		}
		else
		{
			if ( ! $post_id )
			{
				$post_id = $this->id();
			}

			// -----------------------------------------------------------------------

			return get_post_meta( $post_id, $key, $single );
		}

	}
	// meta_value()



	/**
	 * @since 1.0.0
	 */
	public function meta_image_url( $key, $size, $post_id = null )
	{

		$args = [
			'size'	=> $size,
			'limit'	=> 1,
		];

		// -------------------------------------------------------------------------

		if ( ! $out = $this->meta_value( $key, $args, $post_id ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// this would be the case of a single value

		if ( ! is_array( $out ) )
		{
			return $out;
		}

		// -------------------------------------------------------------------------

		if ( ! $out = array_values( $out )[0] )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// 2 possible scenarios:
		//	1. returned by rwmb_meta which should is an associative array with url element
		//	2. returned by get_post_meta which should have been flattened to the first element above
		//
		// we only need to handle the case of rwmb_meta

		if ( is_array( $out ) )
		{
			if ( empty( $out['url'] ) )
			{
				return;
			}
			else
			{
				$out = $out['url'];
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// meta_image_url()



	/**
	 * @since 1.0.0
	 */
	public function is_featured( $post_id = null )
	{

		if ( ! $post_id )
		{
			$post_id = $this->id();
		}

		// -------------------------------------------------------------------------

		return get_post_meta( $post_id, $this->meta_keys['featured'], true );

	}
	// is_featured()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function title_attribute( $post = 0, $before = '', $after = '' )
	{

		$args = [
			'before'=> $before,
			'after'	=> $after,
			'echo'	=> false,
		];

		// -------------------------------------------------------------------------

		if ( $post )
		{
			$args['post'] = $post;
		}

		// -------------------------------------------------------------------------

		return the_title_attribute( $args );

	}
	// title_attribute()



	/**
	 * a replacement of deprecated get_page_by_title()
	 */
	public function get_by_title( $title, $post_type = 'post', $post_status = 'all' )
	{

		$posts = get_posts([
			'title'				=> $title,
			'post_type'		=> $post_type,
			'post_status'	=> $post_status,
			'numberposts'	=> 1,
			'orderby'			=> 'post_date ID',
			'order'				=> 'ASC',
		]);

		if ( ! empty( $posts ) )
		{
			return $posts[0];
		}

	}
	// get_by_title()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MISC - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GeoPattern - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function GeoPattern()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		/*
		if ( ! class_exists( '\RedeyeVentures\GeoPattern' ) )
		{
			require_once $this->core->dir_includes('lib/vendor/autoload.php');
		}
		*/

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = new \RedeyeVentures\GeoPattern\GeoPattern();

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// GeoPattern()



	/**
	 * @since 1.0.0
	 */
	public function GeoPattern_url( $size = '', $post = '' )
	{

		//$this->GeoPattern()->setString('Mastering Markdown');
		//$this->GeoPattern()->setBaseColor('#ffcc00');
		//$this->GeoPattern()->setGenerator('sine_waves');

		// -------------------------------------------------------------------------

		//$svg		= $this->GeoPattern()->toSVG();
		//$base64	= $this->GeoPattern()->toBase64();
		//$dataURI= $this->GeoPattern()->toDataURI(); // data:image/svg+xml;base64,...

		// -------------------------------------------------------------------------

		//F::print_r( $post, '$post' );

		/*
		if ( ! empty( $post->post_title ) )
		{
			$this->GeoPattern()->setString( $post->post_title );
		}
		*/

		if ( $title = get_the_title( $post ) )
		{
			$this->GeoPattern()->setString( $title );
		}

		// -------------------------------------------------------------------------

		$out = $this->GeoPattern()->toDataURI();

		// -------------------------------------------------------------------------

		return $out;

	}
	// GeoPattern_url()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GeoPattern - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Post
