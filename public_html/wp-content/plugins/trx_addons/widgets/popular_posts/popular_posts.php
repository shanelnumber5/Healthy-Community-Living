<?php
/**
 * Widget: Popular posts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_popular_posts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_popular_posts_load' );
	function trx_addons_widget_popular_posts_load() {
		register_widget('trx_addons_widget_popular_posts');
	}
}

// Widget Class
class trx_addons_widget_popular_posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => esc_html__('The most popular and most commented blog posts', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_popular_posts', esc_html__('ThemeREX Addons - Most Popular & Commented Posts', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$tabs = array(
			array(
				'title' => isset($instance['title_popular']) 	? $instance['title_popular']	: '',
				'content' => ''
				),
			array(
				'title' => isset($instance['title_commented'])	? $instance['title_commented']	: '',
				'content' => ''
				),
			array(
				'title' => isset($instance['title_liked'])		? $instance['title_liked']		: '',
				'content' => ''
				)
			);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		$tabs_count = 0;

		for ($i=0; $i<3; $i++) {
			if (empty($tabs[$i]['title'])) continue;
			$tabs_count++;
			$q_args = array(
				'post_type' => 'post',
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'post_password' => '',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true,
				'order' => 'DESC',
			);
			if ($i==0) {			// Most popular
				$q_args['meta_key'] = 'trx_addons_post_views_count';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($i==2) {		// Most liked
				$q_args['meta_key'] = 'trx_addons_post_likes_count';
				$q_args['orderby'] = 'meta_value_num';
			} else {				// Most commented
				$q_args['orderby'] = 'comment_count';
			}
			
			$q = new WP_Query($q_args); 
			
			// Loop posts
			if ( $q->have_posts() ) {
				$post_number = 0;
				set_query_var('trx_addons_output_widgets_posts', '');
				while ($q->have_posts()) { $q->the_post();
					$post_number++;
					trx_addons_get_template_part('templates/tpl.posts-list.php',
												'trx_addons_args_widgets_posts',
												array(
													'counters' => $i==0 ? 'views' : ($i==1 ? 'comments' : 'likes'),
													'show_image' => isset($instance['show_image']) ? (int) $instance['show_image'] : 0,
													'show_date' => isset($instance['show_date']) ? (int) $instance['show_date'] : 0,
													'show_author' => isset($instance['show_author']) ? (int) $instance['show_author'] : 0,
													'show_counters'	=> isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0,
										            'show_categories' => isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0
							            			)
												);
					if ($post_number >= $number) break;
				}
				$tabs[$i]['content'] .= get_query_var('trx_addons_output_widgets_posts');
			}
		}

		wp_reset_postdata();

		if ( $tabs[0]['title'].$tabs[0]['content'].$tabs[1]['title'].$tabs[1]['content'].$tabs[2]['title'].$tabs[2]['content'] ) {

			trx_addons_get_template_part('widgets/popular_posts/tpl.default.php',
										'trx_addons_args_widget_popular_posts', 
										array_merge($args, compact('title', 'tabs', 'tabs_count'))
										);

			if (!is_customize_preview() && $tabs_count > 1) {
				wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
				wp_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);
			}
		}
	}

	// Update the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_popular'] = strip_tags($new_instance['title_popular']);
		$instance['title_commented'] = strip_tags($new_instance['title_commented']);
		$instance['title_liked'] = strip_tags($new_instance['title_liked']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['show_categories'] = (int) $new_instance['show_categories'];

		return $instance;
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'title_popular' => '', 
			'title_commented' => '', 
			'title_liked' => '', 
			'number' => '4', 
			'show_date' => '1', 
			'show_image' => '1', 
			'show_author' => '1', 
			'show_counters' => '1',
			'show_categories' => '1'
			)
		);
		$title = $instance['title'];
		$title_popular = $instance['title_popular'];
		$title_commented = $instance['title_commented'];
		$title_liked = $instance['title_liked'];
		$number = (int) $instance['number'];
		$show_date = (int) $instance['show_date'];
		$show_image = (int) $instance['show_image'];
		$show_author = (int) $instance['show_author'];
		$show_counters = (int) $instance['show_counters'];
		$show_categories = (int) $instance['show_categories'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_popular')); ?>"><?php esc_html_e('Most popular tab title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_popular')); ?>" name="<?php echo esc_attr($this->get_field_name('title_popular')); ?>" value="<?php echo esc_attr($title_popular); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_commented')); ?>"><?php esc_html_e('Most commented tab title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_commented')); ?>" name="<?php echo esc_attr($this->get_field_name('title_commented')); ?>" value="<?php echo esc_attr($title_commented); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_liked')); ?>"><?php esc_html_e('Most liked tab title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_liked')); ?>" name="<?php echo esc_attr($this->get_field_name('title_liked')); ?>" value="<?php echo esc_attr($title_liked); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number posts to show:', 'trx_addons'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($number); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e("Show post's image:", 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="1" <?php echo (1==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="0" <?php echo (0==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e("Show post's author:", 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="1" <?php echo (1==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="0" <?php echo (0==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e("Show post's date:", 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="1" <?php echo (1==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="0" <?php echo (0==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e("Show post's counters:", 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_2" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="1" <?php echo (1==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="0" <?php echo (0==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e("Show post's categories:", 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_2" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="1" <?php echo (1==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="0" <?php echo (0==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

	<?php
	}
}


// trx_widget_popular_posts
//-------------------------------------------------------------
/*
[trx_widget_popular_posts id="unique_id" title="Widget title" title_popular="title for the tab 'most popular'" title_commented="title for the tab 'most commented'" title_liked="title for the tab 'most liked'" number="4"]
*/
if ( !function_exists( 'trx_addons_sc_widget_popular_posts' ) ) {
	function trx_addons_sc_widget_popular_posts($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_popular_posts', $atts, array(
			// Individual params
			"title" => "",
			"title_popular" => "",
			"title_commented" => "",
			"title_liked" => "",
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => 1,
			"show_categories" => 1,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 0;
		if ($atts['show_categories']=='') $atts['show_categories'] = 0;
		extract($atts);
		$type = 'trx_addons_widget_popular_posts';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_popular_posts' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_popular_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_popular_posts', 'widget_popular_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_popular_posts', $atts, $content);
	}
}


