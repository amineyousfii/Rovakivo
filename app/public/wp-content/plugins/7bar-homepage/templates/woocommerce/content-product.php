<?php
/**
 * Product in WooCommerce lists (shortcodes, widgets): the shared storefront card.
 *
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

bar7_product_card( $product, true, 'li' );
