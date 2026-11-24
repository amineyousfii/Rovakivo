<?php
/**
 * Shared helpers: URLs, icons, images, breadcrumbs.
 */

defined( 'ABSPATH' ) || exit;

/** Whether the storefront design should handle this request (front end, or front-end AJAX). */
function bar7_active(): bool {
	if ( ! function_exists( 'WC' ) ) {
		return false;
	}
	if ( is_admin() && ! wp_doing_ajax() ) {
		return false;
	}
	// Leave the page builder's own editor and preview alone.
	if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['elementor_library'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}
	return true;
}

function bar7_img( string $file ): string {
	return esc_url( BAR7_URL . 'assets/img/' . $file );
}

/** Real destinations, resolved once per request. */
function bar7_urls(): array {
	static $urls = null;
	if ( null !== $urls ) {
		return $urls;
	}
	$shop    = wc_get_page_permalink( 'shop' );
	$casual  = get_term_by( 'slug', 'casual', 'product_cat' );
	$travel  = get_term_by( 'slug', 'travel', 'product_cat' );
	$about   = get_page_by_path( 'about' );
	$contact = get_page_by_path( 'contact' );

	$privacy = get_privacy_policy_url();
	if ( ! $privacy ) {
		$page    = get_page_by_path( 'privacy-policy-2' );
		$privacy = ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
	}

	$urls = array(
		'home'     => home_url( '/' ),
		'shop'     => $shop,
		'sale'     => add_query_arg( 'on_sale', '1', $shop ),
		'casual'   => $casual ? get_term_link( $casual ) : $shop,
		'travel'   => $travel ? get_term_link( $travel ) : $shop,
		'cart'     => wc_get_cart_url(),
		'checkout' => wc_get_checkout_url(),
		'account'  => wc_get_page_permalink( 'myaccount' ),
		'about'    => ( $about && 'publish' === $about->post_status ) ? get_permalink( $about ) : '',
		'contact'  => ( $contact && 'publish' === $contact->post_status ) ? get_permalink( $contact ) : '',
		'privacy'  => $privacy,
	);
	return $urls;
}

