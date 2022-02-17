<?php
/**
 * Theme storage manipulations
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('academee_storage_get')) {
	function academee_storage_get($var_name, $default='') {
		global $ACADEMEE_STORAGE;
		return isset($ACADEMEE_STORAGE[$var_name]) ? $ACADEMEE_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('academee_storage_set')) {
	function academee_storage_set($var_name, $value) {
		global $ACADEMEE_STORAGE;
		$ACADEMEE_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('academee_storage_empty')) {
	function academee_storage_empty($var_name, $key='', $key2='') {
		global $ACADEMEE_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($ACADEMEE_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($ACADEMEE_STORAGE[$var_name][$key]);
		else
			return empty($ACADEMEE_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('academee_storage_isset')) {
	function academee_storage_isset($var_name, $key='', $key2='') {
		global $ACADEMEE_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($ACADEMEE_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($ACADEMEE_STORAGE[$var_name][$key]);
		else
			return isset($ACADEMEE_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('academee_storage_inc')) {
	function academee_storage_inc($var_name, $value=1) {
		global $ACADEMEE_STORAGE;
		if (empty($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = 0;
		$ACADEMEE_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('academee_storage_concat')) {
	function academee_storage_concat($var_name, $value) {
		global $ACADEMEE_STORAGE;
		if (empty($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = '';
		$ACADEMEE_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('academee_storage_get_array')) {
	function academee_storage_get_array($var_name, $key, $key2='', $default='') {
		global $ACADEMEE_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($ACADEMEE_STORAGE[$var_name][$key]) ? $ACADEMEE_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($ACADEMEE_STORAGE[$var_name][$key][$key2]) ? $ACADEMEE_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('academee_storage_set_array')) {
	function academee_storage_set_array($var_name, $key, $value) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if ($key==='')
			$ACADEMEE_STORAGE[$var_name][] = $value;
		else
			$ACADEMEE_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('academee_storage_set_array2')) {
	function academee_storage_set_array2($var_name, $key, $key2, $value) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if (!isset($ACADEMEE_STORAGE[$var_name][$key])) $ACADEMEE_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$ACADEMEE_STORAGE[$var_name][$key][] = $value;
		else
			$ACADEMEE_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Merge array elements
if (!function_exists('academee_storage_merge_array')) {
	function academee_storage_merge_array($var_name, $key, $value) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if ($key==='')
			$ACADEMEE_STORAGE[$var_name] = array_merge($ACADEMEE_STORAGE[$var_name], $value);
		else
			$ACADEMEE_STORAGE[$var_name][$key] = array_merge($ACADEMEE_STORAGE[$var_name][$key], $value);
	}
}

// Add array element after the key
if (!function_exists('academee_storage_set_array_after')) {
	function academee_storage_set_array_after($var_name, $after, $key, $value='') {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if (is_array($key))
			academee_array_insert_after($ACADEMEE_STORAGE[$var_name], $after, $key);
		else
			academee_array_insert_after($ACADEMEE_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('academee_storage_set_array_before')) {
	function academee_storage_set_array_before($var_name, $before, $key, $value='') {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if (is_array($key))
			academee_array_insert_before($ACADEMEE_STORAGE[$var_name], $before, $key);
		else
			academee_array_insert_before($ACADEMEE_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('academee_storage_push_array')) {
	function academee_storage_push_array($var_name, $key, $value) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($ACADEMEE_STORAGE[$var_name], $value);
		else {
			if (!isset($ACADEMEE_STORAGE[$var_name][$key])) $ACADEMEE_STORAGE[$var_name][$key] = array();
			array_push($ACADEMEE_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('academee_storage_pop_array')) {
	function academee_storage_pop_array($var_name, $key='', $defa='') {
		global $ACADEMEE_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($ACADEMEE_STORAGE[$var_name]) && is_array($ACADEMEE_STORAGE[$var_name]) && count($ACADEMEE_STORAGE[$var_name]) > 0) 
				$rez = array_pop($ACADEMEE_STORAGE[$var_name]);
		} else {
			if (isset($ACADEMEE_STORAGE[$var_name][$key]) && is_array($ACADEMEE_STORAGE[$var_name][$key]) && count($ACADEMEE_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($ACADEMEE_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('academee_storage_inc_array')) {
	function academee_storage_inc_array($var_name, $key, $value=1) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if (empty($ACADEMEE_STORAGE[$var_name][$key])) $ACADEMEE_STORAGE[$var_name][$key] = 0;
		$ACADEMEE_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('academee_storage_concat_array')) {
	function academee_storage_concat_array($var_name, $key, $value) {
		global $ACADEMEE_STORAGE;
		if (!isset($ACADEMEE_STORAGE[$var_name])) $ACADEMEE_STORAGE[$var_name] = array();
		if (empty($ACADEMEE_STORAGE[$var_name][$key])) $ACADEMEE_STORAGE[$var_name][$key] = '';
		$ACADEMEE_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('academee_storage_call_obj_method')) {
	function academee_storage_call_obj_method($var_name, $method, $param=null) {
		global $ACADEMEE_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($ACADEMEE_STORAGE[$var_name]) ? $ACADEMEE_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($ACADEMEE_STORAGE[$var_name]) ? $ACADEMEE_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('academee_storage_get_obj_property')) {
	function academee_storage_get_obj_property($var_name, $prop, $default='') {
		global $ACADEMEE_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($ACADEMEE_STORAGE[$var_name]->$prop) ? $ACADEMEE_STORAGE[$var_name]->$prop : $default;
	}
}
?>