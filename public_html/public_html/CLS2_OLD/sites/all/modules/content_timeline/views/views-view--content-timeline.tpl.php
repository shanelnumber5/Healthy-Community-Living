<?php if(arg(2) == "views") {print '<div class="messages warning">Views preview is currently not available. Go to the defined path to see Content Timeline in action</div>';} ?>
<?php if (!empty($rows)):?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<div id="<?php print $view_id; ?>" class="timeline<?php print ' ' . $gso_line_style; print ' ' . $gso_nav_style; print ' ' . $bso_button_type; ?>">
  <?php foreach ($rows as $id => $row): ?>
    <?php print $row; ?>
  <?php endforeach; ?>
</div>
<?php drupal_add_js('(function ($) {
  Drupal.behaviors.contentTimelineModuleTimeline_'.$view_id.' = {
    attach: function(context, settings) {
      $("#'.$view_id.'").timeline({
          itemMargin : '.$gso_item_margin.',
          scrollSpeed : '.$go_scroll_speed.',
          easing : "'.$go_easing.'",
          openTriggerClass : \''.$gso_read_more.'\',
          swipeOn : true,
          startItem : "'.$go_start_item.'",
          yearsOn : '.$go_hide_years.',
          hideTimeline : '.$gso_hide_timeline.',
          hideControles : '.$gso_hide_navigation.',
          closeText : "'.$bso_close_button_text.'",
          '.$timeline_categories.'
          '.$timeline_number_of_segmets.'
          closeItemOnTransition: '.$go_close_item_on_transition.'
        });
      }
    };
  })(jQuery);', array('type' => 'inline', 'scope' => 'footer', 'weight' => 6)); ?>
<?php endif; ?>