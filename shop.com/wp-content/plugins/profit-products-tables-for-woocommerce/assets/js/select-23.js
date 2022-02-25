/**
 * @summary     Select23
 * @description single choice drop-down
 * @version     1.0.0
 * @file        select-23
 * @author      realmag777
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/select-23
 * @copyright   Copyright 2020 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 *   MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

class Select23 {
    constructor(container, data, connect_id = '', attributes = {}) {

        this.connect_id = connect_id;
        this.select = document.createElement('select');
        this.container=container;

        if (Object.keys(attributes).length) {
            for (let key in attributes) {
                this.select.setAttribute(key, attributes[key]);
            }
        }

        if (Object.keys(data).length) {
            for (let key in data) {
                let option = document.createElement('option');
                option.value = key;
                option.innerText = data[key];
                this.select.appendChild(option);
            }
        }

        //***
        container.innerHTML = '';
        container.appendChild(this.select);
        this.init();

    }

    init() {
        this.select.addEventListener('change', (e) => {
            document.dispatchEvent(new CustomEvent('select23-change', {detail: {
                    connect_id: this.connect_id,
                    value: this.select.value
                }}));
        });
    }
}
