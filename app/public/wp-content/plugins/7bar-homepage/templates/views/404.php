<?php
/**
 * Page not found.
 */

defined( 'ABSPATH' ) || exit;

$u = bar7_urls();
?>
<section class="bar7-404" aria-labelledby="bar7-404-title" data-intro>
	<div class="bar7-wrap bar7-404__inner">
		<p class="bar7-404__code" aria-hidden="true" data-intro-item>404</p>
		<div>
			<p class="bar7-eyebrow" data-intro-item><?php esc_html_e( 'Page not found', 'bar7' ); ?></p>
			<h1 id="bar7-404-title" class="bar7-h1" data-intro-item><?php esc_html_e( 'This trail doesn’t lead anywhere.', 'bar7' ); ?></h1>
			<p class="bar7-lede" data-intro-item><?php esc_html_e( 'The page may have moved, or the link may be mistyped. Search the collection or head back to a familiar place.', 'bar7' ); ?></p>
			<div data-intro-item><?php bar7_search_form( 'bar7-404-search' ); ?></div>
			<div class="bar7-404__links" data-intro-item>
				<a class="bar7-btn bar7-btn--yellow" href="<?php echo esc_url( $u['shop'] ); ?>"><?php esc_html_e( 'Shop backpacks', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
				<a class="bar7-link" href="<?php echo esc_url( $u['home'] ); ?>"><?php esc_html_e( 'Back to the homepage', 'bar7' ); ?><?php echo bar7_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>
