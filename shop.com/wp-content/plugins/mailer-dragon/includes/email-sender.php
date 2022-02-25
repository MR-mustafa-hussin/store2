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
class ic_mailer_sender {

	private $sender_name;
	private $sender_email;
	private $emails_limit;
	private $settings;

	public function __construct() {
		$this->settings		 = ic_get_email_settings();
		$this->sender_email	 = ic_mailer_sender();
		$this->sender_name	 = ic_mailer_sender_name();
		$this->emails_limit	 = $this->settings[ 'max_emails' ];
		//add_action( 'publish_ic_mailer', array( $this, 'ic_mailer_publish' ), 10, 1 );
		add_action( 'transition_post_status', array( $this, 'ic_mailer_publish' ), 10, 3 );
		add_action( 'trashed_post', array( $this, 'ic_mailer_unpublish' ), 10, 1 );
		add_action( 'ic_hourly_scheduled_events', array( $this, 'send_emails' ), 10, 1 );
		add_action( 'email_edit_save', array( $this, 'test_email' ) );
	}

	public function ic_mailer_publish( $new_status, $old_status, $post ) {
		if ( $post->post_type === 'ic_mailer' && $new_status !== $old_status && $new_status === 'publish' ) {
			$email_id = intval( $post->ID );
			if ( empty( $email_id ) ) {
				return;
			}
			$published = $this->get_published_emails();
			if ( !in_array( $email_id, $published ) ) {
				$published[] = $email_id;
				update_option( 'ic_mailers_published', $published );
			}
		}
		return;
	}

	public function ic_mailer_unpublish( $email_id ) {
		$post_type = get_post_type( $email_id );
		if ( $post_type == 'ic_mailer' || !$post_type ) {
			//wp_clear_scheduled_hook( 'ic_mailer_send', array( $email_id ) );
			$published	 = $this->get_published_emails();
			$key		 = array_search( $email_id, $published );
			if ( $key !== false ) {
				unset( $published[ $key ] );
				update_option( 'ic_mailers_published', $published );
			}
		}
	}

	public function get_published_emails() {
		$published = get_option( 'ic_mailers_published', array() );
		return $published;
	}

	function test_email( $email ) {
		if ( !empty( $email->ID ) ) {
			$email_id		 = $email->ID;
			$email_title	 = $this->email_title( $email_id );
			$email_content	 = $this->email_content( $email_id );
			$user_email		 = $this->settings[ 'test_email' ];
			if ( !empty( $user_email ) ) {
				$email_content = $this->htmlize( $email_content, '', $user_email );
				ic_mail_simple_html( $email_content, $this->sender_name, $this->sender_email, $user_email, $email_title );
			}
		}
	}

	public function send_emails() {
		$published	 = $this->get_published_emails();
		$counter	 = 0;
		foreach ( $published as $email_id ) {
			if ( $counter >= $this->emails_limit ) {
				break;
			}
			$counter = $this->ic_mailer_send( $email_id, $counter );
		}
		return;
	}

	public function ic_mailer_send( $email_id, $counter = 0 ) {
		$status = get_post_status( $email_id );
		if ( $status !== 'publish' ) {
			$this->ic_mailer_unpublish( $email_id );
			return $counter;
		}
		$user_ids = ic_get_email_receivers( $email_id );
		if ( !empty( $user_ids ) ) {
			$email_title	 = $this->email_title( $email_id );
			$email_content	 = $this->email_content( $email_id );
			foreach ( $user_ids as $user_id ) {
				if ( !is_ic_mailer_subscription_confirmed( $user_id, $email_id ) ) {
					continue;
				}
				if ( $counter == $this->emails_limit ) {
					break;
				}
				$user_email = ic_get_user_email( $user_id );
				if ( !empty( $user_email ) ) {
					$user_email_content = $this->htmlize( $email_content, $user_id, $user_email );
					ic_mail_simple_html( $user_email_content, $this->sender_name, $this->sender_email, $user_email, $email_title );
					$this->update_users_done( $email_id, $user_id );
					$this->update_send_date( $user_id, $email_id );
					$counter++;
				}
			}
		}
		return $counter;
	}

	function email_title( $email_id ) {
		$email_title = get_the_title( $email_id );
		return $email_title;
	}

	function email_content( $email_id ) {
		$email_content = apply_filters( 'the_content', get_post_field( 'post_content', $email_id ) );
		return $email_content;
	}

	public function update_users_done( $email_id, $user_id ) {
		$done	 = ic_mailer_done( $email_id );
		$done[]	 = $user_id;
		update_post_meta( $email_id, 'ic_mail_done', $done );
	}

	public function update_send_date( $user_id ) {
		update_user_meta( $user_id, 'ic_mail_last_sent', time() );
	}

	public function unsubscribe_url( $user_id ) {
		$url = ic_mailer_thank_you_url();
		if ( !empty( $url ) ) {
			$hash = ic_mailer_action_hash( $user_id );
			return add_query_arg( array( 'unsubscribe' => $user_id, 'sec' => $hash ), $url );
		}
		return '';
	}

	public function add_unsubscribe_link( $email_content, $user_id ) {
		$url = $this->unsubscribe_url( $user_id );
		if ( !empty( $url ) ) {
			$email_content .= "\r\n" . $this->settings[ 'unsubscribe_note' ] . ' ' . $url;
		}
		return $email_content;
	}

	function htmlize( $email_content, $user_id, $user_email ) {
		$htmlized	 = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="min-width: 100%;" role="presentation">';
		$htmlized	 .= '<tr><td>';
		$htmlized	 .= $email_content;
		$htmlized	 .= '</td></tr>';
		$htmlized	 .= '<tr><td>';
		$htmlized	 .= $this->info_html( $user_email, $user_id );
		$htmlized	 .= '</td></tr>';
		$htmlized	 .= '</table>';
		return $htmlized;
	}

	function info_html( $user_email, $user_id ) {
		$html = '<br style="clear: both;">
<div style="height:1px; width: 100%; margin-left: auto; margin-right: auto; background-color:black;display:block !important;"></div>
<br>
<div style="margin-left: auto; margin-right: auto; width: 100%; background-color:#ffffff !important; display:block !important;">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; display:table !important;">
<tr style="display:table-row !important;">
<td style="width:20%; display:table-cell !important;"></td>
<td align="center" style="mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;text-align:center;vertical-align:middle; display:table-cell !important;font-size:8.0pt; font-family:\'Arial\',\'sans-serif\'; color:#666666;">
You are receiving this email as a customer or because you signed up on our website.
<br>
This email was sent to <b>' . $user_email . '</b> by <b>' . $this->sender_email . '</b>
<br>
<br>
 ' . $this->settings[ 'address_line' ] . '
<br>
<br>
<div style="display:block">
<a style="border:0px;color:#000;display:inline !important;" rel="nofollow" href="' . $this->unsubscribe_url( $user_id ) . '">Unsubscribe</a>
</div>
</td>
<td align="right" style="text-align:right;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;width:20%;vertical-align:middle; display:table-cell !important;font-size:8.0pt; font-family:\'Arial\',\'sans-serif\'; color:#666666;" valign="middle"></td>
</tr>
</table>
</div>';
		return $html;
	}

}

$ic_mailer_sender = new ic_mailer_sender;
