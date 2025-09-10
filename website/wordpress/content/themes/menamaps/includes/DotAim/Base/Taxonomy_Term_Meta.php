<?php

namespace DotAim\Base;

use DotAim\F;

/**
 * @internal
 */
class Taxonomy_Term_Meta
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected $taxonomy;
	protected $meta_fields;

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	public function __construct( $taxonomy, $meta_fields )
	{

		if ( empty( $taxonomy ) || empty( $meta_fields ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->taxonomy			= $taxonomy;
		$this->meta_fields	= $meta_fields;

		// -------------------------------------------------------------------------

		$this->hooks();

	}
	// __construct()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * hooks - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function hooks()
	{

		// Save term meta data

		add_action( "created_{$this->taxonomy}", [ $this, 'save_term_fields' ] );
		add_action( "edited_{$this->taxonomy}" , [ $this, 'save_term_fields' ] );

		// -------------------------------------------------------------------------

		$this->admin_hooks();

	}
	// hooks()



	/**
	 * @internal
	 */
	public function save_term_fields( $term_id )
	{

		if ( ! isset( $_POST['term_meta'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $this->meta_fields as $field )
		{
			if ( empty( $field['id'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$field_id = $field['id'];

			if ( isset( $_POST['term_meta'][$field_id] ) )
			{
				$value = sanitize_text_field( $_POST['term_meta'][$field_id] );

				update_term_meta( $term_id, $field_id, $value );
			}
		}

	}
	// save_term_fields()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * hooks - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * admin - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function admin_hooks()
	{

		if ( ! is_admin() )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// Add form fields

		add_action("{$this->taxonomy}_add_form_fields", [$this, 'admin_add_term_fields']);
		add_action("{$this->taxonomy}_edit_form_fields", [$this, 'admin_edit_term_fields'], 10, 2);

		// -------------------------------------------------------------------------

		// Add custom columns

		add_filter("manage_edit-{$this->taxonomy}_columns", [$this, 'admin_add_term_columns']);
		add_filter("manage_{$this->taxonomy}_custom_column", [$this, 'admin_add_term_column_content'], 10, 3);

		// -------------------------------------------------------------------------

		// Add admin scripts

		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

	}
	// admin_hooks()



	/**
	 * @internal
	 */
	public function admin_add_term_fields()
	{

		foreach ( $this->meta_fields as $field )
		{
			if ( empty( $field['id'] ) || empty( $field['type'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$field_id	= $field['id'];
			$label		= ! empty( $field['label'] ) ? $field['label'] : $field_id;
			$default	= ! empty( $field['default'] ) ? $field['default'] : '';

			switch ( $field['type'] )
			{
				case 'text':

					$field_markup = $this->admin_field_text( $field_id, $default );

					break;

				// ---------------------------------------------------------------------

				case 'image':

					$field_markup = $this->admin_field_image( $field_id, $default );

					break;

				// ---------------------------------------------------------------------

				// @consider adding other field types such as checkbox
			}

			if ( empty( $field_markup ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			?>
			<div class="form-field term-<?php echo esc_attr( $field_id ); ?>-wrap">
				<label for="term-<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
				<?php
				echo $field_markup;

				if ( ! empty( $field['description'] ) )
				{
					printf( '<p class="description">%s</p>', $field['description'] );
				}
				?>
			</div>
			<?php
		}

	}
	// admin_add_term_fields()



	/**
	 * @internal
	 */
	public function admin_edit_term_fields( $term, $taxonomy )
	{

		foreach ( $this->meta_fields as $field )
		{
			if ( empty( $field['id'] ) || empty( $field['type'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$field_id	= $field['id'];
			$label		= ! empty( $field['label'] ) ? $field['label'] : $field_id;
			$default	= ! empty( $field['default'] ) ? $field['default'] : '';
			$value		= get_term_meta( $term->term_id, $field_id, true );
			$value		= ! empty( $value ) ? $value : $default;

			switch ( $field['type'] )
			{
				case 'text':

					$field_markup = $this->admin_field_text( $field_id, $value );

					break;

				// ---------------------------------------------------------------------

				case 'image':

					$field_markup = $this->admin_field_image( $field_id, $value );

					break;

				// ---------------------------------------------------------------------

				// @consider adding other field types such as checkbox
			}

			if ( empty( $field_markup ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			?>
			<tr class="form-field term-<?php echo esc_attr( $field_id ); ?>-wrap">
				<th scope="row">
					<label for="term-<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
				</th>
				<td>
					<?php
					echo $field_markup;

					if ( ! empty( $field['description'] ) )
					{
						printf( '<p class="description">%s</p>', $field['description'] );
					}
					?>
				</td>
			</tr>
			<?php
		}

	}
	// admin_edit_term_fields()



	/**
	 * @internal
	 */
	public function admin_add_term_columns( $columns )
	{

		foreach ( $this->meta_fields as $field )
		{
			if ( empty( $field['id'] ) || empty( $field['custom_column'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$label = ! empty( $field['custom_column_label'] ) ? $field['custom_column_label'] : $field['label'];

			if ( 'append' === $field['custom_column'] )
			{
				$columns[ $field['id'] ] = $label;
			}
			else if ( 'prepend' === $field['custom_column'] )
			{
				if ( isset( $columns['cb'] ) )
				{
					$new_columns = [
						'cb'					=> $columns['cb'],
						$field['id']	=> $label,
					];

					unset( $columns['cb'] );
				}
				else
				{
					$new_columns = [ $field['id'] => $label ];
				}

				$columns = array_merge( $new_columns, $columns );
			}
			else if ( is_string( $field['custom_column'] ) && isset( $columns[ $field['custom_column'] ] ) )
			{
				// Insert after specific column

				$new_columns = [];

				foreach ( $columns as $key => $value )
				{
					$new_columns[$key] = $value;

					if ( $key === $field['custom_column'] )
					{
						$new_columns[ $field['id'] ] = $label;
					}
				}

				$columns = $new_columns;
			}
		}

		// -------------------------------------------------------------------------

		return $columns;

	}
	// admin_add_term_columns()



	/**
	 * @internal
	 */
	public function admin_add_term_column_content( $content, $column_name, $term_id )
	{

		foreach ( $this->meta_fields as $field )
		{
			if ( empty( $field['id'] ) || $column_name !== $field['id'] )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$value = get_term_meta( $term_id, $field['id'], true );

			// -----------------------------------------------------------------------

			switch ( $field['type'] )
			{
				case 'image':

					if ( $image_url = $this->admin_field_image_url( $value ) )
					{
						$content = sprintf(
							'<img%s>',
							F::html_attributes([
								'src'		=> $image_url,
								'alt'		=> '',
								'width'	=> 50,
								'height'=> 50,
								'style'	=> [
									'max-width'		=> '50px',
									'max-height'	=> '50px',
								],
							])
						);
					}

					break;

				// ---------------------------------------------------------------------

				// Add other field types as needed

				default:

					$content = ! empty( $value ) ? esc_html( $value ) : '&mdash;';

					break;
			}
		}

		// -------------------------------------------------------------------------

		return $content;

	}
	// admin_add_term_column_content()



	/**
	 * @internal
	 */
	private function admin_field_text( $field_id, $value )
	{

		return sprintf(
			'<input%s>',
			F::html_attributes([
				'type'	=> 'text',
				'id'		=> sprintf( 'term-%s', esc_attr( $field_id ) ),
				'name'	=> sprintf( 'term_meta[%s]', esc_attr( $field_id ) ),
				'value'	=> $value,
			])
		);

	}
	// admin_field_text()



	/**
	 * @internal
	 */
	private function admin_field_image( $field_id, $value )
	{

		$image_url = $this->admin_field_image_url( $value );

		// -------------------------------------------------------------------------

		ob_start();

		?>
		<div class="term-image-wrap">
			<div class="term-image-preview" style="margin-bottom:10px;">
				<img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 100%; display: <?php echo empty( $image_url ) ? 'none' : 'block'; ?>;">
			</div>
			<input type="hidden" id="term-<?php echo esc_attr( $field_id ); ?>" name="term_meta[<?php echo esc_attr( $field_id ); ?>]" value="<?php echo esc_attr( $value ); ?>">
			<button type="button" class="button upload-term-image"><?php DA()->_e('Upload Image'); ?></button>
			<button type="button" class="button remove-term-image" style="display: <?php echo empty( $value ) ? 'none' : 'inline-block'; ?>;"><?php DA()->_e('Remove Image'); ?></button>
		</div>
		<?php

		// -------------------------------------------------------------------------

		return F::minify( ob_get_clean() );

	}
	// admin_field_image()



	/**
	 * @internal
	 */
	private function admin_field_image_url( $attachment_id, $size = 'thumbnail' )
	{

		$attachment_id = absint( $attachment_id );

		if ( 		$attachment_id
				 && $url = wp_get_attachment_image_url( $attachment_id, $size ) )
		{
			return $url;
		}

		// -------------------------------------------------------------------------

		return $this->admin_default_image_url();

	}
	// admin_field_image_url()



	/**
	 * @internal
	 */
	private function admin_default_image_url( $args = [] )
	{

		$defaults = [
			'width'					=> 400,
			'height'				=> 300,
			'add_icon'			=> true,
			'add_gradient'	=> false,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( $add_icon )
		{
			$icon	= sprintf(
				'<path%s/>',
				F::html_attributes([
					'transform'				=> 'translate(100,50) scale(8)',
					'stroke'					=> 'currentColor',
					'stroke-width'		=> '2',
					'stroke-linecap'	=> 'round',
					'stroke-linejoin'	=> 'round',
					'style'		=> [
						'color' => 'rgba(0, 0, 0, 0.3)',
					],

					'd' => 'm3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z',
				])
			);
		}

		// -------------------------------------------------------------------------

		$svg_attr = [
			'xmlns'		=> 'http://www.w3.org/2000/svg',
			'viewBox'	=> "0 0 {$width} {$height}",
			'width'		=> $width,
			'height'	=> $height,
			'fill'		=> 'none',
			'style'		=> [
				'background-color' => 'rgba(0, 0, 0, 0.05)',
			],
		];

		// -------------------------------------------------------------------------

		ob_start();

		?>
		<svg<?php echo F::html_attributes( $svg_attr ); ?>>
			<?php if ( ! empty( $icon ) ) { echo $icon; } ?>
		</svg>
		<?php

		$svg = trim( ob_get_clean() );

		// -------------------------------------------------------------------------

		return sprintf( 'data:image/svg+xml;base64,%s', base64_encode( $svg ) );

	}
	// admin_default_image_url()



	/**
	 * @internal
	 */
	public function admin_enqueue_scripts( $hook )
	{

		if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$screen = get_current_screen();

		if ( ! $screen || $this->taxonomy !== $screen->taxonomy )
		{
			return;
		}

		// -------------------------------------------------------------------------

		wp_enqueue_media();

		wp_add_inline_script( 'jquery', $this->admin_get_term_image_js(), 'after' );

	}
	// admin_enqueue_scripts()



	/**
	 * @internal
	 */
	private function admin_get_term_image_js()
	{

		ob_start();

		?>
		jQuery(document).ready(function($) {

			// Upload image

			$(document).on('click', '.upload-term-image', function(e) {

				e.preventDefault();

				var button = $(this);
				var wrap = button.closest('.term-image-wrap');
				var preview = wrap.find('.term-image-preview img');
				var input = wrap.find('input[type="hidden"]');
				var removeButton = wrap.find('.remove-term-image');

				var frame = wp.media({
					title: '<?php DA()->_e('Select or Upload Image'); ?>',
					button: {
						text: '<?php DA()->_e('Use this image'); ?>'
					},
					multiple: false
				});

				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					preview.attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
					preview.show();
					input.val(attachment.id);
					removeButton.show();
				});

				frame.open();

			});

			// -----------------------------------------------------------------------

			// Remove image

			$(document).on('click', '.remove-term-image', function(e) {

				e.preventDefault();

				var button = $(this);
				var wrap = button.closest('.term-image-wrap');
				var preview = wrap.find('.term-image-preview img');
				var input = wrap.find('input[type="hidden"]');

				preview.attr('src', '');
				preview.hide();
				input.val('');
				button.hide();

			});

		});
		<?php

		return F::minify( ob_get_clean() );

	}
	// admin_get_term_image_js()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * admin - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// class Taxonomy_Term_Meta
