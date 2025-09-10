<?php

if ( post_password_required() )
{
	return;
}
?>

<section id="comments">

	<h2 class="comments_title">
		<?php
		$comments_number = get_comments_number();

		switch ( $comments_number )
		{
			case 0:

				printf(
					esc_html( DA()->__('Be the first to comment on &ldquo;%1$s&rdquo;') ),
					get_the_title()
				);

				break;

			// -----------------------------------------------------------------------

			case 1:

				printf(
					esc_html( DA()->__('One comment on &ldquo;%1$s&rdquo;') ),
					get_the_title()
				);

				break;

			// -----------------------------------------------------------------------

			default:

				printf(
					esc_html( DA()->_nx(
						'%1$s comment on &ldquo;%2$s&rdquo;',
						'%1$s comments on &ldquo;%2$s&rdquo;',
						$comments_number,
						'comments title'
					)),
					number_format_i18n( $comments_number ),
					get_the_title()
				);

				break;
		}
		?>
	</h2>

	<?php if ( have_comments() ) : ?>
		<ol class="comment_list">
			<?php
			wp_list_comments([
				'style'				=> 'ol',
				'short_ping'	=> true,
				'avatar_size'	=> 40,
				'callback'		=> function( $comment, $args, $depth ) {
					?>
					<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
						<article class="comment_body">
							<?php if ( 0 != $args['avatar_size'] ) : ?>
								<div class="comment_avatar">
									<?php echo get_avatar( $comment, $args['avatar_size'], '', '', ['class' => 'rounded-full'] ); ?>
								</div>
							<?php endif; ?>
							<div class="comment_content">
								<header class="comment_meta">
									<?php printf( '<cite>%s</cite>', get_comment_author_link() ); ?>
									<time datetime="<?php comment_time( 'c' ); ?>">
										<?php
										printf(
											DA()->_x('%1$s at %2$s', '1: date, 2: time'),
											get_comment_date(),
											get_comment_time()
										);
										?>
									</time>
								</header>
								<div class="comment_text">
									<?php comment_text(); ?>
								</div>
								<?php
								comment_reply_link( array_merge( $args, [
									'reply_text'	=> DA()->__('Reply'),
									'depth'				=> $depth,
									'max_depth'		=> $args['max_depth'],
									'before'			=> '<div class="comment_reply">',
									'after'				=> '</div>',
								] ) );
								?>
							</div>
						</article>
					<?php
					// @notes:
					// no closing tag </li>
					// it's handled by end-callback or wordpress will add it
				},
				'end-callback' => function( $comment ) {

						printf( '</li><!-- #comment-%s -->', get_comment_ID() );

				},
			]);
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav class="comment_navigation" role="navigation">
			<div class="flex justify-between">
				<div class="comment_navigation_previous"><?php previous_comments_link( esc_html( DA()->__('&larr; Older Comments') ) ); ?></div>
				<div class="comment_navigation_next"><?php next_comments_link( esc_html( DA()->__('Newer Comments &rarr;') ) ); ?></div>
			</div>
		</nav>
		<?php endif; ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
	comment_form([
		'class_form'         => 'comment_form',
		'title_reply_before' => '<h2 id="reply-title" class="comment_reply_title">',
		'title_reply_after'  => '</h2>',
		'class_submit'       => 'submit button secondary rounded-sm cursor-pointer',
	]);
	?>

</section><!-- #comments -->
