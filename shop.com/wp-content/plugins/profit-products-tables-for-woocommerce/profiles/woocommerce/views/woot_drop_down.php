<?php
/**
 * Template for wrapping shortcode [woot] into <div> to simulate drop-down list
 * 
 * @see https://products-tables.com/shortcode/woot_drop_down/
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="woot-notice">
    <?php echo esc_html__('WOOT: Premium feature', 'profit-products-tables-for-woocommerce') ?>, <a href="https://products-tables.com/upgrading-to-premium/" target="_blank"><?php echo esc_html__('see details', 'profit-products-tables-for-woocommerce') ?></a>
</div>

