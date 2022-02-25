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
if ( !function_exists( 'ic_select_page' ) ) {

	function ic_select_page( $option_name, $first_option, $selected_value, $buttons = false, $custom_view_url = false,
						  $echo = 1, $custom = false ) {
		$args		 = array(
			'sort_order'	 => 'ASC',
			'sort_column'	 => 'post_title',
			'hierarchical'	 => 1,
			'exclude'		 => '',
			'include'		 => '',
			'meta_key'		 => '',
			'meta_value'	 => '',
			'authors'		 => '',
			'child_of'		 => 0,
			'parent'		 => -1,
			'exclude_tree'	 => '',
			'number'		 => '',
			'offset'		 => 0,
			'post_type'		 => 'page',
			'post_status'	 => 'publish'
		);
		remove_all_actions( 'get_pages' );
		$pages		 = get_pages( $args );
		$select_box	 = '<div class="select-page-wrapper"><select id="' . $option_name . '" name="' . $option_name . '"><option value = "noid">' . $first_option . '</option>';
		foreach ( $pages as $page ) {
			$select_box .= '<option name="' . $option_name . '[' . $page->ID . ']" value="' . $page->ID . '" ' . selected( $page->ID, $selected_value, 0 ) . '>' . $page->post_title . '</option>';
		}
		if ( $custom ) {
			$select_box .= '<option value="custom"' . selected( 'custom', $selected_value, 0 ) . '>' . __( 'Custom URL', 'ecommerce-product-catalog' ) . '</option>';
		}
		$select_box .= '</select>';
		if ( $buttons && ($selected_value != 'noid' || $custom_view_url != '') ) {
			$edit_link	 = get_edit_post_link( $selected_value );
			$front_link	 = $custom_view_url ? $custom_view_url : get_permalink( $selected_value );
			if ( !empty( $edit_link ) ) {
				$select_box .= ' <a class="button button-small" style="vertical-align: middle;" href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			}
			if ( !empty( $front_link ) ) {
				$select_box .= ' <a class="button button-small" style="vertical-align: middle;" href="' . $front_link . '">' . __( 'View Page' ) . '</a>';
			}
		}
		$select_box .= '</div>';
		return echo_ic_setting( $select_box, $echo );
	}

}

if ( !function_exists( 'ic_get_user_email' ) ) {

	function ic_get_user_email( $user_id ) {
		$userdata = get_userdata( $user_id );
		if ( !$userdata ) {
			return '';
		}
		if ( is_email( $userdata->user_email ) ) {
			return $userdata->user_email;
		} else if ( is_email( $userdata->user_login ) ) {
			return $userdata->user_login;
		}
		return '';
	}

}
if ( !function_exists( 'ic_get_post_type_label' ) ) {

	function ic_get_post_type_label( $post_type ) {
		$object = get_post_type_object( $post_type );
		if ( isset( $object->label ) ) {
			return $object->label;
		}
		return false;
	}

}
if ( !function_exists( 'ic_get_post_type_posts' ) ) {

	function ic_get_post_type_posts( $post_type ) {
		$args[ 'post_type' ]		 = $post_type;
		$args[ 'orderby' ]			 = 'date';
		$args[ 'order' ]			 = 'DESC';
		$args[ 'posts_per_page' ]	 = 1000;
		$args[ 'post_status' ]		 = 'publish';
		$posts_array				 = get_posts( $args );
		$av_posts					 = array();
		foreach ( $posts_array as $post ) {
			$post_title				 = empty( $post->post_title ) ? $post->ID : $post->post_title . ' (' . $post->ID . ')';
			$av_posts[ $post->ID ]	 = $post_title;
		}
		return $av_posts;
	}

}
if ( !function_exists( 'ic_filemtime' ) ) {

	function ic_filemtime( $path ) {
		if ( file_exists( $path ) ) {
			return '?' . filemtime( $path );
		}
	}

}
if ( !function_exists( 'get_all_products' ) ) {

	/**
	 * Returns array of all products objects
	 * @return array
	 */
	function get_all_products( $args = null ) {
		$args[ 'post_type' ]		 = product_post_type_array();
		$args[ 'post_status' ]		 = 'publish';
		$args[ 'posts_per_page' ]	 = -1;
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
<div style="background:#ffffff;width:598px;max-width:100%;padding:0 0 10px 0;margin:0 auto 0 auto;border: 1px solid #cdcdcd;">
<div style="height:30px;clear:both;float:none;"> </div>
<div style="padding:15px 20px 20px 20px;margin: 0;font-size:16px;text-align:left;line-height:1.5em">' . str_replace( '<br>', '<br>' . "\n", $message ) . '</div>
<div style="height:30px;clear:both;float:none;"> </div>
</div>';
		if ( !is_email( $sender_name ) ) {
			$htmlized .= '<div style="text-align:center;line-height:1.5em;padding:5px;font-size:12px;color:#696969;width:100%;">' . sprintf( __( 'This email is a service from %s.', 'al-implecode-license-system' ), '<a href="' . site_url() . '" style="color:#696969">' . $sender_name . '</a>' ) . '</div>';
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
			$table_tag = ic_email_table();
			if ( strpos( $ic_mail_content, $table_tag ) !== false ) {
				preg_match_all( "/<tr[^>]*>(.*?)<\/tr>/s", $ic_mail_content, $matches );
				$rows	 = $matches[ 1 ];
				preg_match_all( "/<td[^>]*>(.*?)<\/td>/s", $rows[ 0 ], $matches );
				$th		 = $matches[ 1 ];
				$replace = '';
				foreach ( $rows as $row_key => $row ) {
					if ( $row_key != 0 ) {
						preg_match_all( "/<td[^>]*>(.*?)<\/td>/s", $row, $matches );
						$row_fields = $matches[ 1 ];
						foreach ( $row_fields as $key => $field ) {
							$replace .= $th[ $key ] . ': ' . $field . "<br>";
						}
						$replace .= "<br>";
					}
				}
				$search			 = "/[^<table[^>]*>](.*)[^</table>]/s";
				$ic_mail_content = preg_replace( "/<table[^>]*>(.*?)<\/table>/s", $replace, $ic_mail_content );
			}
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
		$p = '<table cellspacing="0" cellpadding="10" border="0" style="margin: 15px 20px;color:#555555;border: 1px solid #555555;">';
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
			$headers[]	 = 'Content-type: multipart/alternative';
			$message	 = ic_htmlize_email( $message, $title, $sender_name );
		} else {
			$headers[]	 = 'Content-type: text/plain';
			$message	 = strip_tags( str_replace( array( '<br>', '</p>', '<ul>' ), array( "\r\n", "\r\n", "\r\n" ), $message ), "\r\n" );
		}
		wp_mail( $receiver_email, $title, $message, $headers, $attachments );
	}

}

if ( !function_exists( 'ic_mail_simple_html' ) ) {

	/**
	 * Sends email
	 * @param string $sender_name
	 * @param email $sender_email
	 * @param email $receiver_email
	 * @param string $title
	 * @param boolean $template
	 */
	function ic_mail_simple_html( $message, $sender_name, $sender_email, $receiver_email, $title, $attachments = null ) {
		$headers[] = 'From: ' . $sender_name . ' <' . $sender_email . '>';
		if ( is_email( $sender_name ) ) {
			$headers[] = 'Reply-To: <' . $sender_name . '>';
		}

		$headers[]	 = 'Content-type: text/html; charset=UTF-8';
		$headers[]	 = 'Content-Transfer-Encoding: quoted-printable';
		$message	 = ic_mail_simple_htmlize( $message, $title );

		wp_mail( $receiver_email, $title, $message, $headers, $attachments );
	}

}

if ( !function_exists( 'ic_mail_simple_htmlize' ) ) {

	function ic_mail_simple_htmlize( $message, $title ) {
		$htmlized = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>' . $title . '</title>

		<!--[if gte mso 9]><xml>
	      <o:OfficeDocumentSettings>
		    <o:AllowPNG/>
		    <o:PixelsPerInch>96</o:PixelsPerInch>
		  </o:OfficeDocumentSettings>
		</xml><![endif]-->

	</head>
	<body style="margin:0;padding:0;min-width:100%;background-color:#ffffff;">

	<div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;"></div>

			' . $message . '
</body>
</html>';


		return $htmlized;
	}

}