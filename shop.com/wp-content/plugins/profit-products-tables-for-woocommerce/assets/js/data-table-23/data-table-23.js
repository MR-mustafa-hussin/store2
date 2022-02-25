'use strict';
//Made by RealMag777
//ver.1.0.0
//19-10-2020
class DataTable23 {
    constructor(table_data, table_html_id, additional = {}) {
        this.table_html_id = table_html_id;
        this.is_mobile = 'ontouchstart' in document.documentElement;
        this.selected_lang = 'en_US';
        this.cache = {};
        this.use_cache = true;
        this.filter_form = null;
        this.allow_reset = true;
        this.additional = additional;//for flags of any custom options fir each table
        DataTable23.do_not_print = [];

        let defaults = {
            mode: 'ajax', //ajax,json
            ajax_url: '', //should be inited from outside
            heads: {title: 'Title'},
            cells_width: [],
            hide_on_mobile: [],
            orders: {title: false},
            editable: {},
            request_data: {},
            total_rows_count: 0, //how many rows at all (for pagination)
            filter_data: {}, //connect filtrations
            filter_provider: '', //for example: woof
            current_page: 0,
            use_load_more: 0,
            css_classes: '',
            no_found_text: '',
            show_print_btn: 0,
            post_id: 0,
            compact_view_width: -1,
            stop_notice: '', //replace the table by notice block if not empty!
            style: '',
            skin: '',
            use_flow_header: true,
            link_get_data: (new URLSearchParams((new URL(window.location.href)).search)).toString(), //$_GET
            lang: {
                loading: 'Loading ...',
                no_data: 'No data!',
                error: 'Error!',
                items: 'Items',
                page: 'Page',
                pages: 'Pages',
                load_more: 'Load more'
            },
            pagination: {
                position: 't', //t,b,tb
                next: {
                    class: 'btn',
                    content: '&gt;'
                },
                prev: {
                    class: 'btn',
                    content: '&lt;'
                },
                input: {
                    class: 'table23-form-control'
                }
            },
            per_page_sel_pp: -1,
            per_page_sel_position: 't', //t,b,tb
            print_css_url: ''
        };

        this.settings = this.extend(defaults, table_data);
        this.request_data = this.settings.request_data;//ref to request data to short syntax

        //lang bone fix for ajax requests
        if (DataTable23.selected_lang) {
            this.request_data.lang = DataTable23.selected_lang;
        } else {
            this.request_data.lang = this.settings.selected_lang;
        }


        this.container = document.getElementById(table_html_id);
        if (this.settings.css_classes) {
            this.container.classList.add(this.settings.css_classes);
        }

        this.table = this.container.querySelector('table');
        this.table.setAttribute('id', 't' + Math.random().toString(36).substring(7));
        this.table.innerHTML = '';

        //stop and exit from execution
        if (this.settings.stop_notice.length > 0) {
            this.container.innerHTML = this.settings.stop_notice;
            return;
        }

        this.wrapper = document.createElement('div');
        this.wrapper.className = 'table23-wrapper';

        //!important!
        if (this.is_mobile) {
            this.wrapper.style.overflowX = 'scroll';
        }

        this.wrapper.appendChild(this.table);
        this.container.appendChild(this.wrapper);
        if (this.container.querySelector('.table23-place-loader')) {
            this.container.querySelector('.table23-place-loader').remove();
        }

        if (this.settings.style.length > 0) {
            let style = document.createElement('style');
            style.setAttribute('data-table23-skin', this.settings.skin);
            style.setAttribute('data-table23-skin-of', this.table_html_id);
            style.innerHTML = this.settings.style;
            this.wrapper.appendChild(style);
        }

        //***
        //allows after ajax loading manipulate by table data in json mode
        if (this.settings.mode === 'to_json') {
            this.settings.mode = 'ajax';
            this.settings.switch_to_json = true;
        }
        
        if (this.settings.mode === 'json') {
            this.settings.switch_to_json = true;
        }

        if (this.request_data.mode === 'json' || this.request_data.mode === 'to_json' || this.settings.use_load_more) {
            this.use_cache = false;//as it cached by logic itself
        }

        //***

        this.show_compact = false;

        if (window.innerWidth <= this.settings.compact_view_width && this.settings.compact_view_width !== -1) {
            this.show_compact = true;
        } else {
            this.show_compact = false;
        }

        if (this.settings.use_flow_header && !this.show_compact) {
            this.flow_header = document.createElement('div');
            this.flow_header.className = 'table23-flow-header';
            this.flow_header.style.display = 'none';
            this.flow_header.innerHTML = '<div></div>'.repeat(this.get_cols_count());
            this.wrapper.appendChild(this.flow_header);
        }


        if (this.show_compact && this.settings.use_flow_header && this.flow_header) {
            this.use_flow_header = false;
            this.flow_header.style.display = 'none';
        }

        //draw table
        if (this.get_cols_count() > 0) {
            //set heads
            this.thead = document.createElement('thead');
            let tfoot = document.createElement('tfoot');
            let tbody = document.createElement('tbody');

            this.thead.appendChild(document.createElement('tr'));
            tfoot.appendChild(document.createElement('tr'));

            this.table.appendChild(this.thead);
            this.table.appendChild(tfoot);
            this.table.appendChild(tbody);

            let _this = this;

            //+++
            let counter = 0;
            Object.keys(this.settings.heads).forEach((field_key) => {

                if (this.is_mobile) {
                    if (this.settings.hide_on_mobile.includes(field_key)) {
                        return false;
                    }
                }

                //***

                let v = this.settings.heads[field_key];
                var th = document.createElement('th');
                let span = '';//for additions into html

                th.innerHTML = v + span;

                th.setAttribute('data-key', field_key);

                //***
                //cells width management
                th.style.minWidth = 'auto';
                if (typeof this.settings.cells_width[counter] !== 'undefined') {
                    let w = this.settings.cells_width[counter];

                    if (w.indexOf('px') !== -1 || w.indexOf('%') !== -1) {
                        th.style.width = w;
                        th.style.minWidth = w;
                    } else {
                        th.style.minWidth = w;
                    }
                }

                counter++;

                //***

                if (this.settings.orders[field_key]) {
                    th.classList.add('table23-order-able');
                    if (this.request_data.orderby == field_key) {
                        th.classList.add('table23-order-' + this.request_data.order);
                    }
                }

                this.thead.querySelector('tr').appendChild(th);
                tfoot.querySelector('tr').appendChild(th.cloneNode(true));
            });


            //***

            var th_order_able = this.table.querySelectorAll('th.table23-order-able');

            if (th_order_able.length > 0) {
                for (let i = 0; i < th_order_able.length; i++) {
                    th_order_able[i].addEventListener('click', function (e) {
                        e.stopPropagation();
                        _this.request_data.orderby = this.getAttribute('data-key');
                        _this.request_data.order = _this.settings.orders[_this.request_data.orderby];

                        if (this.classList.contains('table23-order-desc')) {
                            _this.request_data.order = 'asc';
                        }

                        if (this.classList.contains('table23-order-asc')) {
                            _this.request_data.order = 'desc';
                        }

                        //***

                        _this.table.querySelectorAll('th').forEach(function (item) {
                            item.classList.remove('table23-order-asc');
                            item.classList.remove('table23-order-desc');
                        });

                        _this.table.querySelectorAll("th[data-key='" + _this.request_data.orderby + "']").forEach(function (item) {
                            item.classList.add('table23-order-' + _this.request_data.order);
                        });

                        _this.request_data.current_page = 0;
                        _this.allow_reset = true;//for load more button
                        //_this.use_cache = false;//for tests
                        _this.draw_data();
                        _this.draw_pagination();
                        return false;
                    });
                }
            }

            //***
            //not_load_on_init is special dev param
            if (typeof this.settings.not_load_on_init === 'undefined') {
                this.draw_data();
                this.draw_pagination();
            }

            if (this.settings.per_page_sel_position !== 'none') {
                switch (this.settings.per_page_sel_position) {
                    case 't':
                        this.draw_per_page_selector('top');
                        break;

                    case 'b':
                        this.draw_per_page_selector('bottom');
                        break;

                    default:
                        this.draw_per_page_selector('top');
                        this.draw_per_page_selector('bottom');
                        break;
                }
            }


            if (this.settings.use_load_more) {
                this.draw_load_more_button();
            }

        }


        //***
        //for active elements, for example favourites!!
        document.addEventListener('table23-reset-cache', e => {
            if (this.table_html_id === e.detail.table_html_id) {
                this.cache = {};
            }
        }, false);

        //for extensions with table communication returns self
        document.addEventListener('table23-get', e => {
            if (this.table_html_id === e.detail.table_html_id) {
                e.detail.answer.table = this;
            }
        }, false);

        document.dispatchEvent(new CustomEvent('table23-html-drawn', {detail: {otable: this}}));

        //+++
        this.resize_ev_is_locked = false;
        setTimeout(() => {
            window.addEventListener('resize', (e) => {
                if (!this.resize_ev_is_locked) {
                    this.resize_ev_is_locked = true;
                    let prev_state = this.show_compact;

                    if (window.innerWidth <= this.settings.compact_view_width) {
                        this.show_compact = true;
                    } else {
                        this.show_compact = false;
                    }

                    if (this.show_compact) {
                        if (this.settings.use_flow_header && this.flow_header) {
                            this.use_flow_header = false;
                            this.flow_header.style.display = 'none';
                        }
                        this.container.classList.add('table23-in-compact-mode');
                    } else {
                        this.container.classList.remove('table23-in-compact-mode');
                    }

                    if (prev_state !== this.show_compact) {
                        this.redraw_table(false);
                    }

                    setTimeout(() => {
                        //damper, to avoid double act
                        this.resize_ev_is_locked = false;
                    }, 999);
                }
            });
        }, 999);

        //+++

        if (!DataTable23.tables) {
            DataTable23.tables = [];
        }
        //using DataTable23.tables it is possible get any table class object just by table_html_id
        DataTable23.tables[this.table_html_id] = this;
    }

