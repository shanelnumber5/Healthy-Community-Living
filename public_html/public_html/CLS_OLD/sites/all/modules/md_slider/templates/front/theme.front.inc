<?php
  /**
   * @file: theme.front.inc
   * User: Duy
   * Date: 1/29/13
   * Time: 4:18 PM
   */
  function template_preprocess_md_slider_render(&$variables) {
    $data_properties = array();
    $options = array();
    $slider = $variables['slider'];
    $variables['slides'] = MDSlide::get_by_slider_id($slider->slid);
    $settings = array_merge(MDSlider::get_default_settings(), $slider->settings);

    # Create options for slider
    $options['fullwidth'] = (boolean)$settings['full_width'];
    $options['transitionsSpeed'] = intval($settings['transtime']);
    $options['width'] = $settings['width'];
    $options['height'] = $settings['height'];
    $options['enableDrag'] = (boolean)$settings['touch_swipe'];
    $options['responsive'] = (boolean)$settings['responsive'];
    $options['pauseOnHover'] = (boolean)$settings['pause_hover'];
    if (isset($settings['loop']))
	  $options['loop'] = (boolean)$settings['loop'];
    else
      $options['loop'] = TRUE;
    $options['showLoading'] = ($settings['loadingbar'] != 'none') ? TRUE : FALSE;
    $options['loadingPosition'] = $settings['bar_position'];
    $options['showArrow'] = (boolean)$settings['show_next_prev_button'];
    $options['showBullet'] = (boolean)$settings['show_bullet'];
    $options['posBullet'] = $settings['bullet_position'];
    $options['showThumb'] = (boolean)$settings['show_thumbnail'];
    $options['posThumb'] = $settings['thumbnail_position'];
    $options['slideShowDelay'] = $settings['delay'];
    $options['slideShow'] = (boolean)$settings['auto_play'];
    $options['styleBorder'] = $settings['border_style'];
    $options['styleShadow'] = $settings['dropshadow_style'];
    if (isset($settings["videobox"]))
        $options["videoBox"] = (boolean)$settings["videobox"];
    else
        $options["videoBox"] = FALSE;

    drupal_add_js(array("md_slider_options_{$slider->slid}" => $options), 'setting');

    # Add google fonts
    $google_fonts = $settings['dmf_google'];
    if ($google_fonts != '' && !$settings['enable_font_css']) {
      drupal_add_css($google_fonts, array('type' => 'external'));
    }

    # Add data-thumb-width & data-thumb-height
    $data_properties[] = "data-thumb-width='{$settings['thumb_width']}'";
    $data_properties[] = "data-thumb-height='{$settings['thumb_height']}'";
    $variables['data_properties'] = implode(' ', $data_properties);

    # Create css class for layers
    md_slider_prepare_layers_class($slider->slid, $variables['slides']);
  }

  function template_preprocess_front_slide_render(&$variables) {
    $slide = $variables['slide'];
    $data_properties = array();
    $data_properties[] = "data-timeout='{$slide->settings['timelinewidth']}00'";

    // Add data for slide settings thumbnail alt and background image alt
    if (!isset($slide->settings["background_image_alt"]))
      $slide->settings["background_image_alt"] = "";
    if (!isset($slide->settings["custom_thumbnail_alt"]))
      $slide->settings["custom_thumbnail_alt"] = "";

    // Add data transitions for slide
    if (!empty($slide->settings["transitions"]))
      $data_properties[] = 'data-transition="' . implode(",", $slide->settings["transitions"]) . '"';

    // Process background settings
    $variables['background_style'] = "";
    if ($slide->settings['background_image'] != -1 && ($background = file_load($slide->settings['background_image']))) {
      // Load background image
      $create_bg_imgs = $variables['slider_settings']['create_bg_imgstyle'];
      if ($create_bg_imgs)
        $variables['background_url'] = image_style_url('md_slider_' . $slide->slid . '_bg', $background->uri);
      else
        $variables['background_url'] = file_create_url($background->uri);

      // Load thumbnail image
      $thumb = $background;
      if ($slide->settings['custom_thumbnail'] != -1)
        $thumb = file_load($slide->settings['custom_thumbnail']);

      // Load thumbnail alt
      $thumb_alt = isset($slide->settings["background_image_alt"]) ? $slide->settings["background_image_alt"] : "";
      if (isset($slide->settings["custom_thumbnail_alt"]) && $slide->settings["custom_thumbnail_alt"])
        $thumb_alt = $slide->settings["custom_thumbnail_alt"];

      $thumb_data = image_style_url('md_slider_' . $slide->slid . '_thumb', $thumb->uri);
      $data_properties[] = "data-thumb-type='image'";
      $data_properties[] = "data-thumb-alt='{$thumb_alt}'";
    }
    else {
      // use background color
      if (!$variables["slider_settings"]["responsive"])
        $style = array("height: {$variables["slider_settings"]["height"]}px");
      else
        $style = array("height: 100%");

      if (!$variables["slider_settings"]["full_width"] && !$variables["slider_settings"]["responsive"])
        $style[] = "width: {$variables["slider_settings"]["width"]}px";
      else
        $style[] = "width: 100%";

      if ($slide->settings["background_color"] != "") {
        if (strpos($slide->settings["background_color"], "rgba") !== FALSE) {
          $color = explode(",", $slide->settings["background_color"]);
          unset($color["3"]);
          $color = implode(",", $color) . ")";
          $color = str_replace("rgba", "rgb", $color);
          $style[] = "background-color: {$color}";
        }
        $style[] = "background-color: {$slide->settings["background_color"]}";
      }
      $variables['background_style'] = implode("; ", $style);

      if ($slide->settings['custom_thumbnail'] != -1) {
        $thumb = file_load($slide->settings['custom_thumbnail']);
        $thumb_data = image_style_url('md_slider_' . $slide->slid . '_thumb', $thumb->uri);
        $data_properties[] = "data-thumb-type='image'";

        // Process data for thumbnail alt
        $thumb_alt = "";
        if (isset($slide->settings["custom_thumbnail_alt"]) && $slide->settings["custom_thumbnail_alt"])
          $thumb_alt = $slide->settings["custom_thumbnail_alt"];
        $data_properties[] = "data-thumb-alt='{$thumb_alt}'";
      }
      else {
        $data_properties[] = "data-thumb-type='color'";
        $thumb_data = array(
          "height: {$variables["slider_settings"]["thumb_height"]}px",
          "width: {$variables["slider_settings"]["thumb_width"]}px",
        );

        if ($slide->settings["background_color"] != "") {
          if (strpos($slide->settings["background_color"], "rgba") !== FALSE) {
            $color = explode(",", $slide->settings["background_color"]);
            unset($color["3"]);
            $color = implode(",", $color) . ")";
            $color = str_replace("rgba", "rgb", $color);
            $thumb_data[] = "background-color: {$color}";
          }
          $thumb_data[] = "background-color: {$slide->settings["background_color"]}";
        }

        $thumb_data = implode("; ", $thumb_data);
      }
    }
    $data_properties[] = "data-thumb='{$thumb_data}'";

    $variables['data_properties'] = implode(' ', $data_properties);
    $variables['elements_height'] = $variables['slider_settings']['height'];
  }

  /**
   * Implements template_preprocess_hook for front_layers_render theme
   */
  function template_preprocess_front_layers_render(&$variables) {
    $layer = $variables['layer'];

    if (!isset($layer{"title"}))
      $layer["title"] = "";

    # Get image url for image layer
    if ($layer['type'] == 'image') {
      $layer['url'] = '';
      if (isset($layer['fileid'])) {
        $image = file_load($layer['fileid']);
        if ($image)
          $layer['url'] = file_create_url($image->uri);
      }
    }

    if ($layer['type'] == 'video')
      $layer['url'] = md_slider_generate_video_embeded_url($layer['fileid']);
    $variables['data'] = process_element_data($layer);


    $class = array('md-object');
    if (isset($layer['style']) && $layer['style'] != 'none') {
      $class[] = $layer['style'];
    }
    if (isset($layer['mdtclass']) && $layer['mdtclass'] != ''){
      $class[] = $layer['mdtclass'];
    }
    $variables['class'] = implode(' ', $class);

    # Generate layer link
    if (isset($layer['link']) && is_array($layer['link'])) {
      $link = $layer['link'];
      if (strpos($link['value'], 'http') === FALSE && strpos($link["value"], "#") !== 0){
        $url = parse_url($link['value']);
        $options = array();
        $options['absolute'] = TRUE;
        if (isset($url['fragment']))
          $options['fragment'] = $url['fragment'];

        $variables["link"] = url($url['path'], $options);
      }
      else
          $variables["link"] = $link['value'];
    }
    if (isset($layer["opacity"]) && intval($layer["opacity"]) != 100) {
      $percent = intval($layer["opacity"])/100;
      $layer["opacity"] = "-ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity={$layer["opacity"]})'; filter: alpha(opacity={$layer["opacity"]}); opacity: {$percent};";
    }
    else
      $layer["opacity"] = "";

    $variables['layer'] = (object)$layer;
  }

  /**
   * Process data for elements
   */
  function process_element_data($element) {
    $type = $element['type'];
    $element_full = array_merge(MDCommon::$default_layers[$type], $element);
    $element_full['starttime'] = $element_full['starttime'];
    $element_full['stoptime'] = $element_full['stoptime'];

    $output = array();
    $output[] = "data-x='{$element_full['left']}'";
    $output[] = "data-y='{$element_full['top']}'";
    $output[] = "data-width='{$element_full['width']}'";
    $output[] = "data-height='{$element_full['height']}'";
    $output[] = "data-start='{$element_full['starttime']}'";
    $output[] = "data-stop='{$element_full['stoptime']}'";
    if ($element_full['startani'] != 'none' && $element_full['startani'] != '') {
      $output[] = "data-easein='{$element_full['startani']}'";
    }
    if ($element_full['stopani'] != 'none' && $element_full['stopani'] != '') {
      $output[] = "data-easeout='{$element_full['stopani']}'";
    }
    if (isset($element_full['paddingtop'])) {
      $output[] = "data-paddingtop='{$element_full['paddingtop']}'";
    }
    if (isset($element_full['paddingright'])) {
      $output[] = "data-paddingright='{$element_full['paddingright']}'";
    }
    if (isset($element_full['paddingbottom'])) {
      $output[] = "data-paddingbottom='{$element_full['paddingbottom']}'";
    }
    if (isset($element_full['paddingleft'])) {
      $output[] = "data-paddingleft='{$element_full['paddingleft']}'";
    }
    return implode(' ', $output);
  }

/**
 * Prepare class for layers
 */
function md_slider_prepare_layers_class($slid, &$slides) {
  foreach ($slides as $slide_index => $slide) {
    foreach ($slide->layers as $layer_index => &$layer) {
      if (isset($layer['style']))
        $layer['style'] = implode(' ', array($layer['style'], "md-layer-{$slid}-{$slide_index}-{$layer_index}"));
      else
        $layer["style"] = "md-layer-{$slid}-{$slide_index}-{$layer_index}";
    }
  }
}
