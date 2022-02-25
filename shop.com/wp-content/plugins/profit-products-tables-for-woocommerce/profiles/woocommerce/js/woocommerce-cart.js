'use strict';

var woot_cart;

document.addEventListener('table23-html-drawn', function (e) {
    e.detail.otable.table.querySelectorAll("[data-key='add_to_cart']").forEach(function (item) {
        if (!item.hasAttribute('data-inited')) {
            item.setAttribute('data-inited', 1);
            item.addEventListener('click', function (e) {
                //e.preventDefault();
                e.stopPropagation();

                if (e.target === item) {
                    woot_cart.call_popup(woot_helper.get_closest_skin(this));
                }

                return false;
            }, false);
        }
    });

    return true;
});

document.addEventListener('table23-html-drawn', function (e) {
    e.detail.otable.table.querySelectorAll("th[data-key='add_to_cart']").forEach(function (item) {

        let button = document.createElement('a');
        button.innerHTML = woot_helper_vars.lang.add_to_cart_selected;
        button.setAttribute('href', '#');
        button.className = 'woot-add-to-cart-all-btn';
        item.appendChild(button);

        button.addEventListener('click', function (e) {
            e.preventDefault();

            let checks = button.closest('table').querySelectorAll('.woot-add-to-cart-check:checked');
            let products = [];
            if (checks.length) {
                checks.forEach(function (ch) {
                    products.push({product_id: ch.getAttribute('data-post-id'), count: ch.closest('td').querySelector('input').value});
                    ch.checked = false;
                });

                button.style.display = 'none';
                button.parentElement.querySelector('.woot-add-to-cart-all-checkbox').checked = false;

                woot_helper.message(woot_helper_vars.lang.updating, 'warning');
                fetch(woot_helper_vars.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: woot_helper.prepare_ajax_form_data({
                        action: 'woot_woocommerce_add_to_cart_some',
                        products: JSON.stringify(products),
                        lang: woot_helper_vars.selected_lang
                    })
                }).then(response => response.json()).then(data => {
                    woot_helper.message(woot_helper_vars.lang.c_updated, 'notice');
                    woot_cart.__refresh(data);
                }).catch((err) => {
                    //woot_helper.message(err, 'error', 5000);
                });

            }

            return false;
        }, false);

        //***

        let checkbox = document.createElement('input');
        checkbox.setAttribute('type', 'checkbox');
        checkbox.className = 'woot-add-to-cart-all-checkbox';

        let label = document.createElement('label');
        label.className = 'woot-checkmark-container';
        label.setAttribute('title', woot_helper_vars.lang.select_all_prod);

        let span = document.createElement('span');
        span.className = 'woot-checkmark';

        label.appendChild(checkbox);
        label.appendChild(span);
        item.appendChild(label);

        let _this = this;
        checkbox.addEventListener('click', function (e) {

            let selectors = this.closest('table').querySelectorAll('.table23_td_add_to_cart .woot-add-to-cart-check');

            if (selectors.length) {
                let display = 'block';

                if (this.checked === true) {
                    selectors.forEach(function (ch) {
                        ch.checked = true;
                    });
                } else {
                    selectors.forEach(function (ch) {
                        ch.checked = false;
                        display = 'none';
                    });
                }

                //+++

                if (this.closest('table').querySelectorAll('.woot-add-to-cart-all-btn').length > 0) {
                    button.style.display = display;
                }
            }

            return true;
        }, false);

    });

    return true;

});

window.addEventListener('load', function () {
    woot_cart = new WOOT_Cart();

    //for shortcode
    if (document.querySelectorAll('.woot-cart-shortcode').length) {
        woot_cart.timer = setInterval(function () {
            if (typeof woot_cart.table_data !== 'undefined') {
                woot_cart.__draw_tables();
                clearInterval(woot_cart.timer);
            }
        }, 200);
    }

    //+++

    jQuery(document.body).on('removed_from_cart', (e) => {
        woot_cart.refresh_info();
    });

});



document.addEventListener('woot-woo-cart-refreshed', (e) => {
    woot_cart.rewrite_buttons_counter();
});

//***


