<?php
/**
 * Elementor for pages (switched back on at the owner's request, 2026-11-23).
 *
 * - A page saved with Elementor shows its Elementor layout inside the Rovakivo header and footer. Elementor's
 *   styles and scripts load on those pages only; every other page stays as light as before.
 * - Cart, Checkout, My account and Sale stay in the WordPress editor: WooCommerce's forms and the sale list live
 *   there, and an Elementor layout would replace them. (Elementor itself leaves out the Shop page.)
 * - Pages designed with Rovakivo blocks (Home, About, Contact) can be opened with Elementor, after a warning in the
 *   WordPress editor: saving them in Elementor replaces the block design (the block version stays in Revisions).
 * - [rovakivo block="…"] places a live Rovakivo section (product grid, sale banner, collection tiles…) in
 *   Elementor's Shortcode widget.
 */

defined( 'ABSPATH' ) || exit;

function bar7_elementor_ready(): bool {
	return defined( 'ELEMENTOR_VERSION' );
}

/** A page saved with Elementor: its Elementor layout is what the website shows. */
function bar7_is_elementor_page( int $id = 0 ): bool {
	$id = $id ? $id : (int) get_queried_object_id();
	if ( ! $id || ! bar7_elementor_ready() || 'builder' !== get_post_meta( $id, '_elementor_edit_mode', true ) ) {
		return false;
	}
	$data = (string) get_post_meta( $id, '_elementor_data', true );
	return '' !== $data && '[]' !== $data;
}

/** Pages that stay in the WordPress editor. */
function bar7_elementor_locked_pages(): array {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return array();
	}
	$ids = array( wc_get_page_id( 'cart' ), wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ) );
	if ( function_exists( 'bar7_sale_page_id' ) ) {
		$ids[] = bar7_sale_page_id();
	}
	return array_values( array_filter( array_map( 'intval', $ids ), static fn( $id ) => $id > 0 ) );
}

/** Whether Elementor's own styles and scripts are needed on this request. */
function bar7_elementor_assets_allowed(): bool {
	return bar7_elementor_ready() && is_singular( 'page' ) && bar7_is_elementor_page( (int) get_queried_object_id() );
}

/* ---------------------------------------------------------------------------
 * Locked pages: no Elementor button, and the Elementor editor explains where to edit instead.
 * ------------------------------------------------------------------------ */

add_action(
	'admin_action_elementor',
	static function (): void {
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! $id || ! in_array( $id, bar7_elementor_locked_pages(), true ) ) {
			return;
		}
		$message = sprintf(
			/* translators: %s: page title */
			esc_html__( '“%s” is edited in the WordPress editor, not Elementor: it holds WooCommerce’s own forms or the live sale list, which an Elementor layout would replace.', 'bar7' ),
			esc_html( get_the_title( $id ) )
		);
		wp_die(
			'<p>' . $message . '</p><p><a class="button button-primary" href="' . esc_url( (string) get_edit_post_link( $id, 'raw' ) ) . '">' . esc_html__( 'Edit it in the WordPress editor', 'bar7' ) . '</a></p>',
			esc_html__( 'Edit this page in the WordPress editor', 'bar7' ),
			array( 'response' => 403 )
		);
	},
	1
);

foreach ( array( 'page_row_actions', 'post_row_actions' ) as $bar7_hook ) {
	add_filter(
		$bar7_hook,
		static function ( $actions, $post ) {
			if ( $post instanceof WP_Post && in_array( $post->ID, bar7_elementor_locked_pages(), true ) ) {
				unset( $actions['edit_with_elementor'] );
			}
			return $actions;
		},
		20,
		2
	);
}

add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		$post = get_post();
		if ( ! bar7_elementor_ready() || ! $post || 'page' !== $post->post_type ) {
			return;
		}
		// Locked pages: Elementor's "Edit with Elementor" button is left out of the WordPress editor.
		if ( in_array( $post->ID, bar7_elementor_locked_pages(), true ) ) {
			wp_dequeue_script( 'elementor-gutenberg' );
			return;
		}
		// Pages designed with Rovakivo blocks: say what Elementor would do before anyone switches.
		if ( ! bar7_is_elementor_page( $post->ID ) && str_contains( (string) $post->post_content, '<!-- wp:rovakivo/' ) ) {
			$text = __( 'This page is designed with Rovakivo blocks, and that is exactly how it looks on the website. If you open it with Elementor and save, Elementor’s layout replaces this design, including its live sections (products, prices, sale banner). The block version stays in Revisions.', 'bar7' );
			wp_register_script( 'bar7-elementor-notice', false, array( 'wp-data', 'wp-notices', 'wp-dom-ready' ), false, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
			wp_enqueue_script( 'bar7-elementor-notice' );
			wp_add_inline_script(
				'bar7-elementor-notice',
				'wp.domReady(function(){wp.data.dispatch("core/notices").createNotice("warning",' . wp_json_encode( $text ) . ',{id:"bar7-elementor-notice",isDismissible:true});});'
			);
		}
	},
	20
);

/* ---------------------------------------------------------------------------
 * Elementor's editor preview uses the theme; it gets the site's own fonts so Elementor's text matches the website.
 * ------------------------------------------------------------------------ */

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_enqueue_style( 'bar7-fonts', BAR7_URL . 'assets/fonts/fonts.css', array(), bar7_asset_ver( 'assets/fonts/fonts.css' ) );
		}
	}
);

/* ---------------------------------------------------------------------------
 * [rovakivo block="product-grid" ids="57,64"] — a live Rovakivo section inside an Elementor Shortcode widget.
 * Attributes are the block's own settings (ids, filterable, section, headinglevel, formid).
 * ------------------------------------------------------------------------ */

add_shortcode(
	'rovakivo',
	static function ( $atts ): string {
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		$slug = sanitize_key( $atts['block'] ?? '' );
		// The catalog and account title follow the page being viewed; the cart preview is editor-only.
		if ( '' === $slug || in_array( $slug, array( 'product-catalog', 'account-title', 'woo-preview' ), true ) ) {
			return '';
		}
		$name = 'rovakivo/' . $slug;
		$type = WP_Block_Type_Registry::get_instance()->get_registered( $name );
		if ( ! $type ) {
			return '';
		}
		$attrs = array();
		foreach ( (array) $type->attributes as $key => $def ) {
			$k = strtolower( $key );
			if ( ! isset( $atts[ $k ] ) ) {
				continue;
			}
			$kind          = $def['type'] ?? 'string';
			$attrs[ $key ] = 'boolean' === $kind
				? in_array( strtolower( (string) $atts[ $k ] ), array( '1', 'true', 'yes', 'on' ), true )
				: ( 'number' === $kind ? (int) $atts[ $k ] : sanitize_text_field( (string) $atts[ $k ] ) );
		}
		return render_block( array( 'blockName' => $name, 'attrs' => $attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
	}
);
