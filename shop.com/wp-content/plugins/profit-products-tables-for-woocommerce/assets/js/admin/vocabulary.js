'use strict';
class WOOT_GeneratedVocabulary extends WOOT_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'woot_save_vocabulary_field';//reinit ajax action
        this.switcher_action = 'woot_save_vocabulary_field';
        this.init_switchers_listener();
        this.init_json_fields_saving();
    }

    do_after_draw() {
        super.do_after_draw();
    }

    create() {
        this.message(woot_helper_vars.lang.creating, 'warning');
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'woot_create_vocabulary_field',
                tail: woot_helper.create_id('a')
            })
        }).then(response => response.json()).then(data => {
            this.message(woot_helper_vars.lang.created);
            woot_vocabulary_table.settings.table_data = data;
            woot_vocabulary_table.draw_data(null);
        }).catch((err) => {
            this.message(woot_helper_vars.lang.error + ' ' + err, 'error');
        });
    }

    delete(id) {
        if (confirm(woot_helper_vars.lang.sure)) {
            this.message(woot_helper_vars.lang.deleting, 'warning');
            woot_vocabulary_table.delete_row(id);
            fetch(this.settings.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: this.prepare_ajax_form_data({
                    action: 'woot_delete_vocabulary_field',
                    id: id
                })
            }).then(response => response.json()).then(data => {
                this.message(woot_helper_vars.lang.deleted);
                woot_vocabulary_table.settings.table_data = data;
            }).catch((err) => {
                this.message(woot_helper_vars.lang.error + ' ' + err, 'error');
            });
        }
    }
}
