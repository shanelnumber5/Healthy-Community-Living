<?php

/**
 * @file
 * Ubercart callbacks for the checkout- and order panes.
 */

// ---------------------------------------------------------------------------
// UBERCART CALLBACKS
// checkout panes
// ---------------------------------------------------------------------------

/**
 * Gets the delivery information.
 *
 * Overrides uc_checkout_pane_delivery().
 *
 * @see uc_addresses_checkout_pane_address()
 */
function uc_addresses_checkout_pane_shipping($op, $order, $form = NULL, &$form_state = NULL) {
  $description = t('Enter your delivery address and information here.');
  $copy = t('My delivery information is the same as my billing information.');
  return uc_addresses_checkout_pane_address('shipping', $op, $order, $form, $form_state, $description, $copy);
}

/**
 * Gets the billing information.
 *
 * Overrides uc_checkout_pane_billing().
 *
 * @see uc_addresses_checkout_pane_address()
 */
function uc_addresses_checkout_pane_billing($op, $order, $form = NULL, &$form_state = NULL) {
  $description = t('Enter your billing address and information here.');
  $copy = t('My billing information is the same as my delivery information.');
  return uc_addresses_checkout_pane_address('billing', $op, $order, $form, $form_state, $description, $copy);
}

/**
 * Generic address pane handler.
 *
 * @param string $type
 *   The address type: billing or shipping.
 * @param string $op
 *   The operation that is being performed.
 * @param object $order
 *   The Ubercart order.
 * @param array $form
 *   The checkout form.
 * @param array $form_state
 *   The form state of the checkout form.
 * @param string $description
 *   The help text shown above the form.
 * @param string $copy
 *   The text shown for the "copy address" checkbox.
 *
 * @return mixed
 *   Depending on the operation, different results may be returned.
 */
