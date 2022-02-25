<?php
/**
 * Template for generating popup HTML
 * 
 * @see https://products-tables.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<template id="woot-popup-template">

    <div class="woot-modal">
        <div class="woot-modal-inner">
            <div class="woot-modal-inner-header">
                <h3 class="woot-modal-title">&nbsp;</h3>
                <div class="woot-modal-title-info">&nbsp;</div>
                <a href="javascript: void(0);" class="woot-modal-close"></a>
            </div>
            <div class="woot-modal-inner-content">
                <div class="woot-form-element-container"><div class="table23-place-loader"><?php echo WOOT_Vocabulary::get(esc_html__('Loading ...', 'profit-products-tables-for-woocommerce')) ?></div><br /></div>
            </div>
            <div class="woot-modal-inner-footer">
                <a href="javascript: void(0);" class="<?php if (is_admin()): ?>button button-primary <?php endif; ?>woot-btn woot-modal-button-large-1"><?php esc_html_e('Close', 'profit-products-tables-for-woocommerce') ?></a>
            </div>
        </div>
    </div>

    <div class="woot-modal-backdrop"></div>

</template>

