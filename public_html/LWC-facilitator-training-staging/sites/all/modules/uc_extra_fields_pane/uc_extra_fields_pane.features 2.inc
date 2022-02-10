<?php
/**
 * @file
 * Features integration.
 */

/**
 * Implements hook_features_api().
 */
function uc_extra_fields_pane_features_api() {
  return array(
    'uc_extra_fields_pane_field' => array(
      'name' => t('Extra Fields Pane field'),
      'feature_source' => TRUE,
      'default_hook' => 'uc_extra_fields_pane_default_fields',
      'default_file' => FEATURES_DEFAULTS_INCLUDED,
      'file' => drupal_get_path('module', 'uc_extra_fields_pane') . '/uc_extra_fields_pane.features.inc',
    ),
  );
}

/**
 * Implements hook_features_export().
 */
function uc_extra_fields_pane_field_features_export($data, &$export, $module_name = '') {
  $export['dependencies']['features'] = 'features';
  $export['dependencies']['uc_extra_fields_pane'] = 'uc_extra_fields_pane';
  $export['package'] = 'Ubercart - extra';
  foreach ($data as $field) {
    $export['features']['uc_extra_fields_pane_field'][$field] = $field;
  }
  return array();
}

/**
 * Implements hook_features_export_options().
 */
function uc_extra_fields_pane_field_features_export_options() {
  $fields = array();
  $result = db_select('uc_extra_fields', 'ucxf')
    ->fields('ucxf', array('db_name', 'label'))
    ->execute();
  while ($field = $result->fetch()) {
    $fields[$field->db_name] = $field->label;
  }
  return $fields;
}

/**
 * Implements hook_features_export_render().
 */
function uc_extra_fields_pane_field_features_export_render($module, $data) {
  $code = array();
  $code[] = '  $uc_extra_fields_pane_fields = array();';
  $code[] = '';

  if (count($data) > 1) {
    // If we export more than one field, than load all fields first.
    UCXF_FieldList::getAllFields();
  }

  foreach ($data as $name) {
    if ($field = UCXF_FieldList::getFieldByName($name)) {
      $field_data = $field->to_array();
      unset($field_data['field_id']);
      $code[] = "  \$uc_extra_fields_pane_fields['{$name}'] = " . features_var_export($field_data) . ";";
    }
  }
  $code[] = "return \$uc_extra_fields_pane_fields;";
  $code = implode("\n", $code);

  return array('uc_extra_fields_pane_default_fields' => $code);
}

/**
 * Implements hook_features_revert().
 */
function uc_extra_fields_pane_field_features_revert($module) {
  uc_extra_fields_pane_field_features_rebuild($module);
}

/**
 * Implements hook_features_rebuild().
 */
function uc_extra_fields_pane_field_features_rebuild($module) {
  if ($defaults = features_get_default('uc_extra_fields_pane_field', $module)) {
    // Load all fields first.
    UCXF_FieldList::getAllFields();
    foreach ($defaults as $field_data) {
      $field = UCXF_FieldList::getFieldByName($field_data['db_name']);
      // If the field doesn't exist, then create it.
      if (!$field) {
        $field = UCXF_FieldList::createField($field_data['pane_type']);
      }
      // Set the field's data and save the field.
      $field->from_array($field_data);
      $field->save();
    }
  }
}
