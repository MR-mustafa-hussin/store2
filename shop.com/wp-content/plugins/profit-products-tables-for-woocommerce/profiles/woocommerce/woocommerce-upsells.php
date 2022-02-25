<?php

/**
 * Shortcode [woot_upsells]
 *
 * Generates a product upsells products HTML table
 *
 * @see https://products-tables.com/shortcode/woot_upsells/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOT_WooCommerceUpsells extends WOOT_WooCommerceUniversal {

    public $slug = 'upsells';

    public function __construct() {
        $this->settings_columns_title = esc_html__('Upsells columns', 'profit-products-tables-for-woocommerce');
        parent::__construct();
    }

    /**
     * Set filtration arguments for the current table
     *
     * @since 1.0.0
     * @param array $args wp_query arguments
     * @param array $filter_data current filtration data
     * @param array $shortcode_args arguments from shortcode
     * @return array wp_query arguments
     */
    public function filter_provider($args, $filter_data, $shortcode_args) {
        //[woot_button filter_provider="woot_upsells" product_id=49 mode="to_json"]
        if (isset($shortcode_args['product_id'])) {
            $filter_data['product_id'] = intval($shortcode_args['product_id']);
        }

        //+++

        if (is_array($filter_data) AND isset($filter_data['product_id']) AND intval($filter_data['product_id']) > 0) {
            $product = WOOT_WooCommerce::get_product(intval($filter_data['product_id']));

            if ($product AND method_exists($product, 'get_upsell_ids')) {
                if (!$args['post__in'] = $product->get_upsell_ids()) {
                    $args['post__in'] = [-1];
                }
            } else {
                $args['post__in'] = [-1];
            }
        }

        return $args;
    }

}

new WOOT_WooCommerceUpsells();
