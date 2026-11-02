<?php
declare(strict_types=1);

/** Dependency-free behavioral safety harness. No WordPress, DB or network is bootstrapped. */
if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/fake-wordpress/');
if (!defined('WC_VERSION')) define('WC_VERSION', '11.2.1-test');

$GLOBALS['fmgc_caps'] = [];
$GLOBALS['fmgc_options'] = ['unrelated_option' => 'keep-me'];
$GLOBALS['fmgc_option_writes'] = [];
$GLOBALS['fmgc_option_deletes'] = [];
$GLOBALS['fmgc_activation'] = null;
$GLOBALS['fmgc_routes'] = [];
$GLOBALS['fmgc_menu_args'] = [];
$GLOBALS['fmgc_localized'] = [];
$GLOBALS['fmgc_logged_in'] = true;
$GLOBALS['fmgc_can'] = ['fm_gifts_control_manage' => true, 'fm_gifts_control_approve' => true];
$GLOBALS['fmgc_nonce_valid'] = true;
$GLOBALS['fmgc_logs'] = [];

final class WP_Error
{
    public string $code;
    public string $message;
    public array $data;
    public function __construct(string $code, string $message, array $data = []) { $this->code = $code; $this->message = $message; $this->data = $data; }
}
final class WP_REST_Response
{
    private $data;
    private int $status;
    public function __construct($data = null, int $status = 200) { $this->data = $data; $this->status = $status; }
    public function get_data() { return $this->data; }
    public function get_status(): int { return $this->status; }
}
final class WP_REST_Server { public const READABLE = 'GET'; public function get_routes(): array { return $GLOBALS['fmgc_routes']; } }
final class WP_REST_Request
{
    private array $params;
    private array $headers;
    public function __construct(array $params = [], array $headers = []) { $this->params = $params; $this->headers = $headers; }
    public function get_param(string $key) { return $this->params[$key] ?? null; }
    public function get_header(string $key): string { return (string) ($this->headers[$key] ?? ''); }
}
final class Test_Role
{
    public function add_cap(string $cap): void { $GLOBALS['fmgc_caps'][$cap] = true; }
    public function remove_cap(string $cap): void { unset($GLOBALS['fmgc_caps'][$cap]); }
}
final class Test_DB
{
    public string $prefix = 'wp_';
    public function esc_like(string $value): string { return addcslashes($value, '_%\\'); }
    public function prepare(string $sql, string $value): string { return $sql . ' /* ' . $value . ' */'; }
    public function get_var(string $sql) { return false; }
    public function get_col(string $sql): array { return str_contains($sql, 'information_schema') ? ['InnoDB'] : []; }
}
final class Test_Date { public function date(string $format): string { return '2026-11-02T10:00:00+05:00'; } }
final class WC_Product
{
    public function get_id(): int { return 71; }
    public function get_name(): string { return 'Synthetic Watch'; }
    public function get_sku(): string { return 'SIM-W-071'; }
    public function get_price(): string { return '12500.00'; }
    public function get_stock_status(): string { return 'instock'; }
    public function get_stock_quantity(): int { return 8; }
    public function get_category_ids(): array { return [9]; }
    public function get_status(): string { return 'publish'; }
    public function get_image_id(): int { return 17; }
}
final class Test_Line_Item
{
    public function get_name(): string { return 'Synthetic Watch'; }
    public function get_quantity(): int { return 2; }
    public function get_total(): string { return '25000.00'; }
}
final class WC_Order
{
    public function get_id(): int { return 501; }
    public function get_status(): string { return 'processing'; }
    public function get_date_created(): Test_Date { return new Test_Date(); }
    public function get_currency(): string { return 'PKR'; }
    public function get_item_count(): int { return 2; }
    public function get_total(): string { return '25000.00'; }
    public function get_items(string $type = 'line_item'): array { return [new Test_Line_Item()]; }
    // Synthetic PII canaries: the production serializer must never call these.
    public function get_billing_email(): string { return 'pii-canary@example.invalid'; }
    public function get_billing_phone(): string { return '+92 300 1234567'; }
    public function get_customer_id(): int { return 987654; }
    public function get_transaction_id(): string { return 'TXN-PRIVATE-CANARY'; }
}

