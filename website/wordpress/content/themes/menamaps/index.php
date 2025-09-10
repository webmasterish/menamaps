<?php get_header(); ?>

<section class="section">

	<?php
		global $wp_query;

		$inner_attr = DA()->html_attributes([
			'class' => [
				'inner format lg:format-lg format-primary dark:format-invert',
				DA()->Settings()->get('blog_center') ? 'text-center' : null,
			],
		]);
	?>
	<div<?php echo $inner_attr; ?>>

		<?php if ( have_posts() ) :	?>

			<?php
			if ( is_home() )
			{
				// default
				$archive_title		= DA()->__('Blog');
				$archive_subtitle	= '';

				if ( $page_id = get_option('page_for_posts'))
				{
					if ( $page_title = DA()->Post()->title( $page_id ) )
					{
						$archive_title = $page_title;
					}

					// @consider: subtitle from post meta
					//$archive_subtitle	= '';
				}

				// ---------------------------------------------------------------------

				if ( $header_title = DA()->Settings()->get('blog_header_title') )
				{
					$archive_title = $header_title;
				}

				// ---------------------------------------------------------------------

				if ( $header_subtitle = DA()->Settings()->get('blog_header_subtitle') )
				{
					$archive_subtitle = $header_subtitle;
				}
			}
			elseif ( is_post_type_archive() )
			{
				$archive_title = post_type_archive_title( '', false );
			}
			elseif ( is_search() )
			{
				$format = '%1$s <span>"%2$s"</span>';

				// ---------------------------------------------------------------------

				$found_posts = $wp_query->found_posts ?? 0;

				if ( $found_posts > 1 )
				{
					$format .= ' <small class="font-normal">(%3$d)</small>';
				}

				// ---------------------------------------------------------------------

				$archive_title = sprintf(
					$format,
					DA()->__('Search Results for:'),
					get_search_query(),
					$found_posts
				);
			}
			else
			{
				$archive_title = get_the_archive_title();
			}

			// @todo:
			// - $archive_description = get_the_archive_description();
			?>

			<?php if ( ! empty( $archive_title ) ) : ?>
				<header class="max-w-2xl mx-auto mb-12">
					<h1 class="mb-4 lg:mb-4"><?php echo $archive_title; ?></h1>
					<?php if ( ! empty( $archive_subtitle ) ) : ?>
						<h3 class="font-light text-primary-500 dark:text-primary-400"><?php echo $archive_subtitle; ?></h3>
					<?php endif; ?>
				</header>
			<?php endif; ?>


			<?php while ( have_posts() ): the_post(); ?>
				<article id="post_<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php
					if ( DA()->Settings()->get('blog_show_category') )
					{
						$categories = get_the_category();

						if ( ! empty( $categories ) )
						{
							printf(
								'<div class="not-format"><a%s>%s</a></div>',
								DA()->html_attributes([
									'href'	=> esc_url( get_category_link( $categories[0]->term_id ) ),
									'class'	=> [
										'inline-flex items-center justify-center',
										'px-2.5 py-0.5',
										//'rounded-sm',
										'rounded-full',
										'text-xs font-medium',
										DA()->Settings()->get('blog_post_title_regular_link')
										? 'bg-primary-100 text-primary-800 dark:bg-primary-700 dark:text-primary-300'
										: 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:text-secondary-300',
									],
								]),
								esc_html( $categories[0]->name )
							);
						}
					}
					?>

					<header class="post_header">
						<?php
						if ( is_singular() )
						{
							the_title( '<h1 class="post_title">', '</h1>' );
						}
						else
						{
							the_title(
								sprintf(
									'<h2 class="post_title !my-4%s"><a href="%s" rel="bookmark">',
									DA()->Settings()->get('blog_post_title_regular_link') ? null : ' not-format',
									esc_url( get_permalink() )
								),
								'</a></h2>'
							);

							if ( DA()->Settings()->get('blog_show_date') )
							{
								printf(
									'<div class="flex items-center justify-center">%s</div>',
									the_date( null, '<span>', '</span>', false )
								);
							}
						}
						?>
					</header><!-- .post_header -->

					<?php // @todo post_thumbnail/header image ?>

					<?php if ( is_singular() ) : ?>
						<div class="post_content">
							<?php
							the_content(
								sprintf(
									wp_kses(
										DA()->__('Continue reading<span class="sr-only"> "%s"</span>'),
										['span' => ['class' => []]]
									),
									wp_kses_post( get_the_title() )
								)
							);
							?>
						</div><!-- .post_content -->

					<?php else : ?>

						<?php if ( DA()->Settings()->get('blog_show_excerpt') ) : ?>
							<div class="post_summary"><?php the_excerpt(); ?></div>
						<?php endif; ?>

					<?php endif; ?>

				</article><!-- #post_<?php the_ID(); ?> -->

				<?php
				if ( 		! is_singular()
						 && $wp_query->current_post +1 !== $wp_query->post_count
						 && DA()->Settings()->get('blog_add_separator') )
				{
					echo '<hr class="max-w-2xl mx-auto">';
				}
				?>
			<?php endwhile; ?>

			<?php get_template_part('template_parts/pagination'); ?>

		<?php else: ?>

			<?php get_template_part('template_parts/nothing_found'); ?>

		<?php endif; ?>

	</div><!-- .inner -->
</section>

<?php get_footer(); ?>
