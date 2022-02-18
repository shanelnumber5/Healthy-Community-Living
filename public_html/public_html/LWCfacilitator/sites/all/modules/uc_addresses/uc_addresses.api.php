<?php

/**
 * @file
 * These hooks are invoked by the Ubercart Addresses module.
 * @todo more documentation needed for hook_uc_addresses_field_handlers().
 * @todo Document the rest of the API.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * With this hook you can register new field handlers that can be
 * used by fields in address edit forms.
 *
 * A field handler is a class that extends UcAddressesFormFieldHandler.
 * The declaration of field handlers is based on the CTools plugin API.
 *
 * For a working example, look at uc_addresses_uc_addresses_field_handlers()
 * in the file uc_addresses.uc_addresses_fields.inc.
 *
 * @return array
 *   A list of field handler definitions.
 */
function hook_uc_addresses_field_handlers() {
  $path = drupal_get_path('module', 'mymodule') . '/handlers';
  $info = array();

  $info['MyCustomFieldHandler'] = array(
    'handler' => array(
      'parent' => 'UcAddressesFieldHandler',
      'class' => 'MyCustomFieldHandler',
      'file' => 'MyCustomFieldHandler.class.php',
      'path' => $path,
    ),
  );

  return $info;
}

/**
 * This hook allows you to register extra fields to be used in
 * all address edit forms.
 *
 * For a working example, look at uc_addresses_uc_addresses_fields()
 * in the file uc_addresses.uc_addresses_fields.inc.
 *
 * @return array
 *   An associative array containing:
 *   - title: the title of the field, safe for output.
 *   - type: (optional) The data type of the property.
 *   - handler: handler class, registered through
 *     hook_uc_addresses_field_handlers().
 *   - display_settings: (optional) An array of contexts to show or hide the
 *     field on:
 *     - default: boolean, if it may be displayed by default.
 *     - address_form: boolean, if it may be displayed on the address edit form.
 *     - address_view: boolean, if it may be displayed on the address book page.
 *     - checkout_form: boolean, if it may be displayed on the checkout page.
 *     - checkout_review: boolean, if it may be displayed on the checkout review
 *       page.
 *     - order_form: boolean, if it may be displayed on the order edit page.
 *     - order_view: boolean, if it may be displayed on order view pages.
 *     Adding display settings to the field definition is optional.
 *     If you don't set this, assumed is that the field may be showed
 *     everywhere.
 *   - compare: (optional) boolean, may this field be used in address
 *     comparisons?
 *     An address comparison is done to avoid having double addresses in the
 *     address book.
 *   - (additional data used by Entity API, see hook_entity_property_info().)
 *
 * Optionally you can define extra properties in the definition. Properties can
 * be reached from within the handler by calling getProperty(). When a handler
 * uses extra properties, these properties will be required. Check the
 * documentation of the handler to see which extra properties it requires.
 *
 * Since each field is also a property of the uc_addresses entity, the
 * definition for each field will also be passed to Entity API as metadata. This
 * means that implementing hook_entity_property_info() separately to define
 * additional (or similar) information about your field is not needed. Your
 * field will automatically become available in Rules, for example.
 * However, you may need to define extra properties for your field in order to
 * let it operate properly in entity related functionalities. Check the
 * documentation of hook_entity_property_info() to see which extra properties
 * you can define.
 *
 * @see hook_uc_addresses_field_handlers()
 * @see hook_entity_property_info()
 */
function hook_uc_addresses_fields() {
  // Example: register my own field.
  return array(
    'myfield' => array(
      'title' => t('My field'),
      'type' => 'text',
      'handler' => 'MyCustomFieldHandler',
      'display_settings' => array(
        'default' => TRUE, // Display it by default.
        'address_form' => TRUE, // Display it on the address edit form.
        'address_view' => TRUE, // Display it in the address book.
        'checkout_form' => FALSE, // Don't display during checkout.
        'checkout_review' => FALSE, // Don't display at checkout review.
        'order_form' => TRUE, // Display on order edit forms.
        'order_view' => TRUE, // Display on order view pages.
      ),
      'compare' => TRUE, // Field is used in address comparisons.
    ),
  );
}

