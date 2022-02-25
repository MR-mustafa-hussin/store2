<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Integrates with digital products extension
 *
 * Created by Norbert Dreszer.
 * Date: 06-Mar-15
 * Time: 11:38
 * Package: digital-products.php
 */
class ic_customers_digital {

	function __construct() {
		add_filter( 'customer_products_table_cells', array( $this, 'table_cells' ), 30, 2 );
		add_filter( 'customer_products_table_heads', array( $this, 'table_heads' ), 30, 2 );
	}

	function table_cells( $cells, $product_id ) {
		$product_url = get_digital_product_url( $product_id );
		if ( !empty( $product_url ) ) {
			$cells[] = '<a href="' . $product_url . '" class="button">' . __( 'Download', 'ecommerce-product-catalog' ) . '</a>';
		}
		return $cells;
	}

	function table_heads( $heads ) {
		$heads[] = __( 'Download', 'ecommerce-product-catalog' );
		return $heads;
	}

}

$ic_customers_digital = new ic_customers_digital;
