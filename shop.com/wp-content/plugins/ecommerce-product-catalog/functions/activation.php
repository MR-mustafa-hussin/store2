<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages functions necessary on plugin activation.
 *
 *
 * @version        1.1.3
 * @package        ecommerce-product-catalog/functions
 * @author        impleCode
 */
//add_action( 'admin_init', 'epc_activation_function', 1 );

function epc_activation_function() {
	if ( is_ic_activation_hook() && current_user_can( 'activate_plugins' ) ) {
		$product_count = ic_products_count();
		if ( $product_count < 3 ) {
			if ( ! function_exists( 'start_ic_woocat' ) ) {
				set_transient( '_ic_welcome_screen_activation_redirect', true, 30 );
			}
			update_option( 'IC_EPC_activation_message', 1 );
			delete_option( 'implecode_wp_hidden_tooltips' );
			delete_option( 'implecode_wp_tooltips' );
			delete_option( 'ic_cat_wizard_woo_choice' );
			delete_option( 'ic_hidden_notices' );
			delete_option( 'ic_hidden_boxes' );
			delete_option( 'ic_epc_tracking_notice' );
			delete_option( 'ic_cat_recommended_extensions' );
			delete_option( 'ic_block_woo_template_file' );
			delete_option( 'ic_allow_woo_template_file' );
			ic_catalog_notices::review_notice_hide();
			save_default_multiple_settings();
			ic_save_default_labels();
			create_sample_product();
			create_products_page();
			ic_set_filter_bar_default_widgets();
		}
		add_product_caps();
		permalink_options_update();
		do_action( 'ic_EPC_activation' );
		delete_option( 'IC_EPC_install' );
	}
}

/**
 * Saves default values for multiple settings for compatibility with multilanguage plugins
 *
 */
function save_default_multiple_settings() {
	$check = get_option( 'archive_multiple_settings' );
	if ( empty( $check ) ) {
		$archive_multiple_settings                                      = get_option( 'archive_multiple_settings', get_default_multiple_settings() );
		$archive_multiple_settings['catalog_plural']                    = isset( $archive_multiple_settings['catalog_plural'] ) ? $archive_multiple_settings['catalog_plural'] : DEF_CATALOG_PLURAL;
		$archive_multiple_settings['catalog_singular']                  = isset( $archive_multiple_settings['catalog_singular'] ) ? $archive_multiple_settings['catalog_singular'] : DEF_CATALOG_SINGULAR;
		$archive_multiple_settings['shortcode_mode']['show_everywhere'] = isset( $archive_multiple_settings['shortcode_mode']['show_everywhere'] ) ? $archive_multiple_settings['shortcode_mode']['show_everywhere'] : 1;
		update_option( 'archive_multiple_settings', $archive_multiple_settings );
	}
}

function ic_save_default_labels() {
	$single_names = get_option( 'single_names' );
	if ( empty( $single_names ) ) {
		$default_single_names = default_single_names();
		update_option( 'single_names', $default_single_names );
	}
	$archive_names = get_option( 'archive_names' );
	if ( empty( $archive_names ) ) {
		$default_archive_names = default_archive_names();
		update_option( 'archive_names', $default_archive_names );
	}
}

function create_products_page( $status = 'publish' ) {
	if ( current_user_can( 'publish_pages' ) ) {
		$content = ic_catalog_shortcode();
		/*
		  if ( is_advanced_mode_forced() ) {
		  $content = '';
		  }
		 *
		 */
		$product_page = array(
			'post_title'     => DEF_CATALOG_PLURAL,
			'post_type'      => 'page',
			'post_content'   => $content,
			'post_status'    => $status,
			'comment_status' => 'closed'
		);

		$plugin_data    = get_plugin_data( AL_PLUGIN_MAIN_FILE );
		$plugin_version = $plugin_data["Version"];
		$first_version  = get_option( 'first_activation_version', '1.0' );

		if ( $first_version == '1.0' ) {
			add_option( 'first_activation_version', $plugin_version );
			add_option( 'ecommerce_product_catalog_ver', $plugin_version );
		}
		$listing_id = get_product_listing_id();
		if ( empty( $listing_id ) || $listing_id == 'noid' ) {
			$listing_id = wp_insert_post( $product_page );
			if ( ! is_wp_error( $listing_id ) ) {
				update_option( 'product_archive_page_id', $listing_id );
				update_option( 'product_archive', $listing_id );
			}
		}

		return $listing_id;
	}
}

