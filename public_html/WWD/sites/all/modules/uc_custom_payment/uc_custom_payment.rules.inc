<?php
/**
 * @file
 * Rules hook implementations for uc_custom_payment
 */
/**
 * Implements hook_rules_condition_info().
 */
function uc_custom_payment_rules_condition_info() {
  $info = array();
  $info['uc_custom_payment_method_check'] = array(
    'label' => t("Check an order's payment method"),
    'group' => t('Order'),
    'parameter' => array(
      'order' => array(
        'type' => 'uc_order',
        'label' => t('Order'),
      ),
      'method' => array(
        'type' => 'text',
        'options list' => 'uc_custom_payment_method_options',
        'label' => t('Payment method'),
        'description' => t('This condition will evaluate to %true for all orders with the payment method specified below.', array('%true' => 'TRUE')),
      ),
    ),
  );
  return $info;
}

/**
 * Implements hook_rules_action_info().
 */
function uc_custom_payment_rules_action_info() {
  $info = array();
  $info['uc_custom_payment_enter'] = array(
    'label' => t("Enter a payment for an order"),
    'group' => t('Payment'),
    'parameter' => array(
      'order' => array(
        'type' => 'uc_order',
        'label' => t('Order'),
      ),
      'amount' => array(
        'type' => 'decimal',
        'label' => t('Amount'),
        'description' => t('The amount of the payment to enter'),
      ),
      'comment' => array(
        'type' => 'text',
        'label' => t('Comment'),
        'description' => t('The comment to add to the order when this payment is entered'),
        'default value' => t('Payment entered automatically.'),
      ),
    ),
  );
  return $info;
}

/**
 * Condition callback for uc_custom_payment_method_check.
 *
 * @param $order
 *   The order whose payment method should be checked
 * @param $method
 *   The method to check for.
 */
function uc_custom_payment_method_check($order, $method) {
  return !empty($order->payment_method) && $order->payment_method == $method;
}

/**
 * Action callback fro uc_custom_payment_enter.
 *
 * @param $order
 *   The order for which to enter a payment.
 * @param $amount
 *   The amount of the payment.
 * @param $comment
 *   The comment to log for this payment.
 */
function uc_custom_payment_enter($order, $amount, $comment) {
  global $user;
  uc_payment_enter($order->order_id, $order->payment_method, $amount, $user->uid, NULL, $comment);
}

/**
 * Options callback for uc_custom_payment_method_check.
 */
function uc_custom_payment_method_options() {
  $options = array();
  $methods = _uc_payment_method_list();
  foreach ($methods as $key => $method) {
    $options[$key] = $method['name'];
  }
  return $options;
}
