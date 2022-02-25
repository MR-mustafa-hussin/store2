<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages 2Ckeckout functions
 *
 * Here 2Ckeckout functions are defined and managed.
 *
 * @version		1.0.0
 * @package		mailer_dragon/functions
 * @author 		Norbert Dreszer
 */
if ( !function_exists( 'get_custom_templates_folder' ) ) {

	function get_custom_templates_folder() {
		return get_stylesheet_directory() . '/implecode/';
	}

}
if ( !function_exists( 'ic_get_template_file' ) ) {

	/**
	 * Manages template files paths
	 *
	 * @param type $file_path
	 * @return type
	 */
	function ic_get_template_file( $file_path, $base_path = MAILER_DRAGON_BASE_PATH ) {
		$folder		 = get_custom_templates_folder();
		$file_name	 = basename( $file_path );
		if ( file_exists( $folder . $file_name ) ) {
			return $folder . $file_name;
		} else if ( file_exists( $base_path . '/templates/template-parts/' . $file_path ) ) {
			return $base_path . '/templates/template-parts/' . $file_path;
		} else {
			return false;
		}
	}

}

if ( !function_exists( 'ic_show_template_file' ) ) {

	/**
	 * Includes template file
	 *
	 * @param type $file_path
	 * @return type
	 */
	function ic_show_template_file( $file_path, $base_path = MAILER_DRAGON_BASE_PATH, $product_id = false ) {
		$path = ic_get_template_file( $file_path, $base_path );
		if ( $path ) {
			if ( $product_id ) {
				$prev_id = ic_get_global( 'product_id' );
				if ( $prev_id !== $product_id && is_ic_product( $product_id ) ) {
					ic_save_global( 'product_id', $product_id );
				}
			}
			include $path;
			if ( $product_id ) {
				$prev_id = isset( $prev_id ) ? $prev_id : false;
				if ( $prev_id && $prev_id !== $product_id && is_ic_product( $prev_id ) ) {
					ic_save_global( 'product_id', $prev_id );
				} else {
					ic_delete_global( 'product_id' );
				}
			}
		}
		return;
	}

}