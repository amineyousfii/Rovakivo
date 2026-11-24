<?php
/**
 * Editable pages: what the block editor shows is what the website shows.
 *
 * - Home, About and Contact are block content, rendered inside the storefront design (templates/views/*.php).
 * - Data-driven parts are "Rovakivo" blocks rendered on the server, in the editor too (inc/sections.php).
 * - Motion comes from CSS class names, so editors never touch code:
 *   is-intro / is-intro-item / is-intro-media / is-intro-body, is-reveal, is-reveal-group / is-reveal-item, is-reveal-media.
 * - Buttons with a bar7-button--{yellow|dark|outline|link|link-light} class are output with the site's own button markup
 *   (add bar7-icon--arrow-up for the up-right arrow). A Buttons block with is-unwrapped outputs the buttons alone.
 * - An Image block with bar7-hero__media becomes the responsive, high-priority hero photo.
 * - A section/aside Group is labelled by its first heading that has an HTML anchor; is-focus-target makes it focusable.
 * - The page editor loads the storefront fonts and stylesheet instead of the old theme's editor styles.
 */

defined( 'ABSPATH' ) || exit;

/** WordPress's generic block-gap rules would change the storefront spacing (the old theme's values no longer load). */
add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'disable-layout-styles' );
	},
	20
);

/**
 * Elementor's image-loading optimisation adds a second loading="lazy" to images that already have one
 * (WordPress's own handling checks first). Storefront pages set their own image attributes, so it is left out there.
 */
add_action(
	'template_redirect',
	static function (): void {
		global $wp_filter;
		if ( ! bar7_active() || empty( $wp_filter['wp_content_img_tag'] ) ) {
			return;
		}
		foreach ( $wp_filter['wp_content_img_tag']->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $cb ) {
				$fn = $cb['function'];
				if ( is_array( $fn ) && is_object( $fn[0] ) && str_contains( get_class( $fn[0] ), 'Elementor\\Modules\\ImageLoadingOptimization' ) ) {
					remove_filter( 'wp_content_img_tag', $fn, $priority );
				}
			}
		}
	},
	20
);

/* ---------- Rovakivo server-rendered blocks ---------- */

function bar7_block_definitions(): array {
	return array(
		'rovakivo/breadcrumbs'      => array( __( 'Rovakivo: breadcrumbs', 'bar7' ), 'bar7_block_breadcrumbs', array() ),
		'rovakivo/product-count'    => array( __( 'Rovakivo: product count', 'bar7' ), 'bar7_block_product_count', array() ),
		'rovakivo/collection-tiles' => array(
			__( 'Rovakivo: collection tiles', 'bar7' ),
			'bar7_block_collection_tiles',
			array(
				'section'      => array( 'type' => 'boolean', 'default' => true ),
				'headingLevel' => array( 'type' => 'number', 'default' => 2 ),
			),
		),
		'rovakivo/newsletter'       => array( __( 'Rovakivo: newsletter sign-up', 'bar7' ), 'bar7_block_newsletter', array() ),
		'rovakivo/support-links'    => array( __( 'Rovakivo: shopping help strip', 'bar7' ), 'bar7_section_support', array() ),
		'rovakivo/product-grid'     => array(
			__( 'Rovakivo: product grid', 'bar7' ),
			'bar7_block_product_grid',
			array(
				'ids'        => array( 'type' => 'string', 'default' => '' ),
				'filterable' => array( 'type' => 'boolean', 'default' => false ),
			),
		),
		'rovakivo/product-tabs'     => array( __( 'Rovakivo: product filter buttons', 'bar7' ), 'bar7_block_product_tabs', array() ),
		'rovakivo/sale-banner'      => array( __( 'Rovakivo: sale banner', 'bar7' ), 'bar7_section_sale', array() ),
		'rovakivo/testimonials'     => array( __( 'Rovakivo: testimonials', 'bar7' ), 'bar7_section_testimonials', array() ),
		'rovakivo/contact-form'     => array(
			__( 'Rovakivo: contact form', 'bar7' ),
			'bar7_block_contact_form',
			array( 'formId' => array( 'type' => 'number', 'default' => 506 ) ),
		),
		'rovakivo/help-links'       => array( __( 'Rovakivo: help links', 'bar7' ), 'bar7_contact_help_links', array() ),
		'rovakivo/product-catalog'  => array( __( 'Rovakivo: product catalog', 'bar7' ), 'bar7_block_product_catalog', array() ),
		'rovakivo/account-title'    => array( __( 'Rovakivo: account page title', 'bar7' ), 'bar7_block_account_title', array() ),
		// Editor-only preview behind the WooCommerce cart/checkout/account shortcodes (not in the block inserter).
		'rovakivo/woo-preview'      => array(
			__( 'Rovakivo: cart or checkout preview', 'bar7' ),
			'bar7_block_woo_preview',
			array( 'kind' => array( 'type' => 'string', 'default' => 'cart' ) ),
		),
	);
}

