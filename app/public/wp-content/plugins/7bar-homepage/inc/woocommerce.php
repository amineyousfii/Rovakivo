<?php
/**
 * WooCommerce integration: templates, cart count, mini-cart, sale filter, archive filters.
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Templates: use WooCommerce's own templates (or ours) instead of the theme's copies,
 * which depend on the theme's scripts and styles.
 * ---------------------------------------------------------------------- */

add_filter(
	'wc_get_template',
	static function ( $template, $template_name ) {
		if ( ! bar7_active() ) {
			return $template;
		}
		$ours = BAR7_DIR . 'templates/woocommerce/' . $template_name;
		if ( file_exists( $ours ) ) {
			return $ours;
		}
		if ( false !== strpos( wp_normalize_path( (string) $template ), '/themes/blocksy/' ) ) {
			$core = WC()->plugin_path() . '/templates/' . $template_name;
			if ( file_exists( $core ) ) {
				return $core;
			}
		}
		return $template;
	},
	999,
	2
);

add_filter(
	'wc_get_template_part',
	static function ( $template, $slug, $name ) {
		if ( bar7_active() && 'content' === $slug && 'product' === $name ) {
			return BAR7_DIR . 'templates/woocommerce/content-product.php';
		}
		return $template;
	},
	999,
	3
);

/** The theme's quantity buttons need its scripts; the storefront adds its own steppers. */
add_filter(
	'theme_mod_has_custom_quantity',
	static function ( $value ) {
		return bar7_active() ? 'no' : $value;
	}
);

/* -------------------------------------------------------------------------
 * Header cart count and mini-cart, kept current by WooCommerce cart fragments.
 * ---------------------------------------------------------------------- */

function bar7_cart_count_html(): string {
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
	return sprintf(
		'<span class="bar7-cart-count" aria-hidden="true">%1$d</span><span class="screen-reader-text">%2$s</span>',
		$count,
		esc_html( sprintf( _n( 'Cart, %d item', 'Cart, %d items', $count, 'bar7' ), $count ) )
	);
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( array $fragments ): array {
		$fragments['span.bar7-cart-wrap'] = '<span class="bar7-cart-wrap">' . bar7_cart_count_html() . '</span>';
		$count                            = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
		$fragments['span.bar7-drawer-count'] = '<span class="bar7-drawer-count">' . esc_html( sprintf( _n( '%d item', '%d items', $count, 'bar7' ), $count ) ) . '</span>';
		return $fragments;
	}
);

/** Mini-cart buttons in the storefront's button styles. */
add_action(
	'init',
	static function (): void {
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
		add_action(
			'woocommerce_widget_shopping_cart_buttons',
			static function (): void {
				echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="bar7-btn bar7-btn--yellow bar7-btn--block checkout">' . esc_html__( 'Checkout', 'bar7' ) . bar7_icon( 'arrow' ) . '</a>'; // phpcs:ignore
				echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="bar7-btn bar7-btn--outline bar7-btn--block wc-forward">' . esc_html__( 'View cart', 'bar7' ) . '</a>';
			}
		);
	},
	20
);

/* -------------------------------------------------------------------------
 * "Sale" collection: ?on_sale=1 on the shop, categories and search lists products discounted now.
 * WooCommerce builds the catalog query's post__in from this filter.
 * ---------------------------------------------------------------------- */

