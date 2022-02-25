<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product content functions
 *
 * Here all plugin content functions are defined and managed.
 *
 * @version        1.0.0
 * @package        ecommerce-product-catalog/functions
 * @author        impleCode
 */
/* Classic List */

function c_list_desc( $post_id = null, $shortdesc = null ) {
	if ( $shortdesc == '' && ! empty( $post_id ) ) {
		$shortdesc = clean_short_description( $post_id );
	} else if ( ! empty( $shortdesc ) ) {
		$shortdesc = strip_tags( $shortdesc );
		$shortdesc = trim( strip_shortcodes( $shortdesc ) );
		$shortdesc = str_replace( array( "\r\n" ), ' ', $shortdesc );
	} else {
		return '';
	}
	$desclenght = strlen( $shortdesc );
	$more       = '';
	$limit      = apply_filters( 'c_list_desc_limit', 243 );
	if ( $desclenght > $limit ) {
		$more = ' [...]';
	}

	return apply_filters( 'c_list_desc_content', ic_substr( $shortdesc, 0, $limit ) . $more, $post_id );
}

/**
 * Returns short description text without HTML
 *
 * @param int $product_id
 *
 * @return string
 */
function clean_short_description( $product_id, $new_line = ' ' ) {
	$shortdesc = get_product_short_description( $product_id );
	$shortdesc = strip_tags( $shortdesc );
	$shortdesc = trim( strip_shortcodes( $shortdesc ) );
	$shortdesc = str_replace( array( "\r\n" ), $new_line, $shortdesc );

	return $shortdesc;
}

/* Single Product */
add_action( 'single_product_end', 'add_back_to_products_url', 99, 2 );

/**
 *
 * @param object $post
 * @param array $single_names
 * @param string $taxonomies
 */
function add_back_to_products_url( $post, $single_names ) {
	$force_back_url = apply_filters( 'force_back_to_products_url', false );
	if ( is_ic_product_listing_enabled() || $force_back_url ) {
		echo get_back_to_products_url( $single_names );
	}
}

/**
 * Returns back to products URL
 *
 * @param array $v_single_names
 *
 * @return string
 */
function get_back_to_products_url( $v_single_names = null ) {
	if ( is_ic_product_listing_enabled() ) {
		$single_names = isset( $v_single_names ) ? $v_single_names : get_single_names();
		$listing_url  = product_listing_url();
		if ( ! empty( $listing_url ) ) {
			$url = '<a class="back-to-products" href="' . product_listing_url() . '">' . $single_names['return_to_archive'] . '</a>';

			return $url;
		}
	}

	return;
}

/**
 * Shows product search form
 */
function product_search_form() {
	$search_button_text = __( 'Search', 'ecommerce-product-catalog' );
	echo '<form role="search" method="get" class="search-form product_search_form" action="' . esc_url( home_url( '/' ) ) . '">
<input type="hidden" name="post_type" value="' . get_current_screen_post_type() . '" />
<input class="product-search-box" type="search" value="' . get_search_query() . '" id="s" name="s" placeholder="' . __( 'Product Search', 'ecommerce-product-catalog' ) . '" />
<input class="search-submit product-search-submit" type="submit" value="' . esc_attr( $search_button_text ) . '" />
</form>';
}

function ic_enqueue_main_catalog_js_css() {
	$enqueued = ic_get_global( 'enqueued_main_js_css' );
	if ( $enqueued ) {
		return;
	}
	if ( ! did_action( 'ic_catalog_localize_scripts' ) ) {
		do_action( 'ic_catalog_localize_scripts' );
	}
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'al_product_styles' );
	wp_enqueue_script( 'al_product_scripts' );
	do_action( 'enqueue_main_catalog_scripts' );
	ic_save_global( 'enqueued_main_js_css', 1 );
}

add_action( 'ic_catalog_localize_scripts', 'ic_localize_main_catalog_js' );

function ic_localize_main_catalog_js() {
	$colorbox_set = json_decode( apply_filters( 'colorbox_set', '{"transition": "elastic", "initialWidth": 200, "maxWidth": "90%", "maxHeight": "90%", "rel":"gal"}', ic_get_product_id() ) );
	wp_localize_script( 'al_product_scripts', 'product_object', apply_filters( 'ic_catalog_product_object_js', array( 'ajaxurl'             => admin_url( 'admin-ajax.php' ),
	                                                                                                                  'post_id'             => ic_get_product_id(),
	                                                                                                                  'lightbox_settings'   => $colorbox_set,
	                                                                                                                  'filter_button_label' => __( 'Filter', 'ecommerce-product-catalog' ),
	                                                                                                                  'design_schemes'      => design_schemes( 'box', 0 )
	) ) );
}
