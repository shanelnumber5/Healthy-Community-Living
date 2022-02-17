<?php

/**
 * @file
 * Provide views data and handlers for time_spent.module.
 *
 * @ingroup views_module_handlers
 */

/**
 * Implements hook_views_data().
 */
function time_spent_views_data() {
  $data['time_spent_page']['table']['group'] = t('Timespent');

  $data['time_spent_page']['table']['base'] = array(
    'field' => 'time_spent_id',
    'title' => t('Time spent'),
    'defaults' => array(
      'field' => 'time_spent_id',
    ),
  );

  $data['time_spent_page']['nid'] = array(
    'title' => t('Node ID'),
    'help' => t('Time spent node ID.'),
    'relationship' => array(
      'base' => 'node',
      'base field' => 'nid',
      'handler' => 'views_handler_relationship',
      'label' => t('Timespent node'),
    ),
  );

  $data['time_spent_page']['uid'] = array(
    'title' => t('User ID'),
    'help' => t('Time spent user ID.'),
    'relationship' => array(
      'base' => 'users',
      'base field' => 'uid',
      'handler' => 'views_handler_relationship',
      'label' => t('Timespent user'),
    ),
  );

  $data['time_spent_page']['time_spent_id'] = array(
    'title' => t('Time spent id'),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );

  $data['time_spent_page']['timespent'] = array(
    'title' => t('Time spent length'), // The item it appears as on the UI,
    'help' => t('The timespent length'),
    'field' => array(
      'handler' => 'time_spent_handler_field_timespent',
      'click sortable' => TRUE,
    ),
  );

  return $data;
}
',
      'click sortable' => TRUE,
    ),
  );

  return $data;
}
