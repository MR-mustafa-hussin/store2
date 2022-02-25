<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 *
 *  @version       1.0.0
 *  @author        impleCode
 *
 */
if ( ! class_exists( ( 'ic_html_util' ) ) ) {
	require_once( dirname( __FILE__ ) . '/html.php' );
}
if ( ! class_exists( ( 'ic_catalog_widget' ) ) ) {
	require_once( dirname( __FILE__ ) . '/widget.php' );
}
