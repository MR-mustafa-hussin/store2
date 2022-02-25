<?php
/**
 * Template for generating gallery HTML
 * 
 * @see https://products-tables.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');

if (!isset($uniqid)) {
    $uniqid = uniqid();
}

if (!isset($is_shortcode)) {
    $is_shortcode = false;
}
?>

<div <?php if ($is_shortcode): ?>class="woot-gallery-container"<?php endif; ?> id="woot-gallery-container-<?php esc_attr_e($uniqid) ?>" <?php if ($is_shortcode): ?>style="display: none;"<?php endif; ?>>
    <div class="woot-gallery woot-gallery-cell" id="woot-gallery-<?php esc_attr_e($uniqid) ?>">
        <?php foreach ($img_data as $counter => $img) : ?>
            <div class="woot-gallery-nav"><img src="<?php esc_attr_e($img['thumb']) ?>" loading="lazy" alt="" /><a href="#woot-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter) ?>">&nbsp;</a></div>
        <?php endforeach; ?>
    </div>


    <?php foreach ($img_data as $counter => $img) : ?>
        <div class="woot-gallery-lightbox" id="woot-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter) ?>">

            <?php if (isset($img_data[$counter - 1])): ?>
                <div class="woot-gallery-nav  woot-gallery-nav-left"><a href="#woot-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter - 1) ?>"><img src="<?php esc_attr_e($img_data[$counter - 1]['thumb']) ?>" loading="lazy" width="50" alt="" /></a></div>
            <?php endif; ?>

            &nbsp;<div class="woot-gallery-content"><img src="<?php esc_attr_e($img['original']) ?>" loading="auto" alt="" />           
                <div class="woot-gallery-title"><?php esc_attr_e($img['title']) ?></div>
                <a class="woot-gallery-close" href="#/woot-gallery-<?php esc_attr_e($uniqid) ?>"></a>
            </div>&nbsp;

            <?php if (isset($img_data[$counter + 1])): ?>
                <div class="woot-gallery-nav woot-gallery-nav-right"><a href="#woot-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter + 1) ?>"><img src="<?php esc_attr_e($img_data[$counter + 1]['thumb']) ?>" loading="lazy" width="50" alt="" /></a></div>
                    <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>
<?php if ($is_shortcode): ?>

    <script>
        //stop image blinking while loading
        setTimeout(function () {
            document.getElementById('woot-gallery-container-<?php esc_attr_e($uniqid) ?>').classList.add('woot-gallery-container-active')
        }, 777);
    </script>

<?php endif; ?>
