<?php

/*
if ( ! DA()->Settings()->get('blog_single_meta_show') )
{
	return;
}
*/

// -----------------------------------------------------------------------------

$remove_author		= DA()->Settings()->get('blog_single_meta_remove_author');
$remove_date			= DA()->Settings()->get('blog_single_meta_remove_date');
$remove_category	= DA()->Settings()->get('blog_single_meta_remove_category');

if ( $remove_author && $remove_date && $remove_category )
{
	return;
}

// -----------------------------------------------------------------------------

$content = [];

// -----------------------------------------------------------------------------

if ( ! $remove_author )
{
	$author_id			= get_the_author_meta('ID');
	$author_name		= get_the_author();
	$author_title		= sprintf( DA()->__('View %s\'s articles'), $author_name );
	$author_url			= get_author_posts_url( $author_id );

	$content[] = sprintf(
		'<cite>%s <a href="%s" title="%s">%s</a></cite>',
		DA()->__('By'),
		$author_url,
		$author_title,
		$author_name
	);
}

// -----------------------------------------------------------------------------

if ( ! $remove_date )
{
	$date					= get_the_date();
	$time					= get_the_time();
	$date_as_ago	= false; // @consider optionize

	if ( $date_as_ago )
	{
		$content[] = sprintf(
			DA()->__('<time datetime="%s" itemprop="datePublished" title="%s">%s ago</time>'),
			get_the_date('c'),
			sprintf( 'Published: %s %s', $date, $time ),
			human_time_diff( get_the_time('U'), current_time('timestamp') )
		);
	}
	else
	{
		$content[] = sprintf(
			DA()->__('<time datetime="%s" itemprop="datePublished" title="%s">on %s</time>'),
			get_the_date('c'),
			sprintf( 'Published: %s %s', $date, $time ),
			$date
		);
	}
}

// -----------------------------------------------------------------------------

if ( ! $remove_category )
{
	$categories_list = get_the_term_list( get_the_ID(), 'category', DA()->__('in '), ', ' );

	if ( ! empty( $categories_list ) && ! is_wp_error( $categories_list ) )
	{
		$content[] = sprintf(
			'<span>%s</span>',
			$categories_list
		);
	}
}

// -----------------------------------------------------------------------------

if ( ! empty( $content ) )
{
	$separator = ' <span>&bull;</span> '; // @todo: optionize

	printf(
		'<div class="post_meta not-format">%s</div>',
		implode( $separator, $content )
	);
}
?>
