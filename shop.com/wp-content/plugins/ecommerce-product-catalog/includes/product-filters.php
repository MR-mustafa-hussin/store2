<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product search widget
 *
 * Here product search widget is defined.
 *
 * @version        1.4.0
 * @package        ecommerce-product-catalog/includes
 * @author        impleCode
 */
if ( ! function_exists( 'get_product_catalog_session' ) ) {

	function get_product_catalog_session() {
		if ( ! is_admin() || is_ic_front_ajax() ) {
			global $IC_Session;
			$prefix = ic_get_session_prefix();
			if ( ic_use_php_session() ) {
				if ( ! ic_is_session_started() && ! headers_sent() && ic_ic_cookie_enabled() ) {
					session_start();
					add_action( 'shutdown', 'session_write_close', 10, 0 );
					add_action( 'ic_session_save_end', 'session_write_close', 10, 0 );
					add_action( 'requests-curl.before_request', 'session_write_close', 10, 0 );
				}
				if ( ! isset( $_SESSION[ $prefix ] ) || ( isset( $_SESSION[ $prefix ] ) && ! is_array( $_SESSION[ $prefix ] ) ) ) {
					$_SESSION[ $prefix ] = array();
				}
				if ( empty( $IC_Session ) ) {
					if ( isset( $_SESSION[ $prefix ] ) ) {
						$IC_Session[ $prefix ] = $_SESSION[ $prefix ];
					} else {
						$IC_Session[ $prefix ] = array();
					}
				}
				$session = $IC_Session[ $prefix ];
			} else {
				if ( empty( $IC_Session ) && class_exists( 'WP_Session' ) ) {
					$IC_Session = WP_Session::get_instance();
				}
				if ( ! isset( $IC_Session[ $prefix ] ) || ( isset( $IC_Session[ $prefix ] ) && ! is_array( $IC_Session[ $prefix ] ) ) ) {
					$IC_Session[ $prefix ] = array();
				}
				$session = $IC_Session[ $prefix ];
			}

			if ( empty( $session ) || ! is_array( $session ) ) {
				$session = array();
			}

			return $session;
		}

		return array();
	}

	function ic_get_session_prefix() {
		$prefix = 'implecode';
		if ( is_multisite() ) {
			$prefix .= '_' . get_current_blog_id();
		}

		return $prefix;
	}

}
if ( ! function_exists( 'set_product_catalog_session' ) ) {

	/**
	 * Saves product catalog session
	 *
	 * @param array $session
	 */
	function set_product_catalog_session( $session ) {
		if ( ! is_admin() || is_ic_front_ajax() ) {
			global $IC_Session;
			$prefix = ic_get_session_prefix();
			if ( ic_use_php_session() ) {
				if ( ! ic_is_session_started() && ! headers_sent() && ic_ic_cookie_enabled() && ( ! is_admin() || is_ic_front_ajax() ) ) {
					session_start();
					add_action( 'shutdown', 'session_write_close', 10, 0 );
					add_action( 'ic_session_save_end', 'session_write_close', 10, 0 );
					add_action( 'requests-curl.before_request', 'session_write_close', 10, 0 );
				}
				if ( ! headers_sent() && ic_is_session_started() ) {
					if ( empty( $_SESSION[ $prefix ] ) ) {
						$_SESSION[ $prefix ] = array();
					}
					$_SESSION[ $prefix ] = $session;
				}
			} else {
				if ( empty( $IC_Session ) && ic_ic_cookie_enabled() && class_exists( 'WP_Session' ) ) {
					$IC_Session = WP_Session::get_instance();
				}
			}
			$IC_Session[ $prefix ] = $session;
			do_action( 'ic_session_save_end' );
		}
	}

}

function product_filter_element( $id, $what, $label, $class = null ) {
	$class = isset( $class ) ? 'filter-url ' . $class : 'filter-url';
	if ( is_product_filter_active( $what ) ) {
		if ( is_product_filter_active( $what, $id ) ) {
			$class .= ' active-filter';
			$id    = 'clear';
		} else {
			$class .= ' not-active-filter';
		}
	}
	if ( is_paged() ) {
		if ( is_ic_permalink_product_catalog() ) {
//echo get_pagenum_link( 1 );
			$url = remove_query_arg( $what );
		} else {
			$url = remove_query_arg( array( 'paged', $what ) );
		}
	} else {
		$url = remove_query_arg( array( $what ) );
	}
	$attr = '';
	if ( is_ic_ajax() && ! empty( $_GET['page'] ) ) {
		$attr = 'data-page="' . intval( $_GET['page'] ) . '"';
	}
	$active_filters = get_active_product_filters( true );
	unset( $active_filters[ $what ] );
	$final_url = add_query_arg( array_merge( array( $what => $id ), $active_filters ), $url );
	if ( ! empty( $_GET['s'] ) && ! empty( $_GET['post_type'] ) ) {
		$final_url = add_query_arg( array( 's' => $_GET['s'], 'post_type' => $_GET['post_type'] ), $final_url );
	}

	return apply_filters( 'ic_category_filter_element', '<a class="' . $class . '" href="' . esc_url( $final_url ) . '" ' . $attr . '>' . $label . '</a>', $label, $id );
}

