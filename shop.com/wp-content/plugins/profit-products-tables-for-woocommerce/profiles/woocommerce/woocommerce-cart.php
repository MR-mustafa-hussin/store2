<?php
/**
 * Shortcode [woot_cart]
 *
 * Generates shop cart HTML table
 *
 * @see https://products-tables.com/shortcode/woot_cart/
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOT_Cart {

    private $default_columns = 'id,post_title,price';
    public $setting_key = '';

    public function __construct() {

        $this->setting_key = "woocommerce_cart_columns";

        add_action('wp_ajax_woot_woocommerce_get_cart_info', array($this, 'get_cart_info'));
        add_action('wp_ajax_nopriv_woot_woocommerce_get_cart_info', array($this, 'get_cart_info'));

        add_action('wp_ajax_woot_remove_product_from_cart', array($this, 'remove_product_from_cart'));
        add_action('wp_ajax_nopriv_woot_remove_product_from_cart', array($this, 'remove_product_from_cart'));

        add_action('wp_ajax_woot_remove_all_products_from_cart', array($this, 'remove_all_products_from_cart'));
        add_action('wp_ajax_nopriv_woot_remove_all_products_from_cart', array($this, 'remove_all_products_from_cart'));

        add_action('wp_ajax_woot_update_product_count_in_cart', array($this, 'update_product_count_in_cart'));
        add_action('wp_ajax_nopriv_woot_update_product_count_in_cart', array($this, 'update_product_count_in_cart'));

        add_action('wp_ajax_woot_woocommerce_add_to_cart_some', array($this, 'add_to_cart_some'));
        add_action('wp_ajax_nopriv_woot_woocommerce_add_to_cart_some', array($this, 'add_to_cart_some'));

        add_action('woot_include_assets', function() {
            if (function_exists('wc_get_cart_url')) {
                wp_enqueue_script('woot-woocommerce-cart', WOOT_LINK . 'profiles/woocommerce/js/woocommerce-cart.js', [], WOOT_VERSION, true);
                wp_localize_script('woot-woocommerce-cart', 'woot_woo_js_vars', [
                    'cart_link' => wc_get_cart_url(),
                    'checkout_link' => wc_get_checkout_url(),
                        ]
                );
            }
        });

        //***

        add_action('woot_cart', function ($table_id = 0, $shortcode_args = []) {

            $profile = [];
            $profile[0] = [];
            $selected_columns_keys = (array) explode(',', WOOT_Settings::get($this->setting_key));
            $woo_profile = apply_filters(WOOT::get_table_action(0), $table_id);

            if (!empty($selected_columns_keys)) {
                foreach ($selected_columns_keys as $field_key) {
                    if (isset($woo_profile[$field_key])) {
                        $profile[$field_key] = $woo_profile[$field_key];
                    }
                }
            }

            $profile = array_merge($profile, [
                'quantity' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Quantity', 'profit-products-tables-for-woocommerce')),
                    //'order' => 'desc',
                    'action' => function($product_id, $quantity) {
                        $product = WOOT_WooCommerce::get_product($product_id);
                        if (!$product->get_sold_individually()) {
                            return WOOT_HELPER::draw_html_item('input', [
                                        'type' => 'number',
                                        'value' => $quantity,
                                        'min' => 1,
                                        'step' => 1,
                                        'max' => $product->get_stock_quantity(),
                                        'size' => 4,
                                        'inputmode' => 'numeric',
                                        'sku' => $product->get_sku(),
                                        'onkeyup' => "woot_cart.count_update(event,{$product_id},this);void(0);",
                                    ]) . '&nbsp;' . WOOT_HELPER::draw_html_item('a', [
                                        'href' => "javascript: void(0);",
                                        'class' => 'woot_cart_update',
                                        'onclick' => "woot_cart.update({$product_id},this);void(0);"
                                            ], '<i class="woot-icon">&#xe802;</i>');
                        } else {

                            return apply_filters('woot_quantity_individually', WOOT_Vocabulary::get(esc_html__('individually 1', 'profit-products-tables-for-woocommerce')));
                        }
                    }
                ],
                'subtotal' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Subtotal', 'profit-products-tables-for-woocommerce')),
                    'order' => 'desc',
                    'action' => function($product_id, $quantity) {
                        $WC_Cart = new WC_Cart();
                        $product = WOOT_WooCommerce::get_product($product_id);
                        return $WC_Cart->get_product_subtotal($product, $quantity);
                    }
                ],
                'actions' => [
                    'title' => 'X',
                    'action' => function($product_id) {
                        return WOOT_HELPER::draw_html_item('a', [
                                    'href' => "javascript: woot_cart.remove({$product_id});void(0);"
                                        ], '<i class="woot-icon">&#xe803;</i>');
                    }
                ]
            ]);

            return $profile;
        }, 1, 2);

        add_shortcode('woot_cart', function($args) {
            woot()->include_assets();
            return WOOT_HELPER::render_html('profiles/woocommerce/views/woot_cart.php', $args);
        });

        //***

        add_action('woot_extend_settings', function($rows) {
            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Hide shop cart button', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('hide_shop_cart_btn', WOOT_Settings::get('hide_shop_cart_btn'), 0, 'woot_save_settings_field'),
                'notes' => esc_html__('Hide shop cart button on WOOT Cart panel', 'profit-products-tables-for-woocommerce')
            ];

            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Hide checkout button', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('hide_checkout_btn', WOOT_Settings::get('hide_checkout_btn'), 0, 'woot_save_settings_field'),
                'notes' => esc_html__('Hide checkout button on WOOT Cart panel', 'profit-products-tables-for-woocommerce')
            ];

            //+++

            $selected = explode(',', WOOT_Settings::get($this->setting_key));
            $options = [];

            foreach (apply_filters(WOOT_WooCommerce::$action, 0, []) as $key => $value) {
                if (isset($value['title'])) {
                    $options[$key] = $value['title'];
                }
            }

            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Shop cart columns', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => WOOT_HELPER::draw_select([
                        'class' => 'woot-multiple-select',
                        'multiple' => '',
                        'data-action' => 'woot_save_settings_field',
                        'data-values' => WOOT_Settings::get($this->setting_key)
                            ], $options, $selected),
                    'custom_field_key' => $this->setting_key
                ],
                'notes' => esc_html__('Columns of WOOT shop cart. Press and wait to reorder.', 'profit-products-tables-for-woocommerce')
            ];

            return $rows;
        }, 10, 1);

        add_action('woot_extend_settings_default', function($defaults) {
            $defaults[$this->setting_key] = $this->default_columns;
            return $defaults;
        }, 10, 1);
    }

    //ajax
    public function get_cart_info() {
        die($this->__get_cart_info());
    }

    private function __get_cart_info($jsoned = true, $table_only = false) {
        $res = $this->__get_products_in_cart();

        $res['table'] = woot()->draw_table_data([
            'mode' => 'json',
            'table_data' => $res['products'],
            'action' => 'woot_cart',
            'columns' => array_keys(apply_filters('woot_cart', 0)),
            'orderby' => 'title',
            'order' => 'asc',
            'per_page' => -1,
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
                ], 'no matter here', FALSE);

        //***

        if ($table_only) {
            return $res['table'];
        }

        if ($jsoned) {
            return json_encode($res);
        }

        return $res;
    }

    private function __get_products_in_cart() {
        global $woocommerce;

        $ids = [];
        $products = [];
        $quantity = 0;

        //***
        $profile = apply_filters('woot_cart', 0);

        foreach ($woocommerce->cart->get_cart() as $values) {
            $ids[] = $product_id = intval($values['data']->get_id());

            $p = [];
            $p['pid'] = $product_id;
            foreach ($profile as $field_key => $field) {
                if ($field_key === 0) {
                    continue;
                }

                if (is_callable($field['action'])) {
                    $p[$field_key] = $field['action']($product_id, intval($values['quantity']));
                    if ('quantity' === $field_key) {
                        $p['q'] = intval($values['quantity']);
                    }
                }
            }

            //+++

            $products[] = $p;
            $quantity += $values['quantity'];
        }


        return ['ids' => $ids, 'quantity' => $quantity, 'products' => $products, 'subtotal' => WC()->cart->get_cart_subtotal(false)];
    }

    //ajax
    public function remove_product_from_cart() {

        $product_id = intval($_REQUEST['product_id']);

        if ($product_id > 0) {
            $cart = WC()->instance()->cart;

            if (!empty($cart->cart_contents)) {
                foreach ($cart->cart_contents as $c) {
                    if (intval($c['product_id']) === $product_id OR intval($c['variation_id']) === $product_id) {
                        $cart->set_quantity($c['key'], 0);
                        break;
                    }
                }
            }

            die($this->__get_cart_info());
        }

        exit;
    }

    //ajax
    public function remove_all_products_from_cart() {

        $products_ids = explode(',', WOOT_HELPER::sanitize_text($_REQUEST['products_ids']));

        if (!empty($products_ids)) {
            global $woocommerce;
            $woocommerce->cart->empty_cart();
            die($this->__get_cart_info());
        }

        exit;
    }

    //ajax
    public function update_product_count_in_cart() {

        $product_id = intval($_REQUEST['product_id']);
        $quantity = intval($_REQUEST['quantity']);

        if ($product_id > 0 AND $quantity > 0) {
            $cart = WC()->instance()->cart;

            if (!empty($cart->cart_contents)) {
                foreach ($cart->cart_contents as $c) {
                    if (intval($c['product_id']) === $product_id OR intval($c['variation_id']) === $product_id) {
                        $cart->set_quantity($c['key'], $quantity);
                        break;
                    }
                }
            }

            die($this->__get_cart_info());
        }

        exit;
    }

    //ajax
    public function add_to_cart_some() {
        if (!empty($_REQUEST['products'])) {
            $products = WOOT_HELPER::sanitize_array(json_decode(stripslashes($_REQUEST['products']), true));

            foreach ($products as $pd) {
                $product = wc_get_product(intval($pd['product_id']));
                switch ($product->get_type()) {
                    case 'variation':
                        $variations = [];
                        foreach ($product->get_attributes('edit') as $key => $value) {
                            $variations['attribute_' . $key] = $value;
                        }

                        WC()->cart->add_to_cart($product->get_parent_id(), intval($pd['count']), intval($pd['product_id']), $variations);
                        break;
                    default:
                        $count = isset($pd['count']) ? intval($pd['count']) : 0;
                        WC()->cart->add_to_cart(intval($pd['product_id']), $count, 0);
                        break;
                }
            }
        }

        die($this->__get_cart_info());
    }

}

new WOOT_Cart();
