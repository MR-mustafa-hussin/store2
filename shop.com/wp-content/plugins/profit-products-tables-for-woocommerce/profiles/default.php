<?php

/**
 * Profile for not woocommerce fields
 *
 * Generates and handles fields of the tables
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

woot_register_profile('woot_default_tables', esc_html__('Default', 'profit-products-tables-for-woocommerce'));

//26-02-2020
add_action('woot_default_tables', function ($table_id = 0, $shortcode_args = []) {

    $current_action = current_action();
    extract(woot_profiles_data_processor($table_id, $shortcode_args, $current_action)); //$post_type, $post_statuses

    $profile = [
        0 => [
            'post_type' => $post_type,
            //false - in options we can select post type for this profile, true - not
            'post_type_fixed' => FALSE,
            'post_statuses' => $post_statuses
        ],
        'id' => [
            'title' => 'ID',
            'order' => 'desc'
        ],
        'thumbnail' => [
            'title' => esc_html__('Thumbnail', 'profit-products-tables-for-woocommerce'),
            'order' => FALSE
        ],
        'post_title' => [
            'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce'),
            'order' => 'asc'
        ],
        'post_excerpt' => [
            'title' => esc_html__('Excerpt', 'profit-products-tables-for-woocommerce'),
            'action' => function($post_id) {
                $title = esc_html__('Excerpt', 'profit-products-tables-for-woocommerce') . ': ';
                $title .= get_post_field('post_title', $post_id);
                return WOOT_HELPER::draw_html_item('a', [
                            'href' => "javascript: new Popup23({title: \"{$title}\",post_id:$post_id, what: \"excerpt\"}); void(0);"
                                ], esc_html__('See Excerpt', 'profit-products-tables-for-woocommerce'));
            }
        ],
        'post_content' => [
            'title' => esc_html__('Content', 'profit-products-tables-for-woocommerce'),
            'action' => function($post_id) {
                $title = esc_html__('Content', 'profit-products-tables-for-woocommerce') . ': ';
                $title .= get_post_field('post_title', $post_id);
                return WOOT_HELPER::draw_html_item('a', [
                            'href' => "javascript: new Popup23({title: \"{$title}\",post_id:$post_id, what: \"content\"}); void(0);"
                                ], esc_html__('See Content', 'profit-products-tables-for-woocommerce'));
            }
        ],
        'post_status' => [
            'title' => esc_html__('Status', 'profit-products-tables-for-woocommerce'),
            'order' => 'asc'
        ],
        'post_author' => [
            'title' => esc_html__('Author', 'profit-products-tables-for-woocommerce')
        ],
        'post_date' => [
            'title' => esc_html__('Date', 'profit-products-tables-for-woocommerce'),
            'order' => 'desc'
        ],
        'post_modified' => [
            'title' => esc_html__('Modified', 'profit-products-tables-for-woocommerce'),
            'order' => 'desc'
        ],
        'comment_count' => [
            'title' => esc_html__('Comment count', 'profit-products-tables-for-woocommerce'),
            'order' => 'desc',
            'action' => function($post_id) {
                return get_comments_number($post_id);
            }
        ],
    ];

    return apply_filters('woot_profile_extend', apply_filters("ext_{$current_action}", $profile), $current_action, $shortcode_args);
}, 1, 2);