/** Inline SVG icons (decorative). */
function bar7_icon( string $name ): string {
	$paths = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'arrow-up' => '<path d="M7 17 17 7M9 7h8v8"/>',
		'arrow-down' => '<path d="M12 5v14M6 13l6 6 6-6"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c1.2-3.8 4-5.5 7.5-5.5s6.3 1.7 7.5 5.5"/>',
		'bag'      => '<path d="M5 8h14l-1 12.5H6L5 8Z"/><path d="M9 10V6.5a3 3 0 0 1 6 0V10"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'    => '<path d="m6 6 12 12M18 6 6 18"/>',
		'grid'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1"/>',
		'tag'      => '<path d="M3.5 12.2V4.5a1 1 0 0 1 1-1h7.7l8.3 8.3-8.7 8.7-8.3-8.3Z"/><circle cx="8" cy="8" r="1.4"/>',
		'support'  => '<path d="M4.5 14v-2a7.5 7.5 0 0 1 15 0v2"/><rect x="3.5" y="13" width="4" height="6" rx="1.5"/><rect x="16.5" y="13" width="4" height="6" rx="1.5"/><path d="M18.5 19c0 1.2-1.5 2-4 2"/>',
		'quote'    => '<path d="M10 7H6.5A2.5 2.5 0 0 0 4 9.5V13h5v5H4M20 7h-3.5A2.5 2.5 0 0 0 14 9.5V13h5v5h-5"/>',
		'filter'   => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'minus'    => '<path d="M6 12h12"/>',
		'plus'     => '<path d="M12 6v12M6 12h12"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'chevron'  => '<path d="m6 9 6 6 6-6"/>',
		'mail'     => '<rect x="3.5" y="5.5" width="17" height="13" rx="1.5"/><path d="m4 7 8 6 8-6"/>',
		'box'      => '<path d="M3.5 7.5 12 3l8.5 4.5v9L12 21l-8.5-4.5v-9Z"/><path d="M3.5 7.5 12 12l8.5-4.5M12 12v9"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="bar7-icon bar7-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/** Supplied photo slug for a catalog product, or '' when the pack has none for it. */
function bar7_photo_slug( WC_Product $product ): string {
	return BAR7_PHOTOS[ $product->get_id() ] ?? ( BAR7_PHOTOS[ $product->get_parent_id() ] ?? '' );
}

/** Breadcrumb trail built from WooCommerce's own breadcrumb logic. */
function bar7_breadcrumbs(): void {
	if ( is_front_page() || ! class_exists( 'WC_Breadcrumb' ) ) {
		return;
	}
	$trail = new WC_Breadcrumb();
	$trail->add_crumb( __( 'Home', 'bar7' ), home_url( '/' ) );
	$crumbs = $trail->generate();
	// The Sale collection (?on_sale=1) is a view of the shop or a category: Shop/Category becomes a link and "Sale" the current crumb.
	if ( ! empty( $_GET['on_sale'] ) && ( is_shop() || is_product_taxonomy() ) && ! is_search() && count( $crumbs ) >= 2 ) { // phpcs:ignore WordPress.Security.NonceVerification
		$last = count( $crumbs ) - 1;
		if ( empty( $crumbs[ $last ][1] ) ) {
			$link                = is_product_taxonomy() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
			$crumbs[ $last ][1] = is_wp_error( $link ) ? '' : $link;
		}
		$sale_page = function_exists( 'bar7_sale_page_id' ) ? bar7_sale_page_id() : 0;
		$crumbs[]  = array( $sale_page ? get_the_title( $sale_page ) : __( 'Sale', 'bar7' ), '' );
	}
	// My account: a signed-out visitor opening a section (e.g. /my-account/orders/) is shown the sign-in form, so
	// the trail ends at "My account"; the reset-password view is named like its heading.
	if ( function_exists( 'is_account_page' ) && is_account_page() && WC()->query ) {
		$endpoint = WC()->query->get_current_endpoint();
		if ( 'lost-password' === $endpoint ) {
			$crumbs[ count( $crumbs ) - 1 ][0] = __( 'Reset password', 'bar7' );
		} elseif ( $endpoint && ! is_user_logged_in() && count( $crumbs ) > 2 ) {
			array_pop( $crumbs );
		}
	}
	if ( count( $crumbs ) < 2 ) {
		return;
	}
	echo '<nav class="bar7-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'bar7' ) . '" data-intro-item><ol>';
	$last = count( $crumbs ) - 1;
	foreach ( $crumbs as $i => $crumb ) {
		echo '<li>';
		if ( $i < $last && ! empty( $crumb[1] ) ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/** The product search form, shared by header, search results, 404 and empty states. */
function bar7_search_form( string $id, string $label = '', string $class = '' ): void {
	$label = $label ? $label : __( 'Search backpacks', 'bar7' );
	?>
	<form class="bar7-searchform <?php echo esc_attr( $class ); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="<?php echo esc_attr( $id ); ?>" class="bar7-searchform__label"><?php echo esc_html( $label ); ?></label>
		<div class="bar7-searchform__row">
			<input id="<?php echo esc_attr( $id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off" placeholder="<?php esc_attr_e( 'Search by product name', 'bar7' ); ?>">
			<input type="hidden" name="post_type" value="product">
			<button class="bar7-btn bar7-btn--dark" type="submit"><?php esc_html_e( 'Search', 'bar7' ); ?></button>
		</div>
	</form>
	<?php
}

/** Admin-only reminder about content that still needs the store's real information. */
function bar7_admin_note( string $text ): void {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	echo '<p class="bar7-admin-note" role="note"><strong>' . esc_html__( 'Only visible to site editors:', 'bar7' ) . '</strong> ' . esc_html( $text ) . '</p>';
}
