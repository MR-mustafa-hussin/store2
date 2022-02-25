jQuery(document).ready(function ($) {
    "use strict";
    $('.woo_sc_short_code').on('click', function () {
        $(this).select();
        document.execCommand('copy');
    });
    $('.woo_sc_short_code').on('click', function () {
        let _this = $(this).parent().find('.woo_sc_copied');
        _this.css('visibility', 'visible');
        setTimeout(function () {
            _this.css('visibility', 'hidden');
        }, 1000);
    });
});