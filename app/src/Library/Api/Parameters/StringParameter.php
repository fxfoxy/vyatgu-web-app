<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class StringParameter extends ParameterAbstract {
    private ?int $minLength = null;
    private ?int $maxLength = null;

    private bool $silentCutOnMaxLength = false;

    public function setMinLength(?int $minLength): static {
        $this->minLength = $minLength;
        return $this;
    }

    public function setMaxLength(?int $maxLength): static {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function silentCutOnMaxLength(bool $enabled = true): static {
        $this->silentCutOnMaxLength = $enabled;
        return $this;
    }

    protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        if (is_array($unverifiedParameterValue)) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_STRING;
            return;
        }

        $value = trim((string) $unverifiedParameterValue);

        if ($this->minLength && mb_strlen($value) < $this->minLength) {
            $errors[$parameterName][] = ApiParameterErrors::MIN_LENGTH;
            return;
        }

        if ($this->maxLength && mb_strlen($value) > $this->maxLength) {
            if (!$this->silentCutOnMaxLength) {
                $errors[$parameterName][] = ApiParameterErrors::MAX_LENGTH;
                return;
            }
            $value = mb_substr($value, 0, $this->maxLength);
        }

        $validatedData[$parameterName] = $value;
    }
}
