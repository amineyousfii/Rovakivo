<?php
/**
 * Titles, meta descriptions, canonicals, robots, structured data, sitemap clean-up and social-sharing
 * tags. The site has no SEO plugin; if one is activated later, the overlapping parts switch off.
 *
 * Per-item values live in post meta `_bar7_seo_title` / `_bar7_meta_description` (pages and products)
 * and term meta `_bar7_seo_title` / `_bar7_meta_description` (categories).
 */

defined( 'ABSPATH' ) || exit;

function bar7_seo_enabled(): bool {
	return bar7_active() && ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'AIOSEO_VERSION' ) && ! defined( 'SEOPRESS_VERSION' );
}

/** Listing views narrowed by a filter or sort: not indexed, so they never compete with the clean listing. */
function bar7_is_filtered_listing(): bool {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return false;
	}
	foreach ( array( 'on_sale', 'min_price', 'max_price', 'orderby', 'filter_', 'query_type_' ) as $key ) {
		foreach ( array_keys( $_GET ) as $param ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( 0 === strpos( (string) $param, $key ) ) {
				return true;
			}
		}
	}
	return false;
}

/** Plain-text description for the current request. */
function bar7_seo_description(): string {
	$text = '';
	if ( is_front_page() ) {
		$text = (string) get_option( 'bar7_home_meta_description', '' );
	} elseif ( is_singular() ) {
		$id   = get_queried_object_id();
		$text = (string) get_post_meta( $id, '_bar7_meta_description', true );
		if ( '' === $text && 'product' === get_post_type( $id ) ) {
			$product = wc_get_product( $id );
			$text    = $product ? $product->get_short_description() : '';
		}
		if ( '' === $text ) {
			$text = (string) get_post_field( 'post_excerpt', $id );
		}
	} elseif ( is_product_taxonomy() ) {
		$term = get_queried_object();
		$text = (string) get_term_meta( $term->term_id, '_bar7_meta_description', true );
		if ( '' === $text ) {
			$text = (string) term_description( $term );
		}
	} elseif ( is_shop() ) {
		// The Sale view uses Pages → Sale's description when it has one.
		$sale = ! empty( $_GET['on_sale'] ) && ! is_search() && bar7_sale_page_id() ? (string) get_post_meta( bar7_sale_page_id(), '_bar7_meta_description', true ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$text = $sale ? $sale : (string) get_post_meta( wc_get_page_id( 'shop' ), '_bar7_meta_description', true );
	}
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
	if ( '' === $text ) {
		$text = (string) get_bloginfo( 'description' );
	}
	return wp_html_excerpt( $text, 160, '…' );
}

/** Image for social sharing: the product's own photo (JPEG), otherwise the branded share image. */
function bar7_seo_image(): array {
	if ( is_singular( 'product' ) ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product && $product->get_image_id() ) {
			$id  = (int) $product->get_image_id();
			$src = wp_get_attachment_image_src( $id, 'large' );
			if ( $src ) {
				$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
				return array( $src[0], (int) $src[1], (int) $src[2], $alt ? $alt : $product->get_name() );
			}
		}
	}
	return array( BAR7_URL . 'assets/img/share-default.jpg', 1200, 630, __( 'Rovakivo: bags and backpacks for every day, travel and time outdoors.', 'bar7' ) );
}

