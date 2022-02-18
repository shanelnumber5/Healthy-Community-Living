<?php

/**
 * @file
 * Page callbacks for viewing, adding, editing, and deleting addresses.
 */

// -----------------------------------------------------------------------------
// VIEW ADDRESSES
// -----------------------------------------------------------------------------

/**
 * Generates a list of all addresses owned by a single user.
 *
 * This address list is themed for display.
 *
 * If the current user is allowed to edit the addresses, then an edit link for
 * each address will be provided.
 *
 * @param object $address_user
 *   The user whose address book we want to display.
 * @return string
 *   The themed address book.
 */
function uc_addresses_address_book($address_user) {
  drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
  drupal_set_title(t('Address book'));

  // Initialize addresses array used for in the template.
  $addresses = array();
  try {
    // Get all addresses for address user.
    $address_book = UcAddressesAddressBook::get($address_user);
    $all_addresses = $address_book->getAddresses();

    if (count($all_addresses) > 0) {
      // Check for each address if the user may view it.
      foreach ($all_addresses as $address) {
        $address_output = uc_addresses_list_one_address($address_user, $address);
        if ($address_output) {
          $addresses[$address->getId()] = $address_output;
        }
      }
    }
  }
  catch (Exception $e) {
    drupal_set_message($e->getMessage(), 'error');
  }

  $options = array();
  // Check if user may add addresses.
  if (UcAddressesPermissions::canEditAddress($address_user)) {
    // Show add link.
    $options['add_link'] = TRUE;
  }

  return theme('uc_addresses_address_book', array('addresses' => $addresses, 'address_book' => $address_book, 'options' => $options));
}

/**
 * Prepares variables for address book.
 *
 * @param array $vars
 *   An array with the following:
 *   - addresses (array of UcAddressesAddress instances)
 *   - address_book' (instance of UcAddressesAddressBook)
 *   - options (array):
 *     - add_link (boolean): whether or not display an add address link.
 *
 * @return void
 */
function template_preprocess_uc_addresses_address_book(&$vars) {
  // Initialize variables.
  $vars['options'] += array(
    'add_link' => FALSE,
  );
  $vars['add_address_link'] = NULL;
  $vars['other_addresses'] = array();
  $vars['default_billing_address'] = NULL;
  $vars['default_shipping_address'] = NULL;

  // Find out which addresses are default addresses and which are not.
  $address_book = $vars['address_book'];
  foreach ($vars['addresses'] as $aid => $themed_address) {
    $address_object = $address_book->getAddressById($aid);
    if ($address_object) {
      $vars['other_addresses'][$aid] = $themed_address;
      if ($address_object->isDefault('billing') && variable_get('uc_addresses_use_default_billing', TRUE)) {
        $vars['default_billing_address'] = $themed_address;
        unset($vars['other_addresses'][$aid]);
      }
      if ($address_object->isDefault('shipping') && variable_get('uc_addresses_use_default_shipping', TRUE)) {
        $vars['default_shipping_address'] = $themed_address;
        unset($vars['other_addresses'][$aid]);
      }
    }
  }

  // Setup odd/even for the other addresses.
  $count = 0;
  foreach ($vars['other_addresses'] as $num => $row) {
    $vars['row_classes'][$num][] = ($count++ % 2 == 0) ? 'odd' : 'even';
  }

  // Setup admin links.
  if ($vars['options']['add_link']) {
    $vars['add_address_link'] = l(t('Add a new address'), 'user/' . $address_book->getUserId() . '/addresses/add', array('attributes' => array('class' => 'address-link add-address-link')));
  }
}

/**
 * Displays a single address.
 *
 * If the current user can edit the address, then provide an edit link.
 *
 * @param object $address_user
 *   The user who "owns" the address.
 * @param UcAddressesAddress $address
 *   The address to display.
 * @param array $options
 *   Options for display, see template_preprocess_uc_addresses_list_address().
 *
 * @return string
 *   The themed address.
 */
function uc_addresses_list_one_address($address_user, UcAddressesAddress $address, $options = array()) {
  if (UcAddressesPermissions::canViewAddress($address_user, $address)) {
    $options['edit_link'] = TRUE;
    $options['delete_link'] = TRUE;
    return theme('uc_addresses_list_address', array('address' => $address, 'options' => $options));
  }
}

