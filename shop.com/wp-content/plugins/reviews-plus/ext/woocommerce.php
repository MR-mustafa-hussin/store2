<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Integrates shipping calculator with PayPal Gateway
 *
 * Created: May 4, 2015
 * Package: paypal-gateway
 */
add_action( 'init', 'ic_initialice_reviews_woocommerce', 30 );

function ic_initialice_reviews_woocommerce() {
	$post_types = get_ic_review_active_post_types();
	if ( in_array( 'product', $post_types ) ) {
		if ( isset( $_POST['ic_review_rating'] ) ) {
			$_POST['rating'] = ic_sanitize_rating( $_POST['ic_review_rating'] );
		}
		//remove_filter( 'preprocess_comment', array( 'WC_Comments', 'check_comment_rating' ), 0 );

		add_filter( 'woocommerce_product_tabs', 'ic_ic_revs_tab' );
	}
}

function ic_ic_revs_tab( $tabs ) {
	remove_filter( 'the_content', 'show_auto_ic_reviews', 30 );
	$count           = '';
	$tabs['reviews'] = array(
		'title'    => sprintf( __( 'Reviews%s', 'reviews-plus' ), $count ),
		'priority' => 50,
		'callback' => 'ic_add_ic_rev_form'
	);

	return $tabs;
}

add_filter( 'ic_get_ic_rev_rating', 'ic_get_woocommerce_rev_rating', 10, 2 );

function ic_get_woocommerce_rev_rating( $rating, $review_id ) {
	if ( ! empty( $rating ) ) {
		return $rating;
	}
	$woo_rating = empty( $review_id ) ? 0 : intval( get_comment_meta( $review_id, 'rating', true ) );

	return $woo_rating;
}
