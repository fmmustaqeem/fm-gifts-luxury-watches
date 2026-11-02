<?php
declare(strict_types=1);
require_once __DIR__ . '/../../wordpress/wp-content/plugins/fm-gifts-control/includes/class-logger.php';

use FMGifts\\Control\\Logger;

$input = (object) [
    'profile' => (object) [
        'email' => 'owner@example.invalid',
        'display_name' => 'Synthetic Customer',
        'phone' => '+92 300 1234567',
    ],
    'network' => (object) ['ip_address' => '192.0.2.10'],
    'credentials' => (object) ['api_key' => 'private-api-key-canary'],
    'message' => 'Bearer private-bearer-canary and Basic private-basic-canary; sk-proj-fmgiftsCanary1234567890; ghp_fmgiftsCanary12345678901234567890; api_key=private-inline-key-canary',
];
$redacted = Logger::redact($input);
$json = json_encode($redacted, JSON_UNESCAPED_SLASHES);
foreach ([
    'owner@example.invalid', 'Synthetic Customer', '1234567', '192.0.2.10',
    'private-api-key-canary', 'private-bearer-canary', 'private-basic-canary',
    'sk-proj-fmgiftsCanary1234567890', 'ghp_fmgiftsCanary12345678901234567890',
    'private-inline-key-canary'
] as $secret) {
    if (str_contains((string) $json, $secret)) {
        fwrite(STDERR, "FAIL: object redaction leaked {$secret}" . PHP_EOL);
        exit(1);
    }
}
$opaque = new class { private string $secret = 'private-object-canary'; };
if (Logger::redact($opaque) !== '[OBJECT REDACTED]') {
    fwrite(STDERR, 'FAIL: opaque object was not safely redacted' . PHP_EOL);
    exit(1);
}
echo "PASS: nested object properties and sensitive keys are redacted" . PHP_EOL;
echo "PASS: email, phone, IP and authorization strings are redacted" . PHP_EOL;
echo "PASS: common provider tokens and inline credentials are redacted" . PHP_EOL;
echo "PASS: opaque objects are replaced instead of serialized" . PHP_EOL;
