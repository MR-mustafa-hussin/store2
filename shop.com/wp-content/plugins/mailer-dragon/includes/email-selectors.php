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
function ic_mailer_filters( $email_id ) {
	$filters = get_post_meta( $email_id, 'ic_mailer', true );
	if ( empty( $filters ) ) {
		$filters = array();
	}
	return $filters;
}

function ic_mailer_done( $email_id ) {
	$filters = get_post_meta( $email_id, 'ic_mail_done', true );
	if ( empty( $filters ) ) {
		$filters = array();
	}
	return $filters;
}

function ic_mailer_roles( $email_id ) {
	$filters			 = ic_mailer_filters( $email_id );
	$filters[ 'roles' ]	 = isset( $filters[ 'roles' ] ) ? $filters[ 'roles' ] : '';
	return $filters[ 'roles' ];
}

function ic_mailer_users( $email_id ) {
	$filters			 = ic_mailer_filters( $email_id );
	$filters[ 'users' ]	 = isset( $filters[ 'users' ] ) ? $filters[ 'users' ] : '';

	return $filters[ 'users' ];
}

function ic_mailer_contents( $email_id ) {
	$filters				 = ic_mailer_filters( $email_id );
	$filters[ 'contents' ]	 = isset( $filters[ 'contents' ] ) ? $filters[ 'contents' ] : '';
	return $filters[ 'contents' ];
}

function ic_mailer_custom( $email_id ) {
	$filters			 = ic_mailer_filters( $email_id );
	$filters[ 'custom' ] = isset( $filters[ 'custom' ] ) ? $filters[ 'custom' ] : '';
	return $filters[ 'custom' ];
}
