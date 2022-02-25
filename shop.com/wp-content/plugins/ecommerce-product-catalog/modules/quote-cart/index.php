<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 *
 *  @version       1.0.0
 *  @package
 *  @author        impleCode
 *
 */

if ( !function_exists( 'shopping_cart_products' ) ) {

	function shopping_cart_products( $raw = 1 ) {
		$settings = get_shopping_cart_settings();
		return ic_cart_products( $raw, false, 'cart_content', $settings );
	}

}

if ( !function_exists( 'get_shopping_checkout_form_fields' ) ) {

	function get_shopping_checkout_form_fields() {
		$shopping_checkout_form = default_quote_checkout_settings();
		return $shopping_checkout_form;
	}

}

class ic_simple_quote_cart {

	function __construct() {
		if ( function_exists( 'start_quote_cart' ) ) {
			return;
		}
		add_filter( 'ic_simple_cart_settings_menu_name', array( $this, 'menu_name' ) );
		add_filter( 'ic_simple_cart_default_customer_email_text', array( $this, 'email_text' ) );
		add_filter( 'ic_simple_cart_default_admin_email_text', array( $this, 'email_text' ) );
		add_action( 'product_details', array( $this, 'show_add_button' ), 7, 1 );
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'ic_shopping_cart_settings', array( $this, 'button_label' ) );
		add_filter( 'ic_cart_form_editor_settings', array( $this, 'checkout_button_label' ) );
		add_filter( 'ic_is_variations_price_effect_active', array( $this, 'return_false' ) );
		add_filter( 'ic_cart_settings_html', array( $this, 'override_settings_labels' ) );
	}

	function init() {
		global $ic_cart_checkout_form_email;
		if ( !empty( $ic_cart_checkout_form_email ) ) {
			remove_filter( 'ic_formbuilder_admin_email', array( $ic_cart_checkout_form_email, 'modify_admin_email' ), 5, 2 );
			remove_filter( 'ic_formbuilder_user_email', array( $ic_cart_checkout_form_email, 'modify_user_email' ), 5, 2 );
			add_filter( 'ic_formbuilder_admin_email', array( $this, 'modify_admin_email' ), 5, 2 );
			add_filter( 'ic_formbuilder_user_email', array( $this, 'modify_user_email' ), 5, 2 );
		}
		remove_filter( 'ic_formbuilder_before_button', 'order_form_payment_options', 8, 2 );
	}

	function override_settings_labels( $settings_html ) {
		$settings_html = str_replace( array( __( 'Order', 'ecommerce-product-catalog' ), ' ' . __( 'order', 'ecommerce-product-catalog' ) ), array( __( 'Quote', 'ecommerce-product-catalog' ), ' ' . __( 'quote', 'ecommerce-product-catalog' ) ), $settings_html );
		return $settings_html;
	}

	function button_label( $settings ) {
		$settings[ 'button_label' ]				 = __( 'Add to Inquiry', 'ecommerce-product-catalog' );
		$settings[ 'contnue_shopping_label' ]	 = '< ' . __( 'Add more Products', 'ecommerce-product-catalog' );
		$settings[ 'place_order_label' ]		 = __( 'Continue', 'ecommerce-product-catalog' ) . ' >';
		return $settings;
	}

	function checkout_button_label( $settings ) {
		$settings[ 'form_button_label' ] = __( 'Submit Your Inquiry', 'ecommerce-product-catalog' );
		return $settings;
	}

	function menu_name() {
		return __( 'Quote Cart', 'ecommerce-product-catalog' );
	}

	function email_text( $text ) {
		return str_replace( array
			(
			__(
			'order', 'ecommerce-product-catalog' ),
			__( 'Order', 'ecommerce-product-catalog' ),
			'[payment_details]' . "\n\r"
		), array(
			__( 'quote', 'ecommerce-product-catalog' ),
			__( 'Quote', 'ecommerce-product-catalog' ),
			''
		), $text );
	}

	function show_add_button( $product ) {
		$price = product_price( $product->ID );
		if ( empty( $price ) ) {
			add_filter( 'product_price', array( $this, 'enable_price' ) );
			add_filter( 'is_ic_price_enabled', array( $this, 'return_true' ) );
			add_filter( 'ic_cart_add_button', 'ic_cart_added_info_button' );

			ic_cart_add_button();
			remove_filter( 'product_price', array( $this, 'enable_price' ) );
			remove_filter( 'is_ic_price_enabled', array( $this, 'return_true' ) );
		}
	}

	function enable_price( $price ) {
		if ( empty( $price ) ) {
			return ' ';
		}
		return $price;
	}

	function return_true() {
		return true;
	}

	function return_false() {
		return false;
	}

	/**
	 * Replaces customer_details shortcode in admin email template with order data
	 *
	 * @param string $message
	 * @param string $pre_name
	 * @return string
	 */
	function modify_admin_email( $message, $pre_name ) {
		if ( $pre_name == 'cart_' ) {
			$email_settings = get_shopping_cart_settings();

			$p			 = ic_email_paragraph();
			$ep			 = ic_email_paragraph_end();
			$new_message = wpautop( $email_settings[ 'admin_email' ] );
			$new_message = str_replace( '<p>', $p, $new_message );
			$order_data	 = $this->products_summary( 'admin' );
			//$order_data	 .= $p . trim( $message, "<br>" ) . $ep;
			$order_data	 .= $p . $message . $ep;
			$new_message = str_replace( '[customer_details]', $order_data, $new_message );
			return $new_message;
		}
		return $message;
	}

	/**
	 * Replaces customer_details shortcode in customer email template with order data
	 *
	 * @param string $message
	 * @param string $pre_name
	 * @return string
	 */
	function modify_user_email( $message, $pre_name ) {
		if ( $pre_name == 'cart_' ) {
			$email_settings = get_shopping_cart_settings();

			$p			 = ic_email_paragraph();
			$ep			 = ic_email_paragraph_end();
			$new_message = wpautop( $email_settings[ 'user_email' ] );
			$new_message = str_replace( '<p>', $p, $new_message );
			$order_data	 = $this->products_summary( 'user' );
			//$order_data	 .= $p . trim( $message, "<br>" ) . $ep;
			$order_data	 .= $p . $message . $ep;
			$new_message = str_replace( '[customer_details]', $order_data, $new_message );
			return $new_message;
		}
		return $message;
	}

	/**
	 * Returns order products summary for email
	 *
	 * @param string $message
	 * @param string $pre_name
	 * @return string
	 */
	function products_summary( $who = '' ) {
		$cart_content	 = ic_shopping_cart_content( true );
		$products_array	 = shopping_cart_products_array( $cart_content );
		$pre_message	 = '';
		$line			 = '<br>';
		$td				 = ic_email_table_td();
		$etd			 = ic_email_table_td_end();
		$pre_message	 .= ic_email_table();
		$pre_message	 .= ic_email_table_th();

		$pre_message .= apply_filters( 'ic_cart_checkout_email_name_header', ic_email_table_td_first() . __( 'Product name', 'ecommerce-product-catalog' ) . ic_email_table_td_end(), $products_array );
		if ( function_exists( 'is_ic_sku_enabled' ) && is_ic_sku_enabled() ) {
			$single_names	 = get_single_names();
			$pre_message	 .= $td . str_replace( ':', '', $single_names[ 'product_sku' ] ) . $etd;
		}
		$pre_message						 .= $td . __( 'Quantity', 'ecommerce-product-catalog' ) . $etd;
		//$pre_message						 .= $td . __( 'Price', 'ecommerce-product-catalog' ) . $etd;
		//$pre_message						 .= $td . __( 'Subtotal', 'ecommerce-product-catalog' ) . $etd;
		$pre_message						 .= ic_email_table_th_end();
		global $ic_shopping_cart_totals;
		$ic_shopping_cart_totals[ 'total' ]	 = 0;
		$order_total						 = 0;
		$total_tax							 = 0;
		foreach ( $products_array as $cart_id => $p_quantity ) {
			$product_id	 = cart_id_to_product_id( $cart_id );
			$pre_message .= ic_email_table_tr();
			$pre_message .= apply_filters( 'ic_cart_checkout_email_name_td', ic_email_table_td_first() . apply_filters( 'cart_email_product_name', html_entity_decode( get_the_title( $product_id ), null, 'UTF-8' ), $product_id, $cart_id ) . ic_email_table_td_end(), $product_id );
			if ( function_exists( 'is_ic_sku_enabled' ) && is_ic_sku_enabled() ) {
				$sku		 = get_product_sku( $product_id );
				$pre_message .= $td . $sku . $etd;
			}
			$pre_message .= $td . $p_quantity . $etd;
			$pre_message .= ic_email_table_tr_end();
		}
		$pre_message .= ic_email_table_end();
		$p			 = ic_email_paragraph();
		$ep			 = ic_email_paragraph_end();
		$pre_message = apply_filters( 'cart_checkout_' . $who . '_product_data', $pre_message, $order_total, $total_tax, $p, $ep, $line );
		return $pre_message;
	}

}

