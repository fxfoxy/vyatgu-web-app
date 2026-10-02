<?php declare(strict_types=1);

namespace App\Library;

use App\Library\Config\ApplicationConfig;
use App\Library\Config\EnvironmentsEnum;
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
        if (self::$logger) {
            return self::$logger;
        }

        $lineFormatter = new LineFormatter(self::LOGGER_MESSAGE_FORMAT, self::LOGGER_DATE_FORMAT);

        $steamHandler = new StreamHandler(__DIR__ . '/../logs/application.log', Level::Debug);
        $steamHandler->setFormatter($lineFormatter);

        $logger = new Logger('application');
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

        self::$logger = $logger;
        return self::$logger;
    }

    private static ?ApplicationConfig $applicationConfig = null;

    public static function config(): ApplicationConfig {
        if (!self::$applicationConfig) {
            self::$applicationConfig = ApplicationConfig::getInstance(EnvironmentsEnum::getEnvironment()->value);
        }

        return self::$applicationConfig;
    }

}
