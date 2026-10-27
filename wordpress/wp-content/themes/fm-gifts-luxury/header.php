<?php
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="fm-topbar"><?php esc_html_e( 'LUXURY GIFTS • DELIVERY CHARGES PKR 300 • CASH ON DELIVERY AVAILABLE', 'fm-gifts-luxury' ); ?></div>
<header class="fm-header">
  <div class="fm-header-inner">
    <a class="fm-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'FM Gifts & Luxury Watches' ); ?><small><?php esc_html_e( 'Gifts • Watches • Fragrances', 'fm-gifts-luxury' ); ?></small></a>
    <button class="fm-menu-button" type="button" aria-expanded="false" aria-controls="fm-primary-nav"><?php esc_html_e( 'Menu', 'fm-gifts-luxury' ); ?></button>
    <nav class="fm-nav" id="fm-primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'fm-gifts-luxury' ); ?>">
      <?php if ( has_nav_menu( 'primary' ) ) { wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'items_wrap' => '%3$s' ) ); } else { ?>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'fm-gifts-luxury' ); ?></a>
      <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'fm-gifts-luxury' ); ?></a>
      <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'fm-gifts-luxury' ); ?></a>
      <?php } ?>
      <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?><a class="fm-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Cart', 'fm-gifts-luxury' ); ?></a><?php endif; ?>
    </nav>
  </div>
</header>
<main id="main-content">