/**
 * Preview of the cart or checkout in the page editor: the editor's own current cart, rendered by WooCommerce.
 * Nothing is added to or changed in the cart. On the website this block outputs nothing (the shortcode does the work).
 */
function bar7_block_woo_preview( array $attrs ): void {
	if ( ! wp_is_serving_rest_request() || ! function_exists( 'WC' ) ) {
		return;
	}
	$kind = in_array( $attrs['kind'] ?? '', array( 'checkout', 'my_account' ), true ) ? $attrs['kind'] : 'cart';
	if ( ! WC()->cart ) {
		WC()->frontend_includes();
		wc_load_cart();
	}
	if ( ! WC()->cart ) {
		return;
	}
	if ( 'checkout' === $kind ) {
		// Show the checkout form even when the cart is empty (the website redirects to the cart instead).
		add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
	}
	echo do_shortcode( '[woocommerce_' . $kind . ']' ); // phpcs:ignore
}

/** My Account heading: changes with the visitor's state and the account section (see bar7_account_title()). */
function bar7_block_account_title(): void {
	echo '<h1 class="bar7-h1" data-intro-item>' . esc_html( wp_strip_all_tags( bar7_account_title() ) ) . '</h1>';
}

/** Is the visitor's cart empty? (false when there is no cart, e.g. in the editor) */
function bar7_cart_is_empty(): bool {
	return function_exists( 'WC' ) && WC()->cart && WC()->cart->is_empty();
}

/**
 * The shop's product listing (filters, sorting, grid, pagination). On the website it lists the products of the
 * page being viewed (page number, sorting and filters included); in the editor it previews the first page.
 */
