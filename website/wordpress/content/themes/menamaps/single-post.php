<?php

get_header();

// -----------------------------------------------------------------------------

$meta_show = DA()->Settings()->get('blog_single_meta_show');

?>

<section class="section">

	<div class="inner">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php
			if ( $custom_css = DA()->Post()->meta_value( DA()->meta_box_prefix . 'post_settings_custom_css' ) )
			{
				printf(
					'<style id="post_%d_custom_css">%s</style>',
					get_the_id(),
					do_shortcode( $custom_css )
				);
			}
			?>

			<article id="post_<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php
				if ( 'before_header' === $meta_show )
				{
					get_template_part('template_parts/single_meta');
				}
				?>

				<header class="post_header">
					<?php
					if ( ! DA()->Post()->meta_value( DA()->meta_box_prefix . 'post_settings_title_disable' ) )
					{
						the_title('<h1 class="post_title">', '</h1>');

						// -----------------------------------------------------------------

						if ( $subtitle = DA()->Post()->meta_value( DA()->meta_box_prefix . 'post_settings_subtitle' ) )
						{
							$subtitle_class = 'post_subtitle';

							if ( DA()->Settings()->get('blog_single_subtitle_font_thin') )
							{
								$subtitle_class .= ' font_thin';
							}

							printf('<h3 class="%s">%s</h3>', $subtitle_class, $subtitle);
						}
					}
					?>
				</header><!-- .post_header -->

				<?php
				if ( 'after_header' === $meta_show )
				{
					get_template_part('template_parts/single_meta');
				}
				?>

				<?php // @todo post_thumbnail/header image ?>

				<div class="post_content">

					<?php the_content(); ?>

				</div><!-- .post_content -->

				<?php if ( get_edit_post_link() ) : ?>
					<footer class="post_footer">
						<?php
						edit_post_link(
							sprintf(
								wp_kses(
									DA()->__('Edit <span class="screen_reader_text">%s</span>'),
									[
										'span' => [ 'class' => [] ],
									]
								),
								wp_kses_post( get_the_title() )
							),
							'<span class="edit_link">',
							'</span>'
						);
						?>
					</footer><!-- .post_footer -->
				<?php endif; ?>

			</article><!-- #post_<?php the_ID(); ?> -->

		<?php endwhile; ?>

			<?php
			if ( comments_open() || get_comments_number() )
			{
				comments_template();
			}
			?>

		<?php endif; ?>

	</div><!-- .inner -->

</section>

<?php get_footer(); ?>
