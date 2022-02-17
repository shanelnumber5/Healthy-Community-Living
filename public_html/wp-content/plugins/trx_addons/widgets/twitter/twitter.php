<?php
/**
 * Widget: Twitter
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_twitter_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_twitter_load' );
	function trx_addons_widget_twitter_load() {
		register_widget('trx_addons_widget_twitter');
	}
}

// Widget Class
class trx_addons_widget_twitter extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_twitter', 'description' => esc_html__('Last Twitter Updates. Version for new Twitter API 1.1', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_twitter', esc_html__('ThemeREX Addons - Twitter', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		if (empty($instance['twitter_username']) || empty($instance['twitter_consumer_key']) || empty($instance['twitter_consumer_secret']) || empty($instance['twitter_token_key']) || empty($instance['twitter_token_secret'])) return;

		$data = trx_addons_get_twitter_data(array(
			'mode'            => 'user_timeline',
			'consumer_key'    => $instance['twitter_consumer_key'],
			'consumer_secret' => $instance['twitter_consumer_secret'],
			'token'           => $instance['twitter_token_key'],
			'secret'          => $instance['twitter_token_secret']
			)
		);
		
		if (!$data || !isset($data[0]['text'])) return;
		$instance['data'] = $data;

		extract( $args );

		/* Our variables from the widget settings. */
		$layout = $instance['type'] = isset($instance['type']) ? $instance['type'] : 'list';
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$bg_image = isset($instance['bg_image']) ? $instance['bg_image'] : '';
		
		// Before widget (defined by themes)
		if (!empty($bg_image)) {
			$bg_image = trx_addons_get_attachment_url($bg_image, trx_addons_get_thumb_size('avatar'));
			$before_widget = str_replace(
				'class="widget ',
				'style="background-image:url('.esc_url($bg_image).');"'
				.' class="widget widget_bg_image ',
				$before_widget
			);
		}

		// Before widget (defined by themes)
		trx_addons_show_layout($before_widget);
			
		// Display the widget title if one was input (before and after defined by themes)
		trx_addons_show_layout($title, $before_title, $after_title);

		trx_addons_get_template_part(array(
										'widgets/twitter/tpl.'.trx_addons_esc($layout).'.php',
										'widgets/twitter/tpl.default.php'
										),
										'trx_addons_args_widget_twitter', 
										$instance
									);
			
		// After widget (defined by themes). */
		trx_addons_show_layout($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['bg_image'] = strip_tags( $new_instance['bg_image'] );
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username'] );
		$instance['twitter_consumer_key'] = strip_tags( $new_instance['twitter_consumer_key'] );
		$instance['twitter_consumer_secret'] = strip_tags( $new_instance['twitter_consumer_secret'] );
		$instance['twitter_token_key'] = strip_tags( $new_instance['twitter_token_key'] );
		$instance['twitter_token_secret'] = strip_tags( $new_instance['twitter_token_secret'] );
		$instance['twitter_count'] = strip_tags( $new_instance['twitter_count'] );
		$instance['follow'] = isset( $new_instance['follow'] ) ? 1 : 0;

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'bg_image' => '',
			'twitter_username' => '',
			'twitter_consumer_key' => '',
			'twitter_consumer_secret' => '',
			'twitter_token_key' => '',
			'twitter_token_secret' => '',
			'twitter_count' => 2,
			'follow' => 1
			)
		);
		$title = $instance['title'];
		$bg_image = $instance['bg_image'];
		$twitter_username = $instance['twitter_username'];
		$twitter_consumer_key = $instance['twitter_consumer_key'];
		$twitter_consumer_secret = $instance['twitter_consumer_secret'];
		$twitter_token_key = $instance['twitter_token_key'];
		$twitter_token_secret = $instance['twitter_token_secret'];
		$twitter_count = max(1, (int) $instance['twitter_count']);
		$follow = (int) $instance['follow'] ? 1 : 0;
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_count' )); ?>"><?php esc_html_e('Tweets number:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_count' )); ?>" value="<?php echo esc_attr($twitter_count); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_username' )); ?>"><?php esc_html_e('Twitter Username:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_username' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_username' )); ?>" value="<?php echo esc_attr($twitter_username); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_key' )); ?>"><?php esc_html_e('Consumer Key:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_key' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_consumer_key' )); ?>" value="<?php echo esc_attr($twitter_consumer_key); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_secret' )); ?>"><?php esc_html_e('Consumer Secret:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_secret' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_consumer_secret' )); ?>" value="<?php echo esc_attr($twitter_consumer_secret); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_token_key' )); ?>"><?php esc_html_e('Token Key:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_token_key' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_token_key' )); ?>" value="<?php echo esc_attr($twitter_token_key); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_token_secret' )); ?>"><?php esc_html_e('Token Secret:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_token_secret' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_token_secret' )); ?>" value="<?php echo esc_attr($twitter_token_secret); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('follow')); ?>" name="<?php echo esc_attr($this->get_field_name('follow')); ?>" value="1" <?php echo (1==$follow ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('follow')); ?>"><?php esc_html_e('Show "Follow us"', 'trx_addons'); ?></label><br />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'bg_image' )); ?>"><?php esc_html_e('Background image:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'bg_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'bg_image' )); ?>" value="<?php echo esc_attr($bg_image); ?>" class="widgets_param_fullwidth widgets_param_media_selector" />
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'bg_image_button' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'bg_image' )), $bg_image));
			?>
		</p>

	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_twitter_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_twitter_load_scripts_front');
	function trx_addons_widget_twitter_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_twitter', trx_addons_get_file_url('widgets/twitter/twitter.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_twitter_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_twitter_merge_styles');
	function trx_addons_widget_twitter_merge_styles($list) {
		$list[] = 'widgets/twitter/twitter.css';
		return $list;
	}
}



