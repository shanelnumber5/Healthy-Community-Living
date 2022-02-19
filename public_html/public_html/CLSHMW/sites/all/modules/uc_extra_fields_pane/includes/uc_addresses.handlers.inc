<?php
/**
 * @file
 * Integration code for Ubercart Addresses 7.x-1.x
 *
 * This file implements an Ubercart Addresses field handler that is used
 * by all Extra Fields Pane fields.
 */

/**
 * Field handler for all Extra Fields Pane fields.
 */
class UcAddressesUCXFHandler extends UcAddressesFieldHandler {
  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * Extra Fields Pane field object.
   *
   * @var UCXF_Field
   * @access private
   */
  private $ucxf_field;

  // -----------------------------------------------------------------------------
  // CONSTRUCT
  // -----------------------------------------------------------------------------

  /**
   * Initialize.
   *
   * @access public
   * @return void
   */
  public function init() {
    parent::init();
    $fieldName = $this->getFieldName();
    $this->ucxf_field = UCXF_FieldList::getFieldByName($fieldName);
  }

  // -----------------------------------------------------------------------------
  // ACTION
  // -----------------------------------------------------------------------------

  /**
   * Returns the editable field
   *
   * @param array $form
   * @param array $form_values
   * @access public
   * @return array
   */
  public function getFormField($form, $form_values) {
    // Check if the field may be shown if it is a specific pane we are generating
    // the field for.
    if (
      isset($form['#key_prefix'])
        && ($form['#key_prefix'] == 'delivery' || $form['#key_prefix'] == 'billing')
        && !$this->ucxf_field->in_pane('extra_' . $form['#key_prefix'])
    ) {
      // On the checkout and the order form, "#key_prefix" tells us for which pane
      // the field must be generated ("delivery" or "billing").
      // Extra Fields Pane has a setting to show the field in either the delivery
      // or the billing pane or both.
      // If the field may not be shown in one of these panes and that pane is the
      // current pane to generate a field for then:
      // - don't generate the field
      // - empty the field's value
      // - return an empty array instead.
      $this->getAddress()->setField($this->getFieldName(), '');
      return array();
    }

    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    $field[$fieldName] = $this->ucxf_field->generate();
    if (!is_null($default) || !isset($field[$fieldName]['#default_value'])) {
      $field[$fieldName]['#default_value'] = $default;
    }

    if (isset($form['#uc_addresses_required']) && $form['#uc_addresses_required'] === FALSE || $this->isFieldRequired() === FALSE) {
      $field[$fieldName]['#required'] = FALSE;
    }

    // On the order edit form, a generated field shouldn't be a hidden field.
    // In this case the field will be set to a normal textfield, so it's editable.
    if ($this->getContext() == 'order_form') {
      if ($field[$fieldName]['#type'] == 'hidden') {
        $field[$fieldName]['#type'] = 'textfield';
        if (is_null($default)) {
          $field[$fieldName]['#default_value'] = $field[$fieldName]['#value'];
        }
        // Unset value, field already has an default value.
        unset($field[$fieldName]['#value']);
        // Set title and size of field.
        $field[$fieldName]['#title'] = $this->ucxf_field->output('label');
        $field[$fieldName]['#size'] = 32;
      }
    }
    elseif ($field[$fieldName]['#type'] == 'hidden' && $this->ucxf_field->may_display('checkout')) {
      $field[$fieldName . '_item'] = array(
        '#type' => 'item',
        '#title' => $this->ucxf_field->output('label'),
        '#markup' => $field[$fieldName]['#value'],
      );
    }

    // In case of select fields, add default value as an option if it is not an available option yet.
    if ($this->getContext() == 'order_form' && $field[$fieldName]['#type'] == 'select') {
      $default_value = $field[$fieldName]['#default_value'];
      if ($default_value != '' && !isset($field[$fieldName]['#options'][$default_value])) {
        $field[$fieldName]['#options'][$default_value] = t('Deleted option: @option', array('@option' => $default_value));
      }
    }

    return $field;
  }

  /**
   * Check to see if the field is enabled.
   *
   * @access public
   * @return boolean
   *	 TRUE if the field is enabled.
   */
  public function isFieldEnabled() {
    return (bool) $this->ucxf_field->enabled;
  }

  /**
   * Check to see if the field is required.
   *
   * @access public
   * @return boolean
   *	 TRUE if the field is required.
   */
  public function isFieldRequired() {
    if ($this->getContext() == 'order_form') {
      return FALSE;
    }
    return (bool) $this->ucxf_field->required;
  }

  /**
   * Returns a default value for this field.
   *
   * @access public
   * @return null
   */
  public function getDefaultValue() {
    return NULL;
  }

  // -----------------------------------------------------------------------------
  // OUTPUT
  // -----------------------------------------------------------------------------

  /**
   * Output a fields value.
   *
   * @param mixed $value
   * @access public
   * @return string
   */
  public function outputValue($value = '', $format = '') {
    if ($value === '') {
      $value = $this->getAddress()->getField($this->getFieldName());
    }
    $output = $this->ucxf_field->output_value($value);
    if ($output == t('n/a') && $this->getContext() == 'token') {
      // If the context is "token", don't output "n/a". Just return an
      // empty string in this case.
      // This is because tokens can be used in address labels and we
      // don't want to end up with a "n/a" in there.
      return '';
    }
    return $output;
  }
}
