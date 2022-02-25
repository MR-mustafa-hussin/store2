<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages scheduled_disc settings
 *
 * Here all scheduled_disc settings are defined and managed.
 *
 * @version		1.0.0
 * @package		mailer-dragon/includes
 * @author 		Norbert Dreszer
 */
add_action( 'admin_menu', 'ic_mailer_register_settings_menu' );

function ic_mailer_register_settings_menu() {
	add_submenu_page( 'edit.php?post_type=ic_mailer', __( 'Settings', 'mailer-dragon' ), __( 'Settings', 'mailer-dragon' ), 'manage_email_settings', basename( __FILE__ ), 'ic_mailer_settings' );
	do_action( 'mailer_settings_menu' );
}

add_action( 'admin_init', 'ic_mailer_settings_list' );

function ic_mailer_settings_list() {
	register_setting( 'ic_mailer_settings', 'ic_mailer_settings' );
}

add_filter( 'option_page_capability_ic_mailer_settings', 'ic_mailer_settings_capability' );

function ic_mailer_settings_capability() {
	return 'manage_email_settings';
}

function ic_mailer_settings() {
	$settings	 = ic_get_email_settings();
	?>
	<div id="implecode_settings" class="wrap">
		<h2><?php _e( 'Settings', 'ecommerce-product-catalog' ) ?> - Mailer Dragon</h2>
		<style>textarea {width: 400px; height: 300px;}</style>
		<form method="POST" action="options.php">
			<?php
			settings_fields( 'ic_mailer_settings' );
			?>
			<table>
				<?php
				$tip		 = __( 'The email address used to send emails. This should be in the site domain to reduce the rate in which your messages will land in user spam folder.', 'mailer-dragon' );
				implecode_settings_text( ic_settings_tip( $tip ) . __( 'Sender Email Address', 'mailer-dragon' ), 'ic_mailer_settings[sender_email]', $settings[ 'sender_email' ] );
				$tip		 = __( 'The name to be used in every email "from" field.', 'mailer-dragon' );
				implecode_settings_text( ic_settings_tip( $tip ) . __( 'Sender Email Name', 'mailer-dragon' ), 'ic_mailer_settings[sender_name]', $settings[ 'sender_name' ] );
				$tip		 = __( 'Email address to get a test message an each newsletter content update.', 'mailer-dragon' );
				implecode_settings_text( ic_settings_tip( $tip ) . __( 'Test Email', 'mailer-dragon' ), 'ic_mailer_settings[test_email]', $settings[ 'test_email' ] );
				?>
				<tr>
					<td>
						<span title="<?php _e( 'The page where the user is redirected after successfull subscription confirmation.', 'mailer-dragon' ) ?>" class="dashicons dashicons-editor-help ic_tip"></span>
						<?php _e( 'Thank You Page', 'ecommerce-product-catalog' ); ?>:
					</td>
					<td><?php
						ic_select_page( 'ic_mailer_settings[thank_you]', __( 'Select Page', 'mailer-dragon' ), $settings[ 'thank_you' ], true );
						?>
					</td>
				</tr>
				<?php
				$tip		 = __( 'The email that is sent immediately after subscription. The user will not get any additional email before using the activation link.', 'mailer-dragon' );
				implecode_settings_textarea( ic_settings_tip( $tip ) . __( 'Confirmation Email', 'mailer-dragon' ), 'ic_mailer_settings[confirmation]', $settings[ 'confirmation' ] );
				?>
			</table>
			<h3><?php _e( 'Advanced Settings', 'mailer-dragon' ) ?></h3>
			<table>
				<?php
				/*
				  $selected_roles	 = $settings[ 'av_roles' ];
				  if ( !is_int( key( $selected_roles ) ) ) {
				  $selected_roles = array_keys( $selected_roles );
				  }
				  implecode_settings_dropdown( __( 'Filter Roles', 'mailer-dragon' ), 'ic_mailer_settings[av_roles]', $selected_roles, ic_mailer_default_roles(), 1, 'multiple class="ic_chosen"' );
				  $selected_types = $settings[ 'av_post_types' ];
				  if ( !is_int( key( $selected_types ) ) ) {
				  $selected_types = array_keys( $selected_types );
				  }
				  implecode_settings_dropdown( __( 'Filter Content', 'mailer-dragon' ), 'ic_mailer_settings[av_post_types]', $selected_types, ic_mailer_default_post_types(), 1, 'multiple class="ic_chosen"' );
				 */
				$tip		 = __( 'The maximum frequency on which the emails will be sent to one user. Decreasing this number will make your mailing less effective in most cases. Sending emails to often is considered a bad practice and will make your subscribers leave.', 'mailer-dragon' );
				implecode_settings_number( ic_settings_tip( $tip ) . __( 'Email Frequency', 'mailer-dragon' ), 'ic_mailer_settings[email_frequency]', $settings[ 'email_frequency' ], __( 'days', 'mailer-dragon' ) );
				$tip		 = __( 'The maximum number of emails sent per hour. Increasing this number can cause problems with your server admin or with spam filters. If you want to increase it you should ask your host for email sending limits per hour.', 'mailer-dragon' );
				implecode_settings_number( ic_settings_tip( $tip ) . __( 'Max Emails', 'mailer-dragon' ), 'ic_mailer_settings[max_emails]', $settings[ 'max_emails' ], __( 'emails per hour', 'mailer-dragon' ) );
				?>
			</table>
			<h3><?php _e( 'Email Content', 'mailer-dragon' ) ?></h3>
			<table>
				<?php
				implecode_settings_text( __( 'Footer Address Line', 'mailer-dragon' ), 'ic_mailer_settings[address_line]', $settings[ 'address_line' ] );
				?>
			</table>
			<h3><?php _e( 'Uninstall Settings', 'mailer-dragon' ) ?></h3>
			<table>
				<?php
				implecode_settings_checkbox( __( 'Delete all emails and subscribers on uninstall', 'mailer-dragon' ), 'ic_mailer_settings[delete_all]', $settings[ 'delete_all' ] )
				?>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary"
					   value="<?php _e( 'Save changes', 'mailer-dragon' ); ?>"/>
			</p>
		</form>
		<h3><?php _e( 'Recommended Extensions', 'mailer-dragon' ) ?></h3>
		<ul>
			<li><a href="<?php echo admin_url( 'plugin-install.php?s=WP+MAIL+SMTP+by+WPForms&tab=search&type=term' ); ?>">WP Mail SMTP</a> - <?php _e( 'To authorize outgoing emails with password. This will reduce the rate in which your messages will be sent to user spam folder.', 'mailer-dragon' ) ?></li>
			<li><a href="<?php echo admin_url( 'plugin-install.php?s=WP+Mail+Logging+Christian+Zöller&tab=search&type=term' ); ?>">WP Mail Logging</a> - <?php _e( 'To track all your outgoing emails in one place.', 'mailer-dragon' ) ?></li>
		</ul>
	</div>
	<?php
}

