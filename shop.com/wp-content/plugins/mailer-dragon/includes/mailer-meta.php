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
class ic_mailer_meta {

	public function __construct() {
		add_action( 'ic_mailer_groups', array( $this, 'roles_selector' ) );
		add_action( 'ic_mailer_groups', array( $this, 'users_selector' ) );
		add_action( 'ic_mailer_groups', array( $this, 'content_selector' ) );
		add_action( 'ic_mailer_groups', array( $this, 'custom_selector' ) );
		add_action( 'ic_mailer_groups', array( $this, 'receivers_info' ), 50 );
	}

	function roles_selector( $email_id ) {
		$roles		 = ic_mailer_roles( $email_id );
		$av_roles	 = ic_mailer_av_roles();
		if ( !empty( $av_roles ) ) {
			$tip		 = __( 'Limits submission to users with selected roles.', 'mailer_dragon' );
			$label_tip	 = ic_settings_tip( $tip ) . __( 'Roles', 'mailer-dragon' );
			echo implecode_settings_dropdown( $label_tip, 'ic_mailer[roles][]', $roles, $av_roles, 0, 'multiple class="ic_chosen" data-placeholder="' . __( 'Select Roles', 'mailer-dragon' ) . '"' );
		}
	}

	function users_selector( $email_id ) {
		$users		 = ic_mailer_users( $email_id );
		$av_users	 = ic_mailer_av_users( 'all', false, true );
		if ( !empty( $av_users ) ) {
			$tip		 = __( 'Limits submission to selected users.', 'mailer_dragon' );
			$label_tip	 = ic_settings_tip( $tip ) . __( 'Users', 'mailer-dragon' );
			echo implecode_settings_dropdown( $label_tip, 'ic_mailer[users][]', $users, $av_users, 0, 'multiple class="ic_chosen" data-placeholder="' . __( 'Select Users', 'mailer-dragon' ) . '"' );
		}
	}

	function content_selector( $email_id ) {
		$contents		 = ic_mailer_contents( $email_id );
		$av_post_types	 = ic_mailer_av_post_types();
		$selector		 = '';

		foreach ( $av_post_types as $post_type ) {
			$post_type_posts = ic_get_post_type_posts( $post_type );
			if ( !empty( $post_type_posts ) ) {
				$selected	 = isset( $contents[ $post_type ] ) ? $contents[ $post_type ] : '';
				$label		 = ic_get_post_type_label( $post_type );
				if ( $label ) {
					$tip			 = sprintf( __( 'Visitors that subscribed on selected %s.', 'mailer_dragon' ), $label );
					$label_tip		 = ic_settings_tip( $tip ) . $label;
					$post_type_posts = apply_filters( 'ic_mailer_content_selector_values', $post_type_posts, $post_type );
					$selector		 .= implecode_settings_dropdown( $label_tip, 'ic_mailer[contents][' . $post_type . '][]', $selected, $post_type_posts, 0, 'multiple class = "ic_chosen email-post-types" data-type = "' . $post_type . '" data-placeholder = "' . sprintf( __( 'Select Some %s', 'mailer-dragon' ), $label ) . '"' );
				}
			}
		}
		echo $selector;
	}

	function custom_selector( $email_id ) {
		$selected_custom = ic_mailer_custom( $email_id );
		$av_custom		 = ic_mailer_av_custom();
		if ( !empty( $av_custom ) ) {
			$tip		 = __( 'Limits submission to users that subscribed to a form with selected custom parameter.', 'mailer_dragon' );
			$label_tip	 = ic_settings_tip( $tip ) . __( 'Custom', 'mailer-dragon' );
			echo implecode_settings_dropdown( $label_tip, 'ic_mailer[custom][]', $selected_custom, $av_custom, 0, 'multiple class="ic_chosen" data-placeholder="' . __( 'Select Custom', 'mailer-dragon' ) . '"' );
		}
	}

	function receivers_info( $email_id ) {
		$receivers_count	 = ic_mailer_count_receivers( $email_id, null, null, null, null, false );
		$receivers_done		 = ic_mailer_count_receivers_done( $email_id );
		$receivers_delayed	 = ic_mailer_count_delayed( $email_id );
		echo '<tr>';
		echo '<td colspan="2" class="receivers-info">';
		//if ( empty( $receivers_count ) ) {
		$tip				 = __( 'This doesn\'t count delayed submissions.', 'mailer-dragon' );
		echo ic_settings_tip( $tip );
		//}
		echo sprintf( __( '%s receivers for immediate submission!', 'mailer-dragon' ), '<strong>' . $receivers_count . '</strong>' );
		echo '<td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2" class="delayed-info">';
		$tip				 = sprintf( __( 'If an user has already got a message within last %s days the submission is temporary delayed for him.', 'mailer-dragon' ), ic_mailer_frequency() );
		echo ic_settings_tip( $tip );
		echo sprintf( __( '%s receivers for delayed submission.', 'mailer-dragon' ), '<strong>' . $receivers_delayed . '</strong>' );
		echo '<td>';
		echo '</tr>';
		if ( !empty( $receivers_done ) ) {
			$tip = __( 'The number of users that got this email. It will not be sent again to them even if you modify the email content or delivery filters.', 'mailer_dragon' );
			echo '<tr>';
			echo '<td colspan="2" class="received-info">';
			echo ic_settings_tip( $tip );
			echo sprintf( __( 'Already sent to %s email receivers!', 'mailer-dragon' ), '<strong>' . $receivers_done . '</strong>' );
			echo '<td>';
			echo '</tr>';
		}
	}

}

$ic_mailer_selectors = new ic_mailer_meta;
