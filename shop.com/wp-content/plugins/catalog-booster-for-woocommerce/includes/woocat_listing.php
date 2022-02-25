<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages WooCommerce Listing desgin
 *
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-booster/includes
 * @author 		Norbert Dreszer
 */
add_action( 'ic_epc_loaded', 'ic_woocat_listing_setup' );

function ic_woocat_listing_setup() {
	if ( !is_ic_shortcode_integration() ) {
		global $ic_catalog_template;
		if ( !empty( $ic_catalog_template ) ) {
			add_action( 'parse_query', array( $ic_catalog_template, 'initialize_product_adder_template' ), 99 );
		} else if ( function_exists( 'initialize_product_adder_template' ) ) {
			add_action( 'parse_query', 'initialize_product_adder_template', 99 );
		}
	}
}

add_action( 'pre_get_posts', 'ic_woo_ic_listing_enable', 2 );
add_action( 'ic_ajax_self_submit_init', 'ic_woo_ic_listing_enable' );

function ic_woo_ic_listing_enable() {
	$enabled = ic_is_listing_for_woo_enabled();
	if ( !empty( $enabled ) ) {
		remove_filter( 'product_price', array( 'ic_price_display', 'raw_price_format' ), 5 );

		add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_enable' );
		add_filter( 'product_taxonomy_array', 'ic_woo_ic_listing_tax_enable' );
		add_filter( 'current_product_post_type', 'ic_woo_ic_listing_post_type' );
		add_filter( 'ic_current_product_tax', 'ic_woo_ic_listing_taxonomy' );
		add_filter( 'current_product_catalog_taxonomy', 'ic_woo_ic_listing_taxonomy' );
		add_filter( 'show_categories_taxonomy', 'ic_woo_ic_listing_taxonomy' );
		add_filter( 'price_format', 'ic_woo_price_format', 10, 2 );
		add_filter( 'widget_product_categories_dropdown_args', 'ic_listing_category_widget_tax' );
		add_filter( 'widget_product_categories_args', 'ic_listing_category_widget_tax' );
		add_filter( 'shortcode_query', 'ic_listing_shortcode_query_post_type' );
		add_filter( 'home_product_listing_query', 'ic_listing_home_query' );
		add_filter( 'product_listing_id', 'ic_listing_product_listing_id' );
		add_filter( 'ic_category_image_id', 'ic_woo_category_image_id', 10, 2 );
		if ( is_ic_shortcode_integration() && (is_ic_product_listing() || is_ic_taxonomy_page() || is_ic_product_search()) ) {
			//ic_woo_listing_reset_globals();
			remove_all_actions( 'woocommerce_before_shop_loop' );
			remove_all_actions( 'woocommerce_after_shop_loop' );
			remove_all_actions( 'woocommerce_shop_loop' );
			remove_action( 'template_redirect', array( 'WC_Template_Loader', 'unsupported_theme_init' ) );
			//remove_filter( 'the_content', array( 'WC_Template_Loader', 'unsupported_theme_shop_content_filter' ), 10 );
			add_action( 'woocommerce_shop_loop', 'ic_woo_remove_products_from_loop' );
			add_action( 'woocommerce_before_shop_loop', 'ic_woo_shortcode_mode_listing' );
			add_action( 'woocommerce_before_shop_loop', 'ic_woo_remove_products_from_loop' );
		}
		if ( function_exists( 'ic_get_listing_template_path' ) ) {
			add_action( 'woocommerce_shortcode_before_featured_products_loop', 'ic_woo_manage_shortcode_listing' );
			add_action( 'woocommerce_shortcode_after_featured_products_loop', 'ic_woo_manage_shortcode_listing_end' );
		}
	}
}

function ic_woo_manage_shortcode_listing() {
	ic_enqueue_main_catalog_js_css();
	$archive_template = get_product_listing_template();
	echo '<div class="product-list responsive ' . $archive_template . ' ' . product_list_class( $archive_template ) . '" ' . product_list_attr() . '>';
	add_filter( 'wc_get_template_part', 'ic_woo_shortcode_listing', 10, 3 );
}

