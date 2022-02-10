<?php

/**
 * @file
 * Hook implementations for the Views module.
 */

/**
 * Implements hook_views_data().
 */
function uc_addresses_views_data() {
  $data = array();

  // Get schema.
  $schema = drupal_get_schema('uc_addresses');
  // Get registered address fields.
  $address_fields = uc_addresses_get_address_fields();

  // Ubercart Addresses table.
  $data['uc_addresses']['table']['group'] = t('Ubercart Addresses');
  $data['uc_addresses']['table']['base'] = array(
    'field' => 'aid',
    'title' => t('Ubercart Addresses'),
    'help' => t('Addresses in the customers address book that can be referenced when placing an order.'),
  );

  // Declare entity type.
  $data['uc_addresses']['table']['entity type'] = 'uc_addresses';

  // Declare each schema field.
  foreach ($schema['fields'] as $fieldname => $schema_field) {
    if (isset($address_fields[$fieldname])) {
      // Views properties for all fields.
      $data['uc_addresses'][$fieldname] = array(
        'title' => $address_fields[$fieldname]['title'],
        'help' => $address_fields[$fieldname]['description'],
      );
      // Set Views handlers per field.
      switch ($fieldname) {
        case 'aid':
          // Address ID (primary key).
          $data['uc_addresses']['aid'] += array(
            'field' => array(
              'handler' => 'views_handler_field_numeric',
              'click sortable' => TRUE,
            ),
            'filter' => array(
              'handler' => 'views_handler_filter_numeric',
            ),
            'sort' => array(
              'handler' => 'views_handler_sort',
            ),
            'argument' => array(
              'handler' => 'views_handler_argument_numeric',
            ),
          );
          break;

        case 'uid':
          // Address owner.
          $data['uc_addresses']['uid'] += array(
            'relationship' => array(
              'help' => t('Relate an address to the user who owns it.'),
              'handler' => 'views_handler_relationship',
              'base' => 'users',
              'field' => 'uid',
              'label' => t('owner'),
            ),
            'field' => array(
              'handler' => 'views_handler_field_user',
              'click sortable' => TRUE,
            ),
            'filter' => array(
              'handler' => 'views_handler_filter_user_name',
            ),
            'argument' => array(
              'handler' => 'views_handler_argument_numeric',
            ),
          );
          break;

        case 'zone':
        case 'country':
          // Fields 'zone' and 'country' are setup differently (see further).
          break;

        case 'default_shipping':
        case 'default_billing':
          // Boolean fields.
          $data['uc_addresses'][$fieldname] += array(
            'field' => array(
              'handler' => 'views_handler_field_boolean',
              'click sortable' => TRUE,
            ),
            'filter' => array(
              'handler' => 'views_handler_filter_boolean_operator',
              'type' => 'yes-no',
              'use equal' => TRUE,
            ),
            'sort' => array(
              'handler' => 'views_handler_sort',
            ),
          );
          break;

        case 'created':
        case 'modified':
          // Date fields.
          // Use the Date module views handler if available.
          if (module_exists('date_views')) {
            $data['uc_addresses'][$fieldname]['filter'] = array(
              'handler' => 'date_views_filter_handler_simple',
              'is date' => TRUE,
            );
          }
          $data['uc_addresses'][$fieldname] += array(
            'field' => array(
              'handler' => 'views_handler_field_date',
              'click sortable' => TRUE,
            ),
            'sort' => array(
              'handler' => 'views_handler_sort_date',
            ),
            'filter' => array(
              'handler' => 'views_handler_filter_date',
            ),
          );
          break;

        default:
          // Assume all other fields are text fields.
          $data['uc_addresses'][$fieldname] += array(
            'field' => array(
              'handler' => 'views_handler_field',
              'click sortable' => TRUE,
            ),
            'sort' => array(
              'handler' => 'views_handler_sort',
            ),
            'filter' => array(
              'handler' => 'views_handler_filter_string',
            ),
            'argument' => array(
              'handler' => 'views_handler_argument_string',
            ),
          );
          break;
      }

      // Set correct title of Ubercart core address fields.
      // First set default value for 'street2' as that is by default empty.
      $data['uc_addresses']['street2']['title'] = t('Street address 2');
      switch ($fieldname) {
        // @todo This might as well moved to uc_addresses_uc_addresses_fields() instead.
        case 'first_name':
        case 'last_name':
        case 'company':
        case 'street1':
        case 'street2':
        case 'city':
        case 'postal_code':
        case 'phone':
          $title = uc_get_field_name($fieldname);
          if ($title) {
            $data['uc_addresses'][$fieldname]['title'] = $title;
          }
          break;
      }
    }
  }

  // Countries.
  $data['uc_addresses']['country'] = array(
    'title' => t('ISO country code (numeric)'),
    'help' => t("The addressee's !field.", array('!field' => t('numeric ISO country code'))),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );
  $data['uc_addresses_countries']['table']['group'] = $data['uc_addresses']['table']['group'];
  $data['uc_addresses_countries']['table']['join']['uc_addresses'] = array(
    'table' => 'uc_countries',
    'left_field' => 'country',
    'field' => 'country_id',
  );
  $data['uc_addresses_countries']['country_name'] = array(
    'title' => t('Country name'),
    'help' => t("The addressee's !field.", array('!field' => t('country name'))),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );
  $data['uc_addresses_countries']['country_iso_code_2'] = array(
    'title' => t('ISO country code (2 characters)'),
    'help' => t("The addressee's !field.", array('!field' => t('ISO country code'))),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );
  $data['uc_addresses_countries']['country_iso_code_3'] = array(
    'title' => t('ISO country code (3 characters)'),
    'help' => t("The addressee's !field.", array('!field' => t('ISO country code'))),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

  // Zones.
  $data['uc_addresses']['zone'] = array(
    'title' => t('Zone ID'),
    'help' => t("The addressee's !field.", array('!field' => t('zone ID'))),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );
  $data['uc_addresses_zones']['table']['group'] = $data['uc_addresses']['table']['group'];
  $data['uc_addresses_zones']['table']['join']['uc_addresses'] = array(
    'table' => 'uc_zones',
    'left_field' => 'zone',
    'field' => 'zone_id',
  );
  $data['uc_addresses_zones']['zone_name'] = array(
    'title' => t('State/Province'),
    'help' => t("The addressee's !field.", array('!field' => t('state or province'))),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );
  $data['uc_addresses_zones']['zone_code'] = array(
    'title' => t('State/Province code'),
    'help' => t("The addressee's !field.", array('!field' => t('state or province code'))),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

  // Access filters.
  $data['uc_addresses']['access_view'] = array(
    'title' => t('Access'),
    'help' => t('Filter the Views results by address view access.'),
    'real field' => 'aid',
    'filter' => array(
      'help' => t('Filters the result of the view by checking address view access.') . ' ' . t('Note that this filter does not work well with pager.'),
      'handler' => 'uc_addresses_handler_filter_access',
      'uc_addresses_access_type' => 'view',
    ),
  );
  $data['uc_addresses']['access_edit'] = array(
    'title' => t('Edit access'),
    'help' => t('Filter the Views results by address edit access.'),
    'real field' => 'aid',
    'filter' => array(
      'help' => t('Filters the result of the view by checking address edit access.') . ' ' . t('Note that this filter does not work well with pager.'),
      'handler' => 'uc_addresses_handler_filter_access',
      'uc_addresses_access_type' => 'edit',
    ),
  );
  $data['uc_addresses']['access_delete'] = array(
    'title' => t('Delete access'),
    'help' => t('Filter the Views results by address delete access.'),
    'real field' => 'aid',
    'filter' => array(
      'help' => t('Filters the result of the view by checking address delete access.') . ' ' . t('Note that this filter does not work well with pager.'),
      'handler' => 'uc_addresses_handler_filter_access',
      'uc_addresses_access_type' => 'delete',
    ),
  );

  // View, edit and delete links.
  $data['uc_addresses']['view'] = array(
    'field' => array(
      'title' => t('Link'),
      'help' => t('Provide a simple link to the address.'),
      'handler' => 'uc_addresses_handler_field_uc_addresses_link',
    ),
  );
  $data['uc_addresses']['edit'] = array(
    'field' => array(
      'title' => t('Edit link'),
      'help' => t('Provide a simple link to edit the address.'),
      'handler' => 'uc_addresses_handler_field_uc_addresses_link_edit',
    ),
  );
  $data['uc_addresses']['delete'] = array(
    'field' => array(
      'title' => t('Delete link'),
      'help' => t('Provide a simple link to delete the address.'),
      'handler' => 'uc_addresses_handler_field_uc_addresses_link_delete',
    ),
  );

  // Make address fields available in user based views.
  $data['users']['uc_addresses'] = array(
    'title' => t('Ubercart addresses address'),
    'help' => t('Addresses of the given user.'),
    'real field' => 'uid',
    'relationship' => array(
      'base' => 'uc_addresses',
      'base field' => 'uid',
      'handler' => 'views_handler_relationship',
      'label' => t('Ubercart addresses address'),
    ),
  );

  return $data;
}

/**
 * Implements hook_views_plugins().
 */
function uc_addresses_views_plugins() {
  return array(
    'row' => array(
      'uc_addresses' => array(
        'title' => t('Ubercart Addresses address'),
        'help' => t('Display the address with standard address view.'),
        'handler' => 'uc_addresses_views_plugin_row_address_view',
        'path' => drupal_get_path('module', 'uc_addresses') . '/views',
        'base' => array('uc_addresses'), // only works with 'uc_addresses' as base.
        'uses options' => TRUE,
        'type' => 'normal',
      ),
    ),
    'access' => array(
      'uc_addresses_views_access' => array(
        'title' => t('Ubercart Addresses Views Access'),
        'help' => t('Check for address access based on a user ID or address ID argument.'),
        'handler' => 'uc_addresses_views_access',
        'uses options' => TRUE,
        'path' => drupal_get_path('module', 'uc_addresses') . '/views',
      ),
    ),
    'argument validator' => array(
      'uc_addresses_address_access' => array(
        'title' => t('Address access'),
        'parent' => 'views_plugin_argument_validate',
        'handler' => 'uc_addresses_views_plugin_argument_address_access',
        'path' => drupal_get_path('module', 'uc_addresses') . '/views',
      ),
      'uc_addresses_user_address_access' => array(
        'title' => t('User: address access'),
        'parent' => 'views_plugin_argument_validate_user',
        'handler' => 'uc_addresses_views_plugin_argument_user_address_access',
        'path' => drupal_get_path('module', 'uc_addresses') . '/views',
      ),
    ),
  );
}