function get_product_category_filter_element( $category, $posts = null, $show_count = true, $check_count = true ) {
	if ( is_ic_product_listing() && ! empty( $category->count ) && ! is_product_filters_active() && ! apply_filters( 'ic_force_query_count_calculation', false ) ) {
		$count = $category->count;
	} else if ( $check_count ) {
		$count = total_product_category_count( $category->term_id, $category->taxonomy, $posts );
	} else {
		$count      = 1;
		$show_count = false;
	}
	if ( empty( $count ) && ! $check_count ) {
		$show_count = false;
	}
	if ( $count > 0 || ! $check_count ) {
		$name = $category->name;
		if ( $show_count ) {
			$name .= ' <span class="ic-catalog-category-count">(' . $count . ')</span>';
		}

		return product_filter_element( $category->term_id, 'product_category', $name );
	}

	return;
}

add_action( 'wp_loaded', 'set_product_filter' );

/*
 * Sets up active filters
 *
 */

function set_product_filter() {
	if ( is_admin() && ! is_ic_ajax() ) {
		return;
	}
	$session = get_product_catalog_session();
	if ( isset( $_GET['product_category'] ) ) {
		$filter_value = apply_filters( 'ic_catalog_save_product_filter_value', intval( $_GET['product_category'] ), $_GET['product_category'] );
		if ( ! empty( $filter_value ) ) {
			if ( ! isset( $session['filters'] ) ) {
				$session['filters'] = array();
			}
			$session['filters']['product_category'] = $filter_value;
		} else if ( isset( $session['filters']['product_category'] ) ) {
			unset( $session['filters']['product_category'] );
		}
	} else if ( isset( $session['filters']['product_category'] ) ) {
		unset( $session['filters']['product_category'] );
	}

	set_product_catalog_session( $session );
	do_action( 'ic_set_product_filters', $session );
	$session = get_product_catalog_session();
	if ( ! empty( $session['filters'] ) && empty( $session['filters']['filtered-url'] ) ) {
		if ( isset( $_POST['request_url'] ) && is_ic_ajax() ) {
//$old_request_url						 = $_SERVER[ 'REQUEST_URI' ];
//$_SERVER[ 'REQUEST_URI' ]				 = $_POST[ 'request_url' ];
//add_filter( 'get_pagenum_link', 'ic_ajax_pagenum_link' );
//$session[ 'filters' ][ 'filtered-url' ]	 = remove_query_arg( $active_filters, get_pagenum_link( 1, false ) );
			$session['filters']['filtered-url'] = esc_url( $_POST['request_url'] );
//remove_filter( 'get_pagenum_link', 'ic_ajax_pagenum_link' );
//$_SERVER[ 'REQUEST_URI' ]				 = $old_request_url;
		} else {
			$active_filters                     = get_active_product_filters();
			$session['filters']['filtered-url'] = remove_query_arg( $active_filters, get_pagenum_link( 1, false ) );
		}
		if ( ic_string_contains( $session['filters']['filtered-url'], '&s=' ) ) {
			if ( is_ic_ajax() && empty( $_POST['is_search'] ) ) {
				$session['filters']['filtered-url'] = remove_query_arg( array(
					's',
					'post_type'
				), $session['filters']['filtered-url'] );
			} else {
				$session['filters']['filtered-url'] = add_query_arg( 'reset_filters', 'y', $session['filters']['filtered-url'] );
			}
		}
		if ( is_ic_shortcode_query() ) {
			$session['filters']['filtered-url'] = add_query_arg( 'reset_filters', 'y', $session['filters']['filtered-url'] );
		}
		$session['filters']['filtered-url'] = $session['filters']['filtered-url'] . '#product_filters_bar';
		set_product_catalog_session( $session );
	}
}

//add_action( 'ic_pre_get_products', 'delete_product_filters', 2 );
//add_action( 'ic_pre_get_products_shortcode', 'delete_product_filters', 2 );
add_action( 'wp_loaded', 'delete_product_filters', 11 );

/**
 * Clears current filters if there is a page reload without new filter assignment
 *
 */
