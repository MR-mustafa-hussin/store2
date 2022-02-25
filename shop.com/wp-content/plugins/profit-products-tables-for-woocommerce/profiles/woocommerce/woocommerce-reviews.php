<?php

/**
 * Shortcode [woot_reviews]
 *
 * Generates a product reviews HTML table
 *
 * @see https://products-tables.com/shortcode/woot_reviews/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOT_WooCommerceReviews {

    private $action = 'woot_woocommerce_reviews';
    public $default_columns = 'users,comment_content,stars,comment_date';

    public function __construct() {
        //profile
        add_action($this->action, function ($table_id = 0, $shortcode_args = []) {

            //$current_action = current_action();

            $profile = [
                0 => [],
                'users' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('User', 'profit-products-tables-for-woocommerce'))
                ],
                'comment_content' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Comment', 'profit-products-tables-for-woocommerce'))
                ],
                'stars' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Rating', 'profit-products-tables-for-woocommerce')),
                    'order' => 'desc'
                ],
                'comment_date' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Date', 'profit-products-tables-for-woocommerce')),
                    'order' => 'desc'
                ],
            ];

            return apply_filters("ext_{$this->action}", $profile);
        }, 1, 2);

        //***

        add_filter('woot_get_woocommerce_reviews_table', function($what, $table_html_id) {
            return $this->draw_table(intval($what['product_id']), $table_html_id, ['skin' => $what['skin']]);
        }, 10, 2);

        //***

        add_shortcode('woot_reviews', function($args) {
            woot()->include_assets();

            $args = (array) $args;
            $product_id = 0;

            if (isset($args['id'])) {
                $product_id = intval($args['id']);
                unset($args['id']);
            } else {
                global $product;
                if (is_object($product)) {
                    $product_id = $product->get_id();
                }
            }

            //***

            if ($product_id > 0 AND get_post_field('post_type', $product_id) === 'product') {
                return '<div class="woot-reviews-shortcode">' . $this->draw_table($product_id, uniqid('t'), $args) . '</div>';
            }
        });
    }

    /**
     * Get table HTML
     *
     * @since 1.0.0
     * @param int $product_id product ID
     * @param string $table_html_id table HTML id
     * @param array $args shortcode arguments
     * @return string table HTML
     */
    private function draw_table($product_id, $table_html_id, $args = []) {
        $args = (array) $args;
        $per_page = 10;
        if (isset($args['per_page'])) {
            $per_page = intval($args['per_page']);
        }

        return WOOT_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $table_html_id,
                    'hide_text_search' => true,
                    'classes' => 'woot-data-table-self-call',
                    'style' => isset($args['skin']) ? woot()->skins->get_theme_css($args['skin'], $table_html_id) : '',
                    'skin' => isset($args['skin']) ? $args['skin'] : '',
                    'table_view' => isset($args['table_view']) ? $args['table_view'] : ''
                )) . woot()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'columns' => $this->default_columns,
                    'orderby' => 'comment_date',
                    'order' => 'desc',
                    'per_page' => $per_page,
                    'per_page_position' => 't',
                    'per_page_sel_position' => 'none',
                    'table_data' => $this->get_table_data($product_id),
                    'use_flow_header' => 0
                        ], $table_html_id);
    }

    /**
     * Get table data
     *
     * @since 1.0.0
     * @param int $product_id product ID
     * @return array table data
     */
    private function get_table_data($product_id) {
        if ($product_id > 0) {
            $query = new WP_Comment_Query([
                'post_id' => $product_id,
                'comment_type' => 'review',
                'orderby' => 'comment_date',
                'order' => 'desc'
            ]);

            $fields = array_keys(apply_filters($this->action, NULL));

            //***
            $comments = [];

            if (!empty($fields) AND!empty($query)) {
                foreach ($query->comments as $comment) {
                    $tmp = [];
                    $tmp['pid'] = $comment->comment_ID;
                    foreach ($fields as $field) {
                        switch ($field) {
                            case 'comment_content':
                                $h = WOOT_HELPER::render_html('profiles/woocommerce/views/reviews.php', [
                                            'product_id' => $product_id,
                                            'average_rating' => get_comment_meta($comment->comment_ID, 'rating', true),
                                            'reviews_count' => WOOT_WooCommerce::get_product($product_id)->get_review_count(),
                                            'title' => ''
                                ]);

                                $tmp[$field] = WOOT_HELPER::wrap_text_to_container($comment->comment_content, $h);
                                break;
                            case 'comment_date':
                                $tmp[$field] = date_i18n(get_option('date_format'), strtotime($comment->comment_date));
                                break;
                            case 'stars':
                                $tmp[$field] = WOOT_HELPER::render_html('profiles/woocommerce/views/reviews.php', [
                                            'product_id' => $product_id,
                                            'average_rating' => get_comment_meta($comment->comment_ID, 'rating', true),
                                            'reviews_count' => WOOT_WooCommerce::get_product($product_id)->get_review_count(),
                                            'title' => ''
                                ]);
                                break;

                            case 'users':
                                $tmp[$field] = $comment->comment_author;
                                break;
                        }
                    }

                    $comments[] = $tmp;
                }
            }

            return ['rows' => $comments, 'count' => count($comments)];
        }

        return [];
    }

}

new WOOT_WooCommerceReviews();
