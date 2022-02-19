<?php

/**
 * @file
 * Contains the UcAddressesSchemaAddress class.
 */

/**
 * The schema address class.
 *
 * This is the base class for addresses. It's goal is to provide functionality
 * for address fields. You can get and set address field values. For this it's
 * connected with the field handler system. Unlike the UcAddressesAddress class
 * it's not connected with the address book class, which means you *could* use
 * this class if you want to make use of the field handler system and you want
 * to bypass any restrictions implied with UcAddressesAddress (such as having
 * unique nicknames), but in most cases you should not interact with this class
 * directly.
 *
 * The class doesn't interact with the database itself: this should be done in
 * subclasses (such as UcAddressesAddress).
 */
class UcAddressesSchemaAddress {
  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * The base schema address object.
   *
   * We extend this by aggregation.
   *
   * @var stdClass
   * @access private
   */
  private $schemaAddress;

  /**
   * TRUE if the address is changed after being loaded or created.
   *
   * @var boolean
   * @access private
   */
  private $dirty = FALSE;

  /**
   * A variable that's used to keep data when the address object.
   * is being serialized.
   *
   * @var array
   * @access protected
   */
  protected $sleep = array();

  // -----------------------------------------------------------------------------
  // CONSTRUCT
  // -----------------------------------------------------------------------------

  /**
   * Construct a schema address.
   *
   * @param object $schemaAddress
   *   The schema address array to wrap. If null, a new stdClass
   *   object is created.
   *
   * @access public
   * @return void
   */
  public function __construct($schemaAddress = NULL) {
    $this->schemaAddress = new stdClass();
    if (is_object($schemaAddress)) {
      $this->schemaAddress = $schemaAddress;
    }
    // Make sure all fields are present.
    $fields = self::getDefinedFields();
    foreach ($fields as $fieldName => $fielddata) {
      if (!isset($this->schemaAddress->$fieldName)) {
        $instance = $this->getHandler($fieldName);
        $this->schemaAddress->$fieldName = $instance->getDefaultValue();
      }
    }
  }

  /**
   * Tells which members may be kept when the address is being serialized.
   *
   * @access public
   * @return array
   *   An array of members to keep upon serialization.
   */
  public function __sleep() {
    $vars = get_object_vars($this);
    foreach ($vars as $key => $value) {
      if ($key != 'sleep') {
        $this->sleep[$key] = $value;
      }
    }
    return array(
      'sleep',
    );
  }

  /**
   * Restore variables when the address is unserialized.
   *
   * @access public
   * @return array
   */
  public function __wakeup() {
    // Restore variables saved in sleep.
    foreach ($this->sleep as $key => $value) {
      $this->$key = $value;
    }
    // Clear out sleep.
    $this->sleep = array();
  }

  // -----------------------------------------------------------------------------
  // "DIRTY" METHODS
  // -----------------------------------------------------------------------------

  /**
   * Clear the dirty flag.
   *
   * When set, the dirty flag signals that the address needs to be
   * saved in the database.
   *
   * @access protected
   * @return void
   */
  protected function clearDirty() {
    $this->dirty = FALSE;
  }

  /**
   * Set the dirty flag.
   *
   * When set, the dirty flag signals that the address needs to be
   * saved in the database.
   *
   * @access protected
   * @return void
   */
  protected function setDirty() {
    $this->dirty = TRUE;
  }

  /**
   * Reports if the address is modified since it was loaded from the database.
   *
   * @access protected
   * @return boolean
   *   TRUE if the address is modified.
   *   FALSE otherwise.
   */
  protected function isDirty() {
    return $this->dirty;
  }

  // -----------------------------------------------------------------------------
  // FIELDS
  // -----------------------------------------------------------------------------

  /**
   * Magic getter.
   *
   * Returns properties/fields from the address object.
   *
   * @return mixed
   *   Property or field values.
   */
  public function __get($property) {
    try {
      if (self::fieldExists($property)) {
        return $this->getField($property);
      }
    }
    catch (UcAddressesUndefinedFunctionException $e) {
      // Ignore undefined function exceptions.
    }
    if (isset($this->$property)) {
      return $this->$property;
    }
  }

  /**
   * Magic setter.
   *
   * Passes values to the address object.
   *
   * @return void
   * @throws UcAddressesException
   */
  public function __set($property, $value) {
    try {
      if (self::fieldExists($property)) {
        return $this->setField($property, $value);
      }
    }
    catch (UcAddressesUndefinedFunctionException $e) {
      // Ignore undefined function exceptions.
    }
    $this->$property = $value;
  }

