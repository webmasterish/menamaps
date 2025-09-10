<?php

namespace DotAim\Admin;

use DotAim\F;

/**
 * @internal
 */
abstract class Panel
{

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * PROPERTIES - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public $component;
	public $core;
	public $class_name;
	public $css_class;
	public $args;
	public $prefix;
	public $transient_prefix;
	public $cron_job_hook_prefix;
	public $ajax_action;
	public $ajax_nonce_name;
	public $ajax_nonce;
	public $settings;
	public $user_prefs;
	public $field_select_options_dashicons;
	public $field_select_options_post_types;
	public $field_select_options_taxonomies;
	public $field_select_options_taxonomy_terms;
	public $field_select_options_users;
	public $field_select_options_users_roles;
	public $field_select_options_users_capabilities;

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
	 * @since 1.0.0
	 */
	public function __construct( $component, $args )
	{

		$this->component	= $component;
		$this->core				= $component->core;
		$this->class_name	= $this->core->NS()->class_name( get_class( $this ) );

		// -------------------------------------------------------------------------

		$class_name_for_css	= strtolower( $this->core->NS()->class_name( __CLASS__ ) );
		$this->css_class		= "{$component->css_class}_{$class_name_for_css}";

		// -------------------------------------------------------------------------

		$this->args		= $args;
		//$config			= $this->config();
		//$this->args	= F::parse_args_deep( $config, $args );

		// -------------------------------------------------------------------------

		$this->prefix								= "{$this->component->prefix}{$this->id}_";
		$this->transient_prefix			= "{$this->core->prefix}{$this->prefix}";
		$this->cron_job_hook_prefix	= "{$this->core->prefix}{$this->prefix}";

		// -------------------------------------------------------------------------

		$fn = 'apply';

		if ( method_exists( $this, $fn ) )
		{
			$this->{$fn}();
		}

		// -------------------------------------------------------------------------

		if ( is_admin() )
		{
			$fn_name								= 'ajax_process';
			$this->ajax_action			= "{$this->prefix}{$fn_name}";
			$this->ajax_nonce_name	= "{$this->ajax_action}_nonce";
			$this->ajax_nonce				= wp_create_nonce( $this->ajax_nonce_name );

			$fn = [ $this, $fn_name ];

			if ( is_callable( $fn ) )
			{
				add_action( "wp_ajax_{$this->ajax_action}", $fn );
			}
		}

	}
	// __construct()



	/**
	 * @since 1.0.0
	 */
	public function __get( $name )
	{

		if ( ! isset( $this->{$name} ) && isset( $this->args[ $name ] ) )
		{
			return $this->args[ $name ];
		}

	}
	// __get()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MAGIC METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG METHODS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	abstract public function config();

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * CONFIG METHODS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function __( ...$args ) { return $this->core->__( ...$args ); }



	/**
	 * @internal
	 */
	protected function _e( ...$args ) { return $this->core->_e( ...$args ); }



	/**
	 * @internal
	 */
	protected function _n( ...$args ) { return $this->core->_n( ...$args ); }



	/**
	 * @internal
	 */
	protected function debug_log( $val, $title = '', $backtrace_index = 3 )
	{

		return $this->core->debug_log( $val, $title, $backtrace_index );

	}
	// debug_log()



	/**
	 * @internal
	 */
	protected function get_performed_in_sec_msg( $sec = null )
	{

		if ( ! isset( $sec ) )
		{
			$sec = timer_stop();
		}

		// -------------------------------------------------------------------------

		return sprintf(
			$this->_n('Performed in %d second', 'Performed in %d seconds', $sec),
			$sec
		);

	}
	// get_performed_in_sec_msg()



	/**
	 * @internal
	 */
	protected function in_component_page()
	{

		return $this->component->in_component_page();

	}
	// in_component_page()



	/**
	 * @internal
	 */
	public function get_section_title( $section )
	{

		return F::array_get( $section, ['title'], F::humanize( $section['id'] ) );

	}
	// get_section_title()



	/**
	 * @since 1.0.0
	 */
	public function settings( $path = null, $default = null, $stripslashes = true )
	{

		if ( ! isset( $this->settings ) )
		{
			$this->settings = $this->component->options_settings( [$this->id], null, $stripslashes );
		}

		// -------------------------------------------------------------------------

		return $path ? F::array_get( $this->settings, $path, $default ) : $this->settings;

	}
	// settings()



	/**
	 * @internal
	 */
	protected function user_prefs( $path = null, $default = null )
	{

		if ( ! isset( $this->user_prefs ) )
		{
			$this->user_prefs = $this->component->options_user_prefs(['panels', 'prefs', $this->id]);
		}

		// -------------------------------------------------------------------------

		return $path ? F::array_get( $this->user_prefs, $path, $default ) : $this->user_prefs;

	}
	// user_prefs()



