<?php
/**
 * Cookie consent.
 *
 * What the site sets (checked in the browser on 2026-11-20):
 * - Essential: WooCommerce cart/session cookies, the sign-in cookie, and this consent cookie.
 * - Optional: WooCommerce order attribution ("sbjs_*" cookies), which records how a visitor arrived
 *   and saves it with an order. It now runs only after the visitor chooses "Allow".
 * There are no analytics or advertising tags on the site.
 *
 * The choice is stored for 180 days in the `rovakivo_consent` cookie ("accepted" or "rejected")
 * and can be changed from the "Cookie settings" link in the footer.
 */

defined( 'ABSPATH' ) || exit;

const BAR7_CONSENT_COOKIE = 'rovakivo_consent';

function bar7_consent_choice(): string {
	$value = isset( $_COOKIE[ BAR7_CONSENT_COOKIE ] ) ? sanitize_key( wp_unslash( $_COOKIE[ BAR7_CONSENT_COOKIE ] ) ) : '';
	return in_array( $value, array( 'accepted', 'rejected' ), true ) ? $value : '';
}

/** WooCommerce order attribution only with consent (its supported switch). */
add_filter(
	'wc_order_attribution_allow_tracking',
	static function ( $allow ) {
		return 'accepted' === bar7_consent_choice() ? $allow : false;
	}
);

/** The banner. Hidden until the script finds no stored choice; without JavaScript nothing optional runs. */
add_action(
	'wp_footer',
	static function (): void {
		if ( ! bar7_active() ) {
			return;
		}
		$privacy = bar7_urls()['privacy'];
		?>
		<section class="bar7-consent" id="bar7-consent" aria-labelledby="bar7-consent-title" hidden data-bar7-consent>
			<div class="bar7-consent__text">
				<h2 id="bar7-consent-title" class="bar7-consent__title"><?php esc_html_e( 'Cookies', 'bar7' ); ?></h2>
				<p>
					<?php esc_html_e( 'We use essential cookies to run the cart, checkout and your account. With your permission, we’d also save how you found us (for example, the link that brought you here) with any order you place. We don’t use advertising cookies.', 'bar7' ); ?>
					<?php if ( $privacy ) : ?>
						<a href="<?php echo esc_url( $privacy . '#cookies' ); ?>"><?php esc_html_e( 'Read more', 'bar7' ); ?></a>
					<?php endif; ?>
				</p>
			</div>
			<div class="bar7-consent__actions">
				<button type="button" class="bar7-btn bar7-btn--outline" data-bar7-consent-choice="rejected"><?php esc_html_e( 'Essential only', 'bar7' ); ?></button>
				<button type="button" class="bar7-btn bar7-btn--dark" data-bar7-consent-choice="accepted"><?php esc_html_e( 'Allow', 'bar7' ); ?></button>
			</div>
		</section>
		<?php
	},
	5
);
