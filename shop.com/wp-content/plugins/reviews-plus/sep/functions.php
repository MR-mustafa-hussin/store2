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

/**
 * Returns active post types except al_product related
 *
 * @return type
 */
function get_ic_review_active_post_types() {
	$settings	 = get_ic_reviews_sep_settings();
	$post_types	 = array_filter( $settings[ 'enabled' ], 'filter_ic_review_post_types_array' );
	return $post_types;
}

/**
 * Returns post types where price shows up automatically
 *
 * @return type
 */
function get_ic_review_show_active_post_types() {
	$settings	 = get_ic_reviews_sep_settings();
	$post_types	 = array_filter( $settings[ 'show' ], 'filter_ic_review_post_types_array' );
	return $post_types;
}

/**
 * Returns post types where price shows up automatically
 *
 * @return type
 */
function get_ic_review_show_where_post_types() {
	$settings				 = get_ic_reviews_sep_settings();
	$settings[ 'show-on' ]	 = isset( $settings[ 'show-on' ] ) ? $settings[ 'show-on' ] : array();
	$post_types				 = array_filter( $settings[ 'show-on' ], 'filter_ic_review_post_types_array' );
	return $post_types;
}

/**
 * Deletes all product post types from array
 *
 * @param type $string
 * @return type
 */
function filter_ic_review_post_types_array( $string ) {
	return strpos( $string, 'al_product' ) === false;
}

if ( !function_exists( 'ic_get_external_single_names' ) ) {

	/**
	 * Defines single name for shipping if catalog is missing
	 * @return string
	 */
	function ic_get_external_single_names() {
		if ( function_exists( 'get_single_names' ) ) {
			$single_names = get_single_names();
		} else {
			$single_names = array( 'ic_revs' => __( 'Reviews', 'shipping-options' ) . ':', 'product_price' => __( 'Price', 'shipping-options' ) . ':', 'product_features' => __( 'Features', 'shipping-options' ), 'product_shipping' => __( 'Shipping', 'shipping-options' ) . ':' );
		}
		return $single_names;
	}

}

add_shortcode( 'reviews', 'ic_reviews_shortcode' );

/**
 * Defines price field table shortcode
 *
 * @param type $atts
 * @return type
 */
function ic_reviews_shortcode( $atts ) {
	if ( !empty( $atts ) ) {
		$available_args	 = apply_filters( 'ic_reviews_shortcode_args', array(
			'post_type'	 => '',
			'tax_id'	 => '',
			'taxonomy'	 => '',
			'post_id'	 => '',
		) );
		$args			 = shortcode_atts( $available_args, $atts );
		ob_start();
		ic_reviews_list( $args[ 'post_id' ], $args[ 'post_type' ], $args[ 'tax_id' ], $args[ 'taxonomy' ] );
		$content		 = ob_get_clean();
	} else {
		ob_start();
		ic_add_ic_rev_form();
		$content = ob_get_clean();
	}
	return $content;
}

add_shortcode( 'reviews_form', 'ic_reviews_form_shortcode' );

function ic_reviews_form_shortcode( $atts ) {
	$available_args	 = apply_filters( 'ic_reviews_form_shortcode_args', array(
		'post_type'	 => '',
		'post_id'	 => '',
	) );
	$args			 = shortcode_atts( $available_args, $atts );
	ob_start();
	ic_reviews_form( $args[ 'post_id' ], $args[ 'post_type' ] );
	$content		 = ob_get_clean();
	return $content;
}

/**
 * Shows price field
 *
 * @param type $id
 */
function ic_reviews() {
	ic_add_ic_rev_form();
}

add_filter( 'the_content', 'show_auto_ic_reviews', 30 );

/**
 * Shows reviews on certain post types
 *
 * @param type $content
 * @return type
 */
function show_auto_ic_reviews( $content ) {
	global $ic_is_excerpt, $reviews_auto_shown;
	if ( $ic_is_excerpt || !empty( $reviews_auto_shown ) ) {
		return $content;
	}
	$post_type			 = get_post_type();
	$enabled_post_types	 = get_ic_review_active_post_types();
	if ( in_array( $post_type, $enabled_post_types ) ) {
		$price_show_post_type = get_ic_review_show_active_post_types();
		if ( in_array( $post_type, $price_show_post_type ) ) {
			$reviews_auto_shown	 = 1;
			ob_start();
			ic_add_ic_rev_form();
			$content			 .= ob_get_clean();
			//$single_names	 = ic_get_external_single_names();
			//$old			 = $content;
			//$content		 = get_price_field_table( get_the_ID(), $single_names );
			//$content .= $old;
		} else if ( !ic_string_contains( $post_type, 'al_product' ) ) {
			add_filter( 'comments_template', 'ic_clear_default_comments_form', 21 );
		}
	}
	return $content;
}
