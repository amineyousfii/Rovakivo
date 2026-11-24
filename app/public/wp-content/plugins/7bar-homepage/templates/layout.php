<?php
/**
 * Layout shared by every storefront page: header, the page's view, footer.
 */

defined( 'ABSPATH' ) || exit;

$bar7_view_file = BAR7_DIR . 'templates/views/' . bar7_view() . '.php';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bar7-skip" href="#main"><?php esc_html_e( 'Skip to content', 'bar7' ); ?></a>

<?php require BAR7_DIR . 'templates/parts/header.php'; ?>

<main id="main" tabindex="-1">
	<?php require file_exists( $bar7_view_file ) ? $bar7_view_file : BAR7_DIR . 'templates/views/page.php'; ?>
</main>

<?php require BAR7_DIR . 'templates/parts/footer.php'; ?>

<p class="screen-reader-text" role="status" aria-live="polite" id="bar7-live"></p>
<?php wp_footer(); ?>
</body>
</html>
