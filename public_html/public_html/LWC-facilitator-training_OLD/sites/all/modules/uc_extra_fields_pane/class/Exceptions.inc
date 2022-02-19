<?php
/**
 * @file
 * Exception classes for Extra Fields Pane
 */

/**
 * UCXF_Exception class.
 *
 * Base class for all Extra Fields Pane exceptions.
 */
class UCXF_Exception extends Exception {
  /**
   * Prints error message on screen.
   */
  public function printMessage() {
    drupal_set_message($this->getMessage(), 'error');
  }

  /**
   * Logs error in watchdog
   */
  public function logError($severity = WATCHDOG_ERROR) {
    watchdog('ucxf', $this->getMessage(), array(), $severity);
  }
}

/**
 * UCXF_DbException
 *
 * Used when a database error occurs.
 */
class UCXF_DbException extends UCXF_Exception { }
