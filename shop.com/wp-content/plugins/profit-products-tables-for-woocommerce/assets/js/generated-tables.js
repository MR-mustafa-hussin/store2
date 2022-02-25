'use strict';
class WOOT_GeneratedTables extends DataTable23 {
    constructor(table_data, table_html_id, additional = {}) {
        super(table_data, table_html_id, additional);

        DataTable23.do_not_print.push('add_to_cart');
        DataTable23.do_not_print.push('single');
        DataTable23.do_not_print.push('gallery');
        DataTable23.do_not_print.push('upsell');
        DataTable23.do_not_print.push('cross_sell');
        DataTable23.do_not_print.push('variations');
        DataTable23.do_not_print.push('grouped');
        DataTable23.do_not_print.push('file');

        if (this.settings.stop_notice.length > 0) {
            return;
        }

        //***
        let _this = this;
        _this.wrapper.parentElement.querySelectorAll('.woot-text-search').forEach(function (input) {
            _this.attach_keyup_event(input);
            _this.attach_mouseup_event(input);
        });


        if (typeof WOOT_Filter !== 'undefined') {
            this.filter_form = new WOOT_Filter(this);
        }

        //WOOF ajax compatibility
        if (_this.request_data.post_type === 'product') {
            document.addEventListener('woof-ajax-form-redrawing', function (e) {
                document.querySelector('.woof[data-ajax-redraw="1"] .woof_submit_search_form_container .woof_submit_search_form').style.display = 'none';
                let data = new URL(e.detail.link).search;

                let filter_data = {};
                data.replace(/[?&]+([^=&]+)=([^&]*)/gi,
                        function (m, key, value) {
                            filter_data[key] = value;
                        });

                if (typeof (_this.request_data.filter_data.post_title) !== 'undefined') {
                    filter_data.post_title = _this.request_data.filter_data.post_title;
                }

                if (Object.keys(filter_data).length > 1) {
                    _this.draw_data(filter_data);
                } else {
                    _this.draw_data(null);
                }

                woof_is_ajax = 1;//to make reset button ajaxable!!
            });
        }
        //avalon23 compatibility  ajax
        document.addEventListener('avalon23-filter-ajax-search', function (e) {
                let data = new URL(e.detail.link).search;

                let filter_data = {};
                data.replace(/[?&]+([^=&]+)=([^&]*)/gi,
                        function (m, key, value) {
                            filter_data[key] = value;
                        });

                if (typeof (_this.request_data.filter_data.post_title) !== 'undefined') {
                    filter_data.post_title = _this.request_data.filter_data.post_title;
                }

                if (Object.keys(filter_data).length > 1) {
                    _this.draw_data(filter_data);
                } else {
                    _this.draw_data(null);
                }        
        });
        //***
        //sort-by select installation
        if (this.settings.mode === 'ajax' && !this.settings.switch_to_json) {
            let orderby_select = this.wrapper.parentElement.querySelector('.woot-order-select');
            if (orderby_select) {
                this.order_select_connect_id = woot_helper.create_id('sel');
                this.order_select = new Select23(orderby_select, JSON.parse(orderby_select.innerText), this.order_select_connect_id, {});
                this.order_select.ordered_by_table = this.request_data.orderby;//before by select
                orderby_select.removeAttribute('style');

                document.addEventListener('select23-change', (e) => {
                    if (e.detail.connect_id === this.order_select_connect_id) {
                        let val = e.detail.value;
                        if (parseInt(val) === 0) {
                            val = this.order_select.ordered_by_table;
                        } else {
                            //prefix orderby_select_ added for the recognition on the server that ordering is by the drop-down
                            val = 'orderby_select_' + val;
                        }

                        this.request_data.orderby = val;
                        this.redraw_table();
                    }
                });

                document.addEventListener('woot-do-after-draw', (e) => {
                    if (e.detail.otable === this) {
                        if (!(this.request_data.orderby.match(/orderby_select_/g) || []).length && this.order_select) {
                            this.order_select.container.querySelector('select').value = 0;
                        }
                    }
                });

            }
        }

        this.init_drop_downs();
    }

