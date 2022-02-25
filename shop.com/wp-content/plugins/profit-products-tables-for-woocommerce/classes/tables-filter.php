<?php

/**
 * WOOT Tables Filter
 *
 * Handles products filter data on admin panel side
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_TablesFilter {

    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'), 9999);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    /**
     * Hook admin_init
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_init() {
        if (WOOT_HELPER::can_manage_data()) {
            add_action('wp_ajax_woot_get_fields_for_filter', array($this, 'get_fields'));
            add_action('wp_ajax_woot_save_fields_for_filter', array($this, 'save_fields'));
        }
    }

    /**
     * Hook admin_enqueue_scripts
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_enqueue_scripts() {
        if (isset($_GET['page']) AND $_GET['page'] == 'woot') {
            wp_enqueue_script('block-constructor-23', WOOT_ASSETS_LINK . 'js/block-constructor-23.js', [], WOOT_VERSION, true);
            wp_enqueue_style('block-constructor-23', WOOT_ASSETS_LINK . 'css/block-constructor-23.css', [], WOOT_VERSION);
        }
    }

    /**
     * Get filter fields by ajax
     *
     * @since 1.0.0
     * 
     * @return output
     */
    public function get_fields() {

        $table_id = intval($_REQUEST['post_id']);
        $profile_action = WOOT::get_table_action($table_id);
        $donor_data = [];
        $acceptor_data = [];

        if ($profile_action) {
            $profile = apply_filters($profile_action, $table_id);
            $post_type = $profile[0]['post_type'];
            if (!empty($post_type)) {

                $tmp_sort = [];
                foreach ($profile as $key => $value) {
                    if (isset($value['filter']) AND $value['filter']) {
                        $tmp_sort[$value['title']] = $key;
                    }
                }

                if (!empty($tmp_sort)) {
                    ksort($tmp_sort);
                    foreach ($tmp_sort as $title => $key) {
                        $donor_data[$key] = [
                            'content' => $title,
                            'has_settings' => woot()->filter->fields_options->get_field_data($table_id, $key)['count']
                        ];
                    }
                }

                //***

                $acceptor_keys = $this->get_acceptor_keys($table_id);

                if (!empty($acceptor_keys)) {
                    foreach ($acceptor_keys as $key) {
                        if (isset($donor_data[$key])) {
                            $acceptor_data[$key] = $donor_data[$key];
                            unset($donor_data[$key]);
                        }
                    }
                }
            }
        }


        die(json_encode(['donor_data' => $donor_data, 'acceptor_data' => $acceptor_data]));
    }

    /**
     * Save fields data by ajax
     *
     * @since 1.0.0
     * 
     * @return output
     */
    public function save_fields() {
        //data format json, sanitized as array
        $acceptor_data = WOOT_HELPER::sanitize_array(array_keys(json_decode(stripslashes($_REQUEST['acceptor_data']), true)));
        woot()->tables->update_field(intval($_REQUEST['post_id']), 'filter', json_encode($acceptor_data));
        die(json_encode($acceptor_data));
    }

    /**
     * Get filter keys selected for filter form
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    public function get_acceptor_keys($table_id) {
        $res = [];

        if (woot()->tables->get(intval($table_id))) {
            $res = strval(woot()->tables->get(intval($table_id))['filter']);
        }

        if ($res) {
            $res = json_decode($res, true);
        }

        return $res;
    }

}