/** Canonical URL for the current request, or '' where none should be printed. */
function bar7_canonical(): string {
	if ( is_404() || is_search() || bar7_is_filtered_listing() ) {
		return '';
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( is_shop() ) {
		$base = wc_get_page_permalink( 'shop' );
	} elseif ( is_product_taxonomy() ) {
		$base = get_term_link( get_queried_object() );
		if ( is_wp_error( $base ) ) {
			return '';
		}
	} else {
		return ''; // Singular pages: WordPress prints its own canonical.
	}
	return $paged > 1 ? trailingslashit( $base ) . user_trailingslashit( 'page/' . $paged, 'paged' ) : $base;
}

/** Document titles. */
add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		if ( ! bar7_seo_enabled() ) {
			return $title;
		}
		$brand = ' | ' . get_bloginfo( 'name' );
		if ( is_front_page() ) {
			$custom = (string) get_option( 'bar7_home_seo_title', '' );
			return $custom ? $custom : $title;
		}
		if ( ! empty( $_GET['on_sale'] ) && ( is_shop() || is_product_taxonomy() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( is_product_taxonomy() ) {
				return sprintf( __( '%s on Sale', 'bar7' ), single_term_title( '', false ) ) . $brand;
			}
			$custom = bar7_sale_page_id() ? (string) get_post_meta( bar7_sale_page_id(), '_bar7_seo_title', true ) : '';
			return $custom ? $custom : __( 'Sale', 'bar7' ) . $brand;
		}
		if ( is_product_taxonomy() ) {
			$custom = (string) get_term_meta( get_queried_object_id(), '_bar7_seo_title', true );
			if ( $custom ) {
				$paged = max( 1, (int) get_query_var( 'paged' ) );
				return $paged > 1 ? str_replace( $brand, '', $custom ) . ' – ' . sprintf( __( 'Page %d', 'bar7' ), $paged ) . $brand : $custom;
			}
		}
		if ( is_account_page() ) {
			if ( ! is_user_logged_in() ) {
				return ( isset( $GLOBALS['wp']->query_vars['lost-password'] ) ? __( 'Reset Your Password', 'bar7' ) : __( 'Sign In', 'bar7' ) ) . $brand;
			}
			$endpoint = WC()->query->get_current_endpoint();
			if ( $endpoint ) {
				return wp_strip_all_tags( WC()->query->get_endpoint_title( $endpoint ) ) . $brand;
			}
		}
		if ( is_checkout() && ! empty( $GLOBALS['wp']->query_vars['order-received'] ) ) {
			return __( 'Order Received', 'bar7' ) . $brand;
		}
		$id = 0;
		if ( is_singular() ) {
			$id = get_queried_object_id();
		} elseif ( is_shop() && ! is_search() ) {
			$id = wc_get_page_id( 'shop' );
		}
		if ( $id ) {
			$custom = (string) get_post_meta( $id, '_bar7_seo_title', true );
			if ( $custom ) {
				$paged = max( 1, (int) get_query_var( 'paged' ) );
				return ( $paged > 1 && is_shop() ) ? str_replace( $brand, '', $custom ) . ' – ' . sprintf( __( 'Page %d', 'bar7' ), $paged ) . $brand : $custom;
			}
		}
		return $title;
	},
	20
);

add_filter(
	'document_title_separator',
	static function ( $sep ) {
		return bar7_seo_enabled() ? '|' : $sep;
	}
);

