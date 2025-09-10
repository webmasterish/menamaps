<?php

namespace DotAim\Admin\Components\App_Settings;

use DotAim\F;
use DotAim\File;

/**
 * @internal
 */
class Misc extends \DotAim\Admin\Panel
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function config()
	{

		$sections = [

			['id' => 'meta_tags'	, 'icon' => 'dashicons-editor-code'],
			['id' => 'analytics'	, 'icon' => 'dashicons-analytics'],
			['id' => 'taxonomies'	, 'icon' => 'dashicons-category'],
			['id' => 'caching'		, 'icon' => 'dashicons-database'],
			['id' => 'logs'				, 'icon' => 'dashicons-book'],
			['id' => 'cron'				, 'icon' => 'dashicons-clock'],
			['id' => 'minify'			, 'icon' => 'dashicons-media-code'],

		];

		// -------------------------------------------------------------------------

		return [
			'title'		=> $this->__('Misc'),
			'icon'		=> 'dashicons-admin-generic',
			'content'	=> $this->populate_sections_args( $sections ),
		];

	}
	// config()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function apply()
	{

		$this->handle_submit();

		// -------------------------------------------------------------------------

		if ( empty( $this->settings() ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->analytics_apply();

		// -------------------------------------------------------------------------

		$this->taxonomies_apply();

		// -------------------------------------------------------------------------

		$this->caching_apply();

		// -------------------------------------------------------------------------

		$this->cron_apply();

		// -------------------------------------------------------------------------

		$this->minify_apply();

	}
	// apply()



	/**
	 * @internal
	 */
	public function handle_submit()
	{

		$this->logs_apply();

		// -------------------------------------------------------------------------

		if ( ! $action = F::array_get( $_REQUEST, ['action'] ) )
		{
			return;
		}

		if ( ! $nonce = F::array_get( $_REQUEST, ['_wpnonce'] ) )
		{
			return;
		}

		if ( ! wp_verify_nonce( $nonce, $this->id ) )
		{
			return;
		}

		if ( ! method_exists( $this, $action ) )
		{
			return;
		}

		if ( ! $result = $this->{$action}() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// @consider:
		// - display notice in relevant section
		// - ajax

		$notice = $this->component->notice_markup([
			'success'	=> ! empty( $result['success'] ),
			'error'		=> ! empty( $result['error'] ),
			'message'	=> "<p>{$result['message']}</p>",
		]);

		if ( ! empty( $notice ) )
		{
			add_action( 'admin_notices', function() use ( $notice ){ echo $notice; } );
		}

	}
	// handle_submit()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * meta_tags - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function meta_tags_content()
	{

		$out = array_merge(
			$this->meta_tags_front_page_fields(),
			$this->meta_tags_image_fields()
		);

		// -------------------------------------------------------------------------

		return $out;

	}
	// meta_tags_options()



	/**
	 * @internal
	 */
	private function meta_tags_front_page_fields()
	{

		$fields = [

			[
				'id' 				=> 'front_page_title',
				'label' 		=> $this->__('Front Page Meta Title'),
				'type' 			=> 'text',
				'attr' 			=> ['class'	=> ['large-text']],
				'sc_allowed'=> true,
				'default' 	=> '',
				'desc'			=> sprintf(
					// @todo: should include <title>
					$this->__('Used in meta tags such as <code>%s</code> and <code>%s</code>. Defaults to <code>%s</code>.'),
					esc_html('<meta property="og:title">'),
					esc_html('<meta name="twitter:title">'),
					'wp_get_document_title()'
				),
			],

			// -----------------------------------------------------------------------

			[
				'id' 				=> 'front_page_description',
				'label' 		=> $this->__('Front Page Meta Description'),
				'type' 			=> 'textarea',
				'attr' 			=> ['class'	=> ['large-text']],
				'sc_allowed'=> true,
				//'default' => $this->__(''),
				'desc'			=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code>, <code>%s</code>, and <code>%s</code>. e.g. <code>%s</code>'),
					esc_html('<meta name="description">'),
					esc_html('<meta property="og:description">'),
					esc_html('<meta name="twitter:description">'),
					'[site_tagline]'
				),
			],

			// -----------------------------------------------------------------------

			// @consider button to auto generate one with logo

			[
				'id' 				=> 'front_page_image_url',
				'label' 		=> $this->__('Front Page Meta Image URL'),
				'type' 			=> 'image',
				'attr' 			=> ['class' => ['large-text']],
				'sc_allowed'=> true,
				'default'		=> '[url path=images]/meta_image.png',
				'desc'			=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code> or <code>%s</code>.'),
					esc_html('<meta property="og:image">'),
					esc_html('<meta name="twitter:image">'),
				),
			],

		];

		// -------------------------------------------------------------------------

		return $fields;

	}
	// meta_tags_image_fields()



	/**
	 * @internal
	 */
	private function meta_tags_image_fields()
	{

		$fields = [

			[
				'id' 				=> 'image_default',
				'label' 		=> $this->__('Meta Image: Default'),
				'type' 			=> 'media',
				'attr' 			=> ['class' => ['large-text']],
				'sc_allowed'=> true,
				'default'		=> '[url path=images]/meta_image.png',
			],

		];

		// -------------------------------------------------------------------------

		$fields = array_merge(
			$fields,
			$this->meta_tags_image_auto_generate_fields()
		);

		// -------------------------------------------------------------------------

		return $fields;

	}
	// meta_tags_image_fields()



	/**
	 * @internal
	 */
	private function meta_tags_image_auto_generate_fields()
	{

		if ( 		! extension_loaded('imagick')
				 || ! class_exists( 'Imagick', false )
				 || ! class_exists( 'ImagickDraw', false ) )
		{
			return [];
		}

		// -------------------------------------------------------------------------

		$gravity_options = [
			''													=> $this->__('--- Select Gravity ---'),
			\Imagick::GRAVITY_NORTHWEST	=> $this->__('NorthWest'),
			\Imagick::GRAVITY_NORTH			=> $this->__('North'),
			\Imagick::GRAVITY_NORTHEAST	=> $this->__('NorthEast'),
			\Imagick::GRAVITY_WEST			=> $this->__('West'),
			\Imagick::GRAVITY_CENTER		=> $this->__('Centre'),
			\Imagick::GRAVITY_SOUTHWEST	=> $this->__('SouthWest'),
			\Imagick::GRAVITY_SOUTH			=> $this->__('South'),
			\Imagick::GRAVITY_SOUTHEAST	=> $this->__('SouthEast'),
			\Imagick::GRAVITY_EAST			=> $this->__('East'),
		];

		// -------------------------------------------------------------------------

		$fonts_options = ['' => $this->__('--- Select Font ---')];

		if ( $fonts = \Imagick::queryFonts() )
		{
			$fonts_options += array_combine( $fonts, $fonts );
		}

		// -------------------------------------------------------------------------

		$fields = [

			[
				'id' 						=> 'image_auto_generate_enable',
				'label' 				=> $this->__('Meta Image Auto Generate: Enable for the Following Post Types'),
				'type' 					=> 'select_pre_populated',
				'data'					=> 'post_types',
				'data_exclude'	=> ['attachment'],
				'multiple'			=> true,
				'checkboxes'		=> true,
				'default'				=> ['post'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 				=> 'image_auto_generate_logo',
				'label' 		=> $this->__('Meta Image Auto Generate: Logo'),
				'type' 			=> 'media',
				'attr' 			=> ['class' => ['large-text']],
				'sc_allowed'=> true,
				'default'		=> '[url path=images]/logo_for_meta_image.png',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_width',
				'label' 	=> $this->__('Meta Image Auto Generate: Width'),
				'type' 		=> 'number',
				'default'	=> 1200,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_height',
				'label' 	=> $this->__('Meta Image Auto Generate: Height'),
				'type' 		=> 'number',
				'default'	=> 630,
			],

			// -----------------------------------------------------------------------

			[
				'id' 		=> 'image_auto_generate_background_color',
				'label' => $this->__('Meta Image Auto Generate: Background Color'),
				'type' 	=> 'color',
				'desc'	=> sprintf(
					'e.g <code>%s</code>',
					F::get_random_hex_color('light')
				),
			],

			// -----------------------------------------------------------------------

			[
				'id'		=> 'image_auto_generate_background_gradient',
				'label'	=> $this->__('Meta Image Auto Generate: Background Gradient'),
				'type'	=> 'text',
				'desc'	=> sprintf(
					'e.g <code>%s-%s</code>',
					F::get_random_hex_color('light'),
					F::get_random_hex_color('light')
				),
				//'default' => '#fafafa-#cfcfcf',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_font',
				'label' 	=> $this->__('Meta Image Auto Generate: Font'),
				'type' 		=> 'select',
				'options'	=> $fonts_options,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_font_size',
				'label' 	=> $this->__('Meta Image Auto Generate: Font Size'),
				'type' 		=> 'number',
				'default'	=> 60,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_font_weight',
				'label' 	=> $this->__('Meta Image Auto Generate: Font Weight'),
				'type' 		=> 'number',
				'default'	=> 900,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_text_gravity',
				'label' 	=> $this->__('Meta Image Auto Generate: Text Gravity'),
				'type' 		=> 'select',
				'options'	=> $gravity_options,
				'default'	=> \Imagick::GRAVITY_CENTER,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_text_color',
				'label' 	=> $this->__('Meta Image Auto Generate: Text Color'),
				'type' 		=> 'color',
				'default'	=> '#000000',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_stroke_width',
				'label' 	=> $this->__('Meta Image Auto Generate: Stroke Width'),
				'type' 		=> 'number',
				'default'	=> 0,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_stroke_color',
				'label' 	=> $this->__('Meta Image Auto Generate: Stroke Color'),
				'type' 		=> 'color',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_color_for_dark_background',
				'label' 	=> $this->__('Meta Image Auto Generate: Color for Dark Background'),
				'type' 		=> 'color',
				'default'	=> '#ffffff',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_color_for_light_background',
				'label' 	=> $this->__('Meta Image Auto Generate: Color for Light Background'),
				'type' 		=> 'color',
				'default'	=> '#000000',
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'image_auto_generate_padding',
				'label' 	=> $this->__('Meta Image Auto Generate: Padding'),
				'type' 		=> 'number',
				'default'	=> 200,
			],

			// -----------------------------------------------------------------------

			[
				'id' 		=> 'image_auto_generate_set_post_thumbnail',
				'label' => $this->__('Meta Image Auto Generate: Set as Featured Image (post thumbnail)'),
				'desc' 	=> $this->__('Will set the auto generated Meta Image as post featured image (post thumbnail)'),
				'type' 	=> 'checkbox',
			],

		];

		// -------------------------------------------------------------------------

		return $fields;

	}
	// meta_tags_image_auto_generate_fields()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * meta_tags - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * analytics - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function analytics_content()
	{

		$out = [

			[
				'id' 				=> 'tracking_code',
				'label' 		=> $this->__('Tracking Code'),
				'type' 			=> 'textarea',
				'attr' 			=> [
					'class'	=> ['large-text'],
					'rows'	=> '5',
				],
				'sc_allowed'=> true,
				'default' 	=> '',
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'track_users',
				'label' 			=> $this->__('Track Logged In Users'),
				'desc' 				=> $this->__('Tracking will also work when a user having one of the selected roles is logged in.'),
				'type' 				=> 'select_pre_populated',
				'data'				=> 'users_roles',
				'multiple'		=> true,
				'checkboxes'	=> true,
				//'default'		=> ['subscriber'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'in_head',
				'label' 	=> $this->__('Place in Document Head'),
				'desc' 		=> $this->__('If selected, the tracking code will be placed in document head using <code>wp_head()</code>, otherwise, it will be placed using <code>wp_footer()</code>.'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'minify',
				'label' 	=> $this->__('Minify Code'),
				'desc' 		=> $this->__('Simple Minifcation of the tracking code by removing tabs and new lines.'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'force_in_dev',
				'label' 	=> $this->__('Force In Dev Mode'),
				'desc' 		=> $this->__('Forcing in dev mode will comment out the tracking code; helpful for debugging.'),
				'type' 		=> 'checkbox',
				'default'	=> false,
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// analytics_content()



	/**
	 * @internal
	 */
	private function analytics_apply()
	{

		if ( ! $this->settings(['analytics', 'tracking_code']) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_user_logged_in() )
		{
			$track_users = $this->settings(['analytics', 'track_users']);

			if ( empty( $track_users ) || ! is_array( $track_users ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$user = wp_get_current_user();

			if ( empty( $user->roles ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			if ( ! array_intersect( $track_users, (array) $user->roles ) )
			{
				return;
			}
		}

		// -------------------------------------------------------------------------

		$fn = [ $this, 'analytics_apply_code' ];

		if ( $this->settings(['analytics', 'in_head']) )
		{
			add_action( 'wp_head', $fn, 100 );
		}
		else
		{
			add_action( 'wp_footer', $fn );
		}

	}
	// analytics_apply()



	/**
	 * @internal
	 */
	public function analytics_apply_code()
	{

		if ( ! $code = $this->settings(['analytics', 'tracking_code']) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $code = trim( do_shortcode( $code ) ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// code commented out for debuging while in dev...

		if ( $this->settings(['analytics', 'force_in_dev']) )
		{
			$code = "<!--\n{$code}\n-->\n";
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['analytics', 'minify']) )
		{
			$code = F::minify( $code );
		}

		// -------------------------------------------------------------------------

		echo "\n{$code}\n";

	}
	// analytics_apply_code()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * analytics - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * taxonomies - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function taxonomies_content()
	{

		$out = [

			[
				'id' 			=> 'allow_html_in_term_description',
				'label' 	=> $this->__('Allow HTML in term description'),
				'type' 		=> 'checkbox',
				'default' => true,
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// taxonomies_content()



	/**
	 * @internal
	 */
	private function taxonomies_apply()
	{

		if ( $this->settings(['taxonomies', 'allow_html_in_term_description']) )
		{
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
			remove_filter( 'term_description', 'wp_kses_data' );
		}

	}
	// taxonomies_apply()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * taxonomies - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * caching - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function caching_content()
	{

		$out = [

			[
				'id' 		=> 'transients_active',
				'label' => $this->__('Use Transients'),
				'desc' 	=> sprintf(
					$this->__('Stores complex database queries in <a href="%s" target="_blank">transients</a>.'),
					'http://codex.wordpress.org/Transients_API'
				),
				'type' 		=> 'checkbox',
				'default' => true,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'transients_default_expiration',
				'label' 	=> $this->__('Transients Default Expiration Time'),
				'type' 		=> 'select',
				'options' => [
					'0'									=> $this->__('Never Expire'),
					'MINUTE_IN_SECONDS'	=> $this->__('1 Minute'),
					'HOUR_IN_SECONDS'		=> $this->__('1 Hour'),
					'DAY_IN_SECONDS'		=> $this->__('1 Day'),
					'WEEK_IN_SECONDS'		=> $this->__('1 Week'),
					'YEAR_IN_SECONDS'		=> $this->__('1 Year'),
					'custom'						=> $this->__('Custom'),
				],
				'conditional' => ['transients_active' => 'checked'],
				'default' 		=> 'YEAR_IN_SECONDS',
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'transients_default_expiration_custom',
				'label' 			=> $this->__('Transients Default Custom Expiration Time in Seconds'),
				'type' 				=> 'text',
				'conditional' => [
					'transients_active'							=> 'checked',
					'transients_default_expiration'	=> 'custom',
				],
			],

		];

		// -------------------------------------------------------------------------

		$out[] = [
			'id'			=> 'wp_super_cache_clean_cache_if_transients_expired',
			'label' 	=> $this->__('WP Super Cache Clean Cache if Transients Expired'),
			'desc' 		=> $this->__('This will check the set transients and if they are expired <code>wp_cache_clean_cache()</code> is used'),
			'type'		=> 'array_field',
			// @todo display as single
			'fields'	=> [
				[
					'id'		=> 'transient_name_prefix',
					'type'	=> 'text',
					'label'	=> $this->__('Transient Name Prefix'),
					'attr'	=> ['placeholder' => $this->__('Transient Name Prefix')],
				]
			],
		];

		// -------------------------------------------------------------------------

		// transients delete button

		$query_arg = [
			'page'	=> $this->component->menu_slug,
			'action'=> 'caching_transients_delete',
		];

		$btn_attr = F::html_attributes([
			'href'		=> wp_nonce_url( add_query_arg( $query_arg, admin_url('admin.php') ), $this->id ),
			'id'			=> $query_arg['action'],
			'class'		=> ['button'],
			'onclick'	=> sprintf(
				'return confirm("%s")',
				$this->__('This action is irreversible and will delete all related database entries.\r\n\nAre you sure you want to Delete All App Transients?')
			),
		]);

		$out[] = [
			'id' 			=> 'transients_delete',
			'label' 	=> $this->__('Delete All App Transients'),
			'type' 		=> 'html',
			'content'	=>
				'<p>' .
					"<a{$btn_attr}>" .
						$this->__('Delete App Transients') .
					'</a>' .
				'</p>' .
				'<p>' .
					'<span class="description">' .
						$this->__('This will delete all app transients by performing the following sql query:') .
					'</span>' .
					'<br>' .
					'<code>' . $this->caching_transients_delete_query() . '</code>' .
				'</p>',
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// caching_content()



	/**
	 * @internal
	 */
	private function caching_transients_delete_query()
	{

		global $wpdb;

		return sprintf(
			"
			DELETE
			FROM `{$wpdb->options}`
			WHERE `option_name`
			LIKE '%s'
			",
			"_transient%{$this->core->prefix}%"
		);

	}
	// caching_transients_delete_query()



	/**
	 * @internal
	 */
	private function caching_transients_delete()
	{

		global $wpdb;

		$res = $wpdb->query( $this->caching_transients_delete_query() );

		// -------------------------------------------------------------------------

		// @consider clearing super cache
		// not really needed as it can be done in super cache plugin

		// -------------------------------------------------------------------------

		$out = [];

		if ( false === $res )
		{
			$out['error']		= true;
			$out['message']	= $this->__('Something went wrong.');
		}
		else
		{
			$out['success'] = true;

			if ( $res > 0 )
			{
				$out['message'] = sprintf(
						$this->core->_n(
						'Found %s app transient and deleted it.',
						'Found %s app transients and deleted them.',
						$res
					),
					number_format_i18n( $res )
				);
			}
			else
			{
				$out['message'] = $this->__('No app transients found.');
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// caching_transients_delete()



	/**
	 * @internal
	 */
	private function caching_apply()
	{

		// if wp super cache is enabled
		// hook in init to check if transients expired
		// and then clear it's cache
		// transient timeout name would be something like
		// "_transient_timeout_{$transient_name_prefix}%"

		global $cache_enabled, $super_cache_enabled;

		if ( 		( $cache_enabled || $super_cache_enabled )
				 && function_exists('wpsc_delete_post_cache') )
		{
			$transients_to_check = $this->settings(['caching', 'wp_super_cache_clean_cache_if_transients_expired']);

			if ( ! empty( $transients_to_check ) )
			{
				// @todo:
				// should hook into wsc hook where it checks and loads cache if it exists

				add_action( 'init', [ $this, 'caching_apply_wp_super_cache_clean_cache_if_transients_expired' ] );
			}
		}

	}
	// caching_apply()



	/**
	 * @internal
	 */
	public function caching_apply_wp_super_cache_clean_cache_if_transients_expired()
	{

		$transients = $this->settings(['caching', 'wp_super_cache_clean_cache_if_transients_expired']);

		if ( empty( $transients ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$prefix	= '_transient_timeout_';
		$where	= [];

		foreach ( $transients as $transient )
		{
			if ( ! $transient['transient_name_prefix'] )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$name_prefix = $transient['transient_name_prefix'];

			if ( ! F::starts_with( $name_prefix, $prefix ) )
			{
				$name_prefix = "{$prefix}{$name_prefix}";
			}

			// -----------------------------------------------------------------------

			$where[] = "`option_name` LIKE '{$name_prefix}%'";
		}

		// -------------------------------------------------------------------------

		if ( empty( $where ) )
		{
			return;
		}

		$where = implode( ' OR ', $where );

		// -------------------------------------------------------------------------

		global $wpdb;

		$time_to_check = time() - MINUTE_IN_SECONDS;

		$query = "
			SELECT
			COUNT(case when option_value < {$time_to_check} then 1 end) as `expired`
			FROM `{$wpdb->options}`
			WHERE ( {$where} )
			";

		if ( ! $res = $wpdb->get_var( $query ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		global $file_prefix;

		wp_cache_clean_cache( $file_prefix, $all = true );

	}
	// caching_apply_wp_super_cache_clean_cache_if_transients_expired()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * caching - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * logs - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function logs_content()
	{

		$out = [

			[
				'id' 			=> 'debug_enable',
				'label' 	=> $this->__('Enable Debuging'),
				'desc'		=> sprintf(
					$this->__('Log relevant actions/errors to <br><code>%s</code>'),
					$this->core->dir_logs('debug.log')
				),
				'type' 		=> 'checkbox',
				'default' => $this->core->is_local_dev(),
			],

		];

		// -------------------------------------------------------------------------

		$buttons = array_filter([
			$this->logs_view_file_button('debug'),
			$this->logs_download_file_button('debug'),
			$this->logs_clear_file_button('debug'),
		]);

		if ( ! empty( $buttons ) )
		{
			$debug_log_content = sprintf(
				'<div%s>%s</div>%s',
				F::html_attributes([
					'id'    => 'debug_log_content',
					'style' => [
						'display'    => 'none',
						'background' => '#f8f8f8',
						'padding'    => '15px',
						'border'     => '1px solid #ddd',
						'overflow'   => 'auto',
						'max-height' => '500px',
						'font-family'=> 'monospace',
						'margin-top' => '15px',
						'white-space'=> 'pre-wrap',
					],
				]),
				'<div class="log_loading" style="text-align: center; display: none;"><span class="spinner is-active" style="float:none;"></span> ' . $this->__('Loading log content...') . '</div>' .
				'<div class="log_content"></div>' .
				'<div class="log_pagination" style="display:flex; flex-wrap:wrap; gap:5px; margin-top:10px;"></div>',
				$this->logs_view_file_js()
			);


			$out[] = [
				'id' 			=> 'debug_log_file_actions',
				'label' 	=> $this->__('Debug Log File Actions'),
				'type' 		=> 'html',
				'content'	=> '<p>' . implode( ' ', $buttons ) . '</p>',
				'content'	=> sprintf(
					'<div%s>%s</div>%s',
					F::html_attributes([
						'id'		=> 'debug_log_file_actions_buttins',
						'style'	=> [
							'display'		=> 'flex',
							'flex-wrap'	=> 'wrap',
							'gap'				=> '10px',
						],
					]),
					implode( ' ', $buttons ),
					$debug_log_content
				),
			];
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// logs_content()



	/**
	 * @internal
	 */
	private function logs_action_get_log_file()
	{

		if ( empty( $_GET['log_name'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $log_file = debug_log_file() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! is_writable( $log_file ) )
		{
			return new \WP_Error(
				__FUNCTION__ . '_not_writable',
				sprintf( $this->__('Not writable: %s'), $log_file )
			);
		}

		// -------------------------------------------------------------------------

		return $log_file;

	}
	// logs_action_get_log_file()



	/**
	 * @internal
	 */
	private function logs_view_file_button( $log_name )
	{

		if ( ! $log_file = debug_log_file() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$action			= 'logs_view_debug_log';
		$query_arg	= [
			'page'			=> $this->component->menu_slug,
			'action'		=> $action,
			'log_name'	=> $log_name,
		];
		$url = add_query_arg( $query_arg, admin_url('admin.php') );
		$url = wp_nonce_url( $url, $this->id );

		// -------------------------------------------------------------------------

		return sprintf(
			'<button%s>%s</button>',
			F::html_attributes([
				'id'						=> $action,
				'class'					=> ['button'],
				'data-action'		=> $action,
				'data-nonce'		=> wp_create_nonce( $action ),
				'data-log_name'	=> $log_name,
			]),
			sprintf( $this->__('View %s'), basename( $log_file ) )
		);

	}
	// logs_view_file_button()



	/**
	 * @internal
	 */
	private function logs_view_file_js()
	{

		static $alread_added = null;

		if ( isset( $alread_added ) )
		{
			return;
		}

		$alread_added = true;

		// -------------------------------------------------------------------------

		ob_start();

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

			$('#logs_view_debug_log').on('click', function(e) {

				e.preventDefault();

				// ---------------------------------------------------------------------

				const $button					= $(this);
				const $log_container	= $('#debug_log_content');
				const $log_content		= $log_container.find('.log_content');
				const $log_loading		= $log_container.find('.log_loading');
				const $log_pagination	= $log_container.find('.log_pagination');

				// ---------------------------------------------------------------------

				// Toggle visibility

				if ( $log_container.is(':visible') )
				{
					$log_container.slideUp('fast');

					return;
				}

				// ---------------------------------------------------------------------

				// Show container and loading spinner

				$log_container.slideDown('fast');
				$log_content.html('');
				$log_loading.show();
				$log_pagination.hide();

				// ---------------------------------------------------------------------

				const action		= $button.data('action');
				const nonce			= $button.data('nonce');
				const log_name	= $button.data('log_name');

				// ---------------------------------------------------------------------

				$.ajax({
					url			: ajaxurl,
					type		: 'POST',
					data		: { action, nonce, log_name, page: 1, },
					success	: function(response) {
						$log_loading.hide();

						if ( response.success )
						{
							$log_content.html(response.data.content);

							// Add pagination if needed
							if ( response.data.total_pages > 1 )
							{
								$log_pagination.show();
								$log_pagination.html('');

								for ( let i = 1; i <= Math.min( response.data.total_pages, 10 ); i++ )
								{
									const pageClass = (i === 1) ? 'button button-small button-primary' : 'button button-small';
									$('<a href="#" class="' + pageClass + '">' + i + '</a>')
										.data('page', i)
										.appendTo($log_pagination);
								}

								// Add "last page" link if there are many pages
								if ( response.data.total_pages > 10 )
								{
									$log_pagination.append(' ... ');
									$('<a href="#" class="button button-small">' + response.data.total_pages + '</a>')
										.data('page', response.data.total_pages)
										.appendTo($log_pagination);
								}

								// Handle pagination clicks
								$log_pagination.on('click', 'a', function(e) {
									e.preventDefault();

									const page = $(this).data('page');
									$log_pagination.find('a').removeClass('button-primary');
									$(this).addClass('button-primary');

									$log_content.html('');
									$log_loading.show();

									$.ajax({
										url			: ajaxurl,
										type		: 'POST',
										data		: { action, nonce, log_name, page },
										success	: function(response) {
											$log_loading.hide();
											if (response.success) {
												$log_content.html(response.data.content);
											} else {
												$log_content.html('<div class="error">' + response.data.message + '</div>');
											}
										}
									});
								});
							}
						}
						else
						{
							$log_content.html('<div class="error">' + response.data.message + '</div>');
						}
					},
					error: function() {
						$log_loading.hide();
						$log_content.html('<div class="error"><?php echo esc_js($this->__('Ajax request failed')); ?></div>');
					}
				});

			});

		});
		</script>
		<?php

		// -------------------------------------------------------------------------

		return F::minify( ob_get_clean() );

	}
	// logs_view_file_js()



	/**
	 * @internal
	 */
	private function logs_download_file_button( $log_name )
	{

		if ( ! $log_file = debug_log_file() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$action			= 'logs_download_file';
		$query_arg	= [
			'page'		=> $this->component->menu_slug,
			'action'	=> $action,
			'log_name'=> $log_name,
		];
		$url = add_query_arg( $query_arg, admin_url('admin.php') );
		$url = wp_nonce_url( $url, $this->id );

		// -------------------------------------------------------------------------

		return sprintf(
			'<a%s>%s</a>',
			F::html_attributes([
				'href'	=> $url,
				'id'		=> $action,
				'class'	=> ['button'],
			]),
			sprintf( $this->__('Download %s'), basename( $log_file ) )
		);

	}
	// logs_download_file_button()



	/**
	 * @internal
	 */
	private function logs_download_file()
	{

		$log_file = $this->logs_action_get_log_file();

		if ( ! $log_file )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_wp_error( $log_file ) )
		{
			return [
				'error'		=> true,
				'message'	=> $log_file->get_error_message(),
			];
		}

		// -------------------------------------------------------------------------

		$log_file_name = basename( $log_file );

		if ( ! ( $content = file_get_contents( $log_file ) ) )
		{
			return [
				'error'		=> true,
				'message'	=> sprintf(
					$this->__('%s has no content'),
					$log_file_name
				),
			];
		}

		// -------------------------------------------------------------------------

		File::export( $content, $log_file_name );

	}
	// logs_download_file()



	/**
	 * @internal
	 */
	private function logs_clear_file_button( $log_name )
	{

		if ( ! $log_file = debug_log_file() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$action			= 'logs_clear_file';
		$query_arg	= [
			'page'		=> $this->component->menu_slug,
			'action'	=> $action,
			'log_name'=> $log_name,
		];
		$url = add_query_arg( $query_arg, admin_url('admin.php') );
		$url = wp_nonce_url( $url, $this->id );

		// -------------------------------------------------------------------------

		$log_file_name	= basename( $log_file );
		$confirm_msg		= sprintf(
			$this->__('This action is irreversible and will empty %1$s from its contents.\n\nAre you sure you want to empty %1$s File?'),
			$log_file_name
		);

		// -------------------------------------------------------------------------

		return sprintf(
			'<a%s>%s</a>',
			F::html_attributes([
				'href'		=> $url,
				'id'			=> $action,
				'class'		=> ['button'],
				'onclick'	=> "if(confirm('{$confirm_msg}')){return true;}return false;",
			]),
			sprintf( $this->__('Clear %s'), $log_file_name )
		);

	}
	// logs_clear_file_button()



	/**
	 * @internal
	 */
	private function logs_clear_file()
	{

		$log_file = $this->logs_action_get_log_file();

		if ( ! $log_file )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = [];

		if ( is_wp_error( $log_file ) )
		{
			$out['error']		= true;
			$out['message']	= $log_file->get_error_message();
		}
		else
		{
			File::clear( $log_file );

			// -----------------------------------------------------------------------

			// @consider an option to delete the file
			//
			//@unlink( $log_file );

			// -----------------------------------------------------------------------

			$out['success']	= true;
			$out['message'] = sprintf(
				$this->__('%s Emptied from contents'),
				basename( $log_file )
			);
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// logs_clear_file()



	/**
	 * @internal
	 */
	private function logs_apply()
	{

		if ( is_admin() )
		{
			add_action(
				'wp_ajax_logs_view_debug_log',
				[ $this, 'logs_view_debug_log_ajax_handle' ]
			);
		}

	}
	// logs_apply()



	/**
	 * @internal
	 */
	public function logs_view_debug_log_ajax_handle()
	{

		if ( ! check_ajax_referer('logs_view_debug_log', 'nonce', false) )
		{
			wp_send_json_error(['message' => $this->__('Security check failed')]);
		}

		// -------------------------------------------------------------------------

		// Get log file

		$log_name = isset( $_POST['log_name'] ) ? sanitize_text_field( $_POST['log_name'] ) : 'debug';
		$log_file = debug_log_file();

		if ( ! $log_file || ! file_exists( $log_file ) )
		{
			wp_send_json_error(['message' => $this->__('Log file not found')]);
		}

		// -------------------------------------------------------------------------

		// Get file size, and read the appropriate chunk of the file

		$file_size			= filesize( $log_file );
		$lines_per_page	= 500;
		$current_page		= isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$content				= '';
		$handle					= fopen( $log_file, 'r' );

		if ( $handle )
		{
			$line_count				= 0;
			$displayed_lines	= 0;
			$start_line				= ( $current_page - 1 ) * $lines_per_page;

			while ( ! feof( $handle ) )
			{
				$line = fgets( $handle );

				if ( $line_count >= $start_line && $displayed_lines < $lines_per_page )
				{
					$content .= htmlspecialchars( $line );

					$displayed_lines++;
				}

				// ---------------------------------------------------------------------

				$line_count++;

				// ---------------------------------------------------------------------

				// If we've displayed enough lines, break

				if ( $displayed_lines >= $lines_per_page )
				{
					break;
				}
			}

			// -----------------------------------------------------------------------

			// Count total lines for pagination

			fseek( $handle, 0 );

			$total_lines = 0;

			while ( ! feof( $handle ) )
			{
				fgets( $handle );

				$total_lines++;
			}

			// -----------------------------------------------------------------------

			fclose( $handle );

			// -----------------------------------------------------------------------

			// Calculate total pages

			$total_pages = ceil( $total_lines / $lines_per_page );
		}
		else
		{
			wp_send_json_error(['message' => $this->__('Could not open log file')]);
		}

		// -------------------------------------------------------------------------

		// Add file info

		if ( $file_size > 1048576 ) // 1MB
		{
			$content = sprintf(
				'<div class="notice notice-warning" style="margin:0 0 10px 0;"><p>%s</p></div>',
				sprintf(
					$this->__('This log file is large (%s). Displaying %d lines per page.'),
					size_format($file_size), $lines_per_page
				)
			) . $content;
		}

		// -------------------------------------------------------------------------

		// If empty log

		if ( empty( trim( $content ) ) )
		{
			$content = '<em>' . $this->__('Log file is empty') . '</em>';
		}

		// -------------------------------------------------------------------------

		wp_send_json_success([
			'content'				=> $content,
			'total_pages'		=> $total_pages,
			'current_page'	=> $current_page,
		]);

	}
	// logs_view_debug_log_ajax_handle()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * logs - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * cron - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function cron_content()
	{

		$out = [
			[
				'id'			=> 'schedules',
				'label'		=> $this->__('Add Cron Schedules'),
				'type'		=> 'array_field',
				'fields'	=> [
					[
						'id'		=> 'key',
						'type'	=> 'text',
						'label'	=> $this->__('Schedule Key'),
					],
					[
						'id'		=> 'interval',
						'type'	=> 'number',
						'attr'	=> ['min' => 0],
						'label'	=> $this->__('Interval (Seconds)'),
					],
					[
						'id'		=> 'display',
						'type'	=> 'text',
						'label'	=> $this->__('Display Name'),
					],
				],
			],
		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// cron_content()



	/**
	 * @internal
	 */
	private function cron_apply()
	{

		$schedules = $this->settings(['cron', 'schedules']);

		if ( ! empty( $schedules ) )
		{
			add_filter( 'cron_schedules', [ $this, 'cron_apply_schedules_filter' ] );
		}

	}
	// cron_apply()



	/**
	 * @internal
	 */
	public function cron_apply_schedules_filter( $schedules )
	{

		$schedules_to_add = $this->settings(['cron', 'schedules']);

		if ( ! empty( $schedules_to_add ) )
		{
			foreach ( $schedules_to_add as $schedule )
			{
				if ( 		! empty( $schedule['key'] )
						 && ! empty( $schedule['interval'] )
						 && ! empty( $schedule['display'] )
						 && ! isset( $schedules[ $schedule['key'] ] ) )
				{
					$schedules[ $schedule['key'] ] = [
						'interval'	=> $schedule['interval'],
						'display'		=> $schedule['display'],
					];
				}
			}
		}

		// -------------------------------------------------------------------------

		return $schedules;

	}
	// cron_apply_schedules_filter()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * cron - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * minify - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function minify_content()
	{

		$out = [

			[
				'id' 			=> 'enable',
				'label' 	=> $this->__('Enable Rendered HTML'),
				'desc' 		=> $this->__('Will minify all rendered HTML.'),
				'type' 		=> 'checkbox',
				'default' => false,
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// minify_content()



	/**
	 * @internal
	 */
	private function minify_apply()
	{

		if ( $enable = $this->settings(['minify', 'enable']) )
		{
			add_action( 'get_header', [ $this, 'minify_apply_get_header_buffer_start' ] );
			add_action( 'wp_footer' , [ $this, 'minify_apply_wp_footer_buffer_end' ] );
		}

	}
	// minify_apply()



	/**
	 * @internal
	 */
	public function minify_html( $buffer )
	{

		$search = [
			'/\>[^\S ]+/s',     // Remove whitespaces after tags
			'/[^\S ]+\</s',     // Remove whitespaces before tags
			'/(\s)+/s',         // Shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		];

		$replace = [
			'>',
			'<',
			'\\1',
			''
		];

		// -------------------------------------------------------------------------

		return preg_replace( $search, $replace, $buffer );

	}
	// minify_html()



	/**
	 * @internal
	 */
	public function minify_apply_get_header_buffer_start()
	{

		ob_start([$this, 'minify_html']);

	}
	// minify_apply_get_header_buffer_start()



	/**
	 * @internal
	 */
	public function minify_apply_wp_footer_buffer_end()
	{

		ob_end_flush();

	}
	// minify_apply_wp_footer_buffer_end()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * minify - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Misc