// trx_widget_twitter
//-------------------------------------------------------------
/*
[trx_widget_twitter id="unique_id" title="Widget title" bg_image="image_url" number="3" follow="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_widget_twitter' ) ) {
	function trx_addons_sc_widget_twitter($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_twitter', $atts, array(
			// Individual params
			"type" => 'list',
			"title" => "",
			"username" => "",
			"bg_image" => "",
			'back_image' => '',			// Alter name for 'bg_image' in VC (it broke bg_image)
			"count" => 2,
			"columns" => 1,
			"follow" => 1,
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"consumer_key" => "",
			"consumer_secret" => "",
			"token_key" => "",
			"token_secret" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if ($atts['follow']=='') $atts['follow'] = 0;
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if (empty($atts['bg_image'])) $atts['bg_image'] = $atts['back_image'];
		extract($atts);

		$type = 'trx_addons_widget_twitter';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['twitter_username'] = $username;
			$atts['twitter_consumer_key'] = $consumer_key;
			$atts['twitter_consumer_secret'] = $consumer_secret;
			$atts['twitter_token_key'] = $token_key;
			$atts['twitter_token_secret'] = $token_secret;
			$atts['twitter_count'] = max(1, (int) $count);
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_twitter' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_twitter wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_twitter', 'widget_twitter') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_twitter', $atts, $content);
	}
}


// Add [trx_widget_twitter] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_twitter_add_in_vc')) {
	function trx_addons_sc_widget_twitter_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_twitter", "trx_addons_sc_widget_twitter");
		
		vc_lean_map( "trx_widget_twitter", 'trx_addons_sc_widget_twitter_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Twitter extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_widget_twitter_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_widget_twitter_add_in_vc_params')) {
	
	function trx_addons_sc_widget_twitter_add_in_vc_params() {
		
		$params = array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select widget's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "list",
							"admin_label" => true,
					        'save_always' => true,
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('widgets', 'twitter')), 'trx_widget_twitter'),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Tweets number", 'trx_addons'),
							"description" => wp_kses_data( __("Tweets number to show in the feed", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => "2",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of columns. If empty - auto detect by items number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "follow",
							"heading" => esc_html__("Show Follow Us", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display Follow Us link below the feed?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show Follow Us" => "1" ),
							"type" => "checkbox"
						),
					),
					trx_addons_vc_add_slider_param(''),
					array(
						array(
							"param_name" => "back_image",		// Alter name for 'bg_image' in VC (it broke bg_image)
							"heading" => esc_html__("Widget background", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for use it as widget background", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "username",
							"heading" => esc_html__("Twitter Username", 'trx_addons'),
							"description" => wp_kses_data( __("Twitter Username", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "consumer_key",
							"heading" => esc_html__("Consumer Key", 'trx_addons'),
							"description" => wp_kses_data( __("Specify Consumer Key from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "consumer_secret",
							"heading" => esc_html__("Consumer Secret", 'trx_addons'),
							"description" => wp_kses_data( __("Specify Consumer Secret from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "token_key",
							"heading" => esc_html__("Token Key", 'trx_addons'),
							"description" => wp_kses_data( __("Specify Token Key from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "token_secret",
							"heading" => esc_html__("Token Secret", 'trx_addons'),
							"description" => wp_kses_data( __("Specify Token Secret from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_id_param()
				);
		
		$params = trx_addons_vc_add_param_option($params, 'slider', array( 
																		'dependency' => array(
																			'element' => 'type',
																			'value' => 'default'
																			)
																		)
												);

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_twitter",
				"name" => esc_html__("Twitter feed", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with Twitter feed", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_twitter',
				"class" => "trx_widget_twitter",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_widget_twitter' );
			
	}
}
?>