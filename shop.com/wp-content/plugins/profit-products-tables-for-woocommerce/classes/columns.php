<?php

/**
 * WOOT Columns
 *
 * Handles a table columns
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_Columns {

    private $db_table = 'woot_tables_columns';
    private $db = null;
    public $options = null;
    public $meta = null;
    public $filter = null;

    public function __construct() {
        global $wpdb;
        $this->db = &$wpdb; //pointer
        $this->db_table = $this->db->prefix . $this->db_table;
        $this->options = new WOOT_TablesOptions();
        $this->filter = new WOOT_TablesFilter();
        $this->meta = new WOOT_TablesMeta();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_init', array($this, 'admin_init'), 9999);
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
            wp_enqueue_script('woot-columns', WOOT_ASSETS_LINK . 'js/admin/columns.js', [], WOOT_VERSION, true);
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
            add_action('wp_ajax_woot_get_columns_data', array($this, 'get_columns_data'));
            add_action('wp_ajax_woot_save_table_column_field', array($this, 'save_column_field'));
            add_action('wp_ajax_woot_create_table_column', array($this, 'create_column'));
            add_action('wp_ajax_woot_refresh_columns_table', array($this, 'refresh'));
            add_action('wp_ajax_woot_delete_table_column', array($this, 'delete'));
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
        add_action('woot_columns_table', function () {
            return [
                0 => [
                //'ajax_action' => 'woot_get_table_columns'
                ],
                'move' => [
                    'title' => esc_html__('Move', 'profit-products-tables-for-woocommerce')
                ],
                'title' => [
                    'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput',
                    'order' => 'asc'
                ],
                'is_active' => [
                    'title' => esc_html__('Active', 'profit-products-tables-for-woocommerce'),
                    'order' => 'desc'
                ],
                'show_on_mobile' => [
                    'title' => esc_html__('Mobile', 'profit-products-tables-for-woocommerce'),
                    'order' => 'desc'
                ],
                'field_key' => [
                    'title' => esc_html__('Field', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'select'
                ],
                'width' => [
                    'title' => esc_html__('Width', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput'
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
     * Get table columns
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param array $args
     * @param array $where
     * @return array
     */
    public function get_table_columns($table_id, $args = [], $where = []) {
        $fields = '*';
        if (isset($args['fields'])) {
            $fields = $args['fields']; //string
        }

        $orderby = 'pos_num';
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
                $and_where .= " AND {$key}={$value}";
            }
        }

        $sql = "SELECT {$fields} FROM {$this->db_table} WHERE table_id={$table_id} {$and_where} ORDER BY {$orderby} {$order}";

        return $this->db->get_results($sql, ARRAY_A);
    }

    /**
     * Get columns count
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param array $args
     * @param array $where
     * @return int
     */
    public function get_count($table_id, $args = [], $where = []) {
        $args['fields'] = 'COUNT(*) as count';
        return $this->get_table_columns($table_id, $args, $where)[0]['count'];
    }

    /**
     * Get all columns data by $table_id
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param boolean $order_by_active should be data ordered
     * @return array
     */
    private function get_table_columns_data($table_id, $order_by_active = true) {
        $columns = [];
        $fields = array_keys(apply_filters('woot_columns_table', NULL));

        //***

        $found_columns = $this->get_table_columns($table_id);
        $found_columns_count = count($found_columns); //no pagination here
        //+++
        //let active columns always will be on the top
        if (!empty($found_columns) AND $order_by_active) {

            $tmp = [];
            foreach ($found_columns as $key => $value) {
                if ($value['is_active']) {
                    $tmp[] = $value;
                    unset($found_columns[$key]);
                }
            }

            $found_columns = array_merge($tmp, $found_columns);
            unset($tmp);
        }

        //+++

        $table_action = WOOT::get_table_action($table_id);

        //***

        $profile = apply_filters($table_action, $table_id);

        //***        

        if (!empty($fields) AND!empty($found_columns)) {

            foreach ($found_columns as $c) {
                $tmp = [];
                $tmp['pid'] = $c['id']; //VERY IMPORTANT AS IT POST ID IN THE TABLES CELLS ACTIONS

                foreach ($fields as $field) {
                    switch ($field) {
                        case 'id':
                            $tmp[$field] = $c['id'];
                            break;

                        case 'move':
                            $tmp[$field] = WOOT_HELPER::draw_html_item('img', [
                                        'src' => WOOT_ASSETS_LINK . 'img/move.png',
                                        'width' => 20,
                                        'alt' => esc_html__('drag and drope', 'profit-products-tables-for-woocommerce'),
                                        'class' => 'woot-tr-drag-and-drope'
                            ]);
                            break;

                        case 'title':
                            $tmp[$field] = $c['title'];
                            break;

                        case 'is_active':
                            $tmp[$field] = WOOT_HELPER::draw_switcher('is_active', $c['is_active'], $c['id'], 'woot_save_table_column_field');
                            break;

                        case 'show_on_mobile':
                            $tmp[$field] = WOOT_HELPER::draw_switcher('show_on_mobile', $c['show_on_mobile'], $c['id'], 'woot_save_table_column_field');
                            break;

                        case 'notes':
                            $tmp[$field] = $c['notes'];
                            break;

                        case 'width':
                            $tmp[$field] = $c['width'];
                            break;

                        case 'created':
                            $tmp[$field] = date(get_option('date_format') . ' ' . get_option('time_format'), $c['created']);
                            break;

                        case 'field_key':

                            if (!empty($profile)) {
                                $options = [
                                    0 => esc_html__('not selected', 'profit-products-tables-for-woocommerce')
                                ];

                                if (!empty($profile) AND is_array($profile)) {
                                    foreach ($profile as $key => $f) {
                                        if ($key === 0) {
                                            continue;
                                        }

                                        if (isset($f['display']) AND!$f['display']) {
                                            continue;
                                        }

                                        $options[$key] = $f['title'];
                                    }
                                }

                                //asort($options);

                                $tmp[$field] = WOOT_HELPER::draw_select([], $options, $c[$field]);
                            } else {
                                //$tmp[$field] = esc_html__('select table action', 'profit-products-tables-for-woocommerce');
                            }

                            if (!isset($tmp[$field])) {
                                $tmp[$field] = esc_html__('no fields found', 'profit-products-tables-for-woocommerce');
                            }
                            break;

                        case 'actions':

                            $edit_popup_title = sprintf(esc_html__('Table: %s. Field: %s', 'profit-products-tables-for-woocommerce'), addslashes(woot()->tables->get($table_id)['title']), $c['title']);

                            //***                 

                            $help = esc_html__('Help', 'profit-products-tables-for-woocommerce');
                            $edit_button = WOOT_HELPER::draw_html_item('a', [
                                        'href' => "javascript: woot_helper.call_popup(\"woot_show_column_field_option\",{field_id:{$c['id']}, table_id:{$table_id}, not_paste:1},\"woot_options_columns_table\",\"{$edit_popup_title}\", {left:15, right:15}, `<a href=\"https://products-tables.com/woot-documentation\" class=\"woot-btn\" target=\"_blank\">{$help}</a>`); void(0);",
                                        'title' => esc_html__('edit', 'profit-products-tables-for-woocommerce'),
                                        'class' => 'button woot-field-edit-btn woot-dash-btn-single'
                                            ], '<span class="dashicons-before dashicons-edit"></span>');

                            //***

                            $tmp[$field] = $edit_button . WOOT_HELPER::draw_html_item('a', [
                                        'href' => "javascript: woot_columns_table.delete({$c['id']});void(0);",
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


        return ['rows' => $columns, 'count' => $found_columns_count];
    }

    //*******************************************************************************

    /**
     * Generate table of columns data by ajax
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function get_columns_data() {
        $table_html_id = WOOT_HELPER::sanitize_text($_REQUEST['table_html_id']);

        echo WOOT_HELPER::render_html('views/table.php', array(
            'table_html_id' => $table_html_id,
            'hide_text_search' => false,
            'text_search_min_symbols' => 2,
            'placeholder' => esc_html__('search by column title', 'profit-products-tables-for-woocommerce') . ' ...',
            'classes' => 'woot-columns-table',
            'table_view' => 'separated'
        )) . woot()->draw_table_data([
            'mode' => 'json',
            'action' => 'woot_columns_table',
            'post_id' => intval($_REQUEST['post_id']),
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
            'per_page' => -1,
            'table_data' => $this->get_table_columns_data(intval($_REQUEST['post_id']))
                ], $table_html_id);
        exit;
    }

    /**
     * Save table column data by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save_column_field() {

        switch ($_REQUEST['field']) {
            case 'pos_num':
                $ids = explode(',', WOOT_HELPER::sanitize_text($_REQUEST['value']));

                if (!empty($ids) AND is_array($ids)) {
                    $ids = array_map(function($id) {
                        return intval($id); //sanitize
                    }, $ids);

                    //***

                    $pos_num = 0;
                    foreach ($ids as $id) {
                        if ($id > 0) {
                            $this->update_field('pos_num', $id, $pos_num);
                            ++$pos_num;
                        }
                    }
                }

                break;

            default:
                //post_id here is column id
                $this->update_field(WOOT_HELPER::sanitize_text($_REQUEST['field']), WOOT_HELPER::sanitize_text($_REQUEST['post_id']), WOOT_HELPER::sanitize_text($_REQUEST['value']));
                break;
        }


        die(json_encode([
            'value' => WOOT_HELPER::sanitize_text($_REQUEST['value'])
        ]));
    }

    /**
     * Update column data
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function update_field($field, $id, $value) {
        $this->db->update($this->db_table, array(sanitize_key($field) => $value), array('id' => intval($id)));
    }

    /**
     * Create new column
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function create($table_id, $prepend = false, $title = '', $field_key = NULL) {

        if ($prepend) {
            $pos_num = 0 - $this->get_count($table_id);
        } else {
            $pos_num = $this->get_count($table_id) + 1;
        }

        if (empty($title)) {
            $title = esc_html__('New Column', 'profit-products-tables-for-woocommerce');
        }

        if ($table_id > 0) {
            $this->insert(array(
                'title' => $title,
                'table_id' => $table_id,
                'pos_num' => $pos_num,
                'field_key' => $field_key,
                'is_active' => intval(boolval($field_key)),
                'created' => current_time('U', get_option('timezone_string'))
            ));
        }
    }

    /**
     * Create new column by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function create_column() {
        $this->create(intval($_REQUEST['post_id']), boolval($_REQUEST['prepend']));
        die(json_encode($this->get_table_columns_data(intval($_REQUEST['post_id']), false)));
    }

    /**
     * Update column by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function refresh() {
        die(json_encode($this->get_table_columns_data(intval($_REQUEST['post_id']), false)));
    }

    /**
     * Data insert
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function insert($args) {
        $this->db->insert($this->db_table, $args);
    }

    /**
     * Delete column by ajax
     *
     * @since 1.0.0
     * @param int $id
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
     * Get a column data by its ID
     *
     * @since 1.0.0
     * @param int $id column ID
     * @return void
     */
    public function get($id) {
        static $columns = [];

        if (!isset($columns[$id])) {
            $sql = "SELECT * FROM {$this->db_table} WHERE id={$id}";
            $columns[$id] = $this->db->get_row($sql, ARRAY_A);
        }

        return $columns[$id];
    }

    /**
     * Get column data by field AND value
     *
     * @since 1.0.0
     * @param string $field
     * @param string $value
     * @param array $fields select fields
     * @return array
     */
    public function get_by($field, $value, $fields = []) {
        if (empty($fields)) {
            $fields = '*';
        } else {
            $fields = implode(',', $fields);
        }


        $sql = "SELECT {$fields} FROM {$this->db_table} WHERE {$field}=`{$value}`";
        return $this->db->get_row($sql, ARRAY_A);
    }

    /**
     * Get column field data by field key
     *
     * @since 1.0.0
     * @param id $table_id table ID
     * @param string $field_key
     * @return mixed
     */
    public function get_by_field_key($table_id, $field_key) {
        static $res = []; //cache

        if (!isset($res[$table_id])) {
            $res[$table_id] = [];
        }

        if (!isset($res[$table_id][$field_key])) {
            $sql = "SELECT * FROM {$this->db_table} WHERE table_id={$table_id} AND field_key='{$field_key}'";
            $res[$table_id][$field_key] = $this->db->get_row($sql, ARRAY_A);
        }

        return $res[$table_id][$field_key];
    }

    /**
     * Assemble a column CSS options data
     *
     * @since 1.0.0
     * @param id $table_id table ID
     * @param string $table_html_id table HTML id
     * @return string
     */
    public function get_colums_css($table_id, $table_html_id) {
        $col_styles = '';

        if ($table_id > 0 AND!empty($table_html_id)) {
            $table_columns = $this->get_table_columns(intval($table_id), ['fields' => 'id'], ['is_active' => 1]);
            //this css options has more prioritet than table themes styles
            if (!empty($table_columns)) {
                foreach ($table_columns as $c) {
                    $css = $this->get($c['id'], ['field_key', 'options']);

                    if (!empty($css['options'])) {
                        $options = json_decode($css['options'], true);

                        foreach ($options as $key => $value) {
                            if (!empty($value)) {
                                $css_key = '';
                                if (substr($key, 0, 4) === 'css-') {
                                    $css_key = str_replace('css-', '', $key);

                                    if ($css_key === 'media-hide') {
                                        if (intval($value) > 0) {
                                            $col_styles .= "@media screen and (max-width: {$value}px) {
                                            #{$table_html_id} table .table23_td_{$css['field_key']},
                                            #{$table_html_id} table th[data-key='{$css['field_key']}']  {
                                              display: none !important;
                                            }
                                          } ";
                                        }
                                    } else {
                                        $important = '!important';
                                        if ($css_key === 'background') {
                                            $important = ''; //for column selecting by color on ordering
                                            if (substr(trim($value), 0, 4) === 'http') {
                                                $value = "url({$value})";
                                            }
                                        }

                                        $col_styles .= "#{$table_html_id} table .table23_td_{$css['field_key']}{{$css_key}:{$value} {$important};} ";
                                        if ($css_key === 'color') {
                                            $col_styles .= "#{$table_html_id} table .table23_td_{$css['field_key']} a{{$css_key}:{$value} {$important};} ";
                                            $col_styles .= "#{$table_html_id} table .table23_td_{$css['field_key']} .woot-num-cell{{$css_key}:{$value} {$important};} ";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $col_styles;
    }

    /**
     * Get all columns
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function gets() {
        return $this->db->get_results("SELECT * FROM {$this->db_table} ORDER BY id DESC", ARRAY_A);
    }

    /**
     * Import columns data
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function import($data) {
        WOOT_HELPER::import_mysql_table($this->db_table, $data);
    }

}
