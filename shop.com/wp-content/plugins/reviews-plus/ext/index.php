<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages functions folder
 *
 * Here functions folder is defined and managed.
 *
 * @version		1.0.0
 * @package		implecode-product-pdf/functions
 * @author 		Norbert Dreszer
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once(AL_REVIEWS_BASE_PATH . '/ext/woocommerce.php');
}
