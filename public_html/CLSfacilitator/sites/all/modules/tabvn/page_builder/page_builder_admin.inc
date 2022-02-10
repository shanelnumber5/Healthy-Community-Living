<?php

/*
 * Copyright by @Tabvn: http://envato.tabvn.com, Do not copy or use without our permission!
 */

function page_builder_admin_index() {

  $header = array(
      'title' => array('data' => t('Title'), 'field' => 'p.page_title'),
      'status' => array('data' => t('Status'), 'field' => 'p.active'),
      'language' => array('data' => t('Language'), 'field' => 'p.language'),
      'Created' => array('data' => t('Created'), 'field' => 'p.created', 'sort' => 'desc'),
      'Updated' => array('data' => t('Updated'), 'field' => 'p.updated', 'sort' => 'desc')
  );

  $header['operations'] = array('data' => t('Operations'));

  $query = db_select('pagebuilder', 'p')->extend('PagerDefault')->extend('TableSort');
  $pages = $query
          ->fields('p')
          ->limit(50)
          ->orderByHeader($header)
          ->execute()
          ->fetchAll();

  $options = array();
  $destination = drupal_get_destination();
  if (!empty($pages)) {
    foreach ($pages as $p) {

      $data_unserialize = unserialize($p->data);
      $language_name = t('Language neutral');

      if (function_exists('locale_language_name') && module_exists('i18n') && isset($data_unserialize['language']) && $data_unserialize != LANGUAGE_NONE) {
        $language_name = locale_language_name($data_unserialize['language']);
      }
      $l_options = array();
      $options[$p->id] = array(
          'title' => array(
              'data' => array(
                  '#type' => 'link',
                  '#title' => $p->page_title,
                  '#href' => 'page/' . $p->id,
                  '#options' => $l_options,
              ),
          ),
          'status' => $p->active ? t('published') : t('not published'),
          'language' => $language_name,
          'created' => format_date($p->created, 'short'),
          'Updated' => !empty($p->updated) ? format_date($p->updated, 'short') : 'Unchanged',
      );


      $operations = array();
      $operations['edit'] = array(
          'title' => t('edit'),
          'href' => 'admin/page-builder/edit/' . $p->id,
              // 'query' => $destination,
      );
      $operations['delete'] = array(
          'title' => t('delete'),
          'href' => 'admin/page-builder/delete/' . $p->id,
          'query' => $destination,
      );
      $operations['export'] = array(
          'title' => t('export'),
          'href' => 'admin/page-builder/export/' . $p->id,
          'query' => $destination,
      );
      $options[$p->id]['operations'] = array();
      if (count($operations) > 1) {
        // Render an unordered list of operations links.
        $options[$p->id]['operations'] = array(
            'data' => array(
                '#theme' => 'links__node_operations',
                '#links' => $operations,
                '#attributes' => array('class' => array('links', 'inline')),
            ),
        );
      } elseif (!empty($operations)) {
        // Render the first and only operation as a link.
        $link = reset($operations);
        $options[$p->id]['operations'] = array(
            'data' => array(
                '#type' => 'link',
                '#title' => $link['title'],
                '#href' => $link['href'],
                '#options' => array('query' => $link['query']),
            ),
        );
      }
    }
  }

  $form['pages'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $options,
      '#empty' => t('No content available.'),
  );

  $form['pager'] = array('#markup' => theme('pager'));

  return $form;
}

function page_builder_admin_import() {

  return drupal_get_form('page_builder_admin_import_form');
}

/**
 * Form import page builder from export code.
 */
function page_builder_admin_import_form($form, &$form_state) {

  $form['page_builder_data'] = array(
      '#type' => 'textarea',
      '#title' => t('Data'),
      '#description' => t('Enter your string of data that you have exported before.'),
      '#required' => TRUE,
  );
  $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Import'),
  );

  return $form;
}

function page_builder_admin_import_form_submit($form, &$form_state) {
  $values = $form_state['values'];

  $data = @unserialize(base64_decode($values['page_builder_data']));
  $page_title = t('Clone of');
  $page_title .= isset($data->page_title) ? ' ' . $data->page_title : '';
  $show_title = isset($data->show_title) ? $data->show_title : 1;
  $active = isset($data->active) ? $data->active : 1;
  if (!empty($data->data)) {
    $page_id = db_insert('pagebuilder')
            ->fields(array(
                'page_title' => $page_title,
                'show_title' => $show_title,
                'active' => $active,
                'data' => $data->data,
                'created' => REQUEST_TIME,
            ))
            ->execute();
    $page = page_builder_load($page_id);
    _page_builder_admin_file_upload($page);
    module_invoke_all('page_builer_insert', $page);

    $form_state['redirect'] = 'admin/page-builder/edit/' . $page_id;
  } else {

    drupal_set_message(t('Unable import the page.'), 'error');
  }
}

function page_builder_admin_export($page) {
  drupal_set_title(t('<em>Export </em> @title', array('@title' => $page->page_title)), PASS_THROUGH);


  $form['textarea'] = array(
      '#type' => 'textarea',
      '#title' => t('Copy this'),
      '#value' => base64_encode(serialize($page)),
  );

  return $form;
}

function page_builder_admin_add() {
  _page_builder_admin_add_css();
  $page = (object) array('language' => LANGUAGE_NONE);
  $form = drupal_get_form('page_builder_admin_add_form', $page);

  return $form;
}

function page_builder_admin_edit($page) {
  _page_builder_admin_add_css();
  drupal_set_title(t('<em>Edit </em> @title', array('@title' => $page->page_title)), PASS_THROUGH);
  $form = drupal_get_form('page_builder_admin_add_form', $page);

  return $form;
}

function _page_builder_admin_load_all() {
  $result = db_select('pagebuilder', 'p')->fields('p')->execute()->fetchAll();
  return $result;
}

function page_builder_admin_delete_confirm($form, &$form_state, $page) {
  $form['#page'] = $page;
  // Always provide entity id in the same form key as in the entity edit form.
  $form['page_id'] = array('#type' => 'value', '#value' => $page->id);
  return confirm_form($form, t('Are you sure you want to delete %title?', array('%title' => $page->page_title)), 'page/' . $page->id, t('This action cannot be undone.'), t('Delete'), t('Cancel')
  );
}

function page_builder_admin_delete_confirm_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    $page = page_builder_load($form_state['values']['page_id']);
    if (page_builder_admin_delete($form_state['values']['page_id'])) {
      cache_clear_all();
      watchdog('pagebuilder', 'Pagebuilder: deleted %title.', array('%title' => $page->page_title));
      drupal_set_message(t('%title has been deleted.', array('%title' => $page->page_title)));
    }
  }
  $form_state['redirect'] = '<front>';
}

function page_builder_admin_delete($page_id) {
  $confirm = FALSE;
  if (!empty($page_id)) {
    $page = page_builder_load($page_id);
    if (isset($page->id)) {
      $confirm = TRUE;
      db_delete('pagebuilder')
              ->condition('id', $page_id)
              ->execute();

      // now delete path alias
      path_delete(array('source' => 'page/' . $page_id));
      _page_builder_admin_delete_file($page);
      module_invoke_all('page_builer_delete', $page);
    } else {
      drupal_set_message(t('The page you want to delete does not exist.'));
    }
  }
  return $confirm;
}

function _page_builder_admin_delete_file($page) {
  $data = unserialize($page->data);
  $rows = isset($data['rows']) ? $data['rows'] : array();
  if (!empty($rows)) {
    foreach ($rows as $row_id => $row_val) {
      if (!empty($row_val['row_settings']['background_image'])) {

        $file = file_load($row_val['row_settings']['background_image']);
        if ($file) {
          // Change status to permanent.
          file_delete($file, TRUE);
        }
      }
    }
  }
}

