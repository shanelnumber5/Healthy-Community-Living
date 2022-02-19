<?php
/**
 * @file: admin_layers_render.tpl.php
 * User: Duy
 * Date: 1/28/13
 * Time: 3:31 PM
 */
?>
<?php if ($layer['type'] == 'text'):?>
<div class="slider-item ui-widget-content item-text ui-draggable ui-resizable" <?php print $data;?> style="<?php print $styles;?>">
    <div><?php print $layer['title'];?></div>
    <span class="sl-tl"></span><span class="sl-tr"></span><span class="sl-bl"></span><span class="sl-br"></span>
    <span class="sl-top"></span><span class="sl-right"></span><span class="sl-bottom"></span><span class="sl-left"></span>
</div>
<?php elseif ($layer['type'] == 'image'):?>
<div class="slider-item ui-widget-content item-image ui-draggable ui-resizable" <?php print $data;?> style="<?php print $styles;?>">
    <img width="100%" height="100%" src="<?php print $layer['file_url'];?>" />
    <span class="sl-tl"></span><span class="sl-tr"></span><span class="sl-bl"></span><span class="sl-br"></span>
    <span class="sl-top"></span><span class="sl-right"></span><span class="sl-bottom"></span><span class="sl-left"></span>
</div>
<?php elseif ($layer['type'] == 'video'):?>
<div class="slider-item ui-widget-content item-video ui-draggable ui-resizable" <?php print $data;?> style="<?php print $styles;?>">
    <img width="100%" height="100%" src="<?php print $layer['thumb'];?>" />
    <span class="sl-tl"></span><span class="sl-tr"></span><span class="sl-bl"></span><span class="sl-br"></span>
    <span class="sl-top"></span><span class="sl-right"></span><span class="sl-bottom"></span><span class="sl-left"></span>
</div>
<?php endif;?>
