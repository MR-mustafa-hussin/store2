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

if ( !function_exists( 'ic_woocat_cart_hooks' ) ) {
	//add_action( 'init', 'ic_woocat_cart_hooks' );
	add_action( 'wp', 'ic_woocat_cart_hooks' );

	function ic_woocat_cart_hooks() {
		if ( !function_exists( 'wc_get_cart_url' ) || !function_exists( 'wc_get_checkout_url' ) ) {
			return;
		}
		$ic_woocat = ic_woocat_settings();
		if ( !empty( $ic_woocat[ 'general' ][ 'disable_cart' ] ) ) {
			remove_action( 'price_table', 'ic_cart_add_button', 10, 0 );
			return;
		}
		if ( is_product() ) {
			remove_action( 'price_table', 'ic_cart_add_button', 10, 0 );
			add_filter( 'ic_product_page_price_display', 'ic_woocat_price', 10, 2 );
			add_action( 'after_price_table', 'ic_woocat_add_cart_button' );
			add_action( 'before_product_entry', 'ic_woocat_add_cart_messages' );
		} else if ( is_shop() || is_product_category() ) {
			add_filter( 'table_product_listing_other_entry', 'ic_woocat_table_button', 10, 5 );
		}
	}

}

if ( !function_exists( 'ic_woocat_add_cart_button' ) ) {

	function ic_woocat_add_cart_button() {
		?>
		<div style="margin-top: 10px">
			<?php
			woocommerce_template_single_add_to_cart();
			?>
		</div>
		<?php
	}

}

if ( !function_exists( 'ic_woocat_price' ) ) {

	function ic_woocat_price( $price = null, $product_id = null ) {
		global $product;
		if ( empty( $product ) && !empty( $product_id ) ) {
			$product = ic_woocat_get_product_object( $product_id );
		}
		return $product->get_price_html();
	}

}

if ( !function_exists( 'ic_woocat_add_cart_messages' ) ) {

	function ic_woocat_add_cart_messages() {
		do_action( 'woocommerce_before_single_product' );
	}

}


if ( !function_exists( 'ic_woocat_get_product_object' ) ) {

	function ic_woocat_get_product_object( $product_id ) {
		if ( empty( $product_id ) ) {
			return;
		}
		$_pf = new WC_Product_Factory();

		return $_pf->get_product( $product_id );
	}

}