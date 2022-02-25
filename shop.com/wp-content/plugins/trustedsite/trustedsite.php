<?php
defined( 'ABSPATH' ) OR exit;

/**
 * ------------------------------------------------------------------------------------------------------------------
 * @package trustedsite
 * @version 1.1.2
 * Plugin Name: TrustedSite
 * Plugin URI: https://www.trustedsite.com/
 * Description: Display TrustedSite trustmarks on your website to increase visitor confidence and conversion rates.
 * Author: TrustedSite
 * Version: 1.1.2
 * ------------------------------------------------------------------------------------------------------------------
 */

if(defined('WP_INSTALLING') && WP_INSTALLING){
    return;
}
define('TRUSTEDSITE_VERSION', '1.1.2');

add_action('activated_plugin','trustedsite_save_activation_error');
function trustedsite_save_activation_error(){
    update_option('trustedsite_plugin_error', ob_get_contents());
}

require_once('lib/Trustedsite.php');
register_activation_hook(__FILE__, 'Trustedsite::activate');
register_deactivation_hook(__FILE__, 'Trustedsite::deactivate');
register_uninstall_hook(__FILE__, 'Trustedsite::uninstall');

Trustedsite::install();
