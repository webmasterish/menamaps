<?php get_header(); ?>

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

				<header class="post_header">
					<?php
					if ( ! DA()->Post()->meta_value( DA()->meta_box_prefix . 'post_settings_title_disable' ) )
					{
						the_title('<h1 class="post_title">', '</h1>');

						// ---------------------------------------------------------------

						if ( $subtitle = DA()->Post()->meta_value( DA()->meta_box_prefix . 'post_settings_subtitle' ) )
						{
							printf('<h3 class="post_subtitle">%s</h3>', $subtitle);
						}
					}
					?>
				</header><!-- .post_header -->

				<?php // @todo post_thumbnail/header image ?>

				<div class="post_content">
					<?php
					the_content();

					if ( is_page() )
					{
						wp_link_pages([
							'before' => '<div class="page_links">' . DA()->__('Pages:'),
							'after'  => '</div>',
						]);
					}
					?>
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
