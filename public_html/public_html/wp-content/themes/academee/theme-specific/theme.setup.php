<?php
/**
 * Setup theme-specific fonts and colors
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0.22
 */

// Theme init priorities:
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
if ( !function_exists('academee_customizer_theme_setup1') ) {
	add_action( 'after_setup_theme', 'academee_customizer_theme_setup1', 1 );
	function academee_customizer_theme_setup1() {
		
		// -----------------------------------------------------------------
		// -- Theme fonts (Google and/or custom fonts)
		// -----------------------------------------------------------------
		
		// Fonts to load when theme start
		// It can be Google fonts or uploaded fonts, placed in the folder /css/font-face/font-name inside the theme folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		
        academee_storage_set('load_fonts', array(
            // Google font
            array(
                'name'	 => 'Roboto',
                'family' => 'sans-serif',
                'styles' => '300,300italic,400,400italic,700,700italic'		// Parameter 'style' used only for the Google fonts
            ),
            // Font-face packed with theme
            array(
                'name'	 => 'Bree Serif',
                'family' => 'serif',
                'styles' => '400'		// Parameter 'style' used only for the Google fonts
            )
        ));
		
		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		academee_storage_set('load_fonts_subset', 'latin,latin-ext');
		
		// Settings of the main tags
        academee_storage_set('theme_fonts', array(
            'p' => array(
                'title'				=> esc_html__('Main text', 'academee'),
                'description'		=> esc_html__('Font settings of the main text of the site', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '1rem',
                'font-weight'		=> '300',
                'font-style'		=> 'normal',
                'line-height'		=> '1.7857em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '',
                'margin-top'		=> '0em',
                'margin-bottom'		=> '1em'
            ),
            'h1' => array(
                'title'				=> esc_html__('Heading 1', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '4.6667em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.071em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'uppercase',
                'letter-spacing'	=> '2px',
                'margin-top'		=> '1.05em',
                'margin-bottom'		=> '0.5433em'
            ),
            'h2' => array(
                'title'				=> esc_html__('Heading 2', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '4em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.0833em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '-0.3px',
                'margin-top'		=> '1.01em',
                'margin-bottom'		=> '0.58em'
            ),
            'h3' => array(
                'title'				=> esc_html__('Heading 3', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '3.333em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.2em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '-0.3px',
                'margin-top'		=> '1.27em',
                'margin-bottom'		=> '0.7em'
            ),
            'h4' => array(
                'title'				=> esc_html__('Heading 4', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '2.6667em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.2em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '-0.3px',
                'margin-top'		=> '1.374em',
                'margin-bottom'		=> '0.75em'
            ),
            'h5' => array(
                'title'				=> esc_html__('Heading 5', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '2.4em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.2222em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '0px',
                'margin-top'		=> '1.55em',
                'margin-bottom'		=> '0.85em'
            ),
            'h6' => array(
                'title'				=> esc_html__('Heading 6', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '1.6em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '0.6667em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '0px',
                'margin-top'		=> '2.54em',
                'margin-bottom'		=> '1.6412em'
            ),
            'logo' => array(
                'title'				=> esc_html__('Logo text', 'academee'),
                'description'		=> esc_html__('Font settings of the text case of the logo', 'academee'),
                'font-family'		=> 'Bree Serif, serif',
                'font-size' 		=> '1.8em',
                'font-weight'		=> '400',
                'font-style'		=> 'normal',
                'line-height'		=> '1.25em',
                'text-decoration'	=> 'none',
                'text-transform'	=> '',
                'letter-spacing'	=> '1px'
            ),
            'button' => array(
                'title'				=> esc_html__('Buttons', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '14px',
                'font-weight'		=> '900',
                'font-style'		=> 'normal',
                'line-height'		=> '1.5em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'uppercase',
                'letter-spacing'	=> '0.4px'
            ),
            'input' => array(
                'title'				=> esc_html__('Input fields', 'academee'),
                'description'		=> esc_html__('Font settings of the input fields, dropdowns and textareas', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '0.933em',
                'font-weight'		=> '900',
                'font-style'		=> 'normal',
                'line-height'		=> '1.2em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '0px'
            ),
            'info' => array(
                'title'				=> esc_html__('Post meta', 'academee'),
                'description'		=> esc_html__('Font settings of the post meta: date, counters, share, etc.', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '14px',
                'font-weight'		=> '900',
                'font-style'		=> 'normal',
                'line-height'		=> '1.5em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'uppercase',
                'letter-spacing'	=> '0px',
                'margin-top'		=> '0.4em',
                'margin-bottom'		=> ''
            ),
            'menu' => array(
                'title'				=> esc_html__('Main menu', 'academee'),
                'description'		=> esc_html__('Font settings of the main menu items', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '14px',
                'font-weight'		=> '900',
                'font-style'		=> 'normal',
                'line-height'		=> '1.5em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'uppercase',
                'letter-spacing'	=> '0px'
            ),
            'submenu' => array(
                'title'				=> esc_html__('Dropdown menu', 'academee'),
                'description'		=> esc_html__('Font settings of the dropdown menu items', 'academee'),
                'font-family'		=> 'Roboto, sans-serif',
                'font-size' 		=> '13px',
                'font-weight'		=> '300',
                'font-style'		=> 'normal',
                'line-height'		=> '1.5em',
                'text-decoration'	=> 'none',
                'text-transform'	=> 'none',
                'letter-spacing'	=> '0px'
            )
        ));


        // -----------------------------------------------------------------
		// -- Theme colors for customizer
		// -- Attention! Inner scheme must be last in the array below
		// -----------------------------------------------------------------
		academee_storage_set('scheme_color_groups', array(
			'main'	=> array(
							'title'			=> esc_html__('Main', 'academee'),
							'description'	=> esc_html__('Colors of the main content area', 'academee')
							),
			'alter'	=> array(
							'title'			=> esc_html__('Alter', 'academee'),
							'description'	=> esc_html__('Colors of the alternative blocks (sidebars, etc.)', 'academee')
							),
			'extra'	=> array(
							'title'			=> esc_html__('Extra', 'academee'),
							'description'	=> esc_html__('Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'academee')
							),
			'inverse' => array(
							'title'			=> esc_html__('Inverse', 'academee'),
							'description'	=> esc_html__('Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'academee')
							),
			'input'	=> array(
							'title'			=> esc_html__('Input', 'academee'),
							'description'	=> esc_html__('Colors of the form fields (text field, textarea, select, etc.)', 'academee')
							),
			)
		);
		academee_storage_set('scheme_color_names', array(
			'bg_color'	=> array(
							'title'			=> esc_html__('Background color', 'academee'),
							'description'	=> esc_html__('Background color of this block in the normal state', 'academee')
							),
			'bg_hover'	=> array(
							'title'			=> esc_html__('Background hover', 'academee'),
							'description'	=> esc_html__('Background color of this block in the hovered state', 'academee')
							),
			'bd_color'	=> array(
							'title'			=> esc_html__('Border color', 'academee'),
							'description'	=> esc_html__('Border color of this block in the normal state', 'academee')
							),
			'bd_hover'	=>  array(
							'title'			=> esc_html__('Border hover', 'academee'),
							'description'	=> esc_html__('Border color of this block in the hovered state', 'academee')
							),
			'text'		=> array(
							'title'			=> esc_html__('Text', 'academee'),
							'description'	=> esc_html__('Color of the plain text inside this block', 'academee')
							),
			'text_dark'	=> array(
							'title'			=> esc_html__('Text dark', 'academee'),
							'description'	=> esc_html__('Color of the dark text (bold, header, etc.) inside this block', 'academee')
							),
			'text_light'=> array(
							'title'			=> esc_html__('Text light', 'academee'),
							'description'	=> esc_html__('Color of the light text (post meta, etc.) inside this block', 'academee')
							),
			'text_link'	=> array(
							'title'			=> esc_html__('Link', 'academee'),
							'description'	=> esc_html__('Color of the links inside this block', 'academee')
							),
			'text_hover'=> array(
							'title'			=> esc_html__('Link hover', 'academee'),
							'description'	=> esc_html__('Color of the hovered state of links inside this block', 'academee')
							),
            'text_link_bd'=> array(
                'title'			=> esc_html__('Border for link', 'academee'),
                'description'	=> esc_html__('Color for border on link', 'academee')
            ),
            'text_link_bd_hover'=> array(
                'title'			=> esc_html__('Border for link hover', 'academee'),
                'description'	=> esc_html__('Color of the hovered state for border on link', 'academee')
                              ),

			'text_link2'=> array(
							'title'			=> esc_html__('Link 2', 'academee'),
							'description'	=> esc_html__('Color of the accented texts (areas) inside this block', 'academee')
							),
			'text_hover2'=> array(
							'title'			=> esc_html__('Link 2 hover', 'academee'),
							'description'	=> esc_html__('Color of the hovered state of accented texts (areas) inside this block', 'academee')
							),
			'text_link3'=> array(
							'title'			=> esc_html__('Link 3', 'academee'),
							'description'	=> esc_html__('Color of the other accented texts (buttons) inside this block', 'academee')
							),
			'text_hover3'=> array(
							'title'			=> esc_html__('Link 3 hover', 'academee'),
							'description'	=> esc_html__('Color of the hovered state of other accented texts (buttons) inside this block', 'academee')
							)
			)
		);
		academee_storage_set('schemes', array(
		
			// Color scheme: 'default'
			'default' => array(
				'title'	 => esc_html__('Default', 'academee'),
				'colors' => array(
					
					// Whole block border and background
					'bg_color'			=> '#ffffff',//+
					'bd_color'			=> '#dae2e3',//+
		
					// Text and links colors
					'text'				=> '#717778',//+
					'text_light'		=> '#c4c6c6',//+
					'text_dark'			=> '#212323',//+
					'text_link'			=> '#ff5951',//+
					'text_hover'		=> '#02bdc3',//+
                    'text_link_bd'		=> '#ffaca8',//+
                    'text_link_bd_hover'=> '#80dee1',//+
					'text_link2'		=> '#efa9a6',//+
					'text_hover2'		=> '#8be77c',
					'text_link3'		=> '#ddb837',
					'text_hover3'		=> '#eec432',
		
					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'	=> '#02bdc3',//+
					'alter_bg_hover'	=> '#212323',//+
					'alter_bd_color'	=> '#e3e3e3',//+
					'alter_bd_hover'	=> '#dadada',
					'alter_text'		=> '#585858',//+
					'alter_light'		=> '#b7b7b7',//+
					'alter_dark'		=> '#333333',//+
					'alter_link'		=> '#fe7259',
					'alter_hover'		=> '#72cfd5',
                    'alter_link2'		=> '#888e8f',//+
                    'alter_hover2'		=> '#ffffff',//+
					'alter_link3'		=> '#ddb837',
					'alter_hover3'		=> '#eec432',
		
					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'	=> '#212323',//+
					'extra_bg_hover'	=> '#000000',//+
					'extra_bd_color'	=> '#313131',
					'extra_bd_hover'	=> '#3d3d3d',
					'extra_text'		=> '#c4c6c6',//+
					'extra_light'		=> '#c4c6c6',//+
					'extra_dark'		=> '#1e1d22',//+
					'extra_link'		=> '#72cfd5',
					'extra_hover'		=> '#ff5951',//+
					'extra_link2'		=> '#80d572',
					'extra_hover2'		=> '#8be77c',
					'extra_link3'		=> '#ddb837',
					'extra_hover3'		=> '#eec432',
		
					// Input fields (form's fields and textarea)
					'input_bg_color'	=> '#f0f0f0',//+
					'input_bg_hover'	=> '#e7e7e7',//+
					'input_bd_color'	=> '#f0f0f0',//+
					'input_bd_hover'	=> '#e7e7e7',//+
					'input_text'		=> '#212323',//+
					'input_light'		=> '#d0d0d0',
					'input_dark'		=> '#212323',//+
					
					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color'	=> '#67bcc1',//+
					'inverse_bd_hover'	=> '#383939',//+
					'inverse_text'		=> '#ffffff',//+
					'inverse_light'		=> '#c4c6c6',//+
					'inverse_dark'		=> '#000000',
					'inverse_link'		=> '#ffffff',
					'inverse_hover'		=> '#1d1d1d'
				)
			),
		
			// Color scheme: 'dark'
			'dark' => array(
				'title'  => esc_html__('Dark', 'academee'),
				'colors' => array(
					
					// Whole block border and background
					'bg_color'			=> '#212323',//+
					'bd_color'			=> '#1c1b1f',
		
					// Text and links colors
					'text'				=> '#888e8f',//+
					'text_light'		=> '#212323',//+
					'text_dark'			=> '#ffffff',//+
                    'text_link'			=> '#ff5951',//+
                    'text_hover'		=> '#02bdc3',//+
                    'text_link_bd'		=> '#ffaca8',//+
                    'text_link_bd_hover'=> '#80dee1',//+
					'text_link2'		=> '#80d572',
					'text_hover2'		=> '#8be77c',
					'text_link3'		=> '#ddb837',
					'text_hover3'		=> '#eec432',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'	=> '#1e1d22',//+
					'alter_bg_hover'	=> '#28272e',
					'alter_bd_color'	=> '#313131',//+
					'alter_bd_hover'	=> '#3d3d3d',
					'alter_text'		=> '#585858',//+
					'alter_light'		=> '#918a80',//+
					'alter_dark'		=> '#ffffff',//+
					'alter_link'		=> '#c4c6c6',//+
					'alter_hover'		=> '#ff5951',//+
					'alter_link2'		=> '#888e8f',//+
					'alter_hover2'		=> '#ffffff',//+
					'alter_link3'		=> '#ddb837',
					'alter_hover3'		=> '#eec432',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'	=> '#1e1d22',
					'extra_bg_hover'	=> '#28272e',
					'extra_bd_color'	=> '#313131',
					'extra_bd_hover'	=> '#3d3d3d',
					'extra_text'		=> '#a6a6a6',
					'extra_light'		=> '#ffffff',//+
					'extra_dark'		=> '#ffffff',//+
					'extra_link'		=> '#ffaa5f',
					'extra_hover'		=> '#fe7259',
					'extra_link2'		=> '#80d572',
					'extra_hover2'		=> '#8be77c',
					'extra_link3'		=> '#ddb837',
					'extra_hover3'		=> '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'	=> '#404040',//+
					'input_bg_hover'	=> '#505050',//+
					'input_bd_color'	=> '#404040',//+
					'input_bd_hover'	=> '#505050',//+
					'input_text'		=> '#b7b7b7',//+
					'input_light'		=> '#5f5f5f',
					'input_dark'		=> '#212323',//+
					
					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color'	=> '#f0f0f0',//+
					'inverse_bd_hover'	=> '#ffffff',//+
					'inverse_text'		=> '#ffffff',//+
					'inverse_light'		=> '#9d9795',//+
					'inverse_dark'		=> '#000000',//+
					'inverse_link'		=> '#ffffff',
					'inverse_hover'		=> '#1d1d1d'
				)
			)
		
		));
	}
}

			
// Additional (calculated) theme-specific colors
// Attention! Don't forget setup custom colors also in the theme.customizer.color-scheme.js
if (!function_exists('academee_customizer_add_theme_colors')) {
	function academee_customizer_add_theme_colors($colors) {
		if (substr($colors['text'], 0, 1) == '#') {
			$colors['bg_color_0']  = academee_hex2rgba( $colors['bg_color'], 0 );
			$colors['bg_color_02']  = academee_hex2rgba( $colors['bg_color'], 0.2 );
			$colors['bg_color_07']  = academee_hex2rgba( $colors['bg_color'], 0.7 );
			$colors['bg_color_08']  = academee_hex2rgba( $colors['bg_color'], 0.8 );
			$colors['bg_color_09']  = academee_hex2rgba( $colors['bg_color'], 0.9 );
			$colors['alter_bg_color_07']  = academee_hex2rgba( $colors['alter_bg_color'], 0.7 );
			$colors['alter_bg_color_04']  = academee_hex2rgba( $colors['alter_bg_color'], 0.4 );
			$colors['alter_bg_color_02']  = academee_hex2rgba( $colors['alter_bg_color'], 0.2 );
			$colors['alter_bd_color_02']  = academee_hex2rgba( $colors['alter_bd_color'], 0.2 );
			$colors['extra_bg_color_07']  = academee_hex2rgba( $colors['extra_bg_color'], 0.7 );
			$colors['input_bg_color_04']  = academee_hex2rgba( $colors['input_bg_color'], 0.4 );
			$colors['extra_bg_hover_02']  = academee_hex2rgba( $colors['extra_bg_hover'], 0.2 );
			$colors['extra_bg_hover_008']  = academee_hex2rgba( $colors['extra_bg_hover'], 0.08 );
			$colors['text_dark_07']  = academee_hex2rgba( $colors['text_dark'], 0.7 );
			$colors['text_dark_04']  = academee_hex2rgba( $colors['text_dark'], 0.4 );
			$colors['text_link_02']  = academee_hex2rgba( $colors['text_link'], 0.2 );
			$colors['text_link_07']  = academee_hex2rgba( $colors['text_link'], 0.7 );
			$colors['text_link_04']  = academee_hex2rgba( $colors['text_link'], 0.4 );
			$colors['text_hover_04']  = academee_hex2rgba( $colors['text_hover'], 0.4 );
			$colors['inverse_text_07']  = academee_hex2rgba( $colors['inverse_text'], 0.7 );
			$colors['inverse_text_04']  = academee_hex2rgba( $colors['inverse_text'], 0.4 );
			$colors['inverse_dark_03']  = academee_hex2rgba( $colors['inverse_dark'], 0.3 );
			$colors['text_link_blend'] = academee_hsb2hex(academee_hex2hsb( $colors['text_link'], 2, -5, 5 ));
			$colors['alter_link_blend'] = academee_hsb2hex(academee_hex2hsb( $colors['alter_link'], 2, -5, 5 ));
		} else {
			$colors['bg_color_0'] = '{{ data.bg_color_0 }}';
			$colors['bg_color_02'] = '{{ data.bg_color_02 }}';
			$colors['bg_color_07'] = '{{ data.bg_color_07 }}';
			$colors['bg_color_08'] = '{{ data.bg_color_08 }}';
			$colors['bg_color_09'] = '{{ data.bg_color_09 }}';
			$colors['alter_bg_color_07'] = '{{ data.alter_bg_color_07 }}';
			$colors['alter_bg_color_04'] = '{{ data.alter_bg_color_04 }}';
			$colors['alter_bg_color_02'] = '{{ data.alter_bg_color_02 }}';
			$colors['alter_bd_color_02'] = '{{ data.alter_bd_color_02 }}';
			$colors['extra_bg_color_07'] = '{{ data.extra_bg_color_07 }}';
			$colors['input_bg_color_04'] = '{{ data.input_bg_color_04 }}';
			$colors['extra_bg_hover_02'] = '{{ data.extra_bg_hover_07 }}';
			$colors['extra_bg_hover_008'] = '{{ data.extra_bg_hover_008 }}';
			$colors['text_dark_07'] = '{{ data.text_dark_07 }}';
			$colors['text_dark_04'] = '{{ data.text_dark_04 }}';
			$colors['text_link_02'] = '{{ data.text_link_02 }}';
			$colors['text_link_07'] = '{{ data.text_link_07 }}';
			$colors['text_link_04'] = '{{ data.text_link_04 }}';
			$colors['text_hover_04'] = '{{ data.text_hover_04 }}';
			$colors['inverse_text_07'] = '{{ data.inverse_text_07 }}';
			$colors['inverse_text_04'] = '{{ data.inverse_text_04 }}';
			$colors['inverse_dark_03'] = '{{ data.inverse_dark_03 }}';
			$colors['text_link_blend'] = '{{ data.text_link_blend }}';
			$colors['alter_link_blend'] = '{{ data.alter_link_blend }}';
		}
		return $colors;
	}
}


			
// Additional theme-specific fonts rules
// Attention! Don't forget setup fonts rules also in the theme.customizer.color-scheme.js
if (!function_exists('academee_customizer_add_theme_fonts')) {
	function academee_customizer_add_theme_fonts($fonts) {
		$rez = array();	
		foreach ($fonts as $tag => $font) {
			
			if (substr($font['font-family'], 0, 2) != '{{') {
				$rez[$tag.'_font-family'] 		= !empty($font['font-family']) && !academee_is_inherit($font['font-family'])
														? 'font-family:' . trim($font['font-family']) . ';' 
														: '';
				$rez[$tag.'_font-size'] 		= !empty($font['font-size']) && !academee_is_inherit($font['font-size'])
														? 'font-size:' . academee_prepare_css_value($font['font-size']) . ";"
														: '';
				$rez[$tag.'_line-height'] 		= !empty($font['line-height']) && !academee_is_inherit($font['line-height'])
														? 'line-height:' . trim($font['line-height']) . ";"
														: '';
				$rez[$tag.'_font-weight'] 		= !empty($font['font-weight']) && !academee_is_inherit($font['font-weight'])
														? 'font-weight:' . trim($font['font-weight']) . ";"
														: '';
				$rez[$tag.'_font-style'] 		= !empty($font['font-style']) && !academee_is_inherit($font['font-style'])
														? 'font-style:' . trim($font['font-style']) . ";"
														: '';
				$rez[$tag.'_text-decoration'] 	= !empty($font['text-decoration']) && !academee_is_inherit($font['text-decoration'])
														? 'text-decoration:' . trim($font['text-decoration']) . ";"
														: '';
				$rez[$tag.'_text-transform'] 	= !empty($font['text-transform']) && !academee_is_inherit($font['text-transform'])
														? 'text-transform:' . trim($font['text-transform']) . ";"
														: '';
				$rez[$tag.'_letter-spacing'] 	= !empty($font['letter-spacing']) && !academee_is_inherit($font['letter-spacing'])
														? 'letter-spacing:' . trim($font['letter-spacing']) . ";"
														: '';
				$rez[$tag.'_margin-top'] 		= !empty($font['margin-top']) && !academee_is_inherit($font['margin-top'])
														? 'margin-top:' . academee_prepare_css_value($font['margin-top']) . ";"
														: '';
				$rez[$tag.'_margin-bottom'] 	= !empty($font['margin-bottom']) && !academee_is_inherit($font['margin-bottom'])
														? 'margin-bottom:' . academee_prepare_css_value($font['margin-bottom']) . ";"
														: '';
			} else {
				$rez[$tag.'_font-family']		= '{{ data["'.$tag.'_font-family"] }}';
				$rez[$tag.'_font-size']			= '{{ data["'.$tag.'_font-size"] }}';
				$rez[$tag.'_line-height']		= '{{ data["'.$tag.'_line-height"] }}';
				$rez[$tag.'_font-weight']		= '{{ data["'.$tag.'_font-weight"] }}';
				$rez[$tag.'_font-style']		= '{{ data["'.$tag.'_font-style"] }}';
				$rez[$tag.'_text-decoration']	= '{{ data["'.$tag.'_text-decoration"] }}';
				$rez[$tag.'_text-transform']	= '{{ data["'.$tag.'_text-transform"] }}';
				$rez[$tag.'_letter-spacing']	= '{{ data["'.$tag.'_letter-spacing"] }}';
				$rez[$tag.'_margin-top']		= '{{ data["'.$tag.'_margin-top"] }}';
				$rez[$tag.'_margin-bottom']		= '{{ data["'.$tag.'_margin-bottom"] }}';
			}
		}
		return $rez;
	}
}


//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------

if ( !function_exists('academee_customizer_theme_setup') ) {
	add_action( 'after_setup_theme', 'academee_customizer_theme_setup' );
	function academee_customizer_theme_setup() {

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size(370, 0, false);
		
		// Add thumb sizes
		// ATTENTION! If you change list below - check filter's names in the 'trx_addons_filter_get_thumb_size' hook
		$thumb_sizes = apply_filters('academee_filter_add_thumb_sizes', array(
			'academee-thumb-huge'		=> array(1170, 658, true),
			'academee-thumb-big' 		=> array( 760, 428, true),
			'academee-thumb-big-avatar' 	=> array( 570, 560, true),
			'academee-thumb-med' 		=> array( 370, 208, true),
			'academee-thumb-tiny' 		=> array(  90,  90, true),
			'academee-thumb-masonry-big' => array( 760,   0, false),		// Only downscale, not crop
			'academee-thumb-masonry'		=> array( 370,   0, false),		// Only downscale, not crop
			)
		);
		$mult = academee_get_theme_option('retina_ready', 1);
		if ($mult > 1) $GLOBALS['content_width'] = apply_filters( 'academee_filter_content_width', 1170*$mult);
		foreach ($thumb_sizes as $k=>$v) {
			// Add Original dimensions
			add_image_size( $k, $v[0], $v[1], $v[2]);
			// Add Retina dimensions
			if ($mult > 1) add_image_size( $k.'-@retina', $v[0]*$mult, $v[1]*$mult, $v[2]);
		}

	}
}

if ( !function_exists('academee_customizer_image_sizes') ) {
	add_filter( 'image_size_names_choose', 'academee_customizer_image_sizes' );
	function academee_customizer_image_sizes( $sizes ) {
		$thumb_sizes = apply_filters('academee_filter_add_thumb_sizes', array(
			'academee-thumb-huge'		=> esc_html__( 'Fullsize image', 'academee' ),
			'academee-thumb-big'			=> esc_html__( 'Large image', 'academee' ),
			'academee-thumb-big-avatar'	=> esc_html__( 'Large avatar image', 'academee' ),
			'academee-thumb-med'			=> esc_html__( 'Medium image', 'academee' ),
			'academee-thumb-tiny'		=> esc_html__( 'Small square avatar', 'academee' ),
			'academee-thumb-masonry-big'	=> esc_html__( 'Masonry Large (scaled)', 'academee' ),
			'academee-thumb-masonry'		=> esc_html__( 'Masonry (scaled)', 'academee' ),
			)
		);
		$mult = academee_get_theme_option('retina_ready', 1);
		foreach($thumb_sizes as $k=>$v) {
			$sizes[$k] = $v;
			if ($mult > 1) $sizes[$k.'-@retina'] = $v.' '.esc_html__('@2x', 'academee' );
		}
		return $sizes;
	}
}

// Remove some thumb-sizes from the ThemeREX Addons list
if ( !function_exists( 'academee_customizer_trx_addons_add_thumb_sizes' ) ) {
	add_filter( 'trx_addons_filter_add_thumb_sizes', 'academee_customizer_trx_addons_add_thumb_sizes');
	function academee_customizer_trx_addons_add_thumb_sizes($list=array()) {
		if (is_array($list)) {
			foreach ($list as $k=>$v) {
				if (in_array($k, array(
								'trx_addons-thumb-huge',
								'trx_addons-thumb-big',
								'trx_addons-thumb-big-avatar',
								'trx_addons-thumb-medium',
								'trx_addons-thumb-tiny',
								'trx_addons-thumb-masonry-big',
								'trx_addons-thumb-masonry',
								)
							)
						) unset($list[$k]);
			}
		}
		return $list;
	}
}

// and replace removed styles with theme-specific thumb size
if ( !function_exists( 'academee_customizer_trx_addons_get_thumb_size' ) ) {
	add_filter( 'trx_addons_filter_get_thumb_size', 'academee_customizer_trx_addons_get_thumb_size');
	function academee_customizer_trx_addons_get_thumb_size($thumb_size='') {
		return str_replace(array(
							'trx_addons-thumb-huge',
							'trx_addons-thumb-huge-@retina',
							'trx_addons-thumb-big',
							'trx_addons-thumb-big-@retina',
                            'trx_addons-thumb-big-avatar',
							'trx_addons-thumb-big-avatar-@retina',
							'trx_addons-thumb-medium',
							'trx_addons-thumb-medium-@retina',
							'trx_addons-thumb-tiny',
							'trx_addons-thumb-tiny-@retina',
							'trx_addons-thumb-masonry-big',
							'trx_addons-thumb-masonry-big-@retina',
							'trx_addons-thumb-masonry',
							'trx_addons-thumb-masonry-@retina',
							),
							array(
							'academee-thumb-huge',
							'academee-thumb-huge-@retina',
							'academee-thumb-big',
							'academee-thumb-big-@retina',
                            'academee-thumb-big-avatar',
							'academee-thumb-big-avatar-@retina',
							'academee-thumb-med',
							'academee-thumb-med-@retina',
							'academee-thumb-tiny',
							'academee-thumb-tiny-@retina',
							'academee-thumb-masonry-big',
							'academee-thumb-masonry-big-@retina',
							'academee-thumb-masonry',
							'academee-thumb-masonry-@retina',
							),
							$thumb_size);
	}
}
?>