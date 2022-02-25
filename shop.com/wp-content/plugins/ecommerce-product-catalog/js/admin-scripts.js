/*!
 impleCode Admin
 Handles admin settings
 (c) 2021 impleCode - https://implecode.com
 */


jQuery(document).ready(function () {
    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            jQuery(this).width(jQuery(this).width());
        });
        return ui;
    };

    var add_product_button = jQuery("body.post-type-al_product").find('.page-title-action:visible:first');
    if (ic_catalog.import_screen_url && add_product_button.length !== 0) {
        add_product_button.after('<a id="import-export-link-page" href="' + ic_catalog.import_screen_url + '" class="page-title-action">' + ic_catalog.import_export + '</a>');
    }


    jQuery(".ic_chosen").chosen({width: '200px', search_contains: true, allow_single_deselect: true});

    jQuery('.sort-settings tbody').sortable({
        items: "tr:not(.ic-not-sortable)",
        update: function () {
            jQuery('.sort-settings tbody tr').each(function () {
                var r = jQuery(this).index();
                var label_input = jQuery(this).find('td .attribute-label');
                var value_input = jQuery(this).find('td .attribute-value');
                var unit_input = jQuery(this).find('td .attribute-unit');
                var label_base_name = label_input.data('base_name');
                var value_base_name = value_input.data('base_name');
                var unit_base_name = unit_input.data('base_name');
                label_input.attr('name', label_base_name + r);
                value_input.attr('name', value_base_name + r);
                unit_input.attr('name', unit_base_name + r);
                r = r + 1;
                jQuery(this).find('td .shipping-label').attr('name', '_shipping-label' + r);
                jQuery(this).find('td .shipping-value').attr('name', '_shipping' + r);
            });
        },
        helper: fixHelper,
        placeholder: 'sort-settings-placeholder'
    });
