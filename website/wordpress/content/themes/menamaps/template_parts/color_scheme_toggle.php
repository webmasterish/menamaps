<button id="color_scheme_toggle" type="button" class="header_nav_icon_button" title="<?php DA()->_e('Toggle dark theme'); ?>">
	<?php
		echo \DotAim\Icons::moon_filled(['id' => 'color_scheme_toggle_dark_icon', 'class' => 'hidden w-5 h-5']);
		echo \DotAim\Icons::sun_filled(['id' => 'color_scheme_toggle_light_icon', 'class' => 'hidden w-5 h-5']);
	?>
	<span class="sr-only"><?php DA()->_e('Toggle dark theme'); ?></span>
</button>
