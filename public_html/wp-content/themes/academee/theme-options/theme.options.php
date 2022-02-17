<?php
/**
 * Default Theme Options and Internal Theme Settings
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


if ( !function_exists('academee_options_theme_setup1') ) {
	add_action( 'after_setup_theme', 'academee_options_theme_setup1', 1 );
	function academee_options_theme_setup1() {
		
		// -----------------------------------------------------------------
		// -- ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
		// -- Internal theme settings
		// -----------------------------------------------------------------
		academee_storage_set('settings', array(
			
			'disable_jquery_ui'		=> false,		// Prevent loading custom jQuery UI libraries in the third-party plugins
		
			'max_load_fonts'		=> 3,			// Max fonts number to load from Google fonts or from uploaded fonts
		
			'use_mediaelements'		=> true,		// Load script "Media Elements" to play video and audio
		
			'max_excerpt_length'	=> 60,			// Max words number for the excerpt in the blog style 'Excerpt'.
													// For style 'Classic' - get half from this value

			'comment_maxlength'		=> 1000,		// Max length of the message from contact form

			'comment_after_name'	=> true,		// Place 'comment' field before the 'name' and 'email'
			
			'socials_type'			=> 'icons',		// Type of socials:
													// icons - use fontello icons to present social networks
													// images - use images from theme's folder trx_addons/css/icons.png
			
			'icons_type'			=> 'icons',		// Type of other icons:
													// icons - use fontello icons to present icons
													// images - use images from theme's folder trx_addons/css/icons.png
			
			'icons_selector'		=> 'internal'	// Icons selector in the shortcodes:
													// default - standard VC icons selector (very slow and don't support images)
													// internal - internal popup with plugin's or theme's icons list (fast)
		));
	}
}


// -----------------------------------------------------------------
// -- Theme options for customizer
// -----------------------------------------------------------------
if (!function_exists('academee_options_create')) {

	function academee_options_create() {

		// Message about options override. 
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = __('<b>Attention!</b> Some of these options can be overridden in the following sections (Homepage, Blog archive, Shop, Events, etc.) or in the settings of individual pages', 'academee');

		academee_storage_set('options', array(
		
			// Section 'Title & Tagline' - add theme options in the standard WP section
			'title_tagline' => array(
				"title" => esc_html__('Title, Tagline & Site icon', 'academee'),
				"desc" => wp_kses_data( __('Specify site title and tagline (if need) and upload the site icon', 'academee') ),
				"type" => "section"
				),
		
		
			// Section 'Header' - add theme options in the standard WP section
			'header_image' => array(
				"title" => esc_html__('Header', 'academee'),
				"desc" => wp_kses_data( __('Select or upload logo images, select header type and widgets set for the header', 'academee') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"type" => "section"
				),
			'header_image_override' => array(
				"title" => esc_html__('Header image override', 'academee'),
				"desc" => wp_kses_data( __("Allow override the header image with the page's/post's/product's/etc. featured image", 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'header_style' => array(
				"title" => esc_html__('Header style', 'academee'),
				"desc" => wp_kses_data( __('Select style to display the site header', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 'header-default',
				"options" => array(),
				"type" => "select"
				),
			'header_position' => array(
				"title" => esc_html__('Header position', 'academee'),
				"desc" => wp_kses_data( __('Select position to display the site header', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 'default',
				"options" => array(),
				"type" => "select"
				),
			'header_widgets' => array(
				"title" => esc_html__('Header widgets', 'academee'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on each page', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the header on this page', 'academee') ),
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'header_columns' => array(
				"title" => esc_html__('Header columns', 'academee'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"dependency" => array(
					'header_style' => array('header-default'),
					'header_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => academee_get_list_range(0,6),
				"type" => "select"
				),
			'header_scheme' => array(
				"title" => esc_html__('Header Color Scheme', 'academee'),
				"desc" => wp_kses_data( __('Select color scheme to decorate header area', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 'inherit',
				"options" => array(),
				"refresh" => false,
				"type" => "select"
				),
			'header_fullheight' => array(
				"title" => esc_html__('Header fullheight', 'academee'),
				"desc" => wp_kses_data( __("Enlarge header area to fill whole screen. Used only if header have a background image", 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'header_wide' => array(
				"title" => esc_html__('Header fullwide', 'academee'),
				"desc" => wp_kses_data( __('Do you want to stretch the header widgets area to the entire window width?', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"dependency" => array(
					'header_style' => array('header-default')
				),
				"std" => 1,
				"type" => "checkbox"
				),

			'menu_info' => array(
				"title" => esc_html__('Menu settings', 'academee'),
				"desc" => wp_kses_data( __('Select main menu style, position, color scheme and other parameters', 'academee') ),
				"type" => "info"
				),
			'menu_style' => array(
				"title" => esc_html__('Menu position', 'academee'),
				"desc" => wp_kses_data( __('Select position of the main menu', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 'top',
				"options" => array(
					'top'	=> esc_html__('Top',	'academee'),
					'left'	=> esc_html__('Left',	'academee'),
					'right'	=> esc_html__('Right',	'academee')
				),
				"type" => "switch"
				),
			'menu_scheme' => array(
				"title" => esc_html__('Menu Color Scheme', 'academee'),
				"desc" => wp_kses_data( __('Select color scheme to decorate main menu area', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => 'inherit',
				"options" => array(),
				"refresh" => false,
				"type" => "select"
				),
			'menu_side_stretch' => array(
				"title" => esc_html__('Stretch sidemenu', 'academee'),
				"desc" => wp_kses_data( __('Stretch sidemenu to window height (if menu items number >= 5)', 'academee') ),
				"dependency" => array(
					'menu_style' => array('left', 'right')
				),
				"std" => 1,
				"type" => "checkbox"
				),
			'menu_side_icons' => array(
				"title" => esc_html__('Iconed sidemenu', 'academee'),
				"desc" => wp_kses_data( __('Get icons from anchors and display it in the sidemenu or mark sidemenu items with simple dots', 'academee') ),
				"dependency" => array(
					'menu_style' => array('left', 'right')
				),
				"std" => 1,
				"type" => "checkbox"
				),
			'menu_mobile_fullscreen' => array(
				"title" => esc_html__('Mobile menu fullscreen', 'academee'),
				"desc" => wp_kses_data( __('Display mobile and side menus on full screen (if checked) or slide narrow menu from the left or from the right side (if not checked)', 'academee') ),
				"dependency" => array(
					'menu_style' => array('left', 'right')
				),
				"std" => 1,
				"type" => "checkbox"
				),
			'logo_info' => array(
				"title" => esc_html__('Logo settings', 'academee'),
				"desc" => wp_kses_data( __('Select logo images for the normal and Retina displays', 'academee') ),
				"type" => "info"
				),
			'logo' => array(
				"title" => esc_html__('Logo', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_retina' => array(
				"title" => esc_html__('Logo for Retina', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_inverse' => array(
				"title" => esc_html__('Logo inverse', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo to display it on the dark background', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_inverse_retina' => array(
				"title" => esc_html__('Logo inverse for Retina', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_side' => array(
				"title" => esc_html__('Logo side', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo (with vertical orientation) to display it in the side menu', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_side_retina' => array(
				"title" => esc_html__('Logo side for Retina', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'logo_text' => array(
				"title" => esc_html__('Logo from Site name', 'academee'),
				"desc" => wp_kses_data( __('Do you want use Site name and description as Logo if images above are not selected?', 'academee') ),
				"std" => 1,
				"type" => "checkbox"
				),
			'header_top_text_phone' => array(
				"title" => esc_html__('Phone on header', 'academee'),
				"desc" => wp_kses_data( __('Put here text to insert into the Header(Attention only for style "Default Header")', 'academee') ),
				"std" => '0 (800) 123 45 67',
				"type" => "text"
			),
			'header_top_text_mail' => array(
				"title" => esc_html__('Mail on header', 'academee'),
				"desc" => wp_kses_data( __('Put here text to insert into the Header(Attention only for style "Default Header") ', 'academee') ),
				"std" => 'info@academee.com',
				"type" => "text"
			),
			
		
		
			// Section 'Content'
			'content' => array(
				"title" => esc_html__('Content', 'academee'),
				"desc" => wp_kses_data( __('Options of the content area.', 'academee') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"type" => "section",
				),
			'color_scheme' => array(
				"title" => esc_html__('Site Color Scheme', 'academee'),
				"desc" => wp_kses_data( __('Select color scheme to decorate whole site. Attention! Case "Inherit" can be used only for custom pages, not for root site content in the Appearance - Customize', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"std" => 'default',
				"options" => array(),
				"refresh" => false,
				"type" => "select"
				),
			'body_style' => array(
				"title" => esc_html__('Body style', 'academee'),
				"desc" => wp_kses_data( __('Select width of the body content', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"refresh" => false,
				"std" => 'wide',
				"options" => array(
					'boxed'		=> esc_html__('Boxed',		'academee'),
					'wide'		=> esc_html__('Wide',		'academee'),
					'fullwide'	=> esc_html__('Fullwide',	'academee'),
					'fullscreen'=> esc_html__('Fullscreen',	'academee')
				),
				"type" => "select"
				),
			'boxed_bg_image' => array(
				"title" => esc_html__('Boxed bg image', 'academee'),
				"desc" => wp_kses_data( __('Select or upload image, used as background in the boxed body', 'academee') ),
				"dependency" => array(
					'body_style' => array('boxed')
				),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"std" => '',
				"type" => "image"
				),
			'expand_content' => array(
				"title" => esc_html__('Expand content', 'academee'),
				"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Content', 'academee')
				),
				"refresh" => false,
				"std" => 1,
				"type" => "checkbox"
				),
			'remove_margins' => array(
				"title" => esc_html__('Remove margins', 'academee'),
				"desc" => wp_kses_data( __('Remove margins above and below the content area', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Content', 'academee')
				),
				"refresh" => false,
				"std" => 0,
				"type" => "checkbox"
				),
			'border_radius' => array(
				"title" => esc_html__('Border radius', 'academee'),
				"desc" => wp_kses_data( __('Specify the border radius of the form fields and buttons in pixels or other valid CSS units', 'academee') ),
				"std" => 0,
				"type" => "text"
				),
			'no_image' => array(
				"title" => esc_html__('No image placeholder', 'academee'),
				"desc" => wp_kses_data( __('Select or upload image, used as placeholder for the posts without featured image', 'academee') ),
				"std" => '',
				"type" => "image"
				),
			'seo_snippets' => array(
				"title" => esc_html__('SEO snippets', 'academee'),
				"desc" => wp_kses_data( __('Add structured data markup to the single posts and pages', 'academee') ),
				"std" => 0,
				"type" => "checkbox"
				),
            'privacy_text' => array(
                "title" => esc_html__("Text with Privacy Policy link", 'academee'),
                "desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'academee') ),
                "std"   => wp_kses_data( __( 'I agree that my submitted data is being collected and stored.', 'academee') ),
                "type"  => "text"
            ),
			'author_info' => array(
				"title" => esc_html__('Author info', 'academee'),
				"desc" => wp_kses_data( __("Display block with information about post's author", 'academee') ),
				"std" => 1,
				"type" => "checkbox"
				),
			'related_posts' => array(
				"title" => esc_html__('Related posts', 'academee'),
				"desc" => wp_kses_data( __('How many related posts should be displayed in the single post? If 0 - no related posts showed.', 'academee') ),
				"std" => 2,
				"options" => academee_get_list_range(0,9),
				"type" => "select"
				),
			'related_columns' => array(
				"title" => esc_html__('Related columns', 'academee'),
				"desc" => wp_kses_data( __('How many columns should be used to output related posts in the single page (from 2 to 4)?', 'academee') ),
				"std" => 2,
				"options" => academee_get_list_range(1,4),
				"type" => "select"
				),
			'related_style' => array(
				"title" => esc_html__('Related posts style', 'academee'),
				"desc" => wp_kses_data( __('Select style of the related posts output', 'academee') ),
				"std" => 2,
				"options" => academee_get_list_styles(1,2),
				"type" => "select"
				),
			
		
		
			// Section 'Content'
			'sidebar' => array(
				"title" => esc_html__('Sidebar', 'academee'),
				"desc" => wp_kses_data( __('Options of the sidebar area.', 'academee') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"type" => "section",
				),
			'sidebar_widgets' => array(
				"title" => esc_html__('Sidebar widgets', 'academee'),
				"desc" => wp_kses_data( __('Select default widgets to show in the sidebar', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'sidebar_widgets',
				"options" => array(),
				"type" => "select"
				),
			'sidebar_scheme' => array(
				"title" => esc_html__('Sidebar Color Scheme', 'academee'),
				"desc" => wp_kses_data( __('Select color scheme to decorate sidebar', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'dark',
				"options" => array(),
				"refresh" => false,
				"type" => "select"
				),
			'sidebar_position' => array(
				"title" => esc_html__('Sidebar position', 'academee'),
				"desc" => wp_kses_data( __('Select position to show sidebar', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'academee')
				),
				"refresh" => false,
				"std" => 'right',
				"options" => array(),
				"type" => "select"
				),
			'hide_sidebar_on_single' => array(
				"title" => esc_html__('Hide sidebar on the single post', 'academee'),
				"desc" => wp_kses_data( __("Hide sidebar on the single post's pages", 'academee') ),
				"std" => 0,
				"type" => "checkbox"
				),
			'widgets_above_page' => array(
				"title" => esc_html__('Widgets at the top of the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the top of the page (above content and sidebar)', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_above_content' => array(
				"title" => esc_html__('Widgets above the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_content' => array(
				"title" => esc_html__('Widgets below the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_page' => array(
				"title" => esc_html__('Widgets at the bottom of the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the bottom of the page (below content and sidebar)', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'academee')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
		
		
		
			// Section 'Footer'
			'footer' => array(
				"title" => esc_html__('Footer', 'academee'),
				"desc" => wp_kses_data( __('Select set of widgets and columns number in the site footer', 'academee') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"type" => "section"
				),
			'footer_style' => array(
				"title" => esc_html__('Footer style', 'academee'),
				"desc" => wp_kses_data( __('Select style to display the site footer', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Footer', 'academee')
				),
				"std" => 'footer-default',
				"options" => array(),
				"type" => "select"
				),
			'footer_scheme' => array(
				"title" => esc_html__('Footer Color Scheme', 'academee'),
				"desc" => wp_kses_data( __('Select color scheme to decorate footer area', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'academee')
				),
				"std" => 'dark',
				"options" => array(),
				"refresh" => false,
				"type" => "select"
				),
			'footer_widgets' => array(
				"title" => esc_html__('Footer widgets', 'academee'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the footer', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'academee')
				),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 'footer_widgets',
				"options" => array(),
				"type" => "select"
				),
			'footer_columns' => array(
				"title" => esc_html__('Footer columns', 'academee'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'academee')
				),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'footer_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => academee_get_list_range(0,6),
				"type" => "select"
				),
			'footer_wide' => array(
				"title" => esc_html__('Footer fullwide', 'academee'),
				"desc" => wp_kses_data( __('Do you want to stretch the footer to the entire window width?', 'academee') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'academee')
				),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'logo_in_footer' => array(
				"title" => esc_html__('Show logo', 'academee'),
				"desc" => wp_kses_data( __('Show logo in the footer', 'academee') ),
				'refresh' => false,
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'logo_footer' => array(
				"title" => esc_html__('Logo for footer', 'academee'),
				"desc" => wp_kses_data( __('Select or upload site logo to display it in the footer', 'academee') ),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'logo_in_footer' => array('1')
				),
				"std" => '',
				"type" => "image"
				),
			'logo_footer_retina' => array(
				"title" => esc_html__('Logo for footer (Retina)', 'academee'),
				"desc" => wp_kses_data( __('Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'academee') ),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'logo_in_footer' => array('1')
				),
				"std" => '',
				"type" => "image"
				),
			'socials_in_footer' => array(
				"title" => esc_html__('Show social icons', 'academee'),
				"desc" => wp_kses_data( __('Show social icons in the footer (under logo or footer widgets)', 'academee') ),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'copyright' => array(
				"title" => esc_html__('Copyright', 'academee'),
				"desc" => wp_kses_data( __('Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'academee') ),
				"std" => esc_html__('ThemeREX &copy; {Y}. All rights reserved. Terms of use and Privacy Policy', 'academee'),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"refresh" => false,
				"type" => "textarea"
				),
		
		
		
			// Section 'Homepage' - settings for home page
			'homepage' => array(
				"title" => esc_html__('Homepage', 'academee'),
				"desc" => wp_kses_data( __('Select blog style and widgets to display on the homepage', 'academee') ),
				"type" => "section"
				),
			'expand_content_home' => array(
				"title" => esc_html__('Expand content', 'academee'),
				"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden on the Homepage', 'academee') ),
				"refresh" => false,
				"std" => 1,
				"type" => "checkbox"
				),
			'blog_style_home' => array(
				"title" => esc_html__('Blog style', 'academee'),
				"desc" => wp_kses_data( __('Select posts style for the homepage', 'academee') ),
				"std" => 'excerpt',
				"options" => array(),
				"type" => "select"
				),
			'first_post_large_home' => array(
				"title" => esc_html__('First post large', 'academee'),
				"desc" => wp_kses_data( __('Make first post large (with Excerpt layout) on the Classic layout of the Homepage', 'academee') ),
				"dependency" => array(
					'blog_style_home' => array('classic')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'header_style_home' => array(
				"title" => esc_html__('Header style', 'academee'),
				"desc" => wp_kses_data( __('Select style to display the site header on the homepage', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'header_position_home' => array(
				"title" => esc_html__('Header position', 'academee'),
				"desc" => wp_kses_data( __('Select position to display the site header on the homepage', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'header_widgets_home' => array(
				"title" => esc_html__('Header widgets', 'academee'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on the homepage', 'academee') ),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'sidebar_widgets_home' => array(
				"title" => esc_html__('Sidebar widgets', 'academee'),
				"desc" => wp_kses_data( __('Select sidebar to show on the homepage', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'sidebar_position_home' => array(
				"title" => esc_html__('Sidebar position', 'academee'),
				"desc" => wp_kses_data( __('Select position to show sidebar on the homepage', 'academee') ),
				"refresh" => false,
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'widgets_above_page_home' => array(
				"title" => esc_html__('Widgets above the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'academee') ),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_above_content_home' => array(
				"title" => esc_html__('Widgets above the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'academee') ),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_content_home' => array(
				"title" => esc_html__('Widgets below the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'academee') ),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_page_home' => array(
				"title" => esc_html__('Widgets below the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'academee') ),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			
		
		
			// Section 'Blog archive'
			'blog' => array(
				"title" => esc_html__('Blog archive', 'academee'),
				"desc" => wp_kses_data( __('Options for the blog archive', 'academee') ),
				"type" => "section",
				),
			'expand_content_blog' => array(
				"title" => esc_html__('Expand content', 'academee'),
				"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden on the blog archive', 'academee') ),
				"refresh" => false,
				"std" => 1,
				"type" => "checkbox"
				),
			'blog_style' => array(
				"title" => esc_html__('Blog style', 'academee'),
				"desc" => wp_kses_data( __('Select posts style for the blog archive', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' )
				),
				"std" => 'excerpt',
				"options" => array(),
				"type" => "select"
				),
			'blog_columns' => array(
				"title" => esc_html__('Blog columns', 'academee'),
				"desc" => wp_kses_data( __('How many columns should be used in the blog archive (from 2 to 4)?', 'academee') ),
				"std" => 2,
				"options" => academee_get_list_range(2,4),
				"type" => "hidden"
				),
			'post_type' => array(
				"title" => esc_html__('Post type', 'academee'),
				"desc" => wp_kses_data( __('Select post type to show in the blog archive', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' )
				),
				"linked" => 'parent_cat',
				"refresh" => false,
				"hidden" => true,
				"std" => 'post',
				"options" => array(),
				"type" => "select"
				),
			'parent_cat' => array(
				"title" => esc_html__('Category to show', 'academee'),
				"desc" => wp_kses_data( __('Select category to show in the blog archive', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' )
				),
				"refresh" => false,
				"hidden" => true,
				"std" => '0',
				"options" => array(),
				"type" => "select"
				),
			'posts_per_page' => array(
				"title" => esc_html__('Posts per page', 'academee'),
				"desc" => wp_kses_data( __('How many posts will be displayed on this page', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' )
				),
				"hidden" => true,
				"std" => '',
				"type" => "text"
				),
			"blog_pagination" => array( 
				"title" => esc_html__('Pagination style', 'academee'),
				"desc" => wp_kses_data( __('Show Older/Newest posts or Page numbers below the posts list', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"std" => "pages",
				"options" => array(
					'pages'	=> esc_html__("Page numbers", 'academee'),
					'links'	=> esc_html__("Older/Newest", 'academee'),
					'more'	=> esc_html__("Load more", 'academee'),
					'infinite' => esc_html__("Infinite scroll", 'academee')
				),
				"type" => "select"
				),
			'show_filters' => array(
				"title" => esc_html__('Show filters', 'academee'),
				"desc" => wp_kses_data( __('Show categories as tabs to filter posts', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' ),
					'blog_style' => array('portfolio', 'gallery')
				),
				"hidden" => true,
				"std" => 0,
				"type" => "checkbox"
				),
			'first_post_large' => array(
				"title" => esc_html__('First post large', 'academee'),
				"desc" => wp_kses_data( __('Make first post large (with Excerpt layout) on the Classic layout of blog archive', 'academee') ),
				"dependency" => array(
					'blog_style' => array('classic')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			"blog_content" => array( 
				"title" => esc_html__('Posts content', 'academee'),
				"desc" => wp_kses_data( __("Show full post's content in the blog or only post's excerpt", 'academee') ),
				"std" => "excerpt",
				"options" => array(
					'excerpt'	=> esc_html__('Excerpt',	'academee'),
					'fullpost'	=> esc_html__('Full post',	'academee')
				),
				"type" => "select"
				),
			'time_diff_before' => array(
				"title" => esc_html__('Time difference', 'academee'),
				"desc" => wp_kses_data( __("How many days show time difference instead post's date", 'academee') ),
				"std" => 5,
				"type" => "text"
				),
			'sticky_style' => array(
				"title" => esc_html__('Sticky posts style', 'academee'),
				"desc" => wp_kses_data( __('Select style of the sticky posts output', 'academee') ),
				"std" => 'inherit',
				"options" => array(
					'inherit' => esc_html__('Decorated posts', 'academee'),
					'columns' => esc_html__('Mini-cards',	'academee')
				),
				"type" => "select"
				),
			"blog_animation" => array( 
				"title" => esc_html__('Animation for the posts', 'academee'),
				"desc" => wp_kses_data( __('Select animation to show posts in the blog. Attention! Do not use any animation on pages with the "wheel to the anchor" behaviour (like a "Chess 2 columns")!', 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'academee')
				),
				"dependency" => array(
                    '#page_template' => array( 'blog.php' ),
                    '.editor-page-attributes__template select' => array( 'blog.php' )
				),
				"std" => "none",
				"options" => array(),
				"type" => "select"
				),
			'header_style_blog' => array(
				"title" => esc_html__('Header style', 'academee'),
				"desc" => wp_kses_data( __('Select style to display the site header on the blog archive', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'header_position_blog' => array(
				"title" => esc_html__('Header position', 'academee'),
				"desc" => wp_kses_data( __('Select position to display the site header on the blog archive', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'header_widgets_blog' => array(
				"title" => esc_html__('Header widgets', 'academee'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on the blog archive', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'sidebar_widgets_blog' => array(
				"title" => esc_html__('Sidebar widgets', 'academee'),
				"desc" => wp_kses_data( __('Select sidebar to show on the blog archive', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'sidebar_position_blog' => array(
				"title" => esc_html__('Sidebar position', 'academee'),
				"desc" => wp_kses_data( __('Select position to show sidebar on the blog archive', 'academee') ),
				"refresh" => false,
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'hide_sidebar_on_single_blog' => array(
				"title" => esc_html__('Hide sidebar on the single post', 'academee'),
				"desc" => wp_kses_data( __("Hide sidebar on the single post", 'academee') ),
				"std" => 0,
				"type" => "checkbox"
				),
			'widgets_above_page_blog' => array(
				"title" => esc_html__('Widgets above the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'widgets_above_content_blog' => array(
				"title" => esc_html__('Widgets above the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_content_blog' => array(
				"title" => esc_html__('Widgets below the content', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			'widgets_below_page_blog' => array(
				"title" => esc_html__('Widgets below the page', 'academee'),
				"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'academee') ),
				"std" => 'inherit',
				"options" => array(),
				"type" => "select"
				),
			
		
		
			// Section 'Colors' - choose color scheme and customize separate colors from it
			'scheme' => array(
				"title" => esc_html__('* Color scheme editor', 'academee'),
				"desc" => esc_html__("Modify colors and preview changes on your site", 'academee'),
				"priority" => 1000,
				"type" => "section"
				),
		
			'scheme_storage' => array(
				"title" => esc_html__('Color schemes', 'academee'),
				"desc" => esc_html__('Select color scheme to modify. Attention! Only those sections will be changed which this scheme was assigned to', 'academee'),
				"std" => '$academee_get_scheme_storage',
				"refresh" => false,
				"type" => "scheme_editor"
				),


			// Section 'Hidden'
			'media_title' => array(
				"title" => esc_html__('Media title', 'academee'),
				"desc" => wp_kses_data( __('Used as title for the audio and video item in this post', 'academee') ),
				"override" => array(
					'mode' => 'post',
					'section' => esc_html__('Title', 'academee')
				),
				"hidden" => true,
				"std" => '',
				"type" => "text"
				),
			'media_author' => array(
				"title" => esc_html__('Media author', 'academee'),
				"desc" => wp_kses_data( __('Used as author name for the audio and video item in this post', 'academee') ),
				"override" => array(
					'mode' => 'post',
					'section' => esc_html__('Title', 'academee')
				),
				"hidden" => true,
				"std" => '',
				"type" => "text"
				),


			// Internal options.
			// Attention! Don't change any options in the section below!
			'reset_options' => array(
				"title" => '',
				"desc" => '',
				"std" => '0',
				"type" => "hidden",
				),

		));


		// Prepare panel 'Fonts'
		$fonts = array(
		
			// Panel 'Fonts' - manage fonts loading and set parameters of the base theme elements
			'fonts' => array(
				"title" => esc_html__('* Fonts settings', 'academee'),
				"desc" => '',
				"priority" => 1500,
				"type" => "panel"
				),

			// Section 'Load_fonts'
			'load_fonts' => array(
				"title" => esc_html__('Load fonts', 'academee'),
				"desc" => wp_kses_data( __('Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'academee') )
						. '<br>'
						. wp_kses_data( __('<b>Attention!</b> Press "Refresh" button to reload preview area after the all fonts are changed', 'academee') ),
				"type" => "section"
				),
			'load_fonts_subset' => array(
				"title" => esc_html__('Google fonts subsets', 'academee'),
				"desc" => wp_kses_data( __('Specify comma separated list of the subsets which will be load from Google fonts', 'academee') )
						. '<br>'
						. wp_kses_data( __('Available subsets are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'academee') ),
				"refresh" => false,
				"std" => '$academee_get_load_fonts_subset',
				"type" => "text"
				)
		);

		for ($i=1; $i<=academee_get_theme_setting('max_load_fonts'); $i++) {
			$fonts["load_fonts-{$i}-info"] = array(
				"title" => esc_html(sprintf(esc_html__('Font %s', 'academee'), $i)),
				"desc" => '',
				"type" => "info",
				);
			$fonts["load_fonts-{$i}-name"] = array(
				"title" => esc_html__('Font name', 'academee'),
				"desc" => '',
				"refresh" => false,
				"std" => '$academee_get_load_fonts_option',
				"type" => "text"
				);
			$fonts["load_fonts-{$i}-family"] = array(
				"title" => esc_html__('Font family', 'academee'),
				"desc" => $i==1 
							? wp_kses_data( __('Select font family to use it if font above is not available', 'academee') )
							: '',
				"refresh" => false,
				"std" => '$academee_get_load_fonts_option',
				"options" => array(
					'inherit' => esc_html__("Inherit", 'academee'),
					'serif' => esc_html__('serif', 'academee'),
					'sans-serif' => esc_html__('sans-serif', 'academee'),
					'monospace' => esc_html__('monospace', 'academee'),
					'cursive' => esc_html__('cursive', 'academee'),
					'fantasy' => esc_html__('fantasy', 'academee')
				),
				"type" => "select"
				);
			$fonts["load_fonts-{$i}-styles"] = array(
				"title" => esc_html__('Font styles', 'academee'),
				"desc" => $i==1 
							? wp_kses_data( __('Font styles used only for the Google fonts. This is a comma separated list of the font weight and styles. For example: 400,400italic,700', 'academee') )
											. '<br>'
								. wp_kses_data( __('<b>Attention!</b> Each weight and style increase download size! Specify only used weights and styles.', 'academee') )
							: '',
				"refresh" => false,
				"std" => '$academee_get_load_fonts_option',
				"type" => "text"
				);
		}
		$fonts['load_fonts_end'] = array(
			"type" => "section_end"
			);

		// Sections with font's attributes for each theme element
		$theme_fonts = academee_get_theme_fonts();
		foreach ($theme_fonts as $tag=>$v) {
			$fonts["{$tag}_section"] = array(
				"title" => !empty($v['title']) 
								? $v['title'] 
								: esc_html(sprintf(esc_html__('%s settings', 'academee'), $tag)),
				"desc" => !empty($v['description']) 
								? $v['description'] 
								: wp_kses( sprintf(__('Font settings of the "%s" tag.', 'academee'), $tag), 'academee_kses_content' ),
				"type" => "section",
				);
	
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				$options = '';
				$type = 'text';
				$title = ucfirst(str_replace('-', ' ', $css_prop));
				if ($css_prop == 'font-family') {
					$type = 'select';
					$options = array();
				} else if ($css_prop == 'font-weight') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'academee'),
						'100' => esc_html__('100 (Light)', 'academee'), 
						'200' => esc_html__('200 (Light)', 'academee'), 
						'300' => esc_html__('300 (Thin)',  'academee'),
						'400' => esc_html__('400 (Normal)', 'academee'),
						'500' => esc_html__('500 (Semibold)', 'academee'),
						'600' => esc_html__('600 (Semibold)', 'academee'),
						'700' => esc_html__('700 (Bold)', 'academee'),
						'800' => esc_html__('800 (Black)', 'academee'),
						'900' => esc_html__('900 (Black)', 'academee')
					);
				} else if ($css_prop == 'font-style') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'academee'),
						'normal' => esc_html__('Normal', 'academee'), 
						'italic' => esc_html__('Italic', 'academee')
					);
				} else if ($css_prop == 'text-decoration') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'academee'),
						'none' => esc_html__('None', 'academee'), 
						'underline' => esc_html__('Underline', 'academee'),
						'overline' => esc_html__('Overline', 'academee'),
						'line-through' => esc_html__('Line-through', 'academee')
					);
				} else if ($css_prop == 'text-transform') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'academee'),
						'none' => esc_html__('None', 'academee'), 
						'uppercase' => esc_html__('Uppercase', 'academee'),
						'lowercase' => esc_html__('Lowercase', 'academee'),
						'capitalize' => esc_html__('Capitalize', 'academee')
					);
				}
				$fonts["{$tag}_{$css_prop}"] = array(
					"title" => $title,
					"desc" => '',
					"refresh" => false,
					"std" => '$academee_get_theme_fonts_option',
					"options" => $options,
					"type" => $type
				);
			}
			
			$fonts["{$tag}_section_end"] = array(
				"type" => "section_end"
				);
		}

		$fonts['fonts_end'] = array(
			"type" => "panel_end"
			);

		// Add fonts parameters into Theme Options
		academee_storage_merge_array('options', '', $fonts);

		// Add Header Video if WP version < 4.7
		if (!function_exists('get_header_video_url')) {
			academee_storage_set_array_after('options', 'header_image_override', 'header_video', array(
				"title" => esc_html__('Header video', 'academee'),
				"desc" => wp_kses_data( __("Select video to use it as background for the header", 'academee') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'academee')
				),
				"std" => '',
				"type" => "video"
				)
			);
		}
	}
}


// Return lists with choises when its need in the admin mode
if (!function_exists('academee_options_get_list_choises')) {
	add_filter('academee_filter_options_get_list_choises', 'academee_options_get_list_choises', 10, 2);
	function academee_options_get_list_choises($list, $id) {
		if (is_array($list) && count($list)==0) {
			if (strpos($id, 'header_style')===0)
				$list = academee_get_list_header_styles(strpos($id, 'header_style_')===0);
			else if (strpos($id, 'header_position')===0)
				$list = academee_get_list_header_positions(strpos($id, 'header_position_')===0);
			else if (strpos($id, 'header_widgets')===0)
				$list = academee_get_list_sidebars(strpos($id, 'header_widgets_')===0, true);
			else if (strpos($id, 'header_scheme')===0 
					|| strpos($id, 'menu_scheme')===0
					|| strpos($id, 'color_scheme')===0
					|| strpos($id, 'sidebar_scheme')===0
					|| strpos($id, 'footer_scheme')===0)
				$list = academee_get_list_schemes($id!='color_scheme');
			else if (strpos($id, 'sidebar_widgets')===0)
				$list = academee_get_list_sidebars(strpos($id, 'sidebar_widgets_')===0, true);
			else if (strpos($id, 'sidebar_position')===0)
				$list = academee_get_list_sidebars_positions(strpos($id, 'sidebar_position_')===0);
			else if (strpos($id, 'widgets_above_page')===0)
				$list = academee_get_list_sidebars(strpos($id, 'widgets_above_page_')===0, true);
			else if (strpos($id, 'widgets_above_content')===0)
				$list = academee_get_list_sidebars(strpos($id, 'widgets_above_content_')===0, true);
			else if (strpos($id, 'widgets_below_page')===0)
				$list = academee_get_list_sidebars(strpos($id, 'widgets_below_page_')===0, true);
			else if (strpos($id, 'widgets_below_content')===0)
				$list = academee_get_list_sidebars(strpos($id, 'widgets_below_content_')===0, true);
			else if (strpos($id, 'footer_style')===0)
				$list = academee_get_list_footer_styles(strpos($id, 'footer_style_')===0);
			else if (strpos($id, 'footer_widgets')===0)
				$list = academee_get_list_sidebars(strpos($id, 'footer_widgets_')===0, true);
			else if (strpos($id, 'blog_style')===0)
				$list = academee_get_list_blog_styles(strpos($id, 'blog_style_')===0);
			else if (strpos($id, 'post_type')===0)
				$list = academee_get_list_posts_types();
			else if (strpos($id, 'parent_cat')===0)
				$list = academee_array_merge(array(0 => esc_html__('- Select category -', 'academee')), academee_get_list_categories());
			else if (strpos($id, 'blog_animation')===0)
				$list = academee_get_list_animations_in();
			else if ($id == 'color_scheme_editor')
				$list = academee_get_list_schemes();
			else if (strpos($id, '_font-family') > 0)
				$list = academee_get_list_load_fonts(true);
		}
		return $list;
	}
}




// -----------------------------------------------------------------
// -- Create and manage Theme Options
// -----------------------------------------------------------------

// Theme init priorities:
// 2 - create Theme Options
if (!function_exists('academee_options_theme_setup2')) {
	add_action( 'after_setup_theme', 'academee_options_theme_setup2', 2 );
	function academee_options_theme_setup2() {
		academee_options_create();
	}
}

// Step 1: Load default settings and previously saved mods
if (!function_exists('academee_options_theme_setup5')) {
	add_action( 'after_setup_theme', 'academee_options_theme_setup5', 5 );
	function academee_options_theme_setup5() {
		academee_storage_set('options_reloaded', false);
		academee_load_theme_options();
	}
}

// Step 2: Load current theme customization mods
if (is_customize_preview()) {
	if (!function_exists('academee_load_custom_options')) {
		add_action( 'wp_loaded', 'academee_load_custom_options' );
		function academee_load_custom_options() {
			if (!academee_storage_get('options_reloaded')) {
				academee_storage_set('options_reloaded', true);
				academee_load_theme_options();
			}
		}
	}
}

// Load current values for each customizable option
if ( !function_exists('academee_load_theme_options') ) {
	function academee_load_theme_options() {
		$options = academee_storage_get('options');
		$reset = (int) get_theme_mod('reset_options', 0);
		foreach ($options as $k=>$v) {
			if (isset($v['std'])) {
				if (strpos($v['std'], '$academee_')!==false) {
					$func = substr($v['std'], 1);
					if (function_exists($func)) {
						$v['std'] = $func($k);
					}
				}
				$value = $v['std'];
				if (!$reset) {
					if (isset($_GET[$k]))
						$value = academee_get_value_gpc($k);
					else {
						$tmp = get_theme_mod($k, -987654321);
						if ($tmp != -987654321) $value = $tmp;
					}
				}
				academee_storage_set_array2('options', $k, 'val', $value);
				if ($reset) remove_theme_mod($k);
			}
		}
		if ($reset) {
			// Unset reset flag
			set_theme_mod('reset_options', 0);
			// Regenerate CSS with default colors and fonts
			academee_customizer_save_css();
		} else {
			do_action('academee_action_load_options');
		}
	}
}

// Override options with stored page/post meta
if ( !function_exists('academee_override_theme_options') ) {
	add_action( 'wp', 'academee_override_theme_options', 1 );
	function academee_override_theme_options($query=null) {
		if (is_page_template('blog.php')) {
			academee_storage_set('blog_archive', true);
			academee_storage_set('blog_template', get_the_ID());
		}
		academee_storage_set('blog_mode', academee_detect_blog_mode());
		if (is_singular()) {
			academee_storage_set('options_meta', get_post_meta(get_the_ID(), 'academee_options', true));
		}
	}
}


// Return customizable option value
if (!function_exists('academee_get_theme_option')) {
	function academee_get_theme_option($name, $defa='', $strict_mode=false, $post_id=0) {
		$rez = $defa;
		$from_post_meta = false;
		if ($post_id > 0) {
			if (!academee_storage_isset('post_options_meta', $post_id))
				academee_storage_set_array('post_options_meta', $post_id, get_post_meta($post_id, 'academee_options', true));
			if (academee_storage_isset('post_options_meta', $post_id, $name)) {
				$tmp = academee_storage_get_array('post_options_meta', $post_id, $name);
				if (!academee_is_inherit($tmp)) {
					$rez = $tmp;
					$from_post_meta = true;
				}
			}
		}
		if (!$from_post_meta && academee_storage_isset('options')) {
			$blog_mode = academee_storage_get('blog_mode');
			if ( !academee_storage_isset('options', $name) && (empty($blog_mode) || !academee_storage_isset('options', $name.'_'.$blog_mode)) ) {
				$rez = $tmp = '_not_exists_';
				if (function_exists('trx_addons_get_option'))
					$rez = trx_addons_get_option($name, $tmp, false);
				if ($rez === $tmp) {
					if ($strict_mode) {
						$s = debug_backtrace();
						
						$s = array_shift($s);
						echo '<pre>' . sprintf(esc_html__('Undefined option "%s" called from:', 'academee'), $name);
						if (function_exists('dco')) dco($s);
						else print_r($s);
						echo '</pre>';
						wp_die();
					} else
						$rez = $defa;
				}
			} else {
				// Override option from GET or POST for current blog mode
				if (!empty($blog_mode) && isset($_REQUEST[$name . '_' . $blog_mode])) {
					$rez = academee_get_value_gpc($name . '_' . $blog_mode);
				// Override option from GET
				} else if (isset($_REQUEST[$name])) {
					$rez = academee_get_value_gpc($name);
				// Override option from current page settings (if exists)
				} else if (academee_storage_isset('options_meta', $name) && !academee_is_inherit(academee_storage_get_array('options_meta', $name))) {
					$rez = academee_storage_get_array('options_meta', $name);
				// Override option from current blog mode settings: 'home', 'search', 'page', 'post', 'blog', etc. (if exists)
				} else if (!empty($blog_mode) && academee_storage_isset('options', $name . '_' . $blog_mode, 'val') && !academee_is_inherit(academee_storage_get_array('options', $name . '_' . $blog_mode, 'val'))) {
					$rez = academee_storage_get_array('options', $name . '_' . $blog_mode, 'val');
				// Get saved option value
				} else if (academee_storage_isset('options', $name, 'val')) {
					$rez = academee_storage_get_array('options', $name, 'val');
				// Get ThemeREX Addons option value
				} else if (function_exists('trx_addons_get_option')) {
					$rez = trx_addons_get_option($name, $defa, false);
				}
			}
		}
		return $rez;
	}
}


// Check if customizable option exists
if (!function_exists('academee_check_theme_option')) {
	function academee_check_theme_option($name) {
		return academee_storage_isset('options', $name);
	}
}


// Get dependencies list from the Theme Options
if ( !function_exists('academee_get_theme_dependencies') ) {
	function academee_get_theme_dependencies() {
		$options = academee_storage_get('options');
		$depends = array();
		foreach ($options as $k=>$v) {
			if (isset($v['dependency'])) 
				$depends[$k] = $v['dependency'];
		}
		return $depends;
	}
}

// Return internal theme setting value
if (!function_exists('academee_get_theme_setting')) {
	function academee_get_theme_setting($name) {
		if ( !academee_storage_isset('settings', $name) ) {
			$s = debug_backtrace();
			
			$s = array_shift($s);
			echo '<pre>' . sprintf(esc_html__('Undefined setting "%s" called from:', 'academee'), $name);
			if (function_exists('dco')) dco($s);
			else print_r($s);
			echo '</pre>';
			wp_die();
		} else
			return academee_storage_get_array('settings', $name);
	}
}

// Set theme setting
if ( !function_exists( 'academee_set_theme_setting' ) ) {
	function academee_set_theme_setting($option_name, $value) {
		if (academee_storage_isset('settings', $option_name))
			academee_storage_set_array('settings', $option_name, $value);
	}
}
?>