    //***

    draw_data(filter_data = {}) {
        this.notification(this.settings.lang.loading);

        //dynamic js search
        if (typeof filter_data === 'object' && filter_data !== null && Object.keys(filter_data).length > 0) {
            this.request_data.filter_data = filter_data;
            this.request_data.current_page = 0;
        } else {
            //filter reset
            if (filter_data === null) {
                this.request_data.filter_data = {};
                this.request_data.current_page = 0;
            }
        }

        switch (this.settings.mode) {
            case 'json':
                //filter_data not implemented in json requests!
                let d = {};

                if (typeof this.settings.table_data !== 'object') {
                    d = JSON.parse(this.settings.table_data);
                } else {
                    d = this.settings.table_data;
                }

                if (typeof d.rows !== 'undefined') {
                    this.settings.table_data = d.rows;
                    this.settings.total_rows_count = parseInt(d.count, 10);
                } else {
                    this.settings.table_data = d;
                }

                //***

                let sql = 'SELECT * FROM ? ';
                let sql_count = 'SELECT COUNT(*) FROM ? ';

                //+++

                let title_key = 'title';
                if (typeof this.request_data.filter_data[title_key] === 'undefined') {
                    title_key = 'post_title';
                }

                if (typeof this.request_data.filter_data[title_key] !== 'undefined') {
                    let title_tail = ` WHERE (title LIKE '%${this.request_data.filter_data[title_key]}%' OR post_title LIKE '%${this.request_data.filter_data[title_key]}%')`;
                    sql += title_tail;
                    sql_count += title_tail;
                }

                //+++

                this.settings.total_rows_count = parseInt(alasql(sql_count, [this.settings.table_data])[0]['COUNT(*)'], 10);

                //***

                sql += ' ORDER BY ' + this.request_data.orderby + ' ' + this.request_data.order;

                if (parseInt(this.request_data.per_page, 10) !== -1) {
                    sql += ' LIMIT ' + this.request_data.per_page + ' OFFSET ' + (this.request_data.current_page * this.request_data.per_page);
                }

                //***

                let data = alasql(sql, [this.settings.table_data]);
                this.__process_raw_data(data);

                break;
            default:
                //ajax mode

                let fd = this.extend(this.request_data, {
                    filter_data: this.request_data.filter_data ? JSON.stringify(this.request_data.filter_data) : '{}',
                    filter_provider: this.request_data.filter_provider ? this.request_data.filter_provider : ''
                });

                //***

                //data from cache
                if (this.get_cache(this.request_data) && typeof alasql !== 'undefined') {

                    //data from cache
                    if (this.get_pages_count() === 1 && this.get_cache(0)) {
                        let d = this.get_cache(0);

                        if (typeof d.rows !== 'undefined') {
                            d = d.rows;
                        }

                        let sql = 'SELECT * FROM ?';

                        if (typeof this.request_data.filter_data.post_title !== 'undefined') {
                            if (this.request_data.filter_data.post_title.length > 0) {
                                sql += ' WHERE ';
                                for (let k in this.request_data.fields) {
                                    sql += this.request_data.fields[k] + ' LIKE "%' + this.request_data.filter_data.post_title + '%" OR ';
                                }

                                sql = sql.substr(0, sql.length - 3);
                            }
                        }

                        //***

                        sql += ' ORDER BY ' + this.request_data.orderby + ' ' + this.request_data.order;

                        d = alasql(sql, [d]);
                        this.__process_raw_data(d);
                        break;
                    } else {
                        if (this.get_cache(this.request_data)) {
                            this.__process_raw_data(this.get_cache(this.request_data));
                            break;
                        }
                    }

                } else {
                    let body = this.prepare_ajax_form_data(fd);

                    if (this.fetch_controller) {
                        //cancel ajax request if user leaf through too quick
                        this.fetch_controller.abort();
                    }

                    this.fetch_controller = new AbortController();
                    const signal = this.fetch_controller.signal;
                    //avalon23 compatibility  ajax
	            let ajax_url=this.settings.ajax_url;
                    if(location.search.substr(1)){
                        ajax_url+="?"+location.search.substr(1);
                    }
                    fetch(ajax_url, {...{
                                method: 'POST',
                                credentials: 'same-origin', // 'include', default: 'omit'
                                body: body
                        }, signal}).then((response) => response.json()).then((data) => {
                        this.__process_raw_data(data);

                        //useful switch after first ajax data loading
                        if (typeof this.settings.switch_to_json !== 'undefined') {
                            this.settings.table_data = data;
                            this.settings.mode = 'json';
                        }

                        this.set_cache(data, this.request_data);
                    }).catch((err) => {
                        //this.notification(err, 'error', 5000);
                    });

                }

                break;
    }

    }

