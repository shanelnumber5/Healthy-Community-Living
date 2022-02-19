<?php

/**
 * @file
 * Contains the UcAddressesAddressBook class.
 */

/**
 * The address book class
 *
 * The goal of the address book class is to hold a list of addresses that are
 * currently loaded or added during the request. It is designed so that when
 * the address book is asked for the same address twice during one request,
 * it doesn't need to look it up in the database again.
 *
 * Each instance of the address book class belongs to one user and each user can
 * only have one address book.
 */
class UcAddressesAddressBook {
  // -----------------------------------------------------------------------------
  // CONSTANTS
  // -----------------------------------------------------------------------------

  // Performance hint setting.
  const PERF_HINT_LOAD_ONE = 0;
  const PERF_HINT_LOAD_ALL = 1;

  // Load by.
  const BY_AID = 0;
  const BY_NAME = 1;

  // -----------------------------------------------------------------------------
  // STATIC PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * An array of UcAddressesAddressBook objects.
   *
   * Holds all constructed address books.
   *
   * @var array
   * @access private
   * @static
   */
  static private $singleton = array();

  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * Performance hint setting.
   *
   * This setting determines how the address book should operate when it loads
   * addresses. The performance hint setting can be set to:
   * - PERF_HINT_LOAD_ONE
   * - PERF_HINT_LOAD_ALL
   *
   * @var int
   * @access private
   */
  private $performanceHint = self::PERF_HINT_LOAD_ONE;

  /**
   * The id of the user who "owns" this address book.
   *
   * @var int
   * @access private
   */
  private $uid;

  /**
   * A list of addresses in the address book.
   *
   * @var array
   * @access private
   */
  private $addresses = array();

  /**
   * An array of default addresses.
   *
   * @var array
   * @access private
   */
  private $defaultAddresses = array();

  /**
   * Whether or not the default addresses for this address book are loaded.
   *
   * @var boolean
   * @access private
   */
  private $defaultsLoaded = FALSE;

  /**
   * Whether or not all addresses for this address book are loaded.
   *
   * @var boolean
   * @access private
   */
  private $allLoaded = FALSE;

  // -----------------------------------------------------------------------------
  // SINGLETON METHODS
  // -----------------------------------------------------------------------------

  /**
   * AddressBook object constructor.
   *
   * @param mixed $user
   *   Either an user id or an user object.
   *
   * @access private
   * @return void
   */
  private function __construct($user) {
    if (is_object($user)) {
      $user = $user->uid;
    }
    $this->uid = $user;
  }

  /**
   * Disallow cloning the address book.
   *
   * @access private
   * @return void
   */
  private function __clone() { }

  /**
   * Returns address book for the given user.
   *
   * @param mixed $user
   *   Either an user id or an user object.
   * @access public
   * @static
   * @return UcAddressesAddressBook
   *   An instance of this class.
   */
  static public function get($user) {
    if (is_object($user)) {
      $user = $user->uid;
    }
    if (isset(self::$singleton[$user])) {
      $instance = self::$singleton[$user];
      if ($instance) {
        return $instance;
      }
    }
    $singleton = self::$singleton[$user] = new UcAddressesAddressBook($user);
    return $singleton;
  }

  /**
   * Returns all currently loaded address books.
   *
   * @return array
   *   An array of UcAddressesAddressBook instances.
   */
  static public function getAddressBooks() {
    return self::$singleton;
  }

  /**
   * Looks up a single address.
   *
   * This method will first look in all the loaded address books if the address
   * is already known. It it is known, then it will return the found address.
   *
   * If not, it will lookup the address in the database.
   * An UcAddressesAddress object will be created, populated with the loaded values.
   * The address will be added to the user's address book.
   *
   * @param int $aid
   *   ID of the address to load.
   * @access public
   * @static
   * @return UcAddressesAddress
   *   An instance of UcAddressesAddress if the address was found.
   *   FALSE otherwise.
   * @todo Think of a better name for this method.
   */
  static public function loadAddress($aid) {
    self::loadStatic($aid);

    // Look for the address in one of the available address books.
    foreach (self::$singleton as $addressbook) {
      if ($addressbook->addressExists($aid)) {
        return $addressbook->getAddressById($aid);
      }
    }
    return FALSE;
  }

