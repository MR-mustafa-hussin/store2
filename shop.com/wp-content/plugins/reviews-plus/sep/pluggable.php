<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product functions
 *
 * Here all plugin functions are defined and managed.
 *
 * @version        1.0.0
 * @package        price-field/functions
 * @author        Norbert Dreszer
 */
if ( !function_exists( 'ic_string_contains' ) ) {

	function ic_string_contains( $string, $contains ) {
		if ( strpos( $string, $contains ) !== false ) {
			return true;
		}
		return false;
	}

}
if ( !function_exists( 'get_ic_product_name' ) ) {

	function get_ic_product_name( $product_id = null ) {
		return get_the_title( $product_id );
	}

}