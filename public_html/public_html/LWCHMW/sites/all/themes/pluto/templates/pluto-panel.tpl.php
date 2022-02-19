<?php
/**
 * @file
 * pluto-panel.tpl.php
 *
 * Markup for Bootstrap panels ([collapsible] fieldsets).
 */
?>
<?php if ($prefix): ?>
  <?php print $prefix; ?>
<?php endif; ?>
<div <?php print $attributes; ?>>
  <?php if ($title): ?>
    <?php if ($collapsible): ?>
      <div class="panel-heading">
        <h4 class="panel-title">
          <a href="#<?php print $id; ?>" class="panel-title fieldset-legend" data-toggle="collapse">
            <?php print $title; ?>
          </a>
        </h4>
      </div>
    <?php else: ?>
      <div class="panel-heading">
        <div class="panel-title fieldset-legend">
          <h4> <?php print $title; ?></h4>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($collapsible): ?>
    <div id="<?php print $id; ?>" class="panel-collapse collapse fade<?php print (!$collapsed ? ' in' : ''); ?>">
    <?php endif; ?>
    <div class="panel-body">
      <?php if ($description): ?>
        <p class="help-block">
          <?php print $description; ?>
        </p>
      <?php endif; ?>
      <?php print $content; ?>
    </div>
    <?php if ($collapsible): ?>
    </div>
  <?php endif; ?>
</div>
<?php if ($suffix): ?>
  <?php print $suffix; ?>
<?php endif; ?>
