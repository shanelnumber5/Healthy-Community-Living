<?php

function template_preprocess_views_bs_timeline_plugin_style(&$vars) {

  $view = $vars['view'];
  $element_id = $view->name . '-' . $view->style_plugin->display->id;


  static $instances = array();
  if (isset($instances[$element_id])) {
    $instances[$element_id] ++;
    $element_id .= "_" . $instances[$element_id];
  } else {
    $instances[$element_id] = 1;
  }
  $vars['element_id'] = $element_id;

  $use_infinitescroll = !empty($vars['view']->style_options['use_infinitescroll']) ? $vars['view']->style_options['use_infinitescroll'] : 0;
  drupal_add_js(array(
      'viewsBs' => array(
          'timeline' => array(
              $vars['id'] => array(
                  'id' => $element_id,
                  'name' => $vars['view']->name,
                  'view_name' => $vars['view']->name,
                  'display' => $vars['view']->current_display,
                  'use_infinitescroll' => $use_infinitescroll,
                  'finishmsg' => t('All items has been loaded'),
                  'loadingmsg' => t('Loading'),
              ),
          ),
      ),
          ), 'setting');

  $module_path = drupal_get_path('module', 'views_bs');
  drupal_add_css($module_path . '/css/timeline.css');



  if ($use_infinitescroll) {
    drupal_add_js($module_path . '/js/autopager.js');
  }
  drupal_add_js($module_path . '/js/views_bs_timeline.js');

  $handler = $vars['view']->style_plugin;

  $class = explode(' ', $handler->options['class']);
  $class = array_map('views_clean_css_identifier', $class);

  $wrapper_class = explode(' ', $handler->options['wrapper_class']);
  $wrapper_class = array_map('views_clean_css_identifier', $wrapper_class);

  $vars['class'] = implode(' ', $class);
  $vars['wrapper_class'] = implode(' ', $wrapper_class);
  $vars['wrapper_prefix'] = '';
  $vars['wrapper_suffix'] = '';
  $vars['list_type_prefix'] = '<' . $handler->options['type'] . '>';
  $vars['list_type_suffix'] = '</' . $handler->options['type'] . '>';
  if ($vars['wrapper_class']) {
    $vars['wrapper_prefix'] = '<div class="' . $vars['wrapper_class'] . '">';
    $vars['wrapper_suffix'] = '</div>';
  }

  if ($vars['class']) {
    $vars['list_type_prefix'] = '<' . $handler->options['type'] . ' class="' . $vars['class'] . '">';
  }
  template_preprocess_views_view_unformatted($vars);
}
