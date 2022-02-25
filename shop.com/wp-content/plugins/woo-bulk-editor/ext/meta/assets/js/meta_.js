jQuery(function ($) {
    $('body').on('click', '.woobe_meta_delete', function () {
        $(this).parents('li').remove();
        return false;
    });

    //***

    $('#metaform').submit(function () {
        woobe_save_form(this, 'woobe_save_meta');
        return false;
    });

    //***

    //***
    //action for bulk meta_popup_editor
    $(document).on("woobe_act_meta_popup_editor_saved", {}, function (event, product_id, field_name, value) {

        if (product_id === 0) {
            //looks like we want to apply it for bulk editing
            $('#meta_popup_editor').hide();
            $("[name='woobe_bulk[" + field_name + "][value]']").val(value);
            $("[name='woobe_bulk[" + field_name + "][behavior]']").val('new');
        }

        return true;
    });

    //***

    $('#woobe_meta_add_new_btn').click(function () {
        var key = $('.woobe_meta_key_input').val();

        if (key.length > 0) {
            var html = $('#woobe_meta_li_tpl').html();
            html = html.replace(/__META_KEY__/gi, key);
            html = html.replace(/__TITLE__/gi, lang.meta.new_key);
            $('#woobe_meta_list').prepend(html);
            $('.woobe_meta_key_input').val('');
        } else {
            woobe_message(lang.meta.enter_key, 'error');
        }

        return false;
    });

    $('.woobe_meta_key_input').keydown(function (e) {
        if (e.keyCode == 13) {
            $('#woobe_meta_add_new_btn').trigger('click');
        }
    });

    //***

    $('#woobe_meta_get_btn').click(function () {
        var id = parseInt($('.woobe_meta_keys_get_input').val(), 10);

        if (id > 0) {

            $('.woobe_meta_keys_get_input').val('');

            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_meta_get_keys',
                    product_id: id
                },
                success: function (keys) {
                    if (keys.length > 0) {
                        keys = JSON.parse(keys);
                        var html = $('#woobe_meta_li_tpl').html();
                        for (var i = 0; i < keys.length; i++) {
                            var li = html.replace(/__META_KEY__/gi, keys[i]);
                            li = li.replace(/__TITLE__/gi, keys[i]);
                            $('#woobe_meta_list').prepend(li);
                        }
                    } else {
                        woobe_message(lang.meta.no_keys_found, 'error');
                    }
                }
            });

        } else {
            woobe_message(lang.meta.enter_prod_id, 'error');
        }

        return false;
    });

    $('.woobe_meta_keys_get_input').keydown(function (e) {
        if (e.keyCode == 13) {
            $('#woobe_meta_get_btn').trigger('click');
        }
    });

    //***

    $('body').on('change', '.woobe_meta_view_selector', function () {
        var value = $(this).val();
        var type_selector = $(this).parents('li').find('.woobe_meta_type_selector');
        switch (value) {
            case 'popupeditor':
                $(type_selector).val('string');
                $(type_selector).parent().hide();
                break;

            case 'meta_popup_editor':
                $(type_selector).val('string');
                $(type_selector).parent().hide();
                break;

            case 'switcher':
                $(type_selector).val('number');
                $(type_selector).parent().hide();
                break;

            default:
                $(type_selector).parent().show();
                break;
        }

        return true;
    });

});

