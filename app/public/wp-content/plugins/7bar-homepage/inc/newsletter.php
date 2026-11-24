<?php
/**
 * Newsletter: adds the address to the MailPoet list. MailPoet sends its own
 * confirmation email when sign-up confirmation is turned on.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array{ok: bool, code: string, message: string}
 */
function bar7_subscribe( string $email ): array {
	$email = sanitize_email( $email );
	if ( '' === $email || ! is_email( $email ) ) {
		return array( 'ok' => false, 'code' => 'invalid', 'message' => __( 'Enter a valid email address, like name@example.com.', 'bar7' ) );
	}
	if ( ! class_exists( '\MailPoet\API\API' ) ) {
		return array( 'ok' => false, 'code' => 'unavailable', 'message' => __( 'Sign-up is not available right now. Please try again later.', 'bar7' ) );
	}

	try {
		$api = \MailPoet\API\API::MP( 'v1' );
		try {
			$subscriber = $api->getSubscriber( $email );
		} catch ( \Throwable $e ) {
			$subscriber = null;
		}

		if ( $subscriber ) {
			foreach ( (array) ( $subscriber['subscriptions'] ?? array() ) as $sub ) {
				if ( (int) ( $sub['segment_id'] ?? 0 ) === BAR7_MAILPOET_LIST_ID && 'subscribed' === ( $sub['status'] ?? '' ) ) {
					return array( 'ok' => true, 'code' => 'exists', 'message' => __( 'This address is already on our list.', 'bar7' ) );
				}
			}
			$api->subscribeToList( $subscriber['id'], BAR7_MAILPOET_LIST_ID );
			$subscriber = $api->getSubscriber( $email );
		} else {
			$subscriber = $api->addSubscriber( array( 'email' => $email ), array( BAR7_MAILPOET_LIST_ID ) );
		}

		if ( 'unconfirmed' === ( $subscriber['status'] ?? '' ) ) {
			return array( 'ok' => true, 'code' => 'confirm', 'message' => __( 'Almost done. Check your inbox and confirm your email to complete sign-up.', 'bar7' ) );
		}
		return array( 'ok' => true, 'code' => 'subscribed', 'message' => __( 'You are subscribed. Look out for our next update.', 'bar7' ) );
	} catch ( \Throwable $e ) {
		return array( 'ok' => false, 'code' => 'error', 'message' => __( 'We could not add your address. Check it and try again.', 'bar7' ) );
	}
}

function bar7_ajax_subscribe(): void {
	if ( ! check_ajax_referer( 'bar7_subscribe', 'bar7_nonce', false ) ) {
		wp_send_json( array( 'ok' => false, 'code' => 'expired', 'message' => __( 'This form has expired. Reload the page and try again.', 'bar7' ) ), 400 );
	}
	$result = bar7_subscribe( wp_unslash( (string) ( $_POST['email'] ?? '' ) ) );
	wp_send_json( $result, $result['ok'] ? 200 : 400 );
}
add_action( 'wp_ajax_bar7_subscribe', 'bar7_ajax_subscribe' );
add_action( 'wp_ajax_nopriv_bar7_subscribe', 'bar7_ajax_subscribe' );

/** Submission without JavaScript: redirect back to the form with the result. */
function bar7_post_subscribe(): void {
	$code = 'expired';
	if ( isset( $_POST['bar7_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bar7_nonce'] ) ), 'bar7_subscribe' ) ) {
		$code = bar7_subscribe( wp_unslash( (string) ( $_POST['email'] ?? '' ) ) )['code'];
	}
	$back = wp_get_referer();
	$back = $back ? remove_query_arg( 'newsletter', $back ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'newsletter', $code, $back ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_bar7_subscribe', 'bar7_post_subscribe' );
add_action( 'admin_post_nopriv_bar7_subscribe', 'bar7_post_subscribe' );

function bar7_newsletter_notice(): array {
	$code     = isset( $_GET['newsletter'] ) ? sanitize_key( wp_unslash( $_GET['newsletter'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$messages = array(
		'confirm'     => array( true, __( 'Almost done. Check your inbox and confirm your email to complete sign-up.', 'bar7' ) ),
		'subscribed'  => array( true, __( 'You are subscribed. Look out for our next update.', 'bar7' ) ),
		'exists'      => array( true, __( 'This address is already on our list.', 'bar7' ) ),
		'invalid'     => array( false, __( 'Enter a valid email address, like name@example.com.', 'bar7' ) ),
		'unavailable' => array( false, __( 'Sign-up is not available right now. Please try again later.', 'bar7' ) ),
		'error'       => array( false, __( 'We could not add your address. Check it and try again.', 'bar7' ) ),
		'expired'     => array( false, __( 'This form has expired. Reload the page and try again.', 'bar7' ) ),
	);
	return $messages[ $code ] ?? array();
}
