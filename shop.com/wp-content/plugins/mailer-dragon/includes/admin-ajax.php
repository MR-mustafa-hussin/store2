<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages email ajax
 *
 * @version		1.0.0
 * @package		mailer-dragon/includes
 * @author 		Norbert Dreszer
 */
class ic_mailer_ajax {

	public function __construct() {
		add_action( 'wp_ajax_ic_mailer_receivers', array( $this, 'ic_mailer_receivers' ) );
		add_action( 'wp_ajax_ic_mailer_delayed_receivers', array( $this, 'ic_mailer_delayed_receivers' ) );
	}

	/**
	 * Manages ajax email info update
	 *
	 */
	public function ic_mailer_receivers() {
		check_ajax_referer( 'ic_ajax', 'security' );
		if ( isset( $_POST[ 'ic_mailer_roles' ] ) ) {
			$sanitize		 = new ic_mailer_sanitize;
			$set_roles		 = isset( $_POST[ 'ic_mailer_roles' ] ) ? $sanitize->text( $_POST[ 'ic_mailer_roles' ] ) : '';
			$set_users		 = isset( $_POST[ 'ic_mailer_users' ] ) ? $sanitize->number( $_POST[ 'ic_mailer_users' ] ) : '';
			$set_contents	 = isset( $_POST[ 'ic_mailer_contents' ] ) ? $sanitize->text( $_POST[ 'ic_mailer_contents' ] ) : '';
			$set_custom		 = isset( $_POST[ 'ic_mailer_custom' ] ) ? $sanitize->text( $_POST[ 'ic_mailer_custom' ] ) : '';
			echo ic_mailer_count_receivers( null, $set_roles, $set_users, $set_contents, $set_custom, false );
		}
		wp_die();
	}

	/**
	 * Manages ajax delayed email info update
	 *
	 */
	public function ic_mailer_delayed_receivers() {
		check_ajax_referer( 'ic_ajax', 'security' );
		if ( isset( $_POST[ 'ic_mailer_roles' ] ) ) {
			$sanitize		 = new ic_mailer_sanitize;
			$set_roles		 = isset( $_POST[ 'ic_mailer_roles' ] ) ? $sanitize->text( $_POST[ 'ic_mailer_roles' ] ) : '';
			$set_users		 = isset( $_POST[ 'ic_mailer_users' ] ) ? $sanitize->number( $_POST[ 'ic_mailer_users' ] ) : '';
			$set_contents	 = isset( $_POST[ 'ic_mailer_contents' ] ) ? $sanitize->number( $_POST[ 'ic_mailer_contents' ] ) : '';
			$set_custom		 = isset( $_POST[ 'ic_mailer_custom' ] ) ? $sanitize->text( $_POST[ 'ic_mailer_custom' ] ) : '';
			echo ic_mailer_count_delayed( null, $set_roles, $set_users, $set_contents, $set_custom );
		}
		wp_die();
	}

}

$ic_mailer_ajax = new ic_mailer_ajax;
