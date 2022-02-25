'use strict';

document.addEventListener('woot-call-popup', function (e) {
    let call_id = 'woot_options_columns_table';
    if (e.detail.call_id === call_id) {
        new WOOT_ColumnsFieldsOptions(JSON.parse(document.querySelector(`[data-table-id="${call_id}"]`).innerText), call_id);
    }
});

class WOOT_ColumnsFieldsOptions extends WOOT_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'woot_save_table_column_field_option';//reinit ajax action
        this.switcher_action = 'woot_save_table_column_field_option';
        this.init_switchers_listener();
    }

    do_after_draw() {
        super.do_after_draw();
        //as we can have different elements in field value elements actons should be inited here
        this.init_html_items_action('select', 'change');
        this.init_html_items_action('input[type="text"]', 'change');

        //https://seballot.github.io/spectrum/#toc5
        this.table.querySelectorAll('.woot-color-field').forEach(item => {
            jQuery(item).spectrum({
                type: 'text',
                allowEmpty: true,
                showInput: true,
                change: function (color) {
                    if (color) {
                        item.setAttribute('value', color.toHexString());
                        item.dispatchEvent(new Event('change'));
                    }
                }
            });
        });

    }

    init_html_items_action(html_item_type, action) {
        this.table.querySelectorAll(`.table23_td_value ${html_item_type}.woot-columns-field-option`).forEach(item => {
            item.addEventListener(action, e => {
                e.stopPropagation();

                let table_id = item.getAttribute('data-table-id');
                let field_id = item.getAttribute('data-field-id');
                let key = item.getAttribute('data-key');

                this.message(woot_helper_vars.lang.saving, 'warning');

                fetch(this.settings.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: this.prepare_ajax_form_data({
                        action: this.save_table_field_action,
                        table_id: table_id,
                        field_id: field_id,
                        key: key,
                        value: item.value
                    })
                }).then(response => response.text()).then(data => {
                    this.message(woot_helper_vars.lang.saved);
                }).catch((err) => {
                    this.message(woot_helper_vars.lang.error + ' ' + err, 'error');
                });

                return true;
            });
        });
    }

}