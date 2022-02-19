<?php

class ViewsBsIsotopePluginStyle extends views_plugin_style {

  /**
   * Definition.
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['use_isotope_plugin_style'] = array('default' => 1);
    $options['col_lg'] = array('default' => 4);
    $options['col_md'] = array('default' => 4);
    $options['col_sm'] = array('default' => 3);
    $options['col_xs'] = array('default' => 1);
    $options['filter_terms'] = array('default' => '');
    $options['no_item_padding'] = array('default' => 0);
    $options['use_infinitescroll'] = array('default' => 0);
    $options['item_style'] = array('default' => '');

    return $options;
  }

  /**
   * Form.
   */
  function options_form(&$form, &$form_state) {


    $field_arr = array('' => '- ' . t('Select') . '-');
    $fields = $this->display->handler->get_handlers('field');
    if (!empty($fields)) {
      foreach ($fields as $field => $handler) {

        if (!empty($handler->field_info['type']) && ($handler->field_info['type'] == 'taxonomy_term_reference')) {
          if ($label = $handler->label()) {
            $field_arr[$field] = $label;
          } else {
            $field_arr[$field] = $handler->ui_name();
          }
        }
      }
    }



    $form['#pre_render'][] = 'views_ui_pre_render_add_fieldset_markup';
    $options = array(1, 2, 3, 4, 6, 12);

    $form['filter'] = array(
        '#type' => 'fieldset',
        '#title' => t('Filter settings'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );

    $form['filter_terms'] = array(
        '#type' => 'select',
        '#title' => t('Select fitler by'),
        '#default_value' => $this->options['filter_terms'],
        '#options' => $field_arr,
        '#fieldset' => 'filter',
    );


    $form['columns'] = array(
        '#type' => 'fieldset',
        '#title' => t('Columns settings'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );

    $form['col_lg'] = array(
        '#type' => 'select',
        '#title' => t('Large Desktops(≥1200px)'),
        '#default_value' => $this->options['col_lg'],
        '#options' => array_combine($options, $options),
        '#required' => TRUE,
        '#fieldset' => 'columns',
    );
    $form['col_md'] = array(
        '#type' => 'select',
        '#title' => t('Medium Desktops(≥992px)'),
        '#default_value' => $this->options['col_md'],
        '#options' => array_combine($options, $options),
        '#required' => TRUE,
        '#fieldset' => 'columns',
    );
    $form['col_sm'] = array(
        '#type' => 'select',
        '#title' => t('Small Tablets(≥768px)'),
        '#default_value' => $this->options['col_sm'],
        '#options' => array_combine($options, $options),
        '#required' => TRUE,
        '#fieldset' => 'columns',
    );
    $form['col_xs'] = array(
        '#type' => 'select',
        '#title' => t('Mobiles(<768px)'),
        '#default_value' => $this->options['col_xs'],
        '#options' => array_combine($options, $options),
        '#required' => TRUE,
        '#fieldset' => 'columns',
    );


    $form['more'] = array(
        '#type' => 'fieldset',
        '#title' => t('More settings'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $style_options = array(
        '' => t('-None-'),
        'views-bs-view-first' => t('Style #1'),
        'views-bs-view-second' => t('Style #2'),
        'views-bs-view-third' => t('Style #3'),
        'views-bs-view-fourth' => t('Style #4'),
        'views-bs-view-fifth' => t('Style #5'),
        'views-bs-view-sixth' => t('Style #6'),
        'views-bs-view-seventh' => t('Style #7'),
        'views-bs-view-eighth' => t('Style #8'),
        'views-bs-view-ninth' => t('Style #9'),
            // 'views-bs-view-tenth' => t('Style #10'),
    );
    $form['item_style'] = array(
        '#title' => t('Item hover effect style '),
        '#type' => 'select',
        '#options' => $style_options,
        '#default_value' => $this->options['item_style'],
        '#fieldset' => 'more',
    );
    $form['no_item_padding'] = array(
        '#type' => 'checkbox',
        '#title' => t('Remove item css padding'),
        '#default_value' => $this->options['no_item_padding'],
        '#fieldset' => 'more',
    );
    $form['use_infinitescroll'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use auto pager'),
        '#default_value' => $this->options['use_infinitescroll'],
        '#description' => t('Auto loading more contents.'),
        '#fieldset' => 'more',
    );
  }

}
