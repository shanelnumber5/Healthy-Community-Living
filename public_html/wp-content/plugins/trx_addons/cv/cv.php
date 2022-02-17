<?php
/**
 * CV Card support
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Add 'CV Card' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cv_options')) {
	add_action( 'trx_addons_filter_options', 'trx_addons_cv_options');
	function trx_addons_cv_options($options) {
		
		if (trx_addons_components_is_allowed('components', 'cv') && apply_filters('trx_addons_cv_enable', true)) {

			global $TRX_ADDONS_STORAGE;
	
			trx_addons_array_insert_before($options, 'theme_specific_section', array(
			
				// Contacts - address, phone, email, etc.
				'contacts_section' => array(
					"title" => esc_html__('Contacts', 'trx_addons'),
					"desc" => wp_kses_data( __('Address, phone, email, etc.', 'trx_addons') ),
					"type" => "section"
				),
				'contacts_name' => array(
					"title" => esc_html__("Name", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify your name for the printed version of Resume", 'trx_addons') ),
					"std" => '',
					"type" => "text"
				),
				'contacts_position' => array(
					"title" => esc_html__("Position", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify your position for the printed version of Resume", 'trx_addons') ),
					"std" => '',
					"type" => "text"
				),
				'contacts_photo' => array(
					"title" => esc_html__('Photo',  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload your photo for the printed version of Resume',  'trx_addons') ),
					"std" => "",
					"type" => "image"
				),
				'contacts_address' => array(
					"title" => esc_html__("Address", 'trx_addons'),
					"desc" => wp_kses_data( __("Enter your post address", 'trx_addons') ),
					"std" => '',
					"type" => "text"
				),
				'contacts_email' => array(
					"title" => esc_html__("E-mail", 'trx_addons'),
					"desc" => wp_kses_data( __("Enter your e-mail address", 'trx_addons') ),
					"std" => '',
					"type" => "text"
				),
				'contacts_phone' => array(
					"title" => esc_html__("Phone", 'trx_addons'),
					"desc" => wp_kses_data( __("Enter your phone number", 'trx_addons') ),
					"std" => '',
					"type" => "text"
				),
				'contacts_description' => array(
					"title" => esc_html__("About me", 'trx_addons'),
					"desc" => wp_kses_data( __("Short description about site owner (for the printed version of Resume)", 'trx_addons') ),
					"std" => '',
					"type" => "textarea"
				),
		
				// CV Card settings
				'cv_section' => array(
					"title" => esc_html__('CV Card', 'trx_addons'),
					"desc" => wp_kses_data( __('CV Card settings', 'trx_addons') ),
					"type" => "section"
				),
				'cv_info' => array(
					"title" => esc_html__('General Settings', 'trx_addons'),
					"desc" => wp_kses_data( __('General settings of the CV Card - enable/disable CV functionality, sections order, images for the CV/Blog navigation, etc.', 'trx_addons') ),
					"type" => "info"
				),
				'cv_enable' => array(
					"title" => esc_html__('Enable CV Card', 'trx_addons'),
					"desc" => wp_kses_data( __('Enable CV Card functionality on this site', 'trx_addons') ),
					"std" => "0",
					"type" => "checkbox"
				),
				'cv_home' => array(
					"title" => esc_html__('Use CV Card as homepage', 'trx_addons'),
					"desc" => wp_kses_data( __('Use CV Card as homepage of your site', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "0",
					"type" => "checkbox"
				),
				'cv_hide_blog' => array(
					"title" => esc_html__('Hide blog', 'trx_addons'),
					"desc" => wp_kses_data( __('Hide blog and use CV Card as your main site', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_home" => array(1)
					),
					"std" => "0",
					"type" => "checkbox"
				),
				'cv_use_splash' => array(
					"title" => esc_html__('Use splash', 'trx_addons'),
					"desc" => wp_kses_data( __('Show the Splash screen on first visit to the site', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_home" => array(1),
						"cv_hide_blog" => array(0)
					),
					"std" => "0",
					"type" => "checkbox"
				),
				'cv_ajax_loader' => array(
					"title" => esc_html__('Use AJAX loader', 'trx_addons'),
					"desc" => wp_kses_data( __('Use AJAX to load inactive tabs content', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "0",
					"type" => "checkbox"
				),
				'cv_navigation' => array(
					"title" => esc_html__('Navigation', 'trx_addons'),
					"desc" => wp_kses_data( __('Select style of the navigation between CV sections', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "accordion",
					"options" => array(
						"accordion" => esc_html__("Accordion", 'trx_addons'),
						"buttons" => esc_html__("Buttons", 'trx_addons')
						),
					"type" => "radio"
				),
				'cv_button_blog' => array(
					"title" => esc_html__('Small button "Blog"',  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload image for the small button "Blog". If empty - use default image',  'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				'cv_button_cv' => array(
					"title" => esc_html__('Small button "VCard"',  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload image for the small button "VCard". If empty - use default image',  'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				'cv_button_blog2' => array(
					"title" => esc_html__('Splash button "Blog"',  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload image for the large button "Blog", used on the Spalsh screen. If empty - use default image',  'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_home" => array(1),
						"cv_hide_blog" => array(0),
						"cv_use_splash" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				'cv_button_cv2' => array(
					"title" => esc_html__('Splash button "VCard"',  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload image for the large button "VCard", used on the Spalsh screen. If empty - use default image',  'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_home" => array(1),
						"cv_hide_blog" => array(0),
						"cv_use_splash" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				
				// Header Section
				'cv_header_info' => array(
					"title" => esc_html__('Header Settings', 'trx_addons'),
					"desc" => wp_kses_data( __('Header settings - image/photo, socials and typography', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"type" => "info"
				),
				'cv_header_narrow' => array(
					"title" => esc_html__('Header narrow', 'trx_addons'),
					"desc" => wp_kses_data( __("Use narrow header or leave same width for the header and content", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => '0',
					"type" => "checkbox"
				),
				'cv_header_tint' => array(
					"title" => esc_html__('Header bg tint', 'trx_addons'),
					"desc" => wp_kses_data( __('Select main tint of the CV Header background', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "light",
					"options" => array(
						"light" => esc_html__("Light", 'trx_addons'), 
						"dark" => esc_html__("Dark", 'trx_addons')
						),
					"type" => "radio"
				),
				'cv_header_image' => array(
					"title" => esc_html__("Header image",  'trx_addons'),
					"desc" => wp_kses_data( __('Select or upload image for the CV Header area',  'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				'cv_header_image_style' => array(
					"title" => esc_html__('Header image style', 'trx_addons'),
					"desc" => wp_kses_data( __('Select style of the header image: boxed - small image with border, fit - image fit to the header area, cover - image cover whole header area', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_header_image" => array('not_empty')
					),
					"std" => "fit",
					"options" => array(
						"cover" => esc_html__("Cover", 'trx_addons'), 
						"fit" => esc_html__("Fit", 'trx_addons'), 
						"boxed" => esc_html__("Boxed", 'trx_addons')
						),
					"type" => "radio"
				),
				'cv_header_letter' => array(
					"title" => esc_html__("Header letter", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify letter to overlap photo", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => '',
					"type" => "text"
				),
				'cv_header_text' => array(
					"title" => esc_html__("Text in the Header", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify text to display in the Header. If empty - use site name (title)", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => '',
					"type" => "text"
				),
				'cv_header_socials' => array(
					"title" => esc_html__('Social icons', 'trx_addons'),
					"desc" => wp_kses_data( __("Show links to your favorites social networks in the header area", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"std" => '1',
					"type" => "checkbox"
				),
		
				// CV Card parts: About, Resume, Portfolio, Testimonials, Certificates, Contacts
				
				// About Section
				'cv_about_info' => array(
					"title" => esc_html__('About Me Section', 'trx_addons'),
					"desc" => wp_kses_data( __('Select the page that contains information about you', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[about]" => array(1)
					),
					"type" => "info"
				),
				'cv_about_title' => array(
					"title" => esc_html__("Section's title", 'trx_addons'),
					"desc" => wp_kses_data( __("Section's title for this page", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[about]" => array(1)
					),
					"std" => esc_html__('About', 'trx_addons'),
					"type" => "text"
				),
				'cv_about_page' => array(
					"title" => esc_html__('Page About Me', 'trx_addons'),
					"desc" => wp_kses_data( __('Select the page that contains information about you. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[about]" => array(1)
					),
					"std" => '',
					"options" => trx_addons_get_list_pages(),
					"type" => "select2"
				),
	
				// Contacts Section
				'cv_contacts_info' => array(
					"title" => esc_html__('Contacts Section', 'trx_addons'),
					"desc" => wp_kses_data( __('Contacts section parameters', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[contacts]" => array(1)
					),
					"type" => "info"
				),
				'cv_contacts_title' => array(
					"title" => esc_html__("Section's title", 'trx_addons'),
					"desc" => wp_kses_data( __("Contacts section's title", 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[contacts]" => array(1)
					),
					"std" => esc_html__('Contacts', 'trx_addons'),
					"type" => "text"
				),
				'cv_contacts_page' => array(
					"title" => esc_html__('Page Contacts', 'trx_addons'),
					"desc" => wp_kses_data( __('Select the page that contains layout of the Contacts section. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1),
						"cv_parts[contacts]" => array(1)
					),
					"std" => '',
					"options" => trx_addons_get_list_pages(),
					"type" => "select2"
				)
			) );

			// Sections selector
			$std = array(
				'about' => 1,
			);
			$opt = array(
				'about' => esc_html__("About Me", 'trx_addons'),
			);
			if (defined('TRX_ADDONS_CPT_RESUME_PT')) {
				$std['resume'] = 1;
				$opt['resume'] = esc_html__("Resume", 'trx_addons');
			}
			if (defined('TRX_ADDONS_CPT_PORTFOLIO_PT')) {
				$std['portfolio'] = 1;
				$opt['portfolio'] = esc_html__("Portfolio", 'trx_addons');
			}
			if (defined('TRX_ADDONS_CPT_TESTIMONIALS_PT')) {
				$std['testimonials'] = 1;
				$opt['testimonials'] = esc_html__("Testimonials", 'trx_addons');
			}
			if (defined('TRX_ADDONS_CPT_CERTIFICATES_PT')) {
				$std['certificates'] = 1;
				$opt['certificates'] = esc_html__("Certificates", 'trx_addons');
			}
			$std['contacts'] = 1;
			$opt['contacts'] = esc_html__("Contacts", 'trx_addons');

			trx_addons_array_insert_before($options, 'cv_about_info', array( 
				'cv_parts' => array(
					"title" => esc_html__('Sections', 'trx_addons'),
					"desc" => wp_kses_data( __('Select available sections of the CV Card. Drag items to change their order.', 'trx_addons') ),
					"dependency" => array(
						"cv_enable" => array(1)
					),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => $std,
					"options" => $opt,
					"type" => "checklist"
				)
			) );

			// Resume Section
			if (defined('TRX_ADDONS_CPT_RESUME_PT')) {
				$resume = array(
					'cv_resume_info' => array(
						"title" => esc_html__('Resume Section', 'trx_addons'),
						"desc" => wp_kses_data( __('How many posts to be displayed in this section, columns number, use slider, etc.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"type" => "info"
					),
					'cv_resume_title' => array(
						"title" => esc_html__("Section's title", 'trx_addons'),
						"desc" => wp_kses_data( __("Resume section's title", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => esc_html__('Resume', 'trx_addons'),
						"type" => "text"
					),
					'cv_resume_page' => array(
						"title" => esc_html__('Page Resume', 'trx_addons'),
						"desc" => wp_kses_data( __('Select the page that contains layout of the Resume section. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '',
						"options" => trx_addons_get_list_pages(),
						"type" => "select2"
					),
					'cv_resume_parts' => array(
						"title" => esc_html__('Resume parts', 'trx_addons'),
						"desc" => wp_kses_data( __('Select available parts of the Resume section. Drag items to change their order.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"dir" => 'vertical',
						"sortable" => true,
						"std" => array( 'skills' => 1, 'work' => 1, 'education' => 1, 'services' => 1 ),
						"options" => $TRX_ADDONS_STORAGE['cpt_resume_types'],
						"type" => "checklist"
					),
					'cv_resume_print_full' => array(
						"title" => esc_html__('Print full version', 'trx_addons'),
						"desc" => wp_kses_data( __("Print whole resume item's content (full version) or only excerpt (short version)", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '0',
						"type" => "checkbox"
					),
					'cv_resume_download_version' => array(
						"title" => esc_html__("Download version", 'trx_addons'),
						"desc" => wp_kses_data( __("Place here URL to downloadable version of the resume", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '',
						"type" => "text"
					)
				);
				foreach ($TRX_ADDONS_STORAGE['cpt_resume_types'] as $slug => $name) {
					$resume['cv_resume_panel_'.$slug] = array(
						"title" => esc_html($name),
						"desc" => wp_kses_data( __('How many posts to be displayed in this section, columns number, use slider, etc.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"type" => "panel"
					);
					$resume['cv_resume_count_'.$slug] = array(
						"title" => esc_html__("Items number", 'trx_addons'),
						"desc" => wp_kses_data( __("How many items to be displayed?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '4',
						"type" => "text"
					);
					$resume['cv_resume_columns_'.$slug] = array(
						"title" => esc_html__('Columns number', 'trx_addons'),
						"desc" => wp_kses_data( __("How many columns to use to display items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '2',
						"type" => "text"
					);
					$resume['cv_resume_slider_'.$slug] = array(
						"title" => esc_html__('Use Slider', 'trx_addons'),
						"desc" => wp_kses_data( __("Do you want to use Slider to show items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '0',
						"type" => "checkbox"
					);
					$resume['cv_resume_slides_space_'.$slug] = array(
						"title" => esc_html__('Space between slides', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify space between slides (in pixels)", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1),
							"cv_resume_slider_".$slug => array(1)
						),
						"std" => '30',
						"type" => "text"
					);
					$resume['cv_resume_narrow_'.$slug] = array(
						"title" => esc_html__('Narrow', 'trx_addons'),
						"desc" => wp_kses_data( __("Use narrow area to show items in this section", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '0',
						"type" => "checkbox"
					);
					$resume['cv_resume_delimiter_'.$slug] = array(
						"title" => esc_html__('Delimiter', 'trx_addons'),
						"desc" => wp_kses_data( __("Show delimiter between items of this section", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[resume]" => array(1)
						),
						"std" => '0',
						"type" => "checkbox"
					);
				}
				$resume['cv_resume_panel_end'] = array(
					"type" => "panel_end"
				);
				
				trx_addons_array_insert_before($options, 'cv_contacts_info', $resume);
			}
			
			// Portfolio Section
			if (defined('TRX_ADDONS_CPT_PORTFOLIO_PT')) {
				$portfolio = array(
					'cv_portfolio_info' => array(
						"title" => esc_html__('Portfolio Section', 'trx_addons'),
						"desc" => wp_kses_data( __('How many posts to be displayed in this section, columns number, use slider, etc.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"type" => "info"
					),
					'cv_portfolio_title' => array(
						"title" => esc_html__("Section's title", 'trx_addons'),
						"desc" => wp_kses_data( __("Portfolio section's title", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"std" => esc_html__('Portfolio', 'trx_addons'),
						"type" => "text"
					),
					'cv_portfolio_page' => array(
						"title" => esc_html__('Page Portfolio', 'trx_addons'),
						"desc" => wp_kses_data( __('Select the page that contains layout of the Portfolio section. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"std" => '',
						"options" => trx_addons_get_list_pages(),
						"type" => "select2"
						),
					'cv_portfolio_style' => array(
						"title" => esc_html__('Style', 'trx_addons'),
						"desc" => wp_kses_data( __('Select output style for the Portfolio items', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"std" => "1",
						"options" => array(
							"1" => esc_html__("Style 1", 'trx_addons'),
							"2" => esc_html__("Style 2", 'trx_addons'),
							"3" => esc_html__("Style 3", 'trx_addons')
							),
						"type" => "radio"
					),
					'cv_portfolio_count' => array(
						"title" => esc_html__("Items number", 'trx_addons'),
						"desc" => wp_kses_data( __("How many items to be displayed?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"std" => '8',
						"type" => "text"
					),
					'cv_portfolio_columns' => array(
						"title" => esc_html__('Columns number', 'trx_addons'),
						"desc" => wp_kses_data( __("How many columns to use to display items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1)
						),
						"std" => '4',
						"type" => "text"
					),
					'cv_portfolio_slider' => array(
						"title" => esc_html__('Use Slider', 'trx_addons'),
						"desc" => wp_kses_data( __("Do you want to use Slider to show items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1),
							"cv_portfolio_style" => array(1,2)
						),
						"std" => '0',
						"type" => "checkbox"
					),
					'cv_portfolio_slides_space' => array(
						"title" => esc_html__('Space between slides', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify space between slides (in pixels)", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[portfolio]" => array(1),
							"cv_portfolio_style" => array(1,2),
							"cv_portfolio_slider" => array(1)
						),
						"std" => '30',
						"type" => "text"
					)
				);

				trx_addons_array_insert_before($options, 'cv_contacts_info', $portfolio);
			}
	
			
			// Testimonials Section
			if (defined('TRX_ADDONS_CPT_TESTIMONIALS_PT')) {
				$testimonials = array(
					'cv_testimonials_info' => array(
						"title" => esc_html__('Testimonials Section', 'trx_addons'),
						"desc" => wp_kses_data( __('How many posts will be displayed in this section, columns number, use slider, etc.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"type" => "info"
					),
					'cv_testimonials_title' => array(
						"title" => esc_html__("Section's title", 'trx_addons'),
						"desc" => wp_kses_data( __("Testimonials section's title", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"std" => esc_html__('Testimonials', 'trx_addons'),
						"type" => "text"
					),
					'cv_testimonials_page' => array(
						"title" => esc_html__('Page Testimonials', 'trx_addons'),
						"desc" => wp_kses_data( __('Select the page that contains layout of the Testimonials section. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"std" => '',
						"options" => trx_addons_get_list_pages(),
						"type" => "select2"
					),
					'cv_testimonials_count' => array(
						"title" => esc_html__("Items number", 'trx_addons'),
						"desc" => wp_kses_data( __("How many items to be displayed?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"std" => '6',
						"type" => "text"
					),
					'cv_testimonials_columns' => array(
						"title" => esc_html__('Columns number', 'trx_addons'),
						"desc" => wp_kses_data( __("How many columns to use to display items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"std" => '3',
						"type" => "text"
					),
					'cv_testimonials_slider' => array(
						"title" => esc_html__('Use Slider', 'trx_addons'),
						"desc" => wp_kses_data( __("Do you want to use Slider to show items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1)
						),
						"std" => '1',
						"type" => "checkbox"
					),
					'cv_testimonials_slides_space' => array(
						"title" => esc_html__('Space between slides', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify space between slides (in pixels)", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[testimonials]" => array(1),
							"cv_testimonials_slider" => array(1)
						),
						"std" => '30',
						"type" => "text"
					)
				);

				trx_addons_array_insert_before($options, 'cv_contacts_info', $testimonials);
			}
	
			
			// Certificates Section
			if (defined('TRX_ADDONS_CPT_CERTIFICATES_PT')) {
				$certificates = array(
					'cv_certificates_info' => array(
						"title" => esc_html__('Certificates Section', 'trx_addons'),
						"desc" => wp_kses_data( __('How many posts will be displayed in this section, columns number, use slider, etc.', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"type" => "info"
					),
					'cv_certificates_title' => array(
						"title" => esc_html__("Section's title", 'trx_addons'),
						"desc" => wp_kses_data( __("Certificates section's title", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"std" => esc_html__('Certificates', 'trx_addons'),
						"type" => "text"
					),
					'cv_certificates_page' => array(
						"title" => esc_html__('Page Certificates', 'trx_addons'),
						"desc" => wp_kses_data( __('Select the page that contains layout of the Certificates section. Attention! To insert content of this section in the page - place %%CONTENT%% into this page', 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"std" => '',
						"options" => trx_addons_get_list_pages(),
						"type" => "select2"
					),
					'cv_certificates_count' => array(
						"title" => esc_html__("Items number", 'trx_addons'),
						"desc" => wp_kses_data( __("How many items to be displayed?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"std" => '6',
						"type" => "text"
					),
					'cv_certificates_columns' => array(
						"title" => esc_html__('Columns number', 'trx_addons'),
						"desc" => wp_kses_data( __("How many columns to use to display items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"std" => '3',
						"type" => "text"
					),
					'cv_certificates_slider' => array(
						"title" => esc_html__('Use Slider', 'trx_addons'),
						"desc" => wp_kses_data( __("Do you want to use Slider to show items?", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1)
						),
						"std" => '1',
						"type" => "checkbox"
					),
					'cv_certificates_slides_space' => array(
						"title" => esc_html__('Space between slides', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify space between slides (in pixels)", 'trx_addons') ),
						"dependency" => array(
							"cv_enable" => array(1),
							"cv_parts[certificates]" => array(1),
							"cv_certificates_slider" => array(1)
						),
						"std" => '30',
						"type" => "text"
					)
				);

				trx_addons_array_insert_before($options, 'cv_contacts_info', $certificates);
			}


		}
		
		return $options;
	}
}


// Include files with CV
if (!function_exists('trx_addons_cv_load')) {
	add_action( 'after_setup_theme', 'trx_addons_cv_load', 6 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_cv_load', 6 );
	function trx_addons_cv_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		if (trx_addons_components_is_allowed('components', 'cv') 
			&& apply_filters('trx_addons_cv_enable', trx_addons_is_on(trx_addons_get_option('cv_enable', false, false)))) {
			if (($fdir = trx_addons_get_file_dir('cv/includes/cv.php')) != '') {
				include_once $fdir;
			}
			if (trx_addons_get_option('cv_parts[about]') == 1
				&& ($fdir = trx_addons_get_file_dir('cv/includes/cv.about.php')) != '') {
				include_once $fdir;
			}
			if (trx_addons_get_option('cv_parts[resume]') == 1 && defined('TRX_ADDONS_CPT_RESUME_PT')
				&& ($fdir = trx_addons_get_file_dir('cv/includes/cv.resume.php')) != '') {
				include_once $fdir;
			}
			if (trx_addons_get_option('cv_parts[portfolio]') == 1 && defined('TRX_ADDONS_CPT_PORTFOLIO_PT')
				&& ($fdir = trx_addons_get_file_dir('cv/includes/cv.portfolio.php')) != '') {
				include_once $fdir;
			}
			if (trx_addons_get_option('cv_parts[testimonials]') == 1 && defined('TRX_ADDONS_CPT_TESTIMONIALS_PT')
				&& ($fdir = trx_addons_get_file_dir('cv/includes/cv.testimonials.php')) != '') {
				include_once $fdir;
			}
			if (trx_addons_get_option('cv_parts[certificates]') == 1 && defined('TRX_ADDONS_CPT_CERTIFICATES_PT')
				&& ($fdir = trx_addons_get_file_dir('cv/includes/cv.certificates.php')) != '') {
				include_once $fdir;
			}
		}
	}
}
?>