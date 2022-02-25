<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 *
 *  @version       1.0.0
 *  @package
 *  @author        impleCode
 *
 */

add_action( 'wp', 'ic_woo_ic_cart_enable', -2 );
add_action( 'ic_cart_products_start', 'ic_woo_ic_cart_enable', -2 );

function ic_woo_ic_cart_enable() {
	if ( (function_exists( 'is_ic_shopping_page' ) && is_ic_shopping_page()) || is_ic_ajax( 'ic_add_to_cart' ) || is_ic_ajax( 'shopping_cart_products' ) ) {
		add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_cart_enable' );
	}
}

function ic_woo_ic_post_type_cart_enable( $array ) {
	$array[] = 'product';
	return $array;
}