/**
 * Defines Email Dragon settings
 *
 * @return array
 */
function ic_get_email_settings() {
	$settings						 = get_option( 'ic_mailer_settings' );
	$settings[ 'av_roles' ]			 = isset( $settings[ 'av_roles' ] ) ? $settings[ 'av_roles' ] : ic_mailer_default_roles();
	$settings[ 'av_post_types' ]	 = isset( $settings[ 'av_post_types' ] ) ? $settings[ 'av_post_types' ] : ic_mailer_default_post_types();
	$settings[ 'max_emails' ]		 = isset( $settings[ 'max_emails' ] ) ? $settings[ 'max_emails' ] : 20;
	$settings[ 'email_frequency' ]	 = isset( $settings[ 'email_frequency' ] ) ? $settings[ 'email_frequency' ] : 5;
	$settings[ 'sender_email' ]		 = isset( $settings[ 'sender_email' ] ) ? $settings[ 'sender_email' ] : get_bloginfo( 'admin_email' );
	$settings[ 'sender_name' ]		 = isset( $settings[ 'sender_name' ] ) ? $settings[ 'sender_name' ] : get_bloginfo( 'name' );
	$settings[ 'thank_you' ]		 = isset( $settings[ 'thank_you' ] ) ? $settings[ 'thank_you' ] : '';
	$settings[ 'confirmation' ]		 = isset( $settings[ 'confirmation' ] ) ? $settings[ 'confirmation' ] : ic_mailer_default_cofirmation( $settings[ 'sender_email' ] );
	$settings[ 'delete_all' ]		 = isset( $settings[ 'delete_all' ] ) ? $settings[ 'delete_all' ] : '';
	$settings[ 'unsubscribe_note' ]	 = isset( $settings[ 'unsubscribe_note' ] ) ? $settings[ 'unsubscribe_note' ] : __( 'Use the following link to unsubscribe:', 'mailer-dragon' );
	$settings[ 'email_info' ]		 = isset( $settings[ 'email_info' ] ) ? $settings[ 'email_info' ] : '';
	$settings[ 'address_line' ]		 = isset( $settings[ 'address_line' ] ) ? $settings[ 'address_line' ] : '';
	$settings[ 'test_email' ]		 = isset( $settings[ 'test_email' ] ) ? $settings[ 'test_email' ] : '';
	return $settings;
}

