<?php

/**
 * Shortcode [woot_single]
 *
 * Generates a single product HTML table
 *
 * @see https://products-tables.com/shortcode/woot_single/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOT_WooCommerceSingle {

    public $setting_key = 'single_product_columns';
    private $action = 'woot_single_product';
    public $default_columns = 'title,value';

    public function __construct() {

        add_action('woot_extend_settings', function($rows) {

            $selected = explode(',', WOOT_Settings::get($this->setting_key));
            $options = [];

            foreach (apply_filters(WOOT_WooCommerce::$action, 0, []) as $key => $value) {
                if (isset($value['title'])) {
                    if ($key !== 'single') {
                        $options[$key] = $value['title'];
                    }
                }
            }

            //+++

            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Single', 'profit-products-tables-for-woocommerce'),
                'value' => [
                    'value' => WOOT_HELPER::draw_select([
                        'class' => 'woot-multiple-select',
                        'multiple' => '',
                        'data-action' => 'woot_save_settings_field',
                        'data-values' => WOOT_Settings::get($this->setting_key)
                            ], $options, $selected),
                    'custom_field_key' => $this->setting_key
                ],
                'notes' => esc_html__('Columns for table Single and shortcode [woot_single]. Press and wait to reorder.', 'profit-products-tables-for-woocommerce')
            ];

            return $rows;
        }, 10, 1);

        add_action('woot_extend_settings_default', function($defaults) {
            $defaults[$this->setting_key] = $this->default_columns;
            return $defaults;
        }, 10, 1);

        //***
        //profile
        add_action($this->action, function () {
            return apply_filters('woot_single_product_extend', [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Title', 'profit-products-tables-for-woocommerce'))
                ],
                'value' => [
                    'title' => WOOT_Vocabulary::get(esc_html__('Value', 'profit-products-tables-for-woocommerce'))
                ]
            ]);
        });

        //***

        add_filter('woot_get_table_single_product', function($what, $table_html_id) {
            $args = [];
            if (!empty($what['columns'])) {
                $args['columns'] = $what['columns'];
            }
            if (isset($what['skin'])) {
                $args['skin'] = $what['skin'];
            }

            $args['table_view'] = 'separated';

            return $this->draw_table(intval($what['product_id']), $table_html_id, $args);
        }, 10, 2);

        //***

        add_shortcode('woot_single', function($args) {
            woot()->include_assets();

            $args = (array) $args;

            $product_id = 0;

            if (!isset($args['columns'])) {
                $args['columns'] = WOOT_Settings::get($this->setting_key, $args);
            }

            if (isset($args['id'])) {
                $product_id = intval($args['id']);
                unset($args['id']);
            } else {
                global $product;
                if (is_object($product)) {
                    $product_id = $product->get_id();
                }
            }

            if (isset($args['table_id'])) {
                $args['id'] = intval($args['table_id']); //by code id is table id in [woot] shortcode
            }

            //***

            if ($product_id > 0 AND get_post_field('post_type', $product_id) === 'product') {
                return $this->draw_table($product_id, uniqid('t'), $args);
            } else {
                return '<div class="woot-notice">' . sprintf(esc_html__('Product #%s does not exists!', 'profit-products-tables-for-woocommerce'), $product_id) . '</div>';
            }
        });
    }

    /**
     * Get table HTML
     *
     * @since 1.0.0
     * @param int $product_id product ID
     * @param string $table_html_id table HTML id
     * @param array $args shortcode arguments
     * @return string table HTML
     */
    private function draw_table($product_id, $table_html_id, $args = []) {
        return WOOT_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $table_html_id,
                    'hide_text_search' => true,
                    'classes' => 'woot-data-table-self-call',
                    'style' => isset($args['skin']) ? woot()->skins->get_theme_css($args['skin'], $table_html_id) : '',
                    'skin' => isset($args['skin']) ? $args['skin'] : '',
                    'table_view' => isset($args['table_view']) ? $args['table_view'] : ''
                )) . woot()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'columns' => $this->default_columns,
                    'css_classes' => $this->action,
                    'per_page' => -1,
                    'per_page_position' => 'none',
                    'per_page_sel_position' => 'none',
                    'use_flow_header' => 0,
                    'table_data' => $this->get_table_data($product_id, $table_html_id, $args)
                        ], $table_html_id);
    }

    /**
     * Get table data
     *
     * @since 1.0.0
     * @param int $product_id product ID
     * @param string $table_html_id table HTML id
     * @param array $args shortcode arguments
     * @return array table data
     */
    private function get_table_data($product_id, $table_html_id = 0, $args = []) {
        $table_data = [];

        if ($product_id > 0) {

            $woo_profile = apply_filters(WOOT_WooCommerce::$action, NULL);

            if (isset($args['columns'])) {
                $columns = explode(',', $args['columns']);
            } else {
                $columns = explode(',', WOOT_Settings::get($this->setting_key));
            }

            $columns = array_slice($columns, 0, 7);

            if (!empty($columns)) {
                foreach ($columns as $field_key) {

                    $add = TRUE;
                    if (!isset($woo_profile[$field_key])) {
                        $add = FALSE;
                        if (isset(WOOT::$synonyms[$field_key])) {
                            $field_key = WOOT::$synonyms[$field_key];
                            $add = TRUE;
                        }
                    }

                    //***

                    if ($add) {
                        $value = $woo_profile[$field_key]['action']($product_id);

                        //***

                        $variations_as = 'button';
                        $upsell_as = 'button';
                        $cross_as = 'button';
                        $reviews_as = 'button';

                        $variations_per_page = $upsells_per_page = $cross_per_page = $reviews_per_page = 10;

                        $upsells_columns = $cross_columns = $variations_columns = '';

                        if (isset($args['variations_as'])) {
                            $variations_as = $args['variations_as'];
                            if (isset($args['variations_per_page'])) {
                                $variations_per_page = $args['variations_per_page'];
                            }
                        }

                        if (isset($args['variations_columns'])) {
                            $variations_columns = $args['variations_columns'];
                        }

                        if (isset($args['upsells_as'])) {
                            $upsell_as = $args['upsells_as'];
                            if (isset($args['upsells_per_page'])) {
                                $upsells_per_page = $args['upsells_per_page'];
                            }
                        }

                        if (isset($args['upsells_columns'])) {
                            $upsells_columns = $args['upsells_columns'];
                        }

                        if (isset($args['cross_as'])) {
                            $cross_as = $args['cross_as'];
                            if (isset($args['cross_per_page'])) {
                                $cross_per_page = $args['cross_per_page'];
                            }
                        }

                        if (isset($args['cross_columns'])) {
                            $cross_columns = $args['cross_columns'];
                        }

                        if (isset($args['reviews_as'])) {
                            $reviews_as = $args['reviews_as'];
                            if (isset($args['reviews_per_page'])) {
                                $reviews_per_page = $args['reviews_per_page'];
                            }
                        }

                        //***

                        if ($field_key === 'variations' AND $variations_as === 'table') {
                            $value = do_shortcode("[woot_variations id={$product_id} per_page={$variations_per_page}]");
                        }

                        if ($field_key === 'upsell' AND $upsell_as === 'table') {
                            $value = do_shortcode("[woot_upsells id={$product_id} per_page={$upsells_per_page} columns='{$upsells_columns}']");
                        }

                        if ($field_key === 'cross_sell' AND $cross_as === 'table') {
                            $value = do_shortcode("[woot_cross_sells id={$product_id} per_page={$cross_per_page}]");
                        }

                        if ($field_key === 'average_rating' AND $reviews_as === 'table') {
                            $value = do_shortcode("[woot_reviews id={$product_id} per_page={$reviews_per_page}]");
                        }

                        if ($field_key === 'add_to_cart') {
                            $table_data[] = [
                                'title' => "<b data-key='add_to_cart'>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        } elseif ($field_key === 'favourites') {
                            $table_data[] = [
                                'title' => "<b data-key='favourites'>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        } elseif ($field_key === 'compare') {
                            $table_data[] = [
                                'title' => "<b data-key='compare'>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        } else {
                            $table_data[] = [
                                'title' => "<b>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        }
                    }
                }
            }

            //***

            return $table_data;
        }

        return $table_data;
    }

}

new WOOT_WooCommerceSingle();
