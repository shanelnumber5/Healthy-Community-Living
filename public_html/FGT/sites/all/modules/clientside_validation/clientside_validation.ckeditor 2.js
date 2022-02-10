
(function (Drupal, $, CKEDITOR) {
  Drupal.behaviors.cvCKEDITOR = {
    attach: function () {
      $(document).bind('clientsideValidationAlterOptions', function (e, options, form_id) {
        if (!Drupal.settings.clientsideValidation.forms[form_id].includeHidden) {
          // Do not validate hidden fields. Fix ckeditor instances.
          $('#' + form_id).find('textarea.ckeditor-processed').each(function () {
            var $textarea = $(this);
            var id = $textarea.attr('id');
            if (CKEDITOR.instances.hasOwnProperty(id)) {
              // The ckeditor instance has already been initialised, check if
              // the instance is hidden.
              if (CKEDITOR.instances[id].hasOwnProperty('container')) {
                if (!$(CKEDITOR.instances[id].container.$).is(':hidden')) {
                  options.ignore = fixIgnore(options.ignore, id);
                }
              }
              // The ckeditor instance has not yet been initialised. Check if
              // the textarea itself is hidden.
              else if (!$textarea.is(':hidden')) {
                options.ignore = fixIgnore(options.ignore, id);
              }
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

      var debounce = function (func, wait, immediate) {
        var timeout;
        return function () {
          var context = this;
          var args = arguments;
          var later = function () {
            timeout = null;
            if (!immediate)
              func.apply(context, args);
          };
          var callNow = immediate && !timeout;
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
          if (callNow)
            func.apply(context, args);
        };
      };
      var updateText = function (instance) {
        return debounce(function (e) {
          instance.updateElement();
          var event = $.extend(true, {}, e.data.$);
          delete event.target;
          delete event.explicitOriginalTarget;
          delete event.originalTarget;
          delete event.currentTarget;
          $(instance.element.$).trigger(new $.Event(e.name, event));
        }, 250);
      };
      CKEDITOR.on('instanceReady', function () {
        for (var instance in CKEDITOR.instances) {
          if (CKEDITOR.instances.hasOwnProperty(instance)) {
            CKEDITOR.instances[instance].document.on("keyup", updateText(CKEDITOR.instances[instance]));
            CKEDITOR.instances[instance].document.on("paste", updateText(CKEDITOR.instances[instance]));
            CKEDITOR.instances[instance].document.on("keypress", updateText(CKEDITOR.instances[instance]));
            CKEDITOR.instances[instance].document.on("blur", updateText(CKEDITOR.instances[instance]));
            CKEDITOR.instances[instance].document.on("change", updateText(CKEDITOR.instances[instance]));
          }
        }
      });
    }
  };
})(Drupal, jQuery, CKEDITOR);


