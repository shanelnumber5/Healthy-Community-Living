<?php

/**
 * @file
 * This file contains the specialized field formatters.
 */

/**
 * Returns HTML for a link to a file.
 *
 * @param array $variables
 *   An associative array containing:
 *   - file: A file object to which the link will be created.
 *   - icon_directory: (optional) A path to a directory of icons to be used for
 *     files. Defaults to the value of the "file_icon_directory" variable.
 *
 * @ingroup themeable
 */
function theme_track_da_files_file_link($variables) {

  $track_da_files_file_field_links_show_enabled = variable_get('track_da_files_file_field_links_show_enabled', 1);
  // Load related CSS.
  if ($track_da_files_file_field_links_show_enabled) {
    drupal_add_css(drupal_get_path('module', 'track_da_files') . '/includes/track_da_files.css');
  }
  $output = '';
  $file = $variables['file'];
  $icon_directory = $variables['icon_directory'];
  $url = track_da_files_create_url($file->uri);
  $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

  $options = array(
    'attributes' => array(
      'type' => $file->filemime . '; length=' . $file->filesize,
    ),
  );

  // Use the description as the link text if available.
  if (empty($file->description)) {
    $link_text = $file->filename;
  }
  else {
    $link_text = $file->description;
    $options['attributes']['title'] = check_plain($file->filename);
  }

  $options['query']['file'] = '1';

  if (isset($file->type)) {
    $options['query']['type'] = $file->type;
  }

  if (isset($file->id)) {
    $options['query']['id'] = $file->id;
  }

  if (isset($file->force_download)) {
    $options['query']['force'] = $file->force_download;
  }


  $output = '<span class="file">' . $icon . ' ' . l($link_text, $url, $options) . '</span>';
  if ((isset($file->displays)) && ($file->displays > 0 && isset($track_da_files_file_field_links_show_enabled))) {
    $output .= '<span class="file-displayed-counter">' . format_plural($file->displays, 'displayed 1 time', 'displayed @count times') . '</span>';
  }
  return $output;
}

/**
 * Returns plain URL to a file.
 *
 * @param array $variables
 *   An associative array containing:
 *   - file: A file object to which the link will be created.
 *
 * @ingroup themeable
 */
function theme_track_da_files_url_plain($variables) {

  $file = $variables['file'];
  $url = track_da_files_create_url($file->uri);

  $options = array(
    'attributes' => array(
      'type' => $file->filemime . '; length=' . $file->filesize,
    ),
  );

  $options['query']['file'] = '1';

  if (isset($file->type)) {
    $options['query']['type'] = $file->type;
  }

  if (isset($file->id)) {
    $options['query']['id'] = $file->id;
  }

  return url($url, $options);
}


/**
 * Returns HTML for a file attachments table.
 *
 * @param array $variables
 *   An associative array containing:
 *   - items: An array of file attachments.
 *
 * @ingroup themeable
 */
function theme_track_da_files_file_formatter_table($variables) {

  $header = array(t('Attachment'), t('Size'));
  $rows = array();
  foreach ($variables['items'] as $delta => $item) {

    $rows[] = array(
      theme('track_da_files_file_link', array('file' => (object) $item)),
      format_size($item['filesize']),
    );
  }

  return empty($rows) ? '' : theme('table', array('header' => $header, 'rows' => $rows));
}
