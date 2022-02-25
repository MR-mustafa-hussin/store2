<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages product attributes
 *
 * Here all product attributes are defined and managed.
 *
 * @version		1.0.0
 * @package		ecommerce-product-catalog/includes
 * @author 		impleCode
 */

/**
 * Checks if product attributes are enabled
 *
 * @return boolean
 */
function is_ic_attributes_enabled() {
	$attributes_count = product_attributes_number();
	if ( $attributes_count > 0 ) {
		return true;
	}
	return false;
}

/**
 * Checks if product has any attributes selected
 *
 * @param type $product_id
 * @return boolean
 */
function has_product_any_attributes( $product_id ) {
	$attributes_number = product_attributes_number();
	if ( $attributes_number > 0 ) {
		for ( $i = 1; $i <= $attributes_number; $i++ ) {
			//$field_name	 = apply_filters( 'ic_attribute_value_field_name', "_attribute" ) . $i;
			//$at_val		 = get_post_meta( $product_id, $field_name, true );
			$at_val = get_attribute_value( $i, $product_id );
			if ( !empty( $at_val ) ) {
				return apply_filters( 'ic_has_product_any_attributes', true, $product_id );
			}
		}
	}
	return apply_filters( 'ic_has_product_any_attributes', false, $product_id );
}

function is_ic_attributes_size_enabled() {
	$settings = ic_attributes_standard_settings();
	if ( !empty( $settings[ 'size_unit' ] ) && $settings[ 'size_unit' ] == 'disable' ) {
		return false;
	}
	return true;
}

function is_ic_attributes_weight_enabled() {
	$settings = ic_attributes_standard_settings();
	if ( !empty( $settings[ 'weight_unit' ] ) && $settings[ 'weight_unit' ] == 'disable' ) {
		return false;
	}
	return true;
}
