<?php
/**
* Implements hook_views_default_views().
*/
function content_timeline_views_default_views() {
$export = array();

  $view = new view();
  $view->name = 'timeline';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Timeline';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Timeline';
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'content_timeline';
  $handler->display->display_options['style_options']['general_options_scroll_speed'] = '500';
  $handler->display->display_options['style_options']['general_options_close_item_on_transition'] = 0;
  $handler->display->display_options['style_options']['general_options_hide_years'] = 0;
  $handler->display->display_options['style_options']['global_styling_options_timeline_width'] = '920';
  $handler->display->display_options['style_options']['global_styling_options_item_margin'] = '20';
  $handler->display->display_options['style_options']['global_styling_options_item_height'] = '360';
  $handler->display->display_options['style_options']['global_styling_options_hide_timeline'] = 0;
  $handler->display->display_options['style_options']['global_styling_options_hide_navigation'] = 0;
  $handler->display->display_options['style_options']['global_styling_options_nav_style'] = 'darkNav';
  $handler->display->display_options['row_plugin'] = 'content_timeline';
  $handler->display->display_options['row_options']['item_styling_options_width'] = '240';
  $handler->display->display_options['row_options']['item_styling_options_content_padding'] = '20';
  $handler->display->display_options['row_options']['item_styling_options_image_border_width'] = '5';
  $handler->display->display_options['row_options']['item_styling_options_show_date'] = 1;
  $handler->display->display_options['row_options']['item_styling_options_title_line_height'] = '24';
  $handler->display->display_options['row_options']['item_styling_options_title_font_size'] = '24';
  $handler->display->display_options['row_options']['item_styling_options_text_line_height'] = '12';
  $handler->display->display_options['row_options']['item_styling_options_text_font_size'] = '12';
  $handler->display->display_options['row_options']['item_open_styling_options_width'] = '490';
  $handler->display->display_options['row_options']['item_open_styling_options_content_padding'] = '20';
  $handler->display->display_options['row_options']['item_open_styling_options_image_border_width'] = '5';
  $handler->display->display_options['row_options']['item_open_styling_options_title_line_height'] = '24';
  $handler->display->display_options['row_options']['item_open_styling_options_title_font_size'] = '24';
  $handler->display->display_options['row_options']['item_open_styling_options_text_line_height'] = '12';
  $handler->display->display_options['row_options']['item_open_styling_options_text_font_size'] = '12';
  /* Field: Content: Title */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  /* Field: Content: Item Image */
  $handler->display->display_options['fields']['timeline_item_image']['id'] = 'timeline_item_image';
  $handler->display->display_options['fields']['timeline_item_image']['table'] = 'field_data_timeline_item_image';
  $handler->display->display_options['fields']['timeline_item_image']['field'] = 'timeline_item_image';
  $handler->display->display_options['fields']['timeline_item_image']['click_sort_column'] = 'fid';
  $handler->display->display_options['fields']['timeline_item_image']['settings'] = array(
    'image_style' => 'timeline_item',
    'image_link' => '',
  );
  /* Field: Content: Item Title */
  $handler->display->display_options['fields']['timeline_item_title']['id'] = 'timeline_item_title';
  $handler->display->display_options['fields']['timeline_item_title']['table'] = 'field_data_timeline_item_title';
  $handler->display->display_options['fields']['timeline_item_title']['field'] = 'timeline_item_title';
  /* Field: Content: Item Text */
  $handler->display->display_options['fields']['timeline_item_text']['id'] = 'timeline_item_text';
  $handler->display->display_options['fields']['timeline_item_text']['table'] = 'field_data_timeline_item_text';
  $handler->display->display_options['fields']['timeline_item_text']['field'] = 'timeline_item_text';
  /* Field: Content: Read More Link */
  $handler->display->display_options['fields']['timeline_read_more_link']['id'] = 'timeline_read_more_link';
  $handler->display->display_options['fields']['timeline_read_more_link']['table'] = 'field_data_timeline_read_more_link';
  $handler->display->display_options['fields']['timeline_read_more_link']['field'] = 'timeline_read_more_link';
  /* Field: Content: Open Item Image */
  $handler->display->display_options['fields']['timeline_o_item_image']['id'] = 'timeline_o_item_image';
  $handler->display->display_options['fields']['timeline_o_item_image']['table'] = 'field_data_timeline_o_item_image';
  $handler->display->display_options['fields']['timeline_o_item_image']['field'] = 'timeline_o_item_image';
  $handler->display->display_options['fields']['timeline_o_item_image']['click_sort_column'] = 'fid';
  $handler->display->display_options['fields']['timeline_o_item_image']['settings'] = array(
    'image_style' => 'timeline_item_open',
    'image_link' => '',
  );
  /* Field: Content: Open Item Title */
  $handler->display->display_options['fields']['timeline_o_item_title']['id'] = 'timeline_o_item_title';
  $handler->display->display_options['fields']['timeline_o_item_title']['table'] = 'field_data_timeline_o_item_title';
  $handler->display->display_options['fields']['timeline_o_item_title']['field'] = 'timeline_o_item_title';
  /* Field: Content: Open Item Text */
  $handler->display->display_options['fields']['timeline_o_item_text']['id'] = 'timeline_o_item_text';
  $handler->display->display_options['fields']['timeline_o_item_text']['table'] = 'field_data_timeline_o_item_text';
  $handler->display->display_options['fields']['timeline_o_item_text']['field'] = 'timeline_o_item_text';
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  /* Filter criterion: Content: Published */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 1;
  $handler->display->display_options['filters']['status']['group'] = 1;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Filter criterion: Content: Type */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'timeline' => 'timeline',
  );

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page');
  $handler->display->display_options['path'] = 'timeline';
  $handler->display->display_options['menu']['title'] = 'Timeline';
  $handler->display->display_options['menu']['weight'] = '0';
  $handler->display->display_options['menu']['name'] = 'main-menu';
  $handler->display->display_options['menu']['context'] = 0;
  $handler->display->display_options['menu']['context_only_inline'] = 0;

$export['timeline'] = $view;

return $export;
}