function register_activation_hook(string $file, $callback): void { $GLOBALS['fmgc_activation'] = $callback; }
function add_action(string $hook, $callback): void {}
function add_option(string $name, $value, $deprecated = '', $autoload = null): bool { $GLOBALS['fmgc_option_writes'][] = $name; if (array_key_exists($name, $GLOBALS['fmgc_options'])) return false; $GLOBALS['fmgc_options'][$name] = $value; return true; }
function delete_option(string $name): bool { $GLOBALS['fmgc_option_deletes'][] = $name; unset($GLOBALS['fmgc_options'][$name]); return true; }
function get_option(string $name, $default = false) { return $GLOBALS['fmgc_options'][$name] ?? $default; }
function get_role(string $name) { return $name === 'administrator' ? new Test_Role() : null; }
function is_user_logged_in(): bool { return $GLOBALS['fmgc_logged_in']; }
function current_user_can(string $cap): bool { return (bool) ($GLOBALS['fmgc_can'][$cap] ?? false); }
function wp_verify_nonce(string $nonce, string $action) { return $GLOBALS['fmgc_nonce_valid'] && $nonce === 'valid-nonce' && $action === 'wp_rest' ? 1 : false; }
function register_rest_route(string $namespace, string $route, array $args): void { $GLOBALS['fmgc_routes'][$namespace . $route] = $args; }
function is_wp_error($value): bool { return $value instanceof WP_Error; }
function absint($value): int { return abs((int) $value); }
function sanitize_text_field(string $value): string { return trim(strip_tags($value)); }
function sanitize_key(string $value): string { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower($value)); }
function esc_url_raw(string $value): string { return filter_var($value, FILTER_SANITIZE_URL) ?: ''; }
function wp_get_attachment_image_url(int $id, string $size): string { return 'https://local.test/uploads/' . $id . '.jpg'; }
function get_term(int $id, string $taxonomy) { return (object) ['name' => $id === 9 ? 'Watches' : 'Other']; }
function rest_get_server(): WP_REST_Server { return new WP_REST_Server(); }
function get_bloginfo(string $show): string { return $show === 'version' ? '7.1.4-test' : ''; }
function wp_get_ready_cron_jobs(): array { return ['hourly' => [], 'daily' => []]; }
function wp_get_theme() { return new class { public function get(string $key): string { return $key === 'Name' ? 'Test Theme' : '1.0'; } }; }
function rest_url(string $path): string { return 'https://local.test/wp-json/' . $path; }
function wp_create_nonce(string $action): string { return 'valid-nonce'; }
function wp_unslash(string $value): string { return $value; }
function __(string $value, string $domain = ''): string { return $value; }
function esc_html__(string $value, string $domain = ''): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function esc_html(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function wp_die(string $message): void { throw new RuntimeException($message); }
function add_menu_page(...$args): string { $GLOBALS['fmgc_menu_args'] = $args; return 'toplevel_page_fm-gifts-control'; }
function plugin_dir_url(string $file): string { return 'https://local.test/wp-content/plugins/fm-gifts-control/'; }
function wp_enqueue_style(...$args): void {}
function wp_enqueue_script(...$args): void {}
function wp_localize_script(string $handle, string $object, array $data): void { $GLOBALS['fmgc_localized'] = $data; }
function wc_get_products(array $args): array { return [new WC_Product()]; }
function wc_get_orders(array $args): array { return [new WC_Order()]; }
function wc_get_order(int $id) { return $id === 501 ? new WC_Order() : null; }
function wc_get_logger() { return new class { public function log(string $level, string $message, array $context): void { $GLOBALS['fmgc_logs'][] = [$level, $message, $context]; } }; }

function assert_true(bool $condition, string $label): void
{
    if (!$condition) {
        fwrite(STDERR, 'FAIL: ' . $label . PHP_EOL);
        exit(1);
    }
    echo 'PASS: ' . $label . PHP_EOL;
}

$pluginRoot = dirname(__DIR__, 2) . '/wordpress/wp-content/plugins/fm-gifts-control';
$pluginFile = $pluginRoot . '/fm-gifts-control.php';
$wpdb = new Test_DB();
require $pluginFile;

// 1. Activation is limited to two role capabilities and one version option.
assert_true(is_callable($GLOBALS['fmgc_activation']), 'activation callback is registered');
($GLOBALS['fmgc_activation'])();
assert_true(count($GLOBALS['fmgc_caps']) === 2 && isset($GLOBALS['fmgc_caps']['fm_gifts_control_manage'], $GLOBALS['fmgc_caps']['fm_gifts_control_approve']), 'activation adds exactly the two declared administrator capabilities');
assert_true($GLOBALS['fmgc_option_writes'] === ['fm_gifts_control_version'] && get_option('fm_gifts_control_version') === '0.1.0', 'activation writes only the version option');
assert_true(get_option('unrelated_option') === 'keep-me', 'activation preserves unrelated options');

// 2. Admin menu and asset bootstrapping remain capability-gated.
\\FMGifts\\Control\\Plugin::menu();
assert_true(($GLOBALS['fmgc_menu_args'][2] ?? '') === 'fm_gifts_control_manage', 'admin menu requires the manage capability');
\\FMGifts\\Control\\Plugin::assets('toplevel_page_fm-gifts-control');
assert_true(($GLOBALS['fmgc_localized']['canApprove'] ?? false) === true, 'approval UI capability is passed separately');

// 3. All REST endpoints are GET-only and enforce login, capability and nonce.
\\FMGifts\\Control\\Plugin::routes();
assert_true(count($GLOBALS['fmgc_routes']) === 5, 'five GET-only REST routes are registered');
foreach ($GLOBALS['fmgc_routes'] as $route) assert_true($route['methods'] === 'GET', 'route method is GET');
$validRequest = new WP_REST_Request([], ['X-WP-Nonce' => 'valid-nonce']);
assert_true(\\FMGifts\\Control\\Plugin::permission($validRequest), 'valid authenticated capability and nonce are accepted');
$GLOBALS['fmgc_nonce_valid'] = false;
assert_true(!\\FMGifts\\Control\\Plugin::permission($validRequest), 'invalid nonce is rejected');
$GLOBALS['fmgc_nonce_valid'] = true;
$GLOBALS['fmgc_logged_in'] = false;
assert_true(!\\FMGifts\\Control\\Plugin::permission($validRequest), 'logged-out request is rejected');
$GLOBALS['fmgc_logged_in'] = true;
$GLOBALS['fmgc_can']['fm_gifts_control_manage'] = false;
assert_true(!\\FMGifts\\Control\\Plugin::permission($validRequest), 'user without manage capability is rejected');
$GLOBALS['fmgc_can']['fm_gifts_control_manage'] = true;

// 4. Product data uses WooCommerce CRUD getters and is read-only.
$productResponse = \\FMGifts\\Control\\Plugin::products(new WP_REST_Request());
$productData = $productResponse->get_data();
assert_true($productResponse->get_status() === 200 && count($productData['items']) === 1, 'product endpoint returns a WooCommerce product list');
assert_true($productData['items'][0]['categories'] === ['Watches'] && $productData['items'][0]['stock_quantity'] === 8.0, 'product categories and stock are read via getters');

// 5. Order list/detail payloads expose exactly the allowlisted fields.
$orderResponse = \\FMGifts\\Control\\Plugin::orders(new WP_REST_Request());
$orderData = $orderResponse->get_data();
$expectedKeys = ['order_id', 'order_status', 'date', 'currency', 'item_count', 'total', 'line_items'];
assert_true($orderResponse->get_status() === 200 && array_keys($orderData[0]) === $expectedKeys, 'raw order list has only allowlisted top-level fields');
assert_true(array_keys($orderData[0]['line_items'][0]) === ['product_name', 'quantity', 'line_total'], 'raw order line items have only allowlisted fields');
$orderJson = json_encode($orderData, JSON_UNESCAPED_SLASHES);
assert_true(!str_contains($orderJson, 'pii-canary') && !str_contains($orderJson, '1234567') && !str_contains($orderJson, '987654') && !str_contains($orderJson, 'TXN-PRIVATE'), 'raw order JSON excludes PII and transaction canaries');
$detailResponse = \\FMGifts\\Control\\Plugin::order_detail(new WP_REST_Request(['id' => 501]));
assert_true($detailResponse instanceof WP_REST_Response && array_keys($detailResponse->get_data()) === $expectedKeys, 'order detail uses the same strict allowlist');
assert_true(is_wp_error(\\FMGifts\\Control\\Plugin::order_detail(new WP_REST_Request(['id' => 999]))), 'missing order detail returns a 404-style WP_Error');

// 6. Diagnostics report passive metadata and never claim unverified status.
$diagnostics = \\FMGifts\\Control\\Plugin::diagnostics(new WP_REST_Request())->get_data();
assert_true($diagnostics['hosting_identity'] === 'NOT VERIFIED' && $diagnostics['outbound_https'] === 'NOT TESTED', 'hosting and outbound status remain explicitly unverified/untested');
assert_true(isset($diagnostics['php_extensions'], $diagnostics['rest_api'], $diagnostics['cron'], $diagnostics['hpos'], $diagnostics['plugin_tables'], $diagnostics['active_theme'], $diagnostics['server_metadata']['table_engine']), 'diagnostics cover runtime, REST, cron, HPOS, plugin tables, theme and server metadata');

// 7. Approval queue is static, includes proposed values and all required states.
$approvalData = \\FMGifts\\Control\\Plugin::approvals(new WP_REST_Request())->get_data();
$approvalStates = array_column($approvalData['items'], 'state');
assert_true($approvalData['simulation_only'] === true && $approvalData['persistence'] === 'browser-memory-only', 'approval queue is marked simulation-only and browser-memory-only');
assert_true(count($approvalData['items']) === 5 && count(array_filter($approvalData['items'], static fn($item) => str_starts_with($item['id'], 'SIM-'))) === 5, 'approval queue contains only SIM-* IDs');
assert_true(isset($approvalData['items'][0]['proposed_price'], $approvalData['items'][0]['proposed_stock']), 'sample queue includes proposed price and stock values');
foreach (['stale', 'approved-not-executed', 'failed', 'rejected'] as $state) assert_true(in_array($state, $approvalStates, true), 'sample queue includes ' . $state . ' state');

// 8. Logger redacts sensitive keys and PII patterns before writing.
$redacted = \\FMGifts\\Control\\Logger::redact(['email' => 'owner@example.invalid', 'message' => 'owner@example.invalid 192.0.2.10 +92 300 1234567 Bearer private-token']);
$redactedJson = json_encode($redacted);
assert_true(!str_contains($redactedJson, 'owner@example.invalid') && !str_contains($redactedJson, '192.0.2.10') && !str_contains($redactedJson, '1234567') && !str_contains($redactedJson, 'private-token'), 'logger redactor removes email, IP, phone and bearer-token values');
\\FMGifts\\Control\\Logger::log('warning', 'Synthetic log entry', ['customer_email' => 'owner@example.invalid']);
assert_true(isset($GLOBALS['fmgc_logs'][0][2]['context']['customer_email']) && $GLOBALS['fmgc_logs'][0][2]['context']['customer_email'] === '[REDACTED]', 'logger writes redacted context only');

// 9. Source-level guard: no outbound server HTTP clients or browser mutation methods.
$phpFiles = glob($pluginRoot . '/*.php');
$phpFiles = array_merge($phpFiles ?: [], glob($pluginRoot . '/includes/*.php') ?: []);
$allPhp = implode("\\n", array_map('file_get_contents', $phpFiles));
assert_true(!preg_match('/\\bwp_(?:safe_)?remote_(?:get|post|request)|curl_init\\s*\\(|curl_exec\\s*\\(|fsockopen\\s*\\(|stream_socket_client\\s*\\(/i', $allPhp), 'plugin contains no outbound PHP HTTP/socket client');
assert_true(!preg_match('/register_rest_route\\([^;]+[\\'\\"](?:POST|PUT|PATCH|DELETE)[\\'\\"]/is', $allPhp), 'plugin declares no mutation REST methods');
$js = file_get_contents($pluginRoot . '/assets/control.js');
assert_true(str_contains($js, \"method:'GET'\") && str_contains($js, 'url.origin !== window.location.origin'), 'browser requests are GET-only and fail closed on cross-origin URLs');
assert_true(!preg_match('/method:\\s*[\\'\\"](?:POST|PUT|PATCH|DELETE)[\\'\\"]/', $js) && !str_contains($js, 'localStorage') && !str_contains($js, 'sessionStorage'), 'browser UI has no mutation request or persistent approval storage');
assert_true(str_contains($js, 'item.stale') && str_contains($js, \"item.state !== 'pending'\"), 'stale and non-pending sample items cannot be approved');

// 10. Uninstall removes only the plugin capabilities and version option.
$GLOBALS['fmgc_options']['unrelated_option'] = 'keep-me';
define('WP_UNINSTALL_PLUGIN', true);
require $pluginRoot . '/uninstall.php';
assert_true(!isset($GLOBALS['fmgc_caps']['fm_gifts_control_manage']) && !isset($GLOBALS['fmgc_caps']['fm_gifts_control_approve']), 'uninstall removes the two plugin capabilities');
assert_true($GLOBALS['fmgc_option_deletes'] === ['fm_gifts_control_version'] && get_option('unrelated_option') === 'keep-me', 'uninstall deletes only the version option and preserves unrelated data');
assert_true(!preg_match('/\\b(?:CREATE|ALTER|DROP|TRUNCATE)\\s+TABLE\\b/i', $allPhp), 'plugin source creates or destroys no database tables');

echo PHP_EOL . 'All standalone FM Gifts Control Panel safety tests passed.' . PHP_EOL;