function _page_builder_admin_add_css() {
  drupal_add_js(array('page_builder' => array('reloadElements' => false)), 'setting');
  // add libraries jquery UI
  drupal_add_library('system', 'ui');
  drupal_add_library('system', 'farbtastic');
  drupal_add_library('system', 'ui.sortable');
  drupal_add_library('system', 'ui.draggable');
  drupal_add_library('system', 'ui.droppable');
  drupal_add_library('system', 'ui.resizable');
  drupal_add_library('system', 'ui.dialog');

  $path = drupal_get_path('module', 'page_builder');
  drupal_add_css($path . '/css/page_builder_admin_grid.css');
  drupal_add_css($path . '/css/page_builder_admin.css');
  // drupal_add_js($path . '/lib/tinymce/js/tinymce/tinymce.min.js');
  drupal_add_js($path . '/lib/tinymce/jscripts/tiny_mce/tiny_mce.js');
  drupal_add_js($path . '/js/page_builder_admin.js');
  $settings['page_builder_editor'] = array(
      'imce_url' => false,
  );
  if (module_exists('imce')) {
    $editor = 'tinymce';
    $settings['page_builder_editor']['imce_url'] = url('imce', array('query' => array('app' => $editor . '|url@')));
  }
  drupal_add_js($settings, 'setting');
}