function ic_woo_manage_shortcode_listing_end() {
	remove_filter( 'wc_get_template_part', 'ic_woo_shortcode_listing', 10, 3 );
	echo '</div>';
}

function ic_woo_shortcode_listing( $template, $slug, $name ) {
	if ( $slug === 'content' && $name === 'product' ) {
		$path		 = ic_get_listing_template_path();
		$template	 = ic_get_template_file( $path[ 'file' ], $path[ 'base' ] );
	}
	return $template;
}

function ic_woo_listing_reset_globals() {
	global $implecode;
	$implecode = array();
}

function ic_woo_remove_products_from_loop() {
	global $product;
	$product = '';
	//unset( $GLOBALS[ 'woocommerce_loop' ] );
	unset( $GLOBALS[ 'product' ] );
	add_action( 'the_post', 'ic_woo_remove_products_from_loop' );
}

function ic_woo_shortcode_mode_listing() {
	global $ic_shortcode_catalog;
	if ( ( is_ic_product_listing() || is_ic_taxonomy_page()) && !empty( $ic_shortcode_catalog ) ) {
		$ic_shortcode_catalog->catalog_query();
		$ic_shortcode_catalog->product_listing();
	}
}

function ic_woo_ic_post_type_enable( $array ) {
	if ( ic_woocat_enable_on_listing() ) {
		$array[] = 'product';
	}
	return $array;
}

function ic_woo_ic_listing_tax_enable( $array ) {
	if ( ic_woocat_enable_on_listing() ) {
		$array[] = 'product_cat';
	}
	return $array;
}

function ic_woo_ic_listing_post_type( $post_type ) {
	if ( ic_woocat_enable_on_listing() ) {
		return 'product';
	}
	return $post_type;
}

function ic_listing_category_widget_tax( $widget_args ) {
	if ( ic_woocat_enable_on_listing() ) {
		$widget_args[ 'taxonomy' ] = 'product_cat';
	}
	return $widget_args;
}

function ic_woo_ic_listing_taxonomy( $taxonomy ) {
	if ( ic_woocat_enable_on_listing() ) {
		return 'product_cat';
	}
	return $taxonomy;
}

function ic_woo_price_format( $formatted, $raw ) {
	if ( ic_woocat_enable_on_listing() ) {
		$set		 = get_currency_settings();
		$raw		 = str_replace( array( $set[ 'th_sep' ], $set[ 'dec_sep' ] ), array( '', '.' ), $raw );
		$formatted	 = wc_price( $raw );
	}
	return $formatted;
}

function ic_listing_product_listing_id( $id ) {
	$ic_woocat = ic_woocat_settings();
	if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) || ic_woocat_enable_on_listing() ) {
		$id = get_option( 'woocommerce_shop_page_id' );
	}

	return $id;
}

function ic_listing_shortcode_query_post_type( $query ) {
	$ic_woocat = ic_woocat_settings();
	if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) || ic_woocat_enable_on_listing() ) {
		$query[ 'post_type' ] = 'product';
	}
	return $query;
}

function ic_listing_home_query( $query ) {
	$ic_woocat = ic_woocat_settings();
	if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) || ic_woocat_enable_on_listing() ) {
		$query[ 'post_type' ]	 = 'product';
		$query[ 'is_archive' ]	 = true;
	}
	return $query;
}

function ic_woocat_enable_on_listing() {
	global $ic_ajax_query_vars;
	$obj = get_queried_object();
	if ( (is_object( $obj ) && is_shop()) || is_product_category() || is_product_taxonomy() || (is_search() && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'product') || (is_ic_ajax() && !empty( $ic_ajax_query_vars[ 'post_type' ] ) && $ic_ajax_query_vars[ 'post_type' ] === 'product') ) {
		return true;
	}
	return false;
}

function ic_woo_category_image_id( $img_id, $cat_id ) {
	if ( empty( $img_id ) && function_exists( 'get_term_meta' ) ) {
		$img_id = get_term_meta( $cat_id, 'thumbnail_id', true );
	}
	return $img_id;
}
