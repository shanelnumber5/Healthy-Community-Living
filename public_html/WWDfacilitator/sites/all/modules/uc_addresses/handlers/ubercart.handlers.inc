<?php

/**
 * @file
 * Field handlers for Ubercart core address fields:
 * first_name, last_name, company, etc.
 */

/**
 * Base class for Ubercart core address fields
 */
abstract class UcAddressesUcFieldHandler extends UcAddressesFieldHandler {
  /**
   * Overrides UcAddressesFieldHandler::getFieldTitle().
   *
   * Returns the title to use when displaying the field.
   */
  public function getFieldTitle() {
    return uc_get_field_name($this->getFieldName());
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldEnabled().
   */
  public function isFieldEnabled() {
    return uc_address_field_enabled($this->getFieldName());
  }

  /**
   * Implements UcAddressesFieldHandler::isFieldRequired().
   */
  public function isFieldRequired() {
    if ($this->getContext() == 'order_form') {
      return FALSE;
    }
    return uc_address_field_required($this->getFieldName());
  }
}

/**
 * Class for an Ubercart core address text field.
 */
class UcAddressesUcTextFieldHandler extends UcAddressesUcFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $fieldName = $this->getFieldName();
    $fieldValue = $this->getAddress()->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    return array(
      $fieldName => array(
        '#type' => 'textfield',
        '#title' => $this->getFieldTitle(),
        '#required' => $this->isFieldRequired(),
        '#default_value' => $default,
        '#size' => 32,
      ),
    );
  }
}

/**
 * Class for the Ubercart zone field.
 */
class UcAddressesUcZoneFieldHandler extends UcAddressesUcFieldHandler {
  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $address = $this->getAddress();
    $fieldName = $this->getFieldName();
    $fieldValue = $address->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;
    $country_id = (isset($form_values['country'])) ? $form_values['country'] : $this->getAddress()->getField('country');

    if (!empty($country_id)) {
      // Check if country exists.
      $result = db_select('uc_countries')
        ->fields('uc_countries', array('country_id'))
        ->condition('country_id', $country_id)
        ->execute()
        ->fetchField();
      if (!$result) {
        $country_id = uc_store_default_country();
      }
    }

    if (empty($country_id)) {
      $country_id = uc_store_default_country();
    }

    $result = db_select('uc_zones')
      ->condition('zone_country_id', $country_id)
      ->fields('uc_zones')
      ->orderBy('zone_name')
      ->execute();

    $options = array();
    foreach ($result as $zone) {
      $options[$zone->zone_id] = $zone->zone_name;
    }

    if (empty($form['#key_prefix'])) {
      if ($address instanceof UcAddressesAddress) {
        // When no key prefix is set, use the address ID as part of the zone wrapper ID.
        $zone_wrapper_id = 'uc-address' . $address->getId() . '-zone-wrapper';
      }
      else {
        // When no instance of UcAddressesAddress is given, we have no unique
        // value to create a wrapper for.
        $zone_wrapper_id = 'uc-address-zone-wrapper';
      }
    }
    else {
      // When a key prefix is set, make this part of the zone wrapper ID.
      $zone_wrapper_id = 'uc-store-address-' . str_replace('_', '-', $form['#key_prefix']) . '-zone-wrapper';
    }

    if (count($options) == 0) {
      return array(
        $fieldName => array(
          '#title' => $this->getFieldTitle(),
          '#type' => 'hidden',
          '#value' => 0,
          '#required' => FALSE,
          '#prefix' => '<div id="' . $zone_wrapper_id . '">',
          '#suffix' => '</div>',
        )
      );
    }

    $return = array(
      $fieldName => array(
        '#type' => 'select',
        '#title' => $this->getFieldTitle(),
        '#required' => $this->isFieldRequired(),
        '#options' => $options,
        '#default_value' => $default,
        '#prefix' => '<div id="' . $zone_wrapper_id . '">',
        '#suffix' => '</div>',
        '#empty_value' => 0,
      ),
    );

