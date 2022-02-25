<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product includes folder
 *
 * Here all plugin includes folder is defined and managed.
 *
 * @version		1.0.0
 * @package		ecommerce-product-catalog/includes
 * @author 		impleCode
 */
class ic_epc_blocks {

	public $singular_name, $plural_name;

	function __construct() {
		if ( function_exists( 'register_block_type' ) ) {
			require_once(AL_BASE_PATH . '/includes/blocks/block-edit.php' );
			require_once(AL_BASE_PATH . '/includes/blocks/widgets.php' );

			add_action( 'init', array( $this, 'register' ) );
			add_action( 'current_screen', array( $this, 'register_scripts' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
			global $wp_version;
			if ( version_compare( $wp_version, 5.8 ) < 0 ) {
				add_filter( 'block_categories', array( $this, 'block_category' ), 10, 2 );
			} else {
				add_filter( 'block_categories_all', array( $this, 'block_category' ), 10, 2 );
			}
			add_filter( 'ic_catalog_default_listing_content', array( $this, 'auto_insert_block' ) );
			add_filter( 'ic_catalog_shortcode_name', array( $this, 'block_name' ) );
		}
	}

	function auto_insert_block( $content ) {
		//$content = '<!-- wp:ic-epc/show-catalog /-->';
		$content = '<!-- wp:shortcode -->
[show_product_catalog]
<!-- /wp:shortcode -->';
		return $content;
	}

	function block_name() {
		return __( 'Show Catalog block', 'ecommerce-product-catalog' );
	}

	function enqueue() {
		$names				 = get_catalog_names();
		$this->singular_name = $names[ 'singular' ];
		$this->plural_name	 = $names[ 'plural' ];

		wp_enqueue_script( 'ic-epc-show-catalog' );
		wp_enqueue_script( 'ic-epc-show-products' );
		wp_enqueue_script( 'ic-epc-show-categories' );
		wp_localize_script( 'ic-epc-show-catalog', 'ic_epc_blocks', apply_filters( 'ic_epc_blocks_localize', array(
			'strings'					 => array(
				'show_catalog'		 => __( 'Show Catalog', 'ecommerce-product-catalog' ),
				'show_products'		 => sprintf( __( 'Show %s', 'ecommerce-product-catalog' ), $this->plural_name ),
				'show_categories'	 => __( 'Show Categories', 'ecommerce-product-catalog' ),
				'show'				 => __( 'Show', 'ecommerce-product-catalog' ),
				'select_products'	 => sprintf( __( 'Select %s', 'ecommerce-product-catalog' ), $this->singular_name ),
				'select_categories'	 => __( 'Select Categories', 'ecommerce-product-catalog' ),
				'select_limit'		 => sprintf( __( 'Set %s Limit', 'ecommerce-product-catalog' ), $this->singular_name ),
				'select_orderby'	 => __( 'Order by', 'ecommerce-product-catalog' ),
				'select_order'		 => __( 'Order Type', 'ecommerce-product-catalog' ),
				'select_template'	 => __( 'Listing Template', 'ecommerce-product-catalog' ),
				'select_perrow'		 => __( 'Items Per Row', 'ecommerce-product-catalog' ),
				'choose_products'	 => sprintf( __( 'Choose %s to Display', 'ecommerce-product-catalog' ), $this->plural_name ),
				'choose_categories'	 => __( 'Choose Categories to Display', 'ecommerce-product-catalog' ),
				'by_category'		 => __( 'By Category', 'ecommerce-product-catalog' ),
				'by_product'		 => sprintf( __( 'By %s', 'ecommerce-product-catalog' ), $this->singular_name ),
				'sort_limit'		 => __( 'Sort & Limit', 'ecommerce-product-catalog' ),
			),
			'category_options'			 => $this->categories(),
			'product_options'			 => $this->products(),
			'orderby_options'			 => $this->orderby(),
			'category_orderby_options'	 => $this->category_orderby(),
			'order_options'				 => $this->order(),
			'template_options'			 => $this->template(),
			'per_row_def'				 => 3,
			'products_limit_def'		 => 10,
			'archive_template_def'		 => get_product_listing_template()
		) )
		);
		do_action( 'ic_enqueue_block_scripts', $this->singular_name, $this->plural_name );
	}

	function register_scripts() {
		$deps	 = array( 'wp-blocks', 'wp-element', 'wp-i18n', 'ic_chosen' );
		$screen	 = get_current_screen();
		if ( $screen->id === 'widgets' ) {
			$deps[] = 'wp-edit-widgets';
		} else if ( $screen->id === 'customize' ) {
			$deps[] = 'wp-customize-widgets';
		} else {
			$deps[] = 'wp-editor';
		}
		wp_register_script( 'ic-epc-show-catalog', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/show-catalog-block.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/show-catalog-block.js' ), $deps, null, true );
		wp_register_script( 'ic-epc-show-products', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/show-products-block.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/show-products-block.js' ), $deps, null, true );
		wp_register_script( 'ic-epc-show-categories', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/show-categories-block.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/show-categories-block.js' ), $deps, null, true );
		do_action( 'ic_register_block_scripts', $deps );
	}

	function register() {
		register_block_type( 'ic-epc/show-catalog', array(
			'render_callback' => array( $this, 'render_catalog' ),
		) );
		register_block_type( 'ic-epc/show-products', array(
			'attributes'		 => array(
				'category'			 => array(
					'type'		 => 'array',
					'default'	 => array(),
					'items'		 => array( 'type' => 'integer' )
				),
				'product'			 => array(
					'type'		 => 'array',
					'default'	 => array(),
					'items'		 => array( 'type' => 'integer' )
				),
				'products_limit'	 => array(
					'type'		 => 'string',
					'default'	 => 10
				),
				'orderby'			 => array(
					'type'		 => 'string',
					'default'	 => ''
				),
				'order'				 => array(
					'type'		 => 'string',
					'default'	 => ''
				),
				'archive_template'	 => array(
					'type'		 => 'string',
					'default'	 => get_product_listing_template()
				),
				'per_row'			 => array(
					'type'		 => 'string',
					'default'	 => 3
				)
			),
			'render_callback'	 => array( $this, 'render_products' ),
		) );
		register_block_type( 'ic-epc/show-categories', array(
			'attributes'		 => array(
				'category'			 => array(
					'type'		 => 'array',
					'default'	 => array(),
					'items'		 => array( 'type' => 'integer' )
				),
				'orderby'			 => array(
					'type'		 => 'string',
					'default'	 => 'id'
				),
				'order'				 => array(
					'type'		 => 'string',
					'default'	 => 'ASC'
				),
				'archive_template'	 => array(
					'type'		 => 'string',
					'default'	 => get_product_listing_template()
				),
				'per_row'			 => array(
					'type'		 => 'string',
					'default'	 => 3
				)
			),
			'render_callback'	 => array( $this, 'render_categories' ),
		) );

		do_action( 'ic_register_blocks' );
	}

	function render_catalog() {
		global $ic_rendering_catalog_block;
		$ic_rendering_catalog_block	 = 1;
		$rendered					 = do_shortcode( '[show_product_catalog]' );
		if ( empty( $rendered ) && ic_is_rendering_catalog_block() ) {
			if ( is_ic_product_listing_enabled() ) {
				$rendered	 = '<hr>';
				$rendered	 .= sprintf( __( 'There is nothing to display yet. Please add your products or %sconfigure the catalog%s to display something.', 'ecommmerce-product-catalog' ), '<a href="' . admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) . '">', '</a>' );
				$rendered	 .= '<hr>';
			} else {
				$rendered	 = '<hr>';
				$rendered	 .= '<h3>' . __( 'Catalog Container', 'ecommerce-product-catalog' ) . '</h3>';
				$rendered	 .= sprintf( __( 'You have disabled the main listing page in %scatalog settings%s so this block will not display anything. It will only be used to output content on catalog categories and individual product pages.', 'ecommerce-product-catalog' ), '<a href="' . admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) . '">', '</a>' );
				$rendered	 .= '<hr>';
			}
		}
		$ic_rendering_catalog_block = 0;
		return $rendered;
	}

	function render_products( $atts = null ) {
		global $ic_rendering_products_block;
		$ic_rendering_products_block = 1;
		if ( isset( $atts[ 'product' ] ) && is_array( $atts[ 'product' ] ) ) {
			$atts[ 'product' ] = implode( ',', $atts[ 'product' ] );
		}
		if ( isset( $atts[ 'category' ] ) && is_array( $atts[ 'category' ] ) ) {
			$atts[ 'category' ] = implode( ',', $atts[ 'category' ] );
		}
		if ( !empty( $atts[ 'orderby' ] ) ) {
			$atts[ 'orderby' ] = translate_product_order( $atts[ 'orderby' ] );
		}
		$rendered					 = show_products_outside_loop( $atts );
		$ic_rendering_products_block = 0;
		return $rendered;
	}

	function render_categories( $atts = null ) {
		if ( isset( $atts[ 'category' ] ) && is_array( $atts[ 'category' ] ) ) {
			$atts[ 'include' ] = implode( ',', $atts[ 'category' ] );
		}
		if ( !empty( $atts[ 'orderby' ] ) ) {
//$atts[ 'orderby' ] = translate_product_order( $atts[ 'orderby' ] );
		}
		return product_cat_shortcode( $atts );
	}

	function block_category( $categories, $post ) {
		$categories[] = array(
			'slug'	 => 'ic-epc-block-cat',
			'title'	 => __( 'Catalog', 'ecommerce-product-catalog' ),
			'icon'	 => null,
		);

		return $categories;
	}

	function categories() {
		/*
		  $args				 = array();
		  $args[ 'taxonomy' ]	 = apply_filters( 'show_categories_taxonomy', 'al_product-cat', $args );
		  $args[ 'parent' ]	 = '0';
		  $cats				 = get_terms( $args );
		 *
		 */
		$return		 = array();
		$return[]	 = array( 'value' => 0, 'label' => __( 'All', 'ecommerce-product-catalog' ) );
		/*
		  foreach ( $cats as $cat ) {
		  $return[] = array( 'value' => $cat->term_id, 'label' => $cat->name );
		  }
		 *
		 */
		return $this->subcategories( $return, 0 );
	}

	function subcategories( $return, $parent_id, $tab = '-' ) {
		$args				 = array();
		$args[ 'taxonomy' ]	 = apply_filters( 'show_categories_taxonomy', 'al_product-cat', $args );
		$args[ 'parent' ]	 = $parent_id;
		$cats				 = ic_get_terms( $args );
		foreach ( $cats as $cat ) {
			if ( !empty( $cat->name ) ) {
				if ( !empty( $parent_id ) ) {
					$name = $tab . $cat->name;
				} else {
					$name = $cat->name;
				}
				$return[] = array( 'value' => $cat->term_id, 'label' => $name );
				if ( !empty( $parent_id ) ) {
					$tab .= '-';
				} else {
					$tab = '-';
				}
				$return = $this->subcategories( $return, $cat->term_id, $tab );
			}
		}
		return array_filter( $return );
	}

	function products() {
		$all_products	 = get_all_catalog_products();
		$return			 = array();
		$return[]		 = array( 'value' => 0, 'label' => __( 'All', 'ecommerce-product-catalog' ) );
		foreach ( $all_products as $product ) {
			$return[] = array( 'value' => $product->ID, 'label' => $product->post_title );
		}
		return $return;
	}

	function orderby() {
		$sorting_options = get_product_sort_options();
		$return			 = array();
		foreach ( $sorting_options as $name => $label ) {
			$return[] = array( 'value' => $name, 'label' => $label );
		}
		return $return;
	}

	function category_orderby() {
		$sorting_options = array(
			'id'	 => 'ID',
			'count'	 => __( 'Count', 'ecommerce-product-catalog' ),
			'name'	 => __( 'Name', 'ecommerce-product-catalog' ),
			'none'	 => __( 'None', 'ecommerce-product-catalog' )
		);
		$return			 = array();
		foreach ( $sorting_options as $name => $label ) {
			$return[] = array( 'value' => $name, 'label' => $label );
		}
		return $return;
	}

	function order() {
		return array(
			array( 'value' => 'ASC', 'label' => __( 'ASC', 'ecommerce-product-catalog' ) ),
			array( 'value' => 'DESC', 'label' => __( 'DESC', 'ecommerce-product-catalog' ) )
		);
	}

	function template() {
		$templates	 = ic_get_available_templates();
		$return		 = array();
		foreach ( $templates as $name => $label ) {
			$return[] = array( 'value' => $name, 'label' => $label );
		}
		return $return;
	}

}

$ic_epc_blocks = new ic_epc_blocks;


