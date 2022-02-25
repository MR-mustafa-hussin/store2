<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages Discounts payment form
 *
 * Here Discounts payment form is defined and managed.
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-booster/includes
 * @author 		Norbert Dreszer
 */
add_action( 'wp', 'ic_woo_ic_page_enable', -2 );

function ic_woo_ic_page_enable() {
	$enabled = ic_is_page_for_woo_enabled();
	if ( !empty( $enabled ) && is_product() ) {
		global $ic_catalog_template;
		ic_save_global( 'is_ic_shortcode_integration', 0 );
		if ( !empty( $ic_catalog_template ) ) {
			remove_action( 'ic_catalog_wp', array( $ic_catalog_template, 'load_templates' ) );
		}

		add_filter( 'ic_maybe_engueue_all', 'ic_woocat_enqueue_single_product_scripts' );

		remove_all_actions( 'woocommerce_before_single_product_summary' );
		remove_all_actions( 'woocommerce_single_product_summary' );
		remove_all_actions( 'woocommerce_after_single_product_summary' );
		add_action( 'woocommerce_before_single_product_summary', 'ic_woocat_single_css' );
		add_action( 'woocommerce_before_single_product_summary', 'ic_woocat_single_content' );

		remove_filter( 'product_price', array( 'ic_price_display', 'raw_price_format' ), 5 );
		add_filter( 'price_format', 'ic_page_woo_price_format', 10, 2 );

		add_filter( 'widget_product_categories_dropdown_args', 'ic_page_category_widget_tax' );
		add_filter( 'widget_product_categories_args', 'ic_page_category_widget_tax' );
		add_filter( 'ic_get_product_image', 'replace_product_image' );
		add_filter( 'is_lightbox_enabled', 'disable_default_lightbox' );
		add_filter( 'is_magnifier_enabled', 'disable_default_lightbox' );

		add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_page_enable' );
		add_filter( 'product_taxonomy_array', 'ic_woo_ic_page_tax_enable' );
		add_filter( 'current_product_post_type', 'ic_woo_ic_page_post_type' );
		add_filter( 'ic_current_product_tax', 'ic_woo_ic_page_taxonomy' );
		add_filter( 'current_product_catalog_taxonomy', 'ic_woo_ic_page_taxonomy' );
		add_filter( 'show_categories_taxonomy', 'ic_woo_ic_page_taxonomy' );


		add_action( 'after_product_details', 'ic_woocat_reviews', 11 );

		/*
		 *
		  add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_page_enable' );
		  add_filter( 'product_taxonomy_array', 'ic_woo_ic_page_tax_enable' );
		  add_filter( 'current_product_post_type', 'ic_woo_ic_page_post_type' );
		  add_filter( 'ic_current_product_tax', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'current_product_catalog_taxonomy', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'show_categories_taxonomy', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'price_format', 'ic_page_woo_price_format', 10, 2 );

		  //if ( !is_ic_shortcode_integration() ) {
		  //add_filter( 'product_listing_id', 'ic_page_product_listing_id' );
		  //}

		  add_filter( 'widget_product_categories_dropdown_args', 'ic_page_category_widget_tax' );
		  add_filter( 'widget_product_categories_args', 'ic_page_category_widget_tax' );
		  add_filter( 'ic_get_product_image', 'replace_product_image' );
		  add_filter( 'is_lightbox_enabled', 'disable_default_lightbox' );
		  add_filter( 'is_magnifier_enabled', 'disable_default_lightbox' );
		  add_action( 'before_product_entry', 'woo_container_start', 1 );
		  add_action( 'after_product_entry', 'woo_container_end', 99 );
		  add_action( 'after_product_details', 'ic_woocat_reviews', 11 );
		 * remove_filter( 'product_price', array( 'ic_price_display', 'raw_price_format' ), 5 );
		  add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_page_enable' );
		  add_filter( 'product_taxonomy_array', 'ic_woo_ic_page_tax_enable' );
		  add_filter( 'current_product_post_type', 'ic_woo_ic_page_post_type' );
		  add_filter( 'ic_current_product_tax', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'current_product_catalog_taxonomy', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'show_categories_taxonomy', 'ic_woo_ic_page_taxonomy' );
		  add_filter( 'price_format', 'ic_page_woo_price_format', 10, 2 );

		  //if ( !is_ic_shortcode_integration() ) {
		  //add_filter( 'product_listing_id', 'ic_page_product_listing_id' );
		  //}

		  add_filter( 'widget_product_categories_dropdown_args', 'ic_page_category_widget_tax' );
		  add_filter( 'widget_product_categories_args', 'ic_page_category_widget_tax' );
		  add_filter( 'ic_get_product_image', 'replace_product_image' );
		  add_filter( 'is_lightbox_enabled', 'disable_default_lightbox' );
		  add_filter( 'is_magnifier_enabled', 'disable_default_lightbox' );
		  add_action( 'before_product_entry', 'woo_container_start', 1 );
		  add_action( 'after_product_entry', 'woo_container_end', 99 );
		  add_action( 'after_product_details', 'ic_woocat_reviews', 11 );
		 */
	}
	if ( false && !empty( $enabled ) ) {
		remove_filter( 'product_price', array( 'ic_price_display', 'raw_price_format' ), 5 );
		add_filter( 'product_post_type_array', 'ic_woo_ic_post_type_page_enable' );
		add_filter( 'product_taxonomy_array', 'ic_woo_ic_page_tax_enable' );
		add_filter( 'current_product_post_type', 'ic_woo_ic_page_post_type' );
		add_filter( 'ic_current_product_tax', 'ic_woo_ic_page_taxonomy' );
		add_filter( 'current_product_catalog_taxonomy', 'ic_woo_ic_page_taxonomy' );
		add_filter( 'show_categories_taxonomy', 'ic_woo_ic_page_taxonomy' );
		add_filter( 'price_format', 'ic_page_woo_price_format', 10, 2 );

		//if ( !is_ic_shortcode_integration() ) {
		//add_filter( 'product_listing_id', 'ic_page_product_listing_id' );
		//}

		add_filter( 'widget_product_categories_dropdown_args', 'ic_page_category_widget_tax' );
		add_filter( 'widget_product_categories_args', 'ic_page_category_widget_tax' );
		add_filter( 'ic_get_product_image', 'replace_product_image' );
		add_filter( 'is_lightbox_enabled', 'disable_default_lightbox' );
		add_filter( 'is_magnifier_enabled', 'disable_default_lightbox' );
		add_action( 'before_product_entry', 'woo_container_start', 1 );
		add_action( 'after_product_entry', 'woo_container_end', 99 );
		add_action( 'after_product_details', 'ic_woocat_reviews', 11 );

		add_action( 'before_shortcode_catalog', 'ic_woocat_shortcode_catalog_mode' );
		//add_action( 'before_shortcode_catalog', 'woo_container_start' );
		//add_action( 'after_shortcode_catalog', 'woo_container_end' );
		if ( is_ic_shortcode_integration() && is_ic_product_page() ) {
			remove_action( 'template_redirect', array( 'WC_Template_Loader', 'unsupported_theme_init' ) );
		}
	}
}

