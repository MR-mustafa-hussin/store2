<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages Discounts gateaway includes folder
 *
 * Here includes files are defined and managed.
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-mode/includes
 * @author 		Norbert Dreszer
 */
class ic_woocat_activation_wizard extends ic_activation_wizard {

	function __construct() {
		add_filter( 'ic_cat_activation_wizard_complete', array( $this, 'init' ) );
		add_filter( 'ic_cat_activation_wizard_default_response', array( $this, 'response' ) );
		add_filter( 'ic_cat_show_woocommerce_notice', array( $this, 'disable_woocommerce_notice' ) );

		add_filter( 'ic_cat_activation_wizard_final_questions', array( $this, 'final_questions' ) );
	}

	function init( $true ) {
		if ( !$this->activation_message_done() ) {
			$response	 = $this->get_choice_response();
			$questions	 = $this->response_to_question( $response );
			if ( empty( $questions ) ) {
				$this->final_box();
			} else {
				$this->box_header( sprintf( __( '%s is active now!', 'ecommerce-product-catalog' ), 'WooCommerce Catalog' ) );
				$this->box_paragraph( sprintf( __( 'Would you like to modify %s desgin on:', 'ecommerce-product-catalog' ), 'WooCommerce' ) );
				$this->box_choice( $questions );
			}
			remove_action( 'ic_cat_activation_wizard_bottom', array( 'ic_catalog_notices', 'getting_started_docs_info' ) );
			return false;
		}

		return $true;
	}

	function final_box() {
		delete_option( 'IC_EPC_activation_message' );
		delete_option( 'IC_EPC_activation_message_done' );
		$this->box_header( sprintf( __( "Congratulations! Your're ready to customize %s.", 'ecommerce-product-catalog' ), 'WooCommerce' ) );
		$questions = array(
			admin_url( 'admin.php?page=ic-catalog-mode&tab=general' ) => __( 'Customization Settings', 'ecommerce-product-catalog' ),
		);
		$this->box_choice( $questions );
	}

	function response( $false ) {
		$cat_wizard_woo = get_option( 'ic_cat_wizard_woo_choice' );
		if ( !$this->activation_message_done() && $cat_wizard_woo === 'woo-design' ) {
			return $this->get_choice_response();
		}
		return $false;
	}

	function activation_message_done() {
		$done = get_option( 'IC_WOOCAT_activation_message_done', 0 );
		if ( !empty( $done ) ) {
			return true;
		}
		return false;
	}

	function complete_activation() {
		update_option( 'IC_WOOCAT_activation_message_done', 1 );
	}

	function get_choice_response( $answer = null ) {
		$response = array();
		if ( $this->activation_message_done() ) {
			$answer = 'complete';
		} else if ( empty( $answer ) && !empty( $_GET[ 'ic_woocat_activation_choice' ] ) ) {
			$answer = esc_attr( $_GET[ 'ic_woocat_activation_choice' ] );
		} else if ( empty( $answer ) && !empty( $_GET[ 'ic_catalog_activation_choice' ] ) ) {
			$answer = esc_attr( $_GET[ 'ic_catalog_activation_choice' ] );
		}
		switch ( $answer ) {
			case 'single-on':
				$this->enable_epc_on_page();
				$this->disable_epc_on_listing();
				$this->complete_activation();
				break;
			case 'listing-on':
				$this->enable_epc_on_listing();
				$this->disable_epc_on_page();
				$this->complete_activation();

				break;

			case 'both-on':
				$this->enable_epc_on_page();
				$this->enable_epc_on_listing();
				$this->complete_activation();

				break;


			default:
				$response = apply_filters( 'ic_woocat_activation_wizard_default_response', false );
				if ( !$response ) {
					$this->disable_separate_catalog();
					$response = $this->woocat_default_choice_response();
				}
				break;
		}
		return $response;
	}

	function woocat_default_choice_response() {

		$response[ 'one' ]		 = __( 'Single Product Page', 'ecommerce-product-catalog' );
		$response[ 'next_one' ]	 = 'single-on';

		$response[ 'two' ]		 = __( 'Product Listing', 'ecommerce-product-catalog' );
		$response[ 'next_two' ]	 = 'listing-on';

		$response[ 'three' ]		 = __( 'Both', 'ecommerce-product-catalog' );
		$response[ 'next_three' ]	 = 'both-on';

		return $response;
	}

	function enable_epc_on_page() {
		update_option( 'ic_enable_page_for_woo', 1 );
	}

	function enable_epc_on_listing() {
		update_option( 'ic_enable_listing_for_woo', 1 );
	}

	function disable_epc_on_page() {
		update_option( 'ic_enable_page_for_woo', 0 );
	}

	function disable_epc_on_listing() {
		update_option( 'ic_enable_listing_for_woo', 0 );
	}

	function disable_separate_catalog() {
		$ic_woocat							 = ic_woocat_settings();
		$ic_woocat[ 'catalog' ][ 'enable' ]	 = 0;
		update_option( 'ic_woocat', $ic_woocat );
		if ( function_exists( 'get_product_listing_id' ) ) {
			$listing_id		 = get_product_listing_id();
			$listing_page	 = get_post( $listing_id );
			if ( !empty( $listing_page->post_content ) ) {
				if ( $listing_page->post_content === '[show_product_catalog]' ) {
					wp_delete_post( $listing_id, true );
				} else {
					$listing_page->post_content = str_replace( '[show_product_catalog]', '', $listing_page->post_content );
					wp_update_post( $listing_page );
				}
				delete_option( 'product_archive_page_id' );
				delete_option( 'product_archive' );
			}
		}
	}

	function disable_woocommerce_notice( $return ) {
		if ( $this->activation_message_done() ) {
			return false;
		}
		return $return;
	}

	function final_questions( $questions ) {
		if ( class_exists( 'ic_cat_activation_wizard' ) ) {
			if ( ic_cat_activation_wizard::get_woo_choice() === 'woo-design' ) {
				array_shift( $questions );
				remove_action( 'ic_cat_activation_wizard_bottom', array( 'ic_catalog_notices', 'getting_started_docs_info' ) );
			}
		}
		return $questions;
	}

}

$ic_woocat_activation_wizard = new ic_woocat_activation_wizard;
