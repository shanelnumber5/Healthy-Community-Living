<div class="item" <?php !empty($node_title) ? print 'data-description="' . $node_title . '"': ''?> data-id="<?php print $node_created; ?>" <?php print $data_name; ?>>
    <?php print $item_image; ?>
    <?php print $show_date; ?>
    <?php !empty($item_title) ? print '<h2>' . $item_title . '</h2>': ''?>
    <span><?php print $item_text; ?></span>
    <?php print $read_more; ?>
</div>
<div class="item_open<?php empty($item_o_image) ? print ' item_open_noImg' : ''?>" data-id="<?php print $node_created; ?>" style="width:<?php print $ioso_width ?>px">
    <div class="item_open_content">
        <?php print $item_o_image; ?>
        <div class="timeline_open_content" style="height:<?php print $gso_item_height ?>px">
            <?php !empty($open_item_title) ? print '<h2>' . $open_item_title . '</h2>': ''?>
            <span class="scrollable-content" style="height:<?php print $open_content_height ?>px">
                <?php print $open_item_text; ?>
            </span>
        </div>
    </div>
</div>