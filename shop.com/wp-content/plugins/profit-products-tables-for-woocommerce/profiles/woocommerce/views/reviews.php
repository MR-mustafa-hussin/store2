<?php
/**
 * Template for generating HTML of shortcode [woot_reviews]
 * 
 * @see https://products-tables.com/shortcode/woot_reviews/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div class="woocommerce">

    <?php if (!isset($_REQUEST['woot_review_in_table'])) : ?>
        <span class="woot-num-cell"><?php echo $average_rating; ?></span>
    <?php endif; ?>

    <div class="woocommerce-product-rating">
        <?php echo wc_get_rating_html($average_rating, $reviews_count); ?>
    </div>

    <?php
    //do not show the link in the popup
    if (!isset($_REQUEST['woot_review_in_table'])) {
        if ($reviews_count > 0) {

            $skin = '';
            if (isset($table_id) AND $table_id > 0) {
                $skin = woot()->skins->get($table_id);
            }

            if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                $skin = $shortcode_args['skin'];
            }

            $unique_id = uniqid('tr');
            $title = addslashes($title);
            $title = sprintf(WOOT_Vocabulary::get(esc_html__('%s reviews', 'profit-products-tables-for-woocommerce')), "#{$product_id}. {$title}");
            echo WOOT_HELPER::draw_html_item('a', [
                'href' => '#',
                'onclick' => "let skin=woot_helper.get_closest_skin(this, \"{$skin}\"); return woot_helper.call_popup(\"woot_get_woocommerce_reviews_table\",{product_id: {$product_id}, skin: skin, not_paste:1}, \"{$unique_id}\", \"{$title}\");",
                'title' => $title
                    ], sprintf(WOOT_Vocabulary::get(esc_html__('%s reviews', 'profit-products-tables-for-woocommerce')), $reviews_count));
        }
    }
    ?>
</div>