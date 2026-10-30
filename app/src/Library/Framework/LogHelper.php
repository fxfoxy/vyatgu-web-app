<?php declare(strict_types=1);

namespace App\Library\Framework;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Processor\MemoryPeakUsageProcessor;

final class LogHelper {
    /**
    * Формат по-умолчанию "Y-m-d\TH:i:sP"
    */
    private const string LOGGER_DATE_FORMAT = 'Y-m-d\TH:i:s.u';

    /**
     * Формат по-умолчанию "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
     */
    private const string LOGGER_MESSAGE_FORMAT = "[%datetime%] %level_name%: %message% %context% %extra%\n";

    public static function logger(string $logName): Logger {
        $lineFormatter = new LineFormatter(self::LOGGER_MESSAGE_FORMAT, self::LOGGER_DATE_FORMAT);

        $steamHandler = new StreamHandler(ROOT_DIR . "/logs/{$logName}.log", Level::Debug);
        $steamHandler->setFormatter($lineFormatter);

        $logger = new Logger($logName);
        $logger->pushHandler($steamHandler);

        if (PHP_SAPI === 'cli') {
            $steamHandler = new StreamHandler(STDERR, Level::Debug);
            $steamHandler->setFormatter($lineFormatter);

            $logger->pushHandler($steamHandler);
        }

        $logger->pushProcessor(function (LogRecord $record): LogRecord {
            $messagePrepend = getmypid() . ' ';
            if (PHP_SAPI !== 'cli') {
                $messagePrepend .= "{$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']} ";
            }
            return $record->with(message: $messagePrepend . $record->message);
        });
        $logger->pushProcessor(new MemoryPeakUsageProcessor());

        return $logger;
    }
}
