# Standalone test harness

`run.php` stubs the WordPress and WooCommerce APIs required to exercise activation/uninstall, capability checks, REST method/nonce policy, product getters, strict order serialization, diagnostics, approval data and log redaction. It does not bootstrap WordPress, connect to MySQL, call external services or alter a store.

Run from the repository root:

```sh
php -d display_errors=1 tests/fm-gifts-control/run.php
find wordpress/wp-content/plugins/fm-gifts-control tests/fm-gifts-control -type f -name '*.php' -print0 | xargs -0 -n1 php -l
node --check wordpress/wp-content/plugins/fm-gifts-control/assets/control.js
```

This harness is not a substitute for the manual LOCAL / STAGING test plan in `MANUAL-TESTS.md`.
