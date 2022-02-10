<?php
/**
 * @file MD slideshow admin
 *
 * @author Duynv
 *
 */

/**
 * Create overview page for md slider
 *
 */
function md_slider_admin_overview() {
  $header = array(
    t('Title'),
    array('data' => t('Operations'), 'colspan' => '3')
  );
  $rows = array();
  $results = MDSlider::get_all();
  foreach ($results as $slider) {
    $row = array(
      theme('md_slider_overview', array(
        'title' => $slider->title,
        'description' => $slider->description
      ))
    );
    $row[] = array('data' => l(t('Edit'), 'admin/structure/md-slider/' . $slider->slid . '/edit'));
    $row[] = array('data' => l(t('Configure'), 'admin/structure/md-slider/' . $slider->slid . '/configure'));
    if (user_access('delete md_slider')) {
      $row[] = array('data' => l(t('Clone'), 'admin/structure/md-slider/' . $slider->slid . '/clone', array('query' => drupal_get_destination())));
    }
    $rows[] = $row;
  }

  return theme('table', array('header' => $header, 'rows' => $rows));
}

/**
 * Manage form for slider
 */
function md_slider_manage($slid, $option = 'configure') {
  $form['md_slider'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );

  $slider_tabs = array();

  if ($option == 'edit') {
    drupal_set_title(t('Edit Mega Slider: ' . $slid[0]));
    $data = drupal_get_form('md_slider_edit_form', $slid);

    $slider_tabs[0] = array(
      '#type' => 'markup',
      'markup' => $data,
    );

    $form['md_slider'] += $slider_tabs;
  }
  elseif ($option == 'configure') {
    drupal_set_title(t('Configure - MD Slider: ' . $slid[0]));
    $data = drupal_get_form('md_slider_configure_form', $slid);

    $slider_tabs[0] = array(
      '#type' => 'markup',
      'markup' => $data,
    );

    $form['md_slider'] += $slider_tabs;
  }

  return $form;
}

/**
 * Create form to configure slideshow
 */
