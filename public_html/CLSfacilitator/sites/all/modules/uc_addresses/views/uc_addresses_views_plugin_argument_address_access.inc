<?php

/**
 * @file
 * Definition of uatest_views_plugin_argument_address_access.
 */

/**
 * Checks if the current user has access to addresses of the given user.
 *
 * This inherits checks from views_plugin_argument_validate_user.
 */
class uc_addresses_views_plugin_argument_address_access extends views_plugin_argument_validate {
  /**
   * Implements views_plugin_argument_validate#option_definition().
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['access_type'] = array('default' => 'view');
    return $options;
  }

  /**
   * Implements views_plugin_argument_validate#options_form().
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['access_type'] = array(
      '#type' => 'select',
      '#title' => t('Address access type'),
      '#description' => t('Select for which type of operation permissions should be checked. Usually "!access_type".', array('!access_type' => t('View'))),
      '#options' => array(
        'view' => t('View'),
        'edit' => t('Edit'),
        'delete' => t('Delete'),
      ),
      '#default_value' => $this->options['access_type'],
    );
  }

  /**
   * Validates if argument is a valid user
   * and if the current user has access to addresses of
   * the given user.
   */
  public function validate_argument($argument) {
    // Check for address access.
    $address = UcAddressesAddressBook::loadAddress($argument);
    if (!$address) {
      // Address does not exist.
      return FALSE;
    }
    $address_user = user_load($address->getUserId());
    switch ($this->options['access_type']) {
      case 'view':
        return UcAddressesPermissions::canViewAddress($address_user, $address);

      case 'edit':
        return UcAddressesPermissions::canEditAddress($address_user, $address);

      case 'delete':
        return UcAddressesPermissions::canDeleteAddress($address_user, $address);
    }
    return FALSE;
  }
}