function ic_woocat_enqueue_single_product_scripts( $return ) {
	if ( is_product() ) {
		return true;
	}
	return $return;
}

function ic_woocat_single_content() {
	do_action( 'before_product_page' );
	content_product_adder_single();
	do_action( 'after_product_page' );
}

function ic_woocat_single_css() {
	?>
	<style>
		.single-product div.product .woocommerce-product-gallery, .woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images {
			width: 100%;
			float: none;
		}
		.woocommerce div.product #product_details .single_variation_wrap {margin-top: 10px}
		.woocommerce div.product .woocommerce-variation-price span.price {display:block;margin-bottom:5px;}
		.woocommerce div.product #product_details form.cart .variations label {margin-top: 5px;}
	</style>
	<?php
}

add_action( 'after_shortcode_catalog', 'ic_woocat_page_reset_data' );

function ic_woocat_page_reset_data() {
	if ( function_exists( 'wc_setup_product_data' ) && is_product( get_the_ID() ) ) {
		wc_setup_product_data( get_the_ID() );
	}
}

ic_woocat_shortcode_catalog_mode();

function ic_woocat_shortcode_catalog_mode() {
	$enabled = ic_is_page_for_woo_enabled();
	if ( !empty( $enabled ) ) {
		add_filter( 'product_listing_id', 'ic_page_product_listing_id' );
	}
}

function ic_woocat_reviews() {
	if ( function_exists( 'start_ic_revs' ) || get_post_type() !== 'product' ) {
		return;
	}
	if ( comments_open() || '0' != get_comments_number() ) {
		echo '<div id="product_reviews">';
		echo '<h3 class="catalog-header">' . __( 'Reviews', 'woocommerce' ) . '<h3>';
		comments_template();
		echo '</div>';
	}
}

function replace_product_image( $image ) {
	if ( ic_woocat_woo_gallery_enabled() && is_product() ) {
		ob_start();
		woocommerce_show_product_images();
		$image = ob_get_clean();
	}
	return $image;
}

function disable_default_lightbox( $return ) {
	if ( ic_woocat_woo_gallery_enabled() && is_product() ) {
		return false;
	}
	return $return;
}

function woo_container_start() {

	if ( is_product() ) {
		?>
		<div class="product">
			<style>.woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images {float: none;width: 100%;}</style>
			<?php
		}
	}

	function woo_container_end() {
		if ( is_product() ) {
			echo '</div>';
		}
	}

	function ic_woo_ic_post_type_page_enable( $array ) {
		if ( is_product() ) {
			$array[] = 'product';
		}
		return $array;
	}

	function ic_woo_ic_page_tax_enable( $array ) {
		if ( is_product() ) {
			$array[] = 'product_cat';
		}
		return $array;
	}

	function ic_woo_ic_page_post_type( $post_type ) {
		if ( is_product() ) {
			return 'product';
		}
		return $post_type;
	}

	function ic_woo_ic_page_taxonomy( $taxonomy ) {
		if ( is_product() ) {
			return 'product_cat';
		}
		return $taxonomy;
	}

	function ic_page_woo_price_format( $formatted, $raw ) {
		if ( is_product() ) {
			$set		 = get_currency_settings();
			$raw		 = str_replace( array( $set[ 'th_sep' ], $set[ 'dec_sep' ] ), array( '', '.' ), $raw );
			$formatted	 = wc_price( $raw );
		}
		return $formatted;
	}

	function ic_page_product_listing_id( $id ) {
		if ( function_exists( 'ic_is_wp_object_available' ) && !ic_is_wp_object_available() ) {
			return $id;
		}
		if ( is_product() ) {
			$id = get_option( 'woocommerce_shop_page_id' );
		}
		return $id;
	}

	function ic_page_category_widget_tax( $widget_args ) {
		if ( is_product() ) {
			$widget_args[ 'taxonomy' ] = 'product_cat';
		}
		return $widget_args;
	}
