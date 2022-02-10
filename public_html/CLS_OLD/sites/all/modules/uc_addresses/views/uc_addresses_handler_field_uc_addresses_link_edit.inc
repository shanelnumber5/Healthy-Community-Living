<?php

/**
 * @file
 * Definition of uc_addresses_handler_field_uc_addresses_link_edit.
 */

/**
 * Field handler to present a link edit an address.
 *
 * @ingroup views_field_handlers
 */
class uc_addresses_handler_field_uc_addresses_link_edit extends uc_addresses_handler_field_uc_addresses_link {
  /**
   * Renders the link if the user may edit the address.
   */
  function render_link($entity, $values) {
    if (!uc_addresses_entity_access('update', $entity)) {
      return;
    }

    $uri = entity_uri('uc_addresses', $entity);

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = $uri['path'] . '/edit';
    $this->options['alter']['query'] = drupal_get_destination();

    $text = !empty($this->options['text']) ? $this->options['text'] : t('edit');
    return $text;
  }
}
