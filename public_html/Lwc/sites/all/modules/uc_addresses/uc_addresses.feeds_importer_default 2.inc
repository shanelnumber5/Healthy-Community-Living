<?php

/**
 * @file
 * Contains default importers for Feeds.
 */

/**
 * Implements hook_feeds_importer_default().
 *
 * @return array
 *   Default importers for Feeds.
 */
function uc_addresses_feeds_importer_default() {
  $export = array();
  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'uc_addresses';
  $feeds_importer->config = array(
    'name' => 'Ubercart Addresses address import',
    'description' => 'Import addresses from a CSV file.',
    'fetcher' => array(
      'plugin_key' => 'FeedsFileFetcher',
      'config' => array(
        'allowed_extensions' => 'txt csv tsv xml opml',
        'direct' => FALSE,
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsCSVParser',
      'config' => array(
        'delimiter' => ';',
        'no_headers' => 0,
      ),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsUcAddressesProcessor',
      'config' => array(
        'mappings' => array(
          array(
            'source' => 'user_name',
            'target' => 'user_name',
            'unique' => 0,
          ),
          array(
            'source' => 'first_name',
            'target' => 'first_name',
            'unique' => 0,
          ),
          array(
            'source' => 'last_name',
            'target' => 'last_name',
            'unique' => 0,
          ),
          array(
            'source' => 'phone',
            'target' => 'phone',
            'unique' => 0,
          ),
          array(
            'source' => 'company',
            'target' => 'company',
            'unique' => 0,
          ),
          array(
            'source' => 'street1',
            'target' => 'street1',
            'unique' => 0,
          ),
          array(
            'source' => 'street2',
            'target' => 'street2',
            'unique' => 0,
          ),
          array(
            'source' => 'city',
            'target' => 'city',
            'unique' => 0,
          ),
          array(
            'source' => 'zone',
            'target' => 'zone:zone_name',
            'unique' => 0,
          ),
          array(
            'source' => 'postal_code',
            'target' => 'postal_code',
            'unique' => 0,
          ),
          array(
            'source' => 'country',
            'target' => 'country:country_name',
            'unique' => 0,
          ),
          array(
            'source' => 'address_name',
            'target' => 'address_name',
            'unique' => 0,
          ),
          array(
            'source' => 'default_shipping',
            'target' => 'default_shipping',
            'unique' => 0,
          ),
          array(
            'source' => 'default_billing',
            'target' => 'default_billing',
            'unique' => 0,
          ),
          array(
            'source' => 'guid',
            'target' => 'guid',
            'unique' => 1,
          ),
        ),
        'update_existing' => '2',
        'mark_as_default_billing' => 1,
        'mark_as_default_shipping' => 1,
      ),
    ),
    'content_type' => '',
    'update' => 0,
    'import_period' => '-1',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['uc_addresses'] = $feeds_importer;
  return $export;
}
