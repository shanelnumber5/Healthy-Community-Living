<?php

/**
 * Implementation of template preprocess for the view.
 */
function template_preprocess_flexslider_format_plugin_style(&$vars) {

  $module_path = drupal_get_path('module', 'flexslider_format');
  $view = $vars['view'];
  $options = $vars['view']->style_options;
  $element_id = 'views-flexslider-format-' . $view->name . '-' . $view->style_plugin->display->id;
  static $instances = array();
  if (isset($instances[$element_id])) {
    $instances[$element_id] ++;
    $element_id .= "_" . $instances[$element_id];
  } else {
    $instances[$element_id] = 1;
  }
  $options['animationLoop'] = $options['animationLoop'] ? TRUE : FALSE;
  $options['slideshow'] = $options['slideshow'] ? TRUE : FALSE;
  $options['move'] = $options['move'] ? TRUE : FALSE;
  $options['controlNav'] = $options['controlNav'] ? TRUE : FALSE;
  $options['directionNav'] = $options['directionNav'] ? TRUE : FALSE;

  drupal_add_js(array(
      'views_flexslider_format' => array(
          $element_id => array(
              'id' => $element_id,
              'flexslider_settings' => $options,
          ),
      ),
          ), 'setting');


  drupal_add_css($module_path . '/css/flexslider.css');
  drupal_add_js($module_path . '/js/views_flexslider_format.js');

  $css = '';
  if (!empty($options['itemMargin'])) {
    $margin = $options['itemMargin'];
    $css .= "#$element_id .slides > li {margin-right: " . $margin . "px;}";
  }
  drupal_add_css($css, array('type' => 'inline'));

  $vars['flexslider_id'] = $element_id;
}
