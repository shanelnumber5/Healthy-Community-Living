<?php

/**
 * @file
 * Displays all addresses from a single address book.
 *
 * Available variables:
 * - $addresses: An array of all addresses of the user themed with
 *   theme_uc_addresses_list_address().
 * - $default_shipping_address: The default shipping address themed with
 *   theme_uc_addresses_list_address(). Only exists if the user has a default
 *   shipping address.
 * - $default_billing_address: The default billing address themed with
 *   theme_uc_addresses_list_address(). Only exists if the user has a default
 *   billing address.
 * - $other_addresses: An array of addresses of the user that are not a default
 *   address themed with theme_uc_addresses_list_address().
 * - $row_classes: An array of classes to apply to each row, indexed by address ID.
 *   This matches the index in $other_addresses.
 * - $add_address_link: Link for adding a new address, only exists if
 *   $options['add_link'] is TRUE.
 *
 * Other variables:
 * - $address_book: The address book object, instance of UcAddressesAddressBook.
 * - $options: An array of options for how the variables should be set:
 *   - $add_link: if the add address link may be printed.
 *
 * @see template_preprocess_uc_addresses_address_book()
 *
 * @ingroup themeable
 */
?>
<div class="address-book">
  <?php if (count($addresses) > 0): ?>
    <?php if ($default_billing_address || $default_shipping_address): ?>
      <!-- Default addresses -->
      <div class="default-addresses">
        <h2><?php print t('Default addresses'); ?></h2>
        <ol>
        <?php if ($default_billing_address): ?>
          <li class="address-item default-billing-address">
            <h3><?php print t('Default billing address'); ?></h3>
            <?php print $default_billing_address; ?>
          </li>
        <?php endif; ?>
        <?php if ($default_shipping_address): ?>
          <li class="address-item default-shipping-address">
            <h3><?php print t('Default shipping address'); ?></h3>
            <?php print $default_shipping_address; ?>
          </li>
        <?php endif; ?>
        </ol>
      </div>
      <!-- Other addresses -->
      <?php if (count($other_addresses) > 0): ?>
        <div class="additional-addresses">
          <h2><?php print t('Other addresses'); ?></h2>
          <ol>
          <?php foreach ($other_addresses as $aid => $address): ?>
            <li class="address-item <?php print implode(' ', $row_classes[$aid]); ?>">
              <?php print $address; ?>
            </li>
          <?php endforeach; ?>
          </ol>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php if (count($other_addresses) > 0): ?>
        <!-- All addresses -->
        <div class="addresses">
          <h2><?php print t('Addresses'); ?></h2>
          <ol>
          <?php foreach ($other_addresses as $aid => $address): ?>
            <li class="address-item <?php print implode(' ', $row_classes[$aid]); ?>">
              <?php print $address; ?>
            </li>
          <?php endforeach; ?>
          </ol>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  <?php else: ?>
    <?php print t('No addresses have been saved.'); ?>
  <?php endif; ?>

  <?php if ($add_address_link): ?>
    <div class="address-links">
      <?php print $add_address_link; ?>
    </div>
  <?php endif; ?>
</div>