<?php
/**
 * Sinatra Sidebar section in Customizer.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Customizer_Sidebar' ) ) :

	/**
	 * Sinatra Sidebar section in Customizer.
	 */
	class Sinatra_Customizer_Sidebar {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom options in Customizer.
			 */
			add_filter( 'sinatra_customizer_options', array( $this, 'register_options' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Section.
			$options['section']['sinatra_section_sidebar'] = array(
				'title'    => esc_html__( 'Sidebar', 'sinatra' ),
				'priority' => 4,
			);

			// Default sidebar position.
			$options['setting']['sinatra_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_sidebar',
					'label'       => esc_html__( 'Default Position', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position layout. You can change this setting per page via metabox settings.', 'sinatra' ),
					'choices'     => array(
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			// Single post sidebar position.
			$options['setting']['sinatra_single_post_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Single Post', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position layout for single posts. You can change this setting per post via metabox settings.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'sinatra' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			// Single page sidebar position.
			$options['setting']['sinatra_single_page_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Page', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position layout for pages. You can change this setting per page via metabox settings.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'sinatra' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			// Archive sidebar position.
			$options['setting']['sinatra_archive_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'Archives & Search', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position layout for archives and search results.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'sinatra' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			// Sidebar options heading.
			$options['setting']['sinatra_sidebar_options_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Options', 'sinatra' ),
					'section' => 'sinatra_section_sidebar',
				),
			);

			// Sidebar style.
			$options['setting']['sinatra_sidebar_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_sidebar',
					'label'       => esc_html__( 'Sidebar Style', 'sinatra' ),
					'description' => esc_html__( 'Choose sidebar style.', 'sinatra' ),
					'choices'     => array(
						'1' => esc_html__( 'Minimal', 'sinatra' ),
						'2' => esc_html__( 'Title Focus', 'sinatra' ),
						'3' => esc_html__( 'Widgets Separated', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sidebar width.
			$options['setting']['sinatra_sidebar_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'section'     => 'sinatra_section_sidebar',
					'label'       => esc_html__( 'Sidebar Width', 'sinatra' ),
					'description' => esc_html__( 'Change your sidebar width.', 'sinatra' ),
					'min'         => 15,
					'max'         => 50,
					'step'        => 1,
					'unit'        => '%',
					'required'    => array(
						array(
							'control'  => 'sinatra_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sticky sidebar.
			$options['setting']['sinatra_sidebar_sticky'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_sidebar',
					'label'       => esc_html__( 'Sticky Sidebar', 'sinatra' ),
					'description' => esc_html__( 'Stick sidebar when scrolling.', 'sinatra' ),
					'choices'     => array(
						''            => esc_html__( 'Disable', 'sinatra' ),
						'sidebar'     => esc_html__( 'Stick first widget', 'sinatra' ),
						'last-widget' => esc_html__( 'Stick last widget', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sidebar mobile position.
			$options['setting']['sinatra_sidebar_responsive_position'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'sinatra_section_sidebar',
					'label'       => esc_html__( 'Responsive Sidebar Position', 'sinatra' ),
					'description' => esc_html__( 'Control sidebar position on smaller screens.', 'sinatra' ),
					'choices'     => array(
						'hide'           => esc_html__( 'Hide', 'sinatra' ),
						'before-content' => esc_html__( 'Before Content', 'sinatra' ),
						'after-content'  => esc_html__( 'After Content', 'sinatra' ),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sidebar typography heading.
			$options['setting']['sinatra_typography_sidebar_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'    => 'sinatra-heading',
					'label'   => esc_html__( 'Typography', 'sinatra' ),
					'section' => 'sinatra_section_sidebar',
				),
			);

			// Sidebar widget heading.
			$options['setting']['sinatra_sidebar_widget_title_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sinatra_sanitize_responsive',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Widget Title Font Size', 'sinatra' ),
					'description' => esc_html__( 'Specify sidebar widget title font size.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'responsive'  => true,
					'unit'        => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 90,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
					),
					'required'    => array(
						array(
							'control'  => 'sinatra_typography_sidebar_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;

new Sinatra_Customizer_Sidebar();
