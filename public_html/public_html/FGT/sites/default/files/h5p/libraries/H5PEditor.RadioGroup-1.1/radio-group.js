/**
 * RadioGroup widget
 *
 * @param {H5P.jQuery} $
 */
H5PEditor.RadioGroup = H5PEditor.widgets.radioGroup = (function ($) {

  var groupCounter = 0;
  /**
   * Creates an radio button group.
   *
   * @class H5PEditor.ImageRadioButtonGroup
   * @param {Object} parent
   * @param {Object} field
   * @param {Object} params
   * @param {function} setValue
   */
  function RadioGroup(parent, field, params, setValue) {
    this.parent = parent;
    this.field = field;
    this.params = params;
    this.setValue = setValue;

    this.alignment = this.field.alignment || 'vertical';

    groupCounter++;
  }

  /**
   * Append the field to the wrapper.
   * @public
   * @param {H5P.jQuery} $wrapper
   */
  RadioGroup.prototype.appendTo = function ($wrapper) {
    var self = this;

    self.$container = $(H5PEditor.createFieldMarkup(
        self.field,
        '<div class="h5p-editor-radio-group-container ' + self.alignment + '" role="radiogroup"></div>'
    ));

    var $buttonGroup = self.$container.find('.h5p-editor-radio-group-container');

    for (var i = 0; i < self.field.options.length; i++) {
      var option = self.field.options[i];
      var inputId = 'h5p-editor-radio-group-button-' + groupCounter + '-' + i;

      var $button = $('<div>', {
        'class': 'h5p-editor-radio-group-button ' + option.value
      }).appendTo($buttonGroup);

      $('<input>', {
        type: 'radio',
        name: self.field.name + groupCounter,
        value: option.value,
        role: 'radio',
        id: inputId,
        checked: (self.params === option.value) || (self.params === undefined && this.field.default === option.value),
        change: function () {
          self.params = $('input:checked', $buttonGroup).val();
          self.setValue(self.field, self.params);
        }
      }).appendTo($button);

      $('<label>', {
        'for': inputId
      }).append($('<span>', {
        html: option.label
      })).appendTo($button);

      if (option.description) {
        $('<div>', {
          'class': 'h5p-option-description',
          html: option.description
        }).appendTo($button);
      }
    }

    self.$container.appendTo($wrapper);
  };


  /**
   * Validate the current values.
   */
  RadioGroup.prototype.validate = function () {
    return true;
  };

  RadioGroup.prototype.remove = function () {};

  return RadioGroup;
})(H5P.jQuery);
