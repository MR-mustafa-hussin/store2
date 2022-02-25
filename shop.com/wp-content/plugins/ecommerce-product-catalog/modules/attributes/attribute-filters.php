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
class ic_attribute_default_filters {

	function __construct() {
		add_action( 'ic_set_product_filters', array( __CLASS__, 'set_size_filter' ) );
		add_action( 'apply_product_filters', array( $this, 'apply_size_filter' ) );
		add_action( 'ic_size_filters', array( $this, 'show_filters' ) );

		add_filter( 'shortcode_query', array( $this, 'apply_shortcode_size_filter' ) );
		add_filter( 'home_product_listing_query', array( $this, 'apply_shortcode_size_filter' ) );
	}

	function show_filters() {
		echo $this->get_size_filters();
	}

	function apply_size_filter( $query ) {
		$meta_query = $this->meta_query( $query );
		if ( !empty( $meta_query ) ) {
			$meta_query[ 'relation' ] = 'AND';
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Applies product size filter to shortcode query
	 *
	 * @param type $shortcode_query
	 * @return type
	 */
	function apply_shortcode_size_filter( $shortcode_query ) {
		$meta_query = $this->meta_query();
		if ( empty( $meta_query ) ) {
			return $shortcode_query;
		}

		if ( empty( $shortcode_query[ 'meta_query' ] ) ) {
			$shortcode_query[ 'meta_query' ] = array();
		}
		$shortcode_query[ 'meta_query' ][] = $meta_query;

		return $shortcode_query;
	}

	function meta_query( $query = null ) {
		$size_fields = ic_size_field_names();
		$meta_query	 = array();
		foreach ( $size_fields as $field_name => $label ) {
			if ( is_product_filter_active( $field_name ) ) {
				$min_max = apply_filters( 'ic_size_filter_value', get_product_filter_value( $field_name ) );
				if ( empty( $min_max[ 1 ] ) ) {
					continue;
				}
				if ( strval( $min_max[ 0 ] ) === strval( $this->get_min( $field_name, $query ) ) && strval( $min_max[ 1 ] ) === strval( $this->get_max( $field_name, $query ) ) ) {
					continue;
				}
				$this_meta_query				 = array();
				$this_meta_query[ 'relation' ]	 = 'OR';
				$this_meta_query[]				 = array(
					'key'		 => $field_name,
					'value'		 => $min_max,
					'compare'	 => 'BETWEEN',
					'type'		 => 'numeric',
				);
				$this_meta_query[]				 = array(
					'key'		 => '_1' . $field_name . '_filterable',
					'value'		 => $min_max,
					'compare'	 => 'BETWEEN',
					'type'		 => 'numeric',
				);
				$meta_query[]					 = $this_meta_query;
			}
		}
		return $meta_query;
	}

	static function set_size_filter() {
		$session	 = get_product_catalog_session();
		$size_fields = ic_size_field_names();
		$save		 = false;
		foreach ( $size_fields as $field_name => $label ) {
			if ( isset( $_GET[ $field_name ] ) ) {
				if ( !is_array( $_GET[ $field_name ] ) ) {
					$filter_value = strval( $_GET[ $field_name ] );
				} else {
					$filter_value = array_map( 'strval', $_GET[ $field_name ] );
				}
				if ( !empty( $filter_value ) && (is_array( $filter_value ) || ic_string_contains( $filter_value, ';' ) ) ) {
					if ( !is_array( $filter_value ) ) {
						$min_max = explode( ';', $filter_value );
					} else {
						$min_max = $filter_value;
					}
					if ( !isset( $session[ 'filters' ] ) ) {
						$session[ 'filters' ] = array();
					}
					$session[ 'filters' ][ $field_name ] = $min_max;
				} else if ( isset( $session[ 'filters' ][ $field_name ] ) ) {
//unset( $session[ 'filters' ][ $field_name ] );
				}
				$save = true;
			}
		}
		if ( $save ) {
			set_product_catalog_session( $session );
		}
	}

	function get_size_filters() {
		$unit			 = ic_attributes_get_size_unit();
		$field_names	 = ic_size_field_names();
		$filter_fields	 = '';
		foreach ( $field_names as $field_name => $label ) {
			$min		 = $this->get_min( $field_name );
			$max		 = $this->get_max( $field_name );
			$current_min = $this->get_current_min( $field_name );
			$current_max = $this->get_current_max( $field_name );
			if ( !empty( $max ) && ($min !== $max || (is_product_filter_active( $field_name ) && $current_max <= $max && $current_min >= $min) ) ) {
				$filter_fields .= '<div class="size-filter-row"><label for="' . $field_name . '">' . $label . '</label><div class="size-field-container"><input id="' . $field_name . '" data-unit="' . $unit . '" data-current-min="' . $current_min . '" data-current-max="' . $current_max . '" data-min="' . $min . '" data-max="' . $max . '" class="ic-range-slider" type="text" name="' . $field_name . '" value=""></div></div>';
			}
		}
		if ( empty( $filter_fields ) ) {
			$filter_fields = apply_filters( 'ic_one_size_available', '' );
		}
		return $filter_fields;
	}

	function get_current_min( $field_name ) {
		$min_max = get_product_filter_value( $field_name );
		if ( !empty( $min_max[ 0 ] ) ) {
			return apply_filters( 'ic_size_filter_current_min', $min_max[ 0 ] );
		} else {
			return $this->get_min( $field_name );
		}
	}

	function get_current_max( $field_name ) {
		$min_max = get_product_filter_value( $field_name );
		if ( !empty( $min_max[ 1 ] ) ) {
			return apply_filters( 'ic_size_filter_current_max', $min_max[ 1 ] );
		} else {
			return $this->get_max( $field_name );
		}
	}

	function get_min( $field_name, $query = null ) {
		$values	 = $this->filter_array( array_merge( $this->get_meta_values( $field_name, $query ), $this->get_meta_values( '_1' . $field_name . '_filterable', $query ) ) );
		natsort( $values );
		$return	 = apply_filters( 'ic_size_filter_min', intval( reset( $values ) ) );
		return $return;
	}

	function get_max( $field_name, $query = null ) {
		$values	 = $this->filter_array( array_merge( $this->get_meta_values( $field_name, $query ), $this->get_meta_values( '_1' . $field_name . '_filterable', $query ) ) );
		natsort( $values );
		$return	 = apply_filters( 'ic_size_filter_max', intval( end( $values ) ) );
		return $return;
	}

	function filter_array( $array ) {
		$filtered_value = array_map( 'floatval', array_filter( array_unique( $array ) ) );
		return $filtered_value;
	}

	function get_meta_values( $key = '', $query = null, $type = 'al_product', $status = 'publish' ) {

		global $wpdb, $ic_product_filters_query, $ic_ajax_query_vars;
		if ( empty( $key ) ) {
			return;
		}

		$r = ic_get_global( 'get_meta_values' . $key );
		if ( $r !== false ) {
			return $r;
		}
		$r = array();
		if ( ( is_ic_taxonomy_page( $query ) || is_ic_product_search( $query ) ) && !is_ic_product_listing( $query ) /* || !empty( $ic_product_filters_query ) || (is_ic_ajax() && !is_ic_product_listing()) */ ) {
			$product_ids = $this->get_current_products( $key );
			if ( !empty( $product_ids ) ) {
				$r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE
		pm.meta_key = '%s'
        AND p.post_status = '%s'
        AND p.post_type = '%s'
		AND p.ID IN ($product_ids)
    ", $key, $status, $type ) );
			}
		} else {
			$r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s'
        AND p.post_status = '%s'
        AND p.post_type = '%s'
    ", $key, $status, $type ) );
		}
		ic_save_global( 'get_meta_values' . $key, $r );
		return $r;
	}

	function get_current_products( $key ) {
		global $wp_query, $ic_ajax_query_vars;
		if ( !empty( $ic_ajax_query_vars ) && is_ic_ajax() ) {
			$query = $ic_ajax_query_vars;
		} else {
			$query = $wp_query->query;
		}
		if ( !empty( $wp_query->query_vars ) && is_product_filters_active( array( $key ) ) ) {
			if ( !empty( $wp_query->query_vars[ 'tax_query' ] ) ) {
				$query[ 'tax_query' ] = $wp_query->query_vars[ 'tax_query' ];
			}
		}


		$query[ 'posts_per_page' ]	 = 1000;
		unset( $query[ 'paged' ] );
		$product_ids				 = ic_get_global( 'get_meta_values_current_ids' );
		if ( !$product_ids ) {
			remove_action( 'apply_product_filters', array( $this, 'apply_size_filter' ) );
			remove_action( 'ic_pre_get_products', 'set_products_limit', 99 );
			$products	 = new WP_QUERY( $query );
			add_action( 'apply_product_filters', array( $this, 'apply_size_filter' ) );
			add_action( 'ic_pre_get_products', 'set_products_limit', 99 );
			$product_ids = implode( ',', wp_list_pluck( $products->posts, 'ID' ) );
			ic_save_global( 'get_meta_values_current_ids', $product_ids );
			wp_reset_postdata();
		}
		return $product_ids;
	}

}

global $ic_attribute_default_filters;
$ic_attribute_default_filters = new ic_attribute_default_filters;
