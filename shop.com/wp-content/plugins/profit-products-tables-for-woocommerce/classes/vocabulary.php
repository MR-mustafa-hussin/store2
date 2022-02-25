<?php

/**
 * WOOT Vocabulary
 *
 * Handles system dynamic words and sentences
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_Vocabulary {

    public static $translations = [];
    public $action = 'woot_vocabulary_table';
    private $db_table = 'woot_vocabulary';
    private $db = null;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->db_table = $this->db->prefix . $this->db_table;

        //WPML compatibility
        add_filter('woot_current_lang', function($lang) {
            if (class_exists('SitePress')) {
                global $sitepress;
                $sitepress->switch_lang(substr($lang, 0, 2), true);
            }

            return $lang;
        });

        self::$translations = $this->get_data();
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
            wp_enqueue_script('woot-vocabulary', WOOT_ASSETS_LINK . 'js/admin/vocabulary.js', ['woot-generated-tables'], WOOT_VERSION, true);
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
            add_action('wp_ajax_woot_save_vocabulary_field', array($this, 'save'));
            add_action('wp_ajax_woot_create_vocabulary_field', array($this, 'create'));
            add_action('wp_ajax_woot_delete_vocabulary_field', array($this, 'delete'));

            //***

            add_action($this->action, function () {
                $profile = [
                    0 => [
                    //'ajax_action' => ''
                    ],
                    'title' => [
                        'title' => esc_html__('Key Word', 'profit-products-tables-for-woocommerce'),
                        'editable' => 'textinput',
                        'order' => 'asc'
                    ]
                ];

                if (self::is_enabled()) {
                    $languages = explode(',', WOOT_Settings::get('languages'));
                    if (!empty($languages)) {
                        foreach ($languages as $key) {
                            $key = trim($key);
                            $profile[$key] = [
                                'title' => $key,
                                'editable' => 'textinput',
                                'order' => 'asc'
                            ];
                        }
                    }
                }


                $profile['delete'] = [
                    'title' => 'X'
                ];

                return $profile;
            });
        }
    }

    /**
     * Is languages for table vocabulary defined
     *
     * @since 1.0.0
     * 
     * @return boolean
     */
    public static function is_enabled() {
        return intval(WOOT_Settings::get('languages')) > -1 AND!empty(WOOT_Settings::get('languages'));
    }

    /**
     * Draw Vocabulary table on admin side
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function draw_table() {
        $table_html_id = 'woot-vocabulary-table';

        echo WOOT_HELPER::render_html('views/table.php', array(
            'table_html_id' => $table_html_id,
            'hide_text_search' => false,
            'text_search_min_symbols' => 1,
            'placeholder' => esc_html__('search by keyword', 'profit-products-tables-for-woocommerce') . ' ...',
            'table_view' => 'separated'
        )) . woot()->draw_table_data([
            'mode' => 'json',
            'action' => $this->action,
            'orderby' => 'title',
            'order' => 'asc',
            'per_page_position' => 'tb',
            'per_page_sel_position' => 't',
            'per_page' => 10,
            'table_data' => array_values(self::$translations)
                ], $table_html_id, 'woot-vocabulary-json-data');
    }

    /**
     * Get Vocabulary all data
     *
     * @since 1.0.0
     * 
     * @return array
     */
    private function get_data() {

        if ($this->db->get_var("SHOW TABLES LIKE '{$this->db_table}'") !== $this->db_table) {
            return []; //avoid notice while first activation before hook register_activation_hook
        }

        $sql = "SELECT * FROM {$this->db_table}";
        $res = $this->db->get_results($sql, ARRAY_A);

        //***

        $data = [];
        if (!empty($res)) {
            foreach ($res as $v) {
                $tmp = [
                    'pid' => $v['id'],
                    'title' => $v['title']
                ];

                $translations = [];

                if ($v['translations']) {
                    $translations = json_decode($v['translations'], true);
                    if (json_last_error() !== 0) {
                        $translations = [];
                    }
                }

                $languages = explode(',', WOOT_Settings::get('languages'));

                if (!empty($languages)) {
                    foreach ($languages as $lang) {
                        $tmp[$lang] = isset($translations[$lang]) ? $translations[$lang] : '';
                    }
                }

                $tmp['delete'] = WOOT_HELPER::draw_html_item('a', [
                            'href' => "javascript: woot_vocabulary_table.delete({$v['id']});void(0);",
                            'title' => esc_html__('delete', 'profit-products-tables-for-woocommerce'),
                            'class' => 'button woot-dash-btn-single'
                                ], '<span class="dashicons-before dashicons-no"></span>');

                $data[$v['title']] = $tmp;
            }
        }

        return $data;
    }

    /**
     * Save Vocabulary field data by ajax
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function save() {
        $post_id = intval($_REQUEST['post_id']);

        if ($post_id) {
            $field = 'title';
            $value = WOOT_HELPER::sanitize_text($_REQUEST['value']);

            if ($_REQUEST['field'] !== 'title') {
                $field = 'translations';
                $translations = ($this->db->get_row("SELECT {$field} FROM {$this->db_table} WHERE id = {$post_id}"))->translations;

                if (!$translations) {
                    $translations = [];
                } else {
                    $translations = json_decode($translations, true);
                    if (json_last_error() !== 0) {
                        $translations = [];
                    }
                }

                $translations[WOOT_HELPER::sanitize_text($_REQUEST['field'])] = WOOT_HELPER::sanitize_text($_REQUEST['value']);
                $value = json_encode($translations);
            }

            $this->db->update($this->db_table, [$field => $value], ['id' => $post_id]);
        }

        die('{}');
    }

    /**
     * Creat Vocabulary table row
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function create() {
        $this->db->insert($this->db_table, [
            'title' => '0 __' . __('new translate', 'profit-products-tables-for-woocommerce') . ' ' . WOOT_HELPER::sanitize_text($_REQUEST['tail'])
        ]);

        die(json_encode(array_values($this->get_data())));
    }

    /**
     * Delete Vocabulary table row
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function delete() {
        $this->db->delete($this->db_table, ['id' => intval($_REQUEST['id'])]);
        die(json_encode(array_values($this->get_data())));
    }

    /**
     * Wrapping dynamic (and some not dynamic) system words
     *
     * @since 1.0.0
     * @param string $title_key
     * @param string $lang
     * @return string
     */
    public static function get($title_key, $lang = '') {
        $res = $title_key;

        if (isset(self::$translations[$title_key]) AND!empty(self::$translations[$title_key])) {
            if (empty($lang)) {
                $lang = apply_filters('woot_current_lang', get_locale());
            }

            if (isset(self::$translations[$title_key][$lang])) {
                $res = self::$translations[$title_key][$lang];
            }
        }

        if (empty($res)) {
            $res = $title_key;
        }

        return $res;
    }

    /**
     * Get all existing words
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
