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
 * Returns an integer that represents the number of users for who the email submission is delayed
 *
 * @param type $email_id
 * @return type
 */
function ic_mailer_count_delayed( $email_id, $set_roles = null, $set_users = null, $set_contents = null,
								  $set_custom = null ) {
	$users = ic_mailer_delayed( $email_id, $set_roles, $set_users, $set_contents, $set_custom );
	return count( $users );
}

/**
 * Returns an integer that represents the number of users who will receive specified message by email ID or delivery filters
 *
 * @param type $email_id
 * @param type $set_roles
 * @param type $set_users
 * @param type $set_contents
 * @return int
 */
function ic_mailer_count_receivers( $email_id = null, $set_roles = null, $set_users = null, $set_contents = null,
									$custom = null, $ignore_frequency = false ) {
	$user_ids = ic_get_email_receivers( $email_id, $set_roles, $set_users, $set_contents, $custom, $ignore_frequency );
	if ( is_array( $user_ids ) ) {
		return count( $user_ids );
	}
	return 0;
}

/**
 * Returns an integer that represents the number of users who got the specified message
 *
 * @param type $email_id
 * @return int
 */
function ic_mailer_count_receivers_done( $email_id ) {
	$users_done = ic_mailer_done( $email_id );
	if ( !empty( $users_done ) && is_array( $users_done ) ) {
		return count( $users_done );
	}
	return 0;
}
