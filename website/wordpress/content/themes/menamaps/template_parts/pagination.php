<?php
	add_filter('navigation_markup_template', function( $template ) {

		// for the_posts_pagination();
		//$class = 'flex flex-wrap mt-4 gap-x-4 justify-center items-center';

		// for the_posts_navigation()
		//$class = 'flex flex-wrap mt-4 gap-x-2 justify-between';

		return '
			<hr class="max-w-2xl mx-auto">
			<nav class="navigation max-w-2xl mx-auto %1$s" aria-label="%4$s">
				<h2 class="sr-only">%2$s</h2>
				<div class="nav-links flex flex-wrap mt-4 gap-x-4 justify-center items-center">%3$s</div>
			</nav>';

	});

	the_posts_pagination();

	//the_posts_navigation();
?>
