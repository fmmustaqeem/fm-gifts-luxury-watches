# FM Gifts Control Panel — Phase 2 Work Order

**Status:** AI implementation requested; review required  
**Repository:** fmmustaqeem/fm-gifts-luxury-watches  
**Working branch:** feat/fm-gifts-control-panel-phase2-2026-11-02  
**Tracking issue:** #43  
**Target:** isolated LOCAL/STAGING WordPress + WooCommerce only

## Objective
Build a theme-independent WordPress plugin named `FM Gifts Control Panel` (`fm-gifts-control`) for read-only administration and diagnostics. PHP 8.0+, WordPress 6.4+, GPL-2.0-or-later, namespace `FMGifts\\Control`, REST namespace `fm-gifts-control/v1`. No build step; vanilla CSS/JS.

## Implementation checklist
- [ ] Plugin bootstrap, activation and uninstall. Activation may add only `fm_gifts_control_manage` and `fm_gifts_control_approve` to administrator and `fm_gifts_control_version=0.1.0`. Uninstall removes only those capabilities and the version option; never remove store data.
- [ ] Admin menu/dashboard, capability gate, black/gold styling, persistent `READ-ONLY PROTOTYPE` and `LOCAL / STAGING` labels.
- [ ] Read-only products view via WooCommerce CRUD (`wc_get_products()` and getters): thumbnail, name, SKU, price, stock status/quantity, categories, status. No mutation paths.
- [ ] Read-only orders view. Raw REST payload allowlist only: `order_id`, `order_status`, date, currency, item_count, total, and line_items containing product_name, quantity, line_total. Exclude names, emails, phone, addresses, IP, payment/transaction details, notes, customer ID and WP user ID. Redact logs.
- [ ] Diagnostics: runtime PHP/WP/WooCommerce/extensions/REST/cron/HPOS/plugin tables/theme; server metadata only for hostname/software/table engine; hosting identity always `NOT VERIFIED`; outbound HTTPS always `NOT TESTED`.
- [ ] Sample approval queue: hardcoded sample data only, SIM- IDs, browser-memory-only, inert simulation labels, no DB table, no execution. Include proposed price/stock, stale, approved-not-executed, failed, rejected; stale cannot approve; no bulk approve.
- [ ] GET-only routes for products, orders, order detail, diagnostics, sample approvals. Require login, capability and nonce. No POST/PUT/PATCH/DELETE routes.
- [ ] Tests for PHP syntax, activation writes, uninstall safety, PII-free raw JSON, GET-only routes, zero outbound HTTP, and all 10 acceptance criteria.

## Non-negotiable boundaries
- Work only on this branch. Do not modify `main`, merge, or alter existing PRs.
- Do not run or dispatch GitHub Actions without explicit approval.
- No deployment, FTP, Cloudflare, secrets, or production workflow changes.
- Never install/activate on the live ByetHost site.
- No outbound HTTP, AI provider, WooCommerce MCP, external integration, or agent connection.
- Do not alter real products, orders, stock, prices, payments, or store settings.
- Do not invent products, hosting identity, test outputs, or PASS claims.

## Evidence/reporting
For each completed item record file paths, commit SHA, exact test command and actual output. Mark unrun tests as NOT RUN and blockers explicitly. Any failed acceptance test means Phase 2 is not complete. Keep the work in a draft PR for human review; merge, live activation and deployment require separate approval.
