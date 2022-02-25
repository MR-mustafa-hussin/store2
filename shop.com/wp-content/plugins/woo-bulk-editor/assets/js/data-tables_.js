var woobe_sort_order = [];
var data_table = null;
var products_types = null;//data got from server
var products_titles = null;//data got from server
var woobe_show_variations = 0;//show or hide variations of the variable products
var autocomplete_request_delay = 999;
var autocomplete_curr_index = -1;//for selecting by Enter button

//***

jQuery(function ($) {
    if (typeof $.fn.DataTable !== 'undefined') {
        //woobe_show_variations = woobe_get_from_storage('woobe_show_variations');// - disabled because not sure that it will be right for convinience

        //hiding not relevant filter and bulk operations
        if (woobe_show_variations > 0) {
            $('.not-for-variations').hide();
            $('#woobe_show_variations_mode').show();
            $('#woobe_select_all_vars').show();
        }

        //***

        init_data_tables();//data tables

        //***
        //fix to close opened textinputs in the data table
        $('*').mousedown(function (e) {
            if (typeof e.srcElement !== 'undefined' && !$(e.srcElement).hasClass('editable')) {
                if (!$(e.srcElement).parent().hasClass('editable')) {
                    woobe_close_prev_textinput();
                }
            }
            return true;
        });

        //***

        $('body').on('click', '.woobe-id-permalink-var', function () {

            if (woobe_show_variations) {
                $(this).parents('tr').nextAll('tr').each(function (ii, tr) {
                    if ($(tr).hasClass('product_type_variation')) {
                        $(tr).find('.woobe_product_check').prop('checked', true);
                        woobe_checked_products.push(parseInt($(tr).data('product-id'), 10));
                    } else {
                        return false;//terminate tr's selection
                    }
                });

                //remove duplicates if exists
                woobe_checked_products = Array.from(new Set(woobe_checked_products));
                __manipulate_by_depend_buttons();
                __woobe_action_will_be_applied_to();
                return false;
            }

            return true;
        });

        //***

        $('#woobe_select_all_vars').click(function () {

            $('tr.product_type_variation').each(function (ii, tr) {
                $(tr).find('.woobe_product_check').prop('checked', true);
                woobe_checked_products.push(parseInt($(tr).data('product-id'), 10));
            });

            //remove duplicates if exists
            woobe_checked_products = Array.from(new Set(woobe_checked_products));
            __manipulate_by_depend_buttons();
            __woobe_action_will_be_applied_to();

            return false;
        });

        //***
        //fix for applying coloring css styles for stock status drop-downs and etc ...
        $('body').on('change', 'td.editable .select-wrap select', function () {
            $(this).attr('data-selected', $(this).val());
            return true;
        });

    }
});



var do_data_tables_first = true;
function init_data_tables() {
    var oTable = $('#advanced-table');

    var page_fields = oTable.data('fields');
    var page_fields_array = page_fields.split(',');

    var edit_views = oTable.data('edit-views');
    var edit_views_array = edit_views.split(',');

    var edit_sanitize = oTable.data('edit-sanitize');
    var edit_sanitize_array = edit_sanitize.split(',');

    var start_page = oTable.data('start-page');
    //var ajax_additional = oTable.data('additional');
    var per_page = parseInt(oTable.data('per-page'), 10);
    //https://datatables.net/examples/advanced_init/dt_events.html
    data_table = oTable.on('order.dt', function () {
        $('.woobe_tools_panel_uncheck_all').trigger('click');
    }).DataTable({
        dom: 'Bfrtip',
        //https://tunatore.wordpress.com/2012/02/11/datatables-jquert-pagination-on-both-top-and-bottom-solution-if-you-use-bjqueryui/
        //sDom: '<"H"Bflrp>t<"F"ip>',
        sDom: '<"H"Blpr>t<"F"ip>',
        orderClasses: false,
        scrollX: true,
        lengthMenu: [5, 10],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        //https://datatables.net/examples/basic_init/table_sorting.html
        order: [[oTable.data('default-sort-by'), oTable.data('sort')]],
        //https://stackoverflow.com/questions/12008545/disable-sorting-on-last-column-when-using-jquery-datatables/22714994#22714994
        aoColumnDefs: [{
                bSortable: false,
                //aTargets: [-1] /* 1st one, start by the right */
                aTargets: (oTable.data('no-order')).toString().split(',').map(function (num) {
                    return parseInt(num, 10);
                })
            }, {className: "editable", targets: (oTable.data('editable')).toString().split(',').map(function (num) {
                    return parseInt(num, 10);
                })}],
        createdRow: function (row, data, dataIndex) {
            var p_id = data[1];//data[1] is ID col
            p_id = $(p_id).text();//!! important as we have link <a> in ID cell
            $(row).attr('data-product-id', p_id);
            $(row).attr('id', 'product_row_' + p_id);
            $(row).attr('data-row-num', dataIndex);
            $(row).addClass('product_type_' + products_types[p_id]);

            //***

            $.each($('td', row), function (colIndex) {
                $(this).attr('onmouseover', 'woobe_td_hover(' + p_id + ', "' + products_titles[p_id] + '", ' + colIndex + ')');
                $(this).attr('onmouseleave', 'woobe_td_hover(0, "",0)');

                //***

                $(this).attr('data-field', page_fields_array[colIndex]);
                $(this).attr('data-editable-view', edit_views_array[colIndex]);
                $(this).attr('data-sanitize', edit_sanitize_array[colIndex]);
                $(this).attr('data-col-num', colIndex);
                if (edit_views_array[colIndex] == 'url') {
                    $(this).addClass('textinput_url');
                }
                if (edit_views_array[colIndex] == 'textinput' || edit_views_array[colIndex] == 'url') {
                    $(this).addClass('textinput_col');
                    $(this).attr('onclick', 'woobe_click_textinput(this, ' + colIndex + ')');
                    //$(this).attr('title', 'test');
                }

                if (edit_sanitize_array[colIndex] == 'floatval' || edit_sanitize_array[colIndex] == 'intval') {
                    $(this).attr('onmouseover', 'woobe_td_hover(' + p_id + ', "' + products_titles[p_id] + '", ' + colIndex + ');woobe_onmouseover_num_textinput(this, ' + colIndex + ');');
                    $(this).attr('data-product-id', p_id);
                } else {
                    $(this).attr('onmouseout', 'woobe_td_hover(0, "",0);woobe_onmouseout_num_textinput();');
                }

                //***
                //remove class editable in cells which are not editable
                if ($(this).find('.info_restricked').length > 0) {
                    $(this).removeClass('editable');
                }
            });

        },
        processing: true,
        serverSide: true,
        bDeferRender: true,
        deferRender: true,
        //https://datatables.net/manual/server-side
        //https://datatables.net/examples/data_sources/server_side.html
        //ajax: ajaxurl + '?action=woobe_get_products',
        ajax: {
            url: ajaxurl,
            type: "POST",
            bDeferRender: true,
            deferRender: true,
            data: {
                action: 'woobe_get_products',
                woobe_show_variations: function () {
                    return woobe_show_variations;//we use function to return actual value for the current moment
                },
                filter_current_key: function () {
                    return woobe_filter_current_key;//we use function to return actual value for the current moment
                },
                lang: woobe_lang
            }
        },
        searchDelay: 100,
        pageLength: per_page,
        displayStart: start_page > 0 ? (start_page - 1) * per_page : 0,
        oLanguage: {
            sEmptyTable: lang.sEmptyTable,
            sInfo: lang.sInfo,
            sInfoEmpty: lang.sInfoEmpty,
            sInfoFiltered: lang.sInfoFiltered,
            sLoadingRecords: lang.sLoadingRecords,
            sProcessing: lang.sProcessing,
            sZeroRecords: lang.sZeroRecords,
            oPaginate: {
                sFirst: lang.sFirst,
                sLast: lang.sLast,
                sNext: lang.sNext,
                sPrevious: lang.sPrevious
            }
        },
        fnPreDrawCallback: function (a) {
            if (typeof a.json != 'undefined') {
                products_types = a.json.products_types;
                products_titles = a.json.products_titles;
            }
            //console.log(products_types);
            woobe_message(lang.loading, '', 300000);
        },
        fnDrawCallback: function () {
            do_data_tables_first = false;

            init_data_tables_edit();
            $('.all_products_checker').prop('checked', false);
            __manipulate_by_depend_buttons(false);
            woobe_message('', 'clean');
            woobe_init_scroll();


            $('.woobe_product_check').each(function (ii, ch) {
                if ($.inArray(parseInt($(ch).data('product-id'), 10), woobe_checked_products) != -1) {
                    $(ch).prop('checked', true);
                }
            });


            __manipulate_by_depend_buttons();
            $(document).trigger("data_redraw_done");

            //***

            __trigger_resize();
        }
    });

    //$(data_table)

    $("#advanced-table_paginate").on("click", "a", function () {
        //var info = table.page.info();
        //*** if remove next row - checked products will be stay checked even after page changing
        woobe_checked_products = [];

    });


    //https://stackoverflow.com/questions/5548893/jquery-datatables-delay-search-until-3-characters-been-typed-or-a-button-clicke
    $(".dataTables_filter input")
            .unbind()
            .bind('keyup change', function (e) {
                if (e.keyCode == 13/* || this.value == ""*/) {
                    data_table.search(this.value).draw();
                }
            });

    //to left/right scroll buttons init


}


