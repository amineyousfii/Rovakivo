<?php
/**
 * The shared product card, used on every product grid.
 */

defined( 'ABSPATH' ) || exit;

/** Star rating, only when the product has real ratings. */
function bar7_rating( WC_Product $product, bool $with_count = false ): string {
	$count = (int) $product->get_rating_count();
	$avg   = (float) $product->get_average_rating();
	if ( $count < 1 || $avg <= 0 ) {
		return '';
	}
	$label = sprintf(
		/* translators: 1: average rating, 2: number of ratings */
		_n( 'Rated %1$s out of 5 from %2$d rating', 'Rated %1$s out of 5 from %2$d ratings', $count, 'bar7' ),
		wc_format_decimal( $avg, 1, true ),
		$count
	);
	$pct  = max( 0, min( 100, $avg / 5 * 100 ) );
	$html = '<span class="bar7-stars" role="img" aria-label="' . esc_attr( $label ) . '"><span class="bar7-stars__fill" style="width:' . esc_attr( (string) $pct ) . '%"></span></span>';
	if ( $with_count ) {
		$html .= '<span class="bar7-rating__text" aria-hidden="true">' . esc_html( wc_format_decimal( $avg, 1, true ) ) . ' · ' . esc_html( sprintf( _n( '%d review', '%d reviews', $count, 'bar7' ), $count ) ) . '</span>';
	}
	return $html;
}

/** First real category of a product. */
function bar7_primary_category( WC_Product $product ): ?WP_Term {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	foreach ( $terms as $term ) {
		if ( 'uncategorized' !== $term->slug ) {
			return $term;
		}
	}
	return null;
}

/** Product image markup: supplied WebP when the pack has one, otherwise the WooCommerce image. */
function bar7_product_image( WC_Product $product, string $sizes, bool $lazy, string $alt = '' ): string {
	$slug    = bar7_photo_slug( $product );
	$loading = $lazy ? 'loading="lazy" decoding="async"' : 'decoding="async" fetchpriority="high"';
	if ( $slug ) {
		return sprintf(
			'<img src="%1$s" srcset="%2$s 400w, %1$s 800w" sizes="%3$s" width="800" height="1067" alt="%4$s" %5$s>',
			bar7_img( 'product-' . $slug . '.webp' ),
			bar7_img( 'product-' . $slug . '-400w.webp' ),
			esc_attr( $sizes ),
			esc_attr( $alt ),
			$loading
		);
	}
	$image_id = $product->get_image_id();
	if ( $image_id ) {
		return wp_get_attachment_image(
			(int) $image_id,
			'woocommerce_single',
			false,
			array(
				'alt'      => $alt,
				'sizes'    => $sizes,
				'loading'  => $lazy ? 'lazy' : 'eager',
				'decoding' => 'async',
			)
		);
	}
	return wc_placeholder_img( 'woocommerce_single', array( 'alt' => $alt ) );
}

/**
 * One product card.
 *
 * @param WC_Product $product From the live catalog.
 * @param bool       $lazy    Lazy-load the photo.
 * @param string     $tag     Wrapper element: "article", or "li" inside WooCommerce lists.
 */
