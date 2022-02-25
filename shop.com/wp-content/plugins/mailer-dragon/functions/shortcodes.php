<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages 2Ckeckout functions
 *
 * Here 2Ckeckout functions are defined and managed.
 *
 * @version		1.0.0
 * @package		mailer_dragon/functions
 * @author 		Norbert Dreszer
 */
class ic_mailer_shortcodes {

	public function __construct() {
		add_shortcode( 'subscribe_form', array( $this, 'subscribe_form' ) );
		add_shortcode( 'subscribe_thank_you', array( $this, 'thank_you_shortcode' ) );
		add_action( 'ic_mailer_before_form', array( $this, 'set_form_id' ), 5, 2 );
		add_action( 'ic_mailer_before_form', array( $this, 'capture_subscription' ), 10, 2 );
		add_action( 'ic_mailer_dragon_before_button', array( $this, 'hidden_fields' ), 10, 2 );
	}

	public function subscribe_form( $atts ) {
		$args		 = shortcode_atts( array(
			'paragraphs'	 => '',
			'custom'		 => '',
			'track_content'	 => 1
		), $atts );
		$post_type	 = get_post_type();
		$post_id	 = get_the_ID();
		if ( !empty( $post_type ) && !empty( $post_id ) ) {
			ob_start();
			do_action( 'ic_mailer_before_form', $post_id, $args );
			ic_show_template_file( 'subscribe-form.php', MAILER_DRAGON_BASE_PATH );
			do_action( 'ic_mailer_after_form', $post_id, $args );
			if ( !empty( $args[ 'paragraphs' ] ) ) {
				return wpautop( ob_get_clean(), true );
			} else {
				return ob_get_clean();
			}
		}
	}

	public function thank_you_shortcode() {
		$thank_you_text = $this->subscribe_thank_you();
		if ( !empty( $thank_you_text ) && $thank_you_text !== 'wrong' ) {
			return implecode_success( $thank_you_text, 0 );
		} else {
			return implecode_warning( __( 'The URL provided is not correct!', 'mailer-dragon' ), 0 );
		}
	}

	public function subscribe_thank_you() {
		if ( !empty( $_GET[ 'confirmation' ] ) && !empty( $_GET[ 'sec' ] ) ) {
			$user_id = intval( $_GET[ 'confirmation' ] );
			$sec	 = urldecode( strval( $_GET[ 'sec' ] ) );
			if ( !empty( $user_id ) && ic_mailer_check_hash( $user_id, 'sub', $sec ) ) {
				return $this->confirm_subscription( $user_id );
			} else {
				return 'wrong';
			}
		} else if ( !empty( $_GET[ 'unsubscribe' ] ) && !empty( $_GET[ 'sec' ] ) ) {
			$user_id = intval( $_GET[ 'unsubscribe' ] );
			$sec	 = urldecode( strval( $_GET[ 'sec' ] ) );
			if ( !empty( $user_id ) && ic_mailer_check_hash( $user_id, 'unsub', $sec ) ) {
				return $this->unsubscribe( $user_id );
			} else {
				return 'wrong';
			}
		}
	}

	public function capture_subscription( $post_id, $args ) {
		if ( !empty( $_POST[ 'subscriber_email' ] ) && !empty( $post_id ) ) {
			if ( !$this->secure() ) {
				implecode_success( __( 'Thanks for Subscribing!', 'mailer-dragon' ) );
				return;
			}
			$expected_form_name = ic_mail_form_name();
			if ( !isset( $_POST[ $expected_form_name ] ) ) {
				return;
			}
			$user_name	 = sanitize_text_field( $_POST[ 'subscriber_name' ] );
			$user_email	 = is_email( sanitize_email( $_POST[ 'subscriber_email' ] ) );
			if ( $user_email ) {
				if ( empty( $user_name ) ) {
					$user_name = $user_email;
				}
				$user_id = $this->generate_new_user( $user_email, $user_name );
				if ( $user_id ) {
					$this->assign_subscription( $user_id, $post_id, $args );
				} else {
					implecode_warning( __( 'Sorry, we cannot register at this time. Please contact us directly.', 'mailer-dragon' ) );
				}
			} else if ( empty( $user_name ) ) {
				implecode_warning( __( 'Please fix name field.', 'mailer-dragon' ) );
			} else if ( empty( $user_email ) ) {
				implecode_warning( __( 'Please fix email field.', 'mailer-dragon' ) );
			}
		} else if ( isset( $_POST[ 'subscriber_email' ] ) ) {
			$expected_form_name = ic_mail_form_name();
			if ( !isset( $_POST[ $expected_form_name ] ) ) {
				return;
			}
			implecode_warning( __( 'Please fix email field.', 'mailer-dragon' ) );
		} else if ( !empty( $_GET[ 'sec' ] ) && !is_ic_mailer_thank_you() ) {
			$confirmation_text = $this->subscribe_thank_you();
			if ( !empty( $confirmation_text ) && $confirmation_text !== 'wrong' ) {
				implecode_success( $confirmation_text );
			} else {
				implecode_warning( __( 'The URL provided is not correct!', 'mailer-dragon' ) );
			}
		}
	}

