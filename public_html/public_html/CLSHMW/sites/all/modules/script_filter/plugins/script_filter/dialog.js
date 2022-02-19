/**
 * @file
 * Wysiwyg plugin's dialog window handlers.
 */

(function ($) {

  /**
   * Dialog functions.
   */
  Drupal.behaviors.scriptFilterDialog = {
    attach: function (context, settings) {
      // Dialog variables.
      var dialog = Drupal.settings.wysiwyg;
      var target = window.opener || window.parent;

      // Close the dialog window.
      $('#script-filter-dialog-cancel', context).click(function () {
        target.Drupal.wysiwyg.instances[dialog.instance].closeDialog(window);
        return false;
      });

      // Insert the changed pattern into the wysiwyg editor.
      $('#script-filter-wysiwyg-form', context).submit(function () {
        var code = $("textarea[name='code']").val();
        var modifiedCode = '';

        // Empty script textarea.
        if (code == '') {
          alert(Drupal.t('You did not add any script to insert!'));
          return false;
        }

        // If script html tag is founded, change them to the pattern. If script
        // html tag is not founded in the code, add the pattern around the code
        // as a wrapper.
        if (code.match(/<script/)) {
          modifiedCode = code.replace(/<script([^>]*)>/, "[script$1]");
          modifiedCode = modifiedCode.replace("</script>", "[/script]");
        }
        else {
          modifiedCode = "[script]" + code + "[/script]";
        }

        // Insert the code into the wysiwyg, close the dialog window and block
        // the form submit event.
        target.Drupal.wysiwyg.instances[dialog.instance].insert(modifiedCode);
        target.Drupal.wysiwyg.instances[dialog.instance].closeDialog(window);
        return false;
      });
    }
  };
})(jQuery);
