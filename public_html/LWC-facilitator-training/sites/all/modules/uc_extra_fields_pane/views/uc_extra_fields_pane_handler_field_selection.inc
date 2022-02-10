<?php
/**
 * @file
 * Views field handler for Extra Fields Pane selection field types.
 */

/**
 * Display the label of select fields instead of the saved key.
 */
class uc_extra_fields_pane_handler_field_selection extends views_handler_field {
  /**
   * Render the field.
   *
   * @param $values
   *   The values retrieved from the database.
   */
  function render($values) {
    $value = $values->{$this->field_alias};
    $field = uc_extra_fields_pane_field_load($this->definition['field_id']);
    return $field->output_value($value);
  }
}