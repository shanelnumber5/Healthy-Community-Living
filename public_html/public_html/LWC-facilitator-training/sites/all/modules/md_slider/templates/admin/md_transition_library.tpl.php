<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Mega Slider Alpha</title>

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
  <link href="<?php print file_create_url(drupal_get_path('module', 'md_slider') . '/js/preview_transition/css/md-slider.css');?>" rel="stylesheet" type="text/css" />
  <script src="<?php print file_create_url(drupal_get_path('module', 'md_slider') . '/js/preview_transition/js/md-slider-min.js');?>"></script>
  <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $("#md-slider").mdSlider({
        transitions: "<?php echo isset($parameters['transition']) ? $parameters['transition'] : "fade" ?>",
        height: 200,
        width: 480,
        fullwidth: true,
        showArrow: true,
        showLoading: false,
        slideShow: true,
        showBullet: true,
        showThumb: false,
        slideShowDelay: 3000,
        loop: true,
        strips: 5,
        transitionsSpeed: 1500
      });
    });
  </script>
</head>
<body>
<div class="wrap">
<div class="md-slide-wrap">
  <div class="md-slide-items" id="md-slider">
    <div class="md-slide-item" data-timeout="2000">
      <div class="md-mainimg"><img src="<?php print file_create_url(drupal_get_path('module', 'md_slider') . '/js/preview_transition/img/1.jpg');?>"  style="top:0; left:0;" /></div>
      <div class="md-objects">

      </div>
    </div>
    <div class="md-slide-item" data-timeout="2000">
      <div class="md-mainimg"><img src="<?php print file_create_url(drupal_get_path('module', 'md_slider') . '/js/preview_transition/img/2.jpg');?>" style="top:0; left:0;" /></div>
      <div class="md-objects">
      </div>
    </div>
  </div>
</div>
</div>
</body>
</html>