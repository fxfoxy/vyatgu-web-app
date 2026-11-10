<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class BooleanParameter extends ParameterAbstract {
    protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        $value = $unverifiedParameterValue;

        if ($value === true || $value === 1 || $value === '1' || $value === 'true') {
            $value = true;
        } elseif ($value === false || $value === 0 || $value === '0' || $value === 'false') {
            $value = false;
        } else {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_BOOLEAN;
            return;
        }

        $validatedData[$parameterName] = $value;
    }
}