	/**
	 * @since 1.0.0
	 */
	protected function populate_sections_args( $sections )
	{

		$section_defaults = [
			'ajax' => true,
		];

		foreach ( $sections as &$section )
		{
			if ( empty( $section['id'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$section = F::parse_args( $section, $section_defaults );

			// -----------------------------------------------------------------------

			if ( ! empty( $section['content'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$fn_name = "{$section['id']}_content";

			// -----------------------------------------------------------------------

			if ( ! method_exists( $this, $fn_name ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( $content = $this->{$fn_name}() )
			{
				$section['content'] = $content;
			}
		}

		// -------------------------------------------------------------------------

		return $sections;

	}
	// populate_sections_args()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * HELPERS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MARKUP - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function markup()
	{

		if ( ! current_user_can( $this->capability ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->args = wp_parse_args( $this->config(), $this->args );

		if ( empty( $this->args['content'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_string( $this->args['content'] ) )
		{
			// this is html or any valid markup passed as string

			$content = [ $this->args['content'] ];
		}
		elseif ( is_array( $this->args['content'] ) )
		{
			// this is the case of sections

			$content = [];

			foreach ( $this->args['content'] as $section_args )
			{
				if ( $section_markup = $this->section_markup( $section_args ) )
				{
					$content[] = $section_markup;
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $content ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$content = implode( $content );

		// -------------------------------------------------------------------------

		$attr = F::html_attributes([
			'id'		=> "{$this->css_class}_{$this->id}",
			'class' => [
				"{$this->css_class}",
				"{$this->css_class}_{$this->id}",
			],
		]);

		// -------------------------------------------------------------------------

		return "<div{$attr}>{$content}</div>";

	}
	// markup()



	/**
	 * @since 1.0.0
	 */
	public function section_markup( $args )
	{

		$defaults = [
			'id'				=> '',
			'title'			=> '',
			'icon'			=> '',
			'before'		=> '',
			'content'		=> null, // html string | array of fields
			'submit'		=> ['bottom' => $this->component->has_options],
			'after'			=> '',
			'stacked'		=> false,
			'collapsed'	=> null,
			'ajax'			=> true,
		];

		// -------------------------------------------------------------------------

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! $id || ! $content )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! strlen( $title ) )
		{
			$title = $args['title'] = F::humanize( $id );
		}

		// -------------------------------------------------------------------------

		if ( is_array( $content ) )
		{
			$content = $this->table_markup([
				'fields'						=> $content,
				'field_id_prefix'		=> "{$this->id}_{$id}_",
				'field_name_prefix'	=> "{$this->id}[{$id}]",
				'stacked'						=> $stacked,
				'attr'							=> ['data-section_id' => $id],
			]);
		}

		// -------------------------------------------------------------------------

		$content_markup = [];

		// -------------------------------------------------------------------------

		if ( $before )
		{
			$content_markup[] = $before;
		}

		// -------------------------------------------------------------------------

		if ( $content )
		{
			$submit_markup = ! empty( $submit ) ? $this->submit_markup( $args ) : null;

			// -----------------------------------------------------------------------

			if ( $submit_markup && F::array_get( $submit, ['top'] ) )
			{
				$content_markup[] = $submit_markup;
			}

			// -----------------------------------------------------------------------

			$content_markup[] = $content;

			// -----------------------------------------------------------------------

			if ( $submit_markup && F::array_get( $submit, ['bottom'] ) )
			{
				$content_markup[] = $submit_markup;
			}
		}
		else
		{
			$content_markup[] = $this->component->content_none_markup();
		}

		// -------------------------------------------------------------------------

		if ( empty( $content_markup ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $after )
		{
			$content_markup[] = $after;
		}

		// -------------------------------------------------------------------------

		$heading_attr = ['class' => ['section_head', 'dashicons-before'] ];

		if ( $icon )
		{
			$icon		= "<span class=\"dashicons {$icon}\"></span>";
			$title	= "{$icon}{$title}";
		}

		$heading_attr = F::html_attributes( $heading_attr );

		// -------------------------------------------------------------------------

		$attr = [
			'id'							=> "{$this->css_class}_section_{$id}",
			'class'						=> ["{$this->css_class}_section", 'collapsible'],
			'data-panel_id'		=> $this->id,
			'data-section_id'	=> $id,
		];

		if ( ! isset( $collapsed ) )
		{
			$collapsed = in_array( $id, $this->user_prefs(['sections', 'collapsed'], []) );
		}

		if ( $collapsed )
		{
			$attr['class'][] = 'collapsed';
		}

		$attr = F::html_attributes( $attr );

		// -------------------------------------------------------------------------

		ob_start();

		// -------------------------------------------------------------------------

		?>

		<div<?php echo $attr; ?>>

			<h2<?php echo $heading_attr; ?>><?php echo $title; ?></h2>

			<div class="section_content">

				<?php echo implode( $content_markup ); ?>

			</div>

		</div>

		<?php

		// -------------------------------------------------------------------------

		return ob_get_clean();

	}
	// section_markup()



	/**
	 * @since 1.0.0
	 */
	public function table_markup( $args )
	{

		$defaults = [
			'fields'						=> [],
			'field_id_prefix'		=> '',
			'field_name_prefix'	=> '',
			'stacked'						=> false,
			'attr'							=> [
				'class'					=> ['form-table', 'fixed'],
				'data-panel_id'	=> $this->id,
			],
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( empty( $fields ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$rows = [];

		foreach ( $fields as $field )
		{
			if ( empty( $field['id'] ) || empty( $field['type'] ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( ! F::array_get( $field, ['attr', 'id'] ) )
			{
				F::array_set( $field, ['attr', 'id'], "{$field_id_prefix}{$field['id']}" );
			}

			// -----------------------------------------------------------------------

			if ( ! isset( $field['content'] ) )
			{
				if ( ! F::array_get( $field, ['attr', 'name'] ) )
				{
					F::array_set( $field, ['attr', 'name'], "{$field_name_prefix}[{$field['id']}]" );
				}

				// ---------------------------------------------------------------------

				if ( ! F::array_get( $field, ['attr', 'data-section_id'] ) )
				{
					F::array_set( $field, ['attr', 'data-section_id'], F::array_get( $attr, ['data-section_id'] ) );
				}

				// ---------------------------------------------------------------------

				if ( ! F::array_get( $field, ['attr', 'data-key'] ) )
				{
					F::array_set( $field, ['attr', 'data-key'], $field['id'] );
				}

				// ---------------------------------------------------------------------

				if ( ! isset( $field['value'] ) )
				{
					if ( $field_name = F::array_get( $field, ['attr', 'name'] ) )
					{
						preg_match_all( '/\[(.*?)\]/', $field_name, $name_paths );

						if ( ! empty( $name_paths[ 1 ] ) )
						{
							$field['value'] = $this->settings( $name_paths[ 1 ] );
						}
					}
				}

				// ---------------------------------------------------------------------

				if ( ! $field['content'] = $this->field_markup( $field ) )
				{
					continue;
				}
			}
			else
			{
				// @notes: this mainly for the case of conditional field type html

				if ( 		! F::array_get( $field, ['attr', 'name'] )
						 && ! F::array_get( $field, ['attr', 'data-name'] ) )
				{
					F::array_set( $field, ['attr', 'data-name'], "{$field_name_prefix}[{$field['id']}]" );
				}
			}

			// -----------------------------------------------------------------------

			$label = '';

			if ( ! empty( $field['label'] ) )
			{
				$label_attr = [];

				if ( 'group' === $field['type'] )
				{
					$label_attr['class'] = ['group_label'];
				}
				else
				{
					$label_attr['for'] = F::array_get( $field, ['attr', 'id'] );
				}

				$label = sprintf(
					'<label%s>%s</label>',
					F::html_attributes( $label_attr ),
					stripslashes( $field['label'] )
				);
			}

			// -----------------------------------------------------------------------

			$td_content = [];
			$td_attr		= [];
			$th_content = [];
			$th_attr		= ['scope' => 'row'];

			if ( ! empty( $field['colspan_full'] ) )
			{
				if ( $label )
				{
					$td_content[] = $label;
				}

				// ---------------------------------------------------------------------

				$td_attr['colspan'] = 2;
			}
			else
			{
				if ( ! empty( $label ) )
				{
					$th_content[] = $label;
				}
				else
				{
					if ( ! $stacked )
					{
						$th_content[] = '&nbsp;';
					}
				}
			}

			// -----------------------------------------------------------------------

			if ( $before = F::array_get( $field, ['before'] ) )
			{
				$td_content[] = $before;
			}

			// -----------------------------------------------------------------------

			if ( empty( $field['desc'] ) )
			{
				$td_content[] = $field['content'];
			}
			else
			{
				if ( 'checkbox' === $field['type'] )
				{
					$td_content[] = "<label>{$field['content']} {$field['desc']}</label>";
				}
				else
				{
					$td_content[] = $field['content'];

					// -------------------------------------------------------------------

					$desc = [];

					if ( ! empty( $field['desc'] ) )
					{
						$desc[] = sprintf(
							'<p class="description">%s</p>',
							$field['desc']
						);
					}

					if ( ! empty( $field['sc_allowed'] ) )
					{
						$desc[] = sprintf(
							'<p class="description sc_allowed">%s</p>',
							$this->__('Shortcodes allowed.')
						);
					}

					if ( ! empty( $desc ) )
					{
						$td_content[] = implode( $desc );
					}
				}
			}

			// -----------------------------------------------------------------------

			if ( $after = F::array_get( $field, ['after'] ) )
			{
				$td_content[] = $after;
			}

			// -----------------------------------------------------------------------

			$tr_content = [];
			$tr_attr		= ['valign' => 'top'];

			if ( $conditional_display_attr = $this->field_conditional_display_attr( $field ) )
			{
				$tr_attr['class']											= ['hide-if-js', 'conditional_display'];
				$tr_attr['data-conditional_display']	= $conditional_display_attr;
			}

			if ( ! empty( $th_content ) )
			{
				$tr_content[] = sprintf(
					'<th%s>%s</th>',
					F::html_attributes( $th_attr ),
					implode( $th_content )
				);
			}

			if ( ! empty( $td_content ) )
			{
				$tr_content[] = sprintf(
					'<td%s>%s</td>',
					F::html_attributes( $td_attr ),
					implode( $td_content )
				);
			}

			if ( ! empty( $tr_content ) )
			{
				$rows[] = sprintf(
					'<tr%s>%s</tr>',
					F::html_attributes( $tr_attr ),
					implode( $tr_content )
				);
			}
		}

		// -------------------------------------------------------------------------

		if ( empty( $rows ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $stacked ) )
		{
			$attr['class'][] = 'stacked';
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<table%s><tbody>%s</tbody></table>',
			F::html_attributes( $attr ),
			implode( $rows )
		);

	}
	// table_markup()



	/**
	 * @since 1.0.0
	 */
	public function submit_markup( $section_args = [] )
	{

		$section_id			= F::array_get( $section_args, ['id'], '' );
		$section_title	= F::array_get( $section_args, ['title'], '' );
		$section_submit	= F::array_get( $section_args, ['submit'], [] );

		// -------------------------------------------------------------------------

		$defaults = [
			'top'						=> false,
			'bottom'				=> $this->component->has_options,
			'panel_id'			=> $this->id,
			'section_id'		=> $section_id,
			'action_suffix'	=> "_{$this->id}_{$section_id}",
			'ajax'					=> isset( $section_args['ajax'] ) ? $section_args['ajax'] : true,
			'buttons'				=> [],
		];

		// -------------------------------------------------------------------------

		if ( $this->component->has_options )
		{
			$defaults['buttons']['left'] = [

				[
					'id'		=> 'save',
					'text'	=> sprintf( $this->__('Save %s'), $section_title ),
					'attr'	=> ['class' => ['button', 'button-primary']],
				],

				// ---------------------------------------------------------------------

				[
					'id'					=> 'reset',
					'text'				=> sprintf( $this->__('Reset %s'), $section_title ),
					'attr'				=> ['class' => ['button', 'button-secondary']],
					'confirm_msg'	=> sprintf(
						$this->__(
							'This action is irreversible and will delete all related database entries.' .
							'\n\n' .
							'Are you sure you want to Reset %s Options?'
						),
						$section_title
					),
				],

			];
		}

		// -------------------------------------------------------------------------

		$args = F::parse_args_deep( $section_submit, $defaults );

		// -------------------------------------------------------------------------

		if ( ! empty( $args['top'] ) || ! empty( $args['bottom'] ) )
		{
			return $this->component->submit_markup( $args );
		}

	}
	// submit_markup()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * MARKUP - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FIELDS - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @since 1.0.0
	 */
	public function field_markup( $args )
	{

		if ( empty( $args['id'] ) || empty( $args['type'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$aliases = [
			'text'	=> 'textbox',
			'image'	=> 'media',
		];

		if ( isset( $aliases[ $args['type'] ] ) )
		{
			$args['type'] = $aliases[ $args['type'] ];
		}

		// -------------------------------------------------------------------------

		$fn = [ $this, "field_{$args['type']}" ];

		if ( is_callable( $fn ) )
		{
			return call_user_func( $fn, $args );
		}

	}
	// field_markup()



	/**
	 * @since 1.0.0
	 */
	public function field_input( $args = [] )
	{

		$defaults = [
			'attr'					=> [],
			'value'					=> '',
			'stripslashes'	=> true,
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		// @todo: remove the use of id and name and only use attr

		if ( ! isset( $attr['id'] ) && isset( $id ) )
		{
			$attr['id'] = $id;
		}

		if ( ! isset( $attr['name'] ) && isset( $name ) )
		{
			$attr['name'] = $name;
		}

		// -------------------------------------------------------------------------

		if ( ! is_null( $value ) && strlen( $value ) )
		{
			if ( $stripslashes )
			{
				$value = stripslashes( $value );
			}
		}
		else
		{
			$value = isset( $default ) && strlen( $default ) ? $default : null;
		}

		$attr['value'] = $value;

		// -------------------------------------------------------------------------

		$input = sprintf('<input%s>', F::html_attributes( $attr ) );

		if ( empty( $button_args ) )
		{
			return $input;
		}

		// -------------------------------------------------------------------------

		$button_text = F::array_get( $button_args, ['text'] );

		if ( $button_attr = F::array_get( $button_args, ['attr'] ) )
		{
			$button_attr = F::html_attributes( $button_attr );
		}

		$button = "<button{$button_attr}>{$button_text}</button>";

		// -------------------------------------------------------------------------

		return "<div class=\"input_with_button\">{$input}{$button}</div>";

	}
	// field_input()



	/**
	 * @since 1.0.0
	 */
	public function field_textbox( $args = [] )
	{

		$defaults = [
			'attr' => [
				'type'	=> 'text',
				'class'	=> ['regular-text'], // tiny-text | small-text | regular-text | large-text
			],
		];

		$args = F::parse_args_deep( $args, $defaults );

		// -------------------------------------------------------------------------

		return $this->field_input( $args );

	}
	// field_textbox()



	/**
	 * @since 1.0.0
	 */
	public function field_url( $args = [] )
	{

		F::array_set( $args, ['attr', 'type'], 'url' );

		// -------------------------------------------------------------------------

		return $this->field_textbox( $args );

	}
	// field_url()



	/**
	 * @since 1.0.0
	 */
	public function field_password( $args = [] )
	{

		F::array_set( $args, ['attr', 'type'], 'password' );

		// -------------------------------------------------------------------------

		return $this->field_textbox( $args );

	}
	// field_password()



	/**
	 * @since 1.0.0
	 */
	public function field_number( $args = [] )
	{

		$defaults = [
			'attr' => ['class' => 'small-text'],
		];

		$args = F::parse_args_deep( $args, $defaults );

		// -------------------------------------------------------------------------

		F::array_set( $args, ['attr', 'type'], 'number' );

		// -------------------------------------------------------------------------

		return $this->field_textbox( $args );

	}
	// field_number()



	/**
	 * @since 1.0.0
	 */
	public function field_checkbox( $args = [] )
	{

		F::array_set( $args, ['attr', 'type'], 'checkbox' );

		// -------------------------------------------------------------------------

		if ( ! empty( $args['value'] ) )
		{
			F::array_set( $args, ['attr', 'checked'], 'checked' );

			unset( $args['value'] );
		}
		elseif ( ! empty( $args['default'] ) )
		{
			F::array_set( $args, ['attr', 'checked'], 'checked' );
		}

		// -------------------------------------------------------------------------

		return $this->field_input( $args );

	}
	// field_checkbox()



	/**
	 * @since 1.0.0
	 */
	public function field_textarea( $args = [] )
	{

		$defaults = [
			'attr'					=> ['class' => 'regular-text'], // small-text | regular-text | large-text
			'value'					=> '',
			'stripslashes'	=> true,
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		// @todo: remove the use of id and name and only use attr

		if ( ! isset( $attr['id'] ) && isset( $id ) )
		{
			$attr['id'] = $id;
		}

		if ( ! isset( $attr['name'] ) && isset( $name ) )
		{
			$attr['name'] = $name;
		}

		// -------------------------------------------------------------------------

		if ( is_string( $value ) && strlen( $value ) )
		{
			if ( $stripslashes )
			{
				$value = stripslashes( $value );
			}
		}
		else
		{
			$value = isset( $default ) ? $default : '';
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<textarea%s>%s</textarea>',
			F::html_attributes( $attr ),
			$value
		);

	}
	// field_textarea()



	/**
	 * @since 1.0.0
	 */
	public function field_media( $args = [] )
	{

		$defaults = [
			'attr' => [
				'class' => [
					'regular-text',
					"{$this->core->css_prefix}field_media",
				],
			],
			'button_args' => [
				//'text' => $this->__('Upload'),
				'attr' => [
					'class' => [
						'button',
						'dashicons-before',
						'dashicons-admin-media',
					],
				],
			],
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		return $this->field_textbox( $args );

	}
	// field_media()



	/**
	 * @since 1.0.0
	 */
	public function field_color( $args = [] )
	{

		$defaults = [
			'attr' => [
				'type'					=> 'text',
				'class'					=> ["{$this->core->css_prefix}field_color"],
				'data-palettes'	=> true,
			],
			'value'					=> '',
			'stripslashes'	=> true,
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! in_array( 'wp-color-picker', $attr['class'] ) )
		{
			$attr['class'][] = 'wp-color-picker';
		}

		// -------------------------------------------------------------------------

		if ( is_string( $value ) && strlen( $value ) )
		{
			if ( $stripslashes )
			{
				$value = stripslashes( $value );
			}
		}
		else
		{
			$value = isset( $default ) && strlen( $default ) ? $default : null;
		}

		$attr['value'] = $value;

		// -------------------------------------------------------------------------

		if ( isset( $default ) )
		{
			$attr['data-default-color'] = $default;
		}

		// -------------------------------------------------------------------------

		$args['attr'] = $attr;

		// -------------------------------------------------------------------------

		return $this->field_input( $args );

	}
	// field_color()



	/**
	 * @since 1.0.0
	 */
	public function field_select( $args = [] )
	{

		$defaults = [
			'attr'										=> [],
			'options'									=> [],
			'multiple'								=> false,
			'checkboxes'							=> false,
			'value'										=> null,
			'stripslashes'						=> true,
			'options_keys_as_values'	=> false, // used in the case of keyless array
			'add_option_class'				=> false, // adds value as option class (for styling options individually)
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( $checkboxes )
		{
			return $this->field_select_checkbox( $args );
		}

		// -------------------------------------------------------------------------

		// @todo: remove the use of id and name and only use attr

		if ( ! isset( $attr['id'] ) && isset( $id ) )
		{
			$attr['id'] = $id;
		}

		if ( ! isset( $attr['name'] ) && isset( $name ) )
		{
			$attr['name'] = $name;
		}

		if ( $multiple )
		{
			$attr['multiple'] = 'multiple';

			// -----------------------------------------------------------------------

			if ( ! F::ends_with( $attr['name'], '[]' ) )
			{
				$attr['name'] = "{$attr['name']}[]";
			}
		}

		// -------------------------------------------------------------------------

		if ( is_array( $value ) )
		{
			if ( empty( $value ) && isset( $default ) )
			{
				$value = $default;
			}
		}
		else
		{
			if ( ( is_null( $value ) || ! strlen( $value ) ) && isset( $default ) && strlen( $default ) )
			{
				$value = $default;
			}
		}

		if ( $value && $stripslashes )
		{
			$value = stripslashes_deep( $value );
		}

		// reset it to args so it can be passed to select_option_markup()
		$args['value'] = $value;

		// -------------------------------------------------------------------------

		$options_markup = [];

		if ( ! empty( $options ) )
		{
			foreach( $options as $key => $option )
			{
				if ( is_array( $option ) && ! empty( $option ) )
				{
					// optgroup case:
					// - $key is optgroup label
					// - option is array of options

					$optgroup_options_markup = [];

					foreach( $option as $optgroup_option_key => $optgroup_option )
					{
						if ( $option_markup = $this->field_select_option_markup( $optgroup_option_key, $optgroup_option, $args ) )
						{
							$optgroup_options_markup[] = $option_markup;
						}
					}

					if ( ! empty( $optgroup_options_markup ) )
					{
						$options_markup[] = sprintf(
							'<optgroup label="%s">%s</optgroup>',
							$key,
							implode( $optgroup_options_markup )
						);
					}
				}
				else
				{
					if ( $option_markup = $this->field_select_option_markup( $key, $option, $args ) )
					{
						$options_markup[] = $option_markup;
					}
				}
			}
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<select%s>%s</select>',
			F::html_attributes( $attr ),
			! empty( $options_markup ) ? implode( $options_markup ) : ''
		);

	}
	// field_select()



	/**
	 * @since 1.0.0
	 */
	protected function field_select_option_markup( $key, $option, $args )
	{

		extract( $args );

		// -------------------------------------------------------------------------

		$option_attr = [];

		// -------------------------------------------------------------------------

		$option_text					= $option;
		$option_value					= ! empty( $options_keys_as_values ) ? $option : $key;
		$option_attr['value']	= $option_value;

		// -------------------------------------------------------------------------

		if ( isset( $value ) )
		{
			if ( ! empty( $multiple ) && is_array( $value ) )
			{
				if ( in_array( $option_value, $value ) )
				{
					$option_attr['selected'] = 'selected';
				}
			}
			else
			{
				if ( $option_value == $value )
				{
					$option_attr['selected'] = 'selected';
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $add_option_class ) )
		{
			$option_attr['class'] = [ $option_value ];
		}

		// -------------------------------------------------------------------------

		return sprintf(
			'<option%s>%s</option>',
			F::html_attributes( $option_attr ),
			$option_text,
		);

	}
	// field_select_option_markup()



	/**
	 * @since 1.0.0
	 */
	public function field_multi_select( $args = [] )
	{

		$args['multiple'] = true;

		// -------------------------------------------------------------------------

		return $this->field_select( $args );

	}
	// field_multi_select()



	/**
	 * @since 1.0.0
	 */
	public function field_select_checkbox( $args = [] )
	{

		$defaults = [
			'attr'										=> ['class' => 'select_checkbox'],
			'options'									=> [],
			'multiple'								=> false,
			'column_count'						=> null, // 1 to ... use css to control display
			'value'										=> '',
			'stripslashes'						=> true,
			'options_keys_as_values'	=> false, // used in the case of keyless array
			'add_option_class'				=> false, // adds value as option class (for styling options individually)
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! isset( $attr['id'] ) && isset( $id ) )
		{
			$attr['id'] = $id;
		}

		// -------------------------------------------------------------------------

		$input_type = $multiple ? 'checkbox' : 'radio';
		$input_name = '';

		if ( isset( $attr['name'] ) )
		{
			$input_name = $multiple ? "{$attr['name']}[]" : $attr['name'];

			// -----------------------------------------------------------------------

			// @notes: $attr['name'] should not be set on container

			unset( $attr['name'] );
		}

		// -------------------------------------------------------------------------

		if ( is_array( $value ) )
		{
			if ( empty( $value ) && isset( $default ) )
			{
				$value = $default;
			}
		}
		else
		{
			if ( ( is_null( $value ) || ! strlen( $value ) ) && isset( $default ) )
			{
				if ( is_array( $default ) && ! empty( $default ) )
				{
					$value = $default;
				}
				elseif ( strlen( $default ) )
				{
					$value = $default;
				}
			}
		}

		if ( $value && $stripslashes )
		{
			$value = stripslashes_deep( $value );
		}

		// -------------------------------------------------------------------------

		$items = [];

		if ( ! empty( $options ) )
		{
			foreach( $options as $key => $option )
			{
				$option_value	= $key;
				$option_text	= $option;
				$input_attr		= [
					'type'								=> $input_type,
					'name'								=> $input_name,
					'value'								=> $option_value,
					'class'								=> ['select_checkbox_input'],
					'data-af_field_name'	=> $input_name, // used by array_field
				];

				// ---------------------------------------------------------------------

				if ( $multiple && is_array( $value ) )
				{
					if ( in_array( $option_value, $value ) )
					{
						$input_attr['checked'] = 'checked';
					}
				}
				else
				{
					if ( $option_value == $value )
					{
						$input_attr['checked'] = 'checked';
					}
				}

				// ---------------------------------------------------------------------

				$items[] = sprintf(
					'<li><label><input%s> %s</label></li>',
					F::html_attributes( $input_attr ),
					$option_text
				);
			}
		}

		// -------------------------------------------------------------------------

		// @consider not displaying if empty

		if ( ! empty( $items ) )
		{
			$content = '<ul>' . implode( $items ) . '</ul>';
		}
		else
		{
			$content = '<p class="nothing_found">' . $this->__('Nothing Found') . '</p>';
		}

		// -------------------------------------------------------------------------

		if ( empty( $attr['class'] ) )
		{
			$attr['class'] = [];
		}
		else
		{
			if ( ! is_array( $attr['class'] ) )
			{
				$attr['class'] = [ $attr['class'] ];
			}
		}

		if ( $multiple )
		{
			$attr['class'][] = 'multiple';
		}

		if ( $column_count )
		{
			$attr['class'][] = "multi_columns columns_{$column_count}";

			if ( empty( $attr['style'] ) )
			{
				$attr['style'] = ['column-count' => $column_count];
			}
			else
			{
				if ( is_array( $attr['style'] ) )
				{
					$attr['style']['column-count'] = $column_count;
				}
				else
				{
					$attr['style'] = "{$attr['style']};column-count:{$column_count}";
				}
			}

		}

		// -------------------------------------------------------------------------

		return sprintf('<div%s>%s</div>', F::html_attributes( $attr ), $content );

	}
	// field_select_checkbox()



	/**
	 * @since 1.0.0
	 */
	public function field_multi_select_checkbox( $args = [] )
	{

		$args['multiple'] = true;

		// -------------------------------------------------------------------------

		return $this->field_select_checkbox( $args );

	}
	// field_multi_select_checkbox()



	/**
	 * @since 1.0.0
	 */
	public function field_select_pre_populated( $args = [] )
	{

		$defaults = [
			'data'					=> null, // post_types | taxonomies | posts | terms | users | users_roles | users_capabilities | callback_fn
			'data_args'			=> null, // function args
			'data_exclude'	=> null, // exclude from fetched data - it's not rellated to query...
			'data_prepend'	=> null, // prepend options such as "--- Select ---" - more likely used with regular/single select
			'data_append'		=> null, // append options - more likely used with regular/single select
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( ! isset( $args['options'] ) && ! empty( $data ) )
		{
			$args['options'] = [];

			$fn = [ $this, "field_select_options_{$data}" ];

			if ( is_callable( $fn ) )
			{
				if ( $options = call_user_func( $fn, $data_args ) )
				{
					$args['options'] = $options;
				}
			}
		}

		// -------------------------------------------------------------------------

		if ( ! empty( $args['options'] ) && ! empty( $data_exclude ) )
		{
			if ( ! is_array( $args['data_exclude'] ) )
			{
				$data_exclude = preg_split( "/[\s,]+/", $data_exclude, null, PREG_SPLIT_NO_EMPTY );
			}

			// -----------------------------------------------------------------------

			foreach ( $args['options'] as $k => $v )
			{
				if ( in_array( $k, $data_exclude ) )
				{
					unset( $args['options'][ $k ] );
				}
			}
		}

		// -------------------------------------------------------------------------

		// prepend options - such as "--- Select ---"

		if ( ! empty( $data_prepend ) && is_array( $data_prepend ) )
		{
			// @notes array_merge causes re-indexing, using "+" instead

			$args['options'] = $data_prepend + $args['options'];
		}

		// -------------------------------------------------------------------------

		// append options

		if ( ! empty( $data_append ) && is_array( $data_append ) )
		{
			$args['options'] = $args['options'] + $data_append;
		}

		// -------------------------------------------------------------------------

		return $this->field_select( $args );

	}
	// field_select_pre_populated()



	/**
	 * @since 1.0.0
	 */
	public function field_select_dashicons( $args = [] )
	{

		$args['data'] = 'dashicons';

		// -------------------------------------------------------------------------

		return $this->field_select_pre_populated( $args );

	}
	// field_select_dashicons()



	/**
	 * ref:
	 * - https://github.com/WordPress/dashicons
	 * - https://developer.wordpress.org/resource/dashicons
	 */
	public function field_select_options_dashicons()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		if ( ! defined( 'ABSPATH' ) || ! defined( 'WPINC' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$css_file = path_join( ABSPATH, path_join( WPINC, 'css/dashicons.css' ) );

		if ( ! is_readable( $css_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $file_contents = file_get_contents( $css_file ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		// find regular css classes such as:

		/*
		.dashicons-menu:before {
			content:"\f333";
		}
		*/

		$css_class_prefix = 'dashicons';

		$match_css_class = '(\.?' . addcslashes( $css_class_prefix, '-' ) . '.*?)';

		$pattern = 	'/' .
			$match_css_class . '\s?\:before' .
			'\s*' .
			'\{\s*content' .
		'/';

		preg_match_all( $pattern, $file_contents, $matches );

		if ( ! empty( $matches[1] ) )
		{
			$css_classes = $matches[1];
		}

		// -------------------------------------------------------------------------

		// find combined css classes such as:

		/*
		.dashicons-admin-links:before,
		.dashicons-format-links:before {
			content: "\f103";
		}

		.dashicons-admin-post:before,
		.dashicons-format-standard:before {
			content: "\f109";
		}

		.dashicons-welcome-write-blog:before,
		.dashicons-welcome-edit-page:before {
			content:"\f119";
		}
		*/

		$pattern = 	'/' .
			$match_css_class . '\s?\:before,' .
			'\s*' .
			$match_css_class . '\s?\:before' .
			'\s*' .
			'\{\s*content' .
		'/';

		preg_match_all( $pattern, $file_contents, $matches );

		/*
		@notes:
			the result would be something like this:

				Array
				(
					[0] => Array
						(
							[0] => .dashicons-admin-links:before,
				.dashicons-format-links:before {
					content
							[1] => .dashicons-admin-post:before,
				.dashicons-format-standard:before {
					content
							[2] => .dashicons-welcome-write-blog:before,
				.dashicons-welcome-edit-page:before {
					content
						)

					[1] => Array
						(
							[0] => .dashicons-admin-links
							[1] => .dashicons-admin-post
							[2] => .dashicons-welcome-write-blog
						)

					[2] => Array
						(
							[0] => .dashicons-format-links
							[1] => .dashicons-format-standard
							[2] => .dashicons-welcome-edit-page
						)

				)
		*/

		if ( ! empty( $matches ) )
		{
			foreach ( $matches as $k => $match )
			{
				if ( 	0 === $k || ! ( is_array( $match ) && $match ) )
				{
					continue;
				}

				// ---------------------------------------------------------------------

				foreach ( $match as $css_class )
				{
					$css_classes[] = $css_class;
				}
			}
		}

		// -------------------------------------------------------------------------

		// sort them alphabetically

		sort( $css_classes );

		// -------------------------------------------------------------------------

		$out = [];

		foreach ( $css_classes as $css_class )
		{
			if ( ! $css_class )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// set option value by removing the leading "." of the css class

			$value = str_replace( '.', '', $css_class );

			// -----------------------------------------------------------------------

			// set option text by humanizing option value

			$text = str_replace(
				[ $css_class_prefix . '-', '-' ],
				[ '', ' ' ],
				$value
			);

			$text = ucwords( $text );

			// -----------------------------------------------------------------------

			$out[ $value ] = $text;
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = $out;

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_dashicons()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_post_types( $args = [] )
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$defaults = [
			'public' => true,
		];

		$args = wp_parse_args( $args, $defaults );

		// -------------------------------------------------------------------------

		if ( ! $post_types = get_post_types( $args, 'objects' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $post_types as $post_type )
		{
			$this->{__FUNCTION__}[ $post_type->name ] = $post_type->labels->singular_name;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_post_types()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_posts( $args = [] )
	{

		if ( ! $posts = get_posts( $args ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$out = [];

		foreach ( $posts as $post )
		{
			$out[ $post->ID ] = apply_filters( 'the_title', $post->post_title );
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// field_select_options_posts()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_pages( $args = [] )
	{

		$args['post_type'] = 'page';

		// -------------------------------------------------------------------------

		return $this->field_select_options_posts( $args );

	}
	// field_select_options_pages()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_taxonomies( $args = [] )
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$defaults = [
			'public'	=> true,
			'show_ui'	=> true
		];

		$args = wp_parse_args( $args, $defaults );

		// -------------------------------------------------------------------------

		if ( ! $taxonomies = get_taxonomies( $args, 'objects' ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $taxonomies as $taxonomy )
		{
			$this->{__FUNCTION__}[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_taxonomies()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_taxonomy_terms( $args = [] )
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$defaults = [
			'taxonomy'		=> '',
			'key_field'		=> 'term_id',
			'terms_args'	=> [
				'hide_empty' => false,
				/* ref:
				'taxonomy'			=> '',
				'orderby'       => 'name',
				'order'         => 'ASC',
				'hide_empty'    => true,
				'exclude'       => [],
				'exclude_tree'  => [],
				'include'       => [],
				'number'        => '',
				'fields'        => 'all',
				'slug'          => '',
				'parent'        => '',
				'hierarchical'  => true,
				'child_of'      => 0,
				'get'           => '',
				'name__like'    => '',
				'pad_counts'    => false,
				'offset'        => '',
				'search'        => '',
				'cache_domain'  => 'core',
				*/
			],
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( empty( $terms_args['taxonomy'] ) && ! $taxonomy )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( empty( $terms_args['taxonomy'] ) )
		{
			$terms_args['taxonomy'] = $taxonomy;
		}

		// -------------------------------------------------------------------------

		if ( ! $terms = get_terms( $terms_args ) )
		{
			return;
		}

		if ( is_wp_error( $terms ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $terms as $key => $term )
		{
			if ( ! isset( $term->name ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			if ( isset( $term->{$key_field} ) )
			{
				$key = $term->{$key_field};
			}

			// -----------------------------------------------------------------------

			$this->{__FUNCTION__}[ $key ] = $term->name;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_taxonomy_terms()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_users( $args = [] )
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		if ( ! $users = get_users( $args ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $users as $user )
		{
			$this->{__FUNCTION__}[ $user->ID ] = $user->display_name;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_users()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_users_roles()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		if ( $roles = wp_roles() )
		{
			$this->{__FUNCTION__} = $roles->role_names;
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_users_roles()



	/**
	 * @since 1.0.0
	 */
	public function field_select_options_users_capabilities()
	{

		if ( isset( $this->{__FUNCTION__} ) )
		{
			return $this->{__FUNCTION__};
		}

		// -------------------------------------------------------------------------

		$this->{__FUNCTION__} = [];

		// -------------------------------------------------------------------------

		$roles = wp_roles();

		if ( empty( $roles->role_objects ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$capabilities = [];

		foreach ( $roles->role_objects as $key => $role )
		{
			if ( ! is_array( $role->capabilities ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			foreach ( $role->capabilities as $key => $value )
			{
				$capabilities[] = $key;
			}
		}

		$capabilities = array_unique( $capabilities );

		// -------------------------------------------------------------------------

		$old_levels = [
			'level_0',
			'level_1',
			'level_2',
			'level_3',
			'level_4',
			'level_5',
			'level_6',
			'level_7',
			'level_8',
			'level_9',
			'level_10'
		];

		$capabilities = array_diff( $capabilities, $old_levels );

		sort( $capabilities );

		// -------------------------------------------------------------------------

		foreach ( $capabilities as $capability )
		{
			$this->{__FUNCTION__}[ $capability ] = F::humanize( $capability );
		}

		// -------------------------------------------------------------------------

		return $this->{__FUNCTION__};

	}
	// field_select_options_users_capabilities()



	/**
	 * @since 1.0.0
	 */
	public function field_conditional_display_attr( $field )
	{

		if ( empty( $field['conditional'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$field_name = F::array_get(
			$field,
			['attr', 'name'],
			F::array_get( $field, ['attr', 'data-name'] )
		);

		if ( ! $field_name )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$tokens = [];

		foreach ( $field['conditional'] as $key => $condition )
		{
			if ( ! $selector = trim( $key ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$condition = is_array( $condition ) ? implode( '|', $condition ) : $condition;

			if ( ! $condition = trim( $condition ) )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			$tokens[ $selector ][] = $condition;
		}

		if ( empty( $tokens ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		foreach ( $tokens as $selector => $conditions )
		{
			if ( ! $conditions )
			{
				continue;
			}

			// -----------------------------------------------------------------------

			// @consider using field id instead of name

			// @notes:
			// replace last occurence of [$field_id] with selector id
			// tha means the conditional is only applied to same level fields

			$selector_name	= preg_replace('~^.*\[\K[^]]+~m', $selector, $field_name);
			$conditions			= implode( ',', array_unique( $conditions ) );

			// -----------------------------------------------------------------------

			// @notes:
			// separator used between selector and conditions is ":"
			// conditions "OR" separator is "|"
			//
			// this would look like something like this:
			// panel_id[section_id][field_id]:condition_1|condition_2

			$tokens[ $selector ] = "{{$selector_name}:{$conditions}}";
		}

		// -------------------------------------------------------------------------

		return implode( $tokens );

	}
	// field_conditional_display_attr()



	/**
	 * @since 1.0.0
	 */
	public function field_callback( $args = [] )
	{

		if ( empty( $args['callback'] ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( is_callable( $args['callback'] ) )
		{
			$callback_args = F::array_get( $args, ['callback_args'], $args );

			return call_user_func( $args['callback'], $callback_args );
		}

	}
	// field_callback()



	/**
	 * @since 1.0.0
	 */
	public function field_group( $args = [] )
	{

		$defaults = [
			'id'			=> '',
			'fields'	=> [],
			'stacked'	=> false,
		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		if ( empty( $id ) || empty( $fields ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( $attr_id = F::array_get( $args, ['attr', 'id'] ) )
		{
			$field_id_prefix = "{$attr_id}_";
		}
		else
		{
			$field_id_prefix = "{$id}_";
		}

		// -------------------------------------------------------------------------

		if ( $attr_name = F::array_get( $args, ['attr', 'name'] ) )
		{
			$field_name_prefix = $attr_name;
		}
		else
		{
			$field_name_prefix = "{$id}";
		}

		// -------------------------------------------------------------------------

		return $this->table_markup([
			'fields'						=> $fields,
			'field_id_prefix'		=> $field_id_prefix,
			'field_name_prefix'	=> $field_name_prefix,
			'stacked'						=> $stacked,
		]);

	}
	// field_group()



	/**
	 * @since 1.0.0
	 */
	public function field_array_field( $args = [] )
	{

		$defaults = [

			'id'						=> '',
			'fields'				=> [],
			'stacked'				=> false,
			'value'					=> null,
			'stripslashes'	=> true,
			'minify'				=> ! $this->core->is_local_dev(),

			// -----------------------------------------------------------------------

			// options used in $.dotaim_field_array_field jQuery plugin
			// they are added as data-options_${key}
			/*
			'options' => [
				'css_class'										=> 'array_field',
				'max_items'										=> 0,
				'item_header_field_selector'	=> ':input:first', // use something like '[data-key="field_id"]'
				'animation_speed'							=> 'fast',
			],
			*/

		];

		$args = F::parse_args_deep( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		$attr_name	= F::array_get( $args, ['attr', 'name'] );
		$attr_id		= F::array_get( $args, ['attr', 'id'] );

		// -------------------------------------------------------------------------

		$section_id	= F::array_get( $args, ['attr', 'data-section_id'] );
		$key				= F::array_get( $args, ['attr', 'data-key'] );

		$container_attr = [
			'id'											=> $attr_id,
			'class'										=> ['array_field'],
			'data-array_field'				=> true,
			'data-panel_id'						=> $this->id,
			'data-section_id'					=> $section_id,
			'data-key'								=> $key,
			'data-field_name_prefix'	=> "{$this->id}[{$section_id}][{$key}]", // $attr_name
			'data-field_id_prefix'		=> $attr_id,
		];

		if ( ! empty( $options ) )
		{
			foreach ( $options as $option_key => $option_value )
			{
				$container_attr["data-options_{$option_key}"] = $option_value;
			}
		}

		$container_attr = F::html_attributes( $container_attr );

		// -------------------------------------------------------------------------

		$template = '';

		$field_group_args = [
			'id'			=> $id,
			'fields'	=> $fields,
			'stacked'	=> $stacked,
			'attr'		=> [],
		];

		if ( $attr_name )
		{
			$field_group_args['attr']['name'] = "{$attr_name}";
		}

		if ( $attr_id )
		{
			$field_group_args['attr']['id'] = "{$attr_id}";
		}

		if ( $field_group = $this->field_group( $field_group_args ) )
		{
			$template = $this->field_array_field_item( $field_group );
		}

		// -------------------------------------------------------------------------

		$items = [];

		if ( ! empty( $value ) )
		{
			foreach ( $value as $item_index => $saved_fields )
			{
				$field_group_args = [
					'id'			=> $id,
					'fields'	=> $fields,
					'stacked'	=> $stacked,
					'attr'		=> [],
				];

				if ( $attr_name )
				{
					$field_group_args['attr']['name'] = "{$attr_name}[{$item_index}]";
				}

				if ( $attr_id )
				{
					$field_group_args['attr']['id'] = "{$attr_id}_{$item_index}";
				}

				if ( $field_group = $this->field_group( $field_group_args ) )
				{
					$items[] = $this->field_array_field_item( $field_group );
				}
			}
		}

		$items = ! empty( $items ) ? implode( $items ) : '';

		// -------------------------------------------------------------------------

		ob_start();

		?>

		<div<?php echo $container_attr; ?>>

			<template class="array_field_item_template">
				<?php echo $template; ?>
			</template>

			<ul class="array_field_list">
				<?php echo $items; ?>
			</ul>

			<div class="array_field_actions">
				<button data-action="item_add" class="button array_field_action" type="button">
					<?php $this->_e('Add New'); ?>
				</button>
				<button data-action="collapse_all" class="button array_field_action" type="button">
					<?php $this->_e('Collapse All'); ?>
				</button>
				<button data-action="delete_all" class="button array_field_action" type="button" data-confirm_message="<?php $this->_e('Are you sure you want to delete all fields?'); ?>">
					<?php $this->_e('Delete All'); ?>
				</button>
			</div><!-- .array_field_actions -->

		</div><!-- .array_field -->

		<p class="array_field_unsupported_notice hide-if-js">
			<?php $this->_e('Array Field Requires JavaScript.'); ?>
		</p>

		<?php

		$markup = ob_get_clean();

		// -------------------------------------------------------------------------

		return $minify ? F::minify( $markup ) : $markup;

	}
	// field_array_field()



	/**
	 * @since 1.0.0
	 */
	public function field_array_field_item( $field_group, $args = [] )
	{

		$defaults = [
			'collapsed'			=> true,	// @consider based on user_pref
			'item_actions'	=> false,	// don't display especially with nested array_fields as it becomes confusing and cluttering
		];

		$args = F::parse_args( $args, $defaults );

		extract( $args );

		// -------------------------------------------------------------------------

		ob_start();

		?>

		<li class="array_field_item collapsible collapsed">

			<div class="array_field_item_header array_field_item_sort_handle">

				<button class="array_field_item_collapsible_toggle no_text dashicons-before" type="button"></button>

				<div class="array_field_item_header_title"></div>

				<div class="array_field_item_header_actions">
					<button data-action="item_add" class="array_field_action no_text dashicons-before dashicons-insert" type="button" title="<?php $this->_e('Add'); ?>"></button>
					<button data-action="item_clone" class="array_field_action no_text dashicons-before dashicons-admin-page" type="button" title="<?php $this->_e('Clone'); ?>"></button>
					<button data-action="item_delete" class="array_field_action no_text dashicons-before dashicons-remove" type="button" title="<?php $this->_e('Delete'); ?>"></button>
				</div><!-- .array_field_item_header_actions -->

			</div><!-- .array_field_item_header -->


			<div class="array_field_item_settings">

				<?php echo $field_group; ?>

				<?php if ( $item_actions ) : ?>
					<div class="array_field_item_actions">
						<button data-action="item_add" class="button array_field_action" type="button">
							<?php $this->_e('Add'); ?>
						</button>
						<button data-action="item_clone" class="button array_field_action" type="button">
							<?php $this->_e('Clone'); ?>
						</button>
						<button data-action="item_delete" class="button array_field_action" type="button">
							<?php $this->_e('Delete'); ?>
						</button>
					</div><!-- .array_field_item_actions -->
				<?php endif ?>

			</div><!-- .array_field_item_settings -->

		</li>

		<?php

		return ob_get_clean();

	}
	// field_array_field_item()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * FIELDS - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AJAX RELATED - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * minimum required/expected data
	 */
	protected function ajax_required_data()
	{

		return [
			'panel_id',
			'section_id',
		];

	}
	// ajax_required_data()



	/**
	 * @internal
	 */
	protected function ajax_common_checks()
	{

		if ( ! $nonce = F::array_get( $_POST, ['nonce'] ) )
		{
			return false;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], "{$this->ajax_action}_nonce" ) )
		{
			return new \WP_Error(
				__FUNCTION__ . '_nonce_error',
				$this->__('Nonce Error')
			);
		}

		// -------------------------------------------------------------------------

		if ( ! current_user_can( $this->capability ) )
		{
			return new \WP_Error(
				__FUNCTION__ . '_permission_error',
				$this->__('Permission Error')
			);
		}

		// -------------------------------------------------------------------------

		$required_data = $this->ajax_required_data();

		if ( ! empty( $required_data ) )
		{
			foreach ( $required_data as $key )
			{
				if ( ! isset( $_POST[ $key ] ) )
				{
					return new \WP_Error(
						__FUNCTION__ . '_missing_required_data',
						sprintf( $this->__('Missing Required Data "%s"'), $key )
					);
				}
			}
		}

		// -------------------------------------------------------------------------

		$panel_id = F::array_get( $_POST, ['panel_id'] );

		if ( $panel_id !== $this->id )
		{
			return new \WP_Error(
				__FUNCTION__ . '_incorrect_panel_id',
				sprintf(
					$this->__('posted panel_id "%s" not equal this panel id "%s"'),
					$panel_id,
					$this->id
				)
			);
		}

		// -------------------------------------------------------------------------

		return true;

	}
	// ajax_common_checks()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * AJAX RELATED - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Panel
