<?php

namespace App\Library\Framework\Routing;

use App\Library\Framework\EnumHelperTrait;
use BackedEnum;

/**
 * Доступные фичи приложения - содержащие контроллеры
 *
 * @method static BackedEnum[] cases()
 */
enum AvailableFeaturesEnum: string {
    use EnumHelperTrait;

    case API = 'Api';
}
