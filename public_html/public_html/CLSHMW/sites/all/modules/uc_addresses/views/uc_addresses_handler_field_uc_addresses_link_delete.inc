<?php

/**
 * @file
 * Definition of views_handler_field_node_link_delete.
 */

/**
 * Field handler to present a link to delete an address.
 *
 * @ingroup views_field_handlers
 */
class uc_addresses_handler_field_uc_addresses_link_delete extends uc_addresses_handler_field_uc_addresses_link {
  /**
   * Renders the link if the user may delete the address.
   *
   * Note that because default addresses may not be deleted,
   * user 1 may see no delete links either for some addresses.
   */
  function render_link($entity, $values) {
    if (!uc_addresses_entity_access('delete', $entity)) {
      return;
    }

    $uri = entity_uri('uc_addresses', $entity);

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = $uri['path'] . '/delete';
    $this->options['alter']['query'] = drupal_get_destination();

    $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
    return $text;
  }
}