function bar7_block_product_catalog(): void {
	global $wp_query;
	if ( ! wp_is_serving_rest_request() ) {
		require BAR7_DIR . 'templates/parts/catalog.php';
		return;
	}
	$saved    = $wp_query;
	$ordering = WC()->query->get_catalog_ordering_args();
	// On Pages → Sale the preview is the Sale view: only products with a sale price, with the sale filter on.
	$post     = get_post();
	$sale     = $post && function_exists( 'bar7_sale_page_id' ) && bar7_sale_page_id() === $post->ID;
	$args     = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() ),
		'orderby'        => $ordering['orderby'],
		'order'          => $ordering['order'],
		'meta_key'       => $ordering['meta_key'] ?? '', // phpcs:ignore WordPress.DB.SlowDBQuery
		'tax_query'      => WC()->query->get_tax_query(), // phpcs:ignore WordPress.DB.SlowDBQuery
	);
	if ( $sale ) {
		$args['post__in'] = array_merge( array( 0 ), array_map( 'absint', wc_get_product_ids_on_sale() ) );
		$had_sale         = isset( $_GET['on_sale'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$_GET['on_sale']  = '1';
	}
	$wp_query = new WP_Query( $args ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	require BAR7_DIR . 'templates/parts/catalog.php';
	if ( $sale && empty( $had_sale ) ) {
		unset( $_GET['on_sale'] );
	}
	$wp_query = $saved; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();
}

add_action(
	'init',
	static function (): void {
		wp_register_script(
			'bar7-editor-blocks',
			BAR7_URL . 'assets/js/editor-blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n', 'wp-hooks', 'wp-compose' ),
			bar7_asset_ver( 'assets/js/editor-blocks.js' ),
			true
		);
		foreach ( bar7_block_definitions() as $name => [ $title, $callback, $attributes ] ) {
			register_block_type(
				$name,
				array(
					'api_version'     => 3,
					'title'           => $title,
					'category'        => 'design',
					'icon'            => 'store',
					'attributes'      => $attributes,
					'supports'        => array( 'html' => false, 'reusable' => false ),
					'editor_script'   => 'bar7-editor-blocks',
					'render_callback' => static function ( $attrs = array() ) use ( $callback ): string {
						ob_start();
						$callback( (array) $attrs );
						return (string) ob_get_clean();
					},
				)
			);
		}
	}
);

function bar7_block_breadcrumbs(): void {
	if ( ! wp_is_serving_rest_request() ) {
		bar7_breadcrumbs();
		return;
	}
	// In the editor preview there is no front-end query, so show the page's own trail.
	$post = get_post();
	if ( $post && (int) get_option( 'page_on_front' ) === $post->ID ) {
		return;
	}
	echo '<nav class="bar7-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'bar7' ) . '"><ol><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'bar7' ) . '</a></li>';
	// Pages → Sale sits under the shop on the website: Home / Shop / Sale.
	if ( $post && function_exists( 'bar7_sale_page_id' ) && bar7_sale_page_id() === $post->ID ) {
		echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html( get_the_title( wc_get_page_id( 'shop' ) ) ) . '</a></li>';
	}
	echo '<li><span aria-current="page">' . esc_html( $post ? get_the_title( $post ) : __( 'This page', 'bar7' ) ) . '</span></li></ol></nav>';
}

function bar7_block_product_count(): void {
	$total  = (int) ( wp_count_posts( 'product' )->publish ?? 0 );
	$casual = get_term_by( 'slug', 'casual', 'product_cat' );
	$travel = get_term_by( 'slug', 'travel', 'product_cat' );
	if ( ! $total ) {
		return;
	}
	echo '<p>';
	printf(
		/* translators: 1: total products, 2: casual count, 3: travel count */
		esc_html__( 'Right now there are %1$d backpacks in the shop: %2$d casual and %3$d travel.', 'bar7' ),
		$total, // phpcs:ignore
		$casual ? (int) $casual->count : 0, // phpcs:ignore
		$travel ? (int) $travel->count : 0 // phpcs:ignore
	);
	echo '</p>';
}

function bar7_block_collection_tiles( array $attrs ): void {
	$level = (int) ( $attrs['headingLevel'] ?? 2 );
	if ( isset( $attrs['section'] ) && ! $attrs['section'] ) {
		bar7_collection_tiles( $level );
		return;
	}
	?>
	<section class="bar7-section bar7-section--tight-top" aria-label="<?php esc_attr_e( 'Shop by collection', 'bar7' ); ?>">
		<div class="bar7-wrap"><?php bar7_collection_tiles( $level ); ?></div>
	</section>
	<?php
}

function bar7_block_newsletter(): void {
	require BAR7_DIR . 'templates/parts/newsletter.php';
}

function bar7_block_product_grid( array $attrs ): void {
	bar7_product_grid( bar7_parse_ids( $attrs['ids'] ?? '' ), ! empty( $attrs['filterable'] ) );
}

/** The filter buttons use the product IDs of the filterable grid on the same page. */
function bar7_block_product_tabs(): void {
	$post = get_post();
	if ( ! $post ) {
		return;
	}
	$find = static function ( array $blocks ) use ( &$find ) {
		foreach ( $blocks as $b ) {
			if ( 'rovakivo/product-grid' === $b['blockName'] && ! empty( $b['attrs']['filterable'] ) ) {
				return (string) ( $b['attrs']['ids'] ?? '' );
			}
			$inner = $find( $b['innerBlocks'] ?? array() );
			if ( null !== $inner ) {
				return $inner;
			}
		}
		return null;
	};
	$ids = $find( parse_blocks( $post->post_content ) );
	if ( null !== $ids ) {
		bar7_product_tabs( bar7_parse_ids( $ids ) );
	}
}

function bar7_block_contact_form( array $attrs ): void {
	bar7_contact_form( (int) ( $attrs['formId'] ?? 506 ) );
}

/* ---------- Front end: site markup and motion attributes ---------- */

add_filter(
	'render_block',
	static function ( string $html, array $block ): string {
		if ( is_admin() || wp_is_serving_rest_request() || '' === $html ) {
			return $html;
		}
		$name    = (string) $block['blockName'];
		$classes = (string) ( $block['attrs']['className'] ?? '' );

		// Cart page: the intro line and "Continue shopping" only show when the cart has products.
		if ( str_contains( $classes, 'bar7-hide-if-cart-empty' ) && bar7_cart_is_empty() ) {
			return '';
		}
		if ( 'core/button' === $name && str_contains( $classes, 'bar7-button--continue' ) ) {
			if ( bar7_cart_is_empty() ) {
				return '';
			}
			$p = new WP_HTML_Tag_Processor( $html );
			if ( $p->next_tag( 'a' ) ) {
				$label = trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) );
				return '<a class="bar7-link bar7-cart-continue" href="' . esc_url( (string) $p->get_attribute( 'href' ) ) . '">' . bar7_icon( 'arrow-left' ) . esc_html( $label ) . '</a>';
			}
		}

		// Site buttons: same markup as the rest of the storefront.
		if ( 'core/button' === $name && preg_match( '/\bbar7-button--(yellow|dark|outline|link-light|link)\b/', $classes, $m ) ) {
			$p = new WP_HTML_Tag_Processor( $html );
			if ( $p->next_tag( 'a' ) ) {
				$href  = (string) $p->get_attribute( 'href' );
				$label = trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) );
				$class = array(
					'yellow'     => 'bar7-btn bar7-btn--yellow',
					'dark'       => 'bar7-btn bar7-btn--dark',
					'outline'    => 'bar7-btn bar7-btn--outline',
					'link'       => 'bar7-link',
					'link-light' => 'bar7-link bar7-link--light',
				)[ $m[1] ];
				$icon = str_contains( $classes, 'bar7-icon--arrow-up' ) ? 'arrow-up' : ( str_contains( $classes, 'bar7-icon--arrow-down' ) ? 'arrow-down' : 'arrow' );
				return '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $href ) . '">' . esc_html( $label ) . bar7_icon( $icon ) . '</a>';
			}
		}

		// Buttons wrapper: keep only the storefront classes, or no wrapper at all.
		if ( 'core/buttons' === $name && str_contains( $classes, 'bar7-' ) || 'core/buttons' === $name && str_contains( $classes, 'is-unwrapped' ) ) {
			if ( str_contains( $classes, 'is-unwrapped' ) ) {
				return (string) preg_replace( '/^\s*<div[^>]*>(.*)<\/div>\s*$/s', '$1', $html );
			}
			$html = (string) preg_replace( '/<div class="wp-block-buttons[^"]*"/', '<div class="' . esc_attr( trim( $classes ) ) . '"', $html, 1 );
		}

		// About page opening photo: the first thing seen, so it loads with priority and at the size it is shown.
		if ( 'core/image' === $name && str_contains( $classes, 'bar7-aboutopen__media' ) && ! empty( $block['attrs']['id'] ) ) {
			$id = (int) $block['attrs']['id'];
			$p  = new WP_HTML_Tag_Processor( $html );
			if ( $p->next_tag( 'img' ) ) {
				$p->set_attribute( 'srcset', (string) wp_get_attachment_image_srcset( $id, 'full' ) );
				// The 3:2 photo fills a taller frame (object-fit: cover), so it is drawn wider than the frame: about 1.5 × the
				// frame height on desktop (≤ 990px), the full width in the 3:2 tablet frame, 1.9 × the width in the 4:5 phone frame.
				$p->set_attribute( 'sizes', '(min-width: 900px) 990px, (min-width: 600px) 100vw, 188vw' );
				$src = wp_get_attachment_image_src( $id, 'full' );
				if ( $src ) {
					$p->set_attribute( 'width', (string) $src[1] );
					$p->set_attribute( 'height', (string) $src[2] );
				}
				$p->set_attribute( 'fetchpriority', 'high' );
				$p->set_attribute( 'decoding', 'async' );
				$p->remove_attribute( 'loading' );
				$html = $p->get_updated_html();
			}
		}

		// Hero photo: responsive, high priority, as a <picture> like the original design.
		if ( 'core/image' === $name && str_contains( $classes, 'bar7-hero__media' ) && ! empty( $block['attrs']['id'] ) ) {
			$id  = (int) $block['attrs']['id'];
			$src = wp_get_attachment_image_src( $id, 'full' );
			if ( $src ) {
				$p   = new WP_HTML_Tag_Processor( $html );
				$alt = $p->next_tag( 'img' ) ? (string) $p->get_attribute( 'alt' ) : '';
				return sprintf(
					'<picture class="bar7-hero__media"%1$s><img src="%2$s" srcset="%3$s" sizes="100vw" width="%4$d" height="%5$d" alt="%6$s" fetchpriority="high" decoding="async"></picture>',
					str_contains( $classes, 'is-intro-media' ) ? ' data-intro-media' : '',
					esc_url( $src[0] ),
					esc_attr( (string) wp_get_attachment_image_srcset( $id, 'full' ) ),
					(int) $src[1],
					(int) $src[2],
					esc_attr( $alt )
				);
			}
		}

		$p = new WP_HTML_Tag_Processor( $html );
		if ( ! $p->next_tag() ) {
			return $html;
		}
		$changed = false;

		// Sections and asides are named by their first heading that has an HTML anchor.
		if ( 'core/group' === $name && in_array( $p->get_tag(), array( 'SECTION', 'ASIDE' ), true ) && null === $p->get_attribute( 'aria-labelledby' ) && null === $p->get_attribute( 'aria-label' ) ) {
			$scan = new WP_HTML_Tag_Processor( $html );
			$scan->next_tag();
			while ( $scan->next_tag() ) {
				if ( in_array( $scan->get_tag(), array( 'H1', 'H2', 'H3' ), true ) ) {
					$hid = $scan->get_attribute( 'id' );
					if ( is_string( $hid ) && '' !== $hid ) {
						$p->set_attribute( 'aria-labelledby', $hid );
						$changed = true;
					}
					break;
				}
			}
		}
		if ( $p->has_class( 'is-focus-target' ) ) {
			$p->set_attribute( 'tabindex', '-1' );
			$changed = true;
		}

		// Motion attributes from class names.
		$map = array(
			'is-intro'        => 'data-intro',
			'is-intro-item'   => 'data-intro-item',
			'is-intro-media'  => 'data-intro-media',
			'is-intro-body'   => 'data-intro-body',
			'is-reveal'       => 'data-reveal',
			'is-reveal-group' => 'data-reveal-group',
			'is-reveal-item'  => 'data-reveal-item',
			'is-reveal-media' => 'data-reveal-media',
		);
		foreach ( $map as $class => $attr ) {
			if ( $p->has_class( $class ) ) {
				$p->set_attribute( $attr, true );
				$changed = true;
			}
		}
		return $changed ? $p->get_updated_html() : $html;
	},
	10,
	2
);

