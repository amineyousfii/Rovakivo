<?php
/**
 * Single product. Price, stock, variations, quantity and add to cart use WooCommerce's own
 * templates and scripts, so validation, stock rules and cart behaviour are unchanged.
 */

defined( 'ABSPATH' ) || exit;

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	$name     = $product->get_name();
	$category = bar7_primary_category( $product );
	$slug     = bar7_photo_slug( $product );

	// Gallery: the supplied photo (or the product image), then the product's existing gallery images.
	$images = array();
	if ( $slug ) {
		$images[] = array(
			'src'    => bar7_img( 'product-' . $slug . '.webp' ),
			'srcset' => bar7_img( 'product-' . $slug . '-400w.webp' ) . ' 400w, ' . bar7_img( 'product-' . $slug . '.webp' ) . ' 800w',
			'thumb'  => bar7_img( 'product-' . $slug . '-400w.webp' ),
			'w'      => 800,
			'h'      => 1067,
			// Same photograph as the product image, so it shares that image's alt text.
			'alt'    => (string) get_post_meta( (int) $product->get_image_id(), '_wp_attachment_image_alt', true ),
			'id'     => (int) $product->get_image_id(),
		);
	} elseif ( $product->get_image_id() ) {
		$images[] = (int) $product->get_image_id();
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$images[] = (int) $gid;
	}
	$images = array_map(
		static function ( $img ) {
			if ( is_array( $img ) ) {
				return $img;
			}
			$main  = wp_get_attachment_image_src( $img, 'woocommerce_single' );
			$thumb = wp_get_attachment_image_src( $img, 'woocommerce_gallery_thumbnail' );
			return array(
				'src'    => $main ? $main[0] : '',
				'srcset' => (string) wp_get_attachment_image_srcset( $img, 'woocommerce_single' ),
				'thumb'  => $thumb ? $thumb[0] : '',
				'w'      => $main ? (int) $main[1] : 800,
				'h'      => $main ? (int) $main[2] : 1067,
				'alt'    => (string) get_post_meta( $img, '_wp_attachment_image_alt', true ),
				'id'     => $img,
			);
		},
		$images
	);
	$images = array_values( array_filter( $images, static fn( $i ) => '' !== $i['src'] ) );

	$tabs     = apply_filters( 'woocommerce_product_tabs', array() );
	$related  = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), 4 ) ) );
	$stock    = wc_get_stock_html( $product );
	$reviews  = (int) $product->get_review_count();
	?>
	<div class="bar7-wrap bar7-pdp-notices"><?php woocommerce_output_all_notices(); ?></div>

	<div class="bar7-wrap">
		<?php bar7_breadcrumbs(); ?>
	</div>

	<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'bar7-pdp', $product ); ?>>
		<div class="bar7-wrap bar7-pdp__grid">
			<div class="bar7-gallery" data-bar7-gallery data-intro-media>
				<div class="bar7-gallery__main">
					<?php if ( $images ) : ?>
						<img
							src="<?php echo esc_url( $images[0]['src'] ); ?>"
							<?php echo $images[0]['srcset'] ? 'srcset="' . esc_attr( $images[0]['srcset'] ) . '"' : ''; ?>
							sizes="(min-width: 1100px) 640px, (min-width: 700px) 50vw, 100vw"
							width="<?php echo (int) $images[0]['w']; ?>" height="<?php echo (int) $images[0]['h']; ?>"
							alt="<?php echo esc_attr( $images[0]['alt'] ? $images[0]['alt'] : $name ); ?>"
							fetchpriority="high" decoding="async" data-bar7-gallery-main="1"><?php // A value is needed: Imagify's <picture> rewrite drops attributes without one. ?>
					<?php else : ?>
						<?php echo wc_placeholder_img( 'woocommerce_single' ); // phpcs:ignore ?>
					<?php endif; ?>
					<?php if ( $product->is_on_sale() ) : ?>
						<span class="bar7-badge"><?php esc_html_e( 'Sale', 'bar7' ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( count( $images ) > 1 ) : ?>
					<ul class="bar7-gallery__thumbs" aria-label="<?php esc_attr_e( 'Product photos', 'bar7' ); ?>">
						<?php foreach ( $images as $i => $img ) : ?>
							<li>
								<button type="button" class="bar7-gallery__thumb" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
									data-src="<?php echo esc_url( $img['src'] ); ?>" data-srcset="<?php echo esc_attr( $img['srcset'] ); ?>" data-webp-srcset="<?php echo esc_attr( function_exists( 'bar7_webp_srcset' ) ? bar7_webp_srcset( (string) $img['srcset'] ) : '' ); ?>"
									data-w="<?php echo (int) $img['w']; ?>" data-h="<?php echo (int) $img['h']; ?>" data-alt="<?php echo esc_attr( $img['alt'] ? $img['alt'] : $name ); ?>" data-image-id="<?php echo (int) $img['id']; ?>">
									<img src="<?php echo esc_url( $img['thumb'] ? $img['thumb'] : $img['src'] ); ?>" alt="" width="80" height="107" loading="lazy" decoding="async">
									<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Show photo %1$d of %2$d', 'bar7' ), $i + 1, count( $images ) ) ); ?></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="bar7-pdp__details" data-intro>
				<?php if ( $category ) : ?>
					<p class="bar7-eyebrow" data-intro-item><a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( sprintf( __( '%s backpack', 'bar7' ), $category->name ) ); ?></a></p>
				<?php endif; ?>
				<h1 class="bar7-pdp__title" data-intro-item><?php echo esc_html( $name ); ?></h1>

				<?php if ( $reviews > 0 && wc_review_ratings_enabled() ) : ?>
					<a class="bar7-pdp__rating" href="#tab-reviews" data-bar7-tab-link="reviews" data-intro-item><?php echo bar7_rating( $product, true ); // phpcs:ignore ?></a>
				<?php endif; ?>

				<div class="bar7-pdp__price bar7-price" data-intro-item><?php echo wp_kses_post( $product->get_price_html() ); ?></div>

				<?php if ( $product->get_short_description() ) : ?>
					<div class="bar7-pdp__short bar7-prose" data-intro-item><?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); // phpcs:ignore ?></div>
				<?php endif; ?>

				<?php if ( ! $product->is_type( 'variable' ) ) : ?>
					<p class="bar7-stock <?php echo $product->is_in_stock() ? 'is-in' : 'is-out'; ?>" data-intro-item>
						<?php echo bar7_icon( $product->is_in_stock() ? 'check' : 'close' ); // phpcs:ignore ?>
						<?php echo $stock ? esc_html( wp_strip_all_tags( $stock ) ) : ( $product->is_in_stock() ? esc_html__( 'In stock', 'bar7' ) : esc_html__( 'Out of stock', 'bar7' ) ); ?>
					</p>
				<?php endif; ?>

				<div class="bar7-pdp__buy" data-intro-item>
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>

				<dl class="bar7-pdp__meta" data-intro-item>
					<?php if ( $product->get_sku() ) : ?>
						<div><dt><?php esc_html_e( 'SKU', 'bar7' ); ?></dt><dd><?php echo esc_html( $product->get_sku() ); ?></dd></div>
					<?php endif; ?>
					<?php $cats = wc_get_product_category_list( $product->get_id(), ', ' ); ?>
					<?php if ( $cats ) : ?>
						<div><dt><?php esc_html_e( 'Category', 'bar7' ); ?></dt><dd><?php echo wp_kses_post( $cats ); ?></dd></div>
					<?php endif; ?>
				</dl>
			</div>
		</div>

		<?php if ( $tabs ) : ?>
			<section class="bar7-wrap bar7-pdp__info" aria-label="<?php esc_attr_e( 'Product information', 'bar7' ); ?>" data-reveal>
				<div class="bar7-ptabs" data-bar7-tabset>
					<div class="bar7-ptabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Product information', 'bar7' ); ?>">
						<?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
							<button type="button" role="tab" id="tab-title-<?php echo esc_attr( $key ); ?>" aria-controls="tab-<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" tabindex="<?php echo $first ? '0' : '-1'; ?>" data-tab="<?php echo esc_attr( $key ); ?>">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?>
							</button>
						<?php $first = false; endforeach; ?>
					</div>
					<?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
						<div class="bar7-ptabs__panel bar7-prose" role="tabpanel" id="tab-<?php echo esc_attr( $key ); ?>" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>" tabindex="0"<?php echo $first ? '' : ' hidden'; ?>>
							<?php
							if ( isset( $tab['callback'] ) ) {
								call_user_func( $tab['callback'], $key, $tab );
							}
							?>
						</div>
					<?php $first = false; endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $related ) : ?>
			<section class="bar7-section bar7-section--paper bar7-related" aria-labelledby="bar7-related-title">
				<div class="bar7-wrap">
					<div class="bar7-head" data-reveal>
						<div>
							<p class="bar7-eyebrow"><?php esc_html_e( 'Keep exploring', 'bar7' ); ?></p>
							<h2 id="bar7-related-title" class="bar7-h2"><?php esc_html_e( 'You might also like.', 'bar7' ); ?></h2>
						</div>
						<?php if ( $category ) : ?>
							<a class="bar7-link" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( sprintf( __( 'More %s backpacks', 'bar7' ), strtolower( $category->name ) ) ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
						<?php endif; ?>
					</div>
					<div class="bar7-grid" data-reveal-grid>
						<?php foreach ( $related as $rp ) { bar7_product_card( $rp ); } ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
		<?php if ( $product->is_purchasable() && $product->is_in_stock() || $product->is_type( 'grouped' ) ) : ?>
			<div class="bar7-stickybuy" data-bar7-stickybuy data-type="<?php echo esc_attr( $product->get_type() ); ?>" hidden>
				<div class="bar7-stickybuy__info">
					<span class="bar7-stickybuy__name"><?php echo esc_html( $name ); ?></span>
					<span class="bar7-stickybuy__price bar7-price" data-bar7-stickybuy-price><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				</div>
				<button type="button" class="bar7-btn bar7-btn--yellow" data-bar7-stickybuy-btn
					data-label-add="<?php esc_attr_e( 'Add to cart', 'bar7' ); ?>"
					data-label-choose="<?php esc_attr_e( 'Choose options', 'bar7' ); ?>">
					<?php
					if ( $product->is_type( 'simple' ) ) {
						esc_html_e( 'Add to cart', 'bar7' );
					} elseif ( $product->is_type( 'variable' ) ) {
						esc_html_e( 'Choose options', 'bar7' );
					} else {
						esc_html_e( 'View options', 'bar7' );
					}
					?>
				</button>
			</div>
		<?php endif; ?>
	</article>
	<?php
	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}
endwhile;
