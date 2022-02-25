<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages activation functions
 *
 * Created by Norbert Dreszer.
 * Date: 19-Feb-15
 * Time: 13:40
 * Package: functions/
 */
function ic_woocat_activation() {
	if ( function_exists( 'impleCode_EPC' ) ) {
		$ic_woocat							 = ic_woocat_settings();
		$ic_woocat[ 'catalog' ][ 'enable' ]	 = 1;
		update_option( 'ic_woocat', $ic_woocat );
	}
	if ( function_exists( 'implecode_wp_tooltip_add' ) ) {
		implecode_wp_tooltip_add( __( 'New menu element', 'catalog-booster-for-woocommerce' ), __( 'A new Catalog Booster settings link has been added. Click here to see it.', 'catalog-booster-for-woocommerce' ), 'toplevel_page_woocommerce' );
		implecode_wp_tooltip_add( __( 'Catalog Booster Settings', 'catalog-booster-for-woocommerce' ), __( 'Click here to modify WooCommerce.', 'catalog-booster-for-woocommerce' ), 'toplevel_page_woocommerce .wp-submenu li:last' );
	}
	delete_option( 'IC_WOOCAT_activation_message_done' );
}
