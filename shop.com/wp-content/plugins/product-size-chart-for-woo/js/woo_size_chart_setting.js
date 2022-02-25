jQuery(document).ready(function ($) {
    "use strict";
    /*Setting Field Rules*/
    var label = $('.woo_sc_sc_label');
    $('.vi-ui.tabular.menu .item').vi_tab();
    $('.dropdown').dropdown();

    if ($('#woo_sc_type_btn').val() === 'text') {
        $('.woo_sc_sc_label').show();
    } else {
        $('.woo_sc_sc_label').hide();
    }
    if ($('#woo_cs_select_position').val() === 'before_add_to_cart' || $('#woo_cs_select_position').val() === 'after_add_to_cart') {
        $('.woo_sc_btn_popup_position').hide();
        $('.woo_sc_btn_color').show();
        $('.get_short_code').hide();
        $('.woo_sc_btn_type').show();
        if ($('#woo_sc_type_btn').val() === 'text') {
            label.show();
        } else {
            label.hide();
        }
    }
    if ($('#woo_cs_select_position').val() === 'pop-up') {
        $('.woo_sc_btn_popup_position').show();
        $('.get_short_code').hide();
        $('.woo_sc_btn_color').show();
        $('.woo_sc_btn_type').show();
        if ($('#woo_sc_type_btn').val() === 'text') {
            label.show();
        } else {
            label.hide();
        }
    }
    if ($('#woo_cs_select_position').val() === 'product_tabs') {
        $('.woo_sc_multi').show();
        $('.woo_sc_btn_popup_position').hide();
        $('.get_short_code').hide();
        $('.woo_sc_btn_type').hide();
        $('.woo_sc_btn_color').hide();
        label.show();
    }
    if ($('#woo_cs_select_position').val() === 'none') {
        $('.woo_sc_btn_popup_position').hide();
        $('.get_short_code').show();
        $('.woo_sc_btn_type').hide();
        $('.woo_sc_btn_color').hide();
        label.hide();
    }
    $('#woo_cs_select_position').on('change', function () {
        let size_chart_type = $('#woo_cs_select_position').val();
        let button_type = $('#woo_sc_type_btn').val();
        if (size_chart_type === "before_add_to_cart" || size_chart_type === 'after_add_to_cart') {
            $('.woo_sc_multi').hide();
            $('.woo_sc_btn_popup_position').hide();
            $('.woo_sc_btn_color').show();
            $('.get_short_code').hide();
            $('.woo_sc_btn_type').show();
            if (button_type === 'text') {
                label.show();
            } else {
                label.hide();
            }
        }
        if (size_chart_type === "pop-up") {
            $('.woo_sc_multi').hide();
            $('.woo_sc_btn_popup_position').show();
            $('.get_short_code').hide();
            $('.woo_sc_btn_color').show();
            $('.woo_sc_btn_type').show();
            if (button_type === 'text') {
                label.show();
            } else {
                label.hide();
            }
        }
        if (size_chart_type === "product_tabs") {
            $('.woo_sc_multi').show();
            $('.woo_sc_btn_popup_position').hide();
            $('.get_short_code').hide();
            $('.woo_sc_btn_type').hide();
            $('.woo_sc_btn_color').hide();
            label.show();
        }
        if (size_chart_type === "none") {
            $('.woo_sc_multi').hide();
            $('.woo_sc_btn_popup_position').hide();
            $('.get_short_code').show();
            $('.woo_sc_btn_type').hide();
            $('.woo_sc_btn_color').hide();
            label.hide();
        }
    });
    $('#woo_sc_type_btn').on('change', function () {
        let btn_type_val = $('#woo_sc_type_btn').val();
        if (btn_type_val === 'text') {
            $('.woo_sc_sc_label').show();
        } else {
            $('.woo_sc_sc_label').hide();
        }
    });


    /*Color picker*/
    if ($('.color-picker').length !== 0) {
        $('.color-picker').iris({
            change: function (event, ui) {
                $(this).parent().find('.color-picker').css({backgroundColor: ui.color.toString()});
            },
            hide: true,
            border: true
        }).click(function () {
            $('.iris-picker').hide();
            $(this).closest('td').find('.iris-picker').show();
        });

        $('body').click(function () {
            $('.iris-picker').hide();
        });
        $('.color-picker').click(function (event) {
            event.stopPropagation();
        });
    }


});