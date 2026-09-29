<?php declare(strict_types=1);

namespace App\Library;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Processor\MemoryPeakUsageProcessor;

final class Super {
    /**
     * Формат по-умолчанию "Y-m-d\TH:i:sP"
     */
    private const string LOGGER_DATE_FORMAT = 'Y-m-d\TH:i:s.u';

    /**
     * Формат по-умолчанию "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
     */
    private const string LOGGER_MESSAGE_FORMAT = "[%datetime%] %level_name%: %message% %context% %extra%\n";

    private static ?Logger $logger = null;

    public static function logger(): Logger {
        if (isset(self::$logger)) {
            return self::$logger;
        }

        $steamHandler = new StreamHandler(__DIR__ . '/../logs/application.log', Level::Debug);
        $steamHandler->setFormatter(new LineFormatter(self::LOGGER_MESSAGE_FORMAT, self::LOGGER_DATE_FORMAT));

        $logger = new Logger('application');
        $logger->pushHandler($steamHandler);

        $logger->pushProcessor(function (LogRecord $record): LogRecord {
            $messagePrepend = getmypid() . " {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']} ";
            return $record->with(message: $messagePrepend . $record->message);
        });
        $logger->pushProcessor(new MemoryPeakUsageProcessor());

        self::$logger = $logger;
        return self::$logger;
    }

}
