<?php
/**
 * @file
 * Hook implementations for the Ubercart Addresses module.
 */

/**
 * Implements hook_uc_addresses_fields().
 *
 * Register all address fields used by Ubercart Addresses.
 */
function uc_extra_fields_pane_uc_addresses_fields() {
  try {
    $data = array();
    $fields = UCXF_FieldList::getAllAddressFields();

    foreach ($fields as $field) {
      $data[$field->db_name] = array(
        'title' => $field->output('label'),
        'handler' => 'UcAddressesUCXFHandler',
        'display_settings' => array(
          'default' => TRUE,
          'address_view' => $field->may_display('uc_addresses'),
          'checkout_review' => $field->may_display('review'),
          'order_view' => $field->may_display('order'),
        ),
      );
      // Specify data type. Defaults to 'text'.
      switch ($field->value_type) {
        case UCXF_Field::UCXF_WIDGET_TYPE_CHECKBOX:
          $data[$field->db_name]['type'] = 'boolean';
          break;
        default:
          $data[$field->db_name]['type'] = 'text';
          break;
      }
      // Entity API integration: specify option list for select
      // fields.
      switch ($field->value_type) {
        case UCXF_Field::UCXF_WIDGET_TYPE_SELECT:
        case UCXF_Field::UCXF_WIDGET_TYPE_PHP_SELECT:
          $data[$field->db_name]['options list'] = 'uc_extra_fields_pane_option_list';
          break;
      }
    }

    return $data;
  }
  catch (UCXF_Exception $e) {
    $e->printMessage();
    $e->logError();
  }
}

/**
 * Implements hook_uc_addresses_field_handlers().
 *
 * Register form fields and their definitions.
 */
function uc_extra_fields_pane_uc_addresses_field_handlers() {
  $path = drupal_get_path('module', 'uc_extra_fields_pane') . '/includes';

  $info = array();
  $info['UcAddressesUCXFHandler'] = array(
    'hidden' => FALSE,
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesUCXFHandler',
      'file' => 'uc_addresses.handlers.inc',
      'path' => $path,
    ),
  );

  return $info;
}

/**
 * Implements hook_uc_addresses_address_load().
 */
function uc_extra_fields_pane_uc_addresses_address_load($address, $obj) {
  // Load values
  $value_list = uc_extra_fields_pane_value_list_load($address->getId(), UCXF_Value::UCXF_VALUE_ADDRESS);

  foreach ($value_list as $oValue) {
    $address->setField($oValue->db_name, $oValue->getValue());
  }
}

/**
 * Implements hook_uc_addresses_address_insert().
 */
function uc_extra_fields_pane_uc_addresses_address_insert($address) {
  // Load fields for uc_addresses.
  $fields = UCXF_FieldList::getAllAddressFields();

  foreach ($fields as $field) {
    uc_extra_fields_pane_value_save(
      array(
        'element_id' => $address->getId(),
        'element_type' => UCXF_Value::UCXF_VALUE_ADDRESS,
        'field_id' => $field->field_id,
        'value' => $address->getField($field->db_name),
      )
    );
  }
}

/**
 * Implements hook_uc_addresses_address_update().
 */
function uc_extra_fields_pane_uc_addresses_address_update($address) {
  // Do the same as when an address is inserted.
  uc_extra_fields_pane_uc_addresses_address_insert($address);
}

/**
 * Implements hook_uc_addresses_address_delete().
 */
function uc_extra_fields_pane_uc_addresses_address_delete($address) {
  // Delete all values associated with this address.
  db_delete('uc_extra_fields_values')
    ->condition('element_id', $address->getId())
    ->condition('element_type', UCXF_Value::UCXF_VALUE_ADDRESS)
    ->execute();
}

/**
 * Implements hook_uc_addresses_preprocess_address_alter().
 *
 * Don't display address fields in panes if is set they shouldn't
 * appear in that pane. (UCXF_AddressField->pane_types setting)
 */
function uc_extra_fields_pane_uc_addresses_preprocess_address_alter(&$fields, $address, $context) {
  if (!empty($address->ucxf_pane_type)) {
    // Check which fields may be displayed in one pane only.
    // Remove fields that may not be shown in the active pane.
    $ucxf_fields = UCXF_FieldList::getAllAddressFields();
    foreach ($ucxf_fields as $ucxf_field) {
      if (isset($fields[$ucxf_field->db_name]) && !$ucxf_field->in_pane($address->ucxf_pane_type)) {
        unset($fields[$ucxf_field->db_name]);
      }
    }
  }
}
