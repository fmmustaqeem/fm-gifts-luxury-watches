<?php
/**
 * Plugin Name: FM Gifts AI Assistant
 * Description: Optional storefront AI assistant for FM Gifts. Requires a server-side FM_GIFTS_OPENAI_API_KEY constant.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: FM
 * License: GPL-2.0-or-later
 * Text Domain: fm-gifts-ai
 */
defined( 'ABSPATH' ) || exit;

final class FM_Gifts_AI_Assistant {
    const REST_NAMESPACE = 'fm-gifts-ai/v1';
    const OPTION_MODEL = 'fm_gifts_ai_model';

    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
        add_action( 'wp_footer', array( __CLASS__, 'render_widget' ) );
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
        add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
    }

    public static function api_key() {
        return defined( 'FM_GIFTS_OPENAI_API_KEY' ) && is_string( FM_GIFTS_OPENAI_API_KEY ) ? trim( FM_GIFTS_OPENAI_API_KEY ) : '';
    }

    public static function assets() {
        if ( is_admin() ) { return; }
        wp_enqueue_style( 'fm-gifts-ai', plugin_dir_url( __FILE__ ) . 'assets/chat.css', array(), '1.0.0' );
        wp_enqueue_script( 'fm-gifts-ai', plugin_dir_url( __FILE__ ) . 'assets/chat.js', array(), '1.0.0', true );
        wp_localize_script( 'fm-gifts-ai', 'FMGiftsAI', array(
            'endpoint' => esc_url_raw( rest_url( self::REST_NAMESPACE . '/chat' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'configured' => (bool) self::api_key(),
            'labels' => array(
                'title' => __( 'FM AI Assistant', 'fm-gifts-ai' ),
                'placeholder' => __( 'Ask about gifts, watches or orders…', 'fm-gifts-ai' ),
                'send' => __( 'Send', 'fm-gifts-ai' ),
                'welcome' => __( 'Hello! I can help you explore the FM Gifts collection. For order-specific help, please contact the store team.', 'fm-gifts-ai' ),
                'notConfigured' => __( 'The AI service is not configured yet. The site administrator must add the API key securely on the server.', 'fm-gifts-ai' ),
                'error' => __( 'Sorry, I could not get a reply. Please try again later.', 'fm-gifts-ai' ),
                'busy' => __( 'Thinking…', 'fm-gifts-ai' ),
            ),
        ) );
    }

    public static function render_widget() {
        if ( is_admin() ) { return; }
        ?>
        <div id="fm-gifts-ai" class="fm-ai" data-configured="<?php echo self::api_key() ? '1' : '0'; ?>">
          <button class="fm-ai-launch" type="button" aria-expanded="false" aria-controls="fm-ai-panel"><?php esc_html_e( 'Chat with FM AI', 'fm-gifts-ai' ); ?></button>
          <section id="fm-ai-panel" class="fm-ai-panel" hidden aria-label="<?php esc_attr_e( 'FM AI Assistant', 'fm-gifts-ai' ); ?>">
            <header class="fm-ai-head"><strong><?php esc_html_e( 'FM AI Assistant', 'fm-gifts-ai' ); ?></strong><button type="button" class="fm-ai-close" aria-label="<?php esc_attr_e( 'Close chat', 'fm-gifts-ai' ); ?>">×</button></header>
            <div class="fm-ai-messages" role="log" aria-live="polite"><p class="fm-ai-message fm-ai-assistant"><?php esc_html_e( 'Hello! I can help you explore the FM Gifts collection. For order-specific help, please contact the store team.', 'fm-gifts-ai' ); ?></p></div>
            <form class="fm-ai-form"><label class="screen-reader-text" for="fm-ai-input"><?php esc_html_e( 'Your message', 'fm-gifts-ai' ); ?></label><input id="fm-ai-input" name="message" maxlength="1200" autocomplete="off" required placeholder="<?php esc_attr_e( 'Ask about gifts, watches or orders…', 'fm-gifts-ai' ); ?>"><button type="submit"><?php esc_html_e( 'Send', 'fm-gifts-ai' ); ?></button></form>
            <p class="fm-ai-footnote"><?php esc_html_e( 'AI can make mistakes. Do not share passwords or payment details.', 'fm-gifts-ai' ); ?></p>
          </section>
        </div>
        <?php
    }

    public static function register_routes() {
        register_rest_route( self::REST_NAMESPACE, '/chat', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array( __CLASS__, 'chat' ),
            'permission_callback' => '__return_true',
            'args' => array( 'message' => array( 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ) ),
        ) );
    }

    public static function chat( WP_REST_Request $request ) {
        $key = self::api_key();
        if ( '' === $key ) {
            return new WP_Error( 'fm_gifts_ai_not_configured', __( 'AI service is not configured.', 'fm-gifts-ai' ), array( 'status' => 503 ) );
        }
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
        $rate_key = 'fm_gifts_ai_rate_' . hash( 'sha256', $ip );
        $count = (int) get_transient( $rate_key );
        if ( $count >= 10 ) {
            return new WP_Error( 'fm_gifts_ai_rate_limited', __( 'Please wait a minute before sending more messages.', 'fm-gifts-ai' ), array( 'status' => 429 ) );
        }
        set_transient( $rate_key, $count + 1, MINUTE_IN_SECONDS );

        $message = trim( (string) $request->get_param( 'message' ) );
        if ( '' === $message || strlen( $message ) > 1200 ) {
            return new WP_Error( 'fm_gifts_ai_invalid_message', __( 'Please enter a message under 1200 characters.', 'fm-gifts-ai' ), array( 'status' => 400 ) );
        }
        $model = sanitize_text_field( get_option( self::OPTION_MODEL, 'gpt-4o-mini' ) );
        if ( ! preg_match( '/^[a-zA-Z0-9._-]{2,80}$/', $model ) ) { $model = 'gpt-4o-mini'; }
        $payload = array(
            'model' => $model,
            'instructions' => 'You are FM AI, a helpful customer-service assistant for FM Gifts & Luxury Watches in Pakistan. Be concise, courteous, and reply in the language used by the customer, including Urdu/Roman Urdu when appropriate. Help customers discover watches, perfumes, gifts, kids items and accessories. Do not invent product availability, product prices, delivery promises, order status, discounts, or payment confirmations. If catalogue/order data is not provided, say you cannot verify it and direct the customer to the store contact page. Never request passwords, card numbers, OTPs, or API keys. You cannot place orders, process payments, or change account data.',
            'input' => $message,
            'max_output_tokens' => 300,
            'store' => false,
        );
        $response = wp_remote_post( 'https://api.openai.com/v1/responses', array(
            'timeout' => 25,
            'headers' => array( 'Authorization' => 'Bearer ' . $key, 'Content-Type' => 'application/json' ),
            'body' => wp_json_encode( $payload ),
            'data_format' => 'body',
        ) );
        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fm_gifts_ai_upstream_error', __( 'AI service is temporarily unavailable.', 'fm-gifts-ai' ), array( 'status' => 502 ) );
        }
        $status = (int) wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( $status < 200 || $status >= 300 || ! is_array( $body ) ) {
            error_log( 'FM Gifts AI upstream request failed with HTTP ' . $status );
            return new WP_Error( 'fm_gifts_ai_upstream_error', __( 'AI service could not complete the request.', 'fm-gifts-ai' ), array( 'status' => 502 ) );
        }
        $reply = '';
        if ( isset( $body['output'] ) && is_array( $body['output'] ) ) {
            foreach ( $body['output'] as $item ) {
                if ( ! empty( $item['content'] ) && is_array( $item['content'] ) ) {
                    foreach ( $item['content'] as $content ) {
                        if ( isset( $content['type'], $content['text'] ) && 'output_text' === $content['type'] ) { $reply .= $content['text']; }
                    }
                }
            }
        }
        if ( '' === trim( $reply ) ) {
            return new WP_Error( 'fm_gifts_ai_empty_reply', __( 'AI service returned no text. Please try again.', 'fm-gifts-ai' ), array( 'status' => 502 ) );
        }
        return rest_ensure_response( array( 'reply' => wp_strip_all_tags( $reply ) ) );
    }

    public static function register_settings() {
        register_setting( 'fm_gifts_ai_settings', self::OPTION_MODEL, array(
            'type' => 'string',
            'sanitize_callback' => function ( $value ) {
                $value = sanitize_text_field( $value );
                return preg_match( '/^[a-zA-Z0-9._-]{2,80}$/', $value ) ? $value : 'gpt-4o-mini';
            },
            'default' => 'gpt-4o-mini',
        ) );
    }

    public static function add_settings_page() {
        add_options_page( __( 'FM Gifts AI', 'fm-gifts-ai' ), __( 'FM Gifts AI', 'fm-gifts-ai' ), 'manage_options', 'fm-gifts-ai', array( __CLASS__, 'settings_page' ) );
    }

    public static function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        ?>
        <div class="wrap"><h1><?php esc_html_e( 'FM Gifts AI Assistant', 'fm-gifts-ai' ); ?></h1>
          <p><?php esc_html_e( 'The API key is read only from the server-side FM_GIFTS_OPENAI_API_KEY constant in wp-config.php. Never paste it into this settings page or browser code.', 'fm-gifts-ai' ); ?></p>
          <form method="post" action="options.php"><?php settings_fields( 'fm_gifts_ai_settings' ); ?>
            <label for="fm-gifts-ai-model"><strong><?php esc_html_e( 'OpenAI model', 'fm-gifts-ai' ); ?></strong></label>
            <input id="fm-gifts-ai-model" name="<?php echo esc_attr( self::OPTION_MODEL ); ?>" value="<?php echo esc_attr( get_option( self::OPTION_MODEL, 'gpt-4o-mini' ) ); ?>" class="regular-text">
            <?php submit_button(); ?>
          </form>
          <p><?php echo self::api_key() ? esc_html__( 'API key detected on server.', 'fm-gifts-ai' ) : esc_html__( 'API key not detected. Chat widget will show a setup notice until configured.', 'fm-gifts-ai' ); ?></p>
        </div>
        <?php
    }

    public static function admin_notice() {
        if ( ! current_user_can( 'manage_options' ) || self::api_key() ) { return; }
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'FM Gifts AI plugin is installed, but replies are disabled until FM_GIFTS_OPENAI_API_KEY is configured securely in wp-config.php.', 'fm-gifts-ai' ) . ' <a href="' . esc_url( admin_url( 'options-general.php?page=fm-gifts-ai' ) ) . '">' . esc_html__( 'AI settings', 'fm-gifts-ai' ) . '</a></p></div>';
    }
}
FM_Gifts_AI_Assistant::init();