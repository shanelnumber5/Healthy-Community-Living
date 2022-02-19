<?php

/**
 * @file
 * Definition of uc_addresses_handler_field_uc_addresses_link.
 */

/**
 * Field handler to present a link to the address.
 *
 * @ingroup views_field_handlers
 */
class uc_addresses_handler_field_uc_addresses_link extends views_handler_field_entity {
  /**
   * Implements views_handler_field#option_definition().
   */
  function option_definition() {
    $options = parent::option_definition();
    $options['text'] = array('default' => '', 'translatable' => TRUE);
    return $options;
  }

  /**
   * Implements views_handler_field#options_form().
   */
  function options_form(&$form, &$form_state) {
    $form['text'] = array(
      '#type' => 'textfield',
      '#title' => t('Text to display'),
      '#default_value' => $this->options['text'],
    );
    parent::options_form($form, $form_state);

    // The path is set by render_link function so don't allow to set it.
    $form['alter']['path'] = array('#access' => FALSE);
    $form['alter']['external'] = array('#access' => FALSE);
  }

  /**
   * Implements views_handler_field#render().
   */
  function render($values) {
    $entity = $this->get_value($values);
    if ($entity) {
      return $this->render_link($entity, $values);
    }
  }

  /**
   * Renders the link if the user may view the address.
   *
   * @param UcAddressesAddress $entity
   *   An instance of UcAddressesAddress.
   * @param array $values
   *   The values retrieved from the database.
   *
   * @return string
   *   The title of the link to display.
   */
  function render_link($entity, $values) {
    if (uc_addresses_entity_access('view', $entity)) {
      $uri = entity_uri('uc_addresses', $entity);
      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = $uri['path'];
      $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
      return $text;
    }
  }
}