    // If the country value was updated, the selected zone does not exists.
    // In that case, just empty the value.
    if (!empty($form_values['zone']) && !isset($options[$form_values['zone']])) {
      $return[$fieldName]['#value'] = 0;
    }
    return $return;
  }

  /**
   * Overrides UcAddressesFieldHandler::getDefaultValue().
   *
   * The zone ID should always be an integer.
   */
  public function getDefaultValue() {
    return 0;
  }

  /**
   * Overrides UcAddressesFieldHandler::getMappingTargets().
   *
   * The zone field has some extra mapping targets.
   */
  public function getMappingTargets() {
    $targets = parent::getMappingTargets();

    // Specify clearer names and descriptions.
    $targets['zone:zone_name']['name'] = t('Zone name');
    $targets['zone:zone_code']['name'] = t('Zone code');
    $targets['zone']['description'] = t('Zone ID as known to Ubercart');

    return $targets;
  }

  /**
   * Overrides UcAddressesFieldHandler::mapValue().
   *
   * The zone field has some extra mapping targets.
   */
  public function mapValue($value, $format = '') {
    switch ($format) {
      case 'zone_code':
      case 'zone_name':
        // Lookup zone data.
        $zone_id = db_select('uc_zones', 'uc_zones')
          ->condition($format, $value)
          ->fields('uc_zones', array('zone_id'))
          ->execute()
          ->fetchField();
        if ($zone_id) {
          $value = $zone_id;
        }
        break;
    }
    parent::mapValue($value, $format);
  }

  /**
   * Implements UcAddressesFieldHandler::getOutputFormats().
   */
  public function getOutputFormats() {
    return array(
      'zone_code' => t('Abbreviation of the zone'),
      'zone_name' => t('Full name of the zone'),
    );
  }

  /**
   * Overrides UcAddressesFieldHandler::outputValue().
   *
   * The zone field can be outputted in different formats.
   */
  public function outputValue($value = '', $format = '') {
    if ($value === '') {
      $value = $this->getAddress()->getField($this->getFieldName());
    }

    // Get zone data.
    $result = db_select('uc_zones')
      ->condition('zone_id', $value)
      ->fields('uc_zones')
      ->execute();

    $row = $result->fetch();
    if ($row) {
      $zone_data = array(
        'zone_code' => $row->zone_code,
        'zone_name' => $row->zone_name,
      );
    }
    else {
      $zone_data = array(
        'zone_code' => t('N/A'),
        'zone_name' => t('Unknown'),
      );
    }

    if (isset($zone_data[$format])) {
      return $zone_data[$format];
    }

    // If no format is specified, return zone name.
    return $zone_data['zone_name'];
  }
}

/**
 * Class for the Ubercart country field.
 */
class UcAddressesUcCountryFieldHandler extends UcAddressesUcFieldHandler {
  /**
   * An array of all loaded countries.
   *
   * @var array $countries
   * @access private
   * @static
   */
  private static $countries;

  /**
   * Implements UcAddressesFieldHandler::getFormField().
   */
  public function getFormField($form, $form_values) {
    $address = $this->getAddress();
    $fieldName = $this->getFieldName();
    $fieldValue = $address->getField($fieldName);
    $default = (isset($form_values[$fieldName])) ? $form_values[$fieldName] : $fieldValue;

    $result = db_select('uc_countries')
      ->condition('version', 0, '>')
      ->fields('uc_countries')
      ->orderBy('country_name', 'ASC')
      ->execute();

    $countries = array();
    foreach ($result as $country) {
      // Create option.
      $countries[$country->country_id] = t($country->country_name);
      // Save for later use.
      self::$countries[$country->country_id] = $country;
    }
    if (count($countries) == 0) {
      $countries[] = t('No countries found.');
    }
    else {
      // Sort countries list in natural order.
      natcasesort($countries);
    }

    if (empty($form['#key_prefix'])) {
      if ($address instanceof UcAddressesAddress) {
        // When no key prefix is set, use the address ID as part of the zone wrapper ID.
        $zone_wrapper_id = 'uc-address' . $address->getId() . '-zone-wrapper';
      }
      else {
        // When no instance of UcAddressesAddress is given, we have no unique
        // value to create a wrapper for.
        $zone_wrapper_id = 'uc-address-zone-wrapper';
      }
    }
    else {
      // When a key prefix is set, make this part of the zone wrapper ID.
      $zone_wrapper_id = 'uc-store-address-' . str_replace('_', '-', $form['#key_prefix']) . '-zone-wrapper';
    }

    return array(
      $fieldName => array(
        '#type' => 'select',
        '#title' => $this->getFieldTitle(),
        '#options' => $countries,
        '#required' => $this->isFieldRequired(),
        '#default_value' => empty($default) ? uc_store_default_country() : $default,
        '#ajax' => array(
          'callback' => 'uc_store_update_address_field_zones',
          'wrapper' => $zone_wrapper_id,
          'progress' => array(
            'type' => 'throbber',
          ),
        ),
        '#element_validate' => array('uc_addresses_validate_address_field_country'),
        '#key_prefix' => empty($form['#key_prefix']) ? '' : $form['#key_prefix'],
      ),
    );
  }

