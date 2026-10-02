<?php declare(strict_types=1);

require __DIR__ . '/../../init.php';

use App\Library\Config\EncryptedValue;

if (empty($_SERVER['argv'][1])) {
    die("Value for decryption must be provided\n");
}

$valueForDecryption = $_SERVER['argv'][1];

echo (new EncryptedValue($valueForDecryption))->getDecryptedValue(), PHP_EOL;