  /**
   * Magic method for giving back if property exists or not.
   *
   * @return boolean
   *   TRUE if the property exists.
   *   FALSE otherwise.
   */
  public function __isset($property) {
    try {
      if (self::fieldExists($property)) {
        return TRUE;
      }
    }
    catch (UcAddressesUndefinedFunctionException $e) {
      // Ignore undefined function exceptions.
    }
    // Else, fallback to the "real" properties.
    return isset($this->$property);
  }

  /**
   * Get a field's value.
   *
   * @param string $fieldName
   *   The name of the field whose value we want.
   *
   * @access public
   * @return mixed
   *   The field value.
   * @throws UcAddressInvalidFieldException
   */
  public function getField($fieldName) {
    self::fieldMustExist($fieldName);
    return $this->schemaAddress->$fieldName;
  }

  /**
   * Set a field's value.
   *
   * @param string $fieldName
   *   The name of the field whose value we will set.
   * @param mixed $value
   *   The value to which to set the field.
   *
   * @access public
   * @return void
   * @throws UcAddressInvalidFieldException
   */
  public function setField($fieldName, $value) {
    self::fieldMustExist($fieldName);

    // Convert value to the right data type.
    $fields_data = self::getDefinedFields();
    switch ($fields_data[$fieldName]['type']) {
      case 'text':
      case 'string':
        $value = (string) $value;
        break;

      case 'int':
      case 'integer':
      case 'date':
        $value = (int) $value;
        break;

      case 'decimal':
      case 'duration':
      case 'float':
      case 'numeric':
        $value = (float) $value;
        break;

      case 'boolean':
        $value = (bool) $value;
        break;

      default:
        // In all other cases the setted value is left untouched.
        break;
    }

    if ($this->schemaAddress->$fieldName !== $value) {
      $this->schemaAddress->$fieldName = $value;
      $this->setDirty();
    }
  }

  /**
   * Set multiple fields at once.
   *
   * @param array $fields
   *   An array of fields with $fieldName => $value.
   * @param boolean $fieldsMustExist
   *   (optional) If TRUE, every field in the array must exists.
   *   If there are fields in the array that do not exists an
   *   UcAddressInvalidFieldException will be thrown.
   *   Defaults to FALSE (no exceptions will be thrown).
   *
   * @access public
   * @return void
   * @throws UcAddressInvalidFieldException
   */
  public function setMultipleFields($fields, $fieldsMustExist = FALSE) {
    foreach ($fields as $fieldName => $value) {
      if (!$fieldsMustExist && !self::fieldExists($fieldName)) {
        continue;
      }
      $this->setField($fieldName, $value);
    }
  }

  /**
   * Returns TRUE if field is registered through the API.
   *
   * @param string $fieldName
   *   The name of the field whose existence we want to check.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if addresses have a field with the given name.
   *   FALSE otherwise.
   */
  static public function fieldExists($fieldName) {
    $fields_data = self::getDefinedFields();
    return isset($fields_data[$fieldName]);
  }

  /**
   * Throws an exception if the field does not exist.
   *
   * @param string $fieldName
   *   The name of the field whose existence is required.
   *
   * @access private
   * @static
   * @return void
   * @throws UcAddressInvalidFieldException
   *   When the field does not exists.
   */
  static private function fieldMustExist($fieldName) {
    if (!self::fieldExists($fieldName)) {
      throw new UcAddressesInvalidFieldException(t('Invalid field name %name', array('%name' => $fieldName)));
    }
  }

  /**
   * Returns "safe" field data.
   *
   * @access public
   * @return array
   *   Address values that are safe for output.
   */
  public function getFieldData() {
    $values = array();
    $fields_data = self::getDefinedFields();
    foreach ($fields_data as $fieldName => $fielddata) {
      $instance = $this->getHandler($fieldName);
      $values[$fieldName] = $instance->outputValue($this->getField($fieldName));
    }
    return $values;
  }

  /**
   * Returns "raw" field data (contents of the schema address object).
   *
   * @access public
   * @return array
   *   The saved address values, NOT safe for output.
   */
  public function getRawFieldData() {
    return (array) $this->schemaAddress;
  }

  /**
   * Get a "safe" field value from a single field.
   *
   * @param string $fieldName
   *   The name of the field whose value we want.
   * @param string $format
   *   (optional) The format in which the value should be outputted.
   *   See outputValue() in UcAddressesFieldHandler.class.php for
   *   more information.
   * @param string $context
   *   (optional) The context where the field is used for.
   *
   * @access public
   * @return mixed
   *   The field's value safe for ouput.
   * @throws UcAddressInvalidFieldException
   */
  public function getFieldValue($fieldName, $format = '', $context = 'default') {
    self::fieldMustExist($fieldName);
    $handler = $this->getHandler($fieldName, $context);
    return $handler->outputValue($this->getField($fieldName), $format);
  }

