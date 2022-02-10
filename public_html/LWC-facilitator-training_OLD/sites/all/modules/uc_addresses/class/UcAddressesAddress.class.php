<?php

/**
 * @file
 * Contains the UcAddressesAddress class.
 */

/**
 * The main address class used by uc_addresses (and extension modules).
 *
 * This is the main address class used by Ubercart Addresses. It's goal is to
 * provide specific address book features, such as an address nickname and flags
 * for being the default shipping and/or the default billing address.
 *
 * Each instance is created through the address book, so the address book is
 * able to keep track of all the addresses it contains.
 *
 * The class extends UcAddressesSchemaAddress.
 */
class UcAddressesAddress extends UcAddressesSchemaAddress {
  // -----------------------------------------------------------------------------
  // STATIC PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * Which ID a new address will get when constructed.
   *
   * This value will be decreased with 1 every time
   * a new address is constructed.
   * A new address in this case is an address not coming
   * from the database.
   *
   * @var int
   * @access private
   * @static
   */
  static private $nextNewAid = -1;

  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * The address book the address belongs to.
   *
   * @var UcAddressesAddressBook
   * @access private
   */
  private $addressBook = NULL;

  // -----------------------------------------------------------------------------
  // CONSTRUCT
  // -----------------------------------------------------------------------------

  /**
   * UcAddressesAddress object constructor.
   *
   * @param UcAddressesAddressBook $addressBook
   *   An instance of UcAddressesAddressBook.
   * @param object $schemaAddress
   *   Values to fill the address with.
   *
   * @access public
   * @return void
   */
  public function __construct(UcAddressesAddressBook $addressBook, $schemaAddress = NULL) {
    parent::__construct($schemaAddress);
    $this->addressBook = $addressBook;

    if (!is_object($schemaAddress) || !$schemaAddress->aid) {
      // We always need an ID.
      $this->getSchemaAddress()->aid = self::$nextNewAid--;
    }

    // Set other given values.
    if ($schemaAddress) {
      foreach ($schemaAddress as $fieldName => $value) {
        $this->privSetUcAddressField($fieldName, $value);
      }
    }

    if ($this->getSchemaAddress()->aid > 0) {
      // If an address is just loaded, mark this instance as 'clean' (= unchanged).
      $this->clearDirty();
    }

    // All addresses need to be contained by an address book.
    $addressBook->addAddress($this);
  }

  /**
   * Tells which members may kept when the address is being serialized.
   *
   * @access public
   * @return array
   *   An array of members to keep upon serialization.
   */
  public function __sleep() {
    $this->getSchemaAddress()->uid = $this->getUserId();
    return parent::__sleep();
  }

