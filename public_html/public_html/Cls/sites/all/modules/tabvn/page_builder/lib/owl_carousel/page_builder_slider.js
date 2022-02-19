(function($) {
  Drupal.behaviors.page_builder_sliders = {
    attach: function(context, settings) {

      $.each(settings.page_builder.sliders, function(section, slider) {
        $('.' + section + ' .page-builder-column-wrapper').owlCarousel(slider);
      });


    }

  };

})(jQuery);