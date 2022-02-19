<?php

class ViewsBsTimelinePluginStyle extends views_plugin_style {

  function option_definition() {
    $options = parent::option_definition();

    $options['type'] = array('default' => 'ul');
    $options['class'] = array('default' => 'timeline-wrap');
    $options['wrapper_class'] = array('default' => 'timeline');
    $options['use_infinitescroll'] = array('default' => 0);

    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);


    $form['pager'] = array(
        '#type' => 'fieldset',
        '#title' => t('Pager settings'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
    );

    $form['use_infinitescroll'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use auto pager'),
        '#default_value' => $this->options['use_infinitescroll'],
        '#description' => t('Auto loading more contents.'),
        '#fieldset' => 'pager',
    );
  }

}
