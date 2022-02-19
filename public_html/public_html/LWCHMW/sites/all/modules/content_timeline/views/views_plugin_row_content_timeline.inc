<?php
class views_plugin_row_content_timeline extends views_plugin_row{
  /**
   * Extends the default options inherited by this plugin.
   *
   * @return array
   *   The list of options provided by this plugin.
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['button_styling_options_button_type'] = array('default' => 'sharp');
    $options['button_styling_options_read_more_button_text'] = array('default' => 'Read more');
    $options['button_styling_options_close_button_text'] = array('default' => 'Close');
    $options['button_styling_options_button_color'] = array('default' => '#1986ac');
    $options['button_styling_options_button_hover_color'] = array('default' => '#1986ac');

    $options['item_styling_options_width'] = array('default' => 240);
    $options['item_styling_options_content_padding'] = array('default' => 20);
    $options['item_styling_options_image_border_width'] = array('default' => 5);
    $options['item_styling_options_show_date'] = array('default' => FALSE);
    $options['item_styling_options_image_border_color'] = array('default' => '#1986ac');
    $options['item_styling_options_title_line_height'] = array('default' => 24);
    $options['item_styling_options_title_font_size'] = array('default' => 24);
    $options['item_styling_options_title_font_type'] = array('default' => 'regular');
    $options['item_styling_options_title_color'] = array('default' => '#2d2d2d');
    $options['item_styling_options_text_line_height'] = array('default' => 12);
    $options['item_styling_options_text_font_size'] = array('default' => 12);
    $options['item_styling_options_text_alignment'] = array('default' => 'center');
    $options['item_styling_options_text_font_type'] = array('default' => 'regular');
    $options['item_styling_options_text_color'] = array('default' => '#4d4d4d');
    $options['item_styling_options_background_color'] = array('default' => '#fff');

    $options['item_open_styling_options_width'] = array('default' => 490);
    $options['item_open_styling_options_content_padding'] = array('default' => 20);
    $options['item_open_styling_options_image_border_width'] = array('default' => 5);
    $options['item_open_styling_options_image_border_color'] = array('default' => '#1986ac');
    $options['item_open_styling_options_title_line_height'] = array('default' => 24);
    $options['item_open_styling_options_title_font_size'] = array('default' => 24);
    $options['item_open_styling_options_title_font_type'] = array('default' => 'regular');
    $options['item_open_styling_options_title_color'] = array('default' => '#2d2d2d');
    $options['item_open_styling_options_text_line_height'] = array('default' => 12);
    $options['item_open_styling_options_text_font_size'] = array('default' => 12);
    $options['item_open_styling_options_text_font_type'] = array('default' => 'regular');
    $options['item_open_styling_options_text_color'] = array('default' => '#4d4d4d');
    $options['item_open_styling_options_background_color'] = array('default' => '#fff');
    return $options;
  }
  /**
   * Extends the options form inherited by this plugin.
   *
   * @param array $form
   *   The form being generated.
   * @param array $form_state
   *   The state that the form has been posted in.
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['button_styling_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Button Styling Options'),
    );
    $form['button_styling_options_button_type'] = array(
      '#type' => 'select',
      '#title' => t('Button type'),
      '#options' => array(
        'flatButton' => 'Flat',
        'flatWideButton' => 'Flat wide',
        'sharp' => 'Sharp',
      ),
      '#default_value' => $this->options['button_styling_options_button_type'],
      '#description' => t('Button design'),
    );
    $form['button_styling_options_read_more_button_text'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Text of the \'Read more\' button (default: \'Read more\')'),
      '#title' => t('Read more button text'),
      '#default_value' => $this->options['button_styling_options_read_more_button_text'],
    );
    $form['button_styling_options_close_button_text'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Text of the \'Close\' element in open item (default: \'Close\')'),
      '#title' => t('Close button text'),
      '#default_value' => $this->options['button_styling_options_close_button_text'],
    );
    $form['button_styling_options_button_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Background color of \'Read more\' and \'Close\' button'),
      '#title' => t('Button color'),
      '#default_value' => $this->options['button_styling_options_button_color'],
    );
    $form['button_styling_options_button_hover_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Hover color of \'Read more\' and \'Close\' button'),
      '#title' => t('Button hover color'),
      '#default_value' => $this->options['button_styling_options_button_hover_color'],
    );
    $form['item_styling_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Item Styling Options'),
    );
    $form['item_styling_options_width'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Width of items (default: 240px)'),
      '#title' => t('Width'),
      '#default_value' => $this->options['item_styling_options_width'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_content_padding'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Padding of items content (default: 20px)'),
      '#title' => t('Content Padding'),
      '#default_value' => $this->options['item_styling_options_content_padding'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_image_border_width'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Image Border Width'),
      '#default_value' => $this->options['item_styling_options_image_border_width'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_show_date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show date'),
      '#title_display' => 'after',
      '#default_value' => $this->options['item_styling_options_show_date'],
    );
    $form['item_styling_options_image_border_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Image Border Color'),
      '#default_value' => $this->options['item_styling_options_image_border_color'],
    );
    $form['item_styling_options_title_line_height'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Line Height'),
      '#default_value' => $this->options['item_styling_options_title_line_height'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_title_font_size'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Font Size'),
      '#default_value' => $this->options['item_styling_options_title_font_size'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_title_font_type'] = array(
      '#type' => 'select',
      '#title' => t('Title Font Type'),
      '#options' => array(
        'regular' => 'Regular',
        'thick' => 'Thick',
        'bold' => 'Bold',
        'bold_italic' => 'Bold-Italic',
        'italic' => 'Italic',
      ),
      '#default_value' => $this->options['item_styling_options_title_font_type'],
    );
    $form['item_styling_options_title_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Color'),
      '#default_value' => $this->options['item_styling_options_title_color'],
    );
    $form['item_styling_options_text_line_height'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Line Height'),
      '#default_value' => $this->options['item_styling_options_text_line_height'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_text_font_size'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Font Size'),
      '#default_value' => $this->options['item_styling_options_text_font_size'],
      '#field_suffix' => 'px'
    );
    $form['item_styling_options_text_alignment'] = array(
      '#type' => 'select',
      '#title' => t('Text alignment'),
      '#options' => array(
        'left' => 'Left',
        'right' => 'Right',
        'center' => 'Center',
      ),
      '#default_value' => $this->options['item_styling_options_text_alignment'],
    );
    $form['item_styling_options_text_font_type'] = array(
      '#type' => 'select',
      '#title' => t('Text Font Type'),
      '#options' => array(
        'regular' => 'Regular',
        'thick' => 'Thick',
        'bold' => 'Bold',
        'bold_italic' => 'Bold-Italic',
        'italic' => 'Italic',
      ),
      '#default_value' => $this->options['item_styling_options_text_font_type'],
    );
    $form['item_styling_options_text_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Color'),
      '#default_value' => $this->options['item_styling_options_text_color'],
    );
    $form['item_styling_options_background_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Background Color'),
      '#default_value' => $this->options['item_styling_options_background_color'],
      '#description' => t('Base item background color (default: #fff)'),
    );
    $form['item_open_styling_options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Item Open Styling Options'),
    );
    $form['item_open_styling_options_width'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Width of open items (default: 490px)'),
      '#title' => t('Width'),
      '#default_value' => $this->options['item_open_styling_options_width'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_content_padding'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#description' => t('Padding of open items content (default: 20px)'),
      '#title' => t('Content Padding'),
      '#default_value' => $this->options['item_open_styling_options_content_padding'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_image_border_width'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Image Border Width'),
      '#default_value' => $this->options['item_open_styling_options_image_border_width'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_image_border_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Image Border Color'),
      '#default_value' => $this->options['item_open_styling_options_image_border_color'],
    );
    $form['item_open_styling_options_title_line_height'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Line Height'),
      '#default_value' => $this->options['item_open_styling_options_title_line_height'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_title_font_size'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Font Size'),
      '#default_value' => $this->options['item_open_styling_options_title_font_size'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_title_font_type'] = array(
      '#type' => 'select',
      '#title' => t('Title Font Type'),
      '#options' => array(
        'regular' => 'Regular',
        'thick' => 'Thick',
        'bold' => 'Bold',
        'bold_italic' => 'Bold-Italic',
        'italic' => 'Italic',
      ),
      '#default_value' => $this->options['item_open_styling_options_title_font_type'],
    );
    $form['item_open_styling_options_title_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Title Color'),
      '#default_value' => $this->options['item_open_styling_options_title_color'],
    );
    $form['item_open_styling_options_text_line_height'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Line Height'),
      '#default_value' => $this->options['item_open_styling_options_text_line_height'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_text_font_size'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Font Size'),
      '#default_value' => $this->options['item_open_styling_options_text_font_size'],
      '#field_suffix' => 'px'
    );
    $form['item_open_styling_options_text_font_type'] = array(
      '#type' => 'select',
      '#title' => t('Text Font Type'),
      '#options' => array(
        'regular' => 'Regular',
        'thick' => 'Thick',
        'bold' => 'Bold',
        'bold_italic' => 'Bold-Italic',
        'italic' => 'Italic',
      ),
      '#default_value' => $this->options['item_open_styling_options_text_font_type'],
    );
    $form['item_open_styling_options_text_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Text Color'),
      '#default_value' => $this->options['item_open_styling_options_text_color'],
    );
    $form['item_open_styling_options_background_color'] = array(
      '#type' => 'textfield',
      '#size' => 10,
      '#title' => t('Background Color'),
      '#default_value' => $this->options['item_open_styling_options_background_color'],
      '#description' => t('Base item background color (default: #fff)'),
    );
  }
}