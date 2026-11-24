<?php
/**
 * Old image addresses keep working after the media renaming of 2026-11-24 (e.g. backpack-3-scaled.jpg →
 * product-023-scaled.jpg): a request for a renamed file is permanently redirected to its new name.
 * The map (old path → new path, relative to the uploads folder) is the option `bar7_media_redirects`.
 * Requests for missing files reach WordPress through its standard rewrite rules (Apache .htaccess, and the
 * nginx fallback used here), so no server configuration is needed.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	static function (): void {
		if ( ! is_404() ) {
			return;
		}
		$uploads = wp_get_upload_dir();
		$prefix  = trailingslashit( (string) wp_parse_url( $uploads['baseurl'], PHP_URL_PATH ) );
		$path    = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( '/' === $prefix || ! str_starts_with( $path, $prefix ) ) {
			return;
		}
		$old = rawurldecode( substr( $path, strlen( $prefix ) ) );
		$map = get_option( 'bar7_media_redirects', array() );
		if ( ! is_array( $map ) || empty( $map[ $old ] ) ) {
			return;
		}
		$new = (string) $map[ $old ];
		if ( ! file_exists( trailingslashit( $uploads['basedir'] ) . $new ) ) {
			return;
		}
		wp_safe_redirect( trailingslashit( $uploads['baseurl'] ) . $new, 301, 'Rovakivo media' );
		exit;
	},
	0
);
