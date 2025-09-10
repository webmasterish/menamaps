<?php

$header_nav_attr = [
	'class' => [
		'header_nav',
	],
];

$header_nav_attr = [
	'class' => [
		'header_nav',
	],
];

if ( $header_nav_colors_theme = DA()->Settings()->get('header_nav_colors_theme') )
{
	$header_nav_attr['class'][] = $header_nav_colors_theme;
}

if ( DA()->Settings()->get('header_nav_background_color_highlight') )
{
	$header_nav_attr['class'][] = 'background_color_highlight';
}

if ( DA()->Settings()->get('header_nav_glass') )
{
	$header_nav_attr['class'][] = 'glass';
}

if ( DA()->Settings()->get('header_nav_fixed') )
{
	$header_nav_attr['class'][] = 'header_nav_fixed';
}

if ( $header_nav_floating = DA()->Settings()->get('header_nav_floating') )
{
	$header_nav_attr['class'][] = 'header_nav_floating';
}

if ( $header_nav_border = DA()->Settings()->get('header_nav_border') )
{
	$header_nav_attr['class'][] = 'header_nav_with_border';
	$header_nav_attr['class'][] = $header_nav_border;
}

if ( $header_nav_shadow = DA()->Settings()->get('header_nav_shadow') )
{
	$header_nav_attr['class'][] = 'header_nav_with_shadow';
	$header_nav_attr['class'][] = $header_nav_shadow;
}

// -----------------------------------------------------------------------------

$header_nav_menu = null;

if ( has_nav_menu('header_nav') )
{
	$header_nav_menu = wp_nav_menu([
		'theme_location'=> 'header_nav',
		'container'			=> '',
		'menu_id'				=> 'header_nav_menu',
		'menu_class'		=> 'header_nav_menu',
		'echo'					=> false,
	]);
}

// -----------------------------------------------------------------------------

$header_nav_menu_container_attr = DA()->html_attributes([
	'id'		=> 'header_nav_menu_container',
	'class'	=> [
		$header_nav_floating ? 'hidden' : null,
		'lg:flex lg:w-auto lg:order-1',
		'justify-between items-center',
		'w-full',
	],
	':class' => ! $header_nav_floating ? "mobile_menu_open ? '' : 'hidden'" : null,
]);
?>

<nav<?php echo DA()->html_attributes( $header_nav_attr ); ?>>

	<div class="flex flex-wrap justify-between items-center mx-auto max-w-(--breakpoint-xl)">

		<?php get_template_part('template_parts/header_logo'); ?>

		<div class="flex items-center justify-self-end self-center gap-1 md:gap-2 lg:order-2">
			<?php get_template_part('template_parts/user_account_menu'); ?>
			<?php
				if ( ! DA()->Settings()->get('header_remove_share_button') )
				{
					get_template_part('template_parts/share_button');
				}
			?>
			<?php
				if ( 		! DA()->Settings()->get('header_remove_color_scheme_toggle')
						 && ! DA()->Settings()->get('color_scheme') )
				{
					get_template_part('template_parts/color_scheme_toggle');
				}
			?>

			<?php if ( $header_nav_menu ) : ?>
				<button type="button" class="header_nav_icon_button lg:hidden inline-flex items-center p-2" aria-controls="header_nav_menu_container" x-on:click="mobile_menu_open = ! mobile_menu_open">
					<span class="sr-only"><?php DA()->_e('Open menu'); ?></span>
					<?php
						echo \DotAim\Icons::bars(['class' => 'w-5 h-5', 'additional_attr' => 'x-show="! mobile_menu_open"']);
						echo \DotAim\Icons::close(['class' => 'w-5 h-5','additional_attr' => 'x-show="mobile_menu_open" style="display:none;"']);
					?>
				</button>
			<?php endif; ?>
		</div>

		<?php
		if ( $header_nav_menu )
		{
			printf(
				'<div%s>%s</div>',
				$header_nav_menu_container_attr,
				$header_nav_menu
			);
		}
		?>

	</div>

</nav>

<?php if ( $header_nav_floating && $header_nav_menu ) : ?>
<?php
$header_nav_floating_mobile_menu_attr = [
	'id'		=> 'header_nav_floating_mobile_menu_container',
	'class'	=> [
		'header_nav',
		$header_nav_colors_theme,
		'lg:hidden',
		'fixed top-0 z-50',
		'w-full h-full',
	],
	'style'		=> ['display' => 'none'],
	'x-show'	=> 'mobile_menu_open',
];

?>
	<nav<?php echo DA()->html_attributes( $header_nav_floating_mobile_menu_attr ); ?> x-transition>
		<div class="flex justify-end">
			<button type="button" class="header_nav_icon_button" aria-controls="header_nav_menu_mobile" x-on:click="mobile_menu_open = false">
				<?php echo \DotAim\Icons::close(['class' => 'w-6 h-6']); ?>
				<span class="sr-only"><?php DA()->_e('Close menu'); ?></span>
			</button>
		</div>

		<?php
		wp_nav_menu([
			'theme_location'=> 'header_nav',
			'container'			=> '',
			'menu_id'				=> 'header_nav_menu_mobile',
			'menu_class'		=> 'header_nav_menu',
		]);
		?>
	</nav>
<?php endif; ?>