//jQuery('.attributes .ui-sortable').height(jQuery('.attributes .ui-sortable').height());
//jQuery('.shipping .ui-sortable').height(jQuery('.shipping .ui-sortable').height());
    var fields_hide_simple = new Array('input[name="archive_multiple_settings\[category_archive_url\]"]', 'input[name="archive_multiple_settings\[seo_title\]"]', 'input[name="archive_multiple_settings\[seo_title_sep\]"]', 'input[name="archive_multiple_settings\[breadcrumbs_title\]"]', 'input[name="archive_multiple_settings\[enable_product_breadcrumbs\]"]', 'input[name="archive_multiple_settings\[product_listing_cats\]"]', 'input[name="archive_multiple_settings\[category_top_cats\]"]', 'input[name="archive_multiple_settings\[cat_template\]"]');
    var fields_hide_theme = new Array('input[name="archive_multiple_settings\[product_listing_cats\]"]', 'input[name="archive_multiple_settings\[category_top_cats\]"]', 'input[name="archive_multiple_settings\[cat_template\]"]', 'input[name="archive_multiple_settings\[cat_image_disabled\]"]');
    jQuery('.integration-mode-selection').change(function () {
        var disable = false;
        var is_checked = jQuery(this).is(':checked');
        var val = jQuery(this).val();
        if (is_checked && val === 'simple') {
            disable = 'simple';
        } else if (is_checked && val === 'theme') {
            disable = 'theme';
        }
        if (is_checked) {
            if (!disable) {
                jQuery(".simple_mode_settings").hide('slow');
                jQuery(".theme_mode_settings").hide('slow');
                jQuery(".advanced_mode_settings").show();
                jQuery(".advanced_mode_settings_inline").show();
                jQuery.each(fields_hide_simple, function (index, element) {
                    //jQuery( element ).prop( "disabled", disable );
                    jQuery(element).closest("tr").show();
                });
            } else if (disable === 'simple') {
                jQuery(".advanced_mode_settings").hide();
                //jQuery( ".advanced_mode_settings_inline" ).hide();
                jQuery(".theme_mode_settings").hide('slow');
                jQuery(".simple_mode_settings").show('slow');
                jQuery.each(fields_hide_simple, function (index, element) {
                    //jQuery( element ).prop( "disabled", disable );
                    //jQuery( element ).closest( "tr" ).hide();
                });
            } else if (disable === 'theme') {
                jQuery(".advanced_mode_settings").hide();
                jQuery(".simple_mode_settings").hide('slow');
                jQuery(".theme_mode_settings").show('slow');
                jQuery(".advanced_mode_settings_inline").show();
                jQuery.each(fields_hide_simple, function (index, element) {
                    //jQuery( element ).prop( "disabled", disable );
                    jQuery(element).closest("tr").show();
                });
                jQuery.each(fields_hide_theme, function (index, element) {
                    //jQuery( element ).prop( "disabled", disable );
                    jQuery(element).closest("tr").hide();
                });
            }
        }
    });
    jQuery('.integration-mode-selection').trigger("change");
    /*
     jQuery( ".overall-product-settings .submit .button-primary" ).click( function () {
     jQuery.each( fields, function ( index, element ) {
     jQuery( element ).prop( "disabled", false );
     } );
     } );
     */

    jQuery(".implecode-review .dashicons-no").click(function () {
        var data = {
            'action': 'hide_review_notice'
        };
        jQuery.post(ajaxurl, data, function (response) {
            jQuery(".implecode-review").hide("slow");
        });
    });

    jQuery(".implecode-translate .dashicons-no").click(function () {
        var data = {
            'action': 'hide_translate_notice'
        };
        jQuery.post(ajaxurl, data, function (response) {
            jQuery(".implecode-translate").hide("slow");
        });
    });

    jQuery(".implecode-review-thanks .dashicons-yes").click(function () {
        jQuery(".implecode-review-thanks").hide("slow");
    });

    jQuery(".implecode-review a").click(function (e) {
        if (jQuery(this).hasClass("button")) {
            e.preventDefault();
            var data = {
                'action': 'hide_review_notice',
                'forever': 'yes'
            };
            jQuery.post(ajaxurl, data, function (response) {
                jQuery(".implecode-review").hide("slow");
            });
        } else {
            var data = {
                'action': 'hide_review_notice',
                'forever': 'yes'
            };
            jQuery.post(ajaxurl, data, function (response) {
                jQuery(".implecode-review").hide("slow");
                jQuery(".implecode-review-thanks").show("slow");
            });
        }
    });

    jQuery('.implecode-review.is-dismissible').on('click', '.notice-dismiss', function (event) {
        var data = {
            'action': 'hide_review_notice'
        };
        jQuery.post(ajaxurl, data);
    });

    jQuery('.ic-notice.is-dismissible').on('click', '.notice-dismiss', function (event) {
        var element = jQuery(this).closest(".is-dismissible").data("ic_dismissible");
        if (element !== undefined) {
            var data = {
                'action': 'hide_ic_notice',
                'element': element
            };
            jQuery.post(ajaxurl, data);
        }
    });

    jQuery('.al-box').on('click', '.notice-dismiss', function () {
        var container = jQuery(this).closest(".al-box");
        container.css("opacity", "0.5");
        var hash = container.data('hash');
        if (hash !== undefined) {
            var data = {
                'action': 'ic_ajax_hide_message',
                'hash': hash
            };
            jQuery.post(ajaxurl, data, function () {
                container.hide("slow");
            });
        }
    });

    jQuery(function () {
        jQuery(".setting-content input, span.ic_tip").tooltip({
            position: {
                my: "left-48 top+37",
                at: "right+48 bottom-37",
                collision: "flip",
            },
            track: true,
            tooltipClass: "ui-ic-tooltip"
        });
    });

    jQuery("body").on("click", ".add_catalog_media", function () {
        var clicked = jQuery(this);
        var clicked_container = clicked.parent("div");
        var clicked_prev = clicked.prev("div");
        var upload_type = clicked_container.children("#upload_type").val();
        wp.media.editor.send.attachment = function (props, attachment) {
            if (upload_type == "url") {
                clicked_container.children("#uploaded_image").val(attachment.url);
            } else {
                clicked_container.children("#uploaded_image").val(attachment.id);
            }
            clicked_prev.children("img").attr("src", attachment.url);
            clicked_prev.children("img").show();
            clicked_prev.children(".catalog-reset-image-button").show();
            clicked.prev("div.implecode-admin-media-image.empty").removeClass('empty');
            clicked.hide();
            clicked.closest(".custom-uploader").addClass("active-image");
        }
        wp.media.editor.open(clicked);
        return false;
    });

    jQuery(".catalog-reset-image-button").click(function () {
        var clicked = jQuery(this);
        clicked.parent("div").prev(".uploaded_image").val("");
        src = jQuery("#default").val();
        clicked.next(".media-image").attr("src", src);
        if (src != '') {
            clicked.next(".media-image").show();
        } else {
            clicked.next(".media-image").hide();
        }
        clicked.parent("div").next(".add_catalog_media").show();
        clicked.parent(".implecode-admin-media-image").addClass('empty');
        clicked.hide();
        clicked.closest(".custom-uploader").removeClass("active-image");
    });
    jQuery("form#post").submit(function (e) {
        if (jQuery('input[name="_price"]').length) {
            if (!jQuery('input[name="_price"]').valid()) {
                e.preventDefault();
                jQuery('html, body').animate({
                    scrollTop: jQuery("#_price-error").offset().top - 200
                }, 100);
            }
        }
    });

    jQuery(".ic_autocomplete").each(function () {
        var closing = false;
        var autocomplete = jQuery(this).data('ic-autocomplete');
        if (autocomplete !== undefined) {
            jQuery(this).autocomplete({
                source: autocomplete,
                minLength: 0,
                close: function () {
                    // avoid double-pop-up issue
                    closing = true;
                    setTimeout(function () {
                        closing = false;
                    }, 300);
                }
            }).focus(function () {
                var value = jQuery(this).val();
                if (!closing && value == '') {
                    jQuery(this).autocomplete("search");
                }
            });

        }
    });


});

