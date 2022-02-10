<?php

/**
 * @file
 * Contains uc_addresses_views_access class.
 */

/**
 * Access plugin that provides access control based on custom PHP code.
 *
 * @ingroup views_access_plugins
 */
class uc_addresses_views_access extends views_plugin_access {
  /**
   * Implements views_plugin_access#summary_title().
   */
  public function summary_title() {
    $title = array();

    switch ($this->options['access_type']) {
      case 'view':
        $title[] = t('View access');
        break;

      case 'edit':
        $title[] = t('Edit access');
        break;

      case 'delete':
        $title[] = t('Delete access');
        break;
    }

    if ($this->options['uid_argument'] && $this->options['aid_argument']) {
      $title[] = t('by User ID and Address ID');
    }
    elseif ($this->options['uid_argument']) {
      $title[] = t('by User ID');
    }
    elseif ($this->options['aid_argument']) {
      $title[] = t('by Address ID');
    }
    else {
      return t('Error! No arguments selected.');
    }

    return implode(', ', $title);
  }

  /**
   * Implements views_plugin_access#option_definition().
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['uid_argument'] = array('default' => NULL);
    $options['aid_argument'] = array('default' => NULL);
    $options['access_type'] = array('default' => 'view');
    return $options;
  }

  /**
   * Implements views_plugin#options_form().
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['help'] = array(
      '#type' => 'markup',
      '#value' => t('With this access control type you can restrict access to this display based on an user ID argument and/or an address ID argument.') . ' ' . t('Note that this currently only works for path based displays, such as  "!page" displays.', array('!page' => t('Page')))
    );

    // Get list of Views arguments.
    $handlers = $this->display->handler->get_handlers('argument');

    // User can select specific arguments to pass on.
    $options = array(
      '' => t('- None -'),
    );
    foreach ($handlers as $key => $handler) {
      $options[$key] = $handler->definition['title'];
    }
    $form['uid_argument'] = array(
      '#type' => 'select',
      '#title' => t('User argument'),
      '#description' => t('Optionally select an Views argument that represents an User ID. Only users with permission to access addresses of this user will have access to this display.'),
      '#options' => $options,
      '#default_value' => $this->options['uid_argument'],
    );
    $form['aid_argument'] = array(
      '#type' => 'select',
      '#title' => t('Address ID argument'),
      '#description' => t('Optionally select an Views argument that represents an address ID. Only users with permission to access this address will have access to this display.'),
      '#options' => $options,
      '#default_value' => $this->options['aid_argument'],
    );

    // User can choose which access operation to check: view, edit or delete.
    $form['access_type'] = array(
      '#type' => 'select',
      '#title' => t('Access type'),
      '#description' => t('Select for which type of operation permissions should be checked. Usually "!access_type".', array('!access_type' => t('View'))),
      '#options' => array(
        'view' => t('View'),
        'edit' => t('Edit'),
        'delete' => t('Delete'),
      ),
      '#default_value' => $this->options['access_type'],
    );
  }

  /**
   * Implements views_plugin#options_validate().
   *
   * Checks if at least one argument is selected.
   *
   * @todo Implement!
   */
  public function options_validate(&$form, &$form_state) {
    $option_values = $form_state['values']['access_options'];
    if (empty($option_values['uid_argument']) && empty($option_values['aid_argument'])) {
      form_set_error('access_options][uid_argument', t('Please select at least one argument.'));
      form_set_error('access_options][aid_argument');
    }
  }

  /**
   * Implements views_plugin_access#access().
   */
  public function access($account) {
    $args = $this->view->args;

    if (count($args) < 1) {
      // Not enough information. Deny access.
      return FALSE;
    }

    // Pick placeholder path.
    $path = implode('/', array_fill(1, count($args), '%'));
    $positions = $this->uc_addresses_find_arg_positions($path);

    $uid = NULL;
    $aid = NULL;
    if (isset($args[$positions['uid']])) {
      $uid = $args[$positions['uid']];
    }
    if (isset($args[$positions['aid']])) {
      $aid = $args[$positions['aid']];
    }
    return uc_addresses_check_access_by_ids($this->options['access_type'], $uid, $aid, $account);
  }

  /**
   * Implements views_plugin_access#get_access_callback().
   */
  public function get_access_callback() {
    // Find out at which position in the path the given argument is.
    if (isset($this->display->display_options['path'])) {
      $positions = $this->uc_addresses_find_arg_positions($this->display->display_options['path']);
      return array('uc_addresses_check_access_by_ids', array($this->options['access_type'], $positions['uid'], $positions['aid']));
    }
    // If no path is applied for this display, then there are no restrictions.
    return TRUE;
  }

  /**
   * Looksup the position in the path for each argument.
   *
   * @param string $path
   *   The path to examine.
   *
   * @return array positions
   *   An array with the following items:
   *   - uid_arg_position: integer if found, NULL otherwise.
   *   - aid_arg_position: integer if found, NULL otherwise.
   */
  protected function uc_addresses_find_arg_positions($path) {
    $positions = array(
      'uid' => NULL,
      'aid' => NULL,
    );

    // Get list of Views arguments.
    $handlers = $this->display->handler->get_handlers('argument');

    // Find the positions for any arguments embedded in the path via '%'.
    $i = 0;
    $args = array();
    foreach (explode('/', $path) as $element) {
      if ($element == '%') {
        $args[] = $i;
      }
      $i++;
    }
    // Find out which argument handler belongs to which position.
    $j = 0;
    foreach ($handlers as $key => $handler) {
      switch ($key) {
        case $this->options['uid_argument']:
        case $this->options['aid_argument']:
          // Check if this argument is explicitly noted in the path.
          if (isset($args[$j])) {
            // The argument is one of the '%' signs in the path.
            $position = $args[$j];
          }
          else {
            // The argument is not defined in the path. Assume the
            // argument is at the end of the path.
            $position = $i;
            $i++;
          }
          if ($key == $this->options['uid_argument']) {
            $positions['uid'] = $position;
          }
          if ($key == $this->options['aid_argument']) {
            $positions['aid'] = $position;
          }
          break;
      }
      $j++;
    }

    return $positions;
  }
}