  /**
   * Overrides UcAddressesFieldHandler::getDefaultValue().
   */
  public function getDefaultValue() {
    return uc_store_default_country();
  }

  /**
   * Overrides UcAddressesFieldHandler::getMappingTargets().
   *
   * The country field has some extra mapping targets.
   */
  public function getMappingTargets() {
    $targets = parent::getMappingTargets();
    // Formats ending on "if" have no use for Feeds.
    unset($targets['country:country_name_if']);
    unset($targets['country:country_code2_if']);
    unset($targets['country:country_code3_if']);

    // Specify clearer names and descriptions.
    $targets['country:country_name']['name'] = t('Country name (@language)', array('@language' => 'English'));
    $targets['country:country_code2']['name'] = t('Country code 2');
    $targets['country:country_code3']['name'] = t('Country code 3');
    $targets['country']['description'] = t('Country ID as known to Ubercart');
    $targets['country:country_name']['description'] = t('The name of the country in English.');

    // Add support for countries in native language.
    $targets['country:country_name:current'] = array(
      'name' => t('Country name (current language)'),
      'description' => t('Name of the country in the current language. Country names are expected to be in the language that is active when importing addresses. For example, if are you viewing the site in Dutch when importing addresses, country names should be in Dutch. If you then switch the site to French, country names are expected to be in French.'),
    );
    $languages = language_list();
    foreach ($languages as $key => $language) {
      if ($key == 'en') {
        // English is already handled.
        continue;
      }
      $targets['country:country_name:' . $key] = array(
        'name' => t('Country name (@language)', array('@language' => $language->name)),
        'description' => t('The name of the country in @language-native', array('@language-native' => $language->native)),
      );
    }

    return $targets;
  }

  /**
   * Overrides UcAddressesFieldHandler::mapValue().
   *
   * The country field has some extra mapping targets.
   */
  public function mapValue($value, $format = '') {
    if (strpos($format, 'country_name:') === 0) {
      // Country is specified in native or current language.
      $explode = explode(':', $format);
      $langcode = $explode[1];
      if ($langcode == 'current') {
        // Country is expected to be specified in the current language.
        global $language;
        $langcode = $language->language;
      }
      // Look through complete country list and find one that matches.
      $countries = $this->getAllCountries();
      foreach ($countries as $country) {
        $translated_country = t($country->country_name, array(), array('langcode' => $langcode));
        if ($value == $translated_country) {
          // Country in native language found!
          return parent::mapValue($country->country_id, $format);
        }
      }
      // No country found. Report error.
      $message = t('Country %name not found.', array('%name' => $value));
      if (user_access('administer store')) {
        $message .= ' ' . t('Check if the country is spelled correctly and if it is installed in the store.');
      }
      else {
        $message .= ' ' . t('Check if the country is spelled correctly or contact the site administrator.');
      }
      throw new UcAddressesException($message);
    }

    switch ($format) {
      case 'country_code2':
      case 'country_code2_if':
        $format = 'country_iso_code_2';
        break;

      case 'country_code3':
      case 'country_code3_if':
        $format = 'country_iso_code_3';
        break;
    }

    switch ($format) {
      case 'country_name':
      case 'country_iso_code_2':
      case 'country_iso_code_3':
        // Lookup country data.
        $country_id = db_select('uc_countries', 'uc_countries')
          ->condition($format, $value)
          ->fields('uc_countries', array('country_id'))
          ->execute()
          ->fetchField();
        if ($country_id) {
          $value = $country_id;
        }
        else {
          $message = t('Country @name not found.', array('@name' => $value));
          if (user_access('administer store')) {
            $message .= ' ' . t('Check if the country is spelled correctly and if it is enabled in the store.');
          }
          else {
            $message .= ' ' . t('Check if the country is spelled correctly or contact the site administrator.');
          }
          throw new UcAddressesException($message);
        }
        break;
    }
    parent::mapValue($value, $format);
  }

