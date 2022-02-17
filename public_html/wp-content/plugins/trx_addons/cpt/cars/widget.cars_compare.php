<?php
/**
 * Widget: Cars Compare
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.25
 */

// Load widget
if (!function_exists('trx_addons_widget_cars_compare_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_cars_compare_load' );
	function trx_addons_widget_cars_compare_load() {
		register_widget('trx_addons_widget_cars_compare');
	}
}

// Widget Class
class trx_addons_widget_cars_compare extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cars_compare', 'description' => esc_html__('Compare selected cars', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_cars_compare', esc_html__('ThemeREX Addons - Cars Compare', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');

		$list = trx_addons_get_value_gpc('trx_addons_cars_compare_list', array());
		if (!empty($list)) $list = json_decode($list, true);

		trx_addons_get_template_part('cpt/cars/tpl.widget.cars_compare.php',
										'trx_addons_args_widget_cars_compare',
										array_merge($args, compact('title', 'list'))
									);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => ''
			)
		);
		$title = $instance['title'];
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>
		<?php
	}
}



// trx_widget_cars_compare
//-------------------------------------------------------------
/*
[trx_widget_cars_compare id="unique_id" title="Widget title"]
*/
if ( !function_exists( 'trx_addons_sc_widget_cars_compare' ) ) {
	function trx_addons_sc_widget_cars_compare($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_cars_compare', $atts, array(
			// Individual params
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_cars_compare';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_cars_compare' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_cars_compare wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_cars_compare', 'widget_cars_compare') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_cars_compare', $atts, $content);
	}
}


// Add [trx_widget_cars_compare] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_cars_compare_add_in_vc')) {
	function trx_addons_sc_widget_cars_compare_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_cars_compare", "trx_addons_sc_widget_cars_compare");
		
		vc_lean_map("trx_widget_cars_compare", 'trx_addons_sc_widget_cars_compare_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Cars_Compare extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_cars_compare_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_cars_compare_add_in_vc_params')) {
	function trx_addons_sc_widget_cars_compare_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_cars_compare",
				"name" => esc_html__("Cars Compare", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget to compare selected cars", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_cars_compare',
				"class" => "trx_widget_cars_compare",
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
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_cars_compare' );
	}
}
?>