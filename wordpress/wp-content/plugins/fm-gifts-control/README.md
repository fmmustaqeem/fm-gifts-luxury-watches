# FM Gifts Control Panel — Phase 2 prototype

**Status:** Read-only source implementation; standalone behavioral safety tests included. Real LOCAL / STAGING WordPress + WooCommerce integration checks remain required.

## Scope
- Theme-independent WordPress admin panel with persistent `READ-ONLY PROTOTYPE` and `LOCAL / STAGING` labels.
- Read-only WooCommerce product views via `wc_get_products()` and product CRUD getters, including thumbnail and categories.
- Read-only order list/detail responses with a strict allowlist: `order_id`, `order_status`, `date`, `currency`, `item_count`, `total`, and line items `product_name`, `quantity`, `line_total` only.
- PII-aware log redaction for sensitive context keys, email, phone, IP and bearer tokens.
- Diagnostics for PHP/WP/WooCommerce, PHP extensions, REST, cron, HPOS, WooCommerce/plugin tables, theme and limited server metadata.
- Hosting identity remains `NOT VERIFIED`; outbound HTTPS remains `NOT TESTED`. No outbound request is made.
- Hardcoded `SIM-*` approval samples include proposed price/stock, stale, approved-not-executed, failed and rejected states. Browser state is memory-only; stale items cannot be approved.
- GET-only REST endpoints require login, `fm_gifts_control_manage`, and a valid WordPress REST nonce. No mutation route exists.

## Run local checks
```sh
php -d display_errors=1 tests/fm-gifts-control/run.php
find wordpress/wp-content/plugins/fm-gifts-control tests/fm-gifts-control -type f -name '*.php' -print0 | xargs -0 -n1 php -l
node --check wordpress/wp-content/plugins/fm-gifts-control/assets/control.js
```

## Not yet proven
The standalone harness uses WordPress/WooCommerce stubs. It does not replace real LOCAL / STAGING activation, REST, HPOS, browser, database-write-count or uninstall tests. See `tests/fm-gifts-control/MANUAL-TESTS.md`. Do not install or activate on live ByetHost without separate approval.
