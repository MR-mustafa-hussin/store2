<?php

/**
 * WOOT Helper
 *
 * Handles helper general functions
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOT_HELPER {

    /**
     * Draw HTML item
     *
     * @since 1.0.0
     * @param string $type html tag: a, div, p, select, etc ...
     * @param array $data tag attributes
     * @param string $content tag content
     * @return string html of an element
     */
    public static function draw_html_item($type, $data, $content = '') {
        $item = '<' . $type;
        foreach ($data as $key => $value) {
            if (is_string($key) AND is_scalar($value)) {
                $item .= " {$key}='{$value}'";
            }
        }

        if (!empty($content) OR in_array($type, array('textarea'))) {
            $item .= '>' . $content . "</{$type}>";
        } else {
            $item .= ' />';
        }

        return $item;
    }

    /**
     * Draw <select> item
     *
     * @since 1.0.0
     * @param string $type draw <select>
     * @param array $attributes <select> attributes
     * @param array $options <options>s
     * @param string $selected selected key
     * @param array $options_attributes <options>s attributes
     * @param boolean $value_as_key option value the same as the option key
     * @return string html of a <select>
     */
    public static function draw_select($attributes, $options, $selected = '', $options_attributes = [], $value_as_key = false) {
        $select = '<div class="woot-select-wrap"><select';
        foreach ($attributes as $key => $value) {
            $select .= " {$key}='{$value}'";
        }
        $select .= '>';

//***

        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $content = '';
        if (!empty($options) AND is_array($options)) {
            foreach ($options as $key => $value) {
                $data_color = '';
                if (isset($options_attributes[$key]) AND!empty($options_attributes[$key])) {
                    if (isset($options_attributes[$key]['color']) AND $options_attributes[$key]['color']) {
                        $data_color = "data-color='{$options_attributes[$key]['color']}'";
                    }
                }

                $option_value = $key;

                if ($value_as_key) {
                    $option_value = $value;
                }

                $content .= '<option ' . self::selected(in_array($option_value, $selected)) . ' ' . $data_color . ' value="' . $option_value . '" title="' . $value . '">' . $value . '</option>';
            }
        }

        $select .= $content . '</select></div>';
        return $select;
    }

    /**
     * Is selected
     *
     * @since 1.0.0
     * @param boolean $is_selected is selected
     * @param boolean $echo return if false
     * @return string
     */
    private static function selected($is_selected, $echo = false) {
        if ($is_selected) {
            if ($echo) {
                echo 'selected';
            } else {
                return 'selected';
            }
        }
    }

    /**
     * Draw HTML switcher
     *
     * @since 1.0.0
     * @param string $name name
     * @param boolean $is_checked is checked
     * @param int $page_id item ID (page_id)
     * @param string $event triggered js event
     * @param array $custom_ajax_data for triggered js event
     * @return string html
     */
    public static function draw_switcher($name, $is_checked, $page_id, $event = '', $custom_ajax_data = []) {
        $id = uniqid();
        $checked = 'data-n';
        $is_checked = boolval(intval($is_checked) > 0);

        if ($is_checked) {
            $checked = 'checked';
        }

        return '<div>' . self::draw_html_item('input', array(
                    'type' => 'hidden',
                    'name' => $name,
                    'value' => $is_checked ? 1 : 0
                )) . self::draw_html_item('input', array(
                    'type' => 'checkbox',
                    'id' => $id,
                    'class' => 'switcher23',
                    'value' => $is_checked ? 1 : 0,
                    $checked => $checked,
                    'data-post-id' => $page_id,
                    'data-event' => $event,
                    'data-custom-data' => count($custom_ajax_data) ? json_encode($custom_ajax_data) : ''
                )) . self::draw_html_item('label', array(
                    'for' => $id,
                    'class' => 'switcher23-toggle'
                        ), '<span></span>') . '</div>';
    }

    /**
     * String lower
     *
     * @since 1.0.0
     * @param string $string
     * @return string
     */
    public static function strtolower($string) {
        if (function_exists('mb_strtolower')) {
            $string = mb_strtolower($string, 'UTF-8');
        } else {
            $string = strtolower($string);
        }

        return $string;
    }

    /**
     * Render HTML based ob view file
     *
     * @since 1.0.0
     * @param string $pagepath path to view file
     * @param array $data amy data to render
     * @param boolean $with_root if is true - files will be taken from folder 'views'
     * @return string rendered in HTML data
     */
    public static function render_html($pagepath, $data = array(), $with_root = true) {

        if (is_array($data) AND!empty($data)) {
            if (isset($data['pagepath'])) {
                unset($data['pagepath']);
            }
            extract($data);
        }

//***

        ob_start();
        if ($with_root) {
            $pagepath = WOOT_PATH . $pagepath;
        }
        include(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath));
        return ob_get_clean();
    }

    /**
     * Wrapping string into flow container
     *
     * @since 1.0.0
     * @param string $text
     * @param string $header_txt text header
     * @return string
     */
    public static function wrap_text_to_container($text, $header_txt) {
        return '<div class="woot-more-less-container" onclick="return woot_open_txt_container(this)"><div><strong>' . $header_txt . '</strong>' . $text . '<a href="#" onclick="return woot_close_txt_container(this, event); void(0);" class="woot-more-less-container-closer">X</a></div></div>';
    }

    /**
     * Importing any MySQL data
     *
     * @since 1.0.0
     * @param string $table
     * @param array $data
     * @return void
     */
    public static function import_mysql_table($table, $data) {
        global $wpdb;

        $wpdb->query("TRUNCATE TABLE {$table}");
        if (!empty($data)) {
            foreach ($data as $row) {
                $rd = [];
                foreach ($row as $key => $value) {
                    $rd[$key] = $value;
                }

                $wpdb->insert($table, $rd);
            }
        }
    }

    /**
     * Get data from link sended by iframe (remote page)
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public static function get_link_data() {
        $res = [];

        if (isset($_REQUEST['woot_link_get_data']) AND!empty($_REQUEST['woot_link_get_data'])) {
            $res = WOOT_HELPER::sanitize_array(json_decode(stripslashes($_REQUEST['woot_link_get_data']), true));
        }

        return (array) $res;
    }

    /**
     * Is user can change/manage data
     *
     * @since 1.0.0
     * @param int $user_id user ID
     * @return boolean
     */
    public static function can_manage_data($user_id = 0) {

        if ($user_id === 0) {
            $user = wp_get_current_user();
        } else {
            $user = get_userdata($user_id);
        }

        if (in_array('administrator', $user->roles) OR count(array_intersect($user->roles, explode(',', (string) WOOT_Settings::get('user_roles_can'))))) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * String sanitizer
     *
     * @since 1.0.0
     * @param string $string
     * @return string
     */
    public static function sanitize_text($string) {

        if (is_string($string)) {
            $string = preg_replace('/[\n\r]/', '', trim(strip_tags($string)));
        }

        return $string;
    }

    public static function sanitize_array($array) {

        if (is_array($array) AND!empty($array)) {
            foreach ($array as $key => $data) {
                if (is_array($data)) {
                    $array[$key] = self::sanitize_array($data);
                } else {
                    $array[$key] = sanitize_text_field($data);
                }
            }
        }

        return $array;
    }

    /**
     * Get users roles
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function get_roles_can_manage() {
        global $wp_roles;
        $roles = [];

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('manage_options', $all_roles);

        if (!empty($editable_roles)) {
            foreach ($editable_roles as $key => $r) {
                $roles[$key] = $r['name'];
            }
        }

        unset($roles['administrator']);
        unset($roles['subscriber']);
        unset($roles['customer']);

        return $roles;
    }

}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/**
 * Fields profile registration
 *
 * @since 1.0.0
 * @param string $profile_key
 * @param string $profile_title
 * @return void
 */
function woot_register_profile($profile_key, $profile_title = 'Custom profile') {
    if (!isset($GLOBALS['active_tables_actions'])) {
        $GLOBALS['active_tables_actions'] = [];
    }
    $GLOBALS['active_tables_actions'][$profile_key] = $profile_title;
}

/**
 * Fields profile registration - important function for profile data
 *
 * @since 1.0.0
 * @param int $table_id table ID
 * @param array $shortcode_args
 * @param string $current_action
 * @return array
 */
function woot_profiles_data_processor($table_id, $shortcode_args, $current_action) {
    $post_type = 'post';
    $post_statuses = 'publish';

    if ($table_id > 0) {
        $post_type = woot()->tables->get($table_id)['post_type'];
        $post_statuses = woot()->columns->options->get($table_id, 'post_statuses', 'publish');
    } else {
//for shortcodes
        if (isset($shortcode_args['post_type'])) {
            $post_type = $shortcode_args['post_type'];
        }
    }

    if (!$post_type) {
        $post_type = 'post';
    }

    if (!$post_statuses) {
        $post_statuses = 'publish';
    }

//***

    add_filter("ext_{$current_action}", function ($profile) use ($post_type, $shortcode_args) {

        //todo

        return $profile;
    });

    return ['post_type' => $post_type, 'post_statuses' => $post_statuses];
}

/**
 * Calendar names overloading
 *
 * @since 1.0.0
 * @param array $names calendar names
 * @return array
 */
add_filter('woot_get_calendar_names', function ($names) {
    return [
'month_names' => [
    WOOT_Vocabulary::get(esc_html__('January', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('February', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('March', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('April', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('May', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('June', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('July', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('August', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('September', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('October', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('November', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('December', 'profit-products-tables-for-woocommerce'))
],
 'month_names_short' => [
    WOOT_Vocabulary::get(esc_html__('Jan', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Feb', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Mar', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Apr', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('May', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Jun', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Jul', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Aug', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Sep', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Oct', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Nov', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Dec', 'profit-products-tables-for-woocommerce'))
],
 'day_names' => [
    WOOT_Vocabulary::get(esc_html__('Mo', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Tu', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('We', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Th', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Fr', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Sa', 'profit-products-tables-for-woocommerce')),
    WOOT_Vocabulary::get(esc_html__('Su', 'profit-products-tables-for-woocommerce'))
]
    ];
});

