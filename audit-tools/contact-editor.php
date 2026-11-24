<?php
define( 'REST_REQUEST', true );
require_once ABSPATH . 'wp-admin/includes/admin.php';
wp_set_current_user( 1 );
foreach ( array( 'rovakivo/breadcrumbs' => array(), 'rovakivo/contact-form' => array( 'formId' => 506 ), 'rovakivo/help-links' => array() ) as $b => $attrs ) {
	$req = new WP_REST_Request( 'GET', '/wp/v2/block-renderer/' . $b );
	$req->set_query_params( array( 'context' => 'edit', 'post_id' => 117, 'attributes' => $attrs ) );
	$res = rest_do_request( $req );
	$h   = (string) ( $res->get_data()['rendered'] ?? '' );
	echo $b, ': status ', $res->get_status(), ' | ', substr( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $h ) ) ), 0, 110 ), ' | inputs ', substr_count( $h, '<input' ), "\n";
}
$GLOBALS['post'] = get_post( 117 );
setup_postdata( $GLOBALS['post'] );
$req2 = new WP_REST_Request( 'GET', '/rovakivo/v1/chrome' );
$req2->set_param( 'post', 117 );
$chrome = rest_do_request( $req2 )->get_data();
$s = get_block_editor_settings( array(), new WP_Block_Editor_Context( array( 'post' => get_post( 117 ) ) ) );
$styles = array_values( array_map( fn( $x ) => array( 'css' => $x['css'], 'baseURL' => $x['baseURL'] ?? '' ), array_filter( $s['styles'], fn( $x ) => str_contains( $x['baseURL'] ?? '', '7bar-homepage' ) || str_contains( $x['baseURL'] ?? '', 'woocommerce/assets' ) ) ) );
file_put_contents( 'C:/Users/HP/AppData/Local/Temp/claude/49a97806ab90cf9d/258a5ee7-bdc8-4778-b0bb-2b5da6bd5f79/scratchpad/editor-data-contact.json', wp_json_encode( array( 'html' => apply_filters( 'the_content', get_post( 117 )->post_content ), 'chrome' => $chrome, 'editor_css' => $styles ) ) );
echo 'chrome current: ', preg_match( '/aria-current="page">([^<]*)/', $chrome['header'], $m ) ? $m[1] : '-', "\n";