/**
 * When all modules have registered their fields, you have
 * a chance to alter the definitions with this hook.
 *
 * @param array $fields
 *   A list of field definitions registered through hook_uc_addresses_fields().
 *
 * @return void
 */
function hook_uc_addresses_fields_alter(&$fields) {
  // Change the handler of my custom field.
  $fields['myfield']['handler'] = 'MyOtherCustomFieldHandler';
}

/**
 * This hook allows to you alter a uc_addresses_address field element.
 *
 * This is useful if you want to make a change to address edit forms
 * that's applyable for all places it appears.
 *
 * The address object the field element is for can be find in:
 * $element['#uc_addresses_address']
 *
 * @param array $element
 *   The form element, contains several subfields.
 *
 * @return void
 */
function hook_uc_addresses_address_field_alter(&$element) {
  // Add extra validation if the address of this element is a default billing address
  $address = $element['#uc_addresses_address'];
  if ($address->isDefault('billing')) {
    $element['#element_validate'][] = 'mymodule_uc_addresses_address_validate';
  }
}

/**
 * This hook allows you to alter the address field listing before
 * it's being displayed.
 *
 * Examples of where the addresses can be listed:
 * - the address book
 * - the checkout review page
 * - order view pages
 *
 * @param array $fields
 *   An array containing the field data like this:
 *   - fieldname (array):
 *     - title => field title (string)
 *     - 'data' => field value (string)
 *     - '#weight' => weight (int)
 * @param UcAddressesAddress $address
 *   The address object
 * @param string $context
 *   The context in which the fields will be displayed:
 *   - address_view: the address book
 *   - checkout_review: the checkout review page
 *   - order_view: order view pages
 *
 * @return void
 */
function hook_uc_addresses_preprocess_address_alter(&$fields, $address, $context) {
  // Example 1: add extra data in case this is the default shipping address.
  if ($address->isDefault('shipping')) {
    $fields['mydata'] = array(
      'title' => t('Title'),
      'data' => t('Default shipping address'),
      '#weight' => 1,
    );
  }

  // Example 2: move my own field to the top.
  if (isset($fields['myfield'])) {
    $fields['myfield']['#weight'] = -20;
  }
}

/**
 * This hook allows you to act on addresses being loaded from the database.
 *
 * @param UcAddressesAddress $address
 *   The address object.
 * @param object $obj
 *   The fetched database record.
 *
 * @return void
 */
function hook_uc_addresses_address_load($address, $obj) {
  // Example: set a value for my custom added field (through hook_uc_addresses_fields()).
  $address->setField('myfield', 'myvalue');
}

/**
 * This hook allows you to act on addresses being attached on an order.
 *
 * @param object $order
 *   The order to which addresses are attached.
 *
 * @return void
 */
function hook_uc_addresses_order_load($order) {
  // Example: set a value for my custom added field (through hook_uc_addresses_fields()).
  if (isset($order->uc_addresses['shipping'])) {
    $order->uc_addresses['shipping']->setField('myfield', 'myvalue');
  }
  if (isset($order->uc_addresses['billing'])) {
    $order->uc_addresses['billing']->setField('myfield', 'myvalue');
  }
}

/**
 * This hook allows you alter the address just before it's saved.
 *
 * @param UcAddressesAddress $address
 *   The address object.
 *
 * @return void
 */
function hook_uc_addresses_address_presave($address) {
  // Example: set a nickname for this address if there is none.
  if ($address->getName() == '') {
    $nickname = 'my address name';
    if (!$address->setName($nickname)) {
      // Try an other name if this nickname is already used.
      $numb = 2;
      $other_nickname = $nickname . ' ' . $numb++;
      while (!$address->setName($other_nickname)) {
        $other_nickname = $nickname . ' ' . $numb++;
      }
    }
  }
}

/**
 * This hook allows you to respond to creation of a new address.
 *
 * @param UcAddressesAddress $address
 *   The address object.
 *
 * @return void
 */
function hook_uc_addresses_address_insert($address) {
  // Example: get the value of my custom field and insert it in my own table.
  $record = array(
    'aid' => $address->getId(),
    'myfield' => $address->getField('myfield'),
  );
  drupal_write_record('mydbtable', $record);
}

