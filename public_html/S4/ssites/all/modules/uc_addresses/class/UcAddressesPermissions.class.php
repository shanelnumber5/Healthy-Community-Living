<?php

/**
 * @file
 * Permission class.
 */

/**
 * The permission class: UcAddressesPermissions.
 *
 * This class checks for view, edit and delete access for a single address. So
 * whenever you want to check permissions for address access, you should not
 * call user_access(), but call the appropiate method in this class. Call
 * canViewAddress() for view access, canEditAddress() for edit access and
 * canDeleteAddress() for delete access. The class will then take care for
 * calling user_access() itself.
 *
 * If the permissions defined by Ubercart Addresses do not fit your needs, you
 * are able to get further control about address access by implementing
 * hook_uc_addresses_may_view(), hook_uc_addresses_may_edit() or
 * hook_uc_addresses_may_delete(). See uc_addresses.api.php - included with the
 * module - for more information.
 */
class UcAddressesPermissions {
  // -----------------------------------------------------------------------------
  // CONSTANTS
  // -----------------------------------------------------------------------------

  /**
   * Give users permissiony to view their default address.
   */
  const VIEW_OWN_DEFAULT = 'view own default addresses';

  /**
   * Give users permission to view all of their own addresses.
   * Implies VIEW_OWN_DEFAULT.
   */
  const VIEW_OWN = 'view own addresses';

  /**
   * Give users the ability to view anyone's default address.
   * Implies VIEW_OWN_DEFAULT.
   */
  const VIEW_ALL_DEFAULTS = 'view all default addresses';

  /**
   * Give users the ability to view anyone's addresses.
   * Implies VIEW_OWN, VIEW_ALL_DEFAULTS.
   */
  const VIEW_ALL = 'view all addresses';

  /**
   * Give users the ability to add or edit their own addresses.
   * Implies VIEW_OWN.
   */
  const EDIT_OWN = 'add/edit own addresses';

  /**
   * Give users the ability to add or edit anyone's addresses.
   * Implies VIEW_ALL, EDIT_OWN.
   */
  const EDIT_ALL = 'add/edit all addresses';

  /**
   * Give users the ability to delete their own addresses.
   * Implies VIEW_OWN.
   */
  const DELETE_OWN = 'delete own addresses';

  /**
   * Give users the ability to delete anyone's addresses.
   * Implies VIEW_ALL, DELETE_OWN.
   */
  const DELETE_ALL = 'delete all addresses';

  // -----------------------------------------------------------------------------
  // METHODS
  // -----------------------------------------------------------------------------

  /**
   * Check if user may view this address.
   *
   * @param object $address_user
   *   The owner of the address.
   * @param UcAddressesAddress|NULL $address
   *   (optional) The address object.
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the given user has permission to view the address.
   *   FALSE otherwise.
   */
  static public function canViewAddress($address_user, UcAddressesAddress $address = NULL, $account = NULL) {
    $account = self::getAccount($account);
    if ($address_user->uid == $account->uid) {
      // User is the owner of the address.

      // If trying to view own address.
      if (self::canViewOwn($account)) {
        // Ask other modules if the address may be viewed.
        return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
      }

      // If viewing all addresses, we permit the operation if the user
      // can view the default address. The non-default addresses will
      // need to be filtered out elsewhere.
      if ($address == NULL) {
        if (self::canViewOwnDefault($account)) {
          // Ask other modules if the address may be viewed.
          return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
        }
        return FALSE;
      }

      // Check if the address is a default address and if the user
      // may view own default addresses.
      if ($address->isDefault('shipping') || $address->isDefault('billing')) {
        if (self::canViewOwnDefault($account)) {
          // Ask other modules if the address may be viewed.
          return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
        }
      }
    }
    else {
      // User is NOT the owner of the address.

      // If trying to view someone else's address.
      if (self::canViewAll($account)) {
        // Ask other modules if the address may be viewed.
        return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
      }

      // If viewing all addresses, we permit the operation if the user
      // can view the default address. The non-default addresses will
      // need to be filtered out elsewhere.
      if ($address == NULL) {
        if (self::canViewAllDefaults($account)) {
          // Ask other modules if the address may be viewed.
          return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
        }
        return FALSE;
      }

      // Check if the address is a default address and if the user
      // may view default addresses of all users.
      if ($address->isDefault('shipping') || $address->isDefault('billing')) {
        if (self::canViewAllDefaults($account)) {
          // Ask other modules if the address may be viewed.
          return self::invoke('uc_addresses_may_view', $address_user, $address, $account);
        }
      }
    }

    // No other cases are permitted.
    return FALSE;
  }

