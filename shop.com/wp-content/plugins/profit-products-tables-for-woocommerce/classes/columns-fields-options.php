<?php

/**
 * WOOT Columns
 *
 * Handles a table column fields options
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_ColumnsFieldsOptions {

    public $table_html_id = 'woot_options_columns_table';
    public $action = 'woot_columns_fields_options_table';

    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'), 9999);

        //fields options popup data 
        add_filter('woot_show_column_field_option', function($what) {
            $res = WOOT_HELPER::render_html('views/table.php', array(
                        'table_html_id' => $this->table_html_id,
                        'hide_text_search' => true,
                        'table_view' => 'separated'
            ));

            //print_r($what);
            return $res . $this->get_columns_options(intval($what['table_id']), intval($what['field_id']));
        });
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
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('wp_ajax_woot_save_table_column_field_option', array($this, 'save'));
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
            wp_enqueue_script('woot-columns-fields-options', WOOT_ASSETS_LINK . 'js/admin/columns-fields-options.js', ['woot-generated-tables'], WOOT_VERSION, true);
        }
    }

    /**
     * Generate a column fields options
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_id field id
     * @return string html
     */
    public function get_columns_options($table_id, $field_id) {
        return woot()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'per_page_position' => 'none',
                    'per_page_sel_position' => 'none',
                    'per_page' => -1,
                    'use_flow_header' => 0,
                    'table_data' => $this->get_columns_options_data($table_id, $field_id)
                        ], $this->table_html_id);
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
                //'ajax_action' => 'woot_columns_fields_options_table'
                ],
                'title' => [
                    'title' => esc_html__('Title', 'profit-products-tables-for-woocommerce')
                ],
                'value' => [
                    'title' => esc_html__('Value', 'profit-products-tables-for-woocommerce')
                ],
                'notes' => [
                    'title' => esc_html__('Info', 'profit-products-tables-for-woocommerce')
                ]
            ];
        });
    }

    /**
     * Columns options data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_id field id
     * @return array
     */
    private function get_columns_options_data($table_id, $field_id) {
        $args = [
            'table_id' => $table_id,
            'field_id' => $field_id,
            'rows' => []
        ];

        $col = woot()->columns->get($field_id, ['field_key', 'options']);

        if ($col) {
            $field_key = $col['field_key'];
            $profile = apply_filters(WOOT::get_table_action($table_id), $table_id);
            if (isset($profile[$field_key]) AND isset($profile[$field_key]['options'])) {
                $options = $profile[$field_key]['options'];
                if (!empty($options)) {
                    foreach ($options as $option_key) {
                        $args = $this->options($option_key, $args);
                    }
                }
            }
        }

        return ['rows' => $args['rows'], 'count' => count($args['rows'])];
    }

    /**
     * Hook
     *
     * @since 1.0.0
     * @param int $option_key option key
     * @param array $args
     * @return array
     */
    public function options($option_key, $args) {
        $table_id = intval($args['table_id']);
        $field_id = intval($args['field_id']);
        $col = woot()->columns->get($field_id, ['field_key', 'options']);

        if ($col) {
            switch ($option_key) {
                case 'display_as_text':

                    $is_checked = $this->extract_from($col['options'], $option_key);
                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Display as text', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_switcher($option_key, $is_checked, $table_id . '_' . $field_id, 'woot_save_table_column_field_option'),
                        'notes' => esc_html__('If enabled, taxonomy terms will be displayed as text. By default: links.', 'profit-products-tables-for-woocommerce'),
                    ];


                    break;

                case 'title_as_text':

                    $value = $this->extract_from($col['options'], $option_key);
                    $ajax_data = [
                        'table_id' => $table_id,
                        'field_id' => $field_id,
                        'key' => $option_key
                    ];
                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Display as text', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_switcher($option_key, $value, $table_id, 'woot_save_table_column_field_option', $ajax_data),
                        'notes' => esc_html__('Display as text instead of link to the product', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'show_labels':

                    $value = $this->extract_from($col['options'], $option_key);
                    $ajax_data = [
                        'table_id' => $table_id,
                        'field_id' => $field_id,
                        'key' => $option_key
                    ];
                    $args['rows'][] = [
                        'id' => $table_id,
                        'title' => esc_html__('Show labels', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_switcher($option_key, $value, $table_id, 'woot_save_table_column_field_option', $ajax_data),
                        'notes' => esc_html__('If enabled in the title cell will be able to shown such labels as: featured, on sale, out of stock', 'profit-products-tables-for-woocommerce')
                    ];

                    break;

                case 'thumbnail_width':

                    $value = $this->extract_from($col['options'], $option_key);

                    if (!$value) {
                        $value = WOOT_Settings::get('thumbnail_size');
                    }

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Width', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option',
                            'type' => 'text',
                            'value' => $value,
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('Thumbnail width (px)', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'thumbnail_no_link':

                    $value = intval($this->extract_from($col['options'], $option_key));

                    $ajax_data = [
                        'table_id' => $table_id,
                        'field_id' => $field_id,
                        'key' => $option_key
                    ];

                    $args['rows'][] = [
                        'id' => $table_id,
                        'title' => esc_html__('No Link', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_switcher($option_key, $value, $table_id, 'woot_save_table_column_field_option', $ajax_data),
                        'notes' => esc_html__('If enabled thumbnail will be displayed without link to the single product', 'profit-products-tables-for-woocommerce')
                    ];

                    break;

                case 'thumbnail_preview_width':

                    $value = $this->extract_from($col['options'], $option_key);

                    if (!$value) {
                        $value = 400;
                    }

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Preview on hover', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option',
                            'type' => 'text',
                            'value' => $value,
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('Width of the preview when mouse hover on the thumbnail (px). Set -1 to disable.', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'css-font-size':

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Font size', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option',
                            'type' => 'text',
                            'value' => $this->extract_from($col['options'], $option_key),
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('Example: 16px OR 1.2em', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'css-font-family':

                    $fonts = array_map(function($item) {
                        return trim($item);
                    }, array_merge(['inherit'], (array) explode(',', stripslashes(WOOT_Settings::get('fonts')))));

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Font family', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_select([
                            'class' => 'woot-columns-field-option',
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                                ], $fonts, $this->extract_from($col['options'], $option_key), [], true),
                        'notes' => esc_html__('The column text font family. Include them in the tab Settings!', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'css-color':

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Color', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option woot-color-field',
                            'type' => 'text',
                            'value' => $this->extract_from($col['options'], $option_key),
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('Example: #00ff00', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'css-background':

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Background', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option',
                            'type' => 'text',
                            'value' => $this->extract_from($col['options'], $option_key),
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('Example: #000000 OR https://__IMG_URL__', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                case 'css-media-hide':

                    $args['rows'][] = [
                        'pid' => $table_id,
                        'title' => esc_html__('Hide on small screen', 'profit-products-tables-for-woocommerce'),
                        'value' => WOOT_HELPER::draw_html_item('input', [
                            'class' => 'woot-columns-field-option',
                            'type' => 'text',
                            'value' => $this->extract_from($col['options'], $option_key),
                            'data-table-id' => $table_id,
                            'data-field-id' => $field_id,
                            'data-key' => $option_key
                        ]),
                        'notes' => esc_html__('If you want to hide this column on small screens, set here width, lower which its will be hidden. Example: 500. Leave it empty to disable.', 'profit-products-tables-for-woocommerce'),
                    ];

                    break;

                default:

                    $option = apply_filters('woot_ext_column_fields_options', $option_key, $args);
                    if ($option) {
                        $args['rows'][] = $option;
                    }

                    break;
            }
        }


        return $args;
    }

    /**
     * Save by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {

        $d = [];

        if (isset($_REQUEST['post_id'])) {
            //for old versions of requests, should be remade in js's
            $d = explode('_', WOOT_HELPER::sanitize_text($_REQUEST['post_id']));
        }

        if (count($d) === 2) {
            $table_id = intval($d[0]);
            $field_id = intval($d[1]);
            $key = WOOT_HELPER::sanitize_text($_REQUEST['field']);
            $value = intval($_REQUEST['value']);
        } else {
            $table_id = intval($_REQUEST['table_id']);
            $field_id = intval($_REQUEST['field_id']);
            $key = WOOT_HELPER::sanitize_text($_REQUEST['key']);
            $value = WOOT_HELPER::sanitize_text($_REQUEST['value']);
        }

        //***

        $options = $this->get($field_id);
        $options[$key] = $value;
        $this->update($field_id, $options);

        die(json_encode(['value' => $value]));
    }

    /**
     * Get field options data
     *
     * @since 1.0.0
     * @param string $field_id field id
     * @return array
     */
    public function get($field_id) {
        $options = woot()->columns->get($field_id, ['options'])['options'];

        if (!$options) {
            $options = [];
        } else {
            $options = json_decode($options, true);
        }


        return $options;
    }

    /**
     * Get a table an option value
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_key
     * @param string $option_key
     * @return string
     */
    public function get_option($table_id, $field_key, $option_key) {

        static $options = []; //cache

        if (!isset($options[$table_id])) {

            $options[$table_id] = woot()->columns->get_by_field_key($table_id, $field_key)['options'];

            if (!$options[$table_id]) {
                $options[$table_id] = [];
            } else {
                $options[$table_id] = json_decode($options[$table_id], true);
            }
        }

        return isset($options[$table_id][$option_key]) ? $options[$table_id][$option_key] : NULL;
    }

    /**
     * Extract value from array or JSON string
     *
     * @since 1.0.0
     * @param string | array $value
     * @param string $key
     * @return mixed
     */
    public function extract_from($value, $key) {
        if (!empty($value) AND!is_array($value)) {
            $value = json_decode($value, true);
        }

        if (isset($value[$key])) {
            return $value[$key];
        }

        return NULL;
    }

    /**
     * Save field options
     *
     * @since 1.0.0
     * @param int $field_id
     * @param array $options
     * @return void
     */
    public function update($field_id, $options) {
        woot()->columns->update_field('options', $field_id, json_encode($options));
    }

}