function md_slider_configure_form($form, &$form_state, $slid = NULL) {
  $slider = NULL;
  $settings = MDSlider::get_default_settings();
  $module_path = drupal_get_path('module', 'md_slider');

  drupal_add_css($module_path . '/css/md-slider-admin.css');
  drupal_add_js($module_path . '/js/md-slider-admin-configure.js');

  if ($slid) {
    $slider = MDSlider::get_by_id($slid);
    $settings = array_merge($settings, $slider->settings);
  }

  $form['sls_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Slider title'),
    '#default_value' => ($slider) ? $slider->title : '',
    '#description' => t('The title is not shown on the public site, just for the administrator to identify this slideshow.'),
    '#required' => TRUE,
  );

  $form['machine_name'] = array(
    '#type' => 'machine_name',
    '#title' => t('Machine name'),
    '#default_value' => ($slider) ? $slider->machine_name : '',
    '#maxlength' => MENU_MAX_MENU_NAME_LENGTH_UI,
    '#description' => t('A unique name to identify for the menu. It must only contain lowercase letters, numbers and hyphens.'),
    '#machine_name' => array(
      'exists' => 'md_slider_check_machine_name',
      'source' => array('sls_title'),
      'label' => t('Machine name'),
      'replace_pattern' => '[^a-z0-9-]+',
      'replace' => '-',
    ),
    '#disabled' => ($slider && $slider->machine_name != ''),
  );

  $form['sls_desc'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => ($slider) ? $slider->description : '',
    '#resizable' => FALSE,
    '#description' => t('The description about this slider.'),
  );

  $form['sls_id'] = array(
    '#type' => 'hidden',
    '#default_value' => ($slider) ? $slider->slid : -1,
  );

  $form['sls_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Slider settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['sls_settings']['sls_full_width'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fullwidth'),
    '#default_value' => $settings['full_width'],
  );

  $form['sls_settings']['sls_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Width'),
    '#default_value' => $settings['width'],
    '#field_suffix' => 'px',
    '#attributes' => array('class' => array('w60')),
  );

  $form['sls_settings']['sls_height'] = array(
    '#type' => 'textfield',
    '#title' => t('Height'),
    '#default_value' => $settings['height'],
    '#field_suffix' => 'px',
    '#attributes' => array('class' => array('w60')),
  );

  $form['sls_settings']['sls_create_bg_imgstyle'] = array(
    '#type' => 'checkbox',
    '#title' => t('Create image style for slide background'),
    '#default_value' => $settings['create_bg_imgstyle'],
  );

  $form['sls_settings']['sls_touch_swipe'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable touch swipe'),
    '#default_value' => $settings['touch_swipe'],
  );

  $form['sls_settings']['sls_responsive'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable responsive'),
    '#default_value' => $settings['responsive'],
  );

  $form['sls_settings']['sls_loop'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable loop'),
    '#default_value' => $settings['loop'],
  );
  $form['sls_settings']['pause_hover'] = array(
    '#type' => 'checkbox',
    '#title' => t('Pause on hover'),
    '#default_value' => $settings['pause_hover'],
  );

  $form['sls_settings']['videobox'] = array(
    '#type' => 'checkbox',
    '#title' => t('View video on lightbox'),
    '#default_value' => $settings['videobox'],
  );


  $form['sls_settings']['sls_ldbar'] = array(
    '#type' => 'radios',
    '#title' => t('Loading bar'),
    '#options' => array('bar' => t('Bar'), 'none' => t('None')),
    '#default_value' => $settings['loadingbar'],
  );

  $form['sls_settings']['sls_bar_position'] = array(
    '#type' => 'select',
    '#title' => t('Bar position'),
    '#options' => array('top' => t('Top'), 'bottom' => t('Bottom')),
    '#default_value' => $settings['bar_position'],
  );

  $form['sls_settings']['show_next_prev_button'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show next previous button'),
    '#default_value' => $settings['show_next_prev_button'],
    '#prefix' => '<div class="md-slider-nav"><h3>Navigation</h3>',
  );

  $form['sls_settings']['auto_play'] = array(
    '#type' => 'checkbox',
    '#title' => t('Auto play slides'),
    '#default_value' => $settings['auto_play'],
  );

  $form['sls_settings']['show_bullet'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show bullet'),
    '#default_value' => $settings['show_bullet'],
  );

  $form['sls_settings']['bullet_position'] = array(
    '#type' => 'select',
    '#title' => t('Bullet position'),
    '#options' => array(
      '1' => t('Bottom left'),
      '2' => t('Bottom center'),
      '3' => t('Bottom right'),
      '4' => t('Top left'),
      '5' => t('Top center'),
      '6' => t('Top right'),
    ),
    '#default_value' => $settings['bullet_position'],
  );

  $form['sls_settings']['show_thumbnail'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show thumbnail'),
    '#default_value' => $settings['show_thumbnail'],
  );

  $form['sls_settings']['thumbnail_position'] = array(
    '#type' => 'select',
    '#title' => t('Thumbnail position'),
    '#options' => array(
      '1' => t('Center 1'),
      '2' => t('Center 2'),
      '3' => t('Left'),
      '4' => t('Right'),
    ),
    '#default_value' => $settings['thumbnail_position'],
  );

  $form['sls_settings']['sls_thumb_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Thumbnail width'),
    '#default_value' => $settings['thumb_width'],
    '#field_suffix' => 'px',
    '#attributes' => array('class' => array('w60')),
  );

  $form['sls_settings']['sls_thumb_height'] = array(
    '#type' => 'textfield',
    '#title' => t('Thumbnail height'),
    '#default_value' => $settings['thumb_height'],
    '#field_suffix' => 'px',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('w60')),
  );
  $form['sls_settings']['sls_device_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Device Width'),
    '#default_value' => isset($settings['device_width']) ? $settings['device_width'] : 460,
    '#field_suffix' => 'px',
    '#suffix' => '</div>',
    '#description' => t('When you add class <strong>hideonmobile</strong> for a layer on the slider, this layer will be hidden if the device width is <= the above value'),
    '#attributes' => array('class' => array('w60')),
  );

  $form['sls_settings']['sls_device_enable'] = array(
    '#type' => 'checkbox',
    '#title' => t('Hide all object with device width'),
    '#default_value' => isset($settings['device_enable']) ? $settings['device_enable'] : '',
    '#description' => 'All layers on slider will be hidden except background ifthe device width is <= "device width" value',
  );


  $style_options = array();
  for ($id = 1; $id <= 9; $id++) {
    $style_options[$id] = t("Style {$id}");
  }
  $form['sls_settings']['border_style'] = array(
    '#type' => 'select',
    '#title' => t('Border style'),
    '#options' => array(0 => t("None")) + $style_options,
    '#default_value' => $settings['border_style'],
  );

  $style_options = array();
  for ($id = 1; $id <= 5; $id++) {
    $style_options[$id] = t("Style {$id}");
  }
  $form['sls_settings']['dropshadow_style'] = array(
    '#type' => 'select',
    '#title' => t('Dropshadow style'),
    '#options' => array(0 => t("None")) + $style_options,
    '#default_value' => $settings['dropshadow_style'],
  );

  $form['sls_settings']['sls_delay'] = array(
    '#type' => 'textfield',
    '#title' => t('Slide delay'),
    '#default_value' => $settings['delay'],
    '#field_suffix' => 'milliseconds',
    '#attributes' => array('class' => array('w60')),
  );

  $form['sls_settings']['sls_transtime'] = array(
    '#type' => 'textfield',
    '#title' => t("Slide's translation time"),
    '#default_value' => $settings['transtime'],
    '#field_suffix' => 'milliseconds',
    '#attributes' => array('class' => array('w60')),
  );

  # Text/Typography settings
  $form['sls_settings']['dmf_google'] = array(
    '#type' => 'textarea',
    '#title' => t('@import'),
    '#resizable' => FALSE,
    '#rows' => 1,
    '#description' => t("For example: &lt;link href='<strong>http://fonts.googleapis.com/css?family=Roboto+Condensed</strong>' rel='stylesheet' type='text/css'&gt"),
    '#default_value' => $settings['dmf_google'],
    '#prefix' => '<div id="customfont-wrap" class="form-subform"><h3>Google Web Fonts</h3>
      <ul class="steps">
      <li>Go to <a href="http://www.google.com/webfonts" target="_blank">www.google.com/webfonts</a>, choose your fonts and add to collection</li>
      <li>Click &quot;Use&quot; in the bottom bar after choose fonts</li>
      <li>Find &quot;Add this code to your website&quot;, copy from <strong>http://</strong> to the nearest <strong>\'</strong> and paste it below to activate.</li>
      </ul>',
  );

  $form['sls_settings']['enable_font_css'] = array(
    '#type' => 'checkbox',
    '#title' => t("Do not add fonts to theme. My theme's already included this fonts."),
    '#default_value' => $settings['enable_font_css'],
  );

  $form['sls_settings']['generate_css_file'] = array(
    '#type' => 'checkbox',
    '#title' => t("Generate css file."),
    '#default_value' => $settings['generate_css_file'],
  );
  $form["sls_settings"]["on_start"] = array(
    "#type" => "textarea",
    "#title" => t("TransitionStart callback"),
    "#default_value" => $settings['on_start'],
    "#description" => t("Callback function when start change slide.Example: function() {//Code in here}"),
    "#resizable" => FALSE,
  );
  $form["sls_settings"]["on_end"] = array(
    "#type" => "textarea",
    "#title" => t("TransitionEnd callback"),
    "#default_value" => $settings['on_end'],
    "#description" => t("Callback function when end change slide. Example: function() {//Code in here}"),
    "#resizable" => FALSE,
  );

  $form['sls_create'] = array(
    '#type' => 'submit',
    '#value' => ($slider) ? t('Save') : t('Create'),
  );

  if ($slider) {
    $form['sls_save_edit'] = array(
      '#type' => 'submit',
      '#value' => t('Save & Edit'),
    );

    # Check user permission to show delete button
    if (user_access('delete md_slider')) {
      $form['sls_delete'] = array(
        '#type' => 'submit',
        '#value' => t('Delete'),
      );
    }
  }

  return $form;
}

