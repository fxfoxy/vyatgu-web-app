<?php declare(strict_types=1);

use App\Library\Config\EncryptedValue;
use App\Library\Config\EnvironmentsEnum;

$pgSqlConfig = [
    'host'      => 'postgres',
    'port'      => 5432,
    'database'  => 'app',
    'username'  => new EncryptedValue('be9238875b2e80b0e32f85a9cb6d0a56Bnafbza6aPcpB28Yhu6RAw=='),
    'password'  => new EncryptedValue('9786eafbca868ef26e6593673d38b23cGbu+tidr1zvI+IeW35AAxEA2rpemNdwIitEeD9jX3Ok='),
];

return [
    EnvironmentsEnum::TEST->value => $pgSqlConfig,
    EnvironmentsEnum::DEV->value  => $pgSqlConfig,
    EnvironmentsEnum::PROD->value => [
        'host'      => 'postgres-prod',
        'port'      => 5432,
        'database'  => 'app-prod',
        'username'  => null,
        'password'  => null,
    ],
];
