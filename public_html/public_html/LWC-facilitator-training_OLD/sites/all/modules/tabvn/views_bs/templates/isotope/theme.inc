<?php

/**
 * Implementation of template preprocess for the view.
 */
function template_preprocess_views_bs_isotope_plugin_style(&$vars) {

  $view = $vars['view'];
  $col_lg = !empty($vars['view']->style_options['col_lg']) ? $vars['view']->style_options['col_lg'] : 4;
  $col_md = !empty($vars['view']->style_options['col_md']) ? $vars['view']->style_options['col_md'] : 4;
  $col_sm = !empty($vars['view']->style_options['col_sm']) ? $vars['view']->style_options['col_md'] : 3;
  $col_xs = !empty($vars['view']->style_options['col_xs']) ? $vars['view']->style_options['col_xs'] : 1;
  $use_infinitescroll = !empty($vars['view']->style_options['use_infinitescroll']) ? $vars['view']->style_options['use_infinitescroll'] : 0;
  $vars['col_lg'] = 12 / $col_lg;
  $vars['col_md'] = 12 / $col_md;
  $vars['col_sm'] = 12 / $col_sm;
  $vars['col_xs'] = 12 / $col_xs;

  $vars['columns_css'] = 'col-xs' . $vars['col_xs'] . ' col-md-' . $vars['col_md'] . ' col-sm-' . $vars['col_sm'] . ' col-lg-' . $vars['col_lg'];

  $element_id = $view->name . '-' . $view->style_plugin->display->id;


  static $instances = array();
  if (isset($instances[$element_id])) {
    $instances[$element_id] ++;
    $element_id .= "_" . $instances[$element_id];
  } else {
    $instances[$element_id] = 1;
  }
  $vars['element_id'] = $element_id;

  drupal_add_js(array(
      'viewsBs' => array(
          'isotope' => array(
              $element_id => array(
                  'id' => $element_id,
                  'name' => $vars['view']->name,
                  'view_name' => $vars['view']->name,
                  'display' => $vars['view']->current_display,
                  'use_infinitescroll' => $use_infinitescroll,
                  'finishmsg' => t('All items has been loaded'),
                  'loadingmsg' => t('Loading'),
              ),
          ),
          'finishmsg' => t('All items has been loaded'),
          'loadingmsg' => t('Loading'),
      ),
          ), 'setting');
  $module_path = drupal_get_path('module', 'views_bs');
  drupal_add_js($module_path . '/js/modernizr.custom.js');
  drupal_add_js($module_path . '/js/jquery.isotope.min.js');
  drupal_add_js($module_path . '/js/imagesloaded.js');




  if ($use_infinitescroll) {
    drupal_add_js($module_path . '/js/autopager.js'); 
    
  }
  drupal_add_js($module_path . '/js/views_bs_isotope.js');
  drupal_add_library('system', 'jquery.bbq');
  drupal_add_css($module_path . '/css/grid.css');
  drupal_add_css($module_path . '/css/hover_effects.css');
  drupal_add_css($module_path . '/css/views_bs_isotope.css');

  $custom_class = '';
  if (!empty($vars['view']->style_options['no_item_padding']) && $vars['view']->style_options['no_item_padding']) {
    $custom_class .= 'no-item-padding';
  }
  $vars['custom_class'] = $custom_class;



  $terms_class = array();

  if (!empty($view->result)) {
    foreach ($view->result as $key => $value) {
      $filter_term = 'field_' . $vars['options']['filter_terms'];

      if (!empty($value->{$filter_term})) {
        $filter_value = $value->{$filter_term};
        if (!empty($filter_value)) {
          $css_name = '';
          foreach ($filter_value as $val) {
            $css_name .= ' ' . drupal_html_class($val['raw']['taxonomy_term']->name);
          }
          $terms_class[$key] = $css_name;
        }
      }
    }
  }

  $vars['custom_terms_class'] = $terms_class;
  $terms_arr = array();
  $field_filter_name = !empty($vars['view']->style_options['filter_terms']) ? $vars['view']->style_options['filter_terms'] : FALSE;
  if ($field_filter_name && !empty($vars['view']->field[$field_filter_name]->field_info['settings']['allowed_values'][0]['vocabulary'])) {

    $vob_name = $vars['view']->field[$field_filter_name]->field_info['settings']['allowed_values'][0]['vocabulary'];

    $vocabulary = taxonomy_vocabulary_machine_name_load($vob_name);

    $terms = taxonomy_get_tree($vocabulary->vid);
    if (!empty($terms)) {
      $terms_arr = $terms;
    }
  }

  $vars['terms_filter_arr'] = $terms_arr;
  $views_bs_item_style = !empty($vars['view']->style_options['item_style']) ? $vars['view']->style_options['item_style'] : '';
  if ($views_bs_item_style) {
    $views_bs_item_style = 'views-bs-view ' . $views_bs_item_style;
  } else {
    $views_bs_item_style = 'views-bs-default-style';
  }
  $vars['views_bs_item_style'] = $views_bs_item_style;
}