function init_data_tables_edit(product_id = 0) {

    if (product_id === 0) {
        //for multi-select drop-downs - disabled as take a lot of resources while loading page
        //replaced to init by woobe_multi_select_onmouseover(this)
        if ($('.woobe_data_select').length) {
            if ($("#advanced-table .chosen-select").length) {
                //$("#advanced-table .chosen-select").chosen(/*{disable_search_threshold: 10}*/);
            }
        }

        //***
        //popup for taxonomies
        /*
         if ($('.js_woobe_tax_popup').length) {
         $.woobe_mod = $.woobe_mod || {};
         
         $.woobe_mod.popup_prepare = function () {
         new $.woobe_popup_prepare('.js_woobe_tax_popup');
         };
         
         $.woobe_mod.popup_prepare();
         }
         */

    }

    //***

    if (woobe_settings.load_switchers) {
        woobe_init_switchery(true, product_id);
    }

    __manipulate_by_depend_buttons();
    __woobe_action_will_be_applied_to();
}

var woobe_clicked_textinput_prev = [];//flag to track opened textinputs and close them
function woobe_click_textinput(_this, colIndex) {

    if ($(_this).find('.editable_data').size() > 0) {
        return false;
    }

    if (!$(_this).hasClass('editable')) {
        return false;
    }

    //***
    //lest close previous opened any textinput/area
    woobe_close_prev_textinput();
    woobe_clicked_textinput_prev = [_this, colIndex];

    //***
    /*
     if ($(_this).hasClass('textinput_url')) {
     var content = $(_this).html();
     } else {
     var content = $(_this).find('a').html();
     }
     */
    var content = $(_this).html();

    //***

    var product_id = $(_this).parents('tr').data('product-id');
    //var edit_view = $(_this).data('editable-view');


    if ($(_this).find('.info_restricked').length > 0) {
        return;
    }

    //***
    //fix to avoid editing titles of variable products
    if ($(_this).data('editable-view') == 'textinput' && $(_this).data('field') == 'post_title') {
        if ($(_this).parents('tr').hasClass('product_type_variation')) {
            return;
        }
    }

    //***

    var input_type = 'text';

    if ($(_this).data('sanitize') == 'intval' || $(_this).data('sanitize') == 'floatval') {
        input_type = 'number';
    }

    //inserting input into td cell
    if (input_type == 'text') {
        $(_this).html('<textarea class="form-control input-sm editable_data">' + content + '</textarea>');
    } else {
        $(_this).html('<input type="' + input_type + '" value="' + content + '" class="form-control input-sm editable_data" />');
    }

    var v = $(_this).find('.editable_data').val();//set focus to the end
    $(_this).find('.editable_data').focus().val("").val(v).select();

    woobe_th_width_synhronizer(colIndex, $(_this).width());

    //***

    $(_this).find('.editable_data').keydown(function (e) {

        var input = this;
        //38 - up, 40 - down, 13 - enter, 18 - ALT
        if ($.inArray(e.keyCode, [13, 18, 38, 40]) > -1) { // keyboard keys
            e.preventDefault();
            if (content !== $(input).val()) {
                woobe_message(lang.saving, '');
                $(_this).html($(input).val());
                $.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_update_page_field',
                        product_id: product_id,
                        field: $(_this).data('field'),
                        value: $(input).val()
                    },
                    success: function (answer) {
                        /*
                         if ($(_this).hasClass('textinput_url')) {
                         answer = '<a href="' + answer + '" title="' + answer + '" class="zebra_tips1" target="_blank">' + answer + '</a>';
                         woobe_init_tips($(_this).find('.zebra_tips1'));
                         }
                         */
                        //***

                        $(_this).html(answer);
                        woobe_message(lang.saved, 'notice');
                        woobe_th_width_synhronizer(colIndex, $(_this).width());

                        //fix for stock_quantity + manage_stock
                        if ($(_this).data('field') == 'stock_quantity') {
                            woobe_redraw_table_row($('#product_row_' + product_id));
                        }

                        $('.woobe_num_rounding').val(0);
                        $(document).trigger('woobe_page_field_updated', [product_id, $(_this).data('field'), $(input).val()]);
                    }
                });
            } else {
                $(_this).html(content);
                woobe_th_width_synhronizer(colIndex, $(_this).width());
            }

            //***
            //lets set focus to textinput under if its exists
            var col = $(_this).data('col-num');
            switch (e.keyCode) {
                case 38:
                case 18:
                    //keys alt or up
                    if ($(_this).closest('tr').prev('tr').length > 0) {
                        var prev_tr = $(_this).closest('tr').prev('tr');
                    } else {
                        var prev_tr = $(_this).closest('tbody').find('tr:last-child');
                    }
                    var c = $(_this).closest('tbody').find('tr').length;
                    while (true) {
                        if (c < 0) {
                            break;
                        }
                        if ($(prev_tr).find("td.editable[data-col-num='" + col + "']").length > 0) {
                            $(prev_tr).find("td.editable[data-col-num='" + col + "']").trigger('click');
                            break;
                        }

                        if ($(prev_tr).prev('tr').length) {
                            prev_tr = $(prev_tr).prev('tr');
                        } else {
                            prev_tr = $(_this).closest('tbody').find('tr:last-child');
                        }

                        c--;
                    }
                    woobe_th_width_synhronizer(colIndex, $(_this).width());
                    break;

                default:
                    //13,40
                    //keys ENTER or down
                    if ($(_this).closest('tr').next('tr').length > 0) {
                        var next_tr = $(_this).closest('tr').next('tr');
                    } else {
                        var next_tr = $(_this).closest('tbody').find('tr:first-child');
                    }
                    var c = $(_this).closest('tbody').find('tr').length;
                    while (true) {
                        if (c < 0) {
                            break;
                        }
                        if ($(next_tr).find("td.editable[data-col-num='" + col + "']").length > 0) {
                            $(next_tr).find("td.editable[data-col-num='" + col + "']").trigger('click');
                            break;
                        }

                        if ($(next_tr).next('tr').length) {
                            next_tr = $(next_tr).next('tr');
                        } else {
                            next_tr = $(_this).closest('tbody').find('tr:first-child');
                        }

                        c--;
                    }
                    woobe_th_width_synhronizer(colIndex, $(_this).width());
                    break;
            }


            //***

            return false;
        }
        if (e.keyCode === 27) { // esc
            $(_this).html(content);
            woobe_th_width_synhronizer(colIndex, $(_this).width());
        }

    });

}

