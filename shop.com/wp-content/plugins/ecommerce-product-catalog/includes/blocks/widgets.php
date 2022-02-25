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

class ic_epc_widget_blocks {

	function __construct() {
		add_action( 'ic_register_blocks', array( $this, 'register' ) );
		add_action( 'ic_register_block_scripts', array( $this, 'register_scripts' ) );
		add_action( 'ic_enqueue_block_scripts', array( $this, 'enqueue' ) );
		add_filter( 'ic_epc_blocks_localize', array( $this, 'localize' ) );
	}

	function render_product_search( $atts = null ) {
		ob_start();
		$atts[ 'title' ] = isset( $atts[ 'title' ] ) ? $atts[ 'title' ] : '';
		the_widget( 'product_widget_search', $atts );
		return ob_get_clean();
	}

	function render_product_category( $atts = null ) {
		ob_start();
		$atts[ 'title' ]		 = isset( $atts[ 'title' ] ) ? $atts[ 'title' ] : '';
		$atts[ 'dropdown' ]		 = isset( $atts[ 'dropdown' ] ) ? $atts[ 'dropdown' ] : '';
		$atts[ 'count' ]		 = isset( $atts[ 'count' ] ) ? $atts[ 'count' ] : '';
		$atts[ 'hierarchical' ]	 = isset( $atts[ 'hierarchical' ] ) ? $atts[ 'hierarchical' ] : '';
		the_widget( 'product_cat_widget', $atts );
		return ob_get_clean();
	}

	function register_scripts( $deps ) {
		wp_register_script( 'ic-epc-product-search-widget', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/product-search-widget-block.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/product-search-widget-block.js' ), $deps, null, true );
		wp_register_script( 'ic-epc-product-categories-widget', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/product-category-widget-block.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/product-category-widget-block.js' ), $deps, null, true );
	}

	function register() {
		register_block_type( 'ic-epc/product-search-widget', array(
			'attributes'		 => array(
				'title' => array(
					'type'		 => 'string',
					'default'	 => ''
				)
			),
			'render_callback'	 => array( $this, 'render_product_search' ),
		) );
		register_block_type( 'ic-epc/product-category-widget', array(
			'attributes'		 => array(
				'title'			 => array(
					'type'		 => 'string',
					'default'	 => ''
				),
				'dropdown'		 => array(
					'type'		 => 'boolean',
					'default'	 => ''
				),
				'count'			 => array(
					'type'		 => 'boolean',
					'default'	 => ''
				),
				'hierarchical'	 => array(
					'type'		 => 'boolean',
					'default'	 => ''
				),
			),
			'render_callback'	 => array( $this, 'render_product_category' ),
		) );
	}

	function localize( $localize ) {
		if ( is_plural_form_active() ) {
			$names				 = get_catalog_names();
			$search_label		 = sprintf( __( '%s Search', 'ecommerce-product-catalog' ), ic_ucfirst( $names[ 'singular' ] ) );
			$categories_label	 = sprintf( __( '%s Categories', 'ecommerce-product-catalog' ), ic_ucfirst( $names[ 'singular' ] ) );
		} else {
			$search_label		 = __( 'Product Search', 'ecommerce-product-catalog' );
			$categories_label	 = __( 'Catalog Categories', 'ecommerce-product-catalog' );
		}
		$localize[ 'strings' ][ 'search_widget' ]		 = $search_label;
		$localize[ 'strings' ][ 'settings' ]			 = __( 'Settings', 'ecommerce-product-catalog' );
		$localize[ 'strings' ][ 'select_title' ]		 = __( 'Title', 'ecommerce-product-catalog' );
		$localize[ 'strings' ][ 'category_widget' ]		 = $categories_label;
		$localize[ 'strings' ][ 'select_dropdown' ]		 = __( 'Display as dropdown', 'ecommerce-product-catalog' );
		$localize[ 'strings' ][ 'select_count' ]		 = __( 'Show product counts', 'ecommerce-product-catalog' );
		$localize[ 'strings' ][ 'select_hierarchical' ]	 = __( 'Show hierarchy', 'ecommerce-product-catalog' );
		return $localize;
	}

	function enqueue() {
		wp_enqueue_script( 'ic-epc-product-search-widget' );
		wp_enqueue_script( 'ic-epc-product-categories-widget' );
	}

}

$ic_epc_widget_blocks = new ic_epc_widget_blocks;
