FM Gifts AI Assistant
Contributors: FM
Tags: ai, openai, chatbot, customer service
Requires at least: 6.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later

A lightweight storefront chat assistant that calls OpenAI from the WordPress server. It does not process payments or read private orders.

Configuration
1. Obtain an OpenAI API key from the OpenAI platform and confirm API billing/limits.
2. Add this constant to wp-config.php ABOVE the "stop editing" comment:
   define( 'FM_GIFTS_OPENAI_API_KEY', 'YOUR_REAL_API_KEY_HERE' );
3. Replace the placeholder with the real key on the server only. Do not commit the key, paste it in chat, or put it in JavaScript.
4. Activate the plugin and test a chat message. Open Settings > FM Gifts AI to change the model if needed.

Default model: gpt-4o-mini. API usage may incur charges.
If the API key is not configured, the widget is displayed but chat replies return a setup error. This is deliberate: no fake AI replies are shown.

Security notes
- API key remains server-side.
- The endpoint limits each source IP to 10 requests per minute using a WordPress transient.
- Never enter passwords, card numbers, or one-time codes into the chat.
- Verify hosting supports outbound HTTPS requests to api.openai.com.
