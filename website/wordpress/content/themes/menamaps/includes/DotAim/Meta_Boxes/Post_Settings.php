<?php

namespace DotAim\Meta_Boxes;

use DotAim\Base\RW_Meta_Box;
use DotAim\F;

final class Post_Settings extends RW_Meta_Box
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function settings()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$fields = [

			'title_disable' => [
				'type'	=> 'checkbox',
				'name'	=> $this->__('Disable title'),
				'desc'	=> $this->__('If checked, the title will not be displayed'),
				//'std'		=> '',
			],

			// -----------------------------------------------------------------------

			'subtitle' => [
				'type'	=> 'text',
				'name'	=> $this->__('Subtitle'),
			],

			// -----------------------------------------------------------------------

			'custom_css' => [
				'type'	=> 'textarea',
				'name'	=> $this->__('Custom CSS'),
				'desc'	=> sprintf(
					$this->__('Without <code>%s</code> tag. Shortcodes allowed.'),
					esc_html('<style>'),
				),
			],

			// -----------------------------------------------------------------------

			// @consider

			/*
			'display_featured_image' => [
				'type'		=> 'select',
				'name'		=> $this->__('Display Featured Image'),
				'desc'		=> $this->__('This will set the featured image (post thumbnail) as background in selected location'),
				'options'	=> [
					''			=> $this->__('No'),
					'as_section_background'	=> $this->__('As Page Section Background'),
					'as_header_background'	=> $this->__('As Page Header Background'),
					'as_page_header'				=> $this->__('As Page Header'),
				],
			],

			// -----------------------------------------------------------------------

			'section_background_image_opacity' => [
				'type'	=> 'number',
				'min'		=> 0,
				'max'		=> 1,
				'step'	=> 0.1,
				'name'	=> $this->__('Section Background Image Opacity'),
				'desc'	=> $this->__('Applcable when featured image is dispalyed as Page Section Background'),
			],
			*/

		];

		// -------------------------------------------------------------------------

		$fields = array_merge( $fields, $this->meta_tags_fields() );

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [

			'title'	=> $this->__('Settings'),

			// -----------------------------------------------------------------------

			'post_types' => [
				'post',
				'page',
			],

			// -----------------------------------------------------------------------

			'fields' => $fields,

		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// settings()



	/**
	 * @since 1.0.0
	 */
	private function meta_tags_fields()
	{

		$fields = [

			'meta_tags_heading' => [
				'type'	=> 'heading',
				'name'	=> $this->__('Meta Tags'),
				'desc'	=> $this->__('Settings related to meta tags'),
			],

			// -----------------------------------------------------------------------

			'meta_title' => [
				'type'	=> 'text',
				'name'	=> $this->__('Meta Title'),
				'desc'	=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code> and <code>%s</code>. Defaults to post title.'),
					esc_html('<meta property="og:title">'),
					esc_html('<meta name="twitter:title">'),
				),
				// @todo: add when <title> tag is supported
				/*
				'desc'	=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code>, <code>%s</code>, and <code>%s</code>. Defaults to post title.'),
					esc_html('<title>'),
					esc_html('<meta property="og:title">'),
					esc_html('<meta name="twitter:title">'),
				),
				*/
			],

			// -----------------------------------------------------------------------

			'meta_description' => [
				'type'	=> 'textarea',
				'name'	=> $this->__('Meta Description'),
				'desc'	=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code>, <code>%s</code>, and <code>%s</code>. Defaults to trimmed post excerpt.'),
					esc_html('<meta name="description">'),
					esc_html('<meta property="og:description">'),
					esc_html('<meta name="twitter:description">'),
				),
			],

			// -----------------------------------------------------------------------

			'meta_image_url' => [
				'type'	=> 'text',
				'name'	=> $this->__('Meta Image URL'),
				'desc'	=> sprintf(
					$this->__('Used in meta tags such as <code>%s</code> or <code>%s</code>. Defaults to featured image if empty, and if featured image is not set, it will auto generate one based on settings.  You can generate one using the button below.'),
					esc_html('<meta property="og:image">'),
					esc_html('<meta name="twitter:image">'),
				),
			],

		];

		// -------------------------------------------------------------------------

		// only add if Imagick exists

		if ( 		extension_loaded('imagick')
				 && class_exists( 'Imagick', false )
				 && class_exists( 'ImagickDraw', false ) )
		{
			$fields['meta_image_generate'] = [
				'type'	=> 'button',
				'name'	=> $this->__('Meta Image Manually Generate'),
				'std'		=> sprintf( '<span class="button_with_loader_text">%s</span>', $this->__('Generate Meta Image') ),
				'desc'	=> sprintf(
					$this->__('This will generate the image used in meta tags such as <code>%s</code> or <code>%s</code>.  It\'s generated using post title and site logo and based on saved settings.'),
					esc_html('<meta property="og:image">'),
					esc_html('<meta name="twitter:image">'),
				),
				'attributes' => [
					'class'								=> "{$this->prefix()}action button_with_loader",
					'data-button_action'	=> 'meta_image_generate',
					'data-metabox_id'			=> $this->id(),
					'data-ajax_action'		=> $this->ajax_action,
					'data-ajax_nonce'			=> $this->ajax_nonce,
				],
			];

			$fields['meta_image_auto_generate_disable'] = [
				'type'	=> 'checkbox',
				'name'	=> $this->__('Meta Image Auto Generate Disable'),
				'desc'	=> $this->__('This would disable auto generating a meta image if one is not set'),
			];
		}

		// -------------------------------------------------------------------------

		$fields['additional_meta_tags'] = [
			'type'	=> 'key_value',
			'name'	=> $this->__('Additional Meta Tags'),
			'desc'				=> $this->__('Add any additional custom meta tags as needed'),
			'placeholder'	=> [
				'key'		=> $this->__('name attribute'),
				'value'	=> $this->__('content attribute'),
			],
		];

		// -------------------------------------------------------------------------

		return $fields;

	}
	// meta_tags_fields()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	protected function hooks()
	{

		parent::hooks();

		// -------------------------------------------------------------------------

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

	}
	// hooks()()



	/**
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook )
	{

		if ( 'post.php' !== $hook )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$post_types = $this->setting('post_types');

		if ( 		empty( $post_types )
				 || ! in_array( get_post_type(), $post_types ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$url_assets	= $this->url_assets();

		// -------------------------------------------------------------------------

		$filename		= 'style.css';
		$url				= path_join( $url_assets, $filename );
		$handle			= "{$this->prefix()}{$filename}";
		$deps				= [];

		wp_enqueue_style( $handle, $url, $deps, $this->core->version );

		// -------------------------------------------------------------------------

		$filename		= 'edit_screen_actions.js';
		$url				= path_join( $url_assets, $filename );
		$handle			= "{$this->prefix()}{$filename}";
		$deps				= ['jquery'];
		$in_footer	= true;

		wp_enqueue_script( $handle, $url, $deps, $this->core->version, $in_footer );

	}
	// admin_enqueue_scripts()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AJAX RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function ajax_required_data()
	{

		$out = parent::{__FUNCTION__}();

		// -------------------------------------------------------------------------

		$out[] = 'button_action';

		// -------------------------------------------------------------------------

		return $out;

	}
	// ajax_required_data()



	/**
	 * @internal
	 */
	public function ajax_process()
	{

		if ( ! $data = $this->ajax_common_checks( __FUNCTION__ ) )
		{
			wp_send_json_error(['msg_title' => $this->__('No data')]);
		}

		if ( is_wp_error( $data ) )
		{
			wp_send_json_error(['msg_title' => $data->get_error_message()]);
		}

		// -------------------------------------------------------------------------

		$button_action = $_POST['button_action'];

		$fn = "ajax_process_{$button_action}";

		if ( ! method_exists( $this, $fn ) )
		{
			wp_send_json_error([
				'msg_title' => sprintf( $this->__('Action "%s" is not supported'), $button_action )
			]);
		}

		// -------------------------------------------------------------------------

		$this->{$fn}();

	}
	// ajax_process()



	/**
	 * @internal
	 */
	private function ajax_process_meta_image_generate()
	{

		// @consider checking if post already has a featured image

		// -------------------------------------------------------------------------

		if ( ! $meta_image_url = $this->core->Post()->generate_meta_image( $_POST['post_id'] ) )
		{
			wp_send_json_error( $this->__('Unable to create image') );
		}

		if ( is_wp_error( $meta_image_url ) )
		{
			wp_send_json_error( $meta_image_url->get_error_message() );
		}

		// -------------------------------------------------------------------------

		wp_send_json_success( $meta_image_url );

	}
	// ajax_process_meta_image_generate()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AJAX RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Post_Settings