/**
 * Handle submit form configure slider
 */
function md_slider_configure_form_submit($form, &$form_state) {
  $settings = MDSlider::get_default_settings();
  $custom = $form_state['values'];

  if ($custom['op'] == t('Delete')) {
    $form_state['redirect'] = array(
      'admin/structure/md-slider/delete/confirm',
      array('query' => array('slid' => $custom['sls_id']))
    );
    return;
  }

  $settings['full_width'] = $custom['sls_full_width'];
  $settings['width'] = $custom['sls_width'];
  $settings['height'] = $custom['sls_height'];
  $settings['touch_swipe'] = $custom['sls_touch_swipe'];
  $settings['responsive'] = $custom['sls_responsive'];
  $settings['loadingbar'] = $custom['sls_ldbar'];
  $settings['loop'] = $custom['sls_loop'];
  $settings['bar_position'] = $custom['sls_bar_position'];
  $settings['show_next_prev_button'] = $custom['show_next_prev_button'];
  $settings['auto_play'] = $custom['auto_play'];
  $settings['pause_hover'] = $custom['pause_hover'];
  $settings['show_bullet'] = $custom['show_bullet'];
  $settings['bullet_position'] = $custom['bullet_position'];
  $settings['show_thumbnail'] = $custom['show_thumbnail'];
  $settings['thumbnail_position'] = $custom['thumbnail_position'];
  $settings['border_style'] = $custom['border_style'];
  $settings['dropshadow_style'] = $custom['dropshadow_style'];
  $settings['delay'] = $custom['sls_delay'];
  $settings['transtime'] = $custom['sls_transtime'];
  $settings['thumb_width'] = $custom['sls_thumb_width'];
  $settings['thumb_height'] = $custom['sls_thumb_height'];
  $settings['device_enable'] = $custom['sls_device_enable'];
  $settings['device_width'] = $custom['sls_device_width'];
  $settings['create_bg_imgstyle'] = $custom['sls_create_bg_imgstyle'];
  $settings['dmf_google'] = $custom['dmf_google'];
  $settings['enable_font_css'] = $custom['enable_font_css'];
  $settings['generate_css_file'] = $custom['generate_css_file'];
  $settings['on_start'] = $custom['on_start'];
  $settings['on_end'] = $custom['on_end'];
  $settings["videobox"] = $custom["videobox"];

  // Create new slider or update existed slider to database
  if ($custom['sls_id'] != -1) {
    $slider = MDSlider::get_by_id((int) $custom['sls_id']);
    $slider->title = $custom['sls_title'];
    $slider->description = $custom['sls_desc'];
    $slider->settings = $settings;
    $slider->save();

    if ($custom['op'] == t('Save')) {
      $form_state['redirect'] = 'admin/structure/md-slider';
    }
    else {
      unset($_GET['destination']);
      $form_state['redirect'] = 'admin/structure/md-slider/' . $custom['sls_id'] . '/edit';
    }

    $action = "updated configurations.";
  }
  else {
    $slider = MDSlider::create($custom['sls_title'], $custom['sls_desc'], $settings, $custom['machine_name']);
    $form_state['redirect'] = 'admin/structure/md-slider/' . $slider->slid . '/edit';

    if ($slider) {
      $action = "created.";
    }
    else {
      $action = "created fail.";
    }
  }

  # Create image style for background and thumbnail image
  if ($settings['show_thumbnail']) {
    update_image_style('md_slider_' . $slider->slid . '_thumb', $settings['thumb_width'], $settings['thumb_height']);
  }

  if ($settings['create_bg_imgstyle']) {
    update_image_style('md_slider_' . $slider->slid . '_bg', $settings['width'], $settings['height'], TRUE);
  }

  if ($slider) {
    // Create css file if setting is required
    $css_content = variable_get("md_slider_{$slider->slid}_css", FALSE);
    if ($settings["generate_css_file"] && $css_content !== FALSE) {
      file_unmanaged_save_data($css_content, "public://md-slider-{$slider->machine_name}-layers.css", FILE_EXISTS_REPLACE);
    }

    drupal_set_message(t("MDSlider: '{$slider->title}' has been {$action}"));
  }
  else {
    drupal_set_message(t("MDSlider: '{$custom['sls_title']}' has been {$action}"));
  }
}