  /**
   * Check if the user can edit addresses of this user.
   *
   * @param object $address_user
   *   The owner of the address.
   * @param UcAddressesAddress
   *   (optional) The address object.
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the given user has permission to edit the address.
   *   FALSE otherwise.
   */
  static public function canEditAddress($address_user, UcAddressesAddress $address = NULL, $account = NULL) {
    $account = self::getAccount($account);

    if ($address_user->uid == $account->uid && self::canEditOwn($account)) {
      // Ask other modules if the address may be edited.
      return self::invoke('uc_addresses_may_edit', $address_user, $address, $account);
    }
    if ($address_user->uid != $account->uid && self::canEditAll($account)) {
      // Ask other modules if the address may be edited.
      return self::invoke('uc_addresses_may_edit', $address_user, $address, $account);
    }

    // No other cases are permitted.
    return FALSE;
  }

  /**
   * Check if the user can delete addresses of this user.
   * Default addresses can never be deleted.
   *
   * @param object $address_user
   *   The owner of the address.
   * @param UcAddressesAddress
   *   (optional) The address object.
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the given user has permission to delete the address.
   *   FALSE otherwise.
   */
  static public function canDeleteAddress($address_user, UcAddressesAddress $address = NULL, $account = NULL) {
    $account = self::getAccount($account);

    if ($address instanceof UcAddressesAddress) {
      // Check if the address is a default address. If so, the address may not be deleted.
      if ($address->isDefault('shipping') || $address->isDefault('billing')) {
        return FALSE;
      }
    }

    if ($address_user->uid == $account->uid && self::canDeleteOwn($account)) {
      // Ask other modules if the address may be deleted.
      return self::invoke('uc_addresses_may_delete', $address_user, $address, $account);
    }
    if ($address_user->uid != $account->uid && self::canDeleteAll($account)) {
      // Ask other modules if the address may be deleted.
      return self::invoke('uc_addresses_may_delete', $address_user, $address, $account);
    }

    // No other cases are permitted.
    return FALSE;
  }

  /**
   * If the account may view its own default addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may view its own default addresses.
   *   FALSE otherwise.
   */
  static public function canViewOwnDefault($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::VIEW_OWN_DEFAULT, $account) ||
      self::canViewAllDefaults($account) ||
      self::canViewOwn($account);
  }

  /**
   * If the account may view its own addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may view its own addresses.
   *   FALSE otherwise.
   */
  static public function canViewOwn($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::VIEW_OWN, $account) ||
      self::canViewAll($account) ||
      self::canEditOwn($account) ||
      self::canDeleteOwn($account);
  }

  /**
   * If the account may view all default addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may view all default addresses.
   *   FALSE otherwise.
   */
  static public function canViewAllDefaults($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::VIEW_ALL_DEFAULTS, $account) ||
      self::canViewAll($account);
  }

  /**
   * If the account may view all addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may view all addresses.
   *   FALSE otherwise.
   */
  static public function canViewAll($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::VIEW_ALL, $account) ||
      self::canEditAll($account) ||
      self::canDeleteAll($account);
  }

  /**
   * If the account may edit its own addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may edit its own addresses.
   *   FALSE otherwise.
   */
  static public function canEditOwn($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::EDIT_OWN, $account) ||
      self::canEditAll($account);
  }

  /**
   * If the account may edit all addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may edit all addresses.
   *   FALSE otherwise.
   */
  static public function canEditAll($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::EDIT_ALL, $account);
  }

  /**
   * If the account may delete its own addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may delete its own addresses.
   *   FALSE otherwise.
   */
  static public function canDeleteOwn($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::DELETE_OWN, $account) ||
      self::canDeleteAll($account);
  }

  /**
   * If the account may delete all addresses.
   *
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access public
   * @static
   * @return boolean
   *   TRUE if the account may delete all addresses.
   *   FALSE otherwise.
   */
  static public function canDeleteAll($account = NULL) {
    $account = self::getAccount($account);
    return
      user_access(self::DELETE_ALL, $account);
  }

  // -----------------------------------------------------------------------------
  // PRIVATE
  // -----------------------------------------------------------------------------

  /**
   * Helper function for getting the account to check access for.
   *
   * @param object $account
   *   (optional) The account return.
   *   Defaults to the current active user.
   *
   * @access private
   * @static
   * @return object
   *   The account to check access for.
   */
  private static function getAccount($account = NULL) {
    if (!$account) {
      global $user;
      return $user;
    }
    return $account;
  }

  /**
   * Ask other modules if a particular operation is permitted.
   *
   * @param string $hook
   *   The hook to invoke.
   * @param object $address_user
   *   The owner of the address.
   * @param UcAddressesAddress
   *   (optional) The address object.
   * @param object $account
   *   (optional) The account to check access for.
   *   Defaults to the current active user.
   *
   * @access private
   * @static
   * @return boolean
   *   TRUE if all modules agree that the operation is permitted.
   *   FALSE otherwise.
   */
  static private function invoke($hook, $address_user, UcAddressesAddress $address = NULL, $account = NULL) {
    $account = self::getAccount($account);
    if ($account->uid != 1) {
      // Ask other modules if the operation on the address is permitted.
      // If one of the modules returns FALSE, then the operation on the address is not permitted.
      // The superuser (user 1) may do everything, for this user the check is bypassed.
      foreach (module_implements($hook) as $module) {
        $function = $module . '_' . $hook;
        if (!$function($address_user, $address, $account)) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }
}
