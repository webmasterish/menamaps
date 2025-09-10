<?php get_header(); ?>

<main class="grid place-items-center px-6 py-10 lg:px-8">
	<div class="text-center">
		<p class="text-5xl font-semibold text-primary-900 dark:text-white">404</p>
		<h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-5xl text-primary-900 dark:text-white"><?php DA()->_e('Page not found'); ?></h1>
		<p class="mt-6 text-base leading-7 text-primary-600 dark:text-primary-100"><?php DA()->_e('Sorry, the page you are looking for could not be found.'); ?></p>
		<div class="mt-10 flex items-center justify-center gap-x-6">
			<a href="<?php bloginfo('url'); ?>" class="rounded-md px-3.5 py-2.5 font-semibold shadow-xs text-sm text-primary-50 bg-primary-900 hover:bg-primary-700 dark:bg-primary-800 dark:hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900"><?php DA()->_e('Go back home'); ?></a>
			<span class="text-primary-900 underline hover:text-primary-700 dark:text-primary-200 dark:hover:text-primary-100"><?php
				echo do_shortcode(sprintf(
					'[email_link email="%s" text="%s"]',
					DA()->get_contact_email(),
					DA()->__('Contact support'),
					'<span aria-hidden="true">&rarr;</span>'
				));
			?></span>
		</div>
	</div>
</main>

<?php get_footer(); ?>
