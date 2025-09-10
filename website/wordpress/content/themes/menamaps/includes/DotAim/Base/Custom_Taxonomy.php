<?php

namespace DotAim\Base;

use DotAim\F;

abstract class Custom_Taxonomy
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
	public $register_check;
	public $register_reserved_names;

	/**
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * @since 1.0.0
	 */
	public $obj;

	/**
	 * @since 1.0.0
	 */
	protected $term_meta_instance;

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
	public function __construct( $core )
	{

		$this->core = $core;

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
				'name'	=> '',		// required
				'types'	=> null,	// optional (array) | null
				'args'	=> [	// optional
					'labels' => $this->default_settings_labels(),

					/* ref:

					'label'									=> (string),
					'labels' 								=> (array),
					'description' 					=> (string),
					'public' 								=> (boolean),
					'show_ui' 							=> (boolean),
					'show_in_nav_menus'			=> (boolean),
					'show_tagcloud'					=> (boolean),
					'meta_box_cb'						=> (string),
					'show_admin_column'			=> (boolean),
					'hierarchical'					=> (boolean),
					'update_count_callback'	=> (string),
					'query_var' 						=> (boolean or string),
					'rewrite' 							=> (boolean or array),
					'capabilities'					=> (array),
					'sort'									=> (boolean),
					'_builtin'							=> (boolean),

					*/
				],
			],

			// -----------------------------------------------------------------------

			'pre_add_form' => '',

			// -----------------------------------------------------------------------

			// term_meta

			/*
			'meta'	=> [
				[
					'id'									=> '',
					'label'								=> '',
					'type'								=> '',
					'default'							=> '',
					'custom_column'				=> 'append',
					'custom_column_label'	=> '',
				],
			],
			*/

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
			'name' 											=> $plural,
			'singular_name' 						=> $singular,
			'menu_name' 								=> $plural,
			'name_admin_bar'						=> $singular,
			'all_items' 								=> sprintf( $this->__('All %s'), $plural ),
			'edit_item' 								=> sprintf( $this->__('Edit %s'), $singular ),
			'view_item' 								=> sprintf( $this->__('View %s'), $singular ),
			'update_item' 							=> sprintf( $this->__('Update %s'), $singular ),
			'add_new_item' 							=> sprintf( $this->__('Add New %s'), $singular ),
			'new_item_name' 						=> sprintf( $this->__('New %s'), $singular ),
			'parent_item' 							=> sprintf( $this->__('Parent %s'), $singular ),
			'parent_item_colon' 				=> sprintf( $this->__('Parent %s:'), $singular ),
			'search_items' 							=> sprintf( $this->__('Search %s'), $plural ),
			'popular_items' 						=> sprintf( $this->__('Popular %s'), $plural ),
			'separate_items_with_commas'=> sprintf( $this->__('Separate %s with commas'), strtolower( $plural ) ),
			'add_or_remove_items'				=> sprintf( $this->__('Add or remove %s'), strtolower( $plural ) ),
			'choose_from_most_used'			=> sprintf( $this->__('Choose from the most used %s'), strtolower( $plural ) ),
			'not_found' 								=> sprintf( $this->__('No %s found'), strtolower( $plural ) ),
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
			return;
		}

		// -------------------------------------------------------------------------

		$this->name = $this->settings['register']['name'];

		// -------------------------------------------------------------------------

		$this->settings = F::parse_args_deep( $this->settings, $this->default_settings() );

		// -------------------------------------------------------------------------

		if ( ! $this->register() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// Initialize term meta if needed

		$meta = $this->setting('meta');

		if ( ! empty( $meta ) )
		{
			$this->term_meta_instance = new Taxonomy_Term_Meta( $this->name, $meta );
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
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_action( 'current_screen', [ $this, 'check_current_taxonomy' ] );

		// -------------------------------------------------------------------------

		if ( $this->setting('pre_add_form') )
		{
			add_action( "{$this->name}_pre_add_form", [ $this, 'pre_add_form' ] );
		}

		// -------------------------------------------------------------------------

		// enqueue

		//add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

	}
	// hooks_admin()



	/**
	 * @since 1.0.0
	 */
	public function check_current_taxonomy( $screen )
	{

		if ( ! $screen || $screen->base !== 'edit-tags' )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( isset( $screen->taxonomy) && $screen->taxonomy === $this->name )
		{
			add_filter( 'get_terms', [ $this, 'admin_trim_term_description' ], 10, 2 );
		}

	}
	// check_current_taxonomy()



	/**
	 * @since 1.0.0
	 */
	public function admin_trim_term_description( $terms, $taxonomies )
	{

		if ( ! in_array( $this->name, $taxonomies ) )
		{
			return $terms;
		}

		// -------------------------------------------------------------------------

		foreach ( $terms as &$term )
		{
			if ( ! empty( $term->description ) )
			{
				$term->description = wp_trim_words( $term->description, 10 );
			}
		}

		// -------------------------------------------------------------------------

		return $terms;

	}
	// admin_trim_term_description()



	/**
	 * @since 1.0.0
	 */
	public function pre_add_form( $sortable )
	{

		if ( $content = $this->setting('pre_add_form') )
		{
			echo wpautop( $content );
		}

	}
	// pre_add_form()

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

		$obj = register_taxonomy(
			F::array_get( $this->settings, ['register', 'name'] ),
			F::array_get( $this->settings, ['register', 'types'] ),
			F::array_get( $this->settings, ['register', 'args'] )
		);

		// -------------------------------------------------------------------------

		if ( is_wp_error( $obj ) )
		{
			return $this->obj;
		}

		// -------------------------------------------------------------------------

		// get taxonomy object

		$this->obj = get_taxonomy( $this->name );

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

		if ( ! taxonomy_exists( $this->name ) )
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

		/*
		reserved terms:

			'attachment',
			'attachment_id',
			'author',
			'author_name',
			'calendar',
			'cat',
			'category',
			'category__and',
			'category__in',
			'category__not_in',
			'category_name',
			'comments_per_page',
			'comments_popup',
			'customize_messenger_channel',
			'customized',
			'cpage',
			'day',
			'debug',
			etc...

			@see: http://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms
		*/

		$this->{__FUNCTION__} = [];

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
	 * HELPERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

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
	protected function _x( ...$args )
	{

		$this->core->_x( ...$args );

	}
	// _x()



	/**
	 * @since 1.0.0
	 */
	public function get_term_image_url( $term_id, $size = 'thumbnail' )
	{

		if ( $image_id = get_term_meta( $term_id, 'image', true ) )
		{
			return wp_get_attachment_image_url( $image_id, $size );
		}

	}
	// get_term_image_url()



	/**
	 * @since 1.0.0
	 */
	public function get_term_image( $term_id, $size = 'thumbnail', $attr = [] )
	{

		if ( $image_id = get_term_meta( $term_id, 'image', true ) )
		{
			return wp_get_attachment_image( $image_id, $size, false, $attr );
		}

	}
	// get_term_image()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Custom_Taxonomy