function create_sample_product() {
	$sample_id = sample_product_id();
	if ( ( current_user_can( 'publish_products' ) || current_user_can( 'administrator' ) ) && ( ( ( ! is_advanced_mode_forced() || is_ic_shortcode_integration() ) && empty( $sample_id ) ) || isset( $_GET['create_sample_product_page'] ) ) ) {

		$product_sample                     = array(
			'post_title'     => __( 'Sample Product Page', 'ecommerce-product-catalog' ),
			'post_type'      => 'al_product',
			'post_content'   => '[sample_long_desc]',
			'post_status'    => 'publish',
			'comment_status' => 'closed'
		);
		$product_id                         = wp_insert_post( $product_sample );
		$product_field['_price']            = 30;
		$product_field['_sku']              = 'INT102';
		$product_field['_attribute-label1'] = __( 'Color', 'ecommerce-product-catalog' );
		$product_field['_attribute-label2'] = __( 'Size', 'ecommerce-product-catalog' );
		$product_field['_attribute-label3'] = __( 'Weight', 'ecommerce-product-catalog' );
		$product_field['_attribute1']       = __( 'White', 'ecommerce-product-catalog' );
		$product_field['_attribute2']       = __( 'Big', 'ecommerce-product-catalog' );
		$product_field['_attribute3']       = 130;
		$product_field['_attribute-unit1']  = '';
		$product_field['_attribute-unit2']  = '';
		$product_field['_attribute-unit3']  = __( 'lbs', 'ecommerce-product-catalog' );
		$product_field['_shipping-label1']  = 'UPS';
		$product_field['_shipping1']        = 15;
		//$product_field[ 'excerpt' ]				 = '[theme_integration class="fixed-box"]';
		$product_field['excerpt'] = '<p>' . __( 'Welcome on a product test page. This is a short description. It should show up on the left (for plain product page template) or right (for Formatted product page template) of the product image and below the product name.', 'ecommerce-product-catalog' ) . '</p>';
		$product_field['excerpt'] = '<p>' . __( 'You can change the product page template in catalog settings.', 'ecommerce-product-catalog' ) . '</p>';
		$product_field['excerpt'] .= '<p><strong>' . __( 'Please read this page carefully to fully understand all product page elements.', 'ecommerce-product-catalog' ) . '</strong></p>';

		$long_desc                = '[sample_long_desc]';
		$product_field['content'] = $long_desc;
		foreach ( $product_field as $key => $value ) {
			add_post_meta( $product_id, $key, $value, true );
		}
		update_option( 'sample_product_id', $product_id );

		return $product_id;
	}
}

add_shortcode( 'sample_long_desc', 'ic_sample_long_desc' );