/**
 * Implements theme md_slider_overview
 */
function theme_md_slider_overview($variables) {
  $output = check_plain($variables['title']);
  $output .= '<div class="despcription">' . filter_xss_admin($variables['description']) . '</div>';

  return $output;
}

/**
 * Create slider edit form
 */
function md_slider_edit_form($form, &$form_state, $slid) {
  $module_path = drupal_get_path('module', 'md_slider');
  $libraries = drupal_get_library('media');
  $form['#attached']['library'] = array(
    array('system', 'jquery.cookie'),
    array('system', 'ui.tabs'),
    array('system', 'ui.sortable'),
    array('system', 'ui.draggable'),
    array('system', 'ui.droppable'),
    array('system', 'ui.dialog'),
    array('media', 'media_browser'),
  );

  $form['#attached']['js'] = array(
    $module_path . '/js/spectrum/spectrum.js',
    $module_path . '/js/preview_transition/js/md-slider-min.js',
    $module_path . '/js/md-slider-lib.js',
    $module_path . '/js/md-slider-toolbar.js',
    $module_path . '/js/md-slider-timeline.js',
    $module_path . '/js/md-slider-panel.js',
    $module_path . '/js/md-slider-admin-edit.js'
  );
  if (!isset($libraries['media_browser_settings'])) {
    $settings = array(
      'browserUrl' => url('media/browser', array(
          'query' => array(
            'render' => 'media-popup'
          ))
      ),
      'styleSelectorUrl' => url('media/-media_id-/format-form', array(
          'query' => array(
            'render' => 'media-popup'
          ))
      ),
      'dialogOptions' => array(
        'dialogclass' => variable_get('media_dialogclass', 'media-wrapper'),
        'modal' => (boolean)variable_get('media_modal', TRUE),
        'draggable' => (boolean)variable_get('media_draggable', FALSE),
        'resizable' => (boolean)variable_get('media_resizable', FALSE),
        'minwidth' => (int)variable_get('media_minwidth', 500),
        'width' => (int)variable_get('media_width', 670),
        'height' => (int)variable_get('media_height', 280),
        'position' => variable_get('media_position', 'center'),
        'overlay' => array(
          'backgroundcolor' => variable_get('media_backgroundcolor', '#000000'),
          'opacity' => (float)variable_get('media_opacity', 0.4),
        ),
        'zindex' => (int)variable_get('media_zindex', 10000),
      ),
    );
    $form['#attached']['js'][] = array(
      'data' => array('media' => $settings),
      'type' => 'setting',
    );
  }
  else {
    $form['#attached']['library'][] = array('media', 'media_browser_settings');
  }

  $form['#attached']['css'] = array(
    $module_path . '/js/spectrum/spectrum.css',
    $module_path . '/js/preview_transition/css/md-slider.css',
    $module_path . '/css/md-slider-admin.css'
  );
  $info = system_get_info('module', 'media');
  # Remove last breadcrumb
  $breadcrumb = drupal_get_breadcrumb();
  unset($breadcrumb[count($breadcrumb) - 1]);
  drupal_set_breadcrumb($breadcrumb);

  $slider = MDSlider::get_by_id($slid);
  $settings = MDSlider::get_default_settings();
  if ($slider == NULL) {
    $form['slider_msg'] = array(
      '#markup' => '<h3>This slider not exist. Please try again!</h3>',
    );

    return $form;
  }
  $settings = array_merge($settings, $slider->settings);

  $form['slider_main'] = array(
    '#markup' => theme('slider_edit_form', array('slider' => $slider)),
  );

  $form['slider_id'] = array(
    '#type' => 'hidden',
    '#default_value' => $slid,
  );

  $form['slider_data_save'] = array(
    '#type' => 'textarea',
    '#default_value' => '',
    '#resizable' => FALSE,
  );

  $form['color_saved'] = array(
    '#type' => 'hidden',
    '#default_value' => $settings['color_saved'],
  );

  $form['slider_save'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );
  $form['slider_save_continue'] = array(
    '#type' => 'submit',
    '#value' => t('Save & Continue'),
  );

  return $form;
}

