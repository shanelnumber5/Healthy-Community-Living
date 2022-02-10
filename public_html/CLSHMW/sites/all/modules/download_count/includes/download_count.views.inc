<?php

/**
 * @file
 * Provide views data for the download_count.module.
 */

/**
 * Implements hook_views_data().
 */
function download_count_views_data() {

  // base table
  $data['download_count']['table']['group'] = t('Download history');
  $data['download_count']['table']['base'] = array(
    'field' => 'dcid',
    'title' => t('Download history'),
    'help' => t('Download history of download count module managed file downloads.'),
  );

  // join for files table
  $data['download_count']['table']['join']['file_managed'] = array(
      'left_field' => 'fid',
      'field' => 'fid',
  );

  // fields

  // dc_id
  $data['download_count']['dcid'] = array(
    'title' => t('ID'),
    'help' => t('The download count ID of the download.'),
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

  // fid
  $data['download_count']['fid'] = array(
    'title' => t('File'),
    'help' => t('The file information of the downloaded file.'),
    'relationship' => array(
      'base' => 'file_managed',
      'base field' => 'fid',
      'handler' => 'views_handler_relationship',
      'label' => t('File'),
    ),
  );

  // uid
  $data['download_count']['uid'] = array(
    'title' => t('User'),
    'help' => t('The user who downloaded the file.'),
    'relationship' => array(
      'base' => 'users',
      'base field' => 'uid',
      'handler' => 'views_handler_relationship',
      'label' => t('User'),
    ),
  );

  // type (entity type)
  $data['download_count']['type'] = array(
    'title' => t('Entity Type'),
    'help' => t('The type of the entity from which the file was downloaded.'),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

    // id (entity id)
  $data['download_count']['id'] = array(
    'title' => t('Entity ID'),
    'help' => t('The id of the entity from which the file was downloaded.'),
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

  // referrer
  $data['download_count']['referrer'] = array(
    'title' => t('Referrer'),
    'help' => t('Referrer URI.'),
    'field' => array(
      'handler' => 'views_handler_field_url',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  // ip_address
  $data['download_count']['ip_address'] = array(
    'title' => t('IP Address'),
    'help' => t('IP address of the user who downloaded the file.'),
    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

  // timestamp
  $data['download_count']['timestamp'] = array(
    'title' => t('Date-time Downloaded'),
    'help' => t('The date and time the file was downloaded.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );

  return $data;
}


function download_count_views_data_alter(&$data) {
  // join for files table
  $data['file_managed']['table']['join']['download_count'] = array(
      'left_field' => 'fid',
      'field' => 'fid',
  );
}
