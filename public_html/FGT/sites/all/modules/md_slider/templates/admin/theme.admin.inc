<?php
/**
 * @file: theme.admin.inc
 * User: Duy
 * Date: 1/23/13
 * Time: 3:45 PM
 */

/**
 * Implements hook_preprocess for slider_edit_form
 */
function template_preprocess_slider_edit_form(&$variables)
{
    $slider = $variables['slider'];
    $slider->settings['delay'] = $slider->settings['delay'] / 100;

    $variables['class_fullwidth'] = "";
    if ($slider->settings['full_width'])
        $variables['class_fullwidth'] = " md-fullwidth";

    $slides = MDSlide::get_by_slider_id($slider->slid);
    $variables['slides'] = $slides;

    $css_file = md_slider_load_css_file($slider->slid);
    $variables['style_options'] = get_style_options($css_file);
    $variables['default_bg'] = file_create_url("public://md_slider_def_bg.jpg");

    $google_fonts = $slider->settings['dmf_google'];
    if ($google_fonts != '') {
        drupal_add_css($google_fonts, array('type' => 'external'));
        $fonts = md_slider_process_google_web_font($google_fonts);
        $variables['fonts'] = $fonts;
    } else {
        $variables['fonts'] = array();
    }
}

/**
 * Implements hook_preprocess for admin_layers_render theme
 */
function template_preprocess_admin_layers_render(&$variables) {
    $layer = $variables['layer'];
    $data = array();

    foreach ($layer as $attr => $value) {
        if ($attr == 'link') {
            $link_val = drupal_json_encode($value);
            $data[] = "data-link='{$link_val}'";
            continue;
        }
        if ($attr == 'thumb' && !empty($value) && isset($layer['fileid'])) {
            $image = file_load($layer['fileid']);
            if ($image) {
                $thumb = file_create_url($image->uri);
                $data[] = "data-thumb='{$thumb}'";
            } else {
                $data[] = "data-thumb=''";
            }
            continue;
        }
        if ($attr == "title")
          $value = htmlentities($value, ENT_QUOTES, "UTF-8");
        $data[] = "data-{$attr}=\"{$value}\"";
    }

    $variables['data'] = implode(' ', $data);

    if ($layer['type'] == 'image') {
        $layer['file_url'] = '';
        if (isset($layer['fileid'])) {
            $image = file_load($layer['fileid']);
            $layer['file_url'] = file_create_url($image->uri);
        }
    }

    $variables['layer'] = $layer;

    $style = array();
    $style[] = "z-index: {$layer['zindex']};";
    $style[] = "width: {$layer['width']}px;";
    $style[] = "height: {$layer['height']}px;";
    $style[] = "top: {$layer['top']}px;";
    $style[] = "left: {$layer['left']}px;";

    if ($layer["type"] == "text" && isset($layer["fontweight"]) && $layer["fontweight"] != "") {
        if (is_numeric($layer["fontweight"]) === FALSE) {
            $font_w = substr($layer["fontweight"], 0, 3);
            $font_s = substr($layer["fontweight"], 3);
            $style[] = "font-weight: {$font_w};\n";
            $style[] = "font-style: {$font_s};\n";
        }
        else
            $style[] = "font-weight: {$layer["fontweight"]};\n";
    }
    $variables['styles'] = implode(' ', $style);
}

/**
 * Get style options from css file style
 */
function get_style_options($file_path)
{
    $output = array();
    $file = fopen($file_path, 'r');
    $is_data = FALSE;
    if ($file !== FALSE) {
        while (!feof($file)) {
            $line = fgets($file);
            if (!$is_data) {
                if (strpos($line, '==start==') !== FALSE) {
                    $is_data = TRUE;
                    continue;
                }
            } else {
                if (strpos($line, '==end==') === FALSE) {
                    $line = explode('|', $line);
                    $output[$line[0]] = $line[1];
                } else {
                    break;
                }
            };
        }
    }

    fclose($file);
    return $output;
}

/**
 * Implements hook template_preprocess_admin_slides_render()
 */
function template_preprocess_admin_slides_render(&$variables) {
    $slides = $variables['slides'];
    $slider = $variables['slider'];

    $variables['class_fullwidth'] = "";
    if ($slider->settings['full_width'])
        $variables['class_fullwidth'] = " md-fullwidth";

    foreach ($slides as &$slide) {
        $slide->settings["slide_id"] = $slide->sid;
        if ($slide->settings['background_image'] != -1 && ($background = file_load($slide->settings['background_image']))) {
            if ($slider->settings['create_bg_imgstyle'])
                $slide->background_url = image_style_url('md_slider_' . $slider->slid . '_bg', $background->uri);
            else
                $slide->background_url = file_create_url($background->uri);
        }
        else
            $slide->background_url = "";

      $slide->background_style = array("height: {$slider->settings["height"]}px");
      if (!$slider->settings["full_width"])
        $slide->background_style[] = "width:{$slider->settings["width"]}px";
      if (empty($slide->background_url) && isset($slide->settings["background_color"]) && $slide->settings["background_color"] != "")
        $slide->background_style[] = "background-color: {$slide->settings["background_color"]}";
    }
}
