<div class="views-bs-timeline-wrapper" id="views-bs-timeline-<?php print $element_id; ?>">
  <?php print $wrapper_prefix; ?>
  <?php if (!empty($title)) : ?>
    <h3><?php print $title; ?></h3>
  <?php endif; ?>
  <?php print $list_type_prefix; ?>
  <?php foreach ($rows as $id => $row): ?>
    <li class="event <?php print $classes_array[$id]; ?>"><?php print $row; ?></li>
    <?php endforeach; ?>
    <?php print $list_type_suffix; ?>
    <?php print $wrapper_suffix; ?>
</div>