/**
 * This hook allows you to respond to updates to an address.
 *
 * @param UcAddressesAddress $address
 *   The address object.
 *
 * @return void
 */
function hook_uc_addresses_address_update($address) {
  // Example: get the value of my custom field and update it in my own table.
  $record = array(
    'aid' => $address->getId(),
    'myfield' => $address->getField('myfield'),
  );
  drupal_write_record('mydbtable', $record, array('aid'));
}

/**
 * This hook allows you to respond to address deletion.
 *
 * @param UcAddressesAddress $address
 *   The address object.
 *
 * @return void
 */
function hook_uc_addresses_address_delete($address) {
  // Example: delete the value from my table.
  db_delete('mydbtable')
    ->condition('aid', $address->getId())
    ->execute();
}

/**
 * This hook allows you to prevent a certain address from being viewed.
 *
 * Don't use this hook if you want to prevent viewing addresses for users
 * with a certain role. You can use the permission settings for that.
 *
 * If you want the address not to be viewed return FALSE.
 * Return TRUE in all other cases.
 * WARNING: If you don't return TRUE, then no address may be viewed.
 *
 * Note that this hook is only invoked when permissions are checked and not
 * when the address itself is displayed (e.g., through theme
 * ('uc_addresses_list_address')).
 *
 * @param object $address_user
 *   The owner of the address.
 * @param UcAddressesAddress $address
 *   (optional) Address object.
 * @param object $account
 *   The account to check access for.
 *
 * @return boolean
 *   FALSE if the account may not view the address or any address from
 *   the address user if no address is passed.
 *   TRUE otherwise.
 */
function hook_uc_addresses_may_view($address_user, $address, $account) {
  // No specific restrictions for viewing addresses.
  return TRUE;
}

/**
 * This hook allows you to prevent a certain address from being edited.
 *
 * Don't use this hook if you want to prevent editing addresses for users
 * with a certain role. You can use the permission settings for that.
 *
 * If you want the address not to be edited return FALSE.
 * Return TRUE in all other cases.
 * WARNING: If you don't return TRUE, then no address may be edited.
 *
 * Note that this hook is only invoked when permissions are checked and not
 * when changes to an address are done programmatically.
 *
 * @param object $address_user
 *   The owner of the address.
 * @param UcAddressesAddress $address
 *   (optional) Address object.
 * @param object $account
 *   The account to check access for.
 *
 * @return boolean
 *   FALSE if the account may not edit the address or any address from
 *   the address user if no address is passed.
 *   TRUE otherwise.
 */
function hook_uc_addresses_may_edit($address_user, $address, $account) {
  // Example: don't allow editing of default addresses.
  if ($address instanceof UcAddressesAddress) {
    if ($address->isDefault('shipping') || $address->isDefault('billing')) {
      return FALSE;
    }
  }
  // In all other cases, the address may be edited.
  return TRUE;
}

/**
 * This hook allows you to prevent a certain address from being deleted.
 *
 * Don't use this hook if you want to prevent deleting addresses for users
 * with a certain role. You can use the permission settings for that.
 * Default addresses are always protected from being deleted.
 *
 * If you want the address not to be deleted return FALSE.
 * Return TRUE in all other cases.
 * WARNING: If you don't return TRUE, then no address may be deleted.
 *
 * Note that this hook is only invoked when permissions are checked and not
 * when an address is deleted programmatically.
 *
 * @param object $address_user
 *   The owner of the address.
 * @param UcAddressesAddress $address
 *   (optional) Address object.
 * @param object $account
 *   The account to check access for.
 *
 * @return boolean
 *   FALSE if the account may not delete the address or any address from
 *   the address user if no address is passed.
 *   TRUE otherwise.
 */
function hook_uc_addresses_may_delete($address_user, $address, $account) {
  // No specific restrictions for deleting addresses.
  return TRUE;
}

/**
 * With this hook you can deliver an array of addresses on which the user
 * can select one at checkout or when editing the order, depending on the
 * context $context.
 *
 * You can return an array of address arrays or an array of UcAddressesAddress
 * instances.
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
 *   An array of address arrays or an array of UcAddressesAddress instances.
 */