function delete_product_filters() {
//if ( !is_admin() && is_product_filters_active() && (!is_search() || isset( $_GET[ 'reset_filters' ] ) ) && $query->is_main_query() ) {
	if ( ! is_ic_ajax() && is_product_filters_active() && ( ! is_search() || isset( $_GET['reset_filters'] ) ) ) {
		$active_filters = get_active_product_filters();
		$out            = false;
		foreach ( $active_filters as $filter ) {
			if ( isset( $_GET[ $filter ] ) ) {
				$out = true;
				break;
			}
		}
		if ( ! $out ) {
			$session = get_product_catalog_session();
			if ( ! isset( $session['permanent-filters'] ) ) {
				$session['permanent-filters'] = array();
			}
			if ( ! empty( $session['filters'] ) ) {
				foreach ( $session['filters'] as $filter_name => $filter_value ) {
					if ( ! in_array( $filter_name, $session['permanent-filters'] ) ) {
						unset( $session['filters'][ $filter_name ] );
					}
				}
			}
			if ( empty( $session['filters'] ) ) {
				unset( $session['filters'] );
			}
			set_product_catalog_session( $session );
		}
	}
}

/**
 * Defines active product filters
 *
 * @return array
 */
function get_active_product_filters( $values = false, $encode = false ) {
	$active_filters = apply_filters( 'active_product_filters', array(
		'product_category',
		'min-price',
		'max-price',
		'product_order'
	) );
	if ( $values ) {
		$filters = array();
		foreach ( $active_filters as $filter_name ) {
			$filter_value = get_product_filter_value( $filter_name );
			if ( ! empty( $filter_value ) ) {
				if ( $encode ) {
					$filter_value = ic_urlencode( $filter_value );
				}
				$filters[ $filter_name ] = $filter_value;
			}
		}

		return $filters;
	}

	return $active_filters;
}

function ic_urlencode( $data ) {
	if ( is_array( $data ) ) {
		if ( is_array( reset( $data ) ) ) {
			foreach ( $data as $key => $value ) {
				$data[ $key ] = ic_urlencode( $value );
			}
		} else {
			$data = array_map( 'urlencode', $data );
		}
	} else {
		$data = urlencode( $data );
	}

	return $data;
}

function get_product_filter_value( $filter_name, $encode = false ) {
	if ( is_product_filter_active( $filter_name ) ) {
		$session = get_product_catalog_session();
		if ( $encode ) {
			return htmlentities( $session['filters'][ $filter_name ] );
		} else {
			return $session['filters'][ $filter_name ];
		}
	}

	return '';
}

//add_action( 'ic_pre_get_products', 'apply_product_filters', 20 );
//add_action( 'ic_pre_get_products_listing', 'apply_product_filters', 20 );
//add_action( 'ic_pre_get_products_tax', 'apply_product_filters', 20 );
//add_action( 'ic_pre_get_products_search', 'apply_product_filters', 20 );
add_action( 'ic_pre_get_products_only', 'apply_product_filters', 20 );

/**
 * Applies current filters to the query
 *
 * @param object $query
 */
function apply_product_filters( $query ) {
	if ( ! empty( $query->query['pagename'] ) ) {
		return;
	}
	ic_set_pre_filters_query_vars( $query->query );
	global $ic_product_filters_query;
	if ( is_product_filters_active() ) {
		if ( is_product_filter_active( 'product_category' ) ) {
			$category_id = get_product_filter_value( 'product_category' );
			$taxonomy    = get_current_screen_tax();
			if ( empty( $query->query['ic_exclude_tax'] ) || ( ! empty( $query->query['ic_exclude_tax'] ) && ! in_array( $taxonomy, $query->query['ic_exclude_tax'] ) ) ) {
				$tax_query = $query->get( 'tax_query' );
				if ( empty( $tax_query ) ) {
					$tax_query = array();
				}
				if ( is_array( $category_id ) ) {
					$category_id = array_values( $category_id );
				}
				$filter_tax_query = array(
					'taxonomy' => $taxonomy,
					'terms'    => $category_id,
					//'field'		 => 'term_id'
				);
				if ( ! in_array( $filter_tax_query, $tax_query ) ) {
					$tax_query[] = $filter_tax_query;
				}
				$query->set( 'tax_query', apply_filters( 'ic_catalog_product_category_filter_query', $tax_query ) );
			}
		}
	}
	do_action( 'apply_product_filters', $query );
	if ( is_product_filters_active() ) {
		$ic_product_filters_query = $query;
	}
}

add_filter( 'shortcode_query', 'apply_product_category_filter' );
add_filter( 'home_product_listing_query', 'apply_product_category_filter' );
add_filter( 'category_count_query', 'apply_product_category_filter', 10, 2 );

/**
 * Applies product category filter to shortcode query
 *
 * @param type $shortcode_query
 *
 * @return type
 */
