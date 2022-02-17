<?php
/*
Plugin Name: elegro Crypto Payment
Description: Extends WooCommerce with an elegro Crypto Payment.
Version: 1.0.0
Author: Niko Technologies
Author URI: https://niko-technologies.eu/


Copyright 2018  Niko Technologies

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

// define elegro auth header
define('AUTHHEADER', 'Elegro-Authorization');

add_action('plugins_loaded', 'init_Elegro_Payment', 20);
function init_Elegro_Payment() {

    if(!class_exists('WC_Payment_Gateway')) return;

    class WC_Elegro_Payment extends WC_Payment_Gateway {

        public function __construct(){

            /**
             * add bandgee.js and elegro-style.css
             */
            wp_register_style('elegro-style', plugins_url( 'elegro-style.css', __FILE__ ), array(), null);
            wp_enqueue_style('elegro-style');
            wp_enqueue_script('bandge', 'https://widget.acceptance.elegro.eu/checkout/widget.js', array('jquery'), null);
            wp_enqueue_script('elegro-script', plugins_url( 'elegro-script.js', __FILE__ ), array('jquery'), null);
            wp_enqueue_script('elegro-ga', plugins_url( 'elegro-ga.js', __FILE__ ), array('jquery'), null);

            $this->id                 = 'elegro';
            $this->has_fields         = false;
            $this->method_title       = 'elegro Crypto Payment';
            $this->method_description = 'elegro Crypto Payment';
            $this->icon               = apply_filters('woocommerce_elegro_icon', 'https://elegro-public.s3.eu-central-1.amazonaws.com/elegro_email_logo.png');
            $this->init_form_fields();
            $this->init_settings();

            // Load settings
            $this->enabled         = $this->get_option( 'enabled' );
            $this->title           = $this->get_option( 'title' );
            $this->description     = $this->get_option( 'description' );
            $this->public_api_key  = $this->get_option( 'public_api_key' );
            $this->private_api_key = $this->get_option( 'private_api_key' );

            // Actions
            add_action('woocommerce_receipt_'. $this->id, array( $this, 'receipt_page' ) );
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_api_wc_elegro_payment', array($this, 'check_ipn_response'));
        }

        public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'On/Off',
                    'type'        => 'checkbox',
                    'label'       => 'On/Off plugin',
                    'default'     => 'yes'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'The title that appears on the checkout page',
                    'default'     => 'elegro Crypto Payment',
                    'desc_tip'    => true,
                ),
                'description'     => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'The description that appears during the payment method selection process',
                    'default'     => 'Pay through the elegro Crypto Payment',
                ),
                'listen_url'      => array(
                    'title'       => 'Response server URL',
                    'type'        => 'text',
                    'description' => 'Copy this url to "Processing Listen URL" field on <a target="_blank" href="https://dashboard.acceptance.elegro.eu">dashboard.acceptance.elegro.eu</a>',
                    'default'     => get_site_url() . '/wc-api/wc_elegro_payment/'
                ),
                'public_api_key'  => array(
                    'title'       => 'Public API Key',
                    'type'        => 'text',
                    'description' => 'Public API Key in elegro system.',
                    'default'     => '',
                ),
                'private_api_key' => array(
                    'title'       => 'Secret API Key',
                    'type'        => 'text',
                    'description' => 'Secret API Key in elegro system.',
                    'default'     => '',
                )
            );

            return true;
        }

        function process_payment($order_id) {
            $order = new WC_Order($order_id);
            return array(
                'result'    => 'success',
                'redirect'  => add_query_arg('order', $order_id, add_query_arg('key', $order->get_order_key(), get_permalink(wc_get_page_id('pay'))))
            );
        }

        public function receipt_page($order) {
            echo '<p>Thank you for your order, please click the button below to pay.</p>';
            echo $this->generate_form($order);
        }

        public function generate_form($order_id) {
            $order = new WC_Order( $order_id );
            global $woocommerce;

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', 'Awaiting payment response');

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Prepare payment request`s data
            $args = array(
                'amount'           => $order->get_total(),
                'currency'         => get_woocommerce_currency(),
                'public_api_key'   => $this->public_api_key,
                'order'            => $order_id,
                'pay_way'          => 'elegro Crypto Payment'
            );

            $orderReceivedPage = get_option("woocommerce_checkout_order_received_endpoint");

            return '<div id="modal">' .
                '</div>' .
                '<a href="#" class="btn btn-md btn-primary btn-buy" id="btn-buy">Buy</a>' .
                '<script>' .
                    'jQuery(document).ready(function() {' .
                        'var btn = document.getElementById("btn-buy");' .
                        'var modal = document.getElementById("modal");' .
                        'btn.onclick = function() {' .
                            'modal.style.position = "fixed";' .
                            'modal.style.background = "#0000007a";' .
                            'modal.style.display = "block";' .
                        '};' .
                        'window.onclick = function(event) {' .
                            'if (event.target == modal) {' .
                                'modal.style.display = "none";' .
                            '}' .
                        '};' .
                        'document.querySelector(".btn-buy").addEventListener("click", buttonClick);' .
                        'function buttonClick() {' .
                            'var elegro = ElegroWidget('.
                                '"' . $args["public_api_key"] . '",'.
                                '"#modal",'.
                                '{' .
                                    'incomingAmount:' . $args["amount"] . ',' .
                                    'incomingCurrencyCode:"' . $args["currency"] . '",' .
                                    'orderId:"' . $args["order"] . '"' .
                                '}' . 
                            ');' .
                            'elegro.addListener("close", function () {' .
                                'modal.style.display = "none";' .
                                'var locationParts = location.href.split("?");' . 
                                'location.href = locationParts[0] + "' . $orderReceivedPage . '?" + locationParts[1];' .
                            '});' .
                            'elegro.addListener("failed", function () {' .
                                '(document.querySelector(".info") || {}).innerHTML = "Please try again in order to finalize your order!";' .
                            '});' .
                        '}' .
                    '});' .
                '</script>';
        }


        /**
         * When we have a payment`s response
         */
        function check_ipn_response(){

            $requestHeaders = getallheaders();

            if (!isset($requestHeaders[AUTHHEADER])) {
                wp_die( 'Access denied!');
            }

            // Get the Private api key from db
            $private_api_key_client = $this->get_option('private_api_key');

            if ($private_api_key_client !== $requestHeaders[AUTHHEADER]) {
                wp_die( 'Access denied!');
            }

            // Get orderId and order status
            $request = json_decode(file_get_contents('php://input'), true);
            $response_order_id = $request['orderId'] ? (int)$request['orderId'] : '';
            $response_order_status = $request['status'] ? ($request['status'] === 'success' ? 'processing' : 'cancelled') : '';
            $response_order_amount = $request['amount'] ? $request['amount'] : '';

            if ($response_order_status !== '' && $response_order_amount !== '') {
                $order = new WC_Order($response_order_id);

                if (floatval($order->get_total()) === $response_order_amount) {
                    $order->update_status($response_order_status);
                    $order->add_order_note( 'Order status: ' . $response_order_status );

                    $response = array('status' => $response_order_status === 'processing' ? 'success' : 'failed');
                } else {
                    $order->update_status('failed');
                    $order->add_order_note( 'Order status: failed. Insufficient funds.' );

                    $response = array('status' => 'failed', 'reason' => 'insufficient_funds');
                }
                die(json_encode($response));
            } else {
                wp_die('IPN request failed!');
            }
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_WC_Elegro_Payment_Gateway' );
function add_WC_Elegro_Payment_Gateway( $methods ){
    $methods[] = 'WC_Elegro_Payment';
    return $methods;
}
?>
