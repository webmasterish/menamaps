<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url('/'); ?>">
	<label for="s" class="sr-only mb-2 text-sm font-medium text-neutral-900 dark:text-white"><?php DA()->_e('Search for:'); ?></label>
	<div class="relative w-full">
		<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
			<?php echo \DotAim\Icons::search(['class' => 'w-5 h-5 text-neutral-500 dark:text-neutral-400']); ?>
		</div>
		<input type="search" name="s" id="s" value="<?php echo get_search_query(); ?>" class="block w-full p-4 ps-10 text-sm text-neutral-900 border border-neutral-300 rounded-lg bg-neutral-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="<?php echo esc_attr( DA()->__('Search &hellip;') ); ?>" required minlength="2">
		<button type="submit" class="absolute end-2.5 bottom-2.5 px-4 py-2 rounded-lg font-medium text-sm text-white bg-primary-500 hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-hidden focus:ring-4 focus:ring-primary-300  dark:focus:ring-primary-800"><?php DA()->_e('Search'); ?></button>
	</div>
</form>