function bar7_product_card( WC_Product $product, bool $lazy = true, string $tag = 'article' ): void {
	$url      = get_permalink( $product->get_id() );
	$name     = $product->get_name();
	$category = bar7_primary_category( $product );
	$cats     = get_the_terms( $product->get_id(), 'product_cat' );
	$cat_slug = ( $cats && ! is_wp_error( $cats ) ) ? implode( ' ', wp_list_pluck( $cats, 'slug' ) ) : '';
	$simple   = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
	$tag      = in_array( $tag, array( 'article', 'li' ), true ) ? $tag : 'article';
	$classes  = 'li' === $tag ? implode( ' ', wc_get_product_class( 'bar7-card', $product ) ) : 'bar7-card';
	?>
	<<?php echo $tag; // phpcs:ignore ?> class="<?php echo esc_attr( $classes ); ?>" data-cats="<?php echo esc_attr( $cat_slug ); ?>">
		<a class="bar7-card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo bar7_product_image( $product, '(min-width: 1100px) 302px, (min-width: 700px) 45vw, 46vw', $lazy ); // phpcs:ignore ?>
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="bar7-badge"><?php esc_html_e( 'Sale', 'bar7' ); ?></span>
			<?php endif; ?>
			<?php if ( ! $product->is_in_stock() ) : ?>
				<span class="bar7-badge bar7-badge--muted"><?php esc_html_e( 'Out of stock', 'bar7' ); ?></span>
			<?php endif; ?>
		</a>
		<div class="bar7-card__body">
			<div class="bar7-card__meta">
				<?php if ( $category ) : ?>
					<span class="bar7-card__cat"><?php echo esc_html( sprintf( __( '%s backpack', 'bar7' ), $category->name ) ); ?></span>
				<?php endif; ?>
				<?php echo bar7_rating( $product ); // phpcs:ignore ?>
			</div>
			<h3 class="bar7-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
			<p class="bar7-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
			<?php if ( $simple ) : ?>
				<a
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					data-quantity="1"
					data-product_id="<?php echo esc_attr( (string) $product->get_id() ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					class="bar7-card__action add_to_cart_button ajax_add_to_cart"
					aria-label="<?php echo esc_attr( sprintf( __( 'Add “%s” to your cart', 'bar7' ), $name ) ); ?>"
					rel="nofollow"><span class="bar7-card__action-text"><?php esc_html_e( 'Add to cart', 'bar7' ); ?></span><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php elseif ( $product->is_type( 'external' ) ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="bar7-card__action"><span class="bar7-card__action-text"><?php esc_html_e( 'View product', 'bar7' ); ?></span><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php else : ?>
				<a
					href="<?php echo esc_url( $url ); ?>"
					class="bar7-card__action"
					aria-label="<?php echo esc_attr( $product->is_in_stock() ? sprintf( __( 'Choose options for “%s”', 'bar7' ), $name ) : sprintf( __( 'View “%s”', 'bar7' ), $name ) ); ?>"><span class="bar7-card__action-text"><?php echo $product->is_in_stock() ? esc_html__( 'Choose options', 'bar7' ) : esc_html__( 'View product', 'bar7' ); ?></span><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</<?php echo $tag; // phpcs:ignore ?>>
	<?php
}

/**
 * Catalog products by exact name, in the order given.
 *
 * @param string[] $names
 * @return WC_Product[]
 */
function bar7_products_by_name( array $names ): array {
	$out = array();
	foreach ( $names as $name ) {
		$found = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'title'          => $name,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$product = $found ? wc_get_product( $found[0] ) : null;
		if ( $product ) {
			$out[] = $product;
		}
	}
	return $out;
}

/**
 * The Casual / Travel photographs, set in one place: 'page' = the category page's opening photo, 'tile' = the
 * collection tiles on Home and About (Media → "Casual section" / "Travel section").
 * [ Media Library ID, alt text, fallback WebP in the plugin, fallback alt ] — the fallback only shows if the
 * Media Library photo is ever removed.
 */
function bar7_category_photo( string $slug, string $use = 'tile' ): array {
	$photos = array(
		'page' => array(
			'casual' => array( 74, __( 'A man outdoors carrying a bright yellow backpack with leather straps.', 'bar7' ), 'category-casual-yellow.webp', __( 'A man outdoors carrying a bright yellow backpack with leather straps.', 'bar7' ) ),
			'travel' => array( 151, __( 'A hiker with a tan backpack looks out over a mountain valley.', 'bar7' ), 'category-travel-hiker.webp', __( 'A hiker with a tan backpack looks out over a mountain valley.', 'bar7' ) ),
		),
		'tile' => array(
			'casual' => array( 668, __( 'A man carries a tan canvas backpack with leather straps along a quiet, tree-lined road.', 'bar7' ), 'category-casual-yellow.webp', __( 'A man outdoors carrying a bright yellow backpack with dark straps.', 'bar7' ) ),
			'travel' => array( 669, __( 'A woman with a light-blue backpack sits on a rock, looking out into the fog.', 'bar7' ), 'category-travel-hiker.webp', __( 'A hiker with a tan backpack looks out over a mountain valley.', 'bar7' ) ),
		),
	);
	return $photos[ $use ][ $slug ] ?? array();
}

/** The category photograph as an <img>: the Media Library image (responsive sizes), or the plugin fallback. */
function bar7_category_photo_img( string $slug, string $class, string $sizes, bool $priority, string $use = 'tile' ): string {
	$photo = bar7_category_photo( $slug, $use );
	if ( ! $photo ) {
		return '';
	}
	[ $id, $alt, $fallback, $fallback_alt ] = $photo;
	if ( wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image(
			$id,
			'full',
			false,
			array_filter(
				array(
					'class'         => $class,
					'alt'           => $alt,
					'sizes'         => $sizes,
					'loading'       => $priority ? 'eager' : 'lazy',
					'fetchpriority' => $priority ? 'high' : '',
					'decoding'      => 'async',
				)
			)
		);
	}
	return '<img' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . ' src="' . bar7_img( $fallback ) . '" width="410" height="560" alt="' . esc_attr( $fallback_alt ) . '"' . ( $priority ? ' fetchpriority="high"' : ' loading="lazy"' ) . ' decoding="async">';
}

/**
 * Category tile (casual or travel), shared by the homepage, About and Elementor's [rovakivo block="collection-tiles"].
 */
function bar7_category_tile( string $slug, string $url, string $heading_tag = 'h3' ): void {
	$copy = array(
		'casual' => array( __( 'Everyday', 'bar7' ), __( 'Casual backpacks.', 'bar7' ), __( 'Easy-going backpacks for commutes, errands and the daily routine.', 'bar7' ), __( 'Shop casual', 'bar7' ) ),
		'travel' => array( __( 'On the move', 'bar7' ), __( 'Travel backpacks.', 'bar7' ), __( 'Backpacks for weekends away, city breaks and longer trips.', 'bar7' ), __( 'Shop travel', 'bar7' ) ),
	);
	if ( ! isset( $copy[ $slug ] ) ) {
		return;
	}
	[ $kicker, $title, $text, $cta ] = $copy[ $slug ];
	$heading_tag                     = in_array( $heading_tag, array( 'h2', 'h3' ), true ) ? $heading_tag : 'h3';
	?>
	<a class="bar7-cat bar7-cat--<?php echo esc_attr( $slug ); ?>" href="<?php echo esc_url( $url ); ?>" data-reveal-item>
		<div class="bar7-cat__copy">
			<p class="bar7-cat__kicker"><?php echo esc_html( $kicker ); ?></p>
			<<?php echo $heading_tag; // phpcs:ignore ?> class="bar7-cat__title"><?php echo esc_html( $title ); ?></<?php echo $heading_tag; // phpcs:ignore ?>>
			<p class="bar7-cat__text"><?php echo esc_html( $text ); ?></p>
			<span class="bar7-link bar7-link--static"><?php echo esc_html( $cta ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></span>
		</div>
		<div class="bar7-cat__media"><?php echo bar7_category_photo_img( $slug, 'bar7-cat__img', '(min-width: 1100px) 280px, (min-width: 700px) 38vw, 48vw', false ); // phpcs:ignore ?></div>
	</a>
	<?php
}

/**
 * Catalog products by ID, in the order given (unpublished or missing IDs are skipped).
 *
 * @param int[] $ids
 * @return WC_Product[]
 */
function bar7_products_by_id( array $ids ): array {
	$out = array();
	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( $product && 'publish' === $product->get_status() ) {
			$out[] = $product;
		}
	}
	return $out;
}
