<?php
/**
 * @file
 * Contains the UCXF_Field class.
 */

/**
 * Base class for a Extra Fields Pane field
 */
class UCXF_Field {
  // -----------------------------------------------------------------------------
  // CONSTANTS
  // -----------------------------------------------------------------------------

  // Fieldtypes
  const UCXF_WIDGET_TYPE_SELECT = 1;
  const UCXF_WIDGET_TYPE_CONSTANT = 2;
  const UCXF_WIDGET_TYPE_PHP = 3;
  const UCXF_WIDGET_TYPE_CHECKBOX = 4;
  const UCXF_WIDGET_TYPE_TEXTFIELD = 5;
  const UCXF_WIDGET_TYPE_PHP_SELECT = 6;

  // -----------------------------------------------------------------------------
  // PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * An array of pane types the field is in.
   * @var array
   * @access private
   */
  private $pane_types;

  /**
   * An array of page names on which the field may be displayed
   * @var array
   * @access private
   */
  private $display_settings;

  // -----------------------------------------------------------------------------
  // CONSTRUCT
  // -----------------------------------------------------------------------------

  /**
   * UCXF_Field object constructor
   * @access public
   * @return void
   */
  public function __construct() {
    // Set default values
    $this->weight = 0;
    $this->value_type = self::UCXF_WIDGET_TYPE_TEXTFIELD;
    $this->required = FALSE;
    $this->enabled = TRUE;
    $this->pane_types = array();
    $this->display_settings = array();
  }

  // -----------------------------------------------------------------------------
  // STATIC METHODS
  // -----------------------------------------------------------------------------

  /**
   * Returns an example for the value section
   * @param int $field_type
   *   One of the field types defined by Extra Fields Pane
   * @access public
   * @static
   * @return string
   */
  public static function get_example($field_type) {
    switch ($field_type) {
      case self::UCXF_WIDGET_TYPE_SELECT:
        return '<code>
          &nbsp;|' . t('Please select') . '<br />
          option1|' . t('Option 1') . '<br />
          option2|' . t('Option 2')
           . '</code>';
      case self::UCXF_WIDGET_TYPE_CONSTANT:
        return '<code>' . t('A constant value') . '</code>';
      case self::UCXF_WIDGET_TYPE_PHP:
        return '<code>&lt;?php return "' . t('A string') . '"; ?&gt;</code>';
      case self::UCXF_WIDGET_TYPE_PHP_SELECT:
        return "<code>
          &lt;?php<br />
          return array(<br />
          &nbsp;&nbsp;'' => '" . t('Please select') . "',<br />
          &nbsp;&nbsp;'option1' => '" . t('Option 1') . "',<br />
          &nbsp;&nbsp;'option2' => '" . t('Option 2') . "',<br />
          );<br />
          ?&gt;"
           . "</code>";
    }
  }

  // -----------------------------------------------------------------------------
  // SETTERS
  // -----------------------------------------------------------------------------

  /**
   * Setter
   * @param string $p_sMember
   * @param mixed $p_mValue
   * @access public
   * @return boolean
   */
  public function __set($p_sMember, $p_mValue) {
    switch ($p_sMember) {
      case 'display_settings':
        if (is_string($p_mValue)) {
          $p_mValue = unserialize($p_mValue);
        }
        if (is_array($p_mValue)) {
          foreach ($p_mValue as $option_id => $display_setting) {
            $this->display_settings[$option_id] = ($display_setting) ? TRUE : FALSE;
          }
          return TRUE;
        }
        break;
      case 'pane_type':
        if (is_string($p_mValue)) {
          $pane_types = explode('|', $p_mValue);
          return $this->__set('pane_type', $pane_types);
        }
        elseif (is_array($p_mValue)) {
          $this->pane_types = array();
          foreach ($p_mValue as $pane_type) {
            $pane_type = (string) $pane_type;
            if (!empty($pane_type)) {
              $this->pane_types[$pane_type] = $pane_type;
            }
          }
          return TRUE;
        }
      default:
        $this->{$p_sMember} = $p_mValue;
        return TRUE;
    }
    return FALSE;
  }

  /**
   * Load an existing item from an array.
   * @access public
   * @param array $p_aParams
   */
  function from_array($p_aParams) {
    foreach ($p_aParams as $sKey => $mValue) {
      $this->__set($sKey, $mValue);
    }
  }

