<?php declare(strict_types=1);

use App\Library\Database\Database;
use App\Library\Database\Session;
use App\Library\Framework\Routing\ControllerAbstract;
use App\Library\Super;

require __DIR__ . '/../src/init.php';

ControllerAbstract::processRequest();

/*
die;

$rows    = [];
$dbError = null;

try {
    $rows = Database::select('
        SELECT
            table_name AS "Таблица",
            column_name AS "Колонка",
            data_type AS "Тип данных",
            character_maximum_length AS "Длина",
            is_nullable AS "Разрешен NULL",
            column_default AS "По умолчанию"
        FROM information_schema.columns
        WHERE table_schema = \'public\'
        ORDER BY table_name, ordinal_position;
    ');

    $rows = Session::all();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
    Super::log()->error($dbError);
}

echo '<pre>';
if ($dbError) {
    echo $dbError;
} else {
    print_r($rows);
}
echo '</pre>';
*/
