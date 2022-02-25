<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @author								Andrei Zobnin <fiter92@gmail.com>
 * @link              		https://zobnin.dev/wordpress/plugins/text-attributes-for-woocommerce
 * @since             		1.0.0
 * @version           		1.0.3
 * @package           		Zobnin_Text_Attributes_For_WooCommerce
 *
 * @wordpress-plugin
 * Plugin Name:       		Text Attributes for WooCommerce
 * Plugin URI:        		https://zobnin.dev/wordpress/plugins/text-attributes-for-woocommerce
 * Description:       		Allows you to type attribute values in the input field instead of choosing from the drop-down list
 * Version:           		1.0.3
 * Author:            		Andrei Zobnin
 * Author URI:        		https://zobnin.dev
 * License:           		GPL-2.0+
 * License URI:       		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       		text-attributes-for-woocommerce
 * Domain Path:       		/languages
 * Requires at least: 		4.7
 * Tested up to: 					5.4
 * WC requires at least: 	3.6
 * WC tested up to: 			4.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'ZOBNIN_TEXT_ATTRIBUTES_FOR_WOOCOMMERCE_VERSION' ) ) {
	define( 'ZOBNIN_TEXT_ATTRIBUTES_FOR_WOOCOMMERCE_VERSION', '1.0.3' );
	include_once 'bootstrap.php';
	// plugins loaded callback
	add_action( 'plugins_loaded', 'zobnin_text_attributes_for_woocommerce_on_all_plugins_loaded', 21 );
}
