<?php
/**
 * Page editor: shows the real site header (announcement bar, logo, menu) above the content and the real footer
 * below it, so editing a page looks like the live page. They are a read-only preview: the menus are edited in
 * Appearance → Menus. Rendered by the same templates as the website.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'rovakivo/v1',
			'/chrome',
			array(
				'methods'             => 'GET',
				'permission_callback' => static fn() => current_user_can( 'edit_pages' ),
				'args'                => array( 'post' => array( 'type' => 'integer', 'default' => 0 ) ),
				'callback'            => static function ( WP_REST_Request $request ) {
					$post = get_post( (int) $request['post'] );
					$GLOBALS['bar7_chrome_preview'] = true;
					$GLOBALS['bar7_preview_url']    = $post && 'publish' === $post->post_status ? (string) get_permalink( $post ) : '';
					ob_start();
					require BAR7_DIR . 'templates/parts/header.php';
					$header = (string) ob_get_clean();
					ob_start();
					require BAR7_DIR . 'templates/parts/footer.php';
					$footer = (string) ob_get_clean();
					unset( $GLOBALS['bar7_chrome_preview'], $GLOBALS['bar7_preview_url'] );
					return rest_ensure_response(
						array(
							'header'      => $header,
							'footer'      => $footer,
							'headerLabel' => __( 'Site header · menu: Appearance → Menus', 'bar7' ),
							'footerLabel' => __( 'Site footer · links: Appearance → Menus', 'bar7' ),
						)
					);
				},
			)
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		if ( ! bar7_editor_uses_site_styles() ) {
			return;
		}
		wp_enqueue_script(
			'bar7-editor-chrome',
			BAR7_URL . 'assets/js/editor-chrome.js',
			array( 'wp-api-fetch', 'wp-data', 'wp-dom-ready' ),
			bar7_asset_ver( 'assets/js/editor-chrome.js' ),
			true
		);
	}
);
