# FM Gifts Control Panel — Manual Test Plan

Run only on a disposable LOCAL / STAGING WordPress + WooCommerce copy. Never use the live ByetHost site.

## Preconditions
- WordPress 6.4+ and PHP 8.0+.
- WooCommerce available for product/order checks.
- A non-production administrator account.
- A disposable database/filesystem backup before activation.

## Ten acceptance criteria

1. **Bootstrap/compatibility:** plugin metadata declares WordPress 6.4+, PHP 8.0+, GPL-2.0-or-later; no build step is required.
2. **Activation scope:** activation adds only `fm_gifts_control_manage` and `fm_gifts_control_approve` to administrator and adds `fm_gifts_control_version=0.1.0`; no table is created.
3. **Uninstall safety:** uninstall removes only those two administrator capabilities and the version option; unrelated options, posts, products, orders and tables remain unchanged.
4. **Admin gate:** a user without `fm_gifts_control_manage` cannot open the menu or read REST routes; both safety labels remain visible to an authorized user.
5. **Products:** dashboard displays getters from `wc_get_products()` including image, name, SKU, price, stock status/quantity, categories and status; no mutation controls exist.
6. **Orders/PII:** inspect raw `/orders` and `/orders/{id}` JSON; only `order_id`, `order_status`, `date`, `currency`, `item_count`, `total`, and line-item `product_name`, `quantity`, `line_total` appear. Seed synthetic PII canaries and verify they never appear in responses or logs.
7. **REST security:** all five routes are GET-only and reject logged-out, insufficient-capability and invalid/missing-nonce requests; POST/PUT/PATCH/DELETE are not registered.
8. **Diagnostics:** PHP/WP/WooCommerce/extensions/REST/cron/HPOS/tables/theme and bounded server metadata are shown; hosting identity remains `NOT VERIFIED`, outbound HTTPS remains `NOT TESTED`, and no cron is executed.
9. **Sample approvals:** only `SIM-*` rows appear with proposed price/stock and stale, approved-not-executed, failed and rejected states. A non-stale pending row changes only in browser memory; stale rows cannot be approved; refresh resets state; no bulk action exists.
10. **No network/store mutations:** browser Network panel shows same-origin GET requests only. No outbound provider request, WooCommerce MCP, AI integration, product/order/price/stock/payment mutation or custom table is created.

## Evidence to capture
- PHP/WP/WooCommerce versions.
- Administrator capability diff before activation, after activation and after uninstall.
- Raw order list/detail JSON with PII canaries absent.
- REST route methods and denied-request status codes.
- Browser Network panel showing same-origin GET requests only.
- Product/order counts and DB table list before/after.
- Sample queue before approval, after simulated approval and after refresh.

These staging checks are **NOT RUN** by the standalone harness. Record the date, environment, command/screen and actual result before marking any manual criterion passed.
