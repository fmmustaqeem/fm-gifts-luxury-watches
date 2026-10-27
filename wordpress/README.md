# FM Gifts WordPress Theme & Plugin

This directory contains an **isolated WordPress theme and plugin for FM Gifts & Luxury Watches**. It is separate from the FM Commerce repository; no FM Commerce files or deployment settings are changed.

## Packages
- Theme: `wp-content/themes/fm-gifts-luxury/`
- Plugin: `wp-content/plugins/fm-gifts-core/`

## Package ZIPs
Run the GitHub Actions workflow **Package FM Gifts WordPress ZIPs** manually from the Actions tab. It validates PHP syntax and uploads two ZIP files as a workflow artifact. This workflow does not deploy to the live website.

## Installation checklist
1. Take a backup of the FM Gifts WordPress site.
2. Upload/install the theme ZIP through **Appearance → Themes → Add New Theme → Upload Theme**.
3. Upload/install the plugin ZIP through **Plugins → Add New Plugin → Upload Plugin**.
4. Install/activate WooCommerce if not already installed.
5. Configure a shipping zone and add **FM Gifts Delivery**. Check the destination coverage and real courier price before enabling it.
6. Add verified products, images, stock, SKUs and prices in WooCommerce. The homepage intentionally does not invent real inventory.
7. Test product page, cart, checkout, mobile navigation, RTL, order emails and delivery charges before launch.

## Important limitations
- The theme provides a black-and-gold storefront, category links and a New Arrivals section that reads published WooCommerce products.
- The plugin provides a configurable flat-rate delivery method with a default of PKR 300. This is a starting configuration, not a verified courier quote.
- WooCommerce Cash on Delivery can be enabled in WooCommerce settings. **Easypaisa and advance online payments are not activated by this code**; use a legitimate supported payment gateway and configure its merchant credentials securely.
- No AI/API key is exposed in browser code. OpenAI integration is not included in this first package.
- Review and test on staging first. This branch/PR is not merged and no deployment was triggered.