  // -----------------------------------------------------------------------------
  // GETTERS
  // -----------------------------------------------------------------------------

  /**
   * Getter
   * @param string $p_sMember
   * @access public
   * @return mixed
   */
  public function __get($p_sMember) {
    switch ($p_sMember) {
      case 'id':
        return $this->__get('field_id');
      case 'pane_type':
        return implode('|', $this->pane_types);
      case 'pane_types':
        return $this->pane_types;
      default:
        if (isset($this->{$p_sMember})) {
          return $this->{$p_sMember};
        }
        break;
    }
    return NULL;
  }

  /**
   * Return as an array of values.
   * @access public
   * @return array
   */
  public function to_array() {
    $aOutput = array();
    // Return fields as specified in the schema.
    $schema = drupal_get_schema('uc_extra_fields');
    if (!empty($schema['fields']) && is_array($schema['fields'])) {
      foreach ($schema['fields'] as $field => $info) {
        $aOutput[$field] = $this->__get($field);
      }
    }
    return $aOutput;
  }

  /**
   * Output a value with filtering
   * @param string $p_sMember
   * @access public
   * @return string
   */
  public function output($p_sMember) {
    switch ($p_sMember) {
      case 'description':
        return filter_xss_admin(uc_extra_fields_pane_tt("field:$this->db_name:description", $this->{$p_sMember}));
      case 'label':
        return check_plain(uc_extra_fields_pane_tt("field:$this->db_name:label", $this->{$p_sMember}));
      case 'pane_type':
        return check_plain($this->__get('pane_type'));
      default:
        if (isset($this->{$p_sMember})) {
          return check_plain($this->{$p_sMember});
        }
    }
    return '';
  }

  /**
   * Output a value based on the field type
   * @param string $p_sValue
   *   The given value
   * @access public
   * @return void
   */
  public function output_value($p_sValue) {
    switch ($this->value_type) {
      case self::UCXF_WIDGET_TYPE_CHECKBOX:
        return ($p_sValue) ? t('Yes') : t('No');
      case self::UCXF_WIDGET_TYPE_SELECT:
      case self::UCXF_WIDGET_TYPE_PHP_SELECT:
        $values = $this->generate_value();
        return (isset($values[$p_sValue])) ? check_plain($values[$p_sValue]) : check_plain($p_sValue);
      default:
        return ($p_sValue != '') ? check_plain($p_sValue) : t('n/a');
    }
  }

  /**
   * Returns the "readable" value type, as a string.
   *
   * @return string
   */
  public function get_value_type() {
    switch ($this->value_type) {
      case UCXF_Field::UCXF_WIDGET_TYPE_TEXTFIELD:
        return t('Textfield');
      case UCXF_Field::UCXF_WIDGET_TYPE_SELECT:
        return t('Select list');
      case UCXF_Field::UCXF_WIDGET_TYPE_CHECKBOX:
        return t('Checkbox');
      case UCXF_Field::UCXF_WIDGET_TYPE_CONSTANT:
        return t('Constant');
      case UCXF_Field::UCXF_WIDGET_TYPE_PHP:
        return t('PHP string');
      case UCXF_Field::UCXF_WIDGET_TYPE_PHP_SELECT:
        return t('PHP select list');
    }
  }

  // -----------------------------------------------------------------------------
  // LOGIC
  // -----------------------------------------------------------------------------

  /**
   * Returns if the field's value may be displayed on te given page.
   *
   * Returns TRUE if the display setting for the given page does not exist.
   *
   * @param string $p_sPage
   * @access public
   * @return boolean
   */
  public function may_display($p_sPage) {
    if (isset($this->display_settings[$p_sPage])) {
      return ($this->display_settings[$p_sPage]) ? TRUE : FALSE;
    }
    return TRUE;
  }

  /**
   * Returns if field is in given pane
   *
   * @param string $p_sPane
   * @access public
   * @return boolean
   */
  public function in_pane($p_sPane) {
    return (isset($this->pane_types[$p_sPane]));
  }

  // -----------------------------------------------------------------------------
  // DATABASE REQUESTS
  // -----------------------------------------------------------------------------

