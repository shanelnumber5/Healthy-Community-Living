<?php

/**
 * @file
 * Field handlers for Ubercart Addresses address fields:
 * - address_name;
 * - default_shipping;
 * - default_billing;
 * - created;
 * - modified;
 * - aid;
 * - uid.
 */

/**
 * Handler for a simple text field.
 */
class UcAddressesTextFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
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
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return TRUE;
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }
}

/**
 * Class for the address name field.
 */
class UcAddressesAddressNameFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::init().
   *
   * @throws UcAddressesIncompatibleHandlerException
   *   In case the given address instance is not an instance of
   *   UcAddressesAddress.
   */
  protected function init() {
    $address = $this->getAddress();
    if (!($address instanceof UcAddressesAddress)) {
      throw new UcAddressesIncompatibleHandlerException(t('The handler %handler needs an UcAddressesAddress instance to function.', array('%handler' => get_class($this))));
    }
  }

  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    return array(
      $fieldName => array(
        '#type' => 'textfield',
        '#title' => t('Save as'),
        '#description' => t('Enter a name for this address (e.g. Home or Work)'),
        '#default_value' => $default,
        '#required' => $this->isFieldRequired(),
        '#maxlength' => 20,
        '#size' => 32,
      ),
    );
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return variable_get('uc_addresses_use_address_name', TRUE);
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }

  /**
   * Implements UcAddressesFieldHandler::validateValue().
   *
   * Checks if the given name is unique.
   */
  public function validateValue(&$value) {
    $fieldName = $this->getFieldName();
    if ($value) {
      if (!$this->getAddress()->setName($value)) {
        form_set_error($fieldName, t('You already have an address named %name in your address book.', array('%name' => $value)));
      }
    }
  }
}

/**
 * Class for default address field.
 *
 * This class requires extra properties in the field definition:
 * - default_type: lowercase string that specifies the kind of default
 *   address (e.g., shipping);
 * - suffix: used as a suffix to the form field in address edit forms.
 */
class UcAddressesDefaultAddressFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::init().
   *
   * @throws UcAddressesIncompatibleHandlerException
   *   In case the given address instance is not an instance of
   *   UcAddressesAddress.
   */
  protected function init() {
    $address = $this->getAddress();
    if (!($address instanceof UcAddressesAddress)) {
      throw new UcAddressesIncompatibleHandlerException(t('The handler %handler needs an UcAddressesAddress instance to function.', array('%handler' => get_class($this))));
    }
  }

  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;
    $default_type = $this->getProperty('default_type');

    // Check if user already has a default address of type $type.
    $default_address = $this->getAddress()->getAddressBook()->getDefaultAddress($default_type);

    if (!is_null($default_address)) {
      return array(
        $fieldName => array(
          '#type' => 'checkbox',
          '#title' => $this->getFieldTitle(),
          '#uc_addresses_default_address_suffix' => $this->getProperty('suffix'),
          '#default_value' => $default,
          '#disabled' => $fieldValue,
          '#required' => $this->isFieldRequired(),
          '#theme' => 'uc_addresses_default_address_checkbox',
        ),
      );
    }
    else {
      // No default address of type $type yet. Force default.
      return array(
        $fieldName => array(
          '#type' => 'value',
          '#value' => 1,
        ),
        $fieldName . '_item' => array(
          '#type' => 'checkbox',
          '#title' => $this->getFieldTitle(),
          '#uc_addresses_default_address_suffix' => $this->getProperty('suffix'),
          '#default_value' => 1,
          '#disabled' => TRUE,
          '#required' => $this->isFieldRequired(),
          '#theme' => 'uc_addresses_default_address_checkbox',
        ),
      );
    }
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return variable_get('uc_addresses_use_default_' . $this->getProperty('default_type'), TRUE);
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }

  /**
   * Overrides UcAddressesFieldHandler::getDefaultValue().
   */
  public function getDefaultValue() {
    return 0;
  }
}

/**
 * Class for the created/modified field.
 */
class UcAddressesDateFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;
    if (is_numeric($default)) {
      $default = array(
        'hour' => date('H', $default),
        'minute' => date('i', $default),
        'second' => date('s', $default),
        'month' => date('n', $default),
        'day' => date('j', $default),
        'year' => date('Y', $default),
      );
    }

    return array(
      $fieldName => array(
        '#type' => 'date',
        '#title' => $this->getFieldTitle(),
        '#default_value' => $default,
        '#required' => $this->isFieldRequired(),
      ),
    );
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return TRUE;
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }

  // -----------------------------------------------------------------------------
  // VALIDATE
  // -----------------------------------------------------------------------------

  /**
   * Implements UcAddressesFieldHandler::validateValue().
   *
   * Makes sure the value is converted to a timestamp.
   */
  public function validateValue(&$value) {
    $value = mktime(0, 0, 0, $value['month'], $value['day'], $value['year']);
  }

  // -----------------------------------------------------------------------------
  // OUTPUT
  // -----------------------------------------------------------------------------

  /**
   * Overrides UcAddressesFieldHandler::getTokenInfo().
   *
   * Specifies that tokens of this field are of type 'date'.
   */
  public function getTokenInfo() {
    $fieldname = $this->getFieldName();
    $tokens = parent::getTokenInfo();

    $tokens[$fieldname] = array(
      'name' => $this->getFieldTitle(),
      'description' => $this->getProperty('description'),
      'type' => 'date',
    );
    return $tokens;
  }

  /**
   * Overrides UcAddressesFieldHandler::outputValue().
   *
   * If a format is specified, the value will be formatted using that (date) format.
   * If no format is specified, the value will be formatted using the site default
   * date format.
   */
  public function outputValue($value = '', $format = '') {
    $values = &drupal_static('UcAddressesDateFieldHandler::outputValue', array());
    if ($value === '') {
      $value = $this->getAddress()->getField($this->getFieldName());
    }
    if (isset($values[$value][$format])) {
      return $values[$value][$format];
    }
    if ($format) {
      $token = token_generate('date', array($format => $format), array('date' => $value));
      if (isset($token[$format])) {
        $values[$value][$format] = $token[$format];
      }
    }
    else {
      $values[$value][$format] = format_date($value);
    }
    if (isset($values[$value][$format])) {
      return $values[$value][$format];
    }
  }
}

/**
 * Class for aid/uid field (which can not be set directly).
 */
class UcAddressesHiddenFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    return array(
      $fieldName => array(
        '#type' => 'value',
        '#title' => $this->getFieldTitle(),
        '#value' => $default,
      ),
    );
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return TRUE;
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }
}

/**
 * Handler for a missing handler.
 */
class UcAddressesMissingFieldHandler extends UcAddressesFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    return array();
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return FALSE;
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    return FALSE;
  }
}
