<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * 
 * @since             1.0.0
 * @package           Zobnin_Text_Attributes_For_WooCommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

spl_autoload_register( function( $class ) {
  $classes = array(
    'Zobnin_Text_Attributes_For_WooCommerce_Loader' => 'includes/class-text-attributes-for-woocommerce-loader.php',
    'Zobnin_Text_Attributes_For_WooCommerce_i18n' => 'includes/class-text-attributes-for-woocommerce-i18n.php',
    'Zobnin_Text_Attributes_For_WooCommerce' => 'includes/class-text-attributes-for-woocommerce.php',
    'Zobnin_Text_Attributes_For_WooCommerce_Admin' => 'admin/class-text-attributes-for-woocommerce-admin.php',
  );

  // if the file exists, require it
  $path = plugin_dir_path( __FILE__ );
  if ( array_key_exists( $class, $classes ) && file_exists( $path.$classes[ $class ] ) ) {
    require $path . $classes[ $class ];
  }
});

function run_zobnin_text_attributes_for_woocommerce() { 
  $plugin = new Zobnin_Text_Attributes_For_WooCommerce( ZOBNIN_TEXT_ATTRIBUTES_FOR_WOOCOMMERCE_VERSION );
  $plugin->run();
}

function zobnin_text_attributes_for_woocommerce_on_all_plugins_loaded() {
  if ( zobnin_text_attributes_for_woocommerce_check_woocommerce_plugin_status() ) {
    run_zobnin_text_attributes_for_woocommerce();
  }
}

/**
 * Check if the WooCommerce is active
 * @return bool
 */
function zobnin_text_attributes_for_woocommerce_check_woocommerce_plugin_status()
{
  // if you are using a custom folder name other than woocommerce just define the constant to TRUE
  if ( defined( 'RUNNING_CUSTOM_WOOCOMMERCE' ) && RUNNING_CUSTOM_WOOCOMMERCE === true ) {
    return true;
  }
  // it the plugin is active, we're good.
  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return true;
  }
  if ( ! is_multisite() ) return false;
  $plugins = get_site_option( 'active_sitewide_plugins' );
  return isset( $plugins[ 'woocommerce/woocommerce.php' ] );
}