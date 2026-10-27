<?php
defined( 'ABSPATH' ) || exit;
get_header();
$categories = array( 'Watches', 'Perfumes', 'Gifts', 'Kids', 'Accessories' );
?>
<section class="fm-hero"><div class="fm-wrap fm-hero-grid">
  <div><span class="fm-eyebrow"><?php esc_html_e( 'A thoughtful kind of luxury', 'fm-gifts-luxury' ); ?></span>
    <h1><?php esc_html_e( 'Gifts that make every moment memorable.', 'fm-gifts-luxury' ); ?></h1>
    <p><?php esc_html_e( 'Discover watches, fragrances, accessories and carefully chosen gifts for the people who matter.', 'fm-gifts-luxury' ); ?></p>
    <div class="fm-actions"><a class="fm-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Explore Collection', 'fm-gifts-luxury' ); ?></a><a class="fm-button fm-button-outline" href="#fm-categories"><?php esc_html_e( 'Browse Categories', 'fm-gifts-luxury' ); ?></a></div>
  </div>
  <div class="fm-hero-art" role="img" aria-label="<?php esc_attr_e( 'FM Gifts luxury collection', 'fm-gifts-luxury' ); ?>"><strong>FM<br><?php esc_html_e( 'Gifts & Luxury', 'fm-gifts-luxury' ); ?><br><span class="fm-eyebrow"><?php esc_html_e( 'Made to be remembered', 'fm-gifts-luxury' ); ?></span></strong></div>
</div></section>
<section class="fm-section" id="fm-categories"><div class="fm-wrap">
  <div class="fm-section-heading"><div><span class="fm-eyebrow"><?php esc_html_e( 'Find your perfect gift', 'fm-gifts-luxury' ); ?></span><h2><?php esc_html_e( 'Shop by Category', 'fm-gifts-luxury' ); ?></h2></div></div>
  <div class="fm-categories"><?php foreach ( $categories as $category ) :
    $term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'name', $category, 'product_cat' ) : false;
    $url = ( $term && ! is_wp_error( $term ) ) ? get_term_link( $term ) : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) );
  ?><a class="fm-category" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $category ); ?> <span aria-hidden="true">↗</span></a><?php endforeach; ?></div>
</div></section>
<section class="fm-section"><div class="fm-wrap">
  <div class="fm-section-heading"><div><span class="fm-eyebrow"><?php esc_html_e( 'Freshly selected', 'fm-gifts-luxury' ); ?></span><h2><?php esc_html_e( 'New Arrivals', 'fm-gifts-luxury' ); ?></h2><p class="fm-muted"><?php esc_html_e( 'Real products appear here when added in your store admin.', 'fm-gifts-luxury' ); ?></p></div><a class="fm-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'View all products →', 'fm-gifts-luxury' ); ?></a></div>
  <?php if ( class_exists( 'WooCommerce' ) ) : $products = wc_get_products( array( 'status' => 'publish', 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) ); ?>
    <?php if ( $products ) : ?><div class="fm-product-grid"><?php foreach ( $products as $product ) : ?><article class="fm-product">
      <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?><div class="fm-product-info"><span class="fm-eyebrow"><?php echo esc_html( wc_get_product_category_list( $product->get_id(), ', ' ) ? wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ' ) ) : 'FM Collection' ); ?></span><h3><?php echo esc_html( $product->get_name() ); ?></h3><div class="fm-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div></div></a>
    </article><?php endforeach; ?></div><?php else : ?><div class="fm-empty"><?php esc_html_e( 'Your verified product catalogue will appear here. Add real products in WooCommerce to populate this section.', 'fm-gifts-luxury' ); ?></div><?php endif; ?>
  <?php else : ?><div class="fm-empty"><?php esc_html_e( 'WooCommerce is not active yet. Install and configure it to enable products, cart and checkout.', 'fm-gifts-luxury' ); ?></div><?php endif; ?>
</div></section>
<section class="fm-section"><div class="fm-wrap fm-benefits">
  <div class="fm-benefit"><strong><?php esc_html_e( 'Carefully Chosen', 'fm-gifts-luxury' ); ?></strong><p class="fm-muted"><?php esc_html_e( 'A considered collection of gifts and accessories.', 'fm-gifts-luxury' ); ?></p></div>
  <div class="fm-benefit"><strong><?php esc_html_e( 'Pakistan Delivery', 'fm-gifts-luxury' ); ?></strong><p class="fm-muted"><?php esc_html_e( 'Delivery charge is planned at PKR 300; confirm courier rates before launch.', 'fm-gifts-luxury' ); ?></p></div>
  <div class="fm-benefit"><strong><?php esc_html_e( 'Payment Options', 'fm-gifts-luxury' ); ?></strong><p class="fm-muted"><?php esc_html_e( 'Cash on delivery can be enabled in WooCommerce. Online payment needs a verified gateway.', 'fm-gifts-luxury' ); ?></p></div>
</div></section>
<?php get_footer(); ?>