  // Deprecated
  /**
   * load()
   * Loads field from database
   * @param int $p_iField_id
   * @access public
   * @return UCXF_Field
   */
  public function load($p_iField_id) {
    return uc_extra_fields_pane_field_load($p_iField_id);
  }

  /**
   * save()
   * Saves field in database
   * @access public
   * @return void
   */
  public function save() {
    // Prepare values
    $values = $this->to_array();

    $update = array();
    $sHook = 'insert';
    if (!empty($this->field_id)) {
      $update[] = 'field_id';
      $sHook = 'update';
    }
    drupal_write_record('uc_extra_fields', $values, $update);
    $this->field_id = $values['field_id'];

    // Let other modules react on this
    module_invoke_all('ucxf_field', $this, $sHook);
  }

  /**
   * Delete the field from the database.
   * @access public
   * @return boolean
   */
  public function delete() {
    return UCXF_FieldList::deleteField($this);
  }

  // -----------------------------------------------------------------------------
  // FORMS
  // -----------------------------------------------------------------------------

  /**
   * Get the edit form for the field.
   * @access public
   * @return array
   */
  public function edit_form() {
    $form = array('#tree' => TRUE);

    // Add instance of this to the form
    $form['field'] = array(
      '#type' => 'value',
      '#value' => $this,
    );

    if (!empty($this->field_id)) {
      $form['ucxf']['field_id'] = array(
        '#type' => 'hidden',
        '#value' => $this->field_id,
      );
      drupal_set_title(t('Modify field: @name', array('@name' => $this->db_name)), PASS_THROUGH);
    }

    $form['ucxf']['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t('Label shown to customers in checkout pages.'),
      '#required' => TRUE,
      '#default_value' => $this->label,
      '#weight' => 0,
    );
    $default_db_name = $this->db_name;
    if (strpos($default_db_name, 'ucxf_') !== 0) {
      $default_db_name = 'ucxf_';
    }
    $form['ucxf']['db_name'] = array(
      '#title' => t('Field name'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t('Database field name. It must contain only lower chars a-z, digits 0-9 and _. Max allowed length is !number characters. This is inclusive the prefix %prefix.', array('!number' => 32, '%prefix' => 'ucxf_')),
      '#required' => TRUE,
      '#default_value' => $this->db_name,
      '#weight' => 1,
      '#maxlength' => 32,
    );
    if (isset($this->field_id)) {
      // if field already exists, don't allow to alter the name
      $form['ucxf']['db_name']['#disabled'] = 'disabled';
      $form['ucxf']['db_name']['#value'] = $this->db_name;
    }
    $form['ucxf']['description'] = array(
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#rows' => 3,
      '#description' => t('Insert a description to tell customers how to fill this field. ONLY applies for select/textbox options'),
      '#default_value' => $this->description,
      '#weight' => 3,
    );

    $form['ucxf']['weight'] = array(
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#delta' => 30,
      '#default_value' => $this->weight,
      '#description' => t('The listing position to display the order data on checkout/order panes.'),
      '#weight' => 5,
    );
    $form['ucxf']['pane_type'] = array(
      '#title' => t('Select which pane you would like the form value to be hooked into.'),
      '#type' => 'select',
      '#options' => array('extra_information' => t('Extra Information pane')),
      '#default_value' => $this->pane_type,
      '#weight' => 7,
    );

    $value_type_options = array(
      self::UCXF_WIDGET_TYPE_TEXTFIELD => array(
        '#title' => t('Textfield'),
        '#description' => t('Let the user input the data in a textbox. If you want a default value, put it in "value" field below.'),
      ),
      self::UCXF_WIDGET_TYPE_SELECT => array(
        '#title' => t('Select list'),
        '#description' => t('Let the user select from a list of options (enter one <strong>safe_key|Some readable option</strong> per line).'),
      ),
      self::UCXF_WIDGET_TYPE_CHECKBOX => array(
        '#title' => t('Checkbox'),
        '#description' => t('Let the user select from a checkbox.'),
      ),
      self::UCXF_WIDGET_TYPE_CONSTANT => array(
        '#title' => t('Constant'),
        '#description' => t('Show a admin defined constant value, insert the value in the "value" section.'),
      ),
    );
    if (user_access('use php fields')) {
      $value_type_options += array(
        self::UCXF_WIDGET_TYPE_PHP => array(
          '#title' => t('PHP string'),
          '#description' => t('Set the value to the php code that returns a <code>STRING</code> (PHP-mode, experts only).'),
        ),
        self::UCXF_WIDGET_TYPE_PHP_SELECT => array(
          '#title' => t('PHP select list'),
          '#description' => t('Let the user select from a list of options from php code returning a <code>ARRAY</code> of key => value pairs. ie- <code>return array(\'element1\' => \'somevalue1\',\'element2\' => \'somevalue2\')</code> (PHP-mode, experts only).'),
        ),
      );
    }

    $form['ucxf']['value_type'] = array(
      '#type' => 'radios',
      '#title' => t('Field type'),
      '#options' => $value_type_options,
      '#default_value' => $this->value_type,
      '#weight' => 9,
      '#required' => TRUE,
      '#after_build' => array('uc_extra_fields_pane_field_value_type_after_build'),
    );

    $form['ucxf']['value'] = array(
      '#type' => 'textarea',
      '#title' => t('Value'),
      '#description' => t('If the PHP-mode is chosen, enter PHP code between %php. Note that executing incorrect PHP-code can break your Drupal site.', array('%php' => '<?php ?>')),
      '#default_value' => $this->value,
      '#weight' => 10,
    );
    $form['ucxf']['required'] = array(
      '#title' => t('Field required'),
      '#type' => 'checkbox',
      '#description' => t('Check this item if the field is mandatory.'),
      '#default_value' => $this->required,
      '#weight' => 12,
    );
    $form['ucxf']['enabled'] = array(
      '#title' => t('Enabled'),
      '#type' => 'checkbox',
      '#default_value' => $this->enabled,
      '#weight' => 14,
    );
    $display_options = module_invoke_all('ucxf_display_options', $this);
    $form['ucxf']['display_settings'] = array(
      '#title' => t('Display options'),
      '#type' => 'fieldset',
      '#weight' => 16,
      '#description' => t('Choose on which pages you want to display the field.'),
    );
    foreach ($display_options as $option_id => $option) {
      $form['ucxf']['display_settings'][$option_id] = array(
        '#title' => $option_id,
        '#type' => 'checkbox',
        '#default_value' => $this->may_display($option_id),
      );
      foreach ($option as $attribute_name => $attribute_value) {
        switch ($attribute_name) {
          case 'title':
            $form['ucxf']['display_settings'][$option_id]['#title'] = $attribute_value;
            break;
          case 'description':
            $form['ucxf']['display_settings'][$option_id]['#description'] = $attribute_value;
            break;
          case 'weight':
            $form['ucxf']['display_settings'][$option_id]['#weight'] = $attribute_value;
            break;
        }
      }
    }
    $form['ucxf']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#weight' => 50,
    );

    if ($this->returnpath) {
      // Add 'cancel'-link
      $form['ucxf']['submit']['#suffix'] = l(t('Cancel'), $this->returnpath);
    }
    return $form;
  }

