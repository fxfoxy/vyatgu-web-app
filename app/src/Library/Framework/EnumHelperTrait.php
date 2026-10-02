<?php declare(strict_types=1);

namespace App\Library\Framework;

use BackedEnum;

trait EnumHelperTrait {
    /**
     * @return BackedEnum[]
     */
    abstract public static function cases(): array;

    public static function isValid(mixed $value): bool {
        foreach (self::cases() as $case) {
            if ($value === $case->value) {
                return true;
            }
        }
        return false;
    }

    public static function toCase(mixed $value): ?BackedEnum {
        foreach (self::cases() as $case) {
            if ($value === $case->value) {
                return $case;
            }
        }
        return null;
    }
}
