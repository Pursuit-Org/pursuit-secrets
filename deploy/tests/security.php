<?php declare(strict_types=1);

// Run against the built deployment image, not the historical source checkout:
// docker run --rm --entrypoint php -v "$PWD/deploy/tests:/tests:ro" \
//   pursuit-secrets:security-review /tests/security.php
define('PATH', '/srv/');
define('PUBLIC_PATH', '/var/www');
require PATH . 'vendor/autoload.php';

use PrivateBin\Configuration;
use PrivateBin\Exception\TranslatedException;
use PrivateBin\Request;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

check(version_compare(PHP_VERSION, '8.5.12', '>='), 'Unpatched PHP runtime');
check(!extension_loaded('session') || in_array(ini_get('session.upload_progress.enabled'), ['0', ''], true), 'Upload progress enabled');

$configuration = new Configuration;
foreach ($configuration->getSection('sri') as $file => $integrity) {
    $actual = 'sha512-' . base64_encode(hash_file('sha512', PUBLIC_PATH . '/' . $file, true));
    check(hash_equals($integrity, $actual), 'SRI mismatch: ' . $file);
}

$paste = [
    'v' => 2,
    'adata' => [
        ['gMSNoLOk4z0RnmsYwXZ8mw==', 'TZO+JWuIuxs=', 100000, 256, 128, 'aes', 'gcm', 'zlib'],
        'plaintext', 0, 0,
    ],
    'meta' => ['expire' => '5min'],
    'ct' => 'ME5JF/YBEijp2uYMzLZozbKtWc5wfy6R59NBb7SmRig=',
];
$outer = $nested = $comment = $paste;
$outer['adata'][4] = str_repeat('A', 10000);
$nested['adata'][0][8] = str_repeat('B', 10000);
$comment['adata'] = $nested['adata'][0];
unset($comment['meta']);
$comment['pasteid'] = $comment['parentid'] = '5b65a01b43987bc2';

// The actual body is measured, regardless of Content-Length or HTTP verb.
$stream = new ReflectionProperty(Request::class, '_inputStream');
foreach (['POST', 'PUT', 'DELETE'] as $method) {
    $_GET = [];
    $_SERVER = ['REQUEST_METHOD' => $method, 'HTTP_X_REQUESTED_WITH' => 'JSONHttpRequest'];
    foreach ([$outer, $nested, $comment] as $data) {
        $body = json_encode($data, JSON_THROW_ON_ERROR);
        $stream->setValue(null, 'data://text/plain;base64,' . base64_encode($body));
        $request = new Request;
        try {
            $request->getData(1024);
            throw new RuntimeException('Oversized metadata accepted for ' . $method);
        } catch (TranslatedException $expected) {
            check(str_contains($expected->getMessage(), 'limited'), 'Unexpected rejection');
        }
    }
}

// Preserve valid encrypted payloads, including the exact full-request boundary.
$_SERVER['REQUEST_METHOD'] = 'POST';
$body = json_encode($paste, JSON_THROW_ON_ERROR);
$stream->setValue(null, 'data://text/plain;base64,' . base64_encode($body));
$request = new Request;
check($request->getData(strlen($body)) == $paste, 'Valid paste was changed');
check(PrivateBin\FormatV2::isValid($paste), 'Valid encrypted format rejected');
try {
    $request->getData(strlen($body) - 1);
    throw new RuntimeException('Request one byte above limit accepted');
} catch (TranslatedException $expected) {
}

echo 'PASS: PHP floor, upload progress, all asset SRI hashes, oversized metadata in pastes/comments across POST/PUT/DELETE, valid encrypted payload, exact byte boundary.', PHP_EOL;
