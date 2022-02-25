/**
 * @summary     SelectM23
 * @description multi choice drop-down
 * @version     1.0.0
 * @file        selectm-23
 * @author      realmag777
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/selectm-23
 * @copyright   Copyright 2020 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 *   MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

class SelectM23 {
    constructor(select, use_drag = false, placeholder = '') {

        if (!select.hasAttribute('multiple')) {
            return false;
        }

        this.selected_values = [];
        this.use_drag = use_drag;
        this.placeholder = placeholder;

        if (select.hasAttribute('data-use-drag')) {
            if (parseInt(select.getAttribute('data-use-drag'), 10)) {
                this.use_drag = true;
            } else {
                this.use_drag = false;
            }
        }

        this.dragged_element = null;//for moving selected options
        this.ghost = null;
        this.ghost_delay_timer = null;//to avoid immediate clicks
        this.is_mobile = 'ontouchstart' in document.documentElement;

        //***

        this.select = select;
        this.hide(this.select);

        this.wrapper = document.createElement('div');
        this.wrapper.className = 'selectm23';
        this.select.after(this.wrapper);
        this.wrapper.appendChild(this.select);
        this.set_placeholder();

        this.selecton = document.createElement('span');//selected options here
        this.selecton.className = 'selectm23-selecton';
        this.wrapper.appendChild(this.selecton);


        this.input = document.createElement('input');
        this.input.className = 'selectm23-input selectm23-hidden';
        this.input.setAttribute('type', 'text');
        this.input.setAttribute('autocomplete', 'off');
        this.wrapper.appendChild(this.input);

        this.options = document.createElement('div');//all options exept selected ones
        this.options.className = 'selectm23-options selectm23-hidden';
        this.wrapper.appendChild(this.options);

        this.select.querySelectorAll('option').forEach((o) => {

            if (o.value === 'selectm23-exclude') {
                o.remove();
                return;
            }

            let option = document.createElement('div');
            option.className = 'selectm23-option';
            option.setAttribute('data-value', o.value);
            option.innerHTML = o.innerText;

            if (o.hasAttribute('selected')) {
                this.hide(option);
            }

            this.options.appendChild(option);

            option.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
                this.add_selected(option);
                this.input.focus();
                return true;
            });
        });

        //***

        this.wrapper.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
            this.show_options();
            this.show(this.input);
            this.input.focus();
            return true;
        });

        this.input.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
            this.input.focus();
            this.show_options();
            return true;
        });

        document.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
            let show = e.target === this.input;
            if (!show) {
                show = e.target === this.wrapper;
            }

            if (!show) {
                if (e.target.classList.contains('selectm23-selected')) {
                    show = true;
                    this.input.focus();
                }
            }

            if (!show) {
                this.hide(this.options);
                this.hide(this.input);
                this.unselect_selected();
            }

            this.input.value = '';
            //this.set_placeholder(false);

            return true;
        });

        this.input.addEventListener('keyup', (e) => {
            this.show_options();
            switch (e.keyCode) {
                case 37:
                case 39:
                    this.select_option(this.find_next_visible_sibling(this.options.querySelectorAll('.selectm23-option')[0]));
                    break;
                case 38://up
                case 40://down
                    if (this.options.querySelector('.selectm23-option-selected')) {
                        let current = this.options.querySelector('.selectm23-option-selected');
                        if (e.keyCode === 40) {
                            let next = this.find_next_visible_sibling(current);
                            if (next) {
                                this.unselect_selected();
                                this.select_option(next);
                                this.options.scrollTop = next.offsetTop;
                                break;
                            }
                        } else {
                            let prev = this.find_prev_visible_sibling(current);
                            if (prev) {
                                this.unselect_selected();
                                this.select_option(prev);
                                this.options.scrollTop = prev.offsetTop;
                                break;
                            }
                        }
                    } else {
                        this.select_option(this.find_next_visible_sibling(this.options.querySelectorAll('.selectm23-option')[0]));
                    }

                    break;
                case 13:
                    if (this.options.querySelector('.selectm23-option-selected')) {
                        let option = this.options.querySelector('.selectm23-option-selected');
                        this.add_selected(option);
                    }
                    break;
                case 27:

                    if (this.input.value.length > 0) {
                        this.input.value = '';
                        this.options.querySelectorAll('.selectm23-option').forEach((option) => {
                            if (!this.selected_values.includes(option.getAttribute('data-value'))) {
                                this.show(option);
                            }
                        });
                    } else {
                        this.hide(this.options);
                        this.hide(this.input);
                    }

                    this.unselect_selected();
                    break;
                default:

                    this.options.querySelectorAll('.selectm23-option').forEach((option) => {
                        if (this.input.value.length > 0) {
                            if (option.innerText.search(new RegExp(this.input.value, 'i')) !== -1) {
                                if (!this.selected_values.includes(option.getAttribute('data-value'))) {
                                    this.show(option);
                                }
                            } else {
                                this.hide(option);
                            }
                        } else {
                            if (!this.selected_values.includes(option.getAttribute('data-value'))) {
                                this.show(option);
                            }
                        }
                    });
                    break;
            }


            if (this.input.value.length > 0) {
                this.set_placeholder(true);
            } else {
                this.set_placeholder(false);
            }

            return true;
        });

        //***


        this.set_select_selected_options();
    }

    /*********************************************/

    set_select_selected_options() {
        let selected_options = [];
        let selected_vals = '';

        if (this.select.hasAttribute('data-values')) {
            selected_vals = this.select.getAttribute('data-values').split(',');
        }

        //***

        this.select.querySelectorAll('option').forEach((option) => {
            if (option.hasAttribute('selected')) {
                if (selected_vals.length) {
                    try {
                        selected_options[option.value] = this.options.querySelector(`[data-value="${option.value}"]`);
                    } catch (e) {
                        //key "length": https://stackoverflow.com/questions/61655194/uncaught-rangeerror-invalid-array-length-js-engine-bug-see-the-code-example
                        selected_options['~' + option.value] = this.options.querySelector(`[data-value="${option.value}"]`);
                    }
                } else {
                    this.add_selected(option, false);
                }
            }

        });

        //***

        for (let i = 0; i < selected_vals.length; i++) {
            if (selected_options['~' + selected_vals[i]]) {
                //fix for length field https://stackoverflow.com/questions/61655194/uncaught-rangeerror-invalid-array-length-js-engine-bug-see-the-code-example
                this.add_selected(selected_options['~' + selected_vals[i]], false);
            } else {
                this.add_selected(selected_options[selected_vals[i]], false);
            }

        }

        //***
        if (this.use_drag) {
            document.addEventListener(this.is_mobile ? 'touchend' : 'mouseup', (e, x) => {
                if (this.ghost) {
                    e.preventDefault();
                    this.delete_ghost();
                    this.dragged_element = null;
                }
            });

            document.addEventListener(this.is_mobile ? 'touchmove' : 'mousemove', (e) => {
                if (this.ghost) {

                    let target = e.target;

                    if (this.is_mobile) {
                        target = document.elementFromPoint(e.changedTouches[e.changedTouches.length - 1].pageX, e.changedTouches[e.changedTouches.length - 1].pageY);
                    }

                    if (target.closest('.selectm23') === this.wrapper) {
                        if (target.classList.contains('selectm23-selected')) {

                            if (this.ghost !== target) {

                                if (this.find_prev_visible_sibling(this.ghost) === target) {
                                    target.before(this.ghost);
                                } else {
                                    target.after(this.ghost);
                                }

                            }
                        }
                    }

                }
            });
        }
    }

    hide(element) {
        element.classList.add('selectm23-hidden');
    }

    show(element) {
        element.classList.remove('selectm23-hidden');
    }

    find_prev_visible_sibling(option) {
        let prev = null;

        if (option.previousElementSibling) {
            if (!option.previousElementSibling.classList.contains('selectm23-hidden')) {
                prev = option.previousElementSibling;
            } else {
                prev = this.find_prev_visible_sibling(option.previousElementSibling);
            }
        }

        return prev;
    }

    find_next_visible_sibling(option) {
        let next = null;

        if (option.nextElementSibling) {
            if (!option.nextElementSibling.classList.contains('selectm23-hidden')) {
                next = option.nextElementSibling;
            } else {
                next = this.find_next_visible_sibling(option.nextElementSibling);
            }
        }

        return next;
    }

    add_selected(option, cast = true) {
        if (option) {
            let option_value = null;
            if (option.hasAttribute('data-value')) {
                option_value = option.getAttribute('data-value');
            } else {
                option_value = option.getAttribute('value');
            }

            let selected = document.createElement('span');
            selected.className = 'selectm23-selected';
            selected.setAttribute('data-value', option_value);
            this.selected_values.push(option_value);
            if (this.select.querySelector(`option[value="${option_value}"]`)) {
                this.select.querySelector(`option[value="${option_value}"]`).setAttribute('selected', '');
            }
            selected.innerHTML = option.innerText;
            this.hide(option);
            this.hide(this.options);
            //remove all text nodes
            //[...this.selecton.childNodes].forEach(elm => elm.nodeType !== 1 && elm.parentNode.removeChild(elm))
            this.set_placeholder(true);
            this.selecton.appendChild(selected);

            let cross = document.createElement('span');
            cross.className = 'selectm23-selected-cross';
            cross.innerHTML = 'x';
            selected.appendChild(cross);

            if (cast) {
                this.cast();
            }

            selected.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
                clearTimeout(this.ghost_delay_timer);
                this.show_options();
                this.input.focus();

                return true;
            });

            //+++
            if (this.use_drag) {
                selected.addEventListener(this.is_mobile ? 'touchstart' : 'mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    this.ghost_delay_timer = setTimeout(() => {
                        this.hide(this.options);
                        this.create_ghost(selected);
                    }, 223);

                });
            }
            //***

            cross.addEventListener(this.is_mobile ? 'touchstart' : 'click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.selected_values.splice(this.selected_values.indexOf(selected.getAttribute('data-value')), 1);
                this.show(this.options.querySelector(`[data-value="${selected.getAttribute('data-value')}"]`));
                this.select.querySelector(`option[value="${option_value}"]`).removeAttribute('selected');
                selected.remove();
                this.hide(this.options);
                this.hide(this.input);
                this.cast();
                clearTimeout(this.ghost_delay_timer);

                if (this.selecton.querySelectorAll('.selectm23-selected').length === 0) {
                    this.set_placeholder();
                }

                return true;
            });
    }
    }

    unselect_selected() {
        if (this.options.querySelector('.selectm23-option-selected')) {
            this.options.querySelector('.selectm23-option-selected').classList.remove('selectm23-option-selected');
        }
    }

    select_option(option) {
        option.classList.add('selectm23-option-selected');
    }

    show_options() {
        if (typeof SelectM23.start_z_index === 'undefined') {
            SelectM23.start_z_index = 9999;
        }

        this.options.style.zIndex = SelectM23.start_z_index++;
        this.show(this.options);
    }

    cast() {

        if (this.selected_values.length > 0) {
            this.set_placeholder(true);            
        } else {
            this.set_placeholder(false);
        }

        if (this.use_drag) {
            this.reorder_values();
        } else {
            this.select.dispatchEvent(new Event('change'));//disabled because not right reorder saved if to add new option
        }
    }

    //+++

    create_ghost(element) {
        this.dragged_element = element;
        this.ghost = this.dragged_element.cloneNode(true);
        this.ghost.classList.add('selectm23-ghost');
        this.dragged_element.after(this.ghost);
        this.hide(this.dragged_element);
    }

    delete_ghost() {
        clearTimeout(this.ghost_delay_timer);
        if (this.ghost) {
            this.ghost.before(this.dragged_element);
            this.show(this.dragged_element);
            this.ghost.remove();
            this.ghost = null;

            //***

            this.reorder_values();
        }
    }

    reorder_values() {
        let values = [];
        this.selecton.querySelectorAll('.selectm23-selected').forEach((option) => {
            values.push(option.getAttribute('data-value'));
        });

        //***

        if (this.select.getAttribute('data-values') !== values.join(',')) {
            this.select.setAttribute('data-values', values.join(','));

            this.select.dispatchEvent(new CustomEvent('selectm23-reorder', {detail: {
                    values: this.select.getAttribute('data-values')
                }}));
        }
    }

    set_placeholder(empty = false) {
        if (empty) {
            this.wrapper.style.background = '';
        } else {
            this.wrapper.style.background = `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='50px' width='120px'><text x='5' y='18' fill='black' font-size='14'>${this.placeholder}</text></svg>") no-repeat`;
    }

    }
}
