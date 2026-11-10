<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class DatetimeParameter extends StringParameter {
    public function __construct() {
        $this
            ->setMaxLength(25)
            ->setMinLength(25);
    }

    protected ?string $minDatetime = null;
    protected ?string $maxDatetime = null;

    /** Ширина интервала, принимаемого за "текущее время" */
    protected int $datetimePrecisionSeconds = 1;

    protected $cannotBePast = false;

    final public function setMinDatetime(?string $datetime): static {
        if ($datetime === null) {
            $this->minDatetime = null;
        } else {
            $this->minDatetime = $datetime;
        }
        return $this;
    }

    final public function setMaxDatetime(?string $datetime): static {
        if ($datetime === null) {
            $this->maxDatetime = null;
        } else {
            $this->maxDatetime = $datetime;
        }
        return $this;
    }

    final public function setCannotBePast(bool $cannotBePast = true): static {
        $this->cannotBePast = $cannotBePast;
        return $this;
    }

    /**
     * Ожидаем дату вида: 2026-11-10T10:30:45+03:00
     */
    public function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        parent::validateInternal($parameterName, $unverifiedParameterValue, $validatedData, $errors);
        if ($errors) {
            return;
        }

        $value = $validatedData[$parameterName];
        if (!preg_match('/^\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\d[+-]\d\d:\d\d$/', $value)) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_DATETIME_FORMAT;
            return;
        }

        // Переводим дату в серверный часовой пояс
        $time = strtotime($value);
        if ($time === false) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_DATETIME;
            return;
        }

        // Компенсируем задержки во время доставки и обработки
        $allowedMinTime = $time + $this->datetimePrecisionSeconds;
        $allowedMaxTime = $time - $this->datetimePrecisionSeconds;

        if ($this->cannotBePast && $allowedMinTime < time()) {
            $errors[$parameterName][] = ApiParameterErrors::CANNOT_BE_PAST;
            return;
        }

        if ($this->minDatetime !== null && $allowedMinTime < strtotime($this->minDatetime)) {
            $errors[$parameterName][] = ApiParameterErrors::MIN_DATE;
            return;
        }
        if ($this->maxDatetime !== null && $allowedMaxTime > strtotime($this->maxDatetime)) {
            $errors[$parameterName][] = ApiParameterErrors::MAX_DATE;
            return;
        }

        $validatedData[$parameterName] = date('c', $time);
    }
}
