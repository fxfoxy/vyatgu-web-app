<?php declare(strict_types=1);

use App\Library\Auth\SessionHandler;
use App\Library\Database\Database;
use App\Library\Super;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT_DIR', __DIR__);

// Конвертируем ошибки в исключения
set_error_handler(function (int $severity, string $message, string $file, int $line)  {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

// А тут уже разберёмся с не пойманными исключениями
set_exception_handler(function (Throwable $exception) {
    Super::log()->error($exception->getMessage(), $exception->getTrace());

    if (PHP_SAPI === 'cli') {
        exit(1);
    }

    http_response_code(500);
});

// Временный хак, что бы все запросы не дублировались, перенести в nginx
if (!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/favicon.ico') {
    http_response_code(404);
    die;
}

// Обычно без БД никуда
Database::init();

// Для веба используем сессии, хранимые в БД
if (PHP_SAPI !== 'cli') {
    session_set_save_handler(new SessionHandler(), true);

    // PHP тут читает отправленные браузером куки, но не обновляет
    // дату истечения уже отправленной нами куки, и что бы сделать
    // всё по уму, проще вообще отказаться от встроенного
    // механизма работы с куками :)
    session_start();
}