/**
 * Slide setting form
 */
function get_slide_background() {
  $_POST = $_REQUEST;
  $fid = -1;
  $slid = -1;
  if (isset($_POST['fid'])) {
    $fid = $_POST['fid'];
  }

  if (isset($_POST['slider_id'])) {
    $slid = urldecode($_POST['slider_id']);
  }

  if ($fid == -1 || $slid == -1) {
    $output = "";
  }
  else {
    $file = file_load($fid);
    $slider = MDSlider::get_by_id($slid);
    if ($slider->settings['create_bg_imgstyle']) {
      $output = image_style_url("md_slider_{$slid}_bg", $file->uri);
    }
    else {
      $output = file_create_url($file->uri);
    }
  }

  print $output;
}

/**
 * Get video info
 */
function get_video_info() {
  $output = '';

  if (isset($_GET['url']) && $_GET['url'] != '') {
    $ret = process_video_url($_GET['url']);
    $output = get_video_data($ret['type'], $ret['id']);
  }
  print json_encode($output);
}

/**
 * Process video url
 */
function process_video_url($url) {
  $output = array();
  if (strpos($url, 'youtube') !== FALSE || strpos($url, 'youtu.be') !== FALSE) {
    $output['type'] = 'youtube';
    $output['id'] = md_slider_get_youtube_id_from_url($url);
  }
  elseif (strpos($url, 'vimeo') !== FALSE) {
    $output['type'] = 'vimeo';
    $output['id'] = md_slider_get_vimeo_id_from_url($url);
  }

  return $output;
}

/**
 * Get video id from url for youtube link
 */
function md_slider_get_youtube_id_from_url($url) {
  $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
  preg_match($pattern, $url, $matches);
  return isset($matches[1]) ? $matches[1] : '';
}

/**
 * Get video id from url of vimeo link
 */
function md_slider_get_vimeo_id_from_url($url) {
  preg_match('/(?:vimeo\.com).*\/([0-9]*)/i', $url, $matches);
  return isset($matches[1]) ? $matches[1] : "";
}

/**
 * Implements handle submit edit form
 */
