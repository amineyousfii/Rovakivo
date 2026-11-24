<?php
/**
 * Site menus, edited in Appearance → Menus.
 *
 * - "Rovakivo: header menu"    → the header links and the mobile menu (CSS class "is-sale" adds the yellow dot).
 * - "Rovakivo: footer columns" → each top-level item is a column heading; the items under it are its links.
 * Without an assigned menu, the storefront falls back to its built-in links.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function (): void {
		register_nav_menus(
			array(
				'rovakivo-header' => __( 'Rovakivo: header menu', 'bar7' ),
				'rovakivo-footer' => __( 'Rovakivo: footer columns', 'bar7' ),
			)
		);
		// The old theme's header/footer are not shown anywhere, so its menu locations are hidden from
		// Appearance → Menus to avoid confusion. Their saved assignments are kept (they come back if this plugin is off).
		foreach ( array( 'menu_1', 'menu_2', 'menu_mobile', 'footer' ) as $location ) {
			unregister_nav_menu( $location );
		}
	},
	99
);

/**
 * The menu at a location as a tree: [ [ 'title', 'url', 'classes', 'children' => [...] ], ... ], or null if none is assigned.
 */
function bar7_menu_tree( string $location ): ?array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return null;
	}
	$items = wp_get_nav_menu_items( (int) $locations[ $location ] );
	if ( ! $items ) {
		return null;
	}
	$nodes = array();
	foreach ( $items as $item ) {
		// Links to pages that aren't published (drafts, private, trash) stay hidden until the page is published.
		if ( 'post_type' === $item->type && 'publish' !== get_post_status( (int) $item->object_id ) ) {
			continue;
		}
		$nodes[ (int) $item->ID ] = array(
			'title'    => (string) $item->title,
			'url'      => (string) $item->url,
			'classes'  => array_values( array_filter( (array) $item->classes ) ),
			'parent'   => (int) $item->menu_item_parent,
			'children' => array(),
		);
	}
	$tree = array();
	foreach ( $nodes as $id => &$node ) {
		if ( $node['parent'] && isset( $nodes[ $node['parent'] ] ) ) {
			$nodes[ $node['parent'] ]['children'][] = &$node;
		} else {
			$tree[] = &$node;
		}
	}
	unset( $node );
	return $tree;
}

/** Compare two links by path and query (ignores host and trailing slash). */
function bar7_same_url( string $a, string $b ): bool {
	$norm = static function ( string $url ): string {
		$url   = html_entity_decode( $url, ENT_QUOTES, 'UTF-8' );
		$parts = wp_parse_url( $url );
		$path  = untrailingslashit( $parts['path'] ?? '' );
		return strtolower( $path ) . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
	};
	return '' !== $a && '' !== $b && $norm( $a ) === $norm( $b );
}

/** Header links: from the menu, or the built-in list. */
function bar7_header_links(): array {
	$tree = bar7_menu_tree( 'rovakivo-header' );
	if ( null !== $tree ) {
		$map = static function ( array $n ) use ( &$map ): array {
			return array(
				'title'    => $n['title'],
				'url'      => $n['url'],
				'sale'     => in_array( 'is-sale', $n['classes'], true ),
				'children' => array_map( $map, $n['children'] ),
			);
		};
		return array_map( $map, $tree );
	}
	$u     = bar7_urls();
	$links = array(
		array( 'title' => __( 'Shop all', 'bar7' ), 'url' => $u['shop'], 'sale' => false ),
		array( 'title' => __( 'Casual', 'bar7' ), 'url' => $u['casual'], 'sale' => false ),
		array( 'title' => __( 'Travel', 'bar7' ), 'url' => $u['travel'], 'sale' => false ),
		array( 'title' => __( 'Sale', 'bar7' ), 'url' => $u['sale'], 'sale' => true ),
	);
	if ( $u['about'] ) {
		$links[] = array( 'title' => __( 'About', 'bar7' ), 'url' => $u['about'], 'sale' => false );
	}
	return $links;
}

/** Footer columns: from the menu, or the built-in columns. */
function bar7_footer_columns(): array {
	$tree = bar7_menu_tree( 'rovakivo-footer' );
	if ( null !== $tree ) {
		return array_map(
			static fn( $n ) => array(
				'title' => $n['title'],
				'links' => array_map( static fn( $c ) => array( 'title' => $c['title'], 'url' => $c['url'] ), $n['children'] ),
			),
			$tree
		);
	}
	$u    = bar7_urls();
	$cols = array(
		array(
			'title' => __( 'Shop', 'bar7' ),
			'links' => array(
				array( 'title' => __( 'Shop all', 'bar7' ), 'url' => $u['shop'] ),
				array( 'title' => __( 'Casual backpacks', 'bar7' ), 'url' => $u['casual'] ),
				array( 'title' => __( 'Travel backpacks', 'bar7' ), 'url' => $u['travel'] ),
				array( 'title' => __( 'Sale', 'bar7' ), 'url' => $u['sale'] ),
			),
		),
		array(
			'title' => __( 'Help', 'bar7' ),
			'links' => array_values(
				array_filter(
					array(
						$u['contact'] ? array( 'title' => __( 'Contact us', 'bar7' ), 'url' => $u['contact'] ) : null,
						array( 'title' => __( 'My account', 'bar7' ), 'url' => $u['account'] ),
						array( 'title' => __( 'Cart', 'bar7' ), 'url' => $u['cart'] ),
					)
				)
			),
		),
		array(
			'title' => __( 'Rovakivo', 'bar7' ),
			'links' => array_values(
				array_filter(
					array(
						$u['about'] ? array( 'title' => __( 'About us', 'bar7' ), 'url' => $u['about'] ) : null,
						$u['privacy'] ? array( 'title' => __( 'Privacy policy', 'bar7' ), 'url' => $u['privacy'] ) : null,
					)
				)
			),
		),
	);
	return $cols;
}

/** The link of the section being viewed (for aria-current in the header). */
function bar7_current_section_url(): string {
	if ( ! empty( $GLOBALS['bar7_preview_url'] ) ) {
		return (string) $GLOBALS['bar7_preview_url'];
	}
	$u = bar7_urls();
	if ( ! empty( $_GET['on_sale'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return $u['sale'];
	}
	if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		return $u['shop'];
	}
	if ( is_product_category() ) {
		$term = get_queried_object();
		return $term instanceof WP_Term ? (string) get_term_link( $term ) : '';
	}
	if ( is_page() ) {
		return (string) get_permalink( get_queried_object_id() );
	}
	return '';
}
