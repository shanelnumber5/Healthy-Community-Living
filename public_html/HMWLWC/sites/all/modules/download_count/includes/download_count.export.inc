<?php

/**
 * @file
 * Page callback file for the download_count module export feature.
 */

/**
 * Download_count export form.
 */
function download_count_export_form($form, $form_state, $dc_entry = NULL) {
  drupal_add_js(drupal_get_path('module', 'download_count') . '/js/download_count.js');
  if ($dc_entry == 'all') {
    drupal_set_title(t('Download Count Export CSV - All Files'));
  }
  else {
    drupal_set_title(t('Download Count Export CSV - @filename from @type @id', array('@filename' => $dc_entry->filename, '@type' => $dc_entry->type, '@id' => $dc_entry->id)));
  }

  $form['download_count_export_note'] = array(
    '#prefix' => '<div id="download-count-export-note">',
    '#suffix' => '</div>',
    '#markup' => l(t('&#0171; Back to summary'), 'admin/reports/download-count', array('html' => TRUE))
      . '<br /><br />'
      . t('The following data will be exported:')
      . '<ul>'
      . '<li>' . t('Download count id')
      . '<li>' . t('File id')
      . '<li>' . t('File name')
      . '<li>' . t('File size')
      . '<li>' . t('Entity type')
      . '<li>' . t('Entity id')
      . '<li>' . t('Downloading user id')
      . '<li>' . t('Downloading username')
      . '<li>' . t('Downloading user ip address')
      . '<li>' . t('HTTP referrer')
      . '<li>' . t('Date - time (YYYY-MM-DD  HH:MM:SS)')
      . '</ul>',
  );
  $form['download_count_export_range'] = array(
    '#type' => 'radios',
    '#title' => t('Export Range'),
    '#options' => array(
      t('export all data'),
      t('export data for a specified date range'),
    ),
    '#default_value' => variable_get('download_count_export_range', 0),
  );
  $form['download_count_export_date_range_from'] = array(
    '#type' => 'date',
    '#title' => t('Export Range From Date'),
    '#description' => t('This field will be ignored if the Export Range \'export all data\' option is selected above.'),
  );
  $form['download_count_export_date_range_to'] = array(
    '#type' => 'date',
    '#title' => t('Export Range To Date'),
    '#description' => t('This field will be ignored if the Export Range \'export all data\' option is selected above.'),
  );
  $form['download_count_file_info'] = array(
    '#type' => 'value',
    '#value' => $dc_entry,
  );
  $form['download_count_export_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Export'),
  );
  $form['download_count_export_cancel'] = array(
    '#value' => '<a href="javascript:history.back(-1)">' . t('Cancel') . '</a>',
  );

  $form_state['#redirect'] = TRUE;
  return $form;
}

/**
 * Implements hook_submit().
 */
function download_count_export_form_submit($form, &$form_state) {
  variable_set('download_count_export_range', $form_state['values']['download_count_export_range']);
  $filename = 'download_count_export_' . ($form_state['values']['download_count_file_info'] == 'all' ? 'all_files' : check_plain($form_state['values']['download_count_file_info']->filename)) . '_' . date('Y-m-d') . '.csv';
  $range = $form_state['values']['download_count_export_range'];
  $file_info =  $form_state['values']['download_count_file_info'];
  $start = $form_state['values']['download_count_export_date_range_from'];
  $end = $form_state['values']['download_count_export_date_range_to'];

  $result = download_count_export_data($filename, $range, $file_info, $start, $end);
  drupal_set_message($filename . " has been successfully exported.", 'status');
  return;
}

function download_count_export_data($filename, $range, $file_info, $start, $end) {
  ob_end_clean();
  drupal_add_http_header('Content-Disposition', 'attachment; filename="' . $filename . '"');
  drupal_add_http_header('Content-Type', 'application/csv');
  drupal_send_headers();

  $query = db_select('download_count', 'dc')
    ->fields('dc', array('dcid', 'fid', 'type', 'id', 'uid', 'ip_address', 'referrer', 'timestamp'))
    ->fields('f', array('filename', 'filesize', 'uri'))
    ->fields('u', array('name'));
  $query->join('file_managed', 'f', 'dc.fid = f.fid');
  $query->join('users', 'u', 'dc.uid = u.uid');
  if ($file_info != 'all') {
    $query->condition('dc.type', $file_info->type, '=');
    $query->condition('dc.id', $file_info->id, '=');
    $query->condition('dc.fid', $file_info->fid, '=');
  }
  if ($range > 0) {
    $from = strtotime($start['year'] . '-' . $start['month'] . '-' . $start['day']);
    $to = strtotime($end['year'] . '-' . $end['month'] . '-' . $end['day']);
    if ($from == $to) {
      $to += 86400;
    }
    $query->condition('dc.timestamp', $from, '>=');
    $query->condition('dc.timestamp', $to, '<=');
  }

  $result = $query->execute();
  $column_names = '"Download count id","File id","File name","File URI","File size","Entity type","Entity id","Downloading user id","Downloading username","Downloading user ip address","HTTP referrer","Date time"' . "\n";
  print $column_names;
  foreach ($result as $record) {
    $row  = '"' . $record->dcid . '"' . ',';
    $row .= '"' . $record->fid . '"' . ',';
    $row .= '"' . $record->filename . '"' . ',';
    $row .= '"' . $record->uri . '"' . ',';
    $row .= '"' . $record->filesize . '"' . ',';
    $row .= '"' . $record->type . '"' . ',';
    $row .= '"' . $record->id . '"' . ',';
    $row .= '"' . $record->uid . '"' . ',';
    $row .= '"' . $record->name . '"' . ',';
    $row .= '"' . $record->ip_address . '"' . ',';
    $row .= '"' . $record->referrer . '"' . ',';
    $row .= '"' . date('Y-m-d H:i:s', $record->timestamp) . '"';
    $row .= "\n";
    print $row;
  }
  exit;
}