  /**
   * Validate the edit form for the item.
   * @param array $form
   * @param array $form_state
   * @access public
   * @return void
   */
  public function edit_form_validate($form, &$form_state) {
    $field = $form_state['values']['ucxf'];

    // No label.
    if (!$field['label']) {
      form_set_error('ucxf][label', t('Custom order field: you need to provide a label.'));
    }
    // No field name.
    if (!$field['db_name']) {
      form_set_error('ucxf][db_name', t('Custom order field: you need to provide a field name.'));
    }
    if (isset($field['weight']) && !$field['weight'] && $field['weight'] !== 0 && $field['weight'] !== '0') {
      form_set_error('ucxf][weight', t('Custom order field: you need to provide a weight value for this extra field.'));
    }
    if (isset($form['ucxf']['pane_type']) && empty($field['pane_type'])) {
      form_set_error('ucxf][pane_type', t('Custom order field: you need to provide a pane-type for this extra field.'));
    }
    if (!$field['value_type']) {
      form_set_error('ucxf][value_type', t('Custom order field: you need to provide a way of processing the value for this field as either textbox, select, constant, or php.'));
    }
    if (($field['value_type'] == self::UCXF_WIDGET_TYPE_CONSTANT || $field['value_type'] == self::UCXF_WIDGET_TYPE_PHP) && !$field['value'] ) {
      form_set_error('ucxf][value', t('Custom order field: you need to provide a value for this way of calculating the field value.'));
    }

    // Field name validation.
    if (empty($field['field_id'])) {
      $field_name = $field['db_name'];
      // Add the 'ucxf_' prefix.
      if (strpos($field_name, 'ucxf_') !== 0) {
        $field_name = 'ucxf_' . $field_name;
        form_set_value($form['ucxf']['db_name'], $field_name, $form_state);
      }
      // Invalid field name.
      if (!preg_match('!^ucxf_[a-z0-9_]+$!', $field_name)) {
        form_set_error('ucxf][db_name', t('Custom order field: the field name %field_name is invalid. The name must include only lowercase unaccentuated letters, numbers, and underscores.', array('%field_name' => $field_name)));
      }
      // considering prefix ucxf_ no more than 32 characters (32 max for a db field)
      if (strlen($field_name) > 32) {
        form_set_error('ucxf][db_name', t('Custom order field: the field name %field_name is too long. The name is limited to !number characters, including the \'ucxf_\' prefix.', array('!number' => 32, '%field_name' => $field_name)));
      }
      // Check if field name already exists in table.
      $count = db_select('uc_extra_fields')
        ->condition('db_name', $field_name)
        ->countQuery()
        ->execute()
        ->fetchField();
      if ((int) $count > 0) {
        form_set_error('ucxf][db_name', t('Custom order field: the field name %field_name already exists.', array('%field_name' => $field_name)));
      }
    }

    // Check if php tags are present in case of a php field
    if ($field['value_type'] == self::UCXF_WIDGET_TYPE_PHP || $field['value_type'] == self::UCXF_WIDGET_TYPE_PHP_SELECT) {
      $php_open_tag_position = stripos($field['value'], '<?php');
      $php_close_tag_position = strripos($field['value'], '?>');
      if ($php_open_tag_position === FALSE || $php_close_tag_position === FALSE || $php_open_tag_position > $php_close_tag_position) {
        form_set_error('ucxf][value', t('The PHP code is not entered between %php.', array('%php' => '<?php ?>')));
        return;
      }
    }

    // Display a warning when select or PHP-select fields are marked as required, but have no option with an empty key
    if (($field['value_type'] == self::UCXF_WIDGET_TYPE_SELECT || $field['value_type'] == self::UCXF_WIDGET_TYPE_PHP_SELECT) && $field['required'] == TRUE) {
      $this->value_type = $field['value_type'];
      $this->value = $field['value'];
      $options = $this->generate_value(FALSE);
      $has_empty_key = FALSE;
      foreach ($options as $key => $value) {
        if ($key === '' || $key === ' ') {
          $has_empty_key = TRUE;
          break;
        }
      }
      if (!$has_empty_key) {
        switch ($field['value_type']) {
          case self::UCXF_WIDGET_TYPE_SELECT:
            $message_suffix = t('In this example the key of the first item is just a single space.');
            break;
          case self::UCXF_WIDGET_TYPE_PHP_SELECT:
            $message_suffix = t('In this example the key of the first item is an empty string.');
            break;
        }
        drupal_set_message(t('The select field %field is marked as required, but there is no "empty" option in the list. Enter an empty option in the value section as in this example: !example', array('%field' => $field['db_name'], '!example' => '<br />' . self::get_example($field['value_type']) . '<br />')) . $message_suffix, 'warning');
      }
    }
  }