  /**
   * Create a new unowned address.
   *
   * This method will create an empty address without an owner.
   * This is useful when you want to ask an anonymous user for an address
   * (e.g., when registering).
   * However, unonwed addresses can not be saved. In order to save this
   * address, the UcAddressesAddress method setOwner() should be called.
   *
   * @access public
   * @static
   * @return UcAddressesAddress
   *   A new instance of UcAddressesAddress.
   */
  static public function newAddress() {
    return self::get(0)->addAddress();
  }

  // -----------------------------------------------------------------------------
  // PERFORMANCE
  // -----------------------------------------------------------------------------

  /**
   * Sets the performance hint setting.
   *
   * @param int $hint
   *   The hint to set.
   * @return void
   * @throws UcAddressesInvalidParameterException
   */
  public function setPerformanceHint($hint) {
    switch ($hint) {
      case self::PERF_HINT_LOAD_ONE:
      case self::PERF_HINT_LOAD_ALL:
        $this->performanceHint = $hint;
        break;

      default:
        throw new UcAddressesInvalidParameterException(t('Tried to set an invalid performance hint for the address book'));
    }
  }

  /**
   * Returns the performance hint setting.
   *
   * @return int
   *   The performance hint setting.
   */
  public function getPerformanceHint() {
    return $this->performanceHint;
  }

  // -----------------------------------------------------------------------------
  // ADDRESS BOOK FUNCTIONS
  // -----------------------------------------------------------------------------

  /**
   * Adds address to address book.
   *
   * @param UcAddressesAddress $address
   *   (optional) An instance of UcAddressesAddress to add.
   *   Defaults to a new instance of UcAddressesAddress.
   *
   * @access public
   * @return UcAddressesAddress
   *   The instance of UcAddressesAddress that was added.
   * @throws UcAddressesInvalidParameterException
   * @throws UcAddressesNameCollisionException
   */
  public function addAddress(UcAddressesAddress $address = NULL) {
    // If we add an address, then we'll probably save it, which
    // requires loading all addresses for error checking.
    if ($this->performanceHint == self::PERF_HINT_LOAD_ONE) {
      $this->performanceHint = self::PERF_HINT_LOAD_ALL;
    }

    if ($address) {
      // In case of a new address with an address name,
      // load other addresses to do a name check comparison.
      if ($address->isNew() && $address->getName() && !$this->allLoaded) {
        $this->loadAll();
      }

      // Check if address is already in addressbook.
      foreach ($this->addresses as $aid => $addressBookAddress) {
        if ($address === $addressBookAddress) {
          throw new UcAddressesInvalidParameterException(t('Tried to add an address already in the address book'));
        }
        if ($address->getName() && $address->getName() == $addressBookAddress->getName()) {
          throw new UcAddressesNameCollisionException(t('Tried to add an address with a name matching that of an address already in the address book'));
        }
      }

      // Check if address belongs to this address book.
      if ($address->getAddressBook() !== $this && $address->isOwned()) {
        throw new UcAddressesInvalidParameterException(t('Tried to add an address already in an other address book'));
      }
    }

    if (!$address) {
      $address = new UcAddressesAddress($this);
    }
    $this->addresses[$address->getId()] = $address;

    if ($address->isDefault('shipping')) {
      $this->defaultAddresses['shipping'] = $address;
    }
    if ($address->isDefault('billing')) {
      $this->defaultAddresses['billing'] = $address;
    }

    // Make sure this becomes one of our addresses.
    if ($address->getAddressBook() !== $this) {
      $address->privChangeAddressBook($this);
    }

    return $address;
  }

