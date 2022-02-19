<?php

/**
 * @file
 * Administrative page callbacks for Track da files module.
 */

/**
 * Settings form.
 */
function track_da_files_config_form() {

  // Variable which stores options to show in main reports.
  $track_da_files_report_datas = variable_get('track_da_files_report_datas', array(
    'total_ips',
    'average_by_ip',
    'last_display',
  )
  );

  // Variable which stores datas that will appear in files displays reports.
  $track_da_files_report_files_datas = variable_get('track_da_files_report_files_datas', array(
    'timestamp',
    'filesize',
    'filemime',
  )
  );

  // Variable which stores stores options to show in file specific report.
  $track_da_files_single_file_report_datas = variable_get('track_da_files_single_file_report_datas', array(
    'id',
    'referer',
    'browser',
    'browser_version',
    'browser_platform',
    'ip',
    'uid',
  )
  );

  // Variable which stores value for enabling user specific report.
  $track_da_files_user_report_enabled = variable_get('track_da_files_user_report_enabled', 1);

  // Variable which stores stores options to show in user specific report.
  $track_da_files_single_user_report_datas = variable_get('track_da_files_single_user_report_datas', array(
    'referer',
    'browser',
    'browser_version',
    'browser_platform',
    'ip',
  )
  );

  // Variable storing value for enabling showing tracking datas on the site.
  $track_da_files_file_field_links_show_enabled = variable_get('track_da_files_file_field_links_show_enabled', 1);

  if (module_exists('colorbox')) {
    $track_da_files_colorbox_enabled = variable_get('track_da_files_colorbox_enabled', 0);
  }

  $form['track_da_files'] = array(
    '#type' => 'vertical_tabs',
  );

  // Main report configuration section.
  $form['track_da_files']['track_da_files_report_datas'] = array(
    '#type' => 'fieldset',
    '#title' => t('Main report'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['track_da_files']['track_da_files_report_datas']['track_da_files_report_datas'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Displays datas to show in main report'),
    '#options' => array(
      'total_ips' => t('Total ips'),
      'average_by_ip' => t('Average count by ip'),
      'last_display' => t('Last display date'),
    ),
    '#default_value' => $track_da_files_report_datas,
    '#description' => t('Datas that will appear in main files displays reports.'),
  );

  $form['track_da_files']['track_da_files_report_datas']['track_da_files_report_files_datas'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Files datas to show in main report'),
    '#options' => array(
      'timestamp' => t('Created'),
      'filesize' => t('File size'),
      'filemime' => t('File mime'),
    ),
    '#default_value' => $track_da_files_report_files_datas,
    '#description' => t('Specific files datas that will appear in main report.'),
  );

  // File specific report configuration section.
  $form['track_da_files']['file_specific_reports'] = array(
    '#type' => 'fieldset',
    '#title' => t('File report'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['track_da_files']['file_specific_reports']['track_da_files_single_file_report_datas'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Displays datas to show in file report'),
    '#options' => array(
      'id' => t('Related content'),
      'browser' => t('Browser'),
      'browser_version' => t('Browser version'),
      'browser_platform' => t('Browser platform'),
      'referer' => t('Displayed from URL'),
      'ip' => t('Ip'),
      'uid' => t('User who displayed file'),
    ),
    '#default_value' => $track_da_files_single_file_report_datas,
    '#description' => t('Datas that will appear in file specific report.'),
  );

  // User specific report configuration section.
  $form['track_da_files']['user_specific_reports'] = array(
    '#type' => 'fieldset',
    '#title' => t('User report'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['track_da_files']['user_specific_reports']['track_da_files_user_report_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable user specific report'),
    '#default_value' => $track_da_files_user_report_enabled,
  );

  $form['track_da_files']['user_specific_reports']['track_da_files_single_user_report_datas'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Displays datas to show in user report'),
    '#options' => array(
      'browser' => t('Browser'),
      'browser_version' => t('Browser version'),
      'browser_platform' => t('Browser platform'),
      'referer' => t('Displayed from URL'),
      'ip' => t('Ip'),
    ),
    '#default_value' => $track_da_files_single_user_report_datas,
    '#description' => t('Datas that will appear in users displays reports.'),
    '#states' => array(
      'visible' => array(
        ':input[name=track_da_files_user_report_enabled]' => array('checked' => TRUE),
      ),
    ),
  );

  // Configure to show datas near from file field links.
  $form['file_field_links_show'] = array(
    '#type' => 'fieldset',
    '#title' => t('File field links'),
  );

  $form['file_field_links_show']['track_da_files_file_field_links_show_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Provide counter near file field links'),
    '#default_value' => $track_da_files_file_field_links_show_enabled,
    '#description' => t('A counter showing number of times a file has been displayed will appear in a near from files links'),
  );

  if (module_exists('colorbox')) {

    $form['track_da_files_colorbox'] = array(
      '#type' => 'fieldset',
      '#title' => t('Colorbox'),
    );

    $form['track_da_files_colorbox']['track_da_files_colorbox_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Track da files colorbox compatibility'),
      '#default_value' => $track_da_files_colorbox_enabled,
      '#description' => t('When enabled, in content interface select display "tdf : Colorbox image" for desired fields'),
    );

  }

  // Render the role overview.
  $form['track_da_files_roles']['role_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Roles'),
  );

  $form['track_da_files_roles']['role_settings']['track_da_files_roles'] = array(
    '#type' => 'radios',
    '#title' => t('Add tracking for roles'),
    '#options' => array(
      t('Add to the selected roles only'),
      t('Add to every role except the selected ones'),
    ),
    '#default_value' => variable_get('track_da_files_roles', 0),
  );

  $role_options = array_map('check_plain', user_roles());
  $form['track_da_files_roles']['role_settings']['track_da_files_specific_roles'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Roles'),
    '#default_value' => variable_get('track_da_files_specific_roles', array()),
    '#options' => $role_options,
    '#description' => t('If none of the roles are selected, all users will be tracked. If a user has any of the roles checked, that user will be tracked (or excluded, depending on the setting above).'),
  );

  return system_settings_form($form);
}


/**
 * Build variables which can be used to create reports table.
 */
function track_da_files_table_build() {

  // We check avalaible optional datas.
  $track_da_files_report_datas = variable_get('track_da_files_report_datas', array(
    'total_ips',
    'average_by_ip',
    'last_display',
    )
    );
  $track_da_files_report_files_datas = variable_get('track_da_files_report_files_datas', array(
    'timestamp',
    'filesize',
    'filemime',
    )
    );

  // We prepare main report table header.
  $header = array(
    array('data' => t('Filename')),
    array(
      'data' => t('Counter'),
      'field' => 'counter',
      'sort' => 'DESC',
    ),
  );

  // We put displays datas in main report table header.
  foreach ($track_da_files_report_datas as $data => $value) {
    if (!empty($value)) {
      if ($value == 'total_ips') {
        $header[] = array(
          'data' => t('Total ips'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'average_by_ip') {
        $header[] = array(
          'data' => t('Average by ip'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'last_display') {
        $header[] = array(
          'data' => t('Last display'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
    }
  }

  // We put files datas in main report table header.
  foreach ($track_da_files_report_files_datas as $data => $value) {
    if (!empty($value)) {
      if ($value == 'timestamp') {
        $header[] = array(
          'data' => t('Created'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'filesize') {
        $header[] = array(
          'data' => t('File size'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'filemime') {
        $header[] = array(
          'data' => t('File mime'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
    }
  }

  $header[] = array('data' => t('File datas'));

  // We prepare fields.
  $fields = array('pid', 'recid');
  foreach ($track_da_files_report_datas as $key => $value) {
    if ($value == 'average_by_ip') {
      $average_by_ip = $value;
    }
    elseif ($value == 'total_ips') {
      $total_ips = $value;
    }
    elseif ($value == 'last_display') {
      $last_display = $value;
    }
    else {
      $fields[] = $value;
    }
  }

  $fields2 = array('filename');
  foreach ($track_da_files_report_files_datas as $key => $value) {
    if ($value) {
      $fields2[] = $value;
    }
  }

  // We select the datas in database.
  $query = db_select('track_da_files', 't');
  $query->groupBy('pid');
  $query->addExpression('COUNT(recid)', 'counter');
  $query->addExpression('COUNT(DISTINCT(ip))', 'total_ips');
  $query->addExpression('MAX(time)', 'last_display');
  $query->addExpression('ROUND(COUNT(recid) / COUNT(DISTINCT(ip)))', 'average_by_ip');
  $query->join('track_da_files_paths', 'p', 't.pid = p.pid');
  $query->join('file_managed', 'f', 'p.fid = f.fid');
  $query
    ->extend('PagerDefault')
      ->limit(20)
    ->extend('TableSort')
    ->orderByHeader($header)
    ->fields('f', $fields2)
    ->fields('t', $fields)
    ->fields('p', array('fid', 'path'));
  $results = $query->execute();
  $rows = array();

  // We loop into the results to build table rows for the main report table.
  foreach ($results as $key => $record) {
    foreach ($record as $key2 => $row) {
      if ($key2 == 'filename') {
        $rows[$key][$key2] = l($row, file_create_url($record->path));
      }
      elseif (($key2 == 'pid') || ($key2 == 'path')) {
        unset($rows[$key][$key2]);
      }
      elseif (($key2 == 'last_display' && !empty($last_display)) || ($key2 == 'timestamp')) {
        $rows[$key][$key2] = format_date($row, 'short');
      }
      elseif ($key2 == 'filesize') {
        $rows[$key][$key2] = format_size($row);
      }
      elseif ((($key2 == 'filemime') || ($key2 == 'counter')) || (!empty($total_ips) && $key2 == 'total_ips')) {
        $rows[$key][$key2] = $row;
      }
      elseif ($key2 == 'fid') {
        $rows[$key][$key2] = l(t('View more'), 'admin/reports/track_da_files/file_report/' . $record->fid . '/' . $record->pid);
      }
      elseif ((!empty($average_by_ip)) && ($key2 == 'recid')) {
        $rows[$key][$key2] = $record->average_by_ip;
      }
    }
  }

  // We reorder rows to match table headers order.
  $rows_indexes_ordered = array(
    'filename',
    'counter',
    'total_ips',
    'recid',
    'last_display',
    'timestamp',
    'filesize',
    'filemime',
    'fid',
  );
  $rows_ordered = array();

  foreach ($rows as $row => $value) {
    foreach ($value as $key => $value2) {
      $keys[] = $key;
    }
  }

  $rows_final = array();
  $variables = array();

  foreach ($rows as $row) {
    foreach ($rows_indexes_ordered as $index) {
      if (in_array($index, $keys)) {
        $rows_ordered[$index] = $row[$index];
      }
    }
    $rows_final[] = $rows_ordered;
  }

  $variables = array(
    'header' => $header,
    'rows' => $rows_final,
  );
  return $variables;
}


/**
 * Build variables used to create file report.
 */
function track_da_files_table_build_file_report($fid, $pid) {

  $track_da_files_single_file_report_datas = variable_get('track_da_files_single_file_report_datas', array(
    'id',
    'referer',
    'browser',
    'browser_version',
    'browser_platform',
    'ip',
    'uid',
  )
  );
  $track_da_files_user_report_enabled = variable_get('track_da_files_user_report_enabled', 1);

  // We prepare table header for file specific report.
  $header = array(
    array('data' => t('Time')),
  );

  // Header datas.
  foreach ($track_da_files_single_file_report_datas as $data => $value) {
    if (!empty($value)) {
      if ($value == 'uid') {
        $header[] = array(
          'data' => t('Username'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'id') {
        $header[] = array(
          'data' => t('Related content'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'referer') {
        $header[] = array(
          'data' => t('Displayed from'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'browser') {
        $header[] = array(
          'data' => t('Browser'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'browser_version') {
        $header[] = array(
          'data' => t('Browser version'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'browser_platform') {
        $header[] = array(
          'data' => t('Platform'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'ip') {
        $header[] = array(
          'data' => t('Ip'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
      elseif ($value == 'uid') {
        $header[] = array(
          'data' => t('User name'),
          'field' => $value,
          'sort' => 'DESC',
        );
      }
    }
  }

  // We build an array with header values to make some verifications.
  foreach ($header as $key => $value) {
    foreach ($value as $value2) {
      $header_values[]  = $value2;
    }
  }

  if ($track_da_files_user_report_enabled) {
    $header[] = array('data' => t('User datas'));
  }

  // We prepare fields.
  $fields = array('time');

  if (isset($track_da_files_single_file_report_datas['id']) && $track_da_files_single_file_report_datas['id'] == '0') {
    unset($track_da_files_single_file_report_datas['id']);
  }

  foreach ($track_da_files_single_file_report_datas as $key => $value) {
    if (!empty($value)) {
      if ($value == 'id') {
        $fields[] = 'id';
        $fields[] = 'type';
      }
      else {
        $fields[] = $value;
      }
    }
  }
  if ($track_da_files_user_report_enabled && !in_array('uid', $header_values)) {
    $fields[] = 'uid';
  }
  $query = db_select('track_da_files', 't');
  $query
    ->extend('PagerDefault')
      ->limit(20)
    ->extend('TableSort')
    ->orderByHeader($header)
    ->fields('t', $fields)
    ->condition('pid', $pid);

  $results = $query->execute();
  $rows = array();

  // We loop into the results to build table rows for file report table.
  foreach ($results as $key => $record) {
    foreach ($record as $key2 => $row) {
      if ($key2 == 'time') {
        $rows[$key][$key2] = format_date($row, 'short');
      }
      elseif ($key2 == 'id') {
        if ($record->type == 'node') {
          $node = node_load($row);
          $rows[$key][$key2] = l($node->title, $record->type . '/' . $row) . '<br />(' . t('content') . ')';
        }
        elseif ($record->type == 'comment') {
          $comment = comment_load($row);
          $rows[$key][$key2] = l($comment->subject, $record->type . '/' . $row, array('fragment' => 'comment-' . $row)) . '<br />(' . t('comment') . ')';
        }
        else {
          $rows[$key][$key2] = '&nbsp;';
        }
      }
      elseif ($key2 == 'type') {
        unset ($rows[$key][$key2]);
      }
      elseif ($key2 == 'uid') {

        $uid = $row;
        $user = user_load($uid);

        if ($track_da_files_user_report_enabled) {
          if (in_array('uid', $header_values)) {
            if (!empty($user->name)) {
              $rows[$key][$key2] = theme('username', array('account' => $user));
            }
            else {
              $rows[$key][$key2] = t('Anonymous user');
            }
          }

          $rows[$key]['view_more'] = l(t('View more'), 'admin/reports/track_da_files/user_report/' . $uid);
        }
        else {
          $rows[$key][$key2] = theme('username', array('account' => $user));
        }

      }
      else {
        $rows[$key][$key2] = $row;
      }
    }
  }

  $variables = array(
    'header' => $header,
    'rows' => $rows,
  );

  return $variables;
}


/**
 * Build variables used to create table which shows datas for a specific user.
 */
function track_da_files_table_build_user_report($uid) {

  $track_da_files_user_report_enabled = variable_get('track_da_files_user_report_enabled', 1);
  $track_da_files_single_user_report_datas = variable_get('track_da_files_single_user_report_datas', array(
    'referer',
    'browser',
    'browser_version',
    'browser_platform',
    'ip',
  )
  );

  if ($track_da_files_user_report_enabled) {

    $header = array(
      array('data' => t('Filename')),
      array(
        'data' => t('time'),
        'field' => 'time',
        'sort' => 'DESC',
      ),
    );

    // Header datas.
    foreach ($track_da_files_single_user_report_datas as $data => $value) {
      if (!empty($value)) {
        if ($value == 'referer') {
          $header[] = array(
            'data' => t('Displayed from'),
            'field' => $value,
            'sort' => 'DESC',
          );
        }
        elseif ($value == 'browser') {
          $header[] = array(
            'data' => t('Browser'),
            'field' => $value,
            'sort' => 'DESC',
          );
        }
        elseif ($value == 'browser_version') {
          $header[] = array(
            'data' => t('Browser version'),
            'field' => $value,
            'sort' => 'DESC',
          );
        }
        elseif ($value == 'browser_platform') {
          $header[] = array(
            'data' => t('Platform'),
            'field' => $value,
            'sort' => 'DESC',
          );
        }
        elseif ($value == 'ip') {
          $header[] = array(
            'data' => t('Ip'),
            'field' => $value,
            'sort' => 'DESC',
          );
        }
      }
    }

    // We prepare fields.
    $fields = array('time');

    foreach ($track_da_files_single_user_report_datas as $key => $value) {
      if ($value) {
        $fields[] = $value;
      }
    }

    $query = db_select('track_da_files', 't');
    $query->join('track_da_files_paths', 'p', 't.pid = p.pid');
    $query
    ->extend('PagerDefault')
      ->limit(20)
    ->extend('TableSort')
    ->orderByHeader($header)
    ->fields('p', array('fid'))
    ->fields('t', $fields)
    ->condition('uid', $uid);
    $results = $query->execute();
    $rows = array();

    // We loop into results to build rows for user report table.
    foreach ($results as $key => $record) {
      foreach ($record as $key2 => $row) {
        if ($key2 == 'time') {
          $rows[$key][$key2] = format_date($row, 'short');
        }
        elseif ($key2 == 'fid') {

          $file = file_load($row);
          if (isset($file->filename)) {
            $rows[$key][$key2] = $file->filename;
          }
          else {
            $rows[$key][$key2] = t('No name');
          }
        }
        else {
          $rows[$key][$key2] = $row;
        }
      }
    }

    $variables = array(
      'header' => $header,
      'rows' => $rows,
    );
    return $variables;
  }
  else {
    return drupal_access_denied();
  }
}

/**
 * Builds a table which shows number of displays by file.
 */
function track_da_files_table($fid = NULL, $pid = NULL) {
  $output = '';
  $variables = '';
  // Load specific CSS.
  drupal_add_css(drupal_get_path('module', 'track_da_files') . '/includes/track_da_files.css');

  if (!empty($fid) && !empty($pid)) {
    $variables = track_da_files_table_build_file_report($fid, $pid);
    $file = file_load($fid);
    $filename = $file->filename;
    $output .= '<p>' . t('Datas for @filename', array('@filename' => $filename)) . '</p>';
    $form = drupal_get_form('track_da_files_table_action_form', $fid, $pid);
  }
  else {
    $variables = track_da_files_table_build();
    $form = drupal_get_form('track_da_files_table_action_form');
  }

  // Table.
  $output .= theme('table', $variables);
  // Pager.
  $output .= theme('pager', array(), 20, 0);
  // Clear datas and export to CSV buttons.
  $output .= drupal_render($form);

  return $output;
}

/**
 * Builds a table which shows datas for a specific user.
 */
function track_da_files_table_user_report($uid) {
  $output = '';
  // Load specific CSS.
  drupal_add_css(drupal_get_path('module', 'track_da_files') . '/includes/track_da_files.css');
  $variables = track_da_files_table_build_user_report($uid);
  $user = user_load($uid);

  if ($uid == '0') {
    $output .= '<p>' . t('Datas for anonymous users') . '</p>';
  }
  else {
    $output .= '<p>' . t('Datas for !username', array('!username' => theme('username', array('account' => $user)))) . '</p>';
  }

  // Table.
  $output .= theme('table', $variables);
  // Pager.
  $output .= theme('pager', array(), 20, 0);
  // Clear datas and export to CSV buttons.
  $form = drupal_get_form('track_da_files_user_table_action_form', $uid);
  $output .= drupal_render($form);
  return $output;
}


/**
 * Clear and export forms for main report and file report.
 */
function track_da_files_table_action_form($form, &$form_state) {
  if (isset($form_state['build_info']['args'][0]) && isset($form_state['build_info']['args'][1])) {
    // File report export.
    if (user_access('administer track da files')) {
      $form['track_da_files_table_action']['track_da_files_export_files_datas'] = array(
        '#type' => 'submit',
        '#value' => t('Export datas into CSV file'),
        '#submit' => array('track_da_files_export_files_datas_submit'),
      );
    }
    // File report clear.
    if (user_access('initialize tracked files displays datas')) {
      $form['track_da_files_table_action']['track_da_files_clear_file_datas'] = array(
        '#type' => 'fieldset',
        '#title' => t('Clear file datas'),
        '#description' => t('Datas will be removed for this file.'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      $form['track_da_files_table_action']['track_da_files_clear_file_datas']['clear'] = array(
        '#type' => 'submit',
        '#value' => t('Remove all datas for this file'),
        '#submit' => array('track_da_files_clear_file_datas_submit'),
      );
    }
  }
  else {
    // Main report export.
    if (user_access('administer track da files')) {
      $form['track_da_files_table_action']['track_da_files_export_files_datas'] = array(
        '#type' => 'submit',
        '#value' => t('Export datas into CSV file'),
        '#submit' => array('track_da_files_export_files_datas_submit'),
      );
    }
    // Main report clear.
    if (user_access('initialize tracked files displays datas')) {
      $form['track_da_files_table_action']['track_da_files_clear_file_datas'] = array(
        '#type' => 'fieldset',
        '#title' => t('Clear all files datas'),
        '#description' => t('Datas will be cleared for all files.'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      $form['track_da_files_table_action']['track_da_files_clear_file_datas']['clear'] = array(
        '#type' => 'submit',
        '#value' => t('Clear datas'),
        '#submit' => array('track_da_files_clear_file_datas_submit'),
      );
    }
  }
  return $form;
}


/**
 * Function to return form for user report clear and export buttons.
 */
function track_da_files_user_table_action_form($form, &$form_state) {
  if (isset($form_state['build_info']['args'][0])) {
    // User report export.
    if (user_access('administer track da files')) {
      $form['track_da_files_user_table_action']['track_da_files_export_users_datas'] = array(
        '#type' => 'submit',
        '#value' => t('Export datas into CSV file'),
        '#submit' => array('track_da_files_export_users_datas_submit'),
      );
    }
    // User report clear.
    if (user_access('initialize tracked files displays datas')) {
      $form['track_da_files_user_table_action']['track_da_files_clear_user_datas'] = array(
        '#type' => 'fieldset',
        '#title' => t('Clear user datas'),
        '#description' => t('Datas will be removed for this user.'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      $form['track_da_files_user_table_action']['track_da_files_clear_user_datas']['clear'] = array(
        '#type' => 'submit',
        '#value' => t('Remove all datas for this user'),
        '#submit' => array('track_da_files_clear_user_datas_submit'),
      );
    }
  }
  return $form;
}


/**
 * Submit callback: clear database.
 */
function track_da_files_clear_file_datas_submit($form, &$form_state) {
  if (isset($form_state['build_info']['args'][0]) && isset($form_state['build_info']['args'][1])) {
    $fid = $form_state['build_info']['args'][0];
    $pid = $form_state['build_info']['args'][1];

    $query = db_delete('track_da_files')
      ->condition('pid', $pid)
      ->execute();

    $query = db_delete('track_da_files_paths')
      ->condition('pid', $pid)
      ->execute();

    drupal_set_message(t('File datas cleared.'));
  }
  else {
    $query = db_delete('track_da_files')
      ->execute();

    $query = db_delete('track_da_files_paths')
      ->execute();

    drupal_set_message(t('File datas cleared.'));
  }
}


/**
 * Submit callback: clear user datas in database.
 */
function track_da_files_clear_user_datas_submit($form, &$form_state) {

  if (isset($form_state['build_info']['args'][0])) {

    $uid = $form_state['build_info']['args'][0];

    // We delete datas associated with specific user id.
    $query = db_delete('track_da_files')
      ->condition('uid', $uid)
      ->execute();

    drupal_set_message(t('User datas cleared.'));
  }

}


/**
 * Submit callback: export files datas.
 */
function track_da_files_export_files_datas_submit($form, &$form_state) {
  // Export datas for a specific user if arguments passed into form datas.
  if (isset($form_state['build_info']['args'][0]) && !isset($form_state['build_info']['args'][1])) {
    $uid = $form_state['build_info']['args'][0];
    return track_da_files_table_export_user_report($uid);
  }

  // Export datas for a specific file if arguments passed into form datas.
  elseif (isset($form_state['build_info']['args'][0]) && isset($form_state['build_info']['args'][1])) {

    $fid = $form_state['build_info']['args'][0];
    $pid = $form_state['build_info']['args'][1];

    return track_da_files_table_export_file_report($fid, $pid);
  }

  // Else export datas for all files.
  else {
    return track_da_files_table_export();
  }
}

/**
 * Submit callback: export files datas.
 */
function track_da_files_export_users_datas_submit($form, &$form_state) {

  // Export datas for a specific user if arguments passed into form datas.
  if (isset($form_state['build_info']['args'][0])) {
    $uid = $form_state['build_info']['args'][0];
    return track_da_files_table_export_user_report($uid);
  }
}

/**
 * General csv export function.
 */
function track_da_files_export_csv($variables, $fid = NULL, $pid = NULL) {

  $track_da_files_user_report_enabled = variable_get('track_da_files_user_report_enabled', 1);

  // Remove file datas links and strip HTML.
  if (isset($variables['rows'])) {
    foreach ($variables['rows'] as $key => $record) {
      foreach ($record as $key2 => $row) {
        $rows[$key][$key2] = strip_tags($row);
        if (($key2 == 'fid' && !$fid) || ($key2 == 'view_more')) {
          unset ($rows[$key][$key2]);
        }
      }
    }

    $variables['rows'] = $rows;
  }

  // Remove last column in main report and file report.
  array_pop($variables['header']);

  $site_name = variable_get('site_name');
  $site_name = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $site_name);
  drupal_add_http_header('Content-Type', 'text/csv; utf-8');

  if ($fid) {
    $file = file_load($fid);
    $filename = $file->filename;
    $path_parts = pathinfo($filename);
    $file_export_name = $path_parts['filename'] . '_' . $path_parts['extension'];
    drupal_add_http_header('Content-Disposition', 'attachment; filename=' .  $file_export_name . '-' . date('Ymd-his') . '-fid' . $fid . 'pid' . $pid);
  }
  else {
    drupal_add_http_header('Content-Disposition', 'attachment; filename=' . $site_name . '-tdf-' . date('Ymd-his'));
  }
  $output = '';
  $keys = array();
  foreach ($variables['header'] as $key => $value) {
    $keys[] = $value['data'];
  }
  if ($keys) {
    $output .= implode("\t", $keys) . "\n";
  }
  foreach ($variables['rows'] as $value) {
    $output .= implode("\t", $value) . "\n";
  }

  print $output;
  exit;
}


/**
 * User specific csv export function.
 */
function track_da_files_export_user_datas_csv($variables, $uid) {

  // We strip html for before exporting into csv file.
  foreach ($variables['rows'] as $key => $record) {
    foreach ($record as $key2 => $row) {
      $rows[$key][$key2] = strip_tags($row);
    }
  }

  $variables['rows'] = $rows;
  drupal_add_http_header('Content-Type', 'text/csv; utf-8');
  $user = user_load($uid);
  if (!empty($user->name)) {
    $username = check_plain($user->name);
    drupal_add_http_header('Content-Disposition', 'attachment; filename=' . $username . '-' . date('Ymd-his') . '-uid' . $uid);
  }
  else {
    drupal_add_http_header('Content-Disposition', 'attachment; filename=anonymous-users-' . date('Ymd-his') . '-uid' . $uid);
  }
  $output = '';
  $keys = array();
  foreach ($variables['header'] as $key => $value) {
    $keys[] = $value['data'];
  }
  if ($keys) {
    $output .= implode("\t", $keys) . "\n";
  }
  foreach ($variables['rows'] as $value) {
    $output .= implode("\t", $value) . "\n";
  }

  print $output;
  exit;
}


/**
 * Exports the report table in csv.
 */
function track_da_files_table_export() {
  $variables = track_da_files_table_build();
  return track_da_files_export_csv($variables);
}


/**
 * Exports the track da files table in csv for a specific file.
 */
function track_da_files_table_export_file_report($fid, $pid) {
  $variables = track_da_files_table_build_file_report($fid, $pid);
  return track_da_files_export_csv($variables, $fid, $pid);
}


/**
 * Exports the track da files table in csv for a specific user.
 */
function track_da_files_table_export_user_report($uid) {
  $variables = track_da_files_table_build_user_report($uid);
  return track_da_files_export_user_datas_csv($variables, $uid);
}
