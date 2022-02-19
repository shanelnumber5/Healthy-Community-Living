<?php
/**
 * @file
 * Provide views data and handlers for Extra Fields Pane
 */

/**
 * Implementation of hook_views_data().
 */
function uc_extra_fields_pane_views_data() {
  $data = array();
  // Define the base group of this table.
  $data['uc_extra_fields_values']['table']['group']  = t('Ubercart order');

  $data['uc_extra_fields_values']['table']['join']['uc_order_products'] = array(
    'left_field' => 'order_id',
    'field' => 'element_id',
  );
  $data['uc_extra_fields_values']['table']['join']['uc_orders'] = array(
    'left_field' => 'order_id',
    'field' => 'element_id',
  );
  $data['uc_extra_fields_values']['table']['join']['uc_addresses'] = array(
    'left_field' => 'aid',
    'field' => 'element_id',
  );

  $panes = uc_extra_fields_pane_views_get_fields();
  foreach ($panes as $pane_type => $fields) {
    switch ($pane_type) {
      case 'extra_delivery':
      case 'extra_billing':
        // Fields for Ubercart order and Ubercart Order Products.
        foreach ($fields as $field) {
          $table_name = 'uc_extra_fields_pane_values_' . $field->db_name . '_' . $field->suffix;
          $data[$table_name] = array(
            'table' => array(
              'group' => t('Order') . ': ' . $field->prefix,
              'join' => array(
                'uc_orders' => array(
                  'table' => 'uc_extra_fields_values',
                  'left_table' => 'uc_orders',
                  'left_field' => 'order_id',
                  'field' => 'element_id',
                  'extra' => array(
                    array('field' => 'field_id', 'value' => $field->field_id),
                    array('field' => 'element_type', 'value' => $field->element_type),
                  ),
                ),
                'uc_order_products' => array(
                  'table' => 'uc_extra_fields_values',
                  'left_table' => 'uc_order_products',
                  'left_field' => 'order_id',
                  'field' => 'element_id',
                  'extra' => array(
                    array('field' => 'field_id', 'value' => $field->field_id),
                    array('field' => 'element_type', 'value' => $field->element_type),
                  ),
                ),
              ),
            ),
          );
          // All fields in the table are named 'value'.
          $data[$table_name]['value'] = uc_extra_fields_pane_views_fetch_field($field);
        }
        break;

      case 'extra_address':
        // Fields for Ubercart Addresses
        foreach ($fields as $field) {
          $table_name = 'uc_extra_fields_pane_values_' . $field->db_name;
          $data[$table_name] = array(
            'table' => array(
              'group' => t('Ubercart Addresses'),
              'join' => array(
                'uc_addresses' => array(
                  'table' => 'uc_extra_fields_values',
                  'left_table' => 'uc_addresses',
                  'left_field' => 'aid',
                  'field' => 'element_id',
                  'extra' => array(
                    array('field' => 'field_id', 'value' => $field->field_id),
                    array('field' => 'element_type', 'value' => $field->element_type),
                  ),
                ),
              ),
            ),
          );
          // All fields in the table are named 'value'.
          $data[$table_name]['value'] = uc_extra_fields_pane_views_fetch_field($field);
        }
        break;
    }
  }
  return $data;
}

/**
 * Get all uc_extra_fields_pane fields.
 *
 * @return array
 */
function uc_extra_fields_pane_views_get_fields() {
  $fields = &drupal_static(__FUNCTION__);

  if (!isset($fields)) {
    // Make sure all fields are loaded.
    $ucxf_fields = UCXF_FieldList::getAllFields();

    $fields = array();
    $fields_by_pane = array(
      'extra_delivery' => UCXF_FieldList::getFieldsFromPane('extra_delivery'),
      'extra_billing' => UCXF_FieldList::getFieldsFromPane('extra_billing'),
      'extra_address' => UCXF_FieldList::getAllAddressFields(),
    );

    foreach ($fields_by_pane as $pane_type => $fieldlist) {
      foreach ($fieldlist as $fieldname => $field) {
        $fields[$pane_type][$fieldname] = clone $field;
        switch ($pane_type) {
          case 'extra_delivery':
            $fields[$pane_type][$fieldname]->element_type = UCXF_Value::UCXF_VALUE_ORDER_DELIVERY;
            $fields[$pane_type][$fieldname]->prefix = t('Delivery address');
            $fields[$pane_type][$fieldname]->suffix = 'delivery';
            break;
          case 'extra_billing':
            $fields[$pane_type][$fieldname]->element_type = UCXF_Value::UCXF_VALUE_ORDER_BILLING;
            $fields[$pane_type][$fieldname]->prefix = t('Billing address');
            $fields[$pane_type][$fieldname]->suffix = 'billing';
            break;
          case 'extra_address':
            $fields[$pane_type][$fieldname]->element_type = UCXF_Value::UCXF_VALUE_ADDRESS;
            break;
        }
      }
    }
  }
  return $fields;
}

/**
 * Add uc_extra_fields_pane fields to view table.
 *
 * @return array
 */
function uc_extra_fields_pane_views_fetch_field($field) {
  $data = array(
    'title' => $field->output('label'),
    'help' => $field->output('description'),
  );

  // Add fields specific to the uc_extra_fields_pane type.
  switch ($field->value_type) {
    case UCXF_Field::UCXF_WIDGET_TYPE_CHECKBOX:
      if (!$data['help']) {
        $data['help'] = t('Extra Fields Pane !fieldtype', array('!fieldtype' => t('Checkbox')));
      }
      $data += array(
        'field' => array(
          'handler' => 'views_handler_field_boolean',
          'click sortable' => TRUE,
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_boolean_operator',
          'label' => t('Checked'),
          'type' => 'yes-no',
        ),
      );
      break;

    case UCXF_Field::UCXF_WIDGET_TYPE_SELECT:
    case UCXF_Field::UCXF_WIDGET_TYPE_PHP_SELECT:
      if (!$data['help']) {
        $data['help'] = t('Extra Fields Pane !fieldtype', array('!fieldtype' => t('Select list')));
      }
      $data += array(
        'field' => array(
          'handler' => 'uc_extra_fields_pane_handler_field_selection',
          'click sortable' => TRUE,
          'field_id' => $field->field_id,
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
        'filter' => array(
          'handler' => 'uc_extra_fields_pane_handler_filter_selection',
          'field_id' => $field->field_id,
        ),
        'argument' => array(
          'handler' => 'views_handler_argument_string',
        ),
      );
      break;

    default:
      if (!$data['help']) {
        $data['help'] = t('Extra Fields Pane !fieldtype', array('!fieldtype' => t('Textfield')));
      }
      $data += array(
        'field' => array(
          'handler' => 'views_handler_field',
          'click sortable' => TRUE,
        ),
        'sort' => array(
          'handler' => 'views_handler_sort',
        ),
        'filter' => array(
          'handler' => 'views_handler_filter_string',
        ),
        'argument' => array(
          'handler' => 'views_handler_argument_string',
        ),
      );
      break;
  }

  return $data;
}

/**
 * Implementation of hook_views_handlers().
 *
 * @return array
 */
function uc_extra_fields_pane_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'uc_extra_fields_pane') . '/views',
    ),
    'handlers' => array(
      'uc_extra_fields_pane_handler_field_selection' => array(
        'parent' => 'views_handler_field',
      ),
      'uc_extra_fields_pane_handler_filter_selection' => array(
        'parent' => 'views_handler_filter_in_operator',
      ),
    ),
  );
}
