<?php

namespace DotAim\Admin\Components\App_Settings;

use DotAim\F;

/**
 * @internal
 */
class Notifications extends \DotAim\Admin\Panel
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

			[
				'id'		=> 'email_send',
				'title'	=> $this->__('Email Sending Options'),
			],

		];

		// -------------------------------------------------------------------------

		return [
			'title'		=> $this->__('Notifications'),
			'icon'		=> 'dashicons-email',
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

		add_action(
			'wp_ajax_test_email_send',
			[ $this, 'email_send_test_ajax_handle' ]
		);

		// -------------------------------------------------------------------------

		if ( empty( $this->settings() ) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$this->email_send_apply();

	}
	// apply()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * APPLY - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */



	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * email_send - START
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

	/**
	 * @internal
	 */
	protected function email_send_content()
	{

		// @todo
		//	methods for sending emails: wp_email or external smtp such as sendgrid or gmail
		//	could use WP Mail SMTP plugin to achieve that

		$admin_email	= get_bloginfo('admin_email');
		$site_name		= get_bloginfo('name');
		$site_domain	= str_replace( 'www.', '', strtolower( $_SERVER['SERVER_NAME'] ) );

		// -----------------------------------------------------------------------

		$out = [

			[
				'id' 			=> 'from_email',
				'label' 	=> $this->__('From Email'),
				'desc' 		=> sprintf(
					$this->__('If left blank, it defaults to "<a href="mailto:%1$s">%1$s</a>".'),
					"wordpress@{$site_domain}"
					//$admin_email
				),
				'type' 		=> 'text',
				//'default'	=> $admin_email,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'from_name',
				'label' 	=> $this->__('From Name'),
				'desc' 		=> sprintf(
					$this->__('If left blank, it defaults to "%1$s"'),
					'WordPress'
					//$site_name
				),
				'type' 		=> 'text',
				//'default'	=> $site_name,
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'method',
				'label' 	=> $this->__('Send Method'),
				'desc' 		=> $this->__('Select an option of how you want emails sent.'),
				'type' 		=> 'select_checkbox',
				'options'	=> [
					'default_wp_mail'	=> $this->__('Default <code>wp_mail()</code>'),
					'smtp'						=> $this->__('Via SMTP'),
					//'gmail'					=> $this->__('Via Gmail'),
					//'sendgrid'			=> $this->__('Via SendGrid'),
				],
				'default'	=> 'default_wp_mail',
			],

			// -----------------------------------------------------------------------

			[
				'id' 		=> 'enable_debugging',
				'type' 	=> 'checkbox',
				'label' => $this->__('Enable Debugging'),
				'desc' 	=> sprintf(
					$this->__('Log email errors to:<br><code>%s</code>'),
					$this->core->get_path( 'dir', 'logs/debug.log', false )
				),
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'smtp_host',
				'label' 			=> $this->__('SMTP Host'),
				'type' 				=> 'text',
				'conditional'	=> ['method' => 'smtp'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'smtp_port',
				'label' 			=> $this->__('SMTP Port'),
				'type' 				=> 'text',
				'conditional'	=> ['method' => 'smtp'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'smtp_secure',
				'label' 	=> $this->__('SMTP Encryption'),
				'desc' 		=> $this->__('TLS is not the same as STARTTLS. For most servers SSL is the recommended option. <code>$phpmailer->SMTPSecure</code>.'),
				'type' 		=> 'select_checkbox',
				'options'	=> [
					'none'	=> $this->__('No encryption'),
					'ssl'		=> $this->__('Use SSL encryption.'),
					'tls'		=> $this->__('Use TLS encryption.'),
				],
				'default'	=> 'none',
				'conditional'	=> ['method' => 'smtp'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'smtp_auth',
				'label' 			=> $this->__('Use SMTP Authentication'),
				'desc' 				=> $this->__('Select whether to use SMTP authentication or not. <code>$phpmailer->SMTPAuth</code>.'),
				'type' 				=> 'checkbox',
				'default'			=> false,
				'conditional'	=> ['method' => 'smtp'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'smtp_auth_username',
				'label' 			=> $this->__('Username'),
				'type' 				=> 'text',
				'conditional'	=> ['method' => 'smtp', 'smtp_auth' => 'checked'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 					=> 'smtp_auth_password',
				'label' 			=> $this->__('Password'),
				'type' 				=> 'text',
				'conditional'	=> ['method' => 'smtp', 'smtp_auth' => 'checked'],
			],

			// -----------------------------------------------------------------------

			[
				'id' 			=> 'smtp_gmail_info',
				'type'		=> 'html',
				'content'	=>
					'<h4>' . $this->__('Gmail instructions:') . '</h4>' .
					'<ul class="ul-disc">' .
						'<li>' . $this->__('SMTP Host: <code>smtp.gmail.com</code>') . '</li>' .
						'<li>' . $this->__('SMTP Port: <code>465</code>') . '</li>' .
						'<li>' . $this->__('Encryption: <code>SSL</code>') . '</li>' .
						'<li>' . $this->__('Authentication: <code>Yes</code>') . '</li>' .
						'<li>' . $this->__('Username: <code>Your full gmail address</code>') . '</li>' .
						'<li>' . $this->__('Password: <code>Your mail password</code>') . '</li>' .
					'</ul>',

				'colspan_full'=> true,
				'conditional'	=> ['method' => 'smtp'],
			],

			// -----------------------------------------------------------------------

			[
				'id'						=> 'test_email',
				'type'					=> 'html',
				'colspan_full'	=> true,
				'content'				=>
					'<div id="test_email_section">' .
						'<h4>' . $this->__('Test Email') . '</h4>' .
						'<input type="email" id="test_email_to" placeholder="' . esc_attr($this->__('Enter test email address')) . '" class="regular-text" style="margin-right: 10px">' .
						'<button type="button" class="button button_with_loader" id="send_test_email" disabled>' .
							sprintf(
								'<span class="button_with_loader_text">%s</span>',
								$this->__('Send Test Email')
							) .
						'</button>' .
						$this->email_send_test_script() .
					'</div>',
			],

		];

		// -------------------------------------------------------------------------

		return $out;

	}
	// email_send_content()



	/**
	 * @internal
	 */
	private function email_send_apply()
	{

		if ( $this->settings(['email_send', 'from_email']) )
		{
			add_filter( 'wp_mail_from', [ $this, 'email_send_apply_from_email' ] );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['email_send', 'from_name']) )
		{
			add_filter( 'wp_mail_from_name', [ $this, 'email_send_apply_from_name' ] );
		}

		// -------------------------------------------------------------------------

		$email_send_method = $this->settings(['email_send', 'method']);

		if ( 'smtp' === $email_send_method )
		{
			add_action( 'phpmailer_init', [ $this, 'email_send_apply_smtp' ] );
		}

		// -------------------------------------------------------------------------

		if ( $this->settings(['email_send', 'enable_debugging']) )
		{
			add_action( 'wp_mail_failed', function( $error ) use ( $email_send_method ) {

				$data = [
					'email_send_method'	=> $email_send_method,
					'to'								=> $error->get_error_data('wp_mail_failed')['to'],
					'subject'						=> $error->get_error_data('wp_mail_failed')['subject'],
					'message'						=> $error->get_error_data('wp_mail_failed')['message'],
					'headers'						=> $error->get_error_data('wp_mail_failed')['headers'],
					'error'							=> $error->get_error_message(),
					'error_data'				=> $error->get_error_data(),
				];

				debug_log( $data, 'Mail Error Details' );

			});
		}

	}
	// email_send_apply()



	/**
	 * @internal
	 */
	public function email_send_apply_from_email( $out )
	{

		if ( $setting = $this->settings(['email_send', 'from_email']) )
		{
			if ( F::is_email( $setting ) )
			{
				$out = $setting;
			}
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// email_send_apply_from_email()



	/**
	 * @internal
	 */
	public function email_send_apply_from_name( $out )
	{

		if ( $setting = $this->settings(['email_send', 'from_name']) )
		{
			$out = $setting;
		}

		// -------------------------------------------------------------------------

		return $out;

	}
	// email_send_apply_from_name()



	/**
	 * @internal
	 */
	public function email_send_apply_smtp( $phpmailer )
	{

		if ( 'smtp' !== $this->settings(['email_send', 'method']) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		if ( ! $host = $this->settings(['email_send', 'smtp_host']) )
		{
			return;
		}

		// -------------------------------------------------------------------------

		$port		= $this->settings(['email_send', 'smtp_port']);
		$secure	= $this->settings(['email_send', 'smtp_secure']);
		$auth		= $this->settings(['email_send', 'smtp_auth']);

		// -------------------------------------------------------------------------

		$phpmailer->Host = $host;
		$phpmailer->Port = $port;

		// -------------------------------------------------------------------------

		if ( 'none' !== $secure )
		{
			$phpmailer->SMTPSecure = $secure;
		}

		// -------------------------------------------------------------------------

		if ( $auth )
		{
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $this->settings(['email_send', 'smtp_auth_username']);
			$phpmailer->Password = $this->settings(['email_send', 'smtp_auth_password']);
		}

		// -------------------------------------------------------------------------

		$phpmailer->isSMTP();

		$phpmailer->Mailer = 'smtp';

		// -------------------------------------------------------------------------

		//$phpmailer->Sender = $phpmailer->From;

		$phpmailer->Sender = $this->settings(['email_send', 'from_email']);
		$phpmailer->setFrom(
			$this->settings(['email_send', 'from_email']),
			$this->settings(['email_send', 'from_name'])
		);

	}
	// email_send_apply_smtp()



	/**
	 * @internal
	 */
	private function email_send_test_script()
	{

		ob_start();

		?>
		<script>
			jQuery(document).ready(function($) {

				const $button					= $('#send_test_email');
				const $email					= $('#test_email_to');
				const $button_parent	= $button.parent();

				// ---------------------------------------------------------------------

				$email.on('input', function() {
					$button.prop('disabled', !this.value || !this.validity.valid);
				});

				// ---------------------------------------------------------------------

				$button.on('click', function() {

					$button
					.addClass('processing')
					.prop('disabled', true);

					// -------------------------------------------------------------------

					const notice_html =
						'<div class="dotaim_component_notice notice dismiss">' +
							'<div class="notice_message" style="padding: 5px 0">{{message}}</div>' +
							'<span class="dashicons dashicons-dismiss dismiss_button"></span>' +
						'</div>';

					// -------------------------------------------------------------------

					$('.dotaim_component_notice', $button_parent).remove();

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action				: 'test_email_send',
							nonce					: '<?php echo wp_create_nonce("test_email_send"); ?>',
							test_email_to	: $email.val(),
						},
						success: function( response ) {

							$(notice_html.replace('{{message}}', response.data.message))
							.addClass(response.success ? 'updated' : 'error')
							.appendTo( $button_parent );

						},
						error: function() {

							$(notice_html.replace('{{message}}','<?php echo esc_js($this->__("Server error occurred")); ?>'))
							.addClass('error')
							.appendTo( $button_parent );

						},
						complete: function() {

							$button
							.removeClass('processing')
							.prop('disabled', !$email.val() || !$email[0].validity.valid);

						}
					});

				});
			});
		</script>
		<?php

		// -------------------------------------------------------------------------

		return F::minify( ob_get_clean() );

	}
	// email_send_test_script()



	/**
	 * @internal
	 */
	public function email_send_test_ajax_handle()
	{

		check_ajax_referer('test_email_send', 'nonce');

		// -------------------------------------------------------------------------

		$to = sanitize_email($_POST['test_email_to']);

		if ( ! F::is_email( $to ) )
		{
			wp_send_json_error(['message' => __('Invalid email address')]);
		}

		// -------------------------------------------------------------------------

		add_action('wp_mail_failed', function( $error ) {

			wp_send_json_error(['message' => $error->get_error_message()]);

		});

		// -------------------------------------------------------------------------

		$subject = sprintf( $this->__('Test email from %s'), get_bloginfo('name') );
		$message = sprintf(
			$this->__('This is a test email sent from %s on %s.'),
			get_bloginfo('name'),
			current_time('mysql')
		);

		$result = wp_mail( $to, $subject, $message );

		if ( $result )
		{
			wp_send_json_success(['message' => $this->__('Test email sent successfully')]);
		}

		// -------------------------------------------------------------------------

		wp_send_json_error(['message' => $this->__('Failed to send test email')]);

	}
	// email_send_test_ajax_handle()

	/* ===========================================================================
	 * ---------------------------------------------------------------------------
	 * email_send - END
	 * ---------------------------------------------------------------------------
	 * ======================================================================== */

}
// Class Notifications