//if we have opened textinput and clcked another cell - previous textinput should be closed!!
function woobe_close_prev_textinput() {

    if (woobe_clicked_textinput_prev.length) {
        var prev = woobe_clicked_textinput_prev[0];

        if ($(prev).find('input').size()) {
            //$(prev).html($(prev).find('input').val());
            $(prev).find('input').trigger($.Event('keydown', {keyCode: 27}));
        } else {
            //$(prev).html($(prev).find('textarea').val());
            $(prev).find('textarea').trigger($.Event('keydown', {keyCode: 27}));
        }

        woobe_th_width_synhronizer(woobe_clicked_textinput_prev[1], $(prev).width());
    }

    return true;
}


function woobe_click_checkbox(_this, numcheck) {

    var product_id = parseInt(numcheck, 10);
    var field = numcheck.replace(product_id + '_', '');
    var value = $(_this).data('val-false');
    var label = $(_this).data('false');

    var is = $(_this).is(':checked');
    if (is) {
        value = $(_this).data('val-true');
        label = $(_this).data('true');
    }

    //***

    $(_this).parent().find('label').text(label);

    //***

    woobe_message(lang.saving, 'warning');
    $.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_update_page_field',
            product_id: product_id,
            field: field,
            value: value
        },
        success: function () {
            $(document).trigger('woobe_page_field_updated', [product_id, field, is]);
            $(this).trigger("check_changed", [_this, field, is, value, numcheck]);
            woobe_message(lang.saved, 'notice');
        }
    });

    return true;
}

//when appearing dynamic textinput in the table cell - column head <th> should has the same width!!
function woobe_th_width_synhronizer(colIndex, width) {
    //$('#advanced-table_wrapper thead').find('th').eq(colIndex).width(width);
    //$('#advanced-table_wrapper tfoot').find('th').eq(colIndex).width(width);
    //__trigger_resize();//conflict with calculator
}



function woobe_act_tax_popup(_this) {

    $('#taxonomies_popup .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    //fix to avoid not popup opening after taxonomies button clicking
    woobe_popup_clicked = $(_this);

    //***

    var product_id = $(_this).data('product-id');
    var key = $(_this).data('key');//tax key
    var checked_terms_ids = [];

    if ($(_this).data('terms-ids').toString().length > 0) {

        checked_terms_ids = $(_this).data('terms-ids').toString().split(',');

        checked_terms_ids = checked_terms_ids.map(function (x) {
            return parseInt(x, 10);
        });
    }

    //lets build terms tree
    $('#taxonomies_popup_list').html('');
    if (Object.keys(taxonomies_terms[key]).length > 0) {
        __woobe_fill_terms_tree(checked_terms_ids, taxonomies_terms[key]);
    }

    $('.quick_search_element').show();
    $('.quick_search_element_container').show();
    $('#taxonomies_popup').show();

    //***

    $('.woobe-modal-save1').unbind('click');
    $('.woobe-modal-save1').click(function () {
        $('#taxonomies_popup').hide();
        var checked_ch = $('#taxonomies_popup_list').find('input:checked');
        var checked_terms = [];

        $(_this).find('ul').html('');

        if (checked_ch.length) {
            $(checked_ch).each(function (i, ch) {
                checked_terms.push($(ch).val());
                $(_this).find('ul').append('<li class="woobe_li_tag">' + $(ch).parent().find('label').text() + '</li>');
            });
        } else {
            $(_this).find('ul').append('<li class="woobe_li_tag">' + lang.no_items + '</li>');
        }

        //***

        $(_this).data('terms-ids', checked_terms.join());

        //***

        woobe_message(lang.saving, 'warning');
        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: key,
                value: checked_terms
            },
            success: function () {
                $(document).trigger('woobe_page_field_updated', [product_id, key, checked_terms]);
                woobe_message(lang.saved, 'notice');
            }
        });
    });

    $('.woobe-modal-close1').unbind('click');
    $('.woobe-modal-close1').click(function () {
        $('#taxonomies_popup').hide();
    });


    //***
    //terms quick search
    $('#term_quick_search').unbind('keyup');
    $('#term_quick_search').val('');
    $('#term_quick_search').focus();
    $('#term_quick_search').keyup(function () {
        var val = $(this).val();
        if (val.length > 0) {
            setTimeout(function () {
                $('.quick_search_element_container').show();

                $('.quick_search_element_container').each(function (i, item) {
                    if (!($(item).parent().data('search-value').toString().indexOf(val.toLowerCase()) + 1)) {
                        $(item).hide();
                    } else {
                        $(item).show();
                    }
                });


                $('.quick_search_element_container:not(:hidden)').each(function (i, item) {
                    $(item).parents('li').children('.quick_search_element_container').show();
                });


            }, 250);
        } else {
            $('.quick_search_element_container').show();
        }

        return true;
    });

    //***
    $('#taxonomies_popup_list_checked_only').unbind('click');
    $('#taxonomies_popup_list_checked_only').prop('checked', false);
    $('#taxonomies_popup_list_checked_only').click(function () {

        if ($(this).is(':checked')) {

            $('#taxonomies_popup_list li.top_quick_search_element').each(function (i, item) {
                if (!$(item).find('input:checked').length) {
                    $(item).hide();
                } else {
                    $(item).show();
                    $(item).find('li').each(function (ii, it) {
                        if (!$(it).find('ul.woobe_child_taxes').length && !$(it).find('input:checked').length) {
                            $(it).hide();
                        }
                    });
                }
            });

        } else {
            $('#taxonomies_popup_list li').show();
        }

        return true;
    });

    //***

    $('.woobe_create_new_term').unbind('click');
    $('.woobe_create_new_term').click(function () {
        __woobe_create_new_term(key);
        return false;
    });

    return true;
}

function __woobe_create_new_term(tax_key, show_parent = true, select_id = '') {
    $('#woobe_new_term_popup .woobe-modal-title span').html(tax_key);

    $('#woobe_new_term_title').val('');
    $('#woobe_new_term_slug').val('');


    if (show_parent) {
        $('#woobe_new_term_parent').parents('.woobe-form-element-container').show();

        $('#woobe_new_term_parent').val('');
        $('#woobe_new_term_parent').html('');

        if (Object.keys(taxonomies_terms[tax_key]).length > 0) {
            $('#woobe_new_term_parent').append('<option value="-1">' + lang.none + '</option>');
            __woobe_fill_select('woobe_new_term_parent', taxonomies_terms[tax_key]);
        }

        //***

        $('#woobe_new_term_parent').chosen({
            //disable_search_threshold: 10,
            width: '100%'
        }).trigger("chosen:updated");
    } else {
        $('#woobe_new_term_parent').parents('.woobe-form-element-container').hide();
    }


    $('#woobe_new_term_popup').show();

    $('.woobe-modal-close9').click(function () {
        $('#woobe_new_term_popup').hide();
    });

    //***
    $('#woobe_new_term_create').unbind('click');
    $('#woobe_new_term_create').click(function () {
        var title = $('#woobe_new_term_title').val();
        var slug = $('#woobe_new_term_slug').val();
        var parent = $('#woobe_new_term_parent').val();

        if (title.length > 0) {
            woobe_message(lang.creating, 'warning', 99999);
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_create_new_term',
                    tax_key: tax_key,
                    titles: title,
                    slugs: slug,
                    parent: parent
                },
                success: function (response) {
                    response = JSON.parse(response);

                    if (response.terms_ids.length > 0) {
                        woobe_message(lang.created, 'notice');
                        taxonomies_terms[tax_key] = response.terms;

                        for (var i = 0; i < response.terms_ids.length; i++) {

                            var li = $('#taxonomies_popup_list_li_tpl').html();
                            li = li.replace(/__TERM_ID__/gi, response.terms_ids[i]);
                            li = li.replace(/__LABEL__/gi, response.titles[i]);
                            li = li.replace(/__SEARCH_TXT__/gi, response.titles[i].toLowerCase());
                            li = li.replace(/__CHECK__/gi, 'checked');
                            if (parent == 0) {
                                li = li.replace(/__TOP_LI__/gi, 'top_quick_search_element');
                            } else {
                                li = li.replace(/__TOP_LI__/gi, '');
                            }
                            li = li.replace(/__CHILDS__/gi, '');
                            $('#taxonomies_popup_list').prepend(li);

                        }

                        //***
                        //if we working with any drop-down
                        if (select_id.length > 0) {
                            for (var i = 0; i < response.terms_ids.length; i++) {
                                $('#' + select_id).prepend('<option selected value="' + response.terms_ids[i] + '">' + response.titles[i] + '</option>');
                            }

                            //***

                            $($('#' + select_id)).chosen({
                                width: '100%'
                            }).trigger("chosen:updated");
                        }

                        //***
                        //lets all WOOBE extensions knows about this event
                        $(document).trigger("taxonomy_data_redrawn", [tax_key, response.term_id]);
                    } else {
                        woobe_message(lang.error + ' ' + lang.term_maybe_exist, 'error');
                    }

                }
            });

            //***

            $('.woobe-modal-close9').trigger('click');
        }

        return false;
    });

}