function ic_sample_long_desc() {
	$long_desc = '<p>' . __( 'This section is a product long description. It should appear under the attributes table or in the description tab. Before that, you should see the price, SKU and shipping options (all can be disabled). The attributes also can be disabled.', 'ecommerce-product-catalog' ) . '</p>';
	$long_desc .= '<h2>' . __( 'Product Page Layout', 'ecommerce-product-catalog' ) . '</h2>';
	$long_desc .= '<p>' . __( 'You can modify the product page and product listing layout by clicking on the admin options links located under the image.', 'ecommerce-product-catalog' ) . '</p>';
	$long_desc .= '<h2>' . __( 'Advanced Theme Integration Mode', 'ecommerce-product-catalog' ) . '</h2>';
	if ( ! is_ic_shortcode_integration() ) {
		$long_desc .= '<p><strong>' . sprintf( __( 'You are currently using %s mode.', 'ecommerce-product-catalog' ), get_integration_type() ) . '</strong></p>';
		$long_desc .= '<p>' . sprintf( __( 'With Advanced Mode, you will be able to use %s in %s. The product listing page, category pages, product search and category widget will be enabled in advanced mode. You can enable the Advanced Mode %s free. To see how please see <a target="_blank" href="%s">Theme Integration Guide</a>', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME, '100%', '100%', 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=sample-product-page&key=integration-mode-test' ) . '</p>';
		$long_desc .= '<p>' . __( 'The Advanced Mode works out of the box on all default WordPress themes and all themes with the integration done correctly.', 'ecommerce-product-catalog' ) . '</p>';
		$long_desc .= '<h2>' . __( 'Simple Theme Integration Mode', 'ecommerce-product-catalog' ) . '</h2>';
		$long_desc .= '<p>' . sprintf( __( 'The simple mode allows using %s most features. You can build the product listing pages and category pages by using a %s shortcode. Simple mode uses your theme page layout so it can show unwanted elements on a product page. If it does please switch to Advanced Mode and see if it works out of the box.', 'ecommerce-product-catalog' ), IC_CATALOG_PLUGIN_NAME, '[show_products]' ) . '</p>';
		$long_desc .= '<h2>' . __( 'How to switch to Advanced Mode?', 'ecommerce-product-catalog' ) . '</h2>';
		$long_desc .= '<p>' . sprintf( __( 'Click <a href="%s">here</a> to test the Automatic Advanced Mode. If the test goes well, you can keep it enabled and enjoy full %s functionality. If the page layout during the test will not be satisfying, please see <a target="_blank" href="%s">Theme Integration Guide</a>.', 'ecommerce-product-catalog' ), '?test_advanced=1', IC_CATALOG_PLUGIN_NAME, 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=sample-product-page&key=integration-mode-test' ) . '</p>';
		$long_desc .= '<p>' . __( 'The theme integration guide will show you a step by step process. If you finish it successfully, the integration will be done. It is recommended to use theme integration guide even if the page looks good in simple mode or advanced mode because it reassures 100% theme integrity.', 'ecommerce-product-catalog' ) . '</p>';
	} else {
		$long_desc .= '<p>' . sprintf( __( 'Currently, %s is being used on the main product listing.', 'ecommerce-product-catalog' ), '[' . ic_catalog_shortcode_name() . ']' ) . '</p>';
		$long_desc .= '<p>' . __( 'If the catalog pages are not displayed correctly within your theme layout you can test a different integration method.', 'ecommerce-product-catalog' ) . '</p>';
		if ( ! is_advanced_mode_forced( false ) ) {
			$long_desc .= '<p>' . sprintf( __( 'Click %shere%s to proceed.', 'ecommerce-product-catalog' ), '<a href="?test_advanced=1">', '</a>' ) . '</p>';
		} else {
			$long_desc .= '<p>' . sprintf( __( 'To proceed with such test, remove %s from your main product listing page and see how the catalog pages look like without it.', 'ecommerce-product-catalog' ), '[' . ic_catalog_shortcode_name() . ']' ) . '</p>';
		}
	}

	return $long_desc;
}

function sample_product_id() {
	$product_id = get_option( 'sample_product_id' );
	if ( ! empty( $product_id ) && ic_product_exists( $product_id ) ) {
		return $product_id;
	} else if ( ! empty( $product_id ) ) {
		delete_option( 'sample_product_id' );
	}

	return false;
}

function sample_product_url() {
	$product_id = sample_product_id();
	if ( $product_id ) {
		$sample_product_url = get_permalink( $product_id );
		$sample_product_url = esc_url( add_query_arg( 'test_advanced', 1, $sample_product_url ) );
	}
	if ( empty( $sample_product_url ) || ( ! empty( $product_id ) && get_post_status( $product_id ) != 'publish' ) ) {
		$sample_product_url = esc_url( add_query_arg( 'create_sample_product_page', 'true' ) );
	}

	return $sample_product_url;
	//return '';
}

function sample_product_button( $p = null, $text = null, $button_type = 'button-primary' ) {
	$sample_url = sample_product_url();
	if ( ! empty( $sample_url ) ) {
		$text = isset( $text ) ? $text : __( 'Start Automatic Theme Integration', 'ecommerce-product-catalog' );
		if ( ! isset( $p ) ) {
			return '<a href="' . $sample_url . '" class="' . $button_type . '">' . $text . '</a>';
		} else {
			return '<p><a href="' . $sample_url . '" class="' . $button_type . '">' . $text . '</a></p>';
		}
	}
}

add_action( 'admin_init', 'ecommerce_product_catalog_upgrade' );

function ecommerce_product_catalog_upgrade() {
	if ( is_admin() && function_exists( 'get_plugin_data' ) ) {
		$plugin_data             = get_plugin_data( AL_PLUGIN_MAIN_FILE );
		$plugin_version          = $plugin_data["Version"];
		$database_plugin_version = get_option( 'ecommerce_product_catalog_ver', $plugin_version );
		if ( $database_plugin_version != $plugin_version ) {
			update_option( 'ecommerce_product_catalog_ver', $plugin_version );
			$first_version = (string) get_option( 'first_activation_version', $plugin_version );
			if ( version_compare( $first_version, '1.9.0' ) < 0 && version_compare( $database_plugin_version, '2.2.4' ) < 0 ) {
				$hide_info = 0;
				ic_catalog_theme_integration::enable_advanced_mode( $hide_info );
			}
			if ( version_compare( $first_version, '2.0.0' ) < 0 && version_compare( $database_plugin_version, '2.2.4' ) < 0 ) {
				$archive_multiple_settings                         = get_multiple_settings();
				$archive_multiple_settings['product_listing_cats'] = 'off';
				$archive_multiple_settings['cat_template']         = 'link';
				update_option( 'archive_multiple_settings', $archive_multiple_settings );
			}
			if ( version_compare( $first_version, '2.0.1' ) < 0 && version_compare( $database_plugin_version, '2.2.4' ) < 0 ) {
				add_product_caps();
			}
			if ( version_compare( $first_version, '2.0.4' ) < 0 && version_compare( $database_plugin_version, '2.2.4' ) < 0 ) {
				delete_transient( 'implecode_extensions_data' );
			}
			if ( version_compare( $first_version, '2.2.5' ) < 0 && version_compare( $database_plugin_version, '2.2.5' ) < 0 ) {
				$archive_names                        = get_option( 'archive_names' );
				$archive_names['all_main_categories'] = '';
				$archive_names['all_products']        = '';
				$archive_names['all_subcategories']   = '';
				update_option( 'archive_names', $archive_names );
			}
			if ( version_compare( $first_version, '2.3.6' ) < 0 && version_compare( $database_plugin_version, '2.3.6' ) < 0 ) {
				$archive_multiple_settings                    = get_multiple_settings();
				$archive_multiple_settings['default_sidebar'] = 1;
				update_option( 'archive_multiple_settings', $archive_multiple_settings );
			}
			if ( version_compare( $first_version, '2.4.0' ) < 0 && version_compare( $database_plugin_version, '2.4.0' ) < 0 ) {
				$archive_multiple_settings            = get_multiple_settings();
				$archive_multiple_settings['related'] = 'categories';
				update_option( 'archive_multiple_settings', $archive_multiple_settings );
				update_option( 'old_sort_bar', 1 );
			}
			if ( version_compare( $first_version, '2.4.15' ) < 0 && version_compare( $database_plugin_version, '2.4.15' ) < 0 ) {
				save_default_multiple_settings();
			}
			if ( version_compare( $first_version, '2.4.16' ) < 0 && version_compare( $database_plugin_version, '2.4.16' ) < 0 ) {
				$single_names         = get_single_names();
				$single_names['free'] = '';
				update_option( 'single_names', $single_names );
				ic_save_global( 'single_names', $single_names );
			}
			if ( version_compare( $first_version, '2.4.21' ) < 0 && version_compare( $database_plugin_version, '2.4.21' ) < 0 ) {
				if ( false !== get_transient( 'implecode_hide_plugin_review_info' ) ) {
					set_site_transient( 'implecode_hide_plugin_review_info', 1 );
				}
				if ( false !== get_transient( 'implecode_hide_plugin_translation_info' ) ) {
					set_site_transient( 'implecode_hide_plugin_translation_info', 1 );
				}
			}
			if ( version_compare( $first_version, '2.4.25' ) < 0 && version_compare( $database_plugin_version, '2.4.25' ) < 0 ) {
				ic_reassign_all_products_attributes();
			}
			if ( version_compare( $first_version, '2.5.0' ) < 0 && version_compare( $database_plugin_version, '2.5.0' ) < 0 ) {
				$single_options                           = get_product_page_settings();
				$single_options['template']               = 'plain';
				$single_options['enable_product_gallery'] = 1;
				update_option( 'multi_single_options', $single_options );
			}
			if ( version_compare( $first_version, '2.6.0' ) < 0 && version_compare( $database_plugin_version, '2.6.0' ) < 0 ) {
				ic_add_catalog_manager_role();
			}
			if ( version_compare( $first_version, '2.7.18' ) < 0 && version_compare( $database_plugin_version, '2.7.18' ) < 0 ) {
				permalink_options_update();
			}
			if ( version_compare( $first_version, '3.0.1' ) < 0 && version_compare( $database_plugin_version, '3.0.1' ) < 0 ) {
				permalink_options_update();
			}
			if ( version_compare( $first_version, '3.0.45' ) < 0 && version_compare( $database_plugin_version, '3.0.45' ) < 0 ) {
				wp_schedule_single_event( time(), 'ic_scheduled_hidden_data_processing' );
			}
			//flush_rewrite_rules();
		}
	}
}

add_action( 'ic_update_product_data', 'ic_update_product_data' );

function ic_update_product_data() {
	$option_name = 'ic_update_product_data_done';
	$hook_name   = 'ic_update_product_data';
	if ( ! wp_get_schedule( $hook_name ) ) {
		if ( ! get_transient( $option_name ) ) {
			wp_schedule_event( time(), 'hourly', $hook_name );
		} else {
			return __( 'Just Finished! Wait 10 minutes before restarting.', 'ecommerce-product-catalog' );
		}

		return '';
	}
	if ( ! function_exists( 'get_all_catalog_products' ) ) {
		wp_clear_scheduled_hook( $hook_name );

		return '';
	}
	$done     = get_option( $option_name, 0 );
	$products = get_all_catalog_products( 'date', 'ASC', 300, $done );

	foreach ( $products as $post ) {
		$current_post_keys = get_post_custom_keys( $post->ID );
		if ( empty( $current_post_keys ) ) {
			continue;
		}
		$product_meta = array();
		foreach ( $current_post_keys as $meta_key ) {
			$product_meta[ $meta_key ] = get_post_meta( $post->ID, $meta_key, true );
		}
		do_action( 'product_meta_save_update', apply_filters( 'ic_product_meta_save_update_data', $product_meta ), $post );
		$done ++;
	}
	if ( ! empty( $products ) ) {
		update_option( $option_name, $done );
		wp_schedule_single_event( time(), $hook_name );
	} else {
		delete_option( $option_name );
		wp_clear_scheduled_hook( $hook_name );
		set_transient( $option_name, 1, MINUTE_IN_SECONDS * 10 );
	}
}
