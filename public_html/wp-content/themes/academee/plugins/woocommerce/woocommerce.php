<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if (!function_exists('academee_woocommerce_theme_setup1')) {
	add_action( 'after_setup_theme', 'academee_woocommerce_theme_setup1', 1 );
	function academee_woocommerce_theme_setup1() {

		add_theme_support( 'woocommerce' );

		// Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
		add_theme_support( 'wc-product-gallery-zoom' );

		// Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
		add_theme_support( 'wc-product-gallery-slider' ); 

		// Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
		add_theme_support( 'wc-product-gallery-lightbox' );

		add_filter( 'academee_filter_list_sidebars', 	'academee_woocommerce_list_sidebars' );
		add_filter( 'academee_filter_list_posts_types',	'academee_woocommerce_list_post_types');

        // Detect if WooCommerce support 'Product Grid' feature
        $product_grid = academee_exists_woocommerce() && function_exists( 'wc_get_theme_support' ) ? wc_get_theme_support( 'product_grid' ) : false;
        add_theme_support( 'wc-product-grid-enable', isset( $product_grid['min_columns'] ) && isset( $product_grid['max_columns'] ) );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if (!function_exists('academee_woocommerce_theme_setup3')) {
	add_action( 'after_setup_theme', 'academee_woocommerce_theme_setup3', 3 );
	function academee_woocommerce_theme_setup3() {
		if (academee_exists_woocommerce()) {
		
			academee_storage_merge_array('options', '', array(
				// Section 'WooCommerce' - settings for show pages
				'shop' => array(
					"title" => esc_html__('Shop', 'academee'),
					"desc" => wp_kses_data( __('Select parameters to display the shop pages', 'academee') ),
					"type" => "section"
					),
				'expand_content_shop' => array(
					"title" => esc_html__('Expand content', 'academee'),
					"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'academee') ),
					"refresh" => false,
					"std" => 1,
					"type" => "checkbox"
					),
				'posts_per_page_shop' => array(
					"title" => esc_html__('Products per page', 'academee'),
					"desc" => wp_kses_data( __('How many products should be displayed on the shop page. If empty - use global value from the menu Settings - Reading', 'academee') ),
					"std" => '',
					"type" => "text"
					),
				'blog_columns_shop' => array(
					"title" => esc_html__('Shop loop columns', 'academee'),
					"desc" => wp_kses_data( __('How many columns should be used in the shop loop (from 2 to 4)?', 'academee') ),
					"std" => 2,
					"options" => academee_get_list_range(2,4),
					"type" => "hidden"
					),
				'related_posts_shop' => array(
					"title" => esc_html__('Related products', 'academee'),
					"desc" => wp_kses_data( __('How many related products should be displayed in the single product page?', 'academee') ),
					"std" => 3,
					"options" => academee_get_list_range(0,9),
					"type" => "select"
					),
				'related_columns_shop' => array(
					"title" => esc_html__('Related columns', 'academee'),
					"desc" => wp_kses_data( __('How many columns should be used to output related products in the single product page?', 'academee') ),
					"std" => 3,
					"options" => academee_get_list_range(1,4),
					"type" => "select"
					),
				'shop_mode' => array(
					"title" => esc_html__('Shop mode', 'academee'),
					"desc" => wp_kses_data( __('Select style for the products list', 'academee') ),
					"std" => 'thumbs',
					"options" => array(
						'thumbs'=> esc_html__('Thumbnails', 'academee'),
						'list'	=> esc_html__('List', 'academee'),
					),
					"type" => "select"
					),
				'shop_hover' => array(
					"title" => esc_html__('Hover style', 'academee'),
					"desc" => wp_kses_data( __('Hover style on the products in the shop archive', 'academee') ),
					"std" => 'none',
					"options" => apply_filters('academee_filter_shop_hover', array(
						'none' => esc_html__('None', 'academee')


					)),
					"type" => "select"
					),
				'header_style_shop' => array(
					"title" => esc_html__('Header style', 'academee'),
					"desc" => wp_kses_data( __('Select style to display the site header on the shop archive', 'academee') ),
					"std" => 'inherit',
					"options" => array(),
					"type" => "select"
					),
				'header_position_shop' => array(
					"title" => esc_html__('Header position', 'academee'),
					"desc" => wp_kses_data( __('Select position to display the site header on the shop archive', 'academee') ),
					"std" => 'inherit',
					"options" => array(),
					"type" => "select"
					),
				'header_widgets_shop' => array(
					"title" => esc_html__('Header widgets', 'academee'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the header on the shop pages', 'academee') ),
					"std" => 'hide',
					"options" => array(),
					"type" => "select"
					),
				'sidebar_widgets_shop' => array(
					"title" => esc_html__('Sidebar widgets', 'academee'),
					"desc" => wp_kses_data( __('Select sidebar to show on the shop pages', 'academee') ),
					"std" => 'woocommerce_widgets',
					"options" => array(),
					"type" => "select"
					),
				'sidebar_position_shop' => array(
					"title" => esc_html__('Sidebar position', 'academee'),
					"desc" => wp_kses_data( __('Select position to show sidebar on the shop pages', 'academee') ),
					"refresh" => false,
					"std" => 'left',
					"options" => array(),
					"type" => "select"
					),
				'hide_sidebar_on_single_shop' => array(
					"title" => esc_html__('Hide sidebar on the single product', 'academee'),
					"desc" => wp_kses_data( __("Hide sidebar on the single product's page", 'academee') ),
					"std" => 0,
					"type" => "checkbox"
					),
				'widgets_above_page_shop' => array(
					"title" => esc_html__('Widgets at the top of the page', 'academee'),
					"desc" => wp_kses_data( __('Select widgets to show at the top of the page (above content and sidebar)', 'academee') ),
					"std" => 'hide',
					"options" => array(),
					"type" => "select"
					),
				'widgets_above_content_shop' => array(
					"title" => esc_html__('Widgets above the content', 'academee'),
					"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'academee') ),
					"std" => 'hide',
					"options" => array(),
					"type" => "select"
					),
				'widgets_below_content_shop' => array(
					"title" => esc_html__('Widgets below the content', 'academee'),
					"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'academee') ),
					"std" => 'hide',
					"options" => array(),
					"type" => "select"
					),
				'widgets_below_page_shop' => array(
					"title" => esc_html__('Widgets at the bottom of the page', 'academee'),
					"desc" => wp_kses_data( __('Select widgets to show at the bottom of the page (below content and sidebar)', 'academee') ),
					"std" => 'hide',
					"options" => array(),
					"type" => "select"
					),
				'footer_scheme_shop' => array(
					"title" => esc_html__('Footer Color Scheme', 'academee'),
					"desc" => wp_kses_data( __('Select color scheme to decorate footer area', 'academee') ),
					"std" => 'dark',
					"options" => array(),
					"type" => "select"
					),
				'footer_widgets_shop' => array(
					"title" => esc_html__('Footer widgets', 'academee'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the footer', 'academee') ),
					"std" => 'footer_widgets',
					"options" => array(),
					"type" => "select"
					),
				'footer_columns_shop' => array(
					"title" => esc_html__('Footer columns', 'academee'),
					"desc" => wp_kses_data( __('Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'academee') ),
					"dependency" => array(
						'footer_widgets_shop' => array('^hide')
					),
					"std" => 0,
					"options" => academee_get_list_range(0,6),
					"type" => "select"
					),
				'footer_wide_shop' => array(
					"title" => esc_html__('Footer fullwide', 'academee'),
					"desc" => wp_kses_data( __('Do you want to stretch the footer to the entire window width?', 'academee') ),
					"std" => 0,
					"type" => "checkbox"
					)
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('academee_woocommerce_theme_setup9')) {
	add_action( 'after_setup_theme', 'academee_woocommerce_theme_setup9', 9 );
	function academee_woocommerce_theme_setup9() {
		
		if (academee_exists_woocommerce()) {
			add_action( 'wp_enqueue_scripts', 								'academee_woocommerce_frontend_scripts', 1100 );
			add_filter( 'academee_filter_merge_styles',						'academee_woocommerce_merge_styles' );
			add_filter( 'academee_filter_get_post_info',		 				'academee_woocommerce_get_post_info');
			add_filter( 'academee_filter_post_type_taxonomy',				'academee_woocommerce_post_type_taxonomy', 10, 2 );
			if (!is_admin()) {
				add_filter( 'academee_filter_detect_blog_mode',				'academee_woocommerce_detect_blog_mode' );
				add_filter( 'academee_filter_get_post_categories', 			'academee_woocommerce_get_post_categories');
				add_filter( 'academee_filter_get_blog_title', 				'academee_woocommerce_get_blog_title');
				add_filter( 'academee_filter_allow_override_header_image',	'academee_woocommerce_allow_override_header_image' );
				add_action( 'academee_action_before_post_meta',				'academee_woocommerce_action_before_post_meta');
				add_action( 'pre_get_posts',								'academee_woocommerce_pre_get_posts' );
			}
		}
		if (is_admin()) {
			add_filter( 'academee_filter_tgmpa_required_plugins',			'academee_woocommerce_tgmpa_required_plugins' );
		}

		// Add wrappers and classes to the standard WooCommerce output
		if (academee_exists_woocommerce()) {

			// Remove WOOC sidebar
			remove_action( 'woocommerce_sidebar', 						'woocommerce_get_sidebar', 10 );

			// Remove link around product item
			remove_action('woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open', 10);
			remove_action('woocommerce_after_shop_loop_item',			'woocommerce_template_loop_product_link_close', 5);

			// Remove add_to_cart button
			
			
			// Remove link around product category
			remove_action('woocommerce_before_subcategory',				'woocommerce_template_loop_category_link_open', 10);
			remove_action('woocommerce_after_subcategory',				'woocommerce_template_loop_category_link_close', 10);
			
			// Open main content wrapper - <article>
			remove_action( 'woocommerce_before_main_content',			'woocommerce_output_content_wrapper', 10);
			add_action(    'woocommerce_before_main_content',			'academee_woocommerce_wrapper_start', 10);
			// Close main content wrapper - </article>
			remove_action( 'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end', 10);		
			add_action(    'woocommerce_after_main_content',			'academee_woocommerce_wrapper_end', 10);

			// Close header section
			add_action(    'woocommerce_archive_description',			'academee_woocommerce_archive_description', 15 );

			// Add theme specific search form
			add_filter(    'get_product_search_form',					'academee_woocommerce_get_product_search_form' );

			// Change text on 'Add to cart' button
			add_filter(    'woocommerce_product_add_to_cart_text',		'academee_woocommerce_add_to_cart_text' );
			add_filter(    'woocommerce_product_single_add_to_cart_text','academee_woocommerce_add_to_cart_text' );

			// Add list mode buttons
			add_action(    'woocommerce_before_shop_loop', 				'academee_woocommerce_before_shop_loop', 10 );

			// Set columns number for the products loop
            if ( ! get_theme_support( 'wc-product-grid-enable' ) ) {
                add_filter('loop_shop_columns', 'academee_woocommerce_loop_shop_columns');
                add_filter('post_class', 'academee_woocommerce_loop_shop_columns_class');
                add_filter('product_cat_class', 'academee_woocommerce_loop_shop_columns_class', 10, 3);
            }
			// Open product/category item wrapper
			add_action(    'woocommerce_before_subcategory_title',		'academee_woocommerce_item_wrapper_start', 9 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'academee_woocommerce_item_wrapper_start', 9 );
			// Close featured image wrapper and open title wrapper
			add_action(    'woocommerce_before_subcategory_title',		'academee_woocommerce_title_wrapper_start', 20 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'academee_woocommerce_title_wrapper_start', 20 );

			// Add tags before title
			add_action(    'woocommerce_before_shop_loop_item_title',	'academee_woocommerce_title_tags', 30 );

			// Wrap product title into link
			add_action(    'the_title',									'academee_woocommerce_the_title');
			// Wrap category title into link
			add_action(		'woocommerce_shop_loop_subcategory_title',  'academee_woocommerce_shop_loop_subcategory_title', 9, 1);

			// Close title wrapper and add description in the list mode
			add_action(    'woocommerce_after_shop_loop_item_title',	'academee_woocommerce_title_wrapper_end', 7);
			add_action(    'woocommerce_after_subcategory_title',		'academee_woocommerce_title_wrapper_end2', 10 );
			// Close product/category item wrapper
			add_action(    'woocommerce_after_subcategory',				'academee_woocommerce_item_wrapper_end', 20 );
			add_action(    'woocommerce_after_shop_loop_item',			'academee_woocommerce_item_wrapper_end', 20 );

			// Add product ID into product meta section (after categories and tags)
			add_action(    'woocommerce_product_meta_end',				'academee_woocommerce_show_product_id', 10);
			
			// Set columns number for the product's thumbnails
			add_filter(    'woocommerce_product_thumbnails_columns',	'academee_woocommerce_product_thumbnails_columns' );

			// Decorate price
			add_filter(    'woocommerce_get_price_html',				'academee_woocommerce_get_price_html' );

	
			// Detect current shop mode
			if (!is_admin()) {
				$shop_mode = academee_get_value_gpc('academee_shop_mode');
				if (empty($shop_mode) && academee_check_theme_option('shop_mode'))
					$shop_mode = academee_get_theme_option('shop_mode');
				if (empty($shop_mode))
					$shop_mode = 'thumbs';
				academee_storage_set('shop_mode', $shop_mode);
			}
		}
	}
}

// Theme init priorities:
// Action 'wp'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)
if (!function_exists('academee_woocommerce_theme_setup_wp')) {
	add_action( 'wp', 'academee_woocommerce_theme_setup_wp' );
	function academee_woocommerce_theme_setup_wp() {
		if (academee_exists_woocommerce()) {
			// Set columns number for the related products
			if ((int) academee_get_theme_option('related_posts') == 0) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			} else {
				add_filter(    'woocommerce_output_related_products_args',	'academee_woocommerce_output_related_products_args' );
				add_filter(    'woocommerce_related_products_columns',		'academee_woocommerce_related_products_columns' );
			}
		}
	}
}


// Check if WooCommerce installed and activated
if ( !function_exists( 'academee_exists_woocommerce' ) ) {
	function academee_exists_woocommerce() {
		return class_exists('Woocommerce');
		
	}
}

// Return true, if current page is any woocommerce page
if ( !function_exists( 'academee_is_woocommerce_page' ) ) {
	function academee_is_woocommerce_page() {
		$rez = false;
		if (academee_exists_woocommerce())
			$rez = is_woocommerce() || is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		return $rez;
	}
}

// Detect current blog mode
if ( !function_exists( 'academee_woocommerce_detect_blog_mode' ) ) {
	
	function academee_woocommerce_detect_blog_mode($mode='') {
		if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())
			$mode = 'shop';
		else if (is_product() || is_cart() || is_checkout() || is_account_page())
			$mode = 'shop';	
		return $mode;
	}
}


// Return taxonomy for current post type
if ( !function_exists( 'academee_woocommerce_post_type_taxonomy' ) ) {
	
	function academee_woocommerce_post_type_taxonomy($tax='', $post_type='') {
		if ($post_type == 'product')
			$tax = 'product_cat';
		return $tax;
	}
}

// Return true if page title section is allowed
if ( !function_exists( 'academee_woocommerce_allow_override_header_image' ) ) {
	
	function academee_woocommerce_allow_override_header_image($allow=true) {
		return is_product() ? false : $allow;
	}
}

// Return shop page ID
if ( !function_exists( 'academee_woocommerce_get_shop_page_id' ) ) {
	function academee_woocommerce_get_shop_page_id() {
		return get_option('woocommerce_shop_page_id');
	}
}

// Return shop page link
if ( !function_exists( 'academee_woocommerce_get_shop_page_link' ) ) {
	function academee_woocommerce_get_shop_page_link() {
		$url = '';
		$id = academee_woocommerce_get_shop_page_id();
		if ($id) $url = get_permalink($id);
		return $url;
	}
}

// Show categories of the current product
if ( !function_exists( 'academee_woocommerce_get_post_categories' ) ) {
	
	function academee_woocommerce_get_post_categories($cats='') {
		if (get_post_type()=='product') {
			$cats = academee_get_post_terms(', ', get_the_ID(), 'product_cat');
		}
		return $cats;
	}
}

// Add 'product' to the list of the supported post-types
if ( !function_exists( 'academee_woocommerce_list_post_types' ) ) {
	
	function academee_woocommerce_list_post_types($list=array()) {
		$list['product'] = esc_html__('Products', 'academee');
		return $list;
	}
}

// Show price of the current product in the widgets and search results
if ( !function_exists( 'academee_woocommerce_get_post_info' ) ) {
	
	function academee_woocommerce_get_post_info($post_info='') {
		if (get_post_type()=='product') {
			global $product;
			if ( $price_html = $product->get_price_html() ) {
				$post_info = '<div class="post_price product_price price">' . trim($price_html) . '</div>' . $post_info;
			}
		}
		return $post_info;
	}
}

// Show price of the current product in the search results streampage
if ( !function_exists( 'academee_woocommerce_action_before_post_meta' ) ) {
	
	function academee_woocommerce_action_before_post_meta() {
		if (get_post_type()=='product') {
			global $product;
			if ( $price_html = $product->get_price_html() ) {
				?><div class="post_price product_price price"><?php academee_show_layout($price_html); ?></div><?php
			}
		}
	}
}
	
// Enqueue WooCommerce custom styles
if ( !function_exists( 'academee_woocommerce_frontend_scripts' ) ) {
	
	function academee_woocommerce_frontend_scripts() {
		
			if (academee_is_on(academee_get_theme_option('debug_mode')) && academee_get_file_dir('plugins/woocommerce/woocommerce.css')!='')
				wp_enqueue_style( 'academee-woocommerce',  academee_get_file_url('plugins/woocommerce/woocommerce.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'academee_woocommerce_merge_styles' ) ) {
	
	function academee_woocommerce_merge_styles($list) {
		$list[] = 'plugins/woocommerce/woocommerce.css';
		return $list;
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'academee_woocommerce_tgmpa_required_plugins' ) ) {
	
	function academee_woocommerce_tgmpa_required_plugins($list=array()) {
		if (in_array('woocommerce', academee_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('WooCommerce', 'academee'),
					'slug' 		=> 'woocommerce',
					'required' 	=> false
				);

		return $list;
	}
}



// Add WooCommerce specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( !function_exists( 'academee_woocommerce_list_sidebars' ) ) {
	
	function academee_woocommerce_list_sidebars($list=array()) {
		$list['woocommerce_widgets'] = array(
											'name' => esc_html__('WooCommerce Widgets', 'academee'),
											'description' => esc_html__('Widgets to be shown on the WooCommerce pages', 'academee')
											);
		return $list;
	}
}




// Decorate WooCommerce output: Loop
//------------------------------------------------------------------------

// Add query vars to set products per page
if (!function_exists('academee_woocommerce_pre_get_posts')) {
	
	function academee_woocommerce_pre_get_posts($query) {
		if (!$query->is_main_query()) return;
		if ($query->get('post_type') == 'product') {
			$ppp = get_theme_mod('posts_per_page_shop', 0);
			if ($ppp > 0)
				$query->set('posts_per_page', $ppp);
		}
	}
}


// Before main content
if ( !function_exists( 'academee_woocommerce_wrapper_start' ) ) {
	
	
	function academee_woocommerce_wrapper_start() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			<article class="post_item_single post_type_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo !academee_storage_empty('shop_mode') ? academee_storage_get('shop_mode') : 'thumbs'; ?>">
				<div class="list_products_header">
			<?php
		}
	}
}

// After main content
if ( !function_exists( 'academee_woocommerce_wrapper_end' ) ) {
	
	
	function academee_woocommerce_wrapper_end() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			</article><!-- /.post_item_single -->
			<?php
		} else {
			?>
			</div><!-- /.list_products -->
			<?php
		}
	}
}

// Close header section
if ( !function_exists( 'academee_woocommerce_archive_description' ) ) {
	
	function academee_woocommerce_archive_description() {
		?>
		</div><!-- /.list_products_header -->
		<?php
	}
}

// Add list mode buttons
if ( !function_exists( 'academee_woocommerce_before_shop_loop' ) ) {
	
	function academee_woocommerce_before_shop_loop() {
		?>
		<div class="academee_shop_mode_buttons"><form action="<?php echo esc_url(academee_get_current_url()); ?>" method="post"><input type="hidden" name="academee_shop_mode" value="<?php echo esc_attr(academee_storage_get('shop_mode')); ?>" /><a href="#" class="woocommerce_thumbs icon-th" title="<?php esc_attr_e('Show products as thumbs', 'academee'); ?>"></a><a href="#" class="woocommerce_list icon-th-list" title="<?php esc_attr_e('Show products as list', 'academee'); ?>"></a></form></div><!-- /.academee_shop_mode_buttons -->
		<?php
	}
}

// Number of columns for the shop streampage
if ( !function_exists( 'academee_woocommerce_loop_shop_columns' ) ) {
	
	function academee_woocommerce_loop_shop_columns($cols) {
		return max(2, min(4, academee_get_theme_option('blog_columns')));
	}
}

// Add column class into product item in shop streampage
if ( !function_exists( 'academee_woocommerce_loop_shop_columns_class' ) ) {
	
	
	function academee_woocommerce_loop_shop_columns_class($classes, $class='', $cat='') {
		global $woocommerce_loop;
		if (is_product()) {
			if (!empty($woocommerce_loop['columns'])) {
				$classes[] = ' column-1_'.esc_attr($woocommerce_loop['columns']);
			}
		} else if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
			$classes[] = ' column-1_'.esc_attr(max(2, min(4, academee_get_theme_option('blog_columns'))));
		}
		return $classes;
	}
}


// Open item wrapper for categories and products
if ( !function_exists( 'academee_woocommerce_item_wrapper_start' ) ) {
	
	
	function academee_woocommerce_item_wrapper_start($cat='') {
		academee_storage_set('in_product_item', true);
		$hover = academee_get_theme_option('shop_hover');
		?>
		<div class="post_item post_layout_<?php echo esc_attr(academee_storage_get('shop_mode')); ?>">
			<div class="post_featured hover_<?php echo esc_attr($hover); ?>">
				<?php do_action('academee_action_woocommerce_item_featured_start'); ?>
				<a href="<?php echo esc_url(is_object($cat) ? get_term_link($cat->slug, 'product_cat') : get_permalink()); ?>">
				<?php
	}
}

// Open item wrapper for categories and products
if ( !function_exists( 'academee_woocommerce_open_item_wrapper' ) ) {
	
	
	function academee_woocommerce_title_wrapper_start($cat='') {
				?></a><?php
				if (($hover = academee_get_theme_option('shop_hover')) != 'none') {
					?><div class="mask"></div><?php
					academee_hovers_add_icons($hover, array('cat'=>$cat));
				}
				do_action('academee_action_woocommerce_item_featured_end');
				?>
			</div><!-- /.post_featured -->
			<div class="post_data">
				<div class="post_data_inner">
					<div class="post_header entry-header">
					<?php
	}
}


// Display product's tags before the title
if ( !function_exists( 'academee_woocommerce_title_tags' ) ) {
	
	function academee_woocommerce_title_tags() {
		global $product;
		academee_show_layout(wc_get_product_tag_list( $product->get_id(), ', ', '<div class="post_tags product_tags">', '</div>' ));
	}
}

// Wrap product title into link
if ( !function_exists( 'academee_woocommerce_the_title' ) ) {
	
	function academee_woocommerce_the_title($title) {
		if (academee_storage_get('in_product_item') && get_post_type()=='product') {
			$title = '<a href="'.esc_url(get_permalink()).'">'.esc_html($title).'</a>';
		}
		return $title;
	}
}

// Wrap category title into link
if ( !function_exists( 'academee_woocommerce_shop_loop_subcategory_title' ) ) {
	
	function academee_woocommerce_shop_loop_subcategory_title($cat) {
		if (academee_storage_get('in_product_item') && is_object($cat)) {
			$cat->name = sprintf('<a href="%s">%s</a>', esc_url(get_term_link($cat->slug, 'product_cat')), $cat->name);
		}
		return $cat;
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'academee_woocommerce_title_wrapper_end' ) ) {
	
	function academee_woocommerce_title_wrapper_end() {
			?>
			</div><!-- /.post_header -->
		<?php
		if (academee_storage_get('shop_mode') == 'list' && (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) && !is_product()) {
		    $excerpt = apply_filters('the_excerpt', get_the_excerpt());
			?>
			<div class="post_content entry-content"><?php academee_show_layout($excerpt); ?></div>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'academee_woocommerce_title_wrapper_end2' ) ) {
	
	function academee_woocommerce_title_wrapper_end2($category) {
			?>
			</div><!-- /.post_header -->
		<?php
		if (academee_storage_get('shop_mode') == 'list' && is_shop() && !is_product()) {
			?>
			<div class="post_content entry-content"><?php academee_show_layout($category->description); ?></div><!-- /.post_content -->
			<?php
		}
	}
}

// Close item wrapper for categories and products
if ( !function_exists( 'academee_woocommerce_close_item_wrapper' ) ) {
	
	
	function academee_woocommerce_item_wrapper_end($cat='') {
				?>
				</div><!-- /.post_data_inner -->
			</div><!-- /.post_data -->
		</div><!-- /.post_item -->
		<?php
		academee_storage_set('in_product_item', false);
	}
}

// Change text on 'Add to cart' button
if ( ! function_exists( 'academee_woocommerce_add_to_cart_text' ) ) {
    function academee_woocommerce_add_to_cart_text( $text = '' ) {
        global $product;
        return is_object( $product ) && $product->is_in_stock()
        && 'grouped' !== $product->get_type()
        && ( 'external' !== $product->get_type() || $product->get_button_text() == '' )
            ? esc_html__( 'Buy now', 'academee' )
            : $text;
    }
}


// Decorate price
if ( !function_exists( 'academee_woocommerce_get_price_html' ) ) {
	
	function academee_woocommerce_get_price_html($price='') {
		if (!is_admin() && !empty($price)) {
			$sep = get_option('woocommerce_price_decimal_sep');
			if (empty($sep)) $sep = '.';
			$price = preg_replace('/([0-9,]+)(\\'.trim($sep).')([0-9]{2})/', '\\1<span class="decimals">\\3</span>', $price);
		}
		return $price;
	}
}



// Decorate WooCommerce output: Single product
//------------------------------------------------------------------------

// Add Product ID for the single product
if ( !function_exists( 'academee_woocommerce_show_product_id' ) ) {
	
	function academee_woocommerce_show_product_id() {
		$authors = wp_get_post_terms(get_the_ID(), 'pa_product_author');
		if (is_array($authors) && count($authors)>0) {
			echo '<span class="product_author">'.esc_html__('Author: ', 'academee');
			$delim = '';
			foreach ($authors as $author) {
				echo  esc_html($delim) . '<span>' . esc_html($author->name) . '</span>';
				$delim = ', ';
			}
			echo '</span>';
		}
		echo '<span class="product_id">'.esc_html__('Product ID: ', 'academee') . '<span>' . get_the_ID() . '</span></span>';
	}
}

// Number columns for the product's thumbnails
if ( !function_exists( 'academee_woocommerce_product_thumbnails_columns' ) ) {
	
	function academee_woocommerce_product_thumbnails_columns($cols) {
		return 4;
	}
}

// Set products number for the related products
if ( !function_exists( 'academee_woocommerce_output_related_products_args' ) ) {
	
	function academee_woocommerce_output_related_products_args($args) {
		$args['posts_per_page'] = max(0, min(9, academee_get_theme_option('related_posts')));
		$args['columns'] = max(1, min(4, academee_get_theme_option('related_columns')));
		return $args;
	}
}

// Set columns number for the related products
if ( !function_exists( 'academee_woocommerce_related_products_columns' ) ) {
	
	function academee_woocommerce_related_products_columns($columns) {
		$columns = max(1, min(4, academee_get_theme_option('related_columns')));
		return $columns;
	}
}

if ( ! function_exists( 'academee_woocommerce_price_filter_widget_step' ) ) {
    add_filter('woocommerce_price_filter_widget_step', 'academee_woocommerce_price_filter_widget_step');
    function academee_woocommerce_price_filter_widget_step( $step = '' ) {
        $step = 1;
        return $step;
    }
}


// Decorate WooCommerce output: Widgets
//------------------------------------------------------------------------

// Search form
if ( !function_exists( 'academee_woocommerce_get_product_search_form' ) ) {
	
	function academee_woocommerce_get_product_search_form($form) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__('Search for products &hellip;', 'academee') . '" value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__('Search', 'academee') . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}

// Return current page title
if ( !function_exists( 'academee_woocommerce_get_blog_title' ) ) {
	
	function academee_woocommerce_get_blog_title($title='') {
		if (is_woocommerce() && is_shop()) {
			$id = academee_woocommerce_get_shop_page_id();
			$title = $id ? get_the_title($id) : esc_html__('Shop', 'academee');
		}
		return $title;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (academee_exists_woocommerce()) { require_once ACADEMEE_THEME_DIR . 'plugins/woocommerce/woocommerce.styles.php'; }
?>