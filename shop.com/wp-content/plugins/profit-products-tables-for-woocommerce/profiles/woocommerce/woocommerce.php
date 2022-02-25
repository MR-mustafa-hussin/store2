<?php

/**
 * Profile for woocommerce fields
 *
 * Generates and handles fields of the products tables
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once WOOT_PATH . 'profiles/woocommerce/compatibility.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-cart.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-universal.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-grouped.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-upsells.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-cross-sells.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-related.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-variations.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-reviews.php';
include_once WOOT_PATH . 'profiles/woocommerce/woocommerce-single.php';

final class WOOT_WooCommerce {

    public static $action = 'woot_woocommerce_tables';
    public static $allowed_tags = '<p><br><br/><br/><hr><strong><b><em><span><a>';
    public static $synonyms = [];
    public $filter = null;
    public static $orderby_select_fields = [];
    public static $fields_options = ['css-font-size', 'css-font-family', 'css-color', 'css-background', 'css-media-hide'];

    public function __construct() {
        woot_register_profile(self::$action, 'WooCommerce');
        add_action(self::$action, [$this, 'action'], 10, 2);

        add_action('admin_init', array($this, 'admin_init'), 9999);
        add_action('init', array($this, 'init'), 9999);

        add_action('woot_filter_provider_woocommerce', function ($args, $filter_data) {
            return woot()->filter->provider($filter_data, $args);
        }, 10, 2);

        //***
        //table here like drop-down list
        add_shortcode('woot_drop_down', function($args) {
            if (isset($args['id'])) {
                woot()->include_assets();

                $data = [];

                return WOOT_HELPER::render_html('profiles/woocommerce/views/woot_drop_down.php', $data);
            }
        });

        add_filter('woot_extend_options', [$this, 'woot_extend_options'], 10, 2);
        add_filter('woot_table_classes', function($args) {

            if (!isset($args['classes'])) {
                $args['classes'] = '';
            }

            if (isset($args['show_cart']) AND intval($args['show_cart'])) {
                $args['classes'] .= ' woot-woocommerces-show-cart';
            } else {
                if (isset($args['id'])) {
                    if (WOOT::get_table_action($args['id']) === self::$action) {
                        $args['classes'] .= woot()->columns->options->get($args['id'], 'is_cart_shown', 0) ? 'woot-woocommerces-show-cart' : '';
                    }
                }
            }

            return $args;
        }, 10, 1);

        //***
        //lets add columns to the plugged-in extensions
        global $woot_extend_ext_profiles;
        if (!empty($woot_extend_ext_profiles[self::$action])) {
            foreach ($woot_extend_ext_profiles[self::$action] as $hook) {
                add_filter($hook, function($profile, $selected_columns_keys) {
                    return $this->extend_ext_profiles($profile, $selected_columns_keys);
                }, 10, 2);
            }
        }

        //***

        add_filter('woot_wp_query_args', function($args, $table_id) {
            if ($table_id > 0 AND woot()->columns->options->get($table_id, 'hide_in_cart_added', 0)) {
                $args['post__not_in'] = $this->get_ids_in_cart();
            }
            return $args;
        }, 10, 2);

        //***
        //process ordering by sort-by select
        add_filter('woot_wp_query_args', function($args, $table_id) {

            if (substr_count($args['orderby'], 'orderby_select_') > 0) {
                $args['orderby'] = str_replace('orderby_select_', '', $args['orderby']);

                switch ($args['orderby']) {
                    case 'price':
                        $args['order'] = 'asc';
                        break;
                    case 'price-desc':
                        $args['orderby'] = 'price';
                        $args['order'] = 'desc';
                        break;

                    case 'id':
                        $args['order'] = 'asc';
                        break;
                    case 'id-desc':
                        $args['orderby'] = 'id';
                        $args['order'] = 'desc';
                        break;

                    case 'title':
                        $args['order'] = 'asc';
                        break;
                    case 'title-desc':
                        $args['orderby'] = 'title';
                        $args['order'] = 'desc';
                        break;

                    case 'sku':
                    case 'sku-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'sku-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'meta_value';
                        $args['meta_key'] = '_sku';

                        break;

                    case 'stock':
                    case 'stock-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'stock-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = '_stock';

                        if (!isset($args['tax_query'])) {
                            $args['tax_query'] = [];
                        }

                        //because for this products types stock is not relevant item
                        $args['tax_query'][] = array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => array('grouped', 'external'),
                            'operator' => 'NOT IN',
                        );

                        break;


                    case 'sales':
                    case 'sales-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'sales-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = 'total_sales';

                        if (!isset($args['tax_query'])) {
                            $args['tax_query'] = [];
                        }

                        //because for this products types total_sales is not relevant item
                        $args['tax_query'][] = array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => array('grouped', 'external'),
                            'operator' => 'NOT IN',
                        );

                        break;

                    case 'comments':
                    case 'comments-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'comments-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'comment_count';

                        break;

                    case 'modified':
                    case 'modified-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'modified-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'post_modified';

                        break;


                    case 'on_sales':

                        $args['order'] = 'desc';
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = '_sale_price';

                        break;


                    case 'on_featured':

                        $args['order'] = 'desc';
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = '_featured';

                        break;

                    case 'on_stock':

                        $args['order'] = 'desc';
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = '_stock';

                        break;

                    default:

                        if ($table_id > 0) {
                            $metas = woot()->columns->meta->get_rows($table_id);
                            if (!empty($metas)) {
                                foreach ($metas as $m) {
                                    if ($args['orderby'] === $m['meta_key'] || $args['orderby'] === $m['meta_key'] . '-desc') {

                                        $args['order'] = 'asc';

                                        if ($args['orderby'] === $m['meta_key'] . '-desc') {
                                            $args['order'] = 'desc';
                                        }

                                        $args['meta_key'] = $m['meta_key'];
                                        if ($m['meta_type'] === 'number') {
                                            $args['orderby'] = 'meta_value_num';
                                        }

                                        break;
                                    }
                                }
                            }
                        }

                        break;
                }


                //for default woo sorts fields as popularity
                WC()->query->get_catalog_ordering_args($args['orderby'], $args['order']);
            }

            return $args;
        }, 10, 2);
    }

    /**
     * Hook init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init() {
        //!!important do it here
        $this->init_orderby_select_fields();
    }

    /**
     * Hook admin_init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_init() {
        //!!important do it here
        $this->init_orderby_select_fields();
    }

    /**
     * Initialization of the fields of "order-by" drop-down
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_orderby_select_fields() {
        self::$orderby_select_fields = apply_filters('woot_woocommerce_catalog_orderby', [
            'popularity' => WOOT_Vocabulary::get(esc_html__('Sort by popularity', 'profit-products-tables-for-woocommerce')),
            'rating' => WOOT_Vocabulary::get(esc_html__('Sort by average rating', 'profit-products-tables-for-woocommerce')),
            'date' => WOOT_Vocabulary::get(esc_html__('Sort by latest', 'profit-products-tables-for-woocommerce')),
            'price' => WOOT_Vocabulary::get(esc_html__('Sort by price: low to high', 'profit-products-tables-for-woocommerce')),
            'price-desc' => WOOT_Vocabulary::get(esc_html__('Sort by price: high to low', 'profit-products-tables-for-woocommerce')),
            'title' => WOOT_Vocabulary::get(esc_html__('Sort by name A-Z', 'profit-products-tables-for-woocommerce')),
            'title-desc' => WOOT_Vocabulary::get(esc_html__('Sort by name Z-A', 'profit-products-tables-for-woocommerce')),
            'sku' => WOOT_Vocabulary::get(esc_html__('SKU: Ascending', 'profit-products-tables-for-woocommerce')),
            'sku-desc' => WOOT_Vocabulary::get(esc_html__('SKU: Descending', 'profit-products-tables-for-woocommerce')),
            'stock' => WOOT_Vocabulary::get(esc_html__('Stock Quantity: Ascending', 'profit-products-tables-for-woocommerce')),
            'stock-desc' => WOOT_Vocabulary::get(esc_html__('Stock Quantity: Descending', 'profit-products-tables-for-woocommerce')),
            'sales' => WOOT_Vocabulary::get(esc_html__('Total Sales: Ascending', 'profit-products-tables-for-woocommerce')),
            'sales-desc' => WOOT_Vocabulary::get(esc_html__('Total Sales: Descending', 'profit-products-tables-for-woocommerce')),
            'comments' => WOOT_Vocabulary::get(esc_html__('Number of Comments: Ascending', 'profit-products-tables-for-woocommerce')),
            'comments-desc' => WOOT_Vocabulary::get(esc_html__('Number of Comments: Descending', 'profit-products-tables-for-woocommerce')),
            'modified' => WOOT_Vocabulary::get(esc_html__('Last Modified Date: Oldest to Newest', 'profit-products-tables-for-woocommerce')),
            'modified-desc' => WOOT_Vocabulary::get(esc_html__('Last Modified Date: Newest to Oldest', 'profit-products-tables-for-woocommerce')),
            'menu_order' => WOOT_Vocabulary::get(esc_html__('Sort by menu order', 'profit-products-tables-for-woocommerce')),
            'on_sales' => WOOT_Vocabulary::get(esc_html__('Sales products first', 'profit-products-tables-for-woocommerce')),
            //'on_featured' => WOOT_Vocabulary::get(esc_html__('Featured products first', 'profit-products-tables-for-woocommerce')),
            'on_stock' => WOOT_Vocabulary::get(esc_html__('Sort by availability', 'profit-products-tables-for-woocommerce')),
            'rand' => WOOT_Vocabulary::get(esc_html__('Sort by random', 'profit-products-tables-for-woocommerce')),
            'id' => WOOT_Vocabulary::get(esc_html__('Sort by product ID: Ascending', 'profit-products-tables-for-woocommerce')),
            'id-desc' => WOOT_Vocabulary::get(esc_html__('Sort by product ID: Descending', 'profit-products-tables-for-woocommerce'))
        ]);
    }

    /**
     * Hook woot_extend_options, adds more plugins settings
     *
     * @since 1.0.0
     * @param array $rows settings array
     * @param int $table_id table ID
     * @return array plugin settings
     */
    public function woot_extend_options($rows, $table_id) {
        if ($table_id > 0) {
            if (WOOT::get_table_action($table_id) === self::$action) {

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Show Sorting Dropdown', 'profit-products-tables-for-woocommerce'),
                    'value' => WOOT_HELPER::draw_switcher('is_sort_droptdown_shown', woot()->columns->options->get($table_id, 'is_sort_droptdown_shown', 0), $table_id, 'woot_save_table_option'),
                    'value_custom_field_key' => 'is_sort_droptdown_shown',
                    'notes' => esc_html__('Displays Sorting Dropdown list', 'profit-products-tables-for-woocommerce')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Sorting Dropdown Fields', 'profit-products-tables-for-woocommerce'),
                    'value' => WOOT_HELPER::draw_select([
                        'class' => 'woot-multiple-select',
                        'multiple' => '',
                        'data-action' => 'woot_save_table_option',
                        'data-values' => woot()->columns->options->get($table_id, 'orderby_select_fields', ''),
                        'data-use-drag' => 1,
                            ], apply_filters('woot_table_orderby_select_args', self::$orderby_select_fields, $table_id), explode(',', woot()->columns->options->get($table_id, 'orderby_select_fields', ''))),
                    'value_custom_field_key' => 'orderby_select_fields',
                    'notes' => esc_html__('fields which you want to see in Sorting Dropdown fields. Press and wait to reorder.', 'profit-products-tables-for-woocommerce')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Show shop cart', 'profit-products-tables-for-woocommerce'),
                    'value' => WOOT_HELPER::draw_switcher('is_cart_shown', woot()->columns->options->get($table_id, 'is_cart_shown', 0), $table_id, 'woot_save_table_option'),
                    'value_custom_field_key' => 'is_cart_shown',
                    'notes' => esc_html__('Displays cart as button with information, on click popup with selected products appears', 'profit-products-tables-for-woocommerce')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Cart/Sorting positions', 'profit-products-tables-for-woocommerce'),
                    'value' => WOOT_HELPER::draw_select([
                        'data-action' => 'woot_save_table_option',
                            ], [
                        0 => esc_html__('Cart right, Sorting dropdown left', 'profit-products-tables-for-woocommerce'),
                        1 => esc_html__('Cart left, Sorting dropdown right', 'profit-products-tables-for-woocommerce'),
                            ], woot()->columns->options->get($table_id, 'cart_position', 0)),
                    'value_custom_field_key' => 'cart_position',
                    'notes' => esc_html__('On which side should be shop cart: be left or right. Sorting Dropdown will take opposite position.', 'profit-products-tables-for-woocommerce')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('After to cart adding', 'profit-products-tables-for-woocommerce'),
                    'value' => WOOT_HELPER::draw_select([
                        'data-action' => 'woot_save_table_option',
                            ], [
                        0 => esc_html__('Stay on the same page', 'profit-products-tables-for-woocommerce'),
                        1 => esc_html__('Redirect to the cart page', 'profit-products-tables-for-woocommerce'),
                        2 => esc_html__('Redirect to the checkout page', 'profit-products-tables-for-woocommerce'),
                            ], woot()->columns->options->get($table_id, 'add_to_cart_instant_redirect', 0)),
                    'value_custom_field_key' => 'add_to_cart_instant_redirect',
                    'notes' => esc_html__('Action after clicking on button Add to cart', 'profit-products-tables-for-woocommerce')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Hide added to cart products', 'profit-products-tables-for-woocommerce'),
                    'value' => '',
                    'value_custom_field_key' => '',
                    'notes' => esc_html__('Do not display in the table products, which are in the cart', 'profit-products-tables-for-woocommerce') . '. ' . '<a href="https://products-tables.com/upgrading-to-premium/" target="_blank" style="color: red;">' . sprintf(esc_html__('Premium version', 'profit-products-tables-for-woocommerce')) . '</a>'
                ];
            }
        }
        return $rows;
    }

    /**
     * Get woocommerce product, data is cached for further requests
     *
     * @since 1.0.0
     *
     * @return object woocommerce product
     */
    public static function get_product($product_id) {
        static $cache = [];

        if (!isset($cache[$product_id])) {
            $cache[$product_id] = wc_get_product($product_id);
        }

        return $cache[$product_id];
    }

    /**
     * Init all possible fields data/output/filtration for table columns initialization - data center
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param array $shortcode_args shortcode arguments
     * @return array woocommerce active fields
     */
    public function action($table_id = 0, $shortcode_args = []) {
        //$current_action = current_action();

        if (!function_exists('wc_get_attribute_taxonomies')) {
            return;
        }

        $post_statuses = 'publish';
        if ($table_id > 0) {
            $post_statuses = woot()->columns->options->get($table_id, 'post_statuses', 'publish');
        }

        if (isset($shortcode_args['post_status'])) {
            $post_statuses = $shortcode_args['post_status'];
        }

        if (!$post_statuses) {
            $post_statuses = 'publish';
        }

        //***

        $profile = [
            0 => [
                'post_type' => 'product',
                //false - in options we can select post type for this profile, true - not
                'post_type_fixed' => TRUE,
                'post_statuses' => $post_statuses,
                'filter_provider' => 'woocommerce'
            ],
            'id' => [
                'title' => esc_html__('ID', 'profit-products-tables-for-woocommerce'),
                'order' => 'desc',
                'options' => WOOT_WooCommerce::$fields_options,
                'action' => function($post_id) {
                    return $post_id;
                }
            ],
            'thumbnail' => [
                'title' => esc_html__('Thumbnail', 'profit-products-tables-for-woocommerce'),
                'order' => FALSE,
                'options' => ['thumbnail_width', 'thumbnail_no_link', 'thumbnail_preview_width', 'css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $product = self::get_product($post_id);
                    $full = $thumb = WOOT_ASSETS_LINK . 'img/not-found.jpg';
                    $has_img = false;

                    //***

                    $width = WOOT_Settings::get('thumbnail_size');

                    if ($table_id > 0) {
                        $options = woot()->get_field_options($table_id, 'thumbnail');

                        if (isset($options['thumbnail_width'])) {
                            $width = intval($options['thumbnail_width']);
                        }
                    }

                    if (!$width) {
                        $width = WOOT_Settings::get('thumbnail_size');
                    }

                    //***

                    $img = WOOT_HELPER::draw_html_item('img', array(
                                'src' => apply_filters('woot_no_img_found', $thumb, $table_id, $post_id),
                                'width' => $width,
                                'alt' => '',
                                'class' => 'woot-thumbnail'
                    ));

                    if (has_post_thumbnail($post_id)) {
                        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');

                        if (is_array($img_src) AND!empty($img_src[0])) {
                            $thumb = $img_src[0];
                            $img = WOOT_HELPER::draw_html_item('img', array(
                                        'src' => $thumb,
                                        'width' => $width,
                                        'alt' => '',
                                        'class' => 'woot-thumbnail'
                            ));

                            //***

                            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
                            $full = $img_src[0];
                            $has_img = true;
                        }
                    }

                    $preview_width = 400;
                    if ($table_id > 0) {
                        $thumbnail_options = woot()->get_field_options($table_id, 'thumbnail');
                        if (isset($thumbnail_options['thumbnail_preview_width'])) {
                            $preview_width = $thumbnail_options['thumbnail_preview_width'];
                        }
                    }

                    if (isset($shortcode_args['preview_image_width'])) {
                        $preview_width = intval($shortcode_args['preview_image_width']);
                    }

                    if (is_object($product)) {

                        $no_link = apply_filters('woot_disable_all_links_on_thumbs', false);

                        if (isset($thumbnail_options)) {
                            if (isset($thumbnail_options['thumbnail_no_link'])) {
                                $no_link = intval($options['thumbnail_no_link']);
                            }
                        }

                        if (isset($shortcode_args['disable_link_on_thumb'])) {
                            $no_link = intval($shortcode_args['disable_link_on_thumb']);
                        }

                        //+++

                        if ($no_link) {
                            return $img;
                        } else {
                            return WOOT_HELPER::draw_html_item('a', array(
                                        'href' => $product->get_permalink(),
                                        'data-full-img' => $full,
                                        'data-width' => $preview_width,
                                        'data-title' => $product->get_title(),
                                        'target' => '_blank',
                                        'onmouseover' => $has_img && $preview_width > 0 ? 'woot_helper.image_preview(this)' : ''
                                            ), $img);
                        }
                    }
                }
            ],
            'post_title' => [
                'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce'),
                'order' => 'asc',
                'options' => array_merge(['title_as_text', 'show_labels'], WOOT_WooCommerce::$fields_options),
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $product = self::get_product($post_id);

                    if (is_object($product)) {
                        if (is_a($product, 'WC_Product_Variation')) {
                            $variation_attributes = [];
                            $attributes = $product->get_attributes();

                            if (!empty($attributes)) {
                                foreach ($attributes as $tax_key => $value) {
                                    $term = get_term_by('slug', $value, $tax_key);
                                    if ($term) {
                                        $variation_attributes[] = $term->name;
                                    }
                                }

                                if (!empty($variation_attributes)) {
                                    $variation_attributes = implode(', ', $variation_attributes);
                                    $variation_attributes = ' - ' . $variation_attributes;
                                } else {
                                    $variation_attributes = '';
                                }
                            }


                            return $product->get_title() . $variation_attributes;
                        } else {

                            $as_text = false;
                            $show_labels = false;

                            if ($table_id > 0) {
                                $options = woot()->get_field_options($table_id, 'post_title');
                                $as_text = isset($options['title_as_text']) ? intval($options['title_as_text']) : 0;
                                $show_labels = isset($options['show_labels']) ? boolval($options['show_labels']) : false;
                            } else {
                                $as_text = apply_filters('woot_disable_all_links_on_titles', false);
                            }

                            if (isset($shortcode_args['post_title_as_text'])) {
                                $as_text = intval($shortcode_args['post_title_as_text']);
                            }

                            //***
                            $labels = '';

                            if ($show_labels) {
                                $labels = '<div class="woot-profuct-tag-container">';

                                $featured_lang = WOOT_Vocabulary::get(esc_html__('featured', 'profit-products-tables-for-woocommerce'));
                                $on_sale_lang = WOOT_Vocabulary::get(esc_html__('on sale', 'profit-products-tables-for-woocommerce'));
                                $stock_out_lang = WOOT_Vocabulary::get(esc_html__('out of stock', 'profit-products-tables-for-woocommerce'));

                                if (in_array($post_id, wc_get_featured_product_ids())) {
                                    $labels .= "<span class='woot-profuct-tag woot-profuct-tag-featured'>{$featured_lang}</span>";
                                }

                                if ($product->is_on_sale()) {
                                    $labels .= "<span class='woot-profuct-tag woot-profuct-tag-sale'>{$on_sale_lang}</span>";
                                }

                                if (!$product->is_in_stock()) {
                                    $labels .= "<span class='woot-profuct-tag woot-profuct-tag-out'>{$stock_out_lang}</span>";
                                }


                                $labels .= '</div>';
                            }

                            //***

                            if ($as_text) {
                                return $labels . $product->get_title();
                            } else {
                                return $labels . WOOT_HELPER::draw_html_item('a', array(
                                            'href' => get_permalink($post_id),
                                            'title' => $product->get_title(),
                                            'target' => '_blank'
                                                ), $product->get_title());
                            }
                        }
                    }
                },
                'get_filter_query_args' => function($args, $value) use($shortcode_args) {

                    $value = trim(WOOT_HELPER::strtolower($value));

                    if (!empty($value)) {

                        add_filter('posts_where', function($where = '') use($args, $value) {
                                    $sql = "";

                                    if (isset($args['woot_text_search_by']) AND!empty($args['woot_text_search_by'])) {
                                        $sql = " AND (";
                                        foreach ($args['woot_text_search_by'] as $field) {
                                            $sql .= "LOWER({$field}) LIKE '%{$value}%' OR ";
                                        }
                                        $sql = trim($sql, ' OR ');
                                        $sql .= ")";
                                    }

                                    $where .= $sql;
                                    return $where;
                                }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'post_title');
                }
            ],
            'post_excerpt' => [
                'title' => esc_html__('Excerpt', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function($post_id) {
                    return WOOT_HELPER::wrap_text_to_container(strip_tags(apply_filters('the_content', get_the_excerpt($post_id)), self::$allowed_tags), get_post_field('post_title', $post_id));
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = trim($value);

                    if (!empty($value)) {
                        add_filter('posts_where', function($where = '') use($value) {
                                    $value = trim(WOOT_HELPER::strtolower($value));
                                    $where .= "  AND LOWER(post_excerpt) LIKE '%{$value}%'";
                                    return $where;
                                }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'post_excerpt');
                }
            ],
            'post_content' => [
                'title' => esc_html__('Content', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function($post_id) {
                    return WOOT_HELPER::wrap_text_to_container(strip_tags(apply_filters('the_content', get_the_content(null, false, $post_id)), self::$allowed_tags), get_post_field('post_title', $post_id));
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = trim($value);

                    if (!empty($value)) {
                        add_filter('posts_where', function($where = '') use($value) {
                                    $value = trim(WOOT_HELPER::strtolower($value));
                                    $where .= "  AND LOWER(post_content) LIKE '%{$value}%'";
                                    return $where;
                                }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'post_content');
                }
            ],
            'single' => [
                'title' => esc_html__('Single', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $title = esc_html__('Single product data', 'profit-products-tables-for-woocommerce') . ': ';
                    $title .= addslashes(get_post_field('post_title', $post_id));
                    $unique_id = uniqid('gp');

                    $skin = '';
                    if (isset($table_id) AND $table_id > 0) {
                        $skin = woot()->skins->get($table_id);
                    }

                    if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                        $skin = $shortcode_args['skin'];
                    }

                    return WOOT_HELPER::draw_html_item('a', array(
                                'href' => '#',
                                'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_get_table_single_product\",{product_id: {$post_id}, skin: skin, not_paste:1}, \"{$unique_id}\", \"{$title}\");",
                                'title' => $title,
                                'class' => 'woot-btn woot-btn-1'
                                    ), apply_filters('woot_single_btn_text', '<i class="woot-icon">&#xf1c6;</i>'));
                }
            ],
            'post_status' => [
                'title' => esc_html__('Status', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'action' => function($post_id) {
                    if (intval($post_id) > 0) {
                        return get_post_statuses()[get_post_field('post_status', $post_id)];
                    }
                }
            ],
            'post_author' => [
                'title' => esc_html__('Author', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'filter' => true,
                'filter_view' => 'select',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        $user_nicename = get_the_author_meta('user_nicename', $post->post_author);
                        return WOOT_HELPER::draw_html_item('a', [
                                    'href' => get_author_posts_url($post->post_author, $user_nicename),
                                    'target' => '_blank'
                                        ], $user_nicename);
                    }
                },
                'get_filter_query_args' => function($args, $value) {

                    if ($value) {
                        $args['author'] = $value[0];
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    global $wpdb;
                    $options = [];
                    $sql = "SELECT DISTINCT(post_author) FROM `{$wpdb->prefix}posts` WHERE post_type='product'";
                    $res = $wpdb->get_results($sql, ARRAY_A);

                    if (!empty($res)) {
                        foreach ($res as $r) {
                            $options[] = [
                                'id' => $r['post_author'],
                                'title' => get_user_by('id', $r['post_author'])->display_name
                            ];
                        }
                    }

                    $title = esc_html__('Author', 'profit-products-tables-for-woocommerce');
                    $tmp = woot()->filter->get_field_drawing_data($table_id, 'post_author');
                    if (isset($tmp['title']) AND!empty($tmp['title'])) {
                        $title = $tmp['title'];
                    }

                    return [
                        'title' => $title,
                        'view' => 'select',
                        'options' => $options
                    ];
                }
            ],
            'post_date' => [
                'title' => esc_html__('Post date', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'calendar',
                'filter_options' => ['placeholder'],
                'action' => function($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        return date(apply_filters('woot_date_format', get_option('date_format')), strtotime($post->post_date));
                    }
                },
                'get_filter_query_args' => function($args, $value, $is_calendar_dir_to = false) {

                    add_filter('posts_where', function($where = '') use($value, $is_calendar_dir_to) {
                                $value = date('Y-m-d H:i:s', $value);
                                if ($is_calendar_dir_to) {
                                    $where .= "  AND post_date <= '{$value}'";
                                } else {
                                    $where .= "  AND post_date >= '{$value}'";
                                }

                                return $where;
                            }, 101);


                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'post_date');
                }
            ],
            'post_modified' => [
                'title' => esc_html__('Post modified', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'calendar',
                'filter_options' => ['placeholder'],
                'action' => function($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        return date(apply_filters('woot_date_format', get_option('date_format')), strtotime($post->post_modified));
                    }
                },
                'get_filter_query_args' => function($args, $value, $is_calendar_dir_to = false) {


                    add_filter('posts_where', function($where = '') use($value, $is_calendar_dir_to) {
                                $value = date('Y-m-d H:i:s', $value);

                                if ($is_calendar_dir_to) {
                                    $where .= "  AND post_modified <= '{$value}'";
                                } else {
                                    $where .= "  AND post_modified >= '{$value}'";
                                }

                                return $where;
                            }, 101);


                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'post_modified');
                }
            ],
            'comment_count' => [
                'title' => esc_html__('Comment count', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . get_comments_number($post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = explode(':', trim($value));

                    if (!empty($value) AND is_array($value)) {
                        add_filter('posts_where', function($where = '') use($value) {
                                    $where .= "  AND (comment_count >= {$value[0]} AND comment_count <= {$value[1]})";
                                    return $where;
                                }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    $res = woot()->filter->get_field_drawing_data($table_id, 'comment_count');

                    global $wpdb;
                    $sql = "SELECT MIN(comment_count) AS min FROM `{$wpdb->prefix}posts` WHERE post_type='product'";
                    $r = $wpdb->get_results($sql, ARRAY_A);
                    $res['min'] = $r[0]['min'];
                    $sql = "SELECT MAX(comment_count) AS max FROM `{$wpdb->prefix}posts` WHERE post_type='product'";
                    $r = $wpdb->get_results($sql, ARRAY_A);
                    $res['max'] = $r[0]['max'];

                    return $res;
                }
            ],
            'price' => [
                'title' => esc_html__('Price', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_price',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return $this->get_field_data('price', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_price',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'price');
                }
            ],
            'regular_price' => [
                'title' => esc_html__('Regular Price', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_regular_price',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return $this->get_field_data('regular_price', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_regular_price',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'regular_price');
                }
            ],
            'sale_price' => [
                'title' => esc_html__('Sale Price', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_sale_price',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return $this->get_field_data('sale_price', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_sale_price',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'sale_price');
                }
            ],
            'on_sale' => [
                'title' => esc_html__('On Sale', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'filter' => true,
                'filter_view' => 'switcher',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    $product = WOOT_WooCommerce::get_product($post_id);
                    if (is_object($product)) {
                        return $product->is_on_sale() ? "<span class='woot-html-check'><i class='woot-icon'>&#xe804;</i></span>" : "<span class='woot-html-cross'><i class='woot-icon'>&#xe805;</i></span>";
                    }
                },
                'get_filter_query_args' => function($args, $value) {

                    //on sale only
                    if (intval($value)) {

                        if (!isset($args['post__in'])) {
                            $args['post__in'] = [];
                        }

                        if (!empty($args['post__in'])) {
                            $args['post__in'] = array_intersect($args['post__in'], wc_get_product_ids_on_sale() ? wc_get_product_ids_on_sale() : [0]);
                        } else {
                            $args['post__in'] = wc_get_product_ids_on_sale();
                        }
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'on_sale');
                }
            ],
            'sku' => [
                'title' => esc_html__('SKU', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value',
                'meta_key' => '_sku',
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'width'],
                'action' => function($post_id) {
                    return $this->get_field_data('sku', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {

                    $args['meta_query'][] = array(
                        'key' => '_sku',
                        'value' => trim($value),
                        'compare' => 'LIKE'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'sku');
                }
            ],
            'downloadable' => [
                'title' => esc_html__('Downloadable', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value',
                'meta_key' => '_downloadable',
                'filter' => true,
                'filter_view' => 'switcher',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    return $this->get_field_data('downloadable', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {

                    if ($value) {
                        $args['meta_query'][] = array(
                            'key' => '_downloadable',
                            'value' => 'yes',
                            'compare' => '='
                        );
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'downloadable');
                }
            ],
            'gallery' => [
                'title' => esc_html__('Gallery', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) {
                    $product = self::get_product($post_id);

                    if (is_object($product)) {
                        $images_ids = $product->get_gallery_image_ids();

                        $img_data = [];

                        foreach ($images_ids as $attachment_id) {
                            $img = wp_get_attachment_image_src($attachment_id);
                            if (isset($img[0])) {
                                $img_data[] = [
                                    'thumb' => $img[0],
                                    'original' => wp_get_attachment_image_src($attachment_id, 'full')[0],
                                    'title' => $product->get_title()
                                ];
                            }
                        }

                        if (!empty($img_data)) {
                            return WOOT_HELPER::render_html('views/gallery.php', apply_filters('woot_print_plugin_options', ['img_data' => $img_data]));
                        } else {
                            return '';
                        }
                    }
                }
            ],
            'weight' => [
                'title' => esc_html__('Weight', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_weight',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('weight', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_weight',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'weight');
                }
            ],
            'length' => [
                'title' => esc_html__('Length', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_length',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('length', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_length',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'length');
                }
            ],
            'height' => [
                'title' => esc_html__('Height', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_height',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('height', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_height',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'height');
                }
            ],
            'width' => [
                'title' => esc_html__('Width', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_width',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('width', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {
                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_width',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'width');
                }
            ],
            'manage_stock' => [
                'title' => esc_html__('Manage stock', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value',
                'meta_key' => '_manage_stock',
                'action' => function($post_id) {
                    return $this->get_field_data('manage_stock', $post_id);
                }
            ],
            'stock_status' => [
                'title' => esc_html__('Stock status', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value',
                'meta_key' => '_stock_status',
                'filter' => true,
                'filter_view' => 'switcher',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    $selected_status = $this->get_field_data('stock_status', $post_id);
                    //$status = wc_get_product_stock_status_options()[$selected_status];

                    $mark = "<span class='woot-html-cross'><i class='woot-icon'>&#xe805;</i></span>";

                    if (in_array($selected_status, ['instock', 'onbackorder'])) {
                        $mark = "<span class='woot-html-check'><i class='woot-icon'>&#xe804;</i></span>";
                    }

                    return $mark;
                },
                'get_filter_query_args' => function($args, $value) {

                    //in stock only
                    if (intval($value)) {
                        $args['meta_query'][] = array(
                            'key' => '_stock_status',
                            'value' => 'outofstock',
                            'compare' => 'NOT LIKE'
                        );
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'stock_status');
                }
            ],
            'stock_quantity' => [
                'title' => esc_html__('Stock quantity', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_stock',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('stock_quantity', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_stock',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'stock_quantity');
                }
            ],
            'sold_individually' => [
                'title' => esc_html__('Sold individually', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value',
                'meta_key' => '_sold_individually',
                'filter' => true,
                'filter_view' => 'switcher',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    return $this->get_field_data('sold_individually', $post_id);
                },
                'get_filter_query_args' => function($args, $value) {

                    if ($value) {
                        $args['meta_query'][] = array(
                            'key' => '_sold_individually',
                            'value' => 'yes',
                            'compare' => '='
                        );
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'sold_individually');
                }
            ],
            'total_sales' => [
                'title' => esc_html__('Total sales', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => 'total_sales',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('total_sales', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => 'total_sales',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'total_sales');
                }
            ],
            'review_count' => [
                'title' => esc_html__('Reviews count', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_wc_review_count',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'min', 'max', 'width'],
                'action' => function($post_id) {
                    return '<span class="woot-num-cell">' . $this->get_field_data('review_count', $post_id) . '</span>';
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => '_wc_review_count',
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'review_count');
                }
            ],
            'tax_status' => [
                'title' => esc_html__('Tax status', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'action' => function($post_id) {
                    return WOOT_Vocabulary::get(trim($this->get_field_data('tax_status', $post_id)));
                }
            ],
            'average_rating' => [
                'title' => esc_html__('Rating', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'order' => 'asc',
                'orderby' => 'meta_value_num',
                'meta_key' => '_wc_average_rating',
                'filter' => true,
                //'filter_options' => ['title'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $product = self::get_product($post_id);
                    if (is_object($product)) {
                        return WOOT_HELPER::render_html('profiles/woocommerce/views/reviews.php', [
                                    'product_id' => $post_id,
                                    'average_rating' => $this->get_field_data('average_rating', $post_id),
                                    'reviews_count' => $this->get_field_data('review_count', $post_id),
                                    'table_id' => $table_id,
                                    'shortcode_args' => $shortcode_args,
                                    'title' => $product->get_title()
                        ]);
                    }
                },
                'get_filter_query_args' => function($args, $value) {

                    $value = intval($value[0]);
                    $args['meta_query'][] = array(
                        'key' => '_wc_average_rating',
                        'value' => array($value, $value + 1),
                        'type' => 'DECIMAL(10,2)',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return [
                        'title' => WOOT_Vocabulary::get(esc_html__('Rating', 'profit-products-tables-for-woocommerce')),
                        'view' => 'select',
                        'options' => [
                            [
                                'id' => 1,
                                'title' => WOOT_Vocabulary::get(esc_html__('from 1 to 2', 'profit-products-tables-for-woocommerce')),
                            ],
                            [
                                'id' => 2,
                                'title' => WOOT_Vocabulary::get(esc_html__('from 2 to 3', 'profit-products-tables-for-woocommerce')),
                            ],
                            [
                                'id' => 3,
                                'title' => WOOT_Vocabulary::get(esc_html__('from 3 to 4', 'profit-products-tables-for-woocommerce')),
                            ],
                            [
                                'id' => 4,
                                'title' => WOOT_Vocabulary::get(esc_html__('from 4 to 5', 'profit-products-tables-for-woocommerce')),
                            ],
                            [
                                'id' => 5,
                                'title' => WOOT_Vocabulary::get(esc_html__('exactly 5', 'profit-products-tables-for-woocommerce')),
                            ]
                        ]
                    ];
                }
            ],
            'grouped' => [
                'title' => esc_html__('Grouped', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $product = self::get_product($post_id);

                    if (is_object($product)) {
                        if ($product->is_type('grouped') AND $product->is_in_stock()) {
                            $count = count($product->get_children());
                            if ($count) {
                                $unique_id = uniqid('gp');
                                $title = addslashes(sprintf("%s - Grouped", "#{$post_id}. {$product->get_title()}"));

                                $skin = '';
                                if (isset($table_id) AND $table_id > 0) {
                                    $skin = woot()->skins->get($table_id);
                                }

                                if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                                    $skin = $shortcode_args['skin'];
                                }


                                return WOOT_HELPER::draw_html_item('a', array(
                                            'href' => '#',
                                            'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_woocommerce_get_grouped_table\",{product_id: {$product->get_id()}, skin: skin, not_paste:1},\"{$unique_id}\", \"{$title}\");",
                                            'title' => $product->get_title(),
                                            'class' => 'woot-btn',
                                                ), sprintf(WOOT_Vocabulary::get(esc_html__('Group[%s]', 'profit-products-tables-for-woocommerce')), $count));
                            } else {
                                return '-';
                            }
                        }
                    }

                    return '-';
                }
            ],
            'upsell' => [
                'title' => esc_html__('Upsells', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    //return $this->get_field_data('upsell', $post_id);
                    $product = self::get_product($post_id);
                    $unique_id = uniqid('upt');

                    if (is_object($product)) {
                        $title = addslashes(sprintf("%s - Upsells", "#{$post_id}. {$product->get_title()}"));

                        if (count($product->get_upsell_ids()) > 0) {

                            $skin = '';
                            if (isset($table_id) AND $table_id > 0) {
                                $skin = woot()->skins->get($table_id);
                            }

                            if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                                $skin = $shortcode_args['skin'];
                            }

                            return WOOT_HELPER::draw_html_item('a', array(
                                        'href' => "#",
                                        'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_woocommerce_get_upsells_table\",{product_id: {$product->get_id()}, skin: skin, not_paste:1},\"{$unique_id}\", \"{$title}\");",
                                        'title' => $product->get_title(),
                                        'class' => 'woot-btn'
                                            ), sprintf(WOOT_Vocabulary::get(esc_html__('Upsells[%s]', 'profit-products-tables-for-woocommerce')), count($product->get_upsell_ids())));
                        } else {
                            return '-';
                        }
                    }
                }
            ],
            'cross_sell' => [
                'title' => esc_html__('Cross sells', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    //return $this->get_field_data('cross_sell', $post_id);

                    $product = self::get_product($post_id);
                    $unique_id = uniqid('upt');

                    if (is_object($product)) {
                        $title = addslashes(sprintf("%s - Cross-sells", "#{$post_id}. {$product->get_title()}"));

                        if (count($product->get_cross_sell_ids()) > 0) {

                            $skin = '';
                            if (isset($table_id) AND $table_id > 0) {
                                $skin = woot()->skins->get($table_id);
                            }

                            if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                                $skin = $shortcode_args['skin'];
                            }

                            return WOOT_HELPER::draw_html_item('a', array(
                                        'href' => '#',
                                        'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_woocommerce_get_cross_sells_table\",{product_id: {$product->get_id()}, skin: skin, not_paste:1},\"{$unique_id}\", \"{$title}\");",
                                        'title' => $product->get_title(),
                                        'id' => 'call-btn-' . $unique_id,
                                        'class' => 'woot-btn'
                                            ), sprintf(WOOT_Vocabulary::get(esc_html__('Cross[%s]', 'profit-products-tables-for-woocommerce')), count($product->get_cross_sell_ids())));
                        } else {
                            return '-';
                        }
                    }
                }
            ],
            'variations' => [
                'title' => esc_html__('Variations', 'profit-products-tables-for-woocommerce'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function($post_id) use($table_id, $shortcode_args) {
                    $product = self::get_product($post_id);

                    if (is_object($product)) {
                        if ($product->is_type('variable')) {
                            $count = count($product->get_children());

                            if ($count > 0) {
                                $unique_id = uniqid('ap');
                                $title = addslashes(sprintf("%s - Variations", "#{$post_id}. {$product->get_title()}"));

                                $skin = '';
                                if (isset($table_id) AND $table_id > 0) {
                                    $skin = woot()->skins->get($table_id);
                                }

                                if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                                    $skin = $shortcode_args['skin'];
                                }

                                return WOOT_HELPER::draw_html_item('a', array(
                                            'href' => '#',
                                            'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_woocommerce_get_variations_table\",{product_id: {$product->get_id()}, skin: skin, not_paste:1},\"{$unique_id}\", \"{$title}\");",
                                            'title' => $product->get_title(),
                                            'class' => 'woot-btn'
                                                ), sprintf(WOOT_Vocabulary::get(esc_html__('Var.[%s]', 'profit-products-tables-for-woocommerce')), $count));
                            } else {
                                return '-';
                            }
                        } else {
                            return '-';
                        }
                    }
                }
            ],
            'product_type' => [
                'title' => esc_html__('Type', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'action' => function($post_id) {
                    $product_type = self::get_product_type($post_id);
                    return trim(str_replace('product', '', wc_get_product_types()[$product_type]));
                }
            ],
            'featured' => [
                'title' => esc_html__('Featured', 'profit-products-tables-for-woocommerce'),
                'options' => WOOT_WooCommerce::$fields_options,
                'filter' => true,
                'filter_view' => 'switcher',
                'filter_options' => ['title'],
                'action' => function($post_id) {
                    $product = self::get_product($post_id);

                    if (is_object($product)) {
                        return $product->is_featured() ? "<span class='woot-html-check'><i class='woot-icon'>&#xe804;</i></span>" : "<span class='woot-html-cross'><i class='woot-icon'>&#xe805;</i></span>";
                    }
                },
                'get_filter_query_args' => function($args, $value) {

                    //featured only
                    if (intval($value)) {
                        if (!isset($args['post__in'])) {
                            $args['post__in'] = [];
                        }

                        if (!empty($args['post__in'])) {
                            $args['post__in'] = array_intersect($args['post__in'], wc_get_featured_product_ids() ? wc_get_featured_product_ids() : [0]);
                        } else {
                            $args['post__in'] = wc_get_featured_product_ids();
                        }
                    }

                    return $args;
                },
                'get_filter_draw_data' => function($table_id) {
                    return woot()->filter->get_field_drawing_data($table_id, 'featured');
                }
            ],
        ];

        //*** lets add woo taxonomies and attributes
        //get all products taxonomies
        $taxonomy_objects = get_object_taxonomies('product', 'objects');
        unset($taxonomy_objects['product_type']);
        unset($taxonomy_objects['product_visibility']);
        unset($taxonomy_objects['product_shipping_class']);

        if (isset($taxonomy_objects['translation_priority'])) {
            unset($taxonomy_objects['translation_priority']);
        }

        if (!empty($taxonomy_objects)) {

            foreach ($taxonomy_objects as $t) {

                $profile[$t->name] = [
                    'title' => $t->label,
                    'options' => array_merge(['display_as_text'], WOOT_WooCommerce::$fields_options),
                    'filter' => true,
                    'filter_view' => 'select',
                    'filter_options' => ['as-mselect', 'mselect-logic', 'exclude', 'include'],
                    'action' => function($post_id) use($t, $table_id) {
                        $res = '';
                        $product = self::get_product($post_id);

                        if (is_a($product, 'WC_Product_Variation')) {
                            $attributes = $product->get_attributes();

                            if (!empty($attributes) AND isset($attributes[$t->name])) {
                                $term = get_term_by('slug', $attributes[$t->name], $t->name);
                                $res = WOOT_HELPER::draw_html_item('a', array(
                                            'href' => get_term_link($term->term_id),
                                            'class' => 'woot-tax-term woot-' . $term->taxonomy . '-' . $term->slug,
                                            'target' => '_blank',
                                                ), WOOT_Vocabulary::get($term->name));
                            } else {
                                $res = esc_html__('attribute not exist', 'profit-products-tables-for-woocommerce');
                            }
                        } else {

                            $include_only = wp_get_post_terms($post_id, $t->name, ['fields' => 'ids']);

                            if (empty($include_only)) {
                                $include_only = [-1];
                            }

                            $options = woot()->filter->build_taxonomy_tree($t->name, woot()->filter->get_terms($t->name, 0), [], $include_only);

                            if (!empty($options)) {
                                $links = [];
                                $display_as_text = false;

                                if ($table_id > 0) {
                                    if ($tmp = woot()->columns->get_by_field_key($table_id, $t->name)) {
                                        $tmp = json_decode($tmp['options'], true);
                                        if (isset($tmp['display_as_text'])) {
                                            $display_as_text = boolval(intval($tmp['display_as_text']));
                                        }
                                    }
                                }

                                foreach ($options as $t) {
                                    if (!$display_as_text) {
                                        $links[] = WOOT_HELPER::draw_html_item('a', array(
                                                    'href' => get_term_link($t['id']),
                                                    'class' => 'woot-tax-term woot-' . $t['name'] . '-' . $t['slug'],
                                                    'target' => '_blank',
                                                        ), WOOT_Vocabulary::get($t['name']));
                                    } else {
                                        $links[] = WOOT_HELPER::draw_html_item('span', array(
                                                    'class' => 'woot-tax-term woot-' . $t['name'] . '-' . $t['slug']
                                                        ), WOOT_Vocabulary::get($t['name']));
                                    }
                                }

                                $res = implode(' ', $links);
                            }
                        }


                        return $res;
                    },
                    'get_filter_query_args' => function($args, $value) use($t, $table_id) {

                        global $wp_taxonomies;
                        if (in_array($t->name, array_keys($wp_taxonomies))) {

                            $logic = 'IN';

                            if ($table_id > 0) {
                                $logic = woot()->filter->fields_options->get($table_id, "{$t->name}-mselect-logic");
                                if (!in_array($logic, ['IN', 'NOT IN', 'AND'])) {
                                    $logic = 'IN';
                                }
                            }

                            $args['tax_query'][] = array(
                                'taxonomy' => $t->name,
                                'field' => 'term_id',
                                'terms' => (array) $value,
                                'operator' => $logic
                            );
                        }


                        return $args;
                    },
                    'get_filter_draw_data' => function($table_id) use($t) {
                        return woot()->filter->get_taxonomy_drawing_data($t->name, $table_id);
                    }
                ];
            }
        }


        //***

        $profile['add_to_cart'] = $this->get_add_to_cart_btn($table_id);

        //***

        if ($table_id > 0) {
            woot()->columns->meta->extend_profile_fields(self::$action, $table_id);
        }

        return apply_filters('woot_profile_extend', apply_filters('ext_' . self::$action, $profile, $table_id, $shortcode_args), self::$action, $shortcode_args);
    }

    /**
     * Initialization "Add to cart" button
     *
     * @since 1.0.0
     * @param int $table_id table ID
     *
     * @return array one add_to_cart field
     */
    private function get_add_to_cart_btn($table_id = 0) {
        return [
            'title' => esc_html__('Cart', 'profit-products-tables-for-woocommerce'),
            'order' => FALSE,
            'action' => function($post_id) use($table_id) {
                $product = self::get_product($post_id);
                if (is_object($product)) {
                    return WOOT_HELPER::render_html('profiles/woocommerce/views/add_to_cart.php', [
                                'intstant_redirect' => woot()->columns->options->get($table_id, 'add_to_cart_instant_redirect', 0),
                                //'table_id' => $table_id,
                                'product_id' => $post_id,
                                'product_type' => $this->get_product_type($post_id),
                                'product' => $product
                    ]);
                }
            }
        ];
    }

    /**
     * Get woocommerce product field data
     *
     * @since 1.0.0
     * @param string $field field key
     * @param int $product_id product ID
     * @return string value
     */
    public function get_field_data($field, $product_id) {
        $product = self::get_product($product_id);
        $value = '';

        if (is_object($product)) {
            switch ($field) {
                case 'price':
                    //props
                    $func_name = 'get_' . $field;

                    if ($product->get_type() === 'variable') {
                        $min_value = $product->get_variation_price('min', FALSE);
                        $max_value = $product->get_variation_price('max', FALSE);

                        $value = WOOT_WooCommerce::wc_price($min_value) . ' - ' . WOOT_WooCommerce::wc_price($max_value);
                    } else {

                        if ($sale_price = $product->get_sale_price()) {
                            $value = '<del>' . WOOT_WooCommerce::wc_price($product->get_regular_price()) . '</del> <ins>' . WOOT_WooCommerce::wc_price($sale_price) . '</ins>';
                        } else {
                            $value = WOOT_WooCommerce::wc_price(floatval($product->$func_name()));
                        }
                    }

                    break;

                case 'regular_price':
                    //props
                    $func_name = 'get_' . $field;

                    if ($product->get_type() === 'variable') {
                        $min_value = $product->get_variation_regular_price('min', FALSE);
                        $max_value = $product->get_variation_regular_price('max', FALSE);

                        $value = WOOT_WooCommerce::wc_price($min_value) . ' - ' . WOOT_WooCommerce::wc_price($max_value);
                    } else {
                        $value = WOOT_WooCommerce::wc_price(floatval($product->$func_name()));
                    }

                    break;

                case 'sale_price':
                    //props
                    $func_name = 'get_' . $field;

                    if ($product->get_type() === 'variable') {
                        $min_value = $product->get_variation_sale_price('min', FALSE);
                        $max_value = $product->get_variation_sale_price('max', FALSE);

                        $value = WOOT_WooCommerce::wc_price($min_value) . ' - ' . WOOT_WooCommerce::wc_price($max_value);
                    } else {
                        $value = WOOT_WooCommerce::wc_price(floatval($product->$func_name()));
                    }

                    break;

                case 'sku':

                case 'downloadable':
                case 'manage_stock':
                case 'stock_status':
                case 'sold_individually':
                    //props
                    $func_name = 'get_' . $field;
                    if (method_exists($product, $func_name)) {
                        $t = $product->$func_name();
                        switch ($t) {
                            case 'TRUE':
                                $value = "<span class='woot-html-check'><i class='woot-icon'>&#xe804;</i></span>";
                                break;
                            case FALSE:
                                $value = "<span class='woot-html-cross'><i class='woot-icon'>&#xe805;</i></span>";
                                break;
                            default:
                                $value = $t;
                                break;
                        }
                    }

                    break;

                case 'stock_quantity':
                case 'weight':
                case 'length':
                case 'height':
                case 'width':
                case 'total_sales':
                case 'review_count':
                case 'average_rating':
                case 'tax_status':
                    //props
                    $func_name = 'get_' . $field;
                    if (method_exists($product, $func_name)) {
                        $t = $product->$func_name();
                        switch ($t) {
                            case FALSE:
                                $value = '-';
                                break;
                            default:
                                $value = $t;
                                break;
                        }
                    }

                    break;

                case 'upsell':
                case 'cross_sell':
                    $func_name = "get_{$field}_ids";
                    $product = self::get_product($product_id);

                    if (is_object($product)) {
                        $ids = $product->$func_name();
                        if (!empty($ids)) {
                            $value = [];
                            foreach ($ids as $pid) {
                                $p = self::get_product($pid);
                                $value[] = WOOT_HELPER::draw_html_item('a', [
                                            'href' => $p->get_permalink(),
                                            'target' => '_blank'
                                                ], $p->get_title());
                            }

                            $value = implode(', ', $value);
                        }
                    }

                    break;
            }
        }

        return $value;
    }

    /**
     * Product type
     *
     * @since 1.0.0
     * @param int $product_id product ID
     * @return string product type
     */
    private function get_product_type($product_id) {
        $product = self::get_product($product_id);

        if (is_object($product)) {
            $terms = get_the_terms($product->get_id(), 'product_type');
            return (!empty($terms)) ? sanitize_title(current($terms)->name) : 'simple';
        }

        return 'simple';
    }

    /**
     * Products IDs added to the site shop cart
     *
     * @since 1.0.0
     *
     * @return array products IDs
     */
    public function get_ids_in_cart() {
        global $woocommerce;
        $ids = [];
        foreach ($woocommerce->cart->get_cart() as $values) {
            $ids[] = $values['data']->get_id();
        }

        return $ids;
    }

    /**
     * Table $table_id fields for "order-by" drop down selected in its options
     *
     * @since 1.0.0
     * @param string $fields_keys fields keys
     * @param int $table_id table ID
     * @return array gields
     */
    public static function get_select_orderby_options($fields_keys = '', $table_id = 0) {
        if (!empty($fields_keys)) {
            $fields_keys = explode(',', $fields_keys);
            $fields = apply_filters('woot_table_orderby_select_args', self::$orderby_select_fields, $table_id);
            $res = [];
            foreach ($fields_keys as $key) {
                if (isset($fields[$key])) {
                    $res[$key] = $fields[$key];
                }
            }

            return $res;
        }

        return [];
    }

    /**
     * Add columns to the plugged-in extensions
     *
     * @since 1.0.0
     * @param array $profile fields data
     * @param array $selected_columns_keys selected fields
     * @return array fields
     */
    public function extend_ext_profiles($profile, $selected_columns_keys) {
        if ($_REQUEST['post_type'] === 'product') {

            $woo_profile = $this->action();

            if (!empty($selected_columns_keys)) {
                foreach ($selected_columns_keys as $key) {
                    if (in_array($key, ['actions'])) {
                        continue;
                    }

                    //***

                    if (isset($woo_profile[$key])) {
                        $profile[$key] = $woo_profile[$key];
                    } else {

                        if (isset(WOOT::$synonyms[$key])) {
                            $profile[$key] = $woo_profile[WOOT::$synonyms[$key]];
                        } else {
                            $profile[$key] = [
                                'title' => esc_html__('not exists', 'profit-products-tables-for-woocommerce'),
                                'action' => function($post_id) {
                                    return esc_html__('not exists', 'profit-products-tables-for-woocommerce');
                                }
                            ];
                        }
                    }
                }
            }

            return $profile;
        }

        return $profile;
    }

    /**
     * Prepare wc price HTML for using in WOOT tables
     *
     * @since 1.0.0
     * @param float $price price
     * @return string woocommerce price
     */
    public static function wc_price($price) {
        $price = wc_price($price);
        $price = str_replace('class', 'data-class', $price);
        return $price;
    }

}

//+++

new WOOT_WooCommerce();