document.addEventListener('table23-after-build-rows', function (e) {

    let skin = e.detail.otable.table.closest('div.data-table-23').getAttribute('data-skin');

    e.detail.otable.table.querySelectorAll('.woot-button-buy-pack').forEach(function (pack) {
        let data = pack.getAttribute('data-about');
        data = data.split(',');

        var type = data[0];
        var id = woot_helper.create_id('tt');

        switch (type) {
            case 'simple':

                var product_id = data[1];
                var quantity = data[2];
                var sku = data[3];

                //this attribute should not be empty
                if (quantity.length === 0) {
                    quantity = 9999;
                }

                let checkbox = document.createElement('input');
                checkbox.className = 'woot-add-to-cart-check';
                checkbox.setAttribute('type', 'checkbox');
                checkbox.setAttribute('data-post-id', product_id);
                checkbox.setAttribute('onclick', 'woot_cart.toggle_add_all_btn(this);');

                let label = document.createElement('label');
                label.className = 'woot-checkmark-container';
                let label_span = document.createElement('span');
                label_span.className = 'woot-checkmark';
                label.appendChild(checkbox);
                label.appendChild(label_span);

                let input = document.createElement('input');
                input.className = 'woot-add-to-cart-input';
                input.value = 1;
                input.setAttribute('min', 1);
                input.setAttribute('step', 1);
                input.setAttribute('max', quantity);
                input.setAttribute('size', 4);
                input.setAttribute('type', 'number');
                input.setAttribute('inputmode', 'numeric');
                input.setAttribute('sku', sku);
                input.setAttribute('id', id);
                input.setAttribute('data-instant-redirect', pack.getAttribute('data-instant-redirect'));
                input.setAttribute('data-product-id', product_id);

                let sold_individually = parseInt(pack.getAttribute('data-sold-individually'), 10);
                if (sold_individually) {
                    input.setAttribute('readonly', true);
                }

                var link = document.createElement('a');
                link.setAttribute('href', `javascript: woot_cart.add(${product_id},\'${id}\');void(0);`);
                link.className = 'woot-btn woot-add-to-cart-btn';
                link.innerHTML = '<span><i class="woot-icon">&#xe806;</i></span>&nbsp;' + woot_helper_vars.lang.add_to_cart;

                var span = document.createElement('span');
                span.className = `woot_count_in_cart woot_count_in_cart_${product_id}`;
                span.style.display = 'none';
                span.setAttribute('data-product-id', product_id);

                link.appendChild(span);
                pack.parentElement.appendChild(input);
                pack.parentElement.appendChild(label);
                pack.parentElement.appendChild(link);

                pack.remove();

                break;

            case 'variable':
                var product_id = data[1];
                var product_title = data[2];
                var vars_count = data[3];

                var link = document.createElement('a');
                link.setAttribute('href', `javascript: woot_helper.call_popup(\"woot_woocommerce_get_variations_table\",{product_id: ${product_id}, skin: \"${skin}\", not_paste:1},\"${id}\", \"${product_title}\"); void(0);`);

                link.setAttribute('title', product_title);
                link.className = 'woot-btn woot-add-to-cart-btn';
                link.innerHTML = `<span></span>&nbsp;${woot_helper_vars.lang.select} [${vars_count}]`;

                pack.parentElement.appendChild(link);
                pack.remove();


                break;


            case 'grouped':
                var product_id = data[1];
                var product_title = data[2];
                var vars_count = data[3];

                var link = document.createElement('a');
                link.setAttribute('href', `javascript: woot_helper.call_popup(\"woot_woocommerce_get_grouped_table\",{product_id: ${product_id}, skin: \"${skin}\", not_paste:1},\"${id}\", \"#${product_title}\"); void(0);`);
                link.setAttribute('title', product_title);
                link.className = 'woot-btn woot-add-to-cart-btn';
                link.innerHTML = `<span></span>&nbsp;${woot_helper_vars.lang.select} [${vars_count}]`;

                pack.parentElement.appendChild(link);
                pack.remove();


                break;


            case 'external':
                var add_to_cart_url = data[1];
                var add_to_cart_text = data[2];

                var link = document.createElement('a');
                link.setAttribute('href', add_to_cart_url);
                link.setAttribute('target', '_blank');
                link.className = 'woot-btn woot-add-to-cart-btn';
                link.innerHTML = add_to_cart_text;

                pack.parentElement.appendChild(link);
                pack.remove();

                break;

        }


    });

    //***   

    if (woot_cart) {
        woot_cart.rewrite_buttons_counter();
    }

    return true;
});


//***

