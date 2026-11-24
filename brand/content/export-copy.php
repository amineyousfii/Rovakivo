<?php
/**
 * Writes the live product copy (as stored in WooCommerce) to Markdown for the handoff.
 * Run with WP-CLI: wp eval-file export-copy.php > products.md
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$old = array(
	66  => 'Aturna condimentum mattis pellentesque nibh tortor',
	57  => 'Fringilla Phasellus Faucus Scelerisque Eleifend Donec',
	64  => 'Mollis nunc sed id semper risus in hendrerit',
	166 => 'Scelerisque mauris pellentesque pulvinar',
	157 => 'Ornare arcudui vivamus arcu felis bibendum',
	155 => 'Libero faucibus nistincidunt elementum integer',
	162 => 'Donec ultrices tincidunt arcu non sodales',
	159 => 'Vestibulum sedarcu nonodio euismod',
	164 => 'Rhoncus aenean velelit scelerisque mauris',
	168 => 'Ornare arcu odio utsem nullapharetra diam',
	170 => 'Tellus molestie nuncnon blandit elementum',
	279 => 'Neque laoreet suspendisse interdum consectetur',
	68  => 'Eleifend donec pretium vulputate sapien sagittis',
);
$md  = "## Old-to-new product names\n\n| ID | SKU | Old name | New name | URL (unchanged) |\n|---|---|---|---|---|\n";
foreach ( $old as $id => $o ) {
	$p   = wc_get_product( $id );
	$md .= sprintf( "| %d | %s | %s | **%s** | /product/%s/ |\n", $id, $p->get_sku(), $o, $p->get_name(), $p->get_slug() );
}
$md .= "\n## Product copy (as published)\n";
foreach ( array_keys( $old ) as $id ) {
	$p    = wc_get_product( $id );
	$cats = implode( ', ', wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'names' ) ) );
	$md  .= sprintf( "\n### %s\n\nID %d · SKU %s · %s · %s · price shown: %s\n\n", $p->get_name(), $id, $p->get_sku(), $cats, $p->get_type(), ( $p->is_type( "simple" ) && $p->is_on_sale() ? wc_format_decimal( $p->get_sale_price(), 2 ) . " € (was " . wc_format_decimal( $p->get_regular_price(), 2 ) . " €)" : ( $p->is_type( "variable" ) || $p->is_type( "grouped" ) ? "from " . wc_format_decimal( $p->get_price(), 2 ) . " €" : wc_format_decimal( $p->get_price(), 2 ) . " €" ) ) );
	$md  .= '**Short description:** ' . trim( wp_strip_all_tags( $p->get_short_description() ) ) . "\n\n";
	$body = $p->get_description();
	$body = preg_replace( '#<h3>(.*?)</h3>#', "\n**$1**\n", $body );
	$body = preg_replace( '#<li>(.*?)</li>#', '- $1', $body );
	$body = trim( preg_replace( "/\n{3,}/", "\n\n", wp_strip_all_tags( preg_replace( '#</p>#', "\n", $body ) ) ) );
	$md  .= "**Full description:**\n\n" . html_entity_decode( $body ) . "\n\n";
	$md  .= '**SEO title:** ' . get_post_meta( $id, '_bar7_seo_title', true ) . "  \n";
	$md  .= '**Meta description:** ' . get_post_meta( $id, '_bar7_meta_description', true ) . "\n\n";
	$md  .= "**Image alt text:**\n\n";
	foreach ( array_merge( array( $p->get_image_id() ), $p->get_gallery_image_ids() ) as $att ) {
		if ( $att ) {
			$md .= sprintf( "- %s: %s\n", basename( get_attached_file( $att ) ), get_post_meta( $att, '_wp_attachment_image_alt', true ) );
		}
	}
}
echo $md;