/**
 * Prepares variables for one address.
 *
 * @param array $vars
 *   An array with the following:
 *   - address (instance of UcAddressesAddress)
 *   - options (array):
 *     - view_link (boolean): whether or not display a link to the address.
 *     - edit_link (boolean): whether or not display a link to edit the address.
 *     - delete_link (boolean): whether or not display a link to delete the
 *       address.
 *     - destination (string): path to return to after editing the address.
 *     - default_flags (boolean): whether or not display "default address"
 *       labels.
 *     - context (string): the context to use for display the addresses. This
 *       has effect on which fields are displayed. Defaults to "address_view".
 *
 * @return void
 *
 * @ingroup themeable
 */
function template_preprocess_uc_addresses_list_address(&$vars) {
  drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
  $address = $vars['address'];

  // Initialize variables.
  $vars['options'] += array(
    'view_link' => FALSE,
    'edit_link' => FALSE,
    'delete_link' => FALSE,
    'destination' => '',
    'default_flags' => FALSE,
    'context' => 'address_view',
  );
  $vars['label'] = NULL;
  $vars['admin_links'] = NULL;

  // Retrieve ID's.
  $vars['aid'] = $address->getId();
  $vars['uid'] = $address->getUserId();

  // Preprocess address fields, context is 'address_view' by default.
  $vars['fields'] = uc_addresses_preprocess_address($address, $vars['options']['context']);

  // Setup admin links.
  $link_options = array();
  $links = array();
  if ($vars['options']['destination']) {
    // Add destinations for all admin links.
    $link_options['query'] = array('destination' => $vars['options']['destination']);
  }
  if ($vars['options']['view_link']) {
    // Create view link.
    $vars['view_address_link'] = uc_addresses_render_link($address, 'view', $link_options);
    if ($vars['view_address_link']) {
      $links['view'] = $vars['view_address_link'];
    }
  }
  if ($vars['options']['edit_link']) {
    // Create edit link.
    $vars['edit_address_link'] = uc_addresses_render_link($address, 'edit', $link_options);
    if ($vars['edit_address_link']) {
      $links['edit'] = $vars['edit_address_link'];
    }
  }
  if ($vars['options']['delete_link']) {
    // Create delete link.
    $vars['delete_address_link'] = uc_addresses_render_link($address, 'delete', $link_options);
    if ($vars['delete_address_link']) {
      $links['delete'] = $vars['delete_address_link'];
    }
  }
  if (count($links) > 0) {
    // Combine all links into a single variable.
    $vars['admin_links'] = implode(' <span class="separator uc-addresses-separator">|</span> ', $links);
  }

  // Setup labels.
  if ($vars['options']['default_flags']) {
    $flags = array();
    if ($address->isDefault('billing') && variable_get('uc_addresses_use_default_billing', TRUE)) {
      $flags['billing'] = '<span class="uc-addresses-default-address-label uc-addresses-default-billing-address-label">' . t('Default billing address') . '</span>';
    }
    if ($address->isDefault('shipping') && variable_get('uc_addresses_use_default_shipping', TRUE)) {
      $flags['shipping'] = '<span class="uc-addresses-default-address-label uc-addresses-default-shipping-address-label">' . t('Default shipping address') . '</span>';
    }
    if (count($flags) > 0) {
      $vars['label'] = implode(' <span class="separator uc-addresses-separator"><br /></span> ', $flags);
    }
  }

  // CSS classes.
  $vars['classes_array'] = array();
  if ($address->isDefault('billing')) {
    $vars['classes_array']['default'] = 'uc-addresses-default-address';
    $vars['classes_array']['default_billing'] = 'uc-addresses-default-billing-address';
  }
  if ($address->isDefault('shipping')) {
    $vars['classes_array']['default'] = 'uc-addresses-default-address';
    $vars['classes_array']['default_shipping'] = 'uc-addresses-default-shipping-address';
  }
  $vars['classes'] = implode(' ', $vars['classes_array']);
}

/**
 * Renders a link to an address if the user has access to the given operation of
 * the address.
 *
 * @param UcAddressesAddress $address
 *   An instance of UcAddressesAddress.
 * @param string $op
 *   The type of link to render: 'view', 'edit' or 'delete'.
 * @param array $options
 *   (optional) A list of options to pass to l().
 *
 * @return string
 *   The rendered link or an empty string if the user has no access.
 *
 * @todo Maybe move this to module file?
 * @todo Good for views address links too?
 */
