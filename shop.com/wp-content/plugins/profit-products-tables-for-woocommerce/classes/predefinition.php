<?php

/**
 * WOOT Predefinition
 *
 * Handles predefined data for shortcode [woot]
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_Predefinition {

    public $action = 'woot_predefinition_table';

    public function __construct() {
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
            wp_enqueue_script('woot-predefinition', WOOT_ASSETS_LINK . 'js/admin/predefinition.js', ['data-table-23'], WOOT_VERSION, true);
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
            add_action('wp_ajax_woot_get_predefinition_table', array($this, 'get_table'));
            add_action('wp_ajax_woot_save_table_predefinition_field', array($this, 'save'));
        }
    }

    /**
     * Draw table on admin panel by ajax
     *
     * @since 1.0.0
     * 
     * @return output
     */
    public function get_table() {
        $table_html_id = 'woot-predefinition-table';

        echo WOOT_HELPER::render_html('views/table.php', array(
            'table_html_id' => $table_html_id,
            'hide_text_search' => true,
            'table_view' => 'separated'
        )) . woot()->draw_table_data([
            'mode' => 'json',
            'action' => $this->action,
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
            'per_page' => -1,
            'use_flow_header' => 0,
            'table_data' => $this->get_table_columns_data(intval($_REQUEST['post_id']))
                ], $table_html_id);
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
        add_action($this->action, function () {
            return [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce')
                ],
                'value' => [
                    'title' => esc_html__('Value', 'profit-products-tables-for-woocommerce'),
                    'editable' => 'textinput',
                    'custom_field_key' => true
                ],
                'notes' => [
                    'title' => esc_html__('Info', 'profit-products-tables-for-woocommerce')
                ]
            ];
        });
    }

    /**
     * Get all columns data by $table_id
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    private function get_table_columns_data($table_id) {
        $columns = [];
        $fields = 'title,value,notes';

        //***

        $found_options = $this->get_rows($table_id);

        //***

        if (!empty($fields) AND!empty($found_options)) {
            $fields = explode(',', $fields);

            foreach ($found_options as $c) {
                $tmp = [];
                $tmp['pid'] = $c['id']; //VERY IMPORTANT AS IT POST ID IN THE TABLES CELLS ACTIONS

                foreach ($fields as $field) {
                    switch ($field) {
                        case 'title':
                            $tmp[$field] = $c['title'];
                            break;

                        case 'value':

                            if (isset($c['value_custom_field_key']) AND!empty($c['value_custom_field_key'])) {
                                $tmp[$field] = [
                                    'value' => $c['value'],
                                    'custom_field_key' => $c['value_custom_field_key']
                                ];
                            } else {
                                $tmp[$field] = $c['value'];
                            }

                            break;

                        case 'notes':
                            $tmp[$field] = $c['notes'];
                            break;

                        default:
                            $tmp[$field] = WOOT_Vocabulary::get(esc_html__('Wrong type', 'profit-products-tables-for-woocommerce'));
                            break;
                    }
                }

                $columns[] = $tmp;
            }
        }


        return ['rows' => $columns, 'count' => count($found_options)];
    }

    /**
     * Rows of options
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    private function get_rows($table_id) {
        $rows = [
            [
                'id' => $table_id,
                'title' => esc_html__('Products ids', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'ids'),
                'value_custom_field_key' => 'ids',
                'notes' => esc_html__('Using comma, set products ids you want to show in the table. Example: 23,99,777. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Exclude products ids', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'ids_exclude'),
                'value_custom_field_key' => 'ids_exclude',
                'notes' => esc_html__('Using comma, set products ids you want to hide in the table. Example: 24,101,888. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Products SKU', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'sku'),
                'value_custom_field_key' => 'sku',
                'notes' => esc_html__('Using comma, set products SKU you want to show in the table. Example: aa1,bb2,cc3. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Exclude products SKU', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'sku_exclude'),
                'value_custom_field_key' => 'sku_exclude',
                'notes' => esc_html__('Using comma, set products SKU you want to hide in the table. Example: aa1,bb2,cc3. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('On sale only', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('on_sale_only', intval($this->get($table_id, 'on_sale_only')), $table_id, 'woot_save_table_predefinition_field'),
                'value_custom_field_key' => 'on_sale_only',
                'notes' => esc_html__('Show products which are on sale only', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Show hidden', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('show_hidden', intval($this->get($table_id, 'show_hidden')), $table_id, 'woot_save_table_predefinition_field'),
                'value_custom_field_key' => 'show_hidden',
                'notes' => esc_html__('Show products which visibility in the catalog is set as hidden', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('In stock only', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('in_stock_only', intval($this->get($table_id, 'in_stock_only')), $table_id, 'woot_save_table_predefinition_field'),
                'value_custom_field_key' => 'in_stock_only',
                'notes' => esc_html__('Show products which are in stock only', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Featured only', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('featured_only', intval($this->get($table_id, 'featured_only')), $table_id, 'woot_save_table_predefinition_field'),
                'value_custom_field_key' => 'featured_only',
                'notes' => esc_html__('Show featured products only', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Authors', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'authors'),
                'value_custom_field_key' => 'authors',
                'notes' => esc_html__('Products by authors ids. Example: 1,2,3. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Bestsellers', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'bestsellers'),
                'value_custom_field_key' => 'bestsellers',
                'notes' => esc_html__('Bestsellers products. Set their count to display. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Newest', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'newest'),
                'value_custom_field_key' => 'newest',
                'notes' => esc_html__('Newest products. Set their count to display. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Included taxonomy', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'by_taxonomy'),
                'value_custom_field_key' => 'by_taxonomy',
                'notes' => esc_html__('Display products which relevant to the rule. Example: product_cat:25,26|pa_color:19|rel:AND. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Excluded taxonomy', 'profit-products-tables-for-woocommerce'),
                'value' => $this->get($table_id, 'not_by_taxonomy'),
                'value_custom_field_key' => 'not_by_taxonomy',
                'notes' => esc_html__('Exclude products which relevant to the rule. Example: pa_color:19|pa_size:21|rel:OR. Set -1 if you do not want to use it.', 'profit-products-tables-for-woocommerce'),
            ],
        ];


        return $rows;
    }

    /**
     * Get table $table_id predefinitions
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $key
     * @return array
     */
    public function get($table_id, $key = NULL) {
        $predefinition = [];

        if (woot()->tables->get($table_id)) {
            $predefinition = woot()->tables->get($table_id)['predefinition'];
        }

        if (!$predefinition) {
            $predefinition = [];
        } else {
            $predefinition = json_decode($predefinition, true);
        }


        //***

        if ($key) {
            return isset($predefinition[$key]) ? $predefinition[$key] : -1;
        }

        return $predefinition;
    }

    //*******************************************************************************

    /**
     * Save predefinition option by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {

        $value = WOOT_HELPER::sanitize_text($_REQUEST['value']);

        $table_id = intval($_REQUEST['post_id']);
        $predefinition = $this->get($table_id);
        $predefinition[WOOT_HELPER::sanitize_text($_REQUEST['field'])] = $value;

        woot()->tables->update_field($table_id, 'predefinition', json_encode($predefinition));

        die(json_encode([
            'value' => $value
        ]));
    }

    /**
     * Get products by predefined taxonomy rules - for products wp_query assembling
     *
     * @since 1.0.0
     * @param array $args
     * @param string $value
     * @param string $operator IN AND
     * @return array
     */
    public function get_by_taxonomy($args, $value, $operator = 'IN') {
        $taxonomy = [];
        $tmp = explode('|', $value);
        if (!empty($tmp)) {
            foreach ($tmp as $v) {
                $v = explode(':', $v);
                if (count($v) === 2) {
                    $taxonomy[$v[0]] = $v[1];
                }
            }
        }

        //***
        //product_cat:25,26|pa_color:19|rel:AND
        if (!empty($taxonomy)) {

            if (!isset($args['tax_query'])) {
                $args['tax_query'] = [];
            }

            $tmp = ['relation' => 'AND']; //by default
            foreach ($taxonomy as $tax_key => $val) {
                if ($tax_key === 'rel') {

                    if ($operator === 'NOT IN') {
                        $val = strtolower($val);
                        switch ($val) {
                            case 'and':
                                $val = 'OR';
                                break;

                            case 'or':
                                $val = 'AND';
                                break;
                        }
                    }

                    $tmp['relation'] = strtoupper($val);
                    continue;
                }

                if (!taxonomy_exists($tax_key)) {
                    continue;
                }

                //***

                $tmp[] = array(
                    'taxonomy' => $tax_key,
                    'field' => 'term_id',
                    'terms' => explode(',', $val),
                    'operator' => $operator
                );
            }

            $args['tax_query'][] = $tmp;
        }

        return $args;
    }

}
