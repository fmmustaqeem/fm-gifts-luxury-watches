<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="fm-section"><div class="fm-wrap">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<article <?php post_class(); ?>><h1><?php the_title(); ?></h1><div><?php the_content(); ?></div></article>
<?php endwhile; the_posts_pagination(); else : ?><h1><?php esc_html_e( 'Welcome to FM Gifts', 'fm-gifts-luxury' ); ?></h1><p><?php esc_html_e( 'No content is published here yet.', 'fm-gifts-luxury' ); ?></p><?php endif; ?>
</div></section>
<?php get_footer(); ?>