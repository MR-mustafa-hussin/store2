<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function woobe_get_total_settings($data) {
    return array(
        'per_page' => array(
            'title' => esc_html__('Default products count per page', 'woo-bulk-editor'),
            'desc' => sprintf(esc_html__('How many rows of products show per page in tab Products. Max possible value is 100! To set more - read %s please', 'woo-bulk-editor'), WOOBE_HELPER::draw_link([
                        'href' => 'https://bulk-editor.com/howto/set-more-rows-of-products-per-page/',
                        'title' => esc_html__('this article', 'woo-bulk-editor'),
                        'target' => '_blank'
                    ])),
            'value' => '',
            'type' => 'number'
        ),
        'default_sort_by' => array(
            'title' => esc_html__('Default sort by', 'woo-bulk-editor'),
            'desc' => esc_html__('Select column by which products sorting is going after plugin page loaded', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => $data['default_sort_by']
        ),
        'default_sort' => array(
            'title' => esc_html__('Default sort', 'woo-bulk-editor'),
            'desc' => esc_html__('Select sort direction for Default sort by column above', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                'desc' => array('title' => 'DESC'),
                'asc' => array('title' => 'ASC')
            )
        ),
        'show_admin_bar_menu_btn' => array(
            'title' => esc_html__('Show button in admin bar', 'woo-bulk-editor'),
            'desc' => esc_html__('Show Bulk Editor button in admin bar for quick access to the products editor', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                1 => array('title' => esc_html__('Yes', 'woo-bulk-editor')),
                0 => array('title' => esc_html__('No', 'woo-bulk-editor')),
            )
        ),
        'show_thumbnail_preview' => array(
            'title' => esc_html__('Show thumbnail preview', 'woo-bulk-editor'),
            'desc' => esc_html__('Show bigger thumbnail preview on mouse over', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                1 => array('title' => esc_html__('Yes', 'woo-bulk-editor')),
                0 => array('title' => esc_html__('No', 'woo-bulk-editor')),
            )
        ),
        'load_switchers' => array(
            'title' => esc_html__('Load beauty switchers', 'woo-bulk-editor'),
            'desc' => esc_html__('Load beauty switchers instead of checkboxes in the products table.', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                1 => array('title' => esc_html__('Yes', 'woo-bulk-editor')),
                0 => array('title' => esc_html__('No', 'woo-bulk-editor')),
            )
        ),
        'autocomplete_max_elem_count' => array(
            'title' => esc_html__('Autocomplete max count', 'woo-bulk-editor'),
            'desc' => esc_html__('How many products display in the autocomplete drop-downs. Uses in up-sells, cross-sells and grouped popups.', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'number'
        ),
        'quick_search_fieds' => array(
            'title' => esc_html__('Add fields to the quick search', 'woo-bulk-editor'),
            'desc' => esc_html__('Adds more fields to quick search fields drop-down on the tools panel. Works only for text fields. Syntax: post_name:Product slug,post_content: Content', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'text'
        ),
        'sync_profiles' => array(
            'title' => esc_html__('Synchronize managers profiles', 'woo-bulk-editor'),
            'desc' => esc_html__('The profiles will be the same for all managers. Except the administrator.', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                1 => array('title' => esc_html__('Yes', 'woo-bulk-editor')),
                0 => array('title' => esc_html__('No', 'woo-bulk-editor'))
            )
        ),
        'show_text_editor' => array(
            'title' => esc_html__('Show content and excerpt text partly', 'woo-bulk-editor'),
            'desc' => esc_html__('Show part of the text in the buttons of the content and excerpt', 'woo-bulk-editor'),
            'value' => '',
            'type' => 'select',
            'select_options' => array(
                1 => array('title' => esc_html__('Yes', 'woo-bulk-editor')),
                0 => array('title' => esc_html__('No', 'woo-bulk-editor'))
            )
        ),
    );
}