function uc_addresses_render_link(UcAddressesAddress $address, $op, $options = array()) {
  $address_user = user_load($address->getUserId());
  $uri = $address->uri();
  switch ($op) {
    case 'view':
      if (!UcAddressesPermissions::canViewAddress($address_user, $address)) {
        return '';
      }
      $title = t('View address');
      $path = $uri['path'];
      break;

    case 'edit':
      if (!UcAddressesPermissions::canEditAddress($address_user, $address)) {
        return '';
      }
      $title = t('Edit address');
      $path = $uri['path'] . '/edit';
      break;

    case 'delete':
      if (!UcAddressesPermissions::canDeleteAddress($address_user, $address)) {
        return '';
      }
      $title = t('Delete address');
      $path = $uri['path'] . '/delete';
      break;
  }

  // Add CSS classes to the link.
  $classes = array(
    'address-link',
    $op . '-address-link',
  );
  if (!isset($options['attributes']['class'])) {
    $options['attributes']['class'] = array();
  }
  $options['attributes']['class'] = array_merge($options['attributes']['class'], $classes);

  return l($title, $path, $options);
}

// -----------------------------------------------------------------------------
// ADD/EDIT ADDRESSES
// -----------------------------------------------------------------------------

/**
 * Creates a form used to add a new address or edit an existing address.
 *
 * @param array $form
 *   The definition of the form.
 * @param array $form_state
 *   The form state.
 * @param object $address_user
 *   The user who "owns" this address.
 * @param UcAddressesAddress $address
 *   The address to edit (NULL for new addresses).
 *
 * @return array
 *   An address form.
 *
 * @ingroup forms
 */
function uc_addresses_get_address_form($form, &$form_state, $address_user, $address = NULL) {
  if (!($address instanceof UcAddressesAddress)) {
    // Try to retrieve from form_state first.
    if (isset($form_state['input']['address']['aid'])) {
      $address = UcAddressesAddressBook::get($address_user)->getAddressById($form_state['input']['address']['aid']);
    }
  }
  // If we have still no valid address instance, create a new instance.
  if (!($address instanceof UcAddressesAddress)) {
    $address = UcAddressesAddressBook::get($address_user)->addAddress();
  }

  $form['uc_addresses'] = array(
    '#type' => 'fieldset',
    '#title' => ($address->isNew()) ? t('Add an address') : t('Edit address information'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['uc_addresses']['address'] = array(
    '#type' => 'uc_addresses_address',
    '#uc_addresses_address' => $address,
    '#uc_addresses_context' => 'address_form',
  );

  // Add submit button.
  if ($address->isNew()) {
    // New address.
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => variable_get('uc_addresses_add_button', t('Save address')),
    );
  }
  else {
    // Existing address.
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => variable_get('uc_addresses_update_button', t('Save address')),
    );
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => variable_get('uc_addresses_delete_button', t('Delete address')),
      '#limit_validation_errors' => array(),
      '#submit' => array('uc_addresses_get_address_form_submit'),
    );

    // Check if address may be deleted.
    // (It's not allowed to delete default addresses).
    if (!UcAddressesPermissions::canDeleteAddress($address_user, $address)) {
      $form['delete']['#access'] = FALSE;
    }
  }

  // Add cancel link.
  $form['cancel'] = array(
    '#markup' => l(t('Cancel'), 'user/' . $address_user->uid . '/addresses/', array('attributes' => array('class' => 'address-link cancel-address-link'))),
  );

  return $form;
}

/**
 * Validate handler for the address form.
 *
 * Checks if the address is similar to other addresses.
 *
 * @return void
 * @see uc_addresses_get_address_form()
 * @see uc_addresses_get_address_form_submit()
 */
function uc_addresses_get_address_form_validate($form, $form_state) {
  try {
    $address = $form['uc_addresses']['address']['#uc_addresses_address'];
    $address_book = UcAddressesAddressBook::get($address->getUserId());
    // Check if the address looks like other addresses.
    if ($address_book->compareAddress($address)) {
      if ($address->isNew()) {
        form_set_error('uc_addresses][address', t('This address already appears in your address book. A new address was not added.'));
      }
      else {
        form_set_error('uc_addresses][address', t('The revised address is already in your address book. Your change was not made.'));
      }
    }
  }
  catch (Exception $e) {
    drupal_set_message($e->getMessage(), 'error');
  }
}

/**
 * Submit handler for the address form.
 *
 * @return void
 * @see uc_addresses_get_address_form()
 * @see uc_addresses_get_address_form_validate()
 */