  /**
   * Updates address ID in the address book.
   *
   * Called by method save() in UcAddressesAddress when the
   * address gets an ID.
   *
   * @param UcAddressesAddress $address
   *   The address to reindex.
   *
   * @access public
   * @return void
   * @throws UcAddressesInvalidParameterException
   */
  public function updateAddress(UcAddressesAddress $address) {
    // Check if address belongs to this address book.
    if ($address->getAddressBook() !== $this) {
      throw new UcAddressesInvalidParameterException(t('Tried to update an address from an other address book'));
    }

    // Loop through all addresses to find out what temporary ID
    // the address is known under.
    foreach ($this->addresses as $aid => $addressBookAddress) {
      if ($address === $addressBookAddress) {
        // Update address ID.
        unset($this->addresses[$aid]);
        $this->addresses[$address->getId()] = $address;
        return;
      }
    }
    // The address should have been found in the address book.
    // However, sometimes it can happen that there are two address
    // objects with the same ID. This can happen when serializing
    // and unserializing address objects.
    $this->addresses[$address->getId()] = $address;
  }

  /**
   * Checks if given address looks like an address already in the
   * address book.
   *
   * Ignores the case if the address to compare is included in this
   * address book.
   *
   * The common case to use this method is when you have a new address
   * and you want to make sure an address that looks the same is not
   * already in the address book.
   *
   * @param UcAddressesAddress $address
   *   The address to compare with other addresses in the address book.
   * @param boolean $compareUnsaved
   *   (optional) If the address should be compared with addresses that
   *   are not yet saved.
   *   Defaults to FALSE.
   * @access public
   * @return
   *   UcAddressesAddress in case a match is found.
   *   FALSE otherwise.
   */
  public function compareAddress(UcAddressesAddress $address, $compareUnsaved = FALSE) {
    if (!$this->allLoaded) {
      $this->loadAll();
    }

    foreach ($this->addresses as $addressBookAddress) {
      if (!$compareUnsaved && $addressBookAddress->isNew()) {
        // Don't compare the addresses with unsaved addresses.
        continue;
      }
      if ($address === $addressBookAddress) {
        // We don't need to compare the address with itself.
        continue;
      }
      if ($address->getId() === $addressBookAddress->getId()) {
        // Somehow we ended up with two addresses with the same ID.
        // This can happen when address objects get serialized and unserialized.
        // Ideally, this case should be prevented, but I'm not sure how. We can't
        // reassign "$this" in __wakeup().
        // Anyway, in this case we need to skip the comparison too.
        continue;
      }

      if ($addressBookAddress->compareAddress($address)) {
        // Found a match! No need to look further.
        return $addressBookAddress;
      }
    }
    return FALSE;
  }

