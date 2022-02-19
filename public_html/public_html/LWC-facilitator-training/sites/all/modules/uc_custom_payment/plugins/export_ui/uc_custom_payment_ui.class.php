<?php
/**
 * @file
 * CTools exportable UI handler class for uc_custom_payment.
 */
/**
 * CTools exportable UI handler class for uc_custom_payment.
 */
class uc_custom_payment_ui extends ctools_export_ui {
  /**
   * @see ctools_export_ui::edit_form()
   */
  function edit_form(&$form, &$form_state) {
    parent::edit_form($form, $form_state);
    $form['#attached']['js'] = array(
      drupal_get_path('module', 'uc_custom_payment') . '/plugins/export_ui/uc_custom_payment_ui.js',
    );
    $form['info']['admin_title']['#title'] = t('Name');
    $form['info']['admin_title']['#required'] = TRUE;
    $form['info']['admin_title']['#description'] = t('The name of this payment method as it will appear in administrative lists');
    $form['info']['name']['#machine_name']['exists'] = 'uc_custom_payment_name_exists';
    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => empty($form_state['item']->title) ? '' : $form_state['item']->title,
      '#description' => t('The title of this payment method as it will appear to the customer.'),
      '#required' => TRUE,
    );
    $form['instructions'] = array(
      '#type' => 'text_format',
      '#title' => t('Instructions'),
      '#default_value' => empty($form_state['item']->instructions) ? '' : $form_state['item']->instructions['value'],
      '#format' => empty($form_state['item']->instructions) ? filter_default_format() : $form_state['item']->instructions['format'],
      '#description' => t('The instructions for this payment method which will appear when the method is selected.'),
    );
    if (module_exists('token')) {
      $form['instructions']['#description'] .= ' ' . t('You may use any of the following replacement patterns.');
      $form['instructions']['#suffix'] = theme('token_tree', array('token_types' => array('uc_order')));
    }

    $default = '';
    if (!empty($form_state['item']->data['service_charge'])) {
      $default = $form_state['item']->data['service_charge'];
      if ($form_state['item']->data['service_charge_type'] === 'percentage') {
        $default .= '%';
      }
    }
    $form['service_charge'] = array(
      '#type' => 'textfield',
      '#title' => t('Service Charge'),
      '#field_prefix' => variable_get('uc_sign_after_amount', FALSE) ? '' : variable_get('uc_currency_sign', '$'),
      '#field_suffix' => variable_get('uc_sign_after_amount', FALSE) ? variable_get('uc_currency_sign', '$') : '',
      '#default_value' => $default,
      '#description' => t('The service charge to be applied to the order when this payment method is selected.
        May be an absolute price or percent of order total (e.g. enter "15%"). Leave blank for no service charge.'),
    );
    $form['service_charge_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Service Charge Title'),
      '#default_value' => empty($form_state['item']->data['service_charge_title']) ? t('Service charge') : $form_state['item']->data['service_charge_title'],
      '#description' => t('The line item title to use for the service charge.'),
      '#states' => array(
        'visible' => array(
          'input[name="service_charge"]' => array('filled' => TRUE),
        ),
      ),
    );
  }

  function edit_form_submit($form, &$form_state) {
    parent::edit_form_submit($form, $form_state);
    $type = substr($form_state['values']['service_charge'], -1) == '%' ? 'percentage' : 'price';
    $amount = str_replace(array('%', '='), '', $form_state['values']['service_charge']);
    $form_state['item']->data = array(
      'service_charge' => trim($amount),
      'service_charge_type' => $type,
      'service_charge_title' => $form_state['values']['service_charge_title'],
    );
  }

   /**
   * @see ctools_export_ui::list_page()
   */
  function list_page($js, $input) {
    module_load_include('inc', 'uc_payment', 'uc_payment.admin');
    return drupal_get_form('uc_payment_methods_form');
  }
}

/**
 * @see ctools_export_ui_edit_name_exists()
 */
function uc_custom_payment_name_exists($name, $element, &$form_state) {
  if (!ctools_export_ui_edit_name_exists($name, $element, $form_state)) {
    return array_key_exists($name, _uc_payment_method_list());
  }
  else {
    return TRUE;
  }
}