    //tables under textinputs
    init_drop_downs() {
        document.querySelectorAll('.woot-woocommerce-text-search-container').forEach(function (item) {
            let wrapper = item.querySelector('.woot-woocommerce-text-search-wrapper');
            item.querySelector('input[type="search"]').addEventListener('change', function (e) {

                if (this.value) {

                    let _this = this;
                    wrapper.style.display = 'block';
                    //for hidden elements (as range slider) to redraw correctly (after creating in hidden container)
                    window.dispatchEvent(new Event('resize'));


                    //***

                    this.parentElement.querySelector('.woot-text-search').value = this.value;
                    this.parentElement.querySelector('.woot-text-search').dispatchEvent(new CustomEvent('keyup', {detail: {
                            woo_text_search: true
                        }}));

                    //***

                    this.addEventListener('mouseup', function (e) {
                        e.stopPropagation();

                        if (_this.value.length > 0) {
                            //click on cross
                            setTimeout(function () {
                                if (_this.value.length === 0) {
                                    wrapper.style.display = 'none';
                                }
                            }, 5);
                        }
                    });

                    this.addEventListener('keydown', function (e) {
                        e.stopPropagation();

                        if (e.keyCode === 27) {//escape
                            wrapper.style.display = 'none';
                        }

                        return false;
                    });


                    this.addEventListener('click', (e) => {
                        if (this.value.length > 0) {
                            wrapper.style.display = 'block';
                            window.dispatchEvent(new Event('resize'));
                        }
                    });

                    document.addEventListener('click', (e) => {
                        if (!item.contains(e.target)) {
                            wrapper.style.display = 'none';
                        }
                    });
                }
            });

            //clean search input if reset button clicked
            document.addEventListener('woot-filter-reset', function (e) {
                if (item.querySelector('.woot-data-table').id === e.detail.dt.table_html_id) {
                    item.querySelector('input[type="search"]').value = '';
                }
            });



        });
    }

    define_display_cell_info() {

        //clean previous defined
        document.querySelectorAll('.table23-display-cell-info').forEach(function (d) {
            d.style.display = 'none';
        });

        //***

        if (this.request_data.post_type === 'product' && this.container.classList.contains('woot_woocommerce_tables')) {

            this.display = document.createElement('div');
            this.display.className = 'table23-display-cell-info';
            this.wrapper.parentElement.insertBefore(this.display, this.wrapper);

            if (this.table.querySelectorAll('tbody tr').length > 0) {
                let _this = this;
                this.table.querySelectorAll('tbody tr').forEach(function (tr) {
                    if (tr.querySelector('.table23_td_post_title')) {
                        let tid = tr.querySelector('.table23_td_id');

                        if (tid) {
                            tid = '#' + tid.innerText;
                        } else {
                            tid = '';
                        }

                        tr.addEventListener(_this.is_mobile ? 'touchstart' : 'mouseover', function (e) {
                            if (!_this.show_compact) {
                                _this.display.style.display = 'block';
                                let data_field = e.target.getAttribute('data-field');
                                let txt = tid + ' ' + (tr.querySelector('.table23_td_post_title a') ? tr.querySelector('.table23_td_post_title a').innerText : '');

                                if (_this.thead.querySelector(`th[data-key="${data_field}"]`)) {
                                    txt += ', ' + _this.thead.querySelector(`th[data-key="${data_field}"]`).innerText;
                                }

                                _this.display.innerHTML = txt;
                            }
                        });

                        document.addEventListener(_this.is_mobile ? 'touchend' : 'mouseout', function (e) {
                            if (!_this.show_compact) {
                                _this.display.innerHTML = '';
                                _this.display.style.display = 'none';
                            }
                        });
                    }
                });
            }
        }
    }

