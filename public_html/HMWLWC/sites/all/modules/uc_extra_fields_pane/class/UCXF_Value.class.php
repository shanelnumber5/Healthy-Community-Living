<?php
/**
 * @file
 * Contains the UCXF_Value class.
 */

/**
 * This class is used to keep track of all field values currently loaded.
 */
class UCXF_Value {
  // -----------------------------------------------------------------------------
  // CONSTANTS
  // -----------------------------------------------------------------------------

  // Element types
  const UCXF_VALUE_ORDER_DELIVERY = 12;
  const UCXF_VALUE_ORDER_BILLING = 13;
  const UCXF_VALUE_ADDRESS = 21;

  // -----------------------------------------------------------------------------
  // STATIC PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * A multidimensional array with cached results, saved this way:
   * [element_id][element_type][field_id]
   *
   * @var array $loaded_values
   * @access private
   * @static
   */
  private static $loaded_values = array();

  /**
   * A multidimensional array that keeps track of which value lists
   * are loaded through load_list(), structured this way:
   * [element_id][element_type]
   *
   * @var array
   * @access private
   * @static
   */
  private static $loaded_lists = array();

  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * Whether or not this a new value
   *
   * Internal setting
   *
   * @var boolean $is_new
   * @access private
   */
  private $is_new;

  /**
   * The ID of the entity (order ID or address ID)
   *
   * Saved in uc_extra_fields_values table
   *
   * @var int $element_id
   * @access protected
   */
  protected $element_id;

  /**
   * Element type, this can be:
   * - Order info (11)
   * - Order delivery (12)
   * - Order billing (13)
   * - Ubercart Addresses address (21)
   *
   * Saved in uc_extra_fields_values table
   *
   * @var int $element_type
   * @access protected
   */
  protected $element_type;

  /**
   * The ID of the UCXF_Field
   *
   * Saved in uc_extra_fields_values table
   *
   * @var int $field_id
   * @access protected
   */
  protected $field_id;

  /**
   * Name of the UCXF_Field
   *
   * Saved in uc_extra_fields table
   *
   * @var string $db_name
   * @access protected
   */
  protected $db_name;

  /**
   * The value filled in by the user
   *
   * Saved in uc_extra_fields_values table
   *
   * @var string $value
   * @access protected
   */
  protected $value;

  // -----------------------------------------------------------------------------
  // CONSTRUCT
  // -----------------------------------------------------------------------------

  /**
   * UCXF_Value object constructor
   *
   * Constructor is private, if you want to create a new value,
   * call static method load() instead.
   *
   * @access private
   * @return void
   */
  private function __construct() {
    $this->is_new = TRUE;
    $this->value = '';
  }

  /**
   * Disallow cloning
   * @access private
   * @return void
   */
  private function __clone() { }

  // -----------------------------------------------------------------------------
  // GETTERS
  // -----------------------------------------------------------------------------

  /**
   * Get a member value
   *
   * @param string $member
   * @access public
   * @return mixed
   */
  public function __get($member) {
    switch ($member) {
      case 'db_name':
        if (empty($this->db_name)) {
          if ($oField = $this->getField()) {
            $this->db_name = $oField->db_name;
            return $oField->db_name;
          }
        }
        break;
    }
    if (isset($this->$member)) {
      return $this->$member;
    }
    return NULL;
  }

  /**
   * Get value
   * @access public
   * @return string
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Get field
   * @access public
   * @return UCXF_Field
   */
  public function getField() {
    if ($this->field_id) {
      return UCXF_FieldList::getFieldById($this->field_id);
    }
  }

  /**
   * Output value with filtering
   * @access public
   */
  public function output() {
    $field = $this->getField();
    if ($field instanceof UCXF_Field) {
      return $field->output_value($this->value);
    }
    return check_plain($this->value);
  }

  /**
   * Convert item to an array
   * @access public
   * @return array
   */
  public function toArray() {
    return array(
      'element_id' => $this->element_id,
      'element_type' => $this->element_type,
      'field_id' => $this->field_id,
      'db_name' => $this->db_name,
      'value' => $this->value,
    );
  }

  // -----------------------------------------------------------------------------
  // SETTERS
  // -----------------------------------------------------------------------------

  /**
   * Set value
   * @param mixed $value
   * @access public
   * @return void
   */
  public function setValue($value) {
    $this->value = (string) $value;
  }

  // -----------------------------------------------------------------------------
  // LOADING, SAVING, DELETING
  // -----------------------------------------------------------------------------

