<?php
/**
 * Widget: Banner
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_banner_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_banner_load' );
	function trx_addons_widget_banner_load() {
		register_widget( 'trx_addons_widget_banner' );
	}
}

// Widget Class
class trx_addons_widget_banner extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_banner', 'description' => esc_html__('Banner with image and/or any html and js code', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_banner', esc_html__('ThemeREX Addons - Banner', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$fullwidth = isset($instance['fullwidth']) ? $instance['fullwidth'] : '';
		$banner_image = isset($instance['banner_image']) ? $instance['banner_image'] : '';
		$banner_link = isset($instance['banner_link']) ? $instance['banner_link'] : '';
		$banner_code = isset($instance['banner_code']) ? $instance['banner_code'] : '';

		trx_addons_get_template_part('widgets/banner/tpl.default.php',
									'trx_addons_args_widget_banner',
									array_merge($args, compact('title', 'fullwidth', 'banner_image', 'banner_link', 'banner_code'))
									);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fullwidth'] = strip_tags( $new_instance['fullwidth'] );
		$instance['banner_image'] = strip_tags( $new_instance['banner_image'] );
		$instance['banner_link'] = strip_tags( $new_instance['banner_link'] );
		$instance['banner_code'] = $new_instance['banner_code'];

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'fullwidth' => '1',
			'banner_image' => '',
			'banner_link' => '',
			'banner_code' => ''
			)
		);
		$title = $instance['title'];
		$fullwidth = $instance['fullwidth'];
		$banner_image = $instance['banner_image'];
		$banner_link = $instance['banner_link'];
		$banner_code = $instance['banner_code'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1"><?php esc_html_e('Widget size:', 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1" name="<?php echo esc_attr($this->get_field_name('fullwidth')); ?>" value="1" <?php echo (1==$fullwidth ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1"><?php esc_html_e('Fullwidth', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_0" name="<?php echo esc_attr($this->get_field_name('fullwidth')); ?>" value="0" <?php echo (0==$fullwidth ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_0"><?php esc_html_e('Boxed', 'trx_addons'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_image' )); ?>"><?php esc_html_e('Image source URL:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'banner_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_image' )); ?>" value="<?php echo esc_attr($banner_image); ?>" class="widgets_param_fullwidth widgets_param_media_selector" />
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'banner_media' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'banner_image' )), $banner_image));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_link' )); ?>"><?php echo esc_html_e('Image link URL:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'banner_link' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_link' )); ?>" value="<?php echo esc_attr($banner_link); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_code' )); ?>"><?php esc_html_e('Paste HTML Code:', 'trx_addons'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'banner_code' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_code' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo htmlspecialchars($banner_code); ?></textarea>
		</p>
	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_banner_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_banner_load_scripts_front');
	function trx_addons_widget_banner_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_banner', trx_addons_get_file_url('widgets/banner/banner.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_banner_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_banner_merge_styles');
	function trx_addons_widget_banner_merge_styles($list) {
		$list[] = 'widgets/banner/banner.css';
		return $list;
	}
}



// trx_widget_banner
//-------------------------------------------------------------
/*
[trx_widget_banner id="unique_id" title="Widget title" fullwidth="0|1" image="image_url" link="Image_link_url" code="html & js code"]
*/
if ( !function_exists( 'trx_addons_sc_widget_banner' ) ) {
	function trx_addons_sc_widget_banner($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_banner', $atts, array(
			// Individual params
			"title" => "",
			"image" => "",
			"link" => "",
			"code" => "",
			"fullwidth" => "off",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_banner';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['banner_image'] = $image;
			$atts['banner_link'] = $link;
			$atts['banner_code'] = !empty($code) ? trim( vc_value_from_safe( $code ) ) : '';
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_banner' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_banner wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_banner', 'widget_banner') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_banner', $atts, $content);
	}
}


// Add [trx_widget_banner] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_banner_add_in_vc')) {
	function trx_addons_sc_widget_banner_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_banner", "trx_addons_sc_widget_banner");
		
		vc_lean_map("trx_widget_banner", 'trx_addons_sc_widget_banner_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Banner extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_banner_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_banner_add_in_vc_params')) {
	function trx_addons_sc_widget_banner_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_banner",
				"name" => esc_html__("Banner", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with banner or any HTML and/or Javascript code", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_banner',
				"class" => "trx_widget_banner",
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
							"param_name" => "image",
							"heading" => esc_html__("Image", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for the banner (leave empty if you paste banner code)", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Banner's link", 'trx_addons'),
							"description" => wp_kses_data( __("Link URL for the banner (leave empty if you paste banner code)", 'trx_addons') ),
							"type" => "textfield"
						),
						array(
							"param_name" => "code",
							"heading" => esc_html__("or paste HTML Code", 'trx_addons'),
							"description" => wp_kses_data( __("Widget's HTML and/or JS code", 'trx_addons') ),
							"type" => "textarea_safe"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_banner' );
	}
}
?>