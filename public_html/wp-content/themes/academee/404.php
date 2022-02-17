<?php
/**
 * The template to display the 404 page
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Tribe Events hack - create empty post object
if (!isset($GLOBALS['post'])) {
	$GLOBALS['post'] = new stdClass();
	$GLOBALS['post']->post_type = 'unknown';
}
// End Tribe Events hack

get_header(); 

get_template_part( 'content', '404' );

get_footer();
?>