<?php
/**
 * The template to display blog archive
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

/*
Template Name: Blog archive
*/

/**
 * Make page with this template and put it into menu
 * to display posts as blog archive
 * You can setup output parameters (blog style, posts per page, parent category, etc.)
 * in the Theme Options section (under the page content)
 * You can build this page in the WPBakery Page Builder to make custom page layout:
 * just insert %%CONTENT%% in the desired place of content
 */

// Get template page's content
$academee_content = '';
$academee_blog_archive_mask = '%%CONTENT%%';
$academee_blog_archive_subst = sprintf('<div class="blog_archive">%s</div>', $academee_blog_archive_mask);
if ( have_posts() ) {
	the_post(); 
	if (($academee_content = apply_filters('the_content', get_the_content())) != '') {
		if (($academee_pos = strpos($academee_content, $academee_blog_archive_mask)) !== false) {
			$academee_content = preg_replace('/(\<p\>\s*)?'.$academee_blog_archive_mask.'(\s*\<\/p\>)/i', $academee_blog_archive_subst, $academee_content);
		} else
			$academee_content .= $academee_blog_archive_subst;
		$academee_content = explode($academee_blog_archive_mask, $academee_content);
		// Add VC custom styles to the inline CSS
		$vc_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
		if ( !empty( $vc_custom_css ) ) academee_add_inline_css(strip_tags($vc_custom_css));
	}
}

// Prepare args for a new query
$academee_args = array(
	'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish'
);
$academee_args = academee_query_add_posts_and_cats($academee_args, '', academee_get_theme_option('post_type'), academee_get_theme_option('parent_cat'));
$academee_page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
if ($academee_page_number > 1) {
	$academee_args['paged'] = $academee_page_number;
	$academee_args['ignore_sticky_posts'] = true;
}
$academee_ppp = academee_get_theme_option('posts_per_page');
if ((int) $academee_ppp != 0)
	$academee_args['posts_per_page'] = (int) $academee_ppp;
// Make a new query
query_posts( $academee_args );
// Set a new query as main WP Query
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];

// Set query vars in the new query!
if (is_array($academee_content) && count($academee_content) == 2) {
	set_query_var('blog_archive_start', $academee_content[0]);
	set_query_var('blog_archive_end', $academee_content[1]);
}

get_template_part('index');
?>