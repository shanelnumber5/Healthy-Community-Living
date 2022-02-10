
(function (Drupal, $) {
  Drupal.behaviors.cvChosen = {
    attach: function () {
      $(document).bind('clientsideValidationAlterOptions', function (e, options, form_id) {
        if (!Drupal.settings.clientsideValidation.forms[form_id].includeHidden) {
          // Do not validate hidden fields. Fix chosen instances.
          $('#' + form_id).find('select.chosen-processed').each(function () {
            var $select = $(this);
            // jQuery validate binds to the click event for selects, not the
            // change event.
            $select.bind('change', function() {
              $(this).trigger('click');
            })
            var chosen = $select.data('chosen');
            if(!chosen.container.is(':hidden')) {
              options.ignore = fixIgnore(options.ignore, $select.attr('id'));
            }
          });
        }
      });

      var fixIgnore = function (ignore, id) {
        var ignores = ignore.split(',');
        for (var i = 0; i < ignores.length; i++) {
          ignores[i] += ':not(#' + id + ')';
        }
        return ignores.join(',');
      };
    }
  };
})(Drupal, jQuery);


