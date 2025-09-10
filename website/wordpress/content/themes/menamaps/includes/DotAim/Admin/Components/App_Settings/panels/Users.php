<?php

namespace DotAim\Admin\Components\App_Settings;

use DotAim\F;

/**
 * @internal
 */
class Users extends \DotAim\Admin\Panel
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

			[
				'id'			=> 'dashboard',
				'icon'		=> 'dashicons-dashboard',
				'before'	=> '<p>All settings applied to <strong>non admins</strong>.</p>',
			],

			// -----------------------------------------------------------------------

			['id' => 'avatars'],

		];

		// -------------------------------------------------------------------------

		return [
			'title'		=> $this->__('Users'),
			'icon'		=> 'dashicons-admin-users',
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

		$this->dashboard_apply();

		// -------------------------------------------------------------------------

		$this->avatars_apply();

	}
	// apply()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * dashboard - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function dashboard_content()
	{

		global $wp_version;

		// -------------------------------------------------------------------------

		$out = [

			[
				'id'						=> 'access_allowed_roles',
				'label'					=> $this->__('Dashboard Page Access Allowed Roles'),
				'desc'					=> $this->__('Administrators are allowed by default.'),
				'type'					=> 'select_pre_populated',
				'checkboxes'		=> true,
				'multiple'			=> true,
				'data'					=> 'users_roles',
				'data_exclude'	=> ['administrator'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'redirect_disallowed_users_to',
				'label' 	=> $this->__('Redirect Disallowed Users To'),
				'type' 		=> 'select',
				'options'	=> [
					''						=> $this->__('Home Page (Default)'),
					'profile'			=> $this->__('Profile Page'),
					'custom_url'	=> $this->__('Custom URL'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'id'					=> 'redirect_to_custom_url',
				'label'				=> $this->__('Redirect URL'),
				'desc'				=> $this->__('Redirect disallowed users to a custom URL. Shortcodes allowed.'),
				'type'				=> 'text',
				'attr'				=> ['class' => ['large-text']],
				'conditional' => ['redirect_disallowed_users_to' => 'custom_url'],
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_admin_bar_wordpress_menu',
				'label'	=> $this->__('Remove Admin Bar WordPress Menu'),
				'desc'	=> $this->__('Remove the WordPress menu from admin bar.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_admin_bar_in_frontend',
				'label'	=> $this->__('Remove Admin Bar in Frontend'),
				'desc'	=> $this->__('Remove the admin bar when in frontend.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_admin_menu_dashboard',
				'label'	=> $this->__('Remove Admin Menu Dashboard'),
				'desc'	=> $this->__('Remove the dashboard menu from side menu bar.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'change_howdy',
				'label'	=> $this->__('Change "Howdy,"'),
				'desc'	=> $this->__('Add custom message to replace default "Howdy,", used in admin bar user menu.'),
				'type'	=> 'text',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_help_tab',
				'label'	=> $this->__('Remove Help Tab'),
				'desc'	=> $this->__('Remove the help tab.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_profile_admin_color_scheme',
				'label'	=> $this->__('Remove Profile Admin Color Scheme'),
				'desc'	=> $this->__('Remove admin color scheme section in profile page.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_profile_section_application_passwords',
				'label'	=> $this->__('Remove Profile Application Passwords Section'),
				'desc'	=> $this->__('Remove application passwords section in profile page.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_footer_wordpress_thankyou',
				'label'	=> $this->__('Remove Footer WordPress Thank you'),
				'desc'	=> $this->__('Remove "Thank you for creating with WordPress." message from footer.'),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'remove_footer_wordpress_version',
				'label'	=> $this->__('Remove Footer WordPress Version'),
				'desc'	=> sprintf( $this->__('Remove "Version %s" message from footer.'), $wp_version ),
				'type'	=> 'checkbox',
			],

			// -----------------------------------------------------------------------

			/*
			@consider:
			- only apply the settings to "subscriber" role
			- theme for dashboard
			*/

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// dashboard_content()



	/**
	 * @internal
	 */
	private function dashboard_apply()
	{

		if ( current_user_can('administrator') || defined('DOING_AJAX') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_action( 'admin_init', [$this, 'dashboard_apply_redirect'] );

		// -------------------------------------------------------------------------

		if ( 		$this->settings(['dashboard', 'remove_admin_bar_wordpress_menu'])
				 || $this->settings(['dashboard', 'change_howdy']) )
		{
			add_filter( 'admin_bar_menu', [$this, 'dashboard_apply_admin_bar_menu'], 99999 );
		}

		// -------------------------------------------------------------------------

		if ( 		! is_admin()
				 && $this->settings(['dashboard', 'remove_admin_bar_in_frontend']) )
		{
			add_filter( 'show_admin_bar', '__return_false' );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'remove_admin_menu_dashboard']) )
		{
			add_action( 'admin_menu', [$this, 'dashboard_apply_admin_menu'] );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'remove_help_tab']) )
		{
			add_action( 'admin_head', [$this, 'dashboard_apply_remove_help_tab'] );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'remove_profile_admin_color_scheme']) )
		{
			add_action(
				'admin_head-profile.php',
				[$this, 'dashboard_apply_remove_admin_color_scheme_picker']
			);
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'remove_profile_section_application_passwords']) )
		{
			add_filter(
				'wp_is_application_passwords_available_for_user',
				[$this, 'dashboard_apply_wp_is_application_passwords_available_for_user'],
				10,
				2
			);
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'remove_footer_wordpress_thankyou']) )
		{
			add_filter('admin_footer_text', '__return_empty_string');
		}

		if ( $this->settings(['dashboard', 'remove_footer_wordpress_version']) )
		{
			add_filter('update_footer', '__return_empty_string');
		}

	}
	// dashboard_apply()



	/**
	 * @internal
	 */
	public function dashboard_apply_redirect()
	{

		global $pagenow;

		$allowed_pages = ['profile.php'];

		if ( in_array( $pagenow, $allowed_pages ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$access_allowed_roles = $this->settings(['dashboard', 'access_allowed_roles']);

		if ( ! empty( $access_allowed_roles ) )
		{
			foreach ( $access_allowed_roles as $role )
			{
				if ( current_user_can( $role ) )
				{
					return;
				}
			}
		}

		// -------------------------------------------------------------------------

		$redirect_to = home_url();

		switch ( $this->settings(['dashboard', 'redirect_disallowed_users_to']) )
		{
			case 'profile':

				$redirect_to = admin_url('profile.php');

				break;

			// -----------------------------------------------------------------------

			case 'custom_url':

				if ( $custom_url = $this->settings(['dashboard', 'redirect_to_custom_url']) )
				{
					$redirect_to = do_shortcode( trim( $custom_url ) );
				}

				break;
		}

		// -------------------------------------------------------------------------

		wp_redirect( $redirect_to );

	}
	// dashboard_apply_redirect()



	/**
	 * @internal
	 */
	public function dashboard_apply_admin_bar_menu( $wp_admin_bar )
	{

		if ( is_admin() )
		{
			$menu_ids = ['comments', 'new-content'];
		}
		else
		{
			$menu_ids = ['dashboard', 'comments', 'new-content', 'edit'];
		}

		if ( $this->settings(['dashboard', 'remove_admin_bar_wordpress_menu']) )
		{
			$menu_ids[] = 'wp-logo';
		}

		foreach ( $menu_ids as $menu_id )
		{
			$wp_admin_bar->remove_menu( $menu_id );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['dashboard', 'change_howdy']) )
		{
			$my_account = $wp_admin_bar->get_node('my-account');

			if ( ! isset( $my_account->title ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$wp_admin_bar->add_node([
				'id'		=> 'my-account',
				'title'	=> str_replace(
					'Howdy,',
					$this->settings(['dashboard', 'change_howdy']),
					$my_account->title
				),
			]);
		}

	}
	// dashboard_apply_admin_bar_menu()



	/**
	 * @internal
	 */
	public function dashboard_apply_admin_menu( $wp_admin_bar )
	{

		remove_menu_page( 'index.php' ); // Dashboard

	}
	// dashboard_apply_admin_menu()



	/**
	 * @internal
	 */
	public function dashboard_apply_remove_help_tab()
	{

		$screen = get_current_screen();

		$screen->remove_help_tabs();

	}
	// dashboard_apply_remove_help_tab()



	/**
	 * @internal
	 */
	public function dashboard_apply_remove_admin_color_scheme_picker()
	{

		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

	}
	// dashboard_apply_remove_admin_color_scheme_picker()



	/**
	 * @internal
	 */
	public function dashboard_apply_wp_is_application_passwords_available_for_user( $available, $user )
	{

		return user_can( $user, 'manage_options' ) ? $available : false;

	}
	// dashboard_apply_wp_is_application_passwords_available_for_user()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * dashboard - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * avatar - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function avatars_content()
	{

		$out = [

			[
				'id' 			=> 'defaults',
				'label' 	=> $this->__('Avatar Defaults'),
				'type' 		=> 'array_field',
				'fields'	=> [
					[
						'id'		=> 'name',
						'type'	=> 'text',
						'label'	=> $this->__('Avatar Name'),
					],
					[
						'id'		=> 'url',
						'type'	=> 'media',
						'label'	=> $this->__('Avatar URL'),
					],
				],
				'default'	=> [
					[
						'name'	=> sprintf( $this->__('%s Logo Mark'), F::array_get( APP, ['site_name'], get_bloginfo('name') ) ),
						'url'		=> $this->core->url_images('logo_mark_1024.png'),
					],
				],
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// avatars_content()



	/**
	 * @internal
	 */
	private function avatars_apply()
	{

		$custom_avatar_defaults = $this->settings(['avatars', 'defaults']);

		if ( ! empty( $custom_avatar_defaults ) )
		{
			add_filter( 'avatar_defaults', [ $this, 'avatar_defaults_filter' ] );
		}

	}
	// avatars_apply()



	/**
	 * @internal
	 */
	public function avatar_defaults_filter( $avatar_defaults = [] )
	{

		$custom_avatar_defaults = $this->settings(['avatars', 'defaults']);

		if ( ! empty( $custom_avatar_defaults ) )
		{
			foreach ( $custom_avatar_defaults as $custom_avatar )
			{
				if ( ! empty( $custom_avatar['url'] ) && ! empty( $custom_avatar['name'] ) )
				{
					$avatar_defaults[ $custom_avatar['url'] ] = $custom_avatar['name'];
				}
			}
		}

		// ---------------------------------------------------------------------

		return $avatar_defaults;

	}
	// avatar_defaults_filter()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * avatar - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Users