$ic_simple_quote_cart = new ic_simple_quote_cart;

function default_quote_checkout_settings() {
	$shopping_cart_settings	 = get_shopping_cart_settings();
	$supported_states		 = ic_supported_states();
	$supported_countries	 = implecode_supported_countries();
//$default = '{"fields":[{"label":"Name","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"name"},{"label":"Email","field_type":"email","required":true,"field_options":{"size":"medium"},"cid":"email"},{"label":"Subject","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"subject"},{"label":"Message","field_type":"paragraph","required":true,"field_options":{"size":"medium"},"cid":"message"}]}';
	$default				 = '{"fields":[';
	$default				 .= '{"label":"<strong>' . __( 'DELIVERY ADDRESS', 'ecommerce-product-catalog' ) . '</strong>","field_type":"section_break","required":false,"cid":"inside_header_1"},{"label":"' . __( 'Company', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":false,"field_options":{"size":"medium"},"cid":"company"},{"label":"' . __( 'Full Name', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"name"},{"label":"' . __( 'Address', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"address"},{"label":"' . __( 'Number', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"number"},{"label":"' . __( 'Postal Code', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"postal"},{"label":"' . __( 'City', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"city"}';
	$default				 .= ',{"label":"' . __( 'Country', 'ecommerce-product-catalog' ) . ':","field_type":"dropdown","required":true,"field_options":{"size":"medium"';
	$default				 .= ',"options":[';
	foreach ( $supported_countries as $code => $country ) {
		$default .= '{"label":"' . $country . '", "checked":false},';
	}
	$default .= ']';
	$default .= '},"cid":"country"}';
	if ( empty( $shopping_cart_settings[ 'disable_state' ] ) ) {
		$default .= ',{"label":"' . __( 'State', 'ecommerce-product-catalog' ) . ':","field_type":"dropdown","required":true,"field_options":{"size":"medium"';
		$default .= ',"options":[';
		foreach ( $supported_states as $code => $state ) {
			$default .= '{"label":"' . $state . '", "checked":false},';
		}
		$default .= ']';
		$default .= '},"cid":"state"}';
	}
	$default .= ',{"label":"' . __( 'Phone', 'ecommerce-product-catalog' ) . ':","field_type":"text","required":true,"field_options":{"size":"medium"},"cid":"phone"},{"label":"' . __( 'Email', 'ecommerce-product-catalog' ) . ':","field_type":"email","required":true,"field_options":{"size":"medium"},"cid":"email"},{"label":"' . __( 'Comment', 'ecommerce-product-catalog' ) . ':","field_type":"paragraph","required":false,"field_options":{"size":"medium"},"cid":"comment"}';
	$default .= ']}';
	return str_replace( ',]', ']', $default );
}
