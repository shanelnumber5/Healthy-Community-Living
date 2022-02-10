<?php

/**
 * @file
 * Contains UcAddressesExampleTextFieldHandler class.
 */

/**
 * Handler for a simple text field.
 */
class UcAddressesExampleTextFieldHandler extends UcAddressesFieldHandler {
  /**
   * Returns the editable field.
   *
   * @param array $form
   *   The address form element.
   * @param array $form_values
   *   An array of filled in values for one address.
   *
   * @access public
   * @return array
   *   A Drupal Form API field.
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    return array(
      $fieldName => array(
        '#type' => 'textfield',
        '#title' => $this->getFieldTitle(),
        '#default_value' => $default,
        '#required' => $this->isFieldRequired(),
        '#maxlength' => 20,
        '#size' => 32,
      ),
    );
  }

  /**
   * Check to see if the field is enabled.
   *
   * @access public
   * @return boolean
   *   TRUE if the field is enabled.
   *   FALSE otherwise.
   */
  public function isFieldEnabled() {
    return TRUE;
  }

  /**
   * Check to see if the field is required.
   *
   * @access public
   * @return boolean
   *   TRUE if the field is required.
   *   FALSE otherwise.
   */
  public function isFieldRequired() {
    return FALSE;
  }
}