//service function to create terms tree in taxonomies popup
function __woobe_fill_terms_tree(checked_terms_ids, data, parent_term_id = 0) {

    var li_tpl = $('#taxonomies_popup_list_li_tpl').html();

    //***

    $(data).each(function (i, d) {
        var li = li_tpl;
        li = li.replace(/__TERM_ID__/gi, d.term_id);
        li = li.replace(/__LABEL__/gi, d.name);
        li = li.replace(/__SEARCH_TXT__/gi, d.name.toLowerCase());

        if ($.inArray(d.term_id, checked_terms_ids) > -1) {
            li = li.replace(/__CHECK__/gi, 'checked');
        } else {
            li = li.replace(/__CHECK__/gi, '');
        }

        if (parent_term_id == 0) {
            li = li.replace(/__TOP_LI__/gi, 'top_quick_search_element');
        } else {
            li = li.replace(/__TOP_LI__/gi, '');
        }

        //***

        if (Object.keys(d.childs).length > 0) {
            li = li.replace(/__CHILDS__/gi, '<ul class="woobe_child_taxes woobe_child_taxes_' + d.term_id + '"></ul>');
        } else {
            li = li.replace(/__CHILDS__/gi, '');
        }

        //***

        if (parent_term_id == 0) {
            $('#taxonomies_popup_list').append(li);
        } else {
            $('#taxonomies_popup_list .woobe_child_taxes_' + parent_term_id).append(li);
        }


        if (d.childs) {
            __woobe_fill_terms_tree(checked_terms_ids, d.childs, d.term_id);
        }
    });

}

//use direct call only instead of attaching event to each element after page loading
//to up performance when a lot of product per page
function woobe_act_popupeditor(_this, post_parent) {

    $('#popupeditor_popup .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = $(_this).data('product_id');
    var key = $(_this).data('key');

    //***

    woobe_message(lang.loading, 'warning');
    $.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_get_post_field',
            product_id: product_id,
            field: key,
            post_parent: post_parent
        },
        success: function (content) {
            woobe_message('', 'clean');
            $('#popupeditor_popup').show();

            if (typeof tinyMCE != 'undefined') {
                try {
                    tinyMCE.get('popupeditor').setContent(content);
                } catch (e) {
                    //fix if editor loaded not in rich mode
                    $('.wp-editor-area').val(content);
                }
            }

            woobe_message(lang.loaded, 'notice');
        }
    });

    //***

    $('.woobe-modal-save2').unbind('click');
    $('.woobe-modal-save2').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        $('#popupeditor_popup').hide();
        woobe_message(lang.saving, 'warning');

        var content = '';

        try {
            content = tinyMCE.get('popupeditor').getContent();
        } catch (e) {
            //fix if editor loaded not in rich mode
            content = $('.wp-editor-area').val();
        }

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: key,
                value: content
            },
            success: function (content) {
                $(document).trigger('woobe_page_field_updated', [product_id, key, content]);
                woobe_message(lang.saved, 'notice');
            }
        });
    });

    $('.woobe-modal-close2').unbind('click');
    $('.woobe-modal-close2').click(function () {
        $('#popupeditor_popup').hide();
    });


}

//use direct call only instead of attaching event to each element after page loading
//to up performance when a lot of product per page
function woobe_act_downloads_editor(_this) {

    var button = _this;
    $('#downloads_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');

    //***

    if ($(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        $($(_this).data('downloads')).each(function (i, d) {
            var li_html = $('#woobe_download_file_tpl').html();
            li_html = li_html.replace(/__TITLE__/gi, d.name);
            li_html = li_html.replace(/__HASH__/gi, d.id);
            li_html = li_html.replace(/__FILE_URL__/gi, d.file);
            html += li_html;
        });


        $('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        $('#downloads_popup_editor').show();
        $('#woobe_downloads_bulk_operations').hide();
        __woobe_init_downloads();



        /*
         woobe_message(lang.loading, 'warning');
         $.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_downloads',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         $('#downloads_popup_editor form').html(content);
         $('#downloads_popup_editor').show();
         
         $('#woobe_downloads_bulk_operations').hide();
         
         //***
         
         __woobe_init_downloads();
         }
         });
         
         */
    } else {

        if (product_id > 0) {
            $('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            $('#woobe_downloads_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#downloads_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            $('#woobe_downloads_bulk_operations').show();
        }

        $('#downloads_popup_editor').show();
        __woobe_init_downloads();
    }


    //***

    //init close and save buttons when first call of popup is done
    $('.woobe-modal-save3').unbind('click');
    $('.woobe-modal-save3').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');


        if (product_id > 0) {
            $('#downloads_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#products_downloads_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    $('#downloads_popup_editor form').html('');
                    $(button).parent().html(html);

                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#products_downloads_form').serialize()]);
                }
            });
        } else {
            //for downloads buttons in any extensions
            $(document).trigger('woobe_act_downloads_editor_saved', [product_id, key, $('#products_downloads_form').serialize()]);
        }

        return false;

    });


    $('.woobe-modal-close3').unbind('click');
    $('.woobe-modal-close3').click(function () {
        //$('#downloads_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $('#downloads_popup_editor').hide();
        return false;
    });


    return false;
}


function woobe_act_gallery_editor(_this) {
    var button = _this;

    $('#gallery_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');

    //***

    if ($(_this).data('count') > 0) {
        if (product_id > 0) {


            var html = '';
            $($(_this).data('images')).each(function (i, a) {
                var li_html = $('#woobe_gallery_li_tpl').html();
                li_html = li_html.replace(/__IMG_URL__/gi, a.url);
                li_html = li_html.replace(/__ATTACHMENT_ID__/gi, a.id);
                html += li_html;
            });

            $('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
            $('#gallery_popup_editor').show();
            $('#woobe_gallery_bulk_operations').hide();
            __woobe_init_gallery();


            /*
             woobe_message(lang.loading, 'warning');
             $.ajax({
             method: "POST",
             url: ajaxurl,
             data: {
             action: 'woobe_get_gallery',
             product_id: product_id,
             field: key
             },
             success: function (content) {
             woobe_message(lang.loaded, 'notice');
             $('#gallery_popup_editor form').html(content);
             $('#gallery_popup_editor').show();
             
             $('#woobe_gallery_bulk_operations').hide();
             
             //***
             
             __woobe_init_gallery();
             
             }
             });
             
             */
        } else {
            //we can use such button for any another extensions
            $('#gallery_popup_editor').show();
            $('#woobe_gallery_bulk_operations').show();
            __woobe_init_gallery();
        }

    } else {
        if (product_id > 0) {
            $('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            $('#woobe_gallery_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#gallery_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }
            $('#woobe_gallery_bulk_operations').show();
        }


        $('#gallery_popup_editor').show();
        __woobe_init_gallery();
    }


    //***


    $('.woobe-modal-save4').unbind('click');
    $('.woobe-modal-save4').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            $('#gallery_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#products_gallery_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //$('#gallery_popup_editor form').html('');
                    $(button).parent().html(html);

                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#products_gallery_form').serialize()]);
                }
            });
        } else {
            //for gallery buttons in any extensions
            $(document).trigger('woobe_act_gallery_editor_saved', [product_id, key, $('#products_gallery_form').serialize()]);
        }


    });

    $('.woobe-modal-close4').unbind('click');
    $('.woobe-modal-close4').click(function () {
        //$('#gallery_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $('#gallery_popup_editor').hide();
    });

    return false;
}


