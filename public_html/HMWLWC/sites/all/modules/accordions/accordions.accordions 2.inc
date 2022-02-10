<?php

/**
 * Implements hook_accordions_types().
 */
function accordions_accordions_types() {
  $types = array();

  $types['block'] = array(
    'name' => t('Blocks'),
    'hooks' => array(
      'accordions' => array(
        'preprocess_block',
        'form_block_admin_configure_alter',
        'form_block_add_block_form_alter',
      ),
    ),
    'theme hooks' => array(
      'block' => array(
        'preprocess functions' => array(
          'accordions_preprocess_block'
        ),
      ),
    ),
    'label selector' => 'h2',
    'content selector' => '.content',
    // The module key is only needed if the values callback is in the module's
    // MODULE.accordions.inc include file.
    'module' => 'accordions',
    'values callback' => 'accordions_block_values_callback',
  );

  /*$types['entity'] = array(
    'module' => 'accordions',
    'name' => t('Entities'),
  );

  $types['field'] = array(
    'module' => 'accordions',
    'name' => t('Fields'),
    'dependencies' => array(
      'field',
    ),
  );

  $types['field_values'] = array(
    'module' => 'accordions',
    'name' => t('Field values'),
    'dependencies' => array(
      'field',
    ),
  );

  $types['views_rows'] = array(
    'module' => 'accordions',
    'name' => t('Views rows'),
    'dependencies' => array(
      'views',
    ),
  );*/

  return $types;
}

/**
 * Values callback that returns an array of type, subtype & ID from submitted
 * form values.
 */
function accordions_block_values_callback($form, $form_state) {
  return array(
    'type' => 'block',
    'subtype' => $form_state['values']['module'],
    'id' => $form_state['values']['delta'],
  );
}
