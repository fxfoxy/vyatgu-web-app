<?php declare(strict_types=1);

namespace App\Library\Config;

use App\Library\Super;

/**
 * Конфиг приложения
 *
 * @property string $host
 * @property int $port
 * @property string $database
 * @property string $username
 * @property string $password
 */
final class ApplicationConfig {
    private const string CONFIG_PATH = __DIR__ . '/../../config';

    private array $configData;

    private function __construct(array $data) {
        $this->configData = $data;
    }

    public function __get(string $name): mixed {
        return $this->configData[$name] ?? null;
    }

    public static function getInstance(string $environment): static {
        return new static(static::getDecryptedConfig($environment));
    }

    private static function getDecryptedConfig(string $environment): array {
        $configData = static::getRawConfig($environment);

        // Расшифровываем пароли.
        // Если ключа нет или он неправильный, ставим вместо значения null
        // ошибки дальше запишутся в лог, когда система инициализируется
        $errors = [];
        array_walk_recursive($configData, function (&$value, string $key) use (&$errors): void {
            if ($value instanceof EncryptedValue) {
                try {
                    $value = $value->getDecryptedValue();
                } catch (ConfigException $e) {
                    $errors[$key] = "Error while processing key '{$key}': {$e->getMessage()}";
                    $value        = null;
                }
            }
        });
        if ($errors) {
            Super::logger()->error("Can't initialize config", $errors);
        }

        return $configData;
    }

    private static function getRawConfig(string $environment): array {
        $configParts = [];
        foreach (glob(self::CONFIG_PATH . '/*.php') as $file) {
            $configPart = include $file;

            if ($configPart && is_array($configPart) && !empty($configPart[$environment])) {
                $configParts[] = $configPart[$environment];
            }
        }

        return array_replace([], ...$configParts);
    }
}
