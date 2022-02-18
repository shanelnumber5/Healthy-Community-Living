<?php

/**
 * Accordions administration form..
 */
function accordions_admin_form($form, &$form_state) {
  accordions_reset_types_cache();
  $info = accordions_get_types(NULL);
  $options = array();
  foreach ($info as $type => $type_info) {
    $options[$type] = $type_info['name'];
  }

  $form['accordions_enabled_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Allowed types of accordions'),
    '#description' => t('Content of the allowed types can be set to act as accordions (but will act normally by default even so). Disable any that will not be used to improve performance.'),
    '#options' => $options,
    '#default_value' => variable_get('accordions_enabled_types', array()),
  );

  $form['#submit'][] = 'accordions_admin_form_submit';

  return system_settings_form($form);
}

/**
 * Submit callback for Foo form.
 */
function accordions_admin_form_submit(&$form, &$form_state) {
  // Reset our cache of accordions types.
  accordions_reset_types_cache();
  // Reset the module_implements cache.
  module_implements('', FALSE, TRUE);
  // Reset the theme registry.
  drupal_theme_rebuild();
}
