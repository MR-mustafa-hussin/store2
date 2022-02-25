<?php

/**
 * WOOT Settings
 *
 * Handles system settings
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_Settings {

    private static $key = 'woot_settings';
    public $action = 'woot_settings_table';

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
            wp_enqueue_script('woot-settings', WOOT_ASSETS_LINK . 'js/admin/settings.js', ['woot-generated-tables'], WOOT_VERSION, true);
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
            add_action('wp_ajax_woot_save_settings_field', array($this, 'save'));

            //custom CSS
            add_action('wp_ajax_woot_save_table_custom_css', function() {
                woot()->tables->update_field(intval($_REQUEST['table_id']), 'custom_css', WOOT_HELPER::sanitize_text($_REQUEST['value']));
                exit;
            });

            add_action('wp_ajax_woot_get_table_custom_css', function() {
                die(self::get_table_custom_css(intval($_REQUEST['table_id'])));
            });

            //***
            //SHOW BUTTON ON THE TOP OF ADMIN PANEL
            add_action('admin_bar_menu', function($wp_admin_bar) {
                if (intval(self::get('show_btn_in_admin_bar'))) {
                    $args = array(
                        'id' => 'woot-btn',
                        'title' => 'WOOT',
                        'href' => admin_url('admin.php?page=woot'),
                        'meta' => array(
                            'class' => 'wp-admin-bar-woot-btn',
                            'title' => 'WOOT - WooCommerce Products Tables'
                        )
                    );
                    $wp_admin_bar->add_node($args);
                }
            }, 250);
        }
    }

    /**
     * Get CSS code for admin editor
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return string
     */
    static public function get_table_custom_css($table_id) {
        if (woot()->tables->get($table_id)) {
            return woot()->tables->get($table_id)['custom_css'];
        }

        return '';
    }

    /**
     * Get CSS code for front
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $table_html_id table HTML id
     * @return string
     */
    static public function get_table_custom_prepared_css($table_id, $table_html_id) {
        $css = self::get_table_custom_css($table_id);

        if (!empty($css)) {
            $css = "/* TABLE CUSTOM CSS */ #{$table_html_id} " . $css;
            $css = str_replace('}' . PHP_EOL, "} #{$table_html_id} ", $css);
            $css = stripslashes($css);
        }
        return $css;
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
     * Draw table on admin panel
     *
     * @since 1.0.0
     * 
     * @return string
     */
    public function draw_table() {
        $table_html_id = 'woot-settings-table';

        return woot()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'per_page_position' => 'none',
                    'per_page_sel_position' => 'none',
                    'per_page' => -1,
                    'table_data' => $this->get_rows(),
                    'use_flow_header' => 0,
                        ], $table_html_id, 'woot-settings-json-data') . WOOT_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $table_html_id,
                    'hide_text_search' => true,
                    'table_view' => 'separated'
        ));
    }

    /**
     * Get settings fields
     *
     * @since 1.0.0
     * 
     * @return array
     */
    private function get_rows() {
        $table_id = 0;

        $rows = [
            [
                'id' => $table_id,
                'title' => esc_html__('Show button in admin bar', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('show_btn_in_admin_bar', self::get('show_btn_in_admin_bar'), $table_id, 'woot_save_settings_field'),
                'notes' => esc_html__('Enable/Disable WOOT button on the top admin bar. Button will appear/disappear after the page reloading!', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Languages', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => self::get('languages'),
                    'custom_field_key' => 'languages'
                ],
                'notes' => esc_html__('Languages for tab Vocabulary. This options created for dynamic words and such plugins as WPML. Use the comma to add languages you want to use on the site front. By default: -1. Example: en_US,fr_FR,es_ES,de_DE,ru_RU. After the field change, settings of Vocabulary will appear after the page reloading.', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Fonts', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => self::get('fonts'),
                    'custom_field_key' => 'fonts'
                ],
                'notes' => esc_html__('For the columns options. Use fonts which are represented in the current WordPress theme. Use comma and do not use quotes! Example: Helvetica,Roboto', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Thumbnail size', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => self::get('thumbnail_size'),
                    'custom_field_key' => 'thumbnail_size'
                ],
                //'value_custom_field_key' => 'thumbnail_size',
                'notes' => esc_html__('Default size of products thumbnails (px)', 'profit-products-tables-for-woocommerce')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Wipe all data', 'profit-products-tables-for-woocommerce'),
                'value' => WOOT_HELPER::draw_switcher('delete_db_tables', self::get('delete_db_tables'), $table_id, 'woot_save_settings_field'),
                'notes' => esc_html__('Enable this option ONLY if you are going to uninstall WOOT completely and forever. For updates keep it disabled.', 'profit-products-tables-for-woocommerce')
            ]
        ];


        $user = wp_get_current_user();
        if (in_array('administrator', $user->roles)) {
            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Users roles can manage', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => WOOT_HELPER::draw_select([
                        'class' => 'woot-multiple-select',
                        'multiple' => '',
                        'data-action' => 'woot_save_settings_field',
                        'data-values' => self::get('user_roles_can')
                            ], WOOT_HELPER::get_roles_can_manage(), explode(',', self::get('user_roles_can'))),
                    'custom_field_key' => 'user_roles_can'
                ],
                'notes' => esc_html__('What users roles (together with administrator) can manage WOOT functionality', 'profit-products-tables-for-woocommerce')
            ];
        }


        return apply_filters('woot_extend_settings', $rows);
    }

    //*******************************************************************************

    /**
     * Get value by setting key, or get all settings if $key == null
     *
     * @since 1.0.0
     * @param string $key table ID
     * @return array | string
     */
    public static function get($key = null) {

        $defaults = apply_filters('woot_extend_settings_default', [
            'show_btn_in_admin_bar' => 1,
            'thumbnail_size' => 40,
            'fonts' => ''
        ]);

        $settings = get_option(self::$key, []);

        if ($settings AND!is_array($settings)) {
            $settings = json_decode($settings, true);
        }

        $settings = array_merge($defaults, $settings);


        if ($key) {
            if (isset($settings[$key])) {
                return $settings[$key];
            } else {
                return -1;
            }
        }

        return $settings;
    }

    /**
     * Save setting field value
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {
        $settings = self::get();

        if (is_int($_REQUEST['value'])) {
            $value = intval($value);
        } else {
            $value = WOOT_HELPER::sanitize_text($_REQUEST['value']);
        }

        $settings[WOOT_HELPER::sanitize_text($_REQUEST['field'])] = $value;

        update_option(self::$key, $settings);

        die(json_encode([
            'value' => $value
        ]));
    }

}
