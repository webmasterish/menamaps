<?php

namespace DotAim\Posts;

use DotAim\F;

class Post_Engagement
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public $table_name;
	private $db_version							= '1.0';
	private $db_version_option_name	= 'dotaim_post_engagement_db_version';

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */


	/**
	 * @internal
	 */
	public function __construct()
	{

		global $wpdb;

		$this->table_name = "{$wpdb->prefix}dotaim_post_engagements";

	}
	// __construct()



	/**
	 * @internal
	 */
	public function init()
	{

		$this->register_ajax_handlers();

	}
	// init()



	/**
	 * @internal
	 */
	public function maybe_create_or_update_table()
	{

		$current_version = get_option( $this->db_version_option_name, '0' );

		if ( version_compare( $current_version, $this->db_version, '=' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			engagement_type varchar(20) NOT NULL,
			post_type varchar(20) NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			user_id bigint(20) unsigned NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY unique_engagement (engagement_type, post_id, user_id),
			KEY engagement_type (engagement_type),
			KEY post_type (post_type),
			KEY post_id (post_id),
			KEY user_id (user_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

		// -------------------------------------------------------------------------

		update_option( $this->db_version_option_name, $this->db_version );

	}
	// maybe_create_or_update_table()



	/**
	 * @internal
	 */
	public function toggle_engagement( $engagement_type, $post_type, $post_id )
	{

		$user_id = get_current_user_id();

		if ( ! $user_id )
		{
			return ['success' => false, 'message' => 'User not logged in'];
		}

		// -------------------------------------------------------------------------

		global $wpdb;

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$this->table_name} WHERE engagement_type = %s AND post_type = %s AND post_id = %d AND user_id = %d",
				$engagement_type, $post_type, $post_id, $user_id
			)
		);

		$args = [
			'engagement_type'	=> $engagement_type,
			'post_type'				=> $post_type,
			'post_id'					=> $post_id,
			'user_id'					=> $user_id,
		];

		if ( $existing )
		{
			$wpdb->delete( $this->table_name, $args );

			$action = 'removed';
		}
		else
		{
			$wpdb->insert( $this->table_name, $args );

			$action = 'added';
		}

		// -------------------------------------------------------------------------

		$count = $this->get_engagement_count( $engagement_type, $post_type, $post_id );

		// -------------------------------------------------------------------------

		return [
			'success'	=> true,
			'action'	=> $action,
			'count'		=> $count,
		];

	}
	// toggle_engagement()



	/**
	 * @internal
	 */
	public function get_engagement_count( $engagement_type, $post_type, $post_id )
	{

		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE engagement_type = %s AND post_type = %s AND post_id = %d",
				$engagement_type, $post_type, $post_id
			)
		);

	}
	// get_engagement_count()



	/**
	 * @internal
	 */
	public function has_user_engaged( $engagement_type, $post_type, $post_id )
	{

		if ( ! $user_id = get_current_user_id() )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		global $wpdb;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$this->table_name} WHERE engagement_type = %s AND post_type = %s AND post_id = %d AND user_id = %d",
				$engagement_type, $post_type, $post_id, $user_id
			)
		);

		return (bool) $result;

	}
	// has_user_engaged()


	/**
	 * @internal
	 */
	public function get_user_engaged_posts( $engagement_type = null, $post_type = null, $user_id = null )
	{

		if ( ! $user_id )
		{
			$user_id = get_current_user_id();
		}

		if ( ! $user_id )
		{
			return [];
		}

		// -------------------------------------------------------------------------

		global $wpdb;

		$query = "SELECT post_id FROM {$this->table_name} WHERE user_id = %d";
		$params = [$user_id];

		if ( $engagement_type )
		{
			$query .= " AND engagement_type = %s";
			$params[] = $engagement_type;
		}

		if ( $post_type )
		{
			$query .= " AND post_type = %s";
			$params[] = $post_type;
		}

		// -------------------------------------------------------------------------

		return $wpdb->get_col( $wpdb->prepare( $query, $params ) );

	}
	// get_user_engaged_posts()



	/**
	 * @internal
	 */
	public function button( $engagement_type, $post_type, $post_id, $args = [] )
	{

		$defaults = [
			'class_name'					=> 'post_engagement_button',
			'additional_class'		=> null,
			'title'								=> null,
			'title_engaged'				=> null,
			'icon'								=> '',
			'icon_engaged'				=> '',
			'show_count_if_zero'	=> false,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$count				= $this->get_engagement_count( $engagement_type, $post_type, $post_id );
		$has_engaged	= $this->has_user_engaged( $engagement_type, $post_type, $post_id );

		// -------------------------------------------------------------------------

		if ( ! is_null( $title ) && is_null( $title_engaged ) )
		{
			$title_engaged = $title;
		}

		// -------------------------------------------------------------------------

		$content = [];

		// -------------------------------------------------------------------------

		if ( $icon )
		{
			$icon_engaged = $icon_engaged ? $icon_engaged : $icon;

			// -----------------------------------------------------------------------

			$content[] = sprintf(
				'<div%s>%s</div>',
				F::html_attributes(['class' => ["{$class_name}_icon"]]),
				$has_engaged ? $icon_engaged : $icon
			);
		}

		// -------------------------------------------------------------------------

		if ( $show_count_if_zero || $count )
		{
			$content[] = sprintf(
				'<span class="%s">%s</span>',
				"{$class_name}_count",
				F::number_format_short( $count )
			);
		}

		// -------------------------------------------------------------------------

		if ( empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$element_tag	= 'div';
		$attr					= [

			'title' => $has_engaged ? $title_engaged : $title,

			// -----------------------------------------------------------------------

			'class' => [
				$class_name,
				"{$class_name}_{$engagement_type}",
				$has_engaged ? 'engaged' : null,
				$additional_class,
			],

			// -----------------------------------------------------------------------

			// @consider if needed

			/*
			'data-engagement_type'	=> $engagement_type,
			'data-post_type'				=> $post_type,
			'data-post_id'					=> $post_id,
			'data-count'						=> $count,
			*/

		];

		// -------------------------------------------------------------------------

		if ( get_current_user_id() )
		{
			$element_tag	= 'button';
			$attr					= array_merge( $attr, [

				'type' => 'button',

				// ---------------------------------------------------------------------

				// @consider spinner (hx-disabled-elt kind of does the job)

				'hx-post'					=> admin_url('admin-ajax.php'),
				'hx-trigger'			=> 'click',
				'hx-disabled-elt'	=> 'this',
				'hx-swap'					=> 'outerHTML',
				'hx-vals'					=> json_encode([
					'action'					=> 'toggle_post_engagement',
					'engagement_type'	=> $engagement_type,
					'post_type'				=> $post_type,
					'post_id'					=> $post_id,
					'args'						=> array_filter( $args ),
				]),

			]);
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<%1$s%2$s>%3$s</%1$s>',
			$element_tag,
			F::html_attributes( $attr ),
			implode( $content )
		);

	}
	// button()



	/**
	 * @internal
	 */
	public function register_ajax_handlers()
	{

		add_action(
			"wp_ajax_toggle_post_engagement",
			[$this, 'ajax_toggle_engagement']
		);

		// -------------------------------------------------------------------------

		// @consider if nopriv is needed as the action only applicable to signed in users

		/*
		add_action(
			"wp_ajax_nopriv_toggle_post_engagement",
			[$this, 'ajax_toggle_engagement']
		);
		*/

	}
	// register_ajax_handlers()



	/**
	 * @internal
	 */
	public function ajax_toggle_engagement()
	{

		$engagement_type	= isset( $_POST['engagement_type'] ) ? sanitize_text_field( $_POST['engagement_type'] ) : '';
		$post_type				= isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$post_id					= isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		// -------------------------------------------------------------------------

		$result = $this->toggle_engagement( $engagement_type, $post_type, $post_id );

		if ( ! empty( $result['success'] ) )
		{
			$args = ! empty( $_POST['args'] ) ? json_decode( stripslashes( $_POST['args'] ), true ) : null;

			echo $this->button( $engagement_type, $post_type, $post_id, $args );
		}
		else
		{
			// @todo: how to handle if there's an error

			//wp_send_json_error( $result['message'] );
		}

		// -------------------------------------------------------------------------

		wp_die();

	}
	// ajax_toggle_engagement()

}
// class Post_Engagement