function woobe_act_upsells_editor(_this) {
    var button = _this;

    $('#upsells_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');

    //***

    var button_data = [];

    if ($('#upsell_ids_upsell_ids_' + product_id + ' li').size() > 0) {
        $('#upsell_ids_upsell_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push($(li).data('product'));
        });
    }

    //***

    if ($(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        $(button_data).each(function (i, li) {
            var li_html = $('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        $('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        $("#upsells_products_search").val('');
        $('#upsells_popup_editor').show();
        $('#woobe_upsells_bulk_operations').hide();
        __woobe_init_upsells();

        /*
         woobe_message(lang.loading, 'warning');
         $.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_upsells',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         $('#upsells_popup_editor form').html(content);
         $("#upsells_products_search").val('');
         $('#upsells_popup_editor').show();
         $('#woobe_upsells_bulk_operations').hide();
         
         //***
         
         __woobe_init_upsells();
         }
         });
         
         */
    } else {
        $("#upsells_products_search").val('');
        if (product_id > 0) {
            $('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            $('#woobe_upsells_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#upsells_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }
            $('#woobe_upsells_bulk_operations').show();
        }

        $('#upsells_popup_editor').show();
        __woobe_init_upsells();
    }

    //***


    $('.woobe-modal-save5').unbind('click');
    $('.woobe-modal-save5').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            $('#upsells_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#products_upsells_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //$('#upsells_popup_editor form').html('');
                    $(button).parent().html(html);

                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#products_upsells_form').serialize()]);
                }
            });
        } else {
            //for buttons in any extensions
            $(document).trigger('woobe_act_upsells_editor_saved', [product_id, key, $('#products_upsells_form').serialize()]);
        }

        return false;
    });

    $('.woobe-modal-close5').unbind('click');
    $('.woobe-modal-close5').click(function () {
        //$('#upsells_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $("#upsells_products_search").val('');
        $('#upsells_popup_editor').hide();
        return false;
    });

}



function woobe_act_cross_sells_editor(_this) {
    var button = _this;

    $('#cross_sells_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');

    //***

    var button_data = [];

    if ($('#cross_sells_cross_sell_ids_' + product_id + ' li').size() > 0) {
        $('#cross_sells_cross_sell_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push($(li).data('product'));
        });
    }

    //***

    if ($(_this).data('count') > 0 && product_id > 0) {
        var html = '';
        $(button_data).each(function (i, li) {
            var li_html = $('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        $('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        $("#cross_sells_products_search").val('');
        $('#cross_sells_popup_editor').show();
        $('#woobe_crossels_bulk_operations').hide();
        __woobe_init_cross_sells();

        /*
         woobe_message(lang.loading, 'warning');
         $.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_cross_sells',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         $('#cross_sells_popup_editor form').html(content);
         $("#cross_sells_products_search").val('');
         $('#cross_sells_popup_editor').show();
         $('#woobe_crossels_bulk_operations').hide();
         
         //***
         
         __woobe_init_cross_sells();
         }
         });
         */

    } else {

        if (product_id > 0) {
            $('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            $('#woobe_crossels_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#cross_sells_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            $('#woobe_crossels_bulk_operations').show();
        }

        $("#cross_sells_products_search").val('');
        $('#cross_sells_popup_editor').show();
        __woobe_init_cross_sells();
    }

    //***


    $('.woobe-modal-save6').unbind('click');
    $('.woobe-modal-save6').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            $('#cross_sells_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#products_cross_sells_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //$('#cross_sells_popup_editor form').html('');
                    $(button).parent().html(html);

                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#products_cross_sells_form').serialize()]);
                }
            });
        } else {
            //for buttons in any extensions
            $(document).trigger('woobe_act_cross_sells_editor_saved', [product_id, key, $('#products_cross_sells_form').serialize()]);
        }

        return false;
    });

    $('.woobe-modal-close6').unbind('click');
    $('.woobe-modal-close6').click(function () {
        //$('#cross_sells_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $("#cross_sells_products_search").val('');
        $('#cross_sells_popup_editor').hide();
        return false;
    });

}


function woobe_act_grouped_editor(_this) {
    var button = _this;

    $('#grouped_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + $(_this).data('key') + ']');
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');

    //***

    var button_data = [];

    if ($('#grouped_ids_grouped_ids_' + product_id + ' li').size() > 0) {
        $('#grouped_ids_grouped_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push($(li).data('product'));
        });
    }

    //***

    if ($(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        $(button_data).each(function (i, li) {
            var li_html = $('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        $('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        $("#grouped_products_search").val('');
        $('#grouped_popup_editor').show();
        $('#woobe_grouped_bulk_operations').hide();
        __woobe_init_grouped();


        /*
         woobe_message(lang.loading, 'warning');
         $.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_grouped',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         $('#grouped_popup_editor form').html(content);
         $("#grouped_products_search").val('');
         $('#grouped_popup_editor').show();
         $('#woobe_grouped_bulk_operations').hide();
         
         //***
         
         __woobe_init_grouped();
         }
         });
         
         */
    } else {
        if (product_id > 0) {
            $('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            $('#woobe_grouped_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#grouped_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            $('#woobe_grouped_bulk_operations').show();
        }

        $("#grouped_products_search").val('');
        $('#grouped_popup_editor').show();
        __woobe_init_grouped();
    }


    //***


    $('.woobe-modal-save7').unbind('click');
    $('.woobe-modal-save7').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            $('#grouped_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#products_grouped_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    $('#grouped_popup_editor form').html('');
                    $(button).parent().html(html);

                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#products_grouped_form').serialize()]);
                }
            });

        } else {
            //for buttons in any extensions
            $(document).trigger('woobe_act_grouped_editor_saved', [product_id, key, $('#products_grouped_form').serialize()]);
        }

        return false;
    });

    $('.woobe-modal-close7').unbind('click');
    $('.woobe-modal-close7').click(function () {
        //$('#grouped_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $("#grouped_products_search").val('');
        $('#grouped_popup_editor').hide();
        return false;
    });

}



function woobe_act_meta_popup_editor(_this) {
    woobe_popup_clicked = $(_this);
    var product_id = parseInt($(_this).data('product_id'), 10);
    var key = $(_this).data('key');
    $('#meta_popup_editor .woobe-modal-title').html($(_this).data('name') + ' [' + key + ']');

    //***
    var meta = JSON.parse($(_this).find('.meta_popup_btn_data').text());

    if (Object.keys(meta).length > 0 && product_id > 0) {
        var html = '';
        //console.log(meta);
        try {
            $.each(meta, function (k, v) {
                var li_html = $('#meta_popup_editor_li').html();
                li_html = li_html.replace(/__KEY__/gi, k);



                if (typeof v !== 'string') {
                    var ul = '<ul class="meta_popup_editor_child_ul">';
                    
                    $.each(v, function (kk, vv) {
                        var li_html2 = $('#meta_popup_editor_li').html();
                        li_html2 = li_html2.replace(/__KEY__/gi, kk);
                        li_html2 = li_html2.replace(/__VALUE__/gi, vv);
                        li_html2 = li_html2.replace(/__CHILD_LIST__/gi, '');
                        li_html2 = li_html2.replace('keys[]', 'keys[' + k + '][]');
                        li_html2 = li_html2.replace('values[]', 'values[' + k + '][]');
                        ul += li_html2;
                    });

                    ul += '</ul>';

                    li_html = li_html.replace(/__CHILD_LIST__/gi, ul + '&nbsp;<a href="#" class="meta_popup_editor_add_sub_item" data-key="' + k + '">' + lang.append_sub_item + '</a><br />');
                    li_html = li_html.replace(/__VALUE__/gi, 'delete-this');
                } else {
                    li_html = li_html.replace(/__VALUE__/gi, v);
                    li_html = li_html.replace(/__CHILD_LIST__/gi, '<ul class="meta_popup_editor_child_ul" data-key="' + k + '"></ul>&nbsp;<a href="#" class="meta_popup_editor_add_sub_item" data-key="' + k + '">' + lang.append_sub_item + '</a><br />');
                }

                html += li_html;
            });
        } catch (e) {
            //***
        }

        $('#meta_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        $('#meta_popup_editor form').find("input[value='delete-this']").remove();
        $('#meta_popup_editor').show();
        __woobe_init_meta_popup_editor();

    } else {

        if (product_id > 0) {
            $('#meta_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
        } else {
            //this we need do for another applications, for example bulk editor
            if ($('#meta_popup_editor form .woobe_fields_tmp').length == 0) {
                $('#meta_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }
        }

        $('#meta_popup_editor').show();
        __woobe_init_meta_popup_editor();
    }

    //***


    $('.woobe-modal-save10').unbind('click');
    $('.woobe-modal-save10').click(function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            $('#meta_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: $('#meta_popup_editor_form').serialize(),
                    is_serialized: 1
                },
                success: function (answer) {
                    $(_this).find('.meta_popup_btn_data').html(answer);
                    woobe_message(lang.saved, 'notice');
                    $(document).trigger('woobe_page_field_updated', [product_id, key, $('#meta_popup_editor_form').serialize()]);
                }
            });
        } else {
            //for buttons in any extensions
            $(document).trigger('woobe_act_meta_popup_editor_saved', [product_id, key, $('#meta_popup_editor_form').serialize()]);
        }

        return false;
    });

    $('.woobe-modal-close10').unbind('click');
    $('.woobe-modal-close10').click(function () {
        //$('#meta_popup_editor_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        $('#meta_popup_editor').hide();
        return false;
    });

}

