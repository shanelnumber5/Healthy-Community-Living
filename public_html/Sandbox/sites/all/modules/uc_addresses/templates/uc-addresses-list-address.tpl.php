<?php

/**
 * @file
 * Displays a single address.
 *
 * Available variables:
 * - $label: A string that can indicate if the address is a default address.
 * - $fields: An array of field values to display. Each $field in $fields can
 *   contain:
 *   - $title: The field's label.
 *   - $data: The field's value.
 *   - #weight: This value has been used by uc_addresses_preprocess_address() to
 *     order the fields.
 * - $aid: The ID of the address.
 * - $uid: User ID of the owner of the address.
 * - $classes: CSS classes to theme the address, one or more of the following
 *   classes:
 *   - addresses-default-address: if the address is a default address (default
 *     shipping or default billing.
 *   - addresses-default-shipping-address: if the address is a default shipping
 *     address.
 *   - addresses-default-billing-address: if the address is a default billing
 *     address.
 * - $admin_links: Links for editing and deleting the address.
 * - $edit_address_link: Link for editing the address, only exists if
 *   $options['edit_link'] is TRUE.
 * - $delete_address_link: Link for deleting the address, only exists if
 *   $options['delete_link'] is TRUE.
 *
 * Other variables:
 * - $address: The address object, instance of UcAddressesAddress.
 * - $options: An array of options for how the variables should be set:
 *   - $view_link: if the view link may be printed.
 *   - $edit_link: if the edit link may be printed.
 *   - $delete_link: if the delete link may be printed.
 *   - $destination: if set, the edit and delete links will be outputted with
 *     ?destination=...
 *   - $default_flags: if the "default address" label may be displayed.
 *   - $context: the context in which the address is displayed.
 * - $classes_array: Same as $classes, but then listed in an array instead of a
 *   string.
 *
 * @see template_preprocess_uc_addresses_list_address()
 * @see uc_addresses_preprocess_address()
 *
 * @ingroup themeable
 */
?>
<div class="list-address-wrapper">
  <?php if ($label): ?>
    <h3><?php print $label; ?></h3>
  <?php endif; ?>
  <table class="list-address <?php print $classes; ?>">
    <tbody>
      <?php if (is_array($fields) && count($fields) > 0): ?>
        <?php foreach ($fields as $field_name => $field): ?>
          <tr class="data-row address-field-<?php print $field_name; ?>">
            <td class="title-col">
              <?php if ($field['title'] != ''):
                print $field['title'] . ':';
              endif; ?>
            </td>
            <td class="data-col"><?php print $field['data']; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if ($admin_links): ?>
        <tr class="address-links">
          <td colspan="2">
            <?php print $admin_links; ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>