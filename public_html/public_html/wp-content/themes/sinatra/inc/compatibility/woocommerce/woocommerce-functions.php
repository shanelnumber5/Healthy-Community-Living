<?php
/**
 * Sinatra WooCommerce compatibility functions.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

if ( ! function_exists( 'sinatra_get_wc_version' ) ) :
	/**
	 * Get the version of the currently installed WooCommerce.
	 *
	 * @since 1.0.0
	 * @return woocommerce version number or null.
	 */
	function sinatra_get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}
endif;

if ( ! function_exists( 'sinatra_header_widget_cart' ) ) :
	/**
	 * Outputs the header cart widget.
	 *
	 * @since 1.0.0
	 * @param array $options Array of widget options.
	 */
	function sinatra_header_widget_cart( $options ) {

		// Cart widget.
		sinatra_wc_cart_icon();

		// Skip dropdown on checkout and cart.
		if ( is_checkout() || is_cart() ) {
			return;
		}

		// Cart dropdown contents.
		sinatra_wc_cart_dropdown();
	}
endif;

if ( ! function_exists( 'sinatra_wc_cart_icon' ) ) :
	/**
	 * Outputs the WooCommerce cart widget icon.
	 *
	 * @since 1.0.0
	 * @param boolean $echo Return or print.
	 */
	function sinatra_wc_cart_icon( $echo = true ) {

		ob_start();

		wc_get_template_part( 'cart/header-widget/icon' );

		$output = ob_get_clean();

		if ( true === $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
endif;

if ( ! function_exists( 'sinatra_wc_cart_dropdown' ) ) :
	/**
	 * Outputs the WooCommerce cart dropdown.
	 *
	 * @since 1.0.0
	 * @param bool $echo Print or return content.
	 */
	function sinatra_wc_cart_dropdown( $echo = true ) {

		ob_start();

		wc_get_template_part( 'cart/header-widget/dropdown' );

		$output = ob_get_clean();

		if ( true === $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
endif;

if ( ! function_exists( 'sinatra_wc_out_of_stock_badge' ) ) :
	/**
	 * Outputs out of stock (sold out) badge for product.
	 *
	 * @since 1.0.0
	 */
	function sinatra_wc_out_of_stock_badge() {

		global $product;

		if ( ! $product->is_in_stock() ) {
			esc_html( sprintf( apply_filters( 'sinatra_woocommerce_out_of_stock_badge', sprintf( '<span class="onsale sold-out">%s</span>', esc_html__( 'Sold Out', 'sinatra' ) ) ) ) );
		}
	}
endif;

if ( ! function_exists( 'sinatra_wc_add_percentage_to_sale_badge' ) ) :
	/**
	 * Outputs badge with percentage discount for product.
	 *
	 * @since 1.0.0
	 * @param string $html    Cart widget content.
	 * @param object $post    Post object.
	 * @param object $product Product object.
	 */
	function sinatra_wc_add_percentage_to_sale_badge( $html, $post, $product ) {

		$badge = sinatra_option( 'product_sale_badge' );

		if ( 'hide' === $badge ) {
			return '';
		}

		if ( ! $product->is_in_stock() && ! $product->backorders_allowed() ) {
			return '';
		}

		$text = '';

		if ( 'text' === $badge ) {

			$text = sinatra_option( 'product_sale_badge_text' );

		} elseif ( 'percentage' === $badge ) {

			if ( $product->is_type( 'variable' ) ) {

				$percentages = array();

				// Get all variation prices.
				$prices = $product->get_variation_prices();

				// Loop through variation prices.
				foreach ( $prices['price'] as $key => $price ) {

					// Only on sale variations.
					if ( $prices['regular_price'][ $key ] !== $price ) {

						// Prevent dividing by 0.
						if ( ! $prices['regular_price'][ $key ] ) {
							return $html;
						}

						// Calculate and set in the array the percentage for each variation on sale.
						$percentages[] = round( 100 - ( $prices['sale_price'][ $key ] / $prices['regular_price'][ $key ] * 100 ) );
					}
				}

				// We keep the highest value.
				$text = '-' . max( $percentages ) . '%';

			} else {

				$regular_price = (float) $product->get_regular_price();
				$sale_price    = (float) $product->get_sale_price();

				// Prevent dividing by 0.
				if ( ! $regular_price ) {
					return $html;
				}

				$text = '-' . round( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';
			}
		}

		return $text || is_customize_preview() ? '<span class="onsale">' . esc_html( $text ) . '</span>' : '';
	}
endif;

if ( ! function_exists( 'sinatra_wc_empty_cart_button' ) ) :
	/**
	 * Add empty cart - button to return to cart page.
	 *
	 * @since 1.0.0
	 */
	function sinatra_wc_empty_cart_button() {

		if ( ! wc_get_page_id( 'shop' ) ) {
			return;
		}
		?>
		<p class="return-to-shop si-woo-return">
			<a class="si-btn btn-reveal btn-left-icon" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound ?>" role="button">
				<span><?php esc_html_e( 'Return to Shop', 'sinatra' ); ?></span>
				<?php echo sinatra()->icons->get_svg( 'arrow-left', array( 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</p>
		<?php
	}
endif;

if ( ! function_exists( 'sinatra_wc_widget_shopping_cart_buttons' ) ) :
	/**
	 * Mini cart buttons for Shopping Cart Widget.
	 *
	 * @since 1.0.0
	 */
	function sinatra_wc_widget_shopping_cart_buttons() {
		wc_get_template_part( 'cart/header-widget/buttons' );
	}
endif;

if ( ! function_exists( 'sinatra_wc_layered_count_filter' ) ) :
	/**
	 * Removes parentheses from WooCommerce layered filter count.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output The count output.
	 * @param array  $count The count.
	 * @param array  $term The term.
	 * @return string
	 */
	function sinatra_wc_layered_count_filter( $output, $count, $term ) {

		$output = str_replace( '(', ' ', $output );
		$output = str_replace( ')', ' ', $output );

		return $output;
	}
endif;

if ( ! function_exists( 'sinatra_wc_rating_count_filter' ) ) :
	/**
	 * Removes parentheses from WooCommerce rating filter count.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output The count output.
	 * @param array  $count  The count.
	 * @param array  $rating The term.
	 * @return  string
	 */
	function sinatra_wc_rating_count_filter( $output, $count, $rating ) {

		$output = str_replace( '(', '<em>', $output );
		$output = str_replace( ')', '</em>', $output );

		return $output;
	}
endif;

if ( ! function_exists( 'sinatra_wc_cat_count_filter' ) ) :
	/**
	 * Filters product category subtitle (count).
	 *
	 * @since 1.0.0
	 *
	 * @param string $output   The count output.
	 * @param array  $category The category.
	 * @return  string
	 */
	function sinatra_wc_cat_count_filter( $output, $category ) {

		$count = $category->count;

		/* translators: %s is category count */
		$text = sprintf( _n( '%s product', '%s products', $count, 'sinatra' ), $count );

		return '<span class="count">' . esc_html( $text ) . '</span>';
	}
endif;