function md_slider_edit_form_submit($form, &$form_state) {
  $custom = $form_state['values'];
  $slides = drupal_json_decode($custom['slider_data_save']);
  $slider_id = $custom['slider_id'];
  $classes = array();

  $slider = MDSlider::get_by_id($slider_id);
  $slider->settings['color_saved'] = $custom['color_saved'];
  $slider->save();
  foreach ($slides as $index => $slide) {
    $slide = (object) $slide;
    $settings = MDSlide::get_default_settings();
    $settings['background_image'] = $slide->itemsetting['background_image'];
    $settings['background_image_alt'] = $slide->itemsetting['background_image_alt'];
    $settings['background_overlay'] = isset($slide->itemsetting['background_overlay']) ? $slide->itemsetting['background_overlay'] : '';
    $settings['timelinewidth'] = $slide->itemsetting['timelinewidth'];
    $settings['custom_thumbnail'] = $slide->itemsetting['custom_thumbnail'];
    $settings['custom_thumbnail_alt'] = $slide->itemsetting['custom_thumbnail_alt'];
    $settings['transitions'] = $slide->itemsetting['transitions'];
    $settings['background_color'] = $slide->itemsetting['background_color'];
    $settings['disabled'] = isset($slide->itemsetting['disabled']) ? $slide->itemsetting['disabled'] : 0;

    # Generate css class for layers in slide
    foreach ($slide->boxitems as $key => &$layer) {
      $css = process_layer_style((object) $layer);
      if ($css != "") {
        if (isset($layer['link']) && is_array($layer['link'])) {
          $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a" . $css;
        }
        else {
          $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key}" . $css;
        }
      }

      // Generate css for hover link
      if (isset($layer['link']) && is_array($layer['link'])) {
        $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} {z-index: {$layer["zindex"]}!important;}";
        $css = md_slider_generate_layer_link_css($layer['link']);
        if ($css != '') {
          if ($layer['type'] != "text") {
            $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a:hover img" . $css;
          }
          else {
            $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a:hover" . $css;
          }
        }
      }

      if (isset($layer["title"])) {
        $layer["title"] = decode_entities($layer["title"]);
      }
    }

    if (($sid = $slide->itemsetting['slide_id']) != -1) {
      $slide_object = MDSlide::get($sid);
      $slide_object->position = $index;
      $slide_object->settings = $settings;
      $slide_object->layers = $slide->boxitems;

      $slide_object->save();
    }
    else {
      MDSlide::create($slider_id, $index, $settings, $slide->boxitems);
    }
  }

  # Set redirect form path
  if ($custom['op'] == t('Save')) {
    $form_state['redirect'] = 'admin/structure/md-slider';
  }
  else {
    unset($_GET['destination']);
  }

  # Generate css file
  $css_content = implode("\r\n", $classes);
  variable_set("md_slider_{$slider->slid}_css", $css_content);
  if ($slider->settings["generate_css_file"]) {
    file_unmanaged_save_data($css_content, "public://md-slider-{$slider->machine_name}-layers.css", FILE_EXISTS_REPLACE);
  }

  drupal_set_message(t("MDSlider: '{$slider->title}' has been updated."));
}

/**
 * Create image_style for thumbnail
 */
function update_image_style($image_style_name, $width, $height, $allow_scaleup = FALSE) {
  # Load image_style if it existed
  $style = image_style_load($image_style_name);
  if (empty($style)) {
    $style = image_style_save(array('name' => $image_style_name));
  }
  else {
    # Remove all effect in this image style
    $current_effects = $style['effects'];
    foreach ($current_effects as $effect) {
      image_effect_delete($effect);
    }
  }

  # Create effect for image_style
  $new_effect = array(
    'name' => 'image_scale_and_crop',
    'data' => array(
      'width' => $width,
      'height' => $height,
      'scaleup' => $allow_scaleup,
    ),
    'isid' => $style['isid'],
  );

  # Save effect for image_styles
  image_effect_save($new_effect);
}

/**
 * Implements delete slide from database
 */
function delete_slide() {
  $sid = $_POST['sid'];
  if (MDSlide::delete($sid)) {
    print "OK";
  }
  else {
    print "Can not delete slide: {$sid}";
  }
}

/**
 * Create form to confirm delete slider
 */
function confirm_delete_form($form, &$form_state) {
  drupal_set_title('Are you sure want to delete this slider?');
  $form['confirm_del'] = array(
    '#type' => 'submit',
    '#value' => t('Delete'),
  );

  $form['confirm_del_slid'] = array(
    '#type' => 'hidden',
    '#default_value' => $_GET['slid'],
  );

  $form['confirm_cancel'] = array(
    '#type' => 'submit',
    '#value' => t('Cancel'),
  );

  return $form;
}

/**
 * Handle submit confirm delete slider form
 */
function confirm_delete_form_submit($form, &$form_state) {
  $custom = $form_state['values'];

  if ($custom['op'] == t('Delete')) {
    unset($_GET['destination']);
    if (MDSlider::delete($custom['confirm_del_slid'])) {
      drupal_set_message(t("MDSlider has been deleted."));
    }
    else {
      drupal_set_message(t("MDSlider has been deleted fail."));
    }

    $form_state['redirect'] = 'admin/structure/md-slider';
  }
  else {
    $form_state['redirect'] = "admin/structure/md-slider/{$custom['confirm_del_slid']}";
  }
}

/**
 * Clone slider callback
 */
