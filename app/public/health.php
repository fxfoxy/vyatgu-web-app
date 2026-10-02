<?php declare(strict_types = 1);

use App\Library\Database\Database;

require __DIR__ . '/../init.php';

header('Content-Type: application/json; charset=utf-8');

$checks = [
    'php' => 'ok',
    'database' => 'ok',
];
$httpStatus = 200;

try {
    Database::select('SELECT 1');
} catch (Throwable $e) {
    $checks['database'] = 'fail';
    $httpStatus = 503;
}

http_response_code($httpStatus);

echo json_encode([
    'status' => $httpStatus === 200 ? 'ok' : 'fail',
    'checks' => $checks,
], JSON_UNESCAPED_UNICODE);
