<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Defines pluggable functions
 *
 * @version		1.0.0
 * @package		product-gallery-advanced/functions
 * @author 		Norbert Dreszer
 */
if ( !function_exists( 'get_all_products' ) ) {

	/**
	 * Returns array of all products objects
	 * @return array
	 */
	function get_all_products( $args = null ) {
		$args[ 'post_type' ]		 = product_post_type_array();
		$args[ 'post_status' ]		 = 'publish';
		$args[ 'posts_per_page' ]	 = 1000;
		$digital_products			 = get_posts( $args );
		return $digital_products;
	}

}

if ( !function_exists( 'ic_htmlize_email' ) ) {

	/**
	 * Initializes HTML email template
	 *
	 * @global string $ic_mail_content
	 * @param string $message
	 * @param string $title
	 * @param string $sender_name
	 * @return string
	 */
	function ic_htmlize_email( $message, $title, $sender_name ) {
		$htmlized = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>' . $title . '</title>
</head>
<body style="">
<style type="text/css">li {font-size:16px;line-height:1.5em;font-weight:bold}
	ul {width:80%;padding-left:30%}
</style>
<div style="font-family: Verdana, sans-serif;color:#555555;font-size:13px;line-height:20px;background:#f5f5f5;width:100%;padding:25px 0 25px 0;margin:0;">
<div style="background:#ffffff;width:598px;padding:0 0 10px 0;margin:0 auto 0 auto;border: 1px solid #cdcdcd;">
<div style="height:30px;clear:both;float:none;"> </div>
' . $message . '
<div style="height:30px;clear:both;float:none;"> </div>
</div>';
		if ( !is_email( $sender_name ) ) {
			$htmlized .= '<div style="text-align:center;line-height:1.5em;padding:5px;font-size:12px;color:#696969;width:100%;">' . sprintf( __( 'This email is a service from %s.', 'ecommerce-product-catalog' ), '<a href="' . site_url() . '" style="color:#696969">' . $sender_name . '</a>' ) . '</div>';
		}
		$htmlized		 .= '</div>
</body>
</html>';
		global $ic_mail_content;
		$ic_mail_content = $message;
		return $htmlized;
	}

}

if ( !function_exists( 'ic_mail_alternate' ) ) {
	add_filter( 'phpmailer_init', 'ic_mail_alternate' );

	/**
	 * Adds text email as alternative to the HTML
	 *
	 * @global string $ic_mail_content
	 * @param object $mailer
	 * @return object
	 */
	function ic_mail_alternate( $mailer ) {
		global $ic_mail_content;
		if ( isset( $ic_mail_content ) && !empty( $ic_mail_content ) ) {
			$button = addslashes( ic_email_button( '(.*?)' ) );
			if ( strpos( $button, $ic_mail_content ) !== false ) {
				$ic_mail_content = preg_replace( '/' . $button . '(.*?)<\/a>/i', '', $ic_mail_content );
			}
			$mailer->AltBody = strip_tags( str_replace( array( '<br>', '</p>', '<ul>' ), array( "\n", "\n", "\n" ), $ic_mail_content ) );
			$ic_mail_content = '';
			unset( $ic_mail_content );
		}
		return $mailer;
	}

}

if ( !function_exists( 'ic_email_paragraph' ) ) {

	/**
	 * Initializes HTML email paragraph
	 *
	 * @return string
	 */
	function ic_email_paragraph() {
		$p = '<p style="padding:15px 20px 20px 20px;margin: 0;font-size:16px;text-align:left;line-height:1.5em">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_paragraph_end' ) ) {

	/**
	 * Initializes HTML email paragraph
	 *
	 * @return string
	 */
	function ic_email_paragraph_end() {
		$p = '</p>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_ul' ) ) {

	/**
	 * Initializes HTML email ul
	 * @return string
	 */
	function ic_email_ul() {
		$p = '<ul style="width:70%;padding-left:10%">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_ul_end' ) ) {

	/**
	 * Initializes HTML email ul
	 * @return string
	 */
	function ic_email_ul_end() {
		$p = '</ul>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_li' ) ) {

	/**
	 * Initializes HTML email li
	 * @return string
	 */
	function ic_email_li() {
		$p = '<li style="font-size:16px;line-height:1.5em;font-weight:bold">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_li_end' ) ) {

	/**
	 * Initializes HTML email li
	 * @return string
	 */
	function ic_email_li_end() {
		$p = '</li>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_button' ) ) {

	/**
	 * Initializes HTML email button
	 * @param type $link
	 * @return string
	 */
	function ic_email_button( $link ) {
		$a = '<a class="remove-plain" style="width:125px; display: block; font-size:15px; background-color:#bb0000;color:#ffffff; text-decoration:none; text-align:center; border-radius:10px; margin:10px auto; padding: 15px 10px 15px 10px;" target="_blank" href="' . $link . '">';
		return $a;
	}

}

if ( !function_exists( 'ic_mail' ) ) {

	/**
	 * Sends email
	 * @param string $sender_name
	 * @param email $sender_email
	 * @param email $receiver_email
	 * @param string $title
	 * @param boolean $template
	 */
	function ic_mail( $message, $sender_name, $sender_email, $receiver_email, $title, $template = true, $attachments = null ) {
		$headers[] = 'From: ' . $sender_name . ' <' . $sender_email . '>';
		if ( is_email( $sender_name ) ) {
			$headers[] = 'Reply-To: <' . $sender_name . '>';
		}
		if ( $template ) {
			$headers[]	 = 'Content-type: multipart/alternative';
			$message	 = ic_htmlize_email( $message, $title, $sender_name );
		} else {
			$headers[] = 'Content-type: text/plain';
		}
		wp_mail( $receiver_email, $title, $message, $headers, $attachments );
	}

}

if ( !function_exists( 'implecode_array_variables_init' ) ) {

	function implecode_array_variables_init( $fields, $data ) {
		if ( !is_array( $data ) ) {
			$data = array();
		}
		foreach ( $fields as $field ) {
			$data[ $field ] = isset( $data[ $field ] ) ? $data[ $field ] : '';
		}
		return $data;
	}

}

if ( !function_exists( 'get_supported_country_name' ) ) {

	/**
	 * Returns country name by its code
	 *
	 * @param string $country_code
	 * @return string
	 */
	function get_supported_country_name( $country_code ) {
		$return		 = 'none';
		$countries	 = implecode_supported_countries();
		foreach ( $countries as $key => $country ) {
			if ( $country_code == $key ) {
				$return = $country;
			}
		}
		if ( $return == 'none' && array_search( $country_code, $countries ) ) {
			$return = $country_code;
		}
		return $return;
	}

}