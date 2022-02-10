<?php

/**
 * @file
 * Feeds integration for Ubercart Addresses module.
 */

/**
 * Implements hook_feeds_after_import().
 *
 * Makes sure all users for which addresses were imported have default
 * addresses.
 */
function uc_addresses_feeds_after_import(FeedsSource $source) {
  // Check if default addresses are used at all.
  if (!variable_get('uc_addresses_use_default_billing', TRUE) && !variable_get('uc_addresses_use_default_shipping', TRUE)) {
    // Addresses may not be marked as default. No reason to continue here.
    return;
  }

  // Get processor config.
  $importer_config = $source->importer()->getConfig();
  $processor_config = $importer_config['processor']['config'];

  // Continue only if it were addresses that got imported.
  if (empty($processor_config['bundle']) || $processor_config['bundle'] != 'uc_addresses') {
    return;
  }

  // Check if addresses may be set as default addresses as set in the config.
  if (empty($processor_config['mark_as_default_billing']) && empty($processor_config['mark_as_default_shipping'])) {
    // Addresses may not be marked as default. No reason to continue here.
    return;
  }

  $address_books = UcAddressesAddressBook::getAddressBooks();
  foreach ($address_books as $address_book) {
    if ($address_book->isOwned()) {
      foreach (uc_addresses_address_types() as $address_type) {
        // Check for default addresses.
        if (variable_get('uc_addresses_use_default_' . $address_type, TRUE) && $processor_config['mark_as_default_' . $address_type]) {
          $address = $address_book->getDefaultAddress($address_type);
          if (!$address) {
            // Mark one address as the default $address_type.
            $addresses = $address_book->getAddresses();
            $address = reset($addresses);
          }
          $address_book->setAddressAsDefault($address, $address_type);
          $address->save();
        }
      }
    }
  }
}
