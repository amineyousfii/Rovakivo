<?php
/**
 * Pages: cart, checkout, order confirmation, account, policies and any other page.
 * The page's own content (including WooCommerce's shortcodes) is rendered unchanged.
 */

defined( 'ABSPATH' ) || exit;

global $wp;

$kind = 'page';
if ( is_cart() ) {
	$kind = 'cart';
} elseif ( is_checkout() ) {
	$kind = ! empty( $wp->query_vars['order-received'] ) ? 'order' : 'checkout';
} elseif ( is_account_page() ) {
	$kind = 'account';
}

$titles = array(
	'cart'     => __( 'Your cart', 'bar7' ),
	'checkout' => __( 'Checkout', 'bar7' ),
	'order'    => __( 'Order received', 'bar7' ),
);

while ( have_posts() ) :
	the_post();

	// Cart, Checkout and My Account are edited in the block editor (heading, intro and links around WooCommerce's shortcode):
	// what is saved there is what is shown here. The order confirmation keeps its own heading below.
	$bar7_designed = in_array( $kind, array( 'cart', 'checkout', 'order', 'account' ), true ) && str_contains( (string) get_post()->post_content, 'bar7-pagehead' );
	if ( $bar7_designed && 'order' !== $kind ) {
		the_content();
		continue;
	}

	// A page saved with Elementor: its own layout, full width, inside the site header and footer.
	if ( 'page' === $kind && function_exists( 'bar7_is_elementor_page' ) && bar7_is_elementor_page( get_the_ID() ) ) {
		echo '<div class="bar7-elementor">';
		the_content();
		echo '</div>';
		continue;
	}

	$title = $titles[ $kind ] ?? get_the_title();

	if ( 'account' === $kind ) {
		$title = bar7_account_title();
	}

	$intro = array(
		'cart'     => __( 'Review your backpacks, update quantities, then check out.', 'bar7' ),
		'checkout' => __( 'A few details and your backpack is on its way.', 'bar7' ),
		'order'    => __( 'Thank you. Your order details are below.', 'bar7' ),
	)[ $kind ] ?? '';
	if ( 'cart' === $kind && WC()->cart && WC()->cart->is_empty() ) {
		$intro = '';
	}
	?>
	<header class="bar7-pagehead bar7-pagehead--compact" data-intro>
		<div class="bar7-wrap">
			<?php bar7_breadcrumbs(); ?>
			<h1 class="bar7-h1" data-intro-item><?php echo esc_html( wp_strip_all_tags( $title ) ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="bar7-lede" data-intro-item><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<div class="bar7-wrap bar7-page bar7-page--<?php echo esc_attr( $kind ); ?>" data-intro-body>
		<div class="<?php echo 'page' === $kind ? 'bar7-prose bar7-prose--page' : 'bar7-woo'; ?>">
			<?php
			if ( $bar7_designed ) {
				// Order confirmation: only WooCommerce's output, not the Checkout page's heading blocks.
				echo do_shortcode( '[woocommerce_checkout]' ); // phpcs:ignore
			} else {
				the_content();
			}
			?>
		</div>
		<?php if ( 'cart' === $kind && WC()->cart && ! WC()->cart->is_empty() ) : ?>
			<a class="bar7-link bar7-cart-continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo bar7_icon( 'arrow-left' ); // phpcs:ignore ?><?php esc_html_e( 'Continue shopping', 'bar7' ); ?></a>
		<?php elseif ( 'order' === $kind ) : ?>
			<div class="bar7-order-next">
				<a class="bar7-btn bar7-btn--yellow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a class="bar7-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'View your orders', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
endwhile;
