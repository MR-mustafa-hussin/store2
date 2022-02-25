<?php
/**
 * Template for creating blank pages and further using in WOOT iframes or/and in WOOT popups
 * 
 * @see https://products-tables.com/how-to-create-remote-page-with-the-products-table/
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />

        <?php if (!get_theme_support('title-tag')) : ?>
            <title><?php wp_title(); ?></title>
        <?php endif; ?>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class('woot-blank-page'); ?>>

        <?php
        while (have_posts()) {
            the_post();
            the_content();
        }
        ?>

        <?php
        woot()->include_assets();
        wp_footer();
        ?>

        <style>
            .wpml-ls-statics-footer{
                display: none;
            }
        </style>

    </body>
</html>