function hook_uc_addresses_select_addresses($uid, $context, $type) {
  // Create and fill an UcAddressesAddress instance.
  $address = UcAddressesAddressBook::newAddress();
  $address->setMultipleFields(
    array(
      'first_name' => '',
      'last_name' => '',
      'phone' => '',
      'company' => '',
      'street1' => '',
      'street2' => '',
      'city' => '',
      'zone' => 0,
      'country' => variable_get('uc_store_country', 840),
      'postal_code' => '',
    )
  );

  // Return an array of address arrays or an array of UcAddressesAddress instances.
  return array(
    // Example: an UcAddressesAddress instance (created earlier).
    $address,
    // Example: an address array.
    array(
      'first_name' => '',
      'last_name' => '',
      'phone' => '',
      'company' => '',
      'street1' => '',
      'street2' => '',
      'city' => '',
      'zone' => 0,
      'country' => variable_get('uc_store_country', 840),
      'postal_code' => '',
    ),
  );
}

/**
 * This hook allows you to alter the addresses that the user can choose from
 * at checkout or when editing the order, depending on the context $context.
 *
 * You will get an array of UcAddressesAddress instances where some of them
 * may be saved in the user's address book and others come from other sources
 * (such as previous orders). Which addresses you get depends on what addresses
 * are delivered by modules that implement hook_uc_addresses_select_addresses()
 * and if the user has any saved addresses in his/her address book.
 *
 * You can find out from which module the address came by checking
 * $address->module. That property is only available in this context, normally
 * UcAddressesAddress instances don't have that property set.
 *
 * This hook will only be invoked if the hook
 * hook_uc_addresses_select_addresses() resulted in any addresses, so you have
 * always at least one address in the addresses array.
 *
 * @param array $addresses
 *   An array of UcAddressesAddress instances.
 * @param int $uid
 *   The user ID to select addresses for.
 * @param string $context
 *   The context in which the addresses are used:
 *   - checkout_form
 *   - order_form
 * @param string $type
 *   The type of address to select addresses for (shipping or billing).
 *
 * @return void
 */
function hook_uc_addresses_select_addresses_alter(&$addresses, $uid, $context, $type) {
  // Example 1: Don't let the user choose from addresses in Canada.
  foreach ($addresses as $index => $address) {
    if ($address->getField('country') == 124) {
      // The addresses' country is Canada (124). Remove from the addresses array.
      unset($addresses[$index]);
    }
  }

  // Example 2: Don't let the user choose the default billing address if it
  // should select an address for shipping.
  if ($type == 'shipping') {
    foreach ($addresses as $index => $address) {
      if ($address->isDefault('billing') && !$address->isDefault('shipping')) {
        // The address is the default billing address (and not the default
        // shipping address). Remove from the addresses array.
        unset($addresses[$index]);
      }
    }
  }

  // Example 3: At checkout, let the user select from his/her address book only
  // (thus only saved addresses are allowed and not addresses from other sources).
  if ($context == 'checkout_form') {
    foreach ($addresses as $index => $address) {
      if ($address->isNew()) {
        // The address is new which means it's not saved in the address book.
        // Remove from the addresses array.
        unset($addresses[$index]);
      }
    }
  }
}

/**
 * This hook allows you to alter an address format before it's being processed.
 *
 * This is useful if you want to display the address in a different way under
 * some kind of circumstances. For example, you may want to exclude the display
 * of first name and last name when you display an address in your own context.
 *
 * Warning: do not convert the address object to a string as that will result
 * into an infinite loop.
 *
 * @param string $format
 *   The unprocessed address format (tokens still need to be replaced).
 * @param UcAddressesAddress $address
 *   The address object for which a format is processed.
 * @param string $context
 *   The context in which the address will be displayed.
 *
 * @return void
 */
function hook_uc_addresses_format_address_alter(&$format, $address, $context) {
  // Example: remove the line that contains the last name completely when the
  // context is "order_view".
  if ($context == "order_view") {
    $lines = explode("\n", $format);
    foreach ($lines as $line_number => $line) {
      if (strpos($line, '[uc_addresses:last_name]') !== FALSE) {
        unset($lines[$line_number]);
      }
    }
    $format = implode("\n", $lines);
  }
}

/**
 * @} End of "addtogroup hooks".
 */
