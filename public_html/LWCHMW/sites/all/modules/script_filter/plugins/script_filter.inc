<?php
/**
 * @file
 * Script filter module's wysywig plugin.
 */

/**
 * Implements hook_INCLUDE_plugin().
 *
 * @return array
 */
function script_filter_script_filter_plugin() {
  $plugins = array();

  $plugins['script_filter'] = array(
    'title' => t('Script filter'),
    'icon file' => 'script_filter.gif',
    'icon title' => t('Insert script tag.'),
    'settings' => array(
      'dialog' => array(
        'url' => base_path() . 'index.php?q=wysiwyg/script_filter',
        'width' => 620,
        'height' => 600,
      ),
    ),
  );

  return $plugins;
}

/**
 * Implements hook_wysiwyg_dialog().
 */
function script_filter_wysiwyg_dialog($instance) {
  drupal_set_title(t('Insert script code'));
  drupal_add_js(drupal_get_path('module', 'script_filter') . '/plugins/script_filter/dialog.js');

  $form = drupal_get_form('script_filter_wysiwyg_form');

  return drupal_render($form);
}

/**
 * Generates the form to insert the script code from the dialog window.
 */
function script_filter_wysiwyg_form($form, &$form_state) {
  $form['code'] = array(
    '#type' => 'textarea',
    '#title' => t('Script'),
  );

  $form['actions'] = array(
    '#type' => 'actions'
  );
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Insert'),
  );
  $form['actions']['cancel'] = array(
    '#markup' => l(t('Cancel'), '#', array('attributes' => array('id' => 'script-filter-dialog-cancel'))),
  );

  return $form;
}
