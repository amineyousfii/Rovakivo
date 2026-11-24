<?php
/**
 * Every front-end page renders through one layout (header, view, footer).
 */

defined( 'ABSPATH' ) || exit;

/** Which view the current request uses. */
function bar7_view(): string {
	static $view = null;
	if ( null !== $view ) {
		return $view;
	}
	if ( is_front_page() ) {
		$view = 'home';
	} elseif ( is_singular( 'product' ) ) {
		$view = 'product';
	} elseif ( is_shop() || is_product_taxonomy() || is_search() ) {
		$view = 'archive';
	} elseif ( is_404() ) {
		$view = '404';
	} elseif ( is_page( 'about' ) ) {
		$view = 'about';
	} elseif ( is_page( 'contact' ) ) {
		$view = 'contact';
	} elseif ( is_singular() ) {
		$view = 'page';
	} else {
		$view = 'posts';
	}
	return $view;
}

add_filter(
	'template_include',
	static function ( $template ) {
		if ( ! bar7_active() || is_embed() || is_feed() ) {
			return $template;
		}
		return BAR7_DIR . 'templates/layout.php';
	},
	PHP_INT_MAX
);

add_filter(
	'body_class',
	static function ( array $classes ): array {
		if ( bar7_active() ) {
			$classes[] = 'bar7-site';
			$classes[] = 'bar7-view-' . bar7_view();
			if ( 'home' === bar7_view() ) {
				$classes[] = 'bar7-home';
			}
		}
		return $classes;
	}
);

/** Site search covers the product catalog. */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}
		if ( ! $query->get( 'post_type' ) ) {
			$query->set( 'post_type', 'product' );
		}
	}
);
