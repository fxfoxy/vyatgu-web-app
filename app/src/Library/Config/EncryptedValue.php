<?php declare(strict_types=1);

namespace App\Library\Config;

final class EncryptedValue {
    /**
     * Первые 16 символов - hex-представление соли
     * 17-й и далее - строка закодированная в base64
     */
    protected string $encryptedString;

    private const string CIPHER       = 'aes-256-cbc';
    private const int SALT_SIZE_BYTES = 16;

    public function __construct(string $encryptedString) {
        $this->encryptedString = $encryptedString;
    }

    public function getDecryptedValue(): string {
        $value = $this->decryptString($this->encryptedString);
        if ($value === false) {
            throw new ConfigException('Cannot decrypt a config value: invalid cipher key');
        }

        return $value;
    }

    private function decryptString(string $string): false|string {
        $cipherKey      = self::getCipherKey();
        $saltHex        = substr($string, 0, 2 * self::SALT_SIZE_BYTES);
        $encryptedValue = substr($string, 2 * self::SALT_SIZE_BYTES);

        if (
            strlen($saltHex) != 2 * self::SALT_SIZE_BYTES
            || !preg_match('/^[0-9a-f]+$/', $saltHex)
            || empty($encryptedValue)
        ) {
            throw new ConfigException("Invalid encrypted value starting with " . substr($string, 0, 32));
        }

        return openssl_decrypt(
            $encryptedValue,
            self::CIPHER,
            $cipherKey,
            0,
            hex2bin($saltHex),
        );
    }

    public static function encrypt(string $plainString): string {
        $cipherKey = self::getCipherKey();

        $saltBytes      = openssl_random_pseudo_bytes(static::SALT_SIZE_BYTES);
        $encryptedValue = openssl_encrypt(
            $plainString,
            static::CIPHER,
            $cipherKey,
            0,
            $saltBytes,
        );

        return bin2hex($saltBytes) . $encryptedValue;
    }

    private static function getCipherKey(): string {
        // Задаём в .env значение ключа в виде размером 32 байта,
        // например: bin2hex(openssl_random_pseudo_bytes(32));
        $cipher = getenv('CONFIG_CIPHER') ?: null;
        if (empty($cipher)) {
            throw new ConfigException('Cipher key is not specified');
        }

        return hex2bin($cipher);
    }
}
