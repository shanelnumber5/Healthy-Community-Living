<?php

/**
 * @file
 * Template to display jquery tab wrapper.
 */
?>
<div id="tabs-<?php print $variables['suffix'];?>">
  <ul>
    <?php print $variables['tabs_list']; ?>
  </ul>
  <?php print $variables['tabs_body']; ?>
</div>
