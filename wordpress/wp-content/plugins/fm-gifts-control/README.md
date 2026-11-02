# FM Gifts Control Panel — Phase 2 prototype

**Status:** Source implementation with static safety checks; isolated WordPress/WooCommerce integration tests still required.

## Scope
- Read-only product and order views using WooCommerce CRUD APIs.
- Product thumbnails and categories are rendered in the dashboard.
- Order REST output is an allowlist; customer and payment identifiers are not serialized.
- Diagnostics report PHP/WP/WooCommerce, PHP extensions, WooCommerce tables, plugin-owned tables, active theme, and limited server metadata (hostname, server software, and distinct table engines).
- Hosting identity remains `NOT VERIFIED`; outbound HTTPS remains `NOT TESTED`. No outbound request is made.
- Approval queue is hardcoded sample data. UI state is browser-memory-only and never executes store changes.
- GET-only REST endpoints require a logged-in user, the custom capability, and a valid WordPress REST nonce.

## Files
- `fm-gifts-control.php`: plugin bootstrap, admin page, REST routes and serializers.
- `uninstall.php`: removes only the two plugin capabilities and its version option.
- `assets/`: dashboard presentation and browser-only sample queue behavior.
- `tests/fm-gifts-control/static-safety.php`: static source guard checks (not a WordPress integration test).

## Run local checks
```sh
php -l wordpress/wp-content/plugins/fm-gifts-control/fm-gifts-control.php
php -l wordpress/wp-content/plugins/fm-gifts-control/uninstall.php
php -l tests/fm-gifts-control/static-safety.php
php tests/fm-gifts-control/static-safety.php
```

## Not yet proven
Activation/uninstall database write counts, live WP capability enforcement, actual REST response PII inspection, HPOS behavior, and zero outbound HTTP require an isolated WordPress/WooCommerce test harness. Do not install on ByetHost or use production store data.
