<?php

namespace App\Library\Config;

use App\Library\Framework\EnumHelperTrait;
use BackedEnum;

/**
 * @method static array cases()
 */
enum EnvironmentsEnum: string {
    use EnumHelperTrait;

    case TEST = 'test';
    case DEV = 'development';
    case PROD = 'production';

    public static function getEnvironment(): BackedEnum {
        return self::toCase(getenv('APPLICATION_ENV')) ?: self::DEV;
    }

    public static function isTest(): bool {
        return self::getEnvironment() === self::TEST;
    }

    public static function isDev(): bool {
        return self::getEnvironment() === self::DEV;
    }

    public static function isProd(): bool {
        return self::getEnvironment() === self::PROD;
    }
}