function __woobe_init_meta_popup_editor() {

    $("#meta_popup_editor form .woobe_fields_tmp, #meta_popup_editor form .meta_popup_editor_child_ul").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    $('.meta_popup_editor_insert_new').unbind('click');
    $('.meta_popup_editor_insert_new').click(function () {
        var li_html = $('#meta_popup_editor_li').html();

        li_html = li_html.replace(/__KEY__/gi, '');
        li_html = li_html.replace(/__VALUE__/gi, '');
        li_html = li_html.replace(/__CHILD_LIST__/gi, '<ul class="meta_popup_editor_child_ul"></ul>&nbsp;<a href="#" class="meta_popup_editor_add_sub_item" data-key="">' + lang.append_sub_item + '</a><br />');

        if ($(this).data('place') == 'top') {
            $('#meta_popup_editor form .woobe_fields_tmp').prepend(li_html);
        } else {
            $('#meta_popup_editor form .woobe_fields_tmp').append(li_html);
        }
        __woobe_init_meta_popup_editor();

        return false;
    });

    //***

    $('.meta_popup_editor_add_sub_item').unbind('click');
    $('.meta_popup_editor_add_sub_item').click(function () {
        var li_html = $('#meta_popup_editor_li').html();

        li_html = li_html.replace(/__KEY__/gi, '');
        li_html = li_html.replace(/__VALUE__/gi, '');
        li_html = li_html.replace(/__CHILD_LIST__/gi, '');

        var key = $(this).data('key');
        if (key.length === 0) {
            key = $(this).prev('.meta_popup_editor_child_ul').data('key');
        }

        if (typeof key == 'undefined') {
            key = $(this).parent().find('.meta_popup_editor_li_key').eq(0).val();
        }

        li_html = li_html.replace('keys[]', 'keys[' + key + '][]');
        li_html = li_html.replace('values[]', 'values[' + key + '][]');

        //remove value textinput of the parent
        $(this).parent().find("[name='values[]']").remove();

        if ($(this).data('place') == 'top') {
            $(this).prev('.meta_popup_editor_child_ul').prepend(li_html);
        } else {
            $(this).prev('.meta_popup_editor_child_ul').append(li_html);
        }
        __woobe_init_meta_popup_editor();

        return false;
    });

    //***

    $('.meta_popup_editor_li_key').unbind('click');
    $('.meta_popup_editor_li_key').keyup(function () {
        $(this).parent().find('.meta_popup_editor_child_ul .meta_popup_editor_li_key').attr('name', 'keys[' + $(this).val() + '][]');
        $(this).parent().find('.meta_popup_editor_child_ul .meta_popup_editor_li_value').attr('name', 'values[' + $(this).val() + '][]');
        return true;
    });

    //***

    $('.woobe_prod_delete').unbind('click');
    $('.woobe_prod_delete').click(function () {
        $(this).parent('li').remove();
        return false;
    });
}



function woobe_act_select(_this) {
    woobe_message(lang.saving, '');
    var product_id = parseInt($(_this).data('product-id'), 10);

    if ($(_this).data('field') == 'product_type') {
        //redraw table row
        woobe_redraw_table_row(_this);
    } else {
        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: $(_this).data('field'),
                value: $(_this).val()
            },
            success: function () {
                $(document).trigger('woobe_page_field_updated', [product_id, $(_this).data('field'), $(_this).val()]);
                woobe_message(lang.saved, 'notice');
            }
        });
    }

    return false;

}

function woobe_redraw_table_row(row, do_trigger = true) {
    var product_id = parseInt($(row).data('product-id'), 10);

    if (!product_id) {
        return;
    }

    //***

    $.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_redraw_table_row',
            product_id: product_id,
            field: $(row).data('field'),
            value: $(row).val()
        },
        success: function (row_data) {
            woobe_message(lang.saved, 'notice');
            var tr_index = $('#product_row_' + product_id).data('row-num');
            data_table.row(tr_index).data($.parseJSON(row_data));

            $.each($('td', $('#product_row_' + product_id)), function (colIndex) {
                if ($(this).find('.info_restricked').length > 0) {
                    $(this).removeClass('editable');
                } else {
                    $(this).addClass('editable');
                }
            });

            //***
            if (do_trigger) {
                $(document).trigger('woobe_page_field_updated', [product_id, $(row).data('field'), $(row).val()]);
            }
            //woobe_checked_products.splice(woobe_checked_products.indexOf(product_id), 1);
            /*
             woobe_checked_products = $.grep(woobe_checked_products, function (value) {
             return value != product_id;
             });
             */

            if ($.inArray(product_id, woobe_checked_products) > -1) {
                $('#product_row_' + product_id).find('.woobe_product_check').prop('checked', true);
            }

            init_data_tables_edit(product_id);
        }
    });
}

function woobe_init_calendar(calendar) {

    if (typeof $(calendar).attr('data-dtp') !== typeof undefined && $(calendar).attr('data-dtp') !== false) {
        return;
    }

    //***

    $(calendar).bootstrapMaterialDatePicker({
        weekStart: 1,
        time: false,
        clearButton: false,
        //minDate: new Date(),
        format: 'DD/MM/YYYY',
        autoclose: true,
        lang: 'en',
        title: $(calendar).data('title'),
        icons: {
            time: "icofont icofont-clock-time",
            date: "icofont icofont-ui-calendar",
            up: "icofont icofont-rounded-up",
            down: "icofont icofont-rounded-down",
            next: "icofont icofont-rounded-right",
            previous: "icofont icofont-rounded-left"
        }
    }).on('change', function (e, date)
    {
        var hidden = $('#' + $(this).data('val-id'));
        if (typeof date != 'undefined') {
            var d = new Date(date);
            //hidden.val(parseInt(d.getTime() / 1000, 10));
            hidden.val(d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate());
        } else {
            //clear
            hidden.val(0);
        }

        //***
        var product_id = parseInt(hidden.data('product-id'), 10);
        if (product_id > 0) {
            woobe_message(lang.saving, '');
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: hidden.data('key'),
                    value: hidden.val()
                },
                success: function () {
                    $(document).trigger('woobe_page_field_updated', [product_id, hidden.data('key'), hidden.val()]);
                    woobe_message(lang.saved, 'notice');
                }
            });
        }

    });



    //***

    $(calendar).parents('td').find('.woobe_calendar_cell_clear').click(function () {
        $(this).parent().find('.woobe_calendar').val('').trigger('change');
        return false;
    });


}

