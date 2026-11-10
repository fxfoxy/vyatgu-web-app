<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\ParameterAbstract;
use App\Library\Auth\UserAuth;
use App\Library\Database\User;
use App\Library\Exceptions\ParametersValidationApiException;
use App\Library\Framework\Routing\HttpMethodsEnum;
use App\Library\Framework\Routing\HttpRequest;

abstract class RequestAbstract {
    protected HttpRequest $httpRequest;

    protected ?User $user;

    protected array $validatedData = [];
    protected array $errors       = [];

    public function __construct(HttpRequest $httpRequest, HttpMethodsEnum $requestMethod) {
        $httpRequest->requireHttpMethod($requestMethod);

        $this->httpRequest = $httpRequest;
        $this->user        = UserAuth::authorizeBySession();

        $this->validateRequestData();
        if ($this->errors) {
            throw new ParametersValidationApiException($this->errors);
        }
    }

    /**
     * Тут проверим логику самого действия: можно или нет?
     */
    abstract public function validate(): void;

    /**
     * Выполняет запрос и получает запрашиваемый список моделей.
     */
    abstract public function execute(): void;

    /**
     * Возвращает массив допустимых параметров запроса с типами.
     * @return ParameterAbstract[]
     */
    abstract protected function getParameters(): array;

    protected function validateRequestData(): void {
        foreach ($this->getParameters() as $name => $parameterValidator) {
            $parameterValidator->validate($name, $this->httpRequest->getUnverifiedData(), $this->validatedData, $this->errors);
        }
    }
}
