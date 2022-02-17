<?php

/**
 * Custom post types methods
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      3.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_CPT', false) ) {

	/**
	 * CPT class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_CPT {
		
		private $plugin;
		private $cpt_format;
		private $implementation;
		
		/**
		 * Constructor
		 * 
		 * @param object $plugin Main plugin
		 * @param string $cpt_format Custom post type format: toolset | acf
		 */
		public function __construct($plugin, $cpt_format) {
			$this->plugin = $plugin;
			$this->cpt_format = $cpt_format;
			
			switch ( $cpt_format ) {
				case 'acf':
					$this->implementation = new FG_Drupal_to_WordPress_CPT_ACF($plugin);
					break;
				
				case 'toolset':
				default:
					$this->implementation = new FG_Drupal_to_WordPress_CPT_Toolset($plugin);
					break;
			}
		}
		
		/**
		 * Delegate all undefined function calls
		 * 
		 * @param string $method Method
		 * @param array $args Arguments
		 * @return mixed
		 */
		public function __call($method, $args) {
			return call_user_func_array(array($this->implementation, $method), $args);
		}
		
	}
}