/** Robots: filtered and sorted listings are not indexed (their links are still followed). */
add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( bar7_seo_enabled() && bar7_is_filtered_listing() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

add_action(
	'wp_head',
	static function (): void {
		if ( ! bar7_seo_enabled() || is_404() ) {
			return;
		}
		$desc      = bar7_seo_description();
		$title     = wp_get_document_title();
		$canonical = bar7_canonical();
		$url       = $canonical ? $canonical : ( is_singular() ? get_permalink() : home_url( add_query_arg( array() ) ) );
		[ $img, $w, $h, $alt ] = bar7_seo_image();
		$type = is_singular( 'product' ) ? 'product' : 'website';

		echo "\n<!-- Rovakivo: description, canonical and sharing tags -->\n";
		if ( $desc ) {
			printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
		}
		if ( $canonical && ! is_singular() ) {
			printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $canonical ) );
		}
		$tags = array(
			'og:site_name'    => get_bloginfo( 'name' ),
			'og:type'         => $type,
			'og:title'        => $title,
			'og:description'  => $desc,
			'og:url'          => $url,
			'og:image'        => $img,
			'og:image:width'  => (string) $w,
			'og:image:height' => (string) $h,
			'og:image:alt'    => $alt,
			'og:locale'       => str_replace( '-', '_', get_bloginfo( 'language' ) ),
		);
		foreach ( $tags as $prop => $value ) {
			if ( '' !== $value ) {
				printf( "<meta property=\"%s\" content=\"%s\">\n", esc_attr( $prop ), esc_attr( $value ) );
			}
		}
		if ( is_singular( 'product' ) ) {
			$product = wc_get_product( get_queried_object_id() );
			if ( $product && '' !== $product->get_price() ) {
				printf( "<meta property=\"product:price:amount\" content=\"%s\">\n", esc_attr( wc_format_decimal( $product->get_price(), 2 ) ) );
				printf( "<meta property=\"product:price:currency\" content=\"%s\">\n", esc_attr( get_woocommerce_currency() ) );
				printf( "<meta property=\"product:availability\" content=\"%s\">\n", $product->is_in_stock() ? 'in stock' : 'out of stock' );
			}
		}
		printf( "<meta name=\"twitter:card\" content=\"summary_large_image\">\n<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr( $title ) );
		if ( $desc ) {
			printf( "<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr( $desc ) );
		}
		printf( "<meta name=\"twitter:image\" content=\"%s\">\n<meta name=\"twitter:image:alt\" content=\"%s\">\n", esc_url( $img ), esc_attr( $alt ) );

		// Organisation and website identity, on the homepage. Only confirmed facts: name, address, logo.
		if ( is_front_page() ) {
			$graph = array(
				'@context' => 'https://schema.org',
				'@graph'   => array(
					array(
						'@type' => 'Organization',
						'@id'   => home_url( '/#organization' ),
						'name'  => get_bloginfo( 'name' ),
						'url'   => home_url( '/' ),
						'logo'  => BAR7_URL . 'assets/brand/favicon/favicon-512.png',
					),
					array(
						'@type'           => 'WebSite',
						'@id'             => home_url( '/#website' ),
						'name'            => get_bloginfo( 'name' ),
						'url'             => home_url( '/' ),
						'publisher'       => array( '@id' => home_url( '/#organization' ) ),
						'potentialAction' => array(
							'@type'       => 'SearchAction',
							'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
							'query-input' => 'required name=search_term_string',
						),
					),
				),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
		}
	},
	5
);

/** Theme colour and web app manifest. */
add_action(
	'wp_head',
	static function (): void {
		if ( bar7_active() ) {
			echo "<meta name=\"theme-color\" content=\"#161616\">\n";
			printf( "<link rel=\"manifest\" href=\"%s\">\n", esc_url( BAR7_URL . 'assets/brand/favicon/site.webmanifest' ) );
		}
	},
	6
);

/** Favicons: the crisp SVG plus PNG/ICO fallbacks, in place of WordPress's resized site-icon crops. */
add_filter(
	'site_icon_meta_tags',
	static function ( array $tags ): array {
		if ( ! bar7_active() ) {
			return $tags;
		}
		$base = BAR7_URL . 'assets/brand/favicon/';
		return array(
			sprintf( '<link rel="icon" href="%s" sizes="any">', esc_url( $base . 'favicon.ico' ) ),
			sprintf( '<link rel="icon" href="%s" type="image/svg+xml">', esc_url( $base . 'favicon.svg' ) ),
			sprintf( '<link rel="icon" href="%s" sizes="32x32" type="image/png">', esc_url( $base . 'favicon-32.png' ) ),
			sprintf( '<link rel="icon" href="%s" sizes="192x192" type="image/png">', esc_url( $base . 'favicon-192.png' ) ),
			sprintf( '<link rel="apple-touch-icon" href="%s">', esc_url( $base . 'apple-touch-icon.png' ) ),
		);
	}
);

/* ---------------------------------------------------------------------------
 * Sitemap (WordPress core, /wp-sitemap.xml): only canonical, indexable URLs.
 * ------------------------------------------------------------------------ */

// No author archives: they only exist to list posts, and they would publish the admin's username.
add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);
add_filter(
	'wp_sitemaps_post_types',
	static function ( array $types ): array {
		unset( $types['mailpoet_page'], $types['post'] ); // MailPoet's subscription/captcha utility pages; the site has no blog.
		return $types;
	}
);
add_filter(
	'wp_sitemaps_taxonomies',
	static function ( array $tax ): array {
		unset( $tax['category'], $tax['post_tag'], $tax['product_tag'], $tax['post_format'] );
		return $tax;
	}
);
// Cart, checkout and account are not indexed (WooCommerce marks them noindex), so they are left out; so is
// Pages → Sale, whose address is the Sale view (a filtered, noindex listing).
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( array $args, string $post_type ): array {
		if ( 'page' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			$args['post__not_in'] = array_filter( array( wc_get_page_id( 'cart' ), wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ), bar7_sale_page_id() ) );
		}
		return $args;
	},
	10,
	2
);

// Author archives return "page not found".
add_action(
	'template_redirect',
	static function (): void {
		if ( bar7_active() && is_author() ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	},
	1
);
