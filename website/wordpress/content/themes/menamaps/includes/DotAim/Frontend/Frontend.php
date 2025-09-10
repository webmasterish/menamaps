<?php

namespace DotAim\Frontend;

use DotAim\Base\Singleton;
use DotAim\File;
use DotAim\F;

final class Frontend extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $meta_key_post_views;
	public $meta_key_post_upvotes;
	public $meta_key_post_external_url_clicks;
	public $Settings;

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

		$this->meta_key_post_views								= ''; // $this->core->Listings()->meta_key_post_views;
		$this->meta_key_post_upvotes							= ''; // $this->core->Upvotes()->meta_key_post_upvotes;
		$this->meta_key_post_external_url_clicks	= ''; // $this->core->Listings()->meta_key_post_external_url_clicks;

		// -------------------------------------------------------------------------

		$this->hooks();

		// -------------------------------------------------------------------------

		$this->transients_hooks();

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
	public function hooks()
	{

		if ( is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		/*
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts_search' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts_tax_posts_per_page' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts_tax_exclude_children' ] );

		add_filter( 'the_posts', [ $this, 'the_posts_featured_at_top' ] );
		*/

		// -------------------------------------------------------------------------

		add_action( 'init', [ $this, 'cleanup_head' ] );

		// -------------------------------------------------------------------------

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );

		// -------------------------------------------------------------------------

		add_action( 'wp_head', [ $this, 'wp_head_meta_tags' ], 5 );

		// -------------------------------------------------------------------------

		if ( current_theme_supports('comments') )
		{
			add_filter(
				'comment_form_default_fields',
				[ $this, 'comment_form_default_fields' ]
			);
		}

		// -------------------------------------------------------------------------

		add_filter( 'language_attributes', [ $this, 'language_attributes_filter'], 10, 2 );

		add_filter( 'body_class', [ $this, 'body_class_filter' ] );

	}
	// hooks()



	/**
	 * @since 1.0.0
	 */
	public function pre_get_posts( $query )
	{

		if ( is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_home() )
		{
			$post_type = 'post';
		}
		else
		{
			$post_type = $query->get('post_type');
		}

		if ( ! $post_type )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @todo: optionize or check the ones passed in Template

		$post_types = [
			'post',
			'listing',
		];

		if ( ! in_array( $post_type, $post_types ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_home() )
		{
			if ( ! $query->is_post_type_archive( $post_type ) )
			{
				return;
			}

			if ( ! $query->is_main_query() )
			{
				return;
			}
		}

		if ( $query->is_search() )
		{
			return;
		}

		if ( $query->is_feed() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$new_query_vars = [];

		// -------------------------------------------------------------------------

		$panel_id = $this->Settings()->post_type_panel_id( $post_type );

		// -------------------------------------------------------------------------

		if ( $posts_per_page = $this->Settings()->get("{$panel_id}_loop_posts_per_page") )
		{
			$new_query_vars['posts_per_page'] = $posts_per_page;
		}

		// -------------------------------------------------------------------------

		if ( 		isset( $_GET['sort'] )
				 && ! $this->Settings()->get("{$panel_id}_loop_disable_sorting") )
		{
			$sort_params	= explode( '-', $_GET['sort'] );
			$sort_orderby	= $sort_params[0] ?? '';
			$sort_order		= $sort_params[1] ?? '';
		}
		else
		{
			$sort_orderby	= $this->Settings()->get("{$panel_id}_loop_default_sort_by");
			$sort_order		= $this->Settings()->get("{$panel_id}_loop_default_sort_order");
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $sort_order ) )
		{
			$new_query_vars['order'] = $sort_order;
		}

		// -------------------------------------------------------------------------

		switch ( $sort_orderby )
		{
			// @notes:
			//	eventhough i would have prefered tp use random as default,
			//	but random wouldn't make much sense when paginating
			//
			/*
			case 'random':

				$new_query_vars['orderby']	= 'rand';

				break;
			*/
			// -----------------------------------------------------------------------

			case 'date':

				$new_query_vars['orderby']	= 'date';

				break;

			// -----------------------------------------------------------------------

			case 'name':

				$new_query_vars['orderby']	= 'title';

				break;

			// -----------------------------------------------------------------------

			case 'views':

				$new_query_vars['orderby']	= 'meta_value_num date';
				$new_query_vars['meta_key']	= $this->meta_key_post_views;

				break;

			// -----------------------------------------------------------------------

			case 'upvotes':

				$new_query_vars['orderby']	= 'meta_value_num date';
				$new_query_vars['meta_key']	= $this->meta_key_post_upvotes;

				break;
		}

		// -------------------------------------------------------------------------

		if ( $new_query_vars )
		{
			foreach ( $new_query_vars as $query_var => $value )
			{
				$query->set( $query_var, $value );
			}
		}

	}
	// pre_get_posts()



	/**
	 * @since 1.0.0
	 */
	public function pre_get_posts_search( $query )
	{

		if ( is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $query->is_search() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$new_query_vars = [];

		// -------------------------------------------------------------------------

		// @consider using include instead of exclude done in

		/*
		$new_query_vars['post_type'] = [
			'listing',
			'post',
		];
		*/

		// -------------------------------------------------------------------------

		if ( $results_per_page = $this->core->Settings->get('search_results_per_page') )
		{
			$new_query_vars['posts_per_page'] = $results_per_page;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $new_query_vars ) )
		{
			foreach ( $new_query_vars as $query_var => $value )
			{
				$query->set( $query_var, $value );
			}
		}

	}
	// pre_get_posts_search()



	/**
	 * @since 1.0.0
	 */
	public function pre_get_posts_tax_posts_per_page( $query )
	{

		if ( is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_category() && ! is_tag() && ! is_tax() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @todo: try using a common function for this and pre_get_posts

		$new_query_vars = [];

		// -------------------------------------------------------------------------

		$taxonomy_name	= get_queried_object()->taxonomy;
		$panel_id				= $this->Settings()->taxonomy_panel_id( $taxonomy_name );

		// -------------------------------------------------------------------------

		if ( $posts_per_page = $this->Settings()->get("{$panel_id}_loop_posts_per_page") )
		{
			$new_query_vars['posts_per_page'] = $posts_per_page;
		}

		// -------------------------------------------------------------------------

		if ( 		isset( $_GET['sort'] )
				 && ! $this->Settings()->get("{$panel_id}_loop_disable_sorting") )
		{
			$sort_params	= explode( '-', $_GET['sort'] );
			$sort_orderby	= $sort_params[0] ?? '';
			$sort_order		= $sort_params[1] ?? '';
		}
		else
		{
			$sort_orderby	= $this->Settings()->get("{$panel_id}_loop_default_sort_by");
			$sort_order		= $this->Settings()->get("{$panel_id}_loop_default_sort_order");
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $sort_order ) )
		{
			$new_query_vars['order'] = $sort_order;
		}

		// -------------------------------------------------------------------------

		switch ( $sort_orderby )
		{
			// @notes:
			//	eventhough i would have prefered to use random as default,
			//	but random wouldn't make much sense when paginating
			//
			/*
			case 'random':

				$new_query_vars['orderby']	= 'rand';

				break;
			*/
			// -----------------------------------------------------------------------

			case 'date':

				$new_query_vars['orderby']	= 'date';

				break;

			// -----------------------------------------------------------------------

			case 'name':

				$new_query_vars['orderby']	= 'title';

				break;

			// -----------------------------------------------------------------------

			case 'views':

				$new_query_vars['orderby']	= 'meta_value_num date';
				$new_query_vars['meta_key']	= $this->meta_key_post_views;

				break;

			// -----------------------------------------------------------------------

			case 'upvotes':

				$new_query_vars['orderby']	= 'meta_value_num date';
				$new_query_vars['meta_key']	= $this->meta_key_post_upvotes;

				break;
		}

		// -------------------------------------------------------------------------

		if ( $new_query_vars )
		{
			foreach ( $new_query_vars as $query_var => $value )
			{
				$query->set( $query_var, $value );
			}
		}

	}
	// pre_get_posts_tax_posts_per_page()



	/**
	 * @since 1.0.0
	 */
	public function pre_get_posts_tax_exclude_children( $query )
	{

		if ( is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @todo:
		//	needs improvement, and needs to accomodate for other taxonomies
		//	should consider making it optional
		//	consider making part of a common pre_get_posts_tax() function


		if ( ! isset( $query->query_vars['listing_type'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$tax_query_args = [
			[
				'taxonomy'					=> 'listing_type',
				'field'							=> 'slug',
				'terms'							=> $query->query_vars['listing_type'],
				'include_children'	=> false,
			],
		];

		// -------------------------------------------------------------------------

		$query->set( 'tax_query', $tax_query_args );

	}
	// pre_get_posts_tax_exclude_children()



	/**
	 * @since 1.0.0
	 */
	public function the_posts_featured_at_top( $posts )
	{

		// @todo: get from settings
		$post_types = ['listing'];

		// -------------------------------------------------------------------------

		$sticky_posts = get_option('sticky_posts');

		// -------------------------------------------------------------------------

		if ( 		empty( $post_types )
				 || empty( $sticky_posts )
				 || is_admin()
				 || ! is_main_query()
				 || is_paged()
				 || is_singular() )
		{
			return $posts;
		}

		// -------------------------------------------------------------------------

		$queried_object = get_queried_object();

		//$q = &$this->query_vars;

		if ( ! is_post_type_archive( $post_types ) )
		{
			if ( is_category() || is_tag() || is_tax() )
			{
				if ( empty( $queried_object->taxonomy ) )
				{
					return $posts;
				}

				// ---------------------------------------------------------------------

				$taxonomy_ok = false;

				foreach ( $post_types as $pt )
				{
					if ( $taxonomies = get_object_taxonomies( $pt ) )
					{
						if ( in_array( $queried_object->taxonomy, $taxonomies ) )
						{
							$post_type	= $pt;
							$term_query	= [
								'taxonomy'	=> $queried_object->taxonomy,
								'field'			=> 'term_id',
								'terms'			=> [ $queried_object->term_id ],
							];

							break;
						}
					}
				}
			}
			else if ( is_search() )
			{
				// @consider

				return $posts;
			}
		}
		else
		{
			$post_type = $queried_object->name;
		}

		// -------------------------------------------------------------------------

		if ( empty( $post_type ) )
		{
			return $posts;
		}

		// -------------------------------------------------------------------------

		// @notes: based on relative part in WP_Query::get_posts()

		$num_posts			= count( $posts );
		$sticky_offset	= 0;

		// Loop over posts and relocate stickies to the front.

		for ( $i = 0; $i < $num_posts; $i++ )
		{
			if ( in_array( $posts[ $i ]->ID, $sticky_posts, true ) )
			{
				$sticky_post = $posts[ $i ];

				// Remove sticky from current position.
				array_splice( $posts, $i, 1 );

				// Move to front, after other stickies.
				array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );

				// Increment the sticky offset. The next sticky will be placed at this offset.
				$sticky_offset++;

				// Remove post from sticky posts array.
				$offset = array_search( $sticky_post->ID, $sticky_posts, true );

				unset( $sticky_posts[ $offset ] );
			}
		}

		// -------------------------------------------------------------------------

		// @consider

		// If any posts have been excluded specifically, Ignore those that are sticky.

		/*
		if ( ! empty( $sticky_posts ) && ! empty( $q['post__not_in'] ) )
		{
			$sticky_posts = array_diff( $sticky_posts, $q['post__not_in'] );
		}
		*/

		// -------------------------------------------------------------------------

		// Fetch sticky posts that weren't in the query results.

		if ( ! empty( $sticky_posts ) )
		{
			$args = [
				'post__in'				=> $sticky_posts,
				'post_type'				=> $post_type,
				'post_status'			=> 'publish',
				'posts_per_page'	=> count( $sticky_posts ),

				// @consider
				/*
				'suppress_filters'				=> $q['suppress_filters'],
				'cache_results'						=> $q['cache_results'],
				'update_post_meta_cache'	=> $q['update_post_meta_cache'],
				'update_post_term_cache'	=> $q['update_post_term_cache'],
				'lazy_load_term_meta'			=> $q['lazy_load_term_meta'],
				*/
			];

			if ( ! empty( $term_query ) )
			{
				$args['tax_query'] = [ $term_query ];
			}

			$stickies = get_posts( $args );

			// -----------------------------------------------------------------------

			foreach ( $stickies as $sticky_post )
			{
				array_splice( $posts, $sticky_offset, 0, [ $sticky_post ] );

				$sticky_offset++;
			}
		}

		// -------------------------------------------------------------------------

		return $posts;

	}
	// the_posts_featured_at_top()



	/**
	 * @since 1.0.0
	 */
	public function cleanup_head()
	{

		// EditURI link.

		remove_action( 'wp_head', 'rsd_link' );

		// -------------------------------------------------------------------------

		/*
		// Post and comment feed links.
		remove_action( 'wp_head', 'feed_links', 2 );

		// Category feed links.
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		*/

		// -------------------------------------------------------------------------

		// Windows Live Writer.

		remove_action( 'wp_head', 'wlwmanifest_link' );

		// -------------------------------------------------------------------------

		// Index link.
		remove_action( 'wp_head', 'index_rel_link' );

		// Previous link.
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

		// Start link.
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

		// -------------------------------------------------------------------------

		// @consider: canonical should probably be kept

		//remove_action( 'wp_head', 'rel_canonical', 10, 0 );

		// -------------------------------------------------------------------------

		// Shortlink.

		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

		// -------------------------------------------------------------------------

		// Links for adjacent posts.

		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

		// -------------------------------------------------------------------------

		// Emoji detection script.

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

		// -------------------------------------------------------------------------

		// Emoji styles.

		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// -------------------------------------------------------------------------

		// WP version.

		remove_action( 'wp_head', 'wp_generator' );

	}
	// cleanup_head()



	/**
	 * @since 1.0.0
	 */
	public function enqueue()
	{

		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );

		// -------------------------------------------------------------------------

		// comment reply script

		if ( current_theme_supports('threaded-comments') )
		{
			if ( 		is_singular()
					 && comments_open()
					 && get_option('thread_comments') )
			{
				wp_enqueue_script('comment-reply');
			}
		}

		// -------------------------------------------------------------------------

		$inline_script_data = apply_filters( "{$this->prefix}enqueue_inline_script_data", [
			/*
			'site_name'						=> get_bloginfo('name'),
			'site_tagline'				=> get_bloginfo('description'),
			'url'									=> home_url(),
			'url_css'							=> $this->core->url_css(),
			'url_images'					=> $this->core->url_images(),
			'url_js'							=> $this->core->url_js(),
			'posts_per_page'			=> get_option('posts_per_page'),
			'cookie_name_prefix'	=> $this->core->cookie_name_prefix(),
			'cookies_attributes'	=> $this->core->cookies_default_attributes(),
			*/
		]);

		if ( is_array( $inline_script_data ) )
		{
			$inline_script_data = sprintf('const APP = %s;', json_encode( $inline_script_data ) );
		}

		// -------------------------------------------------------------------------

		if ( $this->enqueue_vite_dev( $inline_script_data ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$manifest_file = $this->core->dir_assets('build/.vite/manifest.json');

		if ( ! file_exists( $manifest_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$manifest = json_decode( file_get_contents( $manifest_file ), true );

		if ( ! is_array( $manifest ) || empty( $manifest ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// get first key, it would be something like 'assets/src/js/app.js'
		// but it can change so we rely on reading the first key

		$manifest_keys = array_keys( $manifest );

		if ( ! isset( $manifest_keys[0] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$manifest	= $manifest[ $manifest_keys[0] ];
		$version	= wp_get_theme()->get('Version');

		// -------------------------------------------------------------------------

		// css

		if ( ! empty( $manifest['css'] ) )
		{
			foreach( $manifest['css'] as $css_file )
			{
				$basename			= basename( $css_file, '.css' );
				$handle				= "{$this->prefix}styles_{$basename}";
				$url					= $this->core->url_assets("build/{$css_file}");
				$dependencies	= null;
				$media				= 'all';

				wp_enqueue_style( $handle, $url, $dependencies, $version, $media );
			}
		}

		// -------------------------------------------------------------------------

		// js

		if ( ! empty( $manifest['file'] ) )
		{
			$js_file			= $manifest['file'];
			$basename			= basename( $js_file, '.js' );
			$handle				= "{$this->prefix}scripts_{$basename}";
			$url					= $this->core->url_assets("build/{$js_file}");
			$dependencies	= [];
			$args					= [
				'strategy'	=> 'defer', // defer | async
				'in_footer'	=> false,
			];

			wp_enqueue_script( $handle, $url, $dependencies, $version, $args );

			// -----------------------------------------------------------------------

			if ( strlen( $inline_script_data ) )
			{
				wp_add_inline_script( $handle, $inline_script_data, 'before' );
			}
		}

	}
	// enqueue()



	/**
	 * @since 1.0.0
	 */
	private function enqueue_vite_dev( $inline_script_data )
	{

		if ( ! $this->core->is_local_dev() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$package_file = $this->core->dir('package.json');

		if ( ! file_exists( $package_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$package = json_decode( file_get_contents( $package_file ), true );

		if ( ! $vite_dev_flag_file = F::array_get( $package, ['config', 'vite_dev_flag_file'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! file_exists( $this->core->dir( $vite_dev_flag_file ) ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_action('wp_head', function() use ( $inline_script_data ) {

			if ( strlen( $inline_script_data ) )
			{
				printf(
					'<script type="text/javascript">%s</script>',
					$inline_script_data
				);
			}

			// -----------------------------------------------------------------------

			printf(
				'<script type="module" crossorigin src="%s"></script>',
				'http://localhost:3000/assets/src/js/app.js'
			);

		});

		// -------------------------------------------------------------------------

		return true;

	}
	// enqueue_vite_dev()



	/**
	 * @since 1.0.0
	 */
	public function comment_form_default_fields( $fields )
	{

		$fields['author']	=
			'<li>' .
				'<label for="name-txt">' . $this->__('Name *') . '</label>' .
				'<input type="text" id="name-txt" name="author" value="">' .
			'</li>';

		// -------------------------------------------------------------------------

		$fields['email']	=
			'<li>' .
				'<label for="email-txt">' . $this->__('Email *') . '</label>' .
				'<input type="text" id="email-txt" name="email" value="">' .
			'</li>';

		// -------------------------------------------------------------------------

		return $fields;

	}
	// comment_form_default_fields()



	/**
	 * @internal
	 */
	public function language_attributes_filter( $output, $doctype )
	{

		if ( 'html' !== $doctype )
		{
			return $output;
		}

		// -------------------------------------------------------------------------

		$classes = ['no-js'];

		// -------------------------------------------------------------------------

		if ( $color_scheme = $this->Settings()->get('color_scheme') )
		{
			$classes[] = $color_scheme;
		}

		// -------------------------------------------------------------------------

		$output .= sprintf(' class="%s"', implode( ' ', $classes ) );

		// -------------------------------------------------------------------------

		return $output;

	}
	// language_attributes_filter()



	/**
	 * @since 1.0.0
	 */
	public function body_class_filter( $classes )
	{

		if ( is_user_logged_in() )
		{
			$roles = wp_get_current_user()->roles;

			if ( ! empty( $roles ) )
			{
				$roles_classes = array_map(
					function( $role ) { return "logged-in_{$role}"; },
					$roles
				);

				$classes = array_merge( $classes, $roles_classes );
			}
		}

		// -------------------------------------------------------------------------

		return $classes;

	}
	// body_class_filter()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * TRANSIENTS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	private function transients_hooks()
	{

		if ( ! $this->core->transients_active() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		/*

		cases where transients need to be deleted:

			- when setting post terms
			- when setting post featured image set_post_thumbnail()
			- when a post status is changed from published to any and vice versa
			- when a post is deleted (not sure if that fires deleted_post_meta)

		*/

		// -------------------------------------------------------------------------

		// wp_set_object_terms case

		// do_action( 'set_object_terms', $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids );

		add_action(
			'set_object_terms',
			[ $this, 'transients_delete_set_object_terms' ],
			10,
			6
		);

		// do_action( 'deleted_term_relationships', $object_id, $tt_ids, $taxonomy );

		add_action(
			'deleted_term_relationships',
			[ $this, 'transients_delete_deleted_term_relationships' ],
			10,
			3
		);

		// -------------------------------------------------------------------------

		// set_post_thumbnail case

		// do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $meta_value )

		add_action(
			'updated_post_meta',
			[ $this, 'transients_delete_updated_post_meta' ],
			10,
			4
		);

		// do_action( "deleted_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $meta_value )

		add_action(
			'deleted_post_meta',
			[ $this, 'transients_delete_updated_post_meta' ],
			10,
			4
		);

		// -------------------------------------------------------------------------

		// wp_transition_post_status case

		add_action(
			'transition_post_status',
			[ $this, 'transients_delete_transition_post_status' ],
			10,
			3
		);

	}
	// transients_hooks()



	/**
	 * @since 1.0.0
	 */
	public function transients_delete_set_object_terms( $post_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids )
	{

		// @notes:
		//	if i set post type as part of option name then i can detect it here
		//	by using get_post( $post_id )
		//
		//	if i set taxonomy as part of option name then i can detect it here

		// -------------------------------------------------------------------------

		$post = get_post( $post_id );

		// only applicable to published posts

		if ( 		! empty( $post->post_status )
				 && 'publish' !== $post->post_status )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		//	this will delete all transients,
		//	not sure if there is a better way to do this

		$this->transients_delete();

	}
	// transients_delete_set_object_terms()



	/**
	 * @since 1.0.0
	 */
	public function transients_delete_deleted_term_relationships( $post_id, $tt_ids, $taxonomy )
	{

		// @notes:
		//	if i set post type as part of option name then i can detect it here
		//	by using get_post( $post_id )
		//
		//	if i set taxonomy as part of option name then i can detect it here

		// -------------------------------------------------------------------------

		$post = get_post( $post_id );

		// only applicable to published posts

		if ( 		! empty( $post->post_status )
				 && 'publish' !== $post->post_status )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		//	this will delete all transients,
		//	not sure if there is a better way to do this

		$this->transients_delete();

	}
	// transients_delete_deleted_term_relationships()



	/**
	 * @since 1.0.0
	 */
	public function transients_delete_updated_post_meta( $meta_id, $post_id, $meta_key, $meta_value )
	{

		if ( '_thumbnail_id' !== $meta_key )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		//	if i set post type as part of option name then i can detect it here
		//	by using get_post( $post_id )

		// -------------------------------------------------------------------------

		$post = get_post( $post_id );

		// only applicable to published posts

		if ( 		! empty( $post->post_status )
				 && 'publish' !== $post->post_status )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		//	this will delete all transients,
		//	not sure if there is a better way to do this

		$this->transients_delete();

	}
	// transients_delete_updated_post_meta()



	/**
	 * @since 1.0.0
	 */
	public function transients_delete_transition_post_status( $new_status, $old_status, $post )
	{

		if ( $new_status === $old_status )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes:
		//	if i set post type as part of option name then i can detect it here
		//	by using get_post( $post_id )

		// -------------------------------------------------------------------------

		// we're only interested in case we're transitioning from/to publish

		if ( 		'publish' === $new_status
				 || 'publish' === $old_status )
		{
			// @notes:
			//	this will delete all transients,
			//	not sure if there is a better way to do this

			$this->transients_delete();
		}

	}
	// transients_delete_updated_post_meta()



	/**
	 * @since 1.0.0
	 */
	public function transients_delete()
	{

		global $wpdb;

		// -------------------------------------------------------------------------

		$queries = [];

		// -------------------------------------------------------------------------

		/*
		DELETE
		FROM `wp_options` WHERE `option_name`
		LIKE '_transient_dotaim_frontend_template_taxonomy_term_tile_images_%'
		*/

		$option_name = "_transient_{$this->prefix}taxonomy_term_tile_images_%";

		$queries[] = $wpdb->prepare(
			"
			DELETE
			FROM `{$wpdb->options}`
			WHERE `option_name`
			LIKE %s
			",
			$option_name
		);

		// -------------------------------------------------------------------------

		if ( ! $queries )
		{
			return;
		}

		// -------------------------------------------------------------------------

		//debug_log( $queries );

		// -------------------------------------------------------------------------

		foreach ( $queries as $query )
		{
			$wpdb->query( $query );
		}

		// -------------------------------------------------------------------------

		// @consider

		// ref: https://css-tricks.com/the-deal-with-wordpress-transients/

		//wp_cache_flush();

		// -------------------------------------------------------------------------

		// super cache plugin

		/*
		if ( function_exists( '\wp_cache_clear_cache' ) )
		{
			wp_cache_clear_cache();
		}
		*/

		// @consider if this is needed
		//
		/*
		$pages = [];

		if ( ! empty( $pages ) && function_exists ( '\wp_cache_post_change' ) )
		{
			foreach ( $pages as $page_title )
			{
				$post = $this->core->Post()->get_by_title( $page_title, 'page' );

				if ( ! empty( $post->ID ) )
				{
					wp_cache_post_change( $post->ID );
				}
			}
		}
		*/

	}
	// transients_delete()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * TRANSIENTS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META TAGS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function wp_head_meta_tags()
	{

		$title				= wp_get_document_title();
		$site_name		= get_bloginfo('name');
		$description	= get_bloginfo('description'); // this is the tagline
		$url					= esc_url( home_url() );
		$type					= 'website';
		$image				= '';
		$additional		= [];

		// -------------------------------------------------------------------------

		$meta_tags_settings = $this->Settings()->get_component_app_settings( 'misc', 'meta_tags' );

		if ( $default_meta_image = F::array_get( $meta_tags_settings, ['image_default'] ) )
		{
			$image = do_shortcode( $default_meta_image );
		}

		// -------------------------------------------------------------------------

		if ( is_front_page() )
		{
			if ( ! empty( $meta_tags_settings['front_page_title'] ) )
			{
				$title = do_shortcode( $meta_tags_settings['front_page_title'] );
			}

			if ( ! empty( $meta_tags_settings['front_page_description'] ) )
			{
				$description = do_shortcode( $meta_tags_settings['front_page_description'] );
			}

			if ( ! empty( $meta_tags_settings['front_page_image_url'] ) )
			{
				$image = do_shortcode( $meta_tags_settings['front_page_image_url'] );
			}
		}

		// -------------------------------------------------------------------------

		if ( is_singular() && ! is_front_page() )
		{
			global $post;

			// -----------------------------------------------------------------------

			$type = 'article';

			// -----------------------------------------------------------------------

			// @notes: this doesn't add it to <title> tag

			if ( $meta_title = $this->core->Post()->meta_value( 'post_settings_meta_title', [], $post->ID, true ) )
			{
				$title = do_shortcode( $meta_title );
			}
			else
			{
				$title = get_the_title();

				// @consider
				//$title = sprintf( $this->__('%s on %s'), get_the_title(), $site_name );
				//$title = sprintf( '%s %s %s', get_the_title(), $sep = '-', $site_name );
			}

			// -----------------------------------------------------------------------

			if ( $meta_description = $this->core->Post()->meta_value( 'post_settings_meta_description', [], $post->ID, true ) )
			{
				$description = do_shortcode( $meta_description );
			}
			else
			{
				add_filter( 'excerpt_more', [ $this, 'remove_excerpt_more' ] );

				$description = wp_trim_excerpt( '', $post );
				$description = strip_tags( $description );
				$description = str_replace( '"', '\'', $description );

				remove_filter( 'excerpt_more', [ $this, 'remove_excerpt_more' ] );
			}

			// -----------------------------------------------------------------------

			$url = get_permalink( $post );

			// -----------------------------------------------------------------------

			if ( $meta_image_url = $this->core->Post()->get_meta_image_url( $post->ID ) )
			{
				$image = do_shortcode( $meta_image_url );
			}

			// -----------------------------------------------------------------------

			$additional_meta_tags = $this->core->Post()->meta_value(
				'post_settings_additional_meta_tags',
				[],
				$post->ID
			);

			if ( ! empty( $additional_meta_tags ) )
			{
				foreach ( $additional_meta_tags as $meta_tag )
				{
					if ( ! empty( $meta_tag[0] ) && ! empty( $meta_tag[1] ) )
					{
						$additional[ $meta_tag[0] ] = do_shortcode( $meta_tag[1] );
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		$meta_tags_content = $this->apply_filters(
			'meta_tags_content',
			[

				'description' => $description,

				// ---------------------------------------------------------------------

				'twitter' => [
					// @notes: the default is set below based on filtered image
					//'card'			=> ! empty( $image ) ? 'summary_large_image' : 'summary',
					'title'				=> $title,
					'description'	=> $description,
					'image'				=> $image,
					'site'				=> ! empty( $this->core->app_config['twitter_username'] )
												 ? "@{$this->core->app_config['twitter_username']}"
												 : null,
				],

				// ---------------------------------------------------------------------

				'og' => [
					'type'				=> $type,
					'url'					=> $url,
					'title'				=> $title,
					'description'	=> $description,
					'image'				=> $image,
					'site_name'		=> $site_name,
				],

				// ---------------------------------------------------------------------

				'additional' => $additional,

			]
		);

		// -------------------------------------------------------------------------

		$meta_tags = [];

		foreach ( $meta_tags_content as $key => $value )
		{
			if ( ! $value )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			switch ( $key )
			{
				case 'twitter':

					if ( ! isset( $value['card'] ) )
					{
						$value['card'] = ! empty( $value['image'] ) ? 'summary_large_image' : 'summary';
					}

					// -------------------------------------------------------------------

					foreach ( $value as $name => $content )
					{
						if ( ! is_null( $content ) && strlen( $content ) )
						{
							$meta_tags[] = sprintf(
								'<meta name="twitter:%s" content="%s">',
								$name,
								html_entity_decode( $content, ENT_QUOTES, 'UTF-8' )
							);
						}
					}

					break;

				// ---------------------------------------------------------------------

				case 'og':

					foreach ( $value as $name => $content )
					{
						if ( strlen( $content ) )
						{
							$meta_tags[] = sprintf(
								'<meta property="og:%s" content="%s">',
								$name,
								html_entity_decode( $content, ENT_QUOTES, 'UTF-8' )
							);
						}
					}

					break;

				// ---------------------------------------------------------------------

				case 'additional':

					if ( empty( $value ) )
					{
						break;
					}

					foreach ( $value as $name => $content )
					{
						if ( strlen( $content ) )
						{
							$meta_tags[] = sprintf('<meta name="%s" content="%s">', $name, $content);
						}
					}

					break;

				// ---------------------------------------------------------------------

				default:

					$meta_tags[] = sprintf('<meta name="%s" content="%s">', $key, $value);

					break;
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $meta_tags ) )
		{
			echo "\n" . implode( "\n", $meta_tags ) . "\n";
		}

	}
	// wp_head_meta_tags()



	/**
	 * @since 1.0.0
	 */
	public function remove_excerpt_more( $excerpt_more )
	{

		return '';

	}
	// remove_excerpt_more()



	/**
	 * @since 1.0.0
	 */
	public function custom_excerpt( $text, $excerpt )
	{

		if ( $excerpt )
		{
			return $excerpt;
		}

		// -------------------------------------------------------------------------

		$text = strip_shortcodes( $text );
		$text = apply_filters( 'the_content', $text );
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = strip_tags( $text );

		// -------------------------------------------------------------------------

		$excerpt_length	= apply_filters( 'excerpt_length', 55 );
		$excerpt_more		= apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words					= preg_split( "/[\n
		]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

		// -------------------------------------------------------------------------

		if ( count( $words ) > $excerpt_length )
		{
			array_pop( $words );

			$text = implode( ' ', $words );
			$text = $text . $excerpt_more;
		}
		else
		{
			$text = implode( ' ', $words );
		}

		// -------------------------------------------------------------------------

		$text = str_replace( '"', '\'', strip_tags( $text ) );

		// -------------------------------------------------------------------------

		return apply_filters( 'wp_trim_excerpt', $text );

	}
	// custom_excerpt()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META TAGS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST VIEWS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function post_views_set( $post_id = 0 )
	{

		$post_id	= $post_id ?: get_the_ID();
		$meta_key	= $this->meta_key_post_views;
		$count		= absint( get_post_meta( $post_id, $meta_key, true ) );

		// -------------------------------------------------------------------------

		$cookie_key = "{$meta_key}-{$post_id}";

		if ( ! isset( $_COOKIE[ $cookie_key ] ) )
		{
			$count++;

			// -----------------------------------------------------------------------

			update_post_meta( $post_id, $meta_key, $count );

			// -----------------------------------------------------------------------

			setcookie( $cookie_key, true );
		}

	}
	// post_views_set()



	/**
	 * @since 1.0.0
	 */
	public function post_views_get( $post_id = 0 )
	{

		$post_id = $post_id ?: get_the_ID();

		// -------------------------------------------------------------------------

		return absint( get_post_meta( $post_id, $this->meta_key_post_views, true ) );

	}
	// post_views_get()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * POST VIEWS - END
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
	public function Settings()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $this->core->Settings();

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// Settings()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Frontend
