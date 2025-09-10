<?php

namespace DotAim\Posts;

use DotAim\Base\Singleton;
use DotAim\F;

final class Posts extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $post_type = 'post';

	/**
	 * @since 1.0.0
	 */
	public $settings;

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

		$this->settings = [

			// thumbnail column
			//
			// true is index 1 which is after cb, we can set it to other index/position
			// for example 3 or 4 etc...

			'thumbnail_column'			=> true,
			'thumbnail_column_size'	=> [ 30, 30 ], // 'thumbnail',

		];

		// -------------------------------------------------------------------------

		$this->hooks();

	}
	// init()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * INIT - END
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

		return ! empty( $this->settings[ $key ] ) ? $this->settings[ $key ] : '';

	}
	// setting()

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
	public function hooks()
	{

		$this->hooks_admin();

	}
	// hooks()



	/**
	 * @since 1.0.0
	 */
	protected function hooks_admin()
	{

		if ( ! is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// columns

		$this->hooks_admin_columns();

		// -------------------------------------------------------------------------

		// enqueue

		/*
		if ( is_callable( [ $this, 'admin_enqueue_scripts' ] ) )
		{
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 20 );
		}
		*/

	}
	// hooks_admin()



	/**
	 * @since 1.0.0
	 */
	protected function hooks_admin_columns()
	{

		// columns

		$columns_functions = array(
			'manage_edit_columns',
			'manage_posts_custom_column',
			'manage_edit_sortable_columns',
			'manage_edit_sortable_columns_orderby',

			'quick_edit_custom_box',
			'bulk_edit_custom_box',
		);

		// -------------------------------------------------------------------------

		if ( ! $columns_functions )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $columns_functions as $fn )
		{
			$method = array( $this, $fn );

			// -----------------------------------------------------------------------

			if ( ! is_callable( $method ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			switch ( $fn )
			{
				case 'manage_edit_columns':

					add_filter( "manage_edit-{$this->post_type}_columns", $method );

					break;

				// ---------------------------------------------------------------------

				case 'manage_posts_columns':

					add_filter( "manage_{$this->post_type}_posts_columns", $method );

					break;

				// ---------------------------------------------------------------------

				case 'manage_posts_custom_column':

					add_action( "manage_{$this->post_type}_posts_custom_column", $method, 10, 2 );

					break;

				// ---------------------------------------------------------------------

				case 'manage_edit_sortable_columns':

					add_filter( "manage_edit-{$this->post_type}_sortable_columns", $method );

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

		if ( ! $position = $this->setting('thumbnail_column') )
		{
			return $columns;
		}

		// -------------------------------------------------------------------------

		$new_columns = [
			"{$this->core->prefix}post_thumbnail" => '<span class="dotaim_column_icon dashicons-before dashicons-format-image"></span>',
		];

		// -------------------------------------------------------------------------

		return F::array_insert_at_position( $columns, $new_columns, absint( $position ) );

	}
	// manage_edit_columns()



	/**
	 * @since 1.0.0
	 */
	public function manage_posts_custom_column( $column_name, $post_id )
	{

		if ( ! $position = $this->setting('thumbnail_column') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = '';

		// -------------------------------------------------------------------------

		switch ( $column_name )
		{
			case "{$this->core->prefix}post_thumbnail":

				global $post;

				$size = $this->setting('thumbnail_column_size');

				if ( ! $url = $this->core->Post()->image_url( $size, [], $post ) )
				{
					break;
				}

				// ---------------------------------------------------------------------

				$attr = F::html_attributes([
					'class'=> ["{$this->core->prefix}post_thumbnail", 'dotaim_post_thumbnail'],
					'style'=> ['background-image' => "url({$url})" ],
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
	 * ENQUEUE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
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

		if ( $this->post_type !== $post_type )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->admin_styles();

	}
	// admin_enqueue_scripts()
	 */



	/**
	 * @since 1.0.0
	private function admin_styles()
	{

		$handle	= 'font-awesome';
		$src		= $this->core->url_css('font-awesome.min.css');
		$deps		= [];
		$ver		= '4.6.3';
		$media	= 'screen';

		wp_register_style( $handle, $src, $deps, $ver, $media );

		// -------------------------------------------------------------------------

		$handle	= $this->core->prefix . __FUNCTION__;
		$src		= $this->core->url_css('admin/admin.css');
		$deps		= ['font-awesome'];
		$ver		= $this->core->version;
		$media	= 'screen';

		// -------------------------------------------------------------------------

		wp_enqueue_style( $handle, $src, $deps, $ver, $media );

	}
	// admin_styles()
	 */

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ENQUEUE - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Posts
