<?php

/**
 * @file
 * Installs database tables using the uc_addresses 6.x-1.x style.
 */

db_create_table('uc_addresses', array(
  'fields' => array(
    'aid' => array(
      'description' => t('The address ID'),
      'type' => 'serial',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'uid' => array(
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => 0,
    ),
    'first_name' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'last_name' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'phone' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'company' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'street1' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'street2' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'city' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'zone' => array(
      'type' => 'int',
      'size' => 'medium',
      'not null' => TRUE,
      'default' => 0,
    ),
    'postal_code' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'country' => array(
      'type' => 'int',
      'size' => 'medium',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => 0,
    ),
    'address_name' => array(
      'type' => 'varchar',
      'length' => 20,
      'not null' => FALSE,
    ),
    'created' => array(
      'type' => 'int',
      'not null' => TRUE,
      'default' => 0,
    ),
    'modified' => array(
      'type' => 'int',
      'not null' => TRUE,
      'default' => 0,
    ),
  ),
  'indexes' => array(
    'aid_uid_idx' => array('aid', 'uid'),
  ),
  'primary key' => array('aid'),
));

db_create_table('uc_addresses_defaults', array(
  'fields' => array(
    'aid' => array(
      'type' => 'int',
      'unsigned' => 1,
      'not null' => TRUE,
    ),
    'uid' => array(
      'type' => 'int',
      'unsigned' => 1,
      'not null' => TRUE,
    ),
  ),
  'primary key' => array('aid', 'uid'),
));

// Insert two addresses in the uc_addresses table and mark one as default.
$addresses6 = array();
for ($i = 1; $i <= 2; $i++) {
  $addresses6[$i] = array(
    'aid' => $i,
    'uid' => 1,
    'first_name' => self::randomName(),
    'last_name' => self::randomName(),
    'phone' => self::randomString(),
    'company' => self::randomString(),
    'street1' => self::randomString(),
    'street2' => self::randomString(),
    'city' => self::randomString(),
    'postal_code' => mt_rand(10000, 99999),
    'country' => 840,
    'address_name' => '',
    'created' => REQUEST_TIME,
    'modified' => REQUEST_TIME,
  );

  db_insert('uc_addresses')
    ->fields(array_keys($addresses6[$i]))
    ->values(array_values($addresses6[$i]))
    ->execute();
}
// Mark the second address as default.
$record = array(
  'aid' => 2,
  'uid' => 1,
);
db_insert('uc_addresses_defaults')
  ->fields(array_keys($record))
  ->values(array_values($record))
  ->execute();

// Tell Drupal an older version of uc_addresses was already installed.
$record = array(
  'filename' => _drupal_get_filename_fallback('module', 'uc_addresses', FALSE, TRUE),
  'name' => 'uc_addresses',
  'type' => 'module',
  'owner' => '',
  'status' => 0,
  'throttle' => '0',
  'bootstrap' => 0,
  'schema_version' => 6001,
  'weight' => 0,
  'info' => 'a:10:{s:4:"name";s:18:"Ubercart Addresses";s:11:"description";s:87:"Allows users to manage a set of addresses that can be referenced when placing an order.";s:12:"dependencies";a:2:{i:0;s:8:"uc_order";i:1;s:8:"uc_store";}s:7:"package";s:16:"Ubercart - extra";s:4:"core";s:3:"6.x";s:7:"version";s:7:"6.x-1.0";s:7:"project";s:12:"uc_addresses";s:9:"datestamp";s:10:"1278806418";s:10:"dependents";a:0:{}s:3:"php";s:5:"4.3.5";}',
);
db_delete('system')
  ->condition('type', 'module')
  ->condition('name', 'uc_addresses')
  ->execute();
db_insert('system')
  ->fields(array_keys($record))
  ->values(array_values($record))
  ->execute();
