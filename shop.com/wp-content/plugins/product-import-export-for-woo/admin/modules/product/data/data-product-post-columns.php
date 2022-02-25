<?php

if (!defined('WPINC')) {
    exit;
}

if (function_exists('wc_get_filename_from_url')) {
    $file_path_header = 'downloadable_files';
} else {
    $file_path_header = 'file_paths';
}


$post_columns = array(
    'post_title' => 'Product name',
    'post_name' => 'Product slug',
    'post_parent' => 'Parent ID',
    'ID' => 'ID',
    'post_content' => 'Description',
    'post_excerpt' => 'Short description',    
    'post_status' => 'Status',
    'post_password' => 'Post password',
    'menu_order' => 'Menu order',
    'post_date' => 'Post date',
    'post_author' => 'Post author',
    'comment_status' => 'Comment status',
    // Meta

    'sku' => 'SKU',
    'parent_sku' => 'Parent SKU',
    'children' => 'Children', //For Grouped products
    'downloadable' => 'Downloadable',
    'virtual' => 'Virtual',
    'stock' => 'Stock',
    'regular_price' => 'Regular price',
    'sale_price' => 'Sale price',
    'weight' => 'Weight',
    'length' => 'Length',
    'width' => 'Width',
    'height' => 'Height',
    'tax_class' => 'Tax class',
    'visibility' => 'Visibility',
    'stock_status' => 'Stock status',
    'backorders' => 'Backorders',
    'sold_individually' => 'Sold individually',
    'low_stock_amount' => 'Low stock amount',
    'manage_stock' => 'Manage stock',
    'tax_status' => 'Tax status',
    'upsell_ids' => 'Upsell IDs',
    'crosssell_ids' => 'Crosssell IDs',
    'purchase_note' => 'Purchase note',
    'sale_price_dates_from' => 'Sale price dates from',
    'sale_price_dates_to' => 'Sale price dates_to',
    // Downloadable products
    'download_limit' => 'Download limit',
    'download_expiry' => 'Download expiry',    
    // Virtual products
    'product_url' => 'Product URL',
    'button_text' => 'Button text',



    'images' => 'Images (featured and gallery)',
    "$file_path_header" => 'Downloadable file paths',
    'product_page_url' => 'Product page URL',
    //'taxonomies' => 'Taxonomies (cat/tags/shipping-class)',
    //'meta' => 'Meta (custom fields)',
    //'attributes' => 'Attributes',
);

if(class_exists('WPSEO_Options')){
    /* Yoast is active */

    $post_columns['meta:_yoast_wpseo_focuskw'] = 'meta:_yoast_wpseo_focuskw';
    $post_columns['meta:_yoast_wpseo_canonical'] = 'meta:_yoast_wpseo_canonical';
    $post_columns['meta:_yoast_wpseo_bctitle'] = 'meta:_yoast_wpseo_bctitle';
    $post_columns['meta:_yoast_wpseo_meta-robots-adv'] = 'meta:_yoast_wpseo_meta-robots-adv';
    $post_columns['meta:_yoast_wpseo_is_cornerstone'] ='meta:_yoast_wpseo_is_cornerstone';
    $post_columns['meta:_yoast_wpseo_metadesc'] = 'meta:_yoast_wpseo_metadesc';
    $post_columns['meta:_yoast_wpseo_linkdex'] = 'meta:_yoast_wpseo_linkdex';
    $post_columns['meta:_yoast_wpseo_estimated-reading-time-minutes'] = 'meta:yoast_wpseo_estimated-reading-time-minutes';
    $post_columns['meta:_yoast_wpseo_content_score'] = 'meta:_yoast_wpseo_focuskw';
    $post_columns['meta:_yoast_wpseo_title'] = 'meta:_yoast_wpseo_title';
    $post_columns['meta:_yoast_wpseo_metadesc'] = 'meta:_yoast_wpseo_metadesc';
    $post_columns['meta:_yoast_wpseo_metakeywords'] = 'meta:_yoast_wpseo_metakeywords';
    
}

if (function_exists( 'aioseo' )) {
    /* All in One SEO is active */

    $post_columns['meta:_aioseo_title'] = 'meta:_aioseo_title';
    $post_columns['meta:_aioseo_description'] =  'meta:_aioseo_description';
    $post_columns['meta:_aioseo_keywords'] =  'meta:_aioseo_keywords';
    $post_columns['meta:_aioseo_og_title'] =  'meta:_aioseo_og_title';
    $post_columns['meta:_aioseo_og_description'] =  'meta:_aioseo_og_description';
    $post_columns['meta:_aioseo_twitter_title'] =  'meta:_aioseo_twitter_title';
    $post_columns['meta:_aioseo_og_article_tags'] =  'meta:_aioseo_og_article_tags';
    $post_columns['meta:_aioseo_twitter_description'] =  'meta:_aioseo_twitter_description';
}

if (apply_filters('wpml_setting', false, 'setup_complete')) {

    $post_columns['wpml:language_code'] = 'wpml:language_code';
    $post_columns['wpml:original_product_id'] = 'wpml:original_product_id';
    $post_columns['wpml:original_product_sku'] = 'wpml:original_product_sku';
}

return apply_filters('wt_iew_product_post_columns',$post_columns);