<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

final class Database extends Capsule {
    public static function init(): void {
        $capsule = new self();

        $capsule->addConnection([
            'driver'    => 'pgsql',
            'host'      => getenv('DB_HOST') ?: 'postgres',
            'port'      => '5432',
            'database'  => getenv('DB_NAME') ?: 'app',
            'username'  => getenv('DB_USER') ?: 'app',
            'password'  => getenv('DB_PASSWORD') ?: '',
            'charset'   => 'utf8',
            'schema'    => 'public',
            'prefix'    => '',
            'sslmode'   => 'prefer', // prefer, disable, allow, require
       ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public static function getInstance(): object {
        return self::$instance;
    }
}
