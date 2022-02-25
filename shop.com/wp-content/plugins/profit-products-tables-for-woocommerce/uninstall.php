<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
$settings = get_option('woot_settings', []);
if ($settings AND!is_array($settings)) {
    $settings = json_decode($settings, true);
}

if (isset($settings['delete_db_tables']) AND intval($settings['delete_db_tables'])) {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}woot_tables");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}woot_tables_columns");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}woot_tables_meta");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}woot_vocabulary");
    delete_option('woot_settings');
    delete_option('woot_mime_types_association');
}

