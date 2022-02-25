'use strict';
class WOOT_Settings extends WOOT_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'woot_save_settings_field';//reinit ajax action
        this.switcher_action = 'woot_save_settings_field';
        this.init_switchers_listener();

    }

    do_after_draw() {
        super.do_after_draw();
    }
}
