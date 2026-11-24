<?php
/**
 * Plugin Name: Rovakivo Storefront
 * Description: The Rovakivo storefront design for every front-end page: shared header, footer, product cards, WooCommerce pages, and one GSAP + Lenis motion system. Deactivate to return to the Blocksy theme's own layout; no pages, products or settings are changed.
 * Version:     2.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Text Domain: bar7
 */

defined( 'ABSPATH' ) || exit;

const BAR7_VERSION = '2.0.0';
define( 'BAR7_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAR7_URL', plugin_dir_url( __FILE__ ) );

/** Mailing list the newsletter form adds subscribers to (MailPoet "Newsletter mailing list"). */
const BAR7_MAILPOET_LIST_ID = 3;

/**
 * Supplied photography for existing catalog products, matched by product ID (checked against
 * image-manifest.csv and each product's own photo). Products not listed keep their WooCommerce images.
 */
const BAR7_PHOTOS = array(
	66  => 'aturna',      // Olive Everyday Backpack (SKU 011)
	57  => 'fringilla',   // Brown Flap Backpack (SKU 013)
	64  => 'mollis',      // Charcoal Buckle Backpack (SKU 012)
	166 => 'scelerisque', // Forest Green Travel Backpack (SKU 004)
	157 => 'ornare',      // Three Backpack Selection (SKU 008)
	155 => 'libero',      // Green Zip Backpack (SKU 009)
	162 => 'donec',       // Black Everyday Backpack (SKU 006)
	159 => 'vestibulum',  // Black Travel Backpack (SKU 007)
);

require BAR7_DIR . 'inc/helpers.php';
require BAR7_DIR . 'inc/cards.php';
require BAR7_DIR . 'inc/routing.php';
require BAR7_DIR . 'inc/assets.php';
require BAR7_DIR . 'inc/woocommerce.php';
require BAR7_DIR . 'inc/newsletter.php';
require BAR7_DIR . 'inc/seo.php';
require BAR7_DIR . 'inc/consent.php';
require BAR7_DIR . 'inc/testimonials.php';
require BAR7_DIR . 'inc/menus.php';
require BAR7_DIR . 'inc/sections.php';
require BAR7_DIR . 'inc/blocks.php';
require BAR7_DIR . 'inc/editor-chrome.php';
require BAR7_DIR . 'inc/elementor.php';
require BAR7_DIR . 'inc/media-redirects.php';
require BAR7_DIR . 'inc/imagify.php';
