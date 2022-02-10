<?php
class views_plugin_style_content_timeline extends views_plugin_style{
  /**
   * Extends the default options inherited by this plugin.
   *
   * @return array
   *   The list of options provided by this plugin.
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['general_options_scroll_speed'] = array('default' => 500);
    $options['general_options_easing'] = array('default' => 'easeOutSine');
    $options['general_options_close_item_on_transition'] = array('default' => FALSE);
    $options['general_options_hide_years'] = array('default' => FALSE);
    $options['general_options_start_item'] = array('default' => 'last');

    $options['global_styling_options_timeline_width'] = array('default' => 920);
    $options['global_styling_options_item_margin'] = array('default' => 20);
    $options['global_styling_options_item_height'] = array('default' => 360);
    $options['global_styling_options_read_more'] = array('default' => '.read_more');
    $options['global_styling_options_node_description_color'] = array('default' => '#1986ac');
    $options['global_styling_options_hide_timeline'] = array('default' => FALSE);
    $options['global_styling_options_line_style'] = array('default' => 'light');
    $options['global_styling_options_hide_navigation'] = array('default' => FALSE);
    $options['global_styling_options_nav_style'] = array('default' => 'light');
    $options['global_styling_options_shadow'] = array('default' => 'shadow_show');

    $options['timeline_options_granularity'] = array('default' => 'months');
    $options['timeline_options_hide_date_on_dots'] = array('default' => FALSE);
    $options['timeline_options_date_on_dot_hover'] = array('default' => FALSE);
    return $options;
  }
  /**
   * Extends the options form inherited by this plugin.
   *
   * @param array $form
   *   The form being generated.
   * @param array $form_state
   *   The state that the form has been posted in.
   *
   * FIX BUG AS http://cgit.drupalcode.org/juicebox/commit/?id=c658771 , https://www.drupal.org/node/2006830
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['general_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('General Options'),
    );
    $form['general_options_scroll_speed'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Transition speed (default: 500px)'),
      '#title' => t('Scroll Speed'),
      '#default_value' => $this->options['general_options_scroll_speed'],
      '#field_suffix' => 'px'
    );
    $form['general_options_easing'] = array(
      '#type' => 'select',
      '#title' => t('Easing'),
      '#options' => array(
        'easeInQuad' => 'easeInQuad',
        'easeOutQuad' => 'easeOutQuad',
        'easeInOutQuad' => 'easeInOutQuad',
        'easeInCubic' => 'easeInCubic',
        'easeOutCubic' => 'easeOutCubic',
        'easeInOutCubic' => 'easeInOutCubic',
        'easeInQuart' => 'easeInQuart',
        'easeOutQuart' => 'easeOutQuart',
        'easeInOutQuart' => 'easeInOutQuart',
        'easeInQuint' => 'easeInQuint',
        'easeOutQuint' => 'easeOutQuint',
        'easeInOutQuint' => 'easeInOutQuint',
        'easeInSine' => 'easeInSine',
        'easeOutSine' => 'easeOutSine',
        'easeInOutSine' => 'easeInOutSine',
        'easeInExpo' => 'easeInExpo',
        'easeOutExpo' => 'easeOutExpo',
        'easeInOutExpo' => 'easeInOutExpo',
        'easeInCirc' => 'easeInCirc',
        'easeOutCirc' => 'easeOutCirc',
        'easeInOutCirc' => 'easeInOutCirc',
        'easeInElastic' => 'easeInElastic',
        'easeOutElastic' => 'easeOutElastic',
        'easeOutElastic' => 'easeOutElastic',
        'easeInOutElastic' => 'easeInOutElastic',
        'easeInBack' => 'easeInBack',
        'easeOutBack' => 'easeOutBack',
        'easeInOutBack' => 'easeInOutBack',
        'easeInBounce' => 'easeInBounce',
        'easeOutBounce' => 'easeOutBounce',
        'easeInOutBounce' => 'easeInOutBounce',
      ),
      '#default_value' => $this->options['general_options_easing'],
      '#description' => t('Transition easing function (default: \'easeOutSine\')'),
    );
    $form['general_options_close_item_on_transition'] = array(
      '#type' => 'checkbox',
      '#title' => t('Close item on transition'),
      '#title_display' => 'after',
      '#default_value' => $this->options['general_options_close_item_on_transition'],
      '#description' => t('If this option is enabled, it closes the timeline item after the transition to the next element takes place (default: \'false\')'),
    );
    $form['general_options_hide_years'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide Years'),
      '#default_value' => $this->options['general_options_hide_years'],
      '#title_display' => 'after',
    );
    $form['general_options_start_item'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Use node id or "first" or "last" (without quotes) for starting item on page load (default: "last")'),
      '#title' => t('Start Item'),
      '#default_value' => $this->options['general_options_start_item'],
    );
    $form['global_styling_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Global Styling Options'),
    );
    $form['global_styling_options_timeline_width'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Width of the line element (default: 920px)'),
      '#title' => t('Timeline Width'),
      '#default_value' => $this->options['global_styling_options_timeline_width'],
      '#field_suffix' => 'px'
    );
    $form['global_styling_options_item_margin'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Space between 2 items. If negative itens overlap (default: 20px)'),
      '#title' => t('Item Margin'),
      '#default_value' => $this->options['global_styling_options_item_margin'],
      '#field_suffix' => 'px'
    );
    $form['global_styling_options_item_height'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Height of item and open item elements (default: 360px)'),
      '#title' => t('Item Height'),
      '#default_value' => $this->options['global_styling_options_item_height'],
      '#field_suffix' => 'px'
    );
    $form['global_styling_options_read_more'] = array(
      '#type' => 'select',
      '#title' => t('Read More'),
      '#options' => array(
        'none' => 'None',
        '.read_more' => 'Button',
        '.item' => 'Whole Item',
      ),
      '#default_value' => $this->options['global_styling_options_read_more'],
      '#description' => t('Read more button (\'Button\': adds read more buttton, \'Whole Item\': makes the whole item clickable, \'None\': items don\'t open)'),
    );
    $form['global_styling_options_node_description_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Color of description bar that appears when you hover date on timeline'),
      '#title' => t('Node description color'),
      '#default_value' => $this->options['global_styling_options_node_description_color'],
    );
    $form['global_styling_options_hide_timeline'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide Timeline'),
      '#title_display' => 'after',
      '#default_value' => $this->options['global_styling_options_hide_timeline'],
    );
    $form['global_styling_options_line_style'] = array(
      '#type' => 'select',
      '#title' => t('Line Style'),
      '#options' => array(
        'flatLine' => 'Flat',
        'light' => 'Light',
        'darkLine' => 'Dark',
      ),
      '#default_value' => $this->options['global_styling_options_line_style'],
      '#description' => t('Color scheme of timeline element (default: \'Light\')'),
    );
    $form['global_styling_options_hide_navigation'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide Navigation'),
      '#title_display' => 'after',
      '#default_value' => $this->options['global_styling_options_hide_navigation'],
    );
    $form['global_styling_options_nav_style'] = array(
      '#type' => 'select',
      '#title' => t('Nav Style'),
      '#options' => array(
        'flatNav' => 'Flat',
        'light' => 'Light',
        'darkNav' => 'Dark',
      ),
      '#default_value' => $this->options['global_styling_options_nav_style'],
      '#description' => t('Color scheme of nav element (default: \'Light\')'),
    );
    $form['global_styling_options_shadow'] = array(
      '#type' => 'select',
      '#title' => t('Shadow'),
      '#options' => array(
        'shadow_show' => 'Show',
        'shadow_on_hover' => 'Show On Hover',
        'shadow_hide' => 'Hide',
      ),
      '#default_value' => $this->options['global_styling_options_shadow'],
      '#description' => t('Shadow under elements (default: \'Show\')'),
    );
    $form['timeline_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Timeline Options'),
    );
    $form['timeline_options_granularity'] = array(
      '#type' => 'select',
      '#title' => t('Granularity'),
      '#options' => array(
        'months' => 'Months',
        'years' => 'Years',
      ),
      '#default_value' => $this->options['timeline_options_granularity'],
      '#description' => t('Define granularity of timeline (default: \'Months\')'),
    );
    $form['timeline_options_hide_date_on_dots'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide date on dots'),
      '#title_display' => 'after',
      '#default_value' => $this->options['timeline_options_hide_date_on_dots'],
    );
    $form['timeline_options_date_on_dot_hover'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show date on dot hover'),
      '#title_display' => 'after',
      '#default_value' => $this->options['timeline_options_date_on_dot_hover'],
    );
  }
}