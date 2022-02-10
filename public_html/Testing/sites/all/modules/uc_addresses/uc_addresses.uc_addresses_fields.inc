<?php

/**
 * @file
 * In this file fields and field handlers for Ubercart Addresses are registered.
 */

// ---------------------------------------------------------------------------
// UC_ADDRESSES HOOKS
// ---------------------------------------------------------------------------

/**
 * Implements hook_uc_addresses_field_handlers().
 *
 * Register form fields and their definitions.
 *
 * @return array
 *   A list of field handler definitions.
 */
function uc_addresses_uc_addresses_field_handlers() {
  $path = drupal_get_path('module', 'uc_addresses') . '/handlers';

  $info = array();

  // Base class.
  $info['UcAddressesFieldHandler'] = array(
    'hidden' => TRUE,
    'handler' => array(
      'class' => 'UcAddressesFieldHandler',
      'file' => 'UcAddressesFieldHandler.class.php',
      'path' => $path,
    ),
  );

  // Handlers for Ubercart core address fields.
  $file = 'ubercart.handlers.inc';
  $info['UcAddressesUcFieldHandler'] = array(
    'hidden' => TRUE,
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesUcFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesUcTextFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesUcFieldHandler',
      'class' => 'UcAddressesUcTextFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesUcZoneFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesUcFieldHandler',
      'class' => 'UcAddressesUcZoneFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesUcCountryFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesUcFieldHandler',
      'class' => 'UcAddressesUcCountryFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );

  // Handlers for specific Ubercart Addresses address fields.
  $file = 'uc_addresses.handlers.inc';
  $info['UcAddressesAddressNameFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesAddressNameFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesDefaultAddressFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesDefaultAddressFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesDateFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesDateFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );
  $info['UcAddressesHiddenFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesHiddenFieldHandler',
      'file' => $file,
      'path' => $path,
    ),
  );

  // Extra handlers that can be used by other modules.
  $info['UcAddressesTextFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesTextFieldHandler',
      'file' => 'uc_addresses.handlers.inc',
      'path' => $path,
    ),
  );
  $info['UcAddressesMissingFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'UcAddressesMissingFieldHandler',
      'file' => 'uc_addresses.handlers.inc',
      'path' => $path,
    ),
  );

  return $info;
}

/**
 * Implements hook_uc_addresses_fields().
 *
 * Register all address fields used by Ubercart Addresses.
 *
 * @return array
 *   A list of field definitions.
 */
