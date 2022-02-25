'use strict';

Element.prototype.appendBefore = function (element) {
    element.parentNode.insertBefore(this, element);
}, false;


Element.prototype.appendAfter = function (element) {
    element.parentNode.insertBefore(this, element.nextSibling);
}, false;

if (!String.prototype.format) {
    String.prototype.sprintf = function () {
        let args = arguments;
        return this.replace(/{(\d+)}/g, (match, number) => {
            return typeof args[number] !== 'undefined' ? args[number] : match;
        });
    };
}


var woot_helper = new (function () {
    return {
        prepare_ajax_form_data: function (data) {
            const formData = new FormData();

            Object.keys(data).forEach(function (k) {
                formData.append(k, data[k]);
            });

            return formData;
        },
        message: function (message_txt, type = 'notice', duration = 0) {
            if (duration === 0) {
                duration = 1777;
            }

            //***

            let container = null;

            if (!document.querySelectorAll('#growls').length) {
                container = document.createElement('div');
                container.setAttribute('id', 'growls');
                container.className = 'default';
                document.querySelector('body').appendChild(container);
            } else {
                container = document.getElementById('growls');
            }

            //***

            let id = this.create_id('m-');

            let wrapper = document.createElement('div');
            wrapper.className = 'growl growl-large growl-' + type;
            wrapper.setAttribute('id', id);

            let title = document.createElement('div');
            title.className = 'growl-title';
            let title_text = '';

            switch (type) {
                case 'warning':
                    title_text = woot_helper_vars.lang.m_warning;
                    break;

                case 'error':
                    title_text = woot_helper_vars.lang.m_error;
                    break;

                default:
                    title_text = woot_helper_vars.lang.m_notice;
                    break;
            }

            title.innerHTML = title_text;

            let message = document.createElement('div');
            message.className = 'growl-message';
            message.innerHTML = message_txt;

            //***

            //wrapper.appendChild(close);
            wrapper.appendChild(title);
            wrapper.appendChild(message);

            container.innerHTML = '';
            container.appendChild(wrapper);

            wrapper.addEventListener('click', function (e) {
                e.stopPropagation();
                this.remove();
                return false;
            });

            if (duration !== -1) {
                setTimeout(function () {
                    wrapper.style.opacity = 0;
                    setTimeout(function () {
                        wrapper.remove();
                    }, 777);
                }, duration);
        }

        },

        create_id: function (prefix = '') {
            return prefix + Math.random().toString(36).substring(7);
        },
        call_popup(command, more_data = {}, call_id = '', popup_title = '', popup_options = {}, title_info = '') {
            let popup = new Popup23(popup_options);
            popup.set_title(popup_title);

            if (title_info.length > 0) {
                popup.set_title_info(title_info);
            }

            //***

            if (!call_id) {
                call_id = woot_helper.create_id('woot-');
            }

            fetch(woot_helper_vars.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: woot_helper.prepare_ajax_form_data({
                    action: 'woot_get_smth',
                    what: JSON.stringify({call_action: command, more_data: more_data}),
                    call_id: call_id,
                    lang: woot_helper_vars.selected_lang
                })
            }).then(response => response.text()).then(data => {
                if (typeof more_data.not_paste !== 'undefined' && more_data.not_paste) {
                    popup.set_content(data);
                }

                let generated_table = null;

                if (popup.node.querySelector('.woot-data-table.woot-data-table-self-call')) {
                    generated_table = new WOOT_GeneratedTables(JSON.parse(popup.node.querySelector('.woot-table-json-data').innerText), popup.node.querySelector('.woot-table-json-data').getAttribute('data-table-id'));
                }

                document.dispatchEvent(new CustomEvent('woot-call-popup', {detail: {
                        popup: popup,
                        call_id: call_id,
                        data: data,
                        generated_table: generated_table
                    }}));
            }).catch((err) => {
                woot_helper.message(err, 'error', 5000);
            });

            return false;
        },

        draw_switcher(name, value, post_id, event) {
            let id = this.create_id('sw');
            let container = document.createElement('div');
            container.classList.add('switcher23-container');

            let hidden = document.createElement('input');
            hidden.setAttribute('type', 'hidden');
            hidden.setAttribute('name', name);
            hidden.setAttribute('value', value);

            let checkbox = document.createElement('input');
            checkbox.setAttribute('type', 'checkbox');
            checkbox.setAttribute('id', id);
            checkbox.setAttribute('class', 'switcher23');
            checkbox.setAttribute('value', value);

            if (value) {
                checkbox.setAttribute('checked', true);
            }

            checkbox.setAttribute('data-post-id', post_id);
            checkbox.setAttribute('data-event', event);

            let label = document.createElement('label');
            label.setAttribute('for', id);
            label.setAttribute('class', 'switcher23-toggle');
            label.innerHTML = '<span></span>';

            container.appendChild(hidden);
            container.appendChild(checkbox);
            container.appendChild(label);


            return container;
        },

        init_switcher(button) {
            button.addEventListener('click', function (e) {

                e.stopPropagation();

                if (this.value > 0) {
                    this.value = 0;
                    this.previousSibling.value = 0;
                    this.removeAttribute('checked');
                } else {
                    this.value = 1;
                    this.previousSibling.value = 1;
                    this.setAttribute('checked', 'checked');
                }

                //Trigger the event
                if (this.getAttribute('data-event').length > 0) {
                    //window.removeEventListener(this.getAttribute('data-event'));

                    let data = {
                        self: this,
                        ajax_action: this.getAttribute('data-ajax-action'),
                        name: this.previousSibling.getAttribute('name'),
                        post_id: this.getAttribute('data-post-id'),
                        value: parseInt(this.value, 10),
                        custom_data: null
                    };

                    if (this.getAttribute('data-custom-data') && this.getAttribute('data-custom-data').length > 0) {
                        data.custom_data = JSON.parse(this.getAttribute('data-custom-data'));
                    }

                    document.dispatchEvent(new CustomEvent(this.getAttribute('data-event'), {detail: data}));

                    //this.setAttribute('data-event-attached', 1);
                }



                return true;
            });
        },

        get_loader_html() {
            return `<div class="table23-place-loader">${woot_helper_vars.lang.loading}</div><br />`;
        },

        //make thumb bigger on onmouseover
        image_preview(_this) {
            var x = 150;
            var y = 30;
            let container = document.createElement('p');
            container.setAttribute('id', 'woot_img_preview');
            document.body.appendChild(container);

            //***

            let img = document.createElement('img');
            img.setAttribute('src', _this.getAttribute('data-full-img'));
            img.setAttribute('alt', woot_helper_vars.lang.loading);
            //img.setAttribute('width', _this.getAttribute('data-width'));//preview size        
            img.style.maxWidth = _this.getAttribute('data-width') + 'px';

            let text = document.createElement('div');
            text.innerHTML = '<br />' + _this.getAttribute('data-title');

            container.appendChild(img);
            container.appendChild(text);

            container.style.top = (_this.pageY - x) + 'px';
            container.style.left = (_this.pageX + y) + 'px';
            container.style.display = 'block';

            //***

            _this.addEventListener('mousemove', function (e) {
                e.stopPropagation();
                container.style.top = (e.pageY - x) + 'px';
                container.style.left = (e.pageX + y) + 'px';
            });

            _this.addEventListener('mouseleave', function (e) {
                e.stopPropagation();
                container.remove();
            });

        },

        //for tables in popup inbuilted into tables
        get_closest_skin(o_self, skin = '') {
            if (skin.length > 0) {
                return skin;
            }

            let closest = o_self.closest('div.data-table-23');
            skin = closest.getAttribute('data-skin');

            if (!skin) {
                if (closest.parentElement.closest('div.data-table-23')) {
                    closest = closest.parentElement.closest('div.data-table-23');
                    skin = closest.getAttribute('data-skin');
                }
            }

            if (!skin) {
                if (closest.parentElement.closest('div.data-table-23')) {
                    closest = closest.parentElement.closest('div.data-table-23');
                    skin = closest.getAttribute('data-skin');
                }
            }

            return skin;
        }

    };
});