/** The homepage hero photo, if the front page sets one (used to preload it). */
function bar7_front_hero_image_id(): int {
	$front = get_post( (int) get_option( 'page_on_front' ) );
	if ( ! $front || ! has_blocks( $front->post_content ) ) {
		return 0;
	}
	$find = static function ( array $blocks ) use ( &$find ): int {
		foreach ( $blocks as $b ) {
			if ( 'core/image' === $b['blockName'] && str_contains( (string) ( $b['attrs']['className'] ?? '' ), 'bar7-hero__media' ) ) {
				return (int) ( $b['attrs']['id'] ?? 0 );
			}
			$id = $find( $b['innerBlocks'] ?? array() );
			if ( $id ) {
				return $id;
			}
		}
		return 0;
	};
	return $find( parse_blocks( $front->post_content ) );
}

/* ---------- Page editor: storefront styles instead of the old theme's ---------- */

function bar7_editor_uses_site_styles( $post = null ): bool {
	$post = get_post( $post );
	return $post && 'page' === $post->post_type;
}

add_filter(
	'block_editor_settings_all',
	static function ( array $settings, $context ): array {
		if ( ! ( $context instanceof WP_Block_Editor_Context ) || ! bar7_editor_uses_site_styles( $context->post ) ) {
			return $settings;
		}
		// Drop the old theme's editor stylesheet.
		$settings['styles'] = array_values(
			array_filter(
				(array) ( $settings['styles'] ?? array() ),
				static fn( $s ) => false === strpos( (string) ( $s['baseURL'] ?? '' ), '/themes/blocksy/' )
			)
		);
		$read = static function ( string $rel ): string {
			$file = BAR7_DIR . $rel;
			return file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore
		};
		// The storefront scopes its base rules to the body class; in the editor that body is .editor-styles-wrapper.
		$site = str_replace( array( 'body.bar7-site', '.bar7-site' ), '.editor-styles-wrapper', $read( 'assets/css/site.css' ) );
		// WooCommerce's own front-end styles, as the website loads them under the storefront styles (cart/checkout previews).
		if ( defined( 'WC_PLUGIN_FILE' ) ) {
			$wc_dir = plugin_dir_path( WC_PLUGIN_FILE ) . 'assets/css/';
			$wc_url = plugins_url( 'assets/css/', WC_PLUGIN_FILE );
			foreach ( array( 'woocommerce-layout.css' => '', 'woocommerce-smallscreen.css' => '(max-width: 768px)', 'woocommerce.css' => '' ) as $file => $media ) {
				$css = file_exists( $wc_dir . $file ) ? (string) file_get_contents( $wc_dir . $file ) : ''; // phpcs:ignore
				if ( '' !== $css ) {
					$settings['styles'][] = array( 'css' => $media ? "@media {$media} {
{$css}
}" : $css, 'baseURL' => $wc_url . $file, '__unstableType' => 'theme', 'isGlobalStyles' => false );
				}
			}
		}
		$settings['styles'][] = array( 'css' => $read( 'assets/fonts/fonts.css' ), 'baseURL' => BAR7_URL . 'assets/fonts/fonts.css', '__unstableType' => 'theme', 'isGlobalStyles' => false );
		$settings['styles'][] = array( 'css' => $site, 'baseURL' => BAR7_URL . 'assets/css/site.css', '__unstableType' => 'theme', 'isGlobalStyles' => false );
		$settings['styles'][] = array( 'css' => $read( 'assets/css/editor.css' ), 'baseURL' => BAR7_URL . 'assets/css/editor.css', '__unstableType' => 'theme', 'isGlobalStyles' => false );
		return $settings;
	},
	20,
	2
);

/** The old theme's block styles would restyle headings and buttons inside the page editor. */
add_action(
	'enqueue_block_assets',
	static function (): void {
		if ( is_admin() && bar7_editor_uses_site_styles() ) {
			wp_dequeue_style( 'blocksy-theme-blocks-editor-styles' );
		}
	},
	999
);
add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		if ( bar7_editor_uses_site_styles() ) {
			wp_dequeue_style( 'blocksy-theme-blocks-editor-styles' );
		}
	},
	999
);
