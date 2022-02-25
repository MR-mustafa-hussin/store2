<?php

/**
 * WOOT Filter
 *
 * Handles products filtration for shortcode [woot] on the front
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once WOOT_PATH . 'classes/filter-fields-options.php';

class WOOT_Filter {

    public $fields_options = null;

    public function __construct() {
        $this->fields_options = new WOOT_FilterFieldsOptions();
    }

    /**
     * Handles filter data
     *
     * @since 1.0.0
     * @param array $filter_data
     * @param array $args
     * @return array wp_query arguments
     */
    public function provider($filter_data, $args = []) {

        if (!empty($filter_data) AND is_array($filter_data)) {
            $table_id = 0;
            if (isset($_REQUEST['table_id']) AND intval($_REQUEST['table_id']) > 0) {
                $table_id = intval($_REQUEST['table_id']);
            }

            $profile = apply_filters(WOOT::get_table_action($table_id), $table_id);
            global $wp_taxonomies;


            foreach ($filter_data as $fkey => $value) {
                if (in_array($fkey, array_keys($wp_taxonomies))) {
                    $args = $profile[$fkey]['get_filter_query_args']($args, $value);
                } else {
                    if (isset($profile[$fkey])) {
                        $args = $profile[$fkey]['get_filter_query_args']($args, $value);
                    } else {
                        //for calendar
                        if (strlen($fkey) > 5) {
                            if (substr_count(strrev($fkey), 'ot---', 0, 5)) {
                                $fkey = str_replace('---to', '', $fkey);
                                $args = $profile[$fkey]['get_filter_query_args']($args, $value, true);
                            }
                        }
                    }
                }
            }
        }

        return $args;
    }

    /**
     * Assemble taxonomies terms for filter form
     *
     * @since 1.0.0
     * @param string $taxonomy
     * @param int $table_id
     * @return array
     */
    public function get_taxonomy_drawing_data($taxonomy, $table_id = 0) {
        global $wp_taxonomies;
        $filter_data = [];

        if (isset($wp_taxonomies[$taxonomy])) {

            $terms = $this->get_terms($taxonomy, 0);

            if (!empty($terms)) {

                $view = 'select';
                if ($table_id > 0 AND $this->fields_options->get($table_id, "{$taxonomy}-as-mselect")) {
                    $view = 'mselect';
                }

                $filter_data = [
                    'title' => WOOT_Vocabulary::get($wp_taxonomies[$taxonomy]->label),
                    'view' => $view,
                    'options' => []
                ];

                $include_only = [];
                $exclude = [];

                if ($table_id > 0) {
                    if ($include_only = $this->fields_options->get($table_id, "{$taxonomy}-include")) {
                        $include_only = array_map(function($id) {
                            return intval($id);
                        }, explode(',', $include_only));
                    } else {
                        $include_only = [];
                    }

                    if ($exclude = $this->fields_options->get($table_id, "{$taxonomy}-exclude")) {
                        $exclude = array_map(function($id) {
                            return intval($id);
                        }, explode(',', $exclude));
                    } else {
                        $exclude = [];
                    }
                }

                $filter_data['options'] = $this->build_taxonomy_tree($taxonomy, $terms, $exclude, $include_only);
            }
        }

        return $filter_data;
    }

    /**
     * Assembles taxonomies terms tree for filter form
     *
     * @since 1.0.0
     * @param string $taxonomy
     * @param array $terms
     * @param array $exclude exclude terms ids
     * @param array $include_only include only terms ids
     * @param array $options
     * @param int $level level of deep
     * @return array
     */
    public function build_taxonomy_tree($taxonomy, $terms, $exclude, $include_only, $options = [], $level = 0) {

        foreach ($terms as $t) {
            if (is_object($t)) {

                if (!empty($exclude) AND is_array($exclude)) {
                    if (in_array($t->term_id, $exclude)) {
                        continue;
                    }
                }

                if (!empty($include_only) AND is_array($include_only)) {
                    if (!in_array($t->term_id, $include_only)) {
                        continue;
                    }
                }

                $options[] = [
                    'id' => $t->term_id,
                    'name' => $t->name,
                    'slug' => $t->slug,
                    'title' => str_repeat('-', $level) . ' ' . WOOT_Vocabulary::get($t->name)
                ];

                $child_terms = $this->get_terms($taxonomy, $t->term_id);

                if ($child_terms) {
                    $options = $this->build_taxonomy_tree($taxonomy, $child_terms, $exclude, $include_only, $options, $level + 1);
                }
            }
        }

        return $options;
    }

    /**
     * Get taxonomies terms
     *
     * @since 1.0.0
     * @param string $taxonomy
     * @param int $parent_id
     * @return array
     */
    public function get_terms($taxonomy, $parent_id) {
        return get_terms(array(
            'taxonomy' => $taxonomy,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
            'parent' => $parent_id
        ));
    }

    /**
     * Filter form data for table js filter
     *
     * @since 1.0.0
     * @param array $filter_args
     * @param int $table_id
     * @return string JSON
     */
    public function draw_filter_form_data($filter_args, $table_id) {

        if ($table_id > 0 AND empty($filter_args)) {
            $ak = woot()->columns->filter->get_acceptor_keys($table_id);
            if (is_array($ak) AND!empty($ak)) {
                $filter_args = implode(',', $ak);
            }
        }

        $profile = apply_filters(WOOT::get_table_action($table_id), $table_id);

        if (!empty($filter_args)) {
            $filter_args = explode(',', $filter_args);
            $filter_data = [];

            if (!empty($filter_args) AND is_array($filter_args)) {
                foreach ($filter_args as $item) {
                    if (isset($profile[$item]['get_filter_draw_data'])) {
                        $filter_data[$item] = $profile[$item]['get_filter_draw_data']($table_id);
                    }
                }

                //lets order it as described in $filter_args
                $tmp = [];
                foreach ($filter_args as $k) {

                    if (isset($filter_data[$k])) {
                        $tmp[$k] = $filter_data[$k];

                        //for meta calendars, ---to is just for data range transmitting and not exists
                        if (isset($tmp[$k]['view']) AND $tmp[$k]['view'] === 'calendar') {
                            $tmp[$k . '---to'] = $tmp[$k];
                            $placeholder = $tmp[$k]['placeholder'];
                            $tmp[$k]['title'] = $tmp[$k]['placeholder'] = WOOT_Vocabulary::get(sprintf(esc_html__('From: %s', 'profit-products-tables-for-woocommerce'), $placeholder));
                            $tmp[$k . '---to']['title'] = $tmp[$k . '---to']['placeholder'] = WOOT_Vocabulary::get(sprintf(esc_html__('To: %s', 'profit-products-tables-for-woocommerce'), $placeholder));
                        }
                    }
                }

                $filter_data = $tmp;
            }

            //***
            $filter_data = array_slice($filter_data, 0, 2);
            return json_encode($filter_data);
        }

        return '';
    }

    /**
     * Get one field data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_key
     * @return array
     */
    public function get_field_drawing_data($table_id, $field_key) {
        static $available_fields = []; //use cached data to avoid the same mysql queries
        if (empty($available_fields)) {
            $available_fields = apply_filters(WOOT::get_table_action($table_id), $table_id);
        }

        $res = [];

        if (isset($available_fields[$field_key])) {
            $field_options = $available_fields[$field_key]['filter_options'];

            if (isset($available_fields[$field_key]['filter_view'])) {
                $res['view'] = $available_fields[$field_key]['filter_view'];
            }

            if (!empty($field_options)) {
                foreach ($field_options as $option_key) {

                    switch ($option_key) {
                        case 'min':
                        case 'max':

                            $value = -1;

                            if ($table_id > 0) {
                                $value = $this->fields_options->get($table_id, "{$field_key}-{$option_key}");
                            }

                            //meta range
                            if (isset($available_fields[$field_key]['meta_key'])) {
                                if (!is_numeric($value) OR intval($value) === -1) {
                                    global $wpdb;
                                    $sql = "SELECT {$option_key}(cast(meta_value as UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key='%s'";
                                    $query = $wpdb->prepare($sql, $available_fields[$field_key]['meta_key']);
                                    $value = $wpdb->get_var($query);
                                }
                            }

                            $res[$option_key] = $value;
                            break;

                        default:
                            $res[$option_key] = WOOT_Vocabulary::get($this->fields_options->get($table_id, "{$field_key}-{$option_key}"));
                            break;
                    }
                }
            }
        }


        //for filter navigation items

        if (!isset($res['title']) OR!boolval(trim($res['title']))) {
            $profile = apply_filters(WOOT::get_table_action($table_id), $table_id);
            $res['title'] = WOOT_Vocabulary::get($profile[$field_key]['title']);
        }

        if (!isset($res['placeholder']) OR empty($res['placeholder'])) {
            $res['placeholder'] = $res['title'];
        }

        return $res;
    }

}
