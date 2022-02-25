'use strict';
var woot_main_table = null;
var woot_columns_table = null;
var woot_meta_table = null;
var woot_vocabulary_table = null;
var woot_predefinition_table = null;
//***



//***
//Popup with information about all shortcode possibilities
document.addEventListener('table23-html-drawn', function (e) {

    if (e.detail.otable.table_html_id === 'woot-admin-table') {
        /*         
         e.detail.otable.table.querySelectorAll("th[data-key='shortcode']").forEach(function (item) {
         item.addEventListener('click', function (e) {
         let answer = new Object();
         document.dispatchEvent(new CustomEvent('table23-get', {detail: {
         table_html_id: item.closest('div.woot-data-table').id,
         answer: answer
         }}));
         
         new Popup23({title: woot_helper_vars.lang.shortcodes_help, action: 'woot_get_smth', what: 'shortcodes_help'});
         
         }, false);
         });
         */
    }

    return true;
});

//***
//different backend popups data inits
document.addEventListener('woot-popup-smth-loaded', e => {
    if (e.detail.what) {
        let what = e.detail.what;

        if (typeof what === 'string') {
            try {
                what = JSON.parse(what);
            } catch (e) {
                console.log(e);
            }
        }


        if (typeof what === 'object') {
            if (typeof what.call_action !== 'undefined') {
                switch (what.call_action) {
                    case 'woot_show_column_field_option':
                        let container = e.detail.popup.node.querySelector('.woot-table-json-data');
                        new WOOT_ColumnsFieldsOptions(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
                        break;

                }
            }
        } else {
            e.detail.popup.set_content(e.detail.content);
        }
    }

});

document.addEventListener('woot-tabs-switch', e => {
    //fix when in one popup some tables
    Array.from(document.querySelectorAll('.table23-flow-header')).forEach(function (item) {
        item.style.display = 'none';
    });

    //***
    let help_link = document.getElementById('main-table-help-link');
    switch (e.detail.current_tab_link.getAttribute('href')) {
         case '#tabs-columns':
            help_link.setAttribute('href', 'https://products-tables.com/document/columns/');
            break;
        case '#tabs-meta':
            help_link.setAttribute('href', 'https://products-tables.com/document/meta/');
            break;
        case '#tabs-filter':
            help_link.setAttribute('href', 'https://products-tables.com/document/filter/');
            break;
        case '#tabs-predefinition':
            help_link.setAttribute('href', 'https://products-tables.com/document/predefinition/');
            break;
        case '#tabs-options':
            help_link.setAttribute('href', 'https://products-tables.com/document/options/');
            break;

        case '#tabs-custom-css':
            help_link.setAttribute('href', 'https://products-tables.com/document/custom-css/');

            //Custom CSS
            if (!woot_main_table.custom_css_editor) {
                woot_main_table.get_custom_css();
            }

            break;
    }
});

//overwriting CTRL+S behaviour for saving custom CSS
document.addEventListener('keydown', function (e) {
    if ((window.navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey) && e.keyCode === 83) {
        if (woot_main_table.custom_css_editor) {
            if (window.getComputedStyle(document.getElementById('tabs-custom-css'), null).getPropertyValue('display') === 'block') {
                woot_main_table.save_custom_css();
                e.preventDefault();
            }
        }
    }
}, false);


window.onload = function () {

    new WOOT_Tabs(document.querySelectorAll('.woot-tabs'));

    //init data tables
    document.querySelectorAll('.woot-table-json-data').forEach(function (container) {
        if (container.getAttribute('data-table-id') === 'woot-admin-table') {
            woot_main_table = new WOOT_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
        } else {
            new WOOT_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
        }
    });

    //+++
    //settings
    new WOOT_Settings(JSON.parse(document.querySelector('#tabs-main-settings .woot-settings-json-data').innerText), 'woot-settings-table');
    if (document.querySelector('.woot-vocabulary-json-data')) {
        woot_vocabulary_table = new WOOT_GeneratedVocabulary(JSON.parse(document.querySelector('.woot-vocabulary-json-data').innerText), 'woot-vocabulary-table');
    }

    //***

    window.addEventListener('offline', function (e) {
        //woot_helper.message(woot_helper_vars.lang.offline, 'error', -1);
    });

    window.addEventListener('online', function (e) {
        woot_helper.message(woot_helper_vars.lang.online, 'notice');
    });

    if (woot_helper_vars.mode === 'dev') {
        window.addEventListener('error', function (e) {
            woot_helper.message(`Error: ${e.message}, ${e.filename}, #${e.lineno}`, 'error', -1);
        });
    }

};


//***

class WOOT_Tabs {
    constructor(containers) {
        if (containers.length > 0) {
            for (let i = 0; i < containers.length; i++) {
                this.init(containers[i]);
            }
        }
    }

    init(container) {
        container.querySelectorAll('nav li a').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                a.parentElement.parentElement.querySelector('li.tab-current').removeAttribute('class');
                a.parentElement.className = 'tab-current';
                container.querySelector('.content-current').removeAttribute('class');
                container.querySelector('.content-wrap ' + a.getAttribute('href')).className = 'content-current';

                document.dispatchEvent(new CustomEvent('woot-tabs-switch', {detail: {
                        current_tab_link: a
                    }}));

                return false;
            });
        });
    }
}


function woot_change_thumbnail(button) {
    var post_id = button.closest('tr').getAttribute('data-pid');
    var field = 'thumbnail';

    var image = wp.media({
        title: woot_helper_vars.lang.select_table_thumb,
        multiple: false,
        library: {
            type: ['image']
        }
    }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                uploaded_image = uploaded_image.toJSON();

                if (typeof uploaded_image.url != 'undefined') {
                    if (typeof uploaded_image.sizes.thumbnail !== 'undefined') {
                        button.querySelector('img').setAttribute('src', uploaded_image.sizes.thumbnail.url);
                    } else {
                        button.querySelector('img').setAttribute('src', uploaded_image.url);
                    }

                    woot_helper.message(woot_helper_vars.lang.saving, 'warning');

                    fetch(ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: woot_helper.prepare_ajax_form_data({
                            action: 'woot_save_table_field',
                            post_id: post_id,
                            field: field,
                            value: uploaded_image.id
                        })
                    }).then(response => response.text()).then(data => {
                        woot_helper.message(woot_helper_vars.lang.saved, 'notice');
                    }).catch((err) => {
                        woot_helper.message(err, 'error', 5000);
                    });

                }
            });


    return false;

}


function woot_import_options() {

    if (document.getElementById('woot-import-text').value) {
        let data = JSON.parse(document.getElementById('woot-import-text').value);

        if (typeof data === 'object') {
            if (confirm(woot_helper_vars.lang.sure)) {
                woot_helper.message(woot_helper_vars.lang.importing, 'warning');
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json'
                    },
                    credentials: 'same-origin',
                    body: woot_helper.prepare_ajax_form_data({
                        action: 'woot_import_data',
                        data: JSON.stringify(data)
                    })
                }).then(response => response.text()).then(data => {
                    woot_helper.message(woot_helper_vars.lang.imported, 'notice');
                    window.location.reload();
                }).catch((err) => {
                    woot_helper.message(err, 'error', 5000);
                });
            }
        } else {
            woot_helper.message(woot_helper_vars.lang.error, 'error', 5000);
        }
    }
}