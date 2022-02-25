<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages Discounts payment form
 *
 * Here Discounts payment form is defined and managed.
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-booster/includes
 * @author 		Norbert Dreszer
 */
add_filter( 'register_post_type_args', 'ic_woocat_disable_catalog', 10, 2 );

function ic_woocat_disable_catalog( $args, $post_type ) {
	if ( $post_type === 'al_product' ) {
		$ic_woocat = ic_woocat_settings();
		if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) ) {
			$args[ 'public' ] = false;
		}
	}
	return $args;
}

//add_filter( 'pre_option_enable_product_listing', 'ic_woocat_disable_main_listing' );

function ic_woocat_disable_main_listing( $value ) {
	$ic_woocat = ic_woocat_settings();
	if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) ) {
		$value = 0;
	}
	return $value;
}

add_filter( 'woocommerce_page_title', 'ic_woocat_page_title' );

function ic_woocat_page_title( $title ) {
	if ( is_ic_shortcode_integration() ) {
		$query = ic_get_catalog_query();
		if ( is_ic_taxonomy_page( $query ) ) {
			if ( !empty( $query->queried_object->name ) ) {
				$title = get_product_tax_title( $query->queried_object->name );
			}
		}
	}
	return $title;
}
