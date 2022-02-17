<?php

/**
 * Functions to provide backwards compatibility with old versions of PHP and WordPress
 *
 * @link       https://wordpress.org/plugins/fg-drupal-to-wp/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress
 * @subpackage FG_Drupal_to_WordPress/admin
 */

/**
 * Get the last occurred error
 * for PHP < 5.2.0
 */
if (!function_exists('error_get_last')) {
	function error_get_last() {
		$__error_get_last_retval__ = array(
			'type'        => '',
			'message'     => '',
			'file'        => '',
			'line'        => ''
		);
		return $__error_get_last_retval__;
	}

}

/**
 * Parse a configuration string
 * for PHP < 5.3.0
 */
if (!function_exists('parse_ini_string')) {
	if ( !defined('INI_SCANNER_NORMAL') ) define('INI_SCANNER_NORMAL', 0);
	if ( !defined('INI_SCANNER_RAW') ) define('INI_SCANNER_RAW', 1);
	function parse_ini_string($str, $process_sections = false,  $scanner_mode = INI_SCANNER_NORMAL) {
		
		if(empty($str)) return false;

		$lines = explode("\n", $str);
		$ret = Array();
		$inside_section = false;

		foreach($lines as $line) {
			
			$line = trim($line);

			if(!$line || $line[0] == "#" || $line[0] == ";") continue;
			
			if($line[0] == "[" && $endIdx = strpos($line, "]")){
				$inside_section = substr($line, 1, $endIdx-1);
				continue;
			}

			if(!strpos($line, '=')) continue;

			$tmp = explode("=", $line, 2);

			if($inside_section) {
				
				$key = rtrim($tmp[0]);
				$value = ltrim($tmp[1]);

				if(preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
					$value = mb_substr($value, 1, mb_strlen($value) - 2);
				}

				$t = preg_match("^\[(.*?)\]^", $key, $matches);
				if(!empty($matches) && isset($matches[0])) {

					$arr_name = preg_replace('#\[(.*?)\]#is', '', $key);

					if(!isset($ret[$inside_section][$arr_name]) || !is_array($ret[$inside_section][$arr_name])) {
						$ret[$inside_section][$arr_name] = array();
					}

					if(isset($matches[1]) && !empty($matches[1])) {
						$ret[$inside_section][$arr_name][$matches[1]] = $value;
					} else {
						$ret[$inside_section][$arr_name][] = $value;
					}

				} else {
					$ret[$inside_section][trim($tmp[0])] = $value;
				}            

			} else {
				
				$ret[trim($tmp[0])] = ltrim($tmp[1]);

			}
		}
		return $ret;
	}
}