  /**
   * Submit the edit form for the item.
   * @param array $form
   * @param array $form_state
   * @access public
   * @return void
   */
  public function edit_form_submit($form, &$form_state) {
    $this->from_array($form_state['values']['ucxf']);
    $this->display_settings = $form_state['values']['ucxf']['display_settings'];
    $this->save();
    drupal_set_message(t('Field saved'));
    if ($this->returnpath) {
      $form_state['redirect'] = $this->returnpath;
    }
  }

  // -----------------------------------------------------------------------------
  // ACTION
  // -----------------------------------------------------------------------------

  /**
   * generate()
   * Generates a field array used in forms generated by uc_extra_fields_pane
   * @access public
   * @return void
   */
  public function generate() {
    $return_field = array();
    switch ($this->value_type) {
      case self::UCXF_WIDGET_TYPE_TEXTFIELD:
        $return_field = array(
          '#type' => 'textfield',
          '#title' => $this->output('label'),
          '#description' => $this->output('description'),
          '#size' => 32,
          '#maxlength' => 255,
          '#required' => $this->required,
        );
        // Add default value only when there is one
        $default_value = $this->generate_value();
        if ($default_value != '') {
          $return_field['#default_value'] = $default_value;
        }
        break;

      case self::UCXF_WIDGET_TYPE_CHECKBOX:
        $return_field = array(
          '#type' => 'checkbox',
          '#title' => $this->output('label'),
          '#description' => $this->output('description'),
          '#required' => $this->required,
        );
        break;

      case self::UCXF_WIDGET_TYPE_SELECT:
        $return_field = array(
          '#type' => 'select',
          '#title' => $this->output('label'),
          '#description' => $this->output('description'),
          '#required' => $this->required,
          '#options' => $this->generate_value(),
          //'#default_value' => NULL,
        );
        break;

      case self::UCXF_WIDGET_TYPE_PHP:
      case self::UCXF_WIDGET_TYPE_CONSTANT:
        $return_field = array(
          '#type' => 'hidden',
          '#value' => $this->generate_value(),
        );
        break;
      case self::UCXF_WIDGET_TYPE_PHP_SELECT:
        $return_field = array(
          '#type' => 'select',
          '#title' => $this->output('label'),
          '#description' => $this->output('description'),
          '#required' => $this->required,
          '#options' => $this->generate_value(),
          //'#default_value' => NULL,
        );
        break;
    }
    return $return_field;
  }

