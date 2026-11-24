<?php
/**
 * Styles and scripts. One font request, one stylesheet, one copy of GSAP / ScrollTrigger / Lenis.
 */

defined( 'ABSPATH' ) || exit;

function bar7_asset_ver( string $rel ): string {
	$file = BAR7_DIR . $rel;
	return BAR7_VERSION . '.' . ( file_exists( $file ) ? (string) filemtime( $file ) : '0' );
}

/** Whether smooth scrolling suits this page. Forms, payments and account pages keep native scrolling. */
function bar7_smooth_scroll_allowed(): bool {
	return ! ( is_cart() || is_checkout() || is_account_page() );
}

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( ! bar7_active() ) {
			return;
		}

		// DM Sans and Manrope, self-hosted (no request to Google; see assets/fonts/fonts.css).
		wp_enqueue_style( 'bar7-fonts', BAR7_URL . 'assets/fonts/fonts.css', array(), bar7_asset_ver( 'assets/fonts/fonts.css' ) );
		wp_enqueue_style( 'bar7-site', BAR7_URL . 'assets/css/site.css', array( 'bar7-fonts' ), bar7_asset_ver( 'assets/css/site.css' ) );

		// Motion: GSAP core, ScrollTrigger and Lenis, the same versions as the reference project.
		wp_register_script( 'bar7-gsap', BAR7_URL . 'assets/vendor/gsap.min.js', array(), '3.15.1', array( 'in_footer' => true ) );
		wp_register_script( 'bar7-scrolltrigger', BAR7_URL . 'assets/vendor/ScrollTrigger.min.js', array( 'bar7-gsap' ), '3.15.1', array( 'in_footer' => true ) );
		wp_register_script( 'bar7-lenis', BAR7_URL . 'assets/vendor/lenis.min.js', array(), '1.3.27', array( 'in_footer' => true ) );
		wp_enqueue_script( 'bar7-motion', BAR7_URL . 'assets/js/motion.js', array( 'bar7-gsap', 'bar7-scrolltrigger', 'bar7-lenis' ), bar7_asset_ver( 'assets/js/motion.js' ), array( 'in_footer' => true ) );
		wp_localize_script(
			'bar7-motion',
			'bar7Motion',
			array( 'smoothScroll' => bar7_smooth_scroll_allowed() )
		);

		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
		wp_enqueue_script( 'woocommerce' );
		wp_enqueue_script( 'bar7-site', BAR7_URL . 'assets/js/site.js', array( 'jquery', 'bar7-motion' ), bar7_asset_ver( 'assets/js/site.js' ), array( 'in_footer' => true ) );
		wp_localize_script(
			'bar7-site',
			'bar7Site',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'storeApi'      => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
				'storeNonce'    => wp_create_nonce( 'wc_store_api' ),
				'openCartOnAdd' => ! ( is_cart() || is_checkout() ),
				'i18n'          => array(
					'added'      => __( '%s was added to your cart.', 'bar7' ),
					'addedShort' => __( 'Added to cart', 'bar7' ),
					'addToCart'  => __( 'Add to cart', 'bar7' ),
					'updating'   => __( 'Updating your cart…', 'bar7' ),
					'updated'    => __( 'Cart updated.', 'bar7' ),
					'removed'    => __( 'Item removed from your cart.', 'bar7' ),
					'cartError'  => __( 'We could not update your cart. Please try again.', 'bar7' ),
					'shown'      => __( '%d backpacks shown', 'bar7' ),
					'shownOne'   => __( '1 backpack shown', 'bar7' ),
					'invalid'    => __( 'Enter a valid email address, like name@example.com.', 'bar7' ),
					'subscribing'=> __( 'Subscribing…', 'bar7' ),
					'offline'    => __( 'We could not reach the server. Check your connection and try again.', 'bar7' ),
					'messageSent' => __( 'Thank you. Your message has been sent, and we’ll reply by email.', 'bar7' ),
				),
			)
		);
	},
	20
);

/**
 * The theme and page builder style the old layout and would restyle this one.
 * Their assets are left out wherever the storefront design renders the page.
 */
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( ! bar7_active() ) {
			return;
		}
		$skip = array( '/themes/blocksy/', '/plugins/blocksy-companion/' );
		// Pages saved with Elementor need Elementor's own styles and scripts (inc/elementor.php); no other page does.
		if ( ! ( function_exists( 'bar7_elementor_assets_allowed' ) && bar7_elementor_assets_allowed() ) ) {
			array_push( $skip, '/plugins/elementor/', '/uploads/elementor/' );
		}
		foreach ( array( wp_styles(), wp_scripts() ) as $deps ) {
			foreach ( (array) $deps->queue as $handle ) {
				$src = isset( $deps->registered[ $handle ] ) ? (string) $deps->registered[ $handle ]->src : '';
				foreach ( $skip as $needle ) {
					if ( '' !== $src && false !== strpos( $src, $needle ) ) {
						$deps instanceof WP_Styles ? wp_dequeue_style( $handle ) : wp_dequeue_script( $handle );
						break;
					}
				}
			}
		}
		// The product page uses its own gallery and review form: WooCommerce's zoom and lightbox
		// (single-product.js checks they exist before using them) and threaded-comment replies are unused.
		if ( is_product() ) {
			foreach ( array( 'wc-zoom', 'zoom', 'wc-photoswipe', 'photoswipe', 'wc-photoswipe-ui-default', 'photoswipe-ui-default', 'comment-reply' ) as $handle ) {
				wp_dequeue_script( $handle );
			}
			wp_dequeue_style( 'photoswipe' );
			wp_dequeue_style( 'photoswipe-default-skin' );
		}
	},
	9999
);

