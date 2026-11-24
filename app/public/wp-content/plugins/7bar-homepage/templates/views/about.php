<?php
/**
 * About. Built only from facts about the store itself: what it sells and how the collection is organised.
 * The previous page body was template placeholder text, so it is not shown (it is not deleted).
 */

defined( 'ABSPATH' ) || exit;

// The page is edited in the block editor (Pages → About): what is saved there is what is shown here.
$bar7_page = get_queried_object();
if ( $bar7_page instanceof WP_Post && ( has_blocks( $bar7_page->post_content ) || ( function_exists( 'bar7_is_elementor_page' ) && bar7_is_elementor_page( $bar7_page->ID ) ) ) ) { // blocks, or a layout saved with Elementor
	echo apply_filters( 'the_content', $bar7_page->post_content ); // phpcs:ignore
	return;
}

// Fallback when the page has no block content.
$u      = bar7_urls();
$casual = get_term_by( 'slug', 'casual', 'product_cat' );
$travel = get_term_by( 'slug', 'travel', 'product_cat' );
$total  = (int) ( wp_count_posts( 'product' )->publish ?? 0 );
?>
<header class="bar7-pagehead bar7-pagehead--about" data-intro>
	<div class="bar7-wrap">
		<?php bar7_breadcrumbs(); ?>
		<p class="bar7-eyebrow" data-intro-item><?php esc_html_e( 'About Rovakivo', 'bar7' ); ?></p>
		<h1 class="bar7-display" data-intro-item><?php esc_html_e( 'For every day.', 'bar7' ); ?><br><?php esc_html_e( 'For wherever’s next.', 'bar7' ); ?></h1>
		<p class="bar7-lede" data-intro-item><?php esc_html_e( 'Rovakivo is an online shop for bags and backpacks: pieces for the daily commute, for trips away and for time spent outdoors.', 'bar7' ); ?></p>
		<?php bar7_admin_note( __( 'Add Rovakivo’s own story here when it is ready: who is behind the shop, where the bags come from, and anything customers should know. Nothing about history, makers or materials has been written, because none of it is confirmed yet.', 'bar7' ) ); ?>
	</div>
</header>

<figure class="bar7-wrap bar7-about__hero" data-reveal-media>
	<img src="<?php echo bar7_img( 'hero.webp' ); ?>" srcset="<?php echo bar7_img( 'hero-850w.webp' ); ?> 850w, <?php echo bar7_img( 'hero.webp' ); ?> 1700w" sizes="(min-width: 1344px) 1280px, 100vw" width="1700" height="800" alt="<?php esc_attr_e( 'A man in a light coat stands in the fog, carrying a leather-look backpack over one shoulder.', 'bar7' ); ?>" loading="lazy" decoding="async">
</figure>

<section class="bar7-section" aria-labelledby="bar7-about-carry">
	<div class="bar7-wrap bar7-about__split" data-reveal-group>
		<div data-reveal-item>
			<p class="bar7-eyebrow"><?php esc_html_e( 'What you’ll find', 'bar7' ); ?></p>
			<h2 id="bar7-about-carry" class="bar7-h2"><?php esc_html_e( 'Carry your day.', 'bar7' ); ?><br><?php esc_html_e( 'Explore what’s next.', 'bar7' ); ?></h2>
		</div>
		<div class="bar7-prose" data-reveal-item>
			<p><?php esc_html_e( 'The collection is organised around how you use a bag. Casual backpacks are made for the daily routine: commutes, errands and weekends close to home. Travel backpacks suit weekends away, city breaks and longer trips.', 'bar7' ); ?></p>
			<?php if ( $total ) : ?>
				<p>
					<?php
					printf(
						/* translators: 1: total products, 2: casual count, 3: travel count */
						esc_html__( 'Right now there are %1$d backpacks in the shop: %2$d casual and %3$d travel.', 'bar7' ),
						$total, // phpcs:ignore
						$casual ? (int) $casual->count : 0, // phpcs:ignore
						$travel ? (int) $travel->count : 0 // phpcs:ignore
					);
					?>
				</p>
			<?php endif; ?>
			<p><?php esc_html_e( 'Every product page shows the bag’s photos, current price and stock, and the details we can confirm. If you need to know something that isn’t listed, send us a message before you order.', 'bar7' ); ?></p>
		</div>
	</div>
</section>

<section class="bar7-section bar7-section--tight-top" aria-label="<?php esc_attr_e( 'Shop by collection', 'bar7' ); ?>">
	<div class="bar7-wrap">
		<div class="bar7-cats" data-reveal-group>
			<?php
			bar7_category_tile( 'casual', $u['casual'], 'h2' );
			bar7_category_tile( 'travel', $u['travel'], 'h2' );
			?>
		</div>
	</div>
</section>

<section class="bar7-cta" aria-labelledby="bar7-about-cta">
	<div class="bar7-wrap bar7-cta__inner" data-reveal-group>
		<h2 id="bar7-about-cta" class="bar7-h2" data-reveal-item><?php esc_html_e( 'Find your next bag.', 'bar7' ); ?></h2>
		<div class="bar7-cta__actions" data-reveal-item>
			<a class="bar7-btn bar7-btn--yellow" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Explore the collection', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $u['contact'] ) : ?>
				<a class="bar7-link bar7-link--light" href="<?php echo esc_url( $u['contact'] ); ?>"><?php esc_html_e( 'Ask us a question', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php require BAR7_DIR . 'templates/parts/newsletter.php'; ?>