function md_slider_clone($slid) {
  $org_slider = MDSlider::get_by_id($slid[0]);
  $clone = MDSlider::create("clone of " . $org_slider->title, $org_slider->description, $org_slider->settings);
  $classes = array();

  $org_slides = MDSlide::get_by_slider_id($slid[0]);
  foreach ($org_slides as $index => $slide) {
    MDSlide::create($clone->slid, $slide->position, $slide->settings, $slide->layers);

    # Generate css class for layers in slide
    foreach ($slide->layers as $key => $layer) {
      $css = process_layer_style((object) $layer);
      if ($css != "") {
        if (isset($layer['link']) && is_array($layer['link'])) {
          $classes[] = ".md-layer-{$clone->slid}-{$index}-{$key} a" . $css;
        }
        else {
          $classes[] = ".md-layer-{$clone->slid}-{$index}-{$key}" . $css;
        }
      }


      if (isset($layer['link']) && is_array($layer['link'])) {
        $css = md_slider_generate_layer_link_css($layer['link']);
        if ($css != '') {
          if ($layer['type'] != "text") {
            $classes[] = ".md-layer-{$clone->slid}-{$index}-{$key} a:hover img" . $css;
          }
          else {
            $classes[] = ".md-layer-{$clone->slid}-{$index}-{$key} a:hover" . $css;
          }
        }
      }
    }
  }

  # Create image_style for background image
  if ($clone->settings['create_bg_imgstyle']) {
    update_image_style("md_slider_{$clone->slid}_bg", $clone->settings['width'], $clone->settings['height']);
  }

  # Create image style for thumbnail image
  update_image_style("md_slider_{$clone->slid}_thumb", $clone->settings['thumb_width'], $clone->settings['thumb_height']);


  # Generate file custom css for slider
  $content = implode("\r\n", $classes);
  if ($clone->settings["generate_css_file"]) {
    file_unmanaged_save_data($content, "public://md-slider-{$clone->machine_name}-layers.css", FILE_EXISTS_REPLACE);
  }
  else {
    variable_set("md_slider_{$clone->slid}_css", $content);
  }

  drupal_set_message(t("Clone MDSlider: '{$org_slider->title}' successful."));
  drupal_goto(drupal_get_destination());
}

/**
 * Transition library ajax
 */
function md_slider_transition_view() {
  print theme('md_transition_view', array('parameters' => $_REQUEST));
}

/**
 * Callback for get video-setting-form
 */
function video_setting_form() {
  print theme('video_setting_form', array('show_change' => $_POST['change']));
}

/**
 * Import and export form
 */
function md_slider_import_export_form($form, &$form_state) {
  # Get all CuteSliders are existing
  $sliders = MDSlider::get_all();

  $sliders_options = array();
  foreach ($sliders as $slider) {
    $sliders_options[$slider->slid] = $slider->title;
  }
  $form['export'] = array(
    '#type' => 'fieldset',
    '#title' => t('Export'),
    '#collapsible' => FALSE,
  );

  $form['export']['sliders'] = array(
    '#type' => 'radios',
    '#title' => t('Choose sliders to export'),
    '#options' => $sliders_options,
    '#default_value' => array(),
  );

  $form['export']['export_button'] = array(
    '#markup' => '<div><input type="button" name="export-button" class="form-submit" value="Export" /></div>'
  );

  $form['export']['export_data'] = array(
    '#type' => 'textarea',
    '#title' => t('Export data'),
    '#default_value' => '',
    '#description' => t('Copy this data paste into input into Import form of site which you want to import.'),
    '#resizable' => FALSE,
  );

  $form['import'] = array(
    '#type' => 'fieldset',
    '#title' => t('Import'),
    '#collapsible' => FALSE,
  );

  $form['import']['machine_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Machine name'),
    '#default_value' => '',
    '#description' => t('Enter the machine name to use for this MDSlider if it is different from the source MDSlider. Leave blank to use the name of the MDSlider. It must only contain lowercase letters, numbers and hyphens.'),
    '#resizable' => FALSE,
  );

  $form['import']['import_data'] = array(
    '#type' => 'textarea',
    '#title' => t('Import data'),
    '#default_value' => '',
    '#description' => t('Paste MDSlider data export from another site here to import in this site.'),
    '#resizable' => FALSE,
  );

  $form['import']['import_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Import'),
  );

  drupal_add_js(drupal_get_path('module', 'md_slider') . '/js/md-slider-export.js');

  return $form;
}

/**
 * Validate submit form
 */
function md_slider_import_export_form_validate($form, &$form_state) {
  if ($form_state['values']['machine_name'] != '' && !preg_match('/^[a-z0-9\-]{1,}$/', $form_state['values']['machine_name'])) {
    form_set_error('machine_name', t('Machine name do not valid.'));
  }
}

