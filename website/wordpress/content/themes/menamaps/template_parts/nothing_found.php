<?php

$title = DA()->__('Nothing Found');

?>

<article class="post">
	<header>
		<h1><?php echo $title; ?></h1>
	</header>

	<div>
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) )
		{
			printf(
				'<p>%s</p>',
				sprintf(
					wp_kses(
						DA()->__('Ready to publish your first post? <a href="%1$s">Get started here</a>.'),
						['a' => ['href' => []]]
					),
					esc_url( admin_url( 'post-new.php' ) )
				)
			);
		}
		elseif ( is_search() )
		{
			printf(
				'<p>%s</p>',
				DA()->__('Sorry, but nothing matched your search terms. Please try again with some different keywords.')
			);

			get_search_form();
		}
		else
		{
			printf(
				'<p>%s</p>',
				DA()->__('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.')
			);

			get_search_form();
		}
		?>
	</div>

</article>