class WOOT_Cart {
    constructor() {

        if (document.querySelectorAll('.woot-data-table').length || document.querySelectorAll('.woot-cart-shortcode').length) {

            this.tables = [];
            this.carts = [];

            //***

            this.quantity = 0;
            this.subtotal = 0;
            this.popup = null;

            this.refresh_info();
            this.rewrite_buttons_counter();

            document.querySelectorAll('.woot-woocommerces-show-cart').forEach(item => {

                let cart = document.createElement('div');
                cart.className = 'woot-woocommerce-cart';
                let a = document.createElement('a');
                a.className = 'woot_cart_info woot-btn';
                a.setAttribute('href', '#');
                a.setAttribute('onclick', 'return woot_cart.call_popup(woot_helper.get_closest_skin(this));void(0);');
                a.innerHTML = woot_helper_vars.lang.in_cart;
                cart.appendChild(a);
                this.carts.push(cart);

                item.querySelector('.woot-woocommerce-cart-zone').innerHTML='';
                item.querySelector('.woot-woocommerce-cart-zone').appendChild(cart);
                //item.appendChild(clearfix);
            });

            //***

            document.addEventListener('woot-added-to-cart', e => {
                e.stopPropagation();
                this.refresh_info();

                //***

                document.querySelectorAll('th[data-key="add_to_cart"]').forEach(item => {
                    item.style.animationName = 'woot-bounce';
                    setTimeout(function () {
                        item.style.animationName = '';
                    }, 1000);
                });

            }, false);

        }


    }

    rewrite_buttons_counter() {
        if (this.table_data) {
            let table_data = this.table_data.table_data;
            document.querySelectorAll('.woot_count_in_cart').forEach((span) => {
                let product_id = parseInt(span.getAttribute('data-product-id'));
                span.innerHTML = '';
                span.style.display = 'none';

                for (let key in table_data) {
                    if (parseInt(table_data[key]['id']) === product_id) {
                        if (table_data[key]['q'] > 0) {
                            span.style.display = 'block';
                            span.innerHTML = table_data[key]['q'];
                        }
                    }
                }

            });
        }
    }

