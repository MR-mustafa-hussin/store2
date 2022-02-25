<?php

/**
 * WOOT Meta
 *
 * Handles products metadata on admin panel side
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_TablesMeta {

    private $db_table = 'woot_tables_meta';
    private $db = null;

    public function __construct() {
        global $wpdb;
        $this->db = &$wpdb; //pointer
        $this->db_table = $this->db->prefix . $this->db_table;
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_init', array($this, 'admin_init'), 9999);

        //***

        add_filter('woot_table_orderby_select_args', function($args, $table_id = 0) {

            if ($table_id > 0) {
                $found_rows = $this->get_rows($table_id);

                if (!empty($found_rows)) {
                    $table_columns = woot()->columns->get_table_columns(intval($table_id), ['fields' => 'title,field_key']);
                    foreach ($found_rows as $r) {
                        $title = '';
                        $meta_key = $r['meta_key'];

                        array_map(function($item)use($meta_key, &$title) {
                            if ($item['field_key'] === $meta_key) {
                                $title = $item['title'];
                            }
                        }, $table_columns);

                        if (empty($title)) {
                            $title = $r['title'];
                        }

                        $args[$r['meta_key']] = $title . ': ' . esc_html__('Ascending', 'profit-products-tables-for-woocommerce');
                        $args[$r['meta_key'] . '-desc'] = $title . ': ' . esc_html__('Descending', 'profit-products-tables-for-woocommerce');
                    }
                }
            }

            return $args;
        }, 10, 2);
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
            wp_enqueue_script('woot-tables-meta', WOOT_ASSETS_LINK . 'js/admin/tables-meta.js', ['data-table-23'], WOOT_VERSION, true);
        }
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
            add_action('wp_ajax_woot_get_tables_meta', array($this, 'draw_table'));
            add_action('wp_ajax_woot_save_table_meta_field', array($this, 'save'));
            add_action('wp_ajax_woot_create_meta', array($this, 'create'));
            add_action('wp_ajax_woot_delete_table_meta', array($this, 'delete'));
        }
    }

    /**
     * Extend woocommerce profile by added meta fields
     *
     * @since 1.0.0
     * @param string $table_action
     * @param int $table_id table ID
     * @return void
     */
    public function extend_profile_fields($table_action, $table_id) {
        $meta = $this->get_rows($table_id);

        if (!empty($meta)) {
            $add_profile = [];
            foreach ($meta as $m) {              
                $add_profile[$m['meta_key']] = $this->assemble_meta_for_system($m, $table_id);
            }

            //***

            add_filter("ext_{$table_action}", function($profile) use($add_profile) {
                return array_merge($profile, $add_profile);
            });
        }
    }

    /**
     * Add meta field to system and add actions to it
     * Useful for such shortcodes as [woot_favourites]
     *
     * @since 1.0.2
     * @param array $m
     * @param int $table_id table ID
     * @return array
     */
    public function assemble_meta_for_system($m, $table_id = 0) {
        $tmp = [
            'title' => $m['title'],
            'meta_key' => $m['meta_key'],
            'filter' => true,
            'action' => function($product_id) use($m, $table_id) {
                return apply_filters('woot_meta_data_cell', get_post_meta($product_id, $m['meta_key'], true), $m, $product_id, $table_id);
            }
        ];

        switch ($m['meta_type']) {
            case 'text':
                $tmp['options'] = WOOT_WooCommerce::$fields_options;
                $tmp['order'] = 'asc';
                $tmp['orderby'] = 'meta_value';
                $tmp['get_filter_query_args'] = function($args, $value) use($m) {
                    $args['meta_query'][] = array(
                        'key' => $m['meta_key'],
                        'value' => $value,
                        'compare' => 'LIKE'
                    );

                    return $args;
                };


                $tmp['filter_view'] = 'textinput';
                $tmp['filter_options'] = ['placeholder', 'minlength', 'width'];

                break;

            case 'number':
                $tmp['options'] = WOOT_WooCommerce::$fields_options;
                $tmp['order'] = 'desc';
                $tmp['orderby'] = 'meta_value_num';
                $tmp['get_filter_query_args'] = function($args, $value) use($m) {

                    $value = explode(':', $value);
                    $args['meta_query'][] = array(
                        'key' => $m['meta_key'],
                        'value' => array(intval($value[0]), intval($value[1])),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );

                    return $args;
                };

                $tmp['filter_view'] = 'range_slider';
                $tmp['filter_options'] = ['title', 'min', 'max', 'width'];
                break;

            case 'calendar':
                $tmp['options'] = ['css-background', 'css-media-hide'];
                $tmp['order'] = 'desc';
                $tmp['orderby'] = 'meta_value_num';

                $tmp['action'] = function($product_id) use($m, $table_id) {
                    $val = apply_filters('woot_meta_data_cell', get_post_meta($product_id, $m['meta_key'], true), $m, $product_id, $table_id);

                    if (is_string($val)) {
                        $val = strtotime($val);
                    }

                    return date(apply_filters('woot_date_format', get_option('date_format')), $val);
                };

                $tmp['get_filter_query_args'] = function($args, $value, $is_calendar_dir_to = false) use($m, $table_id) {

                    $fkey = $m['meta_key'];

                    $calendar_type = woot()->filter->fields_options->get($table_id, $fkey . '-data-type');

                    if (!$calendar_type) {
                        $calendar_type = 'datetime';
                    }

                    switch ($calendar_type) {
                        case 'unixtimestamp':

                            $args['meta_query'][] = array(
                                'key' => $fkey,
                                'value' => intval($value),
                                'type' => 'numeric',
                                'compare' => $is_calendar_dir_to ? '<=' : '>='
                            );

                            break;

                        //for ACF calendars https://support.advancedcustomfields.com/forums/topic/filtering-by-date/
                        case 'datetime':
                            $args['meta_query'][] = array(
                                'key' => $fkey,
                                'value' => date('Y-m-d H:i:s', $value),
                                'type' => 'DATETIME',
                                'compare' => $is_calendar_dir_to ? '<=' : '>='
                            );
                            break;
                    }

                    return $args;
                };


                $tmp['filter_view'] = 'calendar';
                $tmp['filter_options'] = ['placeholder', 'data-type'];
                break;
        }


        $tmp['get_filter_draw_data'] = function($table_id) use($m) {
            return woot()->filter->get_field_drawing_data($table_id, $m['meta_key']);
        };

        return $tmp;
    }

    /**
     * Draw table with meta fields on admin panel side
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function draw_table() {
        $table_html_id = 'woot_meta_table';
        echo woot()->draw_table_data([
            'mode' => 'json',
            'action' => 'woot_meta_table',
            'orderby' => 'meta_key',
            'order' => 'asc',
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
            'per_page' => -1,
            'table_data' => $this->get_prepared_data(intval($_REQUEST['post_id']))
                ], $table_html_id, FALSE);

        exit;
    }

    /**
     * Table action 
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function add_table_action() {
        add_action('woot_meta_table', function () {
            return [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput',
                    'order' => 'asc'
                ],
                'meta_key' => [
                    'title' => esc_html__('Meta key', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput',
                    'order' => 'asc'
                ],
                'meta_type' => [
                    'title' => esc_html__('Type', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'select',
                    'order' => 'asc'
                ],
                'notes' => [
                    'title' => esc_html__('Notes', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput'
                ],
                'actions' => [
                    'title' => esc_html__('Actions', 'profit-products-tables-for-woocommerce')
                ]
            ];
        });
    }

    /**
     * Get meta of $table_id
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param array $args
     * @param array $where
     * @param string $where_logic AND OR
     * @return array
     */
    public function get_rows($table_id, $args = [], $where = [], $where_logic = 'AND') {
        static $cache = [];


        $fields = '*';
        if (isset($args['fields'])) {
            $fields = $args['fields']; //string
        }

        $orderby = 'meta_key';
        if (isset($args['orderby'])) {
            $orderby = $args['orderby'];
        }

        $order = 'ASC';
        if (isset($args['order'])) {
            $order = $args['order'];
        }

        $and_where = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $and_where .= " {$where_logic} {$key}='{$value}'";
            }
        }

        //***

        $sql = "SELECT {$fields} FROM {$this->db_table} WHERE table_id={$table_id} {$and_where} ORDER BY {$orderby} {$order}";

        if (!isset($cache[md5($sql)])) {
            $cache[md5($sql)] = $this->db->get_results($sql, ARRAY_A);
        }

        return $cache[md5($sql)];
    }

    /**
     * Get prepared meta data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    private function get_prepared_data($table_id) {
        $columns = [];
        $fields = array_keys(apply_filters('woot_meta_table', NULL));

        $found_rows = $this->get_rows($table_id);
        $found_rows_count = count($found_rows); //no pagination here
        //***

        if (!empty($fields) AND!empty($found_rows)) {
            foreach ($found_rows as $r) {
                $tmp = [];
                $tmp['pid'] = $r['id']; //VERY IMPORTANT AS IT POST ID IN THE TABLES CELLS ACTIONS

                foreach ($fields as $field) {
                    switch ($field) {

                        case 'meta_key':
                            $tmp[$field] = $r['meta_key'];
                            break;

                        case 'meta_type':

                            $tmp[$field] = WOOT_HELPER::draw_select([
                                        'style' => 'width: 100%;'
                                            ], [
                                        'not_defined' => esc_html__('not defined', 'profit-products-tables-for-woocommerce'),
                                        'text' => esc_html__('text', 'profit-products-tables-for-woocommerce'),
                                        'number' => esc_html__('number', 'profit-products-tables-for-woocommerce'),
                                        'calendar' => esc_html__('calendar', 'profit-products-tables-for-woocommerce')
                                            ], $r['meta_type']);

                            break;

                        case 'title':
                        case 'notes':
                            $tmp[$field] = $r[$field];
                            break;

                        case 'actions':

                            $tmp[$field] = WOOT_HELPER::draw_html_item('a', [
                                        'href' => "javascript: woot_meta_table.delete({$r['id']});void(0);",
                                        'title' => esc_html__('delete', 'profit-products-tables-for-woocommerce'),
                                        'class' => 'button woot-dash-btn-single'
                                            ], '<span class="dashicons-before dashicons-no"></span>');

                            break;

                        default:

                            $tmp[$field] = esc_html__('Wrong type', 'profit-products-tables-for-woocommerce');

                            break;
                    }
                }

                $columns[] = $tmp;
            }
        }


        return ['rows' => $columns, 'count' => $found_rows_count];
    }

    //*******************************************************************************

    /**
     * Save meta field optionby ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {
        $this->update_field(WOOT_HELPER::sanitize_text($_REQUEST['field']), WOOT_HELPER::sanitize_text($_REQUEST['post_id']), WOOT_HELPER::sanitize_text($_REQUEST['value']));

        die(json_encode([
            'value' => WOOT_HELPER::sanitize_text($_REQUEST['value'])
        ]));
    }

    /**
     * Update field
     *
     * @since 1.0.0
     * @param string $field
     * @param int $id
     * @param string $value
     * @return void
     */
    private function update_field($field, $id, $value) {
        $this->db->update($this->db_table, array(sanitize_key($field) => $value), array('id' => intval($id)));
    }

    /**
     * Create new meta field row by ajax
     *
     * @since 1.0.0 
     * 
     * @return output string
     */
    public function create() {
        $table_id = intval($_REQUEST['table_id']);

        if ($table_id > 0) {
            $this->insert(array(
                'table_id' => $table_id,
                'meta_key' => esc_html__('0 write key here', 'profit-products-tables-for-woocommerce')
            ));
        }

        die(json_encode($this->get_prepared_data($table_id)));
    }

    /**
     * Insert data
     *
     * @since 1.0.0 
     * 
     * @return void
     */
    public function insert($args) {
        $this->db->insert($this->db_table, $args);
    }

    /**
     * Delete meta row fro admin table
     *
     * @since 1.0.0 
     * @param int $id row ID
     * @return void
     */
    public function delete($id = 0) {

        if (!$id) {
            $id = intval($_REQUEST['id']);
        } else {
            $id = intval($id);
        }

        if ($id > 0) {
            $this->db->delete($this->db_table, array('id' => $id));
        }
    }

    /**
     * Get all meta fields
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

}
