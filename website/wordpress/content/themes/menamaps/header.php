<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>

</head>

<body <?php body_class('antialiased flex flex-col min-h-screen bg-white dark:bg-primary-900'); ?>>

<?php
$header_attr = [
	'id'			=> 'page_header',
	'class'		=> [
		DA()->Settings()->get('header_nav_dark')	? 'dark' : null,
		DA()->Settings()->get('header_nav_fixed')	? 'min-h-20' : null,
	],
	'x-data'	=> '{ mobile_menu_open: false }',
];
?>
<header<?php echo DA()->html_attributes( $header_attr ); ?>>

	<?php
	if ( $header_custom_css = DA()->Settings()->get('header_custom_css') )
	{
		printf( '<style id="header_custom_css">%s</style>', trim( $header_custom_css ) );
	}
	?>

	<?php get_template_part('template_parts/header_nav'); ?>

</header><!-- #<?php echo $header_attr['id']; ?> -->

<main id="page_main">
