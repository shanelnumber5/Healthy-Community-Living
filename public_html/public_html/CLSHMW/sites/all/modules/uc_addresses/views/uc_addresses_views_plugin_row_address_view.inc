<?php

/**
 * @file
 * Contains the address view row style plugin.
 */

/**
 * Plugin which performs a address_view on the resulting object.
 *
 * Most of the code on this object is in the theme function.
 */
class uc_addresses_views_plugin_row_address_view extends views_plugin_row {
  // Basic properties that let the row style follow relationships.
  public $base_table = 'uc_addresses';
  public $base_field = 'aid';

  /**
   * The assumed name of the uid field in the Views' results.
   *
   * @var string
   * @access protected
   */
  protected $uid_field_alias = 'unknown';

  /**
   * Overrides views_plugin_row::option_definition().
   *
   * @return array
   *   The available options and their default values.
   */
  public function option_definition() {
    $options = parent::option_definition();

    $options['view_link'] = array('default' => FALSE);
    $options['edit_link'] = array('default' => TRUE);
    $options['delete_link'] = array('default' => TRUE);
    $options['default_flags'] = array('default' => FALSE);

    return $options;
  }

  /**
   * Overrides views_plugin_row::options_form().
   *
   * @return void
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['view_link'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display view links'),
      '#default_value' => $this->options['view_link'],
    );
    $form['edit_link'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display edit links'),
      '#description' => t('Note that edit links are only shown for addresses that the current user may edit.'),
      '#default_value' => $this->options['edit_link'],
    );
    $form['delete_link'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display delete links'),
      '#description' => t('Note that delete links are only shown for addresses that the current user may delete.'),
      '#default_value' => $this->options['delete_link'],
    );
    $form['default_flags'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display default flags'),
      '#description' => t('This adds the "default" label to default addresses. For example, the default billing address will get the label "!label".', array('!label' => t('Default billing address'))),
      '#default_value' => $this->options['default_flags'],
    );
  }

  /**
   * Overrides views_plugin_row::query().
   *
   * Ensures the 'uid' field is added to the query.
   *
   * @return void
   */
  public function query() {
    parent::query();
    $this->uid_field_alias = $this->view->query->add_field($this->view->query->base_table, 'uid');
  }

  /**
   * Implements views_plugin_row::pre_render().
   *
   * Loads all addresses.
   *
   * @return void
   */
  public function pre_render($values) {
    $aid_field_alias = $this->field_alias;
    $uid_field_alias = $this->uid_field_alias;

    $this->addresses = array();
    if (!empty($values)) {
      foreach ($values as $row) {
        $addressBook = UcAddressesAddressBook::get($row->$uid_field_alias);
        // Set performance hint to load all addresses.
        $addressBook->setPerformanceHint(UcAddressesAddressBook::PERF_HINT_LOAD_ALL);
        $address = $addressBook->getAddressById($row->$aid_field_alias);
        $this->addresses[$address->getId()] = $address;
      }
    }
  }

  /**
   * Overrides views_plugin_row::render().
   *
   * Renders a single address.
   *
   * @return string
   *   Rendered HTML.
   */
  function render($row) {
    if (isset($this->addresses[$row->{$this->field_alias}])) {
      $address = $this->addresses[$row->{$this->field_alias}];
      $address->view = $this->view;
      return theme('uc_addresses_list_address', array('address' => $address, 'options' => $this->options));
    }
  }
}
