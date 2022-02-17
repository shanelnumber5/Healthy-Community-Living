<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( !function_exists( 'academee_woocommerce_get_css' ) ) {
	add_filter( 'academee_filter_get_css', 'academee_woocommerce_get_css', 10, 4 );
	function academee_woocommerce_get_css($css, $colors, $fonts, $scheme='') {
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS

.woocommerce .checkout table.shop_table .product-name .variation,
.woocommerce .shop_table.order_details td.product-name .variation, .single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li a {
	{$fonts['p_font-family']}
}
.woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price,
.woocommerce ul.products li.product .post_header, .woocommerce-page ul.products li.product .post_header,
.woocommerce .shop_table th,
.woocommerce span.onsale,
.woocommerce div.product p.price, .woocommerce div.product span.price,
.woocommerce div.product .summary .stock,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce table.cart td.product-name a, .woocommerce-page table.cart td.product-name a, 
.woocommerce #content table.cart td.product-name a, .woocommerce-page #content table.cart td.product-name a,
.woocommerce .checkout table.shop_table .product-name,
.woocommerce .shop_table.order_details td.product-name,
.woocommerce .order_details li strong,
.woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-content .woocommerce-Address-title a, .woocommerce.widget_shopping_cart .total, .woocommerce .widget_shopping_cart .total, .woocommerce-page.widget_shopping_cart .total, .woocommerce-page .widget_shopping_cart .total, .woocommerce.widget_shopping_cart .quantity, .woocommerce .widget_shopping_cart .quantity, .woocommerce-page.widget_shopping_cart .quantity, .woocommerce-page .widget_shopping_cart .quantity, .widget_price_filter .price_label, .woocommerce.widget_recently_viewed_products ul.product_list_widget li, .woocommerce div.product form.cart .variations label {
	{$fonts['h5_font-family']}
}
#btn-buy,
.woocommerce ul.products li.product .button, .woocommerce div.product form.cart .button,
.woocommerce .woocommerce-message .button,
.woocommerce #review_form #respond p.form-submit input[type="submit"],
.woocommerce-page #review_form #respond p.form-submit input[type="submit"],
.woocommerce table.my_account_orders .order-actions .button,
.woocommerce .button, .woocommerce-page .button,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button
.woocommerce #respond input#submit,
.woocommerce input[type="button"], .woocommerce-page input[type="button"],
.woocommerce input[type="submit"], .woocommerce-page input[type="submit"] {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.woocommerce ul.products li.product .post_header .post_tags,
.woocommerce div.product .product_meta span > a, .woocommerce div.product .product_meta span > span,
.woocommerce div.product form.cart .reset_variations,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta time, .woocommerce-page #reviews #comments ol.commentlist li .comment-text p.meta time {
	{$fonts['info_font-family']}
}

CSS;
		
			
			$rad = academee_get_border_radius();
			$css['fonts'] .= <<<CSS



CSS;
		}


		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS

/* Page header */
.woocommerce .woocommerce-breadcrumb {
	color: {$colors['text']};
}
.woocommerce .woocommerce-breadcrumb a {
	color: {$colors['text_link']};
}
.woocommerce .woocommerce-breadcrumb a:hover {
	color: {$colors['text_hover']};
}
.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
.woocommerce .widget_price_filter .ui-slider .ui-slider-handle {
	background-color: {$colors['text_link']};
}

/* List and Single product */
.woocommerce .woocommerce-ordering select {
	border-color: {$colors['bd_color']};
}
.woocommerce span.onsale {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}



.woocommerce ul.products li.product .post_header a {
	color: {$colors['text_dark']};
}
.woocommerce ul.products li.product .post_header a:hover {
	color: {$colors['alter_link']};
}
.woocommerce ul.products li.product .post_header .post_tags,
.woocommerce ul.products li.product .post_header .post_tags a {
	color: {$colors['alter_link']};
}
.woocommerce ul.products li.product .post_header .post_tags a:hover {
	color: {$colors['alter_hover']};
}
.woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price,
.woocommerce ul.products li.product .price ins, .woocommerce-page ul.products li.product .price ins {
	color: {$colors['text_link']};
}
.woocommerce ul.products li.product .price del, .woocommerce-page ul.products li.product .price del {
	color: {$colors['alter_light']};
}
.woocommerce ul.products li.product .post_data .add_to_cart_button, .woocommerce-page ul.products li.product .post_data .add_to_cart_button{
    color: {$colors['text_dark']};
}
.woocommerce ul.products li.product .post_data .add_to_cart_button:hover, .woocommerce-page ul.products li.product .post_data .add_to_cart_button:hover{
    color: {$colors['text_link']};
}
.woocommerce div.product p.price, .woocommerce div.product span.price,
.woocommerce span.amount, .woocommerce-page span.amount {
	color: {$colors['text_link']};
}
.sidebar_inner .woocommerce div.product p.price, .sidebar_inner .woocommerce div.product span.price,
.sidebar_inner .woocommerce span.amount, .sidebar_inner .woocommerce-page span.amount {
	color: {$colors['alter_text']};
}
 .woocommerce .list_products .products span.amount{
    color: {$colors['text_link']};
 }
 .woocommerce.widget_shopping_cart .total .amount{
    color: {$colors['text_link']};
 }
.woocommerce table.shop_table td span.amount {
	color: {$colors['text_dark']};
}
.woocommerce table>tbody>tr:nth-child(2n)>td{
    background-color: {$colors['input_bg_color_04']};
}
aside.woocommerce del,
.woocommerce del, .woocommerce del > span.amount, 
.woocommerce-page del, .woocommerce-page del > span.amount {
	color: {$colors['text_light']} !important;
}
.woocommerce .price del:before {
	background-color: {$colors['text_light']};
}
.woocommerce div.product form.cart div.quantity span, .woocommerce-page div.product form.cart div.quantity span,
.woocommerce .shop_table.cart div.quantity span, .woocommerce-page .shop_table.cart div.quantity span {
	color: {$colors['text_dark']};
}
.woocommerce div.product form.cart div.quantity span:hover, .woocommerce-page div.product form.cart div.quantity span:hover,
.woocommerce .shop_table.cart div.quantity span:hover, .woocommerce-page .shop_table.cart div.quantity span:hover {
	color: {$colors['text_link']};
}
.woocommerce .product_meta span, .woocommerce .product_meta span span, .woocommerce .product_meta a{
    color: {$colors['text_dark']};
}

.woocommerce div.product .product_meta span > a,
.woocommerce div.product .product_meta span > span {
	color: {$colors['text_dark']};
}
.woocommerce div.product .product_meta a:hover {
	color: {$colors['text_link']};
}

.woocommerce div.product div.images .flex-viewport,
.woocommerce div.product div.images img {
	border-color: {$colors['bd_color']};
}
.woocommerce div.product div.images a:hover img {
	border-color: {$colors['text_link']};
}

.single-product div.product .trx-stretch-width .woocommerce-tabs {
	border-color: {$colors['bd_color']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li a {
	color: {$colors['text_dark']};
	background-color: {$colors['input_bg_color']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li.active a {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li:not(.active) a:hover {
	color: {$colors['extra_hover']};
	background-color: {$colors['extra_bg_hover']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs .panel {
	color: {$colors['text']};
}
.woocommerce table.shop_attributes tr:nth-child(2n+1) > * {
	background-color: {$colors['inverse_text']};
}
.woocommerce table.shop_attributes tr:nth-child(2n) > *,
.woocommerce table.shop_attributes tr.alt > * {
	background-color: {$colors['alter_bg_color_02']};
}
.woocommerce table.shop_attributes th {
	color: {$colors['text_dark']};
}


/* Related Products */
.single-product .related {
	border-color: {$colors['bd_color']};
}
.single-product ul.products li.product .post_data {
	color: {$colors['text_dark']};
}
.single-product ul.products li.product .post_data .price span.amount {
	color: {$colors['text_link']};
}
.single-product ul.products li.product .post_data .post_header .post_tags,
.single-product ul.products li.product .post_data .post_header .post_tags a{
	color: {$colors['extra_link']};
}

.single-product ul.products li.product .post_data a {
	color: {$colors['text_dark']};
}
.single-product ul.products li.product .post_data .post_header .post_tags a:hover,
.single-product ul.products li.product .post_data a:hover {
	color: {$colors['extra_hover']};
}
.single-product ul.products li.product .post_data .button {
	color: {$colors['text_dark']};

}
.single-product ul.products li.product .post_data .button:hover {
	color: {$colors['text_link']} !important;

}

/* Rating */
.star-rating span,
.star-rating:before {
	color: {$colors['text_link']};
}
#review_form #respond p.form-submit input[type="submit"] {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
#review_form #respond p.form-submit input[type="submit"]:hover,
#review_form #respond p.form-submit input[type="submit"]:focus {
	color: {$colors['bg_color']};
	background-color: {$colors['text_dark']};
}

/* Buttons */
.academee_shop_mode_buttons a {
	color: {$colors['text_dark']};
}
.academee_shop_mode_buttons a:hover {
	color: {$colors['text_link']};
}


/* Messages */
.woocommerce .woocommerce-message,
.woocommerce .woocommerce-info {

	border-top-color: {$colors['alter_dark']};
}
.woocommerce .woocommerce-error {

	border-top-color: {$colors['alter_link']};
}
.woocommerce .woocommerce-message:before,
.woocommerce .woocommerce-info:before {
	color: {$colors['alter_dark']};
}
.woocommerce .woocommerce-error:before {
	color: {$colors['alter_link']};
}
.woocommerce form .form-row.woocommerce-validated .select2-container > span > span, .woocommerce form .form-row.woocommerce-validated input.input-text, .woocommerce form .form-row.woocommerce-validated select{
    border-radius: 5em;
}

/* Cart */
.woocommerce table.shop_table td {
	border-color: {$colors['alter_bd_color']} !important;
}
.woocommerce table.shop_table th {
	border-color: {$colors['alter_bd_color_02']} !important;
}
.woocommerce table.shop_table tfoot th, .woocommerce-page table.shop_table tfoot th {
	color: {$colors['text_dark']};
	border-color: transparent !important;
	background-color: transparent;
}
.woocommerce .quantity input.qty, .woocommerce #content .quantity input.qty, .woocommerce-page .quantity input.qty, .woocommerce-page #content .quantity input.qty {
	color: {$colors['input_dark']};
}
.woocommerce .cart-collaterals .cart_totals table select,
.woocommerce-page .cart-collaterals .cart_totals table select {
	color: {$colors['input_text']};
	background-color: {$colors['input_bg_color']};
}
.woocommerce .cart-collaterals .cart_totals table select:focus, .woocommerce-page .cart-collaterals .cart_totals table select:focus {
	color: {$colors['input_dark']};
	background-color: {$colors['input_bg_hover']};
}
.woocommerce .cart-collaterals .shipping_calculator .shipping-calculator-button:after,
.woocommerce-page .cart-collaterals .shipping_calculator .shipping-calculator-button:after {
	color: {$colors['text_dark']};
}
.woocommerce table.shop_table .cart-subtotal .amount, .woocommerce-page table.shop_table .cart-subtotal .amount,
.woocommerce table.shop_table .shipping td, .woocommerce-page table.shop_table .shipping td {
	color: {$colors['text_dark']};
}
.woocommerce table.cart td+td a, .woocommerce #content table.cart td+td a, .woocommerce-page table.cart td+td a, .woocommerce-page #content table.cart td+td a,
.woocommerce table.cart td+td span, .woocommerce #content table.cart td+td span, .woocommerce-page table.cart td+td span, .woocommerce-page #content table.cart td+td span {
	color: {$colors['text_dark']};
}
.woocommerce table.cart td+td a:hover, .woocommerce #content table.cart td+td a:hover, .woocommerce-page table.cart td+td a:hover, .woocommerce-page #content table.cart td+td a:hover {
	color: {$colors['text_link']};
}
#add_payment_method table.cart td.actions .coupon .input-text, .woocommerce-cart table.cart td.actions .coupon .input-text, .woocommerce-checkout table.cart td.actions .coupon .input-text {
	border-color: {$colors['input_bd_color']};
}


/* Checkout */
#add_payment_method #payment ul.payment_methods, .woocommerce-cart #payment ul.payment_methods, .woocommerce-checkout #payment ul.payment_methods {
	border-color:{$colors['bd_color']};
}
#add_payment_method #payment div.payment_box, .woocommerce-cart #payment div.payment_box, .woocommerce-checkout #payment div.payment_box {
	color:{$colors['input_dark']};
	background-color:{$colors['input_bg_hover']};
}
#add_payment_method #payment div.payment_box:before, .woocommerce-cart #payment div.payment_box:before, .woocommerce-checkout #payment div.payment_box:before {
	border-color: transparent transparent {$colors['input_bg_hover']};
}
.woocommerce .order_details li strong, .woocommerce-page .order_details li strong {
	color: {$colors['text_dark']};
}
.woocommerce .order_details.woocommerce-thankyou-order-details {
	color:{$colors['alter_text']};
	background-color:{$colors['alter_bg_color']};
}
.woocommerce .order_details.woocommerce-thankyou-order-details strong {
	color:{$colors['alter_dark']};
}

