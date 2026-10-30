<?php declare(strict_types=1);

require __DIR__ . '/../init.php';

use App\Library\Database\Database;
use App\Library\Framework\Routing\FeatureControllerMapBuilder;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

Database::init();

$migrationDirection = $_SERVER['argv'][1] ?? 'up';
if ($migrationDirection !== 'up' && $migrationDirection !== 'down') {
    die("Invalid migration direction. Allowed: up, down\n");
}

$databaseManager     = Database::getInstance()->getDatabaseManager();
$migrationRepository = new DatabaseMigrationRepository($databaseManager, 'migrations');

// Создаем таблицу migrations, если её ещё нет
if (!$migrationRepository->repositoryExists()) {
    $migrationRepository->createRepository();
}

// Получаем список уже выполненных миграций (метод возвращает массив строк)
$executedMigrations = $migrationRepository->getRan();

$migrationsDir  = ROOT_DIR . '/migrations';
$migrationFiles = glob($migrationsDir . '/*.php');

// Получаем номер следующего шага
$nextBatch = $migrationRepository->getNextBatchNumber();
$runCount = 0;

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');

    if (!in_array($migrationName, $executedMigrations)) {
        echo "Start migration: $migrationName... ";

        $migrationInstance = require $file;
        if ($migrationDirection === 'up') {
            $migrationInstance->up();
        } else {
            $migrationInstance->down();
        }

        // Записываем лог через встроенный метод репозитория
        $migrationRepository->log($migrationName, $nextBatch);

        echo "Success!\n";
        $runCount++;
    }
}

if ($runCount === 0) {
    echo "Database is is up to date.\n";
}

echo FeatureControllerMapBuilder::build(), "\n";
