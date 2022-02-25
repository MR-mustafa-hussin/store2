/**
 * @summary     Popup23
 * @description popup window
 * @version     1.0.0
 * @file        popup-23
 * @author      realmag777
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/popup-23
 * @copyright   Copyright 2020 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 *   MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

//1 object == 1 popup
class Popup23 {

    constructor(data = {}) {
        if (typeof Popup23.z_index === 'undefined') {
            Popup23.z_index = 15003;
        }

        ++Popup23.z_index;
        this.create(data);
    }

    create(data = {}) {
        this.node = document.createElement('div');
        let div_id = woot_helper.create_id('popw-');
        this.node.setAttribute('id', div_id);
        this.node.className = 'woot-dynamic-popup-wrapper';
        this.node.innerHTML = document.querySelector('#woot-popup-template').innerHTML;
        document.querySelector('body').appendChild(this.node);
        this.node.querySelector('.woot-modal').style.zIndex = Popup23.z_index;
        this.node.querySelector('.woot-modal-backdrop').style.zIndex = Popup23.z_index - 1;

        this.node.querySelectorAll('.woot-modal-close, .woot-modal-button-large-1').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                
                this.node.remove();
                return false;
            });
        });

        //***

        if (typeof data.iframe !== 'undefined' && data.iframe.length > 0) {
            let iframe = document.createElement('iframe');
            iframe.className = 'woot-iframe-in-popup';

            if (typeof data.height !== 'undefined') {
                iframe.height = data.height;
            } else {
                iframe.height = this.get_content_area_height();
            }

            iframe.frameborder = 0;
            iframe.allowfullscreen = '';
            iframe.allow = typeof data.allow !== 'undefined' ? data.allow : '';

            iframe.src = data.iframe;
            this.set_content('');
            this.append_content(iframe);
        }

        //***

        if (typeof data.title !== 'undefined' && data.title.length > 0) {
            this.set_title(data.title);
        }

        if (typeof data.help_title !== 'undefined' && data.help_title.length > 0) {
            if (typeof data.help_link !== 'undefined' && data.help_link.length > 0) {
                this.set_title_info(`<a href="${data.help_link}" class="woot-btn" target="_blank">${data.help_title}</a>`);
            }
        }

        if (typeof data.width !== 'undefined') {
            this.node.querySelector('.woot-modal').style.maxWidth = data.width + 'px';
        }

        if (typeof data.height !== 'undefined') {
            this.node.querySelector('.woot-modal').style.maxHeight = data.height + 'px';
        }

        if (typeof data.left !== 'undefined') {
            this.node.querySelector('.woot-modal').style.left = data.left + '%';
        }

        if (typeof data.left !== 'undefined') {
            this.node.querySelector('.woot-modal').style.right = data.right + '%';
        }

        if (typeof data.action !== 'undefined' && data.action.length > 0) {
            document.dispatchEvent(new CustomEvent(data.action, {detail: {...data, ...{popup: this}}}));
        }

        if (typeof data.what !== 'undefined' && data.what) {
            fetch(woot_helper_vars.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: woot_helper.prepare_ajax_form_data({
                    action: 'woot_get_smth',
                    what: data.what,
                    post_id: data.post_id,
                    lang: woot_helper_vars.selected_lang
                })
            }).then((response) => response.text()).then((content) => {
                this.set_content(content);
                document.dispatchEvent(new CustomEvent('woot-popup-smth-loaded', {detail: {popup: this, content: content, post_id: data.post_id, what: data.what}}));
            }).catch((err) => {
                woot_helper.message(err, 'error', 5000);
            });
        }

        //***

        if (typeof data.shortcodes_set !== 'undefined' && data.shortcodes_set) {
            fetch(woot_helper_vars.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: woot_helper.prepare_ajax_form_data({
                    action: 'woot_get_smth',
                    what: 'shortcodes_set',
                    shortcodes_set: data.shortcodes_set,
                    lang: woot_helper_vars.selected_lang
                })
            }).then((response) => response.text()).then((content) => {
                this.set_content(content);
                document.dispatchEvent(new CustomEvent('woot-popup-smth-loaded', {detail: {popup: this, content: content, what: {call_action: 'shortcodes_set'}, shortcodes_set: data.shortcodes_set}}));

                /*
                 this.shortcodes_set_interval = setInterval(() => {
                 if (this.node.querySelector('.woot-table')) {
                 this.node.querySelector('.woot-tables-set a').dispatchEvent(new Event('click'));
                 clearInterval(this.shortcodes_set_interval);
                 }
                 }, 999);
                 */

            }).catch((err) => {
                woot_helper.message(err, 'error', 5000);
            });
        }

        //***

        this.node.querySelector('.woot-modal-inner-content').addEventListener('scroll', (e) => {
            document.dispatchEvent(new CustomEvent('popup23-scrolling', {detail: {
                    top: e.srcElement.scrollTop,
                    self: this
                }}));

            //+++

            let elem = this.node.querySelector('.woot-data-table > .table23-wrapper');
            if (elem) {
                let flow = elem.querySelector('.table23-flow-header');

                if (flow) {
                    let box = elem.getBoundingClientRect();
                    let box2 = this.node.querySelector('.woot-modal-inner-header').getBoundingClientRect();
                    let first_row = elem.querySelector('table thead tr');

                    if (box.top <= Math.abs(box2.height) / 3) {

                        flow.style.display = 'block';
                        flow.style.width = (elem.querySelector('table').offsetWidth + 10) + 'px';
                        flow.style.top = 2 * Math.abs(box2.height) + Math.abs(box.top) + 'px';

                        Array.from(first_row.querySelectorAll('th')).forEach((th, index) => {
                            flow.querySelectorAll('div')[index].style.width = th.offsetWidth + 1 + 'px';
                            flow.querySelectorAll('div')[index].innerHTML = th.innerText;
                        });

                    } else {
                        flow.style.display = 'none';
                    }
                }
            }

        });

        //***

        return this.node;
    }

    set_title(title) {
        this.node.querySelector('.woot-modal-title').innerHTML = title;
    }

    set_title_info(info) {
        this.node.querySelector('.woot-modal-title-info').innerHTML = info;
    }

    set_content(content) {
        this.node.querySelector('.woot-form-element-container').innerHTML = content;
    }

    append_content(node) {
        this.node.querySelector('.woot-form-element-container').appendChild(node);
    }

    get_content_area_height() {
        return this.node.querySelector('.woot-modal-inner-content').offsetHeight - 50;
    }
}

