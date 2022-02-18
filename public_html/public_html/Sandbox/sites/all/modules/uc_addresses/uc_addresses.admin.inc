<?php

/**
 * @file
 * Admin settings for Ubercart Addresses.
 */

// -----------------------------------------------------------------------------
// MODULE SETTINGS
// -----------------------------------------------------------------------------

/**
 * Module settings form.
 */
function uc_addresses_settings_form($form, &$form_state) {
  $form['uc_addresses'] = array(
    '#type' => 'fieldset',
    '#title' => t('Ubercart Addresses settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['uc_addresses']['uc_addresses_use_address_name'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use address nicknames'),
    '#description' => t('When checked, users can optionally give an address a name (e.g. "work", "mary", "joe").'),
    '#default_value' => variable_get('uc_addresses_use_address_name', TRUE),
  );
  $form['uc_addresses']['uc_addresses_use_default_billing'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use default billing addresses'),
    '#description' => t('Note that addresses marked as default billing cannot be deleted.'),
    '#default_value' => variable_get('uc_addresses_use_default_billing', TRUE),
  );
  $form['uc_addresses']['uc_addresses_use_default_shipping'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use default shipping addresses'),
    '#description' => t('Note that addresses marked as default shipping cannot be deleted.'),
    '#default_value' => variable_get('uc_addresses_use_default_shipping', TRUE),
  );

  $form['checkout'] = array(
    '#type' => 'fieldset',
    '#title' => t('Checkout settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['checkout']['uc_addresses_default_billing_address'] = array(
    '#type' => 'checkbox',
    '#title' => t('Automatically fill in the billing address with the user\'s default billing address.'),
    '#description' => t('Note that %use-default-setting must be activated for this setting to have effect.', array('%use-default-setting' => $form['uc_addresses']['uc_addresses_use_default_billing']['#title'])),
    '#default_value' => variable_get('uc_addresses_default_billing_address', TRUE),
  );
  $form['checkout']['uc_addresses_default_shipping_address'] = array(
    '#type' => 'checkbox',
    '#title' => t('Automatically fill in the delivery address with the user\'s default shipping address.'),
    '#description' => t('Note that %use-default-setting must be activated for this setting to have effect.', array('%use-default-setting' => $form['uc_addresses']['uc_addresses_use_default_shipping']['#title'])),
    '#default_value' => variable_get('uc_addresses_default_shipping_address', TRUE),
  );

  $form['user'] = array(
    '#type' => 'fieldset',
    '#title' => t('User settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['user']['uc_addresses_require_address'] = array(
    '#type' => 'checkbox',
    '#title' => t('Require that an address be entered during registration'),
    '#default_value' => variable_get('uc_addresses_require_address', TRUE),
  );
  $form['user']['uc_addresses_require_address_admin'] = array(
    '#type' => 'checkbox',
    '#title' => t('Require admins to enter an address upon user creation'),
    '#description' => t('When checked, users with the permission %administer-users need to enter an address when they create a new user.', array('%administer-users' => t('administer users'))),
    '#default_value' => variable_get('uc_addresses_require_address_admin', TRUE),
  );

  return system_settings_form($form);
}

// -----------------------------------------------------------------------------
// ADDRESS FORMAT SETTINGS
// -----------------------------------------------------------------------------

/**
 * Lists all enabled countries were address formats may be set for.
 *
 * @param int $country_id
 *   (optional) The country to set the address format for.
 *   If specified, a different form will be loaded.
 *
 * @return string | array
 *   A list of countries, themed in a table.
 *   Or a form array if a country ID is specified.
 *
 * @see uc_addresses_country_formats_form()
 */
function uc_addresses_country_formats_page($country_id = NULL) {
  if ($country_id) {
    // Return address display form for this country.
    return drupal_get_form('uc_addresses_country_formats_form', $country_id);
  }

  // List all countries.
  $header = array(
    t('Country'),
    t('Status'),
  );

  $rows = array();
  $path = 'admin/store/settings/countries/uc_addresses_formats';
  $countries = array();
  $result = db_query("SELECT country_id, country_name FROM {uc_countries}");
  foreach ($result as $country) {
    $countries[t($country->country_name)] = $country;
  }
  // Sort countries in natural order.
  uksort($countries, 'strnatcasecmp');

  foreach ($countries as $country) {
    $rows[] = array(
      l(t($country->country_name), $path . '/' . $country->country_id),
      (variable_get('uc_addresses_address_format_' . $country->country_id, NULL) ? t('Overridden') : t('Ubercart default')),
    );
  }

  return theme('table', array('header' => $header, 'rows' => $rows));
}

/**
 * Form for address format.
 *
 * @param int $country_id
 *   The ID of the country to set a format for.
 *
 * @return array
 *   The form definition.
 */
function uc_addresses_country_formats_form($form, $form_state, $country_id) {
  // Get country.
  $result = db_query("SELECT country_name FROM {uc_countries} WHERE country_id = :country_id", array(':country_id' => $country_id));
  $country = $result->fetch();
  if (!isset($country->country_name)) {
    return;
  }

  $form = array();
  $form['uc_addresses_address_format_' . $country_id] = array(
    '#type' => 'textarea',
    '#title' => t('Address format for @country', array('@country' => t($country->country_name))),
    '#default_value' => uc_addresses_get_address_format($country_id),
    '#description' => t('Uses tokens to format an address for this country.'),
    '#rows' => 6,
  );

  $form['token_help'] = array(
    '#type' => 'fieldset',
    '#title' => t('Replacement patterns'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#children' => theme('token_tree', array('token_types' => array('uc_addresses'))),
  );

  $form = system_settings_form($form);
  return $form;
}
