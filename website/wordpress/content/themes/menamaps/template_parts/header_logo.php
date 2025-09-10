<?php

$logo_images = [];

$logo_url						= DA()->Settings()->get('header_logo_url');
$logo_url_dark_mode	= DA()->Settings()->get('header_logo_url_dark_mode');

if ( $logo_url && $logo_url_dark_mode )
{
	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'header_logo_image',
			'src'	=> $logo_url,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['mr-3 h-6 sm:h-9 dark:hidden'],
		])
	);

	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'header_logo_image_dark_mode',
			'src'	=> $logo_url_dark_mode,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['mr-3 h-6 sm:h-9 hidden dark:block'],
		])
	);
}
elseif ( $logo_url )
{
	$logo_images[] = sprintf(
		'<img%s/>',
		DA()->html_attributes([
			'id'	=> 'header_logo_image',
			'src'	=> $logo_url,
			'alt'		=> get_bloginfo('name'),
			'class'	=> ['mr-3 h-6 sm:h-9 dark:invert'],
		])
	);
}

?>
<a id="header_logo_link" href="<?php echo home_url(); ?>" class="flex items-center">
	<?php
	if ( ! empty( $logo_images ) )
	{
		echo implode( $logo_images );

		if ( DA()->Settings()->get('header_logo_with_site_title') )
		{
			get_template_part('template_parts/header_logo_text');
		}
	}
	else
	{
		get_template_part('template_parts/header_logo_text');
	}
	?>
</a>
