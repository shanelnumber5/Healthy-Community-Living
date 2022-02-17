<?php
/**
 * Sinatra WooCommerce section in Customizer.
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

if ( ! class_exists( 'Sinatra_Customizer_WooCommerce' ) ) :
	/**
	 * Sinatra WooCommerce section in Customizer.
	 */
	class Sinatra_Customizer_WooCommerce {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Registers our custom options in Customizer.
			add_filter( 'sinatra_customizer_options', array( $this, 'register_options' ), 20 );
			add_action( 'customize_register', array( $this, 'customizer_tweak' ), 20 );

			// Add default values for WooCommerce options.
			add_filter( 'sinatra_default_option_values', array( $this, 'default_customizer_values' ) );

			// Add localized strings to script.
			add_filter( 'sinatra_customizer_localized', array( $this, 'customizer_localized_strings' ) );
		}

		/**
		 * Add defaults for new WooCommerce customizer options.
		 *
		 * @param  array $defaults Array of default values.
		 * @return array           Array of default values.
		 */
		public function default_customizer_values( $defaults ) {

			$defaults['sinatra_wc_product_gallery_lightbox'] = true;
			$defaults['sinatra_wc_product_gallery_zoom']     = true;
			$defaults['sinatra_shop_product_hover']          = 'none';
			$defaults['sinatra_product_sale_badge']          = 'percentage';
			$defaults['sinatra_product_sale_badge_text']     = esc_html__( 'Sale!', 'sinatra' );
			$defaults['sinatra_wc_product_slider_arrows']    = true;
			$defaults['sinatra_wc_product_gallery_style']    = 'default';
			$defaults['sinatra_wc_product_sidebar_position'] = 'no-sidebar';
			$defaults['sinatra_wc_sidebar_position']         = 'no-sidebar';
			$defaults['sinatra_wc_upsell_products']          = true;
			$defaults['sinatra_wc_upsells_columns']          = 4;
			$defaults['sinatra_wc_upsells_rows']             = 1;
			$defaults['sinatra_wc_related_products']         = true;
			$defaults['sinatra_wc_related_columns']          = 4;
			$defaults['sinatra_wc_related_rows']             = 1;
			$defaults['sinatra_wc_cross_sell_products']      = true;
			$defaults['sinatra_wc_cross_sell_rows']          = 1;
			$defaults['sinatra_product_catalog_elements']    = array(
				'category' => true,
				'title'    => true,
				'ratings'  => true,
				'price'    => true,
			);

			return $defaults;
		}

		/**
		 * Tweak Customizer.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $customizer Instance of WP_Customize_Manager class.
		 */
		public function customizer_tweak( $customizer ) {
			// Move WooCommerce panel.
			$customizer->get_panel( 'woocommerce' )->priority = 10;

			return $customizer;
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Shop image hover effect.
			$options['setting']['sinatra_shop_product_hover'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'woocommerce_product_catalog',
					'label'       => esc_html__( 'Product image hover', 'sinatra' ),
					'description' => esc_html__( 'Effect for product image on hover', 'sinatra' ),
					'choices'     => array(
						'none'       => esc_html__( 'No Effect', 'sinatra' ),
						'image-swap' => esc_html__( 'Image Swap', 'sinatra' ),
					),
				),
			);

			// Sale badge.
			$options['setting']['sinatra_product_sale_badge'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'section'     => 'woocommerce_product_catalog',
					'label'       => esc_html__( 'Product sale badge', 'sinatra' ),
					'description' => esc_html__( 'Choose what to display on the product sale badge.', 'sinatra' ),
					'choices'     => array(
						'hide'       => esc_html__( 'Hide badge', 'sinatra' ),
						'percentage' => esc_html__( 'Show percentage', 'sinatra' ),
						'text'       => esc_html__( 'Show text', 'sinatra' ),
					),
				),
			);

			// Sale badge text.
			$options['setting']['sinatra_product_sale_badge_text'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'sinatra-text',
					'label'       => esc_html__( 'Sale badge text', 'sinatra' ),
					'description' => esc_html__( 'Add custom text for the product sale badge.', 'sinatra' ),
					'placeholder' => esc_html__( 'Sale!', 'sinatra' ),
					'section'     => 'woocommerce_product_catalog',
					'required'    => array(
						array(
							'control'  => 'sinatra_product_sale_badge',
							'value'    => 'text',
							'operator' => '==',
						),
					),
				),
			);

			// Catalog product elements.
			$options['setting']['sinatra_product_catalog_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_sortable',
				'control'           => array(
					'type'        => 'sinatra-sortable',
					'section'     => 'woocommerce_product_catalog',
					'label'       => esc_html__( 'Product details', 'sinatra' ),
					'description' => esc_html__( 'Set order and visibility for product details.', 'sinatra' ),
					'choices'     => array(
						'title'    => esc_html__( 'Title', 'sinatra' ),
						'ratings'  => esc_html__( 'Ratings', 'sinatra' ),
						'price'    => esc_html__( 'Price', 'sinatra' ),
						'category' => esc_html__( 'Category', 'sinatra' ),
					),
				),
			);

			// Section.
			$options['section']['sinatra_woocommerce_single_product'] = array(
				'title'    => esc_html__( 'Single Product', 'sinatra' ),
				'priority' => 50,
				'panel'    => 'woocommerce',
			);

			// Product Gallery Zoom.
			$options['setting']['sinatra_wc_product_gallery_zoom'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Gallery Zoom', 'sinatra' ),
					'description' => esc_html__( 'Enable zoom effect when hovering product gallery.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Product Gallery Lightbox.
			$options['setting']['sinatra_wc_product_gallery_lightbox'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Gallery Lightbox', 'sinatra' ),
					'description' => esc_html__( 'Open product gallery images in lightbox.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Product slider arrows.
			$options['setting']['sinatra_wc_product_slider_arrows'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Slider Arrows', 'sinatra' ),
					'description' => esc_html__( 'Enable left and right arrows on product gallery slider.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Related Products.
			$options['setting']['sinatra_wc_related_products'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Related Products', 'sinatra' ),
					'description' => esc_html__( 'Display related products.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Related product column count.
			$options['setting']['sinatra_wc_related_columns'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Related Products Columns', 'sinatra' ),
					'description' => esc_html__( 'How many related products should be shown per row?', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'min'         => 1,
					'max'         => 6,
					'step'        => 1,
					'required'    => array(
						array(
							'control'  => 'sinatra_wc_related_products',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Related product row count.
			$options['setting']['sinatra_wc_related_rows'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Related Products Rows', 'sinatra' ),
					'description' => esc_html__( 'How many rows of related products should be shown?', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'min'         => 1,
					'max'         => 5,
					'step'        => 1,
					'required'    => array(
						array(
							'control'  => 'sinatra_wc_related_products',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Up-Sell Products.
			$options['setting']['sinatra_wc_upsell_products'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Up-Sell Products', 'sinatra' ),
					'description' => esc_html__( 'Display linked upsell products.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Up-Sells column count.
			$options['setting']['sinatra_wc_upsells_columns'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Up-Sell Products Columns', 'sinatra' ),
					'description' => esc_html__( 'How many up-sell products should be shown per row?', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'min'         => 1,
					'max'         => 6,
					'step'        => 1,
					'required'    => array(
						array(
							'control'  => 'sinatra_wc_upsell_products',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Up-Sells rows count.
			$options['setting']['sinatra_wc_upsells_rows'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Up-Sell Products Rows', 'sinatra' ),
					'description' => esc_html__( 'How many rows of up-sell products should be shown?', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'min'         => 1,
					'max'         => 6,
					'step'        => 1,
					'required'    => array(
						array(
							'control'  => 'sinatra_wc_upsell_products',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Cross-Sell Products.
			$options['setting']['sinatra_wc_cross_sell_products'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_toggle',
				'control'           => array(
					'type'        => 'sinatra-toggle',
					'label'       => esc_html__( 'Cross-Sell Products', 'sinatra' ),
					'description' => esc_html__( 'Display linked cross-sell products on cart page.', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'space'       => true,
				),
			);

			// Cross-Sells rows count.
			$options['setting']['sinatra_wc_cross_sell_rows'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_range',
				'control'           => array(
					'type'        => 'sinatra-range',
					'label'       => esc_html__( 'Cross-Sell Products Rows', 'sinatra' ),
					'description' => esc_html__( 'How many rows of cross-sell products should be shown?', 'sinatra' ),
					'section'     => 'sinatra_woocommerce_single_product',
					'min'         => 1,
					'max'         => 6,
					'step'        => 1,
					'required'    => array(
						array(
							'control'  => 'sinatra_wc_cross_sells_products',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			$sidebar_options = array();

			$sidebar_options['sinatra_wc_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'WooCommerce', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position for cart, checkout and catalog pages. You can change this setting per page via metabox settings.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'sinatra' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			$sidebar_options['sinatra_wc_product_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sinatra_sanitize_select',
				'control'           => array(
					'type'        => 'sinatra-select',
					'label'       => esc_html__( 'WooCommerce - Single Product', 'sinatra' ),
					'description' => esc_html__( 'Choose default sidebar position layout for product pages. You can change this setting per product via metabox settings.', 'sinatra' ),
					'section'     => 'sinatra_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'sinatra' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'sinatra' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'sinatra' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'sinatra' ),
					),
				),
			);

			$options['setting'] = sinatra_array_insert( $options['setting'], $sidebar_options, 'sinatra_archive_sidebar_position' );

			return $options;
		}

		/**
		 * Add localize strings.
		 *
		 * @param  array $strings Array of strings to be localized.
		 * @return array          Modified string array.
		 */
		public function customizer_localized_strings( $strings ) {

			// Preview a random single product for WooCommerce > Single Product section.
			$products = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 1,
					'orderby'        => 'rand',
				)
			);

			if ( count( $products ) ) {
				$strings['preview_url_for_section']['sinatra_woocommerce_single_product'] = get_permalink( $products[0] );
			}

			return $strings;
		}
	}
endif;
new Sinatra_Customizer_WooCommerce();
