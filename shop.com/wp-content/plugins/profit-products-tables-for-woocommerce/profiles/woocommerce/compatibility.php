<?php

/**
 * Compatibility with woocommerce products filters
 * *
 * @see https://products-tables.com/hook/woot_filter_provider_/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter("woot_profile_extend", function($profile, $action, $shortcode_args) {

    if ('woot_woocommerce_tables' === $action) {

        if (isset($shortcode_args['filter_provider'])) {

            if ($shortcode_args['filter_provider'] === 'woof') {
                if (array_key_exists('WOOF', $GLOBALS)) {
                    global $WOOF;
                    $profile[0]['filter_provider'] = 'woof';
                    $profile[0]['filter_data'] = $WOOF->get_request_data();


                    add_action('woot_filter_provider_woof', function ($args, $filter_data) {
                        if (!is_array($filter_data)) {
                            $filter_data = json_decode($filter_data, true);
                        }

                        if (!empty($filter_data) AND is_array($filter_data)) {
                            foreach (array_keys($filter_data) as $key) {
                                if (is_integer($key)) {
                                    unset($filter_data[$key]);
                                }
                            }
                        }

                        //***
                        //here is clean WOOF functionality
                        global $WOOF;
                        if ((isset($filter_data[$WOOF->get_swoof_search_slug()]) OR isset($filter_data['min_price'])) AND count($filter_data) > 1) {

                            unset($filter_data['paged']);
                            $_GET = $filter_data;
                            $_REQUEST['perpage'] = -1; //for getting all products ids in WOOF predict filtration

                            if (isset($filter_data['post_title']) AND!empty($filter_data['post_title'])) {
                                woot()->filter->provider($filter_data);
                            }

                            $WOOF->woof_products_ids_prediction(true);
                            $ids = WOOT_HELPER::sanitize_array($_REQUEST['woof_wp_query_ids']);

                            if ($ids) {
                                $args['post__in'] = $ids;
                            } else {
                                //unset($args['post__in']);
								$args['post__in']=array(0);
                            }
                        }


                        return $args;
                    }, 10, 2);
                }
            }

            //+++

            if ($shortcode_args['filter_provider'] === 'mdtf') {
                if (isset($_REQUEST['page_mdf']) AND!empty($_REQUEST['page_mdf'])) {
                    $profile[0]['filter_provider'] = 'mdtf';

                    $_GLOBALS['MDF_META_DATA_FILTER'] = json_decode(base64_decode($_REQUEST['page_mdf']), true);
                    $_REQUEST['mdf_do_not_render_shortcode_tpl'] = true;
                    $_REQUEST['mdf_get_query_args_only'] = true;
                    do_shortcode('[meta_data_filter_results]');
                    $args = WOOT_HELPER::sanitize_array($_REQUEST['meta_data_filter_args']);
                    $args['posts_per_page'] = -1;
                    $args['fields'] = 'ids';

                    if (isset($args['meta_query'])) {
                        if (!empty($args['meta_query'])) {
                            foreach ($args['meta_query'] as $key => $m) {
                                if (isset($m['key'])) {
                                    if ($m['key'] === 'mdf_hide_post') {
                                        unset($args['meta_query'][$key]);
                                    }
                                }
                            }
                        }
                    }


                    if (isset($args['tax_query'])) {
                        if (!empty($args['tax_query'])) {
                            foreach ($args['tax_query'] as $key => $t) {
                                if (isset($t['taxonomy'])) {
                                    if ($t['taxonomy'] === 'product_visibility') {
                                        unset($args['tax_query'][$key]);
                                    }
                                }
                            }
                        }
                    }

                    if (isset($args['ignore_sticky_posts'])) {
                        unset($args['ignore_sticky_posts']);
                    }

                    unset($args['orderby']);
                    unset($args['order']);

                    //***
                    $profile[0]['filter_data'] = [];
                    $profile[0]['filter_data']['ids'] = (new WP_Query($args))->posts;
                }

                //***

                add_action('woot_filter_provider_mdtf', function ($args, $filter_data) {

                    if (!is_array($filter_data)) {
                        $filter_data = json_decode($filter_data, true);
                    }

                    if (isset($filter_data['post_title']) AND!empty($filter_data['post_title'])) {
                        woot()->filter->provider($filter_data);
                    }

                    if (!empty($filter_data['ids'])) {
                        $args['post__in'] = $filter_data['ids'];
                    } else {
                        //$args['post__in'] = [-1];
                    }

                    return $args;
                }, 10, 2);
            }
        }
    }

    return $profile;
}, 10, 3);

