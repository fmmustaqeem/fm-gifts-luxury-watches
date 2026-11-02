<?php
/**
 * Plugin Name: FM Gifts Control Panel
 * Description: Read-only LOCAL / STAGING administration and diagnostics for FM Gifts.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * License: GPL-2.0-or-later
 * Text Domain: fm-gifts-control
 */
namespace FMGifts\Control;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/class-logger.php';

final class Plugin
{
    public const VERSION = '0.1.0';
    public const CAP_MANAGE = 'fm_gifts_control_manage';
    public const CAP_APPROVE = 'fm_gifts_control_approve';
    public const REST_NS = 'fm-gifts-control/v1';

    public static function boot(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
        add_action('rest_api_init', [self::class, 'routes']);
    }

    public static function activate(): void
    {
        $role = get_role('administrator');
        if ($role) {
            $role->add_cap(self::CAP_MANAGE);
            $role->add_cap(self::CAP_APPROVE);
        }
        add_option('fm_gifts_control_version', self::VERSION, '', false);
    }

    public static function menu(): void
    {
        add_menu_page(
            __('FM Gifts Control', 'fm-gifts-control'),
            __('FM Gifts Control', 'fm-gifts-control'),
            self::CAP_MANAGE,
            'fm-gifts-control',
            [self::class, 'render'],
            'dashicons-shield-alt',
            58
        );
    }

