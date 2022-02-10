<?php

/**
 * @file
 * Definition of time_spent_handler_field_timespent.
 */

/**
 * A handler to provide proper display for timespent length.
 *
 * @ingroup views_field_handlers
 */
class time_spent_handler_field_timespent extends views_handler_field {

  function render($values) {
    $value = $this->get_value($values);
    return time_spent_sec2hms($value);
  }

}
