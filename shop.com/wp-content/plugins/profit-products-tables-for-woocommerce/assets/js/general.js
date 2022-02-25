'use strict';
var touch_start_x = 0;//for any touch operations

window.addEventListener('load', function () {

    if (typeof DataTable23 === 'undefined') {
        woot_init_actions();
        return;
    }


    DataTable23.selected_lang = woot_helper_vars.selected_lang;


    //init data tables
    document.querySelectorAll('.woot-table-json-data').forEach(function (container) {
        new WOOT_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
    });

    //***

    woot_init_actions();

    //***

    document.addEventListener('woot-popup-smth-loaded', e => {

        let what = e.detail.what;

        try {
            if (typeof what === 'string') {
                what = JSON.parse(what);
            }
        } catch (e) {
            console.log(e);
        }

        //***

        if (e.detail.post_id === -1) {
            //for [woot_button id=13280 title="Deus Ex" popup_title="Table in Popup23"]
            let container = e.detail.popup.node.querySelector('.woot-table-json-data');
            if (container) {
                new WOOT_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
                woot_cart = new WOOT_Cart();
            }
        }

        //***
        //different calls
        if (typeof what.call_action !== 'undefined') {
            //let container = e.detail.popup.node.querySelector('.woot-table-json-data');
            switch (what.call_action) {
                case 'shortcodes_set':
                    e.detail.popup.node.querySelectorAll('.woot-table-json-data').forEach(function (container) {
                        new WOOT_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
                    });
                    break;
            }
        }

        //***

    });

    //***

    window.addEventListener('offline', function (e) {
        woot_helper.message(woot_helper_vars.lang.offline, 'error', -1);
    });

    window.addEventListener('online', function (e) {
        woot_helper.message(woot_helper_vars.lang.online, 'notice');
    });

    if (woot_helper_vars.mode === 'dev') {
        window.addEventListener('error', function (e) {
            //woot_helper.message(`Error: ${e.message}, ${e.filename}, #${e.lineno}`, 'error', -1);
        });
    }

});

/************************************** make interactions more rich **********************************************/

function woot_init_actions() {
    //add keyboard navigation to the gallery, etc...
    document.addEventListener('keydown', e => {

        if (document.querySelectorAll('.woot-gallery-lightbox:target').length > 0) {
            let current = null;

            switch (e.keyCode) {
                case 37:
                    //left
                    current = document.querySelector('.woot-gallery-lightbox:target .woot-gallery-nav-left a');
                    if (current) {
                        location.hash = current.hash;
                    }
                    break;

                case 39:
                    //right
                    current = document.querySelector('.woot-gallery-lightbox:target .woot-gallery-nav-right a');
                    if (current) {
                        location.hash = current.hash;
                    }
                    break;

                case 27:
                    //escape
                    current = document.querySelector('.woot-gallery-lightbox:target a.woot-gallery-close');
                    if (current) {
                        location.hash = current.hash;
                    }
                    break;
            }
        }

        //+++
        //close text popup (content, excerpt)
        if (e.keyCode === 27) {
            if (document.querySelector('.woot-more-less-container-active')) {
                woot_close_txt_container(document.querySelector('.woot-more-less-container-active'));
            }
        }

    });


    //products gallery eventization
    if ('ontouchstart' in document.documentElement) {
        document.addEventListener('touchstart', e => {
            touch_start_x = e.touches[0].clientX;
        });

        document.addEventListener('touchend', e => {
            if (document.querySelectorAll('.woot-gallery-lightbox:target').length > 0) {
                let current = null;

                let end_x = e.changedTouches[0].clientX;

                if (Math.abs(touch_start_x - end_x) > 20) {
                    if (touch_start_x > end_x) {
                        //right
                        current = document.querySelector('.woot-gallery-lightbox:target .woot-gallery-nav-right a');
                    } else {
                        //left
                        current = document.querySelector('.woot-gallery-lightbox:target .woot-gallery-nav-left a');
                    }

                    if (current) {
                        location.hash = current.hash;
                    }
                }
            }
        });
    }
}

function woot_show_filter(self) {
    let filter = self.parentElement.querySelector('.woot-filter-list');
    filter.classList.toggle('woot-hidden');

    if (filter.classList.contains('woot-hidden')) {
        self.classList.remove('woot-filter-show-btn-closed');
    } else {
        self.classList.add('woot-filter-show-btn-closed');
    }

    return false;
}


function woot_show_tab(e, tab_id) {
    e.currentTarget.parentElement.querySelectorAll('.woot-tab-link').forEach(function (item) {
        item.classList.remove('woot-tab-link-current');
    });

    e.currentTarget.parentElement.querySelectorAll('.woot-tab-content').forEach(function (item) {
        item.classList.add('woot-tab-content-hidden');
        item.classList.remove('woot-tab-content-current');
    });

    e.currentTarget.classList.add('woot-tab-link-current');
    let container = e.currentTarget.parentElement.querySelector('#' + tab_id);
    container.classList.remove('woot-tab-content-hidden');
    container.classList.add('woot-tab-content-current');

    //***

    if (container.closest('.woot-content-in-popup')) {
        if (container.querySelector('table') && container.querySelector('table').classList.contains('woot-table')) {
            let t = DataTable23.tables[container.querySelector('table').parentElement.parentElement.id];
            if (t.scrollbar23) {
                t.scrollbar23.set_the_topmost();
            } else {
                document.querySelectorAll('.horizontal-scrollbar23-wrapper').forEach(function (scroll_bar) {
                    scroll_bar.style.display = 'none';
                });
            }
        }
    }

    window.dispatchEvent(new Event('scroll'));
}

function woot_open_txt_container(self) {
    if (!self.classList.contains('woot-more-less-container-active')) {
        self.classList.add('woot-more-less-container-active');
    }

    for (var link of self.getElementsByTagName('a')) {
        link.setAttribute('target', '_blank');
    }

    return true;
}

function woot_close_txt_container(self, event = null) {
    if (event) {
        event.stopPropagation();
    }

    self.closest('.woot-more-less-container').classList.remove('woot-more-less-container-active');
    return false;
}