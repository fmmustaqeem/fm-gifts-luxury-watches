<?php
/**
 * FM Gifts Luxury theme setup.
 * @package FM_Gifts_Luxury
 */
defined( 'ABSPATH' ) || exit;
function fm_gifts_luxury_setup() {
    load_theme_textdomain( 'fm-gifts-luxury', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 300, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    register_nav_menus( array( 'primary' => __( 'Primary Menu', 'fm-gifts-luxury' ) ) );
}
add_action( 'after_setup_theme', 'fm_gifts_luxury_setup' );
function fm_gifts_luxury_assets() {
    wp_enqueue_style( 'fm-gifts-luxury', get_stylesheet_uri(), array(), '1.0.0' );
    wp_enqueue_script( 'fm-gifts-luxury', get_template_directory_uri() . '/assets/theme.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'fm_gifts_luxury_assets' );
function fm_gifts_luxury_body_class( $classes ) {
    if ( is_rtl() ) { $classes[] = 'fm-rtl'; }
    return $classes;
}
add_filter( 'body_class', 'fm_gifts_luxury_body_class' );