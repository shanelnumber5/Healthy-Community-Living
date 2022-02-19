/**
 * @file
 * Javascript for uc_custom_payment edit form.
 */
(function($) {
  Drupal.behaviors.ucCustomPaymentUI = {
    attach: function(context) {
      $('#edit-service-charge', context).keyup(function() {
        if (this.value.indexOf('%') == -1) {
          $(this).siblings('span').show();
        }
        else {
          $(this).siblings('span').hide();
        }
      }).keyup();
    }
  };
})(jQuery);

