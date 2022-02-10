(function($) {

  $(document).ready(function() {

    // var drupal_base_path = Drupal.settings.basePath;


    $('.color').after(('<div class="ls-colorpicker" />'));

    $('.color-default').click(function() {
      $this = $(this);
      var $input_color = $this.parent('.description').parent('.form-type-textfield').find('input.color');
      $input_color.val($this.text()).css('color', $this.text());
    });
    jQuery('.ls-colorpicker').each(function() {

      var $item = $(this);

      var $input = $item.parent('.form-type-textfield').find('input.color');
      $input.css('color', $input.val());
      $item.farbtastic(function(color) {

        // Set color code in the input
        // jQuery('.color').val(color);

        $item.parent('.form-item.form-type-textfield').find('.color').val(color);

        // Set input background
        //jQuery('.color').css('background-color', color);
        $item.parent('.form-item.form-type-textfield').find('.color').css('color', color);

        // Update preview

      }).hide();
    });

    // Show color picker on focus
    jQuery('.color').focus(function() {
      jQuery(this).next().slideDown();
    });

    // Show color picker on blur
    jQuery('.color').blur(function() {
      jQuery(this).next().slideUp();
    });



  }); // end document


})(jQuery);