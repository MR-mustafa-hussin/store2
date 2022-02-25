/**
 * @summary     Calendar23
 * @description calendar
 * @version     1.0.0
 * @file        calendar-23
 * @author      realmag777
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/calendar-23
 * @copyright   Copyright 2020 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 *   MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

class Calendar23 {

    constructor(container, calendar_id = null, unix_time_stamp = null, additional = {}) {
        this.scene = 0;//0,1,2

        this.month_names = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        this.month_names_short = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        this.day_names = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];

        if (typeof additional.calendar_names !== 'undefined') {
            if (typeof additional.calendar_names.month_names !== 'undefined') {
                this.month_names = additional.calendar_names.month_names;
            }

            if (typeof additional.calendar_names.month_names_short !== 'undefined') {
                this.month_names_short = additional.calendar_names.month_names_short;
            }

            if (typeof additional.calendar_names.day_names !== 'undefined') {
                this.day_names = additional.calendar_names.day_names;
            }
        }

        //***

        if (typeof additional.month_names !== 'undefined') {
            this.month_names = additional.month_names;
        }

        if (typeof additional.month_names_short !== 'undefined') {
            this.month_names_short = additional.month_names_short;
        }

        if (typeof additional.day_names !== 'undefined') {
            this.day_names = additional.day_names;
        }

        //***
        this.calendar_id = calendar_id;

        if (!this.calendar_id) {
            this.calendar_id = woot_helper.create_id('calendar-');
        }

        //***

        if (!unix_time_stamp) {
            this.current_date = new Date();
        } else {
            this.current_date = new Date(unix_time_stamp * 1000);
        }

        this.today = new Date();
        this.selected_date = null;//selected by click

        this.container = container;
        this.cells = [];
        this.fill_container();
    }

    fill_container() {
        this.cells = [];
        this.container.innerHTML = '';

        //***

        switch (this.scene) {
            case 1:
                this.fill_scene_1();//years
                break;

            case 2:
                this.fill_scene_2();//monthes
                break;

            default:
                this.fill_scene_0();//days
                break;
        }
    }

    get_prev_month_days_count() {
        let m = this.get_month() - 1;
        let y = this.get_year();
        if (m < 0) {
            m = 11;
            y--;
        }

        return new Date(y, m + 1, 0).getDate();

    }

    get_month() {
        return this.current_date.getMonth();
    }

    get_year() {
        return this.current_date.getFullYear();
    }

    get_days_in_month() {
        return new Date(this.get_year(), this.get_month() + 1, 0).getDate();
    }

    //***

    create_label_0() {
        let label = document.createElement('div');
        label.className = 'calendar23-label';

        let span = document.createElement('span');
        span.innerText = this.month_names[this.get_month()] + ' ' + this.get_year();
        label.appendChild(span);

        span.addEventListener('click', e => {
            e.stopPropagation();
            this.scene = 1;
            /*
             * 0 - days calendar
             * 1 - monthes list 7x5
             * 2 - years list 7x5
             */

            this.fill_container();
            return false;
        });

        //***

        let prev = document.createElement('a');
        prev.setAttribute('href', '#');
        prev.className = 'calendar23-prev';
        prev.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            let m = this.get_month() - 1;
            let y = this.get_year();
            if (m < 0) {
                m = 11;
                y--;
            }

            this.current_date = new Date(y, m, 1);
            this.fill_container();
            return false;
        });

        label.appendChild(prev);


        //***

        let next = document.createElement('a');
        next.setAttribute('href', '#');
        next.className = 'calendar23-next';
        next.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            let m = this.get_month() + 1;
            let y = this.get_year();
            if (m > 11) {
                m = 0;
                y++;
            }

            this.current_date = new Date(y, m, 1);
            this.fill_container();
            return false;
        });

        label.appendChild(next);

        //***

        return label;
    }

    fill_scene_0() {
        let month = document.createElement('div');
        month.className = 'calendar23-month';
        month.appendChild(this.create_label_0());

        for (let i = 0; i < 7; ++i) {
            let d = document.createElement('div');
            d.className = 'calendar23-dow';
            d.innerText = this.day_names[i];
            month.appendChild(d);
        }

        //***

        for (let i = 0; i < 42; ++i) {//7x6
            this.cells[i] = document.createElement('div');
            month.appendChild(this.cells[i]);
        }

        this.container.appendChild(month);

        //***

        let first_month_day = new Date(this.get_year(), this.get_month(), 1).getDay() - 1;//TODO when sunday first
        if (first_month_day < 0) {
            first_month_day = 6;
        }

        let day = 1;
        for (let i = first_month_day; i < this.get_days_in_month() + first_month_day; ++i) {
            let c = new Date(this.get_year(), this.get_month(), day);
            this.cells[i].innerText = day;
            this.cells[i].className = 'calendar23-day';
            this.cells[i].setAttribute('data-date', c.getTime() / 1000);

            if ([0, 6].includes(new Date(this.get_year(), this.get_month(), day).getDay())) {
                this.cells[i].classList.add('calendar23-weekend');
            }

            if (this.today.getDate() === day && this.today.getMonth() === this.get_month() && this.today.getFullYear() === this.get_year()) {
                this.cells[i].classList.add('calendar23-today');
            }

            if (this.selected_date !== null) {
                if (this.selected_date.getDate() === day && this.selected_date.getMonth() === this.get_month() && this.selected_date.getFullYear() === this.get_year()) {
                    this.cells[i].classList.add('calendar23-focused');
                }
            }

            day++;

            //***
            let _this = this;
            this.cells[i].addEventListener('click', function (e) {
                e.stopPropagation();
                _this.current_date = new Date(this.getAttribute('data-date') * 1000);
                _this.selected_date = new Date(this.getAttribute('data-date') * 1000);

                for (let y = 0; y < _this.cells.length; y++) {
                    if (_this.cells[y].classList.contains('calendar23-focused')) {
                        _this.cells[y].classList.remove('calendar23-focused');
                        break;
                    }
                }

                this.classList.add('calendar23-focused');
                document.dispatchEvent(new CustomEvent('calendar23-date-selected', {detail: {date: this.getAttribute('data-date'), calendar_id: _this.calendar_id}}));
                return false;
            });
        }

        //***
        //prev month days fill
        for (let i = 0; i < first_month_day; ++i) {
            this.cells[i].className = 'calendar23-dummy-day';
            this.cells[i].innerText = this.get_prev_month_days_count() - first_month_day + i + 1;
        }

        //***
        //next month days fill
        let next_month_day = 1;
        for (let i = 0; i < 42; ++i) {
            if (this.cells[i].innerText.length === 0) {
                this.cells[i].className = 'calendar23-dummy-day';
                this.cells[i].innerText = next_month_day++;
            }
        }

        //remove last not this month week
        let clear = true;
        for (let i = 42 - 7; i < 42; i++) {
            if (!this.cells[i].classList.contains('calendar23-dummy-day')) {
                clear = false;
                break;
            }
        }

        if (clear) {
            for (let i = 42 - 7; i < 42; i++) {
                this.cells[i].remove();
            }
        }
    }

    //***

    create_label_1() {
        let label = document.createElement('div');
        label.className = 'calendar23-label';

        let span = document.createElement('span');
        span.innerText = this.get_year();
        label.appendChild(span);

        span.addEventListener('click', e => {
            e.stopPropagation();
            this.scene = 2;
            this.fill_container();
            return false;
        });

        //***

        let prev = document.createElement('a');
        prev.setAttribute('href', '#');
        prev.className = 'calendar23-prev';
        prev.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            this.current_date = new Date(this.get_year() - 1, this.get_month(), 1);
            this.fill_container();
            return false;
        });

        label.appendChild(prev);

        //***

        let next = document.createElement('a');
        next.setAttribute('href', '#');
        next.className = 'calendar23-next';
        next.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            this.current_date = new Date(this.get_year() + 1, this.get_month(), 1);
            this.fill_container();
            return false;
        });

        label.appendChild(next);

        //***

        return label;
    }

    fill_scene_1() {
        let monthes = document.createElement('div');
        monthes.className = 'calendar23-month';
        monthes.appendChild(this.create_label_1());

        for (let i = 0; i < 12; ++i) {
            this.cells[i] = document.createElement('div');
            this.cells[i].className = 'calendar23-big calendar23-day';
            this.cells[i].setAttribute('data-month', i);
            this.cells[i].innerText = this.month_names_short[i];
            if (this.today.getMonth() === i && this.today.getFullYear() === this.get_year()) {
                this.cells[i].classList.add('calendar23-today');
            }
            monthes.appendChild(this.cells[i]);

            //***
            let _this = this;
            this.cells[i].addEventListener('click', function (e) {
                e.stopPropagation();
                _this.current_date.setMonth(this.getAttribute('data-month'));
                _this.scene = 0;
                _this.fill_container();
                return false;
            });
        }

        this.container.appendChild(monthes);

    }

    //***

    create_label_2() {
        let label = document.createElement('div');
        label.className = 'calendar23-label';

        let span = document.createElement('span');
        span.innerText = this.get_year() + ' - ' + (this.get_year() + 12 - 1);
        label.appendChild(span);

        //***

        let prev = document.createElement('a');
        prev.setAttribute('href', '#');
        prev.className = 'calendar23-prev';
        prev.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            this.current_date = new Date(this.get_year() - 12, this.get_month(), 1);
            this.fill_container();
            return false;
        });

        label.appendChild(prev);


        //***

        let next = document.createElement('a');
        next.setAttribute('href', '#');
        next.className = 'calendar23-next';
        next.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
            this.current_date = new Date(this.get_year() + 12, this.get_month(), 1);
            this.fill_container();
            return false;
        });

        label.appendChild(next);

        //***

        return label;
    }

    fill_scene_2() {
        let _this = this;
        let years = document.createElement('div');
        years.className = 'calendar23-month';
        years.appendChild(this.create_label_2());

        for (let i = this.get_year(); i < this.get_year() + 12; ++i) {
            this.cells[i] = document.createElement('div');
            this.cells[i].className = 'calendar23-big calendar23-day';
            this.cells[i].setAttribute('data-year', i);
            this.cells[i].innerText = i;
            if (this.today.getFullYear() === i) {
                this.cells[i].classList.add('calendar23-today');
            }
            years.appendChild(this.cells[i]);

            //***

            this.cells[i].addEventListener('click', function (e) {
                e.stopPropagation();
                _this.current_date.setFullYear(this.getAttribute('data-year'));
                _this.scene = 1;
                _this.fill_container();
                return false;
            });
        }

        this.container.appendChild(years);
    }
}

