<?php
declare(strict_types=1);

/**
 * FM Gifts Phase 1 diagnostic endpoint.
 * Install only under htdocs/fm-gifts-preview/; never in the live document root.
 * Requires preview/.state/token containing: <32-char-hex-token>|<UTC-expiry-ISO8601>
 */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

$stateDir = __DIR__ . '/.state';
$tokenPath = $stateDir . '/token';
$countPath = $stateDir . '/count';

function failClosed(): never {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Not found\n";
    exit;
}

if (!is_file($tokenPath) || !is_readable($tokenPath)) {
    failClosed();
}
$tokenLine = trim((string) file_get_contents($tokenPath));
$parts = explode('|', $tokenLine, 2);
if (count($parts) !== 2 || !preg_match('/^[a-f0-9]{32}$/', $parts[0])) {
    failClosed();
}
try {
    $expires = new DateTimeImmutable($parts[1]);
} catch (Throwable $e) {
    failClosed();
}
if ($expires <= new DateTimeImmutable('now', new DateTimeZone('UTC'))) {
    failClosed();
}

$provided = isset($_GET['t']) && is_string($_GET['t']) ? $_GET['t'] : '';
if ($provided === '' || !hash_equals($parts[0], $provided)) {
    failClosed();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'phase' => 'phase1',
        'method' => 'GET',
        'utc' => gmdate('c'),
        'php' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
    ], JSON_UNESCAPED_SLASHES) . "\n";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    http_response_code(405);
    exit;
}

$contentType = strtolower(trim(explode(';', (string) ($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
if ($contentType !== 'application/json') {
    http_response_code(415);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"ok":false,"error":"application/json required"}';
    exit;
}
$raw = file_get_contents('php://input');
$body = json_decode($raw === false ? '' : $raw, true);
if (!is_array($body) || ($body['ping'] ?? null) !== 'pong') {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"ok":false,"error":"expected JSON {"ping":"pong"}"}';
    exit;
}

$count = 0;
if (is_file($countPath) && is_readable($countPath)) {
    $old = trim((string) file_get_contents($countPath));
    if (ctype_digit($old)) {
        $count = (int) $old;
    }
}
$count++;
if (@file_put_contents($countPath, (string) $count . "\n", LOCK_EX) === false) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"ok":false,"error":"state directory is not writable"}';
    exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true, 'phase' => 'phase1', 'method' => 'POST', 'count' => $count], JSON_UNESCAPED_SLASHES) . "\n";