function page_builder_admin_add_form($form, &$form_state, $page) {

  $form['#page'] = $page;
  if (!empty($form['#page']->id) && (!$form_state['submitted'])) {
    $data = unserialize($form['#page']->data);
    $form_state['num_rows'] = isset($data['rows']) ? $data['rows'] : array();
    $form_state['columns_arr'] = isset($data['columns']) ? $data['columns'] : array();
    $form_state['elements_arr'] = isset($data['elements']) ? $data['elements'] : array();
//    dsm($data);
  }




  $form['page_builder'] = array(
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#title' => FALSE, // t('Page builder'),
      '#attributes' => array('class' => array('page-builder-wrapper')),
  );
  $form['page_builder']['rows'] = array(
      '#type' => 'markup',
      '#prefix' => '<div id="rows-wrapper" class="page-builder-rows-items-wrapper">',
      '#suffix' => '</div>',
  );


  $form['page_builder']['modal_block_settings'] = _page_builder_modal_block_settings();
  $form['page_builder']['modal_global_textarea_settings'] = _page_builder_modal_global_textarea_settings();
  $form['page_builder']['modal_global_node_settings'] = _page_builder_modal_node_settings();

  $num_rows = !empty($form_state['num_rows']) ? $form_state['num_rows'] : array();


  $page_builder_rows_links = _page_builder_admin_rows_links();

  if (!empty($num_rows)) {
    uasort($num_rows, 'drupal_sort_weight'); // sorted rows
    foreach ($num_rows as $i => $val) {
      $settings = isset($val['row_settings']) ? $val['row_settings'] : array();

      $row_id = $i;
      $form['page_builder']['rows']['#tree'] = TRUE;
      $form['page_builder']['rows'][$i] = array(
          '#type' => 'markup',
          '#prefix' => '<div class="page-builder-row-item">',
          '#suffix' => '</div>',
      );
      $form['page_builder']['rows'][$i]['links'] = array(
          '#markup' => $page_builder_rows_links,
      );

      $form['page_builder']['rows'][$i]['row_settings'] = array(
          '#type' => 'fieldset',
          '#title' => t('Row settings'),
          '#prefix' => '<div id="page-builder-row-settings-' . $i . '" class="page-builder-row-settings">',
          '#suffix' => '</div>',
      );
      $form['page_builder']['rows'][$i]['row_settings']['section_title'] = array(
          '#title' => t('Title'),
          '#type' => 'textfield',
          '#description' => t('Enter row title.'),
          '#default_value' => isset($settings['section_title']) ? $settings['section_title'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['css_id'] = array(
          '#type' => 'textfield',
          '#title' => t('ID'),
          '#description' => t('Specifies a unique ID for the row section.'),
          '#default_value' => isset($settings['css_id']) ? $settings['css_id'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['css_class'] = array(
          '#type' => 'textfield',
          '#title' => t('Classes'),
          '#description' => t('Enter additional classes to be added to the row section css class.'),
          '#default_value' => isset($settings['css_class']) ? $settings['css_class'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['inner_css_class'] = array(
          '#type' => 'textfield',
          '#title' => t('Inner classes'),
          '#description' => t('Enter additional classes to be added to the inner of row, this is name of class will group all columns in the row. Eg: <strong>container</strong>'),
          '#default_value' => isset($settings['inner_css_class']) ? $settings['inner_css_class'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['css_custom'] = array(
          '#type' => 'textarea',
          '#title' => t('Custom css style'),
          '#description' => t('Enter additional custom css style to be added to the row section. eg: <br /> <em>margin-bottom: 20px; <br /> background-color: #F5F5F5;</em>'),
          '#default_value' => isset($settings['css_custom']) ? $settings['css_custom'] : '',
      );


      /*
        // upload multi background is future features!, it's in active development.
        $form['page_builder']['rows'][$i]['row_settings']['background_image'] = array(
        '#type' => 'container',
        '#tree' => TRUE,
        );

        $background_images = isset($settings['background_image']) ? $settings['background_image'] : array();
        $filecount = 0;
        if (!empty($background_images)) {

        foreach ($background_images as $key => $fid) {
        $form['page_builder']['rows'][$i]['row_settings']['background_image']['files'][$filecount]['image'] = array(
        '#type' => 'managed_file',
        '#title' => t('Background ' . $filecount),
        '#description' => t('Allowed extensions: gif png jpg jpeg'),
        '#default_value' => isset($fid) ? $fid : '',
        '#upload_location' => 'public://',
        '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        'file_validate_size' => array(10 * 3024 * 3024),
        ),
        );
        $filecount++;
        }
        }

        $form['page_builder']['rows'][$i]['row_settings']['background_image']['files'][$filecount]['image'] = array(
        '#type' => 'managed_file',
        '#title' => t('Background'),
        '#description' => t('Allowed extensions: gif png jpg jpeg'),
        '#upload_location' => 'public://',
        '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        'file_validate_size' => array(10 * 3024 * 3024),
        ),
        );
        $form['page_builder']['rows'][$i]['row_settings']['background_image']['new_file'] = array(
        '#type' => 'submit',
        '#value' => t('Add new background'),
        '#ajax' => array(
        'effect' => 'none',
        'wrapper' => 'rows-wrapper',
        'callback' => 'page_builder_admin_upload_row_background_callback',
        ),
        );
       */

      $form['page_builder']['rows'][$i]['row_settings']['background_image'] = array(
          '#type' => 'managed_file',
          '#title' => t('Background'),
          '#description' => t('Allowed extensions: gif png jpg jpeg'),
          '#default_value' => (isset($settings['background_image']) ? $settings['background_image'] : ''),
          '#upload_location' => 'public://',
          '#upload_validators' => array(
              'file_validate_extensions' => array('gif png jpg jpeg'),
              // Pass the maximum file size in bytes
              'file_validate_size' => array(10 * 3024 * 3024),
          ),
      );

      $form['page_builder']['rows'][$i]['row_settings']['use_parallax'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use parallax background'),
          '#default_value' => isset($settings['use_parallax']) ? $settings['use_parallax'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['parallax'] = array(
          '#type' => 'container',
          '#states' => array(
              'visible' => array(
                  ':input[name="rows[' . $i . '][row_settings][use_parallax]"]' => array('checked' => TRUE),
              ),
          ),
      );
      //overlay
      $form['page_builder']['rows'][$i]['row_settings']['parallax']['use_overlay'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use overlay'),
          '#description' => t('use blurred overlay effect.'),
          '#default_value' => isset($settings['parallax']['use_overlay']) ? $settings['parallax']['use_overlay'] : 0,
      );
      $form['page_builder']['rows'][$i]['row_settings']['parallax']['p_position'] = array(
          '#type' => 'select',
          '#title' => t('Position'),
          '#description' => t('Horizontal position of the background'),
          '#options' => array(
              '0' => t('Left'),
              '50%' => t('Middle'),
              '100%' => t('Right'),
          ),
          '#default_value' => isset($settings['parallax']['p_position']) ? $settings['parallax']['p_position'] : '50%',
      );

      $form['page_builder']['rows'][$i]['row_settings']['parallax']['p_speed'] = array(
          '#type' => 'select',
          '#title' => "Relative speed",
          '#description' => "speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling",
          '#default_value' => isset($settings['parallax']['p_speed']) ? $settings['parallax']['p_speed'] : '0.1',
          '#options' => array('0' => '0', '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3, '0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6, '0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1, '1.25' => 1.25, '1.5' => 1.5, '1.75' => 1.75, '2' => 2, '2.5' => 2.5, '3' => 3),
      );


      $form['page_builder']['rows'][$i]['row_settings']['use_video'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use Youtube video in background'),
          '#default_value' => isset($settings['use_video']) ? $settings['use_video'] : '',
      );

      $form['page_builder']['rows'][$i]['row_settings']['video'] = array(
          '#type' => 'container',
          '#states' => array(
              'visible' => array(
                  ':input[name="rows[' . $i . '][row_settings][use_video]"]' => array('checked' => TRUE),
              ),
          ),
      );
      $form['page_builder']['rows'][$i]['row_settings']['video']['videoURL'] = array(
          '#type' => 'textfield',
          '#title' => t('Youtube video URL'),
          '#default_value' => isset($settings['video']['videoURL']) ? $settings['video']['videoURL'] : '',
      );
      $form['page_builder']['rows'][$i]['row_settings']['video']['mute'] = array(
          '#type' => 'checkbox',
          '#title' => t('Mute audio'),
          '#default_value' => isset($settings['video']['mute']) ? $settings['video']['mute'] : 1,
          '#description' => t('Mute the audio.'),
      );
      $form['page_builder']['rows'][$i]['row_settings']['video']['autoPlay'] = array(
          '#type' => 'checkbox',
          '#title' => t('Auto play'),
          '#default_value' => isset($settings['video']['autoPlay']) ? $settings['video']['autoPlay'] : 1,
          '#description' => t('auto play the video.'),
      );

      $form['page_builder']['rows'][$i]['row_settings']['video']['loop'] = array(
          '#type' => 'checkbox',
          '#title' => t('Loop'),
          '#default_value' => isset($settings['video']['loop']) ? $settings['video']['loop'] : 1,
          '#description' => t('loops the video once ended.'),
      );
      $form['page_builder']['rows'][$i]['row_settings']['video']['optimizeDisplay'] = array(
          '#type' => 'checkbox',
          '#title' => t('Optimize Display'),
          '#default_value' => isset($settings['video']['optimizeDisplay']) ? $settings['video']['optimizeDisplay'] : 1,
          '#description' => t('will fit the video size into the window size optimizing the view.'),
      );

      $form['page_builder']['rows'][$i]['row_settings']['video']['showControls'] = array(
          '#type' => 'checkbox',
          '#title' => t('Show controls'),
          '#default_value' => isset($settings['video']['showControls']) ? $settings['video']['showControls'] : 0,
          '#description' => t('Show or hide the controls bar at the bottom of the page.'),
      );

      $form['page_builder']['rows'][$i]['row_settings']['video']['quality'] = array(
          '#title' => t('Quality'),
          '#type' => 'select',
          '#options' => array(
              'default' => 'Default',
              'small' => 'Small',
              'medium' => 'Medium',
              'large' => 'Large',
              'hd720' => 'hd720',
              'hd1080' => 'hd1080',
              'highres' => 'highres',
          ),
          '#default_value' => isset($settings['video']['quality']) ? $settings['video']['quality'] : 'hd720',
      );

      $form['page_builder']['rows'][$i]['row_settings']['use_slider'] = array(
          '#title' => t('Use slider'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['use_slider']) ? $settings['use_slider'] : '',
          '#prefix' => '<div class="page-builder-container-settings">',
          '#description' => t('Show all coumns in this row as slides items.')
      );

      $form['page_builder']['rows'][$i]['row_settings']['slider'] = array(
          '#type' => 'container',
          '#states' => array(
              'visible' => array(
                  ':input[name="rows[' . $i . '][row_settings][use_slider]"]' => array('checked' => TRUE),
              ),
          ),
          '#suffix' => '</div>',
      );

      $form['page_builder']['rows'][$i]['row_settings']['slider']['items'] = array(
          '#type' => 'textfield',
          '#title' => t('Items'),
          '#default_value' => isset($settings['slider']['items']) ? $settings['slider']['items'] : 4,
          '#description' => t('This variable allows you to set the maximum amount of items displayed at a time with the widest browser width.'),
      );

      $form['page_builder']['rows'][$i]['row_settings']['slider']['itemsDesktop'] = array(
          '#title' => t('Items desktop'),
          '#type' => 'textfield',
          '#default_value' => isset($settings['slider']['itemsDesktop']) ? $settings['slider']['itemsDesktop'] : '1000,3',
          '#description' => t('This allows you to preset the number of slides visible with a particular browser width. The format is x,y whereby x=browser width and y=number of slides displayed. For example [1000,3] means that if(window<=1000){ show 3 slides per page}.'),
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['itemsDesktopSmall'] = array(
          '#type' => 'textfield',
          '#title' => t('items desktop small'),
          '#default_value' => isset($settings['slider']['itemsDesktopSmall']) ? $settings['slider']['itemsDesktopSmall'] : '979,3',
          '#description' => t('Default is: 979,3'),
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['itemsTablet'] = array(
          '#type' => 'textfield',
          '#title' => t('Items tablet'),
          '#default_value' => isset($settings['slider']['itemsTablet']) ? $settings['slider']['itemsTablet'] : '768,2',
          '#description' => t('Default is: 768,2'),
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['itemsMobile'] = array(
          '#type' => 'textfield',
          '#title' => t('Items mobile'),
          '#default_value' => isset($settings['slider']['itemsMobile']) ? $settings['slider']['itemsMobile'] : '479,1',
          '#description' => t('Default is: 479,1')
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['autoPlay'] = array(
          '#type' => 'checkbox',
          '#title' => t('Auto play'),
          '#default_value' => isset($settings['slider']['autoPlay']) ? $settings['slider']['autoPlay'] : FALSE,
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['navigation'] = array(
          '#title' => t('Navigation'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['slider']['navigation']) ? $settings['slider']['navigation'] : FALSE,
          '#description' => t('Display "next" and "prev" buttons.')
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['navigationText'] = array(
          '#title' => t('navigation text'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['slider']['navigationText']) ? $settings['slider']['navigationText'] : FALSE,
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['pagination'] = array(
          '#title' => t('Pagination'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['slider']['pagination']) ? $settings['slider']['pagination'] : TRUE,
          '#description' => t('Show pagination.')
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['mouseDrag'] = array(
          '#title' => t('Mouse drag'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['slider']['mouseDrag']) ? $settings['slider']['mouseDrag'] : TRUE,
          '#description' => t('Turn off/on mouse events.')
      );
      $form['page_builder']['rows'][$i]['row_settings']['slider']['touchDrag'] = array(
          '#title' => t('Touch drag'),
          '#type' => 'checkbox',
          '#default_value' => isset($settings['slider']['touchDrag']) ? $settings['slider']['touchDrag'] : TRUE,
          '#description' => t('Turn off/on touch events.')
      );

      $form['page_builder']['rows'][$i]['row'] = array(
          '#type' => 'textfield',
          '#title' => t('Rows'),
          '#default_value' => $i,
          '#attributes' => array('class' => array('page-builder-row-id')),
          '#prefix' => '<div class="page-builder-hidden-item">',
          '#suffix' => '</div>',
      );
      $form['page_builder']['rows'][$i]['weight'] = array(
          '#type' => 'textfield',
          '#title' => t('Row Weight'),
          '#default_value' => $val['weight'],
          '#attributes' => array('class' => array('page-builder-row-weight')),
          '#prefix' => '<div class="page-builder-hidden-item">',
          '#suffix' => '</div>',
      );
      $form['page_builder']['rows'][$i]['markup_prefix'] = array(
          '#markup' => '<div class="page-builder-columns-wrapper row">',
      );
      if (!empty($form_state['columns_arr'][$i])) {
        $columns = $form_state['columns_arr'][$i];
        uasort($columns, 'drupal_sort_weight');
        foreach ($columns as $col_id => $col_val) {
          $col_grid_size = !empty($col_val['settings']['grid_size']) ? $col_val['settings']['grid_size'] : 1;
          $grid_item = 'page-builder-grid-' . $col_grid_size;
          $form['page_builder']['rows'][$i]['column'][$col_id] = array(
              '#type' => 'markup',
              '#prefix' => '<div class="page-builder-column-item ' . $grid_item . '"><div class="page-builder-column-item-inner">',
              '#suffix' => '</div></div>',
          );

          $page_builder_colums_links = _page_builder_admin_columns_links();

          $page_builder_grid_size_title = $col_val['settings']['grid_size'];
          $page_builder_grid_size_title .= '/12';
          $form['page_builder']['rows'][$i]['column'][$col_id]['column_extra'] = array(
              '#markup' => '<div class="page-builder-column-header-item"><span title="' . t('Drag to sort columns') . '" class="page-builder-handle handle-column icon-move"></span><span class="page-builder-grid-size-title">' . $page_builder_grid_size_title . '</span> ' . $page_builder_colums_links . '</div>',
          );

          $form['page_builder']['rows'][$i]['column'][$col_id]['col'] = array(
              '#type' => 'textfield',
              '#title' => t('Column') . ' #' . $col_id,
              '#default_value' => $col_id,
              '#attributes' => array('class' => array('page-builder-column-id')),
              '#prefix' => '<div class="page-builder-hidden-item">',
              '#suffix' => '</div>',
          );

          $form['page_builder']['rows'][$i]['column'][$col_id]['weight'] = array(
              '#type' => 'textfield',
              '#title' => t('Weight'),
              '#default_value' => $col_val['weight'],
              '#attributes' => array('class' => array('page-builder-column-weight')),
              '#prefix' => '<div class="page-builder-hidden-item">',
              '#suffix' => '</div>',
          );

          $form['page_builder']['rows'][$i]['column'][$col_id]['settings']['grid_size'] = array(
              '#type' => 'textfield',
              '#title' => t('Column size'),
              '#default_value' => $col_val['settings']['grid_size'],
              '#attributes' => array('class' => array('page_builder_column_grid_size')),
              '#prefix' => '<div class="page-builder-hidden-item">',
              '#suffix' => '</div>',
          );


          if (!empty($form_state['elements_arr'][$row_id][$col_id])) {
            $element_items = $form_state['elements_arr'][$row_id][$col_id];
            uasort($element_items, 'drupal_sort_weight');

            foreach ($element_items as $e_id => $e_val) {

              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id] = array(
                  '#type' => 'fieldset',
                  '#title' => '<span class="page-builder-handle handle-element"></span><span class="element-title">' . $e_val['title'] . '</span>',
                  '#attributes' => array('class' => array('page-builder-element-item')),
                  '#collapsed' => TRUE,
                  '#collapsible' => FALSE,
              );
              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['element_id'] = array(
                  '#type' => 'textfield',
                  '#title' => t('Element ID'),
                  '#default_value' => $e_id,
                  '#attributes' => array('class' => array('page_builder_element_id')),
              );
              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['title'] = array(
                  '#type' => 'textfield',
                  '#title' => t('Title'),
                  '#default_value' => $e_val['title'],
                  '#attributes' => array('class' => array('page-builder-element-title')),
              );
              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['show_title'] = array(
                  '#type' => 'textfield',
                  '#title' => t('Show title'),
                  '#default_value' => $e_val['show_title'],
                  '#attributes' => array('class' => array('page-builder-element-show-title')),
              );
              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['settings'] = array(
                  '#type' => 'textarea',
                  '#title' => t('Settings'),
                  '#default_value' => $e_val['settings'],
              );

              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['weight'] = array(
                  '#type' => 'textfield',
                  '#title' => t('Weight'),
                  '#default_value' => $e_val['weight'],
                  '#attributes' => array('class' => array('page-builder-element-weight')),
              );
              $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['type'] = array(
                  '#type' => 'textfield',
                  '#title' => t('Type'),
                  '#default_value' => $e_val['type'],
                  '#attributes' => array('class' => array('page-builder-element-type')),
              );

              if (!empty($e_val['type'])) {
                switch ($e_val['type']) {
                  case 'global_text_area':
                    $settings = unserialize($e_val['settings']);
                    $global_text_area_content = !empty($settings['content']) ? $settings['content'] : '';
                    $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['global_text_area'] = array(
                        '#type' => 'textarea',
                        '#title' => t('Content'),
                        '#default_value' => $global_text_area_content,
                        '#attributes' => array('class' => array('page-builder-element-global-text-area')),
                    );

                    break;

                  case 'node':
                    $settings = unserialize($e_val['settings']);
                    $page_builder_element_nid = !empty($settings['nid']) ? $settings['nid'] : '';
                    $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['nid'] = array(
                        '#type' => 'textfield',
                        '#title' => t('Node ID'),
                        '#default_value' => $page_builder_element_nid,
                        '#attributes' => array('class' => array('page-builder-element-nid')),
                    );
                    $page_builder_element_node_view_mode = !empty($settings['view_mode']) ? $settings['view_mode'] : '';
                    $form['page_builder']['rows'][$row_id]['column'][$col_id]['element'][$e_id]['view_mode'] = array(
                        '#type' => 'textfield',
                        '#title' => t('View mode'),
                        '#default_value' => $page_builder_element_node_view_mode,
                        '#attributes' => array('class' => array('page-builder-element-node-view-mode')),
                    );
                    break;

                  default:
                    break;
                }
              }
            }
          }
        }
      }


      $form['page_builder']['rows'][$i]['markup_suffix'] = array(
          '#markup' => '</div>',
      );
    }
  }

  // add row
  $form['page_builder']['add_row'] = array(
      '#type' => 'submit',
      '#value' => t('Add row'),
      '#submit' => array('page_builder_admin_add_row_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_add_row_callback',
          'wrapper' => 'rows-wrapper',
          'method' => 'replace',
          'effect' => 'none',
      ),
      '#attributes' => array('class' => array('page-builder-insert-row')),
  );

  // remove row
  $form['page_builder']['select_row_to_remove'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter row need removed'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  $form['page_builder']['remove_row'] = array(
      '#type' => 'submit',
      '#value' => t('Remove Row'),
      '#submit' => array('page_builder_admin_remove_row_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_remove_row_callback',
          'wrapper' => 'rows-wrapper',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-remove-row')),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  // add column
  $form['page_builder']['select_row_to_add_column'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter row to add column'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['add_column'] = array(
      '#type' => 'submit',
      '#value' => t('Add column'),
      '#submit' => array('page_builder_admin_add_column_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_add_column_callback',
          'wrapper' => 'rows-wrapper',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-add-column')),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  // remove column
  $form['page_builder']['select_column_to_remove'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter column to remove'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['remove_column'] = array(
      '#type' => 'submit',
      '#value' => t('Remove column'),
      '#submit' => array('page_builder_admin_remove_column_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_remove_column_callback',
          'wrapper' => 'rows-wrapper',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-remove-column')),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  // add element
  $form['page_builder']['select_element_info'] = array(
      '#type' => 'textarea',
      '#title' => t('Enter element info'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  $form['page_builder']['select_column_to_add_element'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter column to add element'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['add_element'] = array(
      '#type' => 'submit',
      '#value' => t('Add element'),
      '#submit' => array('page_builder_admin_add_element_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_add_element_callback',
          'wrapper' => 'rows-wrapper',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-add-element')),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );

  $form['page_builder']['select_element_to_remove'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter element to remove'),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['remove_element'] = array(
      '#type' => 'submit',
      '#value' => t('Remove element'),
      '#submit' => array('page_builder_admin_remove_element_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_remove_element_callback',
          'wrapper' => 'rows-wrapper',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-remove-element')),
      '#prefix' => '<div class="page-builder-hidden-item">',
      '#suffix' => '</div>',
  );



  // add node to lists of elements
  $el_wrap_prefix = '<div id="page-builder-elements-items" class="page-builder-elements-items">';
  $el_wrap_prefix.= '<span class="page-builder-reload-elements">';
  $el_wrap_prefix.= '<span class="icon-refresh"></span> Refresh Elements</span>';
  $el_wrap_prefix.= '<div class="page-builder-elements-inner">';
  $el_wrap_prefix.= '<div id="page-builder-filter-list"><div class="list-search-wrapper"><input class="search" placeholder="' . t('Search') . '" /></div>';
  $form['elements'] = array(
      '#type' => 'markup',
      '#title' => FALSE,
      '#prefix' => $el_wrap_prefix,
      '#suffix' => '</div></div></div>',
  );
  $form['elements']['refresh_element'] = array(
      '#type' => 'submit',
      '#value' => 'Refresh elements',
      '#submit' => array('page_builder_admin_refresh_elements_submit'),
      '#ajax' => array(
          'callback' => 'page_builder_admin_refresh_elements_callback',
          'wrapper' => 'page-builder-elements-items',
          'effect' => 'none',
          'method' => 'replace',
      ),
      '#attributes' => array('class' => array('page-builder-hidden-item', 'page-builder-refresh-elements')),
  );
  $form['elements'] += _page_builder_admin_list_elements();

  $form['page_builder']['settings'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#title' => t('Page settings'),
      '#collapsed' => TRUE,
  );


  $path = array();
  if (!empty($form['#page']->id)) {
    $conditions = array('source' => 'page/' . $form['#page']->id);
    $path = path_load($conditions);
    if ($path === FALSE) {
      $path = array();
    }
  }
  $path += array(
      'pid' => NULL,
      'source' => isset($form['#page']->id) ? 'page/' . $form['#page']->id : NULL,
      'alias' => '',
      'language' => isset($langcode) ? $langcode : LANGUAGE_NONE,
  );



  $form['page_builder']['settings']['path'] = array(
      '#tree' => TRUE,
      '#element_validate' => array('page_builder_path_alias_element_validate'),
  );

  $form['page_builder']['settings']['path']['alias'] = array(
      '#type' => 'textfield',
      '#title' => t('URL alias'),
      '#default_value' => $path['alias'],
      '#maxlength' => 255,
      '#description' => t('Optionally specify an alternative URL by which this content can be accessed. For example, type "about" when writing an about page. Use a relative path and don\'t add a trailing slash or the URL alias won\'t work.'),
  );
  $form['page_builder']['settings']['path']['pid'] = array('#type' => 'value', '#value' => $path['pid']);
  $form['page_builder']['settings']['path']['source'] = array('#type' => 'value', '#value' => $path['source']);
  $form['page_builder']['settings']['path']['language'] = array('#type' => 'value', '#value' => $path['language']);


  $form['page_builder']['settings']['page_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Page title'),
      '#default_value' => isset($form['#page']->page_title) ? $form['#page']->page_title : '',
  );


  $form['page_builder']['settings']['show_page_title'] = array(
      '#title' => t('Show page title'),
      '#type' => 'checkbox',
      '#default_value' => isset($form['#page']->show_title) ? $form['#page']->show_title : 1,
  );
  $form['page_builder']['settings']['active'] = array(
      '#title' => t('Published'),
      '#type' => 'checkbox',
      '#default_value' => isset($form['#page']->active) ? $form['#page']->active : 1,
  );
  $form['page_builder']['settings']['global_css'] = array(
      '#type' => 'textarea',
      '#title' => t('Global custom css'),
      '#default_value' => isset($data['global_css']) ? $data['global_css'] : '',
      '#description' => t('Enter your custom css for this page. Eg: <strong>#page-builder-page-14 .page-builder-element-title{color: #FFF;}</strong>'),
  );
  $form['page_builder']['settings']['global_js'] = array(
      '#type' => 'textarea',
      '#title' => t('Global custom javascript'),
      '#default_value' => isset($data['global_js']) ? $data['global_js'] : '',
      '#description' => t('Enter your custom javascript for this page. Eg: <e>$("nav.navbar").addClass("navbar-fixed-top");</e>'),
  );

  if (module_exists('i18n')) {
    $languges_arr = page_builder_language_list('name');

    $form['page_builder']['settings']['language'] = array(
        '#type' => 'select',
        '#title' => t('languages'),
        '#default_value' => isset($data['language']) ? $data['language'] : LANGUAGE_NONE,
        '#options' => $languges_arr,
    );
  }

  $page_builder_save_value = t('Save');

  if (!empty($form['#page']->id)) {
    $page_builder_save_value = t('Update');
  }
  $form['page_builder']['actions']['save'] = array(
      '#type' => 'submit',
      '#value' => $page_builder_save_value,
      '#submit' => array('page_builder_admin_form_save'),
      '#validate' => array('page_builder_admin_form_validation'),
  );
  return $form;
}

function _page_builder_admin_list_elements() {
  $elements = !empty($form_state['elements']) ? $form_state['elements'] : _block_rehash(); // list all blocks into elements array
  // node element
  $form['elements']['e']['node'] = array(
      '#type' => 'item',
      '#title' => t('[Node] Embed a node'),
      '#prefix' => '<div class="page-builder-element"><h3 class="element-title">' . ('Embed a node into column') . '</h3>',
      '#suffix' => '</div>',
  );
  $form['elements']['e']['node']['e_type'] = array(
      '#type' => 'textfield',
      '#title' => t('Element Type'),
      '#default_value' => 'node',
  );
  $node_data = array(
      'type' => 'node',
      'info' => t('Node'),
  );
  $form['elements']['e']['node']['e_data'] = array(
      '#type' => 'textarea',
      '#title' => t('Element Info'),
      '#default_value' => serialize($node_data),
      '#attributes' => array('class' => array('page-builder-element-data')),
  );
  // end node element
  // Global text area
  $form['elements']['e']['global_text_area'] = array(
      '#type' => 'item',
      '#title' => t('[Global] Textarea'),
      '#prefix' => '<div class="page-builder-element"><h3 class="element-title">' . ('Global textarea') . '</h3>',
      '#suffix' => '</div>',
  );

  $form['elements']['e']['global_text_area']['e_type'] = array(
      '#type' => 'textfield',
      '#title' => t('Element Type'),
      '#default_value' => 'global_text_area',
  );
  $global_text_area_data = array(
      'type' => 'global_text_area',
      'info' => t('[Global] textarea'),
  );
  $form['elements']['e']['global_text_area']['e_data'] = array(
      '#type' => 'textarea',
      '#title' => t('Element Info'),
      '#default_value' => serialize($global_text_area_data),
      '#attributes' => array('class' => array('page-builder-element-data')),
  );
  // end global text area

  if (!empty($elements)) {

    foreach ($elements as $key => $b) {
      $form['elements']['e']['#tree'] = TRUE;
      $form['elements']['e'][$key] = array(
          '#type' => 'item',
          '#title' => '[' . t('Block') . '] ' . $b['info'],
          '#prefix' => '<div class="page-builder-element"><h3 class="element-title">' . $b['info'] . '</h3>',
          '#suffix' => '</div>'
      );


      $block_data = array(
          'type' => 'block',
          'module' => $b['module'],
          'title' => $b['title'],
          'delta' => $b['delta'],
          'bid' => $b['bid'],
          'info' => $b['info'],
      );
      $e_type = 'block';
      $form['elements']['e'][$key]['e_type'] = array(
          '#type' => 'textfield',
          '#title' => t('Element Type'),
          '#default_value' => $e_type,
      );
      $form['elements']['e'][$key]['e_data'] = array(
          '#type' => 'textarea',
          '#title' => t('Element Info'),
          '#default_value' => serialize($block_data),
          '#attributes' => array('class' => array('page-builder-element-data')),
      );
      // type__module__delta__bid
      $block_key = $e_type . '__' . $b['module'] . '__' . $b['delta'] . '__' . $b['bid'];
      $form['elements']['e'][$key]['e_key'] = array(
          '#type' => 'textfield',
          '#title' => t('Element key'),
          '#default_value' => $block_key,
      );
    }
  }

  return $form;
}

function page_builder_language_list($field = 'name', $all = FALSE) {
  if ($all) {
    $languages = language_list();
  } else {
    $languages = language_list('enabled');
    $languages = $languages[1];
  }
  $list = array(LANGUAGE_NONE => t('Language neutral'));
  foreach ($languages as $language) {
    $list[$language->language] = ($field == 'name') ? t($language->name) : $language->$field;
  }
  return $list;
}

function page_builder_path_alias_element_validate($element, &$form_state, $complete_form) {

  // Trim the submitted value.
  $alias = trim($form_state['values']['path']['alias']);
  if (!empty($alias)) {
    form_set_value($element['alias'], $alias, $form_state);
    if (isset($form_state['values']['language'])) {
      form_set_value($element['language'], $form_state['values']['language'], $form_state);
    }

    $path = $form_state['values']['path'];

    // Ensure that the submitted alias does not exist yet.
    $query = db_select('url_alias')
            ->condition('alias', $path['alias'])
            ->condition('language', $path['language']);
    if (!empty($path['source'])) {
      $query->condition('source', $path['source'], '<>');
    }
    $query->addExpression('1');
    $query->range(0, 1);
    if ($query->execute()->fetchField()) {
      form_error($element, t('The alias is already in use.'));
    }
  }
}

function page_builder_admin_form_validation($form, &$form_state) {
  if (empty($form_state['values']['page_title'])) {
    form_set_error('page_title', t('Page title is required.'));
  }
}

/**
 * Save data into pagebuilder table
 */
function page_builder_admin_form_save($form, &$form_state) {
  // updates the form data before save into the database.
  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.


  $rows = isset($form_state['num_rows']) ? $form_state['num_rows'] : array();
  $columns = isset($form_state['columns_arr']) ? $form_state['columns_arr'] : array();
  $elements = isset($form_state['elements_arr']) ? $form_state['elements_arr'] : array();

  $data['rows'] = $rows;
  $data['columns'] = $columns;
  $data['elements'] = $elements;
  $data['global_css'] = isset($form_state['values']['global_css']) ? $form_state['values']['global_css'] : '';
  $data['global_js'] = isset($form_state['values']['global_js']) ? $form_state['values']['global_js'] : '';
  $data['language'] = isset($form_state['values']['language']) ? $form_state['values']['language'] : LANGUAGE_NONE;


  $values = $form_state['values'];

  $page_title = $values['page_title'];
  $active = $values['active'];
  $show_title = $values['show_page_title'];

  if (!empty($form['#page']->id)) {
    $page_id = $form['#page']->id;
    $num_updated = db_update('pagebuilder')
            ->fields(array(
                'page_title' => $page_title,
                'show_title' => $show_title,
                'active' => $active,
                'data' => serialize($data),
                'updated' => REQUEST_TIME,
            ))
            ->condition('id', $form['#page']->id)
            ->execute();
    $page = page_builder_load($page_id);

    _page_builder_admin_file_upload($page);
    module_invoke_all('page_builer_update', $page);
  } else {
    $page_id = db_insert('pagebuilder')
            ->fields(array(
                'page_title' => $page_title,
                'show_title' => $show_title,
                'active' => $active,
                'data' => serialize($data),
                'created' => REQUEST_TIME,
            ))
            ->execute();
    $page = page_builder_load($page_id);
    _page_builder_admin_file_upload($page);
    module_invoke_all('page_builer_insert', $page);

    $form_state['redirect'] = 'admin/page-builder/edit/' . $page_id;
  }
  if ($page_id) {
    _page_builder_admin_save_path($page_id, $form, $form_state);

    $language_name = $data['language'];
    $path_alias = drupal_get_path_alias('page/' . $page_id, $data['language']);
    $enabled_languages = language_list();
    if (isset($enabled_languages[$language_name])) {
      $language_object = $enabled_languages[$language_name];
    }

    if (isset($language_object)) {
      drupal_set_message(t('@title has been saved. <a href="@url">Preview</a>', array('@title' => $page->page_title, '@url' => url($path_alias, array('alias' => TRUE, 'language' => $language_object)))));
    } else {
      drupal_set_message(t('@title has been saved. <a href="@url">Preview</a>', array('@title' => $page->page_title, '@url' => url($path_alias))));
    }
  }
}

function _page_builder_admin_save_path($page_id, $form, $form_state) {
  if (isset($form_state['values']['language'])) {
    $langcode = $form_state['values']['language'];
  }
  $path = $form_state['values']['path'];
  $path['alias'] = trim($path['alias']);
  // Only save a non-empty alias.
  if (!empty($path['alias'])) {

    $path['source'] = 'page/' . $page_id;
    $path['language'] = isset($langcode) ? $langcode : LANGUAGE_NONE;
    path_save($path);
  }
}

function page_builder_admin_node_autocomplete($string) {
  $matches = array();
  if ($string) {
    $or = db_or()
            ->condition('nid', $string, '=')
            ->condition('title', db_like($string) . '%', 'LIKE');
    $result = db_select('node')->fields('node', array('nid', 'title'))->condition($or)->range(0, 10)->execute();
    foreach ($result as $node) {
      $matches[$node->nid] = check_plain($node->title);
    }
  }

  drupal_json_output($matches);
}

function _page_builder_modal_block_settings() {
  $form['page_builder']['modal'] = array(
      '#type' => 'markup',
      '#prefix' => '<div id="page-builder-modal-block" class="page-builder-modal page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['modal']['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#default_value' => '',
      '#attributes' => array('class' => array('page-builder-modal-title')),
  );
  $form['page_builder']['modal']['show_title'] = array(
      '#title' => t('Show title'),
      '#type' => 'checkbox',
      '#value' => 1,
      '#attributes' => array('class' => array('page-builder-modal-show-title'))
  );

  return $form;
}

function _page_builder_modal_global_textarea_settings() {
  $form['page_builder']['modal'] = array(
      '#type' => 'markup',
      '#prefix' => '<div id="page-builder-modal-global-textarea" class="page-builder-modal page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['modal']['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#default_value' => '',
      '#attributes' => array('class' => array('page-builder-modal-title')),
  );
  $form['page_builder']['modal']['show_title'] = array(
      '#title' => t('Show title'),
      '#type' => 'checkbox',
      '#value' => 1,
      '#attributes' => array('class' => array('page-builder-modal-show-title'))
  );

  $form['page_builder']['modal']['content'] = array(
      '#type' => 'textarea',
      '#title' => t('Body'),
      '#default_value' => '',
      '#attributes' => array('class' => array('page-builder-modal-content')),
      '#rows' => 10,
      '#resizable' => FALSE,
  );

  return $form;
}

function _page_builder_modal_node_settings() {
  $form['page_builder']['modal'] = array(
      '#type' => 'markup',
      '#prefix' => '<div id="page-builder-modal-node" class="page-builder-modal page-builder-hidden-item">',
      '#suffix' => '</div>',
  );
  $form['page_builder']['modal']['title'] = array(
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#default_value' => '',
      '#attributes' => array('class' => array('page-builder-modal-title')),
  );
  $form['page_builder']['modal']['show_title'] = array(
      '#title' => t('Show title'),
      '#type' => 'checkbox',
      '#value' => 1,
      '#attributes' => array('class' => array('page-builder-modal-show-title'))
  );

  $form['page_builder']['modal']['nid'] = array(
      '#type' => 'textfield',
      '#title' => t('Node ID'),
      '#default_value' => '',
      '#autocomplete_path' => 'page-builder/node-autocomplete',
      '#attributes' => array('class' => array('page-builder-modal-nid')),
  );
  $page_builder_view_modes_options = array();
  $view_modes = _page_builer_get_view_modes('node');

  if (!empty($view_modes)) {
    foreach ($view_modes as $key => $vm) {
      $page_builder_view_modes_options[$key] = $vm['label'];
    }
  }

  $form['page_builder']['modal']['view_mode'] = array(
      '#type' => 'select',
      '#title' => t('View mode'),
      '#options' => $page_builder_view_modes_options,
      '#default_value' => 'full',
      '#attributes' => array('class' => array('page-builder-modal-view-mode')),
  );

  return $form;
}

function _page_builer_get_view_modes($entity_type) {
  $entity_info = entity_get_info($entity_type);
  $view_modes = $entity_info['view modes'];

  return $view_modes;
}

function page_builder_admin_upload_row_background_callback($form, $form_state) {
  return $form['page_builder']['rows'];
}

function page_builder_admin_add_row_callback($form, $form_state) {
  return $form['page_builder']['rows'];
}

function page_builder_admin_add_row_submit($form, &$form_state) {

  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.

  $current_number_rows = count($form_state['num_rows']);
  $last_number_rows = $current_number_rows++;

  $num_of_next_row = _page_buider_get_num_next_row($last_number_rows, $form_state);
  $max_weight = _page_builder_admin_row_max_weight($form_state);
  if (!isset($form_state['num_rows'][0])) { // fix array issue when we delete first array 0
    $form_state['num_rows'][0] = array('weight' => $max_weight);
  } else {
    $form_state['num_rows'][$num_of_next_row] = array('weight' => $max_weight);
  }

  $form_state['rebuild'] = TRUE;
}

function _page_buider_get_num_next_row($last_number_rows, $form_state) {
  $num = $last_number_rows;

  if (!empty($form_state['num_rows'])) {
    for ($i = 0; $i < count($form_state['num_rows']); $i++) {
      if (!isset($form_state['num_rows'][$i])) {
        $num = $i;
        return $num;
      }
    }
  }
  return $num;
}

function page_builder_admin_remove_row_callback($form, $form_state) {
  return $form['page_builder']['rows'];
}

function page_builder_admin_remove_row_submit($form, &$form_state) {

  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.

  if (isset($form_state['values']['select_row_to_remove'])) {
    $row_remove = $form_state['values']['select_row_to_remove'];
    if (isset($form_state['num_rows'][$row_remove])) {
      unset($form_state['num_rows'][$row_remove]);
    }
    if (isset($form_state['columns_arr'][$row_remove])) {
      unset($form_state['columns_arr'][$row_remove]);
    }
  }


  $form_state['rebuild'] = TRUE;
}

function _page_builder_admin_update_row_weight($form_state) {
  $values = $form_state['values'];


  if (!empty($values['rows'])) {
    foreach ($values['rows'] as $key => $val) {
      if (isset($val['weight'])) {
        $form_state['num_rows'][$key]['weight'] = $val['weight'];
      }
    }
  }
  if (isset($form_state['num_rows'])) {
    return $form_state['num_rows'];
  } else {
    return FALSE;
  }
}

function _page_builder_admin_update_columns_weight($form_state) {

  $values = $form_state['values'];
  //$form['page_builder']['rows'][$i]['column'][$col_id]['weight']
  if (!empty($values['rows'])) {
    foreach ($values['rows'] as $row_id => $row_val) {
      if (!empty($row_val['column'])) {
        foreach ($row_val['column'] as $col_id => $col_val) {
          if (isset($col_val['weight'])) {
            $form_state['columns_arr'][$row_id][$col_id]['weight'] = $col_val['weight'];
          }
          // also updates column grid size
          if (isset($col_val['settings']['grid_size'])) {
            $form_state['columns_arr'][$row_id][$col_id]['settings']['grid_size'] = $col_val['settings']['grid_size'];
          }
        }
      }
    }
  }
  if (isset($form_state['columns_arr'])) {
    return $form_state['columns_arr'];
  } else {
    return array();
  }
}

function _page_builder_admin_row_max_weight($form_state) {
  $current_number_rows = !empty($form_state['num_rows']) ? count($form_state['num_rows']) : 0;
  $last_number_rows = $current_number_rows++;
  $max_weight = $last_number_rows;

  if (!empty($form_state['num_rows'])) {
    foreach ($form_state['num_rows'] as $key => $val) {
      if (($val['weight'] >= $max_weight)) {
        $max_weight = ($val['weight']) + 1;
      }
    }
  }

  return $max_weight;
}

function page_builder_admin_add_column_callback($form, $form_state) {
  return $form['page_builder']['rows'];
}

function page_builder_admin_add_column_submit($form, &$form_state) {

  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.

  if (isset($form_state['values']['select_row_to_add_column']) && !empty($form_state['num_rows'])) {
    $row_id = $form_state['values']['select_row_to_add_column'];
    $last_col_in_row = !empty($form_state['columns_arr'][$row_id]) ? count($form_state['columns_arr'][$row_id]) : 0;

    $next_col_in_row = $last_col_in_row++;
    $col_max_weight_in_row = _page_builder_admin_colum_max_weight($form_state);
    if (!isset($form_state['columns_arr'][$row_id][0])) {
      $next_col_in_row = 0;
    }
    $num_of_next_column = _page_builder_get_column_next_row($next_col_in_row, $row_id, $form_state);
    $form_state['columns_arr'][$row_id][$num_of_next_column] = array(
        'weight' => $col_max_weight_in_row,
    );
    $form_state['columns_arr'][$row_id][$num_of_next_column]['settings']['grid_size'] = 1;
  }


  $form_state['rebuild'] = TRUE;
}

function _page_builder_get_column_next_row($current_item, $row_id, $form_state) {
  $num = $current_item;
  if (!empty($form_state['columns_arr'][$row_id])) {
    for ($i = 0; $i < count($form_state['columns_arr'][$row_id]); $i++) {
      if (!isset($form_state['columns_arr'][$row_id][$i])) {
        $num = $i;
        return $num;
      }
    }
  }

  return $num;
}

function _page_builder_admin_colum_max_weight($form_state) {

  $row_id = $form_state['values']['select_row_to_add_column'];
  $current_last_col = !empty($form_state['columns_arr'][$row_id]) ? count($form_state['columns_arr'][$row_id]) : 0;

  $max_weight = $current_last_col++;
  if (!empty($form_state['columns_arr'][$row_id])) {
    foreach ($form_state['columns_arr'][$row_id] as $col_id => $col_val) {
      if ($col_val['weight'] >= $max_weight) {
        $max_weight = ($col_val['weight'] + 1);
      }
    }
  }

  return $max_weight;
}

function page_builder_admin_remove_column_callback($form, $form_state) {

  return $form['page_builder']['rows'];
}

function page_builder_admin_remove_column_submit($form, &$form_state) {
  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.
  // select_column_to_remove field has text like: row_id,column_id
  if (isset($form_state['values']['select_column_to_remove'])) {
    $select_column_to_remove = $form_state['values']['select_column_to_remove'];
    $arr = explode(',', $select_column_to_remove);

    if (isset($arr[0]) && isset($arr[1])) {
      $row_id = trim($arr[0]);
      $col_id = trim($arr[1]);
      if (isset($form_state['columns_arr'][$row_id][$col_id])) {
        unset($form_state['columns_arr'][$row_id][$col_id]);
      }
      if (isset($form_state['elements_arr'][$row_id][$col_id])) {
        unset($form_state['elements_arr'][$row_id][$col_id]);
      }
    }
  }


  $form_state['rebuild'] = TRUE;
}

function page_builder_admin_add_element_callback($form, $form_state) {

  return $form['page_builder']['rows'];
}

function page_builder_admin_add_element_submit($form, &$form_state) {

  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.
  if (isset($form_state['values']['select_column_to_add_element']) && !empty($form_state['values']['select_element_info'])) {
    $select_column_to_add_element = $form_state['values']['select_column_to_add_element'];
    $select_element_info = $form_state['values']['select_element_info'];

    $arr = explode(',', $select_column_to_add_element);
    if (isset($arr[0]) && isset($arr[1])) {
      $row_id = trim($arr[0]);
      $col_id = trim($arr[1]);
      $element_type = _page_builder_get_element_type($select_element_info);
      $element_weight = _page_builder_admin_element_max_weight($form_state);

      switch ($element_type) {
        case 'block':
          $select_element_info_unserial = unserialize($select_element_info);
          $form_state['elements_arr'][$row_id][$col_id][] = array(
              'title' => $select_element_info_unserial['info'],
              'show_title' => 1,
              'settings' => $select_element_info,
              'type' => 'block',
              'weight' => $element_weight,
          );

          break;

        case 'global_text_area':
          $form_state['elements_arr'][$row_id][$col_id][] = array(
              'title' => t('Global text area'),
              'show_title' => 1,
              'settings' => $select_element_info,
              'type' => 'global_text_area',
              'weight' => $element_weight,
          );

          break;
        case 'node':
          $form_state['elements_arr'][$row_id][$col_id][] = array(
              'title' => t('[Node] embed a node'),
              'show_title' => 1,
              'settings' => $select_element_info,
              'type' => 'node',
              'weight' => $element_weight,
          );
          break;

        default:
          break;
      }
    }
  }
  $form_state['rebuild'] = TRUE;
}

function _page_builder_get_element_type($select_element_info) {
  $type = '';
  if (!empty($select_element_info)) {
    $arr = unserialize($select_element_info);

    if (!empty($arr['type'])) {
      $type = $arr['type'];
    }
  }

  return $type;
}

function _page_builder_admin_element_max_weight($form_state) {
  $max_weight = 0;
  if (isset($form_state['values']['select_column_to_add_element'])) {
    $select_column_to_add_element = $form_state['values']['select_column_to_add_element'];
    $arr = explode(',', $select_column_to_add_element);
    if (isset($arr[0]) && isset($arr[1])) {
      $row_id = $arr[0];
      $col_id = $arr[1];

      $current_last_element = !empty($form_state['elements_arr'][$row_id][$col_id]) ? count($form_state['elements_arr'][$row_id][$col_id]) : 0;
      $max_weight = $current_last_element++;
      if (!empty($form_state['elements_arr'][$row_id][$col_id])) {
        foreach ($form_state['elements_arr'][$row_id][$col_id] as $e_id => $e_val) {
          if ($e_val['weight'] >= $max_weight) {
            $max_weight = ($e_val['weight'] + 1);
          }
        }
      }
    }
  }


  return $max_weight;
}

/**
 *
 * @param type $form_state
 *  return array of elements_arr after updated weight from form.
 */
function _page_builder_admin_update_elements_weight($form_state) {

  $values = $form_state['values'];

  if (!empty($values['rows'])) {
    foreach ($values['rows'] as $row_id => $row_val) {
      if (!empty($row_val['column'])) {
        foreach ($row_val['column'] as $col_id => $col_val) {

          if (!empty($col_val['element'])) {
            foreach ($col_val['element'] as $e_id => $e_val) {
              if (isset($e_val['weight'])) {

                $form_state['elements_arr'][$row_id][$col_id][$e_id]['weight'] = $e_val['weight'];
              }
            }
          }
        }
      }
    }
  }
  if (!empty($form_state['elements_arr'])) {
    return $form_state['elements_arr'];
  } else {

    return FALSE;
  }
}

function page_builder_admin_remove_element_callback($form, $form_state) {
  return $form['page_builder']['rows'];
}

/**
 * Form submit remove element
 */
function page_builder_admin_remove_element_submit($form, &$form_state) {

  $form_state['num_rows'] = _page_builder_admin_update_row_weight($form_state);
  $form_state['columns_arr'] = _page_builder_admin_update_columns_weight($form_state);
  $form_state['elements_arr'] = _page_builder_admin_update_elements_weight($form_state);

  _page_builder_admin_update_form($form, $form_state); // updates form elemetns values.

  if (isset($form_state['values']['select_element_to_remove'])) {

    $element_id = $form_state['values']['select_element_to_remove']; // row_id,col_id,$e_id; 
    $arr = explode(',', $element_id);

    if (isset($arr[0]) && isset($arr[1]) && isset($arr[2])) {
      $row_id = trim($arr[0]);
      $col_id = trim($arr[1]);
      $e_id = trim($arr[2]);


      unset($form_state['elements_arr'][$row_id][$col_id][$e_id]);
    }
  }

  $form_state['rebuild'] = TRUE;
}

function _page_builder_admin_rows_links() {
  $html = '<div class="page-builder-top-links"><span class="page-builder-links-wrapper page-builder-rows-links">';
  //$html .= '<a class="page-builder-links-trigger" href="#">' . t('Configure') . '</a>';
  $html .= '<ul class="page-builder-links">';
  $html .= '<li><a class="page-builder-link-add-column">' . t('Add column') . '</a></li>';
  $html .= '<li><a class="page-builder-link-remove-row">' . t('Remove row') . '</a></li>';
  $html .= '</ul>';
  $html .= '</span></div>';

  $html = '<div class="page-builder-rows-links">';
  $html .= '<a title="' . t('Add column') . '" class="page-builder-link-add-column" href="javascript:void(0);"><span class="icon-plus-sign"></span></a>';
  $html .= '<a title="' . t('Remove row') . '" class="page-builder-link-remove-row" href="javascript:void(0);"><span class="icon-minus-sign"></span></a>';
  $html .= '<a title="' . t('Row settings') . '" class="page-builder-link-settings-row" href="javascript:void(0);"><span class="icon-edit"></span></a>';
  $html .= '<a title="' . t('Sort rows') . '" class="page-builder-handle handle-row"><span class="icon-move"></span></a>';
  $html .='</div>';

  return $html;
}

function _page_builder_admin_columns_links() {
  $html = '<span class="page-builder-link-remove-column icon-minus-sign" title="' . t('Remove column') . '"></span>';
  return $html;
}

function page_builder_admin_refresh_elements_submit($form, &$form_state) {

  $form_state['elements'] = _block_rehash();

  $form_state['rebuild'] = TRUE;
}

function page_builder_admin_refresh_elements_callback($form, $form_state) {
  drupal_add_js(array('page_builder' => array('reloadElements' => true)), 'setting');
  return $form['elements'];
}

function _page_builder_admin_update_form($form, &$form_state) {

  //$form_state['form_page_builder_save_status'] = 1; // that mean changes has not saved yet.


  $values = $form_state['values'];
  if (!empty($values['rows'])) {
    foreach ($values['rows'] as $row_id => $row_val) {
      if (!empty($row_val['row_settings'])) {
        foreach ($row_val['row_settings'] as $rs_key => $rs_val) {
          $form_state['num_rows'][$row_id]['row_settings'][$rs_key] = $rs_val;
        }
      }

      if (!empty($row_val['column'])) {
        foreach ($row_val['column'] as $col_id => $col_val) {

          if (!empty($col_val['element'])) {
            foreach ($col_val['element'] as $e_id => $e_val) {
              if (isset($e_val['title'])) {
                $form_state['elements_arr'][$row_id][$col_id][$e_id]['title'] = $e_val['title'];
              }
              if (isset($e_val['show_title'])) {
                $form_state['elements_arr'][$row_id][$col_id][$e_id]['show_title'] = $e_val['show_title'];
              }
              // updates content of global text

              switch ($e_val['type']) {
                case 'global_text_area':
                  $e_settings = unserialize($e_val['settings']);
                  $e_settings['content'] = isset($e_val['global_text_area']) ? $e_val['global_text_area'] : '';
                  $form_state['elements_arr'][$row_id][$col_id][$e_id]['settings'] = serialize($e_settings);

                  break;

                case 'node':
                  $node_e_settings = unserialize($e_val['settings']);
                  $node_e_settings['nid'] = !empty($e_val['nid']) ? $e_val['nid'] : '';
                  $node_e_settings['view_mode'] = !empty($e_val['view_mode']) ? $e_val['view_mode'] : '';
                  $form_state['elements_arr'][$row_id][$col_id][$e_id]['settings'] = serialize($node_e_settings);

                  break;
                default:
                  break;
              }
            }
          }
        }
      }
    }
  }
}

function _page_builder_admin_file_upload($page) {

  $data = unserialize($page->data);
  $rows = isset($data['rows']) ? $data['rows'] : array();
  if (!empty($rows)) {
    foreach ($rows as $row_id => $row_val) {
      if (!empty($row_val['row_settings']['background_image'])) {

        $file = file_load($row_val['row_settings']['background_image']);

        if ($file) {
          // Change status to permanent.
          $file->status = FILE_STATUS_PERMANENT;
          // Save.
          file_save($file);
          // Record that the module (in this example, user module) is using the file.

          file_usage_add($file, 'page_builder', 'page', $page->id);
        }
      }
    }
  }
}
