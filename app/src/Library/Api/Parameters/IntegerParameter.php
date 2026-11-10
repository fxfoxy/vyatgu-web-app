<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class IntegerParameter extends NumericParameterAbstract {
    protected function filterNumericValue(mixed $unverifiedParameterValue): int {
        return (int) $unverifiedParameterValue;
    }

    protected function getNumericError(): string {
        return ApiParameterErrors::INVALID_INTEGER;
    }
}