/* My Account */
.woocommerce-account .woocommerce-MyAccount-navigation,
.woocommerce-MyAccount-navigation ul li,
.woocommerce-MyAccount-navigation li+li {
	border-color: {$colors['bd_color']};
}
.woocommerce-MyAccount-navigation li.is-active a {
	color: {$colors['text_link']};
}

/* Widgets */
.widget_product_search form:after {
	color: {$colors['input_light']};
}
.widget_product_search form:hover:after {
	color: {$colors['input_dark']};
}
.widget_shopping_cart .total {
	color: {$colors['text_link']};
	border-color: {$colors['bd_color']};
}
.widget_shopping_cart .total strong{
    color: {$colors['extra_light']};
}
.widget_layered_nav ul li.chosen a {
	color: {$colors['text_dark']};
}
.widget_price_filter .price_slider_wrapper .ui-widget-content { 
	background: {$colors['text_light']};
}
.widget_price_filter .price_label span, .widget_price_filter .price_label {
	color: {$colors['alter_link']};
}
.woocommerce.widget_recently_viewed_products ul.product_list_widget li{
    color: {$colors['alter_text']};
}

/* Third-party plugins
---------------------------------------------- */
.yith_magnifier_zoom_wrap .yith_magnifier_zoom_magnifier {
	border-color: {$colors['bd_color']};
}

