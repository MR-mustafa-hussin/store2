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

add_filter( 'pll_copy_post_metas', 'ic_polylang_copy_variations', 10, 2 );

function ic_polylang_copy_variations( $metas, $sync ) {
	$var_meta_keys				 = array();
	$product_variations_settings = get_product_variations_settings();
	for ( $i = 1; $i <= $product_variations_settings[ 'count' ]; $i++ ) {
		if ( empty( $sync ) ) {
			$var_meta_keys[] = $i . '_variation_label';
			$var_meta_keys[] = $i . '_variation_values';
		} else {
			$var_meta_keys[] = $i . '_variation_prices';
			$var_meta_keys[] = $i . '_variation_shipping';
			$var_meta_keys[] = $i . '_variation_mod';
			$var_meta_keys[] = $i . '_variation_type';
		}
	}
	return array_merge( $metas, $var_meta_keys );
}
