<?php
/**
 * Implement hook_views_plugins()
 */
function content_timeline_views_plugins() {
  return array(
    'style' => array(
      'content_timeline' => array(
        'title' => t('Content Timeline'),
        'help' => t('display content on a timeline'),
        'handler' => 'views_plugin_style_content_timeline',
        'path' => drupal_get_path('module', 'content_timeline') . '/views',
        'help' => t('display content on a timeline'),
        'theme' => 'views_view__content_timeline',
        'theme path' => drupal_get_path('module', 'content_timeline') . '/views',
        'uses options' => TRUE,
        'type' => 'normal',
        'even empty' => TRUE,
        'uses row plugin' => TRUE,
        'uses grouping' => FALSE,
      ),
    ),
    'row' => array(
      'content_timeline' => array(
        'title' => t('Content Timeline'),
        'help' => t('display content on a timeline'),
        'handler' => 'views_plugin_row_content_timeline',
        'path' => drupal_get_path('module', 'content_timeline') . '/views',
        'theme' => 'views_view_row_content_timeline',
        'theme path' => drupal_get_path('module', 'content_timeline') . '/views',
        'uses fields' => TRUE,
        'uses options' => TRUE,
        'type' => 'normal',
      )
    ),
  );
}