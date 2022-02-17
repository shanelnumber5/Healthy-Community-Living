<?php
/**
 * WooCommerce Compatibility File.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

// If WooCommerce is not activated then return.
if ( ! sinatra_is_woocommerce_activated() ) {
	add_action( 'activate_woocommerce/woocommerce.php', array( sinatra_dynamic_styles(), 'delete_dynamic_file' ) );
	return;
}

/**
 * Sinatra WooCommerce Compatibility.
 */
if ( ! class_exists( 'Sinatra_Woocommerce' ) ) :

	/**
	 * Sinatra WooCommerce Compatibility
	 *
	 * @since 1.0.0
	 */
	class Sinatra_Woocommerce {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Main Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Woocommerce
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Woocommerce ) ) {
				self::$instance = new Sinatra_Woocommerce();

				self::$instance->includes();
				self::$instance->actions();
			}

			return self::$instance;
		}

		/**
		 * Include files.
		 *
		 * @since 1.0.0
		 */
		private function includes() {

			require SINATRA_THEME_PATH . '/inc/compatibility/woocommerce/woocommerce-functions.php'; // phpcs:ignore
			require SINATRA_THEME_PATH . '/inc/compatibility/woocommerce/class-sinatra-customizer-woocommerce.php'; // phpcs:ignore
		}

		/**
		 * WooCommerce actions.
		 *
		 * @since 1.0.0
		 */
		private function actions() {

			// Cart fragment.
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
				add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_widget_count_fragment' ) );
				add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_widget_dropdown_fragment' ) );
			} else {
				add_filter( 'add_to_cart_fragments', array( $this, 'cart_widget_count_fragment' ) );
				add_filter( 'add_to_cart_fragments', array( $this, 'cart_widget_dropdown_fragment' ) );
			}

			// Frontend actions only.
			if ( ! is_admin() ) {

				add_action( 'wp', array( $this, 'product_catalog_elements' ) );

				// Disable WooCommerce shop title.
				add_filter( 'woocommerce_show_page_title', '__return_false' );

				// Disable Sinatra page description.
				add_filter( 'sinatra_page_header_description', array( $this, 'shop_remove_page_description' ) );

				// Remove WooCommerce content wrappers.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
				remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

				// Remove WooCommerce breadcrumbs.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

				// Extend Sinatra breadcrumb trail.
				add_filter( 'sinatra_breadcrumb_trail_items', array( $this, 'breadcrumbs' ), 20, 2 );
				add_filter( 'sinatra_post_type_archive_title', array( $this, 'breadcrumb_post_type_archive_title' ), 10, 2 );

				// Remove WooCommerce sidebar.
				remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
				add_action( 'sinatra_woocommerce_sidebar', 'woocommerce_get_sidebar' );

				// Add our content wrappers.
				add_action( 'woocommerce_before_main_content', array( $this, 'content_wrapper_start' ), 10 );
				add_action( 'woocommerce_after_main_content', array( $this, 'content_wrapper_end' ), 10 );

				// Replace WooCommerce pagination with Sinatra pagination.
				remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
				add_action( 'woocommerce_after_shop_loop', 'sinatra_pagination' );

				// Add back to shop button to Empty Cart.
				add_action( 'woocommerce_cart_is_empty', 'sinatra_wc_empty_cart_button' );

				// Add wrapper to result count and catalog ordering.
				add_action( 'woocommerce_before_shop_loop', array( $this, 'result_wrapper_start' ), 19 );
				add_action( 'woocommerce_before_shop_loop', array( $this, 'result_wrapper_end' ), 31 );

				// Remove opening link tag.
				remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

				// Add thumbnail wrapper.
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_product_thumb_wrap_start' ), 5 );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_product_thumb_wrap_end' ), 15 );

				// Add product link to thumnail.
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 6 );
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 13 );

				// Add alternative image to display on hover.
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_image_swap' ), 11 );

				// Add to cart button to display on hover.
				add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 14 );

				// Add wrapper to product meta details.
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_product_details_wrap_open' ), 19 );
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'loop_product_details_wrap_end' ), 10 );

				add_action( 'woocommerce_before_single_product_summary', array( $this, 'single_product_wrapper_start' ), 5 );
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_wrapper_end' ), 5 );

				// Remove add to cart button from catalog pages.
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

				// Percentage sale badge.
				add_filter( 'woocommerce_sale_flash', 'sinatra_wc_add_percentage_to_sale_badge', 20, 3 );

				// Out of stock product badge.
				add_action( 'woocommerce_before_shop_loop_item_title', 'sinatra_wc_out_of_stock_badge', 10 );
				add_action( 'woocommerce_before_single_product_summary', 'sinatra_wc_out_of_stock_badge', 10 );

				// Additional classes for add to cart button.
				add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'loop_add_to_cart_args' ) );

				// Heading for checkout page order.
				add_action( 'woocommerce_review_order_before_payment', array( $this, 'review_order_heading' ) );

				// Remove mini cart buttons and replace with ours.
				remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
				remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
				add_action( 'woocommerce_widget_shopping_cart_buttons', 'sinatra_wc_widget_shopping_cart_buttons', 10 );

				// Hide Yith wishlist - we show it in our page title anyway.
				add_filter( 'yith_wcwl_wishlist_title', '__return_false', 20 );

				// Remove checkout heading.
				add_action( 'woocommerce_checkout_shipping', array( $this, 'checkout_shipping_heading' ), 9 );

				add_filter( 'woocommerce_subcategory_count_html', 'sinatra_wc_cat_count_filter', 10, 2 );
				add_filter( 'woocommerce_rating_filter_count', 'sinatra_wc_rating_count_filter', 10, 3 );
				add_filter( 'woocommerce_layered_nav_count', 'sinatra_wc_layered_count_filter', 10, 3 );

				// Upsell Products.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'woocommerce_upsell_display' ), 15 );

				// Related Products.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'woocommerce_related_products' ), 15 );

				// Related products columns/count.
				add_filter( 'woocommerce_output_related_products_args', array( $this, 'single_product_related_products_args' ) );

				add_filter( 'woocommerce_single_product_carousel_options', array( $this, 'single_product_slider_options' ) );

				// Cross-Sell products.
				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
				add_action( 'woocommerce_cart_collaterals', array( $this, 'woocommerce_cross_sell_display' ) );

				// Product gallery thumbnail columns.
				add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'product_thumbnails_columns' ) );
			}

			// Enqueue styles.
			add_action( 'sinatra_enqueue_scripts', array( $this, 'enqueue' ) );

			// Single product actions.
			add_action( 'wp_head', array( $this, 'product_actions' ), 9 );

			// Register WooCommerce sidebars.
			add_action( 'widgets_init', array( $this, 'register_wc_sidebars' ) );

			// Add correct sidebar.
			add_filter( 'sinatra_sidebar_name', array( $this, 'set_sidebar' ) );

			// Set sidebar position.
			add_filter( 'sinatra_default_sidebar_position', array( $this, 'set_default_sidebar_position' ) );

			// Remove item from cart.
			add_action( 'wp_ajax_sinatra_remove_wc_cart_item', array( $this, 'remove_item_from_cart' ) );
			add_action( 'wp_ajax_nopriv_sinatra_remove_wc_cart_item', array( $this, 'remove_item_from_cart' ) );

			// Add theme supports.
			add_action( 'after_setup_theme', array( $this, 'theme_supports' ), 20 );

			// Add customizer cart widget.
			add_filter( 'sinatra_customizer_widgets', array( $this, 'add_customizer_cart_widget' ) );
			add_filter( 'sinatra_main_header_widgets', array( $this, 'add_cart_to_main_header_widgets' ) );

			// Loads Cart Customizer widgets class.
			add_action( 'customize_register', array( $this, 'load_customizer_widget' ), 20 );

			// Handle admin redirects.
			add_action( 'admin_init', array( $this, 'admin_redirects' ), 9 );

			// Add dynamic CSS.
			add_filter( 'sinatra_dynamic_styles', array( $this, 'dynamic_css' ), 5 );

			// Update dynamic styles on deactivation.
			add_action( 'deactivate_woocommerce/woocommerce.php', array( sinatra_dynamic_styles(), 'delete_dynamic_file' ) );

			// Return Shop page ID.
			add_filter( 'sinatra_get_the_id', array( $this, 'get_the_id' ) );

			add_filter( 'woocommerce_product_related_products_heading', array( $this, 'related_products_heading' ) );
		}

		/**
		 * Declare WooCommerce support.
		 *
		 * @since 1.0.0
		 */
		public function theme_supports() {

			// Declare WooCommerce compatibility.
			add_theme_support(
				'woocommerce',
				array(
					'gallery_thumbnail_image_width' => 150,
				)
			);

			// Product Gallery Slider.
			add_theme_support( 'wc-product-gallery-slider' );

			// Product Gallery Zoom.
			if ( sinatra_option( 'wc_product_gallery_zoom' ) ) {
				add_theme_support( 'wc-product-gallery-zoom' );
			}

			// Product Gallery Lightbox.
			if ( sinatra_option( 'wc_product_gallery_lightbox' ) ) {
				add_theme_support( 'wc-product-gallery-lightbox' );
			}
		}

		/**
		 * Enqueue WooCommerce styles.
		 *
		 * @since 1.0.0
		 */
		public function enqueue() {

			// Script debug.
			$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue WooCommerce compatibility stylesheet.
			wp_enqueue_style(
				'sinatra-woocommerce',
				SINATRA_THEME_URI . '/assets/css/compatibility/woocommerce' . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION,
				'all'
			);

			// Enqueue WooCommerce compatibility script.
			wp_enqueue_script(
				'sinatra-wc',
				SINATRA_THEME_URI . '/assets/js/' . $sinatra_dir . 'sinatra-wc' . $sinatra_suffix . '.js',
				array( 'jquery' ),
				SINATRA_THEME_VERSION,
				true
			);
		}

		/**
		 * Add or remove actions depending on enabled product catalog elements.
		 *
		 * @return void
		 */
		public function product_catalog_elements() {

			$elements = sinatra_option( 'product_catalog_elements' );

			$hook     = 'woocommerce_before_shop_loop_item_title';
			$priority = 20;

			if ( ! empty( $elements ) ) {
				foreach ( $elements as $element => $enabled ) {

					if ( 'title' === $element ) {

						if ( ! $enabled ) {
							remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
						} else {
							remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_link_close' );

							add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', $priority );
							add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 4 );
						}

						$hook     = 'woocommerce_after_shop_loop_item_title';
						$priority = 5;

					} elseif ( 'ratings' === $element ) {

						remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

						if ( $enabled ) {
							add_action( $hook, 'woocommerce_template_loop_rating', $priority );
							$priority++;
						}
					} elseif ( 'price' === $element ) {

						remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

						if ( $enabled ) {
							add_action( $hook, 'woocommerce_template_loop_price', $priority );
							$priority++;
						}
					} elseif ( 'category' === $element ) {

						if ( $enabled ) {
							add_action( $hook, array( $this, 'template_loop_category' ), $priority );
							$priority++;
						}
					}
				}
			}
		}

		/**
		 * Print product categories in loop template.
		 *
		 * @return void
		 */
		public function template_loop_category() {

			global $product;

			$product_cats = wp_get_post_terms( $product->get_id(), 'product_cat' );
			$cats         = array();

			if ( is_array( $product_cats ) && ! empty( $product_cats ) ) {
				foreach ( $product_cats as $product_cat ) {
					$cats[] = '<a class="si-loop-product__category" href="' . esc_url( get_term_link( $product_cat, 'product_cat' ) ) . '">' . esc_html( $product_cat->name ) . '</a>';
				}
			}

			echo '<span class="si-loop-product__category-wrap">' . wp_kses_post( implode( ', ', $cats ) ) . '</span>';
		}

		/**
		 * Add start wrapper to result count and catalog ordering.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function result_wrapper_start() {

			if ( ! woocommerce_products_will_display() ) {
				return;
			}

			echo '<div class="si-woo-before-shop clearfix">';
		}

		/**
		 * Add end wrapper to result count and catalog ordering.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function result_wrapper_end() {

			if ( ! woocommerce_products_will_display() ) {
				return;
			}

			echo '</div>';
		}

		/**
		 * Update cart count in Cart Widget via AJAX.
		 *
		 * @param  array $fragments Fragments to refresh via AJAX.
		 * @return array            Fragments to refresh via AJAX
		 * @since  1.0.0
		 */
		public function cart_widget_count_fragment( $fragments ) {

			$fragments['.si-header-widget__cart a.si-cart'] = sinatra_wc_cart_icon( false );

			return $fragments;
		}

		/**
		 * Update Cart Widget dropdown via AJAX.
		 *
		 * @param  array $fragments Fragments to refresh via AJAX.
		 * @return array            Fragments to refresh via AJAX
		 * @since  1.0.0
		 */
		public function cart_widget_dropdown_fragment( $fragments ) {

			$fragments['.si-header-widget__cart .dropdown-item'] = sinatra_wc_cart_dropdown( false );

			return $fragments;
		}

		/**
		 * Add start of WooCommerce content wrapper.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function content_wrapper_start() {
			?>
			<div class="si-container">

				<div id="primary" class="content-area">

					<?php do_action( 'sinatra_before_content' ); ?>

					<main id="content" class="site-content" role="main">
			<?php
		}

		/**
		 * Add end of WooCommerce content wrapper.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function content_wrapper_end() {
			?>
					</main><!-- #content .site-content -->

					<?php do_action( 'sinatra_after_content' ); ?>

				</div><!-- #primary .content-area -->

				<?php do_action( 'sinatra_woocommerce_sidebar' ); ?>

			</div><!-- END .si-container -->
			<?php
		}

		/**
		 * Add start of Single Product content wrapper.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function single_product_wrapper_start() {
			echo '<div class="si-wc-product-wrap">';
		}

		/**
		 * Add end of Single Product content wrapper.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function single_product_wrapper_end() {
			echo '</div><!-- END .si-wc-product-wrap -->';
		}

		/**
		 * Single product actions.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function product_actions() {

			if ( ! is_product() ) {
				return;
			}

			// Remove Sinatra page title on WooCommerce product pages.
			add_filter( 'sinatra_page_header_has_title', '__return_false' );

			// Disable Comments toggle.
			add_filter( 'sinatra_display_comments_toggle', '__return_false' );
		}

		/**
		 * Add support for Customizer cart widget.
		 *
		 * @since 1.0.0
		 * @param array $widgets Array of available customizer widgets.
		 * @return array
		 */
		public function add_customizer_cart_widget( $widgets ) {

			$widgets['cart'] = 'Sinatra_Customizer_Widget_Cart';

			return $widgets;
		}

		/**
		 * Add cart widget to Header widgets.
		 *
		 * @since 1.0.0
		 * @param array $widgets Array of available main header widgets.
		 * @return array
		 */
		public function add_cart_to_main_header_widgets( $widgets ) {

			$widgets['cart'] = array(
				'max_uses' => 1,
			);

			return $widgets;
		}

		/**
		 * Overwrite the items for the breadcrumb trail.
		 *
		 * @since 1.0.0
		 * @param array $items Array of items belonging to the current breadcrumb trail.
		 * @param array $args  Arguments used to build the breadcrumb trail.
		 * @return array
		 */
		public function breadcrumbs( $items, $args ) {

			if ( function_exists( 'is_shop' ) && is_shop() ) {
				$items[ count( $items ) - 1 ] = get_the_title( wc_get_page_id( 'shop' ) );
			}

			return $items;
		}

		/**
		 * Overwrite the items for the breadcrumb trail.
		 *
		 * @since 1.2.0
		 * @param string $label           Current archive label.
		 * @param string $post_type_name  Post Type name.
		 * @return array
		 */
		public function breadcrumb_post_type_archive_title( $label, $post_type_name ) {

			if ( 'product' === $post_type_name ) {
				return get_the_title( wc_get_page_id( 'shop' ) );
			}

			return $label;
		}

		/**
		 * Register WooCommerce sidebars.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_wc_sidebars() {

			// Register WooCommerce Sidebar.
			register_sidebar(
				apply_filters(
					'sinatra_woocommerce_sidebar_name',
					array(
						'name'          => esc_html__( 'WooCommerce Sidebar', 'sinatra' ),
						'id'            => 'sinatra-wc-sidebar',
						'description'   => __( 'Widgets in this area are displayed on WooCommerce pages except Product pages.', 'sinatra' ),
						'before_widget' => '<div id="%1$s" class="si-sidebar-widget si-widget widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h4 class="widget-title">',
						'after_title'   => '</h4>',
					)
				)
			);

			// Register Product Sidebar.
			register_sidebar(
				apply_filters(
					'sinatra_woocommerce_product_sidebar_name',
					array(
						'name'          => esc_html__( 'Product Sidebar', 'sinatra' ),
						'id'            => 'sinatra-wc-product-sidebar',
						'description'   => __( 'Widgets in this area are displayed on WooCommerce Product pages.', 'sinatra' ),
						'before_widget' => '<div id="%1$s" class="si-sidebar-widget si-widget widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h4 class="widget-title">',
						'after_title'   => '</h4>',
					)
				)
			);
		}

		/**
		 * Change sidebar name on WooCommerce pages.
		 *
		 * @since 1.0.0
		 * @param string $sidebar_name Sidebar name for woocmmerce pages.
		 * @return string
		 */
		public function set_sidebar( $sidebar_name ) {

			if ( is_product() ) {
				$sidebar_name = 'sinatra-wc-product-sidebar';
			} elseif ( is_woocommerce() || is_cart() || is_checkout() ) {
				$sidebar_name = 'sinatra-wc-sidebar';
			}

			return $sidebar_name;
		}

		/**
		 * Change default sidebar position on WooCommerce pages.
		 *
		 * @since 1.0.0
		 * @param string $position Sidebar position for woocmmerce pages.
		 * @return string
		 */
		public function set_default_sidebar_position( $position ) {

			if ( is_product() ) {
				$position = sinatra_option( 'wc_product_sidebar_position' );
			} elseif ( is_woocommerce() || is_cart() || is_checkout() ) {
				$position = sinatra_option( 'wc_sidebar_position' );
			}

			if ( is_product() || is_woocommerce() || is_cart() || is_checkout() ) {
				if ( 'default' === $position ) {
					return sinatra_option( 'sidebar_position' );
				} else {
					return $position;
				}
			}

			return $position;
		}

		/**
		 * Remove Sinatra page description on WooCommerce pages.
		 *
		 * @since 1.0.0
		 * @param string $description Page description.
		 * @return boolean|string
		 */
		public function shop_remove_page_description( $description ) {

			if ( is_woocommerce() ) {
				return false;
			}

			return $description;
		}

		/**
		 * Remove item from cart.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function remove_item_from_cart() {

			check_ajax_referer( 'sinatra-nonce' );

			if ( ! isset( $_POST['product_key'] ) ) {
				wp_send_json_error();
			}

			$product_key = sanitize_text_field( wp_unslash( $_POST['product_key'] ) );

			$cart         = WC()->instance()->cart;
			$cart_item_id = $cart->find_product_in_cart( $product_key );

			if ( $cart_item_id ) {
				$cart->set_quantity( $cart_item_id, 0 );
				wp_send_json_success();
			}

			wp_send_json_error();
		}

		/**
		 * Display an alternative image (from product gallery) when hovering product image on catalog pages.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function product_image_swap() {

			global $product;

			$hover_style = sinatra_option( 'shop_product_hover' );

			if ( 'image-swap' === $hover_style ) {

				$attachment_ids = $product->get_gallery_image_ids();

				if ( $attachment_ids ) {

					$image_size    = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					$attachment_id = reset( $attachment_ids );

					echo wp_kses_post( apply_filters( 'sinatra_woocommerce_product_image_swap', wp_get_attachment_image( $attachment_id, $image_size, false, array( 'class' => 'show-on-hover' ) ) ) );
				}
			}
		}

		/**
		 * Add start wrapper for loop product thumbnail.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function loop_product_thumb_wrap_start() {

			$class = 'si-product-thumb';

			if ( 'image-swap' === sinatra_option( 'shop_product_hover' ) ) {

				global $product;
				$attachment_ids = $product->get_gallery_image_ids();

				if ( $attachment_ids ) {
					$class .= ' swap-on-hover';
				}
			}

			echo '<div class="' . esc_attr( $class ) . '">';
		}

		/**
		 * Add end wrapper for loop product thumbnail.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function loop_product_thumb_wrap_end() {
			echo '</div><!-- END .si-product-thumb -->';
		}

		/**
		 * Add start wrapper for loop product details.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function loop_product_details_wrap_open() {
			echo '<div class="meta-wrap">';
		}

		/**
		 * Add end wrapper for loop product details.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function loop_product_details_wrap_end() {
			echo '</div>';
		}

		/**
		 * Additional classes for add to cart button on loop products.
		 *
		 * @since 1.0.0
		 * @param array $args Arguments for add to cart button in loop products.
		 * @return array
		 */
		public function loop_add_to_cart_args( $args ) {

			$args['class'] .= ' si-btn';

			return $args;
		}

		/**
		 * Loads Customizer widgets classes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_customizer_widget() {

			$path = SINATRA_THEME_PATH . '/inc/compatibility/woocommerce/class-sinatra-customizer-widget-cart.php';

			if ( file_exists( $path ) ) {
				require $path; // phpcs:ignore
			}
		}

		/**
		 * Review order heading.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function review_order_heading() {

			echo wp_kses_post( apply_filters( 'sinatra_review_order_heading', '<h3>' . __( 'Payment', 'sinatra' ) . '</h3>' ) );
		}

		/**
		 * Display a heading on Checkout / Shipping.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function checkout_shipping_heading() {

			if ( true !== WC()->cart->needs_shipping_address() ) {
				return;
			}

			echo wp_kses_post( apply_filters( 'sinatra_checkout_shipping_heading', '<h3>' . __( 'Shipping', 'sinatra' ) . '</h3>' ) );
		}

		/**
		 * Related products column count.
		 *
		 * @since 1.0.0
		 * @param array $args Arguments for related products on single product page.
		 * @return array
		 */
		public function single_product_related_products_args( $args ) {

			$columns = intval( sinatra_option( 'wc_related_columns' ) );
			$rows    = intval( sinatra_option( 'wc_related_rows' ) );

			$args['posts_per_page'] = $columns * $rows;
			$args['columns']        = $columns;

			return $args;
		}

		/**
		 * Cross-Sell Products.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function woocommerce_cross_sell_display() {

			// Check if cross-sells are enabled.
			if ( sinatra_option( 'wc_cross_sell_products' ) ) {

				$rows = intval( sinatra_option( 'wc_cross_sell_rows' ) );

				woocommerce_cross_sell_display( 2 * $rows, 2 );
			}
		}

		/**
		 * Upsell Products.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function woocommerce_upsell_display() {

			// Check if upsells are enabled.
			if ( sinatra_option( 'wc_upsell_products' ) ) {

				$columns = intval( sinatra_option( 'wc_upsells_columns' ) );
				$rows    = intval( sinatra_option( 'wc_upsells_rows' ) );

				woocommerce_upsell_display( $columns * $rows, $columns );
			}
		}

		/**
		 * Related Products.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function woocommerce_related_products() {

			// Check if related products are enabled.
			if ( sinatra_option( 'wc_related_products' ) ) {
				woocommerce_output_related_products();
			}
		}

		/**
		 * Add arrows to product slider on single product.
		 *
		 * @since 1.0.0
		 * @param array $options Array of options for product slider.
		 * @return array
		 */
		public function single_product_slider_options( $options ) {

			if ( ! sinatra_option( 'wc_product_slider_arrows' ) ) {
				return $options;
			}

			$options['directionNav'] = true;
			$options['prevText']     = sinatra_animated_arrow( 'left', false );
			$options['nextText']     = sinatra_animated_arrow( 'right', false );

			return $options;
		}

		/**
		 * Product gallery thumbnail columns.
		 *
		 * @since 1.0.0
		 * @param integer $columns Number of product thumnail columns on single product page.
		 * @return integer
		 */
		public function product_thumbnails_columns( $columns ) {
			return 5;
		}

		/**
		 * Related products heading on single product pages.
		 *
		 * @since  1.0.0
		 * @param  string $heading Related products heading.
		 * @return string
		 */
		public function related_products_heading( $heading ) {
			return __( 'Related Products', 'sinatra' );
		}

		/**
		 * Handle redirects to setup/welcome page after install and updates.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function admin_redirects() {

			$current_page = isset( $_GET['page'] ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Prevent WooCommerce automatic wizard redirect.
			if ( false !== strpos( $current_page, 'sinatra' ) ) {
				add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );
			}
		}

		/**
		 * Generates dynamic CSS code for woocommerce.
		 *
		 * @param  string $css Dynamic CSS code generated by the theme.
		 * @return string      Modified CSS code.
		 * @since  1.0.0
		 */
		public function dynamic_css( $css ) {

			// Accent Color.
			$accent_color = sinatra_option( 'accent_color' );

			$css .= '
				.si-header-widgets .si-cart .si-cart-count,
				.si-woo-steps .si-step.is-active > span:first-child,
				.woocommerce div.product form.cart .button,
				.site-main .woocommerce #respond input#submit, 
				.site-main .woocommerce a.button, 
				.site-main .woocommerce button.button, 
				.site-main .woocommerce input.button,
				.woocommerce ul.products li.product .onsale,
				.woocommerce span.onsale,
				.woocommerce-store-notice, 
				p.demo_store,
				.woocommerce ul.products li.product .button,
				.widget.woocommerce .wc-layered-nav-term:hover .count,
				.widget.woocommerce .product-categories li a:hover ~ .count,
				.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen a:before,
				.woocommerce .widget_rating_filter ul li.chosen a::before,
				.widget.woocommerce .wc-layered-nav-term.chosen .count,
				.widget.woocommerce .product-categories li.current-cat > .count,
				.woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
				.woocommerce .widget_price_filter .ui-slider .ui-slider-handle:after,
				.woocommerce .widget_layered_nav_filters ul li a:hover,
				.woocommerce div.product div.images .woocommerce-product-gallery__trigger:hover:before,
				.woocommerce #review_form #respond .form-submit input {
					background-color: ' . $accent_color . ';
				}

				.woocommerce #review_form #respond .form-submit input:hover,
				.woocommerce #review_form #respond .form-submit input:focus,
				.woocommerce div.product form.cart .button:hover,
				.woocommerce div.product form.cart .button:focus,
				.site-main .woocommerce #respond input#submit:hover, 
				.site-main .woocommerce #respond input#submit:focus, 
				.site-main .woocommerce a.button:hover, 
				.site-main .woocommerce a.button:focus, 
				.site-main .woocommerce button.button:hover, 
				.site-main .woocommerce button.button:focus, 
				.site-main .woocommerce input.button:hover,
				.site-main .woocommerce input.button:focus,
				.woocommerce ul.products li.product .button:hover,
				.woocommerce ul.products li.product .button:focus,
				.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
				.widget.woocommerce .wc-layered-nav-rating a:hover em,
				.widget.woocommerce .wc-layered-nav-rating.chosen a em {
					background-color: ' . sinatra_luminance( $accent_color, .15 ) . ';
				}

				.woocommerce #yith-wcwl-form table.shop_table .product-subtotal .amount,
				.woocommerce .woocommerce-cart-form table.shop_table .product-subtotal .amount,
				.woocommerce ul.products li.product .price,
				.woocommerce .woocommerce-checkout-review-order .order-total .woocommerce-Price-amount.amount,
				#main .woocommerce-MyAccount-navigation li.is-active,
				.woocommerce .star-rating span::before,
				.widget.woocommerce .wc-layered-nav-term:hover a,
				.widget.woocommerce .wc-layered-nav-term a:hover,
				.widget.woocommerce .product-categories li a:hover,
				.widget.woocommerce .product-categories li.current-cat > a,
				.woocommerce ins .amount,
				.woocommerce .widget_rating_filter ul li.chosen a::before,
				.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen,
				.woocommerce .widget_shopping_cart .total .amount,
				.woocommerce .widget_shopping_cart .total .tax_label,
				.woocommerce.widget_shopping_cart .total .amount,
				.woocommerce.widget_shopping_cart .total .tax_label,
				.woocommerce .widget_shopping_cart .cart_list li a.remove:hover:before, 
				.woocommerce.widget_shopping_cart .cart_list li a.remove:hover:before,
				.woocommerce div.product .woocommerce-tabs ul.tabs li.active > a,
				.woocommerce div.product p.price, 
				.woocommerce div.product span.price,
				.woocommerce div.product #reviews .comment-form-rating .stars a,
				.woocommerce div.product .woocommerce-pagination ul li span.current,
				.woocommerce div.product .woocommerce-pagination ul li a:hover,
				.wc-cart-widget-header .si-cart-subtotal span,
				.si-header-widget__cart:hover > a,
				.si-woo-steps .si-step.is-active,
				.cart_totals .order-total td {
					color: ' . $accent_color . ';
				}

				.wc-layered-nav-rating a:hover .star-rating span:before {
					color: ' . sinatra_luminance( $accent_color, .15 ) . ';
				}

				.widget.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item.chosen a:before,
				.woocommerce div.product div.images .flex-control-thumbs li img.flex-active,
				.woocommerce div.product .woocommerce-pagination ul li span.current {
					border-color: ' . $accent_color . ';
				}

				.woocommerce-cart table.cart td.actions .coupon .input-text:focus, 
				.woocommerce-checkout table.cart td.actions .coupon .input-text:focus,
				#add_payment_method table.cart td.actions .coupon .input-text:focus {
					border-bottom-color: ' . $accent_color . ';
				}
			';

			// Content text color.
			$content_text_color = sinatra_option( 'content_text_color' );

			$css .= '
				.si-cart-item .si-x,
				.woocommerce form.login .lost_password a,
				.woocommerce form.register .lost_password a,
				.woocommerce a.remove,
				#add_payment_method .cart-collaterals .cart_totals .woocommerce-shipping-destination, 
				.woocommerce-cart .cart-collaterals .cart_totals .woocommerce-shipping-destination, 
				.woocommerce-checkout .cart-collaterals .cart_totals .woocommerce-shipping-destination,
				.woocommerce ul.products li.product .si-loop-product__category-wrap a,
				.woocommerce ul.products li.product .si-loop-product__category-wrap,
				.woocommerce .woocommerce-checkout-review-order table.shop_table thead th,
				#add_payment_method #payment div.payment_box, 
				.woocommerce-cart #payment div.payment_box, 
				.woocommerce-checkout #payment div.payment_box,
				#add_payment_method #payment ul.payment_methods .about_paypal, 
				.woocommerce-cart #payment ul.payment_methods .about_paypal, 
				.woocommerce-checkout #payment ul.payment_methods .about_paypal,
				.woocommerce table dl,
				.woocommerce table .wc-item-meta,
				.widget.woocommerce .reviewer,
				.woocommerce.widget_shopping_cart .cart_list li a.remove:before,
				.woocommerce .widget_shopping_cart .cart_list li a.remove:before,
				.woocommerce .widget_shopping_cart .cart_list li .quantity, 
				.woocommerce.widget_shopping_cart .cart_list li .quantity,
				.woocommerce div.product .woocommerce-product-rating .woocommerce-review-link,
				.woocommerce div.product .woocommerce-tabs table.shop_attributes td,
				.woocommerce div.product .product_meta > span span:not(.si-woo-meta-title), 
				.woocommerce div.product .product_meta > span a,
				.woocommerce .star-rating::before,
				.woocommerce div.product #reviews #comments ol.commentlist li .comment-text p.meta,
				.ywar_review_count,
				.woocommerce .add_to_cart_inline del,
				.woocommerce div.product p.price del, 
				.woocommerce div.product span.price del,
				.woocommerce #yith-wcwl-form table.shop_table thead,
				.woocommerce .woocommerce-cart-form table.shop_table thead,
				.woocommerce .woocommerce-checkout-review-order table.shop_table thead,
				.woocommerce div.product .woocommerce-tabs ul.tabs li a {
					color: ' . sinatra_hex2rgba( $content_text_color, 0.73 ) . ';
				}

				.woocommerce-message,
				.woocommerce-error,
				.woocommerce-info,
				.woocommerce-message,
				.woocommerce div.product .woocommerce-tabs ul.tabs li:not(.active) a:hover {
					color: ' . $content_text_color . ';
				}

				.woocommerce div.product .woocommerce-product-gallery .flex-direction-nav svg path {
					fill: ' . $content_text_color . ' !important;
				}
			';

			// Background Color - generated from text color.
			$background_color = sinatra_get_background_color();

			$css .= '
				.woocommerce div.product .woocommerce-product-gallery .flex-direction-nav .flex-prev,
				.woocommerce div.product .woocommerce-product-gallery .flex-direction-nav .flex-next,
				.woocommerce .quantity .si-woo-minus,
				.woocommerce .quantity .si-woo-plus {
					background-color: ' . $background_color . ';
				}
			';

			$content_text_color_offset = sinatra_light_or_dark( $background_color, sinatra_luminance( $background_color, -0.045 ), sinatra_luminance( $background_color, 0.2 ) );

			$css .= '
				.woocommerce #yith-wcwl-form table.shop_table thead th,
				.woocommerce .woocommerce-cart-form table.shop_table thead th,
				.woocommerce .woocommerce-checkout-review-order table.shop_table thead th,
				.woocommerce .cart_totals table.shop_table .order-total th,
				.woocommerce .cart_totals table.shop_table .order-total td,
				.woocommerce div.product .woocommerce-tabs .wc-tab,
				#page .woocommerce-error,
				#page .woocommerce-info,
				#page .woocommerce-message,
				.woocommerce div.product .woocommerce-tabs ul.tabs:before,
				.woocommerce div.product .woocommerce-tabs ul.tabs:after {
					background-color: ' . $content_text_color_offset . ';
				}
			';

			// Border color.
			$css .= '
				.woocommerce #yith-wcwl-form table.shop_table th:first-child,
				.woocommerce #yith-wcwl-form table.shop_table td:first-child,
				.woocommerce .woocommerce-cart-form table.shop_table th:first-child,
				.woocommerce .woocommerce-cart-form table.shop_table td:first-child,
				.woocommerce .woocommerce-checkout-review-order table.shop_table th:first-child,
				.woocommerce .woocommerce-checkout-review-order table.shop_table td:first-child,
				.woocommerce #yith-wcwl-form table.shop_table td,
				.woocommerce .woocommerce-cart-form table.shop_table td,
				.woocommerce .woocommerce-checkout-review-order table.shop_table td,
				.woocommerce #yith-wcwl-form table.shop_table tr:nth-last-child(2) td,
				.woocommerce .woocommerce-cart-form table.shop_table tr:nth-last-child(2) td,
				.woocommerce .cart_totals table.shop_table,
				.woocommerce .cart_totals table.shop_table th,
				.woocommerce .cart_totals table.shop_table td {
					border-color: ' . $content_text_color_offset . ';
				}
			';

			// Content link hover color.
			$css .= '
				#add_payment_method #payment ul.payment_methods .about_paypal:hover,
				.si-woo-before-shop select.custom-select-loaded:hover ~ #si-orderby, 
				.woocommerce-cart #payment ul.payment_methods .about_paypal:hover, 
				.woocommerce-checkout #payment ul.payment_methods .about_paypal:hover,
				.woocommerce div.product .woocommerce-product-rating .woocommerce-review-link:hover,
				.woocommerce ul.products li.product .meta-wrap .woocommerce-loop-product__link:hover,
				.woocommerce ul.products li.product .si-loop-product__category-wrap a:hover {
					color: ' . sinatra_option( 'content_link_hover_color' ) . ';
				}
			';

			/**
			 * Header.
			 */

			// Background.
			$header_background = sinatra_option( 'header_background' );

			if ( 'color' === $header_background['background-type'] && $header_background['background-color'] ) {
				$css .= '
					.si-header-widget__cart .si-cart .si-cart-count { 
						border: 2px solid ' . $header_background['background-color'] . '; 
					}
				';
			}

			/**
			 * Typography.
			 */

			// Headings.
			$css .= sinatra_dynamic_styles()->get_typography_field_css( '.woocommerce div.product h1.product_title, .woocommerce #reviews #comments h2, .woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title', 'headings_font' );

			$css .= sinatra_dynamic_styles()->get_typography_field_css( '.woocommerce div.product h1.product_title', 'h2_font' );
			$css .= sinatra_dynamic_styles()->get_typography_field_css( '.woocommerce #reviews #comments h2', 'h3_font' );
			$css .= sinatra_dynamic_styles()->get_typography_field_css( '.woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title', 'h4_font' );

			return $css;
		}

		/**
		 * Return post ID.
		 *
		 * @param  int $post_id Post ID.
		 * @return int          Modified post ID.
		 */
		public function get_the_id( $post_id ) {

			if ( is_shop() ) {
				$post_id = wc_get_page_id( 'shop' );
			}

			return $post_id;
		}
	}

endif;

if ( ! function_exists( 'sinatra_woocommerce' ) ) :
	/**
	 * The function which returns the one Sinatra_Woocommerce instance.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	function sinatra_woocommerce() {
		return Sinatra_Woocommerce::instance();
	}
endif;

sinatra_woocommerce();