add_filter(
	'loop_shop_post_in',
	static function ( $ids ) {
		if ( empty( $_GET['on_sale'] ) || ! function_exists( 'wc_get_product_ids_on_sale' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $ids;
		}
		$on_sale = array_map( 'absint', wc_get_product_ids_on_sale() );
		return array_merge( (array) $ids, $on_sale ? $on_sale : array( 0 ) );
	}
);

/**
 * Pages → Sale: its blocks (heading, introduction, product catalog) are what the shop's Sale view shows,
 * the same way Pages → Shop holds the main shop. The page has no address of its own: its link is the
 * Sale view (/shop/?on_sale=1), and opening /sale/ directly goes there.
 */
function bar7_sale_page_id(): int {
	static $id = null;
	if ( null === $id ) {
		$id = (int) get_option( 'bar7_sale_page_id', 0 );
		if ( $id && ( 'page' !== get_post_type( $id ) || 'trash' === get_post_status( $id ) ) ) {
			$id = 0;
		}
	}
	return $id;
}

add_filter(
	'page_link',
	static function ( $link, $post_id ) {
		return ( (int) $post_id && (int) $post_id === bar7_sale_page_id() ) ? bar7_urls()['sale'] : $link;
	},
	10,
	2
);

add_action(
	'template_redirect',
	static function (): void {
		if ( bar7_sale_page_id() && is_page( bar7_sale_page_id() ) && ! is_preview() ) {
			wp_safe_redirect( bar7_urls()['sale'], 302 );
			exit;
		}
	},
	5
);

/* -------------------------------------------------------------------------
 * Archive filter state (category, price, sale), all handled by WooCommerce's own query.
 * ---------------------------------------------------------------------- */

/** @return array{min: string, max: string, sale: bool, orderby: string} */
function bar7_filter_state(): array {
	// phpcs:disable WordPress.Security.NonceVerification
	return array(
		'min'     => isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ? (string) absint( $_GET['min_price'] ) : '',
		'max'     => isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ? (string) absint( $_GET['max_price'] ) : '',
		'sale'    => ! empty( $_GET['on_sale'] ),
		'orderby' => isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '',
	);
	// phpcs:enable
}

/** Base URL for the current listing (shop, category or search) without filters. */
function bar7_listing_base_url(): string {
	if ( is_product_taxonomy() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? wc_get_page_permalink( 'shop' ) : $link;
	}
	if ( is_search() ) {
		return add_query_arg( array( 's' => get_search_query(), 'post_type' => 'product' ), home_url( '/' ) );
	}
	return wc_get_page_permalink( 'shop' );
}

/** A URL keeping the active filters, with some values changed. */
function bar7_filter_url( string $base, array $changes ): string {
	$state = bar7_filter_state();
	$args  = array(
		'min_price' => $state['min'],
		'max_price' => $state['max'],
		'on_sale'   => $state['sale'] ? '1' : '',
		'orderby'   => $state['orderby'],
	);
	$args = array_filter( array_merge( $args, $changes ), static fn( $v ) => '' !== $v && null !== $v );
	return add_query_arg( $args, $base );
}

/** Price bounds of the published catalog, for the price filter placeholders. */
function bar7_price_bounds(): array {
	global $wpdb;
	$row = $wpdb->get_row( "SELECT MIN( min_price ) AS min_price, MAX( max_price ) AS max_price FROM {$wpdb->wc_product_meta_lookup}" ); // phpcs:ignore WordPress.DB
	return array(
		'min' => $row ? (int) floor( (float) $row->min_price ) : 0,
		'max' => $row ? (int) ceil( (float) $row->max_price ) : 0,
	);
}

/** Number of visible on-sale products in each category, keyed by term ID. */
function bar7_sale_counts(): array {
	$ids = array_filter( array_map( 'absint', wc_get_product_ids_on_sale() ) );
	if ( ! $ids ) {
		return array();
	}
	$visible = wc_get_products( array( 'include' => $ids, 'status' => 'publish', 'visibility' => 'catalog', 'limit' => -1, 'return' => 'ids' ) );
	$counts  = array();
	foreach ( $visible as $id ) {
		foreach ( wc_get_product_term_ids( $id, 'product_cat' ) as $tid ) {
			$counts[ $tid ] = ( $counts[ $tid ] ?? 0 ) + 1;
		}
	}
	return $counts;
}

/** Category, price and sale filters. Rendered in the sidebar (desktop) and the filter drawer (mobile). */
function bar7_filter_panel( string $prefix, string $base ): void {
	$state   = bar7_filter_state();
	// In the Sale collection, each category's count is how many of its backpacks are on sale (what its link shows).
	$sale_n  = $state['sale'] ? bar7_sale_counts() : null;
	$bounds  = bar7_price_bounds();
	$current = is_product_taxonomy() ? get_queried_object_id() : 0;
	$terms   = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	$keep    = array( 'min_price' => $state['min'], 'max_price' => $state['max'], 'on_sale' => $state['sale'] ? '1' : '', 'orderby' => $state['orderby'] );
	$keep    = array_filter( $keep, static fn( $v ) => '' !== $v );
	$shop    = wc_get_page_permalink( 'shop' );
	$symbol  = html_entity_decode( get_woocommerce_currency_symbol() );

	// Query args already in the base URL (e.g. the search terms) must survive a GET form submit.
	$base_args = array();
	wp_parse_str( (string) wp_parse_url( $base, PHP_URL_QUERY ), $base_args );
	?>
	<div class="bar7-filters">
		<section class="bar7-filters__group" aria-labelledby="<?php echo esc_attr( $prefix ); ?>-f-cat">
			<h2 class="bar7-filters__title" id="<?php echo esc_attr( $prefix ); ?>-f-cat"><?php esc_html_e( 'Category', 'bar7' ); ?></h2>
			<ul class="bar7-filters__cats">
				<li><a href="<?php echo esc_url( add_query_arg( $keep, $shop ) ); ?>"<?php echo ( is_shop() && ! is_search() ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All backpacks', 'bar7' ); ?></a></li>
				<?php if ( ! is_wp_error( $terms ) ) : foreach ( $terms as $t ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( $keep, get_term_link( $t ) ) ); ?>"<?php echo $current === $t->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $t->name ); ?> <span class="bar7-filters__n">(<?php echo (int) ( null === $sale_n ? $t->count : ( $sale_n[ $t->term_id ] ?? 0 ) ); ?>)</span></a></li>
				<?php endforeach; endif; ?>
			</ul>
		</section>

		<form class="bar7-filters__form" method="get" action="<?php echo esc_url( strtok( $base, '?' ) ); ?>">
			<?php foreach ( $base_args as $k => $v ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( is_array( $v ) ? '' : $v ); ?>">
			<?php endforeach; ?>
			<?php if ( $state['orderby'] ) : ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $state['orderby'] ); ?>">
			<?php endif; ?>

			<fieldset class="bar7-filters__group">
				<legend class="bar7-filters__title"><?php esc_html_e( 'Price', 'bar7' ); ?></legend>
				<div class="bar7-filters__price">
					<label for="<?php echo esc_attr( $prefix ); ?>-min"><span><?php printf( esc_html__( 'From (%s)', 'bar7' ), esc_html( $symbol ) ); ?></span>
						<input id="<?php echo esc_attr( $prefix ); ?>-min" type="number" name="min_price" min="0" step="1" inputmode="numeric" value="<?php echo esc_attr( $state['min'] ); ?>" placeholder="<?php echo esc_attr( (string) $bounds['min'] ); ?>">
					</label>
					<label for="<?php echo esc_attr( $prefix ); ?>-max"><span><?php printf( esc_html__( 'To (%s)', 'bar7' ), esc_html( $symbol ) ); ?></span>
						<input id="<?php echo esc_attr( $prefix ); ?>-max" type="number" name="max_price" min="0" step="1" inputmode="numeric" value="<?php echo esc_attr( $state['max'] ); ?>" placeholder="<?php echo esc_attr( (string) $bounds['max'] ); ?>">
					</label>
				</div>
			</fieldset>

			<fieldset class="bar7-filters__group">
				<legend class="bar7-filters__title"><?php esc_html_e( 'Offers', 'bar7' ); ?></legend>
				<label class="bar7-check" for="<?php echo esc_attr( $prefix ); ?>-sale">
					<input id="<?php echo esc_attr( $prefix ); ?>-sale" type="checkbox" name="on_sale" value="1" <?php checked( $state['sale'] ); ?>>
					<span><?php esc_html_e( 'On sale only', 'bar7' ); ?></span>
				</label>
			</fieldset>

			<div class="bar7-filters__actions">
				<button class="bar7-btn bar7-btn--dark bar7-btn--block" type="submit"><?php esc_html_e( 'Apply filters', 'bar7' ); ?></button>
				<?php if ( '' !== $state['min'] || '' !== $state['max'] || $state['sale'] ) : ?>
					<a class="bar7-link" href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'Clear filters', 'bar7' ); ?></a>
				<?php endif; ?>
			</div>
		</form>
	</div>
	<?php
}

/** Active filter chips, each removable, plus "Clear all". */
function bar7_active_filters( string $base ): void {
	$state  = bar7_filter_state();
	$chips  = array();
	$symbol = html_entity_decode( get_woocommerce_currency_symbol() );
	if ( '' !== $state['min'] ) {
		$chips[] = array( sprintf( __( 'From %1$s %2$s', 'bar7' ), $state['min'], $symbol ), bar7_filter_url( $base, array( 'min_price' => '' ) ) );
	}
	if ( '' !== $state['max'] ) {
		$chips[] = array( sprintf( __( 'Up to %1$s %2$s', 'bar7' ), $state['max'], $symbol ), bar7_filter_url( $base, array( 'max_price' => '' ) ) );
	}
	if ( $state['sale'] ) {
		$chips[] = array( __( 'On sale', 'bar7' ), bar7_filter_url( $base, array( 'on_sale' => '' ) ) );
	}
	if ( ! $chips ) {
		return;
	}
	echo '<div class="bar7-chips" role="group" aria-label="' . esc_attr__( 'Active filters', 'bar7' ) . '">';
	foreach ( $chips as [ $label, $url ] ) {
		printf(
			'<a class="bar7-chip" href="%1$s" aria-label="%2$s">%3$s%4$s</a>',
			esc_url( $url ),
			esc_attr( sprintf( __( 'Remove filter: %s', 'bar7' ), $label ) ),
			esc_html( $label ),
			bar7_icon( 'close' ) // phpcs:ignore
		);
	}
	echo '<a class="bar7-chips__clear" href="' . esc_url( $state['orderby'] ? add_query_arg( 'orderby', $state['orderby'], $base ) : $base ) . '">' . esc_html__( 'Clear all', 'bar7' ) . '</a>';
	echo '</div>';
}

/** Cart and order rows use the supplied photograph when the pack has one for the product. */
add_filter(
	'woocommerce_cart_item_thumbnail',
	static function ( $thumb, $cart_item ) {
		if ( ! bar7_active() || empty( $cart_item['product_id'] ) ) {
			return $thumb;
		}
		$product = wc_get_product( $cart_item['product_id'] );
		// The cart table links this image to the product on its own, so it needs the product name as alt text.
		return ( $product && bar7_photo_slug( $product ) ) ? bar7_product_image( $product, '80px', true, $product->get_name() ) : $thumb;
	},
	10,
	2
);
