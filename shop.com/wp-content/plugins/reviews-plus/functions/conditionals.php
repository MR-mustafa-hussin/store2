<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product conditional functions
 *
 * Here all plugin conditional functions are defined and managed.
 *
 * @version		1.0.0
 * @package		ecommerce-product-catalog/functions
 * @author 		Norbert Dreszer
 */
function is_ic_revs_admin_screen() {
	if ( is_admin() && isset( $_GET[ 'page' ] ) && strpos( $_GET[ 'page' ], 'ic_reviews_' ) !== false ) {
		return true;
	}
	return false;
}

/**
 * Checks if review edit screen is being displayed
 *
 * @return boolean
 */
function is_ic_review_edit_screen() {
	if ( is_admin() && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'editcomment' && isset( $_GET[ 'edit_review' ] ) ) {
		return true;
	}
	return false;
}

/**
 * Checks if review modification screen is being displayed (edit or from email links)
 *
 * @return boolean
 */
function is_ic_review_mod_screen() {
	if ( is_admin() && isset( $_GET[ 'c' ] ) && isset( $_GET[ 'action' ] ) && (isset( $_GET[ 'edit_review' ] ) || isset( $_GET[ 'trash_review' ] ) || isset( $_GET[ 'delete_review' ] ) || isset( $_GET[ 'approve_review' ] ) || isset( $_GET[ 'spam_review' ] )) ) {
		return true;
	}
	return false;
}

add_filter( 'is_ic_catalog_admin_page', 'ic_reviews_admin_page_conditional' );

/**
 * Modifies catalog conditional for admin screen
 *
 * @return boolean
 */
function ic_reviews_admin_page_conditional( $return = false ) {
	if ( is_ic_revs_admin_screen() || is_ic_review_edit_screen() ) {
		return true;
	}
	return $return;
}

/**
 * Checks if a product page is displayed
 *
 * @return boolean
 */
function is_ic_post_type_review_enabled() {
	$post_types = get_ic_review_active_post_types();
	if ( !empty( $post_types ) && is_singular( $post_types ) ) {
		return true;
	}
	return false;
}

/**
 * Checks if review post type admin screen is being displayed
 *
 * @return boolean
 */
function ic_ic_review_post_type_screen() {
	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'al_product' ) {
		return true;
	}
	$post_types = get_ic_review_active_post_types();
	foreach ( $post_types as $post_type ) {
		if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == $post_type ) {
			return true;
		}
	}
	return false;
}

/**
 * Checks if reviews heading should be shows
 *
 * @return boolean
 */
function is_ic_reviews_heading_active() {
	if ( function_exists( 'is_ic_product_page' ) && is_ic_product_page() ) {
		return true;
	}
	return false;
}

/**
 * Checks if comments added in the past should be shown
 *
 * @return boolean
 */
function ic_show_old_comments() {
	return apply_filters( 'ic_show_old_comments', true );
}

/**
 * Checks if commentis a review
 *
 * @param type $comment_id
 * @return boolean
 */
function is_ic_review_comment( $comment_id ) {
	$comment_type	 = get_comment_type( $comment_id );
	$comment_types	 = ic_get_active_comment_types();
	if ( in_array( $comment_type, $comment_types ) ) {
		return true;
	}
	return false;
}
