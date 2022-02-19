(function($) {
  Drupal.behaviors.flexslider_format_plugin = {
    attach: function(context, settings) {

      $.each(settings.views_flexslider_format, function(key, value) {
        var slider_settings = value.flexslider_settings;
        views_flexslider_format_init(key, slider_settings, context);
      });

    }
  }; // end behaviors


  function views_flexslider_format_init(id, f_settings, context) {
    var animation = f_settings.animation;
    var slideshow = f_settings.slideshow;
    var slideshowSpeed = parseInt(f_settings.slideshowSpeed);
    var animationSpeed = parseInt(f_settings.animationSpeed);
    var animationLoop = f_settings.animationLoop;
    var itemWidth = parseInt(f_settings.itemWidth);
    var itemMargin = parseInt(f_settings.itemMargin);
    var minItems = parseInt(f_settings.minItems);
    var maxItems = parseInt(f_settings.maxItems);
    var move = f_settings.move;
    var controlNav = f_settings.controlNav;
    var directionNav = f_settings.directionNav;

    // $('#' + id, context).once('flexslider', function() {
    
    $('#' + id, context).flexslider({
      animation: animation,
      slideshow: slideshow,
      slideshowSpeed: slideshowSpeed,
      animationSpeed: animationSpeed,
      animationLoop: animationLoop,
      itemWidth: itemWidth,
      itemMargin: itemMargin,
      minItems: minItems,
      maxItems: maxItems,
      move: move,
      controlNav: controlNav,
      directionNav: directionNav,

    });
    // });



  }

})(jQuery);


