jQuery(document).ready(function ($) {
    "use strict";
    css_default();
    $('.dropdown').dropdown();

    //Css default
    function css_default() {
        let table_design=$('.woo_sc_table_design');
        table_design.find('.tr:first').find('.td').css('background-color', $('#woo_sc_head_color').val());
        table_design.find('.tr:first').find('.td').find('input[type="text"]').css('background-color', $('#woo_sc_head_color').val());
        $('.tr:first').find('.td').find('input[type="text"]').css('color', $('#woo_sc_text_head_color').val());
        let a = $('.woo_sc_table_design').find('.tr').length;
        for (let i = 0; i < a; i++) {
            if (i > 0 && i % 2 == 0) {
                table_design.find('.tr').eq(i).find('.td').css('background-color', $('#woo_sc_even_rows_color').val());
                table_design.find('.tr').eq(i).find('.td').find('input[type="text"]').css('background-color', $('#woo_sc_even_rows_color').val());
                table_design.find('.tr').eq(i).find('.td').find('input[type="text"]').css('color', $('#woo_sc_even_rows_text_color').val());
            }
            if (i > 0 && i % 2 !== 0) {
                table_design.find('.tr').eq(i).find('.td').find('input[type="text"]').css('background-color', $('#woo_sc_odd_rows_color').val());
                table_design.find('.tr').eq(i).find('.td').css('background-color', $('#woo_sc_odd_rows_color').val());
                table_design.find('.tr').eq(i).find('.td').find('input[type="text"]').css('color', $('#woo_sc_odd_rows_text_color').val());
            }
        }
    }

    //Set number of rows and cols
    // $('#woo_sc_number_rows').val($('.woo_sc_table_design').find('.tr').length);
    // $('#woo_sc_number_cols').val($('.woo_sc_table_design').find('.tr').eq(0).find('.td').length);

    function addrows(dem) {
        let addrow = "<tr class='tr'>";
        for (let a = 0; a < dem; a++) {
            addrow += "<td class='td'><div class='woo_sc_table_input'><input type='text' placeholder='Enter text..' class='input' name='inputTable' autocomplete='off'></div>"
        }
        addrow += '<td class="woo_sc_row_header"><div class="woo_sc_div_tablebutton_rows">' +
            '<button  type="button" class="tableButton addrows">+</button><button  type="button" class="tableButton delrows" disabled>-</button>' +
            '</div></td>';
        addrow += "</tr>";
        return addrow;
    }

    var checkRows = $('.tr').length;
    var imgLink = '';
    if (checkRows > 1) {
        $('.delrows').removeAttr('disabled');
        $('.delrows').addClass('enable');
    }
    var checkCol = $('.delcol').length;
    if (checkCol > 1) {
        $('.delcol').removeAttr('disabled');
        $('.delcol').addClass('enable');
    }
    //add Rows
    $(".bodyTable").on('click', '.addrows', function (e) {
        let dem = $(this).closest('tr').find('.td').length;
        let addrow = addrows(dem);
        $(this).closest('tr').after(addrow);
        css_default();
        css_border();
        //Show delete btn
        var lengthRows = $('.tr').length;
        if (lengthRows > 1) {
            $('.delrows').removeAttr('disabled');
            $('.delrows').addClass('enable');
        }

    });

    //add Columns
    $(".bodyTable").on('click', '.addcol', function (e) {

        var lengthCol, index, length, i;
        var addNewColBtn = "<td>"
        addNewColBtn += "<div class='woo_sc_div_tablebutton_cols'><button type='button' class='tableButton addcol'>+</button><button type='button'  class='tableButton delcol' disabled>-</button></div>"
        addNewColBtn += "</td>";
        var addNewCol = "<td class='td'>"
        addNewCol += "<div class='woo_sc_table_input'><input type='text' class='input' name='inputTable'  placeholder='Enter text..' autocomplete='off'></div>"
        addNewCol += "</td>";
        $(this).closest('td').after(addNewColBtn);
        index = $('.addcol').index($(this));
        length = $('.bodyTable .tr').length;
        for (i = 0; i < length; i++) {
            $('.tr').eq(i).find('.td').eq(index).after(addNewCol);
        }
        lengthCol = $('.delcol').length;
        if (lengthCol > 1) {
            $('.delcol').removeAttr('disabled');
            $('.delcol').addClass('enable');
        }
        css_default();
        css_border();
    });

    //Delete Rows
    $(".woo_sc_table_design").on('click', '.delrows', function (e) {
        //hide delete btn
        var lengthRows = $('.tr').length - 1;
        if (lengthRows < 2) {
            $('.delrows').attr('disabled', true);
            $('.delrows').removeClass('enable');
        }
        $(this).closest('tr').remove();
        css_default();
    });

    //Delete Colums
    $('.woo_sc_table_design').on('click', '.delcol', function (e) {
        var lengthCol;
        var index, length, i;
        index = $('.delcol').index($(this));
        length = $('.woo_sc_table_design .tr').length;
        $(this).closest('td').remove();
        for (i = 0; i < length; i++) {
            $('.tr').eq(i).find('.td').eq(index).remove();
        }
        lengthCol = $('.delcol').length;
        if (lengthCol < 2) {
            $('.delcol').attr('disabled', true);
            $('.delcol').removeClass('enable');
        }
        css_default();
    });

    /*Quick add rows*/
    $('#woo_sc_quick_add').on('click', function () {
        /*add rows*/
        let rows = $('#woo_sc_number_rows').val(),
            max = 999,
            lengthTd = $('.woo_sc_table_design').find('.tr:first').find('.td').length;


        if (rows >= 0 && rows < max) {
            for (let i = 0; i < rows; i++) {
                $('.woo_sc_table_design').append(addrows(lengthTd));
                $('.delrows').removeAttr('disabled');
                $('.delrows').addClass('enable');
                css_default();
                css_border();
            }
        } else {
            alert('Rows must be more than 0 and less than 999');
        }
    });
    /*Quick add columns*/
    $('#woo_sc_quick_add_col').on('click', function () {
        let columns = $('#woo_sc_number_cols').val(),
            max = 999,
            lengthTr = $('.woo_sc_table_design').find('.tr').length,
            lengthTd = $('.woo_sc_table_design').find('.tr:first').find('.td').length;
        if (columns >= 0 && columns < max) {
            let addNewColBtn = "<td>"
            addNewColBtn += "<div class='woo_sc_div_tablebutton_cols'><button  type='button'  class='tableButton addcol'>+</button><button  type='button'  class='tableButton delcol' disabled>-</button></div>"
            addNewColBtn += "</td>";
            let addNewCol = "<td class='td'>"
            addNewCol += "<input type='text' class='input' name='inputTable'  placeholder='Enter text..' autocomplete='off'>"
            addNewCol += "</td>";
            //add btn
            for (let j = 0; j < columns; j++) {
                $('.woo_sc_table_design').find('tr').eq(0).find('td').eq(lengthTd - 1).before(addNewColBtn);
            }
            for (let i = 0; i < columns; i++) {
                $('.addrows').closest('td').before(addNewCol);
            }
            let lengthCol = $('.delcol').length;
            if (lengthCol > 1) {
                $('.delcol').removeAttr('disabled');
                $('.delcol').addClass('enable');
            }
            css_default();
            css_border();
        } else {
            alert('Columns must be more than 0 and less than 999');
        }
    });
    /*Quick dell rows*/
    $('#woo_sc_quick_del').on('click', function () {
        let rows_number = $('#woo_sc_number_rows').val(),
            lengthTr = $('.woo_sc_table_design').find('.tr').length;
        //del Rows
        if (lengthTr > 1 && ((lengthTr - rows_number) > 0)) {
            if (rows_number > 0) {
                if (rows_number == 1) {
                    let confirm_dell_rows = confirm('Are you sure to delete ' + rows_number + ' row?');
                    if (confirm_dell_rows) {
                        for (let i = 0; i < rows_number; i++) {
                            if (lengthTr > 2) {
                                $('.woo_sc_table_design').find('.tr:last').remove();
                            }
                            if (lengthTr == 2) {
                                $('.woo_sc_table_design').find('.tr:last').remove();
                                $('.delrows').attr('disabled', true);
                                $('.delrows').removeClass('enable');
                            }
                        }
                        css_default();
                    }
                } else {

                    let confirm_dell_rows = confirm('Are you sure to delete ' + rows_number + ' rows?');
                    if (confirm_dell_rows) {
                        for (let i = 0; i < rows_number; i++) {
                            if (lengthTr > 2) {
                                $('.woo_sc_table_design').find('.tr:last').remove();
                            }
                            if (lengthTr == 2) {
                                $('.woo_sc_table_design').find('.tr:last').remove();
                                $('.delrows').attr('disabled', true);
                                $('.delrows').removeClass('enable');
                            }
                        }
                        css_default();
                    }
                }
            }
        } else {
            alert('The number of deleted rows must be less than the number of rows in the current table!');
        }
    });
    /*Quick dell columns*/
    $('#woo_sc_quick_del_col').on('click', function () {
        let cols_number = $('#woo_sc_number_cols').val(),
            lengthTd = $('.woo_sc_table_design').find('.tr').eq(0).find('.td').length;
        if (lengthTd > 1 && ((lengthTd - cols_number) > 0)) {
            if (cols_number > 0) {
                if (cols_number == 1) {
                    let confirm_dell_col = confirm('Are you sure to delete ' + cols_number + ' column?');
                    if (confirm_dell_col) {
                        for (let i = 0; i < cols_number; i++) {
                            if (lengthTd > 2) {
                                $('.woo_sc_table_design').find('.tr').find('.td:last').remove();
                                $('.tableButton.addcol:last').closest('td').remove();
                            }
                            if (lengthTd == 2) {
                                $('.woo_sc_table_design').find('.tr').find('.td:last').remove();
                                $('.tableButton.addcol:last').closest('td').remove();
                                $('.delcol').attr('disabled', true);
                                $('.delcol').removeClass('enable');
                            }
                        }
                        css_default();
                    }
                } else {
                    let confirm_dell_col = confirm('Are you sure to delete ' + cols_number + ' columns?');
                    if (confirm_dell_col) {
                        for (let i = 0; i < cols_number; i++) {
                            if (lengthTd > 2) {
                                $('.woo_sc_table_design').find('.tr').find('.td:last').remove();
                                $('.tableButton.addcol:last').closest('td').remove();
                            }
                            if (lengthTd == 2) {
                                $('.woo_sc_table_design').find('.tr').find('.td:last').remove();
                                $('.tableButton.addcol:last').closest('td').remove();
                                $('.delcol').attr('disabled', true);
                                $('.delcol').removeClass('enable');
                            }
                        }
                        css_default();
                    }
                }

            }
        } else {
            alert('The number of deleted columns must be less than the number of columns in the current table!');
        }
    });

    // btn Preview
    function tableToArray() {
        let lengthTr, lengthTd, i, j, result;
        let arr = [], count = [];

        lengthTr = $('.bodyTable').find('.tr').length;
        lengthTd = $('.bodyTable').find('.tr').eq(0).find('.td').length;
        for (i = 0; i < lengthTr; i++) {
            arr[i] = [];
            count[i] = 0;
            for (j = 0; j < lengthTd; j++) {
                let a = $('.tr').eq(i).find('.td').eq(j).find("input[type='text']").val();
                arr[i][j] = a;
                /*remove row empty*/
                if (a.length === 0) {
                    count[i]++;
                }
            }
            if (count[i] === lengthTd) {
                arr.splice(i, 1);
            }
        }
        result = arr.filter(item => item !== 'empty');
        return result;
    }

    $('#woo_sc_preview_btn').on('click', function () {
        if ($('#woo_sc_hide').val() !== 'hide_all') {
            if ($('#woo_sc_hide').val() !== 'hide_table') {
                //Mang
                let arr = tableToArray();
                //xuat mang
                if (arr.length > 0) {
                    let out = "<table class='woo_sc_view_table'>";
                    for (let i = 0; i < arr.length; i++) {
                        out += "<tr>";
                        for (let j = 0; j < arr[i].length; j++) {
                            if (i == 0) {
                                out += "<th>" + arr[i][j] + "</th>";
                            } else {
                                out += "<td>" + arr[i][j] + "</td>";
                            }

                        }
                        out += "</tr>";
                    }
                    out += "</table>";
                    $('.printTable').html(out);
                } else {
                    $('.printTable').html("");
                }
            } else {
                $('.printTable').html("<span style='text-align: center;display:block;margin:15px'>Table is hidden (This message is only displayed in Preview mode)</span>");
            }
            //load content
            if (tinymce.activeEditor == null) {
                let msg = '<strong>You need  switch to Visual in ContentEditor to preview the content</strong>';
                $('.woo_sc_modal_backend_content').html(msg);
                if ($('#woo_sc_hide').val() !== 'hide_image') {
                    if ($('.woo_sc_img_upload').find('.image_Uploaded').length != 0) {
                        let width_img = $('#woo_sc_width_img').val();
                        if (width_img !== "") {
                            let link_image_uploaded = '<img class="image_Uploaded" style="width:' + width_img + '%" src="';
                            link_image_uploaded += $('.image_Uploaded').attr('src');
                            link_image_uploaded += '">';
                            $('.woo_sc_popup_img').html(link_image_uploaded);
                        } else {
                            let link_image_uploaded = '<img class="image_Uploaded" style="width:100%" src="';
                            link_image_uploaded += $('.image_Uploaded').attr('src');
                            link_image_uploaded += '">';
                            $('.woo_sc_popup_img').html(link_image_uploaded);
                        }
                    }
                } else {
                    $('.woo_sc_popup_img').html("<span style='text-align: center;display:block;margin:15px'>Image is hidden (This message is only displayed in Preview mode)</span>");
                }
            } else {
                let content = tinymce.activeEditor.getContent();
                $('.woo_sc_modal_backend_content').html(content);
                //Image preview
                if ($('#woo_sc_hide').val() !== 'hide_image') {
                    if ($('.woo_sc_img_upload').find('.image_Uploaded').length != 0) {
                        let width_img = $('#woo_sc_width_img').val();
                        if (width_img !== "") {
                            let link_image_uploaded = '<img class="image_Uploaded" style="width:' + width_img + '%" src="';
                            link_image_uploaded += $('.image_Uploaded').attr('src');
                            link_image_uploaded += '">';
                            $('.woo_sc_popup_img').html(link_image_uploaded);
                        } else {
                            let link_image_uploaded = '<img class="image_Uploaded" style="width:100%" src="';
                            link_image_uploaded += $('.image_Uploaded').attr('src');
                            link_image_uploaded += '">';
                            $('.woo_sc_popup_img').html(link_image_uploaded);
                        }

                    }
                } else {
                    $('.woo_sc_popup_img').html("<span style='text-align: center;display:block;margin:15px'>Image is hidden (This message is only displayed in Preview mode)</span>");
                }
            }

            //Style Preview
            //head color
            $('.woo_sc_view_table').find('th').css('background-color', $('#woo_sc_head_color').val());
            //text head color
            $('.woo_sc_view_table').find('th').css('color', $('#woo_sc_text_head_color').val());
            //rows color text color
            let a = $('.woo_sc_view_table').find('tr').length;
            for (let i = 0; i < a; i++) {
                if (i > 0 && i % 2 == 0) {
                    $('.woo_sc_view_table').find('tr').eq(i).css('background-color', $('#woo_sc_even_rows_color').val());
                    $('.woo_sc_view_table').find('tr').eq(i).css('color', $('#woo_sc_even_rows_text_color').val());

                } else {
                    $('.woo_sc_view_table').find('tr').eq(i).css('background-color', $('#woo_sc_odd_rows_color').val());
                    $('.woo_sc_view_table').find('tr').eq(i).css('color', $('#woo_sc_odd_rows_text_color').val());
                }
            }
            $('.woo_sc_view_table').find('th').css('border-top',
                $('#woo_sc_horizontal_width').val() + 'px ' + $('#woo_sc_horizontal_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('th').css('border-bottom',
                $('#woo_sc_horizontal_width').val() + 'px ' + $('#woo_sc_horizontal_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('th').css('border-left',
                $('#woo_sc_vertical_width').val() + 'px ' + $('#woo_sc_vertical_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('th').css('border-right',
                $('#woo_sc_vertical_width').val() + 'px ' + $('#woo_sc_vertical_border_style').val() + ' ' + $('#woo_sc_border_color').val());

            $('.woo_sc_view_table').find('td').css('border-top',
                $('#woo_sc_horizontal_width').val() + 'px ' + $('#woo_sc_horizontal_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('td').css('border-bottom',
                $('#woo_sc_horizontal_width').val() + 'px ' + $('#woo_sc_horizontal_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('td').css('border-left',
                $('#woo_sc_vertical_width').val() + 'px ' + $('#woo_sc_vertical_border_style').val() + ' ' + $('#woo_sc_border_color').val());
            $('.woo_sc_view_table').find('td').css('border-right',
                $('#woo_sc_vertical_width').val() + 'px ' + $('#woo_sc_vertical_border_style').val() + ' ' + $('#woo_sc_border_color').val());
        } else {
            $('.printTable').html("");
            $('.woo_sc_popup_img').html("<span style='text-align: center;display:block;margin:15px'>All hidden (This message is only displayed in Preview mode)</span>")
        }
        //modal show
        $('.vi-ui.modal')
            .modal('show')
        ;
    });
    $('.button.woo_sc_close').on("click", function () {
        $('.vi-ui.modal')
            .modal('hide')
    });
    // Ajax Image upload
    $('#woo_sc_up_image').on("click", function (e) {
        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    // uncomment the next line if you want to attach image to the current post
                    // uploadedTo : wp.media.view.settings.post.id,
                    type: 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).open().on('select', function () { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                imgLink = attachment.url;
                $('.woo_sc_img_upload').show().html('<img class="image_Uploaded" src="' + attachment.url + '" style="width:95%;display:inline-block;" />');
                $('.woo_sc_popup_img').show().html('<img class="image_Uploaded" src="' + attachment.url + '" style="max-width:800px;display:inline-block;" />');
                $('.woo_sc_width_img_margin').show();
                $('.remove_image_button').show();

            });
    });
    //check image uploaded
    if ($('.woo_sc_img_upload').find('.image_Uploaded').length != 0) {
        imgLink = $('.image_Uploaded').attr('src');
        $('.woo_sc_width_img_margin').show();
        $('.remove_image_button').show();
    }
// Remove image event
    $('body').on('click', '.remove_image_button', function () {
        imgLink = '';
        let tran_link_to_img = '<img class="image_Default" style="width:95%;margin: 0 auto; display:inline-block;" src="';
        tran_link_to_img += tran_link.link;
        tran_link_to_img += '">';
        $('.woo_sc_img_upload').html(tran_link_to_img);
        $('.woo_sc_popup_img').hide();
        $('.woo_sc_width_img_margin').hide();
        $('.remove_image_button').hide();

    });
    //check empty title
    $('#post').find('#publish').on('click', function (e) {
        if ($('#title').val() == "") {
            e.preventDefault();
            $('#title').focus();
            $('#title').css('background-color', '#FDB6B6');
            $('#title').after('<span style="color:#F40000" class="spanerr">Title is required</span>')
        } else {
            let array = tableToArray();
            if (array.length !== 0) {
                let arr = JSON.stringify(array);
                let inputTable = $("<input>").attr({name: 'tableArray', value: arr});
                $(this).append(inputTable);
            }
            let inputImgLink = $("<input>").attr({name: 'imgLink', value: imgLink});
            $(this).append(inputImgLink);
            return true;
        }
    });
    $('#post').find('#title').keypress(function () {
        $('.spanerr').remove();
        $('#title').css('background-color', '#ffffff');
    });


    $('.check_box_cate').on('change', function () {
        let stt = $(this).prop('checked');
        let _this = $(this);
        let check_box_val = _this.val();
        _this.closest('ul').find('input[data-parent=' + check_box_val + ']').prop('checked', stt).trigger('change');
    });

    //Search product
    $('#sc_input_search_product_id').select2({
        // theme: "classic",
        minimumInputLength: 3,
        placeholder: 'Product name...',
        ajax: {
            type: 'post',
            url: tran_link.adminAjax,
            data: function (params) {
                var query = {
                    key_search: params.term,
                    type: 'public',
                    action: 'size_chart_search_product'
                };
                return query;
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return data;

                /* return {
                     results: data.items
                 };
                 var newOption = new Option(data.text, data.id, false, false);
                 $('.js-data-example-ajax').append(newOption).trigger('change');*/
            }
        }
    });
    $('.button.sc_btn_reset').on('click', function () {
        var confirm_reset = confirm('Are you sure to reset table?');
        if (confirm_reset) {
            $('.woo_sc_table_design').find('.td').find('input[type="text"]').val('');
        }
    });
    //style design table
    $('#woo_sc_head_color').wpColorPicker({
        change: function (event, ui) {
            $('.woo_sc_table_design').find('.tr:first').find('.td').css({backgroundColor: ui.color.toString()});
            $('.woo_sc_table_design').find('.tr:first').find('.td').find('input[type="text"]').css({backgroundColor: ui.color.toString()});
        }
    });
    $('#woo_sc_text_head_color').wpColorPicker({
        change: function (event, ui) {
            $('.tr:first').find('.td').find('input[type="text"]').css({color: ui.color.toString()});
        }
    });
    $('#woo_sc_even_rows_color').wpColorPicker({
        change: function (event, ui) {
            let a = $('.woo_sc_table_design').find('.tr').length;
            for (let i = 0; i < a; i++) {
                if (i > 0 && i % 2 == 0) {
                    $('.woo_sc_table_design').find('.tr').eq(i).find('.td').css({backgroundColor: ui.color.toString()});
                    $('.woo_sc_table_design').find('.tr').eq(i).find('input[type="text"]').css({backgroundColor: ui.color.toString()});
                }
            }
        }
    });

    $('#woo_sc_even_rows_text_color').wpColorPicker({
        change: function (event, ui) {
            let lengthRows = $('.tr').length;
            for (let i = 1; i < lengthRows; i++) {
                if (i > 0 && i % 2 == 0) {
                    $('.woo_sc_table_design').find('.tr').eq(i).find('.td').find('input[type="text"]').css({color: ui.color.toString()});
                }
            }
        }
    });
    $('#woo_sc_odd_rows_color').wpColorPicker({
        change: function (event, ui) {
            let a = $('.woo_sc_table_design').find('.tr').length;
            for (let i = 0; i < a; i++) {
                if (i > 0 && i % 2 !== 0) {
                    $('.woo_sc_table_design').find('.tr').eq(i).find('.td').css({backgroundColor: ui.color.toString()});
                    $('.woo_sc_table_design').find('.tr').eq(i).find('input[type="text"]').css({backgroundColor: ui.color.toString()});
                }
            }
        }
    });
    $('#woo_sc_odd_rows_text_color').wpColorPicker({
        change: function (event, ui) {
            let lengthRows = $('.tr').length;
            for (let i = 1; i < lengthRows; i++) {
                if (i > 0 && i % 2 !== 0) {
                    $('.woo_sc_table_design').find('.tr').eq(i).find('.td').find('input[type="text"]').css({color: ui.color.toString()});
                }
            }
        }
    });

    $('#woo_sc_border_color').wpColorPicker({
        change: function (event, ui) {
            $('.woo_sc_table_design').find('.tr').find('.td').css({borderColor: ui.color.toString()});
        }
    });
    //________________________________________________________________
    /*Import CSV file*/
    $('#sc_import_csv_btn').on('click', function () {
        //import file click
        $('#sc_import_csv').trigger('click');
    });
    //if import type change
    $('#sc_import_csv').on('change', function () {
        var file = document.querySelector('#sc_import_csv').files[0];
        /*Check file extension*/
        var ext = file['name'].split('.').pop().toLowerCase();
        var extension = 'csv';
        if (extension != ext) {
            alert('Required file extension is CSV!');
        } else {
            var confirm_import = confirm('Size chart in CSV file will replace the current table?');
            if (confirm_import) {
                let lengthTr, lengthTd, dem, countbtntop;
                let needrows = '', needcolumns = '';
                lengthTr = $('.woo_sc_table_design').find('.tr').length;
                lengthTd = $('.woo_sc_table_design').find('.tr:first').find('.td').length;
                countbtntop = $('.woo_sc_table_design').find('tr:first').find('td').length;

                var reader = new FileReader();
                reader.readAsText(file);

                //When the file finish load
                reader.onload = function (event) {

                    //get the file.
                    var csv = event.target.result;
                    //split and get the rows in an array
                    var arr = csv.split('\n');
                    var rows = arr.filter(item => item != "");
                    //ADD rows
                    let rows_number = rows.length;
                    needrows = rows_number - lengthTr;
                    if (needrows < 0) {
                        let abs_need_rows = Math.abs(needrows);
                        for (let i = 0; i < abs_need_rows; i++) {
                            $('.woo_sc_table_design').find('.tr:last').remove();
                        }
                    } else {
                        for (let i = 0; i < needrows; i++) {
                            $('.woo_sc_table_design').append(addrows(lengthTd));
                        }
                        let lengthRows = $('.tr').length;
                        if (lengthRows > 1) {
                            $('.delrows').removeAttr('disabled');
                            $('.delrows').addClass('enable');
                        }
                    }
                    // get max row in csv file
                    var length_rows = [];
                    for (var i = 0; i < rows.length; i++) {
                        let count = rows[i].split(',');
                        length_rows[i] = count.length;
                    }
                    var max_row = Math.max.apply(Math, length_rows);
                    //add cols
                    needcolumns = max_row - countbtntop + 1;
                    if (needcolumns < 0) {
                        let abs_need_cols = Math.abs(needcolumns);
                        for (let i = 0; i < abs_need_cols; i++) {
                            $('.woo_sc_table_design').find('.tr').find('.td:last').remove();
                            $('.tableButton.addcol:last').closest('td').remove();
                        }
                    } else {
                        let addNewColBtn = "<td>"
                        addNewColBtn += "<div class='woo_sc_div_tablebutton_cols'><button  type='button'  class='tableButton addcol'>+</button><button  type='button'  class='tableButton delcol' disabled>-</button></div>"
                        addNewColBtn += "</td>";
                        let addNewCol = "<td class='td'>"
                        addNewCol += "<div class='woo_sc_table_input'><input type='text' class='input' name='inputTable'  placeholder='Enter text..' autocomplete='off'></div>"
                        addNewCol += "</td>";
                        //add btn
                        for (let j = 0; j < needcolumns; j++) {
                            $('.woo_sc_table_design').find('tr').eq(0).find('td').eq(lengthTd - 1).before(addNewColBtn);
                        }
                        for (let i = 0; i < needcolumns; i++) {
                            $('.addrows').closest('td').before(addNewCol);
                        }
                        let lengthCol = $('.delcol').length;
                        if (lengthCol > 1) {
                            $('.delcol').removeAttr('disabled');
                            $('.delcol').addClass('enable');
                        }
                    }
                    //___________________________________________
                    $('.woo_sc_table_design').find('.td').find('input[type="text"]').val('');
                    // // move line by line
                    for (var i = 0; i < rows.length; i++) {
                        //split by separator (,) and get the columns
                        var cols = rows[i].split(',');

                        //move column by column
                        for (var j = 0; j < cols.length; j++) {
                            /*the value of the current column.
                            Do whatever you want with the value*/
                            var value = cols[j];
                            $('.woo_sc_table_design').find('.tr').eq(i).find('.td').eq(j).find('input[type="text"]').val(value);
                        }

                    }
                    css_default();
                    css_border();
                }
                /*clear val import btn*/
                $('#sc_import_csv').val('');

            } else {
                $('#sc_import_csv').val('');
            }
        }
    });
    /*border css*/
    $('#woo_sc_horizontal_width').on('change', function () {
        let hor = $('#woo_sc_horizontal_width').val() + 'px ';
        let hor_style = $('#woo_sc_horizontal_border_style').val() + ' ';
        let color = $('#woo_sc_border_color').val();
        let val = hor + hor_style + color;
        $('.woo_sc_table_design').find('.td').css('border-top', val);
        $('.woo_sc_table_design').find('.td').css('border-bottom', val);

    });
    $('#woo_sc_vertical_width').on('change', function () {

        let ver = $('#woo_sc_vertical_width').val();
        let color = $('#woo_sc_border_color').val();
        let style = $('#woo_sc_vertical_border_style').val();
        let val = ver + 'px ' + style + ' ' + color;
        $('.woo_sc_table_design').find('.td').css('border-left', val);
        $('.woo_sc_table_design').find('.td').css('border-right', val);
    });
    $('#woo_sc_horizontal_border_style').on('change', function () {
        let style = $('#woo_sc_horizontal_border_style').val();
        let hor = $('#woo_sc_horizontal_width').val();
        let color = $('#woo_sc_border_color').val();
        let val = hor + 'px ' + style + ' ' + color;
        $('.woo_sc_table_design').find('.td').css('border-top', val);
        $('.woo_sc_table_design').find('.td').css('border-bottom', val);
    });
    $('#woo_sc_vertical_border_style').on('change', function () {
        let style = $('#woo_sc_vertical_border_style').val();
        let ver = $('#woo_sc_vertical_width').val();
        let color = $('#woo_sc_border_color').val();
        let val = ver + 'px ' + style + ' ' + color;
        $('.woo_sc_table_design').find('.td').css('border-left', val);
        $('.woo_sc_table_design').find('.td').css('border-right', val);
    });
    $('#woo_sc_border_color').on('change', function () {
        let color = $('#woo_sc_border_color').val();
        $('.woo_sc_table_design').find('.td').css('border-color', color);
    });

    /*Load css border*/
    function css_border() {

        $('#woo_sc_horizontal_width').trigger('change');
        $('#woo_sc_vertical_width').trigger('change');
    }

    css_border();
    //Template
    /*
    let tempArr = {
        tem_black: {
            bgHead: '',
            textHead: '#ffffff',
            evenRow: '#ffffff',
            textEvenRow: '#000000',
            oddRow: '#ffffff',
            textOddRow: '#000000',
            horWidth: '0',
            horStyle: 'dotted',
            verWidth: '1',
            verStyle: 'dotted'
        }
    };

    $('#woo_sc_template').on('change', function () {
        let val = $(this).val();
        let data = tempArr[val];

        data.bgHead = data.bgHead || '#ff0000';

        $('#woo_sc_head_color').val(data.bgHead).trigger('change');
        $('#woo_sc_text_head_color').val(data.textHead).trigger('change');
        $('#woo_sc_even_rows_color').val(data.evenRow).trigger('change');
        $('#woo_sc_even_rows_text_color').val(data.textEvenRow).trigger('change');
        $('#woo_sc_odd_rows_color').val(data.oddRow).trigger('change');
        $('#woo_sc_odd_rows_text_color').val(data.textOddRow).trigger('change');
    });*/

    $('#woo_sc_template').on('change', function () {
        let tem = $('#woo_sc_template').val();
        if (tem === 'tem_black') {
            template_color('#000000', '#ffffff', '#dbdbdb', '#000000',
                '#ffffff', '#000000', '0', 'dotted', '1', 'dotted');
        }
        if (tem === 'tem_blue') {
            template_color('#2185d0', '#FFFFFF', '#cceafc', '#000000',
                '#FFFFFF', '#000000', '0', '', '0');
        }
        if (tem === 'tem_red') {
            template_color('#db2828', '#FFFFFF', '#ffd9d8', '#000000',
                '#FFFFFF', '#000000', '', '', '0', '', '#ea0707');
        }
        if (tem === 'tem_teal') {
            template_color('#00b5ad', '#FFFFFF', '#bfecff', '#227dd8',
                '#edf8ff', '#227dd8', '2', '', '2', '', '#ffffff');
        }
        if (tem === 'tem_orange') {
            template_color('#ff772e', '#ffffff', '#ffefe0', '#c10000',
                '#ffffff', '#666666', '0', '', '0');
        }
        if (tem === 'tem_oliver') {
            template_color('#b5cc18', '#ffffff', '#d6ffbf', '#227dd8',
                '#edfff3', '#227dd8', '0', '', '1', '', '#77ce48');
        }
        if (tem === 'tem_green') {
            template_color('#21ba45', '#ffffff', '#c1ffd1', '#0084d1',
                '#ffffff', '#d80000', '1', '', '0', '', '#21ba45');
        }
        if (tem === 'tem_purple') {
            template_color('#a333c8', '#ffffff', '#f1bcff', '#0015ff',
                '#fbf4ff', '#0015ff', '0', '', '1', '', '#ffffff');
        }
        if (tem === 'tem_pink') {
            template_color('#f9638b', '#ffffff', '#ffbcce', '#eb596e',
                '#ffffea', '#eb596e', '0', '', '0');
        }
        if (tem === 'tem_yellow') {
            template_color('#fbbd08', '#4d375d', '#ffffc1', '#ef4f4f',
                '#fcfff7', '#ef4f4f', '1', 'dashed', '1', 'dashed', '#ff8577');
        }

    });

    function template_color(head, tex_head, even_row, text_even_row, odd_roww, text_odd_row, hor_width, hor_bor_sty, ver_width, ver_bor_sty, bor_color) {
        $('#woo_sc_head_color').val(head).trigger('change');
        $('#woo_sc_text_head_color').val(tex_head).trigger('change');
        $('#woo_sc_even_rows_color').val(even_row).trigger('change');
        $('#woo_sc_even_rows_text_color').val(text_even_row).trigger('change');
        $('#woo_sc_odd_rows_color').val(odd_roww).trigger('change');
        $('#woo_sc_odd_rows_text_color').val(text_odd_row).trigger('change');
        if (hor_width !== undefined && hor_width !== "") {
            $('#woo_sc_horizontal_width').val(hor_width).trigger('change');
        } else {
            hor_width = 1;
            $('#woo_sc_horizontal_width').val(hor_width).trigger('change');
        }
        if (hor_bor_sty !== undefined && hor_bor_sty !== "") {
            $('#woo_sc_horizontal_border_style').val(hor_bor_sty).trigger('change');
        } else {
            hor_bor_sty = 'solid';
            $('#woo_sc_horizontal_border_style').val(hor_bor_sty).trigger('change');
        }


        if (ver_width !== undefined && ver_width !== "") {
            $('#woo_sc_vertical_width').val(ver_width).trigger('change');
        } else {
            ver_width = 1;
            $('#woo_sc_vertical_width').val(ver_width).trigger('change');
        }
        if (ver_bor_sty !== undefined && ver_bor_sty !== "") {
            $('#woo_sc_vertical_border_style').val(ver_bor_sty).trigger('change');
        } else {
            ver_bor_sty = 'solid';
            $('#woo_sc_vertical_border_style').val(ver_bor_sty).trigger('change');
        }
        if (bor_color !== undefined && bor_color !== "") {
            $('#woo_sc_border_color').val(bor_color).trigger('change');
        } else {
            bor_color = '#cccccc';
            $('#woo_sc_border_color').val(bor_color).trigger('change');
        }
    }

});


