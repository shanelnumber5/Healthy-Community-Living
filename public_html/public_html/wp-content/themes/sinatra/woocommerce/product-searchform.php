<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * Sinatra changes: added search icon inside button.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="search-form woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div>
		<label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'sinatra' ); ?></label>
		<input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'sinatra' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
		<button type="submit" class="search-submit" aria-label="<?php echo esc_attr_x( 'Search', 'submit button', 'sinatra' ); ?>" value="<?php echo esc_attr_x( 'Search', 'submit button', 'sinatra' ); ?>">
			<?php echo sinatra()->icons->get_svg( 'search', array( 'aria-hidden' => 'true' ) ); ?>
			</button>
		<input type="hidden" name="post_type" value="product" />
	</div>
</form>
