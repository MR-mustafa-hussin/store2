'use strict';

class BlockConstructor23 {
    constructor(container, connect_id = '', donor_data = {}, acceptor_data = {}, additional = {}) {

        if (!container) {
            return;
        }

        //***

        this.is_mobile = 'ontouchstart' in document.documentElement;
        this.container = container;
        this.connect_id = connect_id;//for business logic contact

        this.donor_data = donor_data;//json
        this.acceptor_data = acceptor_data;//json

        this.old_donor_data = Object.assign({}, donor_data);//json
        this.old_acceptor_data = Object.assign({}, acceptor_data);//json

        this.additional = additional;//json
        this.dragged_element = null;
        this.mouse_target = null;
        this.ghost = null;
        this.ghost_delay_timer = null;//to avoid immediate clicks

        //***

        this.donor = document.createElement('div');
        this.donor.className = 'blocks-constructor-23-donor';
        this.container.appendChild(this.donor);

        this.acceptor = document.createElement('div');
        this.acceptor.className = 'blocks-constructor-23-acceptor';
        this.container.appendChild(this.acceptor);

        if (typeof additional.hide_acceptor !== 'undefined') {
            if (additional.hide_acceptor) {
                this.acceptor.style.display = 'none';
            }
        }

        //***

        this.fill_by_elements();
        this.init_drag_and_drope();
    }

    fill_by_elements() {
        this.donor.innerHTML = '';
        this.acceptor.innerHTML = '';

        //***

        if (Object.keys(this.donor_data).length) {
            for (let key in this.donor_data) {
                let element = this.create_element(key, this.donor_data[key]);
                this.donor.appendChild(element);
                this.add_element_event(element);
            }
        }

        //***

        if (Object.keys(this.acceptor_data).length) {
            for (let key in this.acceptor_data) {
                let element = this.create_element(key, this.acceptor_data[key]);
                this.acceptor.appendChild(element);
                this.add_element_event(element);
            }
        }
    }

    init_drag_and_drope() {

        let _this = this;

        this.container.addEventListener(this.is_mobile ? 'touchend' : 'mouseup', function (e, x) {
            if (_this.ghost) {
                e.preventDefault();
                e.stopPropagation();
                _this.delete_ghost();
                _this.dragged_element = null;
                _this.rescan_data();
            }
        });
    }

    rescan_data() {
        let all_data = {...this.donor_data, ...this.acceptor_data};

        let new_data = {};
        this.container.querySelectorAll('.blocks-constructor-23-donor .blocks-constructor-23-element').forEach(function (item) {
            let key = item.getAttribute('data-field-key');
            new_data[key] = all_data[key];
        });

        this.donor_data = {...new_data};

        //***

        new_data = {};
        this.container.querySelectorAll('.blocks-constructor-23-acceptor .blocks-constructor-23-element').forEach(function (item) {
            let key = item.getAttribute('data-field-key');
            new_data[key] = all_data[key];
        });

        this.acceptor_data = {...new_data};
        this.cast_changing();
    }

    add_element_event(element) {
        let _this = this;
        element.addEventListener(this.is_mobile ? 'touchstart' : 'mousedown', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (e.target.classList.contains('blocks-constructor-23-element-drag')) {
                _this.dragged_element = this;
                _this.container.classList.add('blocks-constructor-23-dragged');

                //_this.ghost_delay_timer = setTimeout(() => _this.create_ghost(this), 111);
                _this.create_ghost(this);
            }
        });


        element.addEventListener('mousemove', function (e) {
            e.preventDefault();

            if (_this.ghost) {
                _this.ghost.appendBefore(this);
            }

        });

    }

    create_element(key, data) {
        let element = document.createElement('div');
        element.className = 'blocks-constructor-23-element';
        element.setAttribute('data-field-key', key);
        element.innerHTML = data.content;

        let drag = document.createElement('div');
        drag.className = 'blocks-constructor-23-element-drag';
        element.appendChild(drag);

        if (data.has_settings) {
            let btn = document.createElement('div');
            btn.className = 'blocks-constructor-23-element-btn woot-btn';
            btn.innerHTML = '<span class="dashicons-before dashicons-admin-generic"></span>'

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                let call_id = woot_helper.create_id('woot-');

                let args = {
                    table_id: this.additional.table_id,
                    field_key: key,
                    not_paste: 1
                };

                //+++

                woot_helper.call_popup('woot_get_filter_field_options', args, call_id, woot_helper_vars.lang.filter_field_popup_title.sprintf(this.additional.table_id, key), {left: 20, right: 20}, `<a href="https://products-tables.com/woot-documentation/" class="woot-btn" target="_blank">${woot_helper_vars.lang.help}</a>`);

                document.addEventListener('woot-call-popup', (e) => {
                    if (e.detail.call_id === call_id) {
                        new WOOT_FilterFieldsOptions(JSON.parse(document.querySelector(`[data-table-id="${call_id}"]`).innerText), call_id);
                    }
                });

                return false;
            });

            element.appendChild(btn);
        }

        return element;
    }

    cast_changing() {
        //subscribe to event block-constructor23-changed to save changes
        if (JSON.stringify(this.old_acceptor_data) !== JSON.stringify(this.acceptor_data)) {
            this.old_acceptor_data = this.acceptor_data;
            document.dispatchEvent(new CustomEvent('block-constructor23-changed', {detail: {
                    connect_id: this.connect_id,
                    donor_data: this.donor_data,
                    acceptor_data: this.acceptor_data,
                    additional: this.additional
                }}));
        }
    }

    //***

    create_ghost(element) {

        this.ghost = element.cloneNode(true);
        this.ghost.classList.add('blocks-constructor-23-ghost');
        this.ghost.appendAfter(element);
        this.dragged_element.style.display = 'none';

        let _this = this;
        document.addEventListener(this.is_mobile ? 'touchmove' : 'mousemove', function (e) {
            if (_this.ghost) {
                //_this.ghost.style.left = e.clientX + "px";
                //_this.ghost.style.top = e.clientY + "px";

                //***

                let target = e.target;

                if (_this.is_mobile) {
                    target = document.elementFromPoint(e.changedTouches[e.changedTouches.length - 1].pageX, e.changedTouches[e.changedTouches.length - 1].pageY);
                }

                if (target.classList.contains('blocks-constructor-23-acceptor') || target.classList.contains('blocks-constructor-23-donor')) {
                    target.appendChild(_this.ghost);
                } else {
                    if (target.classList.contains('blocks-constructor-23-element')) {
                        _this.ghost.appendBefore(target);
                    }
                }
            }
        });
    }

    delete_ghost() {
        clearTimeout(this.ghost_delay_timer);
        if (this.ghost !== null) {
            this.dragged_element.appendBefore(this.ghost);
            this.dragged_element.style.display = 'block';
            this.ghost.remove();
            this.ghost = null;
        }
    }

}