  /**
   * Implements UcAddressesFieldHandler::getOutputFormats().
   */
  public function getOutputFormats() {
    return array(
      'country_name' => t('Name of the country'),
      'country_code2' => t('2 digit country abbreviation'),
      'country_code3' => t('3 digit country abbreviation'),
      'country_name_if' => $this->getCountryDescriptionMessage(t('Name of the country')),
      'country_code2_if' => $this->getCountryDescriptionMessage(t('2 digit country abbreviation')),
      'country_code3_if' => $this->getCountryDescriptionMessage(t('3 digit country abbreviation')),
    );
  }

  /**
   * Used in getOutputFormats() to output a description for the country format.
   *
   * @param string $country_description
   *   First part of the country description.
   *
   * @access private
   * @return string
   *   Description message for getOutputFormats().
   *
   * @see getOutputFormats()
   */
  private function getCountryDescriptionMessage($country_description) {
    return t('!country_description, display only for addresses whose country is different than the default store country.',
      array('!country_description' => $country_description)
    );
  }

  /**
   * Overrides UcAddressesFieldHandler::outputValue().
   *
   * The country field can be outputted in different formats.
   */
  public function outputValue($value = '', $format = '') {
    if ($value === '') {
      $value = $this->getAddress()->getField($this->getFieldName());
    }

    $country = $this->getCountry($value);
    if (!$country) {
      return t('Unknown');
    }

    // Return country only if the country is not equal to the store's default country.
    if (preg_match('/\_if$/', $format)) {
      if (uc_store_default_country() == $country->country_id) {
        return '';
      }
    }

    switch ($format) {
      case 'country_name':
      case 'country_name_if':
        return t($country->country_name);

      case 'country_code2':
      case 'country_code2_if':
        return $country->country_iso_code_2;

      case 'country_code3':
      case 'country_code3_if':
        return $country->country_iso_code_3;
    }

    // If no format is specified, return country name.
    return t($country->country_name);
  }

  /**
   * Returns country data.
   *
   * @param int $country_id
   *   The country to get.
   *
   * @return object
   *   Data of country if the country is found.
   *   NULL otherwise.
   */
  private function getCountry($country_id) {
    if (isset(self::$countries[$country_id])) {
      return self::$countries[$country_id];
    }

    $result = db_select('uc_countries')
      ->fields('uc_countries')
      ->condition('country_id', $country_id)
      ->execute();

    $country = $result->fetch();
    if ($country) {
      self::$countries[$country->country_id] = $country;
      return $country;
    }
    return NULL;
  }

  /**
   * Returns all countries installed for Ubercart.
   *
   * @return array
   *   List of installed countries in Ubercart.
   */
  private function getAllCountries() {
    static $all_loaded = FALSE;
    if ($all_loaded) {
      return self::$countries;
    }
    $countries = db_select('uc_countries')
      ->fields('uc_countries')
      ->execute()
      ->fetchAll();
    // Index countries.
    foreach ($countries as $country) {
      self::$countries[$country->country_id] = $country;
    }
    $all_loaded = TRUE;
    return self::$countries;
  }
}