/** Fonts the theme and page builder add late (in the footer) are not used either. */
add_filter(
	'style_loader_tag',
	static function ( string $tag, string $handle ): string {
		if ( ! bar7_active() ) {
			return $tag;
		}
		// Elementor's Google Fonts never load (the site serves its own fonts); its other styles only on Elementor pages.
		$elementor_ok = function_exists( 'bar7_elementor_assets_allowed' ) && bar7_elementor_assets_allowed();
		if ( preg_match( '/^(blocksy|ct-)/', $handle ) || str_starts_with( $handle, 'elementor-gf-' ) || ( ! $elementor_ok && str_starts_with( $handle, 'elementor' ) ) ) {
			return '';
		}
		return $tag;
	},
	10,
	2
);

/**
 * WooCommerce's own base stylesheets (layout, small screens, notices, star ratings, select fields).
 * The theme switches them off; the storefront styles build on them.
 */
add_filter(
	'woocommerce_enqueue_styles',
	static function ( $styles ) {
		if ( ! bar7_active() || 'home' === bar7_view() ) {
			return $styles;
		}
		$base = WC()->plugin_url() . '/assets/css/';
		return array(
			'woocommerce-layout'      => array( 'src' => $base . 'woocommerce-layout.css', 'deps' => '', 'version' => WC()->version, 'media' => 'all', 'has_rtl' => true ),
			'woocommerce-smallscreen' => array( 'src' => $base . 'woocommerce-smallscreen.css', 'deps' => 'woocommerce-layout', 'version' => WC()->version, 'media' => 'only screen and (max-width: 768px)', 'has_rtl' => true ),
			'woocommerce-general'     => array( 'src' => $base . 'woocommerce.css', 'deps' => '', 'version' => WC()->version, 'media' => 'all', 'has_rtl' => true ),
		);
	},
	999
);

/**
 * Before first paint: mark that motion may run, so reveal targets can start hidden.
 * If the motion script has not run by DOMContentLoaded (footer scripts have executed by then), the class is removed and everything shows.
 */
add_action(
	'wp_head',
	static function (): void {
		if ( ! bar7_active() ) {
			return;
		}
		?>
<script id="bar7-motion-flag">(function(d,w){var q=w.matchMedia&&w.matchMedia('(prefers-reduced-motion: reduce)').matches;if(q&&!/[?&]motion\b/.test(w.location.search)){return;}d.documentElement.classList.add('bar7-motion');d.addEventListener('DOMContentLoaded',function(){if(!w.bar7MotionReady){d.documentElement.classList.remove('bar7-motion');}});})(document,window);</script>
		<?php
		foreach ( array( 'dmsans-latin', 'manrope-latin' ) as $bar7_font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( BAR7_URL . 'assets/fonts/' . $bar7_font . '.woff2' ) );
		}
		$bar7_hero = 'home' === bar7_view() ? bar7_front_hero_image_id() : 0;
		if ( $bar7_hero && wp_get_attachment_image_src( $bar7_hero, 'full' ) ) {
			// The hero photo chosen in the editor (same srcset and sizes as the <img>, so it is fetched once).
			printf(
				'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_url( wp_get_attachment_image_src( $bar7_hero, 'full' )[0] ),
				esc_attr( (string) wp_get_attachment_image_srcset( $bar7_hero, 'full' ) )
			);
		} elseif ( 'home' === bar7_view() ) {
			printf(
				'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s 850w, %1$s 1700w" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_url( BAR7_URL . 'assets/img/hero.webp' ),
				esc_url( BAR7_URL . 'assets/img/hero-850w.webp' )
			);
		}
	},
	1
);

/** Fonts are self-hosted, so drop any DNS hint to Google Fonts the theme still adds. */
add_filter(
	'wp_resource_hints',
	static function ( array $urls ): array {
		if ( ! bar7_active() ) {
			return $urls;
		}
		return array_values(
			array_filter(
				$urls,
				static function ( $url ): bool {
					$href = is_array( $url ) ? (string) ( $url['href'] ?? '' ) : (string) $url;
					return false === strpos( $href, 'fonts.googleapis.com' ) && false === strpos( $href, 'fonts.gstatic.com' );
				}
			)
		);
	}
);
