<?php declare(strict_types=1);

require __DIR__ . '/../../init.php';

use App\Library\Config\EncryptedValue;

if (empty($_SERVER['argv'][1])) {
    die("Value for encryption must be provided\n");
}

$valueForEncryption = $_SERVER['argv'][1];

echo EncryptedValue::encrypt($valueForEncryption), PHP_EOL;
