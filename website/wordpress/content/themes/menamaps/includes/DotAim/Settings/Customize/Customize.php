<?php

namespace DotAim\Settings\Customize;

use DotAim\Base\Singleton;
use DotAim\F;

final class Customize extends Singleton
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public $capability;

	/**
	 * @internal
	 */
	public $settings_name;

	/**
	 * @internal
	 */
	public $wp_customize;

	/**
	 * @internal
	 */
	public $Sections;
	public $Panels;

	/**
	 * @internal
	 */
	private $options;

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

		$this->capability = 'edit_theme_options';

		// -------------------------------------------------------------------------

		$this->settings_name = $this->core->db_option_name;

		// -------------------------------------------------------------------------

		if ( $this->Sections()->get() )
		{
			add_action( 'customize_register', [ $this, 'customize_register' ] );

			// -----------------------------------------------------------------------

			$this->theme_colors_apply();

			// -----------------------------------------------------------------------

			if ( $add_to_wp_head = $this->get('add_to_wp_head') )
			{
				add_action( 'wp_head', function() use( $add_to_wp_head ) {
					echo do_shortcode( $add_to_wp_head );
				});
			}

			// -----------------------------------------------------------------------

			if ( $add_to_wp_footer = $this->get('add_to_wp_footer') )
			{
				add_action( 'wp_footer', function() use( $add_to_wp_footer ) {
					echo do_shortcode( $add_to_wp_footer );
				});
			}

			// -----------------------------------------------------------------------

			$this->apply_login_visual_options();
		}

	}
	// init()



	/**
	 * @internal
	 */
	private function apply_login_visual_options()
	{

		add_action( 'login_enqueue_scripts', [ $this, 'login_enqueue_scripts' ] );

		// -------------------------------------------------------------------------

		add_filter( 'login_headerurl', [ $this, 'login_headerurl'] );

		// -------------------------------------------------------------------------

		add_filter( 'login_headertext', [ $this, 'login_headertext'] );

		// -------------------------------------------------------------------------

		add_filter( 'login_message', [ $this, 'login_message'] );

	}
	// apply_login_visual_options()



	/**
	 * @internal
	 */
	public function login_enqueue_scripts()
	{

		$out = [];

		// -------------------------------------------------------------------------

		$logo_css = [];

		if ( $logo = $this->get('login_logo') )
		{
			$logo_css[] = "background-image: url({$logo})";
		}

		// -------------------------------------------------------------------------

		if ( $logo_width = $this->get('login_logo_width') )
		{
			$logo_css[] = "width: {$logo_width}";
		}

		// -------------------------------------------------------------------------

		if ( $logo_height = $this->get('login_logo_height') )
		{
			$logo_css[] = "height: {$logo_height}";
		}

		// -------------------------------------------------------------------------

		if ( $logo_width || $logo_height )
		{
			$logo_css[] = "background-size: {$logo_width} {$logo_height}";
		}

		// -------------------------------------------------------------------------

		if ( $logo_padding_bottom = $this->get('login_logo_padding_bottom') )
		{
			$logo_css[] = "padding-bottom: {$logo_padding_bottom}";
		}

		// -------------------------------------------------------------------------

		if ( $logo_css )
		{
			$out[] = sprintf(
				'body.login div#login h1 a {%s !important;}',
				implode( ' !important;', $logo_css )
			);
		}

		// -------------------------------------------------------------------------

		if ( $accent_color = $this->get('login_accent_color') )
		{
			$out[] = implode('', [
				"body.login .message { border-color: $accent_color !important; }",
				"body.login input:focus { border-color: $accent_color !important; box-shadow: 0 0 0 1px $accent_color !important; }",
				".wp-core-ui .button, .wp-core-ui .button-secondary { color: $accent_color !important; border-color: $accent_color !important; }",
				".wp-core-ui .button:hover, .wp-core-ui .button-secondary:hover { color: $accent_color !important; opacity: 0.8 !important; }",
				".wp-core-ui .button-secondary:focus, .wp-core-ui .button.focus, .wp-core-ui .button:focus { border-color: $accent_color !important; box-shadow: 0 0 0 1px $accent_color !important; }",
				".login .button.wp-hide-pw { border-color: transparent !important; box-shadow: none !important; }",
				".login .button.wp-hide-pw:focus { border-color: $accent_color !important; box-shadow: 0 0 0 1px $accent_color !important; }",
				".wp-core-ui .button-primary { background: $accent_color !important; border-color: $accent_color !important; color: white !important; }",
				".wp-core-ui .button-primary:hover { background: $accent_color !important; border-color: $accent_color !important; opacity: 0.8 !important; color: white !important; }",
				".login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover { color: $accent_color !important; }",
				".login #backtoblog a:focus, .login #nav a:focus, .login h1 a:focus { color: $accent_color !important; }",
				"a:focus { box-shadow: 0 0 0 1px $accent_color !important; }",
				sprintf(
					'input[type="checkbox"]:checked::before { content: url("data:image/svg+xml;base64,%s") !important; }',
					base64_encode("<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><path d='M14.83 4.89l1.34.94-5.81 8.38H9.02L5.78 9.67l1.34-1.25 2.57 2.4z' fill='$accent_color'/></svg>")
				),
			]);
		}

		// -------------------------------------------------------------------------

		if ( $css = $this->get('login_custom_css') )
		{
			$out[] = $css;
		}

		// -------------------------------------------------------------------------

		if ( ! $out )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = '<style>' . implode( $out ) . '</style>';

		// -------------------------------------------------------------------------

		echo $out;

	}
	// login_enqueue_scripts()



	/**
	 * @internal
	 */
	public function login_headerurl( $out )
	{

		if ( $this->get('login_logo_link_to_home') )
		{
			$out = get_bloginfo('url');
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// login_headerurl()



	/**
	 * @internal
	 */
	public function login_headertext( $out )
	{

		if ( $title = $this->get('login_logo_title') )
		{
			$out = $title;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// login_headertext()



	/**
	 * @internal
	 */
	public function login_message( $message )
	{

		if ( empty( $_REQUEST['action'] ) && empty( $_GET ) )
		{
			if ( $login_message = $this->get('login_message') )
			{
				$message .= sprintf( '<div class="message">%s</div>', $login_message );
			}
		}

		// -------------------------------------------------------------------------

		return $message;

	}
	// login_message()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * theme_colors - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	private function theme_colors( $color_name = '' )
	{

		$tailwindcss_colors = $this->get_tailwindcss_colors(['neutral', 'blue']);

		// -------------------------------------------------------------------------

		$theme_colors = [

			'primary' => [
				'color'				=> $tailwindcss_colors['neutral'],
				'label'				=> $this->__('Primary Color'),
				'description'	=> $this->__(
					'Used for main text, headings, and UI elements throughout the site. ' .
					'Choose a neutral shade for best readability and consistency.'
				),
				'palette' => [
					'slate',
					'gray',
					'zinc',
					'neutral',
					'stone',
				],
			],

			// -----------------------------------------------------------------------

			'secondary' => [
				'color'				=> $tailwindcss_colors['blue'],
				'label'				=> $this->__('Secondary Color'),
				'description'	=> $this->__(
					'Used for links, buttons, accents, and interactive elements. ' .
					'Choose a vibrant color that complements the primary color and helps highlight important actions.'
				),
				'palette' => [
					'neutral',
					'red',
					'amber',
					'green',
					'cyan',
					'blue',
					'purple',
					'pink',
				],
			],

			// -----------------------------------------------------------------------

			// @consider

			/*
			'accent' => [
				'color'				=> $tailwindcss_colors['amber'],
				'label'				=> $this->__('Accent Color'),
				'description'	=> $this->__(
					'Used sparingly for highlighting special elements or calls to action. ' .
					'Choose a color that stands out from both primary and secondary colors.'
				),
				'palette' => [
					'amber',
					'yellow',
					'orange',
					'rose',
					'fuchsia',
					'violet',
				],
			],
			*/

		];

		// -------------------------------------------------------------------------

		return $color_name ? $theme_colors[ $color_name ] : $theme_colors;

	}
	// theme_colors()



	/**
	 * @internal
	 */
	private function get_tailwindcss_colors( $include = [] )
	{

		$colors = [

			// mainly to be used as primary color

			'slate'		=> '#64748b',
			'gray'		=> '#6b7280',
			'zinc'		=> '#71717a',
			'neutral'	=> '#737373',
			'stone'		=> '#78716c',

			// -----------------------------------------------------------------------

			// mainly to be used as secondary color

			'red'			=> '#ef4444',
			'orange'	=> '#f97316',
			'amber'		=> '#f59e0b',
			'yellow'	=> '#eab308',
			'lime'		=> '#84cc16',
			'green'		=> '#22c55e',
			'emerald'	=> '#10b981',
			'teal'		=> '#14b8a6',
			'cyan'		=> '#06b6d4',
			'sky'			=> '#0ea5e9',
			'blue'		=> '#3b82f6',
			'indigo'	=> '#6366f1',
			'violet'	=> '#8b5cf6',
			'purple'	=> '#a855f7',
			'fuchsia'	=> '#d946ef',
			'pink'		=> '#ec4899',
			'rose'		=> '#f43f5e',

		];

		// -------------------------------------------------------------------------

		if ( ! empty( $include ) )
		{
			$filtered_colors = [];

			foreach ( $include as $color_name )
			{
				if ( isset( $colors[ $color_name ] ) )
				{
					$filtered_colors[ $color_name ] = $colors[ $color_name ];
				}
			}

			$colors = $filtered_colors;
		}

		// -------------------------------------------------------------------------

		return $colors;

	}
	// get_tailwindcss_colors()



	/**
	 * @internal
	 */
	private function theme_colors_apply()
	{

		if ( ! wp_doing_ajax() )
		{
			add_action('wp_head', [ $this, 'theme_colors_css' ]);
		}

		// -------------------------------------------------------------------------

		// Clear cache when theme options are updated

		if ( is_customize_preview() )
		{
			if ( ! wp_doing_ajax() )
			{
				add_action(
					'customize_controls_print_scripts',
					[ $this, 'theme_colors_customize_color_picker' ]
				);
			}

			// -----------------------------------------------------------------------

			add_action(
				"update_option_{$this->settings_name}",
				[ $this, 'theme_colors_update_option' ],
				10,
				2
			);
		}

	}
	// theme_colors_apply()



	/**
	 * @internal
	 */
	public function theme_colors_update_option( $old_value, $value )
	{

		$colors_names = array_keys( $this->theme_colors() );

		if ( empty( $colors_names ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $colors_names as $color_name )
		{
			if ( 		! isset( $old_value["{$color_name}_color"])
					 || ! isset( $value["{$color_name}_color"] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( $old_value["{$color_name}_color"] !== $value["{$color_name}_color"] )
			{
				delete_option("{$this->settings_name}_theme_colors_css_{$color_name}");
			}
		}

	}
	// theme_colors_update_option()



	/**
	 * @internal
	 */
	public function theme_colors_css()
	{

		$colors_names = array_keys( $this->theme_colors() );

		if ( empty( $colors_names ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$css = [];

		foreach ( $colors_names as $color_name )
		{
			if ( is_customize_preview() )
			{
				// In customizer, always generate fresh CSS for live preview

				$color_css = $this->theme_colors_generate_color_css( $color_name );
			}
			else
			{
				// For regular pages, try to get cached CSS

				$option_name = "{$this->settings_name}_theme_colors_css_{$color_name}";

				if ( ! $color_css = get_option( $option_name ) )
				{
					if ( $color_css = $this->theme_colors_generate_color_css( $color_name ) )
					{
						update_option( $option_name, $color_css );
					}
				}
			}

			// -----------------------------------------------------------------------

			if ( $color_css )
			{
				$css[] = $color_css;
			}
		}

		// -------------------------------------------------------------------------

		printf(
			'<style id="%s">%s</style>',
			"{$this->core->prefix}theme_colors",
			implode( $css )
		);

	}
	// theme_colors_css()



	/**
	 * @internal
	 */
	private function theme_colors_generate_color_css( $color_name )
	{

		$default_color = F::array_get( $this->theme_colors( $color_name ), ['color'] );

		if ( ! $color = $this->get( "{$color_name}_color", $default_color ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$shades = Color_Scale_Generator::generate_color_scale( $color );

		if ( empty( $shades ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$color_variables = [];

		foreach ( $shades as $shade => $hex )
		{
			$rgb								= sscanf( $hex, "#%02x%02x%02x" );
			$color_variables[]	= sprintf(
				'--color-%s-%s: %d %d %d;',
				$color_name,
				$shade,
				$rgb[0],
				$rgb[1],
				$rgb[2]
			);
		}

		// -------------------------------------------------------------------------

		return sprintf( ':root{%s}', implode( $color_variables ) );

	}
	// theme_colors_generate_color_css()



	/**
	 * @internal
	 */
	public function theme_colors_customize_color_picker()
	{

		$theme_colors = $this->theme_colors();

		if ( empty( $theme_colors ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$color_palettes = [];

		foreach ( $theme_colors as $color_name => $config )
		{
			if ( 		empty( $config['palette'] )
					 || ! $color_palette = $this->get_tailwindcss_colors( $config['palette'] ) )
			{
				continue;
			}

			$color_palettes[ $color_name ] = array_values( $color_palette );
		}

		if ( empty( $color_palettes ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		ob_start();

		?>
		<script>
			jQuery(document).ready( function( $ ) {

				const color_palettes				= <?php echo json_encode( $color_palettes ); ?>;
				const original_color_picker	= $.fn.wpColorPicker;

				// ---------------------------------------------------------------------

				// Override the wpColorPicker for our specific control

				$.fn.wpColorPicker = function( options ) {

					<?php foreach ( $theme_colors as $color_name => $config ): ?>

						if ( $(this).closest('[id*="<?php echo $color_name; ?>_color"]').length )
						{
							if ( typeof options === 'string')
							{
								return original_color_picker.apply( this, arguments );
							}

							// ---------------------------------------------------------------

							// If options is an object or undefined, it's initialization

							options = $.extend( {}, options || {}, {
								palettes: color_palettes['<?php echo $color_name; ?>'],
							});
						}

					<?php endforeach; ?>

					// -------------------------------------------------------------------

					return original_color_picker.call( this, options );

				};

				// ---------------------------------------------------------------------

				// Initialize color pickers

				<?php foreach ( $theme_colors as $color_name => $config ): ?>

					wp.customize.control('<?php echo $color_name; ?>_color', function( control ) {

						control.container.find('.color-picker-hex').wpColorPicker();

					});

				<?php endforeach; ?>

			});
		</script>
		<?php

		// -------------------------------------------------------------------------

		echo F::minify( ob_get_clean() );

	}
	// theme_colors_customize_color_picker()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * theme_colors - END
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
	public function get( $setting = '', $default = false )
	{

		return $this->get_theme_option( $setting, $default );

	}
	// get()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * GETTERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CUSTOMIZE - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function customize_register( $wp_customize )
	{

		// store customize object to class property as we'll be using it later

		$this->wp_customize = $wp_customize;

		// -------------------------------------------------------------------------

		// manage sections display

		$this->remove_sections();

		$this->add_panels();

		$this->add_sections();

		$this->populate_sections();

	}
	// customize_register()



	/**
	 * @internal
	 */
	private function remove_sections()
	{

		$this->wp_customize->remove_section('themes');

		// -------------------------------------------------------------------------

		$this->wp_customize->remove_section('static_front_page');

		// -------------------------------------------------------------------------

		// add to colors sections

		$this->wp_customize->remove_section('custom_css');

		$this->wp_customize->get_control('custom_css')->section			= 'colors_section';
		$this->wp_customize->get_control('custom_css')->priority		= 1000;
		$this->wp_customize->get_control('custom_css')->label				= $this->__('Custom CSS');
		$this->wp_customize->get_control('custom_css')->description	= $this->__('Add custom CSS styles to customize your site\'s appearance. These styles will override any existing theme styles.');

	}
	// remove_sections()



	/**
	 * @internal
	 */
	private function add_panels()
	{

		$panels = $this->apply_filters( __FUNCTION__, $this->Panels()->get() );

		if ( empty( $panels ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$count = 1;

		foreach ( $panels as $panel )
		{
			if ( empty( $panel['id'] ) || empty( $panel['sections'] ) )
			{
				continue;
			}

			//F::print_r( $panel, '$panel' );

			// -----------------------------------------------------------------------

			$args = [
				'priority'				=> $panel['priority']		?? $count + 160,
				'capability'			=> $panel['capability']	?? $this->capability,
				'title'						=> $panel['title']			?? F::humanize( $panel['slug'] ),
				'description'			=> $panel['description']?? null,
				/*
				'theme_supports'	=> '',
				'type'						=> '',
				'active_callback'	=> '',
				*/
			];

			// -----------------------------------------------------------------------

			$this->wp_customize->add_panel( $panel['id'], $args );

			$count++;

			// -----------------------------------------------------------------------

			foreach ( $panel['sections'] as $index => $section )
			{
				if ( empty( $section['id'] ) || empty( $section['fields'] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				$section_args = [
					'priority'				=> $section['priority']		?? $index + 160,
					'panel'						=> $panel['id'],
					'capability'			=> $section['capability']	?? $this->capability,
					'title'						=> $section['title']			?? F::humanize( $section['id'] ),
					'description'			=> $section['description']?? null,
					/*
					'theme_supports'		=> '',
					'type'							=> '',
					'active_callback'		=> '',
					'description_hidden'=> false, // Hide the description behind a help icon, instead of inline above the first control
					*/
				];

				// ---------------------------------------------------------------------

				$added_section = $this->wp_customize->add_section( $section['id'], $section_args );

				// ---------------------------------------------------------------------

				if ( ! empty( $section['fields'] ) )
				{
					foreach( $section['fields'] as $field )
					{
						$this->register_field( $section, $field );
					}
				}
			}
		}

	}
	// add_panels()



	/**
	 * @internal
	 */
	private function add_sections()
	{

		if ( ! $sections = $this->Sections()->get() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$counter = 0;

		foreach ( $sections as $index => $section )
		{
			if ( empty( $section['slug'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$args = [
				'capability'	=> $section['capability']	?? $this->capability,
				'title'				=> $section['title']			?? F::humanize( $section['slug'] ),
				'priority'		=> $section['priority']		?? $counter + 100,
				'description'	=> $section['description']?? null,
			];

			// -----------------------------------------------------------------------

			$this->wp_customize->add_section( $section['slug'], $args );

			// -----------------------------------------------------------------------

			$counter++;
		}

	}
	// add_sections()



	/**
	 * @internal
	 */
	private function populate_sections()
	{

		if ( ! $sections = $this->Sections()->get() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach( $sections as $index => $section )
		{
			if ( ! empty( $section['callback'] ) )
			{
				//call_user_func( [ $this, $section['callback'] ] );
				$this->{$section['callback']}( $section );
			}

			// -----------------------------------------------------------------------

			if ( ! empty( $section['fields'] ) )
			{
				foreach( $section['fields'] as $field )
				{
					$this->register_field( $section, $field );
				}
			}
		}

	}
	// populate_sections()



	/**
	 * @internal
	 */
	private function register_field( $section, $field )
	{

		if ( 		empty( $field['name'] )
				 || empty( $field['type'] )
				 || empty( $section['slug'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$field_settings_name = "{$this->settings_name}[{$field['name']}]";

		// -------------------------------------------------------------------------

		$setting_args = [
			'capability'	=> $section['capability']	?? $this->capability,
			'default'			=> $field['default']			?? null,
			'type'				=> 'option',
		];

		if ( ! empty( $field['setting_args'] ) )
		{
			$setting_args = array_merge( $setting_args, $field['setting_args'] );
		}

		// -------------------------------------------------------------------------

		$this->wp_customize->add_setting( $field_settings_name, $setting_args );

		// -------------------------------------------------------------------------

		$control_args = [
			'label'				=> $field['label']				?? F::humanize( $field['name'] ),
			'description'	=> $field['description']	?? null,
			'choices'			=> $field['choices']			?? null,
			'input_attrs'	=> $field['input_attrs']	?? null,
			'settings'		=> $field_settings_name,
			'section'			=> $section['slug'],
		];

		// -------------------------------------------------------------------------

		switch ( $field['type'] )
		{
			case 'color':

				$cls = new \WP_Customize_Color_Control(
					$this->wp_customize,
					$field['name'],
					$control_args
				);

				$this->wp_customize->add_control( $cls );

				break;

			// -----------------------------------------------------------------------

			case 'image':

				$cls = new \WP_Customize_Image_Control(
					$this->wp_customize,
					$field['name'],
					$control_args
				);

				$this->wp_customize->add_control( $cls );

				break;

			// -----------------------------------------------------------------------

			default:

				$control_args['type'] = $field['type'];

				$this->wp_customize->add_control( $field['name'], $control_args );

				break;
		}

	}
	// register_field()



	/**
	 * @internal
	 */
	private function find_default_by_name( $name )
	{

		if ( ! $sections = $this->Sections()->get() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach( $sections as $section )
		{
			if ( empty( $section['fields'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			foreach( $section['fields'] as $field )
			{
				if ( 		$field['name'] === $name
						 && ! empty( $field['default'] )
						 && ! is_bool( $field['default'] ) )
				{
					return $field['default'];
				}
			}
		}

		// -------------------------------------------------------------------------

		return false;

	}
	// find_default_by_name()



	/**
	 * @internal
	 */
	public function get_theme_option( $name, $default = null )
	{

		if ( is_customize_preview() )
		{
			$this->options = get_option( $this->settings_name );
		}
		else
		{
			if ( ! isset( $this->options ) )
			{
				$this->options = get_option( $this->settings_name );
			}
		}

		// -------------------------------------------------------------------------

		// return default if it exists

		if ( 		empty( $this->options[ $name ] )
				 && ! is_null( $default ) )
		{
			return $default;
		}

		// -------------------------------------------------------------------------

		// return the option if it exists

		if ( isset( $this->options[ $name ] ) )
		{
			return $this->options[ $name ];
		}

		// -------------------------------------------------------------------------

		// return field default if it exists

		return $this->find_default_by_name( $name );

	}
	// get_theme_option()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CUSTOMIZE - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function Panels()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = new Panels( $this->core );

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// Panels()



	/**
	 * @internal
	 */
	public function Sections()
	{

		if ( ! empty( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = new Sections( $this->core );

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// Sections()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * RELATED CLASSES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Customize
