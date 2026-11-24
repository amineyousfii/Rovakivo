<?php
/**
 * Product listing: filters, sorting, count, grid, pagination and empty states, for the main product query.
 * Used by the listing template and by the "Rovakivo: product catalog" block on the Shop page.
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$u       = bar7_urls();
$state   = bar7_filter_state();
$base    = bar7_listing_base_url();
$search  = is_search() ? get_search_query() : '';
$total   = (int) $wp_query->found_posts;
$filters = ( '' !== $state['min'] || '' !== $state['max'] || $state['sale'] );

$bar7_per_page   = (int) $wp_query->get( "posts_per_page" );
$bar7_paged      = max( 1, (int) get_query_var( "paged" ) );
$bar7_pages      = (int) $wp_query->max_num_pages;
$count_text = sprintf( _n( '%d backpack', '%d backpacks', $total, 'bar7' ), $total );
if ( $bar7_pages > 1 && $bar7_per_page > 0 ) {
	/* translators: 1: first item, 2: last item, 3: total */
	$count_text = sprintf( __( 'Showing %1$d–%2$d of %3$d backpacks', 'bar7' ), ( $bar7_paged - 1 ) * $bar7_per_page + 1, min( $total, $bar7_paged * $bar7_per_page ), $total );
}
wc_set_loop_prop( "is_paginated", true );
wc_set_loop_prop( "total_pages", $bar7_pages );
wc_set_loop_prop( "current_page", $bar7_paged );
wc_set_loop_prop( "total", $total );
wc_set_loop_prop( "per_page", $bar7_per_page );
?>
<div class="bar7-wrap bar7-listing">
	<?php woocommerce_output_all_notices(); ?>

	<aside class="bar7-listing__aside" aria-label="<?php esc_attr_e( 'Filters', 'bar7' ); ?>">
		<?php bar7_filter_panel( 'side', $base ); ?>
	</aside>

	<div class="bar7-listing__main">
		<div class="bar7-toolbar">
			<button class="bar7-btn bar7-btn--outline bar7-toolbar__filters" type="button" aria-controls="bar7-filterdrawer" aria-expanded="false" data-bar7-open="bar7-filterdrawer">
				<?php echo bar7_icon( 'filter' ); // phpcs:ignore ?><?php esc_html_e( 'Filters', 'bar7' ); ?><?php if ( $filters ) : ?><span class="bar7-dot" aria-label="<?php esc_attr_e( '(active)', 'bar7' ); ?>"></span><?php endif; ?>
			</button>
			<p class="bar7-toolbar__count" aria-live="polite"><?php echo esc_html( $count_text ); ?></p>
			<?php if ( $total > 1 ) { woocommerce_catalog_ordering(); } ?>
		</div>

		<?php bar7_active_filters( $base ); ?>

		<?php if ( have_posts() ) : ?>
			<ul class="bar7-grid bar7-grid--listing products" data-reveal-grid>
				<?php
				$i = 0;
				while ( have_posts() ) {
					the_post();
					$product = wc_get_product( get_the_ID() );
					if ( $product ) {
						bar7_product_card( $product, $i > 3, 'li' );
						$i++;
					}
				}
				?>
			</ul>
			<?php if ( $bar7_pages > 1 ) : ?>
				<nav class="woocommerce-pagination" aria-label="<?php esc_attr_e( 'Products pagination', 'bar7' ); ?>">
					<?php
					// WordPress's own links; the theme replaces WooCommerce's pagination with markup of its own.
					echo paginate_links( // phpcs:ignore
						array(
							'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
							'format'    => '',
							'current'   => $bar7_paged,
							'total'     => $bar7_pages,
							'type'      => 'list',
							'end_size'  => 2,
							'mid_size'  => 1,
							'prev_text' => bar7_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'bar7' ) . '</span>',
							'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'bar7' ) . '</span>' . bar7_icon( 'arrow' ),
						)
					);
					?>
				</nav>
			<?php endif; ?>
		<?php else : ?>
			<div class="bar7-empty" data-reveal>
				<?php echo bar7_icon( 'search' ); // phpcs:ignore ?>
				<?php if ( $state['sale'] && '' === $state['min'] && '' === $state['max'] && ! $search ) : ?>
					<?php $bar7_cat = is_product_taxonomy() ? get_queried_object() : null; ?>
					<h2 class="bar7-h3"><?php echo $bar7_cat ? esc_html( sprintf( __( 'No %s backpacks are on sale right now.', 'bar7' ), strtolower( $bar7_cat->name ) ) ) : esc_html__( 'Nothing is on sale right now.', 'bar7' ); ?></h2>
					<p><?php esc_html_e( 'Reduced backpacks appear here as soon as an offer starts. In the meantime, browse the full collection.', 'bar7' ); ?></p>
					<a class="bar7-btn bar7-btn--dark" href="<?php echo esc_url( $bar7_cat ? $base : $u['shop'] ); ?>"><?php echo $bar7_cat ? esc_html( sprintf( __( 'See all %s backpacks', 'bar7' ), strtolower( $bar7_cat->name ) ) ) : esc_html__( 'Shop all backpacks', 'bar7' ); ?></a>
				<?php elseif ( $filters ) : ?>
					<h2 class="bar7-h3"><?php esc_html_e( 'No backpacks match these filters.', 'bar7' ); ?></h2>
					<p><?php esc_html_e( 'Widen the price range or remove a filter to see more.', 'bar7' ); ?></p>
					<a class="bar7-btn bar7-btn--dark" href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'Clear all filters', 'bar7' ); ?></a>
				<?php elseif ( $search ) : ?>
					<h2 class="bar7-h3"><?php printf( esc_html__( 'Nothing found for “%s”.', 'bar7' ), esc_html( $search ) ); ?></h2>
					<p><?php esc_html_e( 'Check the spelling, try a shorter word, or browse by category.', 'bar7' ); ?></p>
					<div class="bar7-empty__actions">
						<a class="bar7-btn bar7-btn--dark" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Shop all backpacks', 'bar7' ); ?></a>
						<a class="bar7-link" href="<?php echo esc_url( $u['casual'] ); ?>"><?php esc_html_e( 'Casual', 'bar7' ); ?></a>
						<a class="bar7-link" href="<?php echo esc_url( $u['travel'] ); ?>"><?php esc_html_e( 'Travel', 'bar7' ); ?></a>
					</div>
				<?php else : ?>
					<h2 class="bar7-h3"><?php esc_html_e( 'No backpacks here yet.', 'bar7' ); ?></h2>
					<p><?php esc_html_e( 'New pieces are on the way. In the meantime, browse the full collection.', 'bar7' ); ?></p>
					<a class="bar7-btn bar7-btn--dark" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Shop all backpacks', 'bar7' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="bar7-drawer bar7-drawer--left bar7-filterdrawer" id="bar7-filterdrawer" hidden>
	<div class="bar7-drawer__backdrop" data-bar7-close></div>
	<div class="bar7-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="bar7-filter-title" data-lenis-prevent>
		<div class="bar7-drawer__head">
			<h2 id="bar7-filter-title" class="bar7-cartpanel__title"><?php esc_html_e( 'Filters', 'bar7' ); ?></h2>
			<button class="bar7-iconbtn" type="button" data-bar7-close>
				<?php echo bar7_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close filters', 'bar7' ); ?></span>
			</button>
		</div>
		<?php bar7_filter_panel( 'drawer', $base ); ?>
	</div>
</div>