function apply_product_category_filter( $shortcode_query, $taxonomy = null ) {

	if ( ! empty( $taxonomy ) && ! is_array( $taxonomy ) && ic_string_contains( $taxonomy, 'al_product-cat' ) ) {
		return $shortcode_query;
	}
	ic_set_pre_filters_query_vars( $shortcode_query, true );
	if ( is_product_filter_active( 'product_category' ) ) {
		$category_id = get_product_filter_value( 'product_category' );
		$taxonomy    = get_current_screen_tax();
		if ( empty( $shortcode_query['tax_query'] ) ) {
			$shortcode_query['tax_query'] = array();
		}
		if ( is_array( $category_id ) ) {
			$category_id = array_values( $category_id );
		}
		$tax_query = apply_filters( 'ic_catalog_product_category_filter_query', array(
			'taxonomy' => $taxonomy,
			'terms'    => $category_id,
		) );
		if ( ! in_array( $tax_query, $shortcode_query['tax_query'] ) ) {
			$shortcode_query['tax_query'][] = $tax_query;
		}
	}

	return apply_filters( 'apply_shortcode_product_filters', $shortcode_query );
}

add_filter( 'product_search_button_text', 'modify_search_widget_filter' );

/**
 * Deletes search button text in filter bar
 *
 * @param string $text
 *
 * @return string
 */
function modify_search_widget_filter( $text ) {
	if ( is_filter_bar() ) {
		$text = '';
	}

	return $text;
}

add_action( 'wp_ajax_hide_empty_bar_message', 'hide_empty_bar_message' );

/**
 * Ajax receiver to hide the filters bar empty message
 *
 */
function hide_empty_bar_message() {
	update_option( 'hide_empty_bar_message', 1 );
	wp_die();
}

/**
 * Returns the URL to redirect after filters reset
 *
 * @return type
 */
function get_filters_bar_reset_url() {
	if ( ! is_product_filters_active() ) {
		return '';
	}
	$session = get_product_catalog_session();
	if ( ! empty( $session['filters']['filtered-url'] ) ) {
		return $session['filters']['filtered-url'];
	}

	return '';
}

add_action( 'ic_catalog_wp', 'ic_set_catalog_query' );
add_action( 'before_ajax_product_list', 'ic_set_catalog_query' );

function ic_set_catalog_query() {
	$catalog_query = ic_get_catalog_query();
	if ( ! $catalog_query ) {
		if ( is_home_archive() || ( ! more_products() && is_custom_product_listing_page() ) ) {
			$catalog_query = ic_set_home_listing_query();
			if ( ! empty( $catalog_query ) && ! empty( $catalog_query->query_vars ) ) {
				ic_save_global( 'catalog_query', $catalog_query );
			}
		} else {
			/*
			$catalog_query = apply_filters( 'ic_catalog_query', '', $GLOBALS['wp_query']->query );
			if ( empty( $catalog_query ) ) {
				$catalog_query = $GLOBALS['wp_query'];
			}
			*/
			if ( ! empty( $GLOBALS['wp_query'] ) && ! empty( $GLOBALS['wp_query']->query_vars ) ) {
				ic_save_global( 'catalog_query', $GLOBALS['wp_query'] );
			}
		}
		ic_get_product_id();
		ic_get_current_category_id();
	}
}

function ic_get_catalog_query( $default = false ) {
	$catalog_query = ic_get_global( 'catalog_query' );
	if ( $default && empty( $catalog_query ) ) {
		global $wp_query;
		$catalog_query = $wp_query;
	}

	return $catalog_query;
}

function ic_set_pre_filters_query_vars( $query_vars = null, $force_save = false ) {
	if ( ! ic_get_global( 'catalog_pre_filters_query' ) || $force_save ) {
		if ( empty( $query_vars ) ) {
			global $wp_query;
			$query_vars = $wp_query->query;
		}
		ic_save_global( 'catalog_pre_filters_query_vars', $query_vars );
	}
}

function ic_get_catalog_query_vars( $default = false, $pre_filters = false ) {
	if ( $pre_filters ) {
		$catalog_query_vars = ic_get_global( 'catalog_pre_filters_query_vars' );
	}
	if ( empty( $catalog_query_vars ) ) {
		$catalog_query = ic_get_catalog_query( $default );
		if ( ! empty( $catalog_query->query ) ) {
			$catalog_query_vars = $catalog_query->query;
		}
	}
	if ( empty( $catalog_query_vars ) ) {
		$catalog_query_vars = array();
	}

	return $catalog_query_vars;
}

/**
 * @return array of taxonomies as keys and filter names as values
 */
function ic_filter_taxonomies( $only_tax = false ) {
	$filter_taxonomies = apply_filters( 'ic_filter_taxonomies', array( 'al_product-cat' => 'product_category' ) );
	if ( $only_tax ) {
		return array_keys( $filter_taxonomies );
	}

	return $filter_taxonomies;
}
