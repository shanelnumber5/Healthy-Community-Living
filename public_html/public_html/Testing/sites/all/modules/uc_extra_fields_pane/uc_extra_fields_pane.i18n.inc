<?php

/**
 * @file
 * Internationalization (i18n) hooks.
 */

/**
 * Implements hook_i18n_string_info().
 *
 * Registers Ubercart Extra Fields text group.
 *
 * @return array
 *   An array of text groups.
 */
function uc_extra_fields_pane_i18n_string_info() {
  $groups['ucxf'] = array(
    'title' => t('Ubercart Extra Fields'),
    'description' => t('Configurable fields descriptions, defaults, options, etc.'),
    'format' => FALSE, // This group doesn't have formatted strings.
    'list' => TRUE, // This group can list all strings.
  );
  return $groups;
}

/**
 * Implements hook_i18n_string_list().
 *
 * Provides a list of translatable strings for the ucxf text group.
 *
 * @param $group
 *   The text group name.
 *
 * @return array
 *   A list of translatable strings, keyed by text group.
 */
function uc_extra_fields_pane_i18n_string_list($group) {
  if ($group == 'ucxf') {
    $strings = array();
    $fields = UCXF_FieldList::getAllFields();
    foreach ($fields as $field) {
      $strings[$field->db_name] = uc_extra_fields_pane_i18n_get_strings($field);
    }
    $return = array(
      'ucxf' => array(
        'field' => $strings,
      ),
    );
    return $return;
  }
}

/**
 * Returns translatable strings for an UCXF Field.
 *
 * @param UCXF_Field $field
 *   The field to get strings for.
 *
 * @return array
 *   Translatable strings, keyed by string ID.
 */
function uc_extra_fields_pane_i18n_get_strings($field) {
  $strings = array(
    'label' => $field->label,
    'description' => $field->description,
  );

  switch ($field->value_type) {
    // Make labels of options in select fields translatable.
    case UCXF_Field::UCXF_WIDGET_TYPE_SELECT:
    case UCXF_Field::UCXF_WIDGET_TYPE_PHP_SELECT:
      $values = $field->generate_value(FALSE);
      foreach ($values as $key => $label) {
        $strings['value:' . $key] = $label;
      }
      break;
    // Make default values translatable.
    case UCXF_Field::UCXF_WIDGET_TYPE_TEXTFIELD:
    case UCXF_Field::UCXF_WIDGET_TYPE_PHP:
    case UCXF_Field::UCXF_WIDGET_TYPE_CONSTANT:
      $value = $field->generate_value(FALSE);
      if (!empty($value)) {
        $strings['value'] = $value;
      }
      break;
  }

  return $strings;
}
