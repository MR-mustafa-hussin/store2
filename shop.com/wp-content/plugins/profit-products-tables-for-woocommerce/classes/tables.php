<?php

/**
 * WOOT Tables
 *
 * Handles system tables
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once WOOT_PATH . 'classes/columns.php';
include_once WOOT_PATH . 'classes/columns-fields-options.php';

include_once WOOT_PATH . 'classes/tables-meta.php';
include_once WOOT_PATH . 'classes/tables-filter.php';
include_once WOOT_PATH . 'classes/tables-options.php';

class WOOT_Tables {

    private $db_table = 'woot_tables';
    private $db = null;

    public function __construct() {
        global $wpdb;
        $this->db = &$wpdb;
        $this->db_table = $this->db->prefix . $this->db_table;
        add_action('admin_init', array($this, 'admin_init'), 9999);
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

            $this->add_table_action();

            add_action('wp_ajax_woot_create_table', array($this, 'create'));
            add_action('wp_ajax_woot_save_table_field', array($this, 'update'));
            add_action('wp_ajax_woot_delete_table', array($this, 'delete'));
            add_action('wp_ajax_woot_clone_table', array($this, 'clone'));
        }
    }

    /**
     * Table action 
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function add_table_action() {
        add_action('woot_admin_table', function () {
            return [
                0 => [],
                'thumbnail' => [
                    'title' => esc_html__('Thumb', 'profit-products-tables-for-woocommerce'),
                    'order' => FALSE
                ],
                'title' => [
                    'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce'),
                    'order' => 'asc',
                    'editable' => 'textinput'
                ],
                'shortcode' => [
                    'title' => WOOT_HELPER::draw_html_item('a', [
                        'href' => 'https://products-tables.com/shortcode/woot/',
                        'target' => '_blank'
                            ], esc_html__('Shortcode', 'profit-products-tables-for-woocommerce')),
                    'order' => FALSE
                ],
                'status' => [
                    'title' => esc_html__('Published', 'profit-products-tables-for-woocommerce'),
                    'order' => FALSE
                ],
                'skin' => [
                    'title' => esc_html__('Skin', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'select'
                ],
                'actions' => [
                    'title' => esc_html__('Actions', 'profit-products-tables-for-woocommerce'),
                    'order' => FALSE
                ]
            ];
        });
    }

    /**
     * Get table data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    public function get($table_id) {
        static $tables = [];

        if ($table_id > 0) {
            if (!isset($tables[$table_id])) {
                $tables[$table_id] = $this->db->get_row("SELECT * FROM {$this->db_table} WHERE id = {$table_id}", ARRAY_A);
            }
        } else {
            return [];
        }

        return $tables[$table_id];
    }

    /**
     * Create table by ajax
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function create() {
        $this->db->insert($this->db_table, [
            'title' => esc_html__('New Table', 'profit-products-tables-for-woocommerce'),
            'table_action' => WOOT_WooCommerce::$action
        ]);

        $table_id = intval($this->db->insert_id);

        $cols = new WOOT_Columns();
        $cols->create($table_id, 0, 'ID', 'id');
        $cols->create($table_id, 0, esc_html__('Thumbnail', 'profit-products-tables-for-woocommerce'), 'thumbnail');
        $cols->create($table_id, 0, esc_html__('Title', 'profit-products-tables-for-woocommerce'), 'post_title');
        $cols->create($table_id, 0, esc_html__('Price', 'profit-products-tables-for-woocommerce'), 'price');
        $cols->create($table_id, 0, esc_html__('Gallery', 'profit-products-tables-for-woocommerce'), 'gallery');
        $cols->create($table_id, 0, esc_html__('Cart', 'profit-products-tables-for-woocommerce'), 'add_to_cart');

        die(json_encode($this->get_admin_table_rows()));
    }

    /**
     * Update table by ajax
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function update() {
        $table_id = intval($_REQUEST['post_id']);
        $field = sanitize_key($_REQUEST['field']);
        $value = WOOT_HELPER::sanitize_text($_REQUEST['value']);

        if ($table_id > 0) {
            switch ($field) {
                case 'title':
                case 'skin':
                    $value = WOOT_HELPER::sanitize_text($value);
                    $this->update_field($table_id, $field, $value);
                    break;

                case 'status':
                case 'thumbnail':
                    $value = intval($value);
                    $this->update_field($table_id, $field, $value);
                    break;
            }
        }

        die(json_encode([
            'value' => $value
        ]));
    }

    /**
     * Update table field
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field
     * @param string$value
     * @return void
     */
    public function update_field($table_id, $field, $value) {
        $this->db->update($this->db_table, [$field => $value], array('id' => $table_id));
    }

    /**
     * Delete table by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function delete() {
        $table_id = intval($_REQUEST['id']);

        //columns
        if ($columns = woot()->columns->get_table_columns($table_id, ['fields' => 'id'])) {
            foreach ($columns as $c) {
                woot()->columns->delete($c['id']);
            }
        }

        //meta
        if ($meta = woot()->columns->meta->get_rows($table_id, ['fields' => 'id'])) {
            foreach ($meta as $c) {
                woot()->columns->meta->delete($c['id']);
            }
        }


        //table
        $this->db->delete($this->db_table, ['id' => $table_id]);
    }

    /**
     * Clone table by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function clone() {
        $donor_table_id = intval($_REQUEST['id']);
        $table = $this->get($donor_table_id);

        if ($table) {
            unset($table['id']);

            $this->db->insert($this->db_table, [
                'title' => esc_html__('New Table', 'profit-products-tables-for-woocommerce'),
                'table_action' => WOOT_WooCommerce::$action
            ]);

            $new_table_id = intval($this->db->insert_id);
            $table['title'] = sprintf(esc_html__('%s (clone)', 'profit-products-tables-for-woocommerce'), $table['title']);
            $this->db->update($this->db_table, $table, array('id' => $new_table_id));

            $columns = woot()->columns->get_table_columns($donor_table_id);
            $meta = woot()->columns->meta->get_rows($donor_table_id);

            if (!empty($columns)) {
                foreach ($columns as $c) {
                    unset($c['id']);
                    $c['table_id'] = $new_table_id;
                    $c['created'] = current_time('U', get_option('timezone_string'));
                    woot()->columns->insert($c);
                }
            }

            if (!empty($meta)) {
                foreach ($meta as $m) {
                    unset($m['id']);
                    $m['table_id'] = $new_table_id;
                    woot()->columns->meta->insert($m);
                }
            }
        }

        die(json_encode($this->get_admin_table_rows()));
    }

    /**
     * Get thumbnail column data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return string
     */
    private function get_thumbnail($table_id) {
        $attachment_id = $this->get($table_id)['thumbnail'];

        if ($attachment_id) {
            $img_src = wp_get_attachment_image_src($attachment_id, 'thumbnail');

            if (is_array($img_src) AND!empty($img_src[0])) {
                return WOOT_HELPER::draw_html_item('a', array(
                            'href' => 'javasctipt: void(0);',
                            'onclick' => 'return woot_change_thumbnail(this);',
                            'data-post-id' => $table_id
                                ), WOOT_HELPER::draw_html_item('img', array(
                                    'src' => $img_src[0],
                                    'width' => 40,
                                    'alt' => ''
                )));
            }
        } else {
            return WOOT_HELPER::draw_html_item('a', array(
                        'href' => 'javasctipt: void(0);',
                        'onclick' => 'return woot_change_thumbnail(this);',
                        'data-post-id' => $table_id,
                        'class' => 'woot-thumbnail'
                            ), WOOT_HELPER::draw_html_item('img', array(
                                'src' => WOOT_ASSETS_LINK . 'img/not-found.jpg',
                                'width' => 40,
                                'alt' => ''
            )));
        }
    }

    /**
     * Table all rows on admin panel
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function get_admin_table_rows() {

        $rows = [];
        $tables = $this->gets();

        if (!empty($tables)) {
            foreach ($tables as $t) {
                $table_id = intval($t['id']);

                $rows[] = [
                    'pid' => $table_id,
                    'thumbnail' => $this->get_thumbnail($table_id),
                    'title' => $this->get($table_id)['title'],
                    'shortcode' => WOOT_HELPER::draw_html_item('input', [
                        'type' => 'text',
                        'class' => 'woot-shortcode-copy-container',
                        'readonly' => 'readony',
                        'value' => "[woot id={$table_id}]"
                    ]),
                    'status' => WOOT_HELPER::draw_switcher('status', $this->get($table_id)['status'], $table_id, 'woot_save_table_field'),
                    'skin' => WOOT_HELPER::draw_select([], woot()->skins->get_skins(), $this->get($table_id)['skin']),
                    'actions' => WOOT_HELPER::draw_html_item('a', array(
                        'href' => "javascript: woot_main_table.call_popup({$table_id}); void(0);",
                        'class' => 'button woot-dash-btn-single',
                        'title' => esc_html__('table options', 'profit-products-tables-for-woocommerce')
                            ), '<span class="dashicons-before dashicons-admin-generic"></span>')
                    . WOOT_HELPER::draw_html_item('a', [
                        'href' => "javascript: woot_main_table.clone({$table_id});void(0);",
                        'title' => esc_html__('clone table', 'profit-products-tables-for-woocommerce'),
                        'class' => 'button woot-dash-btn-single'
                            ], '<span class="dashicons-before dashicons-admin-page"></span>')
                    . WOOT_HELPER::draw_html_item('a', [
                        'href' => "javascript: woot_main_table.delete({$table_id});void(0);",
                        'title' => esc_html__('delete table', 'profit-products-tables-for-woocommerce'),
                        'class' => 'button woot-dash-btn-single'
                            ], '<span class="dashicons-before dashicons-no"></span>')
                ];
            }
        }

        return ['rows' => $rows, 'count' => count($rows)];
    }

    /**
     * Get all existing tables
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function gets() {
        return $this->db->get_results("SELECT * FROM {$this->db_table} ORDER BY id DESC", ARRAY_A);
    }

    /**
     * Import data
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function import($data) {
        WOOT_HELPER::import_mysql_table($this->db_table, $data);
    }

    /**
     * Is table exists
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return boolean
     */
    public function is_exists($table_id) {
        return boolval($this->get($table_id));
    }

}