	public function confirm_subscription( $user_id ) {
		if ( !is_ic_mailer_subscription_confirmed( $user_id ) ) {
			update_user_meta( $user_id, 'ic_subscription_confirmed', 1 );
			delete_user_meta( $user_id, 'ic_subscription_unsubscribed' );
			return sprintf( __( 'Your subscription has been confirmed. Be sure to whitelist the %s email address to make sure none of our emails go to your spam folder.', 'mailer-dragon' ), ic_mailer_sender() );
		} else {
			return sprintf( __( 'Your subscription has already been confirmed before. Be sure to whitelist the %s email address to make sure none of our emails go to your spam folder.', 'mailer-dragon' ), ic_mailer_sender() );
		}
	}

	public function unsubscribe( $user_id ) {
		delete_user_meta( $user_id, 'ic_subscription_confirmed' );
		update_user_meta( $user_id, 'ic_subscription_unsubscribed', 1 );
		return __( "Your subscription has been disabled. We're sorry to see you go. Feel free to get back at anytime!", 'mailer-dragon' );
	}

	public function set_form_id() {
		global $ic_mail_form_id;
		if ( empty( $ic_mail_form_id ) ) {
			$ic_mail_form_id = 1;
		} else {
			$ic_mail_form_id++;
		}
	}

	public function add_custom( $custom_name ) {
		$av_customs = ic_mailer_av_custom();
		if ( !in_array( $custom_name, $av_customs ) ) {
			$av_customs[] = $custom_name;
			update_option( 'ic_mailer_custom', $av_customs );
		}
	}

	public function generate_new_user( $user_email, $user_name ) {
		$user_id = email_exists( $user_email );
		if ( $user_id ) {
			$this->add_mailer_role( $user_id );
			return $user_id;
		}
		/*
		  $user_id = username_exists( $user_name );
		  if ( $user_id ) {
		  $this->add_mailer_role( $user_id );
		  return $user_id;
		  }
		 *
		 */
		$random_password = wp_generate_password();
		$userdata		 = array(
			'first_name' => $user_name,
			'user_login' => $user_email,
			'user_pass'	 => $random_password,
			'user_email' => $user_email,
			'role'		 => 'mailer_subscriber'
		);
		$user_id		 = wp_insert_user( $userdata );
		if ( !is_wp_error( $user_id ) ) {
			return $user_id;
		} else {
			return false;
		}
	}

	public function add_mailer_role( $user_id ) {
		$theUser = new WP_User( $user_id );
		$theUser->add_role( 'mailer_subscriber' );
	}

	public function assign_subscription( $user_id, $post_id, $args = array() ) {
		$assigned = false;
		if ( !empty( $args[ 'custom' ] ) ) {
			$user_customs = ic_mailer_get_user_customs( $user_id );
			if ( is_array( $user_customs ) && !in_array( $args[ 'custom' ], $user_customs ) ) {
				add_user_meta( $user_id, 'ic_mailer_custom', $args[ 'custom' ] );
				$this->add_custom( $args[ 'custom' ] );
				$assigned = true;
			}
		}
		if ( !empty( $args[ 'track_content' ] ) ) {
			$user_content_subscriptions = ic_mailer_get_user_subscriptions( $user_id );
			if ( is_array( $user_content_subscriptions ) && !in_array( $post_id, $user_content_subscriptions ) ) {
				add_user_meta( $user_id, 'ic_mailer_contents', $post_id );
				$assigned = true;
			}
		}
		if ( !is_ic_mailer_subscription_confirmed( $user_id ) ) {
			$this->send_welcome_email( $user_id );
			$user_email = ic_get_user_email( $user_id );
			implecode_info( sprintf( __( 'Thanks for Subscribing! Please check %s inbox or spam folder for the confirmation email.', 'mailer-dragon' ), $user_email ) );
		} else if ( $assigned ) {
			implecode_info( __( 'Thanks for Subscribing!', 'mailer-dragon' ) );
		} else {
			implecode_info( __( 'You already have an active subscription here!', 'mailer-dragon' ) );
		}
	}

	public function hidden_fields() {
		?>
		<p class="submit-additions-this">
			<label for="accept-terms-this">Accept terms this</label><input id="accept-terms-this" type="checkbox" name="accept-terms-this" value="1">
			<label for="accept-name-this">Accept name this</label><input type="text" name="accept-name-this" value="">
		</p>
		<?php

	}

	function secure() {
		if ( empty( $_POST[ 'accept-terms-this' ] ) && empty( $_POST[ 'accept-name-this' ] ) ) {
			return true;
		}
		return false;
	}

	public function generate_confirmation_url( $user_id ) {
		$url	 = ic_mailer_thank_you_url();
		$hash	 = ic_mailer_action_hash( $user_id, 'sub' );
		return add_query_arg( array( 'confirmation' => $user_id, 'sec' => $hash ), $url );
	}

	public function send_welcome_email( $user_id ) {
		$template			 = ic_mailer_confirmation();
		$confirmation_url	 = $this->generate_confirmation_url( $user_id );
		$message			 = str_replace( '[confirmation_url]', $confirmation_url, $template );
		$user_email			 = ic_get_user_email( $user_id );
		$sender_name		 = ic_mailer_sender_name();
		$email_title		 = sprintf( __( '%s : Please Confirm Subscription', 'mailer-dragon' ), $sender_name );
		ic_mail( $message, ic_mailer_sender_name(), ic_mailer_sender(), $user_email, $email_title, false );
	}

}

$ic_maile_shortcodes = new ic_mailer_shortcodes;
