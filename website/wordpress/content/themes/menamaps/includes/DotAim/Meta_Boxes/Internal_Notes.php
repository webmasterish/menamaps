<?php

namespace DotAim\Meta_Boxes;

use DotAim\F;

class Internal_Notes
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	private $core;
	private $post_types;
	private $comment_type; // @notes: should not exceed 20 characters
	private $meta_box_id;
	private $admin_column_name;
	private $wp_editor_args;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * initialize - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function __construct()
	{

		if ( is_admin() && current_user_can('edit_others_posts') )
		{
			add_action( 'admin_init', [ $this, 'init' ] );
		}

	}
	// __construct()



	/**
	 * @internal
	 */
	public function init()
	{

		$this->core = DA();

		// -------------------------------------------------------------------------

		$this->post_types = $this->core->Settings()->get_component_app_settings(
			'general',
			'features',
			'internal_notes_post_types'
		);

		if ( empty( $this->post_types ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->comment_type	= "{$this->core->prefix}internal_note";
		$this->meta_box_id	= "{$this->core->prefix}internal_notes";

		// -------------------------------------------------------------------------

		$this->wp_editor_args = [
			'media_buttons'	=> false,
			'textarea_rows'	=> 5,
			'teeny'					=> true,
			/*
			'tinymce'				=> [
				'toolbar1'	=> 'bold,italic,underline,bullist,numlist,link',
				'toolbar2'	=> '',
			],
			*/
			'tinymce'		=> false,
			'quicktags'	=> [
				'buttons'	=> 'strong,em,link,block,del,ins,img,ul,ol,li,code,close',
			],
		];

		// -------------------------------------------------------------------------

		add_action('add_meta_boxes'								, [ $this, 'add_meta_box'] );
		add_action('admin_enqueue_scripts'				, [ $this, 'admin_scripts'] );
		add_action('wp_ajax_add_internal_note'		, [ $this, 'add_note'] );
		add_action('wp_ajax_edit_internal_note'		, [ $this, 'edit_note'] );
		add_action('wp_ajax_delete_internal_note'	, [ $this, 'delete_note'] );

		// -------------------------------------------------------------------------

		// will not add admin column if empty

		$this->admin_column_name = $this->meta_box_id;

		if ( $this->admin_column_name )
		{
			$this->set_admin_columns();
		}

	}
	// init()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * initialize - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * render - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function add_meta_box( $post_type )
	{

		if ( ! in_array( $post_type, $this->post_types ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$args = [
			'id'						=> $this->meta_box_id,
			'title'					=> $this->core->__('Internal Notes'),
			'callback'			=> [ $this, 'render_meta_box' ],
			'screen'				=> $post_type,
			'context'				=> 'normal',
			'priority'			=> 'default',
			'callback_args'	=> null,
		];

		add_meta_box( ...array_values( $args ) );

	}
	// add_meta_box()



	/**
	 * @internal
	 */
	public function render_meta_box( $post )
	{

		$notes = get_comments([
			'post_id'	=> $post->ID,
			'type'		=> $this->comment_type,
			'order'		=> 'DESC',
			'status'	=> 'private',
		]);

		// -------------------------------------------------------------------------

		?>
		<div id="<?php echo "{$this->meta_box_id}_wrapper"; ?>">

			<div id="<?php echo "{$this->meta_box_id}_form"; ?>" style="display: none;">
				<?php wp_editor('', "{$this->meta_box_id}_content", $this->wp_editor_args); ?>
				 <p>
					<button id="<?php echo "{$this->meta_box_id}_submit"; ?>" type="button" class="button button-primary">
						<?php DA()->_e('Add Note'); ?>
					</button>
					<button id="<?php echo "{$this->meta_box_id}_cancel"; ?>" type="button" class="button">
						<?php DA()->_e('Cancel'); ?>
					</button>
					<span class="spinner"></span>
				 </p>
			</div>

			<p class="hide-if-no-js" id="add-new-note">
				<button id="<?php echo "{$this->meta_box_id}_add"; ?>" type="button" class="button">
					<?php DA()->_e('Add Note'); ?>
				</button>
			</p>

			<table id="<?php echo "{$this->meta_box_id}_table"; ?>" class="widefat fixed striped table-view-list wp-list-table">
				<tbody id="the-note-list" data-wp-lists="list:note">
					<?php
					if ( ! empty( $notes ) )
					{
						foreach ( $notes as $note )
						{
							$this->render_note( $note );
						}
					}
					?>
				</tbody>
			</table>
			<p id="no_notes_message" style="display:none;"><?php DA()->_e('No notes yet.'); ?></p>

			<div id="<?php echo "{$this->meta_box_id}_edit_container"; ?>" style="display: none;">
				<?php wp_editor('', "{$this->meta_box_id}_edit_content", $this->wp_editor_args); ?>
			</div>

			<?php wp_nonce_field($this->meta_box_id, "{$this->meta_box_id}_nonce"); ?>
		</div>
		<?php

	}
	// render_meta_box()



	/**
	 * @internal
	 */
	private function render_note( $note )
	{

		$author_column = false;

		/*
		<td class="author column-author">
			<strong title="<?php echo esc_html( $note->comment_author ); ?>">
				<?php echo get_avatar($note->user_id, 32); ?>
				<?php echo esc_html($note->comment_author); ?>
			</strong>
			<br>
			<a href="mailto:<?php echo esc_attr($note->comment_author_email); ?>">
				<?php echo esc_html($note->comment_author_email); ?>
			</a>
			<br>
			<a href="<?php echo esc_url(admin_url('edit-comments.php?s=' . $note->comment_author_IP . '&mode=detail')); ?>">
				<?php echo esc_html($note->comment_author_IP); ?>
			</a>
		</td>
		*/

		// -------------------------------------------------------------------------

		$current_user = wp_get_current_user();

		// -------------------------------------------------------------------------

		?>
		<tr id="note-<?php echo $note->comment_ID; ?>" class="note">

			<?php if ( $author_column ) : ?>
				<td class="author column-author">
					<strong title="<?php echo esc_html( $note->comment_author ); ?>">
						<?php echo get_avatar($note->user_id, 32); ?>
					</strong>
				</td>
			<?php endif; ?>

			<td class="comment column-comment" colspan="<?php echo ! $author_column ? 2 : 1; ?>">
				<div class="submitted-on">
					<?php printf(
						'%s by %s',
						get_comment_date('M j, Y \a\t g:i a', $note),
						esc_html($note->comment_author)
					); ?>
				</div>

				<div class="comment-content">
					<?php echo wpautop($note->comment_content); ?>
				</div>

				<div id="inline-<?php echo $note->comment_ID; ?>" class="hidden">
					<textarea class="comment" rows="1" cols="1"><?php
						echo esc_textarea( $note->comment_content );
					?></textarea>
				</div>

				<div class="row-actions">
					<span class="edit">
						<button type="button" class="button-link" data-note-id="<?php echo $note->comment_ID; ?>" data-action="edit">
							<?php DA()->_e('Edit'); ?>
						</button>
					</span>
					<span class="delete"> |
						<button type="button" class="button-link vim-d" data-note-id="<?php echo $note->comment_ID; ?>" data-action="delete">
							<?php DA()->_e('Delete'); ?>
						</button>
					</span>
				</div><!-- .row-actions -->
			</td>

		</tr>
		<?php

	}
	// render_note()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * render - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * scripts and styles - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function admin_scripts( $hook )
	{

		if ( 		! in_array( $hook, ['post.php', 'post-new.php'] )
				 || ! in_array( get_post_type(), $this->post_types ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$css = sprintf('
			#%1$s .inside {
				margin: 0;
				padding: 0;
			}
			#%1$s #add-new-note,
			#%1$s #no_notes_message {
				padding: 8px 10px;
				margin: 0;
			}
			#%1$s_table {
				border: 0 none;
			}
			#%1$s_form {
				margin: 10px 0;
				padding: 0 12px;
			}
			#%1$s_wrapper .button-link[data-action="delete"] {
				color: #b32d2e;
			}
			#%1$s_wrapper .row-actions {
				color: #666;
			}
			#%1$s_wrapper .edit-note td {
				padding: 12px;
			}
			#%1$s_wrapper .edit_note_buttons_container {
				margin: 1em 0;
			}
			#%1$s_wrapper .vim-d {
				color: #b32d2e;
			}
			#%1$s_wrapper td.first {
				width: 150px;
			}
			#%1$s_wrapper .button-link {
				color: #2271b1;
				text-decoration: underline;
				background: none;
				border: none;
				padding: 0;
				cursor: pointer;
			}
			#%1$s_wrapper .button-link:hover {
				color: #135e96;
			}
			#%1$s_wrapper .spinner {
				float: none;
				margin: 4px 10px;
			}
			#%1$s_wrapper .submitted-on {
				color: #666;
				margin-bottom: 5px;
			}
			',
			$this->meta_box_id
		);

		wp_add_inline_style( 'wp-admin', F::minify( $css ) );

		// -------------------------------------------------------------------------

		wp_add_inline_script( 'jquery', F::minify( $this->get_js() ) );

	}
	// admin_scripts()



	/**
	 * @internal
	 */
	private function get_js()
	{

		ob_start();

		?>
			jQuery(document).ready( function( $ ) {

				const meta_box_id		= '<?php echo $this->meta_box_id; ?>';
				const $wrapper			= $(`#${meta_box_id}_wrapper`);
				const $addForm			= $(`#${meta_box_id}_form`);
				const $addButton		= $(`#${meta_box_id}_add`);
				const $submitButton	= $(`#${meta_box_id}_submit`);
				const $cancelButton	= $(`#${meta_box_id}_cancel`);

				let currentEditingId = null;

				// ---------------------------------------------------------------------

				if ( window.location.hash === '#' + meta_box_id )
				{
					requestAnimationFrame( function() {

						setTimeout(function() {

							const $target = $(`#${meta_box_id}`);

							if ( $target.length )
							{
								$('html, body').animate({
									scrollTop: $target.offset().top - 32
								}, 500);
							}

						}, 100);

					});
				}

				// ---------------------------------------------------------------------

				const show_hide_no_notes = function() {

					const $notes_table	= $(`#${meta_box_id}_table`);
					const $no_notes			= $('#no_notes_message');

					if ( $('#the-note-list tr').length === 0 )
					{
						$no_notes.show();
						$notes_table.hide();
					}
					else
					{
						$no_notes.hide();
						$notes_table.show();
					}

				};

				show_hide_no_notes();

				// ---------------------------------------------------------------------

				const get_editor_content = function( editor_id ) {
					if (typeof tinymce !== 'undefined' && tinymce.get(editor_id)) {
						return tinymce.get(editor_id).getContent();
					}
					return $('#' + editor_id).val();
				};

				const set_editor_content = function(editor_id, content) {
					if (typeof tinymce !== 'undefined' && tinymce.get(editor_id)) {
						tinymce.get(editor_id).setContent(content);
					} else {
						$('#' + editor_id).val(content);
					}
				};

				const focus_editor = function(editor_id) {
					if (typeof tinymce !== 'undefined' && tinymce.get(editor_id)) {
						tinymce.get(editor_id).focus();
					} else {
						$('#' + editor_id).focus();
					}
				};

				// ---------------------------------------------------------------------

				const $edit_container = $(`#${meta_box_id}_edit_container`);

				const edit_row_show = function( $edit_row ) {

					$edit_row
					.find('.edit-note-content')
					.append( $edit_container.show() );

				};

				const edit_row_hide = function( $edit_row ) {

					$edit_container
					.hide()
					.appendTo( $wrapper );

					$edit_row.remove();

				};

				// ---------------------------------------------------------------------

				$addButton.on('click', function() {
					$addForm.slideDown();
					$addButton.hide();
					focus_editor(`${meta_box_id}_content`);
				});

				// ---------------------------------------------------------------------

				$cancelButton.on('click', function() {
					$addForm.slideUp();
					$addButton.show();
					set_editor_content(`${meta_box_id}_content`, '');
				});

				// ---------------------------------------------------------------------

				$submitButton.on('click', function() {
					const $button		= $(this);
					const $spinner	= $button.siblings('.spinner');
					const content		= get_editor_content(`${meta_box_id}_content`);

					if ( ! content )
					{
						return;
					}

					// -------------------------------------------------------------------

					$button.prop('disabled', true);
					$spinner.addClass('is-active');

					$.post(ajaxurl, {
						action	: 'add_internal_note',
						post_id	: $('#post_ID').val(),
						content	: content,
						nonce		: $(`#${meta_box_id}_nonce`).val()
					})
					.done( function( response ) {

						if ( response.success )
						{
							$('#the-note-list').prepend(response.data.html);
							set_editor_content(`${meta_box_id}_content`, '');
							$addForm.slideUp();
							$addButton.show();
						}
					})
					.always( function() {
						$button.prop('disabled', false);
						$spinner.removeClass('is-active');

						show_hide_no_notes();
					});
				});

				// ---------------------------------------------------------------------

				$wrapper.on('click', '.button-link[data-action="edit"]', function(e) {

					e.preventDefault();

					const noteId			= $(this).data('note-id');
					const $note				= $(`#note-${noteId}`);
					const content			= $note.find('#inline-' + noteId + ' .comment').text();
					currentEditingId	= noteId;

					const $editRow = $(`
						<tr class="edit-note">
							<td colspan="2">
								<div class="edit-note-content"></div>
								<p class="edit_note_buttons_container">
									<button type="button" class="button button-primary <?php echo "{$this->meta_box_id}_edit_submit"; ?>"><?php
										DA()->_e('Update');
									?></button>
									<button type="button" class="button <?php echo "{$this->meta_box_id}_edit_cancel"; ?>"><?php
										DA()->_e('Cancel');
									?></button>
									<span class="spinner"></span>
								</p>
							</td>
						</tr>
					`);

					$note.hide().after( $editRow );

					edit_row_show( $editRow );
					set_editor_content(`${meta_box_id}_edit_content`, content);
					focus_editor(`${meta_box_id}_edit_content`);

				});

				// ---------------------------------------------------------------------

				$wrapper.on('click', `.${meta_box_id}_edit_cancel`, function() {
					const $editRow	= $(this).closest('tr');
					$editRow.prev().show();
					$edit_container.hide().appendTo($wrapper); // Move editor back
					$editRow.remove();
					currentEditingId = null;
				});

				// ---------------------------------------------------------------------

				$wrapper.on('click', `.${meta_box_id}_edit_submit`, function() {

					if ( ! currentEditingId )
					{
						return;
					}

					// -------------------------------------------------------------------

					const $button		= $(this);
					const $spinner	= $button.siblings('.spinner');
					const $editRow	= $button.closest('tr');
					const content		= get_editor_content(`${meta_box_id}_edit_content`);

					if ( ! content )
					{
						return;
					}

					// -------------------------------------------------------------------

					$button.prop('disabled', true);
					$spinner.addClass('is-active');

					$.post(ajaxurl, {
						action	: 'edit_internal_note',
						note_id	: currentEditingId,
						content	: content,
						nonce		: $(`#${meta_box_id}_nonce`).val()
					})
					.done( function( response ) {
						if ( response.success )
						{
							const $note = $(`#note-${currentEditingId}`);
							$note.find('.comment-content').html(response.data.content);
							$note.find('#inline-' + currentEditingId + ' .comment').text(content);
							$note.show();

							edit_row_hide( $editRow );

							currentEditingId = null;
						}
					})
					.always(function() {
						$button.prop('disabled', false);
						$spinner.removeClass('is-active');
					});

				});

				// ---------------------------------------------------------------------

				$wrapper.on('click', '.button-link[data-action="delete"]', function(e) {

					e.preventDefault();

					if ( ! confirm('<?php DA()->_e('Are you sure you want to delete this note?'); ?>') )
					{
						return;
					}

					// -------------------------------------------------------------------

					const noteId = $(this).data('note-id');
					const $note = $(`#note-${noteId}`);

					$.post( ajaxurl, {
						action	: 'delete_internal_note',
						note_id	: noteId,
						nonce		: $(`#${meta_box_id}_nonce`).val()
					})
					.done( function( response ) {

						if ( response.success )
						{
							$note.fadeOut( function() {

								$(this).remove();

								show_hide_no_notes();

							});
						}

					})
					.always(function() {

						show_hide_no_notes();

					});
				});

			});
		<?php

		// -------------------------------------------------------------------------

		return ob_get_clean();

	}

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * scripts and styles - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * admin columns - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	private function set_admin_columns()
	{

		$enable_sorting = false; // @consider optionize

		foreach ( $this->post_types as $post_type )
		{
			add_filter("manage_{$post_type}_posts_columns", [$this, 'add_admin_column']);
			add_action("manage_{$post_type}_posts_custom_column", [$this, 'render_admin_column'], 10, 2);

			if ( $enable_sorting )
			{
				add_filter("manage_edit-{$post_type}_sortable_columns", [$this, 'make_admin_column_sortable']);
			}
		}

		// -------------------------------------------------------------------------

		if ( $enable_sorting )
		{
			add_action( 'pre_get_posts', [$this, 'sort_by_notes_count'] );
		}

	}
	// set_admin_columns()



	/**
	 * @since 1.0.0
	 */
	public function add_admin_column( $columns )
	{

		$column_title = sprintf(
			'<span class="dotaim_column_icon dashicons-before dashicons-welcome-write-blog" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
			$this->core->__('Internal Notes')
		);

		// -------------------------------------------------------------------------

		$new_columns = [];

		foreach ( $columns as $key => $value )
		{
			$new_columns[ $key ] = $value;

			if ( 'comments' === $key )
			{
				$new_columns[ $this->admin_column_name ] = $column_title;
			}
		}

		// -------------------------------------------------------------------------

		// if not added after comments column, append to end

		if ( ! isset( $new_columns[ $this->admin_column_name ] ) )
		{
			$new_columns[ $this->meta_box_id ] = $column_title;
		}

		// -------------------------------------------------------------------------

		return $new_columns;

	}
	// add_admin_column()



	/**
	 * @since 1.0.0
	 */
	public function render_admin_column( $column_name, $post_id )
	{

		if ( $column_name !== $this->admin_column_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$notes_count = get_comments([
			'post_id'	=> $post_id,
			'type'		=> $this->comment_type,
			'count'		=> true,
			'status'	=> 'private',
		]);

		if ( $notes_count > 0 )
		{
			$notes_count_formatted = number_format_i18n( $notes_count );

			$url = esc_url( add_query_arg(
				[
					'post'		=> $post_id,
					'action'	=> 'edit',
				],
				admin_url('post.php')
			) . "#{$this->meta_box_id}" );

			$title = sprintf(
				$this->core->_n(
					'%s Internal Note',
					'%s Internal Notes',
					$notes_count
				),
				$notes_count_formatted,
			);

			$link_attr = F::html_attributes([
				'href'	=> $url,
				'title'	=> $title,
			]);

			printf(
				'<a%1$s><span class="comment-count-approved" aria-hidden="true">%2$s</span><span class="screen-reader-text">%3$s</span></a>',
				$link_attr,
				$notes_count_formatted,
				$title
			);
		}
		else
		{
			printf(
				'<span class="post-com-count post-com-count-no-comments" title="%2$s"><span class="comment-count comment-count-no-comments" aria-hidden="true">%1$s</span><span class="screen-reader-text">%2$s</span></span>',
				'&mdash;',
				$this->core->__('No Internal Notes')
			);
		}

	}
	// render_admin_column()



	/**
	 * @since 1.0.0
	 */
	public function make_admin_column_sortable( $columns )
	{

		$columns[ $this->admin_column_name ] = "{$this->meta_box_id}_count";

		return $columns;

	}
	// make_admin_column_sortable()



	/**
	 * @since 1.0.0
	 */
	public function sort_by_notes_count( $query )
	{

		if ( 		! is_admin()
				 || ! $query->is_main_query()
				 || ! in_array( $query->get('post_type'), $this->post_types )
				 || $query->get('orderby') !== "{$this->meta_box_id}_count" )
		{
			return;
		}

		// -------------------------------------------------------------------------

		add_filter('posts_join', function( $join ) use ( $query ) {

			global $wpdb;

			return $join . " LEFT JOIN (
				SELECT comment_post_ID, COUNT(*) as notes_count
				FROM {$wpdb->comments}
				WHERE comment_type = '{$this->comment_type}'
				AND comment_approved = 'private'
				GROUP BY comment_post_ID
			) AS internal_notes ON ({$wpdb->posts}.ID = internal_notes.comment_post_ID)";

		});

		add_filter('posts_orderby', function( $orderby ) use ( $query ) {

			return "notes_count " . $query->get('order', 'DESC');

		});

	}
	// sort_by_notes_count()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * admin columns - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ajax actions - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function add_note()
	{

		check_ajax_referer( $this->meta_box_id, 'nonce' );

		// -------------------------------------------------------------------------

		$current_user = wp_get_current_user();
		$comment_data = [
			'comment_post_ID'				=> $_POST['post_id'],
			'comment_content'				=> wp_kses_post( $_POST['content'] ),
			'comment_type'					=> $this->comment_type,
			'comment_approved'			=> 'private',
			'user_id'								=> get_current_user_id(),
			'comment_author'				=> $current_user->display_name,
			'comment_author_email'	=> $current_user->user_email,
			'comment_author_url'		=> $current_user->user_url,
			'comment_author_IP'			=> $_SERVER['REMOTE_ADDR'],
			'comment_agent'					=> $_SERVER['HTTP_USER_AGENT'],
		];

		$comment_id = wp_insert_comment( $comment_data );

		if ( $comment_id )
		{
			ob_start();

			$this->render_note( get_comment( $comment_id ) );

			wp_send_json_success(['html' => ob_get_clean()]);
		}

		// -------------------------------------------------------------------------

		wp_send_json_error();

	}
	// add_note()



	/**
	 * @internal
	 */
	public function edit_note()
	{

		check_ajax_referer( $this->meta_box_id, 'nonce' );

		// -------------------------------------------------------------------------

		$comment_id	= intval($_POST['note_id']);
		$content		= wp_kses_post($_POST['content']);

		if ( wp_update_comment(['comment_ID' => $comment_id, 'comment_content' => $content]) )
		{
			wp_send_json_success(['content' => wpautop( $content )]);
		}

		// -------------------------------------------------------------------------

		wp_send_json_error();

	}
	// edit_note()



	/**
	 * @internal
	 */
	public function delete_note()
	{

		check_ajax_referer( $this->meta_box_id, 'nonce' );

		// -------------------------------------------------------------------------

		$comment_id	= intval( $_POST['note_id'] );
		$comment		= get_comment( $comment_id );

		if ( wp_delete_comment( $comment_id, true ) )
		{
			wp_send_json_success(['author' => $comment->comment_author]);
		}

		// -------------------------------------------------------------------------

		wp_send_json_error();

	}
	// delete_note()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * ajax actions - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Internal_Notes
