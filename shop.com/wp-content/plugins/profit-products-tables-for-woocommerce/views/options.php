<?php
/**
 * Page of the plugin options
 * 
 * @see https://products-tables.com/woot-documentation/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div class="woot-admin-preloader">
    <div class="cssload-loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>

<svg class="hidden">
<defs>
<path id="tabshape" d="M80,60C34,53.5,64.417,0,0,0v60H80z"/>
</defs>
</svg>

<?php woot()->rate_alert->show_alert() ?>

<div class="wrap nosubsub woot-options-wrapper">
    <br />
    <div style="display: flex; justify-content: space-between;">

        <div>

            <h2 class="woot-plugin-name"><?php printf(esc_html__('Active Products Tables for WooCommerce v.%s', 'profit-products-tables-for-woocommerce'), WOOT_VERSION) ?></h2>
            <i><?php printf(esc_html__('Actualized for WooCommerce v.%s', 'profit-products-tables-for-woocommerce'), WOOCOMMERCE_VERSION) ?></i><br />
            <br />


        </div>

        <div>&nbsp;</div>

    </div>



    <div class="woot-tabs woot-tabs-style-shape">

        <nav>
            <ul>

                <li class="tab-current">
                    <a href="#tabs-main-tables">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Tables', 'profit-products-tables-for-woocommerce') ?></span>
                    </a>
                </li>


                <li>
                    <a href="#tabs-main-settings">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Settings', 'profit-products-tables-for-woocommerce') ?></span>
                    </a>
                </li>

                <?php if (WOOT_Vocabulary::is_enabled()): ?>
                    <li>
                        <a href="#tabs-main-vocabulary">
                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                            <span><?php esc_html_e('Vocabulary', 'profit-products-tables-for-woocommerce') ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="#tabs-main-help">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Help', 'profit-products-tables-for-woocommerce') ?></span>
                    </a>
                </li>

            </ul>
        </nav>

        <div class="content-wrap">
            <section id="tabs-main-tables" class="content-current">

                <div style="float: left;">
                    <?php
                    echo WOOT_HELPER::draw_html_item('a', [
                        'href' => 'javascript: woot_main_table.create();void(0);',
                        'class' => 'button woot-dash-btn'
                            ], '<span class="dashicons-before dashicons-plus"></span>&nbsp;' . esc_html__('Create table', 'profit-products-tables-for-woocommerce'));
                    ?>
                </div>
                <?php if (woot()->notes_for_free): ?>
                    <div style="float: right;">
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/upgrading-to-premium/',
                            'target' => '_blank',
                            'class' => 'button woot-dash-btn',
                            'style' => 'border-color: tomato; font-size: 14px !important; line-height: 33px;',
                                ], '<span class="dashicons-before dashicons-arrow-up-alt"></span>&nbsp;' . esc_html__('Upgrade to Premium', 'profit-products-tables-for-woocommerce'));
                        ?>
                    </div>
                <?php endif; ?>

                <div class="clearfix"></div>

                <br />

                <?php
                echo wp_kses($main_table, [
                    'div' => [
                        'class' => true,
                        'id' => true,
                        'data-skin' => true,
                        'data-table-id' => true,
                        'style' => true
                    ],
                    'input' => [
                        'type' => true,
                        'data-key' => true,
                        'value' => true,
                        'minlength' => true,
                        'class' => true,
                        'placeholder' => true
                    ],
                    'table' => [
                        'class' => true,
                        'id' => true
                    ],
                    'thead' => [],
                    'tfoot' => [],
                    'tbody' => [
                        'style' => true,
                    ],
                    'th' => [
                        'data-key' => true,
                        'style' => true,
                        'class' => true
                    ],
                    'tr' => [
                        'data-pid' => true
                    ],
                    'td' => [
                        'class' => true,
                        'data-field-type' => true,
                        'data-pid' => true,
                        'data-key' => true,
                        'data-field' => true
                    ],
                ]);
                ?>

            </section>

            <section id="tabs-main-settings">
                <?php if (woot()->notes_for_free): ?>
                    <p class="notice notice-error">
                        <?php printf(esc_html__('In free version is possible to display only first %s columns!', 'profit-products-tables-for-woocommerce'), 7) ?>
                    </p>
                <?php endif; ?>


                <?php
                echo wp_kses($settings_table, [
                    'div' => [
                        'class' => true,
                        'id' => true,
                        'data-skin' => true,
                        'data-table-id' => true,
                        'style' => true
                    ],
                    'input' => [
                        'type' => true,
                        'data-key' => true,
                        'value' => true,
                        'minlength' => true,
                        'class' => true,
                        'placeholder' => true
                    ],
                    'table' => [
                        'class' => true,
                        'id' => true
                    ],
                    'thead' => [],
                    'tfoot' => [],
                    'tbody' => [
                        'style' => true,
                    ],
                    'th' => [
                        'data-key' => true,
                        'style' => true,
                        'class' => true
                    ],
                    'tr' => [
                        'data-pid' => true
                    ],
                    'td' => [
                        'class' => true,
                        'data-field-type' => true,
                        'data-pid' => true,
                        'data-key' => true,
                        'data-field' => true
                    ],
                ]);
                ?>

                <hr />

                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Info data: All possible columns keys for [woot] shortcode attributes', 'profit-products-tables-for-woocommerce') ?>", what: "possible_columns_keys"}); void(0);' class="woot-btn"><?php echo esc_html__('All possible columns keys', 'profit-products-tables-for-woocommerce') ?></a>&nbsp;
                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Export WOOT Data', 'profit-products-tables-for-woocommerce') ?>", what: "export"}); void(0);' class="woot-btn"><?php echo esc_html__('Export WOOT Data', 'profit-products-tables-for-woocommerce') ?></a>&nbsp;
                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Import WOOT Data', 'profit-products-tables-for-woocommerce') ?>", what: "import"}); void(0);' class="woot-btn"><?php echo esc_html__('Import WOOT Data', 'profit-products-tables-for-woocommerce') ?></a>

            </section>

            <?php if (WOOT_Vocabulary::is_enabled()): ?>
                <section id="tabs-main-vocabulary">

                    <?php if (woot()->notes_for_free): ?>
                        <p class="notice notice-error">
                            <?php printf(esc_html__('In free version is possible to display only first %s columns!', 'profit-products-tables-for-woocommerce'), 7) ?>
                        </p>
                    <?php endif; ?>

                    <div class="woot-notice">
                        <?php
                        printf(esc_html__('This vocabulary is not for interface words, which you can translate for example by %s, but for the arbitrary words which you applied in the tables columns. Taxonomies terms also possible to translate here, to display them in the WOOT tables.', 'profit-products-tables-for-woocommerce'), WOOT_HELPER::draw_html_item('a', [
                                    'href' => 'https://wordpress.org/plugins/loco-translate/',
                                    'target' => '_blank'
                                        ], 'Loco Translate'))
                        ?>
                    </div>
                    <?php
                    echo WOOT_HELPER::draw_html_item('a', [
                        'href' => 'javascript: woot_vocabulary_table.create();void(0);',
                        'class' => 'button woot-dash-btn'
                            ], '<span class="dashicons-before dashicons-plus"></span>&nbsp;' . esc_html__('Create', 'profit-products-tables-for-woocommerce'));
                    ?>

                    <br /><br />
                    <?php woot()->vocabulary->draw_table(); ?>
                    <div class="clearfix"></div>
                </section>
            <?php endif; ?>


            <section id="tabs-main-help">

                <?php if (time() < 1638403199): ?>
                    <div style="text-align: center;">
                        <a href="https://1.envato.market/DmRod" target="_blank"><img src="https://pluginus.net/wp-content/uploads/2021/11/cybermonday-2021-1.jpg" alt="Cyber Monday 2021"></a><br>

                        <img src="https://pluginus.net/wp-content/uploads/2021/11/cybermonday-2021-timer2.gif" alt="Cyber Monday 2021 timer"><br>
                    </div>
                <?php endif; ?>


                <ul>

                    <li>

                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/document/after-woot-installation-you-can-do-next/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('You can do next ...', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;

                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/shortcode/woot/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], '[woot]')
                        ?>&nbsp;
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/woot-documentation/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Documentation', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/category/faq/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('FAQ', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/codex/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Codex', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                        <!-- <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/video/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Video', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp; -->
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/document/skins/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Make skins', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://demo.products-tables.com/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Demo', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://pluginus.net/support/forum/woot-woocommerce-active-products-tables/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Support', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;
                    </li>

                    <li>
                        <hr />
                        <h3><?php echo esc_html__('Extensions for WOOT', 'profit-products-tables-for-woocommerce') ?>:</h3>
                    </li>

                    <li>
                        <?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/extension/favourites/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Favourites', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;<?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/extension/compare/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Compare', 'profit-products-tables-for-woocommerce'))
                        ?>&nbsp;<?php
                        echo WOOT_HELPER::draw_html_item('a', [
                            'href' => 'https://products-tables.com/extension/attachments/',
                            'target' => '_blank',
                            'class' => 'woot-btn'
                                ], esc_html__('Attachments', 'profit-products-tables-for-woocommerce'))
                        ?>
                    </li>


                </ul>


                <hr />

                <?php echo esc_html__('Tables for WordPress posts', 'woot-products-tables') ?>:<a href="https://posts-table.com/" target="_blank" class="woot-btn"><?php echo esc_html__('TABLEON - WordPress Posts Table Filterable', 'woot-products-tables') ?></a><br />

                <hr />

                <iframe width="560" height="315" src="https://www.youtube.com/embed/4f1wyApG68Y" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                <hr />

                <h3><?php echo esc_html__('Power your store', 'profit-products-tables-for-woocommerce') ?>:</h3>

                <div>
                    <a href="https://products-filter.com/" title="WOOF - WooCommerce Products Filter" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woof-banner.png" width="250" alt="WOOF - WooCommerce Products Filter"></a>
                    &nbsp;<a href="https://currency-switcher.com/" title="WOOCS - WooCommerce Currency Switcher" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woocs-banner.png" width="250" alt="WOOCS - WooCommerce Currency Switcher"></a>
                    &nbsp;<a href="https://bulk-editor.com/" title="WOOBE - WooCommerce Bulk Editor and Products Manager Professional" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woobe-banner.png" width="250" alt="WOOBE - WooCommerce Bulk Editor and Products Manager Professional"></a>
                    &nbsp;<a href="https://bulk-editor.pro/" title="WPBE - WordPress Posts Bulk Editor Professional" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/wpbe-banner.png" width="250" alt="WPBE - WordPress Posts Bulk Editor Professional"></a>
                    &nbsp;<a href="https://posts-table.com/" title="TABLEON - WordPress Post Tables Filterable" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/tableon-banner.png" width="250" alt="TABLEON - WordPress Post Tables Filterable"></a>
                </div>



            </section>


        </div>


    </div>


    <?php if (woot()->notes_for_free): ?>
        <br />
        <div>
            <a href="https://codecanyon.pluginus.net/item/woot-woocommerce-products-tables/27928580" title="PREMIUM version: WOOT - WooCommerce Active Products Tables" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woot-banner.png" width="250" alt="WOOT - WooCommerce Active Products Tables"></a>
            &nbsp;<a href="https://products-filter.com/" title="WOOF - WooCommerce Products Filter" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woof-banner.png" width="250" alt="WOOF - WooCommerce Products Filter"></a>
            &nbsp;<a href="https://currency-switcher.com/" title="WOOCS - WooCommerce Currency Switcher" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woocs-banner.png" width="250" alt="WOOCS - WooCommerce Currency Switcher"></a>
            &nbsp;<a href="https://bulk-editor.com/" title="WOOBE - WooCommerce Bulk Editor and Products Manager Professional" target="_blank"><img src="<?php echo WOOT_ASSETS_LINK ?>img/banners/woobe-banner.png" width="250" alt="WOOBE - WooCommerce Bulk Editor and Products Manager Professional"></a>
        </div>
    <?php endif; ?>



    <div id="woot-popup-columns-template" style="display: none;">

        <div class="woot-modal">
            <div class="woot-modal-inner">
                <div class="woot-modal-inner-header">
                    <h3 class="woot-modal-title">&nbsp;</h3>

                    <div class="woot-modal-title-info"><a href="https://products-tables.com/document/columns/" id="main-table-help-link" class="woot-btn" target="_blank"><?php echo esc_html__('Help', 'profit-products-tables-for-woocommerce') ?></a></div>

                    <a href="javascript: woot_columns_table.close_popup(); void(0)" class="woot-modal-close"></a>


                </div>
                <div class="woot-modal-inner-content">
                    <div class="woot-form-element-container">

                        <div class="woot-tabs woot-tabs-style-shape">

                            <nav>
                                <ul>

                                    <li class="tab-current">
                                        <a href="#tabs-columns">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Columns', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-meta">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Meta', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>



                                    <li>
                                        <a href="#tabs-filter">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Filter', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-predefinition">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Predefinition', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-custom-css">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Custom CSS', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-options">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Options', 'profit-products-tables-for-woocommerce') ?></span>
                                        </a>
                                    </li>


                                </ul>
                            </nav>

                            <div class="content-wrap">
                                <section id="tabs-columns" class="content-current">

                                    <div>
                                        <?php if (woot()->notes_for_free): ?>
                                            <p class="notice notice-error">
                                                <?php printf(esc_html__('In free version is possible to display only first %s columns!', 'profit-products-tables-for-woocommerce'), 7) ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php
                                        echo WOOT_HELPER::draw_html_item('a', [
                                            'href' => 'javascript: woot_columns_table.create();void(0);',
                                            'class' => 'button woot-dash-btn'
                                                ], '<span class="dashicons-before dashicons-welcome-add-page"></span>' . esc_html__('Prepend column', 'profit-products-tables-for-woocommerce'));
                                        ?>
                                    </div>
                                    <br />

                                    <div class="woot-columns-table-zone"></div>

                                    <br />

                                    <div>
                                        <?php
                                        echo WOOT_HELPER::draw_html_item('a', [
                                            'href' => 'javascript: woot_columns_table.create(false);void(0);',
                                            'class' => 'button woot-dash-btn woot-dash-btn-rotate'
                                                ], '<span class="dashicons-before dashicons-welcome-add-page"></span>' . esc_html__('Append column', 'profit-products-tables-for-woocommerce'));
                                        ?>
                                    </div>

                                </section>

                                <section id="tabs-custom-css">

                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 1px; padding-left: 4px;">
                                                <a href="javascript: woot_main_table.save_custom_css(); void(0)" class="woot-btn woot-btn-1"><i class="woot-icon">&#xe801;</i></a>
                                            </td>

                                            <td>
                                                <div class="woot-notice"><?php
                                                    printf(esc_html__('You can use custom CSS for small changes, but for quite big the table restyling its recommended to use %s. Use hotkey combination CTRL+S for CSS code saving!', 'profit-products-tables-for-woocommerce'), WOOT_HELPER::draw_html_item('a', [
                                                                'href' => 'https://products-tables.com/document/skins/',
                                                                'target' => '_blank'
                                                                    ], esc_html__('table skins', 'profit-products-tables-for-woocommerce')))
                                                    ?></div>
                                            </td>

                                        </tr>
                                    </table>

                                    <div class="woot-options-custom-css-zone"></div>                                    

                                </section>

                                <section id="tabs-options">
                                    <div class="woot-table-options-zone"></div>
                                </section>


                                <section id="tabs-meta">

                                    <div class="woot-notice">
                                        <?php
                                        printf(esc_html__('If to use sorting by meta keys - will be visible only products which has any value for the selected meta key. %s', 'profit-products-tables-for-woocommerce'), WOOT_HELPER::draw_html_item('a', [
                                                    'href' => 'https://products-tables.com/document/meta/',
                                                    'target' => '_blank'
                                                        ], esc_html__('Also read', 'profit-products-tables-for-woocommerce')))
                                        ?>
                                    </div>

                                    <?php
                                    echo WOOT_HELPER::draw_html_item('a', [
                                        'href' => 'javascript: woot_meta_table.create();void(0);',
                                        'class' => 'button woot-dash-btn'
                                            ], '<span class="dashicons-before dashicons-plus"></span>' . esc_html__('Add meta field', 'profit-products-tables-for-woocommerce'));
                                    ?>
                                    <br /><br />

                                    <div class="woot-meta-table-zone"></div>
                                </section>

                                <section id="tabs-filter">
                                    <p class="notice notice-success"><?php
                                        printf(esc_html__('Also for products filtration you can use %s filter!', 'profit-products-tables-for-woocommerce'), WOOT_HELPER::draw_html_item('a', [
                                                    'href' => 'https://demo.products-filter.com/demonstration-of-woot-and-woof-compatibility/',
                                                    'target' => '_blank'
                                                        ], 'WOOF'))
                                        ?></p>

                                    <?php if (woot()->notes_for_free): ?>
                                        <p class="notice notice-error">
                                            <?php printf(esc_html__('In free version is possible to display only first %s items!', 'profit-products-tables-for-woocommerce'), 2) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="tabs-filter-container"></div>
                                </section>

                                <section id="tabs-predefinition">
                                    <div class="woot-notice"><?php
                                        printf(esc_html__('Here you can set rules about what products to display in the table. The filtration will work with the predefined products as with basic ones. %s.', 'profit-products-tables-for-woocommerce'), WOOT_HELPER::draw_html_item('a', [
                                                    'href' => 'https://products-tables.com/document/predefinition/',
                                                    'target' => '_blank'
                                                        ], esc_html__('Read more here', 'profit-products-tables-for-woocommerce')))
                                        ?></div>
                                    <div class="woot-predefinition-table-zone"></div>
                                </section>
                            </div>

                        </div>


                    </div>
                </div>
                <div class="woot-modal-inner-footer">
                    <a href="javascript: woot_columns_table.close_popup(); void(0)" class="button button-primary woot-modal-button-large-1"><?php esc_html_e('Close', 'profit-products-tables-for-woocommerce') ?></a>
                    <!-- <a href="javascript:void(0)" class="woot-modal-save button button-primary button-large-2"><?php esc_html_e('Apply', 'profit-products-tables-for-woocommerce') ?></a>-->
                </div>
            </div>
        </div>

        <div class="woot-modal-backdrop"></div>

    </div>

    <?php echo WOOT_HELPER::render_html('views/popup.php'); ?>


</div>