    __process_raw_data(data) {
        let length = 0;
        if (typeof data.rows === 'undefined') {
            length = data.length;
        } else {
            length = data.rows.length;
        }

        //+++

        if (length > 0) {

            if (typeof data.rows === 'undefined') {
                this.build_rows(data);
            } else {
                this.build_rows(data.rows);
                this.settings.total_rows_count = parseInt(data.count, 10);
                this.set_cache(data);
            }

            this.draw_pagination();
        } else {
            this.reset();

            //if (this.allow_reset) {
            if (this.settings.no_found_text) {
                this.notification(this.settings.no_found_text, true);
            } else {
                this.notification(this.settings.lang.no_data, true);
            }
            //}

            this.settings.total_rows_count = 0;
            this.draw_pagination();
        }


        this.do_after_draw();
    }

    build_rows(data) {
        let _this = this;
        this.reset();
        let head_columns = Object.keys(this.settings.heads);

        let rows_fragment = document.createDocumentFragment();

        //for to_json mode
        let max_iterations = parseInt(this.settings.request_data.per_page);
        if (max_iterations <= 0) {
            max_iterations = Infinity;
        }

        for (let x = 0; x < data.length && x < max_iterations; x++) {
            var row = document.createElement('tr');

            //if show_compact is true
            var compact_td = document.createElement('td');
            compact_td.setAttribute('colspan', this.get_cols_count());


            if (this.show_compact) {
                this.hide_heads();
            } else {
                this.show_heads();
            }

            Object.keys(data[x]).forEach(function (current_field) {

                if (current_field === 'pid') {
                    return;//important special field, this is not column, postID
                }

                if (!head_columns.includes(current_field)) {
                    return;
                }

                if (_this.is_mobile) {
                    if (_this.settings.hide_on_mobile.includes(current_field)) {
                        return;
                    }
                }

                //***

                var css_class = '';
                var data_field_type = '';
                //for editable fields
                if ((Object.keys(_this.settings.editable)).includes(current_field)) {
                    css_class = 'table23-td-editable';
                    data_field_type = _this.settings.editable[current_field];
                }

                //***

                var td = null;

                if (_this.show_compact) {
                    td = document.createElement('div');
                } else {
                    td = document.createElement('td');
                }

                if (css_class.length) {
                    css_class = ' ' + css_class;
                }

                td.className = 'table23_td_cell table23_td_' + current_field.replace(':', '') + css_class;
                td.setAttribute('data-field-type', data_field_type);
                td.setAttribute('data-pid', typeof data[x]['pid'] !== 'undefined' ? data[x]['pid'] : 0);
                td.setAttribute('data-key', current_field);

                if ((Object.keys(_this.settings.custom_field_keys)).includes(current_field)) {
                    //if we need another key for saving for col in each row
                    if (data[x][current_field].custom_field_key) {
                        td.setAttribute('data-field', data[x][current_field].custom_field_key);
                        td.innerHTML = data[x][current_field].value;
                    } else {
                        td.setAttribute('data-field', current_field);
                        td.innerHTML = data[x][current_field];
                    }
                } else {
                    td.setAttribute('data-field', current_field);
                    td.innerHTML = data[x][current_field];
                }

                if (_this.show_compact /* && !['id'].includes(current_field)*/) {
                    td.innerHTML = `<span class="table23_td_cell_title">${_this.settings.heads[current_field]}:</span>` + td.innerHTML;
                }

                //for td view customization depending of its content
                td.classList.add('hash' + _this.hash_code(td.innerHTML));
                row.setAttribute('data-pid', data[x]['pid']);

                if (_this.show_compact) {
                    compact_td.appendChild(td);
                } else {
                    row.appendChild(td);
                }

            });

            if (this.show_compact) {
                row.appendChild(compact_td);
            }

            this.init_row_actions(row);
            rows_fragment.appendChild(row);

        }

        this.table.querySelector('tbody').appendChild(rows_fragment);

        //***
        //load more button
        if (this.settings.use_load_more && this.wrapper.parentElement.querySelector('.table23-load-more-button')) {
            if (this.request_data.per_page > Object.keys(data).length) {
                this.wrapper.parentElement.querySelector('.table23-load-more-button').style.display = 'none';
            } else {
                this.wrapper.parentElement.querySelector('.table23-load-more-button').style.display = 'block';
            }
        }

        //***

        document.dispatchEvent(new CustomEvent('table23-after-build-rows', {detail: {
                otable: this
            }}));
    }

