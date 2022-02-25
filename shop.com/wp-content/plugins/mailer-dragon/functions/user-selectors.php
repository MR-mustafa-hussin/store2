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
 * Returns an array of users IDs or Names and Emails selected by email ID or Role filters
 *
 * @param type $roles
 * @param type $ids
 * @param type $ignore_frequency
 * @return string
 */
function ic_mailer_av_users( $roles = null, $ids = false, $ignore_frequency = false, $ignore_confirmation = false ) {
	if ( empty( $roles ) ) {
		$roles = array( 'mailer_subscriber' );
	} else if ( $roles == 'all' ) {
		$roles = ic_mailer_av_roles();
	}
	if ( is_array( $roles ) && !isset( $roles[ 0 ] ) ) {
		$roles = array_keys( $roles );
	}
	$args[ 'role__in' ] = $roles;
	if ( $ids ) {
		$args[ 'fields' ] = 'ID';
	} else {
		$args[ 'fields' ] = array( 'ID', 'user_email', 'display_name' );
	}
	if ( !$ignore_confirmation ) {
		$args[ 'meta_query' ] = ic_mailer_confirmed_meta_query();
	}
	if ( !$ignore_frequency ) {
		$args[ 'meta_query' ][] = ic_mailer_frequency_meta_query();
	}
	$users = get_users( $args );
	if ( $ids ) {
		$av_users = $users;
	} else {
		$av_users = array();
		foreach ( $users as $user ) {
			if ( is_email( $user->display_name ) ) {
				$av_users[ $user->ID ] = $user->display_name;
			} else {
				$av_users[ $user->ID ] = $user->display_name . ' (' . $user->user_email . ')';
			}
		}
	}
	return $av_users;
}

/**
 * Returns an array of users IDs selected by email content filters
 *
 * @param type $set_users
 * @param type $set_roles
 * @param type $contents
 * @param type $custom
 * @param type $ignore_frequency
 * @return type
 */
function ic_mailer_search_users_content( $set_users = null, $set_roles = null, $contents = null, $custom = null,
										 $ignore_frequency = false, $ignore_confirmation = false ) {
	if ( !empty( $set_users ) ) {
		$args[ 'include' ] = $set_users;
	}
	if ( !empty( $set_roles ) ) {
		$args[ 'role__in' ] = $set_roles;
	}
	if ( !$ignore_confirmation ) {
		$args[ 'meta_query' ] = ic_mailer_confirmed_meta_query();
	}
	if ( !empty( $contents ) ) {
		$args[ 'meta_query' ][] = apply_filters( 'ic_mailer_search_by_content', array(
			'relation' => 'AND',
			array(
				'key'		 => 'ic_mailer_contents',
				'value'		 => $contents,
				'compare'	 => 'IN'
			),
		), $contents );
	}
	if ( !empty( $custom ) ) {
		$args[ 'meta_query' ][] = ic_mailer_custom_meta_query( $custom );
	}
	if ( !$ignore_frequency ) {
		$args[ 'meta_query' ][]	 = ic_mailer_frequency_meta_query();
		//$args[ 'meta_key' ]		 = 'ic_mail_last_sent';
		//$args[ 'orderby' ]		 = 'meta_value';
		$args[ 'orderby' ]		 = array( 'ic_mail_last_sent' => 'ASC' );
	}
	$args[ 'fields' ]	 = 'ID';
	$users				 = get_users( $args );
	return array_unique( $users );
}

/**
 * Returns an array of user IDS that will receive specified message by email ID or delivery filters
 *
 * @param type $email_id
 * @param type $roles
 * @param type $users
 * @param type $contents
 * @param type $ignore_frequency
 * @return type
 */
function ic_get_email_receivers( $email_id = null, $roles = null, $users = null, $contents = null, $custom = null,
								 $ignore_frequency = false ) {
	$ignore_confirmation = ic_mail_ignore_confirmation( $email_id );
	if ( !empty( $email_id ) && intval( $email_id ) ) {
		if ( $contents === null ) {
			$contents = ic_mailer_contents( $email_id );
		}
		if ( $users === null ) {
			$users = ic_mailer_users( $email_id );
		}
		if ( $roles === null ) {
			$roles = ic_mailer_roles( $email_id );
		}
		if ( $custom === null ) {
			$custom = ic_mailer_custom( $email_id );
		}
	}
	if ( empty( $users ) && empty( $contents ) && empty( $custom ) ) {
		$users = ic_mailer_av_users( $roles, true, $ignore_frequency, $ignore_confirmation );
	} else if ( !empty( $contents ) || !empty( $custom ) ) {
		$all_ids = array();
		foreach ( $contents as $content ) {
			if ( !empty( $content ) && is_array( $content ) ) {
				$all_ids = array_merge( $all_ids, $content );
			} else if ( !empty( $content ) ) {
				$all_ids[] = $content;
			}
		}
		//if ( !empty( $all_ids ) ) {
		$users = ic_mailer_search_users_content( $users, $roles, $all_ids, $custom, $ignore_frequency, $ignore_confirmation );
		//}
	}
	if ( !empty( $users ) && is_array( $users ) && !empty( $email_id ) && intval( $email_id ) ) {
		$users_done = ic_mailer_done( $email_id );
		if ( !empty( $users_done ) && is_array( $users_done ) ) {
			$users = array_diff( $users, $users_done );
		}
	}
	return $users;
}

/**
 * Returns and array of user IDs for who the email submission is delayed
 *
 * @param type $email_id
 * @return type
 */
function ic_mailer_delayed( $email_id = null, $set_roles = null, $set_users = null, $set_contents = null,
							$set_custom = null ) {
	$all_users	 = ic_get_email_receivers( $email_id, $set_roles, $set_users, $set_contents, $set_custom, true, true );
	$all_delayed = ic_mailer_all_delayed();
	$users		 = array_intersect( $all_delayed, $all_users );
	return $users;
}

/**
 * Returns and array of user IDs for who the email delivery is currently delayed
 *
 * @return type
 */
function ic_mailer_all_delayed() {
	$args[ 'meta_query' ]	 = ic_mailer_frequency_meta_query( false );
	$args[ 'fields' ]		 = 'ID';
	$users					 = get_users( $args );
	return $users;
}
