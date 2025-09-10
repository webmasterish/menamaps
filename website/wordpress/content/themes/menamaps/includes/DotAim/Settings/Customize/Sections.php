<?php

namespace DotAim\Settings\Customize;

use DotAim\Base\Singleton;
use DotAim\F;

final class Sections extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public $data = [];

	/**
	 * @internal
	 */
	public $methods;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function init()
	{

		if ( wp_doing_ajax() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_customize_preview() )
		{
			add_action(
				'customize_controls_print_footer_scripts',
				[ $this, 'colors_randomizer_script' ]
			);
		}

	}
	// init()



	/**
	 * @internal
	 */
	public function colors_randomizer_script()
	{

		ob_start();

		?>
		<script>
		jQuery(document).ready(function($) {
			$('#customize-control-colors_randomizer')
			.removeClass('customize-control-hidden')
			.append(
				'<button class="button button-secondary" id="colors_randomizer_button">' +
					'<span class="dashicons dashicons-randomize" style="vertical-align:middle;margin-inline-end:5px"></span>' +
					'<?php $this->_e('Generate Random Colors'); ?>' +
				'</button>'
			);

			function hslToHex(h, s, l) {
				l /= 100;
				const a = s * Math.min(l, 1 - l) / 100;
				const f = n => {
					const k = (n + h / 30) % 12;
					const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
					return Math.round(255 * color).toString(16).padStart(2, '0');
				};
				return `#${f(0)}${f(8)}${f(4)}`;
			}

			function generateRandomColors() {
				const primary = {
					h: Math.floor(Math.random() * 360),
					s: Math.floor(Math.random() * 10) + 5,
					l: Math.floor(Math.random() * 20) + 40
				};

				const harmonyTypes		= ['complementary', 'analogous', 'triadic', 'splitComplementary'];
				const selectedHarmony	= harmonyTypes[Math.floor(Math.random() * harmonyTypes.length)];

				let secondaryHue;
				switch(selectedHarmony) {
					case 'complementary':
						secondaryHue = (primary.h + 180) % 360;
						break;
					case 'analogous':
						secondaryHue = (primary.h + (Math.random() > 0.5 ? 30 : -30)) % 360;
						break;
					case 'triadic':
						secondaryHue = (primary.h + 120) % 360;
						break;
					case 'splitComplementary':
						secondaryHue = (primary.h + 180 + (Math.random() > 0.5 ? 30 : -30)) % 360;
						break;
				}

				const secondary = {
					h: secondaryHue,
					s: Math.floor(Math.random() * 20) + 60,
					l: Math.floor(Math.random() * 20) + 45
				};

				return {
					primary: hslToHex(primary.h, primary.s, primary.l),
					secondary: hslToHex(secondary.h, secondary.s, secondary.l)
				};
			}

			$('#colors_randomizer_button').on('click', function(e) {
				e.preventDefault();
				const colors = generateRandomColors();

				const primaryControl		= wp.customize.control('primary_color');
				const secondaryControl	= wp.customize.control('secondary_color');

				if (primaryControl) {
					primaryControl.setting.set(colors.primary);
					primaryControl.container.find('.wp-color-picker').wpColorPicker('color', colors.primary);
				}

				if (secondaryControl) {
					secondaryControl.setting.set(colors.secondary);
					secondaryControl.container.find('.wp-color-picker').wpColorPicker('color', colors.secondary);
				}
			});
		});
		</script>
		<?php

		// -------------------------------------------------------------------------

		echo F::minify( ob_get_clean() );

	}
	// colors_randomizer_script()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GETTERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function get( $key = null, $refresh = false )
	{

		if ( empty( $this->data ) || $refresh )
		{
			$this->data = [];

			// -----------------------------------------------------------------------

			if ( ! $methods = $this->methods() )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$exclude_methods = [
				__FUNCTION__,
				'colors_randomizer_script',
			];

			// -----------------------------------------------------------------------

			foreach ( $methods as $method_name )
			{
				if ( in_array( $method_name, $exclude_methods ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				if ( ! $item = call_user_func( [ $this, $method_name ] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				// must have fields or callback

				if ( empty( $item['fields'] ) && empty( $item['callback'] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				// if empty, set section slug as method name

				if ( empty( $item['slug'] ) )
				{
					$item['slug'] = $method_name;
				}

				// ---------------------------------------------------------------------

				// title fallback

				if ( empty( $item['title'] ) )
				{
					$item['title'] = F::humanize( $item['slug'], false );
				}

				// ---------------------------------------------------------------------

				//$this->data[ $method_name ] = $item;
				$this->data[ $item['slug'] ] = $item;
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $key ) )
		{
			return $this->data;
		}

		// -------------------------------------------------------------------------

		if ( isset( $this->data[ $key ] ) )
		{
			return $this->data[ $key ];
		}

		// -------------------------------------------------------------------------

		return null;

	}
	// get()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GETTERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SECTIONS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function colors_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$fields = [

			[
				'name'				=> 'primary_color',
				'type'				=> 'color',
				'label'				=> $this->__('Primary Color'),
				'default'			=> '#737373', // neutral
				'description'	=> $this->__('Used for main text, headings, and UI elements throughout the site.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'secondary_color',
				'type'				=> 'color',
				'label'				=> $this->__('Secondary Color'),
				'default'			=> '#3B82F6', // blue
				'description'	=> $this->__('Used for links, buttons, accents, and interactive elements.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'colors_randomizer',
				'label'				=> $this->__('Colors Generator'),
				'description'	=> $this->__('Generate harmonious color combinations.'),
				'type'				=> 'hidden',
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'color_scheme',
				'label'		=> $this->__('Color Scheme'),
				'type'		=> 'select',
				'choices'	=> [
					''			=> $this->__('Toggle Color Scheme'),
					'light'	=> $this->__('Only Light Color Scheme'),
					'dark'	=> $this->__('Only Dark Color Scheme'),
				],
			],

		];

		// -------------------------------------------------------------------------

		$out = [
			'title'			=> $this->__('Colors Settings'),
			'slug'			=> $slug,
			'fields'		=> $fields,
			'priority'	=> 20,
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// colors_section()



	/**
	 * @internal
	 */
	public function header_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$fields = [

			[
				'name'		=> 'nav_colors_theme',
				'label'		=> $this->__('Nav: Colors Theme'),
				'type'		=> 'select',
				'default'	=> 'primary',
				'choices'	=> [
					''					=> $this->__('None (neutral)'),
					'primary'		=> $this->__('Primary'),
					'secondary'	=> $this->__('Secondary'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_dark',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Nav: Dark Color Scheme'),
				'description'	=> $this->__('Applies dark color scheme even in light mode.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_background_color_highlight',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Nav: Background Color Highlight'),
				'description'	=> $this->__('Adds background color 50 and on dark mode background color 800.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_glass',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Nav: Glass'),
				'description'	=> $this->__('Adds glass effect to nav. Best viewed with fixed nav.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_fixed',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Nav: Fixed'),
				'description'	=> $this->__('Use box shadow-sm for a non floating nav.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_floating',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Nav: Floating'),
				'description'	=> $this->__('Adds rounded-sm full to achive a floating effect.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'nav_border',
				'label'				=> $this->__('Nav: Border'),
				'description'	=> $this->__('Adds bottom border or all round borders if floating.'),
				'type'				=> 'select',
				'choices'			=> [
					''							=> $this->__('None'),
					'border-solid'	=> $this->__('border-solid'),
					'border-dashed'	=> $this->__('border-dashed'),
					'border-dotted'	=> $this->__('border-dotted'),
					'border-double'	=> $this->__('border-double'),
					'border-solid'	=> $this->__('border-solid'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'nav_shadow',
				'label'		=> $this->__('Nav: Shadow'),
				'type'		=> 'select',
				'choices'	=> [
					''							=> $this->__('None'),
					'shadow-xs'			=> $this->__('shadow-xs'),
					'shadow-sm'				=> $this->__('shadow-sm'),
					'shadow-md'			=> $this->__('shadow-md'),
					'shadow-lg'			=> $this->__('shadow-lg'),
					'shadow-xl'			=> $this->__('shadow-xl'),
					'shadow-2xl'		=> $this->__('shadow-2xl'),
					'shadow-inner'	=> $this->__('shadow-inner'),
				],
			],

			// -----------------------------------------------------------------------

			// @consider logo in middle

			[
				'name'				=> 'logo_url',
				'type'				=> 'image',
				'label'				=> $this->__('Header Logo'),
				'description'	=> sprintf(
					$this->__('Upload a logo that will be displayed in header. If empty <code>%s</code> will be used, in this case it is "%s".'),
					"bloginfo('name')",
					get_bloginfo('name')
				),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_url_dark_mode',
				'type'				=> 'image',
				'label'				=> $this->__('Header Logo: Dark Mode'),
				'description'	=> $this->__('If empty, regular logo colors will be inverted.  Only applicable if regular logo is set.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_with_site_title',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Add Site Title With Logo'),
				'description'	=> sprintf(
					$this->__('It will use <code>%s</code> which will be "%s".'),
					"bloginfo('name')",
					get_bloginfo('name')
				),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'add_user_account_menu',
				'label'				=> $this->__('Add User Account Menu in Top Bar'),
				'description'	=> $this->__('Account menu includes Log in/Log out and user account links. Applicable if user registration allowed.'),
				'type'				=> 'select',
				'choices'			=> [
					''				=> $this->__('Don\'t Add'),
					'as_icon'	=> $this->__('As Icon'),
					'as_text'	=> $this->__('As Text (Log In)'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'add_sign_up_button',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Add Sign Up button'),
				'description'	=> $this->__('Adds a primary button if user is not logged in and if registration is allowed. Applicable if account menu is displayed as text based on the selection above.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'remove_color_scheme_toggle',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Remove Color Scheme Toggle Button'),
				'description'	=> $this->__('Remove the button that toggles dark/light color scheme'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'remove_share_button',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Remove Share Button'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'custom_css',
				'type'				=> 'textarea',
				'label'				=> $this->__('Header Custom CSS'),
				'default'			=> '',
				'description'	=> $this->__('Add any custom CSS related to header (without <code>style</code> tags).'),
			],

		];

		// -------------------------------------------------------------------------

		foreach ( $fields as &$field )
		{
			$field['name'] = "{$id}_{$field['name']}";
		}

		// -------------------------------------------------------------------------

		$out = [
			'title'			=> $this->__('Header Settings'),
			'slug'			=> $slug,
			'fields'		=> $fields,
			'priority'	=> 30,
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// header_section()



	/**
	 * @internal
	 */
	public function blog_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$archive_fields = [

			[
				'name'				=> 'header_title',
				'type'				=> 'text',
				'label'				=> $this->__('Header Title'),
				'description'	=> $this->__('This will override the default blog page title.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'header_subtitle',
				'type'		=> 'text',
				'label'		=> $this->__('Header Subtitle'),
				'default'	=> '',
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'center',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Center'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'post_title_regular_link',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Post Title Regular Link'),
				'description'	=> $this->__('If uncheced will add <code>not-format</code> CSS class'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'show_category',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Show Category'),
				'description'	=> $this->__('Category is dispayed on top of the title'),
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'show_excerpt',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Show Excerpt'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'show_date',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Show Date'),
				'description'	=> $this->__('Date is displayed under the title'),
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'add_separator',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Add Separator'),
			],

		];

		// -------------------------------------------------------------------------

		$single_fields = [

			[
				'name'	=> 'single_subtitle_font_thin',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Single: Subtitle Font Thin'),
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'single_meta_show',
				'label'	=> $this->__('Single Meta: Show'),
				'type'		=> 'select',
				'choices'	=> [
					''							=> $this->__('Don\'t Show'),
					'before_header'	=> $this->__('Before Header'),
					'after_header'	=> $this->__('After Header'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'single_meta_remove_author',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Single Meta: Remove Author'),
			],

			// -----------------------------------------------------------------------

			// @consider option for date as ago

			[
				'name'	=> 'single_meta_remove_date',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Single Meta: Remove Date'),
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'single_meta_remove_category',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Single Meta: Remove Category'),
			],

			// -----------------------------------------------------------------------

			// @todo engagment bar

		];

		// -------------------------------------------------------------------------

		$fields = array_merge(
			$archive_fields,
			$single_fields
		);

		// -------------------------------------------------------------------------

		foreach ( $fields as &$field )
		{
			$field['name'] = "{$id}_{$field['name']}";
		}

		// -------------------------------------------------------------------------

		$out = [
			'title'			=> $this->__('Blog Settings'),
			'slug'			=> $slug,
			'fields'		=> $fields,
			'priority'	=> 600,
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// blog_section()



	/**
	 * @internal
	 */
	public function footer_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$fields = [

			[
				'name'		=> 'colors_theme',
				'label'		=> $this->__('Footer Colors Theme'),
				'type'		=> 'select',
				'default'	=> 'primary',
				'choices'	=> [
					''					=> $this->__('None (neutral)'),
					'primary'		=> $this->__('Primary'),
					'secondary'	=> $this->__('Secondary'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'dark',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Footer Dark Color Scheme'),
				'description'	=> $this->__('Applies dark color scheme even in light mode.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'background_color_highlight',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Footer Background Color Highlight'),
				'description'	=> $this->__('Adds bg-primary-50 dark:bg-neutral-800 CSS classes.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'border',
				'label'				=> $this->__('Footer Border'),
				'description'	=> $this->__('Adds top border to footer.'),
				'type'				=> 'select',
				'choices'			=> [
					''							=> $this->__('None'),
					'border-solid'	=> $this->__('border-solid'),
					'border-dashed'	=> $this->__('border-dashed'),
					'border-dotted'	=> $this->__('border-dotted'),
					'border-double'	=> $this->__('border-double'),
					'border-solid'	=> $this->__('border-solid'),
				],
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'inner_separator',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Add Inner Separator'),
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'logo',
				'type'		=> 'image',
				'label'		=> $this->__('Footer Logo'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_dark_mode',
				'type'				=> 'image',
				'label'				=> $this->__('Footer Logo: Dark Mode'),
				'description'	=> $this->__('If empty, regular logo colors will be inverted.  Only applicable if regular logo is set.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_with_site_title',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Add Site Title With Logo'),
				'description'	=> sprintf(
					$this->__('It will use <code>%s</code> which will be "%s".'),
					"bloginfo('name')",
					get_bloginfo('name')
				),
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'description',
				'type'		=> 'textarea',
				'label'		=> $this->__('Footer Description'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'social_media',
				'type'				=> 'textarea',
				'label'				=> $this->__('Footer Social Media'),
				'description'	=> $this->__('Social media URLs Separated by new lines or commas e.g. <code>https://x.com/dotaim | Follow us on X/Twitter,https://youtube.com/dotaim | Subscribe to our YouTube Channel</code>'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'social_media_add_grayscale',
				'type'				=> 'checkbox',
				'label'				=> $this->__('Footer Social Media: Add grayscale effect'),
				'description'	=> $this->__('This will make the icons grayscal and colored on hover'),
			],

			// -----------------------------------------------------------------------

			[
				'name'	=> 'remove_by_dotaim',
				'type'	=> 'checkbox',
				'label'	=> $this->__('Remove link "by DotAim" in footer after copyright'),
			],

		];

		// -------------------------------------------------------------------------

		foreach ( $fields as &$field )
		{
			$field['name'] = "{$id}_{$field['name']}";
		}

		// -------------------------------------------------------------------------

		$out = [
			'title'			=> $this->__('Footer Settings'),
			'slug'			=> $slug,
			'fields'		=> $fields,
			'priority'	=> 700,
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// footer_section()



	/**
	 * @internal
	 */
	public function misc_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$fields = [

			[
				'name'				=> 'add_to_wp_head',
				'type'				=> 'textarea',
				'label'				=> $this->__('Add To WP Head'),
				'default'			=> '',
				'description'	=> sprintf(
					$this->__('Add %s to <code>wp_head</code>. Shotcodes allowed.'),
					esc_html('<link>, <style>, <script>, etc...'),
				),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'add_to_wp_footer',
				'type'				=> 'textarea',
				'label'				=> $this->__('Add To WP Footer'),
				'default'			=> '',
				'description'	=> sprintf(
					$this->__('Add %s to <code>wp_footer</code>. Shotcodes allowed.'),
					esc_html('<script>, etc...'),
				),
			],

		];

		// -------------------------------------------------------------------------

		$out = [
			'title'			=> $this->__('Misc Settings'),
			'slug'			=> $slug,
			'fields'		=> $fields,
			'priority'	=> 800,
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// misc_section()



	/**
	 * @internal
	 */
	public function login_section()
	{

		$slug	= __FUNCTION__;
		$id		= str_replace( '_section', '', $slug );

		// -------------------------------------------------------------------------

		$args	= [ 'url' => wp_login_url() ];
		$url	= add_query_arg( $args, admin_url('customize.php') );

		// -------------------------------------------------------------------------

		$fields = [

			[
				'name'		=> 'logo_link_to_home',
				'type'		=> 'checkbox',
				'label'		=> $this->__('Login Logo Link to Home'),
				'default'	=> false,
			],

			// -----------------------------------------------------------------------

			[
				'name'		=> 'logo_title',
				'type'		=> 'text',
				'label'		=> $this->__('Login Logo Title'),
				'default'	=> get_bloginfo( 'name', 'display' ),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo',
				'type'				=> 'image',
				'label'				=> $this->__('Login Logo'),
				'description'	=> $this->__('Upload a logo for your login screen. Leave it empty for default WordPress logo.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_width',
				'type'				=> 'text',
				'label'				=> $this->__('Login Logo Width'),
				'default'			=> '84px',
				'description'	=> $this->__('Set login logo width in px or any other acceptable CSS unit.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_height',
				'type'				=> 'text',
				'label'				=> $this->__('Login Logo Height'),
				'default'			=> '84px',
				'description'	=> $this->__('Set login logo height in px or any other acceptable CSS unit.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'logo_padding_bottom',
				'type'				=> 'text',
				'label'				=> $this->__('Login Logo Padding Bottom'),
				'default'			=> '5px',
				'description'	=> $this->__('Set bottom padding for login logo in px or any other acceptable CSS unit.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'message',
				'type'				=> 'textarea',
				'label'				=> $this->__('Message'),
				'default'			=> '',
				'description'	=> $this->__('Message displayed in login screen, before the login form.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'accent_color',
				'type'				=> 'color',
				'label'				=> $this->__('Accent Color'),
				'description'	=> $this->__('The color used for buttons, links, message border, icons, etc.'),
			],

			// -----------------------------------------------------------------------

			[
				'name'				=> 'custom_css',
				'type'				=> 'textarea',
				'label'				=> $this->__('Login Screen Custom CSS'),
				'default'			=> '',
				'description'	=> $this->__('Add any custom CSS to the login screen.'),
			],

		];

		// -------------------------------------------------------------------------

		foreach ( $fields as &$field )
		{
			$field['name'] = "{$id}_{$field['name']}";
		}

		// -------------------------------------------------------------------------

		$out = [
			'title'				=> $this->__('Login Settings'),
			'slug'				=> $slug,
			'fields'			=> $fields,
			'priority'		=> 900,
			'description' => sprintf(
				$this->__('Start customizing by going to a <a href="%s">live preview</a>'),
				$url
			),
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// login_section()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SECTIONS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Sections
