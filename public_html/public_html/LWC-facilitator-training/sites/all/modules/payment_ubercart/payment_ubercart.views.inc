<?php

/**
 * @file
 * Views integration.
 */

/**
 * Implements hook_views_data().
 */
function payment_ubercart_views_data() {
  $data['payment_ubercart'] = array(
    'table' => array(
      'base' => array(
        'title' => t('Payments and Ubercart orders'),
      ),
      'group' => t('Payment'),
      'join' => array(
        'payment' => array(
          'left_field' => 'pid',
          'field' => 'pid',
        ),
        'uc_orders' => array(
          'left_field' => 'order_id',
          'field' => 'uc_order_id',
        ),
      ),
    ),
  );

  return $data;
}

/**
 * Implements hook_views_data_alter().
 */
function payment_ubercart_views_data_alter(&$data) {
  $data['payment']['table']['join']['uc_orders'] = array(
    'left_table' => 'payment_ubercart',
    'left_field' => 'pid',
    'field' => 'pid',
  );
  $data['uc_orders']['table']['join']['payment'] = array(
    'left_table' => 'payment_ubercart',
    'left_field' => 'uc_order_id',
    'field' => 'order_id',
  );
}