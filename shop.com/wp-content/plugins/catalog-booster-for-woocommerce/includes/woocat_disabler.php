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
add_action( 'wp', 'ic_woocat_disabler' );

function ic_woocat_disabler() {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_cart' ] ) ) {
		add_filter( 'woocommerce_is_purchasable', 'ic_woocat_false' );
	}
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_price' ] ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_filter( 'woocommerce_get_price_html', 'ic_woocat_false' );
		//add_filter( 'woocommerce_get_price', 'ic_woocat_false' );
		add_filter( 'woocommerce_product_get_price', 'ic_woocat_false' );
		if ( function_exists( 'is_product' ) && is_product() ) {
			remove_action( 'product_details', array( 'ic_price_display', 'show_price' ), 7, 0 );
		}
	}
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_rating' ] ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_filter( 'option_woocommerce_enable_review_rating', 'ic_woocat_false' );
		add_filter( 'woocommerce_product_get_rating_html', 'ic_woocat_false' );
	}
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_reviews' ] ) ) {
		if ( is_product() ) {
			add_filter( 'comments_open', 'ic_woocat_false' );
		}
		remove_post_type_support( 'product', 'comments' );
	}
}

function ic_woocat_false() {
	return false;
}
