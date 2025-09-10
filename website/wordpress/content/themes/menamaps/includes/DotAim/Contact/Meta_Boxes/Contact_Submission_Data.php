<?php

namespace DotAim\Contact\Meta_Boxes;

use DotAim\F;

final class Contact_Submission_Data extends \DotAim\Base\RW_Meta_Box
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function settings()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$fields = [

			'submitted_data_heading' => [
				'type' => 'heading',
				'name' => $this->__('Submitted Data'),
			],

			// -----------------------------------------------------------------------

			'date' => [
				'name' => $this->__('Date Submitted'),
				'desc' => $this->__('Date and time the form was submitted'),
			],

			// -----------------------------------------------------------------------

			'submitter_name' => [
				'name' => $this->__('Submitter Name'),
				'desc' => $this->__('Name of the user who submitted the form'),
			],

			// -----------------------------------------------------------------------

			'submitter_email' => [
				'name' => $this->__('Submitter Email'),
				'desc' => $this->__('Email of the user who submitted the form'),
			],

			// -----------------------------------------------------------------------

			'category' => [
				'name' => $this->__('Category'),
				'desc' => $this->__('The selected category/inquiry type by user'),
			],

			// -----------------------------------------------------------------------

			'subject' => [
				'name' => $this->__('Subject'),
				'desc' => $this->__('Subject submitted by user'),
			],

			// -----------------------------------------------------------------------

			'message' => [
				'name' => $this->__('Message'),
				'desc' => $this->__('Message submitted by user'),
			],

			// -----------------------------------------------------------------------

			'technical_details_heading' => [
				'type' => 'heading',
				'name' => $this->core->__('Technical Details'),
			],

			// -----------------------------------------------------------------------

			'ip' => [
				'name' => $this->__('IP'),
				'desc' => $this->__('The IP address from where the form was submitted. This was added using a best practice approach to determine the IP address, but isn\'t full proof.  That\'s why it must be compared with the following <code>$_SERVER[\'REMOTE_ADDR\']</code>'),
			],

			// -----------------------------------------------------------------------

			'server_remote_addr' => [
				'name' => $this->__('<code>$_SERVER[\'REMOTE_ADDR\']</code>'),
				'desc' => $this->__('This is the raw <code>$_SERVER[\'REMOTE_ADDR\']</code> as detected when the form was submitted.'),
			],

			// -----------------------------------------------------------------------

			'server_http_user_agent' => [
				'name' => $this->__('<code>$_SERVER[\'HTTP_USER_AGENT\']</code>'),
				'desc' => $this->__('This is the raw <code>$_SERVER[\'HTTP_USER_AGENT\']</code> as detected when the form was submitted.'),
			],

			// -----------------------------------------------------------------------

			'server_http_referer' => [
				'name' => $this->__('<code>$_SERVER[\'HTTP_REFERER\']</code>'),
				'desc' => $this->__('This is the raw <code>$_SERVER[\'HTTP_REFERER\']</code> as detected when the form was submitted.'),
			],

			// -----------------------------------------------------------------------

			/*
			'server_request_uri' => [
				'name' => $this->__('<code>$_SERVER[\'REQUEST_URI\']</code>'),
				'desc' => $this->__('This is the raw <code>$_SERVER[\'REQUEST_URI\']</code> as detected when the form was submitted.'),
			],
			*/

		];

		// -------------------------------------------------------------------------

		foreach ( $fields as $id => &$field )
		{
			if ( empty( $field['type'] ) )
			{
				$field['type']				= 'custom_html';
				$field['save_field']	= false;
			}

			// -----------------------------------------------------------------------

			if ( $field['type'] === 'custom_html' && empty( $field['callback'] ) )
			{
				$field['callback'] = [$this, "custom_html_field_markup_{$id}"];
			}
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [
			'title'				=> $this->__('Contact Submission Data'),
			'post_types'	=> ['contact_submission'],
			'fields'			=> $fields,
			'context'			=> 'after_title',
		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// settings()



	/**
	 * @since 1.0.0
	 */
	public function prefix()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = "{$this->core->meta_box_prefix}contact_submission_";

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// prefix()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CUSTOM HTML - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function custom_html_field_markup_date()
	{

		$date_time	= get_the_date('l, dS M Y \a\t H:m:s');
		$date_c			= get_the_date('c');

		return $this->custom_html_field_markup( __FUNCTION__, "{$date_time} ($date_c)" );

	}
	// custom_html_field_markup_date()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_submitter_name()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_submitter_name()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_submitter_email()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_submitter_email()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_subject()
	{

		return $this->custom_html_field_markup( __FUNCTION__, get_the_title() );

	}
	// custom_html_field_markup_subject()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_category()
	{

		global $post;

		$terms = get_the_terms( $post->ID, 'contact_submission_category' );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) )
		{
			$terms = join( ', ', wp_list_pluck( $terms, 'name' ) );
		}
		else
		{
			$terms = null;
		}

		// -------------------------------------------------------------------------

		return $this->custom_html_field_markup( __FUNCTION__, $terms );

	}
	// custom_html_field_markup_category()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_message()
	{

		return $this->custom_html_field_markup( __FUNCTION__, get_the_content() );

	}
	// custom_html_field_markup_message()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_ip()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_ip()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_server_remote_addr()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_server_remote_addr()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_server_http_user_agent()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_server_http_user_agent()



	/**
	 * @internal
	 */
	public function custom_html_field_markup_server_http_referer()
	{

		return $this->custom_html_field_markup( __FUNCTION__ );

	}
	// custom_html_field_markup_server_http_referer()



	/**
	 * @internal
	 */
	protected function custom_html_field_markup( $fn, $value = null )
	{

		global $post;

		// -------------------------------------------------------------------------

		if ( is_null( $value ) )
		{
			$meta_key	= str_replace( __FUNCTION__ . '_', '', $fn );
			$value		= get_post_meta( $post->ID, "{$this->prefix()}{$meta_key}", true ) ?: '&nbsp;';
		}

		// -------------------------------------------------------------------------

		$attr = F::html_attributes([
			'style' => [
				'max-height'		=> '30vh',
				'scroll'				=> 'auto',
				'white-space'		=> 'pre-wrap',
				'word-wrap'			=> 'break-word',
				'line-height'		=> '2',
				'margin'				=> '0 1px',
				'padding'				=> '0 8px',
				'background'		=> '#f9f9f9',
				'border'				=> '1px solid #8c8f94',
				'border-radius'	=> '4px',
			]
		]);

		$out = "<pre{$attr}>{$value}</pre>";

		// -------------------------------------------------------------------------

		return $out;

	}
	// custom_html_field_markup()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CUSTOM HTML - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Contact_Submission_Data
