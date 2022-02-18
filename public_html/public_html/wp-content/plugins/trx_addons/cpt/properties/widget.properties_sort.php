<?php
/**
 * Widget: Properties Sort
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.22
 */

// Load widget
if (!function_exists('trx_addons_widget_properties_sort_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_properties_sort_load' );
	function trx_addons_widget_properties_sort_load() {
		register_widget('trx_addons_widget_properties_sort');
	}
}

// Widget Class
class trx_addons_widget_properties_sort extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_properties_sort', 'description' => esc_html__('Sort properties list by date, price or title', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_properties_sort', esc_html__('ThemeREX Addons - Properties Sort', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');

		$orderby = isset($instance['orderby']) ? $instance['orderby'] : 'date';
		$order = isset($instance['order']) ? $instance['order'] : 'desc';

		trx_addons_get_template_part('cpt/properties/tpl.widget.properties_sort.php',
										'trx_addons_args_widget_properties_sort',
										array_merge($args, compact('title', 'orderby', 'order'))
									);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['orderby'] = strip_tags($new_instance['orderby']);
		$instance['order'] = strip_tags($new_instance['order']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'orderby' => 'date',
			'order' => 'desc'
			)
		);
		$title = $instance['title'];
		$orderby = $instance['orderby'];
		$order = $instance['order'];
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php esc_html_e('Order by:', 'trx_addons'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>" class="widgets_param_fullwidth">
				<option value="date"<?php if ($orderby == 'date') echo ' selected="selected"'; ?>><?php esc_html_e('Date', 'trx_addons'); ?></option>
				<option value="price"<?php if ($orderby == 'price') echo ' selected="selected"'; ?>><?php esc_html_e('Price', 'trx_addons'); ?></option>
				<option value="title"<?php if ($orderby == 'title') echo ' selected="selected"'; ?>><?php esc_html_e('Title', 'trx_addons'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('order')); ?>"><?php esc_html_e('Order:', 'trx_addons'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('order')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>" class="widgets_param_fullwidth">
				<option value="asc"<?php if ($orderby == 'asc') echo ' selected="selected"'; ?>><?php esc_html_e('Ascending', 'trx_addons'); ?></option>
				<option value="desc"<?php if ($orderby == 'desc') echo ' selected="selected"'; ?>><?php esc_html_e('Descending', 'trx_addons'); ?></option>
			</select>
		</p>
	<?php
	}
}



// trx_widget_properties_sort
//-------------------------------------------------------------
/*
[trx_widget_properties_sort id="unique_id" title="Widget title" orderby="price" order="desc"]
*/
if ( !function_exists( 'trx_addons_sc_widget_properties_sort' ) ) {
	function trx_addons_sc_widget_properties_sort($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_properties_sort', $atts, array(
			// Individual params
			"title" => "",
			"orderby" => "date",
			"order" => "desc",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_properties_sort';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_properties_sort' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_properties_sort wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_properties_sort', 'widget_properties_sort') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_properties_sort', $atts, $content);
	}
}


// Add [trx_widget_properties_sort] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_properties_sort_add_in_vc')) {
	function trx_addons_sc_widget_properties_sort_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_properties_sort", "trx_addons_sc_widget_properties_sort");
		
		vc_lean_map("trx_widget_properties_sort", 'trx_addons_sc_widget_properties_sort_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Properties_Sort extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_properties_sort_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_properties_sort_add_in_vc_params')) {
	function trx_addons_sc_widget_properties_sort_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_properties_sort",
				"name" => esc_html__("Properties Sort", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget to sort properties by price, date or title", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_properties_sort',
				"class" => "trx_widget_properties_sort",
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
							"param_name" => "orderby",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select sorting type of properties", 'trx_addons') ),
							"std" => "date",
							'save_always' => true,
							"value" => array(
								esc_html__('Date', 'trx_addons') => 'date',
								esc_html__('Price', 'trx_addons') => 'price',
								esc_html__('Title', 'trx_addons') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => esc_html__("Order", 'trx_addons'),
							"description" => wp_kses_data( __("Select sorting order of properties", 'trx_addons') ),
							"std" => "desc",
							'save_always' => true,
							"value" => array(
								esc_html__('Ascending', 'trx_addons') => 'asc',
								esc_html__('Descending', 'trx_addons') => 'desc'
							),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_properties_sort' );
	}
}
?>