  /**
   * Loads value from database.
   *
   * @param int $element_id
   *  id of element, order_id or uc_addresses id.
   * @param int $element_type
   *  type of element: order or address.
   * @param int $field_id
   *  id of field as known in uc_extra_fields.
   *
   * @access public
   * @return UCXF_Value
   */
  public static function load($element_id, $element_type, $field_id) {
    // Check if value has already been loaded
    if (isset(self::$loaded_values[$element_id][$element_type][$field_id])) {
      return self::$loaded_values[$element_id][$element_type][$field_id];
    }
    $query = db_select('uc_extra_fields_values', 'ucxf_values');
    $query->innerJoin('uc_extra_fields', 'ucxf_fields', 'ucxf_values.field_id = ucxf_fields.field_id');
    $result = $query->condition('ucxf_values.element_id', $element_id)
      ->condition('ucxf_values.element_type', $element_type)
      ->condition('ucxf_values.field_id', $field_id)
      ->fields('ucxf_values', array('element_id', 'element_type', 'field_id', 'value'))
      ->fields('ucxf_fields', array('db_name'))
      ->execute();

    if ($result) {
      self::dbResultToValue($result);
    }
    // Check if we have a result now
    if (isset(self::$loaded_values[$element_id][$element_type][$field_id])) {
      return self::$loaded_values[$element_id][$element_type][$field_id];
    }
    // Create new UCXF_Value
    $oValue = new self();
    $oValue->element_id = $element_id;
    $oValue->element_type = $element_type;
    $oValue->field_id = $field_id;
    // Save this UCXF_Value in the list
    self::$loaded_values[$element_id][$element_type][$field_id] = $oValue;
    // Return the UCXF_Value
    return $oValue;
  }

  /**
   * Loads a list of values from database
   * @param int $element_id
   *  id of element, order_id or uc_addresses id
   * @param int $element_type
   *  type of element: order or address
   * @return array
   */
  public static function load_list($element_id, $element_type) {
    // Check if value list has already been loaded
    if (
      isset(self::$loaded_lists[$element_id][$element_type])
        && isset(self::$loaded_values[$element_id][$element_type])
        && self::$loaded_lists[$element_id][$element_type]
    ) {
      return self::$loaded_values[$element_id][$element_type];
    }
    $query = db_select('uc_extra_fields_values', 'ucxf_values');
    $query->innerJoin('uc_extra_fields', 'ucxf_fields', 'ucxf_values.field_id = ucxf_fields.field_id');
    $result = $query->condition('ucxf_values.element_id', $element_id)
      ->condition('ucxf_values.element_type', $element_type)
      ->fields('ucxf_values', array('element_id', 'element_type', 'field_id', 'value'))
      ->fields('ucxf_fields', array('db_name'))
      ->execute();

    if ($result) {
      self::dbResultToValue($result);
    }
    // Check if we have a result now
    if (isset(self::$loaded_values[$element_id][$element_type])) {
      // Set flag that the list is loaded.
      self::$loaded_lists[$element_id][$element_type] = TRUE;
      // Return the loaded list.
      return self::$loaded_values[$element_id][$element_type];
    }
    return array();
  }

  /**
   * Save value to database
   * @access public
   * @return boolean
   */
  public function save() {
    $update = array();
    if (!$this->is_new) {
      $update = array('element_id', 'element_type', 'field_id');
    }

    $data = $this->toArray();
    $result = drupal_write_record('uc_extra_fields_values', $data, $update);

    if ($result !== FALSE) {
      $this->is_new = FALSE;
    }
    return $result;
  }

  /**
   * Removes value from db
   *
   * @access public
   * @return boolean
   */
  public function delete() {
    $result = FALSE;
    if (!$this->is_new) {
      $result = db_delete('uc_extra_fields_values')
        ->condition('element_id', $this->element_id)
        ->condition('element_type', $this->element_type)
        ->condition('field_id', $this->field_id)
        ->execute();

      if ($result) {
        unset(self::$loaded_values[$this->element_id][$this->element_type][$this->field_id]);
      }
    }
    return $result;
  }

  // -----------------------------------------------------------------------------
  // PRIVATE METHODS
  // -----------------------------------------------------------------------------

  /**
   * Creates ucxf_value objects from a database resource.
   *
   * @param resource $result
   *   Database result
   * @access private
   * @return void
   */
  private static function dbResultToValue($result) {
    // Create each ucxf_value object from the database record
    while ($obj = $result->fetch()) {
      if (isset(self::$loaded_values[$obj->element_id][$obj->element_type][$obj->field_id])) {
        // This result is already available, don't overwrite it.
        continue;
      }
      $oValue = new self();
      $oValue->element_id = $obj->element_id;
      $oValue->element_type = $obj->element_type;
      $oValue->field_id = $obj->field_id;
      $oValue->db_name = $obj->db_name;
      $oValue->value = $obj->value;
      $oValue->is_new = FALSE;

      // Cache this result
      self::$loaded_values[$obj->element_id][$obj->element_type][$obj->field_id] = $oValue;
    }
  }
}
