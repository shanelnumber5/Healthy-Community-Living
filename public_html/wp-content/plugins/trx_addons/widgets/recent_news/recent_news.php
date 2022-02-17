<?php
/**
 * Widget: Recent News
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_recent_news_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_recent_news_load' );
	function trx_addons_widget_recent_news_load() {
		register_widget('trx_addons_widget_recent_news');
	}
}


// Widget Class
//------------------------------------------------------
class trx_addons_widget_recent_news extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_news', 'description' => esc_html__('Show recent news in many styles', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_recent_news', esc_html__('ThemeREX Addons - Recent News', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '');

		$output = trx_addons_sc_recent_news( array(
			'title' 			=> isset($instance['title']) ? $instance['title'] : '',
			'subtitle'			=> isset($instance['subtitle']) ? $instance['subtitle'] : '',
			'style'				=> isset($instance['style']) ? $instance['style'] : 'news-1',
			'count'				=> isset($instance['count']) ? (int) $instance['count'] : 3,
			'featured'			=> isset($instance['featured']) ? (int) $instance['featured'] : 0,
			'columns'			=> isset($instance['columns']) ? (int) $instance['columns'] : 1,
			'category'			=> isset($instance['category']) ? (int) $instance['category'] : 0,
			'show_categories'	=> isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0
			)
		);

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			trx_addons_show_layout($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($widget_title) trx_addons_show_layout($before_title . $widget_title . $after_title);
	
			// Display widget body
			trx_addons_show_layout($output);
			
			// After widget (defined by themes)
			trx_addons_show_layout($after_widget);
		}
	}

	// Update the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['widget_title']	= strip_tags($new_instance['widget_title']);
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['subtitle']		= strip_tags($new_instance['subtitle']);
		$instance['style']			= strip_tags($new_instance['style']);
		$instance['count']			= max(1, (int) $new_instance['count']);
		$instance['featured']		= max(0, min($instance['count'], (int) $new_instance['featured']));
		$instance['columns']		= max(1, min($instance['featured']+1, (int) $new_instance['columns']));		//	Columns <= Featured+1
		$instance['category']		= max(0, (int) $new_instance['category']);
		$instance['show_categories']= (int) $new_instance['show_categories'] > 0 ? 1 : 0;
		return $instance;
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'widget_title' => '',
			'title' => '',
			'subtitle' => '',
			'style' => '',
			'count' => 3,
			'featured' => 0,
			'columns' => 1,
			'category' => 0,
			'show_categories' => 1
			)
		);
		$widget_title = $instance['widget_title'];
		$title = $instance['title'];
		$subtitle = $instance['subtitle'];
		$style = $instance['style'];
		$count = (int) $instance['count'];
		$featured = (int) $instance['featured'];
		$columns = (int) $instance['columns'];
		$category = (int) $instance['category'];
		$show_categories = (int) $instance['show_categories'] > 0 ? 1 : 0;

		$list_styles = trx_addons_components_get_allowed_layouts('widgets', 'recent_news');
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('widget_title')); ?>"><?php esc_html_e('Widget title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('widget_title')); ?>" name="<?php echo esc_attr($this->get_field_name('widget_title')); ?>" value="<?php echo esc_attr($widget_title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Block title:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>"><?php esc_html_e('Block subtitle:', 'trx_addons'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" value="<?php echo esc_attr($subtitle); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Style:', 'trx_addons'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>" class="widgets_param_fullwidth">
			<?php
				if (is_array($list_styles) && count($list_styles) > 0) {
					foreach ($list_styles as $slug => $name) {
						echo '<option value="'.esc_attr($slug).'"'.($slug==$style ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Number of posts to be displayed:', 'trx_addons'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" value="<?php echo esc_attr($count); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('featured')); ?>"><?php esc_html_e('Number of featured posts:', 'trx_addons'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('featured')); ?>" name="<?php echo esc_attr($this->get_field_name('featured')); ?>" value="<?php echo esc_attr($featured); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e('Number of columns:', 'trx_addons'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>" value="<?php echo esc_attr($columns); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Parent category:', 'trx_addons'); ?></label>
			<?php
			wp_dropdown_categories(array(
				'hide_empty' => 0,
				'name' => $this->get_field_name('category'),
				'orderby' => 'name',
				'selected' => $category,
				'hierarchical' => true,
				'show_option_none' => esc_html__('-- All categories --', 'trx_addons')
				)
			);
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e('Show categories:', 'trx_addons'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="1" <?php echo (1==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e('Show', 'trx_addons'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="0" <?php echo (0==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0"><?php esc_html_e('Hide', 'trx_addons'); ?></label>
		</p>

	<?php
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_recent_news_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_recent_news_load_scripts_front');
	function trx_addons_widget_recent_news_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-widget_recent_news', trx_addons_get_file_url('widgets/recent_news/recent_news.css'), array(), null );
			wp_enqueue_script( 'trx_addons-widget_recent_news', trx_addons_get_file_url('widgets/recent_news/recent_news.js'), array('jquery'), null, true );
		}
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_recent_news_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_widget_recent_news_merge_styles');
	function trx_addons_widget_recent_news_merge_styles($list) {
		$list[] = 'widgets/recent_news/recent_news.css';
		return $list;
	}
}

	
// Merge widget specific scripts into single file
if ( !function_exists( 'trx_addons_widget_recent_news_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_widget_recent_news_merge_scripts');
	function trx_addons_widget_recent_news_merge_scripts($list) {
		$list[] = 'widgets/recent_news/recent_news.js';
		return $list;
	}
}



// trx_recent_news
//-------------------------------------------------------------
/*
[trx_recent_news id="unique_id" columns="2" count="5" featured="1" style="news-1" title="Block title" subtitle="xxx" category="id|slug" show_categories="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_recent_news' ) ) {
	function trx_addons_sc_recent_news($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_recent_news', $atts, array(
			// Individual params
			"style" => "news-magazine",
			"count" => 3,
			"featured" => 3,
			"columns" => 3,
			"ids" => "",
			"category" => 0,
			"offset" => 0,
			"orderby" => "date",
			"order" => "desc",
			"widget_title" => "",
			"title" => "",
			"subtitle" => "",
			"show_categories" => 0,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);

		set_query_var('trx_addons_inside_sc', true);
		add_filter( 'excerpt_length', 'trx_addons_recent_news_excerpt_length' );
		
		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		$count = max(1, (int) $count);
		$featured = max(0, min($count, (int) $featured));
		$columns = max(1, min(12, (int) $columns));
		if (in_array($style, array('news-announce', 'news-excerpt'))) $columns = 1;
		if ($featured > 0) $columns = min($featured+1, $columns);		// Columns <= Featured + 1
		$category = max(0, (int) $category);

		// Get categories list
		if ( !empty($title) && trx_addons_is_on($show_categories)) {
			if ( ($cats = get_query_var('categories_'.$category)) == '' ) {
				$cats = get_categories( array(
					'orderby' => 'name',
					'parent' => $category
					)
				);
				set_query_var('categories_'.$category, $cats);
			}
		}

		$output = '';
		
		// If insert with VC as widget
		if (!empty($widget_title)) {
			global $TRX_ADDONS_STORAGE;
			$widget_args = trx_addons_prepare_widgets_args($TRX_ADDONS_STORAGE['widgets_args'], $id ? $id.'_widget' : 'widget_recent_news', 'widget_recent_news');
			$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_recent_news_wrap' 
								. (trx_addons_exists_visual_composer() ? ' vc_recent_news wpb_content_element' : '') 
						. '">'
							. $widget_args['before_widget']
							. $widget_args['before_title'] .esc_html($widget_title). $widget_args['after_title'];
		}
		
		// Wrapper
		$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_recent_news'
							. ' sc_recent_news_style_'.esc_attr($style)
							. ($featured > 0 ? ' sc_recent_news_with_accented' : ' sc_recent_news_without_accented')
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>';

		// Header
		if ( !empty($title) ) {	// || !empty($subtitle) || (trx_addons_is_on($show_categories) && !empty($cats)) ) {
			$output	.= '<div class="sc_recent_news_header'.(trx_addons_is_on($show_categories) && !empty($cats) ? ' sc_recent_news_header_split' : '').'">'
							. ( !empty($title) || !empty($subtitle)
								? '<div class="sc_recent_news_header_captions">'
										. (!empty($title) ? '<h3 class="sc_recent_news_title">' . esc_html($title) . '</h3>' : '')
										. (!empty($subtitle) ? '<h6 class="sc_recent_news_subtitle">' . esc_html($subtitle) . '</h6>' : '')
									. '</div>'
								: '');

			// Categories list
			if (trx_addons_is_on($show_categories) && !empty($cats)) {
				$output .= '<div class="sc_recent_news_header_categories">';
				if (is_array($cats) && count($cats) > 0) {
					$output .= '<a href="' . esc_url( $category == 0 
						? ( get_option('show_on_front')=='page' 
							? get_permalink(get_option('page_for_posts')) 
							: home_url('/')
							)
						: get_category_link($category) ) . '" class="sc_recent_news_header_category_item">'.esc_html__('All News', 'trx_addons').'</span>';
					$number = 0;
					$number_max = 3;
					foreach ($cats as $cat) {
						$number++;
						if ($number == $number_max)
							$output .= '<span class="sc_recent_news_header_category_item sc_recent_news_header_category_item_more">'.esc_html__('More', 'trx_addons')
										. '<span class="sc_recent_news_header_more_categories">';
						$output .= '<a href="'.esc_url(get_category_link( $cat->term_id )).'" class="sc_recent_news_header_category_item">'.esc_html($cat->name).'</a>';
					}
					if ($number >= $number_max)
						$output .= '</span></span>';
				}
				$output .= '</div>';
			}
	
			$output .= '</div><!-- /.sc_recent_news_header -->';
		}
		
		// Columns
		if ($columns > 1)
			$output .= '<div class="'.esc_attr(trx_addons_get_columns_wrap_class()).'">';
	
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc'
		);
		
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
		
		$args = trx_addons_query_add_sort_order($args, $orderby, $order);
		$args = trx_addons_query_add_posts_and_cats($args, $ids, 'post', $category, 'category');
		$query = new WP_Query( $args );
	
		$count = min($count, $query->found_posts);
		$featured = max(0, min($count, (int) $featured));
		$columns = max(1, min(12, (int) $columns));
		if (in_array($style, array('news-announce', 'news-excerpt'))) $columns = 1;
		if ($featured > 0) $columns = min($featured+1, $columns);		// Columns <= Featured + 1
		
		$post_number = 0;
				
		while ( $query->have_posts() ) { $query->the_post();
			$post_number++;
			ob_start();
			trx_addons_get_template_part(array(
											'widgets/recent_news/tpl.'.trx_addons_esc($style).'.php',
                                            'widgets/recent_news/tpl.excerpt.php'
                                            ),
                                            'trx_addons_args_recent_news',
                                            array(
												'style' => $style,
												'number' => $post_number,
												'count' => $count,
												'columns' => $columns,
												'featured' => $featured
											)
										);
			$output .= ob_get_contents();
			ob_end_clean();
		}
		wp_reset_postdata();
	
		if ($columns > 1) $output .= '</div><!-- /.columns_wrap -->';

		$output .=  '</div><!-- /.sc_recent_news -->';

		if (!empty($widget_title)) $output .=  $widget_args['after_widget'] . '</div><!-- /.sc_recent_news_wrap -->';
	
		// Add template specific scripts and styles
		do_action('trx_addons_action_blog_scripts', $style);
	
		remove_filter( 'excerpt_length', 'trx_addons_recent_news_excerpt_length' );
		set_query_var('trx_addons_inside_sc', false);

		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_recent_news', $atts, $content);
	}
}

// Return excerpt length (in words) for the widget Recent News
if ( !function_exists('trx_addons_recent_news_excerpt_length') ) {
	function trx_addons_recent_news_excerpt_length( $length ) {
		return 25;
	}
}


// Add [trx_recent_news] in the VC shortcodes list
if (!function_exists('trx_addons_sc_recent_news_add_in_vc')) {
	function trx_addons_sc_recent_news_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_widget_recent_news", "trx_addons_sc_recent_news");
		
		vc_lean_map("trx_widget_recent_news", 'trx_addons_sc_recent_news_add_in_vc_params');
		class WPBakeryShortCode_Trx_Recent_News extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_recent_news_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_recent_news_add_in_vc_params')) {
	function trx_addons_sc_recent_news_add_in_vc_params() {
		$list_sort = array(
			"none" 		=> esc_html__('None', 'trx_addons'),
			"ID" 		=> esc_html__('Post ID', 'trx_addons'),
			"date"		=> esc_html__("Date", 'trx_addons'),
			"title"		=> esc_html__("Alphabetically", 'trx_addons'),
			"views"		=> esc_html__("Popular (views count)", 'trx_addons'),
			"comments"	=> esc_html__("Most commented (comments count)", 'trx_addons'),
			"random"	=> esc_html__("Random", 'trx_addons')
		);
		$list_order = array(
			"asc"  => esc_html__("Ascending", 'trx_addons'),
			"desc" => esc_html__("Descending", 'trx_addons')
		);
		
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_recent_news",
				"name" => esc_html__("Recent News", 'trx_addons'),
				"description" => wp_kses_data( __("Insert recent news list", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_recent_news',
				"class" => "trx_widget_recent_news",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "widget_title",
							"heading" => esc_html__("Widget Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title for the widget (fill this field only if you want to use shortcode as widget)", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title for the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle for the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("List style", 'trx_addons'),
							"description" => wp_kses_data( __("Select style to display news list", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'news-magazine',
					        'save_always' => true,
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('widgets', 'recent_news')), 'trx_widget_recent_news'),
							"type" => "dropdown"
						),
						array(
							"param_name" => "show_categories",
							"heading" => esc_html__("Show categories", 'trx_addons'),
							"description" => wp_kses_data( __("Show categories in the shortcode's header", 'trx_addons') ),
							"std" => "0",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array("Show categories" => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "ids",
							"heading" => esc_html__("List IDs", 'trx_addons'),
							"description" => wp_kses_data( __("Comma separated IDs list to show. If not empty - parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "category",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show news. If empty - select news from any category or from IDs list", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"std" => 0,
							"value" => array_flip(trx_addons_array_merge(array(0 => esc_html__('- Select category -', 'trx_addons')), trx_addons_get_list_categories())),
							"type" => "dropdown"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Total posts", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"group" => esc_html__('Query', 'trx_addons'),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("How many columns use to show news list", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'style',
								'value' => array('news-magazine', 'news-portfolio'),
							),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "offset",
							"heading" => esc_html__("Offset before select posts", 'trx_addons'),
							"description" => wp_kses_data( __("Skip posts before select next part.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							'dependency' => array(
								'element' => 'ids',
								'is_empty' => true
							),
							"group" => esc_html__('Query', 'trx_addons'),
							"value" => "0",
							"type" => "textfield"
						),
						array(
							"param_name" => "featured",
							"heading" => esc_html__("Featured posts", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts will be displayed as featured?", 'trx_addons') ),
							"admin_label" => true,
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'style',
								'value' => 'news-magazine'
							),
							"value" => "3",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => esc_html__("Post sorting", 'trx_addons'),
							"description" => wp_kses_data( __("Select desired posts sorting method", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array_flip($list_sort),
					        'save_always' => true,
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => esc_html__("Post order", 'trx_addons'),
							"description" => wp_kses_data( __("Select desired posts order", 'trx_addons') ),
							"group" => esc_html__('Query', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip($list_order),
					        'save_always' => true,
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_recent_news' );
		}
}
?>