jQuery(document).ready(function ($) {
    $.ic = {
        /**
         * Implement a WordPress-link Hook System for Javascript
         * TODO: Change 'tag' to 'args', allow number (priority), string (tag), object (priority+tag)
         */
        hooks: {action: {}, filter: {}},
        addAction: function (action, callable, tag) {
            jQuery.ic.addHook('action', action, callable, tag);
        },
        addFilter: function (action, callable, tag) {
            jQuery.ic.addHook('filter', action, callable, tag);
        },
        doAction: function (action, args) {
            jQuery.ic.doHook('action', action, null, args);
        },
        applyFilters: function (action, value, args) {
            return jQuery.ic.doHook('filter', action, value, args);
        },
        removeAction: function (action, tag) {
            jQuery.ic.removeHook('action', action, tag);
        },
        removeFilter: function (action, tag) {
            jQuery.ic.removeHook('filter', action, tag);
        },
        addHook: function (hookType, action, callable, tag) {
            if (undefined == jQuery.ic.hooks[hookType][action]) {
                jQuery.ic.hooks[hookType][action] = [];
            }
            var hooks = jQuery.ic.hooks[hookType][action];
            if (undefined == tag) {
                tag = action + '_' + hooks.length;
            }
            jQuery.ic.hooks[hookType][action].push({tag: tag, callable: callable});
        },
        doHook: function (hookType, action, value, args) {
            if (undefined != jQuery.ic.hooks[hookType][action]) {
                var hooks = jQuery.ic.hooks[hookType][action];
                for (var i = 0; i < hooks.length; i++) {
                    if ('action' == hookType) {
                        hooks[i].callable(args);
                    } else {
                        value = hooks[i].callable(value, args);
                    }
                }
            }
            if ('filter' == hookType) {
                return value;
            }
        },
        removeHook: function (hookType, action, tag) {
            if (undefined != jQuery.ic.hooks[hookType][action]) {
                var hooks = jQuery.ic.hooks[hookType][action];
                for (var i = hooks.length - 1; i >= 0; i--) {
                    if (undefined == tag || tag == hooks[i].tag)
                        hooks.splice(i, 1);
                }
            }
        }
    }
});
