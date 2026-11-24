<?php
/**
 * Announcement bar, header, search panel, menu drawer and cart drawer.
 */

defined( 'ABSPATH' ) || exit;

$u        = bar7_urls();

// Header links come from Appearance → Menus ("Rovakivo: header menu"); see inc/menus.php.
$nav     = bar7_header_links();
$current = bar7_current_section_url();

$GLOBALS['bar7_logo'] = static function ( string $class ): void {
	$light = false !== strpos( $class, 'bar7-logo--light' );
	$file  = $light ? 'rovakivo-logo-white.svg' : 'rovakivo-logo.svg';
	?>
	<a class="bar7-logo <?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<img src="<?php echo esc_url( BAR7_URL . 'assets/brand/' . $file ); ?>" width="243" height="44" alt="<?php esc_attr_e( 'Rovakivo home', 'bar7' ); ?>">
	</a>
	<?php
};
$bar7_logo = $GLOBALS['bar7_logo'];

$nav_items = static function ( string $context = 'bar' ) use ( $nav, $current ): void {
	foreach ( $nav as $i => $link ) {
		$children = $link['children'] ?? array();
		$is_here  = bar7_same_url( $link['url'], $current );
		$in_here  = (bool) array_filter( $children, static fn( $c ) => bar7_same_url( $c['url'], $current ) );
		$classes  = array_filter( array( $children ? 'has-sub' : '', ( $is_here || $in_here ) ? 'is-ancestor' : '' ) );
		echo '<li' . ( $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '' ) . '>';
		printf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $link['url'] ),
			$link['sale'] ? ' class="is-sale"' : '',
			$is_here ? ' aria-current="page"' : '',
			esc_html( $link['title'] )
		);
		if ( $children ) {
			$sub_id = 'bar7-' . $context . '-sub-' . $i;
			if ( 'bar' === $context ) {
				/* translators: %s: menu item, e.g. Shop */
				printf(
					'<button type="button" class="bar7-nav__toggle" aria-expanded="false" aria-controls="%1$s"><span class="screen-reader-text">%2$s</span>%3$s</button>',
					esc_attr( $sub_id ),
					esc_html( sprintf( __( '%s: show sections', 'bar7' ), $link['title'] ) ),
					bar7_icon( 'chevron' ) // phpcs:ignore
				);
			}
			echo '<ul class="' . ( 'bar' === $context ? 'bar7-nav__sub' : 'bar7-drawer__sub' ) . '" id="' . esc_attr( $sub_id ) . '">';
			foreach ( $children as $child ) {
				printf(
					'<li><a href="%1$s"%2$s%3$s>%4$s</a></li>',
					esc_url( $child['url'] ),
					$child['sale'] ? ' class="is-sale"' : '',
					bar7_same_url( $child['url'], $current ) ? ' aria-current="page"' : '',
					esc_html( $child['title'] )
				);
			}
			echo '</ul>';
		}
		echo '</li>';
	}
};

// The drawer's extra links skip Contact when the menu already has it.
$bar7_menu_urls = array();
foreach ( $nav as $link ) {
	$bar7_menu_urls[] = $link['url'];
	foreach ( $link['children'] ?? array() as $child ) {
		$bar7_menu_urls[] = $child['url'];
	}
}
$bar7_menu_has_contact = $u['contact'] && (bool) array_filter( $bar7_menu_urls, static fn( $x ) => bar7_same_url( $x, $u['contact'] ) );
?>
<div class="bar7-announce">
	<div class="bar7-wrap bar7-announce__inner">
		<span class="bar7-announce__brand"><?php esc_html_e( 'Rovakivo', 'bar7' ); ?></span>
		<p class="bar7-announce__text"><?php esc_html_e( 'For every day. For wherever’s next.', 'bar7' ); ?></p>
		<a class="bar7-announce__link" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Explore the collection', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</div>

