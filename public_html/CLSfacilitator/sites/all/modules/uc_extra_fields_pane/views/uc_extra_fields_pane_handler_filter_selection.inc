<?php
/**
 * @file
 * Views filter handler for Extra Fields Pane selection field types.
 */

/**
 * Filter by a selection widget in case of a select field.
 */
class uc_extra_fields_pane_handler_filter_selection extends views_handler_filter_in_operator {
  function get_value_options() {
    if (isset($this->value_options)) {
      return;
    }

    $this->value_options = array();
    $field = uc_extra_fields_pane_field_load($this->definition['field_id']);
    $this->value_options = $field->generate_value();
  }
}