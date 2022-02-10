<?php
/**
 * @file: front_layers_render.tpl.php
 * User: Duy
 * Date: 1/29/13
 * Time: 3:57 PM
 */
?>
<?php if(!empty($layer->opacity)):?>
<div class="md-item-opacity" <?php print "style=\"{$layer->opacity}\""?>>
<?php endif;?>
  <div class="<?php print $class; ?>" <?php print $data; ?>>
    <?php if ($layer->type == 'text'): ?>
        <?php if (isset($link)):?>
            <a href="<?php print $link;?>"<?php if (isset($layer->link["target"]) && !empty($layer->link["target"])) print " target='{$layer->link["target"]}'";?>><?php print $layer->title;?></a>
        <?php else:?>
            <?php print $layer->title;?>
        <?php endif; ?>
    <?php elseif ($layer->type == 'image'): ?>
        <?php if (isset($link)):?>
            <a href="<?php print $link;?>"<?php if (isset($layer->link["target"]) && !empty($layer->link["target"])) print " target='{$layer->link["target"]}'";?>>
                <img src="<?php print $layer->url;?>" alt="<?php print htmlentities($layer->title, ENT_QUOTES, "UTF-8");?>" />
            </a>
        <?php else:?>
            <img src="<?php print $layer->url;?>" alt="<?php print htmlentities($layer->title, ENT_QUOTES, "UTF-8");?>" />
        <?php endif; ?>
    <?php elseif ($layer->type == 'video'): ?>
    <a title="<?php print htmlentities($layer->title, ENT_QUOTES, 'UTF-8'); ?>" class="md-video"
       href="<?php print $layer->url; ?>">
        <img src="<?php print $layer->thumb; ?>" alt="<?php print htmlentities($layer->title, ENT_QUOTES, 'UTF-8'); ?>"/>
        <span class="md-playbtn"></span>
    </a>
    <?php endif; ?>
  </div>
<?php if(!empty($layer->opacity)):?>
</div>
<?php endif;?>
