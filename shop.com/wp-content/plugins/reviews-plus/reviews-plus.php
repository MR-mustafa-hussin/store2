<?php

/**
 * Plugin Name: Reviews Plus - Google compatible WordPress Reviews for any content
 * Description: Add rich reviews to posts, pages or any custom post type. Reviews summary compatible with SERP.
 * Version: 1.2.14
 * Author: impleCode
 * Author URI: https://implecode.com
 * Text Domain: reviews-plus
 * Domain Path: /lang/
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'AL_REVIEWS_BASE_URL', plugins_url( '/', __FILE__ ) );
define( 'AL_REVIEWS_BASE_PATH', dirname( __FILE__ ) );
define( 'AL_REVIEWS_MAIN_FILE', __FILE__ );

add_action( 'after_setup_theme', 'start_ic_revs', 16 );

function start_ic_revs() {
	load_plugin_textdomain( 'reviews-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	require_once( AL_REVIEWS_BASE_PATH . '/functions/index.php' );
	require_once( AL_REVIEWS_BASE_PATH . '/includes/index.php' );
	require_once( AL_REVIEWS_BASE_PATH . '/sep/index.php' );
	require_once( AL_REVIEWS_BASE_PATH . '/ext/index.php' );
}

add_action( 'init', 'ic_revs_register_styles', 20 );

/**
 * Registers reviews stylesheet
 *
 */
function ic_revs_register_styles() {
	wp_register_style( 'al_ic_revs_styles', AL_REVIEWS_BASE_URL . 'css/reviews-plus.css' );
	wp_register_script( 'al_ic_revs_scripts', AL_REVIEWS_BASE_URL . 'js/reviews-plus.js', array( 'jquery' ) );
}

add_action( 'admin_init', 'ic_revs_admin_register_styles', 20 );

/**
 * Registers reviews admin stylesheet
 *
 */
function ic_revs_admin_register_styles() {
	wp_register_style( 'al_ic_revs_admin_styles', AL_REVIEWS_BASE_URL . 'css/reviews-plus-admin.css', array( 'dashicons' ) );
	wp_register_script( 'al_ic_revs_scripts', AL_REVIEWS_BASE_URL . 'js/reviews-plus.js', array( 'jquery' ) );
	if ( is_ic_revs_admin_screen() || is_ic_review_edit_screen() ) {
		wp_register_script( 'al_ic_revs_admin_scripts', AL_REVIEWS_BASE_URL . 'js/reviews-plus-admin.js?' . filemtime( plugin_dir_path( __FILE__ ) . '/js/reviews-plus-admin.js' ), array( 'jquery', 'admin-comments' ), false, true );
	} else {
		wp_register_script( 'al_ic_revs_admin_scripts', AL_REVIEWS_BASE_URL . 'js/reviews-plus-admin.js?' . filemtime( plugin_dir_path( __FILE__ ) . '/js/reviews-plus-admin.js' ), array( 'jquery', 'admin-comments' ) );
	}
}

add_action( 'wp_enqueue_scripts', 'ic_revs_enqueue_styles', 20 );

/**
 * Enqueues catalog stylesheet
 *
 */
function ic_revs_enqueue_styles() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'al_ic_revs_styles' );
	wp_enqueue_script( 'al_ic_revs_scripts' );
}

add_action( 'admin_enqueue_scripts', 'ic_revs_admin_enqueue_styles', 20 );

/**
 * Enqueues catalog admin stylesheet
 *
 */
function ic_revs_admin_enqueue_styles() {
	wp_enqueue_style( 'al_ic_revs_admin_styles' );
	wp_localize_script( 'al_ic_revs_admin_scripts', 'reviews_object', array( 'showcomm' => __( 'Show more reviews' ), 'endcomm' => __( 'No more reviews found.' ) ) );
	wp_enqueue_script( 'al_ic_revs_admin_scripts' );
	wp_enqueue_script( 'al_ic_revs_scripts' );
}

add_action( 'admin_enqueue_scripts', 'ic_enqueue_review_scripts' );

function ic_enqueue_review_scripts() {

	if ( is_ic_revs_admin_screen() ) {
		wp_enqueue_script( 'admin-comments' );
		enqueue_comment_hotkeys_js();
	}
}
