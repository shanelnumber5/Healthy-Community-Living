<?php

/**
 * @file
 * Views implementation for Track da files module.
 */

/**
 * Implements hook_views_data().
 */
function track_da_files_views_data() {
  $table = array();

  /* Track da files table */

  // Group identifier.
  $table['track_da_files']['table']['group'] = t('Track da Files');

  // Base table.
  $table['track_da_files']['table']['base'] = array(
    'field' => 'recid',
    'title' => t('Track da files'),
    'help' => t('Show tracked files display records.'),
  );

  // Declare how TDF relates to TDFP, users, and file_managed tables.
  $table['track_da_files']['table']['join'] = array(
    'track_da_files_paths' => array(
      'left_field' => 'pid',
      'field' => 'pid',
      'type' => 'INNER',
    ),
    'users' => array(
      'left_field' => 'uid',
      'field' => 'uid',
      'type' => 'INNER',
    ),
    'file_managed' => array(
      'left_table' => 'track_da_files_paths',
      'left_field' => 'pid',
      'type' => 'INNER',
      'field' => 'pid',
    ),
  );

  // Declare TDF table fields.

  // recid.
  $table['track_da_files']['recid'] = array(
    'title' => t('Record ID'),
    'help' => t('The unique record ID of a tracked file.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('Record ID'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on Record ID'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('Record ID'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Record ID'),
    ),
  );

  // pid.
  $table['track_da_files']['pid'] = array(
    'title' => t('Path ID'),
    'help' => t('Path ID is a reference to a unique tracked file path.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('Path ID'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on Path ID'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('Path ID'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Path ID'),
    ),
    'relationship' => array(
      'label' => t('Path ID'),
      'base' => 'track_da_files_paths',
      'base field' => 'pid',
    ),
  );

  // uid.
  $table['track_da_files']['uid'] = array(
    'title' => t('User ID'),
    'help' => t('The user who displayed the file.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('User ID'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on User ID'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('User ID'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by User ID'),
    ),
   'relationship' => array(
      'title' => t('User information'),
      'base' => 'users',
      'base field' => 'uid',
      'handler' => 'views_handler_relationship',
    ),
  );

  // ip.
  $table['track_da_files']['ip'] = array(
    'title' => t('IP address'),
    'help' => t('IP address'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('IP address'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on IP address'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('IP address'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by IP address'),
    ),
  );

  // browser.
  $table['track_da_files']['browser'] = array(
    'title' => t('Browser used'),
    'help' => t('Browser used'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('Browser used'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on Browser used'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('Browser used'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Browser used'),
    ),
  );

  // browser version.
  $table['track_da_files']['browser_version'] = array(
    'title' => t('Browser version'),
    'help' => t('Browser version'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('Browser version'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on Browser version'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('Browser version'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Browser version'),
    ),
  );

  // browser table.
  $table['track_da_files']['browser_platform'] = array(
    'title' => t('Browser platform'),
    'help' => t('Browser platform'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('Browser platform'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on Browser platform'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('Browser platform'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Browser platform'),
    ),
  );

  // browser referer.
  $table['track_da_files']['referer'] = array(
    'title' => t('Referer'),
    'help' => t('Referer'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('Referer'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on Referer'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('Referer'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Referer'),
    ),
  );

  // time.
  $table['track_da_files']['time'] = array(
    'title' => t('Time'),
    'help' => t('Time'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'help' => t('Time'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
      'allow empty' => true,
      'help' => t('Filter on Time'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_date',
      'help' => t('Time'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Time'),
    ),
  );

  // type.
  $table['track_da_files']['type'] = array(
    'title' => t('Entity type'),
    'help' => t('The entity type the file is attached to'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('The entity type the file is attached to'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on The entity type the file is attached to'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('The entity type the file is attached to'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by The entity type the file is attached to'),
    ),
  );

  // id.
  $table['track_da_files']['id'] = array(
    'title' => t('Entity ID'),
    'help' => t('The entity id the file is attached to.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('Entity ID'),
      'click sortable' => true,
    ),
    'filter' => array (
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on Entity ID'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('Entity ID'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Entity ID'),
    ),
  );


 /* Track da files paths */

 // Group identifier.
 $table['track_da_files_paths']['table']['group'] = t('Track da Files paths');

 // Declare how TDFP relates to TDF, users, and file_managed tables.
 $table['track_da_files_paths']['table']['join'] =  array(
   'track_da_files' => array(
        'left_field' => 'pid',
        'field' => 'pid',
        'type' => 'INNER',
      ),
      'file_managed' => array(
        'left_field' => 'fid',
        'field' => 'fid',
        'type' => 'INNER',
      ),
    );

  // pid.
  $table['track_da_files_paths']['pid'] =  array(
    'title' => t('Path id'),
    'help' => t('The path ID of a tracked file.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('Path id'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on Path id'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('Path id'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Path id'),
    ),
  );

  // path.
  $table['track_da_files_paths']['path'] =  array(
    'title' => t('Path'),
    'help' => t('The path of a tracked file'),
    'field' => array(
      'handler' => 'views_handler_field',
      'help' => t('Path'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
      'allow empty' => true,
      'help' => t('Filter on Path'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
      'help' => t('Path'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by Path'),
    ),
  );

  // fid.
  $table['track_da_files_paths']['fid'] =  array(
    'title' => t('File ID'),
    'help' => t('The file ID associated with a tracked file path'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'help' => t('File ID'),
      'click sortable' => true,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
      'allow empty' => true,
      'help' => t('Filter on File ID'),
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
      'help' => t('File ID'),
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
      'help' => t('Sort by File ID'),
    ),
  );

return $table;

}

/**
 * Implements hook_views_data_alter().
 */
function track_da_files_views_data_alter(&$data) {

  // Declare how TDF relates to the file_managed table.
  $data['file_managed']['table']['join'] = array(
    'track_da_files' => array(
      'left_table' => 'track_da_files_paths',
      'left_field' => 'fid',
      'type' => 'INNER',
      'field' => 'fid',
    ),
  );
}