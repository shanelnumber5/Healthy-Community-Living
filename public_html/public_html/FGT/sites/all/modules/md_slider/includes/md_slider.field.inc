<?php
/**
 * @file: md_slider.field.inc
 * @author: MegaDrupal
 */

/**
 * implements hook_field_info()
 */
function md_slider_field_info() {
  return array(
    "md_slider_field" => array(
      "label" => t("MD Slider Field"),
      "description" => t("Allow add mega slider in node as node field."),
      "default_widget" => "md_slider_widget",
      "default_formatter" => "md_slider_default",
      "settings" => array(),
      "instance_settings" => array(),
    ),
  );
}

/**
 * implements hook_field_widget_info()
 */
function md_slider_field_widget_info() {
  return array(
    "md_slider_widget" => array(
      "label" => t("MD Slider widget"),
      "field types" => array("md_slider_field"),
    ),
  );
}

/**
 * implements hook_field_widget_form()
 */
function md_slider_field_widget_form(&$form, &$form_state, $field, $instance, $lang_code, $items, $delta, $element) {
  if ($instance["widget"]["type"] == "md_slider_widget") {
    $options = array("-1" => t("None"));
    $sliders = MDSlider::get_all();
    $fake_select = '<select class="fake-select-slider" style="display:none">';
    if ($sliders !== FALSE) {
      foreach ($sliders as $slider) {
        $options[$slider->machine_name] = $slider->title;
        $fake_select .= '<option value="' . $slider->machine_name . '" data-sliderid="' . $slider->slid . '"></option>';
      }
    }
    $fake_select .= '</select>';
    $element["md_slider_id"] = array(
      "#type" => "select",
      "#title" => t("Choose slider"),
      "#description" => t("Choose slider in list slider to use with this node"),
      "#default_value" => isset($items[$delta]["md_slider_id"]) ? $items[$delta]["md_slider_id"] : -1,
      "#options" => $options,
      "#required" => $element["#required"],
      "#attributes" => array("class" => array("md-slider-field-select")),
      "#field_suffix" => '<div class="md-slider-field-configure"><div><a href="#" class="configure-link" target="_blank">'.t("Configure").'</a></div><div><a href="#" class="edit-link" target="_blank">'.t("Edit").'</a></div></div>'
    );
    $element['md_fake_select'] = array(
      '#markup' => $fake_select,
    );
    drupal_add_js(drupal_get_path("module", "md_slider") . "/js/md-slider-field-configure.js");
  }

  return $element;
}

/**
 * implements hook_field_is_empty()
 */
function md_slider_field_is_empty($item, $field) {
  if ($item["md_slider_id"] == -1 )
    return TRUE;

  return FALSE;
}

/**
 * implements hook_field_presave()
 */
function md_slider_field_presave($entity_type, $entity, $field, $instance, $lang_code, &$items) {
  foreach ($items as $delta => $item) {
    if (isset($item["md_slider_id"]) && $item["md_slider_id"] != "-1")
      $items[$delta]["md_slider_id"] = $item["md_slider_id"];
  }
}

/**
 * implements hook_field_formatter_info()
 */
function md_slider_field_formatter_info() {
  return array(
    "md_slider_default" => array(
      "label" => t("Default"),
      "field types" => array("md_slider_field"),
    )
  );
}

/**
 * implements hook_field_formatter_view()
 */
function md_slider_field_formatter_view($entity_type, $entity, $field, $instance, $lang_code, $items, $display) {
  $elements  = array();

  if ($display["type"] == "md_slider_default") {
    foreach ($items as $delta => $item) {
      if (isset($item["md_slider_id"])) {
        $content = md_slider_view($item["md_slider_id"], "field");
        $edit_link = l(t("Edit MD Slide"), url("admin/structure/md-slider/{$item["md_slider_id"]}/edit", array("absolute" => TRUE, "query" => drupal_get_destination())));
        $configure_link = l(t("Configure MD Slide"), url("admin/structure/md-slider/{$item["md_slider_id"]}/edit", array("absolute" => TRUE, "query" => drupal_get_destination())));

        if (user_access("administer md_slider") || user_access("delete md_slider"))
          $content = "<div class=\"contextual-links-region\">
                        <div class=\"contextual-links-wrapper\">
                          <ul class=\"contextual-links\">
                              <li class=\"edit_slider\">{$edit_link}</li>
                              <li class=\"configure_slider last\">{$configure_link}</li>
                          </ul>
                        </div>
                        {$content}
                    </div>";
        $elements[$delta]["#markup"] = $content;
      }

    }
  }

  return $elements;
}
