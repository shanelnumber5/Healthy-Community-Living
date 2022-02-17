<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Load widget
if (!function_exists('trx_addons_widget_audio_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_audio_load' );
	function trx_addons_widget_audio_load() {
		register_widget( 'trx_addons_widget_audio' );
	}
}

// Widget Class
class trx_addons_widget_audio extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_audio', 'description' => esc_html__('Play audio from Soundcloud and other audio hostings or Local hosted audio', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_audio', esc_html__('ThemeREX Addons - Audio player', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$url = isset($instance['url']) ? $instance['url'] : '';
		$embed = isset($instance['embed']) ? str_replace("`", '"', $instance['embed']) : '';
		if (empty($url) && empty($embed)) return;
		$caption = isset($instance['caption']) ? $instance['caption'] : '';
		$author = isset($instance['author']) ? $instance['author'] : '';
		$cover = isset($instance['cover']) ? $instance['cover'] : '';
		if ($cover!='') $cover = trx_addons_get_attachment_url($cover, trx_addons_get_thumb_size('big'));

		trx_addons_get_template_part('widgets/audio/tpl.default.php',
									'trx_addons_args_widget_audio',
									array_merge($args, compact('title', 'url', 'embed', 'cover', 'caption', 'author'))
									);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['caption'] = strip_tags( $new_instance['caption'] );
		$instance['author'] = strip_tags( $new_instance['author'] );
		$instance['url'] = strip_tags( $new_instance['url'] );
		$instance['embed'] = trim( $new_instance['embed'] );
		$instance['cover'] = strip_tags( $new_instance['cover'] );

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'caption' => '',
			'author' => '',
			'cover' => '',
			'url' => '',
			'embed' => ''
			)
		);
		$title = $instance['title'];
		$caption = $instance['caption'];
		$author = $instance['author'];
		$cover = $instance['cover'];
		$url = $instance['url'];
		$embed = $instance['embed'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'caption' )); ?>"><?php esc_html_e('Caption:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'caption' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'caption' )); ?>" value="<?php echo esc_attr($caption); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'author' )); ?>"><?php esc_html_e('Author:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'author' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'author' )); ?>" value="<?php echo esc_attr($author); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'cover' )); ?>"><?php echo wp_kses_data( __('Cover image URL:<br />(leave empty if you not need the cover)', 'trx_addons') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'cover' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'cover' )); ?>" value="<?php echo esc_attr($cover); ?>" class="widgets_param_fullwidth widgets_param_media_selector" />
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'cover_media' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'cover' )), $cover));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'url' )); ?>"><?php echo wp_kses_data( __('Select local hosted audio', 'trx_addons') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'url' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'url' )); ?>" value="<?php echo esc_attr($url); ?>" class="widgets_param_fullwidth widgets_param_media_selector" />
            <?php
			trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id( 'local_audio' ), array('type'=>'mediamanager', 'data_type' => 'audio', 'linked_field_id'=>$this->get_field_id( 'url' )), null));
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'embed' )); ?>"><?php esc_html_e('or paste HTML code to embed audio:', 'trx_addons'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'embed' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'embed' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo htmlspecialchars($embed); ?></textarea>
		</p>
		
	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_audio_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_audio_load_scripts_front');
	function trx_addons_widget_audio_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_audio', trx_addons_get_file_url('widgets/audio/audio.css'), array(), null );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_audio_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_audio_merge_styles');
	function trx_addons_widget_audio_merge_styles($list) {
		$list[] = 'widgets/audio/audio.css';
		return $list;
	}
}



// trx_widget_audio
//-------------------------------------------------------------
/*
[trx_widget_audio id="unique_id" title="Widget title" embed="HTML code" cover="image url"]
*/
if ( !function_exists( 'trx_addons_sc_widget_audio' ) ) {
	function trx_addons_sc_widget_audio($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_audio', $atts, array(
			// Individual params
			'title' => '',
			'caption' => '',
			'author' => '',
			'url' => '',
			'embed' => '',
			'cover' => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if (!empty($atts['embed'])) $atts['embed'] = trim( vc_value_from_safe( $atts['embed'] ) );
		extract($atts);
		$type = 'trx_addons_widget_audio';
		$output = '';
		global $wp_widget_factory, $TRX_ADDONS_STORAGE;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_audio' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_audio wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_audio', 'widget_audio') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_audio', $atts, $content);
	}
}


// Add [trx_widget_audio] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_audio_add_in_vc')) {
	function trx_addons_sc_widget_audio_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_audio", "trx_addons_sc_widget_audio");
		
		vc_lean_map("trx_widget_audio", 'trx_addons_sc_widget_audio_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Audio extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_audio_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_audio_add_in_vc_params')) {
	function trx_addons_sc_widget_audio_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_audio",
				"name" => esc_html__("Audio player", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with embedded audio from popular audio hosting: SoundCloud, etc. or with local hosted audio", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_audio',
				"class" => "trx_widget_audio",
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
							"param_name" => "caption",
							"heading" => esc_html__("Audio caption", 'trx_addons'),
							"description" => wp_kses_data( __("Caption of this audio", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "author",
							"heading" => esc_html__("Author name", 'trx_addons'),
							"description" => wp_kses_data( __("Name of the author", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "cover",
							"heading" => esc_html__("Cover image", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload cover image or write URL from other site", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "attach_image"
						),
						array(
							"param_name" => "url",
							"heading" => esc_html__("Audio URL", 'trx_addons'),
							"description" => wp_kses_data( __("URL for local hosted audio file", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "embed",
							"heading" => esc_html__("Embed code", 'trx_addons'),
							"description" => wp_kses_data( __("or paste HTML code to embed audio", 'trx_addons') ),
							"type" => "textarea_safe"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_audio' );
	}
}
?>