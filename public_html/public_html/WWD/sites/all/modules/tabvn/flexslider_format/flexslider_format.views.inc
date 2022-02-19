<?php

/**
 * Implements hook_views_plugins().
 */
function flexslider_format_views_plugins() {
  $module_path = drupal_get_path('module', 'flexslider_format');

  return array(
      'style' => array(
          'flexslider_format_plugin_style' => array(
              'title' => t('Flexslider format'),
              'help' => t('Views Flexslider'),
              'path' => $module_path . '/plugins/flexslider',
              'handler' => 'FlexsliderFormatPluginStyle',
              'parent' => 'default',
              'theme' => 'flexslider_format_plugin_style',
              'theme path' => $module_path . '/templates/flexslider',
              'theme file' => 'theme.inc',
              'uses row plugin' => TRUE,
              'uses grouping' => TRUE,
              'uses options' => TRUE,
              'type' => 'normal',
          ),
      ),
  );
}
