<div id="<?php print $flexslider_id; ?>" class="views-flexslider-format flexslider">
  <?php if (!empty($rows)): ?>
    <ul class="slides">
      <?php foreach ($rows as $key => $row): ?>
        <?php
        $animate = 'fadeInUp';
        if ($key % 2 == 0) {
          $animate = 'fadeInDown';
        }
        ?>
        <li class="slide-item slide-item-<?php print $key; ?>">
          <div class="views-flexslider-format-item wow <?php print $animate; ?>">
            <?php print $row; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>