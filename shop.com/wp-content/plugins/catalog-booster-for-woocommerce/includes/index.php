<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages Discounts gateaway includes folder
 *
 * Here includes files are defined and managed.
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-mode/includes
 * @author 		Norbert Dreszer
 */
require_once(IC_WOOCAT_BASE_PATH . '/includes/pluggable/index.php');
require_once(IC_WOOCAT_BASE_PATH . '/includes/tooltips.php');
require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_activation_wizard.php');
require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_settings.php');
$settings = ic_woocat_settings();
require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_disabler.php');
require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_button.php');
if ( function_exists( 'impleCode_EPC' ) ) {
	require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_catalog.php');
	require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_listing.php');
	require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_page.php');
	if ( !class_exists( 'ic_shopping_ecommerce_cart' ) || $settings[ 'general' ][ 'cart_mode' ] === 'woo' ) {
		require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_cart.php');
	} else {
		require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_ic_cart.php');
	}
}
if ( is_admin() ) {
	require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_admin_disabler.php');
}