    do_after_draw() {
        super.do_after_draw();
        document.dispatchEvent(new CustomEvent('woot-do-after-draw', {detail: {otable: this}}));

        if (this.container.classList.contains('woot-define-display-cell-info')) {
            this.define_display_cell_info();
        }


        //scroll bar is for products tables
        if (this.show_compact) {

            if (this.scrollbar23) {
                this.scrollbar23.remove();
                this.wrapper.classList.remove('horizontal-scrollbar23-attached');
            }

        } else {

            if (this.container.classList.contains('woot-data-table')) {
                if (!this.wrapper.classList.contains('horizontal-scrollbar23-attached')) {
                    setTimeout(() => {
                        this.wrapper.classList.add('horizontal-scrollbar23-attached');
                        if (typeof this.additional.no_scroll_bar === 'undefined') {
                            this.scrollbar23 = new HorizontalScrollbar23(this.wrapper);
                        }
                    }, 777);
                }
            }

        }

        //***
        //load tables in the cells
        if (this.table.querySelectorAll('.woot-data-table').length) {
            this.table.querySelectorAll('.woot-data-table').forEach((t) => {
                let id = t.getAttribute('id');
                new WOOT_GeneratedTables(JSON.parse(this.table.querySelector(`[data-table-id="${id}"]`).innerText), id, {no_scroll_bar: true});
            });
        }

        //init switchers
        Array.from(this.table.querySelectorAll('.switcher23')).forEach((button) => {
            woot_helper.init_switcher(button);
        });

        //for hidden elements (as range slider) to redraw correctly (after creating in hidden container)
        window.dispatchEvent(new Event('resize'));
    }

    recreate_scroll_bar() {
        if (this.scrollbar23) {
            this.scrollbar23.remove();
            this.wrapper.classList.remove('horizontal-scrollbar23-attached');
        }

        this.wrapper.classList.add('horizontal-scrollbar23-attached');
        this.scrollbar23 = new HorizontalScrollbar23(this.wrapper);
    }

    //use this for class filter also
    attach_keyup_event(input) {
        let _this = this;

        input.addEventListener('keyup', function (e) {

            let add = {};
            let do_search = false;
            let key = this.getAttribute('data-key');

            if (e.keyCode === 13 || typeof e.detail.woo_text_search !== 'undefined') {

                if (input.value.length === 0) {
                    if (input.classList.contains('woot-not-ready-text-search')) {
                        //if user want to reset by empty string nad Enter key
                        if (typeof _this.request_data.filter_data[key] !== 'undefined') {
                            input.classList.remove('woot-not-ready-text-search');
                            delete _this.request_data.filter_data[key];
                            _this.settings.total_rows_count = 0;
                            _this.draw_pagination();
                            if (!_this.allow_reset) {
                                _this.allow_reset_force = true;//fix for load more button
                            }
                            _this.draw_data();
                            return true;
                        }
                    }
                }

                //***

                if (input.value.length < Number(input.getAttribute('minlength'))) {
                    input.classList.add('woot-not-ready-text-search');
                    return true;
                }

                //woo text search works - table clean for new search request
                if (typeof e.detail.woo_text_search !== 'undefined') {
                    _this.reset();
                    _this.settings.total_rows_count = 0;
                    _this.draw_pagination();
                }


                add = {};
                add[key] = input.value;
                do_search = true;
            }

            if (e.keyCode === 27) {
                delete _this.request_data.filter_data[key];
                do_search = true;
                input.classList.remove('woot-not-ready-text-search');
            }


            if (do_search) {
                input.classList.remove('woot-not-ready-text-search');
                _this.request_data.current_page = 0;
                if (typeof _this.request_data.filter_data !== 'object' && _this.request_data.filter_data.length > 0) {
                    _this.request_data.filter_data = JSON.parse(_this.request_data.filter_data);
                }
                _this.request_data.filter_data = _this.extend(_this.request_data.filter_data, add);
                if (!_this.allow_reset) {
                    _this.allow_reset_force = true;//fix for load more button
                }

                //_this.use_cache = false;//23-04-2020 disabled as this flag disable cache after text loading
                _this.draw_data();
            }

            //***

            document.dispatchEvent(new CustomEvent('woot-filter-is-changed', {detail: {
                    dt: _this
                }}));
        });
    }

    //use this for class filter also
    attach_mouseup_event(input) {
        let _this = this;

        //click on cross
        input.addEventListener('mouseup', function (e) {
            e.stopPropagation();

            if (input.value.length > 0) {
                setTimeout(() => {
                    if (input.value.length === 0) {
                        input.classList.remove('woot-not-ready-text-search');
                        delete _this.request_data.filter_data[this.getAttribute('data-key')];
                        _this.request_data.current_page = 0;
                        _this.draw_data();

                        document.dispatchEvent(new CustomEvent('woot-filter-is-changed', {detail: {
                                dt: _this
                            }}));
                    }
                }, 5);
            }
        });
    }
}

