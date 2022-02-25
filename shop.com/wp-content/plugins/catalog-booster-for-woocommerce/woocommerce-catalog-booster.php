<?php

/**
 * Plugin Name: WooCommerce Catalog Booster & Product Catalog Mode
 * Plugin URI: http://implecode.com
 * Description: Switch WooCoomerce into product catalog and/or customize the view for your purpose.
 * Version: 1.2.1
 * Author: impleCode
 * Author URI: http://implecode.com
 * Text Domain: catalog-booster-for-woocommerce
 * Domain Path: /lang/
 * WC requires at least: 3.0.0
 * WC tested up to: 6.1

  Copyright: 2021 impleCode.
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'IC_WOOCAT_BASE_URL', plugins_url( '/', __FILE__ ) );
define( 'IC_WOOCAT_BASE_PATH', dirname( __FILE__ ) );
define( 'IC_WOOCAT_MAIN_FILE', __FILE__ );

add_action( 'after_setup_theme', 'start_ic_woocat' );

function start_ic_woocat() {

	load_plugin_textdomain( 'catalog-booster-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	if ( ic_is_woocommerce_active() ) {
		require_once(IC_WOOCAT_BASE_PATH . '/includes/index.php' );
	} else {
		require_once(IC_WOOCAT_BASE_PATH . '/functions/compatibility.php' );
	}
}

//add_filter( 'is_ic_shortcode_integration', 'ic_disable_shortcode_integration' );

function ic_disable_shortcode_integration( $return ) {
	if ( ic_is_woocommerce_active() ) {
		return false;
	}
	return $return;
}

add_action( 'admin_init', 'ic_woocat_register_admin_styles' );

/**
 * Registers plugins stylesheets
 *
 */
function ic_woocat_register_admin_styles() {
	wp_register_style( 'ic_woocat_admin', IC_WOOCAT_BASE_URL . 'css/woocat-admin.css?' . filemtime( IC_WOOCAT_BASE_PATH . '/css/woocat-admin.css' ) );
}

add_action( 'admin_enqueue_scripts', 'ic_woocat_enqueue_admin_styles' );

/**
 * Enqueues plugins stylesheets
 *
 */
function ic_woocat_enqueue_admin_styles() {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'ic-catalog-mode' ) {
		wp_enqueue_style( 'ic_woocat_admin' );
	}
}

register_activation_hook( __FILE__, 'IC_WOOCAT_install' );

if ( !function_exists( 'IC_WOOCAT_install' ) ) {

	function IC_WOOCAT_install() {
		require_once(IC_WOOCAT_BASE_PATH . '/includes/woocat_settings.php' );
		require_once(IC_WOOCAT_BASE_PATH . '/functions/activation.php' );
		ic_woocat_activation();
	}

}
if ( !function_exists( 'ic_is_woocommerce_active' ) ) {

	function ic_is_woocommerce_active() {
		/*
		  $plugin_file = 'woocommerce/woocommerce.php';
		  if ( !is_multisite() ) {
		  if ( in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		  return true;
		  }
		  } else {
		  $plugins = get_site_option( 'active_sitewide_plugins' );
		  if ( isset( $plugins[ $plugin_file ] ) ) {
		  return true;
		  }
		  }

		  return false;
		 *
		 */
		if ( class_exists( 'woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

}