  /**
   * Checks if an address exists in the addresses array.
   *
   * This method doesn't do any database calls.
   * It just checks if an address is already available in the address book.
   * Called in loadAddress().
   *
   * @param int $aid
   *   The ID of the address to check existence for.
   *
   * @access public
   * @return boolean
   *   TRUE if the address exists.
   *   FALSE otherwise.
   */
  public function addressExists($aid) {
    if (isset($this->addresses[$aid])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get an address by ID.
   *
   * First a check is done to see if the address is already available in $addresses
   * array. If it's not available, then a database request is send.
   * If the requested address is not found or not owned by the user of the
   * address book, an UcAddressesDbException is thrown.
   *
   * @param int $aid
   *   The ID of the address to get.
   *
   * @access public
   * @return
   *   UcAddressesAddress if the address is found.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  public function getAddressById($aid) {
    $this->loadOne(self::BY_AID, $aid);
    if (isset($this->addresses[$aid])) {
      return $this->addresses[$aid];
    }
    return FALSE;
  }

  /**
   * Get an address by it's nickname.
   *
   * First, the $addresses array is searched to see if the address is already
   * available. If it's not available, then a database request is send.
   * If the requested address is not found or not owned by the user of the
   * address book, an UcAddressesDbException is thrown.
   *
   * @param string $name
   *   The nickname of the address.
   *
   * @access public
   * @return
   *   UcAddressesAddress if the address is found.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  public function getAddressByName($name) {
    $this->loadOne(self::BY_NAME, $name);
    return $this->findByName($name);
  }

  /**
   * Deletes an addres by giving the addres object.
   *
   * @param UcAddressesAddress $address
   *   The address to delete.
   *
   * @return boolean
   *   TRUE if the address is deleted.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  public function deleteAddress(UcAddressesAddress $address) {
    // Check to make sure this is one of our addresses
    if ($address->getAddressBook() !== $this) {
      return FALSE;
    }

    return $this->deleteOne(self::BY_AID, $address->getId());
  }

  /**
   * Deletes an address by ID.
   *
   * This will delete an address from the database.
   *
   * @param int $aid
   *   The id of the address to delete.
   *
   * @access public
   * @return boolean
   *   TRUE if the address is deleted.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  public function deleteAddressById($aid) {
    return $this->deleteOne(self::BY_AID, $aid);
  }

  /**
   * Deletes an address by name.
   *
   * @param string $name
   *   The nickname of the address.
   *
   * @access public
   * @return boolean
   *   TRUE if the address is deleted.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  public function deleteAddressByName($name) {
    return $this->deleteOne(self::BY_NAME, $name);
  }

  /**
   * Returns user ID, the owner of this address book.
   *
   * @access public
   * @return int
   */
  public function getUserId() {
    return $this->uid;
  }

  /**
   * Returns all addresses of the user.
   *
   * @access public
   * @return array
   */
  public function getAddresses() {
    if (!$this->allLoaded) {
      $this->loadAll();
    }
    return $this->addresses;
  }

  /**
   * Sets the owner of an address if the owner was previously unknown.
   *
   * This method is only used to set the owner of the address when it's
   * currently owned by user 0.
   * This is the case when an address is asked at registering
   * or when the user anonymously checked out.
   *
   * Should only be called by UcAddressesAddress.
   *
   * @param UcAddressesAddress $address
   *   The address to change the owner for.
   * @param int $uid
   *   The user who should be the owner of the address.
   *
   * @access public
   * @return UcAddressesAddressBook
   *   The address book the address was transferred to;
   *   Or NULL if the address was already owned.
   */
  public function setAddressOwner(UcAddressesAddress $address, $uid) {
    // Reasons to skip out early.
    if ($this->isOwned()) {
      // Address is already owned.
      return;
    }
    if ($address->getAddressBook() !== $this) {
      // The address does not belong to this address book.
      return $address->setOwner($uid);
    }

    // Add address to user $uid address book.
    $addressBook = self::get($uid);
    $addressBook->addAddress($address);

    // Remove address from this address book.
    $this->removeAddressFromAddressBook($address);

    return $addressBook;
  }

  /**
   * Checks if the address book is owned by an user.
   *
   * An address is owned by an user if the owner's user id
   * is not zero (= anonymous user).
   *
   * @access public
   * @return boolean
   *   TRUE if the address is owned.
   *   FALSE otherwise.
   */
  public function isOwned() {
    return ($this->getUserId() > 0);
  }

  /**
   * Reconstructs the address book completely.
   *
   * This will remove all addresses currently tracked by the address
   * book. All the properties will be set back to the default
   * values.
   *
   * Calling this method is bad for performance as it will force to
   * reload addresses from the database, so use it with caution.
   *
   * This method is generally only of use within automated tests.
   *
   * @access public
   * @return void
   */
  public function reset() {
    $this->addresses = array();
    $this->defaultAddresses = array();
    $this->defaultsLoaded = FALSE;
    $this->allLoaded = FALSE;
  }

  // -----------------------------------------------------------------------------
  // UBERCART ADDRESSES FEATURES
  // -----------------------------------------------------------------------------

  /**
   * Sets the name of the address.
   *
   * @param UcAddressesAddress $address
   *   The address to set a name for.
   * @param string $name
   *   The nickname the address will get.
   *
   * @access public
   * @return boolean
   *   TRUE if the name was changed.
   *   FALSE otherwise.
   */
  public function setAddressName($address, $name) {
    if (!$this->allLoaded) {
      $this->loadAll();
    }

    // Check to make sure this is one of our addresses.
    if ($address->getAddressBook() !== $this) {
      return FALSE;
    }

    // Check if an other address already has the same name.
    // We don't allow two addresses having the same name.
    // One exception: multiple addresses having an empty name is allowed.
    if ($name !== '') {
      foreach ($this->addresses as $aid => $addr) {
        if ($address !== $addr && $addr->getName() == $name) {
          return FALSE;
        }
      }
    }

    $address->privSetUcAddressField('name', $name);
    return TRUE;
  }

  /**
   * Returns a default address.
   *
   * @param string $type
   *   The address type to get (shipping, billing).
   *
   * @access public
   * @return
   *   UcAddressesAddress if the address is found.
   *   NULL otherwise.
   */
  public function getDefaultAddress($type = 'billing') {
    if (isset($this->defaultAddresses[$type])) {
      return $this->defaultAddresses[$type];
    }
    if (!$this->defaultsLoaded) {
      $this->loadDefaults();
    }
    if (isset($this->defaultAddresses[$type])) {
      return $this->defaultAddresses[$type];
    }
    return NULL;
  }

  /**
   * Set an address as a default address.
   *
   * @param UcAddressesAddress $address
   *   The address to set as default.
   * @param string $type
   *   The address type to set (shipping, billing).
   *
   * @access public
   * @return boolean
   *   TRUE if the address was set as default.
   *   FALSE otherwise.
   */
  public function setAddressAsDefault($address, $type = 'billing') {
    // Reasons to skip out early.
    if (!$this->isOwned()) {
      // Address is not owned, so it can't be set as default
      // in this stage.
      return FALSE;
    }
    // Check to make sure this is one of our addresses.
    if ($address->getAddressBook() !== $this) {
      return FALSE;
    }

    // Make sure the previous default address is loaded.
    $this->getDefaultAddress($type);

    // Loop through all addresses to make sure no other
    // addresses are marked as default.
    foreach ($this->addresses as $aid => $addr) {
      if ($address !== $addr && $addr->isDefault($type)) {
        $addr->privSetUcAddressField($type, FALSE);
      }
    }
    // Set given address as the default.
    $address->privSetUcAddressField($type, TRUE);
    $this->defaultAddresses[$type] = $address;
    return TRUE;
  }

  // -----------------------------------------------------------------------------
  // SAVING
  // -----------------------------------------------------------------------------

  /**
   * Saves every address currently loaded in this address book.
   *
   * @access public
   * @return void
   */
  public function save() {
    foreach ($this->addresses as $aid => $address) {
      $address->save();
    }
  }

  // -----------------------------------------------------------------------------
  // REPRESENTATION
  // -----------------------------------------------------------------------------

  /**
   * Returns address book html.
   *
   * @access public
   * @return string
   *   The themed address, as HTML.
   */
  public function __toString() {
    $addresses = array();
    try {
      if (!$this->allLoaded) {
        $this->loadAll();
      }
      foreach ($this->addresses as $address) {
        $addresses[$address->getId()] = (string) $address;
      }
    }
    catch (Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
    return theme('uc_addresses_address_book', array('addresses' => $addresses, 'address_book' => $this));
  }

  // -----------------------------------------------------------------------------
  // PRIVATE METHODS: DATABASE REQUESTS
  // -----------------------------------------------------------------------------

  /**
   * Loads a single address from the database if not already loaded.
   *
   * No database call is done in these cases:
   * - Address is already loaded;
   * - All addresses are already loaded.
   *
   * @param int $type
   *   Type of the argument given, can be the address id (BY_AID) or the address nickname (BY_NAME).
   * @param mixed $arg
   *   Either the address id or the address nickname.
   *
   * @access private
   * @return void
   * @throws UcAddressesDbException
   */
  private function loadOne($type, $arg) {
    // Reasons to skip out early.
    if ($this->allLoaded) {
      return;
    }
    if (!$this->isOwned()) {
      return;
    }
    if ($type == self::BY_AID && isset($this->addresses[$arg])) {
      return;
    }
    if ($type == self::BY_NAME && $this->findByName($arg)) {
      return;
    }

    // If we're going to save an address, we'll need to know about
    // possible name collisions and what the current default
    // addresses are.
    if ($this->performanceHint == self::PERF_HINT_LOAD_ALL) {
      $this->loadAll();
      return;
    }

    // Read the database. Note that we ensure that this requested
    // address is in this address book by including $uid in the
    // query.
    if ($type == self::BY_AID) {
      $result = db_select('uc_addresses')
        ->condition('uid', $this->uid)
        ->condition('aid', $arg)
        ->fields('uc_addresses')
        ->execute();
    }
    else {
      $result = db_select('uc_addresses')
        ->condition('uid', $this->uid)
        ->condition('address_name', $arg)
        ->fields('uc_addresses')
        ->execute();
    }

    $this->dbResultToAddresses($result);
  }

  /**
   * Loads all addresses from database when they not already loaded.
   *
   * @access private
   * @return void
   */
  private function loadDefaults() {
    // Reason to skip out early.
    if ($this->defaultsLoaded) {
      return;
    }
    if ($this->allLoaded) {
      return;
    }
    if (!$this->isOwned()) {
      return;
    }

    // If the performance hint is set to load all addresses,
    // load all addresses instead.
    if ($this->performanceHint == self::PERF_HINT_LOAD_ALL) {
      $this->loadAll();
      return;
    }

    // Get all addresses for this user.
    $result = db_select('uc_addresses')
      ->condition('uid', $this->uid)
      ->condition(db_or()
        ->condition('default_shipping', 1)
        ->condition('default_billing', 1)
      )
      ->fields('uc_addresses')
      ->orderBy('created', 'ASC')
      ->execute();

    // Set flag that default addresses are loaded.
    $this->defaultsLoaded = TRUE;

    $this->dbResultToAddresses($result);
  }

  /**
   * Loads all addresses from database when they not already loaded.
   *
   * @access private
   * @return void
   */
  private function loadAll() {
    // Reason to skip out early.
    if ($this->allLoaded) {
      return;
    }
    if (!$this->isOwned()) {
      return;
    }

    // Update the performance hint setting.
    $this->performanceHint = self::PERF_HINT_LOAD_ALL;

    // Get all addresses for this user.
    $result = db_select('uc_addresses')
      ->condition('uid', $this->uid)
      ->fields('uc_addresses')
      ->orderBy('created', 'ASC')
      ->execute();

    // Set flag that all addresses are loaded.
    $this->allLoaded = TRUE;
    // Set flag that default addresses are loaded.
    $this->defaultsLoaded = TRUE;

    $this->dbResultToAddresses($result);
  }

  /**
   * Loads a single address from the database if not already loaded.
   *
   * @param int $aid
   *   The id of the address.
   *
   * @access private
   * @return boolean
   *   TRUE if the address has been loaded or found.
   *   FALSE otherwise.
   */
  private static function loadStatic($aid) {
    // Reasons to skip out early.
    // Lookup in all address books if the address is already loaded.
    foreach (self::$singleton as $addressbook) {
      if ($addressbook->addressExists($aid)) {
        return TRUE;
      }
    }

    $result = db_select('uc_addresses')
      ->condition('aid', $aid)
      ->fields('uc_addresses')
      ->orderBy('created', 'ASC')
      ->execute();

    // Create an object from the database record.
    $obj = $result->fetch();

    if (!$obj) {
      // If there is no such address record, then abort.
      return FALSE;
    }

    // Get address book for loaded user.
    $addressbook = self::get($obj->uid);

    // Create UcAddressesAddress object.
    $address = new UcAddressesAddress($addressbook, $obj);

    // Give other modules a chance to add their fields.
    module_invoke_all('uc_addresses_address_load', $address, $obj);
    // Invoke entity load hook.
    entity_get_controller('uc_addresses')->invokeLoad(array($address));
    return TRUE;
  }

  /**
   * Creates UcAddressesAddress objects from a database resource.
   *
   * @param resource $result
   *   Database result.
   *
   * @access private
   * @return void
   */
  private function dbResultToAddresses($result) {
    // Create each UcAddressesAddress object from the database record.
    $loaded_addresses = array();
    foreach ($result as $obj) {
      // Skip addresses that have already been loaded (and perhaps modified).
      if (!isset($this->addresses[$obj->aid])) {
        $address = new UcAddressesAddress($this, $obj);
        if ($address->isDefault('shipping')) {
          $this->defaultAddresses['shipping'] = $address;
        }
        if ($address->isDefault('billing')) {
          $this->defaultAddresses['billing'] = $address;
        }

        // Give other modules a chance to add their fields.
        module_invoke_all('uc_addresses_address_load', $address, $obj);
        $loaded_addresses[$obj->aid] = $address;
      }
    }
    if (count($loaded_addresses) > 0) {
      // Invoke entity load hook.
      entity_get_controller('uc_addresses')->invokeLoad($loaded_addresses);
    }
  }

  /**
   * Deletes one address.
   *
   * @param int $type
   *   Type of the argument given, can be the address id (BY_AID) or the address nickname (BY_NAME).
   * @param mixed $arg
   *   Either the address id or the address nickname.
   *
   * @access private
   * @return boolean
   *   TRUE if the address was deleted.
   *   FALSE otherwise.
   * @throws UcAddressesDbException
   */
  private function deleteOne($type, $arg) {
    // Reasons to skip out early
    if (!$this->isOwned()) {
      return FALSE;
    }

    // We can't delete an address that is a default address, so
    // we'll need to make sure this address is loaded.
    $this->loadOne($type, $arg);
    if ($type == self::BY_AID) {
      $address = $this->getAddressById($arg);
    }
    if ($type == self::BY_NAME) {
      $address = $this->getAddressByName($arg);
    }
    if (!$address) {
      return FALSE;
    }
    if ($address->isDefault('shipping') || $address->isDefault('billing')) {
      return FALSE;
    }

    // Delete the address from the database only if it is not new (else it won't exist in the db).
    if (!$address->isNew()) {
      db_delete('uc_addresses')
        ->condition('aid', $address->getId())
        ->execute();
    }

    // Remove from address book object.
    $this->removeAddressFromAddressBook($address);

    // Give other modules a chance to react on this.
    module_invoke_all('uc_addresses_address_delete', $address);
    entity_get_controller('uc_addresses')->invoke('delete', $address);

    return TRUE;
  }

  /**
   * Removes an address from this address book.
   *
   * This method is called when an address is deleted
   * or when the owner of an address is set.
   *
   * @param UcAddressesAddress $address
   *   The address to remove from the address book.
   *
   * @access private
   * @return void
   */
  private function removeAddressFromAddressBook($address) {
    $aid = $address->getId();
    if (isset($this->addresses[$aid])) {
      unset($this->addresses[$address->getId()]);
    }

    // Check default addresses array
    foreach ($this->defaultAddresses as $address_type => $defaultAddress) {
      if ($defaultAddress->getId() == $aid) {
        unset($this->defaultAddresses[$address_type]);
      }
    }
  }

  /**
   * Search for an address by giving the name.
   *
   * @param string $name
   *   The nickname of the address.
   *
   * @access private
   * @return
   *   UcAddressesAddress if address is found.
   *   FALSE otherwise.
   */
  private function findByName($name) {
    if ($name) {
      foreach ($this->addresses as $address) {
        if ($address->getName() && $address->getName() == $name) {
          return $address;
        }
      }
    }
    return FALSE;
  }
}