function uc_addresses_checkout_pane_address($type, $op, $order, $form, &$form_state, $description, $copy) {
  global $user;

  if ($type == 'shipping') {
    $pane = 'delivery';
    $other_pane = 'billing';
    $order_fieldname_prefix = 'delivery';
    $other_order_fieldname_prefix = 'billing';
  }
  else {
    $pane = 'billing';
    $other_pane = 'delivery';
    $order_fieldname_prefix = 'billing';
    $other_order_fieldname_prefix = 'delivery';
  }

  // Source pane for "copy address" checkbox.
  $source = &drupal_static('uc_addresses_checkout_pane_address_source_pane');
  if (!isset($source)) {
    $source = $pane;
  }

  switch ($op) {
    case 'view':
      // Tell address book we want to get multiple addresses.
      UcAddressesAddressBook::get($user->uid)->setPerformanceHint(UcAddressesAddressBook::PERF_HINT_LOAD_ALL);

      // Initialize address variable.
      $address = NULL;

      // Get address for order if set.
      if (isset($order->uc_addresses[$type])) {
        $address = $order->uc_addresses[$type];
      }
      if (!$address) {
        $address = UcAddressesAddress::newAddress();
      }

      // Check if address is copied from source pane
      // (if the "copy address" checkbox was enabled).
      if (
        (isset($address->copy_address) && $address->copy_address)
        || !empty($form_state['values']['panes'][$pane]['copy_address'])
      ) {
        $copy_address_checked = TRUE;
      }
      elseif (
        // Check if the "Use the same address for billing and delivery by default."
        // setting is enabled. If so, then the checkbox should be automatically
        // enabled, but only if the setting has not (yet) been overidden by the form
        // state or the address' copy address setting. Also, the checkbox should only
        // be ticked for the "target" pane, not the "source" pane.
        (!isset($form_state['values']['panes'][$pane]['copy_address']) && !isset($address->copy_address))
        && variable_get('uc_cart_default_same_address', FALSE)
        && $source != $pane
      ) {
        $copy_address_checked = TRUE;
      }
      else {
        $copy_address_checked = FALSE;
      }

      if ($source != $pane) {
        $contents['copy_address'] = array(
          '#type' => 'checkbox',
          '#title' => $copy,
          '#ajax' => array(
            'callback' => 'uc_checkout_pane_address_render',
            'wrapper' => $pane . '-address-pane',
            'progress' => array(
              'type' => 'throbber',
            ),
          ),
          '#default_value' => $copy_address_checked,
        );
      }

      if ($user->uid) {
        $select = uc_addresses_select_addresses($user->uid, 'checkout_form', $pane);
        if ($select) {
          $contents['select_address'] = $select + array(
            '#ajax' => array(
              'callback' => 'uc_checkout_pane_address_render',
              'wrapper' => $pane . '-address-pane',
              'progress' => array(
                'type' => 'throbber',
              ),
              'event' => 'change',
            ),
            '#states' => array(
              'invisible' => array(
                'input[name="panes[' . $pane . '][copy_address]"]' => array('checked' => TRUE),
              ),
            ),
            '#uc_addresses_address_element' => array('panes', $pane, 'address'),
            '#uc_addresses_address_input' => array('panes', $pane, 'address'),
            '#default_value' => $address,
          );
        }
      }

      if (isset($form_state['triggering_element'])) {
        $element = &$form_state['triggering_element'];

        if ($element['#name'] == "panes[$pane][copy_address]") {
          $address_data = &$form_state['values']['panes'][$source];
          foreach ($address_data as $source_fieldname => $value) {
            if (substr($source_fieldname, 0, strlen($source)) == $source) {
              $fieldname = substr($source_fieldname, strlen($source . '_'));
              $target_fieldname = str_replace($source, $pane, $source_fieldname);
              $form_state['input']['panes'][$pane]['address'][$target_fieldname] = $value;
              $order->$target_fieldname = $value;
              if (UcAddressesSchemaAddress::fieldExists($fieldname)) {
                $address->setField($fieldname, $value);
              }
            }
          }
        }

        // Forget any previous Ajax submissions, as we send new default values.
        unset($form_state['uc_addresses_address']);
      }

      $contents['address'] = array(
        '#type' => 'uc_addresses_address',
        '#uc_addresses_context' => 'checkout_form',
        '#uc_addresses_address' => $address,
        '#key_prefix' => $pane,
        '#hidden' => $copy_address_checked && ($source != $pane),
        '#prefix' => '<div id="' . $pane . '-address-pane">',
        '#suffix' => '</div>',
      );
/*
      // Form element for debug purposes, displays the address that is taken.
      $contents['address']['display'] = array(
        '#type' => 'item',
        '#title' => t('Address'),
        '#markup' => (string) $address,
        '#weight' => 100,
      );
//*/

      return array('description' => $description, 'contents' => $contents);

    case 'process':
      $address = $form['panes'][$pane]['address']['#uc_addresses_address'];
      $address->op = $op;
      $panes = &$form_state['values']['panes'];

      if ($address->isNew() && !(isset($panes[$pane]['copy_address']) && $panes[$pane]['copy_address'])) {
        // Set flag that this address may be saved to the addressbook.
        $address->save_address = TRUE;
      }
      // Set flag for if this address is "copied" or not.
      if (isset($panes[$pane]['copy_address'])) {
        if (!empty($panes[$pane]['copy_address'])) {
          $address->copy_address = TRUE;
        }
        else {
          $address->copy_address = FALSE;
        }
      }

      foreach ($panes[$pane]['address'] as $target_fieldname => $value) {
        if (substr($target_fieldname, 0, strlen($pane)) == $pane) {
          if (!empty($panes[$pane]['copy_address'])) {
            $fieldname = substr($target_fieldname, strlen($pane . '_'));
            $source_fieldname = $source . '_' . $fieldname;
            if (isset($panes[$source]['address'][$source_fieldname])) {
              $value = $panes[$source]['address'][$source_fieldname];
              if (UcAddressesSchemaAddress::fieldExists($fieldname)) {
                $address->setField($fieldname, $value);
              }
            }
          }
          $order->$target_fieldname = $value;
        }
      }

      // Temporary save address with order.
      $order->uc_addresses[$type] = $address;
      // Save address into session.
      $_SESSION['uc_addresses_order'][$order->order_id][$type] = serialize($address);
      return TRUE;

    case 'review':
      drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
      return uc_addresses_preprocess_address($order->uc_addresses[$type], 'checkout_review');
  }
}

