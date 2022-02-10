<?php
/**
 * @file
 * Admin functions for adding, editing and deleting fields
 *
 * Form extra address fields at
 * /admin/store/settings/countries/fields
 * /admin/store/settings/countries/fields/add
 * /admin/store/settings/countries/fields/#/edit
 */

// -------------------------------------------------------------------
// EXTRA ADDRESS FIELDS FORM
// - uc_extra_fields_pane_addressfield_form
// -------------------------------------------------------------------

/**
 * Form to add/edit extra address fields
 * Form at /admin/store/settings/countries/fields/add
 * @param array $form_state
 * @param object $field
 * @return array
 * @see uc_extra_fields_pane_addressfield_form_submit()
 */
function uc_extra_fields_pane_addressfield_form($form, &$form_state, $field = NULL) {
  if (empty($field) || empty($field->field_id)) {
    $field = new UCXF_AddressField();
    drupal_set_title(t('Add an address field'));
  }
  return uc_extra_fields_pane_field_edit_form($form_state, $field);
}

// -----------------------------------------------------------------------------
// EDIT FORMS
// -----------------------------------------------------------------------------

/**
 * A form callback to edit a field.
 * @param array $form_state
 * @param UCXF_Field $field
 * @return array
 */
function uc_extra_fields_pane_field_edit_form($form_state, $field) {
  $form = $field->edit_form();
  $form['#validate'][] = 'uc_extra_fields_pane_field_edit_form_validate';
  $form['#submit'][] = 'uc_extra_fields_pane_field_edit_form_submit';
  return $form;
}

/**
 * Validate the field edit form.
 * @param array $form
 * @param array $form_state
 * @return void
 */
function uc_extra_fields_pane_field_edit_form_validate($form, &$form_state) {
  $field = $form_state['values']['field'];
  $field->edit_form_validate($form, $form_state);
}

/**
 * Submit the field edit form.
 * @param array $form
 * @param array $form_state
 * @return void
 */
function uc_extra_fields_pane_field_edit_form_submit($form, &$form_state) {
  $field = $form_state['values']['field'];
  $field->edit_form_submit($form, $form_state);
}

/**
 * Fixes the option list for the radios field 'value_type' after the form
 * is build.
 * @param array $radio_field
 * @return array
 */
function uc_extra_fields_pane_field_value_type_after_build(&$radios_field) {
  // Rearrange the options
  foreach (element_children($radios_field) as $option_id) {
    $option = $radios_field[$option_id];
    $radios_field[$option_id]['#description'] = $option['#title']['#description'];
    $radios_field[$option_id]['#title'] = $option['#title']['#title'];
  }
  return $radios_field;
}

// -----------------------------------------------------------------------------
// DELETE FORMS
// -----------------------------------------------------------------------------

/**
 * Ask confirmation for deletion of a field.
 * @param array $form_state
 * @param UCXF_Field $field
 * @return array
 */
function uc_extra_fields_pane_field_delete_confirm_form($form, &$form_state, $field) {
  $form['field'] = array(
    '#type' => 'value',
    '#value' => $field,
  );

  return confirm_form(
    $form,
    t('Are you sure you want to remove the field %name?', array('%name' => $field->db_name)),
      $field->returnpath,
    t('This action cannot be undone.'),
    t('Remove'),
    t('Cancel')
  );
}

/**
 * Delete a field after confirmation.
 * @param array $form
 * @param array $form_state
 * @return void
 */
function uc_extra_fields_pane_field_delete_confirm_form_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    $field = $form_state['values']['field'];
    $field->delete();
    drupal_set_message(t('Field %name deleted.', array('%name' => $field->db_name)));
  }
  $form_state['redirect'] = $field->returnpath;
}
