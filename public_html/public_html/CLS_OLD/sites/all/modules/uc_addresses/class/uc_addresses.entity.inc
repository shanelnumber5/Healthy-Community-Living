<?php

/**
 * @file
 * Entity integration code.
 */

/**
 * Ubercart Addresses entity controller class.
 */
class UcAddressesEntityController extends EntityAPIController {
  /**
   * Implements DrupalEntityControllerInterface::load().
   *
   * @return array
   *   An array of entities.
   */
  public function load($ids = array(), $conditions = array()) {
    $entities = array();
    if (!empty($this->entityInfo['entity class'])) {
      $class = $this->entityInfo['entity class'];
    }
    else {
      throw new UcAddressesException('There is no entity class specified for the uc_addresses entity.');
    }

    foreach ($ids as $id) {
      $address = UcAddressesAddressBook::loadAddress($id);
      if ($address) {
        $entities[$id] = $address;
      }
    }

    return $entities;
  }

  /**
   * Invokes load hook.
   *
   * This is a bit of a hack, because this makes the protected
   * method attachLoad() public by providing this wrapper method.
   * As this method is only called by the UcAddressesAddressBook
   * class, which takes control about address loading, we should
   * accept that the hack is needed.
   *
   * Please don't call this method in other places.
   *
   * @param array $entities
   *   An array of UcAddressesAddress instances.
   *
   * @return void
   */
  public function invokeLoad($entities) {
    if (!empty($entities)) {
      $this->attachLoad($entities);
    }
  }

  /**
   * Overrides EntityAPIController::create().
   *
   * Creates a new address entity and makes sure it contains
   * some default values for it.
   *
   * @param array $values
   *   An array of values to set, keyed by property name.
   *
   * @return UcAddressesAddress
   *   A new instance of the entity type.
   */
  public function create(array $values = array()) {
    if (isset($values['uid'])) {
      // If the owner is given, an new address is created for the specific user.
      $entity = UcAddressesAddressBook::get($values['uid'])->addAddress();
    }
    else {
      // Else, a new unowned address is given.
      $entity = UcAddressesAddressBook::newAddress();
    }
    $entity->setMultipleFields($values);
    return $entity;
  }

  /**
   * Implements EntityAPIControllerInterface::save().
   *
   * Uses UcAddressesAddress save logics.
   *
   * @param $transaction
   *   (optional) a DatabaseTransaction object to use. Allows overrides to pass
   *   in their transaction object.
   *
   * @return void
   */
  public function save($entity, DatabaseTransaction $transaction = NULL) {
    $transaction = isset($transaction) ? $transaction : db_transaction();
    try {
      $result = $entity->save();

      // Ignore slave server temporarily.
      db_ignore_slave();
      unset($entity->is_new);
      unset($entity->original);
    }
    catch (Exception $e) {
      $transaction->rollback();
      watchdog_exception($this->entityType, $e);
      throw $e;
    }
    return $result;
  }

  /**
   * Implements EntityAPIControllerInterface::delete().
   *
   * Overridden to follow Ubercart Addresses logics.
   *
   * @return void
   */
  public function delete($ids, DatabaseTransaction $transaction = NULL) {
    $entities = $ids ? $this->load($ids) : FALSE;
    if (!$entities) {
      // Do nothing, in case invalid or no ids have been passed.
      return;
    }

    try {
      foreach ($entities as $entity) {
        $entity->delete();
      }
      // Ignore slave server temporarily.
      db_ignore_slave();
    }
    catch (Exception $e) {
      if (isset($transaction)) {
        $transaction->rollback();
      }
      watchdog_exception($this->entityType, $e);
      throw $e;
    }
  }

  /**
   * Overrides EntityAPIController::buildContent().
   *
   * @return array
   *   The renderable array.
   */
  public function buildContent($entity, $view_mode = 'full', $langcode = NULL, $content = array()) {
    $address = $entity;
    $address_user = user_load($address->getUserId());
    $options = array();

    // Check address access.
    if (UcAddressesPermissions::canViewAddress($address_user, $address)) {
      // Check if address may be edited too.
      if (UcAddressesPermissions::canEditAddress($address_user, $address)) {
        // Show edit link.
        $options['edit_link'] = TRUE;
      }
      if (UcAddressesPermissions::canDeleteAddress($address_user, $address)) {
        // Show delete link.
        $options['delete_link'] = TRUE;
      }

      $content['#theme'] = 'uc_addresses_list_address';
      $content['#address'] = $address;
      $content['#options'] = $options;
      $content['#attached'] = array(
        'css' => array(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css'),
      );
    }
    else {
      $content['#access'] = FALSE;
    }

    return parent::buildContent($entity, $view_mode, $langcode, $content);
  }
}
