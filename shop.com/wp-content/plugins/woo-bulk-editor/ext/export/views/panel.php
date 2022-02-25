
<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div class="notice notice-warning">
    <p>
        <?php printf(esc_html__('Export will be applied to: %s', 'woo-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woo-bulk-editor') . '</span>') ?>
    </p>
</div>

<div class="notice notice-info">
    <p>
        <?php esc_html_e('Note: you can change columns set and then set their order in the tab Settings, then save it as columns profile which in future will help you with exporting of the products data format quickly without necessary each time set columns order and their set!', 'woo-bulk-editor') ?>    
    </p>
</div>
<br />
<div class="col-lg-12">
    <div style="display: none;" id="woobe_show_variations_mode_export">
        <?php $combination_attributes = wc_get_attribute_taxonomies(); ?>

        <?php if (!empty($combination_attributes)): ?>
            <hr />

            <select id="woobe_bulk_combination_attributes_export" multiple="" class="chosen-select" style="width: 350px;" data-placeholder="<?php esc_html_e('select combination of attributes', 'woo-bulk-editor') ?>">
                <?php foreach ($combination_attributes as $a) : ?>
                    <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                <?php endforeach; ?>
            </select>
            <select id="woobe_bulk_combination_attributes_export_behavior"  class="chosen-select" style="width: 100px;" data-placeholder="<?php esc_html_e('select behavior', 'woo-bulk-editor') ?>">
                <option value="1" ><?php esc_html_e('In', 'woo-bulk-editor') ?></option>
                <option value="0" ><?php esc_html_e('Not in', 'woo-bulk-editor') ?></option>
            </select>                      
            &nbsp;<a href="javascript: woobe_bulk_add_combination_to_apply_export();void(0);" id="woobe_bulk_add_combination_to_apply_export" class="button button-primary button"><?php esc_html_e('Add attributes combination', 'woo-bulk-editor') ?></a>

            <br />
            <form>
                <ul id="woobe_bulk_to_var_combinations_apply_export"></ul>
            </form>
            <small style="font-style: italic;"><?php esc_html_e('Select combination(s) of attributes you want (in) or do not want (not in) export if you need it. If leave empty will be exported all combinations. Combinations should be strongly exact!', 'woo-bulk-editor') ?></small>
            <br />
            <br />
            <hr />
            <br />

        <?php else: ?>

            <strong><?php
                printf(esc_html__('No attributes created, you can do it %s', 'woo-bulk-editor'), WOOBE_HELPER::draw_link(array(
                            'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                            'title' => esc_html__('here', 'woo-bulk-editor')
                )));
                ?></strong>

        <?php endif; ?>

    </div>    
</div>
<div class="col-lg-6">
    <a href="javascript: woobe_export_to_csv();void(0);" class="button button-primary button-large woobe_export_products_btn"><span class="icon-export"></span>&nbsp;<?php esc_html_e('Export to CSV', 'woo-bulk-editor') ?></a>
    <a href="javascript: woobe_export_to_xml();void(0);" class="button button-primary button-large woobe_export_products_btn"><?php esc_html_e('Export to XML', 'woo-bulk-editor') ?></a>
    <!-- &nbsp;<a href="javascript: woobe_export_to_excel();void(0);" class="button button-primary button-large woobe_export_products_btn"><?php esc_html_e('Export to Excel', 'woo-bulk-editor') ?></a><br /> -->
    <a href="<?php echo $download_link ?>" target="_blank" class="button button-primary button-large woobe_export_products_btn_down" download="" style="display: none; color: forestgreen;"><span class="icon-download"></span>&nbsp;<?php esc_html_e('download CSV', 'woo-bulk-editor') ?>&nbsp;<span class="icon-download"></span></a>
    <a href="<?php echo $download_link ?>" target="_blank" class="button button-primary button-large woobe_export_products_btn_down_xml" download="" style="display: none; color: forestgreen;"><span class="icon-download"></span>&nbsp;<?php esc_html_e('download XML', 'woo-bulk-editor') ?>&nbsp;<span class="icon-download"></span></a>
    <a href="javascript: woobe_export_to_csv_cancel();void(0);" class="button button-primary button-large woobe_export_products_btn_cancel" style="display: none;"><span class="icon-cancel-circled-3"></span>&nbsp;<?php esc_html_e('cancel export', 'woo-bulk-editor') ?></a>
</div>

<div class="col-lg-6 tar">
    <?php
    echo WOOBE_HELPER::draw_link(array(
        'href' => admin_url('edit.php?post_type=product&page=product_importer'),
        'title' => '<span class="icon-upload"></span>&nbsp;' . esc_html__('Import from CSV', 'woo-bulk-editor'),
        'target' => '_blank',
        'class' => 'button button-primary button-large'
    ));
    ?><br />
</div>
<div class="clear"></div>
<br />

<ul>
    <?php if (array_key_exists('download_files', $active_fields)): ?>
        <li>
            <div class="col-lg-4">
                <input type="number" value="5" placeholder="<?php esc_html_e('max downloads per product', 'woo-bulk-editor') ?>" id="woobe_export_download_files_count" style="width: 120px !important;" />&nbsp;
                <?php echo WOOBE_HELPER::draw_tooltip(esc_html__('Set here maximal possible count of downloads per product. Not possible to automate counting of this value because this data is serialized in the data base!', 'woo-bulk-editor')) ?>
            </div>
            <div class="clear"></div>
        </li>
    <?php endif; ?>

    <li>
        <select id="woobe_export_delimiter">
            <option value=",">,</option>
            <option value=";">;</option>
            <option value="|">|</option>
            <option value="^">^</option>
            <option value="~">~</option>
        </select>&nbsp;<?php echo WOOBE_HELPER::draw_tooltip(esc_html__('Select CSV data delimiter. ATTENTION: if you going to import data back using native woocommerce importer - delimiter should be comma or import of the data will not be possible!', 'woo-bulk-editor')) ?>
    </li>
</ul>


<ul>
    <li>
        <div class="col-lg-12">

            <div class="woobe_progress woobe_progress_export" style="display: none;">
                <div class="woobe_progress_in" id="woobe_export_progress">0%</div>
            </div>

        </div>
        <div class="clear"></div>
    </li>

</ul>



<div class="clear"></div>
<br />
<a href="https://bulk-editor.com/document/woocommerce-products-export/" target="_blank" class="button button-primary woobe_btn_order"><span class="icon-book"></span>&nbsp;<?php esc_html_e('Documentation', 'woo-bulk-editor') ?></a>
<br />
