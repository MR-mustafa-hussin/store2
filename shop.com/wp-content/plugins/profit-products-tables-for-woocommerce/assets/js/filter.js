'use strict';

class WOOT_Filter {
    constructor(dtable) {
        if (!dtable.wrapper.parentElement.querySelector('.woot-filter-data')) {
            return;
        }

        this.dtable = dtable;
        this.data = JSON.parse(this.dtable.wrapper.parentElement.querySelector('.woot-filter-data').innerText);
        this.list = this.dtable.wrapper.parentElement.querySelector('.woot-filter-list');
        this.draw();

        document.addEventListener('woot-filter-is-changed', e => {
            if (e.detail.dt === this.dtable) {
                let reset_visibility = 'block';
                if (Object.keys(this.dtable.request_data.filter_data).length === 0) {
                    reset_visibility = 'none';
                }

                if (this.dtable.wrapper.parentElement.querySelector('.woot-filter-reset')) {
                    this.draw_navigation();
                    this.dtable.wrapper.parentElement.querySelector('.woot-filter-reset').style.display = reset_visibility;
                }
            }
        }, false);
    }

    reset() {

        if (!this.dtable.wrapper.parentElement.querySelector('.woot-filter-list')) {
            return;
        }

        this.dtable.request_data.filter_data = {};

        if (this.dtable.wrapper.parentElement.querySelector('.woot-text-search')) {
            this.dtable.wrapper.parentElement.querySelector('.woot-text-search').value = '';
        }

        this.draw();
        this.redraw_table();

        document.dispatchEvent(new CustomEvent('woot-filter-reset', {detail: {
                dt: this.dtable
            }}));
    }

    draw() {
        //console.trace();
        this.list.innerHTML = '';
        if (Object.keys(this.data).length > 0) {

            if (typeof this.dtable.request_data.filter_data !== 'object') {
                this.dtable.request_data.filter_data = {};
            }

            //***
            let _this = this;

            for (let key in this.data) {
                let li = null, elem = null, label = null, value = null;

                switch (this.data[key]['view']) {
                    case 'select':
                    case 'mselect':

                        elem = document.createElement('select');
                        elem.setAttribute('data-key', key);
                        let option = document.createElement('option');
                        option.setAttribute('value', 0);
                        option.innerText = this.data[key]['title'];
                        elem.appendChild(option);

                        let filter_data = this.dtable.request_data.filter_data;

                        Object.values(this.data[key]['options']).map(function (o) {
                            option = document.createElement('option');
                            option.setAttribute('value', o.id);
                            option.innerText = o.title;
                            elem.appendChild(option);

                            //+++

                            if (filter_data[key]) {
                                if (filter_data[key].includes(parseInt(o.id))) {
                                    option.setAttribute('selected', '');
                                }
                            }

                        });

                        //***
                        //for non-multiselect drop-down
                        elem.addEventListener('change', (e) => {
                            e.stopPropagation();
                            let key = elem.getAttribute('data-key');
                            let values = Array.from(elem.querySelectorAll('option:checked')).map(el => Number(el.value));

                            this.dtable.request_data.filter_data[key] = values;

                            if (values.length === 0) {
                                delete this.dtable.request_data.filter_data[key];
                            }

                            if (values.length === 1) {
                                if (values[0] === 0) {//for single select
                                    delete this.dtable.request_data.filter_data[key];
                                }
                            }

                            this.redraw_table();
                        });

                        //***

                        let is_multi = this.data[key]['view'] === 'mselect';

                        if (is_multi && typeof SelectM23 === 'function') {
                            setTimeout(() => {

                                //fix for strange behaviour of multi selects with first option selection while redrawing
                                elem.querySelector('option').setAttribute('value', 'selectm23-exclude');//first option
                                elem.setAttribute('multiple', 'multiple');
                                new SelectM23(elem, false, this.data[key]['title']);//wrapping of <select>

                            }, 111);
                        }

                        break;

                    case 'range_slider':

                        elem = document.createElement('div');
                        elem.setAttribute('data-key', key);
                        elem.className = 'ranger23-track woot-slider';
                        elem.setAttribute('data-min', this.data[key]['min']);
                        elem.setAttribute('data-max', this.data[key]['max']);
                        elem.setAttribute('data-selected-min', this.data[key]['min']);
                        elem.setAttribute('data-selected-max', this.data[key]['max']);

                        if (this.data[key]['title']) {
                            label = document.createElement('div');
                            label.className = 'woot-slider-label';
                            label.innerText = this.data[key]['title'] + ':';
                        }

                        //+++

                        if (_this.dtable.request_data.filter_data[key]) {
                            let val = _this.dtable.request_data.filter_data[key].split(':');
                            elem.setAttribute('data-selected-min', val[0]);
                            elem.setAttribute('data-selected-max', val[1]);
                        }

                        //+++

                        let slider = new Ranger23(elem, woot_helper.create_id('slider-'));

                        //if slider generated in float containers coordinates are wrong
                        setTimeout(() => {
                            slider.resize();
                        }, 125);

                        document.addEventListener('ranger23-update', (e) => {
                            if (e.detail.cast_id === slider.cast_id) {

                                let key = slider.track.getAttribute('data-key');
                                let from = parseInt(e.detail.from, 10);
                                let to = parseInt(e.detail.to, 10);

                                _this.dtable.request_data.filter_data[key] = from + ':' + to;

                                if (slider.min === from && slider.max === to) {
                                    delete _this.dtable.request_data.filter_data[key];
                                }

                                if (Object.keys(_this.dtable.request_data.filter_data).length === 0) {
                                    _this.redraw_table(null);//because another way no reaction if filter_data empty
                                } else {
                                    _this.redraw_table();
                                }
                            }
                        });

                        break;


                    case 'textinput':

                        elem = document.createElement('input');
                        elem.setAttribute('data-key', key);
                        elem.setAttribute('type', 'search');
                        if (this.data[key]['minlength']) {
                            elem.setAttribute('minlength', this.data[key]['minlength']);
                        } else {
                            elem.setAttribute('minlength', 1);
                        }
                        elem.className = 'woot-text-search';

                        if (this.data[key]['placeholder']) {
                            elem.setAttribute('placeholder', this.data[key]['placeholder']);
                        }

                        if (_this.dtable.request_data.filter_data[key]) {
                            elem.value = _this.dtable.request_data.filter_data[key];
                        }

                        this.dtable.attach_keyup_event(elem);//we also use this for post title search input
                        this.dtable.attach_mouseup_event(elem);//we also use this for post title search input

                        break;

                    case 'switcher':

                        value = 0;

                        if (_this.dtable.request_data.filter_data[key]) {
                            value = _this.dtable.request_data.filter_data[key];
                        }
                        
                        elem = document.createElement('div');
                        
                         if (this.data[key]['title']) {
                            label = document.createElement('span');
                            label.className = 'woot-slider-label';
                            label.innerText = this.data[key]['title'] + ': ';
                            
                            elem.appendChild(label);
                        }

                        elem.appendChild(woot_helper.draw_switcher(key, value, 0, ''));
                        woot_helper.init_switcher(elem.querySelector('.switcher23'));

                        elem.querySelector('.switcher23').addEventListener('change', function (e) {

                            if (Number(this.value)) {
                                _this.dtable.request_data.filter_data[key] = Number(this.value);
                            } else {
                                delete _this.dtable.request_data.filter_data[key];
                            }

                            _this.redraw_table();
                            return true;
                        });

                       

                        break;

                    case 'calendar':

                        elem = document.createElement('div');
                        elem.className = 'calendar23-selector';

                        if (_this.dtable.request_data.filter_data[key]) {
                            value = _this.dtable.request_data.filter_data[key];
                        }

                        let selector = new Calendar23_Selector(elem, value, this.data[key]['placeholder'], woot_helper_vars.lang.calendar23_names);

                        selector.selected = () => {
                            this.dtable.request_data.filter_data[key] = selector.unix_time_stamp;
                            this.redraw_table();
                        };

                        break;

                    default:
                        //default filter element here
                        break;
                }

                if (elem && this.data[key]) {
                    li = document.createElement('div');
                    li.className = 'woot-filter-cell-' + key + ' woot-filter-cell-type-' + this.data[key]['view'];

                    if (this.data[key]['width'] && this.data[key]['width'].length > 0) {
                        li.style.width = this.data[key]['width'];
                    }

                    li.appendChild(elem);
                    this.list.appendChild(li);


                    if (label && this.data[key]['view'] !== 'switcher') {
                        label.appendBefore(elem);
                    }
                }
            }

            //add reset button
            let reset = document.createElement('a');
            reset.setAttribute('href', '#');
            reset.className = 'woot-filter-reset woot-btn';
            reset.innerText = woot_helper_vars.lang.reset;
            reset.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                _this.reset();
                _this.dtable.request_data.filter_data = {};
                _this.draw_navigation();

                return false;
            });

