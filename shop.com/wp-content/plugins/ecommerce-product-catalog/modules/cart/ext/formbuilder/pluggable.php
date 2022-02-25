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
		$htmlized	 = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>' . $title . '</title>
</head>
<body style="">
<style type="text/css">li {font-size:16px;line-height:1.5em;font-weight:bold}
	ul {width:80%;padding-left:30%} p{font-size: 16px;}';
		$htmlized	 .= apply_filters( 'ic_catalog_notification_styling', '' );
		$htmlized	 .= '
</style>
<div style="font-family: Verdana, sans-serif;color:#555555;font-size:16px;line-height:20px;background:#f5f5f5;width:100%;padding:25px 0 25px 0;margin:0;">';
		$htmlized	 .= '<div style="width:598px;max-width:100%;padding:0 0 10px 0;margin:0 auto 0 auto;">';
		$htmlized	 .= apply_filters( 'ic_catalog_notification_before_border_div', '' );
		$htmlized	 .= '
<div style="' . apply_filters( 'ic_catalog_notification_border_div_styling', 'background:#ffffff;width: 100%;border: 1px solid #cdcdcd;clear:both;' ) . '">';
		$htmlized	 .= apply_filters( 'ic_catalog_notification_before_top_div', '' );
		$htmlized	 .= '
<div style="' . apply_filters( 'ic_catalog_notification_top_div_styling', 'height:30px;clear:both;float:none;' ) . '">' . apply_filters( 'ic_catalog_notification_top_div_content', ' ' ) . '</div>
<div style="padding:15px 20px 20px 20px;margin: 0;font-size:16px;text-align:left;line-height:1.5em">' . str_replace( '<br>', '<br>' . "\n", $message ) . '</div>
<div style="height:30px;clear:both;float:none;"> </div>
</div></div>';
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

if ( !function_exists( 'ic_email_paragraph' ) ) {

	/**
	 * Initializes HTML email paragraph
	 *
	 * @return string
	 */
	function ic_email_paragraph( $style = null ) {
		$p = '<p style="' . $style . '">';
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

if ( !function_exists( 'ic_email_table' ) ) {

	/**
	 * Initializes HTML email table
	 * @return string
	 */
	function ic_email_table() {
		$p = '<table cellspacing="0" cellpadding="10" border="0" style="margin: 15px 0;color:#555555;border: 1px solid #555555;">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_end' ) ) {

	/**
	 * Finishes HTML email table
	 * @return string
	 */
	function ic_email_table_end() {
		$p = '</table>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_tr' ) ) {

	/**
	 * Initializes HTML email tr
	 * @return string
	 */
	function ic_email_table_tr() {
		$p = '<tr>';
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_tr_end' ) ) {

	/**
	 * Finishes HTML email tr
	 * @return string
	 */
	function ic_email_table_tr_end() {
		$p = '</tr>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_th' ) ) {

	/**
	 * Initializes HTML email tr
	 * @return string
	 */
	function ic_email_table_th() {
		$p = '<tr style="font-weight: bold;">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_th_end' ) ) {

	/**
	 * Finishes HTML email tr
	 * @return string
	 */
	function ic_email_table_th_end() {
		$p = '</tr>' . "\n";
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_td' ) ) {

	/**
	 * Initializes HTML email td
	 * @return string
	 */
	function ic_email_table_td() {
		$p = '<td style="text-align: center;">';
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_td_first' ) ) {

	/**
	 * Initializes HTML email td
	 * @return string
	 */
	function ic_email_table_td_first() {
		$p = '<td>';
		return $p;
	}

}

if ( !function_exists( 'ic_email_table_td_end' ) ) {

	/**
	 * Finishes HTML email td
	 * @return string
	 */
	function ic_email_table_td_end() {
		$p = '</td>' . "\n";
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
			$headers[]	 = 'Content-type: text/html';
			$message	 = ic_htmlize_email( $message, $title, $sender_name );
		} else {
			$headers[]	 = 'Content-type: text/plain';
			$message	 = strip_tags( str_replace( array( '<br>', '</p>', '<ul>' ), array( "\r\n", "\r\n", "\r\n" ), $message ), "\r\n" );
		}
		wp_mail( $receiver_email, $title, $message, $headers, $attachments );
	}

}

if ( !function_exists( 'ic_container_shortcode' ) ) {
	add_shortcode( 'ic_container', 'ic_container_shortcode' );

	function ic_container_shortcode( $atts ) {
		$available_args	 = array(
			'container'	 => 'span',
			'style'		 => '',
		);
		$args			 = shortcode_atts( $available_args, $atts );
		$style			 = $args[ 'style' ];
		return '<' . $args[ 'container' ] . ' style="' . $style . '">';
	}

}

if ( !function_exists( 'ic_container_shortcode_close' ) ) {
	add_shortcode( 'ic_container_close', 'ic_container_shortcode_close' );

	function ic_container_shortcode_close( $atts ) {
		$available_args	 = array(
			'container' => 'span',
		);
		$args			 = shortcode_atts( $available_args, $atts );
		return '</' . $args[ 'container' ] . '>';
	}

}