<header class="bar7-header" data-bar7-header>
	<div class="bar7-wrap bar7-header__inner">
		<button class="bar7-iconbtn bar7-header__menu" type="button" aria-expanded="false" aria-controls="bar7-drawer" data-bar7-open="bar7-drawer">
			<?php echo bar7_icon( 'menu' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'bar7' ); ?></span>
		</button>

		<?php $bar7_logo( 'bar7-header__logo' ); ?>

		<nav class="bar7-nav" aria-label="<?php esc_attr_e( 'Main', 'bar7' ); ?>">
			<ul><?php $nav_items(); ?></ul>
		</nav>

		<div class="bar7-header__tools">
			<button class="bar7-iconbtn" type="button" aria-expanded="false" aria-controls="bar7-search" data-bar7-search-toggle>
				<?php echo bar7_icon( 'search' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Search products', 'bar7' ); ?></span>
			</button>
			<a class="bar7-iconbtn bar7-hide-sm" href="<?php echo esc_url( $u['account'] ); ?>"<?php echo is_account_page() ? ' aria-current="page"' : ''; ?>>
				<?php echo bar7_icon( 'user' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Your account', 'bar7' ); ?></span>
			</a>
			<a class="bar7-iconbtn bar7-cart" href="<?php echo esc_url( $u['cart'] ); ?>"<?php echo ( is_cart() || is_checkout() ) ? '' : ' data-bar7-open="bar7-cartdrawer" aria-haspopup="dialog" aria-controls="bar7-cartdrawer"'; ?><?php echo is_cart() ? ' aria-current="page"' : ''; ?>>
				<?php echo bar7_icon( 'bag' ); // phpcs:ignore ?><span class="bar7-cart-wrap"><?php echo bar7_cart_count_html(); // phpcs:ignore ?></span>
			</a>
		</div>
	</div>

	<div class="bar7-search" id="bar7-search" hidden>
		<div class="bar7-wrap"><?php bar7_search_form( 'bar7-search-input' ); ?></div>
	</div>
</header>

<?php if ( empty( $GLOBALS['bar7_chrome_preview'] ) ) : // The editor preview of the header has no drawers. ?>
<div class="bar7-drawer bar7-drawer--left" id="bar7-drawer" hidden>
	<div class="bar7-drawer__backdrop" data-bar7-close></div>
	<div class="bar7-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'bar7' ); ?>" data-lenis-prevent>
		<div class="bar7-drawer__head">
			<?php $bar7_logo( '' ); ?>
			<button class="bar7-iconbtn" type="button" data-bar7-close>
				<?php echo bar7_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'bar7' ); ?></span>
			</button>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Main', 'bar7' ); ?>">
			<ul class="bar7-drawer__nav"><?php $nav_items( 'drawer' ); ?></ul>
			<ul class="bar7-drawer__links">
				<li><a href="<?php echo esc_url( $u['account'] ); ?>"><?php echo bar7_icon( 'user' ); // phpcs:ignore ?><?php esc_html_e( 'Your account', 'bar7' ); ?></a></li>
				<li><a href="<?php echo esc_url( $u['cart'] ); ?>"><?php echo bar7_icon( 'bag' ); // phpcs:ignore ?><?php esc_html_e( 'Cart', 'bar7' ); ?></a></li>
				<?php if ( $u['contact'] && ! $bar7_menu_has_contact ) : ?>
					<li><a href="<?php echo esc_url( $u['contact'] ); ?>"><?php echo bar7_icon( 'support' ); // phpcs:ignore ?><?php esc_html_e( 'Contact us', 'bar7' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</div>

<?php if ( ! is_cart() && ! is_checkout() ) : ?>
<div class="bar7-drawer bar7-drawer--right" id="bar7-cartdrawer" hidden>
	<div class="bar7-drawer__backdrop" data-bar7-close></div>
	<div class="bar7-drawer__panel bar7-cartpanel" role="dialog" aria-modal="true" aria-labelledby="bar7-cart-title">
		<div class="bar7-drawer__head">
			<h2 id="bar7-cart-title" class="bar7-cartpanel__title"><?php esc_html_e( 'Your cart', 'bar7' ); ?> <span class="bar7-drawer-count"></span></h2>
			<button class="bar7-iconbtn" type="button" data-bar7-close>
				<?php echo bar7_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close cart', 'bar7' ); ?></span>
			</button>
		</div>
		<div class="widget_shopping_cart_content" data-lenis-prevent><?php woocommerce_mini_cart(); ?></div>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>