  /**
   * Restore variables when the address is unserialized.
   *
   * @access public
   * @return void
   */
  public function __wakeup() {
    parent::__wakeup();
    $this->addressBook = UcAddressesAddressBook::get($this->getSchemaAddress()->uid);
    if ($this->getId() <= self::$nextNewAid) {
      self::$nextNewAid = $this->getId() - 1;
    }
    try {
      $this->addressBook->addAddress($this);
    }
    catch (UcAddressesException $e) {
      // Ignore any exceptions.
    }
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
  public static function newAddress() {
    return UcAddressesAddressBook::newAddress();
  }

  /**
   * Make a copy of this address.
   *
   * This method only copies the aggregrated schema object over.
   * The fields aid, address_name, default_shipping and default_billing
   * will be emptied.
   *
   * @param UcAddressesAddressBook $addressBook
   *   (optional) The address book to copy the address to.
   *   Defaults to the address book the current address belongs to.
   *
   * @access public
   * @return UcAddressesAddress
   *   A new instance of UcAddressesAddress.
   */
  public function copyAddress(UcAddressesAddressBook $addressBook = NULL) {
    if (!$addressBook) {
      $addressBook = $this->addressBook;
    }

    // Get a copy of our schema address and empty the fields aid,
    // address_name, default_shipping and default_billing.
    $schemaAddress = clone $this->getSchemaAddress();
    $schemaAddress->aid = 0;
    $schemaAddress->address_name = '';
    $schemaAddress->default_shipping = 0;
    $schemaAddress->default_billing = 0;
    $schemaAddress->uid = $addressBook->getUserId();

    // Create a new address.
    $address = new UcAddressesAddress($addressBook, $schemaAddress);

    return $address;
  }

  // -----------------------------------------------------------------------------
  // ADDRESS METHODS
  // -----------------------------------------------------------------------------

  /**
   * Returns the address book the address is in.
   *
   * @access public
   * @return UcAddressesAddressBook
   *   The address book attached to this address.
   */
  public function getAddressBook() {
    return $this->addressBook;
  }

  /**
   * Returns address ID.
   *
   * @access public
   * @return int
   *   The address ID.
   */
  public function getId() {
    return $this->getSchemaAddress()->aid;
  }

  /**
   * Alias of getId().
   *
   * @return int
   *   The address ID.
   * @see entity_id()
   */
  public function identifier() {
    return $this->getId();
  }

  /**
   * Returns owner of this address.
   *
   * @access public
   * @return int
   *   ID of the owner of this address.
   */
  public function getUserId() {
    return $this->addressBook->getUserId();
  }

  /**
   * Checks if the address is owned by an user.
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
   * Checks if the address is a temporary unsaved
   * address.
   *
   * The address is new if it has a negative ID.
   *
   * @access public
   * @return boolean
   *   TRUE if the address is new.
   *   FALSE if the address is saved in the database.
   */
  public function isNew() {
    return ($this->getId() < 1);
  }

  /**
   * Returns address name if given.
   *
   * If the address has no name, it will use the
   * address format as label instead.
   *
   * @return string
   *   The address name.
   * @todo better alternative for address name?
   */
  public function label() {
    $name = $this->getName();
    if ($name) {
      return $name;
    }
    return preg_replace('/<.*?>/', ', ', trim(uc_addresses_format_address($this)));
  }

  /**
   * Returns the uri of the address.
   *
   * @return array
   *   The addres uri.
   */
  public function uri() {
    return array('path' => 'user/' . $this->getUserId() . '/addresses/' . $this->getId());
  }

  // -----------------------------------------------------------------------------
  // uc_addresses features
  // -----------------------------------------------------------------------------

  /**
   * Override of UcAddressesSchemaAddress::getField().
   *
   * Getting some of the schema fields differently.
   *
   * @param string $fieldName
   *   The name of the field whose value we want.
   *
   * @access public
   * @return mixed
   *   The field value.
   * @throws UcAddressInvalidFieldException
   */
  public function getField($fieldName) {
    switch ($fieldName) {
      case 'aid':
        return $this->getId();

      case 'uid':
        return $this->getUserId();
    }
    return parent::getField($fieldName);
  }

  /**
   * Override of UcAddressesSchemaAddress::setField().
   *
   * Prevents setting some schema fields directly.
   *
   * @param string $fieldName
   *   The name of the field whose value we will set.
   * @param mixed $value
   *   The value to which to set the field.
   *
   * @access public
   * @return void
   * @throws UcAddressInvalidFieldException
   */
  public function setField($fieldName, $value) {
    switch ($fieldName) {
      case 'aid':
        // Don't set.
        // @todo Throw an Exception here?
        break;

      case 'uid':
        // Only set if the address is unowned. Else, ignore it.
        // @todo Throw an Exception here?
        if (!$this->isOwned()) {
          $this->setOwner($value);
        }
        break;

      case 'address_name':
        $this->setName($value);
        break;

      case 'default_shipping':
        if ($value) {
          $this->setAsDefault('shipping');
        }
        break;

      case 'default_billing':
        if ($value) {
          $this->setAsDefault('billing');
        }
        break;

      default:
        parent::setField($fieldName, $value);
        break;
    }
  }

  /**
   * Returns the nickname of the address.
   *
   * @access public
   * @return string
   *   The name of this address.
   */
  public function getName() {
    return $this->getSchemaAddress()->address_name;
  }

  /**
   * Sets the nickname of the address.
   *
   * @param string $name
   *   The name to give to the address.
   *
   * @access public
   * @return boolean
   *   TRUE if setting name was succesful.
   *   FALSE otherwise.
   */
  public function setName($name) {
    if ($this->getName() === $name) {
      // Address already has this name, do nothing
      return TRUE;
    }
    if ($this->addressBook->setAddressName($this, $name)) {
      $this->setDirty();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Tells if the address is a default address of type $type.
   *
   * @param string $type
   *   The type of default.
   *
   * @access public
   * @return boolean
   *   TRUE if the address is a default address of type $type.
   *   FALSE otherwise.
   */
  public function isDefault($type = 'billing') {
    return $this->getSchemaAddress()->{'default_' . $type};
  }

  /**
   * Sets this address as a default address of type $type
   *
   * @param string $type
   *   The type of default to set (e.g., delivery, billing).
   *
   * @access public
   * @return void
   */
  public function setAsDefault($type = 'billing') {
    if ($this->isDefault($type)) {
      // Is already default $type, do nothing so the address doesn't
      // get unnecessary "dirty".
      return;
    }
    $this->addressBook->setAddressAsDefault($this, $type);
    $this->setDirty();
  }

  /**
   * Changes the owner of this address.
   *
   * @param int $uid
   *   The new owner of the address.
   *
   * @access public
   * @return UcAddressesAddressBook
   *   The address book the address will belong to;
   *   or NULL if the address was already owned.
   */
  public function setOwner($uid) {
    // The owner of this address may only be changed if it doesn't belong to
    // anyone yet.
    if ($this->isOwned()) {
      // Changing the owner not allowed.
      return;
    }

    // Setting the owner goes via the address book.
    return $this->addressBook->setAddressOwner($this, $uid);
  }

  /**
   * Deletes address from address book.
   *
   * @access public
   * @return boolean
   *   TRUE if the address was deleted.
   *   FALSE otherwise.
   */
  public function delete() {
    return $this->addressBook->deleteAddress($this);
  }

  // -----------------------------------------------------------------------------
  // SAVING
  // -----------------------------------------------------------------------------

  /**
   * Saves address if address is marked as 'dirty'.
   *
   * @access public
   * @return void
   * @throws UcAddressesDbException
   * @throws UcAddressesUnownedException
   */
  public function save() {
    if (!$this->isOwned()) {
      throw new UcAddressesUnownedException(t('The address can not be saved because it is not owned by an user.'));
    }

    if ($this->isDirty()) {
      // Allow other modules to alter the address before saving.
      module_invoke_all('uc_addresses_address_presave', $this);
      entity_get_controller('uc_addresses')->invoke('presave', $this);

      $address = $this->getSchemaAddress();
      $address->modified = REQUEST_TIME;
      $address->uid = $this->getUserId();
      if ($address->aid < 0) {
        unset($address->aid);
        $address->created = REQUEST_TIME;
        $result = drupal_write_record('uc_addresses', $address);
        $hook = 'insert';

        // Tell address book the address now has a definitive ID.
        $this->addressBook->updateAddress($this);
      }
      else {
        $result = drupal_write_record('uc_addresses', $address, array('aid'));
        $hook = 'update';
      }
      if ($result === FALSE) {
        throw new UcAddressesDbException(t('Failed to write address with id = %aid', array('%aid' => $address->aid)));
      }
      // Address is saved and no longer 'dirty'.
      $this->clearDirty();

      // If this a default address, ensure no other addresses are marked as
      // default in the database.
      if (function_exists('uc_addresses_address_types')) {
        // During installation the function 'uc_addresses_address_types()'
        // may not be available yet.
        $default_types = uc_addresses_address_types();
      }
      else {
        $default_types = array('shipping', 'billing');
      }
      foreach ($default_types as $default_type) {
        if ($this->isDefault($default_type)) {
          // Mark all addresses of the address owner as non-default except
          // the current address.
          db_update('uc_addresses')
            ->fields(array(
              'default_' . $default_type => 0,
            ))
            ->condition('uid', $address->uid)
            ->condition('aid', $address->aid, '!=')
            ->execute();
        }
      }

      // Notify other modules that an address has been saved.
      module_invoke_all('uc_addresses_address_' . $hook, $this);
      entity_get_controller('uc_addresses')->invoke($hook, $this);
    }
  }

  // -----------------------------------------------------------------------------
  // REPRESENTATION
  // -----------------------------------------------------------------------------

  /**
   * Returns address html.
   *
   * @access public
   * @return string
   *   The themed address, as HTML.
   * @todo Should maybe return "address display" instead.
   */
  public function __toString() {
    return theme('uc_addresses_list_address', array('address' => $this));
  }

  /**
   * Export address instance to PHP code.
   *
   * @return string
   *   PHP-code to reconstruct the address with.
   */
  public function varExport() {
    $data = $this->getRawFieldData();
    $code = '$fields = ' . var_export($data, TRUE) . ";\n";
    $uid = $this->getUserId();
    if ($uid) {
      $code .= '$address = UcAddressesAddressBook::get(' . $uid . ')->addAddress();' . "\n";
    }
    else {
      $code .= '$address = UcAddressesAddressBook::newAddress();' . "\n";
    }
    $code .= '$address->setMultipleFields($fields);' . "\n";
    return $code;
  }

  // -----------------------------------------------------------------------------
  // Low-level calls intended only for UcAddressesAddressBook
  // -----------------------------------------------------------------------------

  /**
   * Sets a private variable.
   *
   * This method should only be called by the address book.
   *
   * @param string $fieldName
   *   The field to set a value for.
   * @param string $value
   *   The value to set.
   *
   * @access public
   * @return void
   */
  public function privSetUcAddressField($fieldName, $value) {
    switch ($fieldName) {
      case 'name':
      case 'address_name':
        $this->getSchemaAddress()->address_name = $value;
        break;

      case 'shipping':
      case 'default_shipping':
        $this->getSchemaAddress()->default_shipping = ($value) ? TRUE : FALSE;
        break;

      case 'billing':
      case 'default_billing':
        $this->getSchemaAddress()->default_billing = ($value) ? TRUE : FALSE;
        break;
    }
    $this->setDirty();
  }

  /**
   * Modify the address book of this address.
   *
   * This can only be done if the address book already accepted
   * the address as one of it's addresses.
   *
   * @param UcAddressesAddressBook $addressBook
   *   The address book to move this address to.
   *
   * @access public
   * @return boolean
   *   TRUE on success.
   *   FALSE otherwise.
   */
  public function privChangeAddressBook(UcAddressesAddressBook $addressBook) {
    foreach ($addressBook->getAddresses() as $address) {
      if ($address === $this) {
        // Address appears in address book, changing address book approved.
        $this->addressBook = $addressBook;
        return TRUE;
      }
    }
    return FALSE;
  }
}
