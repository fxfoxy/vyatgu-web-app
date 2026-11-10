<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class FloatParameter extends NumericParameterAbstract {
    protected function filterNumericValue(mixed $unverifiedParameterValue): float {
        return (float) $unverifiedParameterValue;
    }

    protected function getNumericError(): string {
        return ApiParameterErrors::INVALID_FLOAT;
    }
}
