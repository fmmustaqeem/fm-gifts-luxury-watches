<?php
/**
 * Plugin Name: FM Gifts Core
 * Description: Store-specific shipping helper and small utilities for FM Gifts & Luxury Watches. Requires WooCommerce.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: FM
 * License: GPL-2.0-or-later
 * Text Domain: fm-gifts-core
 */
defined( 'ABSPATH' ) || exit;
define( 'FM_GIFTS_CORE_VERSION', '1.0.0' );
add_action( 'before_woocommerce_init', function () {
    if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
        \\Automattic\\WooCommerce\\Utilities\\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_woocommerce' ) || class_exists( 'WooCommerce' ) ) { return; }
    echo '<div class="notice notice-warning"><p>' . esc_html__( 'FM Gifts Core requires WooCommerce. Install and configure WooCommerce to use shipping settings.', 'fm-gifts-core' ) . '</p></div>';
} );
add_filter( 'woocommerce_shipping_methods', function ( $methods ) {
    $methods['fm_gifts_flat_rate'] = 'FM_Gifts_Flat_Rate';
    return $methods;
} );
add_action( 'woocommerce_shipping_init', function () {
    if ( class_exists( 'FM_Gifts_Flat_Rate' ) || ! class_exists( 'WC_Shipping_Method' ) ) { return; }
    class FM_Gifts_Flat_Rate extends WC_Shipping_Method {
        public function __construct( $instance_id = 0 ) {
            $this->id = 'fm_gifts_flat_rate';
            $this->instance_id = absint( $instance_id );
            $this->method_title = __( 'FM Gifts Delivery', 'fm-gifts-core' );
            $this->method_description = __( 'Flat-rate delivery for FM Gifts. Confirm courier costs before enabling at checkout.', 'fm-gifts-core' );
            $this->supports = array( 'shipping-zones', 'instance-settings', 'instance-settings-modal' );
            $this->init();
        }
        public function init() {
            $this->init_instance_form_fields();
            $this->init_settings();
            $this->title = $this->get_option( 'title', __( 'Standard Delivery', 'fm-gifts-core' ) );
            $this->cost = $this->get_option( 'cost', '300' );
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
        }
        public function init_instance_form_fields() {
            $this->instance_form_fields = array(
                'title' => array( 'title' => __( 'Method title', 'fm-gifts-core' ), 'type' => 'text', 'default' => __( 'Standard Delivery', 'fm-gifts-core' ) ),
                'cost' => array( 'title' => __( 'Delivery cost (PKR)', 'fm-gifts-core' ), 'type' => 'price', 'default' => '300', 'description' => __( 'Default is PKR 300. Adjust to your actual courier policy.', 'fm-gifts-core' ) ),
            );
        }
        public function calculate_shipping( $package = array() ) {
            $cost = max( 0, (float) wc_format_decimal( $this->cost ) );
            $this->add_rate( array( 'id' => $this->get_rate_id(), 'label' => $this->title, 'cost' => $cost, 'package' => $package ) );
        }
    }
} );
add_shortcode( 'fm_gifts_delivery_note', function () {
    return '<p class="fm-gifts-delivery-note">' . esc_html__( 'Standard delivery: PKR 300. Final cost may vary by destination and courier policy.', 'fm-gifts-core' ) . '</p>';
} );