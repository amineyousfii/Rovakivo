<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$u = bar7_urls();
?>
<footer class="bar7-footer" data-reveal-group>
	<div class="bar7-wrap">
		<div class="bar7-footer__grid">
			<div class="bar7-footer__brand" data-reveal-item>
				<?php $GLOBALS['bar7_logo']( 'bar7-logo--light' ); ?>
				<p><?php esc_html_e( 'For every day.', 'bar7' ); ?><br><?php esc_html_e( 'For wherever’s next.', 'bar7' ); ?></p>
			</div>
			<?php foreach ( bar7_footer_columns() as $bar7_col ) : ?>
				<?php $bar7_col_id = 'bar7-f-' . sanitize_title( $bar7_col['title'] ); ?>
			<nav class="bar7-footer__col" aria-labelledby="<?php echo esc_attr( $bar7_col_id ); ?>" data-reveal-item>
				<h2 id="<?php echo esc_attr( $bar7_col_id ); ?>"><?php echo esc_html( $bar7_col['title'] ); ?></h2>
				<ul>
					<?php foreach ( $bar7_col['links'] as $bar7_link ) : ?>
						<li><a href="<?php echo esc_url( $bar7_link['url'] ); ?>"><?php echo esc_html( $bar7_link['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			<?php endforeach; ?>
			<p class="bar7-footer__motto" aria-hidden="true" data-reveal-item><?php esc_html_e( 'Carry', 'bar7' ); ?><br><?php esc_html_e( 'your day.', 'bar7' ); ?></p>
		</div>
		<div class="bar7-footer__bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Rovakivo', 'bar7' ); ?></p>
			<p><?php esc_html_e( 'Bags and backpacks for every day, travel and time outdoors.', 'bar7' ); ?></p>
			<button type="button" class="bar7-footer__cookies" data-bar7-consent-open aria-controls="bar7-consent"><?php esc_html_e( 'Cookie settings', 'bar7' ); ?></button>
			<a class="bar7-footer__top" href="#top" data-bar7-top><?php esc_html_e( 'Back to top', 'bar7' ); ?><?php echo bar7_icon( 'arrow-up' ); // phpcs:ignore ?></a>
		</div>
	</div>
</footer>
