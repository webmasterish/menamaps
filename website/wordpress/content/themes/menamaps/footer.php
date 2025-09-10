
</main><!-- #page_main -->

<?php

$footer_attr = [
	'id'		=> 'page_footer',
	'class'	=> [],
];

if ( DA()->Settings()->get('footer_dark') )
{
	$footer_attr['class'][] = 'dark';
}

// -----------------------------------------------------------------------------

$footer_inner_attr = [
	'id'		=> 'page_footer_inner',
	'class'	=> [],
];

if ( $footer_colors_theme = DA()->Settings()->get('footer_colors_theme') )
{
	$footer_inner_attr['class'][] = $footer_colors_theme;
}

if ( DA()->Settings()->get('footer_background_color_highlight') )
{
	$footer_inner_attr['class'][] = 'background_color_highlight';
}

if ( $footer_border = DA()->Settings()->get('footer_border') )
{
	$footer_inner_attr['class'][] = 'footer_with_border';
	$footer_inner_attr['class'][] = $footer_border;
}

// -----------------------------------------------------------------------------

$footer_inner_content_attr = [
	'id'		=> 'page_footer_inner_content',
	'class'	=> [],
];

if ( DA()->Settings()->get('footer_inner_separator') )
{
	$footer_inner_content_attr['class'][] = 'border-t';
}

// -----------------------------------------------------------------------------

$logo_images = [];

$logo_url						= DA()->Settings()->get('footer_logo');
$logo_url_dark_mode	= DA()->Settings()->get('footer_logo_dark_mode');

if ( $logo_url && $logo_url_dark_mode )
{
	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'footer_logo_image',
			'src'	=> $logo_url,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['h-6 sm:h-9 dark:hidden'],
		])
	);

	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'footer_logo_image_dark_mode',
			'src'	=> $logo_url_dark_mode,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['h-6 sm:h-9 hidden dark:block'],
		])
	);
}
elseif ( $logo_url )
{
	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'footer_logo_image',
			'src'	=> $logo_url,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['h-6 sm:h-9 dark:invert'],
		])
	);
}


?>
<footer<?php echo DA()->html_attributes( $footer_attr ); ?>>

	<div<?php echo DA()->html_attributes( $footer_inner_attr ); ?>>

		<div<?php echo DA()->html_attributes( $footer_inner_content_attr ); ?>>

			<?php if ( ! empty( $logo_images ) ) : ?>
				<a href="<?php echo home_url(); ?>" class="flex justify-center items-center gap-2 max-w-max mx-auto">
					<?php echo implode( $logo_images ); ?>
					<?php if ( DA()->Settings()->get('footer_logo_with_site_title') ) : ?>
						<span id="footer_site_title"><?php bloginfo('name'); ?></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>


			<?php
			if ( $description = DA()->Settings()->get('footer_description') )
			{
				printf( '<p class="text-center">%s</p>', $description );
			}
			?>


			<?php if ( has_nav_menu('footer_nav') ) : ?>
				<nav id="footer_nav">
					<?php
						wp_nav_menu([
							'theme_location'=> 'footer_nav',
							'container'			=> '',
							'menu_id'				=> 'footer_nav_menu',
							/*
							'menu_class'		=> implode(' ', [
								'footer_nav_menu',
								'flex flex-wrap items-center justify-center gap-4',
								'text-primary-800 dark:text-primary-200',
							]),
							'add_a_class'	=> 'underline-offset-4 hover:underline',
							*/
						]);
					?>
				</nav>
			<?php endif; ?>


			<div id="copyright" class="text-center">
				<span><?php DA()->_e('Copyright'); ?> © <?php echo do_shortcode('[this_year]'); ?></span>

				<a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>. <?php DA()->_e('All Rights Reserved.'); ?>

				<?php if ( ! DA()->Settings()->get('footer_remove_by_dotaim') ) : ?>
					<span>by <a href="https://dotaim.com">DotAim</a></span>
				<?php endif; ?>
			</div>


			<?php get_template_part('template_parts/footer_social_media'); ?>

		</div><!-- #<?php echo $footer_inner_content_attr['id']; ?> -->

	</div><!-- #<?php echo $footer_inner_attr['id']; ?> -->

</footer><!-- #<?php echo $footer_attr['id']; ?> -->

<?php wp_footer(); ?>

</body>
</html>