    get_pages_count() {
        let pp = parseInt(this.request_data.per_page, 10);
        if (pp === -1) {
            pp = 99999;
        }
        return Math.ceil(this.settings.total_rows_count / pp);
    }

    draw_pagination() {

        if (!document.getElementById(this.table_html_id)) {
            return;
        }

        this.__draw_print_button();

        this.wrapper = document.getElementById(this.table_html_id).querySelector('.table23-wrapper');

        if (this.settings.use_load_more) {
            return false;
        }

        if (parseInt(this.request_data.per_page, 10) === -1) {
            return false;
        }

        //***

        this.wrapper.parentElement.querySelectorAll('.table23-pagination').forEach(function (item) {
            item.remove();
        });

        if (this.settings.pagination.position !== 'none') {
            switch (this.settings.pagination.position) {
                case 't':
                    this.__draw_pagination('top');
                    break;

                case 'b':
                    this.__draw_pagination('bottom');
                    break;

                default:
                    this.__draw_pagination('top');
                    this.__draw_pagination('bottom');
                    break;
            }

        }


    }

    __draw_print_button() {
        if (typeof this.print_btn_created === 'undefined') {
            if (parseInt(this.settings.show_print_btn)) {
                var btn = document.createElement('a');
                btn.className = 'table23-print-btn';
                btn.setAttribute('href', '#');
                btn.innerHTML = '<i class="woot-icon">&#xe800;</i>';
                this.wrapper.parentElement.insertBefore(btn, this.wrapper);

                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (typeof table23_print_window_width === 'undefined') {
                        var table23_print_window_width = 800;
                    }

                    if (typeof table23_print_window_height === 'undefined') {
                        var table23_print_window_height = 600;
                    }

                    let mywindow = window.open('', 'PRINT', `width=${table23_print_window_width},height=${table23_print_window_height}`);
                    mywindow.document.write('<html><head><title>' + document.title + '</title>');

                    mywindow.document.write(`<link rel="stylesheet" href="${this.settings.print_css_url}" type="text/css" />`);

                    mywindow.document.write('</head><body>');
                    mywindow.document.write('<h3>' + document.title + '</h3>');
                    mywindow.document.write(this.table.outerHTML);
                    mywindow.document.write('</body></html>');

                    for (var th of mywindow.document.getElementsByTagName('th')) {
                        th.removeAttribute('style');
                    }

                    if (typeof table23_do_not_print !== 'undefined') {
                        DataTable23.do_not_print = [...DataTable23.do_not_print, ...table23_do_not_print];
                    }

                    Loop1:
                            for (var i = 0; i < DataTable23.do_not_print.length; i++) {
                        Loop2:
                                for (var th of mywindow.document.getElementsByTagName('th')) {
                            if (th.getAttribute('data-key') === DataTable23.do_not_print[i]) {
                                th.remove();
                            }
                        }
                    }

                    Loop1:
                            for (var i = 0; i < DataTable23.do_not_print.length; i++) {
                        Loop2:
                                for (var td of mywindow.document.getElementsByTagName('td')) {
                            if (td.getAttribute('data-field').trim() === DataTable23.do_not_print[i].trim()) {
                                td.remove();
                            }
                        }
                    }

                    mywindow.document.close();
                    mywindow.focus();

                    mywindow.print();
                    mywindow.onafterprint = () => {
                        mywindow.close();
                    };

                    return true;
                });

                this.print_btn_created = true;//flag
            }
        }
    }

    __draw_pagination(position = 'top') {
        var pages_count = this.get_pages_count();

        if (pages_count > 1) {
            var pagination = document.createElement('div');
            pagination.className = 'table23-pagination';

            if (position === 'top') {
                this.wrapper.parentElement.insertBefore(pagination, this.wrapper);
            } else {
                this.wrapper.parentElement.appendChild(pagination, this.wrapper);
            }

            let _this = this;
            var buttons = [-1, 0, +1];
            for (var p = 0; p < buttons.length; p++) {

                var li = document.createElement('span');
                if (buttons[p] === 0) {
                    //draw input
                    var input = document.createElement('input');
                    input.setAttribute('type', 'number');
                    input.setAttribute('min', 1);
                    input.setAttribute('max', pages_count);
                    input.className = 'table23-pager';
                    input.classList.add(this.settings.pagination.input.class);
                    input.value = this.request_data.current_page + 1;

                    input.addEventListener('change', function (e) {
                        e.stopPropagation();

                        _this.wrapper.parentElement.querySelectorAll('.table23-pagination a').forEach(function (item) {
                            item.classList.remove('table23-hidden');
                        });

                        if (this.value <= 0) {
                            this.value = 1;
                        }

                        if (this.value >= _this.get_pages_count()) {
                            this.value = _this.get_pages_count();
                        }

                        let value = this.value;

                        _this.wrapper.parentElement.querySelectorAll('input[type=number]').forEach(function (item) {
                            item.value = value;
                        });

                        //+++

                        _this.request_data.current_page = parseInt(value, 10) - 1;

                        _this.wrapper.parentElement.querySelectorAll('.table23-current-page span').forEach(function (item) {
                            item.innerHTML = _this.request_data.current_page + 1;
                        });

                        _this.draw_data();

                        //***
                        //buttons accessibility
                        _this.wrapper.parentElement.querySelector('.table23-pagination .table23-prev').classList.remove('table23-hidden');
                        _this.wrapper.parentElement.querySelector('.table23-pagination .table23-next').classList.remove('table23-hidden');

                        if (_this.request_data.current_page <= 0) {
                            _this.request_data.current_page = 0;

                            _this.wrapper.parentElement.querySelectorAll('.table23-prev').forEach(function (item) {
                                item.classList.add('table23-hidden');
                            });
                            _this.wrapper.parentElement.querySelectorAll('.table23-next').forEach(function (item) {
                                item.classList.remove('table23-hidden');
                            });
                        }

                        if (_this.request_data.current_page >= pages_count - 1) {
                            _this.request_data.current_page = pages_count - 1;

                            _this.wrapper.parentElement.querySelectorAll('.table23-prev').forEach(function (item) {
                                item.classList.remove('table23-hidden');
                            });
                            _this.wrapper.parentElement.querySelectorAll('.table23-next').forEach(function (item) {
                                item.classList.add('table23-hidden');
                            });
                        }

                        return true;
                    });

                    li.appendChild(input);
                    pagination.appendChild(li);
                    continue;
                }

                //+++

                var a = document.createElement('a');
                a.setAttribute('href', '#');
                a.setAttribute('data-page', buttons[p]);

                if (buttons[p] > 0) {
                    a.innerHTML = _this.settings.pagination.next.content;
                    a.classList.add(_this.settings.pagination.next.class);
                    a.classList.add('table23-next');
                    if (this.request_data.current_page >= pages_count - 1) {
                        a.classList.add('table23-hidden');
                    }
                } else {
                    a.innerHTML = _this.settings.pagination.prev.content;
                    a.classList.add(_this.settings.pagination.prev.class);
                    a.classList.add('table23-prev');
                    if (this.request_data.current_page <= 0) {
                        a.classList.add('table23-hidden');
                    }
                }

                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    _this.allow_reset = true;

                    let do_not_draw_data = false;
                    if (this.classList.contains('table23-hidden')) {
                        do_not_draw_data = true;//avoid redrawing page on the same extreme page
                    }

                    //+++

                    _this.request_data.current_page += parseInt(this.getAttribute('data-page'), 10);

                    _this.wrapper.parentElement.querySelectorAll('.table23-pagination a').forEach(function (item) {
                        item.classList.remove('table23-hidden');
                    });

                    if (_this.request_data.current_page <= 0) {
                        _this.request_data.current_page = 0;
                        //this.classList.add('table23-hidden');
                    }

                    if (_this.request_data.current_page >= pages_count - 1) {
                        _this.request_data.current_page = pages_count - 1;
                        //this.classList.add('table23-hidden');
                    }

                    if (do_not_draw_data) {
                        return false;
                    }

                    //***

                    _this.wrapper.parentElement.querySelectorAll('.table23-pager').forEach(function (item) {
                        item.value = parseInt(_this.request_data.current_page, 10) + 1;
                    });


                    _this.wrapper.parentElement.querySelectorAll('.table23-current-page span').forEach(function (item) {
                        item.innerHTML = _this.request_data.current_page + 1;
                    });


                    _this.wrapper.parentElement.querySelectorAll('.table23-pagination input[type=number]').forEach(function (item) {
                        item.value = parseInt(_this.request_data.current_page, 10) + 1;
                    });

                    _this.draw_data();
                    return false;
                });

                li.appendChild(a);
                pagination.appendChild(li);
            }

            var li = document.createElement('span');
            li.className = 'table23-current-page';
            li.innerHTML = /*this.settings.lang.page + ': ' + '<span>' + (this.request_data.current_page + 1) + '</span>, ' + */this.settings.lang.pages + ': ' + this.get_pages_count() + ', ' + this.settings.lang.items + ': ' + this.settings.total_rows_count;
            pagination.appendChild(li);
        }

        return true;
    }

    draw_per_page_selector(position = 'top') {

        if (this.settings.use_load_more) {
            return false;
        }

        if (parseInt(this.settings.per_page_sel_pp, 10) === -1) {
            return false;
        }

        //***

        var select = document.createElement('select');
        select.className = 'table23-per-page-selector';

        if (position === 'top') {
            this.wrapper.parentElement.insertBefore(select, this.wrapper);
        } else {
            this.wrapper.parentElement.appendChild(select, this.wrapper);
        }

        this.settings.per_page_sel_pp.forEach(function (pp) {
            let option = document.createElement('option');
            option.setAttribute('value', pp);
            option.innerText = pp;
            select.appendChild(option);
        });

        select.value = this.request_data.per_page;

        //***

        let _this = this;
        select.addEventListener('change', function (e) {
            e.stopPropagation();

            _this.request_data.per_page = parseInt(this.options[e.srcElement.selectedIndex].value, 10);
            _this.request_data.current_page = 0;
            _this.allow_reset = true;
            _this.draw_data();
            _this.draw_pagination();

            //***

            var value = parseInt(this.value, 10);

            _this.wrapper.parentElement.querySelectorAll('select.table23-per-page-selector').forEach(function (item) {
                item.value = value;
            });
        });


    }

    draw_load_more_button() {
        this.allow_reset = false;

        var div = document.createElement('div');
        var button = document.createElement('a');
        div.className = 'table23-load-more-button';
        button.setAttribute('href', '#');
        button.innerHTML = this.settings.lang.load_more;

        let _this = this;
        let last_row = _this.table.querySelector('tbody').lastChild;
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            _this.allow_reset = false;
            _this.use_cache = false;
            _this.request_data.current_page = parseInt(_this.request_data.current_page, 10) + 1;

            last_row = _this.table.querySelector('tbody').lastChild;
            _this.draw_data();

            if (_this.request_data.current_page > _this.get_pages_count() - 1) {
                this.style.display = 'none';
            }

            return false;
        });

        //scroll to first new row
        document.addEventListener('table23-after-build-rows', function (e) {
            e.stopPropagation();

            if (e.detail.otable.table_html_id === _this.table_html_id) {
                last_row.scrollIntoView({behavior: 'smooth'});
            }
        });

        div.appendChild(button);
        this.wrapper.parentElement.appendChild(div, this.wrapper);
    }

    reset() {

        if (this.table.querySelector('.table23-tr-notice')) {
            this.table.querySelector('.table23-tr-notice').remove();
        }

        let allow = this.allow_reset;
        if (typeof this.allow_reset_force !== 'undefined') {
            allow = this.allow_reset_force;
            delete this.allow_reset_force;//one time using per operation
        }

        if (allow) {
            this.table.querySelector('tbody').style.height = this.table.querySelector('tbody').clientHeight;//offsetHeight
            this.table.querySelector('tbody').innerHTML = '';
        }
    }

    notification(word, no_flow = false) {
        var row = document.createElement('tr');
        var td = document.createElement('td');
        td.setAttribute('colspan', this.get_cols_count());
        td.setAttribute('align', 'center');
        td.className = 'table23-empty-row';
        td.innerHTML = word;
        row.appendChild(td);
        if (!no_flow) {
            row.className = 'table23-tr-notice';
        }

        if (this.table.querySelector('tbody tr') !== null && this.allow_reset) {
            let first_row = this.table.querySelector('tbody').querySelector('tr');
            if (first_row) {
                row.appendBefore(first_row);
            } else {
                this.table.querySelector('tbody').appendChild(row);
            }
        } else {
            this.table.querySelector('tbody').appendChild(row);
        }

        row.style.width = this.table.offsetWidth - 4 + 'px';
    }

    get_cols_count() {
        return Object.keys(this.settings.heads).length;
    }

    prepare_ajax_form_data(data) {
        const formData = new FormData();

        Object.keys(data).forEach(function (k) {
            formData.append(k, data[k]);
        });

        return formData;
    }

    extend(d1, d2) {
        return {...d1, ...d2};
    }

    //***

    __hash_object(obj) {
        return (JSON.stringify(obj)).split('').reduce((prevHash, currVal) => (((prevHash << 5) - prevHash) + currVal.charCodeAt(0)) | 0, 0);
    }

    set_cache(data, request_data) {
        if (request_data && this.use_cache) {
            let index = 0;

            //if (this.get_pages_count() > 1) {
            index = this.__hash_object(request_data);
            //}

            //for zero index of onepage tables
            if (index === 0) {
                if (Object.keys(this.cache).length !== 0) {
                    index = this.__hash_object(request_data);
                }
            }

            this.cache[index] = data;
        }
    }

    get_cache(request_data) {
        let index = 0;

        if (this.use_cache) {
            //if (this.get_pages_count() > 1) {
            index = this.__hash_object(request_data);
            //}

            if (typeof this.cache[index] !== 'undefined') {
                return this.cache[index];
            }
        }

        return null;
    }

    hash_code(str) {
        return str.split('').reduce((prevHash, currVal) => (((prevHash << 5) - prevHash) + currVal.charCodeAt(0)) | 0, 0);
    }

    delete_row(pid) {
        if (this.table.querySelector('tr[data-pid="' + pid + '"]')) {
            this.table.querySelector('tr[data-pid="' + pid + '"]').remove();

            if (this.settings.mode === 'json') {

                if (typeof this.settings.table_data.rows !== 'undefined') {
                    for (const key of Object.keys(this.settings.table_data.rows)) {
                        if (parseInt(pid) === parseInt(this.settings.table_data.rows[key].pid)) {
                            delete this.settings.table_data.rows[key];
                            //keys reset
                            this.settings.table_data.rows = this.settings.table_data.rows.filter(function (item) {
                                return item !== 'undefined';
                            });
                            break;
                        }
                    }
                } else {
                    for (const key of Object.keys(this.settings.table_data)) {
                        if (parseInt(pid) === parseInt(this.settings.table_data[key].pid)) {
                            delete this.settings.table_data[key];
                            //keys reset
                            this.settings.table_data = this.settings.table_data.filter(function (item) {
                                return item !== 'undefined';
                            });
                            break;
                        }
                    }
                }

            }
        }
    }

    delete_column(data_key) {
        Array.from(this.table.querySelectorAll(`[data-key="${data_key}"]`)).forEach(function (cell) {
            cell.remove();
        });
    }

    redraw_table(set_first_page = true) {

        if (set_first_page) {
            this.request_data.current_page = 0;
        }

        if (!this.allow_reset) {
            this.allow_reset_force = true;
        }

        this.draw_data();
        this.draw_pagination();
    }

    hide_heads() {
        this.table.querySelector('thead').style.display = 'none';
        this.table.querySelector('tfoot').style.display = 'none';
    }

    show_heads() {
        this.table.querySelector('thead').removeAttribute('style');
        this.table.querySelector('tfoot').removeAttribute('style');
    }

    do_after_draw() {
        document.addEventListener('scroll', (e) => {
            let flow = this.wrapper.querySelector('.table23-flow-header');

            if (flow && !this.show_compact) {
                let box = this.wrapper.getBoundingClientRect();
                let first_row = this.wrapper.querySelector('table thead tr');

                if (box.top <= -15) {
                    flow.style.display = 'block';
                    flow.style.width = (this.wrapper.querySelector('table').offsetWidth + 1) + 'px';

                    if (document.getElementById('wpadminbar')) {
                        flow.style.top = Math.abs(box.top) + document.getElementById('wpadminbar').offsetHeight + 'px';
                    } else {
                        flow.style.top = Math.abs(box.top) + 'px';
                    }

                    Array.from(first_row.querySelectorAll('th')).forEach((th, index) => {
                        if(this.container.classList.contains('data-table-23-separated')){
                            flow.querySelectorAll('div')[index].style.width = th.offsetWidth + 1 + 'px';
                        }else{
                            flow.querySelectorAll('div')[index].style.width = th.offsetWidth + 'px';
                        }                        
                        
                        flow.querySelectorAll('div')[index].innerHTML = th.innerText;
                    });

                } else {
                    flow.style.display = 'none';
                }
            }
        });

        //+++
        if (!this.show_compact) {
            Array.from(this.table.querySelectorAll('thead th')).forEach((th, th_index) => {
                ['table23-order-desc', 'table23-order-asc'].forEach((css_class, i) => {
                    if (th.classList.contains(css_class) && this.table.querySelectorAll('tbody td').length > 1) {
                        Array.from(this.table.querySelectorAll('tbody tr')).forEach((tr, index) => {
                            if (tr.querySelector(`td:nth-child(${th_index + 1})`)) {
                                tr.querySelector(`td:nth-child(${th_index + 1})`).classList.add(css_class);
                            }
                        });
                    }
                });
            });
        }

    }

    get_cell(pid, field) {
        return this.table.querySelector(`td[data-pid="${pid}"][data-field="${field}"]`);
    }

    init_json_fields_saving() {
        this.change_cell_ev_handler = this.init_json_fields_saving_listener.bind(this);
        document.addEventListener('after_' + this.save_table_field_action, this.change_cell_ev_handler);
    }

    init_json_fields_saving_listener(e) {
        if (this.settings.mode === 'json'/* && e.detail.self === this*/) {
            //console.log('event ' + 'after_' + this.save_table_field_action);

            let cell = this.get_cell(e.detail.post_id, e.detail.field);

            if (cell) {
                if (cell.querySelector('input[type="checkbox"]')) {
                    if (e.detail.value) {
                        cell.querySelector('input[type="checkbox"]').setAttribute('checked', true);
                    } else {
                        cell.querySelector('input[type="checkbox"]').removeAttribute('checked');
                    }
                }

                if (cell.querySelector('select')) {
                    cell.querySelector('select').querySelectorAll('option').forEach(function (option) {
                        option.removeAttribute('selected');

                        if (option.value === e.detail.value) {
                            option.setAttribute('selected', true);
                        }
                    });
                }

                this.save_jsoned_field(e.detail.field, cell.innerHTML, e.detail.post_id);
            }
        }
    }

    //saving cell data for json-mode tables
    save_jsoned_field(field, value, post_id) {
        if (this.settings.mode === 'json') {
            if (typeof this.settings.table_data.rows !== 'undefined') {
                for (const key of Object.keys(this.settings.table_data.rows)) {
                    if (parseInt(post_id) === parseInt(this.settings.table_data.rows[key].pid)) {
                        this.settings.table_data.rows[key][field] = value;
                        break;
                    }
                }
            } else {
                for (const key of Object.keys(this.settings.table_data)) {
                    if (parseInt(post_id) === parseInt(this.settings.table_data[key].pid)) {
                        this.settings.table_data[key][field] = value;
                        break;
                    }
                }
            }

        }
    }

    init_row_actions(row) {}
    redraw_row(row) {}
}

