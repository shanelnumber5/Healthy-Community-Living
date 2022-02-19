<?php

function layerslider_register_settings() {


  // Add slider
  if (isset($_POST['posted_add']) && strstr($_SERVER['REQUEST_URI'], 'layerslider')) {

    // Table name
    $table_name = LAYERSLIDER_TABLE;


    // Create new record
    if ($_POST['layerkey'] == 0) {


      $slider = array();

      $id = db_insert('layerslider')
              ->fields(array(
                  'name' => '',
                  'data' => '',
                  'date_c' => time(),
                  'date_m' => time(),
              ))
              ->execute();
    } else {


      $slider = db_query("SELECT * FROM {layerslider} ORDER BY id DESC LIMIT 1")->fetchAssoc();

      // ID
      $id = $slider['id'];

      $slider = json_decode($slider['data'], true);
    }

    // Add modifications
    if (isset($_POST['layerslider-slides']['properties']['relativeurls'])) {
      $slider['properties'] = $_POST['layerslider-slides']['properties'];
      $slider['layers'][$_POST['layerkey']] = layerslider_convert_urls($_POST['layerslider-slides']['layers'][$_POST['layerkey']]);
    } else {
      $slider['properties'] = $_POST['layerslider-slides']['properties'];
      $slider['layers'][$_POST['layerkey']] = $_POST['layerslider-slides']['layers'][$_POST['layerkey']];
    }


    // DB data
    $name = $slider['properties']['title'];
    $data = json_encode($slider);

    // Update


    db_query("UPDATE {layerslider} SET
					name = :name,
					data = :data,
					date_m = '" . time() . "'
				  ORDER BY id DESC LIMIT 1", array(':name' => $name, ':data' => $data));

    // Echo last ID for redirect
    echo $id;

    // Redirect back
    //header('Location: '.$_SERVER['REQUEST_URI'].'');
    die();
  }

  // Edit slider
  if (isset($_POST['posted_edit']) && strstr($_SERVER['REQUEST_URI'], 'layerslider')) {


    // Table name
    $table_name = LAYERSLIDER_TABLE;

    // Get the IF of the slider
    $id = (int) $_GET['id'];

    // Get slider
    $slider = db_query("SELECT * FROM {layerslider} WHERE id= :ID ORDER BY date_c DESC LIMIT 0,1", array(":ID" => $id))->fetchAssoc();
    $data = json_decode($slider['data'], true);

    // Empty the slider
    if ($_POST['layerkey'] == 0) {
      $data = array();
    }

    // Add modifications
    if (isset($_POST['layerslider-slides']['properties']['relativeurls'])) {
      $data['properties'] = $_POST['layerslider-slides']['properties'];
      $data['layers'][$_POST['layerkey']] = layerslider_convert_urls($_POST['layerslider-slides']['layers'][$_POST['layerkey']]);
    } else {
      $data['properties'] = $_POST['layerslider-slides']['properties'];
      $data['layers'][$_POST['layerkey']] = $_POST['layerslider-slides']['layers'][$_POST['layerkey']];
    }

    // DB data
    $name = $data['properties']['title'];
    $data = json_encode($data);

    // Update
    db_query("UPDATE {layerslider} SET
					name = :name,
					data = :data,
					date_m = '" . time() . "'
				  WHERE id = '$id' LIMIT 1", array(':name' => $name, ':data' => $data));

    // Redirect back
    //header('Location: '.$_SERVER['REQUEST_URI'].'');
    die();
  }

  // Import settings
  if (isset($_POST['import']) && strstr($_SERVER['REQUEST_URI'], 'layerslider')) {

    // Try to get slider data with JSON
    $import = json_decode(base64_decode($_POST['import']), true);

    // Invalid export code
    if (!is_array($import)) {

      // Try to get slider data with PHP unserialize
      $import = unserialize(base64_decode($_POST['import']));

      // Failed to extract the slider data, exit
      if (!is_array($import)) {
        header('Location: ' . $_SERVER['REQUEST_URI'] . '');
        die();
      }
    }


    // Table name
    $table_name = LAYERSLIDER_TABLE;

    // Iterate over imported sliders
    foreach ($import as $item) {

      // Execute query
      db_query("INSERT INTO {layerslider}
									(name, data, date_c, date_m)
								VALUES (:name, :data, :c, :m)", array(':name'=>$item['properties']['title'], ':data'=>json_encode($item), ':c'=>time(), ':m'=>time())
      );
    }

    // Redirect back
    header('Location: ' . $_SERVER['REQUEST_URI'] . '');
    die();
  }

  // Skin Editor
  if (isset($_POST['posted_ls_skin_editor']) && strstr($_SERVER['REQUEST_URI'], 'layerslider')) {

    // GET SKIN
    if (isset($_GET['skin']) && !empty($_GET['skin'])) {
      $skin = $_GET['skin'];
    } else {

      // Open folder
      $handle = opendir(dirname(__FILE__) . '/skins');

      // Iterate over the contents
      while (false !== ($entry = readdir($handle))) {
        if ($entry == '.' || $entry == '..' || $entry == 'preview') {
          continue;
        } else {
          $skin = $entry;
          break;
        }
      }
    }

    // Get file path
    $file = dirname(__FILE__) . '/skins/' . $skin . '/skin.css';

    // Get content
    $content = $_POST['contents'];

    // Write to file
    $status = @file_put_contents($file, $content);

    if (!$status) {
      drupal_set_message('It looks like your files isn\'t writable, so PHP couldn\'t make any changes (CHMOD).', 'LayerSlider', 'error');
      // wp_die(__('It looks like your files isn\'t writable, so PHP couldn\'t make any changes (CHMOD).', 'LayerSlider'), __('Cannot write to file', 'LayerSlider'), array('back_link' => true));
    } else {
      $url = url('admin/layerslider/skin-editor');
      header('Location: ' . $url . '?skin=' . $skin . '&edited=1');
    }
  }
}