  /**
   * Generates the value for use in fields.
   * This value will be used as a default value for textfields
   * and as an array of options for selection fields.
   * @param boolean $translate
   *   If values may be translated.
   * @access public
   * @return mixed
   */
  public function generate_value($translate = TRUE) {
    switch ($this->value_type) {
      case self::UCXF_WIDGET_TYPE_TEXTFIELD:
        // This will return a string
        $value = (string) $this->value;
        if ($translate) {
          $value = uc_extra_fields_pane_tt("field:$this->db_name:value", $value);
        }
        return $value;
      case self::UCXF_WIDGET_TYPE_CONSTANT:
        // This will return a string, sanitized.
        $value = (string) $this->value;
        if ($translate) {
          $value = check_plain(uc_extra_fields_pane_tt("field:$this->db_name:value", $value));
        }
        return $value;

      case self::UCXF_WIDGET_TYPE_SELECT:
        // This will return an array of options
        // like array('key' => 'label', 'key2' => 'label2')
        $options = array();
        $input_token = strtok($this->value, "\n");
        while ($input_token !== FALSE) {
          if (strpos($input_token, "|")) {
            $arr = explode("|", $input_token);
            $key = trim($arr[0]);
            $label = trim($arr[1]);
          }
          else {
            $key = trim($input_token);
            $label = trim($input_token);
          }
          $options[$key] = $label;
          $input_token = strtok("\n");
        }
        if ($translate) {
          // Translate the labels of the options
          foreach ($options as $key => $label) {
            $options[$key] = uc_extra_fields_pane_tt("field:$this->db_name:value:$key", $label);
          }
        }
        return $options;

      case self::UCXF_WIDGET_TYPE_PHP:
        // This will return a string.
        $value = (string) eval('?>' . $this->value);
        if ($translate) {
          $value = uc_extra_fields_pane_tt("field:$this->db_name:value", $value);
        }
        return $value;

      case self::UCXF_WIDGET_TYPE_PHP_SELECT:
        // This will return an array of options created with eval
        // like array('name' => 'value', 'name2' => 'value2')
        // unfortunately php_eval() is not equipped for the task,
        // so we need to use the standard php-eval.
        $options = eval('?>' . $this->value);
        if ($translate) {
          // Translate the labels of the options
          foreach ($options as $key => $label) {
            $options[$key] = uc_extra_fields_pane_tt("field:$this->db_name:value:$key", $label);
          }
        }
        return $options;
    }
  }
}
