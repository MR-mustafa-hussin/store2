<?php
if (!defined('WPINC')) {
    exit;
}

$allowed_product_types = array(
    'simple' => __('Simple product', 'woocommerce'),
    'grouped' => __('Grouped product', 'woocommerce'),
    'external' => __('External/Affiliate product', 'woocommerce'),
);

return apply_filters('wt_iew_allowed_product_types', $allowed_product_types);