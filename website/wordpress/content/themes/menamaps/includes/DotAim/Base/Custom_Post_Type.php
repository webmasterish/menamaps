<?php

namespace DotAim\Base;

use DotAim\F;

abstract class Custom_Post_Type
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ABSTRACT METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	abstract public function settings();

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ABSTRACT METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected $core;

	/**
	 * @since 1.0.0
	 */
	public $settings;
	public $default_settings;
	public $default_settings_labels;

	/**
	 * @since 1.0.0
	 */
	public $name;
	public $name_singular;
	public $name_plural;

	/**
	 * @since 1.0.0
	 */
	public $obj;

	/**
	 * @since 1.0.0
	 */
	public $register_check;
	public $register_reserved_names;

	/**
	 * @since 1.0.0
	 */
	public $related_post_types = [];

	/**
	 * @internal
	 */
	public $Post;

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
	public function __construct( $core, $related_post_types = [] )
	{

		$this->core = $core;

		// -------------------------------------------------------------------------

		$this->related_post_types = $related_post_types;

		// -------------------------------------------------------------------------

		$this->hooks();

	}
	// __construct()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function setting( $key )
	{

		$out = '';

		// -------------------------------------------------------------------------

		if ( ! empty( $this->settings[ $key ] ) )
		{
			$out = $this->settings[ $key ];
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// setting()



	/**
	 * @since 1.0.0
	 */
	protected function default_settings()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [

			'name_singular'	=> '',
			'name_plural'		=> '',

			// -----------------------------------------------------------------------

			'register' => [
				'name'	=> '',	// required
				'args'	=> [		// optional
					'labels' => $this->default_settings_labels(),

					/*
					ref:
					'label'					=> (string),
					'labels' 				=> (array) $labels,
					'description'			=> (string),
					'public' 				=> (boolean),
					'exclude_from_search'	=> (boolean),
					'publicly_queryable' 	=> (boolean),
					'show_ui' 				=> (boolean),
					'show_in_nav_menus'		=> (boolean),
					'show_in_menu' 			=> (boolean or string),
					'show_in_admin_bar'		=> (boolean),
					// 5-below Posts|10-below Media|20-below Pages|60-below 1st separator|100-below 2nd separator
					'menu_position' 		=> (integer),
					'menu_icon'				=> (string),
					'capability_type' 		=> (string or array),
					'capabilities'			=> (array) $capabilities,
					'map_meta_cap' 			=> (boolean),
					'hierarchical' 			=> (boolean),
					'supports' 				=> (array or boolean) $supports,
					'register_meta_box_cb'	=> (string),
					'taxonomies'			=> (array) $taxonomies,
					'has_archive'			=> (boolean or string),
					'permalink_epmask'		=> (string),
					'rewrite' 				=> (boolean or array),
					'query_var' 			=> (boolean or string),
					'can_export'			=> (boolean),
					'_builtin'				=> (boolean),
					'_edit_link'			=> (boolean),
					*/
				],
			],

			// -----------------------------------------------------------------------

			'register_args' => [],

			// -----------------------------------------------------------------------

			//'title_column_text' => $this->__('Name'),

			// -----------------------------------------------------------------------

			// thumbnail column
			//
			// true is index 1 which is after cb, we can set it to other index/position
			// for example 3 or 4 etc...

			'thumbnail_column' => true,

			'thumbnail_column_size' => [ 60, 60 ], // 'thumbnail',

			// -----------------------------------------------------------------------

			'disable_autosave' => true,

			// -----------------------------------------------------------------------

			// change enter title here to post type name

			'enter_title_here' => true,

			// -----------------------------------------------------------------------

			// change text of featured image

			//'featured_image_text' => true,

			// -----------------------------------------------------------------------

			// add to dashboard at a glance widget

			'dashboard_glance' => true,

		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// default_settings()



	/**
	 * @since 1.0.0
	 */
	protected function default_settings_labels()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		// set singular and plural

		if ( ! $singular = $this->setting('name_singular') )
		{
			$singular = F::humanize( $this->name );
		}

		if ( ! $plural = $this->setting('name_plural') )
		{
			$plural = F::humanize( $this->name );
		}

		// -------------------------------------------------------------------------

		// defaults

		$out = [
			'name' 									=> $plural,
			'singular_name' 				=> $singular,
			'menu_name' 						=> $plural,
			'name_admin_bar'				=> $singular,
			'all_items' 						=> sprintf( $this->__('All %s'), $plural ),
			'add_new' 							=> sprintf( $this->__('Add %s'), $singular ),
			'add_new_item' 					=> sprintf( $this->__('Add New %s'), $singular ),
			'edit_item' 						=> sprintf( $this->__('Edit %s'), $singular ),
			'new_item' 							=> sprintf( $this->__('New %s'), $singular ),
			'view_item' 						=> sprintf( $this->__('View %s'), $singular ),
			'search_items' 					=> sprintf( $this->__('Search %s'), $plural ),
			'not_found' 						=> sprintf( $this->__('No %s found'), strtolower( $plural ) ),
			'not_found_in_trash'		=> sprintf( $this->__('No %s found in Trash'), strtolower( $plural ) ),
			'parent_item_colon'			=> sprintf( $this->__('Parent %s:'), $singular ),

			'archives'							=> sprintf( $this->__('%s Archives'), $singular ),
			'insert_into_item'			=> sprintf( $this->__('Insert into %s'), strtolower( $singular ) ),
			'uploaded_to_this_item'	=> sprintf( $this->__('Uploaded to this %s'), strtolower( $singular ) ),

			'featured_image'				=> sprintf( $this->__('%s Featured Image'), $singular ),
			'set_featured_image'		=> sprintf( $this->__('Set %s image'), strtolower( $singular ) ),
			'remove_featured_image'	=> sprintf( $this->__('Remove %s image'), strtolower( $singular ) ),
			'use_featured_image'		=> sprintf( $this->__('Use as %s image'), strtolower( $singular ) ),

			'filter_items_list'			=> sprintf( $this->__('Filter %s list'), strtolower( $plural ) ),
			'items_list_navigation'	=> sprintf( $this->__('%s list navigation'), $plural ),
			'items_list'						=> sprintf( $this->__('%s list'), $plural ),
		];

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $out;

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// default_settings_labels()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - END
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
	protected function hooks()
	{

		add_action( 'init', [ $this, 'init_action' ] );

	}
	// hooks()



	/**
	 * @since 1.0.0
	 */
	public function init_action()
	{

		// populate settings

		$this->settings();

		// -------------------------------------------------------------------------

		// as a minimum we need a name

		if ( empty( $this->settings['register']['name'] ) )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		$this->name = $this->settings['register']['name'];

		// -------------------------------------------------------------------------

		$this->settings = F::parse_args_deep( $this->settings, $this->default_settings() );

		// -------------------------------------------------------------------------

		if ( ! $this->register() )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		$this->hooks_admin();

	}
	// init_action()



	/**
	 * @since 1.0.0
	 */
	protected function hooks_admin()
	{

		if ( empty( $this->obj ) )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		if ( ! is_admin() )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		// disable autosave

		if ( $this->setting('disable_autosave') )
		{
			add_action( 'admin_print_scripts', [ $this, 'disable_autosave' ] );
		}

		// -------------------------------------------------------------------------

		// Post title fields

		if ( $this->setting('enter_title_here') )
		{
			add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 1, 2 );
		}

		// -------------------------------------------------------------------------

		// featured image text

		/*
		if ( $this->setting('featured_image_text') )
		{
			add_filter( 'gettext', [ $this, 'featured_image_gettext' ] );

			add_filter( 'media_view_strings', [ $this, 'media_view_strings' ], 10, 2 );
		}
		*/

		// -------------------------------------------------------------------------

		// add to dashboard at a glance widget

		if ( $this->setting('dashboard_glance') )
		{
			add_filter( 'dashboard_glance_items', [ $this, 'dashboard_glance_items' ] );
		}

		// -------------------------------------------------------------------------

		// columns

		$this->hooks_admin_columns();

		// -------------------------------------------------------------------------

		// meta boxes

		// -------------------------------------------------------------------------

		// menus

		// -------------------------------------------------------------------------

		// enqueue

		if ( is_callable( [ $this, 'admin_enqueue_scripts' ] ) )
		{
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 20 );
		}

	}
	// hooks_admin()



	/**
	 * @since 1.0.0
	 */
	protected function hooks_admin_columns()
	{

		// columns

		$columns_functions = [
			'manage_edit_columns',
			'manage_posts_custom_column',
			'manage_edit_sortable_columns',
			'manage_edit_sortable_columns_orderby',

			'quick_edit_custom_box',
			'bulk_edit_custom_box',
		];

		// -------------------------------------------------------------------------

		if ( ! $columns_functions )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $columns_functions as $fn )
		{
			$method = [ $this, $fn ];

			// -----------------------------------------------------------------------

			if ( ! is_callable( $method ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			switch ( $fn )
			{
				case 'manage_edit_columns':

					add_filter( "manage_edit-{$this->name}_columns", $method );

					break;

				// ---------------------------------------------------------------------

				case 'manage_posts_columns':

					add_filter( "manage_{$this->name}_posts_columns", $method );

					break;

				// ---------------------------------------------------------------------

				case 'manage_posts_custom_column':

					add_action( "manage_{$this->name}_posts_custom_column", $method, 10, 2 );

					break;

				// ---------------------------------------------------------------------

				case 'manage_edit_sortable_columns':

					add_filter( "manage_edit-{$this->name}_sortable_columns", $method );

					break;

				// ---------------------------------------------------------------------

				case 'manage_edit_sortable_columns_orderby':

					add_filter( 'request', $method );

					break;

				// ---------------------------------------------------------------------

				case 'quick_edit_custom_box':

					add_action( 'quick_edit_custom_box', $method, 10, 2 );

					break;

				// ---------------------------------------------------------------------

				case 'bulk_edit_custom_box':

					add_action( 'bulk_edit_custom_box', $method, 10, 2 );

					break;
			}
		}

	}
	// hooks_admin_columns()



	/**
	 * @since 1.0.0
	 */
	public function manage_edit_columns( $columns )
	{

		if ( $title_column_text = $this->setting('title_column_text') )
		{
			$columns['title'] = $title_column_text;
		}

		// -------------------------------------------------------------------------

		if ( $position = $this->setting('thumbnail_column') )
		{
			$new_columns = [
				"{$this->core->prefix}post_thumbnail" => sprintf(
					'<span class="dotaim_column_icon dashicons-before dashicons-format-image" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
					$this->__('Thumbnail')
				),
			];

			$columns = F::array_insert_at_position( $columns, $new_columns, absint( $position ) );
		}

		// -------------------------------------------------------------------------

		$new_columns = [];

		// -------------------------------------------------------------------------

		if ( $taxonomies = get_object_taxonomies( $this->name, 'objects' ) )
		{
			foreach ( $taxonomies as $taxonomy => $taxonomy_obj )
			{
				if ( ! empty( $taxonomy_obj->show_admin_column ) )
				{
					$new_columns["taxonomy-{$taxonomy}"] = $taxonomy_obj->labels->menu_name;
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $this->related_post_types ) )
		{
			foreach ( $this->related_post_types as $related_post_type )
			{
				if ( $label_name = $this->get_post_type_label( $related_post_type, 'name' ) )
				{
					$col_key = "{$this->core->prefix}{$this->name}_related_{$related_post_type}";

					$new_columns[ $col_key ] = $label_name;
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $new_columns ) )
		{
			return $columns;
		}

		// -------------------------------------------------------------------------

		// @todo: optionize

		$position = 3;

		// -------------------------------------------------------------------------

		return F::array_insert_at_position( $columns, $new_columns, $position );

	}
	// manage_edit_columns()



	/**
	 * @since 1.0.0
	 */
	public function manage_posts_custom_column( $column_name, $post_id )
	{

		$out = '';

		// -------------------------------------------------------------------------

		$related_column_name_prefix = "{$this->core->prefix}{$this->name}_related_";

		// -------------------------------------------------------------------------

		switch ( $column_name )
		{
			case "{$this->core->prefix}post_thumbnail":

				if ( ! $position = $this->setting('thumbnail_column') )
				{
					break;
				}

				// ---------------------------------------------------------------------

				$size						= $this->setting('thumbnail_column_size');
				$image_url_args	= [];

				if ( ! $url = $this->Post()->image_url( $size, $image_url_args, $post_id ) )
				{
					break;
				}

				// ---------------------------------------------------------------------

				$attr = F::html_attributes([
					'class' => ["{$this->core->prefix}post_thumbnail", 'dotaim_post_thumbnail'],
					'style' => ['background-image' => "url({$url})" ],
				]);

				$thumb = "<span{$attr}></span>";

				// ---------------------------------------------------------------------

				if ( $link = get_edit_post_link( $post_id ) )
				{
					$out = "<a href=\"{$link}\">{$thumb}</a>";
				}
				else
				{
					$out = $thumb;
				}

				break;

			// -----------------------------------------------------------------------

			case ( preg_match("/{$related_column_name_prefix}*/"	, $column_name) ? true : false ):

				if ( ! $related_post_type = str_replace( $related_column_name_prefix, '', $column_name ) )
				{
					break;
				}

				// ---------------------------------------------------------------------

				// default out

				$out = sprintf(
					'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
					$this->get_post_type_label( $related_post_type, 'not_found' )
				);

				// ---------------------------------------------------------------------

				$key = "{$this->name}_related_posts_{$related_post_type}";

				if ( ! $related_ids = $this->Post()->meta_value( $key, [], $post_id ) )
				{
					break;
				}

				// ---------------------------------------------------------------------

				$relations = [];

				foreach ( $related_ids as $related_id )
				{
					if ( ! $related_post = get_post( $related_id ) )
					{
						continue;
					}

					// -------------------------------------------------------------------

					// @todo: icon/thumb

					$icon = '';

					// -------------------------------------------------------------------

					$post_title = get_the_title( $related_post );
					$label_text	= $post_title;
					$el_tag			= 'span';
					$attr				= [
						'class'	=> [
							$this->core->prefix . 'related_post_link',
						],
						'title'	=> esc_attr( $label_text ),
					];

					// -------------------------------------------------------------------

					$label = sprintf(
						'<span class="%s">%s</span>',
						$this->core->prefix . 'related_post_label',
						$label_text
					);

					// -------------------------------------------------------------------

					// @todo: consider link as filter instead of edit

					// -------------------------------------------------------------------

					$can_edit_post	= current_user_can( 'edit_post', $related_id );
					$post_status		= $related_post->post_status;

					if ( $can_edit_post && $post_status != 'trash' )
					{
						$el_tag				= 'a';
						$attr['href'] = get_edit_post_link( $related_id );
						$attr['title']= esc_attr( $label_text );
					}

					$el_args = [
						'tag'		=> $el_tag,
						'attr'	=> $attr,
						'minify'=> true,
					];

					// -------------------------------------------------------------------

					$relations[] = F::html_wrap( $icon . $label, $el_args );
				}

				// ---------------------------------------------------------------------

				if ( $relations )
				{
					$out = implode( ', ', $relations );
				}

				// ---------------------------------------------------------------------

				break;
		}

		// -------------------------------------------------------------------------

		if ( $out )
		{
			echo $out;
		}

	}
	// manage_posts_custom_column()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * REGISTER - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function register()
	{

		if ( isset( $this->obj ) )
		{
			return $this->obj;
		}

		// -------------------------------------------------------------------------

		$this->obj = false;

		// -------------------------------------------------------------------------

		if ( ! $this->register_check() )
		{
			return $this->obj;
		}

		// -------------------------------------------------------------------------

		$obj = register_post_type(
			F::array_get( $this->settings, ['register', 'name'] ),
			F::array_get( $this->settings, ['register', 'args'] )
		);

		// -------------------------------------------------------------------------

		if ( ! $obj )
		{
			return $this->obj;
		}

		if ( is_wp_error( $obj ) )
		{
			return $this->obj;
		}

		// -------------------------------------------------------------------------

		$this->obj = $obj;

		// -------------------------------------------------------------------------

		return $this->obj;

	}
	// register()



	/**
	 * @since 1.0.0
	 */
	protected function register_check()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = false;

		// -------------------------------------------------------------------------

		if ( ! post_type_exists( $this->name ) )
		{
			if ( $reserved_names = $this->register_reserved_names() )
			{
				if ( ! in_array( $this->name, $reserved_names ) )
				{
					$this->{__FUNCTION__} = true;
				}
			}
			else
			{
				$this->{__FUNCTION__} = true;
			}
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// register_check()



	/**
	 * @since 1.0.0
	 */
	protected function register_reserved_names()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [
			'post',
			'page',
			'attachment',
			'revision',
			'nav_menu_item',
			'action',
			'order',
			'theme',
		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// register_reserved_names()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * REGISTER - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function disable_autosave()
	{

		if ( empty( $this->obj ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		global $post;

		// -------------------------------------------------------------------------

		if ( $post && get_post_type( $post->ID ) === $this->name )
		{
			wp_dequeue_script( 'autosave' );
		}

	}
	// disable_autosave()



	/**
	 * @since 1.0.0
	 */
	public function enter_title_here( $text, $post )
	{

		if ( empty( $this->obj ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $post->post_type === $this->name )
		{
			$setting = $this->setting('enter_title_here');

			if ( is_bool( $setting ) )
			{
				$text = sprintf( $this->__('%s Title'), $this->name_singular() );
			}
			elseif ( is_string( $setting ) )
			{
				$text = $setting;
			}
		}

		// -------------------------------------------------------------------------

		return $text;

	}
	// enter_title_here()



	/**
	 * @since 1.0.0
	public function featured_image_gettext( $string = '' )
	{

		if ( empty( $this->obj ) )
		{
			return $string;
		}

		// -------------------------------------------------------------------------

		if ( ! $this->is_editing() )
		{
			return $string;
		}

		// -------------------------------------------------------------------------

		// @notes: should have the cases in settings and use foreach...

		switch ( $string )
		{
			case 'Featured Image':

				$string = sprintf( $this->__('%s Image'), $this->name_singular() );

				break;

			// -----------------------------------------------------------------

			case 'Remove featured image':

				$string = sprintf( $this->__('Remove %s Image'), $this->name_singular() );

				break;

			// -----------------------------------------------------------------

			case 'Set featured image':

				$string = sprintf( $this->__('Set %s Image'), $this->name_singular() );

				break;
		}

		// -------------------------------------------------------------------------

		return $string;

	}
	// featured_image_gettext()
	 */



	/**
	 * @since 1.0.0
	public function media_view_strings( $strings = [], $post = null )
	{

		if ( empty( $this->obj ) )
		{
			return $strings;
		}

		// -------------------------------------------------------------------------

		global $post_type;

		if ( $post_type != $this->name )
		{
			return $strings;
		}

		// -------------------------------------------------------------------------

		// @notes: should have the cases in settings and use foreach...

		$obj = get_post_type_object( $this->name );

		$strings['insertIntoPost'] = sprintf(
			$this->__( 'Insert into %s'),
			$obj->labels->singular_name
		);

		$strings['uploadedToThisPost'] = sprintf(
			$this->__('Uploaded to this %s'),
			$obj->labels->singular_name
		);

		// -------------------------------------------------------------------------

		if ( is_object( $post ) )
		{
			$strings['setFeaturedImageTitle'] = sprintf(
				$this->__('Set %s image'),
				$obj->labels->singular_name
			);

			$strings['setFeaturedImage'] = sprintf(
				$this->__('Set %s image'),
				$obj->labels->singular_name
			);
		}

		// -------------------------------------------------------------------------

		return $strings;

	}
	// media_view_strings()
	 */



	/**
	 * @since 1.0.0
	 */
	public function dashboard_glance_items( $items = [] )
	{

		if ( empty( $this->obj ) )
		{
			return $items;
		}

		if ( ! $num_posts = wp_count_posts( $this->name ) )
		{
			return $items;
		}

		// -------------------------------------------------------------------------

		$text = $this->core->_n(
			'%s ' . $this->obj->labels->singular_name,
			'%s ' . $this->obj->labels->name,
			$num_posts->publish
		);

		$text = sprintf(
			$text,
			number_format_i18n( $num_posts->publish )
		);

		// -------------------------------------------------------------------------

		if ( ! empty( $this->obj->menu_icon ) )
		{
			if ( $dashicon_content = $this->get_dashicon_content( $this->obj->menu_icon ) )
			{
				echo
					'<style>' .
						'#dashboard_right_now .' . $this->name . '-count a:before,' .
						'#dashboard_right_now .' . $this->name . '-count span:before{' .
							'content:"' . $dashicon_content . '"!important;' .
						'}' .
					'</style>';
			}
		}

		// -------------------------------------------------------------------------

		if ( current_user_can( $this->obj->cap->edit_posts ) )
		{
			printf(
				'<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>',
				$this->name,
				$text
			);
		}
		else
		{
			printf(
				'<li class="%1$s-count"><span>%2$s</span></li>',
				$this->name,
				$text
			);
		}

		// -------------------------------------------------------------------------

		return $items;

	}
	// dashboard_glance_items()



	/**
	 * @since 1.0.0
	 */
	protected function get_dashicon_content( $class = '' )
	{

		if ( ! $class )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! defined( 'ABSPATH' ) || ! defined( 'WPINC' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$css_file = ABSPATH . WPINC . '/css/dashicons.css';

		// -------------------------------------------------------------------------

		if ( ! is_readable( $css_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$file_contents = file_get_contents( $css_file );

		if ( ! $file_contents )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$pattern =
			'/' .
			"(\.?($class\:before,\s*|$class\:before\s*).*?)" .
			'\{\s*content\s*\:\s*\"(.*?)\"' .
			'/';

		preg_match( $pattern, $file_contents, $matches );

		// -------------------------------------------------------------------------

		if ( empty( $matches[3] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		return $matches[3];

	}
	// get_dashicon_content()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COLUMNS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * COLUMNS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META BOXES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * META BOXES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ENQUEUE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook )
	{

		$screens = [
			'edit.php',
			'post-new.php',
			'post.php',
		];

		if ( ! in_array( $hook, $screens ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		global $post_type;

		if ( $this->name !== $post_type )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @notes: enqueue scripts and styles here - use it in child class

		// -------------------------------------------------------------------------

		$this->admin_styles();

		// -------------------------------------------------------------------------

		//$this->admin_scripts();

	}
	// admin_enqueue_scripts()



	/**
	 * @since 1.0.0
	 */
	private function admin_styles()
	{

		/*
		$handle	= 'font-awesome';
		$src		= $this->core->url_css() . 'font-awesome.min.css';
		$deps		= [];
		$ver		= '4.6.3';
		$media	= 'screen';

		wp_register_style( $handle, $src, $deps, $ver, $media );

		// -------------------------------------------------------------------------

		$handle	= $this->core->prefix . __FUNCTION__;
		$src		= $this->core->url_css() . 'admin/admin.css';
		$deps		= ['font-awesome'];
		$ver		= $this->core->version;
		$media	= 'screen';

		// -------------------------------------------------------------------------

		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
		*/

	}
	// admin_styles()



	/**
	 * @since 1.0.0
	private function admin_scripts()
	{

		$parameters = [
			'handle'		=> $this->prefix . __FUNCTION__,
			'src'				=> $this->core->url_js() . 'admin/' . $this->core->prefix . 'admin.js',
			'deps'			=> ['jquery'],
			'ver'				=> $this->core->version,
			'in_footer'	=> true,
		];

		extract( $parameters );

		// -------------------------------------------------------------------------

		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );

		// -------------------------------------------------------------------------

		$data = [
			//'id'		=> $this->id,
			//'name'	=> $this->name,
		];

		wp_localize_script( $handle, $this->id, $data );

	}
	// admin_scripts()
	 */

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ENQUEUE - END
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
	public function name_singular()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		// default

		if ( ! $out = $this->setting('name_singular') )
		{
			$out = F::humanize( $this->name );
		}

		// -------------------------------------------------------------------------

		// check settings label

		// @notes: could also check settings['labels']['singular_name']

		// -------------------------------------------------------------------------

		// get it from registered object

		if ( ! empty( $this->obj->labels->singular_name ) )
		{
			$out = $this->obj->labels->singular_name;
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $out;

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// name_singular()



	/**
	 * @since 1.0.0
	 */
	public function name_plural()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		// default

		if ( ! $out = $this->setting('name_plural') )
		{
			$out = F::humanize( $this->name );
		}

		// -------------------------------------------------------------------------

		// check settings label

		// @notes: could also check settings['labels']['name']

		// -------------------------------------------------------------------------

		// get it from registered object

		if ( ! empty( $this->obj->labels->name ) )
		{
			$out = $this->obj->labels->name;
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $out;

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// name_plural()



	/**
	 * @since 1.0.0
	 */
	public function get_post_type_label( $post_type, $what_label )
	{

		if ( ! $obj = get_post_type_object( $post_type ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = $obj->labels->{$what_label} ?? '';

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_post_type_label()



	/**
	 * @since 1.0.0
	 */
	public function get_taxonomy_label( $taxonomy, $what_label )
	{

		if ( ! $obj = get_taxonomy( $taxonomy ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = $obj->labels->{$what_label} ?? '';

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_taxonomy_label()



	/**
	 * @since 1.0.0
	 */
	private function is_editing()
	{

		if ( 	! empty( $_GET['post_type'] )
			 && $this->name == $_GET['post_type'] )
		{
			return true;
		}

		// -------------------------------------------------------------------------

		if ( 	! empty( $_GET['post'] )
			 && $this->name == get_post_type( $_GET['post'] ) )
		{
			return true;
		}

		// -------------------------------------------------------------------------

		if ( 	! empty( $_REQUEST['post_id'] )
			 && $this->name == get_post_type( $_REQUEST['post_id'] ) )
		{
			return true;
		}

		// -------------------------------------------------------------------------

		return false;

	}
	// is_editing()



	/**
	 * @since 1.0.0
	 */
	protected function custom_column_prefix()
	{

		return "{$this->core->prefix}{$this->name}_";

	}
	// custom_column_prefix()



	/**
	 * @since 1.0.0
	 */
	protected function custom_column_name( $key )
	{

		return $this->custom_column_prefix() . $key;

	}
	// custom_column_name()



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

		return $this->core->_e( ...$args );

	}
	// _e()



	/**
	 * @since 1.0.0
	 */
	protected function _x( ...$args )
	{

		$this->core->_x( ...$args );

	}
	// _x()



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
	 * RELATED CLASSES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function Post()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $this->core->Post();

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// Post()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Custom_Post_Type
