<?php
/**
 * Widget: Display Contacts info
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_contacts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_contacts_load' );
	function trx_addons_widget_contacts_load() {
		register_widget('trx_addons_widget_contacts');
	}
}

// Widget Class
class trx_addons_widget_contacts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_contacts', 'description' => esc_html__('Contacts - logo and short description, address, phone and email', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_contacts', esc_html__('ThemeREX Addons - Contacts', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
	
		$logo = isset($instance['logo']) ? $instance['logo'] : '';
		$logo_retina = isset($instance['logo_retina']) ? $instance['logo_retina'] : '';
		// Uncomment next section (remove false from the condition)
		// if you want to get logo from current theme (if parameter 'logo' is empty)
		if (false && empty($logo)) {
			$logo = apply_filters('trx_addons_filter_theme_logo', '');
			if (is_array($logo)) {
				$logo = !empty($logo['logo']) ? $logo['logo'] : '';
				$logo_retina = !empty($logo['logo_retina']) ? $logo['logo_retina'] : $logo_retina;
			}
		}
		if (!empty($logo)) {
			$logo = trx_addons_get_attachment_url($logo, 'full');
			$attr = trx_addons_getimagesize($logo);
			$logo = '<img src="'.esc_url($logo).'" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
			// Logo for Retina
			if (!empty($logo_retina)) {
				$logo_retina = trx_addons_get_attachment_url($logo_retina, 'full');
				$logo_retina = '<img src="'.esc_url($logo_retina).'" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
			}
		}
		
		$description = isset($instance['description']) ? $instance['description'] : '';
		$content = isset($instance['content']) ? $instance['content'] : '';

		$address = isset($instance['address']) ? $instance['address'] : '';
		$phone = isset($instance['phone']) ? $instance['phone'] : '';
		$email = isset($instance['email']) ? $instance['email'] : '';
		$columns = isset($instance['columns']) ? (int) $instance['columns'] : 0;
		$socials = isset($instance['socials']) ? (int) $instance['socials'] : 0;

		$googlemap = isset($instance['googlemap']) ? (int) $instance['googlemap'] : 0;
		$googlemap_height = !empty($instance['googlemap_height']) ? $instance['googlemap_height'] : 130;
		$googlemap_position = isset($instance['googlemap_position']) ? $instance['googlemap_position'] : 'top';

		trx_addons_get_template_part('widgets/contacts/tpl.default.php',
									'trx_addons_args_widget_contacts', 
									array_merge($args, compact('title', 'logo', 'logo_retina', 'description', 'content', 'email',
																'columns', 'address', 'phone', 'socials', 'googlemap',
																'googlemap_height', 'googlemap_position'))
									);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['logo'] = strip_tags($new_instance['logo']);
		$instance['logo_retina'] = strip_tags($new_instance['logo_retina']);
		$instance['description'] = wp_kses_data($new_instance['description']);
		$instance['address'] = wp_kses_data($new_instance['address']);
		$instance['phone'] = wp_kses_data($new_instance['phone']);
		$instance['email'] = wp_kses_data($new_instance['email']);
		$instance['columns'] = isset( $new_instance['columns'] ) ? 1 : 0;
		$instance['socials'] = isset( $new_instance['socials'] ) ? 1 : 0;
		$instance['googlemap'] = isset( $new_instance['googlemap'] ) ? 1 : 0;
		$instance['googlemap_height'] = strip_tags($new_instance['googlemap_height']);
		$instance['googlemap_position'] = strip_tags($new_instance['googlemap_position']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'logo' => '',
			'logo_retina' => '',
			'description' => '',
			'address' => '',
			'phone' => '',
			'email' => '',
			'columns' => 0,
			'socials' => 0,
			'googlemap' => 0,
			'googlemap_height' => 140,
			'googlemap_position' => 'top',
			)
		);
		$title = $instance['title'];
		$logo = $instance['logo'];
		$logo_retina = $instance['logo_retina'];
		$description = $instance['description'];
		$address = $instance['address'];
		$phone = $instance['phone'];
		$email = $instance['email'];
		$columns = (int) $instance['columns'] ? 1 : 0;
		$socials = (int) $instance['socials'] ? 1 : 0;
		$googlemap = (int) $instance['googlemap'] ? 1 : 0;
		$googlemap_height = $instance['googlemap_height'];
		$googlemap_position = $instance['googlemap_position'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'logo' )); ?>"><?php esc_html_e('Logo:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'logo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'logo' )); ?>" value="<?php echo esc_attr($logo); ?>" class="widgets_param_fullwidth widgets_param_media_selector">
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'logo_button' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'logo' )), $logo));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'logo_retina' )); ?>"><?php esc_html_e('Logo for Retina:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'logo_retina' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'logo_retina' )); ?>" value="<?php echo esc_attr($logo_retina); ?>" class="widgets_param_fullwidth widgets_param_media_selector">
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'logo_retina_button' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'logo_retina' )), $logo_retina));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e('Short description about user', 'trx_addons'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo esc_html($description); ?></textarea>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('address')); ?>"><?php esc_html_e('Address:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('address')); ?>" name="<?php echo esc_attr($this->get_field_name('address')); ?>" value="<?php echo esc_attr($address); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('phone')); ?>"><?php esc_html_e('Phone:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('phone')); ?>" name="<?php echo esc_attr($this->get_field_name('phone')); ?>" value="<?php echo esc_attr($phone); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('email')); ?>"><?php esc_html_e('E-mail:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" value="<?php echo esc_attr($email); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>" value="1" <?php echo (1==$columns ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e('Break on columns', 'trx_addons'); ?></label><br />
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('socials')); ?>" name="<?php echo esc_attr($this->get_field_name('socials')); ?>" value="1" <?php echo (1==$socials ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('socials')); ?>"><?php esc_html_e('Show Social icons', 'trx_addons'); ?></label><br />
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('googlemap')); ?>" name="<?php echo esc_attr($this->get_field_name('googlemap')); ?>" value="1" <?php echo (1==$googlemap ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('googlemap')); ?>"><?php esc_html_e('Show Google map', 'trx_addons'); ?></label><br />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('googlemap_height')); ?>"><?php esc_html_e('Google map height:', 'trx_addons'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('googlemap_height')); ?>" name="<?php echo esc_attr($this->get_field_name('googlemap_height')); ?>" value="<?php echo esc_attr($googlemap_height); ?>" class="widgets_param_fullwidth">
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_top"><?php esc_html_e('Google map position:', 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_top" name="<?php echo esc_attr($this->get_field_name('googlemap_position')); ?>" value="top" <?php echo ('top'==$googlemap_position ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_top"><?php esc_html_e('Top', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_left" name="<?php echo esc_attr($this->get_field_name('googlemap_position')); ?>" value="left" <?php echo ('left'==$googlemap_position ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_left"><?php esc_html_e('Left', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_right" name="<?php echo esc_attr($this->get_field_name('googlemap_position')); ?>" value="right" <?php echo ('right'==$googlemap_position ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('googlemap_position')); ?>_right"><?php esc_html_e('Right', 'trx_addons'); ?></label>
		</p>
	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_contacts_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_contacts_load_scripts_front');
	function trx_addons_widget_contacts_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_contacts', trx_addons_get_file_url('widgets/contacts/contacts.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_contacts_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_contacts_merge_styles');
	function trx_addons_widget_contacts_merge_styles($list) {
		$list[] = 'widgets/contacts/contacts.css';
		return $list;
	}
}



// trx_widget_contacts
//-------------------------------------------------------------
/*
[trx_widget_contacts id="unique_id" title="Widget title" logo="image_url" logo_retina="image_url" description="short description" address="Address string" phone="Phone" email="Email" socials="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_widget_contacts' ) ) {
	function trx_addons_sc_widget_contacts($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_contacts', $atts, array(
			// Individual params
			"title" => "",
			"logo" => "",
			"logo_retina" => "",
			"description" => "",
			"googlemap" => 0,
			"googlemap_height" => 140,
			"googlemap_position" => "top",
			"address" => "",
			"phone" => "",
			"email" => "",
			"columns" => 0,
			"socials" => 0,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if ($atts['columns']=='') $atts['columns'] = 0;
		if ($atts['socials']=='') $atts['socials'] = 0;
		if ($atts['googlemap']=='') $atts['googlemap'] = 0;
		extract($atts);
		$atts['content'] = do_shortcode($content);
		$type = 'trx_addons_widget_contacts';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_contacts' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_contacts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_contacts', 'widget_contacts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_contacts', $atts, $content);
	}
}


// Add [trx_widget_contacts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_contacts_add_in_vc')) {
	function trx_addons_sc_widget_contacts_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_contacts", "trx_addons_sc_widget_contacts");
		
		vc_lean_map("trx_widget_contacts", 'trx_addons_sc_widget_contacts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Contacts extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_widget_contacts_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_contacts_add_in_vc_params')) {
	function trx_addons_sc_widget_contacts_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_contacts",
				"name" => esc_html__("Contacts", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with logo, short description and contacts", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_contacts',
				"class" => "trx_widget_contacts",
				"content_element" => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_widget_contacts'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
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
							"param_name" => "logo",
							"heading" => esc_html__("Logo", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for site's logo.", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_retina",
							"heading" => esc_html__("Logo Retina", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site: site's logo for the Retina display.", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						),
						array(
							"param_name" => "address",
							"heading" => esc_html__("Address", 'trx_addons'),
							"description" => wp_kses_data( __("Address string. Use '|' to split this string on two parts", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "phone",
							"heading" => esc_html__("Phone", 'trx_addons'),
							"description" => wp_kses_data( __("Your phone", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "email",
							"heading" => esc_html__("E-mail", 'trx_addons'),
							"description" => wp_kses_data( __("Your e-mail address", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Break on columns", 'trx_addons'),
							"description" => wp_kses_data( __("Display address at left side and phone with email at right side", 'trx_addons') ),
							"std" => "0",
							"value" => array("Break on columns" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "googlemap",
							"heading" => esc_html__("Show Googlemap", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to display Google map with address above", 'trx_addons') ),
							"std" => "0",
							"value" => array("Show Google map" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "googlemap_height",
							"heading" => esc_html__("Googlemap height", 'trx_addons'),
							"description" => wp_kses_data( __("Height of the Google map", 'trx_addons') ),
							'dependency' => array(
								'element' => 'googlemap',
								'value' => '1',
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "googlemap_position",
							"heading" => esc_html__("Googlemap position", 'trx_addons'),
							"description" => wp_kses_data( __("Select position of the Google map", 'trx_addons') ),
							'dependency' => array(
								'element' => 'googlemap',
								'value' => '1',
							),
							"std" => "top",
							"value" => array(
								esc_html__('Top', 'trx_addons') => 'top',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "socials",
							"heading" => esc_html__("Show Social Icons", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want to display icons with links on your profiles in the Social networks?", 'trx_addons') ),
							"std" => "0",
							"value" => array("Show Social Icons" => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_contacts');
	}
}
?>