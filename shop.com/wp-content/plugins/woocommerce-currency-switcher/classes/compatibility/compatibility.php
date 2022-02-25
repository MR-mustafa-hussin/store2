<?php
//WooCommerce Advanced Shipping
//https://codecanyon.net/item/woocommerce-advanced-shipping/8634573
add_filter('wcml_shipping_price_amount', 'woof_compatibility_as');
function woof_compatibility_as($val) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        return $WOOCS->woocs_exchange_value(floatval($val));
    }

    return $val;
}


//Aramex Shipping WooCommerce
//https://wordpress.org/plugins/aramex-shipping-woocommerce/
add_filter('woocommerce_shipping_method_add_rate_args', function($arg, $obj) {
    if (stristr($arg['id'], '_aramex') !== FALSE AND $arg['cost']) {
        if (class_exists('WOOCS')) {
            global $WOOCS;
            if ($WOOCS->is_multiple_allowed) {
                $currrent = $WOOCS->current_currency;
                if ($currrent != $WOOCS->default_currency) {
                    $currencies = $WOOCS->get_currencies();
                    $rate = $currencies[$currrent]['rate'];
                    $arg['cost'] = $arg['cost'] / $rate;
                }
            }
        }
    }
    return $arg;
}, 2, 100);


//Dokan
//https://wedevs.com/dokan/
add_action('woocommerce_order_status_completed', 'woocs_payment_complete', 1);
add_action('woocommerce_payment_complete', 'woocs_payment_complete');
function woocs_payment_complete($order_id) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        $WOOCS->recalculate_order($order_id);
    }
}
add_filter('wp_head', function() {
    if (is_page('dashboard')) {
        if (class_exists('WOOCS')) {
            global $WOOCS;
            $WOOCS->reset_currency();
        }
    }
});


//German Market (Marketpress)
//https://marketpress.de/shop/plugins/woocommerce/woocommerce-german-market/
add_action('wp_wc_invoice_pdf_start_template', function( $args ) {
    remove_all_filters('woocommerce_currency_symbol');
});


//Subscriptio – WooCommerce Subscriptions (by RightPress)
//https://codecanyon.net/item/subscriptio-woocommerce-subscriptions/8754068
//add_filter('wcml_raw_price_amount', 'woocs_convert_fix_price');
//function woocs_convert_fix_price($price) {
//    if (class_exists('WOOCS')) {
//        global $WOOCS;
//        $price = $WOOCS->woocs_exchange_value($price);
//    }
//
//    return $price;
//}


//WC Fields Factory
//https://wordpress.org/plugins/wc-fields-factory/
add_filter('wcff_negotiate_price_after_calculation', function($price) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currencies = $WOOCS->get_currencies();
            $conversion_rate = $currencies[$WOOCS->current_currency]['rate'];
            $price = $price / $conversion_rate;
        }
    }
    return $price;
});


//WooCommerce Checkout Add-Ons (by WooCommerce)
//https://woocommerce.com/products/woocommerce-checkout-add-ons/
function wc_checkout_add_ons_woocs_currency_support($cost) {

    return apply_filters('woocs_exchange_value', $cost);
}
add_filter('wc_checkout_add_ons_add_on_get_cost', 'wc_checkout_add_ons_woocs_currency_support');

//WooCommerce Easy Booking
//https://wordpress.org/plugins/woocommerce-easy-booking-system/
add_filter( 'easy_booking_two_dates_price', 'currency_switcher_new_price', 2, 1 );
add_filter( 'easy_booking_one_date_price', 'currency_switcher_new_price', 2, 1 );
function currency_switcher_new_price( $booking_price ) {
    if ( class_exists('WOOCS') ) {
        global $WOOCS;

        $currencies = $WOOCS->get_currencies();
        $booking_price = $WOOCS->back_convert( $booking_price, $currencies[$WOOCS->current_currency]['rate'] );

    }

    return $booking_price;
}


//WooCommerce QuickPay (by Perfect Solution)
//https://wordpress.org/plugins/woocommerce-quickpay/
add_filter('woocommerce_quickpay_currency', function($currency, $order) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        $currency = $WOOCS->current_currency;
    }
    return $currency;
}, 10, 2);


//WooCommerce Smart COD
//https://wordpress.org/plugins/wc-smart-cod/
add_filter('wc_smart_cod_fee', function($fee, $settings) {
    if (class_exists('WOOCS') AND $fee) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currrent = $WOOCS->current_currency;
            if ($currrent != $WOOCS->default_currency) {
                $currencies = $WOOCS->get_currencies();
                $rate = $currencies[$currrent]['rate'];
                $fee = $fee * $rate;
            }
        }
    }
    return $fee;
}, 2, 100);


//YITH WooCommerce Deposits and Down Payments
//https://yithemes.com/themes/plugins/yith-woocommerce-deposits-and-down-payments/
add_filter('yith_wcdp_single_deposit_price', function($deposit_price, $deposit_value) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $deposit_price = wc_price($WOOCS->woocs_exchange_value(floatval($deposit_value)));
        }
    }
    return $deposit_price;
}, 2, 99);


//YITH WooCommerce Role Based Prices
//https://yithemes.com/themes/plugins/yith-woocommerce-role-based-prices/
add_filter('yith_ywcrbp_sale_price', 'woocs_yith_ywcrbp_sale_price', 10, 2);
function woocs_yith_ywcrbp_sale_price($sale_price, $product) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currrent = $WOOCS->current_currency;
            if ($currrent != $WOOCS->default_currency) {

                $currencies = $WOOCS->get_currencies();
                $rate = $currencies[$currrent]['rate'];
                $sale_price = $sale_price * ($rate);
            }
        }
    }

    return $sale_price;
}
add_filter('yith_ywrbp_regular_price', 'woocs_yith_ywrbp_regular_price', 10, 2);
function woocs_yith_ywrbp_regular_price($regular_price, $product = NULL) {
    if (class_exists('WOOCS')) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currrent = $WOOCS->current_currency;
            if ($currrent != $WOOCS->default_currency) {

                $currencies = $WOOCS->get_currencies();
                $rate = $currencies[$currrent]['rate'];
                $regular_price = $regular_price * ($rate);
            }
        }
    }

    return $regular_price;
}
//ywcrbp_product_replace_roleprices
add_filter('ywcrbp_product_replace_roleprices', 'woocs_ywcrbp_product_replace_roleprices', 9999, 4);
function woocs_ywcrbp_product_replace_roleprices($role_price, $user_role, $price, $product) {
    if (class_exists('WOOCS')) {

        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currrent = $WOOCS->current_currency;
            if ($currrent != $WOOCS->default_currency) {

                $currencies = $WOOCS->get_currencies();
                $rate = $currencies[$currrent]['rate'];
                $role_price = $role_price * ($rate);
            }
        }
    }

    return $role_price;
}
//yith_wcrbp_get_role_based_price   CART FIX
add_filter('yith_wcrbp_get_role_based_price', 'woocs_yith_wcrbp_get_role_based_price', 10, 2);
function woocs_yith_wcrbp_get_role_based_price($regular_price, $product) {

    if (class_exists('WOOCS')) {
        global $WOOCS;
        if ($WOOCS->is_multiple_allowed) {
            $currrent = $WOOCS->current_currency;
            if ($currrent != $WOOCS->default_currency AND ( is_cart() OR is_checkout())) {
                $currencies = $WOOCS->get_currencies();
                $rate = $currencies[$currrent]['rate'];
                $regular_price = $regular_price / ($rate);
            }
        }
    }

    return $regular_price;
}