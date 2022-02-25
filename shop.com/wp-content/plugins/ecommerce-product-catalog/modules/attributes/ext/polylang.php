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

add_filter( 'pll_copy_post_metas', 'ic_polylang_translate_attributes', 10, 2 );

function ic_polylang_translate_attributes( $metas, $sync ) {
	if ( $sync === true ) {
		return $metas;
	}
	$attr_meta_keys	 = array();
	$max_attributes	 = product_attributes_number();
	for ( $i = 1; $i <= $max_attributes; $i++ ) {
		$attr_meta_keys[]	 = '_attribute' . $i;
		$attr_meta_keys[]	 = '_attribute-label' . $i;
		$attr_meta_keys[]	 = '_attribute-unit' . $i;
	}
	return array_merge( $metas, $attr_meta_keys );
}
