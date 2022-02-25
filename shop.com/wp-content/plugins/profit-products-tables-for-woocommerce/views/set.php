<?php
/**
 * Template for generating HTML structure of some tables in the tabs
 * 
 * @see https://products-tables.com/codex/
 * @version 1.0.0
 */

if (!defined('ABSPATH'))
    die('No direct access allowed');

$ids = [];
?>

<?php if (!empty($shortcodes)): ?>
    <div class="woot-tables-set">
        <!------------------------ tabs -------------------------------->
        <?php foreach ($shortcodes as $c => $sh): $ids[$c] = uniqid('t') ?>
            <a onclick="woot_show_tab(event, '<?php esc_attr_e($ids[$c]) ?>')" data-tab-id="<?php esc_attr_e($ids[$c]) ?>" class="woot-btn woot-tab-link <?php if ($c === 0): ?>woot-tab-link-current<?php endif; ?>" href="javascript: void(0);"><?php esc_attr_e($sh['title']) ?></a>
        <?php endforeach; ?>

        <!------------------------ content ----------------------------->
        <?php foreach ($shortcodes as $c => $sh): ?>
            <div id="<?php esc_attr_e($ids[$c]) ?>" class="woot-tab-content <?php if ($c === 0): ?>woot-tab-content-current<?php else: ?>woot-tab-content-hidden<?php endif; ?>"><?php echo $sh['content'] ?></div>
        <?php endforeach; ?>

    </div>
<?php endif; ?>