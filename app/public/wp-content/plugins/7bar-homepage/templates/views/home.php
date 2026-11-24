<?php
/**
 * Homepage. Edited in the block editor (Pages → Home): what is saved there is what is shown here.
 * Product names, prices, sale state, ratings and stock come from WooCommerce.
 */

defined( 'ABSPATH' ) || exit;

$bar7_page = get_queried_object();
if ( $bar7_page instanceof WP_Post && ( has_blocks( $bar7_page->post_content ) || ( function_exists( 'bar7_is_elementor_page' ) && bar7_is_elementor_page( $bar7_page->ID ) ) ) ) { // blocks, or a layout saved with Elementor
	echo apply_filters( 'the_content', $bar7_page->post_content ); // phpcs:ignore
	return;
}

// Fallback when the page has no block content.
$u = bar7_urls();
?>
<section class="bar7-hero" aria-labelledby="bar7-hero-title" data-intro>
	<picture class="bar7-hero__media" data-intro-media>
		<source media="(max-width: 699px)" srcset="<?php echo bar7_img( 'hero-850w.webp' ); ?>" width="850" height="400">
		<img src="<?php echo bar7_img( 'hero.webp' ); ?>" srcset="<?php echo bar7_img( 'hero-850w.webp' ); ?> 850w, <?php echo bar7_img( 'hero.webp' ); ?> 1700w" sizes="100vw" width="1700" height="800" alt="<?php esc_attr_e( 'A man in a light coat stands in the fog, carrying a leather-look backpack over one shoulder.', 'bar7' ); ?>" fetchpriority="high" decoding="async">
	</picture>
	<div class="bar7-wrap bar7-hero__inner">
		<div class="bar7-hero__copy">
			<p class="bar7-eyebrow bar7-eyebrow--rule" data-intro-item><?php esc_html_e( 'Bags for everyday and beyond', 'bar7' ); ?></p>
			<h1 id="bar7-hero-title" class="bar7-hero__title" data-intro-item><?php esc_html_e( 'Carry your day.', 'bar7' ); ?><br><?php esc_html_e( 'Explore what’s next.', 'bar7' ); ?></h1>
			<p class="bar7-hero__lede" data-intro-item><?php esc_html_e( 'Discover bags and backpacks for daily routines,', 'bar7' ); ?><br class="bar7-br-lg"> <?php esc_html_e( 'travel plans, and time outdoors.', 'bar7' ); ?></p>
			<div class="bar7-hero__actions" data-intro-item>
				<a class="bar7-btn bar7-btn--yellow" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Shop backpacks', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				<a class="bar7-link" href="#bar7-categories"><?php esc_html_e( 'Explore the collection', 'bar7' ); ?><?php echo bar7_icon( 'arrow-up' ); // phpcs:ignore ?></a>
			</div>
		</div>
		<p class="bar7-hero__foot" data-intro-item><?php esc_html_e( 'Everyday. Travel. Time outdoors.', 'bar7' ); ?></p>
	</div>
</section>

<?php bar7_section_support(); ?>

<section class="bar7-section" aria-labelledby="bar7-featured-title">
	<div class="bar7-wrap">
		<div class="bar7-head" data-reveal>
			<div>
				<p class="bar7-eyebrow"><?php esc_html_e( 'Featured', 'bar7' ); ?></p>
				<h2 id="bar7-featured-title" class="bar7-h2"><?php esc_html_e( 'Meet your next backpack.', 'bar7' ); ?></h2>
				<p class="bar7-sub"><?php esc_html_e( 'Four picks for the daily routine and the weekend away.', 'bar7' ); ?></p>
			</div>
			<a class="bar7-link" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Shop all backpacks', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<?php bar7_product_grid( array( 66, 57, 64, 166 ) ); ?>
	</div>
</section>

<section class="bar7-section bar7-section--tight-top" id="bar7-categories" aria-labelledby="bar7-cat-title" tabindex="-1">
	<div class="bar7-wrap">
		<div class="bar7-head" data-reveal>
			<div>
				<p class="bar7-eyebrow"><?php esc_html_e( 'Shop by collection', 'bar7' ); ?></p>
				<h2 id="bar7-cat-title" class="bar7-h2"><?php esc_html_e( 'A bag for every kind of day.', 'bar7' ); ?></h2>
			</div>
			<p class="bar7-head__aside"><?php esc_html_e( 'The daily routine. The trip ahead.', 'bar7' ); ?><br><?php esc_html_e( 'Pick the bag that fits.', 'bar7' ); ?></p>
		</div>
		<?php bar7_collection_tiles( 3 ); ?>
	</div>
</section>

<?php bar7_section_sale(); ?>

<section class="bar7-section bar7-section--paper" aria-labelledby="bar7-popular-title">
	<div class="bar7-wrap">
		<div class="bar7-head" data-reveal>
			<div>
				<p class="bar7-eyebrow"><?php esc_html_e( 'More to explore', 'bar7' ); ?></p>
				<h2 id="bar7-popular-title" class="bar7-h2"><?php esc_html_e( 'From the collection.', 'bar7' ); ?></h2>
				<p class="bar7-sub"><?php esc_html_e( 'More backpacks for everyday routes and trips away.', 'bar7' ); ?></p>
			</div>
			<?php bar7_product_tabs( array( 157, 155, 162, 159 ) ); ?>
		</div>
		<?php bar7_product_grid( array( 157, 155, 162, 159 ), true ); ?>
		<div class="bar7-center" data-reveal>
			<a class="bar7-btn bar7-btn--outline" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'See the full collection', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<?php bar7_section_testimonials(); ?>

<?php require BAR7_DIR . 'templates/parts/newsletter.php'; ?>
