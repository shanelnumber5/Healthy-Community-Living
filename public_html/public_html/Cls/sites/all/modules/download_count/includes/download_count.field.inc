<?php
/**
 * @file
 * Implement a file field formatter that includes download count.
 */

/**
 * Implements hook_field_formatter_info().
 */
function download_count_field_formatter_info() {
  $formatters = array(
    'download_count_count' => array(
      'label' => t('Generic file with download count'),
      'field types' => array('file'),
    ),
  );

  return $formatters;
}

/**
 * Implements hook_field_formatter_view().
 */
function download_count_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = array();
  $entity_info = entity_get_info($entity_type);
  $access = user_access('view download counts');

  foreach ($items as $delta => $item) {
    if ($access) {
      $item['downloads'] = db_query("SELECT COUNT(fid) from {download_count} where fid = :fid AND type = :type AND id = :id", array(':fid' => $item['fid'], ':type' => $entity_type, ':id' => $entity->$entity_info['entity keys']['id']))->fetchField();
    }
    $element[$delta] = array(
      '#theme' => !$access ? 'file_link' : 'download_count_file_field_formatter',
      '#file' => (object) $item,
    );
  }

  return $element;
}

/**
 * Theme function for file fields with download counts.
 * Mostly copied from the core file module.
 */
function theme_download_count_file_field_formatter($variables) {
  $file = $variables['file'];

  $url = file_create_url($file->uri);
  $icon = theme('file_icon', array('file' => $file));

  $options = array(
    'attributes' => array(
      'type' => $file->filemime . '; length=' . $file->filesize,
    ),
  );

  if (empty($file->description)) {
    $link_text = $file->filename;
  }
  else {
    $link_text = $file->description;
    $options['attributes']['title'] = check_plain($file->filename);
  }

  $output = '<span class="file">' . $icon . ' ' . l($link_text, $url, $options) . '</span><span class="download-count"> &mdash; ';
  if (isset($file->downloads) && $file->downloads > 0) {
    $output .= format_plural($file->downloads, 'Downloaded 1 time', 'Downloaded @count times');
  }
  else {
    $output .= t('Never downloaded');
  }
  $output .= '</span>';

  return $output;
}
