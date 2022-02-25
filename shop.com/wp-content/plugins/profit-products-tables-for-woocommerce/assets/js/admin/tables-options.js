'use strict';
class WOOT_TablesOptions extends WOOT_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'woot_save_table_option';//reinit ajax action
        this.switcher_action = 'woot_save_table_option';
        this.init_switchers_listener();

        /*
        document.getElementById('woot-popup-columns-template').querySelector('.woot-modal-inner-content').addEventListener('scroll', (e) => {
            this.add_scroll_action(document.getElementById('tabs-options').querySelector('.woot-table-options-zone'));
        });
        */
    }

    do_after_draw() {
        super.do_after_draw();
    }

}