//***
//<div class="calendar23-selector" data-date="1729807200" data-name="new_data"></div>
class Calendar23_Selector {
    constructor(selector = null, unix_time_stamp = null, placeholder = '', calendar_names = {}) {
        let _this = this;
        this.unix_time_stamp = null;
        this.placeholder = placeholder;
        this.calendar_names = calendar_names;

        if (!selector) {
            if (!document.querySelectorAll('.calendar23-selector').length) {
                return;
            }

            document.querySelectorAll('.calendar23-selector').forEach(item => {
                this.init(item, unix_time_stamp);
            });
        } else {
            this.init(selector, unix_time_stamp);
        }

        //***

        this.input.addEventListener('focus', function (e) {
            e.stopPropagation();
            if (!_this.calendar_wrapper.style.display || _this.calendar_wrapper.style.display === 'none') {
                _this.calendar_wrapper.style.display = 'block';

                document.dispatchEvent(new CustomEvent('calendar23-date-focused', {detail: {calendar_id: _this.calendar_id}}));

            } else {
                _this.calendar_wrapper.style.display = 'none';
            }

            this.blur();
            return true;
        });

        document.addEventListener('click', e => {
            let close = true;

            if (e.target.closest('.calendar23-selector')) {
                close = false;
            }

            if (e.target.closest('.calendar23-month')) {
                close = false;
            }

            if (close) {
                this.calendar_wrapper.style.display = 'none';
            }
        });

        document.addEventListener('calendar23-date-selected', e => {
            e.stopPropagation();
            if (this.calendar_id === e.detail.calendar_id) {
                let unix_time_stamp = e.detail.date;
                this.input.setAttribute('data-selected-date', unix_time_stamp);
                this.input.setAttribute('value', unix_time_stamp);
                this.set_input_value(unix_time_stamp);
                this.calendar_wrapper.style.display = 'none';

                //***

                this.unix_time_stamp = unix_time_stamp;
                this.selected();
                document.dispatchEvent(new CustomEvent('calendar23-selector-date-selected', {detail: {date: unix_time_stamp, selector: this}}));
            }
        });
    }

