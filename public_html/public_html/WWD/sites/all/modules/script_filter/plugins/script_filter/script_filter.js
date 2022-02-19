/**
 * @file
 * Register the wysiwyg plugin.
 */

(function ($) {

Drupal.wysiwyg.plugins['script_filter'] = {

  /**
   * Execute the button.
   */
  invoke: function(data, settings, instanceId) {
    Drupal.wysiwyg.instances[instanceId].openDialog(settings.dialog, data);
  }
};

})(jQuery);
