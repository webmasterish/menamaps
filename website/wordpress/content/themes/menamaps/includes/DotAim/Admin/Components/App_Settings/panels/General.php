<?php

namespace DotAim\Admin\Components\App_Settings;

use DotAim\F;

/**
 * @internal
 */
class General extends \DotAim\Admin\Panel
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function config()
	{

		$sections = [

			['id' => 'features'					, 'icon' => 'dashicons-yes-alt'],
			['id' => 'feeds'						, 'icon' => 'dashicons-rss'],
			['id' => 'restricted_access', 'icon' => 'dashicons-lock'],
			['id' => 'security'					, 'icon' => 'dashicons-shield'],

		];

		// -------------------------------------------------------------------------

		return [
			'title'		=> $this->__('General'),
			'icon'		=> 'dashicons-admin-settings',
			'content'	=> $this->populate_sections_args( $sections ),
		];

	}
	// config()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function apply()
	{

		if ( empty( $this->settings() ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_action( 'init', [ $this, 'feeds_apply' ] );

		// -------------------------------------------------------------------------

		if ( $this->settings(['restricted_access', 'enable']) )
		{
			add_action( 'parse_request', [ $this, 'restricted_access_apply' ], 1 );
		}

		// -------------------------------------------------------------------------

		add_action( 'init', [ $this, 'security_apply' ] );

	}
	// apply()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * features - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function features_content()
	{

		$out = [];

		// -------------------------------------------------------------------------

		$out[] = [
			'id' 			=> 'enable_contact',
			'label' 	=> $this->__('Enable Contact Submissions'),
			'desc' 		=> $this->__('This feature adds contact submissions related functionalities such as adding <code>contact_submission</code> custom post type along with its taxonomies.'),
			'type' 		=> 'checkbox',
			'default'	=> false,
		];

		// -------------------------------------------------------------------------

		$out[] = [
			'id' 					=> 'internal_notes_post_types',
			'type'				=> 'select_pre_populated',
			'checkboxes'	=> true,
			'multiple'		=> true,
			'data'				=> 'post_types',
			'label'				=> $this->__('Internal Notes Post Types'),
			'desc' 				=> sprintf(
					$this->__('This feature adds Internal Notes meta box to selected post types. Internal notes are private, and use a custom <code>comment_type</code> called <code>%sinternal_notes</code>. Only admins and editors are able to see and manage internal notes.'
				),
				$this->core->prefix
			),
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// features_content()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * features - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * feeds - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function feeds_content()
	{

		$out = [

			/*
			[
				'id' 			=> 'disable_all',
				'label' 	=> $this->__('Disable All Feeds'),
				'desc' 		=> $this->__('Disable all feeds'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],
			*/

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'disable_feed_links',
				'label' 	=> $this->__('Disable Post and comment feed links'),
				'desc' 		=> $this->__('Disable default <code>feed_links</code> (posts and comments feeds) as added by WordPress to <code>wp_head</code>'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'disable_feed_links_extra',
				'label' 	=> $this->__('Disable Category feed links'),
				'desc' 		=> $this->__('Disable default <code>feed_links_extra</code> (Category feed links) as added by WordPress to <code>wp_head</code>'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// feeds_content()



	/**
	 * @internal
	 */
	public function feeds_apply()
	{

		// @todo: doesn't work
		/*
		if ( $this->settings(['feeds', 'disable_all']) )
		{
			add_action('do_feed'							, [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_rdf'					, [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_rss'					, [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_rss2'					, [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_atom'					, [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_rss2_comments', [$this, 'feeds_apply_disable_all'], 1);
			add_action('do_feed_atom_comments', [$this, 'feeds_apply_disable_all'], 1);

			// -----------------------------------------------------------------------

			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );

			// -----------------------------------------------------------------------

			return;
		}
		*/

		// -------------------------------------------------------------------------

		if ( $this->settings(['feeds', 'disable_feed_links']) )
		{
			remove_action( 'wp_head', 'feed_links', 2 );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['feeds', 'disable_feed_links_extra']) )
		{
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}

	}
	// feeds_apply()



	/**
	 * @internal
	 */
	public function feeds_apply_disable_all()
	{

		wp_die(
			sprintf(
				$this->__('No feed available, please visit the <a href="%s">%s</a>!'),
				esc_url( home_url( '/' ) ),
				get_blog_info('name')
			)
		);

	}
	// feeds_apply_disable_all()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * feeds - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * restricted_access - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function restricted_access_content()
	{

		$out = [

			[
				'id' 			=> 'enable',
				'label' 	=> $this->__('Enable Restricted Access'),
				'desc' 		=> $this->__('Enabling restricted access prevents non logged in users from accessing the site.'),
				'type' 		=> 'checkbox',
				'after'		=> sprintf(
					'<p class="description"><strong>%s</strong></p>',
					$this->__('Currently doesn\'t work if WP Super Cache caching is enabled.')
				),
				'default'	=> false,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'handle_restricted',
				'label' 	=> $this->__('Handle Restricted Visitors'),
				'desc' 		=> $this->__('Select an option of how to handle restricted vistors.'),
				'type' 		=> 'select_checkbox',
				'options'	=> [
					'redirect_to_login'	=> $this->__('Redirect to Login Page'),
					'redirect_to_page'	=> $this->__('Redirect to Selected Page'),
					'redirect_to_url'		=> $this->__('Redirect to Custom URL'),
					'die_message'				=> $this->__('Display a Message'),
				],
				'default'			=> 'redirect_to_login',
				'conditional'	=> ['enable' => 'checked'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'page',
				'label' 			=> $this->__('Redirect to Selected Page'),
				'type' 				=> 'select_pre_populated',
				'data'				=> 'pages',
				'conditional'	=> ['handle_restricted' => 'redirect_to_page'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'url',
				'label' 			=> $this->__('Redirect to Custom URL'),
				'type' 				=> 'text',
				'conditional'	=> ['handle_restricted' => 'redirect_to_url'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'message',
				'label' 			=> $this->__('Message'),
				'type' 				=> 'textarea',
				'desc' 				=> $this->__('The message is used in <code>wp_die()</code>'),
				'default'			=> $this->__('Access to this site is restricted.'),
				'conditional'	=> ['handle_restricted' => 'die_message'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'status_code',
				'label' 			=> $this->__('Redirection Status Code'),
				'type' 				=> 'select',
				'options'			=> [
					'301'	=> $this->__('301 Permanent'),
					'302'	=> $this->__('302 Undefined'),
					'307'	=> $this->__('307 Temporary'),
				],
				'default'			=> '302',
				'conditional'	=> ['enable' => 'checked'],
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// restricted_access_content()



	/**
	 * @internal
	 */
	public function restricted_access_apply( $wp )
	{

		// @todo: doesn't work if WP Super Cache caching is enabled

		// -------------------------------------------------------------------------

		// needed to avoid an endless loop

		remove_action( 'parse_request', [ $this, 'restricted_access_apply' ], 1 );

		// -------------------------------------------------------------------------

		if ( ! $this->settings(['restricted_access', 'enable']) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$is_restricted = ! (
				is_admin()
		 || is_user_logged_in()
		 || ( defined( 'WP_INSTALLING' ) && isset( $_GET['key'] ) )
		);

		if ( false === $is_restricted )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$handle_restricted	= $this->settings(['restricted_access', 'handle_restricted']);
		$status_code				= $this->settings(['restricted_access', 'status_code']);

		// -------------------------------------------------------------------------

		switch ( $handle_restricted )
		{
			case 'die_message':

				$title		= sprintf( $this->__('%s - Restricted Access'), get_bloginfo( 'name' ) );
				$message	= $this->settings(['restricted_access', 'message']);

				wp_die( $message, $title );

				break;

			// -----------------------------------------------------------------------

			case 'redirect_to_page':

				if ( $page_id = $this->settings(['restricted_access', 'page']) )
				{
					if ( $page_id = get_post_field( 'ID', $page_id ) )
					{
						unset( $wp->query_vars );

						// -----------------------------------------------------------------

						$wp->query_vars['page_id'] = $page_id;

						// -----------------------------------------------------------------

						return;
					}
				}

				break;

			// -----------------------------------------------------------------------

			case 'redirect_to_url':

				if ( $url = $this->settings(['restricted_access', 'url']) )
				{
					$redirect_to_url = $url;
				}

				break;

			// -----------------------------------------------------------------------

			//case 'redirect_to_login':
			default:

				$status_code		= 302;
				$current_path		= empty( $_SERVER['REQUEST_URI'] ) ? home_url() : $_SERVER['REQUEST_URI'];
				$redirect_to_url= wp_login_url( $current_path );

				break;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $redirect_to_url ) )
		{
			wp_redirect( $redirect_to_url, $status_code );

			die;
		}

	}
	// restricted_access_apply()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * restricted_access - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * security - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function security_content()
	{

		$out = [

			[
				'id' 			=> 'disable_xmlrpc',
				'label' 	=> $this->__('Disable XML-RPC'),
				'desc' 		=> sprintf(
					$this->__('Disable XML-RPC. XML-RPC is a security hazard. It\'s exploited by attackers looking to break in and launch a DDoS attack. Check it in <a href="%1$s">%1$s</a>.  Visit <a href="%2$s">Admin -> Permalinks</a> for <code>.htaccess</code> to take effect.'),
					site_url('xmlrpc.php'),
					admin_url('options-permalink.php')
				),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// security_content()



	/**
	 * @internal
	 */
	public function security_apply()
	{

		if ( $this->settings(['security', 'disable_xmlrpc']) )
		{
			add_filter( 'mod_rewrite_rules', [$this, 'security_mod_rewrite_rules_filter']);

			// @notes: not needed if using .htaccess, also not sure if it really works
			//add_filter( 'xmlrpc_enabled', '__return_false' );
		}

	}
	// security_apply()



	/**
	 * @internal
	 */
	public function security_mod_rewrite_rules_filter( $rules )
	{

		$new_rules = implode("\n", [
			'# Block xmlrpc.php requests',
			'# XML-RPC is a security hazard.',
			'# It\'s exploited by attackers looking to break in and launch a DDoS attack.',
			'# this will block access to ' . site_url('xmlrpc.php'),
			'<Files xmlrpc.php>',
			'  Order Deny,Allow',
			'  Deny from all',
			'</Files>',
		]);

		// -------------------------------------------------------------------------

		return "{$rules}\n\n{$new_rules}\n";

	}
	// security_mod_rewrite_rules_filter()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * security - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class General