  // -----------------------------------------------------------------------------
  // HELPER METHODS
  // These methods call functions defined outside the class.
  // To ensure the class from operating well, we should throw an exception in
  // case the functions were not defined. This can happen early in the Drupal
  // bootstrap phase.
  // -----------------------------------------------------------------------------

  /**
   * Returns defined fields.
   *
   * @return array
   *   A list of address field definitions.
   * @throws UcAddressesUndefinedFunctionException
   *   In case the function uc_addresses_get_address_fields()
   *   does not exists.
   */
  public static function getDefinedFields() {
    if (!function_exists('uc_addresses_get_address_fields')) {
      throw new UcAddressesUndefinedFunctionException('Function uc_addresses_get_address_fields() does not exists.');
    }
    return uc_addresses_get_address_fields();
  }

  /**
   * Returns a handler instance.
   *
   * @param string $fieldName
   *   The field name to get a handler for.
   * @param string $context
   *   The context where the field is used for.
   *
   * @return UcAddressesFieldHandler
   *   An instance of UcAddressesFieldHandler.
   * @throws UcAddressesUndefinedFunctionException
   *   In case the function uc_addresses_get_address_field_handler()
   *   does not exists.
   */
  public function getHandler($fieldName, $context = 'default') {
    if (!function_exists('uc_addresses_get_address_field_handler')) {
      throw new UcAddressesUndefinedFunctionException('Function uc_addresses_get_address_field_handler() does not exists.');
    }
    return uc_addresses_get_address_field_handler($this, $fieldName, $context);
  }

  // -----------------------------------------------------------------------------
  // SCHEMA METHODS
  // -----------------------------------------------------------------------------

  /**
   * Get the aggregated schema address.
   *
   * @access protected
   * @return object
   *   The aggregated address object.
   */
  protected function getSchemaAddress() {
    return $this->schemaAddress;
  }

  /**
   * Set the aggregated schema address.
   *
   * @param object $address
   *   The address object to wrap.
   *
   * @access protected
   * @return void
   */
  protected function setSchemaAddress($address) {
    if (is_object($address)) {
      $this->schemaAddress = $address;
      $this->setDirty();
    }
  }

  /**
   * Returns TRUE if field is part of the schema.
   *
   * @param string $fieldName
   *   The name of the field whose existence we want to check.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if addresses have a field with the given name.
   *   FALSE otherwise.
   */
  static public function schemaFieldExists($fieldName) {
    $schema = drupal_get_schema('uc_addresses');
    if (!empty($schema['fields']) && is_array($schema['fields'])) {
      return isset($schema['fields'][$fieldName]);
    }
    return FALSE;
  }

  /**
   * Throws an exception if the schema field does not exist.
   *
   * @param $fieldName
   *   The name of the field whose existence is required.
   *
   * @access private
   * @static
   * @return void
   * @throws UcAddressInvalidFieldException
   *   When the schema field does not exists.
   */
  static private function schemaFieldMustExist($fieldName) {
    if (!self::schemaFieldExists($fieldName)) {
      throw new UcAddressesInvalidFieldException(t('Invalid schema field name %name', array('%name' => $fieldName)));
    }
  }

  /**
   * Checks if the schema address of the given address
   * is equal to the schema address of this.
   *
   * @param UcAddressesSchemaAddress $address
   *   The address to compare against.
   *
   * @access public
   * @return boolean
   *   TRUE if the addresses are considered equal.
   *   FALSE otherwise.
   */
  public function compareAddress(UcAddressesSchemaAddress $address) {
    $fields_to_compare = &drupal_static('UcAddressesSchemaAddress::compareAddress', array());

    if ($address === $this) {
      // No comparison needed. Given address object is exactly the same.
      return TRUE;
    }

    $fieldsDataThisAddress = $this->getRawFieldData();
    $fieldsDataOtherAddress = $address->getRawFieldData();

    // Find out which field to compare.
    if (count($fields_to_compare) < 1) {
      $fields_data = self::getDefinedFields();
      foreach ($fields_data as $fieldName => $field_data) {
        if ($field_data['compare']) {
          $fields_to_compare[] = $fieldName;
        }
      }
    }

    foreach ($fields_to_compare as $fieldName) {
      if ($fieldsDataThisAddress[$fieldName] != $fieldsDataOtherAddress[$fieldName]) {
        return FALSE;
      }
    }
    return TRUE;
  }
}
