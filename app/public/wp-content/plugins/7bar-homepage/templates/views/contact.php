<?php
/**
 * Contact. Edited in the block editor (Pages → Contact): what is saved there is what is shown here.
 * The form is the site's WPForms "Simple Contact Form", which handles validation, submission and its confirmation.
 */

defined( 'ABSPATH' ) || exit;

$bar7_page = get_queried_object();
if ( $bar7_page instanceof WP_Post && ( has_blocks( $bar7_page->post_content ) || ( function_exists( 'bar7_is_elementor_page' ) && bar7_is_elementor_page( $bar7_page->ID ) ) ) ) { // blocks, or a layout saved with Elementor
	echo apply_filters( 'the_content', $bar7_page->post_content ); // phpcs:ignore
	return;
}

// Fallback when the page has no block content.
?>
<header class="bar7-pagehead bar7-pagehead--compact" data-intro>
	<div class="bar7-wrap">
		<?php bar7_breadcrumbs(); ?>
		<p class="bar7-eyebrow" data-intro-item><?php esc_html_e( 'Customer support', 'bar7' ); ?></p>
		<h1 class="bar7-h1" data-intro-item><?php esc_html_e( 'Contact us.', 'bar7' ); ?></h1>
		<p class="bar7-lede" data-intro-item><?php esc_html_e( 'Questions about a bag, an order or your account? Send us a message using the form and we’ll reply by email.', 'bar7' ); ?></p>
	</div>
</header>

<div class="bar7-wrap bar7-contact" data-intro-body>
	<section class="bar7-contact__form bar7-panel" aria-labelledby="bar7-contact-form-title">
		<h2 id="bar7-contact-form-title" class="bar7-h3"><?php esc_html_e( 'Send a message', 'bar7' ); ?></h2>
		<?php bar7_contact_form( 506 ); ?>
	</section>

	<aside class="bar7-contact__help" aria-labelledby="bar7-help-title" data-reveal-group>
		<h2 id="bar7-help-title" class="bar7-h3"><?php esc_html_e( 'Find it faster', 'bar7' ); ?></h2>
		<?php bar7_contact_help_links(); ?>
	</aside>
</div>