//redrawing of checkbox to switcher on onmouseover
//was in cycle but its make time of page redrawing longer, so been remade for individual initializating
function woobe_set_switchery(_this) {

    //http://abpetkov.github.io/switchery/
    if (typeof Switchery !== 'undefined') {
        new Switchery(_this);
        //while reinit allows more html switchers
        $(_this).parent().find('span.switchery:not(:first)').remove();
    }

    //***

    $(_this).unbind('change');
    $(_this).change(function () {
        var state = _this.checked.toString();
        var numcheck = $(_this).data('numcheck');
        var trigger_target = $(_this).data('trigger-target');
        var label = $("*[data-label-numcheck='" + numcheck + "']");
        var hidden = $("*[data-hidden-numcheck='" + numcheck + "']");
        label.html($(_this).data(state));
        $(label).removeClass($(_this).data('class-' + (!(_this.checked)).toString()));
        $(label).addClass($(_this).data('class-' + state));
        var val = $(_this).data('val-' + state);
        var field_name = $(hidden).attr('name');
        $(hidden).val(val);

        if (trigger_target.length) {
            $(this).trigger("check_changed", [trigger_target, field_name, _this.checked, val, numcheck]);
        }
    });

    //***

    $(_this).unbind('check_changed');
    $(_this).on("check_changed", function (event, trigger_target, field_name, is_checked, val, product_id) {
        woobe_message(lang.saving, '');
        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: field_name,
                value: val
            },
            success: function () {
                $(document).trigger('woobe_page_field_updated', [parseInt(product_id, 10), field_name, val]);
                woobe_message(lang.saved, 'notice');
            }
        });
    });



}

function woobe_act_thumbnail(_this) {
    var product_id = $(_this).parents('tr').data('product-id');
    var field = $(_this).parents('td').data('field');

    var image = wp.media({
        title: lang.upload_image,
        multiple: false,
        library: {
            type: ['image']
        }
    }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                uploaded_image = uploaded_image.toJSON();
                if (typeof uploaded_image.url != 'undefined') {
                    $(_this).find('img').attr('src', uploaded_image.url);
                    //$(_this).removeAttr('srcset');

                    woobe_message(lang.saving, '');
                    $.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'woobe_update_page_field',
                            product_id: product_id,
                            field: field,
                            value: uploaded_image.id
                        },
                        success: function () {
                            $(document).trigger('woobe_page_field_updated', [product_id, field, uploaded_image.id]);
                            woobe_message(lang.saved, 'notice');
                        }
                    });
                }
            });


    return false;

}

//service
function __woobe_init_downloads() {

    $('.woobe_upload_file_button').unbind('click');
    $('.woobe_upload_file_button').click(function ()
    {
        var input_object = $(this).parents('tr').find('.woobe_down_file_url').eq(0);
        var image = wp.media({
            title: lang.upload_file,
            multiple: false
        }).open()
                .on('select', function (e) {
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    uploaded_image = uploaded_image.toJSON();
                    if (typeof uploaded_image.url != 'undefined') {
                        $(input_object).val(uploaded_image.url);
                    }
                });

        return false;
    });

    //***

    $("#downloads_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });


    //***
    $('.woobe_insert_download_file').unbind('click');
    $('.woobe_insert_download_file').click(function () {

        var li_html = $('#woobe_download_file_tpl').html();
        li_html = li_html.replace(/__TITLE__/gi, '');
        li_html = li_html.replace(/__HASH__/gi, '');
        li_html = li_html.replace(/__FILE_URL__/gi, '');

        if ($(this).data('place') == 'top') {
            $('#downloads_popup_editor form .woobe_fields_tmp').prepend(li_html);
        } else {
            $('#downloads_popup_editor form .woobe_fields_tmp').append(li_html);
        }
        __woobe_init_downloads();

        return false;
    });


    $('.woobe_down_file_delete').unbind('click');
    $('.woobe_down_file_delete').click(function () {
        $(this).parents('li').remove();
        return false;
    });

}

//service
function __woobe_init_gallery() {

    $('.woobe_insert_gall_file').unbind('click');
    $('.woobe_insert_gall_file').click(function (e)
    {
        e.preventDefault();

        var image = wp.media({
            title: lang.upload_images,
            multiple: true,
            //cache: 'refresh',
            library: {
                type: ['image'],
                //cache: false
            }
        }).open()
                .on('select', function (e) {
                    //var uploaded_images = image.state().get('selection').first();
                    var uploaded_images = image.state().get('selection');
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    uploaded_images = uploaded_images.toJSON();
                    //console.log(uploaded_images);
                    if (uploaded_images.length) {
                        for (var i = 0; i < uploaded_images.length; i++) {
                            var html = $('#woobe_gallery_li_tpl').html();
                            html = html.replace(/__IMG_URL__/gi, uploaded_images[i]['url']);
                            html = html.replace(/__ATTACHMENT_ID__/gi, uploaded_images[i]['id']);
                            $('#gallery_popup_editor form .woobe_fields_tmp').prepend(html);
                        }
                        __woobe_init_gallery();
                        //$('#media-attachment-date-filters').trigger('change');
                    }
                });

        return false;
    });

    //***

    $("#gallery_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        //handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });


    //***

    $('.woobe_gall_file_delete').unbind('click');
    $('.woobe_gall_file_delete').click(function () {
        $(this).parents('li').remove();
        return false;
    });


    $('.woobe_gall_file_delete_all').unbind('click');
    $('.woobe_gall_file_delete_all').click(function () {
        $('#gallery_popup_editor form .woobe_fields_tmp').html('');
        return false;
    });


}

//service

function __woobe_init_upsells() {

    $("#upsells_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    $("#upsells_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            $('#upsells_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = $("#upsells_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = $('#products_upsells_form').serialize();
            $('#upsells_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = $("#upsells_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = $("#upsells_products_search").getSelectedItemIndex();
                var data = $("#upsells_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = $('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name + '(#' + data.id + ')');
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    $('#upsells_popup_editor form .woobe_fields_tmp').prepend(html);
                    $("#upsells_products_search").val('');
                    __woobe_init_upsells();
                    $("#upsells_products_search").focus();
                } else {
                    $("#upsells_products_search").val('');
                }

                return false;
            }
        },
        requestDelay: autocomplete_request_delay
    });


    $('#upsells_products_search').unbind('keydown');
    $("#upsells_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = $("#upsells_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = $("#upsells_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = $('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                $('#upsells_popup_editor form .woobe_fields_tmp').prepend(html);
                $("#upsells_products_search").val('');
                __woobe_init_upsells();
                $("#upsells_products_search").focus();
            } else {
                $("#upsells_products_search").val('');
                $("#upsells_products_search").focus();
            }
        }
    });

    //***

    $('.woobe_prod_delete').unbind('click');
    $('.woobe_prod_delete').click(function () {
        $(this).parents('li').remove();
        $("#upsells_products_search").focus();
        return false;
    });


    $("#upsells_products_search").focus();



}

//service
function __woobe_init_cross_sells() {

    $("#cross_sells_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    $("#cross_sells_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            $('#cross_sells_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = $("#cross_sells_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = $('#products_cross_sells_form').serialize();
            $('#cross_sells_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = $("#cross_sells_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = $("#cross_sells_products_search").getSelectedItemIndex();
                var data = $("#cross_sells_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = $('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name);
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    $('#cross_sells_popup_editor form .woobe_fields_tmp').prepend(html);
                    $("#cross_sells_products_search").val('');
                    __woobe_init_cross_sells();
                    $("#cross_sells_products_search").focus();
                } else {
                    $("#cross_sells_products_search").val('');
                }
            }
        },
        requestDelay: autocomplete_request_delay
    });


    $("#cross_sells_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = $("#cross_sells_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = $("#cross_sells_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = $('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                $('#cross_sells_popup_editor form .woobe_fields_tmp').prepend(html);
                $("#cross_sells_products_search").val('');
                __woobe_init_cross_sells();
                $("#cross_sells_products_search").focus();
            } else {
                $("#cross_sells_products_search").val('');
                $("#cross_sells_products_search").focus();
            }
        }
    });

    //***

    $('.woobe_prod_delete').unbind('click');
    $('.woobe_prod_delete').click(function () {
        $(this).parents('li').remove();
        $("#cross_sells_products_search").focus();
        return false;
    });


    $("#cross_sells_products_search").focus();
}

