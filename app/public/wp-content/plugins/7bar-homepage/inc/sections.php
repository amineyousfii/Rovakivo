<?php
/**
 * Data-driven page parts, shared by the page templates and the Rovakivo blocks (inc/blocks.php),
 * so a part looks the same whichever renders it.
 */

defined( 'ABSPATH' ) || exit;

/** Parse "66, 57, 64" into product IDs. */
function bar7_parse_ids( $ids ): array {
	if ( is_array( $ids ) ) {
		return array_values( array_filter( array_map( 'absint', $ids ) ) );
	}
	return array_values( array_filter( array_map( 'absint', preg_split( '/[\s,]+/', (string) $ids ) ) ) );
}

/** Product grid; with $filterable the "All / Casual / Travel" buttons of the same page can filter it. */
function bar7_product_grid( array $ids, bool $filterable = false ): void {
	$products = bar7_products_by_id( $ids );
	if ( ! $products ) {
		return;
	}
	echo '<div class="bar7-grid"' . ( $filterable ? ' data-bar7-filter-grid' : '' ) . ' data-reveal-grid>';
	foreach ( $products as $product ) {
		bar7_product_card( $product );
	}
	echo '</div>';
}

/** Filter buttons for a filterable product grid (only categories that appear in it). */
function bar7_product_tabs( array $ids ): void {
	$cats = array();
	foreach ( bar7_products_by_id( $ids ) as $p ) {
		foreach ( (array) get_the_terms( $p->get_id(), 'product_cat' ) as $t ) {
			if ( $t instanceof WP_Term ) {
				$cats[ $t->slug ] = true;
			}
		}
	}
	if ( count( $cats ) < 2 ) {
		return;
	}
	?>
	<div class="bar7-tabs" role="group" aria-label="<?php esc_attr_e( 'Filter these backpacks', 'bar7' ); ?>" data-bar7-tabs hidden>
		<button type="button" aria-pressed="true" data-filter=""><?php esc_html_e( 'All backpacks', 'bar7' ); ?></button>
		<?php if ( isset( $cats['casual'] ) ) : ?>
			<button type="button" aria-pressed="false" data-filter="casual"><?php esc_html_e( 'Casual', 'bar7' ); ?></button>
		<?php endif; ?>
		<?php if ( isset( $cats['travel'] ) ) : ?>
			<button type="button" aria-pressed="false" data-filter="travel"><?php esc_html_e( 'Travel', 'bar7' ); ?></button>
		<?php endif; ?>
	</div>
	<?php
}

/** Shopping-help strip under the homepage hero. */
function bar7_section_support(): void {
	$u = bar7_urls();
	$items = array(
		array( $u['shop'], 'grid', __( 'Casual & travel backpacks', 'bar7' ), __( 'Browse the full collection', 'bar7' ) ),
		array( $u['sale'], 'tag', __( 'On sale now', 'bar7' ), __( 'See current offers', 'bar7' ) ),
		array( $u['contact'], 'support', __( 'Questions?', 'bar7' ), __( 'Send us a message', 'bar7' ) ),
		array( $u['account'], 'user', __( 'Your account', 'bar7' ), __( 'Orders and details', 'bar7' ) ),
	);
	?>
	<section class="bar7-support" aria-label="<?php esc_attr_e( 'Shopping help', 'bar7' ); ?>">
		<ul class="bar7-wrap bar7-support__list" data-reveal-group>
			<?php foreach ( $items as [ $url, $icon, $title, $text ] ) : ?>
				<?php if ( $url ) : ?>
				<li data-reveal-item>
					<a class="bar7-support__item" href="<?php echo esc_url( $url ); ?>">
						<?php echo bar7_icon( $icon ); // phpcs:ignore ?>
						<span><strong><?php echo esc_html( $title ); ?></strong><span><?php echo esc_html( $text ); ?></span></span>
					</a>
				</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}

