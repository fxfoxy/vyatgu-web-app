<?php declare(strict_types=1);

namespace App\Library;

use App\Library\Config\ApplicationConfig;
use App\Library\Config\EnvironmentsEnum;
use App\Library\Framework\CronTaskAbstract;
use App\Library\Framework\LogHelper;
use App\Library\Tasks\RemoveOldSessionsCronTask;
use Monolog\Logger;

final class Super {
    /** @var Logger[] */
    private static array $logs = [];

    public static function log(string $logName = 'application'): Logger {
        if (empty(self::$logs[$logName])) {
            self::$logs[$logName] = LogHelper::logger($logName);
        }

        return self::$logs[$logName];
    }

    private static ?ApplicationConfig $applicationConfig = null;

    public static function config(): ApplicationConfig {
        if (!self::$applicationConfig) {
            self::$applicationConfig = ApplicationConfig::getInstance(EnvironmentsEnum::getEnvironment()->value);
        }

        return self::$applicationConfig;
    }

    /**
     * Получим все задания и выполним по одному
     */
    public static function executeAllTasks(): void {
        /** @var CronTaskAbstract $taskClass */
        foreach (self::getTaskClasses() as $taskClass) {
            $task = new $taskClass();
            if ($task instanceof CronTaskAbstract) {
                $task->executeInternal();
            }
            unset($task);
        }
    }

    private static function getTaskClasses(): array {
        $classes = [];

        /** @see RemoveOldSessionsCronTask Magic usage */
        $namespace = 'App\Library\Tasks\\';
        foreach (glob(ROOT_DIR . '/Library/Tasks/*.php') as $filePath) {
            $fullClassName = $namespace . basename($filePath, '.php');
            if (class_exists($fullClassName)) {
                $classes[] = $fullClassName;
            }
        }

        return $classes;
    }
}
