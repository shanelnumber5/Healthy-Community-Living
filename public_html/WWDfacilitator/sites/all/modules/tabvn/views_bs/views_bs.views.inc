<?php

/**
 * Implements hook_views_plugins().
 */
function views_bs_views_plugins() {
  $module_path = drupal_get_path('module', 'views_bs');

  return array(
      'style' => array(
          'views_bs_isotope_plugin_style' => array(
              'title' => t('Views Isotope'),
              'help' => t('Views isotope Style'),
              'path' => $module_path . '/plugins/isotope',
              'handler' => 'ViewsBsIsotopePluginStyle',
              'parent' => 'default',
              'theme' => 'views_bs_isotope_plugin_style',
              'theme path' => $module_path . '/templates/isotope',
              'theme file' => 'theme.inc',
              'uses row plugin' => TRUE,
              'uses grouping' => TRUE,
              'uses options' => TRUE,
              'type' => 'normal',
          ),
          'views_bs_timeline_plugin_style' => array(
              'title' => t('Vertial timeline'),
              'help' => t('Displays entries as vertial timeline.'),
              'path' => $module_path . '/plugins/timeline',
              'handler' => 'ViewsBsTimelinePluginStyle',
              'theme path' => $module_path . '/templates/timeline',
              'parent' => 'default',
              'theme' => 'views_bs_timeline_plugin_style',
              'theme file' => 'theme.inc',
              'uses row plugin' => TRUE,
              'uses row class' => TRUE,
              'uses options' => TRUE,
              'type' => 'normal',
          ),
      ),
  );
}

function views_bs_views_pre_render(&$view) {

  if (!empty($view->style_plugin) && (is_a($view->style_plugin, 'ViewsBsIsotopePluginStyle'))) {

    if (!empty($view->style_options['filter_terms'])) {
      $id = 'area';
      if (!empty($view->header)) {
        $id = 'area_' . count($view->header);
      }
      $header_filter_content = theme('views_bs_isotope_filter', array('view' => $view));
      $view->attachment_before = $header_filter_content;
    }
  }
}