/** Sale banner, shown only while products are reduced (with the live count). */
function bar7_section_sale(): void {
	$u       = bar7_urls();
	$on_sale = array_filter(
		array_map( 'wc_get_product', wc_get_product_ids_on_sale() ),
		static fn( $p ) => $p && $p->is_visible() && ! $p->get_parent_id() && ! $p->is_type( 'grouped' )
	);
	$count = count( $on_sale );
	if ( ! $count ) {
		return;
	}
	?>
	<section class="bar7-section bar7-section--tight-top" aria-labelledby="bar7-sale-title">
		<div class="bar7-wrap">
			<div class="bar7-sale" data-reveal-sale>
				<div class="bar7-sale__copy">
					<p class="bar7-eyebrow bar7-eyebrow--ink"><?php esc_html_e( 'On sale now', 'bar7' ); ?></p>
					<h2 id="bar7-sale-title" class="bar7-sale__title"><?php esc_html_e( 'The sale edit.', 'bar7' ); ?><br><?php esc_html_e( 'Good bags. Lower prices.', 'bar7' ); ?></h2>
					<p class="bar7-sale__text">
						<?php
						/* translators: %d: number of backpacks currently on sale */
						echo esc_html( sprintf( _n( '%d backpack is reduced right now.', '%d backpacks are reduced right now.', $count, 'bar7' ), $count ) );
						?>
					</p>
					<a class="bar7-btn bar7-btn--dark" href="<?php echo esc_url( $u['sale'] ); ?>"><?php esc_html_e( 'Shop the sale', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				</div>
				<div class="bar7-sale__art" aria-hidden="true">
					<figure class="bar7-sale__photo bar7-sale__photo--a"><img src="<?php echo bar7_img( 'product-fringilla-400w.webp' ); ?>" width="400" height="534" alt="" loading="lazy" decoding="async"></figure>
					<figure class="bar7-sale__photo bar7-sale__photo--b"><img src="<?php echo bar7_img( 'product-scelerisque-400w.webp' ); ?>" width="400" height="534" alt="" loading="lazy" decoding="async"></figure>
					<span class="bar7-sale__stamp"><small><?php esc_html_e( 'The', 'bar7' ); ?></small><?php esc_html_e( 'Sale', 'bar7' ); ?><small><?php esc_html_e( 'edit', 'bar7' ); ?></small></span>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/** "What our customers say" (edited in Appearance → Homepage testimonials). No review structured data. */
function bar7_section_testimonials(): void {
	$data = bar7_testimonials();
	if ( ! $data['items'] ) {
		return;
	}
	?>
	<section class="bar7-section bar7-quotes" aria-labelledby="bar7-quotes-title">
		<div class="bar7-wrap">
			<div class="bar7-head bar7-quotes__head" data-reveal>
				<div>
					<p class="bar7-eyebrow"><?php esc_html_e( 'Reviews', 'bar7' ); ?></p>
					<h2 id="bar7-quotes-title" class="bar7-h2"><?php esc_html_e( 'What our customers say', 'bar7' ); ?></h2>
				</div>
				<?php if ( $data['sample'] ) : ?>
					<p class="bar7-quotes__sample"><?php esc_html_e( 'Sample testimonials — design preview', 'bar7' ); ?></p>
				<?php endif; ?>
			</div>
			<ul class="bar7-quotes__list" data-reveal-group>
				<?php foreach ( $data['items'] as $i => $item ) : ?>
					<li class="bar7-quote" data-reveal-item>
						<figure class="bar7-quote__inner">
							<?php echo bar7_stars( (int) $item['rating'] ); // phpcs:ignore ?>
							<blockquote class="bar7-quote__text"><p><?php echo esc_html( $item['text'] ); ?></p></blockquote>
							<figcaption class="bar7-quote__person">
								<?php echo bar7_testimonial_avatar( $item, $i, $data['sample'] ); // phpcs:ignore ?>
								<span class="bar7-quote__name"><?php echo esc_html( $item['name'] ); ?></span>
							</figcaption>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}

/** The two collection tiles (Casual, Travel). */
function bar7_collection_tiles( int $heading_level = 2 ): void {
	$u   = bar7_urls();
	$tag = 3 === $heading_level ? 'h3' : 'h2';
	echo '<div class="bar7-cats" data-reveal-group>';
	bar7_category_tile( 'casual', $u['casual'], $tag );
	bar7_category_tile( 'travel', $u['travel'], $tag );
	echo '</div>';
}

/** The contact form (WPForms). */
function bar7_contact_form( int $form_id = 506 ): void {
	if ( 'publish' === get_post_status( $form_id ) && shortcode_exists( 'wpforms' ) ) {
		echo do_shortcode( '[wpforms id="' . (int) $form_id . '" title="false" description="false"]' ); // phpcs:ignore
	} else {
		echo '<p>' . esc_html__( 'The contact form is not available right now.', 'bar7' ) . '</p>';
	}
}

/** "Find it faster" links on the contact page. */
function bar7_contact_help_links(): void {
	$u     = bar7_urls();
	$items = array(
		array( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'orders' ) : $u['account'], 'box', __( 'Your orders', 'bar7' ), __( 'Check the status of an order', 'bar7' ) ),
		array( $u['account'], 'user', __( 'Your account', 'bar7' ), __( 'Addresses and account details', 'bar7' ) ),
		array( $u['shop'], 'grid', __( 'The collection', 'bar7' ), __( 'Casual and travel backpacks', 'bar7' ) ),
		array( $u['privacy'], 'mail', __( 'Privacy policy', 'bar7' ), __( 'How we use your details', 'bar7' ) ),
	);
	echo '<ul class="bar7-helplist">';
	foreach ( $items as [ $url, $icon, $title, $text ] ) {
		if ( ! $url ) {
			continue;
		}
		echo '<li data-reveal-item><a href="' . esc_url( $url ) . '">' . bar7_icon( $icon ) . '<span><strong>' . esc_html( $title ) . '</strong><span>' . esc_html( $text ) . '</span></span></a></li>'; // phpcs:ignore
	}
	echo '</ul>';
}

/** The My Account page heading: "Sign in" / "Reset your password" for visitors, the section name for customers. */
function bar7_account_title(): string {
	global $wp;
	if ( ! is_user_logged_in() ) {
		return isset( $wp->query_vars['lost-password'] ) ? __( 'Reset your password', 'bar7' ) : __( 'Sign in', 'bar7' );
	}
	$endpoint = WC()->query->get_current_endpoint();
	$title    = $endpoint ? WC()->query->get_endpoint_title( $endpoint ) : '';
	return $title ? (string) $title : __( 'My account', 'bar7' );
}
