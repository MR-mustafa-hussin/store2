<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Mailer Dragon Conditional Functions
 *
 * @created Apr 3, 2015
 * @package conditionals
 */

/**
 * Checks whether the current screen is Mailer Dragon admin
 *
 * @return boolean
 */
function is_ic_mailer_admin_screen() {
	if ( is_admin() ) {
		if ( !empty( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'ic_mailer' ) {
			return true;
		}
		$post_type = get_post_type();
		if ( !empty( $post_type ) && $post_type == 'ic_mailer' ) {
			return true;
		}
	}
	return false;
}

/**
 * Checks if user subscription is active
 *
 * @param type $user_id
 * @return boolean
 */
function is_ic_mailer_subscription_confirmed( $user_id, $email_id = null ) {
	if ( ic_mail_ignore_confirmation( $email_id ) ) {
		return true;
	}
	$confrimed = get_user_meta( $user_id, 'ic_subscription_confirmed', true );
	if ( !empty( $confrimed ) ) {
		return true;
	}
	return false;
}

function is_ic_mailer_thank_you() {
	$page_id = ic_mailer_thank_you();
	if ( !empty( $page_id ) && is_page( $page_id ) ) {
		return true;
	}
	return false;
}
