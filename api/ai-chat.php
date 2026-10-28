<?php
declare(strict_types=1);

/*
 * FM Gifts AI Shopping Assistant.
 * Configure FM_OPENAI_API_KEY (preferred) or OPENAI_API_KEY in the PHP server
 * environment. Never place the key in JavaScript, HTML, or a committed file.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $body): never {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Use POST to chat with the assistant.']);
}
$contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
if (strpos($contentType, 'application/json') === false) {
    respond(415, ['error' => 'Expected a JSON request.']);
}
if (!function_exists('curl_init')) {
    respond(503, ['error' => 'The AI service is not available on this server yet.']);
}
$apiKey = getenv('FM_OPENAI_API_KEY') ?: getenv('OPENAI_API_KEY');
if (!$apiKey && is_file(__DIR__ . '/config.php')) {
    // Deployment-generated config is protected by api/.htaccess.
    require __DIR__ . '/config.php';
    if (isset($fmOpenAiKey) && is_string($fmOpenAiKey)) {
        $apiKey = $fmOpenAiKey;
    }
}
if (!$apiKey) {
    respond(503, ['error' => 'AI assistant setup is pending. Please contact us on WhatsApp for help.']);
}
$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '', true);
if (!is_array($payload) || !isset($payload['messages']) || !is_array($payload['messages'])) {
    respond(400, ['error' => 'Please send a valid chat message.']);
}
$messages = array_slice($payload['messages'], -8);
$cleanMessages = [];
foreach ($messages as $message) {
    if (!is_array($message) || !in_array($message['role'] ?? '', ['user', 'assistant'], true) || !is_string($message['content'] ?? null)) {
        continue;
    }
    $content = trim($message['content']);
    if ($content === '') continue;
    if (function_exists('mb_substr')) $content = mb_substr($content, 0, 1200, 'UTF-8');
    else $content = substr($content, 0, 1200);
    $cleanMessages[] = ['role' => $message['role'], 'content' => $content];
}
if (!$cleanMessages || end($cleanMessages)['role'] !== 'user') {
    respond(400, ['error' => 'Please type a question first.']);
}

$catalogueContext = 'Catalogue status: demo_collection_only. Never claim an item is in stock or that a demo price is a confirmed offer. Ask the shopper to confirm availability with the store team. ';
$cataloguePath = __DIR__ . '/../data/catalog.json';
if (is_file($cataloguePath)) {
    $catalogue = json_decode((string)file_get_contents($cataloguePath), true);
    if (is_array($catalogue['products'] ?? null)) {
        $items = [];
        foreach ($catalogue['products'] as $product) {
            if (($product['status'] ?? '') !== 'Active') continue;
            $items[] = [
                'name' => (string)($product['name'] ?? 'Product'),
                'category' => (string)($product['category'] ?? ''),
                'price' => $product['price'] ?? null,
                'currency' => 'PKR',
                'sku' => (string)($product['sku'] ?? ''),
                'colors' => $product['color_variations'] ?? []
            ];
        }
        $catalogueContext .= 'Catalogue reference data (may include demo listings; do not represent as verified inventory): ' .
            json_encode(array_slice($items, 0, 140), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
$systemPrompt = 'You are the FM Gifts & Luxury Watches shopping assistant for customers in Pakistan. Be friendly, concise, and helpful. Help shoppers discover watches, fragrances, gifts, belts, wallets, watch chains, clocks, and accessories; explain product options and guide them to WhatsApp for order support. Reply in the language used by the customer (including Urdu/Roman Urdu). Prices, if mentioned, must be explicitly described as catalogue reference prices unless verified by the store. Never invent stock, delivery promises, payment confirmation, order status, or product facts. Never ask for card PINs, passwords, or OTPs. For a real order or availability confirmation, direct the customer to the store WhatsApp. ' . $catalogueContext;

$requestBody = [
    'model' => 'gpt-4o-mini',
    'messages' => array_merge([['role' => 'system', 'content' => $systemPrompt]], $cleanMessages),
    'temperature' => 0.4,
    'max_tokens' => 450
];
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 35,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
    CURLOPT_POSTFIELDS => json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($response === false || $status < 200 || $status >= 300) {
    error_log('FM Gifts AI request failed; HTTP ' . $status . ($curlError ? ' (cURL error)' : ''));
    respond(502, ['error' => 'Sorry, the assistant is temporarily unavailable. Please try again or contact us on WhatsApp.']);
}
$data = json_decode($response, true);
$answer = $data['choices'][0]['message']['content'] ?? '';
if (!is_string($answer) || trim($answer) === '') {
    respond(502, ['error' => 'The assistant could not prepare a reply. Please try again.']);
}
respond(200, ['reply' => trim($answer)]);