//service
function __woobe_init_grouped() {

    $("#grouped_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    $("#grouped_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            $('#grouped_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = $("#grouped_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = $('#products_grouped_form').serialize();
            $('#grouped_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            hideOnEmptyPhrase: false,
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = $("#grouped_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = $("#grouped_products_search").getSelectedItemIndex();
                var data = $("#grouped_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = $('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name);
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    $('#grouped_popup_editor form .woobe_fields_tmp').prepend(html);
                    $("#grouped_products_search").val('');
                    __woobe_init_grouped();
                    $("#grouped_products_search").focus();
                } else {
                    $("#grouped_products_search").val('');
                }
            }
        },
        requestDelay: autocomplete_request_delay
    });

    //***

    $("#grouped_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = $("#grouped_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = $("#grouped_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = $('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                $('#grouped_popup_editor form .woobe_fields_tmp').prepend(html);
                $("#grouped_products_search").val('');
                __woobe_init_grouped();
                $("#grouped_products_search").focus();
            } else {
                $("#grouped_products_search").val('');
                $("#grouped_products_search").focus();
            }
        }
    });

    //***

    $('.woobe_prod_delete').unbind('click');
    $('.woobe_prod_delete').click(function () {
        $(this).parents('li').remove();
        $("#grouped_products_search").focus();
        return false;
    });


    $("#grouped_products_search").focus();
}



function woobe_message(text, type, duration = 0) {
    $('.growl').hide();
    if (duration > 0) {
        Growl.settings.duration = duration;
    } else {
        Growl.settings.duration = 1777;
    }
    switch (type) {
        case 'notice':
            $.growl.notice({message: text});
            break;

        case 'warning':
            $.growl.warning({message: text});
            break;

        case 'error':
            $.growl.error({message: text});
            break;

        case 'clean':
            //clean
            break;

        default:
            $.growl({title: '', message: text});
            break;
}

}

function woobe_init_scroll() {
    setTimeout(function () {

        //$('#advanced-table').wrap( "<div class='woobe_scroll_wrapper'></div>" );

        if ($('#advanced-table').width() > $('#tabs-products').width() + 50) {
            $('#woobe_scroll_left').show();
            $('#woobe_scroll_right').show();

            var anchor1 = $('.dataTables_scrollBody');
            //var anchor2 = $('.dataTables_scrollHead');
            //var anchor3 = $('.dataTables_scrollFoot');
            var corrective = 30;
            var animate_time = 300;
            var leftPos = null;

            $('#woobe_scroll_left').click(function () {
                leftPos = anchor1.scrollLeft();
                $('div.dataTables_scrollBody').animate({scrollLeft: leftPos + $('#tabs-products').width() - corrective}, animate_time);

                //anchor1.animate({scrollLeft: leftPos + $('#tabs-products').width() - corrective}, animate_time);
                //anchor2.animate({scrollLeft: leftPos + $('#tabs-products').width() - corrective}, animate_time);
                //anchor3.animate({scrollLeft: leftPos + $('#tabs-products').width() - corrective}, animate_time);
                return false;
            });


            $('#woobe_scroll_right').click(function () {
                leftPos = anchor1.scrollLeft();
                $('div.dataTables_scrollBody').animate({scrollLeft: leftPos - $('#tabs-products').width() + corrective}, animate_time);

                //anchor1.animate({scrollLeft: leftPos - $('#tabs-products').width() + corrective}, animate_time);
                //anchor2.animate({scrollLeft: leftPos - $('#tabs-products').width() + corrective}, animate_time);
                //anchor3.animate({scrollLeft: leftPos - $('#tabs-products').width() + corrective}, animate_time);
                return false;
            });
        }

    }, 1000);
}

function woobe_multi_select_cell(_this) {

    var cell_dropdown = $(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_dropdown');
    var cell_list = $(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_list');
    var ul = $(cell_list).find('ul');
    var select = $(cell_dropdown).find('select');
    var tax_key = $(select).data('field');
    var product_id = $(select).data('product-id');
    var selected = ($(select).data('selected') + '').split(',').map(function (num) {
        return parseInt(num, 10);
    });

    var select_id = 'mselect_' + tax_key + '_' + product_id;

    $(_this).hide();

    //***

    $(select).empty();
    __woobe_fill_select(select_id, taxonomies_terms[tax_key], selected);

    //***

    $(select).chosen({
        //disable_search_threshold: 10,
        //max_shown_results: 5,
        width: '100%'
    }).trigger("chosen:updated");

    $(cell_dropdown).show();

    //***

    $(cell_dropdown).find('.woobe_multi_select_cell_cancel').unbind('click');
    $(cell_dropdown).find('.woobe_multi_select_cell_cancel').click(function () {
        $(select).chosen('destroy');
        $(cell_dropdown).hide();
        $(_this).show();
        return false;
    });

    $(cell_dropdown).find('.woobe_multi_select_cell_save').unbind('click');
    $(cell_dropdown).find('.woobe_multi_select_cell_save').click(function () {
        $(select).chosen('destroy');
        woobe_act_select(select);
        $(cell_dropdown).hide();
        $(_this).show();

        //***

        var sel = [];
        $(ul).html('');
        if ($(select).find(":selected").length) {
            $(select).find(":selected").each(function (ii, option) {
                sel[ii] = option.value;
                $(ul).append('<li>' + option.label + '</li>');
            });
        } else {
            $(ul).append('<li>' + lang.no_items + '</li>');
        }

        $(select).data('selected', sel.join(','));

        return false;
    });


    $(cell_dropdown).find('.woobe_multi_select_cell_new').unbind('click');
    $(cell_dropdown).find('.woobe_multi_select_cell_new').click(function () {

        __woobe_create_new_term(tax_key, false, select_id);

        return false;
    });


    return false;
}

//make images bigger on their event onmouseover
function woobe_init_image_preview(_this) {
    var xOffset = 150;
    var yOffset = 30;

    _this.t = _this.title;
    //_this.title = "";
    var c = (_this.t != "") ? "<br/>" + _this.t : "";
    $("body").append("<p id='woobe_img_preview'><img src='" + _this.href + "' alt='" + lang.loading + "' width='300' />" + c + "</p>");
    $("#woobe_img_preview")
            .css("top", (_this.pageY - xOffset) + "px")
            .css("left", (_this.pageX + yOffset) + "px")
            .fadeIn("fast");

    $(_this).mousemove(function (e) {
        $("#woobe_img_preview")
                .css("top", (e.pageY - xOffset) + "px")
                .css("left", (e.pageX + yOffset) + "px");
    });

    $(_this).mouseleave(function (e) {
        $("#woobe_img_preview").remove();
    });
}

//to display current product in the top wordpress admin bar
function woobe_td_hover(id, title, col_num) {
    if (!$('#wp-admin-bar-root-default li.woobe_current_cell_view').length) {
        $('#wp-admin-bar-root-default').append('<li class="woobe_current_cell_view">');
    }

    //***

    if (id > 0) {
        var content = '#' + id + '. ' + title + ' [<i>' + $('#woobe_col_' + col_num).text() + '</i>]';
    } else {
        var content = '';
    }

    $('#wp-admin-bar-root-default li.woobe_current_cell_view').html(content);

    return true;
}


function woobe_onmouseover_num_textinput(_this, colIndex) {
    $(document).trigger("woobe_onmouseover_num_textinput", [_this, colIndex]);
    return true;
}

function woobe_onmouseout_num_textinput(_this, colIndex) {
    $(document).trigger("woobe_onmouseout_num_textinput", [_this, colIndex]);
    return true;
}


/*
 function woobe_init_textinput_url() {
 if ($('.textinput_url').length) {
 $('.textinput_url').each(function (index, item) {
 if ($(item).find('.info_restricked')) {
 return;
 }
 var tipContent = $('<p><a href="' + $(item).html() + '" target="_blank">' + $(item).html() + '</a></p>');
 $(item).data('powertipjq', tipContent);
 $(item).powerTip({
 placement: 'w',
 mouseOnToPopup: true
 });
 });
 }
 }
 */



