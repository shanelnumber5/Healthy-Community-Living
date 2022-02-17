<?php
/**
 * Widget: Socials
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_socials_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_socials_load' );
	function trx_addons_widget_socials_load() {
		register_widget('trx_addons_widget_socials');
	}
}

// Widget Class
class trx_addons_widget_socials extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_socials', 'description' => esc_html__('Socials - show links to the profiles in your favorites social networks', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_socials', esc_html__('ThemeREX Addons - Socials', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$description = isset($instance['description']) ? $instance['description'] : '';
		$align = isset($instance['align']) ? $instance['align'] : '';

		trx_addons_get_template_part('widgets/socials/tpl.default.php',
										'trx_addons_args_widget_socials', 
										array_merge($args, compact('title', 'align', 'description'))
									);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = wp_kses_data($new_instance['description']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'description' => ''
			)
		);
		$title = $instance['title'];
		$description = $instance['description'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e('Short description', 'trx_addons'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo esc_html($description); ?></textarea>
		</p>
	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_socials_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_socials_load_scripts_front');
	function trx_addons_widget_socials_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_socials', trx_addons_get_file_url('widgets/socials/socials.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_socials_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_socials_merge_styles');
	function trx_addons_widget_socials_merge_styles($list) {
		$list[] = 'widgets/socials/socials.css';
		return $list;
	}
}



// trx_widget_socials
//-------------------------------------------------------------
/*
[trx_widget_socials id="unique_id" title="Widget title"]
*/
if ( !function_exists( 'trx_addons_sc_widget_socials' ) ) {
	function trx_addons_sc_widget_socials($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_socials', $atts, array(
			// Individual params
			"title" => "",
			"description" => "",
			"align" => "left",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_socials';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_socials' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_socials wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_socials', 'widget_socials') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_socials', $atts, $content);
	}
}


// Add [trx_widget_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc')) {
	function trx_addons_sc_widget_socials_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_socials", "trx_addons_sc_widget_socials");
		
		vc_lean_map("trx_widget_socials", 'trx_addons_sc_widget_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc_params')) {
	function trx_addons_sc_widget_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_socials",
				"name" => esc_html__("Social Icons", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with social icons, that specified in the Theme Customizer", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_socials',
				"class" => "trx_widget_socials",
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
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Align", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
							"std" => "left",
							"value" => array(
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_socials' );
	}
}
?>