<?php declare(strict_types=1);

use App\Library\Database\Database;
use App\Library\Super;

require_once __DIR__ . '/vendor/autoload.php';

// Обычно без БД никуда
Database::init();

// Конвертируем ошибки в исключения
set_error_handler(function (int $severity, string $message, string $file, int $line)  {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

// А тут уже разберёмся с не пойманными исключениями
set_exception_handler(function (Throwable $exception) {
    Super::logger()->error($exception->getMessage(), $exception->getTrace());

    if (PHP_SAPI === 'cli') {
        exit(1);
    }

    http_response_code(500);
});
