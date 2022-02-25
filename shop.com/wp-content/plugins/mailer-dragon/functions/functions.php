<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages Mailer Dragon functions
 *
 *
 * @version		1.0.0
 * @package		mailer_dragon/functions
 * @author 		Norbert Dreszer
 */

/**
 * Returns an array of post ids from multiple post types where the user subscribed
 *
 * @param type $user_id
 * @return array
 */
function ic_mailer_get_user_subscriptions( $user_id ) {
	$user_content_subscriptions = get_user_meta( $user_id, 'ic_mailer_contents' );
	if ( empty( $user_content_subscriptions ) ) {
		$user_content_subscriptions = array();
	}
	return $user_content_subscriptions;
}

/**
 * Returns an array of custom parameters from user subscriptions
 *
 * @param type $user_id
 * @return array
 */
function ic_mailer_get_user_customs( $user_id ) {
	$user_customs = get_user_meta( $user_id, 'ic_mailer_custom' );
	if ( empty( $user_customs ) ) {
		$user_customs = array();
	}
	return $user_customs;
}

function ic_mailer_nearest_delivery( $email_id ) {
	$delayed_users	 = ic_mailer_delayed( $email_id );
	$time			 = '';
	reset( $delayed_users );
	$first_key		 = key( $delayed_users );
	if ( !empty( $delayed_users[ $first_key ] ) ) {
		$time = get_user_meta( $delayed_users[ $first_key ], 'ic_mail_last_sent', true );
	}
	return $time;
}

/**
 * Defines currently displayed form name
 *
 * @global int $ic_mail_form_name
 * @return type
 */
function ic_mail_form_name() {
	global $ic_mail_form_id;
	if ( !empty( $ic_mail_form_id ) ) {
		return 'ic-mail-form-' . $ic_mail_form_id;
	}
	return;
}

/**
 * Returns timestamp for valid email delivery time
 *
 * @return type
 */
function ic_get_valid_send_time() {
	return time() - ic_mailer_frequency() * DAY_IN_SECONDS;
}

/**
 * meta_query for get_users to limit the users with email delivery frequency parameter
 *
 * @param type $not_delayed
 * @return string
 */
function ic_mailer_frequency_meta_query( $not_delayed = true ) {
	$valid_time = ic_get_valid_send_time();
	if ( $not_delayed ) {
		$compare = '<=';
	} else {
		$compare = '>';
	}
	$meta_query = array(
		'relation' => 'OR',
		array(
			'key'		 => 'ic_mail_last_sent',
			'value'		 => $valid_time,
			'compare'	 => $compare,
			'type'		 => 'NUMERIC'
		)
	);
	if ( $not_delayed ) {
		$meta_query[] = array(
			'key'		 => 'ic_mail_last_sent',
			'compare'	 => 'NOT EXISTS'
		);
	}
	return $meta_query;
}

/**
 * meta_query for get_users to limit the users with custom parameter
 *
 * @param type $custom
 * @return string
 */
function ic_mailer_custom_meta_query( $custom ) {
	if ( empty( $custom ) ) {
		return;
	}
	$meta_query = array(
		array(
			'key'		 => 'ic_mailer_custom',
			'value'		 => $custom,
			'compare'	 => 'IN',
		)
	);
	return $meta_query;
}

/**
 * meta_query for get_users to limit the users with confirmed subscription
 *
 * @return string
 */
function ic_mailer_confirmed_meta_query() {
	$meta_query = array(
		array(
			'key'		 => 'ic_subscription_confirmed',
			'value'		 => 1,
			'compare'	 => '=',
		)
	);
	return $meta_query;
}

/**
 * Returns mailer thank you URL
 *
 * @return type
 */
function ic_mailer_thank_you_url() {
	$thank_you_id	 = ic_mailer_thank_you();
	$url			 = get_permalink( $thank_you_id );
	return $url;
}

/**
 * Returns Mailer Dragon sub or unsub action hash for security
 *
 * @param type $user_id
 * @param type $action
 * @return type
 */
function ic_mailer_action_hash( $user_id, $action = 'unsub' ) {
	$hash = wp_hash_password( MAILER_DRAGON_BASE_PATH . $user_id . $action );
	return $hash;
}

function ic_mailer_check_hash( $user_id, $action, $hash ) {
	require_once ABSPATH . WPINC . '/class-phpass.php';
	$wp_hasher		 = new PasswordHash( 8, TRUE );
	$plain_password	 = MAILER_DRAGON_BASE_PATH . $user_id . $action;
	if ( $wp_hasher->CheckPassword( $plain_password, $hash ) ) {
		return true;
	}
	return false;
}

function ic_mail_ignore_confirmation( $email_id = null ) {
	return apply_filters( 'ic_mail_ignore_confirmation', false, $email_id ); // WARNING: Use it only if the recipient has confirmed somewhere else
}