/**
 * Handle submit import form
 */
function md_slider_import_export_form_submit($form, &$form_state) {
  $import_data = $form_state['values']['import_data'];
  $new_name = $form_state['values']['machine_name'];
  $classes = array();
  if ($import_data == '') {
    drupal_set_message(t('MDSlider: Data import is invalided.'), 'error');
    return;
  }

  $slider_data = drupal_json_decode($import_data);
  $slider_data = array_shift($slider_data);
  if (!isset($slider_data['title']) || !isset($slider_data['machine_name']) || !isset($slider_data['description']) || !isset($slider_data['settings'])
    || !isset($slider_data['slides'])
  ) {
    drupal_set_message(t('MDSlider: Data import is invalided.'), 'error');
    return;
  }

  # Check machine name
  if ($new_name == '' && md_slider_check_machine_name($slider_data['machine_name'])) {
    drupal_set_message(t('A MDSlider by that machine name already exists; please choose a different machine name'), 'error');
    return;
  }

  # Add slider in database
  $slider = MDSlider::create($slider_data['title'], $slider_data['description'], $slider_data['settings']);
  if (!$slider) {
    drupal_set_message(t('MDSlider: Import ' . $slider_data['title'] . 'unsuccessful.'), 'error');
    return;
  }

  foreach ($slider_data['slides'] as $index => $slide_data) {
    if (!isset($slide_data['position']) || !isset($slide_data['settings']) || !isset($slide_data['layers'])) {
      continue;
    }

    MDSlide::create($slider->slid, $slide_data['position'], $slide_data['settings'], $slide_data['layers']);

    # Generate css class for layers in slide
    foreach ($slide_data['layers'] as $key => $layer) {
      $css = process_layer_style((object) $layer);
      if ($css != "") {
        if (isset($layer['link']) && is_array($layer['link'])) {
          $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a" . $css;
        }
        else {
          $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key}" . $css;
        }
      }

      if (isset($layer['link']) && is_array($layer['link'])) {
        $css = md_slider_generate_layer_link_css($layer['link']);
        if ($css != '') {
          if ($layer['type'] != "text") {
            $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a:hover img" . $css;
          }
          else {
            $classes[] = ".md-layer-{$slider->slid}-{$index}-{$key} a:hover" . $css;
          }
        }
      }
    }
  }

  # Create image_style for background image
  if ($slider->settings['create_bg_imgstyle']) {
    update_image_style("md_slider_{$slider->slid}_bg", $slider->settings['width'], $slider->settings['height']);
  }

  # Create image style for thumbnail image
  update_image_style("md_slider_{$slider->slid}_thumb", $slider->settings['thumb_width'], $slider->settings['thumb_height']);


  # Generate file custom css for slider
  $content = implode("\r\n", $classes);
  if ($slider->settings["generate_Css_file"]) {
    file_unmanaged_save_data($content, "public://md-slider-{$slider->machine_name}-layers.css", FILE_EXISTS_REPLACE);
  }
  else {
    variable_set("md_slider_{$slider->slid}_css", $content);
  }

  drupal_set_message(t('MDSlider: Import successfull.'));
}

/**
 * Callback for export data
 */
function md_slider_export_data() {
  if (!isset($_POST['slids']) || $_POST['slids'] == '') {
    print 'Data invalid';
    exit;
  }
  else {
    $response = array();
    $slids = explode(',', $_POST['slids']);

    $sliders = MDSlider::get_by_ids($slids);
    $slides = MDSlide::get_by_sliders($slids);

    foreach ($sliders as $slider) {
      $slider_data = array(
        'title' => $slider->title,
        'machine_name' => $slider->machine_name,
        'description' => $slider->description,
        'settings' => $slider->settings
      );
      $slider_data['slides'] = md_slider_get_slides_data($slider->slid, $slides);
      $response[] = $slider_data;
    }

    print drupal_json_encode($response);
  }
}

/**
 * Get slider slides data
 */
function md_slider_get_slides_data($slid, &$slides) {
  $output = array();
  foreach ($slides as $key => $slide) {
    if ($slide->slid == $slid) {
      foreach ($slide->layers as &$layer) {
        if (isset($layer["thumb"])) {
          unset($layer["thumb"]);
        }
      }
      $output[] = array(
        'position' => $slide->position,
        'settings' => $slide->settings,
        'layers' => $slide->layers
      );
      unset($slides[$key]);
    }
  }

  return $output;
}

/**
 * Callback to check machine name of slider
 */
function md_slider_check_machine_name($machine_name) {
  return (MDSlider::get_by_machine_name($machine_name) != NULL) ? TRUE : FALSE;
}

