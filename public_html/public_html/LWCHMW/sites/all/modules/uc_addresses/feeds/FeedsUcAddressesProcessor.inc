<?php

/**
 * @file
 * Contains the FeedsUcAddressesProcessor class.
 */

/**
 * A processor for importing data belonging to an Ubercart Addresses address.
 */
class FeedsUcAddressesProcessor extends FeedsProcessor {
  /**
   * Define entity type.
   */
  public function entityType() {
    return 'uc_addresses';
  }

  /**
   * Implements FeedsProcessor::entityInfo().
   */
  protected function entityInfo() {
    $info = parent::entityInfo();
    $info['label'] = t('Address');
    $info['label plural'] = t('Addresses');
    return $info;
  }

  /**
   * Creates a new address in memory and returns it.
   */
  protected function newEntity(FeedsSource $source) {
    $address = UcAddressesAddressBook::newAddress();
    return $address;
  }

  /**
   * Loads an existing address.
   */
  protected function entityLoad(FeedsSource $source, $aid) {
    return UcAddressesAddressBook::loadAddress($aid);
  }

  /**
   * Validates an address.
   */
  protected function entityValidate($address) {
    if (!$address->isOwned()) {
      throw new FeedsValidationException(t('The address can not be saved because the user it should belong to is not found.'));
    }
  }

  /**
   * Save an address.
   */
  protected function entitySave($address) {
    if ($address->isOwned()) {
      $address->save();
      $address->feeds_item->entity_id = $address->getId();
    }
  }

  /**
   * Deletes a series of addresses.
   */
  protected function entityDeleteMultiple($aids) {
    foreach ($aids as $index => $aid) {
      UcAddressesAddressBook::loadAddress($aid)->delete();
    }
  }

  /**
   * Declare default configuration.
   */
  public function configDefaults() {
    $defaults = parent::configDefaults();
    $defaults += array(
      'mark_as_default_billing' => TRUE,
      'mark_as_default_shipping' => TRUE,
    );
    return $defaults;
  }

  /**
   * Overrides parent::configForm().
   */
  public function configForm(&$form_state) {
    $form = parent::configForm($form_state);
    unset($form['input_format']);

    // Setting to automatically make addresses default billing and default shipping.
    $form['mark_as_default_billing'] = array(
      '#type' => 'checkbox',
      '#title' => t('Automatically mark addresses as default billing.'),
      '#description' => t('If enabled, all users for which addresses are imported, get one default billing address.'),
      '#default_value' => $this->config['mark_as_default_billing'],
    );
    $form['mark_as_default_shipping'] = array(
      '#type' => 'checkbox',
      '#title' => t('Automatically mark addresses as default shipping.'),
      '#description' => t('If enabled, all users for which addresses are imported, get one default shipping address.'),
      '#default_value' => $this->config['mark_as_default_shipping'],
    );

    return $form;
  }

  /**
   * Override setTargetElement to operate on a target item that is an address.
   */
  public function setTargetElement(FeedsSource $source, $target_address, $target_element, $value) {
    switch ($target_element) {
      case 'aid':
        // Don't set.
        return;

      case 'user_name':
        $user = user_load_by_name($value);
        if ($user) {
          $target_address->setOwner($user->uid);
        }
        return;

      case 'user_mail':
        $user = user_load_by_mail($value);
        if ($user) {
          $target_address->setOwner($user->uid);
        }
        return;

      case 'uid':
        $target_address->setOwner($value);
        return;
    }

    // Set target through the field handlers.
    $handler = NULL;
    $fieldname = $target_element;
    $format = NULL;
    // Check if the target element represents a existing address field.
    if (UcAddressesSchemaAddress::fieldExists($fieldname)) {
      $handler = uc_addresses_get_address_field_handler($target_address, $fieldname, 'feeds');
    }
    else {
      // Check if the target element represents a particular format of an existing address field.
      $pieces = explode(':', $target_element);
      if (count($pieces) >= 2) {
        $fieldname = $pieces[0];
        array_shift($pieces);
        $format = implode(':', $pieces);
        if (UcAddressesSchemaAddress::fieldExists($fieldname)) {
          // Get the handler for the field.
          $handler = uc_addresses_get_address_field_handler($target_address, $fieldname, 'feeds');
        }
      }
    }
    if ($handler instanceof UcAddressesFieldHandler) {
      // Use the handler to set the right value.
      $handler->mapValue($value, $format);
    }
    else {
      parent::setTargetElement($source, $target_address, $target_element, $value);
    }
  }

  /**
   * Return available mapping targets.
   */
  public function getMappingTargets() {
    $targets = parent::getMappingTargets();
    $address = UcAddressesAddress::newAddress();
    $fields = uc_addresses_get_address_field_handler_instances($address, 'feeds');
    foreach ($fields as $fieldname => $handler) {
      $targets = array_merge($targets, $handler->getMappingTargets());
    }

    // Extra targets for user.
    $targets['user_name'] = array(
      'name' => t('Username'),
      'description' => t('The Drupal username of the address owner.'),
    );
    $targets['user_mail'] = array(
      'name' => t('User email'),
      'description' => t('The email address of the address owner.'),
    );

    // Set unique targets.
    $targets['aid']['optional_unique'] = TRUE;

    // Allow other modules to modify targets.
    if (method_exists($this, 'getHookTargets')) {
      $this->getHookTargets($targets);
    }

    return $targets;
  }

  /**
   * Get address ID of an existing feed item address if available.
   */
  protected function existingEntityId(FeedsSource $source, FeedsParserResult $result) {
    $aid = parent::existingEntityId($source, $result);
    if ($aid) {
      return $aid;
    }

    // Iterate through all unique targets and test whether they do already
    // exist in the database.
    foreach ($this->uniqueTargets($source, $result) as $target => $value) {
      switch ($target) {
        case 'aid':
          $aid = db_select('uc_addresses')
            ->fields('uc_addresses', array('aid'))
            ->condition('aid', $value)
            ->execute()
            ->fetchField();
          break;
      }
      if ($aid) {
        // Return with the first address ID found.
        return $aid;
      }
    }
    return 0;
  }

  /**
   * Overrides FeedsProcessor::createLogMessage().
   *
   * Creates a log message for when an exception occured during import.
   *
   * @param Exception $e
   *   The exception that was throwned during processing the item.
   * @param $entity
   *   The entity object.
   * @param $item
   *   The parser result for this entity.
   *
   * @return string
   *   The message to log.
   */
  protected function createLogMessage(Exception $e, $entity, $item) {
    include_once DRUPAL_ROOT . '/includes/utility.inc';
    $message = $e->getMessage();
    $message .= '<h3>Original item</h3>';
    $message .= '<pre>' . drupal_var_export($item) . '</pre>';
    $message .= '<h3>Address</h3>';
    $message .= '<pre>' . $entity->varExport() . '</pre>';
    return $message;
  }
}