function uc_addresses_uc_addresses_fields() {
  $data = array();

  // Display settings for Ubercart Core Fields (except phone).
  $ubercart_display_settings = array(
    'default' => TRUE,
    'address_view' => FALSE,
    'checkout_review' => FALSE,
    'order_view' => FALSE,
  );

  // Ubercart Core Fields.
  $data['first_name'] = array(
    'type' => 'text',
    'label' => t('First name'),
    'description' => t('The addressee\'s first name'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['last_name'] = array(
    'type' => 'text',
    'label' => t('Last name'),
    'description' => t('The addressee\'s last name'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['phone'] = array(
    'type' => 'text',
    'label' => t('Phone'),
    'description' => t('The addressee\'s phone number'),
    'handler' => 'UcAddressesUcTextFieldHandler',
  );
  $data['company'] = array(
    'type' => 'text',
    'label' => t('Company'),
    'description' => t('The addressee\'s company name'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['street1'] = array(
    'type' => 'text',
    'label' => t('Street line 1'),
    'description' => t('The addressee\'s residence number and street'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['street2'] = array(
    'type' => 'text',
    'label' => t('Street line 2'),
    'description' => t('The addressee\'s residence number and street (continued)'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['city'] = array(
    'type' => 'text',
    'label' => t('City'),
    'description' => t('The addressee\'s city of residence'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );
  $data['country'] = array(
    'type' => 'integer',
    'label' => t('Country'),
    'description' => t('The addressee\'s country of residence'),
    'handler' => 'UcAddressesUcCountryFieldHandler',
    'display_settings' => $ubercart_display_settings,
    'options list' => 'uc_country_option_list',
  );
  $data['zone'] = array(
    'type' => 'integer',
    'label' => t('Zone'),
    'description' => t('The addressee\'s zone of residence'),
    'handler' => 'UcAddressesUcZoneFieldHandler',
    'display_settings' => $ubercart_display_settings,
    'options list' => 'uc_zone_option_list',
  );
  $data['postal_code'] = array(
    'type' => 'text',
    'label' => t('Postal code'),
    'description' => t('The addressee\'s postal code'),
    'handler' => 'UcAddressesUcTextFieldHandler',
    'display_settings' => $ubercart_display_settings,
  );

  // Ubercart Addresses fields.
  $data['address_name'] = array(
    'title' => t('Address name'),
    'type' => 'text',
    'description' => t('The name used to access this address'),
    'handler' => 'UcAddressesAddressNameFieldHandler',
    'display_settings' => array(
      'default' => TRUE,
      'checkout_form' => FALSE,
      'checkout_review' => FALSE,
      'order_form' => FALSE,
      'order_view' => FALSE,
    ),
    'compare' => FALSE,
  );
  $data['default_shipping'] = array(
    'title' => t('Default shipping address'),
    'type' => 'boolean',
    'description' => t('If the address is the default shipping address'),
    'handler' => 'UcAddressesDefaultAddressFieldHandler',
    'display_settings' => array(
      'default' => TRUE,
      'address_view' => FALSE,
      'checkout_form' => FALSE,
      'checkout_review' => FALSE,
      'order_form' => FALSE,
      'order_view' => FALSE,
    ),
    'compare' => FALSE,
    'suffix' => t('Use as my default shipping address'),
    'default_type' => 'shipping',
  );
  $data['default_billing'] = array(
    'title' => t('Default billing address'),
    'type' => 'boolean',
    'description' => t('If the address is the default billing address'),
    'handler' => 'UcAddressesDefaultAddressFieldHandler',
    'display_settings' => array(
      'default' => TRUE,
      'address_view' => FALSE,
      'checkout_form' => FALSE,
      'checkout_review' => FALSE,
      'order_form' => FALSE,
      'order_view' => FALSE,
    ),
    'compare' => FALSE,
    'suffix' => t('Use as my default billing address'),
    'default_type' => 'billing',
  );
  // Non editable fields.
  $data['aid'] = array(
    'title' => t('Address ID'),
    'type' => 'integer',
    'description' => t('The address ID'),
    'handler' => 'UcAddressesHiddenFieldHandler',
    'display_settings' => array(
      'default' => FALSE,
    ),
    'compare' => FALSE,
    'hidden' => TRUE,
  );
  $data['uid'] = array(
    'title' => t('User ID'),
    'type' => 'user',
    'description' => t('The ID of the user who owns this address'),
    'handler' => 'UcAddressesHiddenFieldHandler',
    'display_settings' => array(
      'default' => FALSE,
    ),
    'compare' => FALSE,
    'hidden' => TRUE,
  );
  $data['created'] = array(
    'title' => t('Created'),
    'type' => 'date',
    'description' => t('The date this address was created'),
    'handler' => 'UcAddressesDateFieldHandler',
    'display_settings' => array(
      'default' => FALSE,
    ),
    'compare' => FALSE,
    'hidden' => TRUE,
  );
  $data['modified'] = array(
    'title' => t('Modified'),
    'type' => 'date',
    'description' => t('The date this address was last modified'),
    'handler' => 'UcAddressesDateFieldHandler',
    'display_settings' => array(
      'default' => FALSE,
    ),
    'compare' => FALSE,
    'hidden' => TRUE,
  );

  return $data;
}

/**
 * Implements hook_uc_addresses_select_addresses().
 *
 * @param int $uid
 *   The user ID to select addresses for.
 * @param string $context
 *   The context in which the addresses are used:
 *   - checkout_form
 *   - order_form
 * @param string $type
 *   The type of address to select addresses for (shipping or billing).
 *
 * @return array
 *   An array of UcAddressesAddress objects.
 *   Or an array of address arrays.
 * @todo Add setting to say if addresses may come from previous orders or not.
 */
function uc_addresses_uc_addresses_select_addresses($uid, $context, $type) {
  $addresses = &drupal_static(__FUNCTION__, array());
  if (!isset($addresses[$type])) {
    $addresses[$type] = array();
  }

  if (count($addresses[$type]) > 0) {
    return $addresses[$type];
  }

  // Address book selecting.
  $uc_addresses = UcAddressesAddressBook::get($uid)->getAddresses();
  if (count($uc_addresses) > 0) {
    foreach ($uc_addresses as $address) {
      if ($address->isNew()) {
        // Skip unsaved addresses.
        continue;
      }
      if (!UcAddressesPermissions::canViewAddress(user_load($address->getUserId()), $address)) {
        // Skip addresses that the currently logged in user may not view.
        continue;
      }
      $addresses[$type][] = $address;
    }
  }

  // If there no addresses in the address book, try to select addresses from
  // previous orders.
  if (count($addresses[$type]) < 1) {
    $addresses[$type] = uc_addresses_get_addresses($uid, $type);
    if (is_array($addresses[$type]) && count($addresses[$type]) > 0) {
      foreach ($addresses[$type] as $index => $address) {
        // Set source module manually this time.
        $addresses[$type][$index]['module'] = 'uc_order';
      }
    }
    else {
      $addresses[$type] = array();
    }
  }
  return $addresses[$type];
}
