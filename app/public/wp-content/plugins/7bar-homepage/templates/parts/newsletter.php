<?php
/**
 * Newsletter sign-up, connected to the MailPoet list.
 */

defined( 'ABSPATH' ) || exit;

$notice = bar7_newsletter_notice();
$u      = bar7_urls();
?>
<section class="bar7-newsletter" id="newsletter" aria-labelledby="bar7-news-title">
	<div class="bar7-wrap bar7-newsletter__inner" data-reveal-group>
		<div data-reveal-item>
			<p class="bar7-eyebrow"><?php esc_html_e( 'Newsletter', 'bar7' ); ?></p>
			<h2 id="bar7-news-title" class="bar7-h2 bar7-h2--sm"><?php esc_html_e( 'Good things. In your inbox.', 'bar7' ); ?></h2>
			<p class="bar7-sub"><?php esc_html_e( 'New arrivals, collection updates and occasional offers from Rovakivo. Unsubscribe at any time.', 'bar7' ); ?></p>
		</div>
		<form class="bar7-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-bar7-newsletter data-reveal-item>
			<label for="bar7-email" class="bar7-form__label"><?php esc_html_e( 'Your email address', 'bar7' ); ?></label>
			<div class="bar7-form__row">
				<input id="bar7-email" type="email" name="email" required autocomplete="email" inputmode="email" placeholder="name@example.com" aria-describedby="bar7-email-msg bar7-email-note">
				<button class="bar7-btn bar7-btn--dark" type="submit"><?php esc_html_e( 'Subscribe', 'bar7' ); ?></button>
			</div>
			<input type="hidden" name="action" value="bar7_subscribe">
			<?php wp_nonce_field( 'bar7_subscribe', 'bar7_nonce', false ); ?>
			<p id="bar7-email-msg" class="bar7-form__msg<?php echo $notice ? ( $notice[0] ? ' is-ok' : ' is-error' ) : ''; ?>" role="status" aria-live="polite"><?php echo $notice ? esc_html( $notice[1] ) : ''; ?></p>
			<p id="bar7-email-note" class="bar7-form__note">
				<?php esc_html_e( 'We’ll ask you to confirm your address first.', 'bar7' ); ?>
				<?php if ( $u['privacy'] ) : ?>
					<?php printf( esc_html__( 'Read our %s.', 'bar7' ), '<a href="' . esc_url( $u['privacy'] ) . '">' . esc_html__( 'privacy policy', 'bar7' ) . '</a>' ); // phpcs:ignore ?>
				<?php endif; ?>
			</p>
		</form>
	</div>
</section>
