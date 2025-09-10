<?php

namespace DotAim\Contact;

final class Taxonomy_Contact_Submission_Category extends \DotAim\Base\Custom_Taxonomy
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

		$this->{__FUNCTION__} = [

			'name_singular'	=> $this->__('Contact Submission Category'),
			'name_plural'		=> $this->__('Contact Submission Categories'),

			// -----------------------------------------------------------------------

			'register' => [
				'name'	=> 'contact_submission_category',
				'types'	=> ['contact_submission'],
				'args'	=> [
					'public'							=> false,
					'show_ui'							=> true,
					'meta_box_cb'					=> false,
					 // @notes: i'm allowing in quick edit in case i want to edit/add a category
					//'show_in_quick_edit'	=> false,
					'show_admin_column'		=> true,
					'hierarchical'				=> true,
					'labels'							=> ['menu_name' => $this->__('Categories')],
				],
			],

		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// settings()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * SETTINGS
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Taxonomy_Contact_Submission_Category
