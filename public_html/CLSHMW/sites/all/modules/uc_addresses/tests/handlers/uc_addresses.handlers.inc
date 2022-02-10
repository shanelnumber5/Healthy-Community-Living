<?php

/**
 * @file
 * Contains UcAddressesTextFieldHandler class.
 */

/**
 * Handler for a text field used by the uc_addresses_test module.
 *
 * This handler extends the handler for a simple text field and adds
 * a check for the field's address type.
 */
class UcAddressesTestTextFieldHandler extends UcAddressesTextFieldHandler {
  /**
   * Checks if field passes the context.
   *
   * @access public
   * @return boolean
   *   TRUE if the field may be used in the given context.
   *   FALSE otherwise.
   */
  public function checkContext() {
    if (parent::checkContext()) {
      $address = $this->getAddress();
      if (!empty($address->address_type) && $address->address_type != $this->getProperty('address_type')) {
        return FALSE;
      }
      return TRUE;
    }
    return FALSE;
  }
}