function uc_addresses_get_address_form_submit(&$form, &$form_state) {
  try {
    $address = $form['uc_addresses']['address']['#uc_addresses_address'];

    if ($form_state['clicked_button']['#value'] == variable_get('uc_addresses_delete_button', t('Delete address'))) {
      $form_state['redirect'] = 'user/' . $address->getUserId() . '/addresses/' . $address->getId() . '/delete';
    }
    elseif ($form_state['clicked_button']['#value']) {
      $address->save();
      drupal_set_message(t('The address is saved.'));
      $form_state['redirect'] = 'user/' . $address->getUserId() . '/addresses';
    }
  }
  catch (Exception $e) {
    drupal_set_message($e->getMessage(), 'error');
  }
}

/**
 * Themes the add or edit address form.
 *
 * @param array $variables
 *   An array containing:
 *   - form (array): The form array to theme.
 *
 * @return string
 *   The themed form.
 *
 * @ingroup themeable
 */
function theme_uc_addresses_get_address_form($variables) {
  $form = $variables['form'];
  drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
  return drupal_render_children($form);
}

/**
 * Prepares variables for an address form.
 *
 * @param array $vars
 *   An associative array containing:
 *   - form: A renderable address form.
 *
 * @return void
 *
 * @ingroup themeable
 */
function template_preprocess_uc_addresses_form(&$vars) {
  drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');
  $vars['req'] = '<span class="form-required">*</span>';
}

// -----------------------------------------------------------------------------
// DELETE ADDRESSES
// -----------------------------------------------------------------------------

/**
 * Displays a confirmation page before deleting an address.
 *
 * @param object $address_user
 *   The user who "owns" the address.
 * @param UcAddressesAddress $address
 *   The address to delete.
 *
 * @return string
 *   A themed HTML page.
 */
function uc_addresses_delete_address_confirm($address_user, UcAddressesAddress $address) {
  $uid = $address_user->uid;
  $aid = $address->getId();

  $form = drupal_get_form('uc_addresses_delete_address_confirm_form', $address_user, $address);

  $help =
    variable_get('uc_addresses_delete_instructions',
      t('Are you sure you want to delete this address? Once deleted, the address cannot be recovered.',
        array(
          '!delete' => variable_get('uc_addresses_delete_button', t('Delete address')),
        )
      )
  );

  $output = theme('uc_addresses_address_delete_confirm', array('help' => $help, 'form' => $form));

  return $output;
}

/**
 * Themes the address deletion confirmation form.
 *
 * @param string $help
 *   The help message to display.
 * @param string $form
 *   The HTML version of the form that by default
 *   includes the 'Back' and 'Delete Address' buttons at the bottom
 *   of the confirmation page.
 *
 * @return string
 *   The themed confirmation form.
 *
 * @ingroup themeable
 * @todo Move to template?
 */
function theme_uc_addresses_address_delete_confirm($variables) {
  $help = $variables['help'];
  $form = $variables['form'];
  drupal_add_css(drupal_get_path('module', 'uc_addresses') . '/uc_addresses.css');

  $output = '<p>' . $help . '</p>';
  $output .= drupal_render($form);
  return $output;
}

/**
 * Gets the submit buttons to confirm deletion of a user's address.
 *
 * @param array $form
 *   The definition of the form.
 * @param array $form_state
 *   The state of the form.
 * @param object $user
 *   The user who "owns" the address.
 * @param UcAddressesAddress $address
 *   The address we are deleting.
 *
 * @return string
 *   The buttons for the form (as a string).
 *
 * @ingroup forms
 */
function uc_addresses_delete_address_confirm_form($form, &$form_state, $address_user, UcAddressesAddress $address) {
  $form['stored_values'] = array(
    '#type' => 'value',
    '#value' => array(
      'user' => $address_user,
      'address' => $address,
    ),
  );

  return confirm_form(
    $form,
    t('Delete address'),
      'user/' . $address_user->uid . '/addresses/',
    (string) $address,
    variable_get('uc_addresses_delete_button', t('Delete address')),
    t('Cancel')
  );
}

/**
 * Deletes a user-confirmed address.
 *
 * @param array $form
 *   The form.
 * @param array $form_state
 *   The form state.
 *
 * @return void
 *
 * @see uc_addresses_delete_address_confirm_form()
 */
function uc_addresses_delete_address_confirm_form_submit($form, &$form_state) {
  $address_user = $form['stored_values']['#value']['user'];
  $address = $form['stored_values']['#value']['address'];

  try {
    if ($address->delete()) {
      drupal_set_message(t('The address has been deleted.'));
    }
    else {
      drupal_set_message(t('The address could not be deleted.'), 'error');
    }
  }
  catch (Exception $e) {
    drupal_set_message($e->getMessage(), 'error');
  }

  $form_state['redirect'] = array('user/' . $address_user->uid . '/addresses');
}
