<?php

namespace DotAim\Shortcodes;

use DotAim\Base\Shortcode;

class Insert_Post extends Shortcode
{

	/**
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' )
	{

		global $post, $wp_query, $wp_current_filter;

		// -------------------------------------------------------------------------

		$defaults = [
			'post' 			=> '',	// {slug}|{id}
			'display'		=> '',	// title|link|excerpt|excerpt-only|content|post-thumbnail|all|{custom-template.php}

			'check_private'	=> true,
			'check_nested'	=> true,
		];

		$atts = shortcode_atts( $defaults, $atts );

		//extract( $atts );

		// -------------------------------------------------------------------------

		if ( empty( $atts['post'] ) || '0' === $atts['post'] )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// only used in single

		if ( ! is_single() || empty( $post->ID ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// don't insert into the same post

		if (		( intval( $atts['post'] ) > 0 && intval( $atts['post'] ) === $post->ID )
				 || $atts['post'] === $post->post_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// Don't allow inserted posts to be added to the_content more than once
		// (prevent infinite loops).

		if ( $atts['check_nested'] )
		{
			$done = false;

			foreach ( $wp_current_filter as $filter )
			{
				if ( 'the_content' === $filter )
				{
					if ( $done )
					{
						return $content;
					}
					else
					{
						$done = true;
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		$post_to_insert = $this->get_post_to_insert( $atts );

		if ( empty( $post_to_insert ) )
		{
			return $content;
		}

		$atts['post'] = $post_to_insert->ID;

		// -------------------------------------------------------------------------

		// If post to insert status is private, don't show to anonymous users
		// unless 'public' option is set.

		if ( $atts['check_private'] && 'private' === $post_to_insert->post_status )
		{
			return $content;
		}

		// -------------------------------------------------------------------------

		return $this->get_post_displayable_content( $post, $post_to_insert, $atts );

	}
	// output()



	/**
	 * @since 1.0.0
	 */
	private function get_post_to_insert( $atts )
	{

		// Get the WP_Post object from the provided slug or ID.

		if ( is_numeric( $atts['post'] ) )
		{
			return get_post( intval( $atts['post'] ) );
		}

		// -------------------------------------------------------------------------

		// Get list of post types that can be inserted (page, post, custom
		// types), excluding builtin types (nav_menu_item, attachment).

		$insertable_post_types = array_filter(
			get_post_types(),
			[$this, 'is_post_type_insertable']
		);

		// -------------------------------------------------------------------------

		$post = get_page_by_path( $atts['post'], OBJECT, $insertable_post_types );

		// -------------------------------------------------------------------------

		// If get_page_by_path() didn't find the page, check to see if the slug
		// was provided instead of the full path (useful for hierarchical pages
		// that are nested under another page).

		if ( is_null( $post ) )
		{
			global $wpdb;

			$id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_name = %s AND (post_status = 'publish' OR post_status = 'private') LIMIT 1",
					$atts['post']
				)
			);

			if ( $id )
			{
				$post = get_post( $id );
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $post->ID ) )
		{
			return $post;
		}

	}
	// get_post_to_insert()



	/**
	 * @since 1.0.0
	 */
	private function get_post_displayable_content( $post, $post_to_insert, $atts )
	{

		$out = '';

		// -------------------------------------------------------------------------

		switch ( $atts['display'] )
		{
			case 'title':

				break;

			// -----------------------------------------------------------------------

			case 'link':

				break;

			// -----------------------------------------------------------------------

			case 'excerpt':

				break;

			// -----------------------------------------------------------------------

			case 'excerpt-only':

				break;

			// -----------------------------------------------------------------------

			case 'content':

				break;

			// -----------------------------------------------------------------------

			case 'post-thumbnail':

				break;

			// -----------------------------------------------------------------------

			case 'all':

				break;

			// -----------------------------------------------------------------------

			// @notes: or make the default the roundup content

			default: // Display is either invalid, or contains a template file to use.

				break;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// get_post_displayable_content()

}
// class Insert_Post
