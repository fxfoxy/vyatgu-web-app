<?php declare(strict_types=1);

namespace App\Library\Framework;

use App\Library\Super;
use LogicException;
use Monolog\Logger;

abstract class CronTaskAbstract {
    readonly public string $humanTaskName;
    readonly public string $systemTaskName;

    protected Logger $logger;

    public function __construct() {
        $className = Utils::getClassSimpleName(static::class);
        if (!str_ends_with($className, 'CronTask')) {
            throw new LogicException("{$className} should end with `Task`");
        }

        $this->humanTaskName  = preg_replace('/CronTask$/', '', $className);
        $this->systemTaskName = Utils::camel2kebab(lcfirst($this->humanTaskName));

        $this->logger = Super::log($this->systemTaskName);
    }

    /**
     * Тут полезная нагрузка
     */
    abstract public function execute(): void;

    public function executeInternal(): void {
        $this->logger->info("Running task '{$this->humanTaskName}'");
        $startTime = microtime(true);

        $this->execute();

        $totalTime = round(microtime(true) - $startTime, 3);
        $this->logger->info("Task '{$this->humanTaskName}' executed in {$totalTime} seconds");
    }
}
