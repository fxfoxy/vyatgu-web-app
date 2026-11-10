<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

abstract class NumericParameterAbstract extends ParameterAbstract {
    /** @var float|int */
    private mixed $minValue;

    /** @var float|int */
    private mixed $maxValue;

    private bool $isMinStrict = false;

    private bool $isMaxStrict = false;

    /**
     * @param int|float $minValue
     */
    final public function setMinValue($minValue): static {
        $this->minValue = $minValue;
        return $this;
    }

    /**
     * @param int|float $maxValue
     */
    final public function setMaxValue($maxValue): static {
        $this->maxValue = $maxValue;
        return $this;
    }

    final public function setIsMinStrict(bool $isMinStrict): static {
        $this->isMinStrict = $isMinStrict;
        return $this;
    }

    final public function setIsMaxStrict(bool $isMaxStrict): static {
        $this->isMaxStrict = $isMaxStrict;
        return $this;
    }

    protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        if (!is_numeric($unverifiedParameterValue)) {
            $errors[$parameterName][] = $this->getNumericError();
            return;
        }

        $value = $this->filterNumericValue($unverifiedParameterValue);

        if ($this->minValue !== null && (($value < $this->minValue && !$this->isMinStrict) || ($value <= $this->minValue && $this->isMinStrict))) {
            $errors[$parameterName][] = ApiParameterErrors::MIN_VALUE;
            return;
        }

        if ($this->maxValue !== null && (($value > $this->maxValue && !$this->isMaxStrict) || ($value >= $this->maxValue && $this->isMaxStrict))) {
            $errors[$parameterName][] = ApiParameterErrors::MAX_VALUE;
            return;
        }

        $validatedData[$parameterName] = $value;
    }

    abstract protected function filterNumericValue(mixed $unverifiedParameterValue): mixed;

    abstract protected function getNumericError(): string;
}
