<?php
/**
 * @file
 * Rules defaults for uc_custom_payment.
 */

/**
 * Implements hook_default_rules_configuration_alter().
 */
function uc_custom_payment_default_rules_configuration_alter(&$configs) {
  if (!empty($configs['uc_payment_method_free_order'])) {
    $configs['uc_payment_method_free_order']->condition(rules_condition('uc_payment_condition_order_balance', array(
      'order:select' => 'order',
      'balance_comparison' => 'less_equal',
    )));
  }
    return;
  $config = rules_and(array(
    'order' => array('type' => 'uc_order', 'label' => t('Order')),
  ));
  $config->label = t('Free Order conditions');
  $config->condition(rules_condition('uc_payment_condition_order_balance', array(
    'order:select' => 'order',
    'balance_comparison' => 'less_equal',
  )));
  $configs['uc_payment_method_free_order'] = $config;
}

/**
 * Implements hook_default_rules_configuration().
 */
function uc_custom_payment_default_rules_configuration() {
  $configs = array();

  $config = rules_reaction_rule();
  $config->label = t('Enter a payment when a free order is completed.');
  $config->event('uc_checkout_complete')
    ->condition('uc_custom_payment_method_check', array(
      'order:select' => 'order',
      'method' => 'free_order',
    ))
    ->action('uc_custom_payment_enter', array(
      'order:select' => 'order',
      'amount' => 0,
      'comment' => t('Checkout completed for a free order.'),
    ));
  $configs['free_order_payment'] = $config;

  return $configs;
}
