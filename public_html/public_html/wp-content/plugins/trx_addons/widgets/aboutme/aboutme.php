<?php
/**
 * Widget: About Me
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_aboutme_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_aboutme_load' );
	function trx_addons_widget_aboutme_load() {
		register_widget('trx_addons_widget_aboutme');
	}
}

// Widget Class
class trx_addons_widget_aboutme extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_aboutme', 'description' => esc_html__('About me - photo and short description about the blog author', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_aboutme', esc_html__('ThemeREX Addons - About Me', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		
		$blogusers = get_users( 'role=administrator' );
		
		$username = isset($instance['username']) ? $instance['username'] : '';
		if (empty($username) && count($blogusers) > 0 )
			$username = $blogusers[0]->display_name;
		
		$description = isset($instance['description']) ? $instance['description'] : '';
		if (empty($description) && count($description) > 0 )
			$description = $blogusers[0]->description;
		
		$avatar = isset($instance['avatar']) ? $instance['avatar'] : '';
		if (empty($avatar)) {
			if ( count($blogusers) > 0 ) {
				$mult = trx_addons_get_retina_multiplier();
				$avatar = get_avatar( $blogusers[0]->user_email, 220*$mult );
			}
		} else {
			$avatar = trx_addons_get_attachment_url($avatar, trx_addons_get_thumb_size('avatar'));
			$attr = trx_addons_getimagesize($avatar);
			$avatar = '<img src="'.esc_url($avatar).'" alt="'.esc_attr($username).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
		}

		trx_addons_get_template_part('widgets/aboutme/tpl.default.php',
									'trx_addons_args_widget_aboutme',
									array_merge($args, compact('title', 'avatar', 'username', 'description'))
									);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['avatar'] = strip_tags($new_instance['avatar']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['description'] = strip_tags($new_instance['description']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'avatar' => '',
			'username' => '',
			'description' => ''
			)
		);
		$title = $instance['title'];
		$avatar = $instance['avatar'];
		$username = $instance['username'];
		$description = $instance['description'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'avatar' )); ?>"><?php esc_html_e('Avatar (if empty - get gravatar by admin email):', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'avatar' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'avatar' )); ?>" value="<?php echo esc_attr($avatar); ?>" class="widgets_param_fullwidth widgets_param_media_selector">
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'avatar_button' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'avatar' )), $avatar));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php esc_html_e('User name (if equal to # - not show):', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>" value="<?php echo esc_attr($username); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e('Short description about user (if equal to # - not show):', 'trx_addons'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo esc_html($description); ?></textarea>
		</p>
	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_aboutme_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_aboutme_load_scripts_front');
	function trx_addons_widget_aboutme_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_aboutme', trx_addons_get_file_url('widgets/aboutme/aboutme.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_aboutme_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_aboutme_merge_styles');
	function trx_addons_widget_aboutme_merge_styles($list) {
		$list[] = 'widgets/aboutme/aboutme.css';
		return $list;
	}
}



// trx_widget_aboutme
//-------------------------------------------------------------
/*
[trx_widget_aboutme id="unique_id" title="Widget title" avatar="image_url" username="User display name" description="short description"]
*/
if ( !function_exists( 'trx_addons_sc_widget_aboutme' ) ) {
	function trx_addons_sc_widget_aboutme($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_aboutme', $atts, array(
			// Individual params
			"title" => "",
			"avatar" => "",
			"username" => "",
			"description" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_aboutme';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_aboutme' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_aboutme wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_aboutme', 'widget_aboutme') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_aboutme', $atts, $content);
	}
}


// Add [trx_widget_aboutme] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_aboutme_add_in_vc')) {
	function trx_addons_sc_widget_aboutme_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_aboutme", "trx_addons_sc_widget_aboutme");
		
		vc_lean_map("trx_widget_aboutme", 'trx_addons_sc_widget_aboutme_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Aboutme extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_aboutme_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_aboutme_add_in_vc_params')) {
	function trx_addons_sc_widget_aboutme_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_aboutme",
				"name" => esc_html__("About Me", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with blog owner's name, avatar and short description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_aboutme',
				"class" => "trx_widget_aboutme",
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
							"param_name" => "avatar",
							"heading" => esc_html__("Avatar", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for user's avatar. If empty - get gravatar from user's e-mail", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "username",
							"heading" => esc_html__("User name", 'trx_addons'),
							"description" => wp_kses_data( __("User display name. If empty - get display name of the first registered blog user", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_aboutme' );
	}
}
?>