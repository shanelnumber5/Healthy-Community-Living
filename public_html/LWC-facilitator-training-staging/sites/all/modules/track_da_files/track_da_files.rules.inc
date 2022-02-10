<?php

/**
 * @file
 * Rules specific functions for Track da files module.
 */

/**
 * Implements hook_rules_event_info().
 */
function track_da_files_rules_event_info() {
  return array(
    'track_da_files' => array(
      'group' => t('Track da files'),
      'label' => t('A file has been displayed'),
      'module' => 'track_da_files',
      'variables' => array(
        'file' => array(
          'type' => 'file',
          'label' => t('Displayed file'),
        ),
        'user' => array(
          'type' => 'user',
          'label' => t('User displaying file'),
        ),
      ),
    ),
  );
}