// Add [trx_widget_popular_posts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_popular_posts", "trx_addons_sc_widget_popular_posts");
		
		vc_lean_map("trx_widget_popular_posts", 'trx_addons_sc_widget_popular_posts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Popular_Posts extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_popular_posts_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc_params')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_popular_posts",
				"name" => esc_html__("Popular Posts", 'trx_addons'),
				"description" => wp_kses_data( __("Insert popular posts list with thumbs, post's meta and category", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_popular_posts',
				"class" => "trx_widget_popular_posts",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title_popular",
							"heading" => esc_html__("Most popular tab title", 'trx_addons'),
							"description" => wp_kses_data( __("Most popular tab title", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title_commented",
							"heading" => esc_html__("Most commented tab title", 'trx_addons'),
							"description" => wp_kses_data( __("Most commented tab title", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title_liked",
							"heading" => esc_html__("Most liked tab title", 'trx_addons'),
							"description" => wp_kses_data( __("Most liked tab title", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number posts to show", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts display in widget?", 'trx_addons') ),
							"admin_label" => true,
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "show_image",
							"heading" => esc_html__("Show post's image", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's featured image?", 'trx_addons') ),
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "1",
							"value" => array("Show image" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_author",
							"heading" => esc_html__("Show post's author", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's author?", 'trx_addons') ),
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "1",
							"value" => array("Show author" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_date",
							"heading" => esc_html__("Show post's date", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's publish date?", 'trx_addons') ),
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "1",
							"value" => array("Show date" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_counters",
							"heading" => esc_html__("Show post's counters", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's counters?", 'trx_addons') ),
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "1",
							"value" => array("Show counters" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_categories",
							"heading" => esc_html__("Show post's categories", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's categories?", 'trx_addons') ),
							"group" => esc_html__('Details', 'trx_addons'),
							"std" => "1",
							"value" => array("Show categories" => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_popular_posts');
	}
}
?>