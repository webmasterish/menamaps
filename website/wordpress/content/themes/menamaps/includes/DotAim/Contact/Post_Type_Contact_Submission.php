<?php

namespace DotAim\Contact;

use DotAim\F;

final class Post_Type_Contact_Submission extends \DotAim\Base\Custom_Post_Type
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected $metabox_prefix;
	public $nonce_prefix;
	public $taxonomies = ['contact_submission_category'];
	private $admin_area_capability = 'edit_others_posts';

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



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

		$register_args = [
			'public'				=> false,
			'menu_icon'			=> 'dashicons-feedback', // dashicons-email
			'can_export'		=> true,
			'supports'			=> [''],
			'capabilities'	=> ['create_posts' => false],
			'map_meta_cap'	=> true,
			'show_ui'				=> $this->admin_area_capability
											 ? current_user_can( $this->admin_area_capability )
											 : true,
		];

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [

			'name_singular'	=> $this->__('Contact Submission'),
			'name_plural'		=> $this->__('Contact Submissions'),

			// -----------------------------------------------------------------------

			'register' => [
				'name'	=> 'contact_submission',
				'args'	=> $register_args,
			],

			// -----------------------------------------------------------------------

			//'title_column_text' => $this->__('Subject'),

			// -----------------------------------------------------------------------

			'thumbnail_column' => false,

			// -----------------------------------------------------------------------

			'enter_title_here' => $this->__('Subject'),

			// -----------------------------------------------------------------------

			// only add to dashboard at a glance widget if user has capability

			'dashboard_glance' => $this->admin_area_capability
													? current_user_can( $this->admin_area_capability )
													: true,

		];

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// settings()

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

		add_action( 'init', [ $this, 'init_action' ] );

	}
	// hooks()



	/**
	 * @internal
	 */
	public function init_action()
	{

		if ( false === parent::init_action() )
		{
			return false;
		}

		// -------------------------------------------------------------------------

		$this->metabox_prefix = "{$this->core->meta_box_prefix}{$this->name}_";

		// -------------------------------------------------------------------------

		$this->nonce_prefix = "{$this->core->prefix}{$this->name}_";
		// dotaim_contact_submission_

		// -------------------------------------------------------------------------

		$this->admin_hooks();

		// -------------------------------------------------------------------------

		add_shortcode( 'contact_form', [ $this, 'form_shortcode' ] );

		// -------------------------------------------------------------------------

		add_action(
			'wp_ajax_add_contact_submission',
			[ $this, 'form_handle_submission' ]
		);

		add_action(
			'wp_ajax_nopriv_add_contact_submission',
			[ $this, 'form_handle_submission' ]
		);

	}
	// init_action()



	/**
	 * @internal
	 */
	private function admin_hooks()
	{

		if ( ! is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_filter(
			'post_date_column_status',
			[$this, 'post_date_column_status'],
			10,
			2
		);

		// -------------------------------------------------------------------------

		add_action('add_meta_boxes', [$this, 'remove_metaboxes']);

		// -------------------------------------------------------------------------

		add_filter('screen_layout_columns', [$this, 'screen_layout_columns']);

		add_filter(
			"get_user_option_screen_layout_{$this->name}",
			[$this, 'screen_layout_columns_user_option']
		);

	}
	// admin_hooks()



	/**
	 * @internal
	 */
	public function post_date_column_status( $status, $post )
	{

		if ( 		$this->name === $post->post_type
				 && 'publish' 	=== $post->post_status )
		{
			$status = $this->__('Submitted');
		}

		// -------------------------------------------------------------------------

		return $status;

	}
	// post_date_column_status()



	/**
	 * @internal
	 */
	public function remove_metaboxes()
	{

		remove_meta_box('slugdiv', $this->name, 'normal');
		remove_meta_box('submitdiv', $this->name, 'side');

	}
	// remove_metaboxes()



	/**
	 * @internal
	 */
	public function screen_layout_columns( $columns )
	{

		$screen = get_current_screen();

		if ( $screen->post_type === $this->name )
		{
			$columns[ $screen->id ] = 1;
		}

		// -------------------------------------------------------------------------

		return $columns;

	}
	// screen_layout_columns()



	/**
	 * @internal
	 */
	public function screen_layout_columns_user_option()
	{

		$screen = get_current_screen();

		if ( $screen->post_type === $this->name )
		{
			return 1;
		}

	}
	// screen_layout_columns_user_option()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HOOKS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function manage_edit_columns( $columns )
	{

		$new_columns = [
			'cb'						=> '<input type="checkbox" />',
			//'date'				=> $this->__('Date'),
			'submitted_by'	=> $this->__('Submitted By'),
			'title'					=> $this->__('Subject'),
			'message'				=> $this->__('Message'),
		];

		// -------------------------------------------------------------------------

		if ( $taxonomies = get_object_taxonomies( $this->name, 'objects' ) )
		{
			foreach ( $taxonomies as $taxonomy => $taxonomy_obj )
			{
				if ( ! empty( $taxonomy_obj->show_admin_column ) )
				{
					$new_columns["taxonomy-{$taxonomy}"] = trim( str_replace(
						$this->name_singular(),
						'',
						$taxonomy_obj->labels->menu_name
					) );
				}
			}
		}

		// -------------------------------------------------------------------------

		$new_columns['date'] = $this->__('Date');

		// -------------------------------------------------------------------------

		return $new_columns;

	}
	// manage_edit_columns()



	/**
	 * @since 1.0.0
	 */
	public function manage_posts_custom_column( $column_name, $post_id )
	{

		ob_start();

		parent::manage_posts_custom_column( $column_name, $post_id );

		if ( $out = ob_get_clean() )
		{
			echo $out;

			return;
		}

		// -------------------------------------------------------------------------

		$single = true;

		switch ( $column_name )
		{
			case 'submitted_by':

				$out					= '&mdash;'; // default output
				$submitted_by	= [];

				if ( $submitter_name = get_post_meta( $post_id, "{$this->metabox_prefix}submitter_name", $single ) )
				{
					$submitted_by[] = "<strong>{$submitter_name}</strong>";
				}

				if ( $submitter_email = get_post_meta( $post_id, "{$this->metabox_prefix}submitter_email", $single ) )
				{
					$submitted_by[] = sprintf('<a href="mailto:%1$s">%1$s</a>', $submitter_email);
				}

				if ( ! empty( $submitted_by ) )
				{
					$out = implode( '<br>', $submitted_by );
				}

				break;

			// -----------------------------------------------------------------------

			case 'message':

				$out = '&mdash;';

				if ( $out = get_the_excerpt() )
				{
					$out = sprintf(
						'<span%s>%s</span>',
						$this->attr([
							'style' => [
								'overflow'						=> 'hidden',
								'display'							=> '-webkit-box',
								'-webkit-box-orient'	=> 'vertical',
								'-webkit-line-clamp'	=> 2,
							],
						]),
						$out
					);
				}

				break;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $out ) )
		{
			echo $out;
		}

	}
	// manage_posts_custom_column()



	/**
	 * @since 1.0.0
	 */
	public function manage_edit_sortable_columns( $columns )
	{

		$columns['submitted_by'] = 'submitted_by';

		// -------------------------------------------------------------------------

		return $columns;

	}
	// manage_edit_sortable_columns()



	/**
	 * @since 1.0.0
	 */
	public function manage_edit_sortable_columns_order_by( $query )
	{

		if ( ! is_admin() )
		{
			return;
		}

		if ( $this->name !== $query->get('post_type') )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$orderby = $query->get('orderby');

		switch ( $orderby )
		{
			case 'submitted_by':

				$meta_key = "{$this->metabox_prefix}submitter_name";

				$query->set( 'meta_query', [
					'relation' => 'OR',
					[
						'key'			=> $meta_key,
						'compare'	=> 'NOT EXISTS',
					],
					[
						'key'			=> $meta_key,
						'compare'	=> 'EXISTS'
					],
				]);

				$query->set( 'orderby', 'meta_value title' );

				break;
		}

	}
	// manage_edit_sortable_columns_order_by()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ADMIN - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FRONTEND - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function form_html( $args = [] )
	{

		$defaults = [
			'id'										=> 'contact_form',
			'theme_secondary'				=> false,
			'add_category_selector'	=> false,
			'button_text'						=> $this->__('Send message'),
			'echo'									=> true,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$theme = $theme_secondary ? 'secondary' : 'primary';

		// -------------------------------------------------------------------------

		// @todo
		/*
		if ( $add_category_selector )
		{

		}
		*/

		// -------------------------------------------------------------------------

		// @consider character counter for message field

		// -------------------------------------------------------------------------

		$label_class = 'block mb-2 text-base font-medium text-primary-700 dark:text-primary-300';
		$field_class = "text_field {$theme}";

		// -------------------------------------------------------------------------

		$submit_button_attr = [
			'type'	=> 'submit',
			'id'		=> "{$id}_submit_button",
			'class'	=> [
				'button',
				$theme,
				// @todo: optionize
				'rounded-sm',

				'relative',
			],

			':disabled' => '!submitter_name || !submitter_email || !subject || !message',
		];

		$submit_button = sprintf(
			'<button%s>%s%s</button>',
			$this->attr( $submit_button_attr ),
			$button_text,
			F::button_spinner()
		);

		// -------------------------------------------------------------------------

		$attr = [
			'id'		=> $id,
			'class'	=> [
				'contact_form',
				'space-y-4 sm:space-y-8',
			],

			// -----------------------------------------------------------------------

			// @todo: if no js

			//'action'

			// -----------------------------------------------------------------------

			'x-show' => 'showForm',

			// -----------------------------------------------------------------------

			'hx-post'								=> admin_url('admin-ajax.php'),
			'hx-trigger'						=> 'submit',
			'hx-target'							=> "#{$id}_response",
			'hx-indicator'					=> "#{$submit_button_attr['id']} .button_spinner",
			'hx-disabled-elt'				=> "#{$submit_button_attr['id']},this",
			'hx-on::after-request'	=> 'this.reset()',
			'hx-vals'								=> sprintf(
				'js:{%s}',
				implode(',', [
					'action:"add_contact_submission"',
				])
			),
		];

		// -------------------------------------------------------------------------

		$container_attr = [

			'id' => "{$id}_container",

			// -----------------------------------------------------------------------

			'x-data' => sprintf(
				'{%s}',
				implode( ',', [
					'showForm: true',
					"submitter_name: ''",
					"submitter_email: ''",
					"subject: ''",
					"message: ''",
				])
			),

			'x-on:htmx:after-request.window' => "showForm = false",

		];

		// -------------------------------------------------------------------------

		ob_start();

		?>
		<div<?php echo $this->attr( $container_attr ); ?>>
			<form<?php echo $this->attr( $attr ); ?>>

				<div class="grid gap-4 md:grid-cols-2">
					<div>
						<label for="submitter_name" class="<?php echo $label_class; ?>"><?php
							$this->_e('Your name *');
						?></label>
						<input type="text" id="submitter_name" name="submitter_name" maxlength="50" class="<?php echo $field_class; ?>" required x-model="submitter_name" placeholder="Enter your name">
					</div>

					<div>
						<label for="submitter_email" class="<?php echo $label_class; ?>"><?php
							$this->_e('Your email *');
						?></label>
						<input type="email" id="submitter_email" name="submitter_email" maxlength="254" class="<?php echo $field_class; ?>" required x-model="submitter_email">
					</div>
				</div>

				<div>
					<label for="subject" class="<?php echo $label_class; ?>"><?php
						$this->_e('Subject *');
					?></label>
					<input type="text" id="subject" name="subject" maxlength="100" class="<?php echo $field_class; ?>" required x-model="subject">
				</div>

				<div>
					<label for="message" class="<?php echo $label_class; ?>"><?php
						$this->_e('Your message *');
					?></label>
					<textarea id="message" name="message" rows="6" maxlength="2000" class="<?php echo $field_class; ?>" required x-model="message"></textarea>
				</div>

				<?php wp_nonce_field("{$this->nonce_prefix}add_contact_submission"); ?>

				<?php echo $submit_button; ?>

			</form><!-- #<?php echo $attr['id']; ?> -->

			<div id="<?php echo "{$id}_response"; ?>" x-show="!showForm" class="mt-4"></div>

			<button type="button" class="button rounded-sm primary mt-4 mx-auto" x-show="!showForm" @click="showForm = true">
				<?php $this->_e('Send another message'); ?>
			</button>

		</div><!-- #<?php echo $container_attr['id']; ?> -->
		<?php

		$out = trim( ob_get_clean() );

		// -------------------------------------------------------------------------

		if ( $echo )
		{
			echo $out;
		}
		else
		{
			return $out;
		}

	}
	// form_html()



	/**
	 * @internal
	 */
	public function form_shortcode( $atts )
	{

		$defaults = [
			'id'										=> 'contact_form',
			'theme_secondary'				=> false,
			'add_category_selector'	=> false,
			'button_text'						=> $this->__('Send message'),
		];

		$args = shortcode_atts( $defaults, $atts );

		$args['echo'] = false;

		// -------------------------------------------------------------------------

		return $this->form_html( $args );

	}
	// form_shortcode()



	/**
	 * @internal
	 */
	public function form_handle_submission()
	{

		$action = 'add_contact_submission';

		if ( ! check_ajax_referer( "{$this->nonce_prefix}{$action}", false, false ) )
		{
			wp_die( F::get_alert([
				'type'			=> 'error',
				'bordered'	=> true,
				'message' 	=> sprintf('<div>%s</div>', $this->__('Unauthorized action.'))
			]));
		}

		// -------------------------------------------------------------------------

		$fields = [
			'submitter_name'	=> ['label' => $this->__('Name')],
			'submitter_email'	=> ['label' => $this->__('Email')],
			'subject'					=> ['label' => $this->__('Subject')],
			'message'					=> ['label' => $this->__('Message')],
		];

		foreach ( $fields as $field_name => $field )
		{
			$field_value = '';

			if ( ! empty( $_POST[ $field_name ] ) )
			{
				$field_value = trim( sanitize_text_field( $_POST[ $field_name ] ) );
			}

			if ( ! $field_value )
			{
				wp_die( F::get_alert([
					'type'			=> 'error',
					'bordered'	=> true,
					'message' 	=> sprintf(
						'<div>%s %s</div>',
						$field['label'],
						$this->__('Required')
					),
				]));
			}

			// -----------------------------------------------------------------------

			$fields[ $field_name ]['value'] = $field_value;
		}

		// -------------------------------------------------------------------------

		$meta_input = [
			"{$this->metabox_prefix}submitter_name"		=> $fields['submitter_name']['value'],
			"{$this->metabox_prefix}submitter_email"	=> $fields['submitter_email']['value'],
		];

		// -------------------------------------------------------------------------

		// technical submission details

		if ( $ip = F::get_ip_address() )
		{
			$meta_input["{$this->metabox_prefix}ip"] = $ip;
		}

		$keys = [
			'REMOTE_ADDR',
			'HTTP_USER_AGENT',
			//'HTTP_REFERER',  // can't use it as it will refer to admin, instead will use _wp_http_referer
			//'REQUEST_URI',
		];

		foreach ( $keys as $key )
		{
			if ( isset( $_SERVER[ $key ] ) )
			{
				$meta_input["{$this->metabox_prefix}server_" . strtolower( $key )] = $_SERVER[ $key ];
			}
		}

		if ( ! empty( $_POST['_wp_http_referer'] ) )
		{
			$meta_input["{$this->metabox_prefix}server_http_referer"] = $_POST['_wp_http_referer'];
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $this->taxonomies ) )
		{
			$tax_input = [];

			foreach ( $this->taxonomies as $taxonomy )
			{
				if ( empty( $_POST[ $taxonomy ] ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				$terms_ids = ! is_array( $_POST[ $taxonomy ] ) ? [ $_POST[ $taxonomy ] ] : $_POST[ $taxonomy ];

				$tax_input[ $taxonomy ] = array_map(
					function( $cat ) { return absint( $cat ); },
					$terms_ids
				);
			}
		}

		// -------------------------------------------------------------------------

		// @todo: optionize
		$email_to = $this->core->get_contact_email();

		// -------------------------------------------------------------------------

		$insert_post_args = array_filter([
			'post_type'			=> $this->name,
			//'post_author'	=> null, // 0 if not signed in, or current user id
			'post_title'		=> $fields['subject']['value'],
			'post_content'	=> $fields['message']['value'],
			'post_status'		=> 'publish',
			'meta_input'		=> $meta_input,
			'tax_input'			=> ! empty( $tax_input ) ? $tax_input : null,
		]);

		if ( ! $post_id = wp_insert_post( $insert_post_args ) )
		{
			wp_die( F::get_alert([
				'type'			=> 'error',
				'bordered'	=> true,
				'message' => sprintf(
					'<div class="text-lg">
						<p class="font-bold mb-2">%s</p>
						<p>%s</p>
					</div>',
					$this->__('Failed to send message.'),
					sprintf(
						$this->__('Please try again or send your message to <b>%s</b>.'),
						do_shortcode("[email_link email='$email_to']")
					)
				),
			]));
		}

		// -------------------------------------------------------------------------

		// send emails

		// @consider
		// - to other users maybe based on category
		// - emailing submitter

		$insert_post_args['post_id'] = $post_id;

		$this->email_new_submission( $insert_post_args, $email_to );

		// -------------------------------------------------------------------------

		wp_die( F::get_alert([
			'type'			=> 'success',
			'bordered'	=> true,
			'message' => sprintf(
				'<div class="text-lg">
					<p class="font-bold mb-2">%s</p>
					<p>%s</p>
				</div>',
				$this->__('Your message has been sent successfully!'),
				$this->__('Thank you for contactiong us, we\'ll get back to you shortly.')
			),
		]));

	}
	// form_handle_submission()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FRONTEND - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * EMAIL RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	private function email_new_submission( $post_args, $to = '' )
	{

		try {

			if ( ! $submitter_name = F::array_get( $post_args, ['meta_input', "{$this->metabox_prefix}submitter_name"] ) )
			{
				return;
			}

			if ( ! $submitter_email = F::array_get( $post_args, ['meta_input', "{$this->metabox_prefix}submitter_email"] ) )
			{
				return;
			}

			if ( ! $subject = F::array_get( $post_args, ['post_title'] ) )
			{
				return;
			}

			if ( ! $message = F::array_get( $post_args, ['post_content'] ) )
			{
				return;
			}

			// -----------------------------------------------------------------------

			$to		= $to ? $to : $this->core->get_contact_email();
			$from	= "$submitter_name <$submitter_email>";

			// -----------------------------------------------------------------------

			$headers  = [
				"Content-Type: text/html; charset=UTF-8;",
				"From: $from",
			];

			$headers = implode( "\n", $headers );

			// -----------------------------------------------------------------------

			$res = wp_mail( $to, $subject, $message, $headers );

		} catch ( Exception $e ) {

			debug_log( sprintf(
				'Error in %s: %s',
				__METHOD__,
				$e->getMessage()
			) );

		}

	}
	// email_new_submission_to_admin()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * EMAIL RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Post_Type_Contact_Submission
