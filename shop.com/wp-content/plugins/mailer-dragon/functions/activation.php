<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages activation functions
 *
 * Created by Norbert Dreszer.
 * Date: 19-Feb-15
 * Time: 13:40
 * Package: functions/
 */
class ic_mailer_activation {

	public function __construct() {
		add_action( 'ic_mailer_dragon_install', array( $this, 'email_activation' ) );
	}

	public function email_activation() {
		$activation = get_option( 'ic_mailer_dragon_install', 0 );
		if ( !empty( $activation ) && current_user_can( 'activate_plugins' ) ) {
			$this->add_email_caps();
			$this->add_mailer_role();
			$this->schedule_email_send();
			$this->create_thank_page();
		}
	}

	public function add_email_caps( $additional = true ) {
		if ( is_user_logged_in() && current_user_can( 'activate_plugins' ) ) {

			$role = get_role( 'administrator' );

			$this->assign_email_caps( $role );

			if ( $additional ) {
				$current_user = wp_get_current_user();
				foreach ( $current_user->roles as $current_role ) {
					if ( $current_role == 'administrator' ) {
						break;
					}
					$role			 = get_role( $current_role );
					$capabilities	 = $role->capabilities;
					if ( !empty( $capabilities[ 'activate_plugins' ] ) ) {
						$this->assign_email_caps( $role );
					}
				}
			}
		}
	}

	public function assign_email_caps( $role ) {
		$role->add_cap( 'publish_emails' );
		$role->add_cap( 'edit_emails' );
		$role->add_cap( 'edit_others_emails' );
		$role->add_cap( 'edit_private_emails' );
		$role->add_cap( 'delete_emails' );
		$role->add_cap( 'delete_others_emails' );
		$role->add_cap( 'read_private_emails' );
		$role->add_cap( 'delete_private_emails' );
		$role->add_cap( 'delete_published_emails' );
		$role->add_cap( 'edit_published_emails' );
		$role->add_cap( 'manage_email_categories' );
		$role->add_cap( 'edit_email_categories' );
		$role->add_cap( 'delete_email_categories' );
		$role->add_cap( 'assign_email_categories' );
		$role->add_cap( ic_mailer_settings_capability() );
	}

	public function add_mailer_role() {
		add_role( 'mailer_subscriber', __( 'Mailer Subscriber', 'mailer-dragon' ) );
	}

	public function schedule_email_send() {
		if ( !wp_next_scheduled( 'ic_hourly_scheduled_events' ) ) {
			wp_schedule_event( time(), 'hourly', 'ic_hourly_scheduled_events' );
		}
	}

	public function create_thank_page() {
		if ( current_user_can( 'publish_pages' ) ) {
			$settings = ic_get_email_settings();
			if ( empty( $settings[ 'thank_you' ] ) ) {
				$email_thank_page		 = array(
					'post_title'	 => __( 'Thank You', 'mailer-dragon' ),
					'post_type'		 => 'page',
					'post_content'	 => '[subscribe_thank_you]',
					'post_status'	 => 'publish',
					'comment_status' => 'closed'
				);
				$page_id				 = wp_insert_post( $email_thank_page );
				$settings[ 'thank_you' ] = $page_id;
				update_option( 'ic_mailer_settings', $settings );
			}
		}
	}

}

$ic_mailer_activation = new ic_mailer_activation;
