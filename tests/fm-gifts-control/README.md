# Standalone test harness

`run.php` stubs the WordPress and WooCommerce APIs required to exercise activation/uninstall, capability checks, REST method/nonce policy, product getters, strict order serialization, diagnostics, approval data and log redaction. It does not bootstrap WordPress, connect to MySQL, call external services or alter a store.

Run from the repository root:

```sh
php -d display_errors=1 tests/fm-gifts-control/run.php
php -d display_errors=1 tests/fm-gifts-control/logger-redaction.test.php
find wordpress/wp-content/plugins/fm-gifts-control tests/fm-gifts-control -type f -name '*.php' -print0 | xargs -0 -n1 php -l
node --check wordpress/wp-content/plugins/fm-gifts-control/assets/control.js
node tests/fm-gifts-control/control-js.test.js
```

The JavaScript regression test uses only Node.js built-ins. It exercises asynchronous queue rendering, same-origin GET-only reads, browser-memory-only simulated approval, stale-item rejection and cross-origin fail-closed behavior.

This harness is not a substitute for the manual LOCAL / STAGING test plan in `MANUAL-TESTS.md`. The manual staging checks remain NOT RUN until executed against a disposable WordPress + WooCommerce instance.
