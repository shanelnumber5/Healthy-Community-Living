<?php

/**
 * URL Rewriting module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.5.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/public
 */

if ( !class_exists('FG_Drupal_to_WordPress_URL_Rewriting', false) ) {

	/**
	 * URL Rewriting class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_URL_Rewriting {

		private static $rewrite_rules = array(
			array( 'rule' => '/(\d+)$',					'view' => 'node',		'meta_key' => '_fgd2wp_old_node_id'),
			array( 'rule' => '/(\d+)/',					'view' => 'node',		'meta_key' => '_fgd2wp_old_node_id'),
			array( 'rule' => '/node/(\d+)',				'view' => 'node',		'meta_key' => '_fgd2wp_old_node_id'),
			array( 'rule' => '/taxonomy/term/(\d+)',	'view' => 'taxonomy',	'meta_key' => '_fgd2wp_old_taxonomy_id'),
		);

		/**
		 * Initialize the class and set its properties.
		 *
		 */
		public function __construct() {

			$premium_options = get_option('fgd2wpp_options');
			$do_redirect = isset($premium_options['url_redirect']) && !empty($premium_options['url_redirect']);
			$do_redirect = apply_filters('fgd2wpp_do_redirect', $do_redirect);
			if ( $do_redirect ) {
				// Hook on template redirect
				add_action('template_redirect', array($this, 'template_redirect'));
			}
		}
		
		/**
		 * Redirection to the new URL
		 */
		public function template_redirect() {
			$matches = array();
			do_action('fgd2wpp_pre_404_redirect');
			
			if ( !is_404() ) { // A page is found, don't redirect
				return;
			}
			
			do_action('fgd2wpp_post_404_redirect');

			// Process the rewrite rules
			$rewrite_rules = apply_filters('fgd2wpp_rewrite_rules', self::$rewrite_rules);
			// Drupal configured with SEF URLs
			foreach ( $rewrite_rules as $rewrite_rule ) {
				// Note: Can't use filter_input(INPUT_SERVER, 'REQUEST_URI') because of FastCGI side-effect
				// http://php.net/manual/fr/function.filter-input.php#77307
				if ( preg_match('#' . $rewrite_rule['rule'] . '#', $_SERVER['REQUEST_URI'], $matches) ) {
					$old_id = $matches[1];
					if ( isset($rewrite_rule['callback']) ) {
						$old_id = $rewrite_rule['callback']($old_id);
					}
					self::redirect($rewrite_rule['meta_key'], $old_id, $rewrite_rule['view']);
				}
			}
		}
		
		/**
		 * Query and redirect to the new URL
		 *
		 * @param string $meta_key Meta Key to search in the postmeta or termmeta table
		 * @param int $old_id Drupal ID
		 * @param string $view node|taxonomy
		 */
		public static function redirect($meta_key, $old_id, $view='node') {
			if ( !empty($old_id) && !empty($meta_key) ) {
				switch ( $view ) {
					case 'node':
						// Get the post by its old ID
						$known_post_types = array_keys(get_post_types(array('public' => 1)));
						query_posts(array(
							'post_type' => $known_post_types,
							'meta_key' => $meta_key,
							'meta_value' => $old_id,
							'ignore_sticky_posts' => 1,
						));
						if ( have_posts() ) {
							the_post();
							$url = get_permalink();
							//die($url);
							wp_redirect($url, 301);
							wp_reset_query();
							exit;
						}
						break;
					
					case 'taxonomy':
						$args = array(
							'hide_empty' => false, // also retrieve terms which are not used yet
							'meta_query' => array(
								array(
								   'key'       => $meta_key,
								   'value'     => $old_id,
								   'compare'   => '='
								)
							)
						);
						$terms = get_terms($args);
						if ( count($terms) > 0 ) {
							$url = get_term_link($terms[0]->term_id);
							//die($url);
							wp_redirect($url, 301);
							wp_reset_query();
							exit;
						}
						break;
				}
				// else continue the normal workflow
			}
		}
	}
}
