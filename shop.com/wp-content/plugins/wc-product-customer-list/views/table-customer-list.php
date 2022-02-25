<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.8.5
 */


// Display cell
if ( ! function_exists( 'wpcl_json_prep_cell' ) ) {
	function wpcl_json_prep_cell( $option_name, $data ) {

		$return = '';

		switch ( $option_name ) {
			case 'wpcl_order_number':
				$return = '<a href="' . admin_url( 'post.php' ) . '?post=' . $data . '&action=edit" target="_blank">' . $data . '</a>';
				break;

			case 'wpcl_billing_email':
				$return = '<a href="mailto:' . $data . '">' . $data . '</a>';
				break;

			case 'wpcl_billing_phone':
				$return = '<a href="tel:' . $data . '">' . $data . '</a>';
				break;

			case 'wpcl_customer_id':
				if ( $data ) {
					$return = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $data . '" target="_blank">' . $data . '</a>';
				}
				break;

			case 'wpcl_variations':
				/* @var WC_Data $data */
				if ( empty( $data ) || method_exists( $data, 'get_meta_data' ) == false ) {
					break;
				}

				$return = '<span style="max-height: 50px; overflow-y: auto; display: block;">';
				foreach ( $data->get_meta_data() as $itemvariation ) {
					if ( ! is_array( ( $itemvariation->value ) ) ) {
						$return .= '<strong>' . wc_attribute_label( $itemvariation->key ) . '</strong>: &nbsp;' . wc_attribute_label( $itemvariation->value ) . '<br />';
					}
				}
				$return .= '</span>';
				break;

			default:
				$return = $data;
				break;
		}

		return $return;
	}
}


if ( ! function_exists( 'wpcl_count_rightpress_entries' ) ) {
	function wpcl_count_rightpress_entries( $display_values ) {
		$found_keys    = array();
		$highest_count = 0;

		foreach ( $display_values as $display_value ) {
			if ( ! isset( $found_keys[ $display_value['key'] ] ) ) {
				$found_keys[ $display_value['key'] ] = 1;
			} else {
				$found_keys[ $display_value['key'] ] ++;
			}

			$highest_count = $found_keys[ $display_value['key'] ] > $highest_count ? $found_keys[ $display_value['key'] ] : $highest_count;
		}


		return $highest_count;

	}
}


// Load metabox at bottom of product admin screen

if ( ! function_exists( 'wpcl_post_meta_boxes_setup' ) ) {
	add_action( 'load-post.php', 'wpcl_post_meta_boxes_setup' );
	function wpcl_post_meta_boxes_setup() {
		add_action( 'add_meta_boxes', 'wpcl_add_post_meta_boxes' );
	}
}

// Set metabox defaults

if ( ! function_exists( 'wpcl_add_post_meta_boxes' ) ) {
	function wpcl_add_post_meta_boxes() {
		add_meta_box(
			'wc-product-customer-list-meta-box',
			esc_html__( 'Customers who bought this product', 'wc-product-customer-list' ),
			'wpcl_post_class_meta_box_json',
			'product',
			'normal',
			'default'
		);
	}
}

// Output customer list inside metabox


if ( ! function_exists( 'wpcl_post_class_meta_box_json' ) ) {
	function wpcl_post_class_meta_box_json() {
		global $post;

		// Get product ID
		if ( ! function_exists( 'yith_wcp_premium_init' ) ) {
			$post_id = $post->ID;
		} else {
			// Fix for YITH Composite Products Premium Bug
			$post_id = intval( $_GET['post'] );
		}

		$order_items = wpcl_gather_item_sales( $post_id );

		if ( count( $order_items ) > 0 ) {
			echo '<script>WPCL_ORDERS = ' . json_encode( $order_items ) . '</script>';

			?>
			<div id="postcustomstuff" class="wpcl">
				<table id="wpcl-list-table" style="width:100%"></table>

				<div class="wpcl-extra-action">
					<?php if ( get_option( 'wpcl_order_qty' ) == 'yes' ) { ?>
						<p class="total">
							<?php echo '<strong>' . __( 'Total quantity sold', 'wc-product-customer-list' ) . ' : </strong> <span class="product-count"></span>'; ?>
						</p>
					<?php } ?>
					<div class="wpcl-btn-mail-to-all-group">
						<a class="button wpcl-btn-mail-to-all" href="mailto:?bcc="><?php _e( 'Email all customers', 'wc-product-customer-list' ); ?></a>


					</div>
					<a href="#" class="button wpcl-btn-email-selected" id="email-selected" disabled><?php _e( 'Email selected customers', 'wc-product-customer-list' ); ?></a>
				</div>
				<div class="wpcl-email-all-technical-note" aria-hidden="true"><?php _e( '*Various browsers have limits as to how many characters mailto: links can have. To be safe, we have broken down your customer list to safe lengths over multiple buttons.', 'wc-product-customer-list' ); ?></div>
			</div>
			<?php
		} else {
			_e( 'This product currently has no customers', 'wc-product-customer-list' );
		}


	}
}


if ( ! function_exists( 'wpcl_gather_item_sales' ) ) {
	function wpcl_gather_item_sales( $post_id ) {
		global $sitepress, $wpdb;

		// get the adjusted post if WPML is active
		if ( isset( $sitepress ) && method_exists( $sitepress, 'get_element_trid' ) && method_exists( $sitepress, 'get_element_translations' ) ) {
			$trid         = $sitepress->get_element_trid( $post_id, 'post_product' );
			$translations = $sitepress->get_element_translations( $trid, 'product' );
			$post_id      = Array();
			foreach ( $translations as $lang => $translation ) {
				$post_id[] = $translation->element_id;
			}
		}


		// Query the orders related to the product

		$order_statuses        = array_map( 'esc_sql', (array) get_option( 'wpcl_order_status_select', array( 'wc-completed' ) ) );
		$order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
		$post_id               = array_map( 'esc_sql', (array) $post_id );
		$post_string           = "'" . implode( "', '", $post_id ) . "'";

		$item_sales = $wpdb->get_results( $wpdb->prepare(
			"SELECT o.ID as order_id, oi.order_item_id,  oim.meta_value AS product_id FROM
			{$wpdb->prefix}woocommerce_order_itemmeta oim
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
			ON oim.order_item_id = oi.order_item_id
			INNER JOIN $wpdb->posts o
			ON oi.order_id = o.ID
			WHERE oim.meta_key = %s
			AND oim.meta_value IN ( $post_string )
			AND o.post_status IN ( $order_statuses_string )
			AND o.post_type NOT IN ('shop_order_refund')
			ORDER BY o.ID DESC",
			'_product_id'
		) );

		return $item_sales;
	}
}
