<?php
/**
 * Shop, category and search listings. The product loop, counts, sorting, price filtering and
 * pagination are WooCommerce's own; this view lays them out.
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$u       = bar7_urls();
$state   = bar7_filter_state();
$base    = bar7_listing_base_url();
$term    = is_product_taxonomy() ? get_queried_object() : null;
$search  = is_search() ? get_search_query() : '';

// The main Shop page is edited in the block editor (Pages → Shop): its heading and the
// "Rovakivo: product catalog" block render here. Categories, sale and search keep the heading below.
$bar7_shop_page = get_post( wc_get_page_id( 'shop' ) );
if ( is_shop() && ! $search && ! $state['sale'] && $bar7_shop_page && has_blocks( $bar7_shop_page->post_content ) ) {
	echo apply_filters( 'the_content', $bar7_shop_page->post_content ); // phpcs:ignore
	return;
}
// The shop's Sale view (/shop/?on_sale=1) is edited the same way, in Pages → Sale.
$bar7_sale_page = bar7_sale_page_id() ? get_post( bar7_sale_page_id() ) : null;
if ( is_shop() && ! $search && $state['sale'] && $bar7_sale_page && 'publish' === $bar7_sale_page->post_status && has_blocks( $bar7_sale_page->post_content ) ) {
	echo apply_filters( 'the_content', $bar7_sale_page->post_content ); // phpcs:ignore
	return;
}

// Heading and introduction.
$eyebrow = __( 'The Rovakivo collection', 'bar7' );
$intro   = __( 'Bags and backpacks for daily routines, travel plans and time outdoors.', 'bar7' );
$photo   = '';
if ( $search ) {
	$eyebrow = __( 'Search', 'bar7' );
	/* translators: %s: search terms */
	$title = sprintf( __( 'Results for “%s”', 'bar7' ), $search );
	$intro = '';
} elseif ( $term instanceof WP_Term ) {
	$eyebrow = __( 'Shop by category', 'bar7' );
	$title   = sprintf( __( '%s backpacks.', 'bar7' ), $term->name );
	$desc    = trim( wp_strip_all_tags( term_description( $term ) ) );
	// The paragraph is the category description (Products → Categories); these are only fallbacks.
	$copy    = array(
		'casual' => __( 'Backpacks for commutes, everyday errands and days around town. Find a style that fits naturally into your daily routine.', 'bar7' ),
		'travel' => __( 'Backpacks for weekends away, city breaks and your next change of scene. Explore a style that fits your plans.', 'bar7' ),
	);
	$intro = $desc ? $desc : ( $copy[ $term->slug ] ?? $intro );
	// Category page photograph: set in bar7_category_photo() (inc/cards.php), next to the tiles' photographs.
	$photo = bar7_category_photo( $term->slug, 'page' ) ? $term->slug : '';
} elseif ( $state['sale'] ) {
	$eyebrow = __( 'On sale now', 'bar7' );
	$title   = __( 'The sale edit.', 'bar7' );
	$intro   = __( 'Backpacks that are reduced right now. Prices shown are the current sale prices.', 'bar7' );
} else {
	$title = __( 'All bags & backpacks.', 'bar7' );
}

$other = null;
if ( $term instanceof WP_Term && in_array( $term->slug, array( 'casual', 'travel' ), true ) ) {
	$other = 'casual' === $term->slug
		? array( 'travel', $u['travel'], __( 'Explore travel backpacks', 'bar7' ) )
		: array( 'casual', $u['casual'], __( 'Explore casual backpacks', 'bar7' ) );
}

if ( $photo && $term instanceof WP_Term ) :
	// Category introduction: copy beside a photograph that fills its column, top to bottom.
	$bar7_img = bar7_category_photo_img( $photo, '', '(min-width: 1100px) 460px, (min-width: 700px) 40vw, calc(100vw - 48px)', true, 'page' );
	?>
<header class="bar7-pagehead bar7-cathero bar7-cathero--<?php echo esc_attr( $term->slug ); ?>" data-intro>
	<div class="bar7-wrap bar7-cathero__grid">
		<div class="bar7-cathero__copy">
			<?php bar7_breadcrumbs(); ?>
			<div class="bar7-cathero__text">
				<p class="bar7-eyebrow" data-intro-item><?php echo esc_html( $eyebrow ); ?></p>
				<h1 class="bar7-h1 bar7-cathero__title" data-intro-item><?php echo esc_html( $title ); ?></h1>
				<?php if ( $intro ) : ?>
					<p class="bar7-lede bar7-cathero__lede" data-intro-item><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
				<?php if ( $other ) : ?>
					<a class="bar7-link bar7-cathero__switch" href="<?php echo esc_url( $other[1] ); ?>" data-intro-item><?php echo esc_html( $other[2] ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>
		<figure class="bar7-cathero__media is-media-clip" data-intro-media>
			<?php echo $bar7_img; // phpcs:ignore ?>
		</figure>
	</div>
</header>
	<?php
	require BAR7_DIR . 'templates/parts/catalog.php';
	return;
endif;

?>
<header class="bar7-pagehead" data-intro>
	<div class="bar7-wrap bar7-pagehead__inner">
		<div class="bar7-pagehead__copy">
			<?php bar7_breadcrumbs(); ?>
			<p class="bar7-eyebrow" data-intro-item><?php echo esc_html( $eyebrow ); ?></p>
			<h1 class="bar7-h1" data-intro-item><?php echo esc_html( $title ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="bar7-lede" data-intro-item><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<?php if ( $search ) : ?>
				<div class="bar7-pagehead__search" data-intro-item><?php bar7_search_form( 'bar7-results-search', __( 'Search again', 'bar7' ) ); ?></div>
			<?php endif; ?>
			<?php if ( $other ) : ?>
				<a class="bar7-link" href="<?php echo esc_url( $other[1] ); ?>" data-intro-item><?php echo esc_html( $other[2] ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>

<?php require BAR7_DIR . 'templates/parts/catalog.php'; ?>
