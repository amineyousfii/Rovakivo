<?php
/**
 * Imagify WebP delivery with <picture> tags (2026-11-24), made layout-neutral for the storefront.
 *
 * Imagify wraps each image that has a WebP copy in <picture><source type="image/webp">…<img></picture> and, by
 * default, moves the image's class, id and style onto the wrapper. Here:
 * - the wrapper only gets class "bar7-webp" (CSS: display: contents), so it never affects layout;
 * - the <img> keeps its own class, id, style and title, so the storefront's styles and scripts still find it.
 * The product gallery swaps the WebP source along with the photo (data-webp-srcset on each thumbnail).
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'imagify_picture_attributes',
	static function () {
		return array( 'class' => 'bar7-webp' );
	},
	10,
	0
);

add_filter(
	'imagify_picture_img_attributes',
	static function ( $attributes, $image ) {
		foreach ( array( 'class', 'id', 'style', 'title' ) as $key ) {
			if ( isset( $image['attributes'][ $key ] ) && '' !== $image['attributes'][ $key ] && ! isset( $attributes[ $key ] ) ) {
				$attributes[ $key ] = $image['attributes'][ $key ];
			}
		}
		return $attributes;
	},
	10,
	2
);

/**
 * WebP twin of a srcset (Imagify stores "photo.jpg.webp" next to "photo.jpg"): the sizes that have a WebP copy,
 * like Imagify's own <source> (it skips sizes it could not make smaller). '' when none has one.
 */
function bar7_webp_srcset( string $srcset ): string {
	if ( '' === trim( $srcset ) ) {
		return '';
	}
	$up  = wp_get_upload_dir();
	$out = array();
	foreach ( explode( ',', $srcset ) as $part ) {
		$part = trim( $part );
		if ( '' === $part ) {
			continue;
		}
		$bits = preg_split( '/\s+/', $part, 2 );
		$url  = $bits[0];
		if ( ! str_starts_with( $url, $up['baseurl'] ) || preg_match( '/\.webp$/i', $url ) ) {
			return '';
		}
		if ( ! file_exists( $up['basedir'] . substr( $url, strlen( $up['baseurl'] ) ) . '.webp' ) ) {
			continue;
		}
		$out[] = trim( $url . '.webp ' . ( $bits[1] ?? '' ) );
	}
	return implode( ', ', $out );
}
