<?php declare(strict_types=1);

namespace App\Library\Database;

use App\Library\Super;
use Illuminate\Database\Capsule\Manager as Capsule;

final class Database extends Capsule {
    public static function init(): void {
        if (isset(self::$instance)) {
            return;
        }

        $capsule = new self();

        $capsule->addConnection([
            'driver'    => 'pgsql',
            'host'      => Super::config()->host,
            'port'      => Super::config()->port,
            'database'  => Super::config()->database,
            'username'  => Super::config()->username,
            'password'  => Super::config()->password,
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