    selected() {
        //should be reloaded by business logic!!
    }

    init(item, unix_time_stamp) {

        this.draw_html(item);

        //***

        this.input = item.querySelector('.calendar23-data-input');
        this.calendar_wrapper = item.querySelector('.calendar23-calendar-wrapper');

        //close another calendars if current one is focused
        document.addEventListener('calendar23-date-focused', (e) => {
            if (e.detail.calendar_id !== this.calendar_id) {
                this.calendar_wrapper.style.display = 'none';
            }
        });

        this.calendar_id = woot_helper.create_id('calendar23-');//for get selected

        //***

        if (this.input.getAttribute('data-selected-date').length > 0) {
            this.unix_time_stamp = parseInt(this.input.getAttribute('data-selected-date'), 10);
        }

        if (unix_time_stamp) {
            this.unix_time_stamp = unix_time_stamp;//more prioritet
        }

        if (this.unix_time_stamp) {
            this.set_input_value(this.unix_time_stamp);
        }

        //***

        this.calendar = new Calendar23(this.calendar_wrapper.querySelector('div'), this.calendar_id, this.unix_time_stamp, {calendar_names: this.calendar_names});

    }

    set_input_value(unix_time_stamp) {
        if (document.querySelector('html').getAttribute('lang')) {
            //var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            this.input.value = new Date(unix_time_stamp * 1000).toLocaleDateString(document.querySelector('html').getAttribute('lang'), {year: 'numeric', month: 'long', day: 'numeric'});
        } else {
            this.input.value = new Date(unix_time_stamp * 1000).toLocaleDateString();
        }
    }

    draw_html(container) {
        let input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('readonly', 'readonly');
        input.className = 'calendar23-data-input';
        input.setAttribute('data-selected-date', '');
        input.setAttribute('value', '');
        input.setAttribute('placeholder', this.placeholder);

        if (container.hasAttribute('data-date')) {
            input.setAttribute('data-selected-date', container.getAttribute('data-date'));
        }

        if (container.hasAttribute('data-name')) {
            input.setAttribute('name', container.getAttribute('data-name'));
        }

        //***

        let wrapper = document.createElement('div');
        wrapper.className = 'calendar23-calendar-wrapper';

        let div = document.createElement('div');

        wrapper.appendChild(div);
        container.appendChild(input);
        container.appendChild(wrapper);
    }
}



