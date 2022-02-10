<?php

/**
 * @file
 * Installs uc_store database tables.
 */

// Countries table.
db_create_table('uc_countries', array(
  'description' => 'Stores country information.',
  'fields' => array(
    'country_id' => array(
      'description' => 'Primary key: numeric ISO country code.',
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'country_name' => array(
      'description' => 'The country name.',
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'country_iso_code_2' => array(
      'description' => 'The two-character ISO country code.',
      'type' => 'char',
      'length' => 2,
      'not null' => TRUE,
      'default' => '',
    ),
    'country_iso_code_3' => array(
      'description' => 'The three-character ISO country code.',
      'type' => 'char',
      'length' => 3,
      'not null' => TRUE,
      'default' => '',
    ),
    'version' => array(
      'description' => 'The version of the CIF that loaded the country information.',
      'type' => 'int',
      'size' => 'small',
      'not null' => TRUE,
      'default' => 0,
    ),
  ),
  'indexes' => array(
    'country_name' => array('country_name'),
  ),
  'primary key' => array('country_id'),
));

// Zones table.
db_create_table('uc_zones', array(
  'description' => 'Stores state/province information within a country.',
  'fields' => array(
    'zone_id' => array(
      'description' => 'Primary key: the unique zone id.',
      'type' => 'serial',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'zone_country_id' => array(
      'description' => 'The {uc_countries}.country_id to which this zone belongs.',
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => 0,
    ),
    'zone_code' => array(
      'description' => 'Standard abbreviation of the zone name.',
      'type' => 'varchar',
      'length' => 32,
      'not null' => TRUE,
      'default' => '',
    ),
    'zone_name' => array(
      'description' => 'The zone name.',
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
  ),
  'indexes' => array(
    'zone_code' => array('zone_code'),
    'zone_country_id' => array('zone_country_id'),
  ),
  'primary key' => array('zone_id'),
  'foreign keys' => array(
    'uc_countries' => array(
      'table' => 'uc_countries',
      'columns' => array('zone_country_id' => 'country_id'),
    ),
  ),
));

// Insert countries.
db_insert('uc_countries')
  ->fields(array(
    'country_id',
    'country_name',
    'country_iso_code_2',
    'country_iso_code_3',
    'version',
  ))
  ->values(array(
    '124',
    'Canada',
    'CA',
    'CAN',
    '2',
  ))
  ->values(array(
    '840',
    'United States',
    'US',
    'USA',
    '1',
  ))
  ->execute();

// Insert zones.
$zones = array(
  array(840, 'AL', 'Alabama'),
  array(840, 'AK', 'Alaska'),
  array(840, 'AS', 'American Samoa'),
  array(840, 'AZ', 'Arizona'),
  array(840, 'AR', 'Arkansas'),
  array(840, 'AF', 'Armed Forces Africa'),
  array(840, 'AA', 'Armed Forces Americas'),
  array(840, 'AC', 'Armed Forces Canada'),
  array(840, 'AE', 'Armed Forces Europe'),
  array(840, 'AM', 'Armed Forces Middle East'),
  array(840, 'AP', 'Armed Forces Pacific'),
  array(840, 'CA', 'California'),
  array(840, 'CO', 'Colorado'),
  array(840, 'CT', 'Connecticut'),
  array(840, 'DE', 'Delaware'),
  array(840, 'DC', 'District of Columbia'),
  array(840, 'FM', 'Federated States Of Micronesia'),
  array(840, 'FL', 'Florida'),
  array(840, 'GA', 'Georgia'),
  array(840, 'GU', 'Guam'),
  array(840, 'HI', 'Hawaii'),
  array(840, 'ID', 'Idaho'),
  array(840, 'IL', 'Illinois'),
  array(840, 'IN', 'Indiana'),
  array(840, 'IA', 'Iowa'),
  array(840, 'KS', 'Kansas'),
  array(840, 'KY', 'Kentucky'),
  array(840, 'LA', 'Louisiana'),
  array(840, 'ME', 'Maine'),
  array(840, 'MH', 'Marshall Islands'),
  array(840, 'MD', 'Maryland'),
  array(840, 'MA', 'Massachusetts'),
  array(840, 'MI', 'Michigan'),
  array(840, 'MN', 'Minnesota'),
  array(840, 'MS', 'Mississippi'),
  array(840, 'MO', 'Missouri'),
  array(840, 'MT', 'Montana'),
  array(840, 'NE', 'Nebraska'),
  array(840, 'NV', 'Nevada'),
  array(840, 'NH', 'New Hampshire'),
  array(840, 'NJ', 'New Jersey'),
  array(840, 'NM', 'New Mexico'),
  array(840, 'NY', 'New York'),
  array(840, 'NC', 'North Carolina'),
  array(840, 'ND', 'North Dakota'),
  array(840, 'MP', 'Northern Mariana Islands'),
  array(840, 'OH', 'Ohio'),
  array(840, 'OK', 'Oklahoma'),
  array(840, 'OR', 'Oregon'),
  array(840, 'PW', 'Palau'),
  array(840, 'PA', 'Pennsylvania'),
  array(840, 'PR', 'Puerto Rico'),
  array(840, 'RI', 'Rhode Island'),
  array(840, 'SC', 'South Carolina'),
  array(840, 'SD', 'South Dakota'),
  array(840, 'TN', 'Tennessee'),
  array(840, 'TX', 'Texas'),
  array(840, 'UT', 'Utah'),
  array(840, 'VT', 'Vermont'),
  array(840, 'VI', 'Virgin Islands'),
  array(840, 'VA', 'Virginia'),
  array(840, 'WA', 'Washington'),
  array(840, 'WV', 'West Virginia'),
  array(840, 'WI', 'Wisconsin'),
  array(840, 'WY', 'Wyoming'),
  array(124, 'AB', 'Alberta'),
  array(124, 'BC', 'British Columbia'),
  array(124, 'MB', 'Manitoba'),
  array(124, 'NL', 'Newfoundland and Labrador'),
  array(124, 'NB', 'New Brunswick'),
  array(124, 'NS', 'Nova Scotia'),
  array(124, 'NT', 'Northwest Territories'),
  array(124, 'NU', 'Nunavut'),
  array(124, 'ON', 'Ontario'),
  array(124, 'PE', 'Prince Edward Island'),
  array(124, 'QC', 'Quebec'),
  array(124, 'SK', 'Saskatchewan'),
  array(124, 'YT', 'Yukon Territory'),
);

$query = db_insert('uc_zones')->fields(array('zone_country_id', 'zone_code', 'zone_name'));
foreach ($zones as $zone) {
  $query->values($zone);
}
$query->execute();

// Tell system about uc_store module.
$record = array(
  'filename' => _drupal_get_filename_fallback('module', 'uc_store', FALSE, TRUE),
  'name' => 'uc_store',
  'type' => 'module',
  'owner' => '',
  'status' => 0,
  'throttle' => '0',
  'bootstrap' => 0,
  'schema_version' => 7300,
  'weight' => 0,
  'info' => 'a:10:{s:4:"name";s:5:"Store";s:11:"description";s:70:"REQUIRED. Handles store settings and management of your Ubercart site.";s:7:"package";s:15:"Ubercart - core";s:4:"core";s:3:"6.x";s:3:"php";s:3:"5.0";s:7:"version";s:15:"6.x-2.10+29-dev";s:7:"project";s:8:"ubercart";s:9:"datestamp";s:10:"1353677153";s:12:"dependencies";a:0:{}s:10:"dependents";a:0:{}}',
);
db_insert('system')
  ->fields(array_keys($record))
  ->values(array_values($record))
  ->execute();