.yith-woocompare-widget a.clear-all {
	color: {$colors['inverse_link']};
	background-color: {$colors['alter_link']};
}
.yith-woocompare-widget a.clear-all:hover {
	color: {$colors['inverse_hover']};
	background-color: {$colors['alter_hover']};
}

.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container-single .chosen-single {
	color: {$colors['input_text']};
	background: {$colors['input_bg_color']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container-single .chosen-single:hover {
	color: {$colors['input_dark']};
	background: {$colors['input_bg_hover']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-drop {
	color: {$colors['input_dark']};
	background: {$colors['input_bg_hover']};
	border-color: {$colors['input_bd_hover']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li {
	color: {$colors['input_dark']};
}
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li:hover,
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li.highlighted,
.widget.WOOCS_SELECTOR .woocommerce-currency-switcher-form .chosen-container .chosen-results li.result-selected {
	color: {$colors['alter_link']} !important;
}

.single-product div.product .trx-stretch-width .woocommerce-tabs:before, .single-product .related:before{
    background: {$colors['input_bg_hover']}; /* Old browsers */
    background: -moz-linear-gradient(left, {$colors['input_bg_hover']} 1%, {$colors['bg_color']} 95%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, right top, color-stop(1%,{$colors['input_bg_hover']}), color-stop(95%,{$colors['bg_color']})); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* Opera 11.10+ */
    background: -ms-linear-gradient(left, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* IE10+ */
    background: linear-gradient(to right, {$colors['input_bg_hover']} 1%,{$colors['bg_color']} 95%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$colors['input_bg_hover']}', endColorstr='{$colors['bg_color']}',GradientType=0 ); /* IE6-9 */
}
.woocommerce form .form-row input.input-text::-webkit-input-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text::-moz-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text:-ms-input-placeholder{color: {$colors['text_dark']};}
.woocommerce form .form-row input.input-text:-moz-placeholder{color: {$colors['text_dark']};}


.shop_mode_list .woocommerce_list,
.shop_mode_thumbs .woocommerce_thumbs{
    color: {$colors['text']};
}

CSS;
		}
		
		return $css;
	}
}
?>