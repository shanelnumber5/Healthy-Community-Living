<?php
/**
 * Plugin support: Elegro Crypto Payment (Add Crypto payments to WooCommerce)
 *
 * @package ThemeREX Addons
 * @since v1.70.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
    die( '-1' );
}

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_elegro_payment' ) ) {
    function trx_addons_exists_elegro_payment() {
        return class_exists( 'WC_Elegro_Payment' );
    }
}

// Add our ref to the link
if ( !function_exists( 'trx_addons_elegro_payment_add_ref' ) ) {
    add_filter( 'woocommerce_settings_api_form_fields_elegro', 'trx_addons_elegro_payment_add_ref' );
    function trx_addons_elegro_payment_add_ref( $fields ) {
        if ( ! empty( $fields['listen_url']['description'] ) ) {
            $fields['listen_url']['description'] = preg_replace( '/href="([^"]+)"/', 'href="$1?ref=246218d7-a23d-444d-83c5-a884ecfa4ebd"', $fields['listen_url']['description'] );
        }
        return $fields;
    }
}