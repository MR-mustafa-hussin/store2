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
add_action( 'admin_init', 'ic_woocat_admin_disabler' );

function ic_woocat_admin_disabler() {
	$ic_woocat = ic_woocat_settings();
	if ( empty( $ic_woocat[ 'general' ][ 'in_dashboard' ] ) ) {
		return;
	}
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_price' ] ) ) {
		add_filter( 'manage_edit-product_columns', 'ic_woocat_disable_columns' );
		add_action( 'woocommerce_product_options_general_product_data', 'ic_woocat_admin_css' );
	}
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_reviews' ] ) ) {
		remove_post_type_support( 'product', 'comments' );
		add_action( 'add_meta_boxes', 'ic_woocat_disable_metaboxes', 99 );
	}
}

function ic_woocat_disable_columns( $columns ) {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_price' ] ) ) {
		unset( $columns[ 'price' ] );
	}
	return $columns;
}

function ic_woocat_admin_css() {
	$style		 = '';
	$ic_woocat	 = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_price' ] ) ) {
		$style .= '#general_product_data div.pricing ._regular_price_field, #general_product_data div.pricing ._sale_price_field  {display: none !important}';
	}
	if ( !empty( $style ) ) {
		echo '<style>';
		echo $style;
		echo '</style>';
	}
}

function ic_woocat_disable_metaboxes() {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'general' ][ 'disable_reviews' ] ) ) {
		remove_meta_box( 'commentsdiv', 'product', 'normal' );
	}
}
