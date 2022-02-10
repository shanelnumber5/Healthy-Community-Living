<?php

/**
 * @file
 * Contains a View to replace the address book page.
 */

/**
 * Implements hook_views_default_views().
 *
 * Provides the following Views:
 * - uc_addresses_address_book
 *   A View for the address book.
 */
function uc_addresses_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'uc_addresses_address_book';
  $view->description = 'Manage your addresses';
  $view->tag = 'uc_addresses';
  $view->base_table = 'uc_addresses';
  $view->human_name = 'Address book';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = TRUE;

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Address book';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'uc_addresses_views_access';
  $handler->display->display_options['access']['uid_argument'] = 'uid';
  $handler->display->display_options['access']['aid_argument'] = '';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['style_plugin'] = 'grid';
  $handler->display->display_options['style_options']['row_class'] = 'address-item';
  $handler->display->display_options['style_options']['columns'] = '2';
  $handler->display->display_options['style_options']['fill_single_line'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'uc_addresses';
  $handler->display->display_options['row_options']['view_link'] = 0;
  $handler->display->display_options['row_options']['edit_link'] = 1;
  $handler->display->display_options['row_options']['delete_link'] = 1;
  $handler->display->display_options['row_options']['default_flags'] = 1;
  /* No results behavior: Global: Text area */
  $handler->display->display_options['empty']['area']['id'] = 'area';
  $handler->display->display_options['empty']['area']['table'] = 'views';
  $handler->display->display_options['empty']['area']['field'] = 'area';
  $handler->display->display_options['empty']['area']['empty'] = TRUE;
  $handler->display->display_options['empty']['area']['content'] = 'No addresses have been saved.';
  $handler->display->display_options['empty']['area']['format'] = 'filtered_html';
  /* Field: Ubercart Addresses: Address ID */
  $handler->display->display_options['fields']['aid']['id'] = 'aid';
  $handler->display->display_options['fields']['aid']['table'] = 'uc_addresses';
  $handler->display->display_options['fields']['aid']['field'] = 'aid';
  /* Sort criterion: Ubercart Addresses: Default billing address */
  $handler->display->display_options['sorts']['default_billing']['id'] = 'default_billing';
  $handler->display->display_options['sorts']['default_billing']['table'] = 'uc_addresses';
  $handler->display->display_options['sorts']['default_billing']['field'] = 'default_billing';
  $handler->display->display_options['sorts']['default_billing']['order'] = 'DESC';
  /* Sort criterion: Ubercart Addresses: Default shipping address */
  $handler->display->display_options['sorts']['default_shipping']['id'] = 'default_shipping';
  $handler->display->display_options['sorts']['default_shipping']['table'] = 'uc_addresses';
  $handler->display->display_options['sorts']['default_shipping']['field'] = 'default_shipping';
  $handler->display->display_options['sorts']['default_shipping']['order'] = 'DESC';
  /* Sort criterion: Ubercart Addresses: Address name */
  $handler->display->display_options['sorts']['address_name']['id'] = 'address_name';
  $handler->display->display_options['sorts']['address_name']['table'] = 'uc_addresses';
  $handler->display->display_options['sorts']['address_name']['field'] = 'address_name';
  /* Contextual filter: Ubercart Addresses: User ID */
  $handler->display->display_options['arguments']['uid']['id'] = 'uid';
  $handler->display->display_options['arguments']['uid']['table'] = 'uc_addresses';
  $handler->display->display_options['arguments']['uid']['field'] = 'uid';
  $handler->display->display_options['arguments']['uid']['default_action'] = 'not found';
  $handler->display->display_options['arguments']['uid']['default_argument_type'] = 'fixed';
  $handler->display->display_options['arguments']['uid']['summary']['number_of_records'] = '0';
  $handler->display->display_options['arguments']['uid']['summary']['format'] = 'default_summary';
  $handler->display->display_options['arguments']['uid']['summary_options']['items_per_page'] = '25';
  $handler->display->display_options['arguments']['uid']['specify_validation'] = TRUE;
  $handler->display->display_options['arguments']['uid']['validate']['type'] = 'uc_addresses_user_address_access';
  $handler->display->display_options['arguments']['uid']['validate']['fail'] = 'access denied';
  /* Filter criterion: Ubercart Addresses: Access */
  $handler->display->display_options['filters']['access_view']['id'] = 'access_view';
  $handler->display->display_options['filters']['access_view']['table'] = 'uc_addresses';
  $handler->display->display_options['filters']['access_view']['field'] = 'access_view';
  $handler->display->display_options['filters']['access_view']['value'] = '1';

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page');
  $handler->display->display_options['defaults']['hide_admin_links'] = FALSE;
  $handler->display->display_options['path'] = 'user/%/addresses';
  $handler->display->display_options['menu']['type'] = 'tab';
  $handler->display->display_options['menu']['title'] = 'Address book';
  $handler->display->display_options['menu']['description'] = 'Manage your addresses';
  $handler->display->display_options['menu']['weight'] = '0';
  $handler->display->display_options['menu']['context'] = 0;
  $translatables['uc_addresses_address_book'] = array(
    t('Master'),
    t('Address book'),
    t('more'),
    t('Apply'),
    t('Reset'),
    t('Sort by'),
    t('Asc'),
    t('Desc'),
    t('No addresses have been saved.'),
    t('Address ID'),
    t('.'),
    t(','),
    t('All'),
    t('Page'),
  );

  $export['uc_addresses_address_book'] = $view;

  return $export;
}