    refresh_info() {
        fetch(woot_helper_vars.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: woot_helper.prepare_ajax_form_data({
                action: 'woot_woocommerce_get_cart_info',
                //skin: 'skin-1',
                lang: woot_helper_vars.selected_lang
            })
        }).then(response => response.json()).then(data => {
            this.__refresh(data);
        }).catch((err) => {
            //woot_helper.message(err, 'error', 5000);
        });
    }

    __refresh(data) {
        this.table_data = JSON.parse(data.table);
        document.dispatchEvent(new CustomEvent('woot-woo-cart-refreshed'));
        this.quantity = parseInt(data.quantity, 10);
        this.subtotal = data.subtotal;
        this.set_cart_info();
    }

    call_popup(skin = '') {
        this.skin=skin;
        this.popup = new Popup23();
        this.popup.set_title(this.set_cart_info());
        let info = `<a href="javascript: woot_cart.clear(this);void(0);" class="woot-btn">${woot_helper_vars.lang.clear_cart}</a>`;

        if (!parseInt(woot_helper_vars.hide_shop_cart_btn)) {
            info += `<a href="${woot_woo_js_vars.cart_link}" target="_blank" class="woot-btn">${woot_helper_vars.lang.view_cart}</a>`;
        }

        if (!parseInt(woot_helper_vars.hide_checkout_btn)) {
            info += `<a href="${woot_woo_js_vars.checkout_link}" target="_blank" class="woot-btn">${woot_helper_vars.lang.checkout}</a>`;
        }

        this.popup.set_title_info(info);
        this.__draw_tables(true);

        return false;
    }

    __generate_table_html(table_html_id) {
        let t = document.createElement('div');
        t.className = 'data-table-23 woot-data-table woot-woocommerce-cart-table';
        t.setAttribute('id', table_html_id);
        t.appendChild(document.createElement('table'));

        return t;
    }

    __draw_tables(popup_only = false) {

        let prev_table = null;

        if (typeof this.tables !== 'undefined') {
            if (typeof this.tables[0] !== 'undefined') {
                prev_table = this.tables[0];
            }
        }

        this.tables = [];

        //***

        if (this.popup) {
            let table_html_id = woot_helper.create_id('cart-');
            let t = this.__generate_table_html(table_html_id);
            this.popup.set_content('');
            this.popup.append_content(t);

            if (prev_table && this.table_data) {
                this.table_data.request_data.orderby = prev_table.request_data.orderby;
                this.table_data.request_data.order = prev_table.request_data.order;
            }

            this.tables.push(new WOOT_GeneratedTables(this.table_data, table_html_id));

            //+++

            //set skin of the parent table
            this.attach_skin(table_html_id);
        }

        //***
        //for shortcode
        if (!popup_only) {
            let _this = this;
            document.querySelectorAll('.woot-cart-shortcode').forEach(function (item) {
                item.innerHTML = '';
                let table_html_id = woot_helper.create_id('cart-');
                let t = _this.__generate_table_html(table_html_id);
                item.appendChild(t);

                if (prev_table) {
                    _this.table_data.request_data.orderby = prev_table.request_data.orderby;
                    _this.table_data.request_data.order = prev_table.request_data.order;
                }

                _this.tables.push(new WOOT_GeneratedTables(_this.table_data, table_html_id));
            });
    }
    }

    set_cart_info() {
        let info = woot_helper_vars.lang.in_cart + ': ' + this.quantity + '; ' + this.subtotal;

        //***

        document.querySelectorAll('a.woot_cart_info').forEach(item => {
            item.innerHTML = info;
        });

        //***

        if (this.popup) {
            this.popup.node.querySelector('h3.woot-modal-title').innerHTML = info;
        }

        if (this.carts.length) {
            this.carts.forEach(cart => {

                if (!cart.querySelector('a.woot-checkout-btn')) {

                    if (!parseInt(woot_helper_vars.hide_shop_cart_btn)) {
                        let view_cart = document.createElement('a');
                        view_cart.setAttribute('href', woot_woo_js_vars.cart_link);
                        view_cart.setAttribute('target', '_blank');
                        view_cart.className = 'woot-btn woot-view-cart-btn';
                        view_cart.innerHTML = woot_helper_vars.lang.view_cart;
                        cart.appendChild(view_cart);
                    }

                    if (!parseInt(woot_helper_vars.hide_checkout_btn)) {
                        let checkout = document.createElement('a');
                        checkout.setAttribute('href', woot_woo_js_vars.checkout_link);
                        checkout.setAttribute('target', '_blank');
                        checkout.className = 'woot-btn woot-checkout-btn';
                        checkout.innerHTML = woot_helper_vars.lang.checkout;
                        cart.appendChild(checkout);
                    }
                }

            });

        }





        //***

        if (this.popup) {
            //this.popup.set_content(info);
            this.__draw_tables(true);
        }

        return info;
    }

    add(product_id, count_input_id) {
        product_id = parseInt(product_id, 10);
        let sku = '';
        let quantity = 1;

        let count_input = document.getElementById(count_input_id);

        if (count_input) {
            sku = count_input.getAttribute('sku');
            quantity = parseInt(count_input.value, 10);

            if (quantity < 1) {
                quantity = 1;
            }
            if (quantity > count_input.getAttribute('max')) {
                quantity = count_input.getAttribute('max');
                woot_helper.message(woot_helper_vars.lang.max_add_to_cart + ' ' + quantity, 'error');
                return;
            }
        }

        //wp-content\plugins\woocommerce\assets\js\frontend\add-to-cart.js #81
        let ajax_url = '?wc-ajax=add_to_cart';
        if (typeof wc_add_to_cart_params !== 'undefined') {
            ajax_url = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
        }

        woot_helper.message(woot_helper_vars.lang.c_adding, 'warning', -1);
        fetch(ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: woot_helper.prepare_ajax_form_data({
                product_id: product_id,
                product_sku: sku,
                quantity: quantity
            })
        }).then(response => response.text()).then(data => {
            data = JSON.parse(data);
            if (typeof data.error === 'undefined') {
                woot_helper.message(woot_helper_vars.lang.c_added, 'notice');
                document.dispatchEvent(new CustomEvent('woot-added-to-cart', {detail: {product_id: product_id}}));

                if (parseInt(count_input.getAttribute('data-instant-redirect')) === 1) {
                    document.location.href = woot_woo_js_vars.cart_link;
                }

                if (parseInt(count_input.getAttribute('data-instant-redirect')) === 2) {
                    document.location.href = woot_woo_js_vars.checkout_link;
                }


                //fix for woocommerce native cart
                //document.body.dispatchEvent(new CustomEvent('woot-added-to-cart'));
                jQuery(document.body).trigger('woot-added-to-cart', [null, null, null]);
            } else {
                woot_helper.message(woot_helper_vars.lang.cant_add_to_cart, 'error', 7000);
            }
        }).catch((err) => {
            woot_helper.message(err, 'error', 5000);
        });

    }

    remove(product_id) {
        if (confirm(woot_helper_vars.lang.sure)) {
            this.delete(product_id);
        }
    }

    delete(product_id) {
        woot_helper.message(woot_helper_vars.lang.deleting + ' ...', 'warning');
        fetch(woot_helper_vars.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: woot_helper.prepare_ajax_form_data({
                action: 'woot_remove_product_from_cart',
                product_id: product_id
            })
        }).then(response => response.text()).then(data => {
            woot_helper.message(woot_helper_vars.lang.deleted, 'notice');

            data = JSON.parse(data);
            this.table_data = JSON.parse(data.table);
            document.dispatchEvent(new CustomEvent('woot-woo-cart-refreshed'));
            this.quantity = parseInt(data.quantity, 10);
            this.subtotal = data.subtotal;
            this.set_cart_info();
            this.__draw_tables();

        }).catch((err) => {
            //woot_helper.message(err, 'error', 5000);
        });
    }

    update(product_id, link_self) {
        woot_helper.message(woot_helper_vars.lang.updating + ' ...', 'warning');
        fetch(woot_helper_vars.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: woot_helper.prepare_ajax_form_data({
                action: 'woot_update_product_count_in_cart',
                product_id: product_id,
                quantity: link_self.parentElement.querySelector('input[type="number"]').value
            })
        }).then(response => response.json()).then(data => {
            woot_helper.message(woot_helper_vars.lang.c_updated, 'notice');

            let td = JSON.parse(data.table);
            this.table_data = td;
            document.dispatchEvent(new CustomEvent('woot-woo-cart-refreshed'));
            this.quantity = parseInt(data.quantity, 10);
            this.subtotal = data.subtotal;
            this.set_cart_info();
            this.__draw_tables();
            document.querySelector(`tr[data-pid="${product_id}"]`).scrollIntoView(true);
        }).catch((err) => {
            //woot_helper.message(err, 'error', 5000);
        });

    }

    count_update(event, product_id, self) {
        if (event.keyCode === 13) {
            this.update(product_id, self.nextElementSibling);
        }
    }

    clear() {
        if (confirm(woot_helper_vars.lang.sure)) {
            if (this.tables.length > 0) {
                for (let i = 0; i < this.tables.length; i++) {
                    if (document.querySelector('.woot-data-table#' + this.tables[i].table_html_id)) {
                        let ids = [];
                        this.tables[i].table.querySelectorAll('tbody tr').forEach(function (tr) {
                            ids.push(parseInt(tr.getAttribute('data-pid'), 10));
                        });

                        //***

                        fetch(woot_helper_vars.ajax_url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            body: woot_helper.prepare_ajax_form_data({
                                action: 'woot_remove_all_products_from_cart',
                                products_ids: ids
                            })
                        }).then(response => response.json()).then(data => {

                            let td = JSON.parse(data.table);
                            this.table_data = td;
                            document.dispatchEvent(new CustomEvent('woot-woo-cart-refreshed'));
                            this.tables[i].table_data = td.table_data;
                            this.quantity = parseInt(data.quantity, 10);
                            this.subtotal = data.subtotal;
                            this.set_cart_info();
                            this.__draw_tables();

                        }).catch((err) => {
                            //woot_helper.message(err, 'error', 5000);
                        });

                    }
                }
            }
        }
    }

    toggle_add_all_btn(ch) {
        let button = ch.closest('table').querySelector('.woot-add-to-cart-all-btn');

        if (ch.closest('table').querySelectorAll('.woot-add-to-cart-check:checked').length > 0) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    }

    attach_skin(table_html_id) {
        if (this.skin.length > 0) {
            if (document.querySelector(`style[data-table23-skin="${this.skin}"]`)) {

                let table_styles = document.querySelector(`style[data-table23-skin="${this.skin}"]`).innerText;
                let parent_table_html_id = document.querySelector(`style[data-table23-skin="${this.skin}"]`).getAttribute('data-table23-skin-of');

                let styles = document.createElement('style');
                styles.innerText = table_styles.split(parent_table_html_id).join(table_html_id);
                document.querySelector('#' + table_html_id).appendChild(styles);
            }
        }
    }
}

