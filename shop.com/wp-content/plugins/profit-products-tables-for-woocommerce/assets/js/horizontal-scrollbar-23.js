/**
 * @summary     Horizontal Scrollbar 23
 * @description horizontal scrollbar for <table>
 * @version     1.0.0
 * @file        horizontal-scrollbar-23
 * @author      realmag777
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/horizontal-scrollbar-23
 * @copyright   Copyright 2020 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 *   MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

class HorizontalScrollbar23 {
    constructor(container) {
        this.is_mobile = 'ontouchstart' in document.documentElement;

        if (this.is_mobile) {
            //user can use finger
            return false;
        }

        //***

        this.container = container;
        this.container.style.overflowX = 'hidden';

        this.wrapper = document.createElement('div');
        this.wrapper.className = 'horizontal-scrollbar23-wrapper';

        this.track = document.createElement('div');
        this.track.className = 'horizontal-scrollbar23-track';
        this.track.style.width = this.container.clientWidth + 'px';

        this.handler = document.createElement('div');
        this.handler.className = 'horizontal-scrollbar23-handler';
        this.__set_handler_width();
        this.track.appendChild(this.handler);

        this.wrapper.appendChild(this.track);
        //append after
        this.container.parentNode.insertBefore(this.wrapper, this.container.nextSibling);

        this.track_width = this.track.offsetWidth;
        this.handler_width = this.handler.offsetWidth;

        this.value = 0;
        this.dragged = null;

        this.click_event = this.is_mobile ? 'touchstart' : 'click';
        this.down_event = this.is_mobile ? 'touchstart' : 'mousedown';
        this.up_event = this.is_mobile ? 'touchend' : 'mouseup';
        this.move_event = this.is_mobile ? 'touchmove' : 'mousemove';

        this.wrapper.style.display = 'none';

        //+++

        this.handler.addEventListener(this.down_event, (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.dragged = this;
        });

        document.addEventListener(this.up_event, (e) => {
            //e.preventDefault();
            this.dragged = null;
            return true;
        });

        document.addEventListener(this.move_event, (e) => {
            //e.preventDefault();

            if (this.dragged) {
                let x = e.clientX + this.handler_width / 2;
                if (this.is_mobile) {
                    x = e.touches[0].clientX;
                }

                this.move(x);
            }

            return true;
        });

        this.track.addEventListener(this.click_event, (e) => {
            e.preventDefault();
            e.stopPropagation();

            let x = e.clientX;
            if (this.is_mobile) {
                x = e.touches[0].clientX;
            }

            //to avoid double moving
            if (e.target === this.track && !this.dragged) {
                this.move(x, this.handler);
            }

        });

        //***

        window.addEventListener('resize', (e) => {
            this.resize();
        });

        //***

        document.addEventListener('scroll', (e) => {
            let bounding = this.container.querySelector('table').getBoundingClientRect();
            let condition = window.innerHeight - bounding.top > 75 &&
                    window.innerHeight - bounding.bottom < window.innerHeight / 4;

            this.__scroll(bounding, condition);
        });

        //adaptation for WOOT
        document.addEventListener('popup23-scrolling', (e) => {
            let table = e.detail.self.node.querySelector('table');

            if (table) {
                if (this.container.closest('.woot-modal-inner-content')) {
                    this.__scroll(table.getBoundingClientRect(), true);
                }
            }
        });


    }

    __scroll(bounding, condition) {

        if (bounding.height < window.innerHeight) {
            condition = true;
        } else {
            this.wrapper.style.position = 'fixed';
        }

        if (condition) {
            this.wrapper.style.display = 'block';
        } else {
            this.wrapper.style.display = 'none';
        }

        this.__set_handler_width();
    }

    __set_handler_width() {
        this.handler.style.width = (this.container.offsetWidth / this.container.scrollWidth) * this.container.offsetWidth + 'px';
        this.track.style.width = this.container.offsetWidth + 'px';

        if (this.container.scrollWidth <= this.handler.offsetWidth + 30) {
            //30 here is minimum width when the scrollbar visibility has the sense
            this.wrapper.style.display = 'none';
            return false;
        }

        return true;
    }

    move(x, handler = null) {

        let h = this.dragged;

        if (handler) {
            h = handler;
        }

        if (h) {
            let track_distance = Math.ceil((this.track.getBoundingClientRect()).x);

            let can_move = true;
            let distance = x - track_distance;

            let set_min = false;
            let set_max = false;

            if (track_distance + this.handler_width > x) {
                this.handler.style.left = '0px';
                can_move = false;
                set_min = true;
            }

            if (distance > this.track_width) {
                can_move = false;
                this.handler.style.left = this.track_width - this.handler_width + 'px';
                set_max = true;
            }

            if (!can_move) {
                this.resize();
            }

            if (can_move) {
                this.handler.style.left = distance - this.handler_width + 'px';
                this.value = Math.ceil(parseFloat(parseInt(this.handler.style.left) / parseInt(this.track_width - this.handler_width)) * 100);

                //closer
                if (this.value >= 100) {
                    this.value = 100;
                }

                //closer
                if (this.value <= 0) {
                    this.value = 0;
                }
            }

            //***

            let scroll = Math.floor(parseFloat((this.container.scrollWidth - this.track_width) / 100) * this.value);

            //closer
            if (set_min) {
                scroll = 0;
            }

            //closer
            if (set_max) {
                scroll *= 2;
            }

            this.container.scrollTo({
                top: 0,
                left: scroll,
                behavior: 'auto'
            });

    }
    }

    resize() {
        this.__set_handler_width();
        this.track_width = this.track.offsetWidth;
        this.track_width = this.track.offsetWidth;
        this.handler_width = this.handler.offsetWidth;
    }

    remove() {
        if (this.wrapper) {
            this.wrapper.remove();
        }
    }

    set_the_topmost() {
        let max_z_index = 0;
        document.querySelectorAll('.horizontal-scrollbar23-wrapper').forEach(function (scroll_bar) {
            if (parseInt(scroll_bar.style.zIndex)) {
                if (parseInt(scroll_bar.style.zIndex) > max_z_index) {
                    max_z_index = parseInt(scroll_bar.style.zIndex);
                }
            }
        });

        this.wrapper.style.zIndex = max_z_index + 1;
    }
}
