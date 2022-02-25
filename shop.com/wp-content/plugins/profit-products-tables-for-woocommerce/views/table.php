<?php
/**
 * Template for generating HTML structure of a table
 *
 * Uses for all WOOT shortcodes
 *
 * @see https://products-tables.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');

if (!isset($action)) {
    $action = '';
}

if (!isset($table_id)) {
    $table_id = 0;
}

if (!isset($classes)) {
    $classes = '';
}

if (!isset($search_data_key)) {
    $search_data_key = 'post_title';
}

if (!isset($text_search_min_symbols)) {
    $text_search_min_symbols = '';
}

if (!isset($placeholder)) {
    $placeholder = '';
}

if (!isset($hide_filter_form)) {
    $hide_filter_form = false;
}

if (!isset($has_filter)) {
    $has_filter = false;
}

if (!isset($cart_position)) {
    $cart_position = 0;
} else {
    if ($cart_position === 'left') {
        $cart_position = 1;
    }
}

if (!isset($orderby_select)) {
    $orderby_select = '';
}

if (!isset($published)) {
    $published = true;
}

if (!isset($skin)) {
    $skin = '';
}
?>

<?php if (boolval($published)): ?>

    <div class='data-table-23 woot-data-table <?php if (isset($table_view) AND $table_view) echo 'data-table-23-' . esc_attr($table_view); ?> <?php esc_attr_e($action) ?> <?php esc_attr_e($classes) ?>' data-skin="<?php esc_attr_e($skin) ?>" id='<?php esc_attr_e($table_html_id) ?>'>
        <input type="search" data-key="<?php esc_attr_e($search_data_key) ?>" value="" minlength="<?php esc_attr_e($text_search_min_symbols) ?>" class="woot-text-search" <?php if ($hide_text_search): ?>style="display: none;"<?php endif; ?> placeholder="<?php esc_attr_e($placeholder) ?>" />


        <?php if (isset($filter) AND!empty($filter)): ?>
            <div class="woot-filter-data" style="display: none;"><?php echo $filter ?></div>

            <?php
            if ($has_filter):
                ?>
                <?php
                if ($hide_filter_form) {
                    echo WOOT_HELPER::draw_html_item('a', [
                        'href' => 'javascript: void(0);',
                        'onclick' => 'javascript: woot_show_filter(this);void(0);',
                        'class' => 'woot-btn woot-filter-show-btn'
                            ], apply_filters('woot_show_filter_btn_txt', '<i class="woot-icon">&#xf0b0;</i>'));
                }
                ?>
                <div class="woot-filter-list  <?php if ($hide_filter_form): ?>woot-hidden<?php endif; ?>"></div>
            <?php endif; ?>

            <div class="woot-clearfix"></div>
        <?php endif; ?>

        <div class="woot-order-select-zone" <?php if ($cart_position === 1): ?>style="float:right;"<?php endif; ?>>
            <?php
            if (!empty($orderby_select)):
                $first_option = [0 => esc_html__('Sorted by table', 'profit-products-tables-for-woocommerce')];
                $orderby_select = array_merge($first_option, $orderby_select);
                ?>
                <div class="woot-order-select" style="display: none;"><?php echo json_encode($orderby_select) ?></div>
            <?php endif; ?>
        </div>  

        <div class="woot-woocommerce-cart-zone" <?php if ($cart_position === 1): ?>style="float:left;"<?php endif; ?>></div>

        <div class="woot-clearfix"></div>

        <div class="table23-place-loader"><?php echo WOOT_Vocabulary::get(esc_html__('Loading ...', 'profit-products-tables-for-woocommerce')) ?></div>
        <table class="woot-table"></table>

    </div>


    <?php
    $table_html_id = esc_attr($table_html_id);
    $skin = esc_attr($skin);
    echo(isset($style) ? "<style data-table23-skin='{$skin}' data-table23-skin-of='{$table_html_id}'>" . $style . '</style>' : '')
    ?>
<?php else: ?>

    <div class="woot-notice"><strong><?php printf(WOOT_Vocabulary::get(esc_html__('Table %s is not active!', 'profit-products-tables-for-woocommerce')), $table_id) ?></strong></div>

<?php endif; ?>