            let li = document.createElement('div');
            li.className = 'woot-filter-reset-container';
            li.appendChild(reset);
            this.list.appendChild(li);
        }
    }

    draw_navigation() {

        if (this.navigation) {
            this.navigation.remove();
        }

        this.navigation = document.createElement('div');
        this.navigation.className = 'woot-filter-navigation woot-clearfix';
        this.dtable.wrapper.parentElement.querySelector('.woot-filter-list').after(this.navigation);

        let keys = Object.keys(this.data);
        keys.push('post_title');

        if (keys.length > 0) {
            let label = null;

            for (let i in keys) {
                
                let key = keys[i];
                
                if(this.navigation.querySelector(`[data-key="${key}"]`)){
                    continue;
                }                

                if (!this.dtable.request_data.filter_data[key]) {
                    continue;
                }

                label = document.createElement('span');
                label.className = 'woot-filter-navigation-label';
                label.setAttribute('data-key', key);


                if (key === 'post_title') {
                    label.innerText = woot_helper_vars.lang.product_title;
                } else {
                    label.innerText = this.data[key]['title'];
                }

                this.navigation.appendChild(label);

                //***
                label.addEventListener('click', () => {
                    label.remove();
                    delete this.dtable.request_data.filter_data[key];

                    if (key === 'post_title') {
                        this.dtable.wrapper.parentElement.querySelector('.woot-text-search').value = '';
                    }

                    this.draw();
                    this.redraw_table();
                    return true;
                });

            }
        }

    }

    redraw_table() {
        this.dtable.redraw_table();

        document.dispatchEvent(new CustomEvent('woot-filter-is-changed', {detail: {
                dt: this.dtable
            }}));

    }

}




