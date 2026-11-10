<?php declare(strict_types=1);

namespace App\Library\Exceptions;

use Throwable;

class ParametersValidationApiException extends ApiException {
    protected array $errors;

    public function __construct(array $errors, string $message = '', int $code = 0, ?Throwable $previous = null) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
