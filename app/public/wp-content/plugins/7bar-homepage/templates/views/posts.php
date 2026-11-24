<?php
/**
 * Any other listing (blog, archives): titles and excerpts in the shared styles.
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="bar7-pagehead bar7-pagehead--compact" data-intro>
	<div class="bar7-wrap">
		<?php bar7_breadcrumbs(); ?>
		<h1 class="bar7-h1" data-intro-item><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ? get_the_archive_title() : __( 'Journal', 'bar7' ) ) ); ?></h1>
	</div>
</header>
<div class="bar7-wrap bar7-page">
	<?php if ( have_posts() ) : ?>
		<ul class="bar7-postlist" data-reveal-grid>
			<?php while ( have_posts() ) : the_post(); ?>
				<li>
					<h2 class="bar7-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="bar7-sub"><?php echo esc_html( get_the_date() ); ?></p>
					<div class="bar7-prose"><?php the_excerpt(); ?></div>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="bar7-empty"><h2 class="bar7-h3"><?php esc_html_e( 'Nothing here yet.', 'bar7' ); ?></h2></div>
	<?php endif; ?>
</div>
