<?php

if ( ! $add_user_account_menu = DA()->Settings()->get('header_add_user_account_menu') )
{
	return;
}

if ( 		! is_user_logged_in()
		 && ! get_option('users_can_register') )
{
	return;
}

// @consider
/*
if ( 		is_user_logged_in()
		 && ! DA()->Settings()->get_component_app_settings('users', 'dashboard', 'remove_admin_bar_in_frontend') )
{
	return;
}
*/

// -----------------------------------------------------------------------------

// @consider wp_get_referer() | get_permalink() | home_url()
//
// @todo:
// need to make sure we don't end up in an endless loop
// if the redirect_to page requires logging in

$redirect_to = wp_get_referer();

// -----------------------------------------------------------------------------

if ( ! is_user_logged_in() )
{
	$login_url = wp_login_url( $redirect_to );

	switch ( $add_user_account_menu )
	{
		case 'as_icon':

			?>
			<a href="<?php echo $login_url; ?>" class="header_nav_icon_button" title="<?php DA()->_e('Log In'); ?>">
				<span class="sr-only"><?php DA()->_e('Log In'); ?></span>
				<?php echo \DotAim\Icons::user_filled(['class' => 'w-5 h-5']); ?>
			</a>
			<?php

			break;

		// -------------------------------------------------------------------------

		case 'as_text':

			?>
			<a href="<?php echo $login_url; ?>" class="header_nav_icon_button font-medium">
				<?php DA()->_e('Log In'); ?>
			</a>
			<?php

			if ( DA()->Settings()->get('header_add_sign_up_button') )
			{
				?>
				<a href="<?php echo wp_registration_url();?>" class="header_nav_sign_up_button">
					<?php DA()->_e('Sign Up'); ?>
				</a>
				<?php
			}

			break;
	}
}
else
{
	if ( ! $user_id = get_current_user_id() )
	{
		return;
	}

	// ---------------------------------------------------------------------------

	$current_user	= wp_get_current_user();
	//$user_email	= $current_user->user_email;

	if ( current_user_can('read') )
	{
		$edit_profile_url = get_edit_profile_url( $user_id ); // admin_url('profile.php')
	}

	// ---------------------------------------------------------------------------

	?>
	<div class="header_nav_user_account_menu" x-data="{open: false}" @click.away="open = false">
		<button type="button" class="header_nav_user_account_menu_button" title="<?php DA()->_e('Open user menu'); ?>" @click="open = ! open">
			<span class="sr-only"><?php DA()->_e('Open user menu'); ?></span>
			<img alt="" src="<?php echo get_avatar_url( $current_user ); ?>" class="w-5 h-5 rounded-full hover:opacity-80" decoding="async">
		</button>
		<div class="header_nav_user_account_menu_dropdown" style="display:none;" x-show="open">
			<?php if ( $current_user->user_login ) : ?>
				<div class="py-3 px-4">
					<?php if ( $current_user->display_name ) : ?>
						<span class="user_display_name"><?php echo $current_user->display_name; ?></span>
					<?php endif; ?>
					<span class="block text-sm truncate"><?php echo $current_user->user_login; ?></span>
					<?php if ( ! empty( $user_email ) ) : ?>
						<span class="block text-sm truncate"><?php echo $user_email; ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $edit_profile_url ) ) : ?>
				<ul aria-labelledby="dropdown">
					<li>
						<a href="<?php echo $edit_profile_url; ?>"><?php DA()->_e('Edit Profile'); ?></a>
					</li>
				</ul>
			<?php endif; ?>
			<ul aria-labelledby="dropdown">
				<li>
					<a href="<?php echo wp_logout_url( $redirect_to ); ?>"><?php DA()->_e('Log Out'); ?></a>
				</li>
			</ul>
		</div>
	</div>
	<?php
}