/**
 * Returns and array of available roles for email delivery filters
 *
 * @return array
 */
function ic_mailer_av_roles() {
	$settings = ic_get_email_settings();
	return $settings[ 'av_roles' ];
}

/**
 * Returns and array of available post types for email delivery filters
 *
 * @return array
 */
function ic_mailer_av_post_types() {
	$settings = ic_get_email_settings();
	return $settings[ 'av_post_types' ];
}

/**
 * Returns and array of available custom parameters for email delivery filters
 *
 * @return array
 */
function ic_mailer_av_custom() {
	$av_custom = get_option( 'ic_mailer_custom', array() );
	return $av_custom;
}

/**
 * Returns and integer that represents the number of days between each delivery to one user
 *
 * @return int
 */
function ic_mailer_frequency() {
	$settings = ic_get_email_settings();
	return $settings[ 'email_frequency' ];
}

/**
 * Returns a confirmation email template
 *
 * @return int
 */
function ic_mailer_confirmation() {
	$settings = ic_get_email_settings();
	return $settings[ 'confirmation' ];
}

/**
 * Returns a email sender
 *
 * @return int
 */
function ic_mailer_sender() {
	$settings = ic_get_email_settings();
	return $settings[ 'sender_email' ];
}

/**
 * Returns email sender name
 *
 * @return int
 */
function ic_mailer_sender_name() {
	$settings = ic_get_email_settings();
	return $settings[ 'sender_name' ];
}

/**
 * Returns email subscription confirmation page ID
 *
 * @return int
 */
function ic_mailer_thank_you() {
	$settings = ic_get_email_settings();
	return $settings[ 'thank_you' ];
}

/**
 * Defines default roles available
 *
 * @global object $wp_roles
 * @return array
 */
function ic_mailer_default_roles() {
	$def_roles = array();
	if ( function_exists( 'get_editable_roles' ) ) {
		$roles = get_editable_roles();
		foreach ( $roles as $role_name => $role ) {
			$def_roles[ $role_name ] = $role[ 'name' ];
		}
	} else {
		global $wp_roles;
		if ( !empty( $wp_roles ) ) {
			$def_roles = $wp_roles->get_names();
		}
	}

	return $def_roles;
}

/**
 * Defines post types available by default
 *
 * @return array
 */
function ic_mailer_default_post_types() {
	$args[ 'show_ui' ]	 = true;
	$post_types			 = get_post_types( $args );
	unset( $post_types[ 'attachment' ] );
	unset( $post_types[ 'ic_mailer' ] );
	return $post_types;
}

/**
 * Returns default subscription confirmation email
 * @return type
 */
function ic_mailer_default_cofirmation( $contact_email ) {
	$first_row	 = __( 'Please use the link below to confirm subscription:', 'mailer-dragon' );
	$second_row	 = __( "If you received this email by mistake, simply delete it. You won't be subscribed if you don't click the confirmation link above.", 'mailer-dragon' );
	$third_row	 = __( "For questions about this list, please contact: ", 'mailer-dragon' );
	return $first_row . "\r\n" . "\r\n" . '[confirmation_url]' . "\r\n" . "\r\n" . $second_row . "\r\n" . "\r\n" . $third_row . "\r\n" . "\r\n" . $contact_email;
}
