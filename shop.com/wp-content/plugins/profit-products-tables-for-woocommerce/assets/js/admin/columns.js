'use strict';
class WOOT_GeneratedColumns extends WOOT_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'woot_save_table_column_field';//reinit ajax action
        this.delete_action = 'woot_delete_table_column';//ajax action for deleting
        this.switcher_action = 'woot_save_table_column_field';

        //call it here because name of the action here is another and no init applied after parent constructor init (super)
        this.init_switchers_listener();
        this.init_json_fields_saving();

        document.getElementById('woot-popup-columns-template').querySelector('.woot-modal-inner-content').addEventListener('scroll', (e) => {
            this.add_scroll_action(document.getElementById('tabs-columns').querySelector('.woot-columns-table-zone'));
        });
    }

    //destructor
    destructor() {
        console.log('destructor here');
        if (this.change_cell_ev_handler) {
            document.removeEventListener('after_' + this.save_table_field_action, this.change_cell_ev_handler);
        }
    }

    do_after_draw() {
        super.do_after_draw();
        let _this = this;

        setTimeout(() => {
            jQuery('.woot-columns-table-zone table tbody').sortable({
                items: 'tr',
                update: function (event, ui) {
                    let tr_pids = [];
                    _this.table.parentElement.querySelectorAll('tbody > tr').forEach(function (tr) {
                        tr_pids.push(parseInt(tr.getAttribute('data-pid'), 10));
                    });

                    if (tr_pids.length > 1) {
                        _this.save(_this.request_data.post_id, 'pos_num', tr_pids);
                    }
                },
                opacity: 0.8,
                cursor: 'crosshair',
                handle: '.woot-tr-drag-and-drope',
                placeholder: 'woot-tr-highlight'
            });

        }, 333);

    }

    create(prepend = true) {
        this.message(woot_helper_vars.lang.creating + ' ...', 'warning');

        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'woot_create_table_column',
                post_id: this.settings.post_id,
                prepend: Number(prepend)
            })
        }).then(response => response.json()).then(data => {
            this.message(woot_helper_vars.lang.created);
            this.settings.table_data = data;
            this.request_data.orderby = 'pos_num';//to allow new row appear on its position
            this.request_data.order = 'asc';
            this.draw_data(null);
        }).catch((err) => {
            this.message(err, 'error', 5000);
        });
    }

    refresh() {
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'woot_refresh_columns_table',
                post_id: this.settings.post_id
            })
        }).then(response => response.json()).then(data => {
            this.settings.table_data = data;
            this.draw_data(null);
        }).catch((err) => {
            this.message(err, 'error', 5000);
        });
    }

    close_popup() {
        document.getElementById('woot-popup-columns-template').style.display = 'none';
    }
}