    public static function assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_fm-gifts-control' || !current_user_can(self::CAP_MANAGE)) {
            return;
        }
        wp_enqueue_style('fm-gifts-control', plugin_dir_url(__FILE__) . 'assets/control.css', [], self::VERSION);
        wp_enqueue_script('fm-gifts-control', plugin_dir_url(__FILE__) . 'assets/control.js', [], self::VERSION, true);
        wp_localize_script('fm-gifts-control', 'FMGiftsControl', [
            'restUrl' => esc_url_raw(rest_url(self::REST_NS . '/')),
            'nonce' => wp_create_nonce('wp_rest'),
            'canApprove' => current_user_can(self::CAP_APPROVE),
        ]);
    }

    public static function render(): void
    {
        if (!current_user_can(self::CAP_MANAGE)) {
            wp_die(esc_html__('You are not allowed to access this page.', 'fm-gifts-control'));
        }
        ?>
        <div class="wrap fmgc-wrap">
            <header class="fmgc-header">
                <div><p class="fmgc-kicker">FM GIFTS &amp; LUXURY WATCHES</p><h1>Control Panel</h1><p>Read-only store overview and environment diagnostics</p></div>
                <div class="fmgc-badges"><span>READ-ONLY PROTOTYPE</span><span>LOCAL / STAGING</span></div>
            </header>
            <div class="fmgc-notice"><strong>Safe mode:</strong> This prototype does not change products, orders, prices, stock, payments, or store settings.</div>
            <nav class="fmgc-tabs" aria-label="Control panel sections">
                <a href="#fmgc-overview">Overview</a><a href="#fmgc-products">Products</a><a href="#fmgc-orders">Orders</a><a href="#fmgc-diagnostics">Diagnostics</a><a href="#fmgc-approvals">Simulation queue</a>
            </nav>
            <section id="fmgc-overview" class="fmgc-grid">
                <article class="fmgc-card"><span class="fmgc-label">WooCommerce</span><strong><?php echo defined('WC_VERSION') ? esc_html(WC_VERSION) : 'NOT AVAILABLE'; ?></strong><small>Detected locally; no connection test performed</small></article>
                <article class="fmgc-card"><span class="fmgc-label">PHP runtime</span><strong><?php echo esc_html(PHP_VERSION); ?></strong><small>Current WordPress runtime</small></article>
                <article class="fmgc-card"><span class="fmgc-label">Hosting identity</span><strong>NOT VERIFIED</strong><small>No hosting provider inference</small></article>
                <article class="fmgc-card"><span class="fmgc-label">Outbound HTTPS</span><strong>NOT TESTED</strong><small>No outbound request is made</small></article>
            </section>
            <section id="fmgc-products" class="fmgc-section"><h2>Products</h2><p>Read-only WooCommerce product data. No edit or mutation controls are provided.</p><div id="fmgc-products-content" class="fmgc-result" data-endpoint="products">Loading products…</div></section>
            <section id="fmgc-orders" class="fmgc-section"><h2>Orders</h2><p>Only allowlisted order fields are returned. Customer-identifying and payment details are excluded at the REST serialization layer.</p><div id="fmgc-orders-content" class="fmgc-result" data-endpoint="orders">Loading redacted order summary…</div></section>
            <section id="fmgc-diagnostics" class="fmgc-section"><h2>Diagnostics</h2><div id="fmgc-diagnostics-content" class="fmgc-result">Loading environment diagnostics…</div></section>
            <section id="fmgc-approvals" class="fmgc-section"><h2>Simulation queue</h2><p>Sample-only, browser-memory simulation. Nothing is saved or executed.</p><div id="fmgc-approvals-content" class="fmgc-result">Loading sample queue…</div></section>
            <footer class="fmgc-footer">FM Gifts Control Panel v<?php echo esc_html(self::VERSION); ?> · Local/staging prototype · No live-site deployment</footer>
        </div>
        <?php
    }

    public static function permission(\WP_REST_Request $request): bool
    {
        $nonce = wp_verify_nonce((string) $request->get_header('X-WP-Nonce'), 'wp_rest');
        return is_user_logged_in() && current_user_can(self::CAP_MANAGE) && $nonce !== false;
    }

    public static function routes(): void
    {
        $get = \WP_REST_Server::READABLE;
        register_rest_route(self::REST_NS, '/products', ['methods' => $get, 'callback' => [self::class, 'products'], 'permission_callback' => [self::class, 'permission']]);
        register_rest_route(self::REST_NS, '/orders', ['methods' => $get, 'callback' => [self::class, 'orders'], 'permission_callback' => [self::class, 'permission']]);
        register_rest_route(self::REST_NS, '/orders/(?P<id>\d+)', ['methods' => $get, 'callback' => [self::class, 'order_detail'], 'permission_callback' => [self::class, 'permission']]);
        register_rest_route(self::REST_NS, '/diagnostics', ['methods' => $get, 'callback' => [self::class, 'diagnostics'], 'permission_callback' => [self::class, 'permission']]);
        register_rest_route(self::REST_NS, '/approvals', ['methods' => $get, 'callback' => [self::class, 'approvals'], 'permission_callback' => [self::class, 'permission']]);
    }

    public static function products(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!function_exists('wc_get_products')) {
            return new \WP_REST_Response(['items' => [], 'status' => 'woocommerce_unavailable'], 503);
        }
        try {
            $products = wc_get_products(['limit' => 50, 'status' => 'any', 'return' => 'objects']);
        } catch (\Throwable $error) {
            Logger::log('error', 'Read-only product query failed.', ['exception' => get_class($error)]);
            return new \WP_REST_Response(['items' => [], 'status' => 'query_failed'], 500);
        }

        $items = [];
        foreach ((array) $products as $product) {
            if (!$product instanceof \WC_Product) {
                continue;
            }
            $categories = [];
            foreach ((array) $product->get_category_ids() as $categoryId) {
                $term = get_term((int) $categoryId, 'product_cat');
                if (is_object($term) && !is_wp_error($term) && isset($term->name)) {
                    $categories[] = sanitize_text_field((string) $term->name);
                }
            }
            $imageId = (int) $product->get_image_id();
            $imageUrl = $imageId ? wp_get_attachment_image_url($imageId, 'thumbnail') : '';
            $items[] = [
                'id' => (int) $product->get_id(),
                'name' => sanitize_text_field((string) $product->get_name()),
                'sku' => sanitize_text_field((string) $product->get_sku()),
                'price' => (string) $product->get_price(),
                'stock_status' => sanitize_key((string) $product->get_stock_status()),
                'stock_quantity' => is_numeric($product->get_stock_quantity()) ? (float) $product->get_stock_quantity() : null,
                'categories' => array_values(array_unique($categories)),
                'status' => sanitize_key((string) $product->get_status()),
                'image' => $imageUrl ? esc_url_raw((string) $imageUrl) : '',
            ];
        }
        return new \WP_REST_Response(['items' => $items, 'count' => count($items)], 200);
    }

    private static function safe_order(\WC_Order $order): array
    {
        $lineItems = [];
        foreach ($order->get_items('line_item') as $item) {
            $lineItems[] = [
                'product_name' => sanitize_text_field((string) $item->get_name()),
                'quantity' => (int) $item->get_quantity(),
                'line_total' => (string) $item->get_total(),
            ];
        }
        $created = $order->get_date_created();
        return [
            'order_id' => (int) $order->get_id(),
            'order_status' => sanitize_key((string) $order->get_status()),
            'date' => $created ? $created->date('c') : null,
            'currency' => sanitize_text_field((string) $order->get_currency()),
            'item_count' => method_exists($order, 'get_item_count') ? (int) $order->get_item_count() : count($lineItems),
            'total' => (string) $order->get_total(),
            'line_items' => $lineItems,
        ];
    }

    public static function orders(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!function_exists('wc_get_orders')) {
            return new \WP_REST_Response([], 503);
        }
        try {
            $orders = wc_get_orders(['limit' => 30, 'orderby' => 'date', 'order' => 'DESC', 'return' => 'objects']);
        } catch (\Throwable $error) {
            Logger::log('error', 'Read-only order query failed.', ['exception' => get_class($error)]);
            return new \WP_REST_Response([], 500);
        }
        $items = [];
        foreach ((array) $orders as $order) {
            if ($order instanceof \WC_Order) {
                $items[] = self::safe_order($order);
            }
        }
        // The raw response is a list of allowlisted order records, not an envelope
        // containing arbitrary order/customer objects or extra metadata.
        return new \WP_REST_Response($items, 200);
    }

    public static function order_detail(\WP_REST_Request $request)
    {
        if (!function_exists('wc_get_order')) {
            return new \WP_Error('woocommerce_unavailable', 'WooCommerce is unavailable.', ['status' => 503]);
        }
        $order = wc_get_order(absint($request->get_param('id')));
        if (!$order instanceof \WC_Order) {
            return new \WP_Error('not_found', 'Order not found.', ['status' => 404]);
        }
        return new \WP_REST_Response(self::safe_order($order), 200);
    }

    public static function diagnostics(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $extensions = ['curl', 'openssl', 'mysqli', 'mbstring', 'json', 'zip', 'gd', 'intl', 'fileinfo', 'xml'];
        $extensionStatus = [];
        foreach ($extensions as $extension) {
            $extensionStatus[$extension] = extension_loaded($extension);
        }

        $woocommerceTables = [];
        $pluginTables = [];
        $engines = [];
        if (is_object($wpdb)) {
            foreach (['woocommerce_sessions', 'wc_orders', 'wc_order_addresses', 'wc_order_operational_data', 'wc_orders_meta'] as $suffix) {
                $table = $wpdb->prefix . $suffix;
                $woocommerceTables[$suffix] = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) === $table;
            }
            $pluginPrefix = $wpdb->prefix . 'fm_gifts_control_';
            $pluginTables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($pluginPrefix) . '%'));
            $engines = $wpdb->get_col('SELECT DISTINCT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND ENGINE IS NOT NULL ORDER BY ENGINE');
        }

        $hpos = 'NOT AVAILABLE';
        $orderUtil = '\\Automattic\\WooCommerce\\Utilities\\OrderUtil';
        if (class_exists($orderUtil) && method_exists($orderUtil, 'custom_orders_table_usage_is_enabled')) {
            try {
                $hpos = $orderUtil::custom_orders_table_usage_is_enabled() ? 'ENABLED' : 'DISABLED';
            } catch (\Throwable $error) {
                Logger::log('warning', 'HPOS status lookup failed.', ['exception' => get_class($error)]);
            }
        }

        $readyCronGroups = function_exists('wp_get_ready_cron_jobs') ? count((array) wp_get_ready_cron_jobs()) : null;
        $serverSoftware = isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field((string) wp_unslash($_SERVER['SERVER_SOFTWARE'])) : 'NOT REPORTED';
        $hostname = function_exists('gethostname') ? gethostname() : false;

        return new \WP_REST_Response([
            'php' => PHP_VERSION,
            'wordpress' => get_bloginfo('version'),
            'woocommerce' => defined('WC_VERSION') ? WC_VERSION : 'NOT AVAILABLE',
            'php_extensions' => $extensionStatus,
            'memory_limit' => (string) ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'rest_api' => ['namespace' => self::REST_NS, 'registered_routes' => count((array) rest_get_server()->get_routes())],
            'cron' => ['enabled' => !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON, 'ready_event_group_count' => $readyCronGroups, 'execution' => 'NOT TRIGGERED'],
            'hpos' => $hpos,
            'woocommerce_tables' => $woocommerceTables,
            'plugin_tables' => array_values(array_map('sanitize_text_field', (array) $pluginTables)),
            'active_plugin_count' => count((array) get_option('active_plugins', [])),
            'active_theme' => wp_get_theme()->get('Name'),
            'server_metadata' => [
                'hostname' => $hostname ? sanitize_text_field((string) $hostname) : 'NOT REPORTED',
                'software' => $serverSoftware,
                'table_engine' => is_array($engines) && $engines ? implode(', ', array_map('sanitize_text_field', $engines)) : 'NOT REPORTED',
            ],
            'hosting_identity' => 'NOT VERIFIED',
            'outbound_https' => 'NOT TESTED',
        ], 200);
    }

    public static function approvals(\WP_REST_Request $request): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'simulation_only' => true,
            'persistence' => 'browser-memory-only',
            'items' => [
                ['id' => 'SIM-1001', 'type' => 'proposed_price', 'label' => 'Sample product price proposal', 'proposed_price' => '12500.00', 'proposed_stock' => 8, 'state' => 'pending', 'stale' => false],
                ['id' => 'SIM-1002', 'type' => 'proposed_stock', 'label' => 'Stale stock proposal', 'proposed_price' => '4800.00', 'proposed_stock' => 3, 'state' => 'stale', 'stale' => true],
                ['id' => 'SIM-1003', 'type' => 'price', 'label' => 'Approved sample — not executed', 'proposed_price' => '9200.00', 'proposed_stock' => 12, 'state' => 'approved-not-executed', 'stale' => false],
                ['id' => 'SIM-1004', 'type' => 'stock', 'label' => 'Failed sample request', 'proposed_price' => '6500.00', 'proposed_stock' => 0, 'state' => 'failed', 'stale' => false],
                ['id' => 'SIM-1005', 'type' => 'price', 'label' => 'Rejected sample request', 'proposed_price' => '3100.00', 'proposed_stock' => 4, 'state' => 'rejected', 'stale' => false],
            ],
        ], 200);
    }
}

register_activation_hook(__FILE__, [Plugin::class, 'activate']);
Plugin::boot();
