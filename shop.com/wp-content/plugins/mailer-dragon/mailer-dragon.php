<?php

/**
 * Plugin Name: Mailer Dragon
 * Description: Create newsletters and contact your visitors directly.
 * Version: 1.1.2
 * Author: impleCode
 * Author URI: http://implecode.com
 * Text Domain: mailer-dragon
 * Domain Path: /lang/

  Copyright: 2021 impleCode.
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( !class_exists( 'ic_mailer_dragon' ) ) {

	final class ic_mailer_dragon {

		public function __construct() {
			register_activation_hook( __FILE__, array( $this, 'install' ) );
			$this->set_constants();
			add_action( 'after_setup_theme', array( $this, 'start_mailer_dragon' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_init', array( $this, 'register_admin_styles' ) );
			add_action( 'init', array( $this, 'register_styles' ) );
		}

		public function set_constants() {
			define( 'MAILER_DRAGON_URL', plugins_url( '/', __FILE__ ) );
			define( 'MAILER_DRAGON_BASE_PATH', dirname( __FILE__ ) );
		}

		function start_mailer_dragon() {
			load_plugin_textdomain( 'mailer-dragon', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
			require_once(MAILER_DRAGON_BASE_PATH . '/functions/index.php' );
			require_once(MAILER_DRAGON_BASE_PATH . '/includes/index.php' );
			require_once( MAILER_DRAGON_BASE_PATH . '/ext/index.php' );
			require_once( MAILER_DRAGON_BASE_PATH . '/templates/index.php' );
		}

		/**
		 * Adds PayPal admin stripts
		 */
		function enqueue_admin_styles() {
			wp_enqueue_style( 'ic_mailer_admin' );
			wp_localize_script( 'ic_mailer_admin', 'ic_mailer_ajax', array(
				'nonce' => wp_create_nonce( "ic_ajax" )
			) );
			if ( is_ic_mailer_admin_screen() ) {
				wp_enqueue_script( 'ic_mailer_admin' );
			}
		}

		/**
		 * Adds PayPal admin stripts
		 */
		function register_admin_styles() {
			wp_register_script( 'ic_chosen', MAILER_DRAGON_URL . 'ext/chosen/chosen.jquery.min.js' . ic_filemtime( MAILER_DRAGON_BASE_PATH . '/css/mailer-dragon.css' ), array( 'jquery' ) );
			wp_register_script( 'ic_mailer_admin', MAILER_DRAGON_URL . 'js/mailer-dragon-admin.js' . ic_filemtime( MAILER_DRAGON_BASE_PATH . '/js/mailer-dragon-admin.js' ), array( 'ic_chosen', 'jquery-ui-tooltip' ) );
			wp_register_style( 'ic_chosen', MAILER_DRAGON_URL . 'ext/chosen/chosen.min.css' . ic_filemtime( MAILER_DRAGON_BASE_PATH . '/css/mailer-dragon.css' ) );
			wp_register_style( 'ic_mailer_admin', MAILER_DRAGON_URL . 'css/mailer-dragon-admin.css' . ic_filemtime( MAILER_DRAGON_BASE_PATH . '/css/mailer-dragon-admin.css' ), array( 'ic_chosen' ) );
		}

		function register_styles() {
			wp_register_style( 'ic_mailer', MAILER_DRAGON_URL . 'css/mailer-dragon.css' . ic_filemtime( MAILER_DRAGON_BASE_PATH . '/css/mailer-dragon.css' ) );
		}

		function enqueue_styles() {
			wp_enqueue_style( 'ic_mailer' );
		}

		function install() {
			$this->start_mailer_dragon();
			update_option( 'ic_mailer_dragon_install', 1 );
			do_action( 'ic_mailer_dragon_install' );
			update_option( 'ic_mailer_dragon_install', 0 );
		}

	}

}

$ic_mailer_dragon = new ic_mailer_dragon;
