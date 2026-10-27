# FM Gifts WordPress Theme & Plugins

This directory contains an isolated WordPress theme and two plugins for FM Gifts & Luxury Watches. FM Commerce is separate; its repository, credentials and deployment settings are not changed.

## Packages
- Theme: `wp-content/themes/fm-gifts-luxury/`
- Store helper plugin: `wp-content/plugins/fm-gifts-core/`
- AI chat plugin: `wp-content/plugins/fm-gifts-ai/`

## Package ZIPs
Run **Actions → Package FM Gifts WordPress ZIPs → Run workflow** manually. The workflow PHP-lints the code and uploads three separate ZIPs as a workflow artifact. It does not deploy to the live website.

## AI assistant configuration
The AI widget is real server-side OpenAI integration, not a canned-response demo. It will not produce answers until the server has a valid API key:
1. Obtain an OpenAI API key and confirm API billing/usage limits.
2. Add `define( 'FM_GIFTS_OPENAI_API_KEY', 'YOUR_REAL_API_KEY_HERE' );` to `wp-config.php` above the “stop editing” comment.
3. Replace the placeholder on the server only. Never paste the key into chat, commit it to GitHub, or put it in browser JavaScript.
4. Activate FM Gifts AI Assistant and test one message. Default model is `gpt-4o-mini`; API usage may incur charges.
5. Ensure hosting allows outbound HTTPS requests to `api.openai.com`.

## Installation and validation
1. Back up the FM Gifts WordPress site.
2. Install the theme ZIP through **Appearance → Themes → Add New Theme → Upload Theme**.
3. Install both plugin ZIPs through **Plugins → Add New Plugin → Upload Plugin**.
4. Activate WooCommerce, FM Gifts Core, and FM Gifts AI Assistant as applicable.
5. Configure the delivery method in a shipping zone. The initial rate is PKR 300; confirm actual courier costs and destination rules before launch.
6. Configure WooCommerce Cash on Delivery. Easypaisa/advance online payment is not included; add a verified gateway if required.
7. Add verified products, stock, images and prices. The theme intentionally does not invent actual inventory.
8. Test mobile layout, RTL, product page, cart, checkout, email delivery, delivery rates and AI before launch.

## Limitations
- The AI can answer general store questions but is not connected to private order records, payment processing or real inventory data. It must not be represented as an order-tracking agent.
- AI endpoint has a basic per-IP transient rate limit. Review abuse controls and host capacity before public launch.
- The plugin expects the OpenAI Responses API and a model available to your API account.
- The theme/plugin code is committed on the open draft branch/PR only. No production upload, activation, packaging workflow run, or deployment has been performed.