// ---------------------------------------------------------------------------
// UBERCART CALLBACKS
// order panes
// ---------------------------------------------------------------------------

/**
 * Callback for the "bill to" pane.
 *
 * Overrides uc_order_pane_bill_to().
 *
 * @see uc_addresses_order_pane_address()
 */
function uc_addresses_order_pane_bill_to($op, $order, &$form = NULL, &$form_state = NULL) {
  return uc_addresses_order_pane_address('billing', $op, $order, $form, $form_state);
}

/**
 * Callback for the "ship to" pane.
 *
 * Overrides uc_order_pane_ship_to().
 *
 * @see uc_addresses_order_pane_address()
 */
function uc_addresses_order_pane_ship_to($op, $order, &$form = NULL, &$form_state = NULL) {
  return uc_addresses_order_pane_address('shipping', $op, $order, $form, $form_state);
}

/**
 * Callback for an address order pane.
 *
 * @param string $type
 *   The address type: billing or shipping.
 * @param string $op
 *   The operation that is being performed.
 * @param object $order
 *   The Ubercart order.
 * @param array $form
 *   The order edit form.
 * @param array $form_state
 *   The form state of the order edit form.
 *
 * @return mixed
 *   Depending on the operation, different results may be returned.
 */
function uc_addresses_order_pane_address($type, $op, $order, &$form = NULL, &$form_state = NULL) {
  if ($type == 'shipping') {
    $pane = 'ship_to';
    $other_pane = 'bill_to';
    $order_fieldname_prefix = 'delivery';
    $other_order_fieldname_prefix = 'billing';
    $copy_text = t('Copy billing information');
  }
  else {
    $pane = 'bill_to';
    $other_pane = 'ship_to';
    $order_fieldname_prefix = 'billing';
    $other_order_fieldname_prefix = 'delivery';
    $copy_text = t('Copy shipping information');
  }

  switch ($op) {
    case 'customer':
    case 'view':
      drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
      $lines = array();
      $values = uc_addresses_preprocess_address($order->uc_addresses[$type], 'order_view');
      foreach ($values as $fieldname => $value) {
        if (isset($value['title']) && $value['title'] != '') {
          $lines[] = '<strong>' . $value['title'] . '</strong>: ' . $value['data'];
        }
        else {
          $lines[] = $value['data'] . '<br />';
        }
      }
      $build = array('#markup' => implode('<br />', $lines));
      return $build;

    case 'edit-form':
      // Tell address book we want to get multiple addresses.
      UcAddressesAddressBook::get($order->uid)->setPerformanceHint(UcAddressesAddressBook::PERF_HINT_LOAD_ALL);

      $form[$pane] = array(
        '#tree' => TRUE,
      );

      // Get address for order if set.
      if (isset($order->uc_addresses[$type])) {
        $address = $order->uc_addresses[$type];
      }
      if (!$address) {
        $address = UcAddressesAddress::newAddress();
      }

      $select = uc_addresses_select_addresses($order->uid, 'order_form', $order_fieldname_prefix);
      if ($select) {
        $form[$pane]['select_address'] = $select + array(
          '#ajax' => array(
            'callback' => 'uc_addresses_address_render',
            'wrapper' => $pane . '-address-pane',
            'progress' => array(
              'type' => 'throbber',
            ),
            'event' => 'change',
          ),
          '#uc_addresses_address_element' => array('panes', $pane, 'address'),
          '#uc_addresses_address_input' => array('panes', $pane, 'address'),
          '#default_value' => $address,
        );
      }

      // Show copy address button if other pane is enabled.
      $panes = _uc_order_pane_list('edit');
      if (in_array('edit', $panes[$other_pane]['show']) && variable_get('uc_order_pane_' . $other_pane . '_show_edit', $panes[$other_pane]['enabled'])) {
        $form[$pane]['copy'] = array(
          '#type' => 'button',
          '#name' => $pane . '_copy',
          '#value' => $copy_text,
          '#ajax' => array(
            'callback' => 'uc_checkout_pane_address_render',
            'wrapper' => $pane . '-address-pane',
            'progress' => array(
              'type' => 'throbber',
            ),
          ),
          '#attributes' => array(
            'class' => array('rules-switch-button', 'uc-addresses-button', 'uc-addresses-copy-button'),
            'title' => $copy_text,
          ),
        );
      }

      if (isset($form_state['triggering_element'])) {
        $element = &$form_state['triggering_element'];

        switch ($element['#name']) {
          case $pane . '[select_address]':
            // An address is selected.
            $address_id = $element['#value'];
            $address_source = UcAddressesAddressBook::get($order->uid)->getAddressById($address_id);
            if ($address_source instanceof UcAddressesAddress) {
              $address = $address_source->copyAddress(UcAddressesAddressBook::get(0));
              $address_data = $address->getRawFieldData();
              foreach ($address_data as $field => $value) {
                if (isset($order->{$order_fieldname_prefix . '_' . $field})) {
                  $form_state['input'][$pane]['address'][$order_fieldname_prefix . '_' . $field] = $value;
                  $order->{$order_fieldname_prefix . '_' . $field} = $value;
                }
              }
            }
            break;
          case $pane . '_copy':
            // Copy over address information from the other address pane
            // if the other address pane exists.
            if (isset($form_state['input'][$other_pane])) {
              foreach ($form_state['input'][$other_pane]['address'] as $source_fieldname => $value) {
                // Substract prefix from fieldname.
                $fieldname = substr($source_fieldname, strlen($other_order_fieldname_prefix) + 1);
                $target_fieldname = $order_fieldname_prefix . '_' . $fieldname;
                // Copy over value to current address pane if the fieldname is not 'aid'.
                if ($fieldname != 'aid' && isset($order->$target_fieldname)) {
                  // Copy.
                  $value = $form_state['input'][$other_pane]['address'][$source_fieldname];
                  if ($address->fieldExists($fieldname)) {
                    $address->setField($fieldname, $value);
                  }
                  $order->$target_fieldname = $value;
                  $form_state['input'][$pane]['address'][$target_fieldname] = $value;
                }
              }
            }
            break;
        }

        // Forget any previous Ajax submissions, as we send new default values.
        unset($form_state['uc_addresses_address']);
      }

      // View the address form.
      $form[$pane]['address'] = array(
        '#type' => 'uc_addresses_address',
        '#uc_addresses_address' => $address,
        '#uc_addresses_context' => 'order_form',
        '#prefix' => '<div id="' . $pane . '-address-pane">',
        '#suffix' => '</div>',
        '#key_prefix' => $order_fieldname_prefix,
      );
/*
      // Form element for debug purposes, displays the address that is taken.
      $form[$pane]['address']['display'] = array(
        '#type' => 'item',
        '#title' => t('Address'),
        '#markup' => (string) $address,
        '#weight' => 100,
      );
//*/
      return $form;

    case 'edit-theme':
      return '<div id="' . $pane . '-pane">' . drupal_render($form[$pane]) . '</div>';

    case 'edit-process':
      $order_values = $form_state['values'];
      $address = $order->uc_addresses[$type];
      $changes = array();
      foreach ($order_values[$pane]['address'] as $key => $value) {
        // Check if this is an address field.
        if (strpos($key, $order_fieldname_prefix . '_') !== 0) {
          continue;
        }

        // Check if the value was changed.
        $fieldname = substr($key, strlen($order_fieldname_prefix) + 1);
        try {
          if ($address->getField($fieldname) != $value && isset($order->$key)) {
            $changes[$key] = $value;
          }
        }
        catch (UcAddressesException $e) {
          // Ignore any Ubercart Addresses exceptions.
        }
      }
      return $changes;
  }
}
