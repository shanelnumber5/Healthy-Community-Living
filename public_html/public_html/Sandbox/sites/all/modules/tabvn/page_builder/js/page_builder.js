(function($) {

  Drupal.behaviors.page_builder = {
    attach: function(context, settings) {
      // begin parallax js
      
      $.each(settings.page_builder.parallax, function(section_key, section_val) {
        
        $('.' + section_key).parallax(section_val.position, parseFloat(section_val.speed));
      });
      // end parallax js

    }

  };

})(jQuery);